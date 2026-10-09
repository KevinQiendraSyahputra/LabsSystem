<?php

namespace App\Services;

use App\Models\LiveChat;
use App\Models\LiveChatMessage;
use Illuminate\Support\Facades\Log;

class DiscordService
{
    /**
     * Dapatkan konfigurasi Discord Bot
     */
    public static function getConfig(): array
    {
        return [
            'bot_token'      => config('services.discord.bot_token') ?: env('DISCORD_BOT_TOKEN', ''),
            'channel_id'     => config('services.discord.channel_id') ?: env('DISCORD_CHANNEL_ID', ''),
            'log_channel_id' => config('services.discord.log_channel_id') ?: env('DISCORD_LOG_CHANNEL_ID', ''),
            'enabled'        => (bool) (config('services.discord.enabled') ?? env('DISCORD_ENABLED', false)),
            'api_url'        => 'https://discord.com/api/v10',
        ];
    }

    /**
     * Daftar Anycast IP Cloudflare Discord untuk bypass batasan DNS resolver InfinityFree/Shared Hosting
     */
    public static function getDiscordResolves(): array
    {
        return [
            'discord.com:443:162.159.138.232',
            'discord.com:443:162.159.137.232',
            'discord.com:443:162.159.136.232',
            'discord.com:443:162.159.135.232',
            'discord.com:443:162.159.130.232',
            'discord.com:443:162.159.129.232',
            'discord.com:443:162.159.128.232',
        ];
    }

    /**
     * Kirim HTTP Request ke Discord REST API (dengan resilient fallback)
     */
    public static function sendRequest(string $method, string $endpoint, array $payload = []): array
    {
        $config = self::getConfig();
        $token  = trim($config['bot_token']);

        if (empty($token)) {
            return ['ok' => false, 'error' => 'Token Bot Discord belum dikonfigurasi.'];
        }

        $url = $config['api_url'] . '/' . ltrim($endpoint, '/');
        $method = strtoupper($method);

        $headers = [
            'Authorization: Bot ' . $token,
            'Content-Type: application/json',
            'User-Agent: DiscordBot (LabSystem, 1.0.0)',
        ];

        // 1. Eksekusi request dengan DNS resolve biasa
        $execCurl = function (bool $useResolve) use ($url, $method, $headers, $payload) {
            $ch = curl_init($url);
            $options = [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST  => $method,
                CURLOPT_HTTPHEADER     => $headers,
                CURLOPT_TIMEOUT        => 15,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4,
            ];

            if ($useResolve) {
                $options[CURLOPT_RESOLVE] = self::getDiscordResolves();
            }

            if (!empty($payload) && in_array($method, ['POST', 'PATCH', 'PUT'])) {
                $options[CURLOPT_POSTFIELDS] = json_encode($payload);
            }

            curl_setopt_array($ch, $options);
            $resp = curl_exec($ch);
            $err  = curl_error($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            return [$resp, $err, $code];
        };

        list($resp, $err, $code) = $execCurl(false);

        // Jika gagal koneksi (misal DNS timeout), coba fallback dengan IP Cloudflare Discord
        if (($code === 0 || $code >= 500 || !empty($err)) && empty($resp)) {
            list($resp, $err, $code) = $execCurl(true);
        }

        // Fallback ke-3: Stream Context (file_get_contents) jika cURL diblokir oleh environment hosting
        if (($code === 0 || empty($resp)) && !empty($err)) {
            try {
                $streamHeaders = $headers;
                $streamOpts = [
                    'http' => [
                        'method'        => $method,
                        'header'        => implode("\r\n", $streamHeaders),
                        'timeout'       => 15,
                        'ignore_errors' => true,
                    ],
                    'ssl' => [
                        'verify_peer'      => false,
                        'verify_peer_name' => false,
                    ]
                ];
                if (!empty($payload) && in_array($method, ['POST', 'PATCH', 'PUT'])) {
                    $streamOpts['http']['content'] = json_encode($payload);
                }
                $context = stream_context_create($streamOpts);
                $streamResp = @file_get_contents($url, false, $context);
                if ($streamResp !== false && !empty($streamResp)) {
                    $resp = $streamResp;
                    $code = 200;
                    $err = null;
                }
            } catch (\Throwable $e) {}
        }

        $json = null;
        if ($resp) {
            $json = json_decode($resp, true);
        }

        $isOk = ($code >= 200 && $code < 300);

        return [
            'ok'    => $isOk,
            'http'  => $code,
            'error' => $err ?: ($json['message'] ?? null),
            'json'  => $json,
        ];
    }

    /**
     * Buat Channel Tiket Baru (Dedicated Text Channel) di Discord saat Sesi CS Dimulai
     */
    public static function createTicketThread(LiveChat $chat): array
    {
        $config = self::getConfig();
        if (!$config['enabled'] || empty($config['channel_id'])) {
            return ['ok' => false, 'error' => 'Discord CS integration disabled.'];
        }

        // Jika channel sudah pernah dibuat
        if (!empty($chat->discord_channel_id)) {
            return ['ok' => true, 'channel_id' => $chat->discord_channel_id];
        }

        $baseChannelId = $config['channel_id'];

        $senderName  = $chat->user_name ?: ($chat->user->name ?? 'Pengguna');
        $senderRole  = strtoupper($chat->user_role ?: ($chat->user->role ?? 'Siswa'));
        $senderEmail = $chat->user_email ?: ($chat->user->email ?? '-');

        // Bersihkan nama channel untuk format Discord: ticket-nama-kodependek
        $cleanName = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $senderName));
        if (empty($cleanName)) {
            $cleanName = 'user';
        }
        $codeParts = explode('-', $chat->session_code);
        $shortCode = strtolower(end($codeParts));
        $channelName = 'ticket-' . substr($cleanName, 0, 12) . '-' . $shortCode;

