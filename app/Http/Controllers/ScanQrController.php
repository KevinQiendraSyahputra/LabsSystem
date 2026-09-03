<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Peminjaman;
use Illuminate\Http\Request;

class ScanQrController extends Controller
{
    public function index()
    {
        return view('scan_qr');
    }

    public function check(Request $request)
    {
        $code = trim($request->query('code', ''));
        if (!$code) {
            return response()->json(['status' => 'error', 'message' => 'Kode QR tidak boleh kosong.'], 400);
        }

        $baseCode = $code;
        $unitIndex = null;
        $barang = null;

        // 1. Ekstrak suffix unit terlebih dahulu jika kode berupa unit spesifik (misal: TKJ-JRG-001-1 atau KODE-1)
        if (preg_match('/^(.*)-(\d+)$/', $code, $matches)) {
            $candidateBase = trim($matches[1]);
            $candidateUnit = (int) $matches[2];

            // Cari barang induk berdasarkan kode_barang atau nomor_seri
            $candidateBarang = Barang::where('kode_barang', $candidateBase)
                ->orWhere('nomor_seri', $candidateBase)
                ->first();

            if ($candidateBarang && $candidateUnit >= 1 && $candidateUnit <= $candidateBarang->jumlah) {
                $barang = $candidateBarang;
                $unitIndex = $candidateUnit;
                $baseCode = $candidateBase;
            }
        }

        // 2. Jika bukan unit suffix, cari barang langsung (kode_barang, nomor_seri, atau id numeric)
        if (!$barang) {
            $barang = Barang::where('kode_barang', $code)
                ->orWhere('nomor_seri', $code)
                ->orWhere(function ($q) use ($code) {
                    if (is_numeric($code)) {
                        $q->where('id', (int) $code);
                    }
                })
                ->first();
        }

        if (!$barang) {
            return response()->json([
                'status'  => 'not_found',
                'message' => 'Alat dengan kode "' . $code . '" tidak ditemukan di dalam sistem laboratorium.',
            ], 404);
        }

        // 3. Pengecekan Unit Spesifik
        if ($unitIndex !== null) {
            // A. Cek fisik unit dari JSON kondisi_per_unit
            $kondisiPerUnit = [];
            if ($barang->kondisi_per_unit) {
                $kondisiPerUnit = is_array($barang->kondisi_per_unit) 
                    ? $barang->kondisi_per_unit 
                    : (json_decode($barang->kondisi_per_unit, true) ?: []);
            }

            $kondisiUnit = $kondisiPerUnit[$unitIndex] ?? $barang->kondisi;

            if ($kondisiUnit !== 'Baik') {
                return response()->json([
                    'status'      => 'unavailable',
                    'message'     => 'Unit ' . $unitIndex . ' dari ' . $barang->nama_barang . ' (' . $baseCode . '-' . $unitIndex . ') saat ini berstatus ' . $kondisiUnit . ' (Maintenance) dan tidak dapat dipinjam.',
                    'nama_barang' => $barang->nama_barang,
                    'unit_index'  => $unitIndex,
                    'kondisi'     => $kondisiUnit,
                ]);
            }

            // B. Cek apakah unit spesifik ini sedang dipinjam (baik standalone atau bagian dari multi-select comma-separated)
            $activeLoans = Peminjaman::where('barang_id', $barang->id)
                ->whereIn('status', ['Menunggu Persetujuan', 'Dipinjam', 'Terlambat'])
                ->get();

            $isBorrowed = false;
            $peminjamName = '';
            $statusPeminjaman = '';

            foreach ($activeLoans as $loan) {
                if ($loan->unit_index !== null && $loan->unit_index !== '') {
                    $indices = array_map('trim', explode(',', $loan->unit_index));
                    if (in_array((string)$unitIndex, $indices, true)) {
                        $isBorrowed = true;
                        $peminjamName = $loan->nama_peminjam ?? 'Pengguna lain';
                        $statusPeminjaman = $loan->status;
                        break;
                    }
                }
            }

            if ($isBorrowed) {
                $statusText = $statusPeminjaman === 'Menunggu Persetujuan' ? 'sedang menunggu persetujuan peminjaman' : 'sedang dipinjam';
                return response()->json([
                    'status'      => 'unavailable',
                    'message'     => 'Unit ' . $unitIndex . ' dari ' . $barang->nama_barang . ' saat ini ' . $statusText . ' oleh ' . $peminjamName . '. Unit ' . $unitIndex . ' tidak dapat dipinjam/discan lagi!',
                    'nama_barang' => $barang->nama_barang,
                    'unit_index'  => $unitIndex,
                    'peminjam'    => $peminjamName,
                ]);
            }
        } else {
            // 4. Pengecekan Kondisi Global Barang jika yang discan adalah QR Kode Induk (tanpa suffix unit)
            if ($barang->kondisi !== 'Baik') {
                return response()->json([
                    'status'      => 'unavailable',
                    'message'     => 'Alat ' . $barang->nama_barang . ' (' . $barang->kode_barang . ') saat ini berstatus ' . $barang->kondisi . ' (Maintenance) dan tidak dapat dipinjam.',
                    'nama_barang' => $barang->nama_barang,
                    'kondisi'     => $barang->kondisi,
                ]);
            }
        }

        // 5. Pengecekan Stok Tersedia Global
        $totalActiveLoans = Peminjaman::where('barang_id', $barang->id)
            ->whereIn('status', ['Dipinjam', 'Menunggu Persetujuan', 'Terlambat'])
            ->count();

        if ($barang->stok_tersedia <= 0 || $totalActiveLoans >= $barang->jumlah) {
            $activeLoan = Peminjaman::where('barang_id', $barang->id)
                ->whereIn('status', ['Dipinjam', 'Menunggu Persetujuan', 'Terlambat'])
                ->latest()
                ->first();

            $peminjamName = $activeLoan ? $activeLoan->nama_peminjam : 'pengguna lain';

            return response()->json([
                'status'      => 'unavailable',
                'message'     => 'Seluruh stok unit ' . $barang->nama_barang . ' (' . $barang->kode_barang . ') saat ini sedang habis / dipinjam. Alat tidak dapat dipinjam lagi saat ini.',
                'nama_barang' => $barang->nama_barang,
                'peminjam'    => $peminjamName,
            ]);
        }

        // 6. Scan Berhasil (Barang & Unit dalam Kondisi Baik dan belum dipinjam)
        $redirectUrl = auth()->user()->isAdmin() 
            ? route('barang.show', $barang->id) 
            : route('katalog.show', $barang->id);

        if ($unitIndex !== null) {
            $redirectUrl .= '?unit=' . $unitIndex;
        }

        $unitText = $unitIndex !== null ? ' (Unit ' . $unitIndex . ')' : '';

        return response()->json([
            'status'     => 'success',
            'redirect'   => $redirectUrl,
            'barang'     => $barang,
            'unit_index' => $unitIndex,
            'message'    => 'Unit ' . $barang->nama_barang . $unitText . ' siap dipinjam. Mengalihkan ke form peminjaman...',
        ]);
    }
}