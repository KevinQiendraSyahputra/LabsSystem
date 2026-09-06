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

        // Dataset interaktif ECharts Hover (Kategori & Tren Tahunan)
        $hoverChartData = $this->getChartHoverData();

        return view('dashboard', compact(
            'totalBarang', 'barangBaik', 'barangRusak', 'barangPerbaikan',
            'barangPerawatan', 'barangHilang', 'barangTerbaru',
            'totalNilai', 'barangDipinjam', 'maintenanceBulanIni',
            'barangBaruTahunIni', 'peminjamanTerlambat', 'peminjamanAktif',
            'kategoriStats', 'kondisiStats', 'hoverChartData'
        ));
    }

    /**
     * Menyiapkan struktur dataset hover ECharts kategori (Jaringan, Komputer, Alat Praktik, dll)
     * Mengambil data riil dari tabel barangs berdasarkan kolom kategori, jumlah, dan tahun_pembelian / created_at.
     */
    private function getChartHoverData(): array
    {
        $currentYear = (int) date('Y');
        
        // PENGATURAN TAHUN:
        // Mencari tahun pengadaan/pembelian terlama di database sebagai batas awal grafik
        $minDbYear = (int) (Barang::whereNotNull('tahun_pembelian')->min('tahun_pembelian') ?: ($currentYear - 4));
        $startYear = min($minDbYear, $currentYear - 4); // Rentang minimal 5 tahun (misal: 2022 s/d 2026)

        $yearsList = [];
        for ($y = $startYear; $y <= $currentYear; $y++) {
            $yearsList[] = (string) $y;
        }

        $source = [];
        $source[] = array_merge(['product'], $yearsList);

        // Kategori terdaftar dari database (diurutkan berdasarkan total unit terbanyak)
        $dbCategories = Barang::whereNotNull('kategori')
            ->selectRaw('kategori, SUM(jumlah) as total_unit')
            ->groupBy('kategori')
            ->orderByDesc('total_unit')
            ->pluck('kategori')
            ->toArray();

        $defaultCategories = ['Jaringan', 'Alat Praktik', 'Komputer', 'Perangkat Keras'];
        $categories = !empty($dbCategories) 
            ? array_values(array_unique(array_merge($dbCategories, $defaultCategories))) 
            : $defaultCategories;

        // Ambil kategori utama
        $categories = array_slice($categories, 0, 5);

        foreach ($categories as $cat) {
            $row = [$cat];

            foreach ($yearsList as $yr) {
                $yrInt = (int) $yr;

                // Hitung kumulatif total unit barang sampai dengan tahun tersebut
                $totalUnitSampaiTahunIni = (int) Barang::where('kategori', $cat)
                    ->where(function ($q) use ($yrInt) {
                        $q->where('tahun_pembelian', '<=', $yrInt)
                          ->orWhere(function ($sub) use ($yrInt) {
                              $sub->whereNull('tahun_pembelian')
                                  ->whereYear('created_at', '<=', $yrInt);
                          });
                    })
                    ->sum('jumlah');

                $row[] = $totalUnitSampaiTahunIni;
            }
            $source[] = $row;
        }

        return [
            'years'       => $yearsList,
            'categories'  => $categories,
            'source'      => $source,
            'initialYear' => (string) end($yearsList),
        ];
    }
}