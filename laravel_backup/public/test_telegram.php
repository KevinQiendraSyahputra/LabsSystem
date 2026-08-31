<?php

// public/test_telegram.php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

header('Content-Type: text/plain; charset=utf-8');

use App\Services\TelegramService;
use App\Models\User;

$cfg = TelegramService::getConfig();

echo "=== PENGUJIAN TELEGRAM BOT SYSTEM PEMINJAMAN LAB ===\n\n";
echo "Bot Token      : " . ($cfg['bot_token'] ? substr($cfg['bot_token'], 0, 8) . '...' . substr($cfg['bot_token'], -5) : '(KOSONG)') . "\n";
echo "Admin Chat ID  : " . ($cfg['admin_chat_id'] ?: '(KOSONG)') . "\n\n";

echo "1. Menghubungi Telegram API (getMe)... ";
$getMe = TelegramService::sendRequest('getMe', []);
if ($getMe['ok'] && isset($getMe['json']['result'])) {
    $bot = $getMe['json']['result'];
    echo "BERHASIL!\n";
    echo "   - ID Bot   : {$bot['id']}\n";
    echo "   - Nama Bot : {$bot['first_name']}\n";
    echo "   - Username : @{$bot['username']}\n\n";
} else {
    echo "GAGAL!\n";
    echo "   - Error: " . ($getMe['error'] ?? 'Unknown') . "\n\n";
    exit;
}

echo "2. Tes Kirim Pesan Permintaan Reset Password...\n";
$dummyUser = User::first() ?: new User(['name' => 'Siswa Tes', 'email' => 'siswa@test.com', 'role' => 'siswa', 'nomor_induk' => '12345']);
$resReset = TelegramService::sendPasswordResetRequest($dummyUser, '081234567890', '6281234567890');

if ($resReset['ok']) {
    echo "   -> BERHASIL TERKIRIM! Tombol WhatsApp aktif di Telegram.\n";
} else {
    echo "   -> GAGAL: " . ($resReset['error'] ?? 'Unknown') . "\n";
}

echo "\n=====================================\n";
