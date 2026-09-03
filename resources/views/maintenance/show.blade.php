@extends('layouts.app')

@section('title', 'Detail Maintenance')
@section('page_title', 'Detail Maintenance')
@section('page_subtitle', 'Informasi lengkap data perbaikan atau perawatan barang')

@section('content')
<div class="max-w-4xl mx-auto space-y-4 sm:space-y-6" x-data="maintenanceShow()">
    <!-- Breadcrumb & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4 no-print">
        <nav class="flex items-center space-x-2 text-xs text-slate-500 min-w-0" aria-label="Breadcrumb">
            <a href="{{ route('maintenance.index') }}" class="hover:text-indigo-600 font-semibold transition-colors">Maintenance</a>
            <svg class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="font-bold text-slate-800 truncate">Detail Maintenance</span>
        </nav>
        
        @php
            $canManageThis = Auth::user()->isAdmin() || (Auth::user()->isKoordinatorLab() && Auth::user()->laboratorium_penugasan === $maintenance->laboratorium);
        @endphp

        {{-- Professional Action Buttons --}}
        <div class="flex items-center gap-2.5 self-start sm:self-auto">
            @if($canManageThis)
                <!-- Edit Button (Aktif) -->
                <a href="{{ route('maintenance.edit', $maintenance->id) }}" 
                   title="Edit Data Maintenance"
                   class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-600 border border-blue-200/80 flex items-center justify-center transition active:scale-95 shadow-sm">
                    <svg class="w-4 h-4 sm:w-4.5 sm:h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </a>

                <!-- Delete Button (Aktif) -->
                <button type="button" 
                        @click="deleteModal = true"
                        title="Hapus Data Maintenance"
                        class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200/80 flex items-center justify-center transition active:scale-95 shadow-sm">
                    <svg class="w-4 h-4 sm:w-4.5 sm:h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            @else
                <!-- Edit Button (Disabled) -->
                <button type="button" 
                        disabled
                        title="Anda tidak memiliki izin mengedit data ini (Khusus {{ $maintenance->laboratorium ?: 'Lab' }})"
                        class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-slate-100 text-slate-400 border border-slate-200 flex items-center justify-center cursor-not-allowed opacity-60">
                    <svg class="w-4 h-4 sm:w-4.5 sm:h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </button>

                <!-- Delete Button (Disabled) -->
                <button type="button" 
                        disabled
                        title="Anda tidak memiliki izin menghapus data ini (Khusus {{ $maintenance->laboratorium ?: 'Lab' }})"
                        class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-slate-100 text-slate-400 border border-slate-200 flex items-center justify-center cursor-not-allowed opacity-60">
                    <svg class="w-4 h-4 sm:w-4.5 sm:h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>

                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[11px] font-bold bg-slate-100 text-slate-500 border border-slate-200">
                    <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <span>Read Only</span>
                </span>
            @endif
        </div>
    </div>

    <!-- Main Detail Card -->
    <div class="bg-white shadow-sm border border-slate-200/60 rounded-2xl sm:rounded-3xl overflow-hidden p-5 sm:p-8">
        
        {{-- Header Detail dengan Keterangan Label Eksplisit --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center border-b border-slate-100 pb-5 mb-6 gap-4">
            <div>
                <div class="mb-2">
                    @if($maintenance->laboratorium === 'Laboratorium AKL')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200/80 shadow-xs">
                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            <span>Laboratorium AKL</span>
                        </span>
                    @elseif($maintenance->laboratorium === 'Laboratorium Pemasaran')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-extrabold bg-amber-50 text-amber-700 border border-amber-200/80 shadow-xs">
                            <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                            <span>Laboratorium Pemasaran</span>
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-extrabold bg-indigo-50 text-indigo-700 border border-indigo-200/80 shadow-xs">
                            <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <span>{{ $maintenance->laboratorium ?: 'Laboratorium TKJ' }}</span>
                        </span>
                    @endif
                </div>
                @if($maintenance->barang)
                    <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 leading-tight">
                        <a href="{{ route('barang.show', $maintenance->barang_id) }}" class="hover:text-indigo-600 transition-colors">
                            {{ $maintenance->barang->nama_barang }}
                        </a>
                    </h2>
                    <div class="flex items-center gap-2 mt-1.5">
                        <span class="text-xs font-mono font-bold text-indigo-700 bg-indigo-50 border border-indigo-100 px-2.5 py-0.5 rounded-lg">{{ $maintenance->barang->kode_barang }}</span>
                        <span class="text-xs text-slate-400 font-medium">Kategori: {{ $maintenance->barang->kategori }}</span>
                    </div>
                @else
                    <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 leading-tight flex items-center gap-2">
                        <span class="w-8 h-8 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </span>
                        <span>Pemeliharaan Fasilitas / Ruangan Lab</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-1">Pemeliharaan infrastruktur, kelistrikan, tata ruang, atau perawatan rutin laboratorium</p>
                @endif
            </div>
            
            {{-- Keterangan Jenis & Status Maintenance --}}
            <div class="flex flex-wrap items-center gap-3 self-stretch md:self-auto justify-start md:justify-end bg-slate-50 md:bg-transparent p-3 md:p-0 rounded-xl">
                <!-- Jenis Maintenance Badge -->
                <div class="flex items-center gap-1.5">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Jenis:</span>
                    @if($maintenance->jenis === 'Preventif')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200/70 shadow-sm">
                            Preventif
                        </span>
                    @elseif($maintenance->jenis === 'Korektif')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-orange-50 text-orange-700 border border-orange-200/70 shadow-sm">
                            Korektif
                        </span>
                    @elseif($maintenance->jenis === 'Penggantian')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200/70 shadow-sm">
                            Penggantian
                        </span>
                    @endif
                </div>

                <div class="hidden md:block w-px h-6 bg-slate-200"></div>

                <!-- Status Maintenance Badge -->
                <div class="flex items-center gap-1.5">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Status:</span>
                    @if($maintenance->status === 'Selesai')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/70 shadow-sm">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <span>Selesai</span>
                        </span>
                    @elseif($maintenance->status === 'Proses')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200/70 shadow-sm">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                            <span>Sedang Proses</span>
                        </span>
                    @elseif($maintenance->status === 'Pending')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200/70 shadow-sm">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                            <span>Pending</span>
                        </span>
                    @endif
                </div>
            </div>
        </div>
        
        {{-- CARD INFORMASI UNIT / FASILITAS YANG DI-MAINTENANCE --}}
        @if($maintenance->barang)
            @php
                $units = !empty($maintenance->unit_index) 
                    ? array_filter(array_map('trim', explode(',', (string) $maintenance->unit_index))) 
                    : [];
            @endphp
            <div class="bg-indigo-50/50 rounded-2xl p-4 sm:p-5 border border-indigo-100/80 mb-6">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-xl bg-indigo-600 text-white flex items-center justify-center shadow-sm shadow-indigo-500/20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-xs sm:text-sm font-extrabold text-slate-800">Unit Barang yang Di-maintenance</h4>
                            <p class="text-[11px] text-slate-500">Daftar unit spesifik yang sedang dalam penanganan</p>
                        </div>
                    </div>
                    <div>
                        @if(count($units) > 0)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold bg-indigo-600 text-white shadow-sm">
                                Total: {{ count($units) }} Unit
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-slate-200 text-slate-700">
                                Seluruh Unit (General)
                            </span>
                        @endif
                    </div>
                </div>

                @if(count($units) > 0)
                    <div class="flex flex-wrap gap-2 pt-1 border-t border-indigo-100/60">
                        @foreach($units as $u)
                            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white border border-indigo-200/60 shadow-sm text-xs font-bold text-slate-800">
                                <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                                <span>Unit {{ $u }}</span>
                                <span class="text-[10px] font-mono font-medium text-slate-400">({{ $maintenance->barang->kode_barang }}-{{ $u }})</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-slate-500 pt-1 border-t border-indigo-100/60">
                        Pekerjaan maintenance ini berlaku secara umum untuk seluruh unit barang <span class="font-bold text-slate-700">{{ $maintenance->barang->nama_barang }}</span> (Total {{ $maintenance->barang->jumlah }} {{ $maintenance->barang->satuan }}).
                    </p>
                @endif
            </div>
        @else
            <div class="bg-indigo-50/50 rounded-2xl p-4 sm:p-5 border border-indigo-100/80 mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-indigo-600 text-white flex items-center justify-center shadow-sm shadow-indigo-500/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div>
                        <h4 class="text-xs sm:text-sm font-extrabold text-slate-800">Maintenance Fasilitas / Ruangan Lab</h4>
                        <p class="text-[11px] text-slate-500">Pemeliharaan berkala untuk infrastruktur dan kenyamanan {{ $maintenance->laboratorium ?: 'Laboratorium TKJ' }}</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Metadata Info Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 mb-8 text-xs sm:text-sm">
            <div class="bg-slate-50/70 p-4 rounded-2xl border border-slate-200/60 space-y-3">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Tanggal Maintenance</span>
                    <p class="font-extrabold text-slate-800 mt-0.5">
                        {{ \Carbon\Carbon::parse($maintenance->tanggal_maintenance)->translatedFormat('d F Y') }}
                    </p>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Nama Teknisi</span>
                    <p class="font-bold text-slate-800 mt-0.5">
                        {{ $maintenance->teknisi ?: 'Teknisi Internal Lab' }}
                    </p>
                </div>
            </div>
            
            <div class="bg-slate-50/70 p-4 rounded-2xl border border-slate-200/60 space-y-3">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Biaya Maintenance</span>
                    <p class="font-extrabold text-emerald-700 font-mono text-base mt-0.5">
                        {{ $maintenance->biaya ? 'Rp ' . number_format($maintenance->biaya, 0, ',', '.') : 'Rp 0 ' }}
                    </p>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Dicatat Oleh</span>
                    <p class="font-bold text-slate-800 mt-0.5">
                        {{ $maintenance->user ? $maintenance->user->name : 'Admin' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Content Sections --}}
        <div class="space-y-4">
            <!-- Deskripsi Kerusakan -->
            <div class="bg-amber-50/70 rounded-2xl p-4 sm:p-5 border border-amber-200/60">
                <h3 class="text-xs sm:text-sm font-bold text-amber-900 flex items-center mb-1.5">
                    <svg class="w-4 h-4 mr-2 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    Deskripsi Kerusakan / Perawatan
                </h3>
                <p class="text-xs sm:text-sm text-amber-950 whitespace-pre-line leading-relaxed">{{ $maintenance->deskripsi_kerusakan }}</p>
            </div>

            <!-- Tindakan -->
            @if($maintenance->tindakan)
            <div class="bg-emerald-50/70 rounded-2xl p-4 sm:p-5 border border-emerald-200/60">
                <h3 class="text-xs sm:text-sm font-bold text-emerald-900 flex items-center mb-1.5">
                    <svg class="w-4 h-4 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Tindakan yang Dilakukan
                </h3>
                <p class="text-xs sm:text-sm text-emerald-950 whitespace-pre-line leading-relaxed">{{ $maintenance->tindakan }}</p>
            </div>
            @endif

            <!-- Catatan -->
            @if($maintenance->catatan)
            <div class="bg-slate-50 rounded-2xl p-4 sm:p-5 border border-slate-200/60">
                <h3 class="text-xs sm:text-sm font-bold text-slate-700 flex items-center mb-1.5">
                    <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Catatan Tambahan
                </h3>
                <p class="text-xs sm:text-sm text-slate-600 whitespace-pre-line leading-relaxed">{{ $maintenance->catatan }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- MODAL KONFIRMASI HAPUS TENGAH LAYAR (Teleport ke body agar Full Screen Blur) --}}
    <template x-teleport="body">
        <div x-show="deleteModal" class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-md" style="display: none;"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            <div @click.away="if(!isDeleting) deleteModal = false" class="bg-white rounded-3xl p-6 sm:p-7 w-full max-w-sm sm:max-w-md shadow-2xl border border-slate-100 text-center"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-90 translate-y-3"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-90 translate-y-3">
                
                <div class="w-14 h-14 bg-rose-100 text-rose-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-sm border border-rose-200/60">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>

                <h3 class="text-base sm:text-lg font-extrabold text-slate-900">Hapus Riwayat Maintenance?</h3>
                <p class="text-xs sm:text-sm text-slate-500 mt-1.5 leading-relaxed">
                    Data maintenance untuk <strong class="text-slate-800">{{ $maintenance->barang ? $maintenance->barang->nama_barang : ($maintenance->laboratorium . ' (Fasilitas Ruangan)') }}</strong> akan dihapus permanen.
                </p>

                <form id="deleteMaintenanceFormShow" action="{{ route('maintenance.destroy', $maintenance->id) }}" method="POST" class="mt-6 flex items-center justify-center gap-3">
                    @csrf
                    @method('DELETE')
                    
                    <button type="button" 
                            :disabled="isDeleting"
                            @click="deleteModal = false" 
                            class="flex-1 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 disabled:opacity-50 text-slate-700 font-semibold rounded-xl text-xs sm:text-sm transition-colors">
                        Batal
                    </button>
                    <button type="button" 
                            :disabled="isDeleting"
                            @click="submitDelete()" 
                            class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-rose-600 hover:bg-rose-700 disabled:bg-rose-400 text-white font-bold rounded-xl text-xs sm:text-sm shadow-md shadow-rose-600/20 transition-all active:scale-95">
                        <svg x-show="isDeleting" class="animate-spin -ml-1 mr-1 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" style="display: none;">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="isDeleting ? 'Menghapus...' : 'Ya, Hapus'"></span>
                    </button>
                </form>
            </div>
        </div>
    </template>
</div>

<script>
function maintenanceShow() {
    return {
        deleteModal: false,
        isDeleting: false,

        submitDelete() {
            this.isDeleting = true;
            setTimeout(() => {
                document.getElementById('deleteMaintenanceFormShow').submit();
            }, 300);
        }
    };
}
</script>
@endsection