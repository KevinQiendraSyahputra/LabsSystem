<?php

namespace App\Http\Controllers;

use App\Models\LiveChat;
use App\Models\LiveChatMessage;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramWebhookController extends Controller
{
    /**
     * Handle incoming webhook updates from Telegram Bot
     */
    public function handle(Request $request)
    {
        $update = $request->all();

        if (empty($update) || !isset($update['message'])) {
            return response()->json(['status' => 'ignored']);
        }

        $message = $update['message'];
        $text = trim($message['text'] ?? '');
        $chatId = $message['chat']['id'] ?? null;
        $messageId = $message['message_id'] ?? null;

        if (empty($text)) {
            return response()->json(['status' => 'no_text']);
        }

        // 1. Tangani Command Typing: /typing atau /t
        if (preg_match('/^\/(?:typing|t)(?:\s+([A-Za-z0-9\-]+))?$/is', $text, $tMatches)) {
            $sessionCode = !empty($tMatches[1]) ? trim($tMatches[1]) : null;
            $liveChat = null;

            if ($sessionCode) {
                $liveChat = LiveChat::where('session_code', $sessionCode)->first();
            } elseif (isset($message['reply_to_message'])) {
                $replyTo = $message['reply_to_message'];
                $replyToMessageId = $replyTo['message_id'] ?? null;
                $replyToText = $replyTo['text'] ?? '';
                if ($replyToMessageId) {
                    $liveChat = LiveChat::where('telegram_last_message_id', $replyToMessageId)->first();
                }
                if (!$liveChat && preg_match('/ID Sesi:\s*([A-Za-z0-9\-]+)/i', $replyToText, $sMatch)) {
                    $liveChat = LiveChat::where('session_code', trim($sMatch[1]))->first();
                }
            }

            if (!$liveChat) {
                $activeChats = LiveChat::where('status', 'active')->orderBy('id', 'desc')->get();
                if ($activeChats->count() === 1) {
                    $liveChat = $activeChats->first();
                }
            }

            if ($liveChat) {
                $liveChat->update(['admin_typing_until' => now()->addSeconds(30)]);
                TelegramService::sendCsMessage(
                    "✍️ <b>Status Typing Aktif</b> pada web <b>" . htmlspecialchars($liveChat->user_name) . "</b> (<code>{$liveChat->session_code}</code>) selama 30 detik.",
                    $chatId
                );
                return response()->json(['status' => 'ok', 'method' => 'typing_activated']);
            } else {
                TelegramService::sendCsMessage(
                    "ℹ️ <b>Petunjuk Typing:</b> Gunakan <code>/t [KODE_SESI]</code> atau balas (Reply) pesan pengguna dengan <code>/t</code>.",
                    $chatId
                );
                return response()->json(['status' => 'typing_no_session']);
            }
        }

        // 2. Tangani Format Command: /reply [SESSION_CODE] [PESAN]
        if (preg_match('/^\/reply\s+([A-Za-z0-9\-]+)\s+(.+)$/is', $text, $matches)) {
            $sessionCode = trim($matches[1]);
            $replyBody   = trim($matches[2]);

            $liveChat = LiveChat::where('session_code', $sessionCode)->first();

            if ($liveChat) {
                $liveChat->update(['admin_typing_until' => null]);

                $savedMsg = LiveChatMessage::create([
                    'live_chat_id'        => $liveChat->id,
                    'sender'              => 'admin',
                    'message'             => $replyBody,
                    'telegram_message_id' => $messageId,
                ]);

                // Kirim konfirmasi balik ke Telegram Admin via CS Bot
                TelegramService::sendCsMessage(
                    "✅ <b>Balasan Terkirim</b> ke <b>{$liveChat->user_name}</b> (<code>{$liveChat->session_code}</code>):\n<i>\"" . htmlspecialchars($replyBody) . "\"</i>",
                    $chatId
                );

                return response()->json(['status' => 'ok', 'method' => 'command_reply']);
            } else {
                TelegramService::sendCsMessage(
                    "❌ <b>Sesi Tidak Ditemukan:</b> Kode sesi <code>{$sessionCode}</code> tidak valid.",
                    $chatId
                );
                return response()->json(['status' => 'session_not_found']);
            }
        }

        // 3. Tangani Balasan via Fitur "Reply" Telegram
        if (isset($message['reply_to_message'])) {
            $replyTo = $message['reply_to_message'];
            $replyToMessageId = $replyTo['message_id'] ?? null;
            $replyToText = $replyTo['text'] ?? '';

            $liveChat = null;

            // Cari berdasarkan telegram_last_message_id atau telegram_message_id di pesan
            if ($replyToMessageId) {
                $liveChat = LiveChat::where('telegram_last_message_id', $replyToMessageId)->first();

                if (!$liveChat) {
                    $matchedMsg = LiveChatMessage::where('telegram_message_id', $replyToMessageId)->first();
                    if ($matchedMsg) {
                        $liveChat = $matchedMsg->liveChat;
                    }
                }
            }

            // Jika belum ketemu, cari session_code di teks pesan yang di-reply
            if (!$liveChat && preg_match('/ID Sesi:\s*([A-Za-z0-9\-]+)/i', $replyToText, $sessionMatches)) {
                $extractedCode = trim($sessionMatches[1]);
                $liveChat = LiveChat::where('session_code', $extractedCode)->first();
            }

            if ($liveChat) {
                if (in_array(strtolower($text), ['/t', '/typing', 'typing', 'sedang mengetik', '...', '.'])) {
                    $liveChat->update(['admin_typing_until' => now()->addSeconds(30)]);
                    TelegramService::sendCsMessage(
                        "✍️ <b>Status Typing Aktif</b> pada web <b>" . htmlspecialchars($liveChat->user_name) . "</b> (<code>{$liveChat->session_code}</code>) selama 30 detik.",
                        $chatId
                    );
                    return response()->json(['status' => 'ok', 'method' => 'reply_typing']);
                }

                $liveChat->update(['admin_typing_until' => null]);

                $savedMsg = LiveChatMessage::create([
                    'live_chat_id'        => $liveChat->id,
                    'sender'              => 'admin',
                    'message'             => $text,
                    'telegram_message_id' => $messageId,
                ]);

                // Konfirmasi ke admin di Telegram via CS Bot
                TelegramService::sendCsMessage(
                    "✅ <b>Pesan Balasan Diteruskan</b> ke web pengguna <b>" . htmlspecialchars($liveChat->user_name) . "</b> (<code>{$liveChat->session_code}</code>).",
                    $chatId
                );

                return response()->json(['status' => 'ok', 'method' => 'native_reply']);
            }
        }

        // 4. Pesan Langsung Tanpa Reply & Bukan Slash Command (Auto-Route jika ada 1 sesi aktif)
        if (!str_starts_with($text, '/')) {
            $activeChats = LiveChat::where('status', 'active')->orderBy('id', 'desc')->get();

            if ($activeChats->count() === 1) {
                $liveChat = $activeChats->first();
                $liveChat->update(['admin_typing_until' => null]);

                LiveChatMessage::create([
                    'live_chat_id'        => $liveChat->id,
                    'sender'              => 'admin',
                    'message'             => $text,
                    'telegram_message_id' => $messageId,
                ]);

                TelegramService::sendCsMessage(
                    "✅ <b>Pesan Diteruskan</b> ke <b>" . htmlspecialchars($liveChat->user_name) . "</b> (<code>{$liveChat->session_code}</code>):\n<i>\"" . htmlspecialchars($text) . "\"</i>",
                    $chatId
                );

                return response()->json(['status' => 'ok', 'method' => 'direct_single_active']);
            } elseif ($activeChats->count() > 1) {
                TelegramService::sendCsMessage(
                    "⚠️ <b>Terdapat " . $activeChats->count() . " Sesi Aktif:</b>\nMohon balas menggunakan fitur <b>Reply</b> Telegram pada pesan pengguna yang ingin dijawab, atau gunakan: <code>/reply [KODE_SESI] [Pesan]</code>",
                    $chatId
                );
                return response()->json(['status' => 'multiple_sessions_need_disambiguation']);
            }
        }

        // Jika pesan bukan reply dan bukan command reply
        if ($text === '/start' || $text === '/help') {
            $helpMsg = "🏛 <b>BOT CUSTOMER SERVICE LAB TKJ</b>\n"
                . "━━━━━━━━━━━━━━━━━━━━\n"
                . "Bot ini terhubung langsung dengan sistem live chat web LabSystem.\n\n"
                . "<b>Cara Membalas Pesan Pengguna:</b>\n"
                . "1. Tekan tombol <b>Reply</b> pada notifikasi pesan masuk dari web.\n"
                . "2. Atau ketik langsung pesan Anda (jika hanya ada 1 sesi aktif).\n"
                . "3. Atau gunakan perintah: <code>/reply [ID_SESI] [Pesan Anda]</code>\n"
                . "4. Untuk memicu animasi sedang mengetik di web: ketik <code>/t</code>\n"
                . "━━━━━━━━━━━━━━━━━━━━\n"
                . "<i>Pesan Anda akan otomatis tampil di layar browser pengguna secara realtime.</i>";

            TelegramService::sendCsMessage($helpMsg, $chatId);
            return response()->json(['status' => 'help_sent']);
        }

        return response()->json(['status' => 'ignored']);
    }

    /**
     * Endpoint untuk mempermudah pendaftaran Webhook via browser
     */
    public function setupWebhook(Request $request)
    {
        $webhookUrl = $request->query('url') ?: url('/api/telegram/webhook');
        $result = TelegramService::setWebhook($webhookUrl);

        return response()->json([
            'target_url' => $webhookUrl,
            'result'     => $result,
        ]);
    }
}
