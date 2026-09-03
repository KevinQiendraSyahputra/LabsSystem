<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Peminjaman;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PeminjamanController extends Controller
{
    public function index(Request $request)
    {
        $query = Peminjaman::with('barang', 'user');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $term = trim($request->search);
            $query->where(function($q) use ($term) {
                $q->where('nama_peminjam', 'like', '%' . $term . '%')
                  ->orWhere('kode_peminjaman', 'like', '%' . $term . '%')
                  ->orWhereHas('barang', fn($b) => $b->where('nama_barang', 'like', '%' . $term . '%'));
            });
        }

        // Auto update status terlambat
        Peminjaman::where('status', 'Dipinjam')
            ->where('tanggal_kembali_rencana', '<', now()->toDateString())
            ->update(['status' => 'Terlambat']);

        $peminjamans = $query->latest('id')->paginate(10)->withQueryString();
        return view('peminjaman.index', compact('peminjamans'));
    }

    public function create()
    {
        $barangs = Barang::where('kondisi', 'Baik')->orderBy('nama_barang')->get();
        return view('peminjaman.create', compact('barangs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'barang_id'                => 'required|exists:barangs,id',
            'nama_peminjam'            => 'required|string|max:255',
            'kelas_atau_jabatan'       => 'nullable|string|max:100',
            'kontak'                   => 'nullable|string|max:50',
            'keperluan'                => 'required|string',
            'jumlah_pinjam'            => 'required|integer|min:1',
            'tanggal_pinjam'           => 'required|date',
            'tanggal_kembali_rencana'  => 'required|date|after_or_equal:tanggal_pinjam',
            'catatan'                  => 'nullable|string',
        ]);

        $barang = Barang::findOrFail($validated['barang_id']);

        if ($validated['jumlah_pinjam'] > $barang->stok_tersedia) {
            return back()->withErrors(['jumlah_pinjam' => 'Jumlah pinjam melebihi stok yang tersedia (' . $barang->stok_tersedia . ' ' . $barang->satuan . ')'])->withInput();
        }

        $validated['user_id'] = Auth::id();
        $validated['kode_peminjaman'] = 'PINJAM-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
        $validated['status']  = 'Dipinjam';

        $peminjaman = Peminjaman::create($validated);
        $peminjaman->load('barang');

        // Kirim Notifikasi ke Bot Telegram
        try {
            TelegramService::sendPeminjamanBaru($peminjaman);
        } catch (\Throwable $e) {
            // Silence error agar transaksi tetap berhasil
        }

        return redirect()->route('peminjaman.index')->with('success', 'Peminjaman alat berhasil dicatat!');
    }

    public function show(Peminjaman $peminjaman)
    {
        $peminjaman->load('barang', 'user');

        // Pastikan hanya admin, guru, atau pemilik peminjaman yang bisa melihat
        if (!Auth::user()->isAdmin() && !Auth::user()->isGuru() && $peminjaman->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat data peminjaman ini.');
        }

        return view('peminjaman.show', compact('peminjaman'));
    }

    /**
     * Endpoint API JSON untuk Live Realtime Status Sync
     */
    public function statusJson(Peminjaman $peminjaman)
    {
        $peminjaman->load('barang', 'user');

        // Cek hak akses
        if (!Auth::user()->isAdmin() && !Auth::user()->isGuru() && $peminjaman->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'id'               => $peminjaman->id,
            'status'           => $peminjaman->status,
            'tanggal_pinjam'   => $peminjaman->tanggal_pinjam ? \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->translatedFormat('d F Y') : '-',
            'tanggal_kembali'  => $peminjaman->tanggal_kembali_rencana ? \Carbon\Carbon::parse($peminjaman->tanggal_kembali_rencana)->translatedFormat('d F Y') : ($peminjaman->tanggal_kembali ? \Carbon\Carbon::parse($peminjaman->tanggal_kembali)->translatedFormat('d F Y') : '-'),
            'tanggal_selesai'  => $peminjaman->tanggal_kembali_aktual ? \Carbon\Carbon::parse($peminjaman->tanggal_kembali_aktual)->translatedFormat('d F Y') : '— (Belum Selesai)',
            'kondisi_kembali'  => $peminjaman->kondisi_kembali ?? '—',
            'catatan'          => $peminjaman->catatan ?? 'Tidak ada catatan khusus.',
            'alasan_penolakan' => $peminjaman->alasan_penolakan ?? null,
        ]);
    }

    public function ajukanKembali(Request $request, Peminjaman $peminjaman)
    {
        if (!Auth::user()->isAdmin() && $peminjaman->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'catatan_pengembalian' => 'nullable|string',
        ]);

        $peminjaman->update([
            'status'  => 'Menunggu Persetujuan',
            'catatan' => $request->catatan_pengembalian ? ($peminjaman->catatan ? $peminjaman->catatan . " | Catatan Kembali: " . $request->catatan_pengembalian : "Catatan Kembali: " . $request->catatan_pengembalian) : $peminjaman->catatan,
        ]);

        $peminjaman->load('barang');

        try {
            TelegramService::sendAjukanKembali($peminjaman, $request->catatan_pengembalian);
        } catch (\Throwable $e) {
            // Silence error
        }

        return back()->with('success', 'Pengajuan pengembalian berhasil dikirim! Menunggu persetujuan admin laboratorium.');
    }

    public function konfirmasiKembali(Request $request, Peminjaman $peminjaman)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Hanya admin yang dapat mengonfirmasi pengembalian barang.');
        }

        $request->validate([
            'tanggal_kembali_aktual' => 'required|date',
            'kondisi_kembali'        => 'required|in:Baik,Perawatan,Perbaikan,Rusak Berat,Hilang',
            'catatan'                => 'nullable|string',
        ]);

        $peminjaman->update([
            'tanggal_kembali_aktual' => $request->tanggal_kembali_aktual,
            'kondisi_kembali'        => $request->kondisi_kembali,
            'status'                 => 'Dikembalikan',
            'catatan'                => $request->catatan,
            'approved_by'            => Auth::id(),
        ]);

        if ($request->kondisi_kembali !== 'Baik') {
            $peminjaman->barang->update(['kondisi' => $request->kondisi_kembali]);
        }

        $peminjaman->load('barang');

        try {
            TelegramService::sendKonfirmasiKembali($peminjaman);
        } catch (\Throwable $e) {
            // Silence error
        }

        return redirect()->route('peminjaman.show', $peminjaman)->with('success', 'Pengembalian alat berhasil disetujui & dicatat sebagai Selesai!');
    }

    public function tolakKembali(Request $request, Peminjaman $peminjaman)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Hanya admin yang dapat menolak pengembalian barang.');
        }

        $request->validate([
            'alasan_penolakan' => 'required|string',
        ]);

        $peminjaman->update([
            'status'          => 'Dipinjam',
            'alasan_penolakan' => $request->alasan_penolakan,
        ]);

        return redirect()->route('peminjaman.show', $peminjaman)->with('error', 'Pengajuan pengembalian ditolak.');
    }

    public function destroy(Peminjaman $peminjaman)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Hanya admin yang diizinkan untuk menghapus data peminjaman.');
        }

        $peminjaman->delete();
        return redirect()->route('peminjaman.index')->with('success', 'Data peminjaman berhasil dihapus!');
    }
}