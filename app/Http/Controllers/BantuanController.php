<?php

namespace App\Http\Controllers;

use App\Models\LiveChat;
use App\Models\LiveChatMessage;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BantuanController extends Controller
{
    /**
     * Tampilkan halaman Bantuan / Customer Service Chatbot.
     */
    public function index()
    {
        $activeSession = null;
        if (Auth::check()) {
            $activeSession = LiveChat::where('user_id', Auth::id())
                ->where('status', 'active')
                ->latest()
                ->first();
        }

        return view('bantuan.index', [
            'initialCsSession' => $activeSession ? $activeSession->session_code : null,
        ]);
    }

    /**
     * Memulai Sesi Live Chat dengan Customer Service (Admin)
     */
    public function startCsSession(Request $request)
    {
        $user = Auth::user();
        
        $sessionCode = 'CS-' . date('Ymd') . '-' . strtoupper(Str::random(5));

        $chat = LiveChat::create([
            'session_code' => $sessionCode,
            'user_id'      => $user ? $user->id : null,
            'user_name'    => $user ? $user->name : ($request->input('name') ?: 'Tamu'),
            'user_email'   => $user ? $user->email : ($request->input('email') ?: '-'),
            'user_role'    => $user ? $user->role : 'Tamu',
            'status'       => 'active',
        ]);

        // Catat pesan sambutan CS otomatis
        $greeting = LiveChatMessage::create([
            'live_chat_id' => $chat->id,
            'sender'       => 'system',
            'message'      => 'Sesi Customer Service langsung telah aktif. Silakan tuliskan pertanyaan atau kendala Anda. Admin pengelola lab akan membalas pesan Anda sesegera mungkin.',
            'is_read'      => true,
        ]);

        return response()->json([
            'status'       => 'success',
            'session_code' => $chat->session_code,
            'created_at'   => $chat->created_at->format('H:i'),
        ]);
    }

    /**
     * Mengirim pesan dari pengguna ke Customer Service (Telegram Admin)
     */
    public function sendCsMessage(Request $request)
    {
        $request->validate([
            'session_code' => 'required|string',
            'message'      => 'required|string|max:1000',
        ]);

        $chat = LiveChat::where('session_code', $request->session_code)
            ->where('status', 'active')
            ->first();

        if (!$chat) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Sesi chat tidak ditemukan atau telah ditutup.',
            ], 404);
        }

        $msgText = trim($request->input('message'));

        $chatMessage = LiveChatMessage::create([
            'live_chat_id' => $chat->id,
            'sender'       => 'user',
            'message'      => $msgText,
            'is_read'      => false,
        ]);

        // Aktifkan indikator pengetikan CS sementara selagi pesan dikirim ke Telegram
        $chat->update([
            'admin_typing_until' => now()->addSeconds(15)
        ]);

        // Teruskan ke Telegram Bot Admin
        $telegramResult = TelegramService::sendLiveChatMessageToAdmin($chat, $chatMessage);

        return response()->json([
            'status'     => 'success',
            'message_id' => $chatMessage->id,
            'time'       => $chatMessage->created_at->format('H:i'),
            'telegram'   => $telegramResult['ok'] ?? false,
        ]);
    }

    /**
     * Polling pesan baru dari Customer Service / Admin
     */
    public function pollMessages(Request $request)
    {
        $sessionCode = $request->query('session_code');
        $lastId      = (int) $request->query('last_id', 0);

        if (empty($sessionCode)) {
            return response()->json(['messages' => []]);
        }

        // Auto-Sync balasan dari Telegram Bot (Sangat efektif di Localhost & Hosting)
        try {
            TelegramService::processIncomingTelegramUpdates();
        } catch (\Throwable $e) {
            // Abaikan kegagalan jaringan sementara agar polling tidak error
        }

        $chat = LiveChat::where('session_code', $sessionCode)->first();

        if (!$chat) {
            return response()->json(['messages' => [], 'status' => 'not_found']);
        }

        $newMessages = LiveChatMessage::where('live_chat_id', $chat->id)
            ->where('id', '>', $lastId)
            ->where('sender', '!=', 'user') // Ambil pesan dari admin atau sistem
            ->orderBy('id', 'asc')
            ->get();

        // Tandai sudah dibaca
        LiveChatMessage::where('live_chat_id', $chat->id)
            ->where('id', '>', $lastId)
            ->update(['is_read' => true]);

        $formatted = $newMessages->map(function ($m) {
            return [
                'id'        => $m->id,
                'sender'    => $m->sender === 'admin' ? 'admin' : 'system',
                'text'      => htmlspecialchars($m->message),
                'time'      => $m->created_at->format('H:i'),
                'is_system' => $m->sender === 'system',
            ];
        });

        $isAdminTyping = false;
        if ($chat->admin_typing_until && now()->lt($chat->admin_typing_until)) {
            $isAdminTyping = true;
        }

        return response()->json([
            'status'      => $chat->status,
            'is_typing'   => $isAdminTyping,
            'messages'    => $formatted,
            'sessionCode' => $chat->session_code,
        ]);
    }

    /**
     * Mengakhiri sesi obrolan Customer Service
     */
    public function closeCsSession(Request $request)
    {
        $sessionCode = $request->input('session_code');

        $chat = LiveChat::where('session_code', $sessionCode)->first();

        if ($chat) {
            $chat->update([
                'status'             => 'closed',
                'admin_typing_until' => null,
            ]);

            LiveChatMessage::create([
                'live_chat_id' => $chat->id,
                'sender'       => 'system',
                'message'      => 'Sesi Customer Service telah diakhiri. Terima kasih telah menghubungi pengelola laboratorium.',
                'is_read'      => true,
            ]);

            // Kirim notifikasi ke Telegram Admin bahwa sesi telah ditutup
            try {
                date_default_timezone_set('Asia/Jakarta');
                $waktu = date('d-m-Y H:i:s') . ' WIB';
                $senderName = $chat->user_name ?: ($chat->user->name ?? 'Pengguna');
                $senderRole = strtoupper($chat->user_role ?: ($chat->user->role ?? 'Siswa'));

                $closeMsg = "🔴 <b>SESI LIVE CHAT DITUTUP PENGGUNA</b>\n"
                    . "━━━━━━━━━━━━━━━━━━━━\n"
                    . "🏷 <b>ID Sesi:</b> <code>{$chat->session_code}</code>\n"
                    . "👤 <b>Pengguna:</b> " . htmlspecialchars($senderName) . " (" . htmlspecialchars($senderRole) . ")\n"
                    . "🕒 <b>Waktu Tutup:</b> {$waktu}\n"
                    . "━━━━━━━━━━━━━━━━━━━━\n"
                    . "<i>Pengguna telah mengakhiri sesi obrolan Customer Service.</i>";

                TelegramService::sendCsMessage($closeMsg);
            } catch (\Throwable $e) {
                // Jangan gagalkan response jika Telegram error
            }
        }

        return response()->json(['status' => 'closed']);
    }
}