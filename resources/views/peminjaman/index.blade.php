@extends('layouts.app')

@section('content')
<div class="space-y-4 sm:space-y-6" x-data="{
    search: @js(request('search', '')),
    selectedStatus: @js(request('status', '')),
    statusDropdownOpen: false,
    isLoading: false,
    deleteModal: false,
    deleteActionUrl: '',
    deleteItemName: '',

    init() {
        if (typeof this.search !== 'string') this.search = '';
        if (typeof this.selectedStatus !== 'string') this.selectedStatus = '';
    },

    confirmDelete(url, name) {
        this.deleteActionUrl = url;
        this.deleteItemName = name;
        this.deleteModal = true;
    },

    selectStatus(st) {
        this.selectedStatus = st;
        this.statusDropdownOpen = false;
        this.fetchData();
    },

    resetFilters() {
        this.search = '';
        this.selectedStatus = '';
        this.statusDropdownOpen = false;
        this.fetchData();
    },

    async fetchData(customUrl = null) {
        this.isLoading = true;

        if (typeof this.search !== 'string') this.search = '';
        if (typeof this.selectedStatus !== 'string') this.selectedStatus = '';

        let url = customUrl;
        if (!url) {
            const params = new URLSearchParams();
            if (this.search) params.append('search', this.search);
            if (this.selectedStatus) params.append('status', this.selectedStatus);
            url = '{{ route('peminjaman.index') }}' + (params.toString() ? '?' + params.toString() : '');
        }
        window.history.pushState({}, '', url);

        try {
            const res = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!res.ok) throw new Error('Network response error');
            const htmlText = await res.text();

            const parser = new DOMParser();
            const doc = parser.parseFromString(htmlText, 'text/html');
            const newContainer = doc.getElementById('peminjamanContainer');

            if (newContainer) {
                const currentContainer = document.getElementById('peminjamanContainer');
                if (currentContainer) {
                    currentContainer.innerHTML = newContainer.innerHTML;
                    if (window.Alpine) {
                        window.Alpine.initTree(currentContainer);
                    }
                }
            } else {
                window.location.reload();
            }
        } catch (err) {
            console.error('Fetch filter error:', err);
        } finally {
            this.isLoading = false;
        }
    }
}" @open-delete-peminjaman.window="confirmDelete($event.detail.url, $event.detail.name)">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Peminjaman Barang</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Kelola peminjaman & verifikasi pengembalian alat lab</p>
        </div>
        <a href="{{ route('peminjaman.create') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-xs transition active:scale-95">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            <span>Catat Peminjaman</span>
        </a>
    </div>

    <!-- Mini Cards Stats (Clickable Status Filters) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div @click="selectStatus(selectedStatus === 'Menunggu Persetujuan' ? '' : 'Menunggu Persetujuan')"
             class="bg-white rounded-2xl shadow-xs border p-4 sm:p-5 flex items-center cursor-pointer transition active:scale-95 hover:border-amber-400"
             :class="selectedStatus === 'Menunggu Persetujuan' ? 'border-amber-500 ring-2 ring-amber-500/20 bg-amber-50/20' : 'border-amber-200/80'">
            <div class="p-3 rounded-xl bg-amber-50 text-amber-600 mr-4 shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-[10px] sm:text-xs font-bold text-amber-600 uppercase tracking-wider">Menunggu Persetujuan</p>
                <p class="text-xl sm:text-2xl font-black text-slate-900 mt-0.5">{{ \App\Models\Peminjaman::where('status', 'like', '%Menunggu%')->orWhereNull('status')->count() }}</p>
            </div>
        </div>
        <div @click="selectStatus(selectedStatus === 'Dipinjam' ? '' : 'Dipinjam')"
             class="bg-white rounded-2xl shadow-xs border p-4 sm:p-5 flex items-center cursor-pointer transition active:scale-95 hover:border-sky-400"
             :class="selectedStatus === 'Dipinjam' ? 'border-sky-500 ring-2 ring-sky-500/20 bg-sky-50/20' : 'border-slate-200/70'">
            <div class="p-3 rounded-xl bg-sky-50 text-sky-600 mr-4 shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
            <div>
                <p class="text-[10px] sm:text-xs font-semibold text-slate-500">Dipinjam</p>
                <p class="text-xl sm:text-2xl font-bold text-slate-900 mt-0.5">{{ \App\Models\Peminjaman::where('status', 'Dipinjam')->count() }}</p>
            </div>
        </div>
        <div @click="selectStatus(selectedStatus === 'Dikembalikan' ? '' : 'Dikembalikan')"
             class="bg-white rounded-2xl shadow-xs border p-4 sm:p-5 flex items-center cursor-pointer transition active:scale-95 hover:border-emerald-400"
             :class="selectedStatus === 'Dikembalikan' ? 'border-emerald-500 ring-2 ring-emerald-500/20 bg-emerald-50/20' : 'border-slate-200/70'">
            <div class="p-3 rounded-xl bg-emerald-50 text-emerald-600 mr-4 shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-[10px] sm:text-xs font-semibold text-slate-500">Dikembalikan</p>
                <p class="text-xl sm:text-2xl font-bold text-slate-900 mt-0.5">{{ \App\Models\Peminjaman::where('status', 'Dikembalikan')->count() }}</p>
            </div>
        </div>
        <div @click="selectStatus(selectedStatus === 'Terlambat' ? '' : 'Terlambat')"
             class="bg-white rounded-2xl shadow-xs border p-4 sm:p-5 flex items-center cursor-pointer transition active:scale-95 hover:border-rose-400"
             :class="selectedStatus === 'Terlambat' ? 'border-rose-500 ring-2 ring-rose-500/20 bg-rose-50/20' : 'border-slate-200/70'">
            <div class="p-3 rounded-xl bg-red-50 text-red-600 mr-4 shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-[10px] sm:text-xs font-semibold text-slate-500">Terlambat</p>
                <p class="text-xl sm:text-2xl font-bold text-slate-900 mt-0.5">{{ \App\Models\Peminjaman::where('status', 'Terlambat')->count() }}</p>
            </div>
        </div>
    </div>

    <!-- Filter & Content Card (Anti-Cropping / Tanpa overflow-hidden) -->
    <div class="bg-white rounded-2xl sm:rounded-3xl shadow-xs border border-slate-200/80 relative">
        {{-- Filter Bar --}}
        <div class="p-4 sm:p-5 border-b border-slate-200/80 bg-slate-50/50 rounded-t-2xl sm:rounded-t-3xl relative z-20">
            <form id="filterPeminjamanForm" @submit.prevent="fetchData()" class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <label for="peminjamanSearchInput" class="sr-only">Cari Peminjam</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" 
                               name="search" 
                               id="peminjamanSearchInput" 
                               x-model="search" 
                               @input.debounce.400ms="fetchData()" 
                               class="focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:outline-none block w-full pl-10 pr-3 py-2.5 text-xs sm:text-sm border border-slate-300 rounded-xl bg-white transition" 
                               placeholder="Cari nama peminjam...">
                    </div>
                </div>

                {{-- Custom Animated Dropdown: Status --}}
                <div class="sm:w-56 relative" @click.outside="statusDropdownOpen = false">
                    <input type="hidden" name="status" :value="selectedStatus">
                    
                    <button type="button" @click="statusDropdownOpen = !statusDropdownOpen"
                            class="bg-white border border-slate-300 text-xs sm:text-sm rounded-xl px-3.5 py-2.5 flex items-center justify-between w-full text-left focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:outline-none transition shadow-xs">
                        <span x-text="{
                            '': 'Semua Status',
                            'Menunggu Persetujuan': 'Menunggu Persetujuan',
                            'Dipinjam': 'Dipinjam',
                            'Dikembalikan': 'Dikembalikan',
                            'Terlambat': 'Terlambat'
                        }[selectedStatus] || 'Semua Status'" :class="{'font-bold text-indigo-700': selectedStatus, 'text-slate-700': !selectedStatus}"></span>
                        <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 shrink-0" :class="{'rotate-180 text-indigo-600': statusDropdownOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="statusDropdownOpen"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                         class="absolute left-0 right-0 z-50 mt-1.5 bg-white border border-slate-200 rounded-2xl shadow-xl py-1 overflow-hidden"
                         style="display: none;">
                        <button type="button" @click="selectStatus('')"
                                class="w-full text-left px-4 py-2.5 text-xs sm:text-sm text-slate-700 hover:bg-slate-50 transition flex items-center justify-between"
                                :class="{'font-bold text-indigo-700 bg-indigo-50/50': selectedStatus === ''}">
                            <span>Semua Status</span>
                            <svg x-show="selectedStatus === ''" class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </button>
                        @foreach(['Menunggu Persetujuan', 'Dipinjam', 'Dikembalikan', 'Terlambat'] as $st)
                        <button type="button" @click="selectStatus('{{ $st }}')"
                                class="w-full text-left px-4 py-2.5 text-xs sm:text-sm text-slate-700 hover:bg-slate-50 transition flex items-center justify-between"
                                :class="{'font-bold text-indigo-700 bg-indigo-50/50': selectedStatus === '{{ $st }}'}">
                            <span>{{ $st }}</span>
                            <svg x-show="selectedStatus === '{{ $st }}'" class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </button>
                        @endforeach
                    </div>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="flex-1 sm:flex-none inline-flex justify-center items-center px-4 py-2.5 border border-slate-300 shadow-xs text-xs sm:text-sm font-bold rounded-xl text-slate-700 bg-white hover:bg-slate-50 transition active:scale-95">
                        Filter
                    </button>
                    <div x-show="search || selectedStatus">
                        <button type="button" @click="resetFilters()" class="inline-flex justify-center items-center px-4 py-2.5 text-xs sm:text-sm font-semibold text-slate-500 hover:text-slate-700 bg-slate-100 rounded-xl transition">
                            Reset
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Data Container --}}
        <div id="peminjamanContainer" class="relative z-10 transition-opacity duration-150" :class="{'opacity-50 pointer-events-none': isLoading}" @click="const a = $event.target.closest('a'); if (a && a.href && (a.closest('nav') || a.closest('.pagination'))) { $event.preventDefault(); fetchData(a.href); }">
            
            {{-- Loading Skeleton State --}}
            <template x-if="isLoading">
                <div class="p-4 sm:p-6 space-y-4">
                    <div class="animate-pulse flex justify-between items-center gap-4">
                        <div class="h-4 bg-slate-200 rounded w-1/3"></div>
                        <div class="h-4 bg-slate-200 rounded w-1/4"></div>
                    </div>
                    <div class="space-y-3 pt-2">
                        <div class="h-12 bg-slate-100 rounded-xl animate-pulse"></div>
                        <div class="h-12 bg-slate-100 rounded-xl animate-pulse"></div>
                        <div class="h-12 bg-slate-100 rounded-xl animate-pulse"></div>
                        <div class="h-12 bg-slate-100 rounded-xl animate-pulse"></div>
                    </div>
                </div>
            </template>

            {{-- Table & Mobile List --}}
            <div x-show="!isLoading">
                {{-- 1. TAMPILAN MOBILE --}}
        <div class="block md:hidden divide-y divide-slate-100 bg-slate-50/30">
            @forelse($peminjamans as $peminjaman)
                <div class="p-4 sm:p-5 space-y-3.5 bg-white transition hover:bg-slate-50/70">
                    <div class="flex items-center justify-between gap-2.5">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-700 font-extrabold flex items-center justify-center text-xs shrink-0 shadow-xs">
                                {{ strtoupper(substr($peminjaman->nama_peminjam, 0, 2)) }}
                            </div>
                            <div class="min-w-0">
                                <h3 class="font-extrabold text-slate-900 text-sm truncate leading-tight">{{ $peminjaman->nama_peminjam }}</h3>
                                <p class="text-[11px] text-slate-400 font-medium truncate mt-0.5">{{ $peminjaman->kelas_atau_jabatan ?? 'Siswa / Anggota Lab' }}</p>
                            </div>
                        </div>

                        <div class="shrink-0">
                            @if(empty($peminjaman->status) || str_contains($peminjaman->status, 'Menunggu') || $peminjaman->status === 'Menunggu Persetujuan')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200 shadow-xs animate-pulse">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                    <span>Menunggu</span>
                                </span>
                            @elseif($peminjaman->status === 'Dipinjam')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-sky-50 text-sky-700 border border-sky-200 shadow-xs">
                                    <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>
                                    <span>Dipinjam</span>
                                </span>
                            @elseif($peminjaman->status === 'Dikembalikan')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-xs">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    <span>Dikembalikan</span>
                                </span>
                            @elseif($peminjaman->status === 'Terlambat')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200 shadow-xs animate-pulse">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                    <span>Terlambat</span>
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $peminjaman->status }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="bg-slate-50/90 rounded-2xl p-3.5 border border-slate-200/80 space-y-2">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0 flex-1">
                                <p class="font-extrabold text-slate-800 text-xs truncate leading-snug">{{ $peminjaman->barang->nama_barang }}</p>
                                <span class="inline-block font-mono text-[10px] text-slate-400 font-semibold mt-0.5">{{ $peminjaman->barang->kode_barang }}</span>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-xl bg-white border border-slate-200 text-xs font-black text-slate-800 shadow-xs shrink-0">
                                {{ $peminjaman->jumlah_pinjam }} {{ $peminjaman->barang->satuan }}
                            </span>
                        </div>

                        @if($peminjaman->unit_index)
                            <div class="pt-2 border-t border-slate-200/60 flex items-center justify-between text-[11px]">
                                <span class="text-slate-400 font-medium">Unit Dipinjam:</span>
                                <span class="font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-lg border border-indigo-100/80 text-[10px]">
                                    Unit {{ implode(', Unit ', array_map('trim', explode(',', $peminjaman->unit_index))) }}
                                </span>
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center justify-between gap-3 pt-1">
                        <div class="text-[11px] space-y-0.5">
                            <div class="flex items-center gap-1.5 text-slate-500">
                                <span class="font-bold text-slate-700 uppercase text-[10px] tracking-wider w-3">P:</span>
                                <span class="font-medium text-slate-600">{{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->translatedFormat('d M Y') }}</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="font-bold uppercase text-[10px] tracking-wider w-3 {{ $peminjaman->isTerlambat() ? 'text-red-500' : 'text-slate-700' }}">K:</span>
                                <span class="font-semibold {{ $peminjaman->isTerlambat() ? 'text-red-600' : 'text-slate-800' }}">
                                    {{ \Carbon\Carbon::parse($peminjaman->tanggal_kembali_rencana)->translatedFormat('d M Y') }}
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            <a href="{{ route('peminjaman.show', $peminjaman->id) }}" 
                               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 shadow-xs transition active:scale-95">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <span>Detail</span>
                            </a>
                            
                            <button type="button" 
                                    @click.stop="$dispatch('open-delete-peminjaman', { url: '{{ route('peminjaman.destroy', $peminjaman->id) }}', name: 'Peminjaman oleh {{ addslashes($peminjaman->nama_peminjam) }} ({{ addslashes($peminjaman->barang->nama_barang) }})' })" 
                                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 shadow-xs transition active:scale-95">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                <span>Hapus</span>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center bg-white">
                    <svg class="mx-auto h-10 w-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    <p class="mt-2 text-xs font-semibold text-slate-700">Belum ada data peminjaman</p>
                </div>
            @endforelse
        </div>

        {{-- 2. TAMPILAN DESKTOP --}}
        <div class="hidden md:block overflow-x-auto rounded-b-2xl sm:rounded-b-3xl">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50/90 text-slate-600 border-b border-slate-200">
                    <tr>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">No</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Peminjam</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Barang</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Jumlah</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Tanggal</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3.5 text-right text-xs font-bold text-slate-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-100 text-xs">
                    @forelse($peminjamans as $index => $peminjaman)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-slate-500 font-medium">
                            {{ $peminjamans->firstItem() + $index }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-bold text-slate-900">{{ $peminjaman->nama_peminjam }}</div>
                            <div class="text-[11px] text-slate-500">{{ $peminjaman->kelas_atau_jabatan }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-bold text-slate-900">{{ $peminjaman->barang->nama_barang }}</div>
                            <div class="text-[11px] font-mono text-slate-500">{{ $peminjaman->barang->kode_barang }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-bold text-slate-900">{{ $peminjaman->jumlah_pinjam }} {{ $peminjaman->barang->satuan }}</div>
                            @if($peminjaman->unit_index)
                                <div class="text-[11px] font-bold text-indigo-600">Unit {{ implode(', ', array_map('trim', explode(',', $peminjaman->unit_index))) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-slate-900 font-medium">P: {{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d M Y') }}</div>
                            <div class="text-slate-500">K: {{ \Carbon\Carbon::parse($peminjaman->tanggal_kembali_rencana)->format('d M Y') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-black text-slate-900 text-xs sm:text-sm">
                                {{ empty($peminjaman->status) || str_contains($peminjaman->status, 'Menunggu') ? 'Menunggu Persetujuan' : $peminjaman->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right font-medium">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('peminjaman.show', $peminjaman->id) }}" 
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 shadow-xs transition active:scale-95" 
                                   title="Detail">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    <span>Detail</span>
                                </a>
                                <button type="button" 
                                        @click.stop="$dispatch('open-delete-peminjaman', { url: '{{ route('peminjaman.destroy', $peminjaman->id) }}', name: 'Peminjaman oleh {{ addslashes($peminjaman->nama_peminjam) }} ({{ addslashes($peminjaman->barang->nama_barang) }})' })" 
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 shadow-xs transition active:scale-95" 
                                        title="Hapus">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    <span>Hapus</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center">
                            <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                            <h3 class="mt-2 text-sm font-semibold text-slate-900">Belum ada data peminjaman</h3>
                            <p class="mt-1 text-xs text-slate-500">Mulai dengan mencatat peminjaman barang baru.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-4 sm:px-6 py-3.5 border-t border-slate-200/80 bg-slate-50/50 rounded-b-2xl sm:rounded-b-3xl">
            {{ $peminjamans->withQueryString()->links() }}
        </div>
            </div>
        </div>
    </div>

    {{-- MODAL KONFIRMASI HAPUS --}}
    <div x-show="deleteModal" class="fixed inset-0 z-[999999] flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs" style="display: none;"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div @click.away="deleteModal = false" class="bg-white rounded-3xl p-6 sm:p-7 w-full max-w-sm sm:max-w-md shadow-2xl border border-slate-100 text-center"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">
            
            <div class="w-12 h-12 bg-rose-100 text-rose-600 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-rose-200/60">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>

            <h3 class="text-base sm:text-lg font-extrabold text-slate-900">Hapus Data Peminjaman Ini?</h3>
            <p class="text-xs sm:text-sm text-slate-500 mt-1 leading-relaxed">
                Anda akan menghapus data <strong class="text-slate-800" x-text="deleteItemName"></strong>. Tindakan ini tidak dapat dibatalkan.
            </p>

            <form :action="deleteActionUrl" method="POST" @submit="isSubmitting = true" class="mt-6 flex items-center justify-center gap-3" x-data="{ isSubmitting: false }">
                @csrf
                @method('DELETE')
                
                <button type="button" :disabled="isSubmitting" @click="deleteModal = false" class="flex-1 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-xs sm:text-sm transition disabled:opacity-50">
                    Batal
                </button>
                <button type="submit" :disabled="isSubmitting" class="flex-1 px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl text-xs sm:text-sm shadow-xs transition active:scale-95 disabled:opacity-50 flex items-center justify-center gap-2">
                    <span x-show="!isSubmitting">Ya, Hapus</span>
                    <span x-show="isSubmitting" class="flex items-center gap-1.5" style="display: none;">
                        <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Menghapus...
                    </span>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection