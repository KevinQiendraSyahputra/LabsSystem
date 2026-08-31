<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;

class InventarisController extends Controller
{
    /**
     * Daftar inventaris per-laboratorium
     */
    public function index(Request $request)
    {
        $laboratoriumList = Barang::$laboratoriumList;
        $activeLab = $request->get('lab', 'Semua');

        $query = Barang::query();
        if ($activeLab && $activeLab !== 'Semua') {
            $query->where('laboratorium', $activeLab);
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }
        if ($request->filled('kondisi')) {
            $query->where('kondisi', $request->kondisi);
        }
        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('nama_barang', 'like', "%{$term}%")
                  ->orWhere('kode_barang', 'like', "%{$term}%")
                  ->orWhere('merk', 'like', "%{$term}%");
            });
        }

        $barangs = $query->orderBy('kategori')->orderBy('nama_barang')->get();

        // Statistik per-lab ini
        $totalUnit  = $barangs->sum('jumlah');
        $totalNilai = $barangs->sum(fn($b) => ($b->harga ?? 0) * $b->jumlah);
        $byKategori = $barangs->groupBy('kategori');

        // Rekap kondisi unit
        $rekapKondisi = ['Baik' => 0, 'Perawatan' => 0, 'Perbaikan' => 0, 'Rusak Berat' => 0, 'Hilang' => 0];
        foreach ($barangs as $b) {
            $kondisiPerUnit = [];
            if ($b->kondisi_per_unit) {
                $kondisiPerUnit = json_decode($b->kondisi_per_unit, true) ?: [];
            }
            for ($i = 1; $i <= $b->jumlah; $i++) {
                $k = $kondisiPerUnit[$i] ?? $b->kondisi;
                if (isset($rekapKondisi[$k])) $rekapKondisi[$k]++;
                else $rekapKondisi['Baik']++;
            }
        }

        // Rekap per lab lainnya (untuk cards overview)
        $rekapLab = [];
        foreach ($laboratoriumList as $lab) {
            $labBarangs = Barang::where('laboratorium', $lab)->get();
            $rekapLab[$lab] = [
                'jumlah_jenis'  => $labBarangs->count(),
                'total_unit'    => $labBarangs->sum('jumlah'),
                'total_nilai'   => $labBarangs->sum(fn($b) => ($b->harga ?? 0) * $b->jumlah),
            ];
        }

        return view('inventaris.index', compact(
            'laboratoriumList', 'activeLab', 'barangs',
            'totalUnit', 'totalNilai', 'byKategori', 'rekapKondisi', 'rekapLab'
        ));
    }

    /**
     * Print/PDF Inventaris per-Lab
     */
    public function pdf(Request $request)
    {
        $activeLab = $request->get('lab', 'Semua');

        $query = Barang::query();
        if ($activeLab && $activeLab !== 'Semua') {
            $query->where('laboratorium', $activeLab);
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }
        if ($request->filled('kondisi')) {
            $query->where('kondisi', $request->kondisi);
        }

        $barangs    = $query->orderBy('kategori')->orderBy('nama_barang')->get();
        $byKategori = $barangs->groupBy('kategori');
        $totalUnit  = $barangs->sum('jumlah');
        $totalNilai = $barangs->sum(fn($b) => ($b->harga ?? 0) * $b->jumlah);

        // Rekap kondisi
        $rekapKondisi = ['Baik' => 0, 'Perawatan' => 0, 'Perbaikan' => 0, 'Rusak Berat' => 0, 'Hilang' => 0];
        foreach ($barangs as $b) {
            $kondisiPerUnit = [];
            if ($b->kondisi_per_unit) {
                $kondisiPerUnit = json_decode($b->kondisi_per_unit, true) ?: [];
            }
            for ($i = 1; $i <= $b->jumlah; $i++) {
                $k = $kondisiPerUnit[$i] ?? $b->kondisi;
                if (isset($rekapKondisi[$k])) $rekapKondisi[$k]++;
                else $rekapKondisi['Baik']++;
            }
        }

        // Kepala Lab per laboratorium
        $kepalaLab = [
            'Laboratorium TKJ'       => ['nama' => 'Bu Sellya, S.Pd',          'jabatan' => 'Kepala Laboratorium TKJ',       'nip' => '198507152010011001'],
            'Laboratorium AKL'       => ['nama' => 'Bpk. Rian Hidayat, S.Kom', 'jabatan' => 'Kepala Laboratorium AKL',       'nip' => '199003202015021004'],
            'Laboratorium Pemasaran' => ['nama' => 'Bu Dewi Rahayu, S.E',      'jabatan' => 'Kepala Laboratorium Pemasaran', 'nip' => '198903112014032002'],
        ];

        return view('inventaris.pdf', compact(
            'activeLab', 'barangs', 'byKategori',
            'totalUnit', 'totalNilai', 'rekapKondisi', 'kepalaLab'
        ));
    }
}
