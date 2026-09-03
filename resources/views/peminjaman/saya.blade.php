@extends('layouts.app')

@section('title', 'Peminjaman Saya')
@section('page_title', 'Peminjaman Saya')
@section('page_subtitle', 'Riwayat dan status peminjaman alat laboratorium Anda')

@section('content')

{{-- Stat Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6 mb-6 sm:mb-8">
    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 shadow-xs flex items-center gap-4">
        <div class="w-11 h-11 sm:w-12 sm:h-12 bg-sky-50 rounded-xl flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
            </svg>
        </div>
        <div>
            <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">Sedang Dipinjam</p>
            <p class="text-xl sm:text-2xl font-black text-slate-900 mt-0.5">{{ $peminjamans->whereIn('status', ['Dipinjam', 'Menunggu Persetujuan'])->count() }} <span class="text-xs font-medium text-slate-500">transaksi</span></p>
        </div>
    </div>

    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 shadow-xs flex items-center gap-4">
        <div class="w-11 h-11 sm:w-12 sm:h-12 bg-emerald-50 rounded-xl flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <div>
            <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">Selesai Dikembalikan</p>
            <p class="text-xl sm:text-2xl font-black text-slate-900 mt-0.5">{{ $peminjamans->where('status', 'Dikembalikan')->count() }} <span class="text-xs font-medium text-slate-500">transaksi</span></p>
        </div>
    </div>

    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 shadow-xs flex items-center gap-4">
        <div class="w-11 h-11 sm:w-12 sm:h-12 bg-rose-50 rounded-xl flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">Terlambat Kembali</p>
            <p class="text-xl sm:text-2xl font-black text-rose-700 mt-0.5">{{ $peminjamans->filter(fn($p) => $p->isTerlambat())->count() }} <span class="text-xs font-medium text-slate-500">transaksi</span></p>
        </div>
    </div>
</div>

{{-- Main Table Container Card dengan Header Background Biru Tua (Slate-900 / Navy) --}}
<div class="bg-white rounded-2xl sm:rounded-3xl shadow-sm border border-slate-200/90 overflow-hidden">
    
    {{-- Card Header --}}
    <div class="bg-slate-900 px-5 sm:px-6 py-4 sm:py-5 border-b border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4">
        <div>
            <h3 class="text-lg sm:text-xl font-bold text-white tracking-tight">Daftar Peminjaman Saya</h3>
            <p class="text-xs sm:text-sm text-slate-300 mt-0.5 font-medium">Semua riwayat peminjaman peralatan laboratorium TKJ</p>
        </div>
        <a href="{{ route('katalog.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs sm:text-sm font-bold px-4 py-2.5 rounded-xl transition shadow-md shadow-indigo-950/30 active:scale-95">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            Pinjam Alat Baru
        </a>
    </div>

    {{-- 1. TAMPILAN MOBILE (Card View) --}}
    <div class="block sm:hidden divide-y divide-slate-100">
        @forelse($peminjamans as $pinjam)
            <div class="p-4 space-y-3 hover:bg-slate-50/60 transition border-l-4 border-l-indigo-600">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <span class="font-mono font-bold text-indigo-700 text-xs">
                            {{ $pinjam->kode_peminjaman ?? ('PINJAM-' . str_pad($pinjam->id, 4, '0', STR_PAD_LEFT)) }}
                        </span>
                        <h4 class="font-extrabold text-slate-900 text-sm mt-0.5">
                            {{ $pinjam->barang->nama_barang ?? 'Barang Dihapus' }}
                        </h4>
                    </div>
                    <div class="shrink-0">
                        <span class="text-xs font-black text-slate-900">
                            {{ $pinjam->isTerlambat() && $pinjam->status !== 'Dikembalikan' ? 'Terlambat' : ($pinjam->status === 'Menunggu Persetujuan' ? 'Menunggu' : $pinjam->status) }}
                        </span>
                    </div>
                </div>

                <div class="bg-slate-50 rounded-xl p-3 border border-slate-200 text-xs space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Jumlah Unit:</span>
                        <span class="font-bold text-slate-800">{{ $pinjam->jumlah_pinjam }} {{ $pinjam->barang->satuan ?? 'Unit' }}</span>
                    </div>
                    @if($pinjam->unit_index)
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Unit Spesifik:</span>
                            <span class="font-bold text-slate-900">Unit {{ implode(', ', array_map('trim', explode(',', $pinjam->unit_index))) }}</span>
                        </div>
                    @endif
                    <div class="flex items-center justify-between pt-1 border-t border-slate-200 text-xs">
                        <span class="text-slate-500">Batas Kembali:</span>
                        <span class="font-bold text-slate-800">
                            {{ $pinjam->tanggal_kembali_rencana->format('d M Y') }}
                        </span>
                    </div>
                </div>

                <div class="flex items-center justify-end pt-1">
                    <a href="{{ route('peminjaman.show', $pinjam->id) }}" class="w-full inline-flex items-center justify-center gap-1.5 text-xs font-bold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 px-4 py-2.5 rounded-xl shadow-xs transition active:scale-95">
                        <span>Detail & Pengembalian</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        @empty
            <div class="p-8 text-center text-slate-500">
                <svg class="w-10 h-10 mx-auto mb-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                <p class="font-bold text-sm text-slate-700">Belum ada riwayat peminjaman.</p>
                <p class="text-xs text-slate-500 mt-0.5">Alat yang Anda pinjam akan tercatat otomatis di halaman ini.</p>
            </div>
        @endforelse
    </div>

    {{-- 2. TAMPILAN DESKTOP (Tabel) --}}
    <div class="hidden sm:block overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50/90 text-slate-600 border-b border-slate-200">
                <tr class="text-xs uppercase tracking-wider font-bold text-slate-600">
                    <th class="px-6 py-3.5 font-bold">Kode Transaksi</th>
                    <th class="px-6 py-3.5 font-bold">Nama Alat</th>
                    <th class="px-6 py-3.5 text-center font-bold">Jumlah</th>
                    <th class="px-6 py-3.5 font-bold">Tgl Pinjam</th>
                    <th class="px-6 py-3.5 font-bold">Batas Kembali</th>
                    <th class="px-6 py-3.5 text-center font-bold">Status</th>
                    <th class="px-6 py-3.5 text-center font-bold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs sm:text-sm bg-white">
                @forelse($peminjamans as $pinjam)
                    <tr class="hover:bg-indigo-50/40 transition-colors">
                        <td class="px-6 py-4 font-mono font-bold text-indigo-700">
                            {{ $pinjam->kode_peminjaman ?? ('PINJAM-' . str_pad($pinjam->id, 4, '0', STR_PAD_LEFT)) }}
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-bold text-slate-800">{{ $pinjam->barang->nama_barang ?? 'Barang Dihapus' }}</p>
                            <p class="text-xs text-slate-500 font-mono">{{ $pinjam->barang->kode_barang ?? '-' }} • {{ $pinjam->barang->kategori ?? '-' }}</p>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <p class="font-bold text-slate-700">{{ $pinjam->jumlah_pinjam }} {{ $pinjam->barang->satuan ?? 'Unit' }}</p>
                            @if($pinjam->unit_index)
                                <p class="text-xs font-bold text-slate-900">Unit {{ implode(', ', array_map('trim', explode(',', $pinjam->unit_index))) }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-600">
                            {{ $pinjam->tanggal_pinjam->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-bold text-slate-800">
                                {{ $pinjam->tanggal_kembali_rencana->format('d M Y') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-xs font-black text-slate-900">
                                {{ $pinjam->isTerlambat() && $pinjam->status !== 'Dikembalikan' ? 'Terlambat' : $pinjam->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('peminjaman.show', $pinjam->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 shadow-xs transition active:scale-95">
                                <span>Detail / Kembalikan</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                            <div class="w-12 h-12 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-2">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                            </div>
                            <p class="font-bold text-sm text-slate-700">Belum ada riwayat peminjaman.</p>
                            <p class="text-xs text-slate-500 mt-0.5">Alat yang Anda pinjam akan tercatat otomatis di halaman ini.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($peminjamans->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $peminjamans->links() }}
        </div>
    @endif
</div>
@endsection