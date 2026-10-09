<?php

use App\Http\Controllers\BarangController;
use App\Http\Controllers\BantuanController;
use App\Http\Controllers\BeritaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KatalogController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ScanQrController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserPeminjamanController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

// Root -> redirect to login
Route::get('/', fn() => redirect()->route('login'));

// ==========================================
// UTILITY: CLEAR CACHE, MIGRATE & DIAGNOSTIK (Untuk Hosting Tanpa Terminal SSH)
// ==========================================
Route::get('/clear-cache', function () {
    // 1. Reset OPcache PHP jika aktif di server
    if (function_exists('opcache_reset')) {
        @opcache_reset();
    }

    // 2. Hapus file-file cache bootstrap secara fisik
    $bootstrapCacheFiles = glob(base_path('bootstrap/cache/*.php'));
    if (is_array($bootstrapCacheFiles)) {
        foreach ($bootstrapCacheFiles as $file) {
            if (basename($file) !== '.gitignore') {
                @unlink($file);
            }
        }
    }

    // 3. Hapus cache view compiled blade secara fisik
    $viewFiles = glob(storage_path('framework/views/*.php'));
    if (is_array($viewFiles)) {
        foreach ($viewFiles as $file) {
            if (basename($file) !== '.gitignore') {
                @unlink($file);
            }
        }
    }

    // 4. Jalankan Artisan commands
    try {
        Artisan::call('optimize:clear');
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        Artisan::call('route:clear');
    } catch (\Throwable $e) {}

    return '<div style="font-family:system-ui,sans-serif; text-align:center; padding:50px 20px; background:#f8fafc; min-height:100vh; display:flex; align-items:center; justify-content:center;">
        <div style="display:inline-block; background:#ffffff; border:1px solid #e2e8f0; border-radius:20px; padding:36px 40px; box-shadow:0 10px 25px -5px rgba(0,0,0,0.05); max-width:520px; width:100%;">
            <div style="width:52px;height:52px;border-radius:14px;background:#ecfdf5;color:#16a34a;display:inline-flex;align-items:center;justify-content:center;margin-bottom:16px;font-size:26px;font-weight:bold;">&#10003;</div>
            <h2 style="color:#0f172a; margin:0 0 10px 0; font-size:22px; font-weight:800;">Seluruh Cache Berhasil Dibersihkan!</h2>
            <p style="color:#64748b; margin:0 0 24px 0; font-size:13.5px; line-height:1.6;">OPcache, Bootstrap Config Cache, Route Cache, View Blade, dan Storage Cache telah di-reset sehingga perubahan kode terbaru langsung aktif seketika.</p>
            <div style="display:flex; gap:10px; justify-content:center; flex-wrap:wrap;">
                <a href="/test-discord" style="background:#4f46e5; color:white; text-decoration:none; padding:10px 20px; border-radius:10px; font-size:13px; font-weight:bold;">Cek Status Discord</a>
                <a href="/bantuan" style="background:#f1f5f9; color:#334155; text-decoration:none; padding:10px 20px; border-radius:10px; font-size:13px; font-weight:bold; border:1px solid #cbd5e1;">Buka Bantuan CS</a>
            </div>
        </div>
    </div>';
});

