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

        return view('dashboard', compact(
            'totalBarang', 'barangBaik', 'barangRusak', 'barangPerbaikan',
            'barangPerawatan', 'barangHilang', 'barangTerbaru',
            'totalNilai', 'barangDipinjam', 'maintenanceBulanIni',
            'barangBaruTahunIni', 'peminjamanTerlambat', 'peminjamanAktif',
            'kategoriStats', 'kondisiStats'
        ));
    }
}