        // 1. Dapatkan informasi Guild ID dan Category ID dari DISCORD_CHANNEL_ID
        $channelInfo = self::sendRequest('GET', "channels/{$baseChannelId}");
        $guildId = $channelInfo['json']['guild_id'] ?? null;
        $categoryId = $channelInfo['json']['parent_id'] ?? null;

        // Jika base channel itu sendiri adalah Category (type 4)
        if (!empty($channelInfo['json']['type']) && $channelInfo['json']['type'] === 4) {
            $categoryId = $baseChannelId;
        }

        $newChannelId = null;

        // 2. Buat Dedicated Text Channel di Server Discord (Type 0 = GUILD_TEXT)
        if ($guildId) {
            $channelPayload = [
                'name'      => $channelName,
                'type'      => 0, // Text Channel
                'topic'     => "Tiket CS: {$chat->session_code} | Pengguna: {$senderName} ({$senderRole})",
            ];

            if ($categoryId) {
                $channelPayload['parent_id'] = $categoryId;
            }

            $createRes = self::sendRequest('POST', "guilds/{$guildId}/channels", $channelPayload);

            if ($createRes['ok'] && !empty($createRes['json']['id'])) {
                $newChannelId = $createRes['json']['id'];
            }
        }

        // Fallback: Jika gagal membuat channel guild (misal butuh permission Manage Channels), buat Thread
        if (!$newChannelId) {
            $threadPayload = [
                'name'                  => $channelName,
                'auto_archive_duration' => 1440,
                'type'                  => 11,
            ];
            $threadRes = self::sendRequest('POST', "channels/{$baseChannelId}/threads", $threadPayload);
            if ($threadRes['ok'] && !empty($threadRes['json']['id'])) {
                $newChannelId = $threadRes['json']['id'];
            }
        }

        if ($newChannelId) {
            $chat->update(['discord_channel_id' => $newChannelId]);

            // Kirim Pesan Sambutan & Kartu Tiket Pertama ke Channel Baru
            $welcomePayload = [
                'content' => "**TIKET BANTUAN CUSTOMER SERVICE BARU**\nSesi obrolan baru telah dibuka dari antarmuka web.",
                'embeds'  => [
                    [
                        'title'       => "Sesi Konsultasi Laboratorium TKJ",
                        'description' => "Pengguna telah memulai sesi obrolan Customer Service dari website.\nKetik balasan Anda langsung di channel ini untuk menjawab pengguna secara real-time.",
                        'color'       => 0x4F46E5, // Indigo
                        'fields'      => [
                            ['name' => 'Kode Sesi', 'value' => '`' . $chat->session_code . '`', 'inline' => true],
                            ['name' => 'Nama Pengguna', 'value' => $senderName, 'inline' => true],
                            ['name' => 'Peran / Email', 'value' => "{$senderRole} ({$senderEmail})", 'inline' => false],
                        ],
                        'footer'      => ['text' => 'Ketik /t untuk status mengetik • Ketik /close untuk menyelesaikan & menutup tiket'],
                        'timestamp'   => now()->toIso8601String(),
                    ]
                ]
            ];

            self::sendRequest('POST', "channels/{$newChannelId}/messages", $welcomePayload);

            return ['ok' => true, 'channel_id' => $newChannelId];
        }