Route::get('/test-discord', function () {
    $config = \App\Services\DiscordService::getConfig();
    $botToken = $config['bot_token'];
    $channelId = $config['channel_id'];
    $enabled = $config['enabled'];

    $maskedToken = !empty($botToken) ? substr($botToken, 0, 8) . '...' . substr($botToken, -6) : '(KOSONG)';

    // 1. Test Bot Info
    $botRes = \App\Services\DiscordService::sendRequest('GET', 'users/@me');
    $botName = $botRes['ok'] ? ($botRes['json']['username'] ?? 'Unknown Bot') : ('Error: ' . ($botRes['error'] ?? 'Gagal koneksi HTTP ' . ($botRes['http'] ?? 0)));

    // 2. Test Base Channel Info
    $channelRes = !empty($channelId) ? \App\Services\DiscordService::sendRequest('GET', "channels/{$channelId}") : ['ok' => false, 'error' => 'Channel ID kosong'];
    $guildId = $channelRes['json']['guild_id'] ?? null;

    // 3. Cari Sesi CS Aktif di DB & Jalankan sinkronisasi
    $activeChats = \App\Models\LiveChat::where('status', 'active')->latest()->get();
    $syncResult = \App\Services\DiscordService::processIncomingDiscordUpdates();

    $report = [
        'discord_enabled'      => $enabled ? 'YA (AKTIF)' : 'TIDAK (NONAKTIF)',
        'bot_token'            => $maskedToken,
        'bot_api_status'       => $botRes['ok'] ? "OK ({$botName})" : "GAGAL (HTTP {$botRes['http']}: {$botRes['error']})",
        'base_channel_id'      => $channelId ?: '(KOSONG)',
        'base_channel_guild'   => $guildId ?: 'Tidak ditemukan',
        'sesi_aktif_di_db'     => $activeChats->count(),
        'pesan_baru_disinkron' => count($syncResult),
    ];

    $html = '<div style="font-family:system-ui,sans-serif; background:#0f172a; color:#f8fafc; padding:40px 20px; min-height:100vh;">
        <div style="max-width:700px; margin:0 auto; background:#1e293b; border:1px solid #334155; border-radius:16px; padding:24px 32px; box-shadow:0 10px 25px -5px rgba(0,0,0,0.3);">';
    $html .= '<h2 style="color:#38bdf8; margin:0 0 16px 0; font-size:20px;">Diagnostik Integrasi Discord CS</h2>';
    $html .= '<table style="width:100%; border-collapse:collapse; font-size:13px; margin-bottom:24px;">';
    foreach ($report as $k => $v) {
        $html .= "<tr style='border-bottom:1px solid #334155;'><td style='padding:10px 0; color:#94a3b8; font-weight:600;'>" . strtoupper(str_replace('_', ' ', $k)) . "</td><td style='padding:10px 0; text-align:right; font-family:monospace; color:#f1f5f9;'>" . htmlspecialchars((string)$v) . "</td></tr>";
    }
    $html .= '</table>';

    if ($activeChats->count() > 0) {
        $html .= '<h3 style="color:#a5b4fc; margin:16px 0 8px 0; font-size:14px;">Daftar Sesi CS Aktif:</h3><div style="font-size:12px; font-family:monospace; background:#090d16; padding:12px; border-radius:8px; line-height:1.8;">';
        foreach ($activeChats as $c) {
            $html .= "<div>&bull; <strong>{$c->session_code}</strong> | User: {$c->user_name} | Channel: " . ($c->discord_channel_id ?: 'BELUM TERHUBUNG') . " | Pesan: " . $c->messages()->count() . "</div>";
        }
        $html .= '</div>';
    }

    $html .= '<div style="margin-top:24px; display:flex; gap:10px;">
        <a href="/clear-cache" style="background:#3b82f6; color:white; text-decoration:none; padding:10px 18px; border-radius:8px; font-size:13px; font-weight:bold;">Clear Cache Ulang</a>
        <a href="/bantuan" style="background:#4f46e5; color:white; text-decoration:none; padding:10px 18px; border-radius:8px; font-size:13px; font-weight:bold;">Buka Halaman Bantuan</a>
    </div></div></div>';

    return $html;
});

Route::get('/migrate', function () {
    try {
        Artisan::call('migrate', ['--force' => true]);
        $output = Artisan::output();
        return '<div style="font-family:system-ui,sans-serif; text-align:center; padding:50px 20px; background:#f8fafc; min-height:100vh;">
            <div style="display:inline-block; background:#ffffff; border:1px solid #e2e8f0; border-radius:20px; padding:32px 40px; box-shadow:0 4px 6px -1px rgba(0,0,0,0.05); max-width:600px; text-align:left;">
                <h2 style="color:#16a34a; margin:0 0 10px 0;">Migrasi Database Berhasil Dijalankan!</h2>
                <pre style="background:#0f172a; color:#38bdf8; padding:15px; border-radius:10px; font-size:12px; overflow-x:auto;">' . htmlspecialchars($output ?: 'Seluruh tabel dan kolom sudah up-to-date.') . '</pre>
                <div style="text-align:center; margin-top:20px;">
                    <a href="/bantuan" style="background:#4f46e5; color:white; text-decoration:none; padding:10px 20px; border-radius:10px; font-size:13px; font-weight:bold; display:inline-block;">Buka Halaman Bantuan</a>
                </div>
            </div>
        </div>';
    } catch (\Throwable $e) {
        return '<div style="font-family:system-ui,sans-serif; text-align:center; padding:50px 20px; background:#f8fafc; min-height:100vh;">
            <div style="display:inline-block; background:#ffffff; border:1px solid #e2e8f0; border-radius:20px; padding:32px 40px; box-shadow:0 4px 6px -1px rgba(0,0,0,0.05); max-width:600px; text-align:left;">
                <h2 style="color:#e11d48; margin:0 0 10px 0;">Gagal Menjalankan Migrasi</h2>
                <p style="color:#64748b; font-size:14px;">' . htmlspecialchars($e->getMessage()) . '</p>
            </div>
        </div>';
    }
});

