<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserPeminjamanController extends Controller
{
    public function index(Request $request)
    {
        $query = Peminjaman::with('barang')
            ->where('user_id', Auth::id());

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Update status terlambat
        Peminjaman::where('user_id', Auth::id())
            ->where('status', 'Dipinjam')
            ->where('tanggal_kembali_rencana', '<', now()->toDateString())
            ->update(['status' => 'Terlambat']);

        $peminjamans = $query->latest()->paginate(10)->withQueryString();

        $stats = [
            'total'       => Peminjaman::where('user_id', Auth::id())->count(),
            'dipinjam'    => Peminjaman::where('user_id', Auth::id())->where('status', 'Dipinjam')->count(),
            'dikembalikan'=> Peminjaman::where('user_id', Auth::id())->where('status', 'Dikembalikan')->count(),
            'terlambat'   => Peminjaman::where('user_id', Auth::id())->where('status', 'Terlambat')->count(),
        ];

        return view('peminjaman.saya', compact('peminjamans', 'stats'));
    }
}
