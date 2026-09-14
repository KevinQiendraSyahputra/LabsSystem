<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Peminjaman;
use App\Models\Maintenance;
use App\Models\Berita;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1. TAMPILAN USER (SISWA / GURU)
        if (!$user->isAdmin()) {
            $barangTersedia = Barang::where('kondisi', 'Baik')->count();
            $myPinjamanAktif = Peminjaman::with('barang')
                ->where('user_id', $user->id)
                ->where('status', 'Dipinjam')
                ->latest()
                ->get();

            $myPinjamanSelesaiCount = Peminjaman::where('user_id', $user->id)
                ->where('status', 'Dikembalikan')
                ->count();

            $myPinjamanTerlambatCount = Peminjaman::where('user_id', $user->id)
                ->where('status', 'Dipinjam')
                ->where('tanggal_kembali_rencana', '<', now()->toDateString())
                ->count();

            $beritaTerbaru = Berita::with('user')
                ->forUser($user)
                ->latest()
                ->take(3)
                ->get();
            $alatPopuler = Barang::where('kondisi', 'Baik')->latest()->take(4)->get();

            return view('dashboard_user', compact(
                'user',
                'barangTersedia',
                'myPinjamanAktif',
                'myPinjamanSelesaiCount',
                'myPinjamanTerlambatCount',
                'beritaTerbaru',
                'alatPopuler'
            ));
        }

        // 2. TAMPILAN ADMIN
        $totalBarang     = Barang::count();
        $barangBaik      = Barang::where('kondisi', 'Baik')->count();
        $barangRusak     = Barang::where('kondisi', 'Rusak Berat')->count();
        $barangPerbaikan = Barang::where('kondisi', 'Perbaikan')->count();
        $barangPerawatan = Barang::where('kondisi', 'Perawatan')->count();
        $barangHilang    = Barang::where('kondisi', 'Hilang')->count();
        $barangTerbaru   = Barang::where('created_at', '>=', now()->subHours(24))
            ->latest()
            ->get();

        // Nilai aset
        $totalNilai = Barang::whereNotNull('harga')
            ->selectRaw('SUM(harga * jumlah) as total')
            ->value('total') ?? 0;

        // Barang dipinjam saat ini
        $barangDipinjam = Peminjaman::where('status', 'Dipinjam')->sum('jumlah_pinjam') ?? 0;

        // Maintenance bulan ini
        $maintenanceBulanIni = Maintenance::whereMonth('tanggal_maintenance', now()->month)
            ->whereYear('tanggal_maintenance', now()->year)
            ->count();

        // Barang baru tahun ini
        $barangBaruTahunIni = Barang::whereYear('created_at', now()->year)->count();

        // Peminjaman terlambat (notifikasi)
        $peminjamanTerlambat = Peminjaman::with('barang')
            ->where('status', 'Dipinjam')
            ->where('tanggal_kembali_rencana', '<', now()->toDateString())
            ->count();

        // Peminjaman aktif terbaru
        $peminjamanAktif = Peminjaman::with('barang', 'user')
            ->where('status', 'Dipinjam')
            ->latest()
            ->take(5)
            ->get();

        // Statistik per kategori
        $kategoriStats = Barang::selectRaw('kategori, COUNT(*) as total')
            ->groupBy('kategori')
            ->orderByDesc('total')
            ->take(6)
            ->get();

        // Kondisi stats
        $kondisiStats = [
            'Baik'        => $barangBaik,
            'Perawatan'   => $barangPerawatan,
            'Perbaikan'   => $barangPerbaikan,
            'Rusak Berat' => $barangRusak,
            'Hilang'      => $barangHilang,
        ];

        // Dataset Grafik Tren Aktivitas & Distribusi Kategori ala Gentelella v4
        $chartData = $this->getDashboardChartData();

        return view('dashboard', compact(
            'totalBarang', 'barangBaik', 'barangRusak', 'barangPerbaikan',
            'barangPerawatan', 'barangHilang', 'barangTerbaru',
            'totalNilai', 'barangDipinjam', 'maintenanceBulanIni',
            'barangBaruTahunIni', 'peminjamanTerlambat', 'peminjamanAktif',
            'kategoriStats', 'kondisiStats', 'chartData'
        ));
    }

    /**
     * Menyiapkan struktur dataset tren aktivitas laboratorium ala Gentelella v4
     * Mengambil data peminjaman dan maintenance 7 hari terakhir, 30 hari, dan bulanan
     */
    private function getDashboardChartData(): array
    {
        // 1. Data 7 Hari Terakhir
        $days7 = [];
        $peminjaman7d = [];
        $maintenance7d = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dayName = $date->translatedFormat('D, d M');
            $dateStr = $date->toDateString();

            $days7[] = $dayName;
            $peminjaman7d[] = (int) Peminjaman::whereDate('tanggal_pinjam', $dateStr)->count();
            $maintenance7d[] = (int) Maintenance::whereDate('tanggal_maintenance', $dateStr)->count();
        }

        // 2. Data 30 Hari Terakhir (per 3 hari untuk keterbacaan)
        $days30 = [];
        $peminjaman30d = [];
        $maintenance30d = [];

        for ($i = 29; $i >= 0; $i -= 3) {
            $dateEnd = now()->subDays($i);
            $dateStart = (clone $dateEnd)->subDays(2);
            $label = $dateStart->format('d/m') . '-' . $dateEnd->format('d/m');

            $days30[] = $label;
            $peminjaman30d[] = (int) Peminjaman::whereBetween('tanggal_pinjam', [$dateStart->toDateString(), $dateEnd->toDateString()])->count();
            $maintenance30d[] = (int) Maintenance::whereBetween('tanggal_maintenance', [$dateStart->toDateString(), $dateEnd->toDateString()])->count();
        }

        // 3. Data 6 Bulan Terakhir
        $months6 = [];
        $peminjaman6m = [];
        $maintenance6m = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthName = $date->translatedFormat('M Y');

            $months6[] = $monthName;
            $peminjaman6m[] = (int) Peminjaman::whereYear('tanggal_pinjam', $date->year)
                ->whereMonth('tanggal_pinjam', $date->month)
                ->count();
            $maintenance6m[] = (int) Maintenance::whereYear('tanggal_maintenance', $date->year)
                ->whereMonth('tanggal_maintenance', $date->month)
                ->count();
        }

        // Total aktivitas peminjaman + maintenance minggu ini
        $totalAktivitasMingguIni = array_sum($peminjaman7d) + array_sum($maintenance7d);

        return [
            'days7'         => $days7,
            'peminjaman7d'  => $peminjaman7d,
            'maintenance7d' => $maintenance7d,
            'days30'        => $days30,
            'peminjaman30d' => $peminjaman30d,
            'maintenance30d'=> $maintenance30d,
            'months6'       => $months6,
            'peminjaman6m'  => $peminjaman6m,
            'maintenance6m' => $maintenance6m,
            'totalMingguIni'=> $totalAktivitasMingguIni,
        ];
    }
}