        return ['ok' => false, 'error' => 'Gagal membuat channel tiket Discord. Pastikan bot memiliki izin Manage Channels.'];
    }

    /**
     * Teruskan Pesan dari Pengguna Web ke Thread Tiket Discord
     */
    public static function sendTicketMessage(LiveChat $chat, LiveChatMessage $message): array
    {
        $config = self::getConfig();
        if (!$config['enabled']) {
            return ['ok' => false, 'error' => 'Discord CS disabled.'];
        }

        // Pastikan Channel Tiket sudah ada, jika belum ada buat sekali saja
        if (empty($chat->discord_channel_id)) {
            self::createTicketThread($chat);
            $chat->refresh();
        }

        $targetChannelId = $chat->discord_channel_id;
        if (empty($targetChannelId)) {
            $targetChannelId = $config['channel_id'];
        }

        if (empty($targetChannelId)) {
            return ['ok' => false, 'error' => 'Channel Discord tidak valid.'];
        }

        $senderName = $chat->user_name ?: ($chat->user->name ?? 'Pengguna');
        $senderRole = strtoupper($chat->user_role ?: ($chat->user->role ?? 'Siswa'));

        $payload = [
            'content' => "**[" . $senderName . " (" . $senderRole . ")]**: " . $message->message,
            'embeds'  => [
                [
                    'author'      => ['name' => "{$senderName} ({$senderRole})"],
                    'description' => $message->message,
                    'color'       => 0x2563EB, // Blue
                    'footer'      => ['text' => "Pesan Pengguna • Sesi {$chat->session_code}"],
                    'timestamp'   => $message->created_at ? $message->created_at->toIso8601String() : now()->toIso8601String(),
                ]
            ]
        ];

        $res = self::sendRequest('POST', "channels/{$targetChannelId}/messages", $payload);

        if ($res['ok'] && !empty($res['json']['id'])) {
            $discordMsgId = (string) $res['json']['id'];
            $message->update(['discord_message_id' => $discordMsgId]);
            $chat->update(['discord_last_message_id' => $discordMsgId]);
        }

        return $res;
    }

    /**
     * Tambahkan Reaction (Centang) pada Pesan Discord
     */
    public static function addReaction(string $channelId, string $messageId, string $emoji = '%E2%9C%85'): array
    {
        $encodedEmoji = urlencode(urldecode($emoji));
        return self::sendRequest('PUT', "channels/{$channelId}/messages/{$messageId}/reactions/{$encodedEmoji}/@me");
    }

    /**
     * Sinkronisasi Pesan Balasan yang Diketik Admin di Discord
     */
    public static function processIncomingDiscordUpdates(?string $targetSessionCode = null): array
    {
        $config = self::getConfig();
        if (!$config['enabled'] || empty($config['bot_token'])) {
            return [];
        }

        $query = LiveChat::where('status', 'active');
        if (!empty($targetSessionCode)) {
            $query->where('session_code', $targetSessionCode);
        } else {
            $query->orderBy('id', 'desc');
        }

        $activeChats = $query->get();
        if ($activeChats->isEmpty()) {
            return [];
        }

        $processed = [];
        $baseChannelId = $config['channel_id'];

        foreach ($activeChats as $chat) {
            $channelId = $chat->discord_channel_id;

            // Jika discord_channel_id kosong di DB, cari channel atau thread di Discord
            if (empty($channelId) && !empty($baseChannelId)) {
                try {
                    $channelInfo = self::sendRequest('GET', "channels/{$baseChannelId}");
                    $guildId = $channelInfo['json']['guild_id'] ?? null;
                    if ($guildId) {
                        $codeParts = explode('-', $chat->session_code);
                        $shortCode = strtolower(end($codeParts));

                        // 1. Cari di list channels guild
                        $guildChannels = self::sendRequest('GET', "guilds/{$guildId}/channels");
                        if ($guildChannels['ok'] && is_array($guildChannels['json'])) {
                            foreach ($guildChannels['json'] as $c) {
                                if (!empty($c['name']) && str_contains(strtolower($c['name']), $shortCode)) {
                                    $channelId = $c['id'];
                                    $chat->update(['discord_channel_id' => $channelId]);
                                    break;
                                }
                            }
                        }

                        // 2. Jika belum ketemu, cari di active threads guild
                        if (empty($channelId)) {
                            $activeThreads = self::sendRequest('GET', "guilds/{$guildId}/threads/active");
                            if ($activeThreads['ok'] && !empty($activeThreads['json']['threads']) && is_array($activeThreads['json']['threads'])) {
                                foreach ($activeThreads['json']['threads'] as $t) {
                                    if (!empty($t['name']) && str_contains(strtolower($t['name']), $shortCode)) {
                                        $channelId = $t['id'];
                                        $chat->update(['discord_channel_id' => $channelId]);
                                        break;
                                    }
                                }
                            }
                        }
                    }
                } catch (\Throwable $e) {}
            }

            if (empty($channelId)) {
                continue;
            }

            $res = self::sendRequest('GET', "channels/{$channelId}/messages?limit=25");

            if (!$res['ok'] || empty($res['json']) || !is_array($res['json'])) {
                continue;
            }

            $messages = array_reverse($res['json']); // Urutkan dari terlama ke terbaru

            foreach ($messages as $m) {
                $msgId = (string) ($m['id'] ?? '');
                $author = $m['author'] ?? [];
                $isBot = !empty($author['bot']);
                $content = trim($m['content'] ?? '');

                // Lewati pesan bot atau pesan kosong
                if ($isBot || empty($content)) {
                    continue;
                }

                // Lewati jika pesan ini sudah pernah disimpan di basis data
                if (LiveChatMessage::where('discord_message_id', $msgId)->exists()) {
                    continue;
                }

                $cleanContent = strtolower(trim($content));

                // 1. Tangani Command Typing di Discord: /typing atau /t
                if (in_array($cleanContent, ['/t', 't', '!t', '/typing', 'typing', 'sedang mengetik', '...', '.'])) {
                    $chat->update(['admin_typing_until' => now()->addSeconds(30)]);
                    LiveChatMessage::create([
                        'live_chat_id'       => $chat->id,
                        'sender'             => 'system',
                        'message'            => 'Admin sedang mengetik balasan...',
                        'discord_message_id' => $msgId,
                        'is_read'            => false,
                    ]);
                    // Beri reaction centang pada pesan command di Discord
                    self::addReaction($channelId, $msgId, '%E2%9C%85');
                    continue;
                }

                $hasAdminNameCol = \Illuminate\Support\Facades\Schema::hasColumn('live_chats', 'admin_name');
                $hasSenderNameCol = \Illuminate\Support\Facades\Schema::hasColumn('live_chat_messages', 'sender_name');

                // 2. Tangani Command Tutup Sesi di Discord: /close, close, !close, /tutup, /selesai, dll.
                $isCloseCmd = in_array($cleanContent, ['/close', 'close', '!close', '.close', 'close/', '/tutup', 'tutup', '!tutup', '/selesai', 'selesai', '/end', 'end'])
                    || preg_match('/^[\/!\.]?(close|tutup|selesai|end)\b/i', $cleanContent);

                if ($isCloseCmd) {
                    $adminName = !empty($author['global_name']) ? trim($author['global_name']) : (!empty($author['username']) ? trim($author['username']) : 'Admin Discord');

                    $closePayload = ['status' => 'closed', 'admin_typing_until' => null];
                    if ($hasAdminNameCol) {
                        $closePayload['admin_name'] = $adminName;
                    }
                    $chat->update($closePayload);

                    LiveChatMessage::create([
                        'live_chat_id'       => $chat->id,
                        'sender'             => 'system',
                        'message'            => 'Sesi Customer Service telah diselesaikan dan ditutup oleh Admin CS (' . $adminName . ').',
                        'discord_message_id' => $msgId,
                        'is_read'            => false,
                    ]);

                    // Beri reaction centang pada pesan /close di Discord
                    self::addReaction($channelId, $msgId, '%E2%9C%85');

                    self::closeTicketThread($chat, 'Admin Discord (' . $adminName . ')');
                    break;
                }

                // 3. Simpan Pesan Balasan Admin ke Pengguna Web
                $adminName = !empty($author['global_name']) ? trim($author['global_name']) : (!empty($author['username']) ? trim($author['username']) : 'Admin');
                $chatUpdate = ['admin_typing_until' => null];
                if ($hasAdminNameCol) {
                    $chatUpdate['admin_name'] = $adminName;
                }
                $chat->update($chatUpdate);

                $msgPayload = [
                    'live_chat_id'       => $chat->id,
                    'sender'             => 'admin',
                    'message'            => $content,
                    'discord_message_id' => $msgId,
                    'is_read'            => false,
                ];
                if ($hasSenderNameCol) {
                    $msgPayload['sender_name'] = $adminName;
                }

                $saved = LiveChatMessage::create($msgPayload);

                // Beri reaction centang pada pesan balasan admin di Discord untuk konfirmasi terkirim ke web
                self::addReaction($channelId, $msgId, '%E2%9C%85');

                $processed[] = $saved->id;
            }
        }

        return $processed;
    }

    /**
     * Kirim Multipart HTTP Request ke Discord API (untuk File Attachment & Payload JSON)
     */
    public static function sendMultipartRequest(string $endpoint, array $payload, array $files = []): array
    {
        $config = self::getConfig();
        $token  = trim($config['bot_token']);

        if (empty($token)) {
            return ['ok' => false, 'error' => 'Token Bot Discord belum dikonfigurasi.'];
        }

        $url = $config['api_url'] . '/' . ltrim($endpoint, '/');

        $headers = [
            'Authorization: Bot ' . $token,
            'User-Agent: DiscordBot (InventarisSystem, 1.0.0)',
        ];

        $postFields = [
            'payload_json' => json_encode($payload),
        ];

        foreach ($files as $index => $file) {
            if (!empty($file['path']) && file_exists($file['path'])) {
                $postFields["files[{$index}]"] = new \CURLFile(
                    $file['path'],
                    $file['mime'] ?? 'text/html',
                    $file['name'] ?? "file_{$index}.html"
                );
            }
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $postFields,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4,
            CURLOPT_RESOLVE        => self::getDiscordResolves(),
        ]);

        $resp = curl_exec($ch);
        $err  = curl_error($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $json = null;
        if ($resp) {
            $json = json_decode($resp, true);
        }

        $isOk = ($code >= 200 && $code < 300);

        return [
            'ok'    => $isOk,
            'http'  => $code,
            'error' => $err ?: ($json['message'] ?? null),
            'json'  => $json,
        ];
    }

    /**
     * Generate File HTML Transkrip Standalone
     */
    public static function generateStandaloneHtmlTranscript(LiveChat $chat, $messages): string
    {
        $senderName  = htmlspecialchars($chat->user_name ?: ($chat->user->name ?? 'Pengguna'));
        $senderRole  = strtoupper(htmlspecialchars($chat->user_role ?: ($chat->user->role ?? 'Siswa')));
        $senderEmail = htmlspecialchars($chat->user_email ?: ($chat->user->email ?? '-'));
        $waktuBuka   = $chat->created_at ? $chat->created_at->format('d F Y, H:i:s') . ' WIB' : '-';
        $waktuTutup  = date('d F Y, H:i:s') . ' WIB';
        $totalPesan  = $messages->count();

        $rows = '';
        foreach ($messages as $m) {
            $time = $m->created_at ? $m->created_at->format('H:i') : '--:--';
            $msgContent = nl2br(htmlspecialchars($m->message));

            if ($m->sender === 'system') {
                $rows .= "<div style='display:flex;justify-content:center;margin:12px 0;'><div style='background:#f1f5f9;color:#475569;font-size:12px;padding:6px 14px;border-radius:9999px;border:1px solid #e2e8f0;text-align:center;'><strong>Sistem:</strong> {$msgContent} <span style='font-size:10px;color:#94a3b8;margin-left:4px;'>{$time}</span></div></div>";
            } elseif ($m->sender === 'user') {
                $rows .= "<div style='display:flex;flex-direction:column;align-items:flex-end;margin-bottom:12px;'><div style='max-width:75%;background:#2563eb;color:#ffffff;padding:12px 16px;border-radius:16px 16px 4px 16px;box-shadow:0 1px 2px rgba(0,0,0,0.05);'><div style='display:flex;justify-content:space-between;font-size:11px;color:#bfdbfe;border-bottom:1px solid rgba(255,255,255,0.2);padding-bottom:4px;margin-bottom:6px;'><span>{$senderName}</span><span>{$time}</span></div><div style='font-size:13px;line-height:1.5;'>{$msgContent}</div></div></div>";
            } else {
                $rows .= "<div style='display:flex;flex-direction:column;align-items:flex-start;margin-bottom:12px;'><div style='max-width:75%;background:#f8fafc;color:#1e293b;border:1px solid #e2e8f0;padding:12px 16px;border-radius:16px 16px 16px 4px;box-shadow:0 1px 2px rgba(0,0,0,0.05);'><div style='display:flex;justify-content:space-between;font-size:11px;color:#4f46e5;font-weight:bold;border-bottom:1px solid #e2e8f0;padding-bottom:4px;margin-bottom:6px;'><span>Admin Customer Service</span><span style='color:#94a3b8;font-weight:normal;'>{$time}</span></div><div style='font-size:13px;line-height:1.5;'>{$msgContent}</div></div></div>";
            }
        }

        return <<<HTML
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transkrip Tiket CS — {$chat->session_code}</title>
    <style>
        body { font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f8fafc; color: #1e293b; margin: 0; padding: 24px 16px; display: flex; justify-content: center; }
        .card { max-width: 800px; width: 100%; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        .header { border-bottom: 1px solid #f1f5f9; padding-bottom: 16px; margin-bottom: 20px; }
        .title { font-size: 20px; font-weight: 700; margin: 0 0 4px 0; color: #0f172a; }
        .badge { display: inline-block; padding: 4px 10px; background: #ecfdf5; color: #065f46; font-size: 11px; font-weight: 700; border-radius: 9999px; margin-bottom: 8px; border: 1px solid #a7f3d0; }
        .meta-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; margin-bottom: 24px; font-size: 13px; }
        @media (max-width: 600px) { .meta-grid { grid-template-columns: 1fr; } }
        .chat-box { border-top: 1px solid #f1f5f9; padding-top: 20px; }
        .footer { text-align: center; font-size: 11px; color: #94a3b8; border-top: 1px solid #f1f5f9; padding-top: 16px; margin-top: 24px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <span class="badge">SESI TIKET SELESAI</span>
            <h1 class="title">Transkrip Percakapan Customer Service</h1>
            <p style="margin:0;font-size:12px;color:#64748b;">Arsip resmi layanan konsultasi Laboratorium TKJ</p>
        </div>
        <div class="meta-grid">
            <div>
                <p style="margin:0 0 6px 0;font-weight:bold;color:#475569;font-size:11px;text-transform:uppercase;">Informasi Pengguna</p>
                <p style="margin:0 0 4px 0;"><strong>Nama:</strong> {$senderName}</p>
                <p style="margin:0 0 4px 0;"><strong>Peran:</strong> {$senderRole}</p>
                <p style="margin:0;"><strong>Email:</strong> {$senderEmail}</p>
            </div>
            <div>
                <p style="margin:0 0 6px 0;font-weight:bold;color:#475569;font-size:11px;text-transform:uppercase;">Informasi Sesi</p>
                <p style="margin:0 0 4px 0;"><strong>Kode Sesi:</strong> <code>{$chat->session_code}</code></p>
                <p style="margin:0 0 4px 0;"><strong>Waktu Buka:</strong> {$waktuBuka}</p>
                <p style="margin:0;"><strong>Waktu Selesai:</strong> {$waktuTutup}</p>
            </div>
        </div>
        <div class="chat-box">
            <h3 style="font-size:14px;font-weight:bold;margin:0 0 16px 0;color:#334155;">Riwayat Percakapan ({$totalPesan} Pesan)</h3>
            {$rows}
        </div>
        <div class="footer">
            Dokumen ini diarsipkan secara otomatis oleh Sistem Inventaris & Laboratorium TKJ.
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Kirim Ringkasan Transkrip / Log Tiket ke Channel Log Discord (Gaya Modern Tickety / Ticket Tool)
     */
    public static function sendLogTicket(LiveChat $chat, string $closedBy = 'Pengguna (Web)'): array
    {
        $config = self::getConfig();
        $logChannelId = $config['log_channel_id'];

        if (!$config['enabled'] || empty($logChannelId)) {
            return ['ok' => false, 'error' => 'Log channel ID not configured.'];
        }

        date_default_timezone_set('Asia/Jakarta');
        $waktuBuka  = $chat->created_at ? $chat->created_at->format('l, d F Y H:i') . ' WIB' : '-';
        $waktuTutup = date('l, d F Y H:i') . ' WIB';

        $senderName  = $chat->user_name ?: ($chat->user->name ?? 'Pengguna');
        $senderRole  = strtoupper($chat->user_role ?: ($chat->user->role ?? 'Siswa'));
        $senderEmail = $chat->user_email ?: ($chat->user->email ?? '-');

        // Bersihkan nama channel tiket
        $cleanName = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $senderName));
        if (empty($cleanName)) {
            $cleanName = 'user';
        }
        $codeParts = explode('-', $chat->session_code);
        $shortCode = strtolower(end($codeParts));
        $ticketName = 'ticket-' . substr($cleanName, 0, 12) . '-' . $shortCode;

        // Ambil seluruh riwayat percakapan sesi ini
        $messages = collect([]);
        if (!empty($chat->id)) {
            try {
                $messages = LiveChatMessage::where('live_chat_id', $chat->id)
                    ->orderBy('id', 'asc')
                    ->get();
            } catch (\Throwable $e) {}
        }

        $totalPesan = $messages->count();

        // Buat Deskripsi Blockquote Rapi persis seperti gambar Tickety
        $description = "**Ticket Information**\n"
            . "> **Ticket Name:** `{$ticketName}`\n"
            . "> **Ticket ID:** `{$chat->session_code}`\n"
            . "> **Created At:** {$waktuBuka}\n"
            . "> **Closed At:** {$waktuTutup}\n"
            . "> **Closed By:** {$closedBy}\n\n"
            . "**User Information**\n"
            . "> **Name:** {$senderName}\n"
            . "> **Role:** {$senderRole}\n"
            . "> **Email:** {$senderEmail}\n"
            . "> **Total Messages:** {$totalPesan} Pesan";

        $transcriptUrl = url('/bantuan/transcript/' . $chat->session_code);

        $embed = [
            'title'       => 'Ticket Closed',
            'description' => $description,
            'color'       => 0x3B82F6, // Blue
            'footer'      => [
                'text' => 'Inventaris Ticket System • ' . date('d/m/Y H:i:s')
            ],
            'timestamp'   => now()->toIso8601String(),
        ];

        $logPayload = [
            'embeds' => [$embed]
        ];

        // Pasang Button Link hanya jika URL valid
        if (filter_var($transcriptUrl, FILTER_VALIDATE_URL) && preg_match('/^https?:\/\//i', $transcriptUrl)) {
            $logPayload['components'] = [
                [
                    'type'       => 1, // Action Row
                    'components' => [
                        [
                            'type'  => 2, // Button
                            'style' => 5, // Link Button
                            'label' => 'View Transcript',
                            'url'   => $transcriptUrl,
                        ]
                    ]
                ]
            ];
        }

        // Siapkan direktori penyimpanan aman di storage Laravel (bebas open_basedir hosting)
        $tempDir = storage_path('app/temp');
        if (!file_exists($tempDir)) {
            @mkdir($tempDir, 0755, true);
        }
        $tempFile = $tempDir . '/transcript_' . md5($chat->session_code . microtime(true)) . '.html';

        $htmlContent = self::generateStandaloneHtmlTranscript($chat, $messages);
        @file_put_contents($tempFile, $htmlContent);

        $files = [];
        if (file_exists($tempFile) && filesize($tempFile) > 0) {
            $files[] = [
                'path' => $tempFile,
                'name' => "transcript-{$chat->session_code}.html",
                'mime' => 'text/html',
            ];
        }

        // Tingkat 1: Coba kirim via Multipart dengan File Attachment
        $res = ['ok' => false];
        if (!empty($files)) {
            try {
                $res = self::sendMultipartRequest("channels/{$logChannelId}/messages", $logPayload, $files);
            } catch (\Throwable $e) {}
        }

        // Tingkat 2 (Fallback): Jika multipart gagal, kirim via Standard POST JSON Embed
        if (!$res['ok']) {
            try {
                $res = self::sendRequest('POST', "channels/{$logChannelId}/messages", $logPayload);
            } catch (\Throwable $e) {}
        }

        // Tingkat 3 (Fallback Tanpa Component): Jika Discord menolak format button
        if (!$res['ok'] && isset($logPayload['components'])) {
            try {
                unset($logPayload['components']);
                $res = self::sendRequest('POST', "channels/{$logChannelId}/messages", $logPayload);
            } catch (\Throwable $e) {}
        }

        // Hapus file temporary
        if (file_exists($tempFile)) {
            @unlink($tempFile);
        }

        return $res;
    }

    /**
     * Tutup / Hapus Channel Tiket di Discord saat Sesi Selesai serta Kirim Log Transkrip
     */
    public static function closeTicketThread(LiveChat $chat, string $closedBy = 'Pengguna (Web)'): array
    {
        $config = self::getConfig();
        if (!$config['enabled']) {
            return ['ok' => false];
        }

        $channelId = $chat->discord_channel_id;
        $baseChannelId = $config['channel_id'];

        // 1. Jika channelId kosong di DB, cari channel di Discord Guild berdasarkan shortcode session
        if (empty($channelId) && !empty($baseChannelId)) {
            try {
                $channelInfo = self::sendRequest('GET', "channels/{$baseChannelId}");
                $guildId = $channelInfo['json']['guild_id'] ?? null;
                if ($guildId) {
                    $guildChannels = self::sendRequest('GET', "guilds/{$guildId}/channels");
                    if ($guildChannels['ok'] && is_array($guildChannels['json'])) {
                        $codeParts = explode('-', $chat->session_code);
                        $shortCode = strtolower(end($codeParts));
                        foreach ($guildChannels['json'] as $c) {
                            if (!empty($c['name']) && str_contains(strtolower($c['name']), $shortCode)) {
                                $channelId = $c['id'];
                                break;
                            }
                        }
                    }
                }
            } catch (\Throwable $e) {}
        }

        // 2. Kirim Log Transkrip Lengkap ke Channel Log Discord
        try {
            self::sendLogTicket($chat, $closedBy);
        } catch (\Throwable $e) {}

        // 3. Hapus Channel Tiket di Discord Secara Langsung
        if (!empty($channelId) && $channelId !== $baseChannelId) {
            try {
                $delRes = self::sendRequest('DELETE', "channels/{$channelId}");

                if (!$delRes['ok']) {
                    // Fallback jika berupa thread: arsipkan dan kunci
                    self::sendRequest('PATCH', "channels/{$channelId}", [
                        'archived' => true,
                        'locked'   => true,
                    ]);
                }
            } catch (\Throwable $e) {}
        }

        return ['ok' => true];
    }

    /**
     * Otomatis Menutup Tiket yang Ditinggalkan / Tidak Ada Percakapan (Default: 5 Menit)
     */
    public static function autoCloseInactiveTickets(int $inactiveMinutes = 5): int
    {
        $config = self::getConfig();
        if (!$config['enabled']) {
            return 0;
        }

        $cutoff = now()->subMinutes($inactiveMinutes);

        try {
            $activeChats = LiveChat::where('status', 'active')->get();
            $closedCount = 0;

            foreach ($activeChats as $chat) {
                // Periksa waktu pesan percakapan terakhir
                $lastMsg = LiveChatMessage::where('live_chat_id', $chat->id)
                    ->latest('id')
                    ->first();

                $lastActivity = $lastMsg ? $lastMsg->created_at : $chat->created_at;

                if ($lastActivity && $lastActivity <= $cutoff) {
                    $chat->update([
                        'status'             => 'closed',
                        'admin_typing_until' => null,
                    ]);

                    LiveChatMessage::create([
                        'live_chat_id' => $chat->id,
                        'sender'       => 'system',
                        'message'      => 'Sesi Customer Service telah ditutup otomatis oleh sistem karena tidak ada percakapan selama ' . $inactiveMinutes . ' menit.',
                        'is_read'      => true,
                    ]);

                    self::closeTicketThread($chat, 'Sistem (Inaktivitas ' . $inactiveMinutes . ' Menit)');
                    $closedCount++;
                }
            }

            return $closedCount;
        } catch (\Throwable $e) {
            return 0;
        }
    }
}

