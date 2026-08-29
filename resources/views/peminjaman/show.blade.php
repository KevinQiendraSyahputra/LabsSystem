@extends('layouts.app')

@section('title', 'Detail Peminjaman — ' . ($peminjaman->kode_peminjaman ?? 'PINJAM-'.$peminjaman->id))
@section('page_title', 'Detail Transaksi Peminjaman')
@section('page_subtitle', 'Rincian data peminjaman dan verifikasi status pengembalian alat')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" 
     x-data="{ 
         showKembaliModal: false, 
         showApproveModal: false, 
         showRejectModal: false, 
         showDeleteModal: false,
         isDeleting: false,
         isApproving: false,
         isRejecting: false,
         isReturning: false
     }">

    {{-- Breadcrumb & Actions --}}
    <div class="flex items-center justify-between">
        <nav class="flex items-center space-x-2 text-xs text-slate-500">
            @if(Auth::user()->isAdmin())
                <a href="{{ route('peminjaman.index') }}" class="hover:text-indigo-600">Kelola Peminjaman</a>
            @else
                <a href="{{ route('peminjaman.saya') }}" class="hover:text-indigo-600">Peminjaman Saya</a>
            @endif
            <span>/</span>
            <span class="font-bold text-slate-800">{{ $peminjaman->kode_peminjaman ?? ('PINJAM-' . str_pad($peminjaman->id, 4, '0', STR_PAD_LEFT)) }}</span>
        </nav>

        {{-- HAPUS HANYA UNTUK ADMIN --}}
        @if(Auth::user()->isAdmin())
            <button type="button" @click="showDeleteModal = true" title="Hapus Peminjaman" class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200/80 flex items-center justify-center transition active:scale-95 shadow-sm">
                <svg class="w-4 h-4 sm:w-4.5 sm:h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </button>
        @endif
    </div>

    <!-- Main Detail Card -->
    <div class="bg-white shadow-sm border border-slate-200/60 rounded-3xl overflow-hidden relative p-6 sm:p-8">
        <div class="flex flex-wrap items-center justify-between gap-4 pb-6 mb-6 border-b border-slate-100">
            <div>
                <span class="text-xs font-mono font-bold text-indigo-600 uppercase">{{ $peminjaman->kode_peminjaman ?? ('PINJAM-' . str_pad($peminjaman->id, 4, '0', STR_PAD_LEFT)) }}</span>
                <h2 class="text-xl font-extrabold text-slate-800 mt-0.5">Informasi Peminjaman Alat</h2>
            </div>

            <div>
                @if($peminjaman->status === 'Dikembalikan')
                    <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 shadow-sm">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Dikembalikan (Selesai)
                    </span>
                @elseif($peminjaman->status === 'Menunggu Persetujuan')
                    <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200 shadow-sm animate-pulse">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Menunggu Persetujuan Admin
                    </span>
                @elseif($peminjaman->isTerlambat())
                    <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-red-100 text-red-800 border border-red-200 shadow-sm animate-pulse">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Terlambat
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-sky-100 text-sky-800 border border-sky-200 shadow-sm">
                        <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        Sedang Dipinjam
                    </span>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
            <!-- Peminjam Info -->
            <div class="bg-slate-50/70 rounded-2xl p-5 border border-slate-200/80 space-y-3">
                <span class="font-bold text-slate-400 uppercase tracking-wider text-[10px]">Identitas Peminjam</span>
                <div>
                    <p class="text-base font-bold text-slate-800">{{ $peminjaman->nama_peminjam }}</p>
                    <p class="text-slate-500 mt-0.5">{{ $peminjaman->kelas_atau_jabatan ?? 'Siswa / Anggota Lab' }}</p>
                    @if($peminjaman->kontak)<p class="text-slate-500 mt-0.5">WA: {{ $peminjaman->kontak }}</p>@endif
                </div>
                <div class="pt-3 border-t border-slate-200">
                    <span class="font-semibold text-slate-500 uppercase text-[10px]">Keperluan Praktikum:</span>
                    <p class="text-slate-800 mt-1 leading-relaxed">{{ $peminjaman->keperluan }}</p>
                </div>
            </div>

            <!-- Barang Info -->
            <div class="bg-slate-50/70 rounded-2xl p-5 border border-slate-200/80 space-y-3">
                <span class="font-bold text-slate-400 uppercase tracking-wider text-[10px]">Barang yang Dipinjam</span>
                <div>
                    <h3 class="text-base font-bold text-slate-900">{{ $peminjaman->barang->nama_barang }}</h3>
                    <p class="font-mono text-slate-500 mt-0.5">{{ $peminjaman->barang->kode_barang }} • {{ $peminjaman->barang->kategori }}</p>
                    <p class="text-slate-600 mt-1">Lokasi Lab: <strong>{{ $peminjaman->barang->lokasi ?? 'Lab TKJ' }}</strong></p>
                </div>
                <div class="pt-3 border-t border-slate-200 flex items-center justify-between">
                    <span class="font-semibold text-slate-500 uppercase text-[10px]">Jumlah & Unit Dipinjam:</span>
                    <div class="text-right">
                        <span class="font-extrabold text-sm text-slate-800 bg-white px-3 py-1 rounded-xl border border-slate-200 shadow-sm inline-block">
                            {{ $peminjaman->jumlah_pinjam }} {{ $peminjaman->barang->satuan }}
                        </span>
                        @if($peminjaman->unit_index)
                            <p class="text-[11px] font-bold text-indigo-600 mt-1 bg-indigo-50 px-2 py-0.5 rounded-lg border border-indigo-100">
                                Unit {{ implode(', Unit ', array_map('trim', explode(',', $peminjaman->unit_index))) }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Waktu Pinjam -->
            <div class="bg-slate-50/70 rounded-2xl p-5 border border-slate-200/80 space-y-2">
                <span class="font-bold text-slate-400 uppercase tracking-wider text-[10px]">Jadwal Peminjaman</span>
                <div class="flex justify-between py-1 border-b border-slate-200">
                    <span class="text-slate-500">Tanggal Pinjam:</span>
                    <span class="font-semibold text-slate-800">{{ $peminjaman->tanggal_pinjam->translatedFormat('d F Y') }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-200">
                    <span class="text-slate-500">Rencana Kembali:</span>
                    <span class="font-semibold {{ $peminjaman->isTerlambat() ? 'text-red-600' : 'text-slate-800' }}">
                        {{ $peminjaman->tanggal_kembali_rencana->translatedFormat('d F Y') }}
                    </span>
                </div>
                <div class="flex justify-between py-1">
                    <span class="text-slate-500">Tanggal Selesai:</span>
                    <span class="font-semibold text-slate-800">
                        {{ $peminjaman->tanggal_kembali_aktual ? $peminjaman->tanggal_kembali_aktual->translatedFormat('d F Y') : '— (Belum Selesai)' }}
                    </span>
                </div>
            </div>

            <!-- Kondisi Saat Kembali & Catatan -->
            <div class="bg-slate-50/70 rounded-2xl p-5 border border-slate-200/80 space-y-2">
                <span class="font-bold text-slate-400 uppercase tracking-wider text-[10px]">Hasil Pengecekan</span>
                <div class="flex justify-between py-1 border-b border-slate-200">
                    <span class="text-slate-500">Kondisi Barang Kembali:</span>
                    <span class="font-bold text-slate-800">{{ $peminjaman->kondisi_kembali ?? '—' }}</span>
                </div>
                @if($peminjaman->catatan)
                    <div class="pt-2">
                        <span class="text-slate-500 block mb-0.5">Catatan:</span>
                        <p class="text-slate-800 italic bg-white p-2.5 rounded-xl border border-slate-200">{{ $peminjaman->catatan }}</p>
                    </div>
                @endif
                @if($peminjaman->alasan_penolakan)
                    <div class="pt-2">
                        <span class="text-red-500 font-bold block mb-0.5">Alasan Penolakan Terakhir:</span>
                        <p class="text-red-700 bg-red-50 p-2.5 rounded-xl border border-red-200">{{ $peminjaman->alasan_penolakan }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- ========================================== --}}
        {{-- TOMBOL AKSI PENGEMBALIAN & PERSETUJUAN --}}
        {{-- ========================================== --}}
        <div class="mt-8 pt-6 border-t border-slate-100 flex flex-wrap items-center justify-end gap-3">
            
            {{-- 1. AKSI USER: Ajukan Pengembalian (Jika status Dipinjam/Terlambat) --}}
            @if(!Auth::user()->isAdmin() && in_array($peminjaman->status, ['Dipinjam', 'Terlambat']))
                <button type="button" @click="showKembaliModal = true" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-6 py-2.5 rounded-xl text-xs shadow-md transition flex items-center gap-2 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                    Ajukan Pengembalian Alat
                </button>
            @endif

            {{-- 2. AKSI ADMIN: Setujui / Konfirmasi Pengembalian --}}
            @if(Auth::user()->isAdmin() && in_array($peminjaman->status, ['Menunggu Persetujuan', 'Dipinjam', 'Terlambat']))
                <button type="button" @click="showApproveModal = true" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-6 py-2.5 rounded-xl text-xs shadow-md transition flex items-center gap-2 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Setujui & Konfirmasi Pengembalian
                </button>

                @if($peminjaman->status === 'Menunggu Persetujuan')
                    <button type="button" @click="showRejectModal = true" class="bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 font-bold px-5 py-2.5 rounded-xl text-xs transition active:scale-95">
                        Tolak Pengembalian
                    </button>
                @endif
            @endif
        </div>
    </div>

    {{-- MODAL ADMIN: Konfirmasi Hapus Peminjaman --}}
    @if(Auth::user()->isAdmin())
    <template x-teleport="body">
        <div x-show="showDeleteModal" 
             class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-md" 
             style="display: none;" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            <div @click.away="if(!isDeleting) showDeleteModal = false" 
                 class="bg-white rounded-3xl p-6 sm:p-7 w-full max-w-md shadow-2xl border border-slate-100"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-2">
                
                <div class="flex items-start gap-4">
                    <div class="p-3 bg-red-100 text-red-600 rounded-2xl shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-base text-slate-900">Hapus Data Peminjaman?</h3>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                            Apakah Anda yakin ingin menghapus data transaksi <strong class="text-slate-700 font-semibold">{{ $peminjaman->kode_peminjaman ?? ('PINJAM-' . str_pad($peminjaman->id, 4, '0', STR_PAD_LEFT)) }}</strong>? Tindakan ini bersifat permanen dan tidak dapat dibatalkan.
                        </p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-5 mt-5 border-t border-slate-100">
                    <button type="button" :disabled="isDeleting" @click="showDeleteModal = false" class="px-4 py-2.5 border border-slate-200 hover:bg-slate-50 text-slate-600 rounded-xl text-xs font-semibold transition disabled:opacity-50">
                        Batal
                    </button>
                    <form action="{{ route('peminjaman.destroy', $peminjaman->id) }}" method="POST" @submit="isDeleting = true" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" :disabled="isDeleting" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 disabled:bg-red-400 text-white rounded-xl text-xs font-bold shadow-md shadow-red-500/20 transition flex items-center gap-1.5 active:scale-95">
                            <svg x-show="isDeleting" class="animate-spin -ml-1 mr-1 h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24" style="display: none;">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="isDeleting ? 'Menghapus...' : 'Ya, Hapus Data'"></span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </template>
    @endif

    {{-- MODAL USER: Ajukan Pengembalian (Full Screen Blur via Teleport) --}}
    <template x-teleport="body">
        <div x-show="showKembaliModal" 
             class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-md" 
             style="display: none;" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            <div @click.away="if(!isReturning) showKembaliModal = false" 
                 class="bg-white rounded-3xl p-6 sm:p-8 w-full max-w-md shadow-2xl border border-slate-100"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-2">
                <h3 class="font-bold text-base text-slate-800 mb-2">Ajukan Pengembalian Alat</h3>
                <p class="text-xs text-slate-500 mb-4">Pengajuan Anda akan diverifikasi oleh Admin/Teknisi Laboratorium saat barang diserahkan kembali.</p>

                <form action="{{ route('peminjaman.ajukan.kembali', $peminjaman->id) }}" method="POST" @submit="isReturning = true" class="space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="block font-semibold text-slate-700 uppercase tracking-wider mb-1">Catatan Tambahan (Opsional)</label>
                        <textarea name="catatan_pengembalian" rows="3" placeholder="Contoh: Alat telah diletakkan di meja teknisi dalam kondisi baik dan lengkap..." class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none resize-none"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" :disabled="isReturning" @click="showKembaliModal = false" class="px-4 py-2 border border-slate-300 text-slate-600 rounded-xl font-medium disabled:opacity-50">Batal</button>
                        <button type="submit" :disabled="isReturning" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 disabled:bg-indigo-400 text-white rounded-xl font-bold transition flex items-center gap-1.5 active:scale-95">
                            <svg x-show="isReturning" class="animate-spin -ml-1 mr-1 h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24" style="display: none;">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="isReturning ? 'Mengirim...' : 'Kirim Pengajuan'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    {{-- MODAL ADMIN: Setujui Pengembalian (Full Screen Blur via Teleport) --}}
    @if(Auth::user()->isAdmin())
    <template x-teleport="body">
        <div x-show="showApproveModal" 
             class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-md" 
             style="display: none;" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            <div @click.away="if(!isApproving) showApproveModal = false" 
                 class="bg-white rounded-3xl p-6 sm:p-8 w-full max-w-md shadow-2xl border border-slate-100"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-2">
                <h3 class="font-bold text-base text-slate-800 mb-1">Persetujuan & Pemeriksaan Fisik Alat</h3>
                <p class="text-xs text-slate-500 mb-4">Periksa kondisi fisik alat sebelum mengonfirmasi pengembalian.</p>

                <form action="{{ route('peminjaman.kembali', $peminjaman->id) }}" method="POST" @submit="isApproving = true" class="space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="block font-semibold text-slate-700 uppercase tracking-wider mb-1">Tanggal Terima Kembali <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_kembali_aktual" value="{{ date('Y-m-d') }}" required class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 uppercase tracking-wider mb-1">Kondisi Alat Saat Diterima <span class="text-red-500">*</span></label>
                        <select name="kondisi_kembali" required class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none font-medium">
                            <option value="Baik">Baik (Siap Digunakan Lagi)</option>
                            <option value="Perawatan">Perawatan (Perlu Pengecekan Ringan)</option>
                            <option value="Perbaikan">Perbaikan (Rusak Ringan / Perlu Servis)</option>
                            <option value="Rusak Berat">Rusak Berat (Tidak Layak Pakai)</option>
                            <option value="Hilang">Hilang / Tidak Dikembalikan</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 uppercase tracking-wider mb-1">Catatan Admin</label>
                        <textarea name="catatan" rows="2" placeholder="Catat kelengkapan kabel, baut, atau kondisi unit..." class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none resize-none"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" :disabled="isApproving" @click="showApproveModal = false" class="px-4 py-2 border border-slate-300 text-slate-600 rounded-xl font-medium disabled:opacity-50">Batal</button>
                        <button type="submit" :disabled="isApproving" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 disabled:bg-emerald-400 text-white rounded-xl font-bold transition flex items-center gap-1.5 active:scale-95">
                            <svg x-show="isApproving" class="animate-spin -ml-1 mr-1 h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24" style="display: none;">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="isApproving ? 'Memproses...' : 'Setujui Pengembalian'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    {{-- MODAL ADMIN: Tolak Pengembalian (Full Screen Blur via Teleport) --}}
    <template x-teleport="body">
        <div x-show="showRejectModal" 
             class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-md" 
             style="display: none;" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            <div @click.away="if(!isRejecting) showRejectModal = false" 
                 class="bg-white rounded-3xl p-6 sm:p-8 w-full max-w-md shadow-2xl border border-slate-100"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-2">
                <h3 class="font-bold text-base text-red-800 mb-1">Tolak Pengajuan Pengembalian</h3>
                <p class="text-xs text-slate-500 mb-4">Berikan alasan penolakan (misal: barang belum diserahkan fisik atau kelengkapan kurang).</p>

                <form action="{{ route('peminjaman.tolak.kembali', $peminjaman->id) }}" method="POST" @submit="isRejecting = true" class="space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="block font-semibold text-slate-700 uppercase tracking-wider mb-1">Alasan Penolakan <span class="text-red-500">*</span></label>
                        <textarea name="alasan_penolakan" required rows="3" placeholder="Contoh: Unit belum diserahkan ke ruang teknisi atau kabel adaptor belum lengkap..." class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-xs focus:ring-2 focus:ring-red-500 focus:outline-none resize-none"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" :disabled="isRejecting" @click="showRejectModal = false" class="px-4 py-2 border border-slate-300 text-slate-600 rounded-xl font-medium disabled:opacity-50">Batal</button>
                        <button type="submit" :disabled="isRejecting" class="px-5 py-2 bg-red-600 hover:bg-red-700 disabled:bg-red-400 text-white rounded-xl font-bold transition flex items-center gap-1.5 active:scale-95">
                            <svg x-show="isRejecting" class="animate-spin -ml-1 mr-1 h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24" style="display: none;">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="isRejecting ? 'Menolak...' : 'Tolak Pengajuan'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>
    @endif

</div>
@endsection