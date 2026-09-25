<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class TelegramService
{
    /**
     * Load Telegram Bot Tokens & Admin Chat ID
     * - bot_token: Bot 1 untuk Notifikasi Transaksi (Peminjaman, Maintenance, Lupa Sandi)
     * - cs_bot_token: Bot 2 khusus untuk Live Chat Customer Service & Balasan User
     */
    public static function getConfig(): array
    {
        $defaultBotToken = env('TELEGRAM_BOT_TOKEN', '8953115545:AAF7946PyqdsjInhmdT3klreVSjVtaqEjhI');
        $csBotToken      = env('TELEGRAM_CS_BOT_TOKEN', $defaultBotToken);

        return [
            'bot_token'     => $defaultBotToken,
            'cs_bot_token'  => !empty($csBotToken) ? $csBotToken : $defaultBotToken,
            'admin_chat_id' => env('TELEGRAM_ADMIN_CHAT_ID', '6685668270'),
            'api_url'       => 'https://api.telegram.org/bot',
        ];
    }

    /**
     * Send HTTP POST Request to Telegram API (mendukung token dinamis untuk dual bot)
     */
    public static function sendRequest(string $method, array $payload, ?string $customToken = null): array
    {
        $config   = self::getConfig();
        $botToken = trim($customToken ?: $config['bot_token']);

        if (empty($botToken)) {
            return ['ok' => false, 'error' => 'Token Bot Telegram kosong.'];
        }

        $url  = $config['api_url'] . $botToken . '/' . $method;
        $resp = false;
        $err  = null;
        $code = 0;

        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            $options = [
                CURLOPT_POST           => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 15,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
                CURLOPT_POSTFIELDS     => json_encode($payload),
            ];

            if (defined('CURL_IPRESOLVE_V4')) {
                $options[CURLOPT_IPRESOLVE] = CURL_IPRESOLVE_V4;
            }

            curl_setopt_array($ch, $options);
            $resp = curl_exec($ch);
            $err  = curl_error($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            // Fallback: Jika DNS resolve error (seperti di free hosting), tembak langsung IP Telegram
            if (($resp === false || $code === 0) && ($err && stripos($err, 'resolve host') !== false)) {
                $ipUrl = str_replace('api.telegram.org', '149.154.167.220', $url);
                $chIp  = curl_init($ipUrl);
                $options[CURLOPT_HTTPHEADER] = ['Content-Type: application/json', 'Host: api.telegram.org'];
                curl_setopt_array($chIp, $options);
                $resp = curl_exec($chIp);
                $err  = curl_error($chIp);
                $code = curl_getinfo($chIp, CURLINFO_HTTP_CODE);
                curl_close($chIp);
            }
        }

        // Fallback: Jika cURL di-block hosting, gunakan file_get_contents
        if (($resp === false || $code === 0) && ini_get('allow_url_fopen')) {
            $httpContext = [
                'http' => [
                    'method'  => 'POST',
                    'header'  => "Content-Type: application/json\r\nHost: api.telegram.org\r\n",
                    'content' => json_encode($payload),
                    'timeout' => 15,
                    'ignore_errors' => true,
                ],
                'ssl' => [
                    'verify_peer'      => false,
                    'verify_peer_name' => false,
                ]
            ];
            $context = stream_context_create($httpContext);
            $resp    = @file_get_contents($url, false, $context);

            if ($resp === false) {
                $ipUrl = str_replace('api.telegram.org', '149.154.167.220', $url);
                $resp  = @file_get_contents($ipUrl, false, $context);
            }

            if ($resp !== false) {
                $err  = null;
                $code = 200;
            }
        }

        $json = null;
        if ($resp) {
            $json = json_decode($resp, true);
        }

        $isOk = !empty($json['ok']);

        if (!$isOk) {
            Log::error("Telegram API Error [{$method}]: " . ($err ?: ($json['description'] ?? 'Unknown Error')));
        }

        return [
            'ok'    => $isOk,
            'http'  => $code,
            'error' => $err ?: ($json['description'] ?? null),
            'raw'   => $resp,
            'json'  => $json,
        ];
    }

    /**
     * Kirim pesan teks sederhana
     */
    public static function sendMessage(string $message, ?string $chatId = null): array
    {
        $config       = self::getConfig();
        $targetChatId = $chatId ?: $config['admin_chat_id'];

        if (empty($targetChatId)) {
            return ['ok' => false, 'error' => 'TELEGRAM_ADMIN_CHAT_ID belum ditentukan.'];
        }

        $payload = [
            'chat_id'                  => (string)$targetChatId,
            'text'                     => $message,
            'parse_mode'               => 'HTML',
            'disable_web_page_preview' => true,
        ];

        return self::sendRequest('sendMessage', $payload);
    }

    /**
     * Kirim pesan teks khusus melalui Bot Customer Service
     */
    public static function sendCsMessage(string $message, ?string $chatId = null): array
    {
        $config       = self::getConfig();
        $targetChatId = $chatId ?: $config['admin_chat_id'];
        $csToken      = $config['cs_bot_token'];

        if (empty($targetChatId)) {
            return ['ok' => false, 'error' => 'TELEGRAM_ADMIN_CHAT_ID belum ditentukan.'];
        }

        $payload = [
            'chat_id'                  => (string)$targetChatId,
            'text'                     => $message,
            'parse_mode'               => 'HTML',
            'disable_web_page_preview' => true,
        ];

        return self::sendRequest('sendMessage', $payload, $csToken);
    }

    /**
     * Format Nama Barang + Detail Unit (contoh: "Router Mikrotik (Unit 2)")
     */
    public static function formatNamaBarangDenganUnit($peminjaman): string
    {
        $namaBarang = $peminjaman->barang->nama_barang ?? '-';
        $unitIndex = trim($peminjaman->unit_index ?? '');

        if (!empty($unitIndex)) {
            $indices = array_filter(array_map('trim', explode(',', $unitIndex)));
            if (!empty($indices)) {
                if (count($indices) === 1) {
                    $unitText = "Unit " . reset($indices);
                } else {
                    $unitText = "Unit " . implode(', Unit ', $indices);
                }
                return "{$namaBarang} ({$unitText})";
            }
        }

        return $namaBarang;
    }

    /**
     * Notifikasi Peminjaman Barang Baru
     */
    public static function sendPeminjamanBaru($peminjaman): array
    {
        $namaBarangWithUnit = self::formatNamaBarangDenganUnit($peminjaman);
        $satuan     = $peminjaman->barang->satuan ?? 'Unit';
        $tglPinjam  = date('d-m-Y', strtotime($peminjaman->tanggal_pinjam));
        $tglRencana = date('d-m-Y', strtotime($peminjaman->tanggal_kembali_rencana));

        $msg = "📢 <b>TRANSAKSI PEMINJAMAN ALAT BARU</b>\n"
            . "━━━━━━━━━━━━━━━━━━━━\n"
            . "🔑 <b>Kode Transaksi:</b> <code>{$peminjaman->kode_peminjaman}</code>\n"
            . "👤 <b>Nama Peminjam:</b> " . htmlspecialchars($peminjaman->nama_peminjam) . "\n"
            . "🏫 <b>Kelas / Jabatan:</b> " . htmlspecialchars($peminjaman->kelas_atau_jabatan ?: '-') . "\n"
            . "📱 <b>No. Kontak / WA:</b> <code>" . htmlspecialchars($peminjaman->kontak ?: '-') . "</code>\n\n"
            . "🛠 <b>Nama Barang:</b> " . htmlspecialchars($namaBarangWithUnit) . "\n"
            . "🔢 <b>Jumlah Pinjam:</b> {$peminjaman->jumlah_pinjam} {$satuan}\n"
            . "📌 <b>Keperluan:</b> " . htmlspecialchars($peminjaman->keperluan) . "\n\n"
            . "📅 <b>Tanggal Pinjam:</b> {$tglPinjam}\n"
            . "📆 <b>Rencana Kembali:</b> {$tglRencana}\n"
            . "📊 <b>Status Peminjaman:</b> 🟢 <b>DIPINJAM</b>\n"
            . "━━━━━━━━━━━━━━━━━━━━\n"
            . "🏛 <i>LabSystem • Laboratorium TKJ</i>";

        return self::sendMessage($msg);
    }

    /**
     * Notifikasi Pengajuan Pengembalian Barang
     */
    public static function sendAjukanKembali($peminjaman, ?string $catatanKembali = null): array
    {
        $namaBarangWithUnit = self::formatNamaBarangDenganUnit($peminjaman);
        $satuan     = $peminjaman->barang->satuan ?? 'Unit';

        $msg = "🔄 <b>PENGAJUAN PENGEMBALIAN ALAT</b>\n"
            . "━━━━━━━━━━━━━━━━━━━━\n"
            . "🔑 <b>Kode Transaksi:</b> <code>{$peminjaman->kode_peminjaman}</code>\n"
            . "👤 <b>Nama Peminjam:</b> " . htmlspecialchars($peminjaman->nama_peminjam) . "\n"
            . "🛠 <b>Nama Barang:</b> " . htmlspecialchars($namaBarangWithUnit) . "\n"
            . "🔢 <b>Jumlah Unit:</b> {$peminjaman->jumlah_pinjam} {$satuan}\n\n"
            . "📝 <b>Catatan Pengembalian:</b> " . htmlspecialchars($catatanKembali ?: '-') . "\n"
            . "📊 <b>Status:</b> ⏳ <b>MENUNGGU VERIFIKASI ADMIN</b>\n"
            . "━━━━━━━━━━━━━━━━━━━━\n"
            . "⚠️ <i>Mohon periksa fisik alat & konfirmasi di panel admin.</i>";

        return self::sendMessage($msg);
    }

    /**
     * Notifikasi Konfirmasi Pengembalian Barang (Selesai)
     */
    public static function sendKonfirmasiKembali($peminjaman): array
    {
        $namaBarangWithUnit = self::formatNamaBarangDenganUnit($peminjaman);
        $satuan     = $peminjaman->barang->satuan ?? 'Unit';
        $tglAktual  = date('d-m-Y', strtotime($peminjaman->tanggal_kembali_aktual));

        $msg = "✅ <b>PENGEMBALIAN ALAT DISETUJUI</b>\n"
            . "━━━━━━━━━━━━━━━━━━━━\n"
            . "🔑 <b>Kode Transaksi:</b> <code>{$peminjaman->kode_peminjaman}</code>\n"
            . "👤 <b>Nama Peminjam:</b> " . htmlspecialchars($peminjaman->nama_peminjam) . "\n"
            . "🛠 <b>Nama Barang:</b> " . htmlspecialchars($namaBarangWithUnit) . "\n"
            . "🔢 <b>Jumlah Unit:</b> {$peminjaman->jumlah_pinjam} {$satuan}\n\n"
            . "📅 <b>Tanggal Kembali:</b> {$tglAktual}\n"
            . "🩺 <b>Kondisi Fisik:</b> " . htmlspecialchars($peminjaman->kondisi_kembali ?: 'Baik') . "\n"
            . "📝 <b>Catatan Admin:</b> " . htmlspecialchars($peminjaman->catatan ?: '-') . "\n"
            . "📊 <b>Status Akhir:</b> 🎉 <b>SELESAI (DIKEMBALIKAN)</b>\n"
            . "━━━━━━━━━━━━━━━━━━━━\n"
            . "🏛 <i>LabSystem • Laboratorium TKJ</i>";

        return self::sendMessage($msg);
    }

    /**
     * Notifikasi Permintaan Reset Password / Lupa Sandi oleh User
     */
    public static function sendPasswordResetRequest($user, string $rawWa, string $formattedWa): array
    {
        date_default_timezone_set('Asia/Jakarta');
        $tanggal = date('d-m-Y H:i') . ' WIB';

        $text = "🔑 <b>PERMINTAAN RESET KATA SANDI</b>\n"
            . "━━━━━━━━━━━━━━━━━━━━\n"
            . "👤 <b>Nama Pengguna:</b> " . htmlspecialchars($user->name) . "\n"
            . "📧 <b>Alamat Email:</b> " . htmlspecialchars($user->email) . "\n"
            . "🏷 <b>Role Akun:</b> " . strtoupper($user->role ?? 'USER') . "\n"
            . "🆔 <b>No. Induk / NIS:</b> " . htmlspecialchars($user->nomor_induk ?? '-') . "\n"
            . "📱 <b>No. WhatsApp:</b> <code>" . htmlspecialchars($rawWa) . "</code>\n"
            . "🕒 <b>Waktu Pengajuan:</b> " . $tanggal . "\n"
            . "━━━━━━━━━━━━━━━━━━━━\n"
            . "💬 <i>Silakan klik tombol di bawah untuk verifikasi via WhatsApp.</i>";

        $pesanWa = "Halo {$user->name}, kami menerima permintaan reset kata sandi akun LabSystem Anda.";

        $config = self::getConfig();
        $payload = [
            'chat_id'                  => (string)$config['admin_chat_id'],
            'text'                     => $text,
            'parse_mode'               => 'HTML',
            'disable_web_page_preview' => true,
            'reply_markup'             => [
                'inline_keyboard' => [
                    [
                        [
                            'text' => '💬 Hubungi via WhatsApp',
                            'url'  => "https://wa.me/{$formattedWa}?text=" . urlencode($pesanWa)
                        ]
                    ]
                ]
            ]
        ];

        return self::sendRequest('sendMessage', $payload);
    }

    /**
     * Format Nama Barang + Detail Unit untuk Maintenance
     */
    public static function formatNamaBarangMaintenance($maintenance): string
    {
        if (!$maintenance->barang) {
            return "🏢 Pemeliharaan Fasilitas / Ruangan Lab";
        }

        $namaBarang = $maintenance->barang->nama_barang . ' (' . $maintenance->barang->kode_barang . ')';
        $unitIndex = trim($maintenance->unit_index ?? '');

        if (!empty($unitIndex)) {
            $indices = array_filter(array_map('trim', explode(',', $unitIndex)));
            if (!empty($indices)) {
                if (count($indices) === 1) {
                    $unitText = "Unit " . reset($indices);
                } else {
                    $unitText = "Unit " . implode(', Unit ', $indices);
                }
                return "{$namaBarang} [{$unitText}]";
            }
        }

        return $namaBarang;
    }

    /**
     * Notifikasi Pencatatan Maintenance Baru
     */
    public static function sendMaintenanceBaru($maintenance): array
    {
        $maintenance->loadMissing('barang', 'user');

        $laboratorium = $maintenance->laboratorium ?: 'Laboratorium TKJ';
        $barangInfo   = self::formatNamaBarangMaintenance($maintenance);
        $tglMaint     = date('d-m-Y', strtotime($maintenance->tanggal_maintenance));
        $biayaStr     = $maintenance->biaya ? 'Rp ' . number_format($maintenance->biaya, 0, ',', '.') : 'Gratis / Internal';
        $teknisi      = $maintenance->teknisi ?: 'Internal Lab';
        $catatanOleh  = $maintenance->user->name ?? 'Admin';

        $statusEmoji = match ($maintenance->status) {
            'Selesai' => '🟢 <b>SELESAI</b>',
            'Proses'  => '🟡 <b>SEDANG PROSES</b>',
            default   => '⚪ <b>PENDING</b>',
        };

        $msg = "🛠️ <b>PENCATATAN MAINTENANCE BARU</b>\n"
            . "━━━━━━━━━━━━━━━━━━━━\n"
            . "🏛️ <b>Laboratorium:</b> " . htmlspecialchars($laboratorium) . "\n"
            . "📦 <b>Target:</b> " . htmlspecialchars($barangInfo) . "\n"
            . "🏷️ <b>Jenis:</b> " . htmlspecialchars($maintenance->jenis) . "\n"
            . "📊 <b>Status:</b> {$statusEmoji}\n\n"
            . "👨‍🔧 <b>Teknisi:</b> " . htmlspecialchars($teknisi) . "\n"
            . "📅 <b>Tanggal:</b> {$tglMaint}\n"
            . "💰 <b>Estimasi Biaya:</b> {$biayaStr}\n\n"
            . "📝 <b>Deskripsi Kerusakan/Perawatan:</b>\n"
            . "<i>" . htmlspecialchars($maintenance->deskripsi_kerusakan) . "</i>\n";

        if (!empty($maintenance->tindakan)) {
            $msg .= "\n🔧 <b>Tindakan:</b>\n" . htmlspecialchars($maintenance->tindakan) . "\n";
        }

        if (!empty($maintenance->catatan)) {
            $msg .= "\n📌 <b>Catatan:</b>\n" . htmlspecialchars($maintenance->catatan) . "\n";
        }

        $msg .= "━━━━━━━━━━━━━━━━━━━━\n"
            . "👤 <i>Dicatat oleh: " . htmlspecialchars($catatanOleh) . "</i>\n"
            . "🏛 <i>LabSystem • Sistem Manajemen Laboratorium</i>";

        return self::sendMessage($msg);
    }

    /**
     * Notifikasi Pembaruan Data / Status Maintenance
     */
    public static function sendMaintenanceUpdate($maintenance): array
    {
        $maintenance->loadMissing('barang', 'user');

        $laboratorium = $maintenance->laboratorium ?: 'Laboratorium TKJ';
        $barangInfo   = self::formatNamaBarangMaintenance($maintenance);
        $tglMaint     = date('d-m-Y', strtotime($maintenance->tanggal_maintenance));
        $biayaStr     = $maintenance->biaya ? 'Rp ' . number_format($maintenance->biaya, 0, ',', '.') : 'Gratis / Internal';
        $teknisi      = $maintenance->teknisi ?: 'Internal Lab';
        $updateOleh   = auth()->user()->name ?? ($maintenance->user->name ?? 'Admin');

        $statusEmoji = match ($maintenance->status) {
            'Selesai' => '✅ <b>SELESAI (SUDAH DIPERBAIKI)</b>',
            'Proses'  => '⏳ <b>SEDANG PROSES PERBAIKAN</b>',
            default   => '⚪ <b>PENDING (MENUNGGU)</b>',
        };

        $msg = "🔄 <b>UPDATE DATA MAINTENANCE</b>\n"
            . "━━━━━━━━━━━━━━━━━━━━\n"
            . "🏛️ <b>Laboratorium:</b> " . htmlspecialchars($laboratorium) . "\n"
            . "📦 <b>Target:</b> " . htmlspecialchars($barangInfo) . "\n"
            . "🏷️ <b>Jenis:</b> " . htmlspecialchars($maintenance->jenis) . "\n"
            . "📊 <b>Status Terbaru:</b> {$statusEmoji}\n\n"
            . "👨‍🔧 <b>Teknisi:</b> " . htmlspecialchars($teknisi) . "\n"
            . "📅 <b>Tanggal:</b> {$tglMaint}\n"
            . "💰 <b>Biaya:</b> {$biayaStr}\n\n"
            . "📝 <b>Deskripsi Kendala:</b>\n"
            . "<i>" . htmlspecialchars($maintenance->deskripsi_kerusakan) . "</i>\n";

        if (!empty($maintenance->tindakan)) {
            $msg .= "\n🔧 <b>Tindakan Dilakukan:</b>\n" . htmlspecialchars($maintenance->tindakan) . "\n";
        }

        if (!empty($maintenance->catatan)) {
            $msg .= "\n📌 <b>Catatan:</b>\n" . htmlspecialchars($maintenance->catatan) . "\n";
        }

        $msg .= "━━━━━━━━━━━━━━━━━━━━\n"
            . "👤 <i>Diperbarui oleh: " . htmlspecialchars($updateOleh) . "</i>\n"
            . "🏛 <i>LabSystem • Sistem Manajemen Laboratorium</i>";

        return self::sendMessage($msg);
    }

    /**
     * Kirim Pesan Live Chat Customer Service dari Pengguna ke Admin Telegram
     */
    public static function sendLiveChatMessageToAdmin(\App\Models\LiveChat $chat, \App\Models\LiveChatMessage $message): array
    {
        date_default_timezone_set('Asia/Jakarta');
        $waktu = date('d-m-Y H:i:s') . ' WIB';
        $senderName = $chat->user_name ?: ($chat->user->name ?? 'Pengguna');
        $senderRole = strtoupper($chat->user_role ?: ($chat->user->role ?? 'Siswa'));
        $senderEmail = $chat->user_email ?: ($chat->user->email ?? '-');

        $text = "💬 <b>PESAN MASUK LIVE CHAT CS</b>\n"
            . "━━━━━━━━━━━━━━━━━━━━\n"
            . "🏷 <b>ID Sesi:</b> <code>{$chat->session_code}</code>\n"
            . "👤 <b>Pengirim:</b> " . htmlspecialchars($senderName) . " (" . htmlspecialchars($senderRole) . ")\n"
            . "📧 <b>Email:</b> " . htmlspecialchars($senderEmail) . "\n"
            . "🕒 <b>Waktu:</b> {$waktu}\n"
            . "━━━━━━━━━━━━━━━━━━━━\n"
            . "📝 <b>Pesan:</b>\n"
            . htmlspecialchars($message->message) . "\n"
            . "━━━━━━━━━━━━━━━━━━━━\n"
            . "💡 <i>Balas pesan ini (fitur Reply Telegram) atau ketik:</i>\n"
            . "<code>/reply {$chat->session_code} [Pesan Anda]</code>";

        $config = self::getConfig();
        $targetChatId = $config['admin_chat_id'];
        $csToken = $config['cs_bot_token'];

        $payload = [
            'chat_id'                  => (string)$targetChatId,
            'text'                     => $text,
            'parse_mode'               => 'HTML',
            'disable_web_page_preview' => true,
        ];

        $res = self::sendRequest('sendMessage', $payload, $csToken);

        if (!empty($res['ok']) && isset($res['json']['result']['message_id'])) {
            $telegramMsgId = $res['json']['result']['message_id'];
            $message->update(['telegram_message_id' => $telegramMsgId]);
            $chat->update(['telegram_last_message_id' => $telegramMsgId]);
        }

        return $res;
    }

    /**
     * Daftarkan URL Webhook ke Telegram API (Default untuk Bot CS)
     */
    public static function setWebhook(string $webhookUrl, ?string $customToken = null): array
    {
        $config = self::getConfig();
        $token = $customToken ?: $config['cs_bot_token'];

        return self::sendRequest('setWebhook', [
            'url'                  => $webhookUrl,
            'drop_pending_updates' => false,
        ], $token);
    }

    /**
     * Dapatkan Informasi Status Webhook dari Telegram API
     */
    public static function getWebhookInfo(?string $customToken = null): array
    {
        $config = self::getConfig();
        $token = $customToken ?: $config['cs_bot_token'];

        return self::sendRequest('getWebhookInfo', [], $token);
    }

    /**
     * Otomatis memproses pesan balasan Telegram (Auto-Poll untuk Localhost & Hosting)
     */
    public static function processIncomingTelegramUpdates(): array
    {
        $config = self::getConfig();
        $csToken = $config['cs_bot_token'];

        $res = self::sendRequest('getUpdates', [
            'limit'   => 15,
            'timeout' => 0,
        ], $csToken);

        $processed = [];

        if (!empty($res['ok']) && !empty($res['json']['result'])) {
            $updates = $res['json']['result'];
            $maxUpdateId = 0;

            foreach ($updates as $up) {
                $updateId = $up['update_id'] ?? 0;
                if ($updateId > $maxUpdateId) {
                    $maxUpdateId = $updateId;
                }

                if (!isset($up['message'])) {
                    continue;
                }

                $msg = $up['message'];
                $msgId = $msg['message_id'] ?? null;
                $text = trim($msg['text'] ?? '');
                $chatId = $msg['chat']['id'] ?? null;

                if (empty($text)) {
                    continue;
                }

                // Cek apakah pesan sudah pernah dicatat di DB
                if ($msgId && \App\Models\LiveChatMessage::where('telegram_message_id', $msgId)->exists()) {
                    continue;
                }

                // 1. Format Command: /typing atau /t [SESSION_CODE] (Bisa dengan atau tanpa session code)
                if (preg_match('/^\/(?:typing|t)(?:\s+([A-Za-z0-9\-]+))?$/is', $text, $tMatches)) {
                    $sessionCode = !empty($tMatches[1]) ? trim($tMatches[1]) : null;
                    
                    $liveChat = null;
                    if ($sessionCode) {
                        $liveChat = \App\Models\LiveChat::where('session_code', $sessionCode)->first();
                    } elseif (isset($msg['reply_to_message'])) {
                        $replyTo = $msg['reply_to_message'];
                        $replyToMessageId = $replyTo['message_id'] ?? null;
                        $replyToText = $replyTo['text'] ?? '';
                        if ($replyToMessageId) {
                            $liveChat = \App\Models\LiveChat::where('telegram_last_message_id', $replyToMessageId)->first();
                        }
                        if (!$liveChat && preg_match('/ID Sesi:\s*([A-Za-z0-9\-]+)/i', $replyToText, $sMatch)) {
                            $liveChat = \App\Models\LiveChat::where('session_code', trim($sMatch[1]))->first();
                        }
                    }

                    if (!$liveChat) {
                        $activeChats = \App\Models\LiveChat::where('status', 'active')->orderBy('id', 'desc')->get();
                        if ($activeChats->count() === 1) {
                            $liveChat = $activeChats->first();
                        }
                    }

                    if ($liveChat) {
                        $liveChat->update(['admin_typing_until' => now()->addSeconds(30)]);
                        self::sendCsMessage(
                            "✍️ <b>Status Typing Aktif</b> pada web <b>" . htmlspecialchars($liveChat->user_name) . "</b> (<code>{$liveChat->session_code}</code>) selama 30 detik.",
                            $chatId
                        );
                    } else {
                        self::sendCsMessage(
                            "ℹ️ <b>Petunjuk Typing:</b> Gunakan <code>/t [KODE_SESI]</code> atau balas (Reply) pesan pengguna dengan <code>/t</code>.",
                            $chatId
                        );
                    }
                }

                // 2. Format Command: /reply [SESSION_CODE] [PESAN] atau /reply [SESSION_CODE] PESAN
                elseif (preg_match('/^\/reply\s+([A-Za-z0-9\-]+)\s+\[?(.+?)\]?$/is', $text, $matches)) {
                    $sessionCode = trim($matches[1]);
                    $replyBody   = trim($matches[2]);

                    $liveChat = \App\Models\LiveChat::where('session_code', $sessionCode)->first();

                    if ($liveChat) {
                        $liveChat->update(['admin_typing_until' => null]);

                        $saved = \App\Models\LiveChatMessage::create([
                            'live_chat_id'        => $liveChat->id,
                            'sender'              => 'admin',
                            'message'             => $replyBody,
                            'telegram_message_id' => $msgId,
                        ]);

                        self::sendCsMessage(
                            "✅ <b>Balasan Terkirim</b> ke <b>{$liveChat->user_name}</b> (<code>{$liveChat->session_code}</code>):\n<i>\"" . htmlspecialchars($replyBody) . "\"</i>",
                            $chatId
                        );

                        $processed[] = $saved->id;
                    } else {
                        self::sendCsMessage(
                            "❌ <b>Sesi Tidak Ditemukan:</b> Kode sesi <code>{$sessionCode}</code> tidak valid.",
                            $chatId
                        );
                    }
                }

                // 3. Format Fitur Reply Bawaan Telegram
                elseif (isset($msg['reply_to_message'])) {
                    $replyTo = $msg['reply_to_message'];
                    $replyToMessageId = $replyTo['message_id'] ?? null;
                    $replyToText = $replyTo['text'] ?? '';

                    $liveChat = null;

                    if ($replyToMessageId) {
                        $liveChat = \App\Models\LiveChat::where('telegram_last_message_id', $replyToMessageId)->first();

                        if (!$liveChat) {
                            $matchedMsg = \App\Models\LiveChatMessage::where('telegram_message_id', $replyToMessageId)->first();
                            if ($matchedMsg) {
                                $liveChat = $matchedMsg->liveChat;
                            }
                        }
                    }

                    if (!$liveChat && preg_match('/ID Sesi:\s*([A-Za-z0-9\-]+)/i', $replyToText, $sessionMatches)) {
                        $extractedCode = trim($sessionMatches[1]);
                        $liveChat = \App\Models\LiveChat::where('session_code', $extractedCode)->first();
                    }

                    if ($liveChat) {
                        // Jika admin membalas dengan kata kunci typing
                        if (in_array(strtolower($text), ['/t', '/typing', 'typing', 'sedang mengetik', '...', '.'])) {
                            $liveChat->update(['admin_typing_until' => now()->addSeconds(30)]);
                            self::sendCsMessage(
                                "✍️ <b>Status Typing Aktif</b> pada web <b>" . htmlspecialchars($liveChat->user_name) . "</b> (<code>{$liveChat->session_code}</code>) selama 30 detik.",
                                $chatId
                            );
                        } else {
                            $liveChat->update(['admin_typing_until' => null]);

                            $saved = \App\Models\LiveChatMessage::create([
                                'live_chat_id'        => $liveChat->id,
                                'sender'              => 'admin',
                                'message'             => $text,
                                'telegram_message_id' => $msgId,
                            ]);

                            self::sendCsMessage(
                                "✅ <b>Pesan Balasan Diteruskan</b> ke web pengguna <b>" . htmlspecialchars($liveChat->user_name) . "</b> (<code>{$liveChat->session_code}</code>).",
                                $chatId
                            );

                            $processed[] = $saved->id;
                        }
                    }
                }

                // 4. Pesan Langsung Tanpa Reply & Bukan Slash Command (Auto-Route jika ada 1 sesi aktif)
                elseif (!str_starts_with($text, '/')) {
                    $activeChats = \App\Models\LiveChat::where('status', 'active')->orderBy('id', 'desc')->get();

                    if ($activeChats->count() === 1) {
                        $liveChat = $activeChats->first();
                        $liveChat->update(['admin_typing_until' => null]);

                        $saved = \App\Models\LiveChatMessage::create([
                            'live_chat_id'        => $liveChat->id,
                            'sender'              => 'admin',
                            'message'             => $text,
                            'telegram_message_id' => $msgId,
                        ]);

                        self::sendCsMessage(
                            "✅ <b>Pesan Diteruskan</b> ke <b>" . htmlspecialchars($liveChat->user_name) . "</b> (<code>{$liveChat->session_code}</code>):\n<i>\"" . htmlspecialchars($text) . "\"</i>",
                            $chatId
                        );

                        $processed[] = $saved->id;
                    } elseif ($activeChats->count() > 1) {
                        self::sendCsMessage(
                            "⚠️ <b>Terdapat " . $activeChats->count() . " Sesi Aktif:</b>\nMohon balas menggunakan fitur <b>Reply</b> Telegram pada pesan pengguna yang ingin dijawab, atau gunakan: <code>/reply [KODE_SESI] [Pesan]</code>",
                            $chatId
                        );
                    }
                }
            }

            // Tandai update Telegram sebagai selesai (offset = maxUpdateId + 1)
            if ($maxUpdateId > 0) {
                self::sendRequest('getUpdates', [
                    'offset'  => $maxUpdateId + 1,
                    'limit'   => 1,
                    'timeout' => 0,
                ], $csToken);
            }
        }

        return $processed;
    }
}