// Dashboard (handles Admin overview or User self-service overview)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// ==========================================
// ADMIN ONLY ROUTES
// ==========================================
Route::middleware(['auth', 'admin'])->group(function () {
    // 1. Inventaris Barang CRUD & Unit Management
    Route::post('barang/bulk-delete', [BarangController::class, 'bulkDelete'])->name('barang.bulk-delete');
    Route::resource('barang', BarangController::class);
    Route::post('barang/{barang}/update-kondisi', [BarangController::class, 'updateKondisi'])->name('barang.update-kondisi');
    Route::post('barang/{barang}/split-units', [BarangController::class, 'splitUnits'])->name('barang.split-units');

    // 2. Kelola Peminjaman (Admin View, Delete, Approval & Return Confirmation)
    Route::get('peminjaman', [PeminjamanController::class, 'index'])->name('peminjaman.index');
    Route::get('peminjaman/create', [PeminjamanController::class, 'create'])->name('peminjaman.create');
    Route::post('peminjaman', [PeminjamanController::class, 'store'])->name('peminjaman.store');
    Route::delete('peminjaman/{peminjaman}', [PeminjamanController::class, 'destroy'])->name('peminjaman.destroy');
    Route::post('peminjaman/{peminjaman}/kembali', [PeminjamanController::class, 'konfirmasiKembali'])->name('peminjaman.kembali');
    Route::post('peminjaman/{peminjaman}/tolak-kembali', [PeminjamanController::class, 'tolakKembali'])->name('peminjaman.tolak.kembali');

    // 4. Laporan Inventaris & Aset (Admin only)
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/inventaris/pdf', [LaporanController::class, 'inventarisPdf'])->name('laporan.inventaris.pdf');

    // 5. Manajemen Pengguna & Hak Akses
    Route::get('pengguna/online-statuses', [UserController::class, 'onlineStatuses'])->name('pengguna.online-statuses');
    Route::post('pengguna/bulk-delete', [UserController::class, 'bulkDelete'])->name('pengguna.bulk-delete');
    Route::resource('pengguna', UserController::class)->parameters(['pengguna' => 'pengguna']);
});

// ==========================================
// MAINTENANCE ROUTES (Admin + Kepala Lab + Koordinator Lab)
// ==========================================
Route::middleware(['auth', 'can_maintenance'])->group(function () {
    // 3. Maintenance Alat Lab
    Route::resource('maintenance', MaintenanceController::class);

    // 4b. Laporan Maintenance
    Route::get('/laporan/maintenance', [LaporanController::class, 'maintenance'])->name('laporan.maintenance');
    Route::get('/laporan/maintenance/json', [LaporanController::class, 'maintenanceJson'])->name('laporan.maintenance.json');
    Route::get('/laporan/maintenance/pdf', [LaporanController::class, 'maintenancePdf'])->name('laporan.maintenance.pdf');
});

// ==========================================
// AUTHENTICATED USER ROUTES (Admin, Guru, Siswa)
// ==========================================
Route::middleware('auth')->group(function () {
    // Presensi Real-Time User (Heartbeat & Tab Close Tracker)
    Route::post('/user-heartbeat', function () {
        if (\Illuminate\Support\Facades\Auth::check()) {
            $userId = \Illuminate\Support\Facades\Auth::id();
            \Illuminate\Support\Facades\Cache::put('user-is-online-' . $userId, true, now()->addSeconds(45));
            \Illuminate\Support\Facades\Cache::put('user-last-seen-' . $userId, now()->timestamp, now()->addDays(7));
        }
        return response()->json(['status' => 'online']);
    })->name('user.heartbeat');

    Route::match(['GET', 'POST'], '/user-offline', function () {
        if (\Illuminate\Support\Facades\Auth::check()) {
            \Illuminate\Support\Facades\Cache::forget('user-is-online-' . \Illuminate\Support\Facades\Auth::id());
        }
        return response()->noContent();
    })->name('user.offline');

    // Layanan Katalog Alat & Peminjaman Mandiri
    Route::get('/katalog', [KatalogController::class, 'index'])->name('katalog.index');
    Route::get('/katalog/{barang}', [KatalogController::class, 'show'])->name('katalog.show');
    Route::post('/katalog/{barang}/pinjam', [KatalogController::class, 'storePinjam'])->name('katalog.pinjam');

    // Scan QR Code
    Route::get('/scan-qr', [ScanQrController::class, 'index'])->name('scan.qr');
    Route::get('/api/check-qr', [ScanQrController::class, 'check'])->name('api.check.qr');

    // Peminjaman Saya, Live Status Polling & Pengajuan Pengembalian
    Route::get('/peminjaman-saya', [UserPeminjamanController::class, 'index'])->name('peminjaman.saya');
    Route::get('/peminjaman/{peminjaman}/status-json', [PeminjamanController::class, 'statusJson'])->name('peminjaman.status.json');
    Route::get('/peminjaman/{peminjaman}', [PeminjamanController::class, 'show'])->name('peminjaman.show');
    Route::post('/peminjaman/{peminjaman}/ajukan-kembali', [PeminjamanController::class, 'ajukanKembali'])->name('peminjaman.ajukan.kembali');

    // Berita Index & Mark All Read (Semua User)
    Route::get('/berita', [BeritaController::class, 'index'])->name('berita.index');
    Route::post('/berita/mark-all-read', [BeritaController::class, 'markAllRead'])->name('berita.markAllRead');
    Route::get('/berita/mark-all-read', [BeritaController::class, 'markAllRead']);

    // Berita Management (Admin & Guru Only)
    Route::middleware('guru_or_admin')->group(function () {
        Route::get('/berita/create', [BeritaController::class, 'create'])->name('berita.create');
        Route::post('/berita', [BeritaController::class, 'store'])->name('berita.store');
        Route::get('/berita/{berita}/edit', [BeritaController::class, 'edit'])->name('berita.edit');
        Route::put('/berita/{berita}', [BeritaController::class, 'update'])->name('berita.update');
        Route::delete('/berita/{berita}', [BeritaController::class, 'destroy'])->name('berita.destroy');
    });

    // Detail Berita (Semua User)
    Route::get('/berita/{berita}', [BeritaController::class, 'show'])->name('berita.show');

    // Profil Akun
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile/foto', [ProfileController::class, 'deleteFoto'])->name('profile.delete-foto');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Halaman Bantuan & Live Chat Asisten Lab
    Route::get('/bantuan', [BantuanController::class, 'index'])->name('bantuan.index');
    Route::match(['GET', 'POST'], '/bantuan/start-cs', [BantuanController::class, 'startCsSession'])->name('bantuan.start-cs');
    Route::post('/bantuan/send-cs', [BantuanController::class, 'sendCsMessage'])->name('bantuan.send-cs');
    Route::get('/bantuan/poll', [BantuanController::class, 'pollMessages'])->name('bantuan.poll');
    Route::match(['GET', 'POST'], '/bantuan/close-cs', [BantuanController::class, 'closeCsSession'])->name('bantuan.close-cs');
});

