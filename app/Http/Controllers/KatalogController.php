<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Peminjaman;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KatalogController extends Controller
{
    public function index(Request $request)
    {
        $user  = Auth::user();
        $labScope = $user->laboratorium_scoped; // null = semua lab, string = lab tertentu

        $query = Barang::query()->where('kondisi', 'Baik');

        // Filter berdasarkan lab user (kelas/role based)
        if ($labScope) {
            if ($labScope === 'Laboratorium TKJ') {
                $query->where(function ($q) use ($labScope) {
                    $q->where('laboratorium', $labScope)
                      ->orWhereNull('laboratorium')
                      ->orWhere('laboratorium', '');
                });
            } else {
                $query->where('laboratorium', $labScope);
            }
        }

        // Pencarian multi-kolom
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('kode_barang', 'like', "%{$search}%")
                  ->orWhere('merk', 'like', "%{$search}%")
                  ->orWhere('lokasi', 'like', "%{$search}%");
            });
        }

        // Filter Kategori
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // Ambil daftar kategori unik langsung dari database untuk dropdown
        $kategoriList = Barang::whereNotNull('kategori')
            ->when($labScope, fn($q) => $q->where('laboratorium', $labScope))
            ->distinct()
            ->orderBy('kategori')
            ->pluck('kategori');

        // 12 item per halaman agar simetris pada 2, 3, dan 4 kolom
        $barangs = $query->orderBy('kategori')
                         ->orderBy('nama_barang')
                         ->paginate(12)
                         ->withQueryString();

        return view('katalog.index', compact('barangs', 'kategoriList', 'labScope'));
    }
    public function show(Barang $barang)
    {
        $activeLoans = Peminjaman::where('barang_id', $barang->id)
            ->whereIn('status', ['Menunggu Persetujuan', 'Dipinjam', 'Terlambat'])
            ->whereNotNull('unit_index')
            ->get();
            
        $borrowedUnits = [];
        foreach ($activeLoans as $loan) {
            $indices = explode(',', $loan->unit_index);
            foreach ($indices as $idx) {
                if (trim($idx) !== '') {
                    $borrowedUnits[] = (int)trim($idx);
                }
            }
        }

        return view('katalog.show', compact('barang', 'borrowedUnits'));
    }

    public function storePinjam(Request $request, Barang $barang)
    {
        $stokTersedia = (int) ($barang->stok_tersedia ?? $barang->jumlah ?? 0);

        if ($stokTersedia <= 0) {
            return back()->with('error', 'Maaf, stok alat ini sedang habis atau seluruhnya sedang dipinjam.')->withInput();
        }

        $validated = $request->validate([
            'jumlah_pinjam'           => ['required', 'integer', 'min:1', 'max:' . $stokTersedia],
            'tanggal_pinjam'          => ['required', 'date'],
            'tanggal_kembali_rencana' => ['required', 'date', 'after_or_equal:tanggal_pinjam'],
            'keperluan'               => ['required', 'string', 'min:5', 'max:500'],
            'kontak'                  => ['nullable', 'string', 'max:50'],
            'catatan'                 => ['nullable', 'string', 'max:1000'],
            'unit_index'              => ['nullable', 'string'],
        ], [
            'jumlah_pinjam.max'                   => 'Jumlah pinjam melebihi stok yang tersedia (' . $stokTersedia . ' ' . ($barang->satuan ?? 'Unit') . ').',
            'tanggal_kembali_rencana.after_or_equal' => 'Tanggal rencana kembali tidak boleh sebelum tanggal pinjam.',
            'keperluan.min'                       => 'Mohon isi keperluan peminjaman minimal 5 karakter.',
        ]);

        // Cek kondisi unit jika unit spesifik dipilih (bisa lebih dari satu unit dipisah koma)
        if ($request->filled('unit_index')) {
            $selectedIndices = array_filter(array_map('intval', explode(',', $request->unit_index)));

            // Validasi ketersediaan unit (apakah sedang dipinjam)
            $activeLoans = Peminjaman::where('barang_id', $barang->id)
                ->whereIn('status', ['Menunggu Persetujuan', 'Dipinjam', 'Terlambat'])
                ->whereNotNull('unit_index')
                ->get();
                
            $borrowedUnits = [];
            foreach ($activeLoans as $loan) {
                $indices = explode(',', $loan->unit_index);
                foreach ($indices as $idx) {
                    if (trim($idx) !== '') {
                        $borrowedUnits[] = (int)trim($idx);
                    }
                }
            }

            $kondisiPerUnit = [];
            if ($barang->kondisi_per_unit) {
                $kondisiPerUnit = json_decode($barang->kondisi_per_unit, true) ?: [];
            }

            foreach ($selectedIndices as $unitIdx) {
                // Validasi indeks unit
                if ($unitIdx < 1 || $unitIdx > $barang->jumlah) {
                    return back()->with('error', 'Pilihan unit ' . $unitIdx . ' tidak valid.')->withInput();
                }

                // Validasi kondisi unit
                $unitKondisi = $kondisiPerUnit[$unitIdx] ?? $barang->kondisi;
                if ($unitKondisi !== 'Baik') {
                    return back()->with('error', 'Maaf, Unit ' . $unitIdx . ' sedang dalam kondisi ' . $unitKondisi . ' dan tidak dapat dipinjam.')->withInput();
                }

                // Cek ketersediaan
                if (in_array($unitIdx, $borrowedUnits)) {
                    return back()->with('error', 'Maaf, Unit ' . $unitIdx . ' saat ini sedang dipinjam oleh orang lain.')->withInput();
                }
            }

            // Jika memilih unit spesifik, otomatis jumlah pinjam adalah jumlah unit yang dipilih
            $validated['jumlah_pinjam'] = count($selectedIndices);
        }

        return DB::transaction(function () use ($validated, $barang, $request) {
            // Hitung ulang stok di dalam transaksi untuk memastikan konsistensi
            $stokTerkini = (int) ($barang->stok_tersedia ?? $barang->jumlah ?? 0);

            if ($stokTerkini < $validated['jumlah_pinjam']) {
                return back()->with('error', 'Stok alat yang tersedia saat ini tidak mencukupi (' . $stokTerkini . ' ' . ($barang->satuan ?? 'Unit') . ').')->withInput();
            }

            $user = Auth::user();
            $kodePeminjaman = 'PINJAM-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

            $peminjaman = Peminjaman::create([
                'barang_id'               => $barang->id,
                'user_id'                 => Auth::id(),
                'kode_peminjaman'         => $kodePeminjaman,
                'nama_peminjam'           => $user->name ?? 'Peminjam',
                'kelas_atau_jabatan'      => $user->kelas_atau_jabatan ?? $user->role ?? '-',
                'kontak'                  => $validated['kontak'] ?? $user->telepon ?? $user->no_hp ?? '-',
                'keperluan'               => $validated['keperluan'],
                'jumlah_pinjam'           => $validated['jumlah_pinjam'],
                'unit_index'              => $request->unit_index ?? null,
                'tanggal_pinjam'          => $validated['tanggal_pinjam'],
                'tanggal_kembali_rencana' => $validated['tanggal_kembali_rencana'],
                'status'                  => 'Dipinjam',
                'catatan'                 => $validated['catatan'] ?? null,
            ]);

            $peminjaman->load('barang');

            // Kirim Notifikasi ke Bot Telegram
            try {
                TelegramService::sendPeminjamanBaru($peminjaman);
            } catch (\Throwable $e) {
                // Silence error agar peminjaman tetap sukses
            }

            return redirect()->route('peminjaman.saya')->with('success', 'Pengajuan peminjaman barang berhasil dicatat! Kode: ' . $kodePeminjaman);
        });
    }
}