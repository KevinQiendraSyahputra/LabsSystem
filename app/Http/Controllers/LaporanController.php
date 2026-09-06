<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Maintenance;
use App\Models\User;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $query = Barang::query();

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }
        if ($request->filled('kondisi')) {
            $konFilter = $request->kondisi;
            $query->where(function ($q) use ($konFilter) {
                $q->where('kondisi', $konFilter)
                  ->orWhere('kondisi_per_unit', 'like', '%"' . $konFilter . '"%');
            });
        }
        if ($request->filled('sumber_dana')) {
            $query->where('sumber_dana', $request->sumber_dana);
        }
        if ($request->filled('laboratorium')) {
            $lab = $request->laboratorium;
            if ($lab === 'Laboratorium TKJ') {
                $query->where(function ($q) use ($lab) {
                    $q->where('laboratorium', $lab)
                      ->orWhereNull('laboratorium')
                      ->orWhere('laboratorium', '');
                });
            } else {
                $query->where('laboratorium', $lab);
            }
        }

        $barangs    = $query->orderBy('kategori')->orderBy('nama_barang')->get();
        $totalNilai = $barangs->sum(fn($b) => ($b->harga ?? 0) * $b->jumlah);
        $byKategori = $barangs->groupBy('kategori');

        // Hitung rekap kondisi per unit secara riil dari JSON
        $rekapKondisi = [
            'Baik'        => 0,
            'Perawatan'   => 0,
            'Perbaikan'   => 0,
            'Rusak Berat' => 0,
            'Hilang'      => 0
        ];

        foreach ($barangs as $b) {
            $kondisiPerUnit = [];
            if ($b->kondisi_per_unit) {
                $kondisiPerUnit = is_array($b->kondisi_per_unit) 
                    ? $b->kondisi_per_unit 
                    : (json_decode($b->kondisi_per_unit, true) ?: []);
            }

            for ($i = 1; $i <= $b->jumlah; $i++) {
                $unitKondisi = $kondisiPerUnit[$i] ?? $b->kondisi;
                if (isset($rekapKondisi[$unitKondisi])) {
                    $rekapKondisi[$unitKondisi]++;
                } else {
                    $rekapKondisi['Baik']++;
                }
            }
        }

        return view('laporan.index', compact('barangs', 'totalNilai', 'byKategori', 'rekapKondisi'));
    }

    /**
     * Halaman Laporan Maintenance
     */
    public function maintenance(Request $request)
    {
        $user     = auth()->user();
        $labScope = ($user && $user->role !== 'admin' && !empty($user->laboratorium_penugasan)) ? $user->laboratorium_penugasan : null;

        $query = Maintenance::with('barang', 'user');

        if ($labScope) {
            $query->where('laboratorium', $labScope);
        } elseif ($request->filled('laboratorium')) {
            $query->where('laboratorium', $request->laboratorium);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_maintenance', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_maintenance', '<=', $request->tanggal_sampai);
        }
        if ($request->filled('search')) {
            $term = trim($request->search);
            $query->where(function ($q) use ($term) {
                $q->whereHas('barang', fn($b) => $b->where('nama_barang', 'like', "%{$term}%"))
                  ->orWhere('teknisi', 'like', "%{$term}%")
                  ->orWhere('deskripsi_kerusakan', 'like', "%{$term}%");
            });
        }

        $maintenances = $query->latest('tanggal_maintenance')->latest('id')->get();

        $totalBiaya   = $maintenances->sum('biaya');
        $rekapStatus  = [
            'Selesai' => $maintenances->where('status', 'Selesai')->count(),
            'Proses'  => $maintenances->where('status', 'Proses')->count(),
            'Pending' => $maintenances->where('status', 'Pending')->count(),
        ];
        $rekapJenis   = $maintenances->groupBy('jenis')->map->count();
        $rekapLab     = $maintenances->groupBy('laboratorium')->map->count();

        return view('laporan.maintenance', compact(
            'maintenances', 'totalBiaya', 'rekapStatus', 'rekapJenis', 'rekapLab', 'labScope'
        ));
    }

    /**
     * Endpoint API JSON Live Maintenance
     */
    public function maintenanceJson(Request $request)
    {
        $user     = auth()->user();
        $labScope = ($user && $user->role !== 'admin' && !empty($user->laboratorium_penugasan)) ? $user->laboratorium_penugasan : null;

        $query = Maintenance::with('barang', 'user');

        if ($labScope) {
            $query->where('laboratorium', $labScope);
        } elseif ($request->filled('laboratorium')) {
            $query->where('laboratorium', $request->laboratorium);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_maintenance', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_maintenance', '<=', $request->tanggal_sampai);
        }
        if ($request->filled('search')) {
            $term = trim($request->search);
            $query->where(function ($q) use ($term) {
                $q->whereHas('barang', fn($b) => $b->where('nama_barang', 'like', "%{$term}%"))
                  ->orWhere('teknisi', 'like', "%{$term}%")
                  ->orWhere('deskripsi_kerusakan', 'like', "%{$term}%");
            });
        }

        $maintenances = $query->latest('tanggal_maintenance')->latest('id')->get();
        $totalBiaya   = (int) $maintenances->sum('biaya');

        $formattedList = $maintenances->map(function ($m, $index) {
            return [
                'no'                  => $index + 1,
                'id'                  => $m->id,
                'tanggal'             => $m->tanggal_maintenance ? \Carbon\Carbon::parse($m->tanggal_maintenance)->format('d/m/Y') : '—',
                'laboratorium'        => $m->laboratorium ?: null,
                'nama_barang'         => $m->barang->nama_barang ?? 'Umum / Fasilitas',
                'kode_barang'         => $m->barang->kode_barang ?? null,
                'teknisi'             => $m->teknisi ?? '—',
                'jenis'               => $m->jenis ?? 'Preventif',
                'deskripsi_kerusakan' => $m->deskripsi_kerusakan ?? '-',
                'tindakan'            => $m->tindakan ?? null,
                'biaya'               => $m->biaya ? 'Rp ' . number_format($m->biaya, 0, ',', '.') : '—',
                'status'              => $m->status,
                'detail_url'          => route('maintenance.show', $m->id),
            ];
        });

        return response()->json([
            'maintenances' => $formattedList,
            'totalCount'   => $maintenances->count(),
            'totalBiaya'   => 'Rp ' . number_format($totalBiaya, 0, ',', '.'),
            'rekapStatus'  => [
                'Selesai' => $maintenances->where('status', 'Selesai')->count(),
                'Proses'  => $maintenances->where('status', 'Proses')->count(),
                'Pending' => $maintenances->where('status', 'Pending')->count(),
            ]
        ]);
    }

    /**
     * Halaman Print/PDF Laporan Inventaris
     */
    public function inventarisPdf(Request $request)
    {
        $query = Barang::query();

        if ($request->filled('laboratorium')) {
            $lab = $request->laboratorium;
            if ($lab === 'Laboratorium TKJ') {
                $query->where(function ($q) use ($lab) {
                    $q->where('laboratorium', $lab)
                      ->orWhereNull('laboratorium')
                      ->orWhere('laboratorium', '');
                });
            } else {
                $query->where('laboratorium', $lab);
            }
        }
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }
        if ($request->filled('kondisi')) {
            $konFilter = $request->kondisi;
            $query->where(function ($q) use ($konFilter) {
                $q->where('kondisi', $konFilter)
                  ->orWhere('kondisi_per_unit', 'like', '%"' . $konFilter . '"%');
            });
        }

        $barangs    = $query->orderBy('laboratorium')->orderBy('kategori')->orderBy('nama_barang')->get();
        $totalNilai = $barangs->sum(fn($b) => ($b->harga ?? 0) * $b->jumlah);
        $byKategori = $barangs->groupBy('kategori');

        $filterLab = $request->laboratorium;

        $kepalaLabUsers = User::where('role', 'kepala_lab')
            ->whereNotNull('laboratorium_penugasan')
            ->get()
            ->keyBy('laboratorium_penugasan');

        $kepalaLabDefault = [
            'Laboratorium TKJ'       => ['nama' => 'Bu Sellya, S.Pd',         'nip' => '-'],
            'Laboratorium AKL'       => ['nama' => 'Kepala Lab AKL',           'nip' => '-'],
            'Laboratorium Pemasaran' => ['nama' => 'Kepala Lab Pemasaran',     'nip' => '-'],
        ];

        return view('laporan.inventaris_pdf', compact(
            'barangs', 'totalNilai', 'byKategori', 'filterLab', 'kepalaLabUsers', 'kepalaLabDefault'
        ));
    }

    /**
     * Halaman Print/PDF Laporan Maintenance
     */
    public function maintenancePdf(Request $request)
    {
        $user     = auth()->user();
        $labScope = ($user && $user->role !== 'admin' && !empty($user->laboratorium_penugasan)) ? $user->laboratorium_penugasan : null;

        $query = Maintenance::with('barang', 'user');

        if ($labScope) {
            $query->where('laboratorium', $labScope);
        } elseif ($request->filled('laboratorium')) {
            $query->where('laboratorium', $request->laboratorium);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_maintenance', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_maintenance', '<=', $request->tanggal_sampai);
        }

        $maintenances = $query->latest('tanggal_maintenance')->latest('id')->get();
        $totalBiaya   = $maintenances->sum('biaya');

        $kepalaLabUsers = User::where('role', 'kepala_lab')
            ->whereNotNull('laboratorium_penugasan')
            ->get()
            ->keyBy('laboratorium_penugasan');

        $kepalaLabDefault = [
            'Laboratorium TKJ'       => ['nama' => 'Bu Sellya, S.Pd',     'nip' => '-'],
            'Laboratorium AKL'       => ['nama' => 'Kepala Lab AKL',       'nip' => '-'],
            'Laboratorium Pemasaran' => ['nama' => 'Kepala Lab Pemasaran', 'nip' => '-'],
        ];

        $filterLab     = $labScope ?? $request->laboratorium;
        $filterStatus  = $request->status;
        $filterJenis   = $request->jenis;
        $tanggalDari   = $request->tanggal_dari;
        $tanggalSampai = $request->tanggal_sampai;

        return view('laporan.maintenance_pdf', compact(
            'maintenances', 'totalBiaya', 'kepalaLabUsers', 'kepalaLabDefault',
            'filterLab', 'filterStatus', 'filterJenis',
            'tanggalDari', 'tanggalSampai'
        ));
    }
}