// Transkrip Tiket Customer Service (Dapat diakses langsung via link Discord atau unduhan web)
Route::get('/bantuan/transcript/{sessionCode}', [\App\Http\Controllers\BantuanController::class, 'viewTranscript'])->name('bantuan.transcript');

// Telegram Bot Webhook & Setup Helper
Route::post('/telegram/webhook', [\App\Http\Controllers\TelegramWebhookController::class, 'handle']);
Route::get('/telegram/setup-webhook', [\App\Http\Controllers\TelegramWebhookController::class, 'setupWebhook']);

// ==========================================
// SERVE STORAGE FILES (Kompatibilitas Penuh InfinityFree / CPanel tanpa symlink)
// ==========================================
Route::get('/storage/{path}', function ($path) {
    $cleanPath = ltrim($path, '/');
    $candidates = [
        public_path('storage/' . $cleanPath),
        storage_path('app/public/' . $cleanPath),
        public_path('uploads/' . $cleanPath),
        base_path('storage/app/public/' . $cleanPath),
    ];

    foreach ($candidates as $target) {
        if (file_exists($target) && !is_dir($target)) {
            $ext = strtolower(pathinfo($target, PATHINFO_EXTENSION));
            $mimeTypes = [
                'webp' => 'image/webp',
                'png'  => 'image/png',
                'jpg'  => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'gif'  => 'image/gif',
                'svg'  => 'image/svg+xml',
            ];
            $contentType = $mimeTypes[$ext] ?? mime_content_type($target) ?: 'application/octet-stream';

            return response()->file($target, [
                'Content-Type'  => $contentType,
                'Cache-Control' => 'public, max-age=86400, stale-while-revalidate=604800',
            ]);
        }
    }
    abort(404);
})->where('path', '.*');

Route::get('/uploads/{path}', function ($path) {
    $cleanPath = ltrim($path, '/');
    $candidates = [
        public_path('uploads/' . $cleanPath),
        base_path('public/uploads/' . $cleanPath),
    ];

    foreach ($candidates as $target) {
        if (file_exists($target) && !is_dir($target)) {
            $ext = strtolower(pathinfo($target, PATHINFO_EXTENSION));
            $mimeTypes = [
                'webp' => 'image/webp',
                'png'  => 'image/png',
                'jpg'  => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'gif'  => 'image/gif',
                'svg'  => 'image/svg+xml',
                'mp3'  => 'audio/mpeg',
            ];
            $contentType = $mimeTypes[$ext] ?? mime_content_type($target) ?: 'application/octet-stream';

            return response()->file($target, [
                'Content-Type'  => $contentType,
                'Cache-Control' => 'public, max-age=86400',
            ]);
        }
    }
    abort(404);
})->where('path', '.*');

require __DIR__ . '/auth.php';