<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use App\Models\Berita;
use App\Models\Peminjaman;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // 1. Deteksi & Paksa HTTPS dari Reverse Proxy (InfinityFree / Cloudflare)
        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            URL::forceScheme('https');
        }

        // 2. Otomatis Clear Cache View & App jika APP_VERSION di .env berubah
        try {
            $currentVersion = env('APP_VERSION', '1.0.0');
            if (Cache::get('system_app_version') !== $currentVersion) {
                Artisan::call('view:clear');
                Cache::forever('system_app_version', $currentVersion);
            }
        } catch (\Throwable $e) {
            // Mencegah error jika koneksi cache driver awal belum siap
        }

        // 3. Header Anti-Cache Browser agar update UI Blade langsung tampil seketika
        Event::listen('kernel.handled', function ($request, $response) {
            if ($response && method_exists($response, 'header')) {
                $response->header('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0');
                $response->header('Pragma', 'no-cache');
                $response->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
            }
        });

        // 4. Share data Berita, Notifikasi Belum Dibaca, dan Status Terlambat ke View Layout
        View::composer('layouts.app', function ($view) {
            if (Auth::check()) {
                try {
                    $currentUser = Auth::user();
                    $readBeritas = [];
                    if (!empty($currentUser->read_beritas)) {
                        $readBeritas = json_decode($currentUser->read_beritas, true) ?: [];
                    }

                    // Query Berita & Filter Target Kelas Siswa secara terpusat
                    $globalBeritas = Berita::with('user')
                        ->forUser($currentUser)
                        ->latest()
                        ->take(5)
                        ->get();

                    $unreadBeritasCount = Berita::forUser($currentUser)
                        ->whereNotIn('id', $readBeritas)
                        ->count();

                    // Auto-update status Terlambat secara otomatis
                    $globalTerlambat = 0;
                    if (Schema::hasTable('peminjamans')) {
                        Peminjaman::where('status', 'Dipinjam')
                            ->where('tanggal_kembali_rencana', '<', now()->toDateString())
                            ->update(['status' => 'Terlambat']);

                        $globalTerlambat = Peminjaman::where('status', 'Terlambat')->count();
                    }

                    $view->with([
                        'globalBeritas'       => $globalBeritas,
                        'unreadBeritasCount'  => $unreadBeritasCount,
                        'readBeritas'         => $readBeritas,
                        'globalTerlambat'     => $globalTerlambat,
                    ]);
                } catch (\Throwable $e) {
                    // Mencegah halaman utama crash jika database sedang sibuk
                }
            }
        });
    }
}