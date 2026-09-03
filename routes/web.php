<?php

use App\Http\Controllers\BarangController;
use App\Http\Controllers\BantuanController;
use App\Http\Controllers\BeritaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventarisController;
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
// UTILITY: CLEAR ALL CACHE (Untuk Hosting Tanpa Terminal SSH)
// ==========================================
Route::get('/clear-cache', function () {
    Artisan::call('optimize:clear');
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('route:clear');

    return '<div style="font-family:system-ui,sans-serif; text-align:center; padding:50px 20px; background:#f8fafc; min-height:100vh;">
        <div style="display:inline-block; background:#ffffff; border:1px solid #e2e8f0; border-radius:20px; padding:32px 40px; box-shadow:0 4px 6px -1px rgba(0,0,0,0.05);">
            <h2 style="color:#16a34a; margin:0 0 10px 0;">Seluruh Cache Berhasil Dibersihkan!</h2>
            <p style="color:#64748b; margin:0 0 20px 0; font-size:14px;">Config, Route, View Blade, dan Application Cache telah di-reset.</p>
            <a href="/" style="background:#4f46e5; color:white; text-decoration:none; padding:10px 20px; border-radius:10px; font-size:13px; font-weight:bold; display:inline-block;">Kembali ke Beranda</a>
        </div>
    </div>';
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
});

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