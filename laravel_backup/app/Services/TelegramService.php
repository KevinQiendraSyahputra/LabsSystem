<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class TelegramService
{
    /**
     * Load Telegram Bot Token & Admin Chat ID
     */
    public static function getConfig(): array
    {
        return [
            'bot_token'     => env('TELEGRAM_BOT_TOKEN', '8953115545:AAF7946PyqdsjInhmdT3klreVSjVtaqEjhI'),
            'admin_chat_id' => env('TELEGRAM_ADMIN_CHAT_ID', '6685668270'),
            'api_url'       => 'https://api.telegram.org/bot',
        ];
    }

    /**
     * Send HTTP POST Request to Telegram API (with SSL Bypass, DNS IP Fallback, & file_get_contents fallback)
     */
    public static function sendRequest(string $method, array $payload): array
    {
        $config   = self::getConfig();
        $botToken = trim($config['bot_token']);

        if (empty($botToken)) {
            return ['ok' => false, 'error' => 'TELEGRAM_BOT_TOKEN kosong.'];
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
}

