@extends('layouts.app')

@section('title', 'Laporan Maintenance')
@section('page_title', 'Laporan Maintenance')
@section('page_subtitle', 'Rekap data perbaikan & perawatan alat laboratorium')

@section('content')
@php
    $initialItems = $maintenances->map(function($m, $i) {
        return [
            'no' => $i + 1,
            'id' => $m->id,
            'tanggal' => $m->tanggal_maintenance ? \Carbon\Carbon::parse($m->tanggal_maintenance)->format('d/m/Y') : '—',
            'laboratorium' => $m->laboratorium ? str_replace('Laboratorium ', 'Lab ', $m->laboratorium) : null,
            'nama_barang' => $m->barang->nama_barang ?? 'Umum / Fasilitas',
            'kode_barang' => $m->barang->kode_barang ?? null,
            'teknisi' => $m->teknisi ?? '—',
            'jenis' => $m->jenis ?? 'Preventif',
            'deskripsi_kerusakan' => $m->deskripsi_kerusakan ?? '-',
            'tindakan' => $m->tindakan ?? null,
            'biaya' => $m->biaya ? 'Rp ' . number_format($m->biaya, 0, ',', '.') : '—',
            'status' => $m->status,
            'detail_url' => route('maintenance.show', $m->id),
        ];
    });
@endphp

<script>
function laporanMaintenanceLive() {
    return {
        filters: {
            search: '{{ request("search", "") }}',
            laboratorium: '{{ request("laboratorium", $labScope ?? "") }}',
            status: '{{ request("status", "") }}',
            jenis: '{{ request("jenis", "") }}',
            tanggal_dari: '{{ request("tanggal_dari", "") }}',
            tanggal_sampai: '{{ request("tanggal_sampai", "") }}'
        },
        items: @json($initialItems),
        totalCount: {{ $maintenances->count() }},
        totalBiaya: 'Rp {{ number_format($totalBiaya ?? 0, 0, ",", ".") }}',
        rekapStatus: @json($rekapStatus ?? []),
        isFetching: false,
        timer: null,

        // Dropdown state tracking
        openDropdown: null,

        toggleDropdown(name) {
            this.openDropdown = (this.openDropdown === name) ? null : name;
        },

        closeDropdown(name) {
            if (this.openDropdown === name) {
                this.openDropdown = null;
            }
        },

        selectFilter(key, value) {
            this.filters[key] = value;
            this.openDropdown = null;
            this.fetchLive(true);
        },

        init() {
            if (window._laporanMaintenanceInterval) {
                clearInterval(window._laporanMaintenanceInterval);
            }

            this.timer = setInterval(() => {
                if (window.location.pathname.includes('/laporan/maintenance')) {
                    this.fetchLive(false);
                } else {
                    clearInterval(this.timer);
                    window._laporanMaintenanceInterval = null;
                }
            }, 5000);

            window._laporanMaintenanceInterval = this.timer;
        },

        destroy() {
            if (this.timer) clearInterval(this.timer);
            if (window._laporanMaintenanceInterval) clearInterval(window._laporanMaintenanceInterval);
        },

        fetchLive(force) {
            if (!window.location.pathname.includes('/laporan/maintenance')) return;
            if (this.isFetching && !force) return;
            this.isFetching = true;

            const params = new URLSearchParams();
            if (this.filters.search) params.set('search', this.filters.search);
            if (this.filters.laboratorium) params.set('laboratorium', this.filters.laboratorium);
            if (this.filters.status) params.set('status', this.filters.status);
            if (this.filters.jenis) params.set('jenis', this.filters.jenis);
            if (this.filters.tanggal_dari) params.set('tanggal_dari', this.filters.tanggal_dari);
            if (this.filters.tanggal_sampai) params.set('tanggal_sampai', this.filters.tanggal_sampai);

            fetch(`{{ route('laporan.maintenance.json') }}?${params.toString()}`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => {
                if (!res.ok) throw new Error();
                return res.json();
            })
            .then(data => {
                if (data && data.maintenances && window.location.pathname.includes('/laporan/maintenance')) {
                    this.items = data.maintenances;
                    this.totalCount = data.totalCount;
                    this.totalBiaya = data.totalBiaya;
                    this.rekapStatus = data.rekapStatus;
                }
            })
            .catch(() => {})
            .finally(() => {
                this.isFetching = false;
            });
        },

        resetFilters() {
            this.filters = { 
                search: '', 
                laboratorium: '{{ $labScope ?? "" }}', 
                status: '', 
                jenis: '', 
                tanggal_dari: '', 
                tanggal_sampai: '' 
            };
            this.openDropdown = null;
            this.fetchLive(true);
        },

        cetakPdf() {
            const params = new URLSearchParams();
            if (this.filters.laboratorium) params.set('laboratorium', this.filters.laboratorium);
            if (this.filters.status) params.set('status', this.filters.status);
            if (this.filters.jenis) params.set('jenis', this.filters.jenis);
            if (this.filters.tanggal_dari) params.set('tanggal_dari', this.filters.tanggal_dari);
            if (this.filters.tanggal_sampai) params.set('tanggal_sampai', this.filters.tanggal_sampai);
            if (this.filters.search) params.set('search', this.filters.search);

            const pdfUrl = '{{ route("laporan.maintenance.pdf") }}' + (params.toString() ? '?' + params.toString() : '');
            window.open(pdfUrl, '_blank');
        }
    };
}
</script>

<div class="space-y-4 sm:space-y-6" x-data="laporanMaintenanceLive()" x-cloak>

    {{-- ===== STATS CARDS ===== --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200/60 p-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-indigo-50 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <p class="text-[11px] text-slate-400 font-medium">Total Maintenance</p>
                    <p class="text-xl font-extrabold text-slate-900" x-text="totalCount">{{ $maintenances->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200/60 p-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-amber-50 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-[11px] text-slate-400 font-medium">Total Biaya</p>
                    <p class="text-sm font-extrabold text-slate-900" x-text="totalBiaya">Rp {{ number_format($totalBiaya ?? 0, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200/60 p-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-[11px] text-slate-400 font-medium">Selesai</p>
                    <p class="text-xl font-extrabold text-emerald-600" x-text="rekapStatus.Selesai || 0">{{ $rekapStatus['Selesai'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200/60 p-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-orange-50 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-[11px] text-slate-400 font-medium">Proses / Pending</p>
                    <p class="text-xl font-extrabold text-orange-600" x-text="(rekapStatus.Proses || 0) + (rekapStatus.Pending || 0)">{{ ($rekapStatus['Proses'] ?? 0) + ($rekapStatus['Pending'] ?? 0) }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== FILTER BAR (Non-clipping Container) ===== --}}
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/60 p-4 sm:p-5 relative z-30">
        <form @submit.prevent="fetchLive(true)" class="space-y-4">
            <div class="flex flex-col sm:flex-row gap-4 flex-wrap">
                
                {{-- Search Input --}}
                <div class="flex-1 min-w-[220px]">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" x-model="filters.search" @input.debounce.400ms="fetchLive(true)"
                               class="bg-slate-50/70 border border-slate-300 text-slate-800 text-xs sm:text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:outline-none block w-full pl-9 pr-3 py-2.5 transition"
                               placeholder="Cari barang, teknisi, atau kerusakan...">
                    </div>
                </div>

                {{-- Dropdown Laboratorium --}}
                @if(!empty($labScope))
                    <div class="sm:w-48 bg-emerald-50/80 border border-emerald-300/80 text-emerald-800 text-xs sm:text-sm rounded-xl px-3.5 py-2.5 flex items-center justify-between">
                        <span class="font-bold truncate">{{ $labScope }}</span>
                        <span class="text-[10px] bg-emerald-200 text-emerald-900 font-extrabold px-2 py-0.5 rounded-full ml-1 shrink-0">Koor</span>
                    </div>
                @else
                    <div class="sm:w-48 relative" @click.outside="closeDropdown('lab')">
                        <button type="button" @click="toggleDropdown('lab')"
                                class="bg-slate-50/70 border border-slate-300 text-slate-800 text-xs sm:text-sm rounded-xl px-3.5 py-2.5 flex items-center justify-between w-full text-left focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:outline-none transition shadow-xs">
                            <span x-text="filters.laboratorium || 'Semua Lab'" :class="{'text-slate-700': !filters.laboratorium, 'font-bold text-indigo-700': filters.laboratorium}" class="truncate pr-2"></span>
                            <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 shrink-0" :class="{'rotate-180 text-indigo-600': openDropdown === 'lab'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="openDropdown === 'lab'"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                             x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                             class="absolute left-0 z-50 mt-1.5 w-full min-w-[190px] bg-white border border-slate-200 rounded-2xl shadow-xl py-1 overflow-hidden"
                             style="display: none;">
                            <button type="button" @click="selectFilter('laboratorium', '')"
                                    class="w-full text-left px-4 py-2.5 text-xs sm:text-sm text-slate-700 hover:bg-slate-50 transition flex items-center justify-between"
                                    :class="{'font-bold text-indigo-700 bg-indigo-50/50': filters.laboratorium === ''}">
                                <span>Semua Laboratorium</span>
                                <svg x-show="filters.laboratorium === ''" class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </button>
                            @foreach(['Laboratorium TKJ' => 'Lab TKJ', 'Laboratorium AKL' => 'Lab AKL', 'Laboratorium Pemasaran' => 'Lab Pemasaran'] as $val => $label)
                                <button type="button" @click="selectFilter('laboratorium', '{{ $val }}')"
                                        class="w-full text-left px-4 py-2.5 text-xs sm:text-sm text-slate-700 hover:bg-slate-50 transition flex items-center justify-between"
                                        :class="{'font-bold text-indigo-700 bg-indigo-50/50': filters.laboratorium === '{{ $val }}'}">
                                    <span>{{ $label }}</span>
                                    <svg x-show="filters.laboratorium === '{{ $val }}'" class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Dropdown Status --}}
                <div class="sm:w-40 relative" @click.outside="closeDropdown('status')">
                    <button type="button" @click="toggleDropdown('status')"
                            class="bg-slate-50/70 border border-slate-300 text-slate-800 text-xs sm:text-sm rounded-xl px-3.5 py-2.5 flex items-center justify-between w-full text-left focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:outline-none transition shadow-xs">
                        <span x-text="filters.status || 'Semua Status'" :class="{'text-slate-700': !filters.status, 'font-bold text-indigo-700': filters.status}" class="truncate pr-2"></span>
                        <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 shrink-0" :class="{'rotate-180 text-indigo-600': openDropdown === 'status'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="openDropdown === 'status'"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                         class="absolute left-0 z-50 mt-1.5 w-full min-w-[160px] bg-white border border-slate-200 rounded-2xl shadow-xl py-1 overflow-hidden"
                         style="display: none;">
                        <button type="button" @click="selectFilter('status', '')"
                                class="w-full text-left px-4 py-2.5 text-xs sm:text-sm text-slate-700 hover:bg-slate-50 transition flex items-center justify-between"
                                :class="{'font-bold text-indigo-700 bg-indigo-50/50': filters.status === ''}">
                            <span>Semua Status</span>
                            <svg x-show="filters.status === ''" class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </button>
                        @foreach(['Selesai', 'Proses', 'Pending'] as $s)
                            <button type="button" @click="selectFilter('status', '{{ $s }}')"
                                    class="w-full text-left px-4 py-2.5 text-xs sm:text-sm text-slate-700 hover:bg-slate-50 transition flex items-center justify-between"
                                    :class="{'font-bold text-indigo-700 bg-indigo-50/50': filters.status === '{{ $s }}'}">
                                <span>{{ $s }}</span>
                                <svg x-show="filters.status === '{{ $s }}'" class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Dropdown Jenis --}}
                <div class="sm:w-40 relative" @click.outside="closeDropdown('jenis')">
                    <button type="button" @click="toggleDropdown('jenis')"
                            class="bg-slate-50/70 border border-slate-300 text-slate-800 text-xs sm:text-sm rounded-xl px-3.5 py-2.5 flex items-center justify-between w-full text-left focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:outline-none transition shadow-xs">
                        <span x-text="filters.jenis || 'Semua Jenis'" :class="{'text-slate-700': !filters.jenis, 'font-bold text-indigo-700': filters.jenis}" class="truncate pr-2"></span>
                        <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 shrink-0" :class="{'rotate-180 text-indigo-600': openDropdown === 'jenis'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="openDropdown === 'jenis'"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                         class="absolute left-0 sm:right-0 sm:left-auto z-50 mt-1.5 w-full min-w-[160px] bg-white border border-slate-200 rounded-2xl shadow-xl py-1 overflow-hidden"
                         style="display: none;">
                        <button type="button" @click="selectFilter('jenis', '')"
                                class="w-full text-left px-4 py-2.5 text-xs sm:text-sm text-slate-700 hover:bg-slate-50 transition flex items-center justify-between"
                                :class="{'font-bold text-indigo-700 bg-indigo-50/50': filters.jenis === ''}">
                            <span>Semua Jenis</span>
                            <svg x-show="filters.jenis === ''" class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </button>
                        @foreach(['Preventif', 'Korektif', 'Penggantian'] as $j)
                            <button type="button" @click="selectFilter('jenis', '{{ $j }}')"
                                    class="w-full text-left px-4 py-2.5 text-xs sm:text-sm text-slate-700 hover:bg-slate-50 transition flex items-center justify-between"
                                    :class="{'font-bold text-indigo-700 bg-indigo-50/50': filters.jenis === '{{ $j }}'}">
                                <span>{{ $j }}</span>
                                <svg x-show="filters.jenis === '{{ $j }}'" class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Rentang Tanggal & Aksi Ekspor --}}
            <div class="flex flex-col sm:flex-row gap-4 items-end">
                <div class="flex items-center gap-2 flex-wrap">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Dari Tanggal</label>
                        <input type="date" x-model="filters.tanggal_dari" @change="fetchLive(true)"
                               class="bg-slate-50/70 border border-slate-300 text-slate-800 text-xs sm:text-sm rounded-xl px-3.5 py-2 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:outline-none transition">
                    </div>
                    <span class="text-slate-400 text-xs mt-4">s/d</span>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Sampai Tanggal</label>
                        <input type="date" x-model="filters.tanggal_sampai" @change="fetchLive(true)"
                               class="bg-slate-50/70 border border-slate-300 text-slate-800 text-xs sm:text-sm rounded-xl px-3.5 py-2 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:outline-none transition">
                    </div>
                </div>

                <div class="flex items-center gap-2 ml-auto">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Live Sync</span>
                    </span>

                    <button type="button" @click="resetFilters()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs sm:text-sm font-bold rounded-xl transition shadow-xs">
                        Reset
                    </button>

                    <button type="button" @click="cetakPdf()"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs sm:text-sm font-bold rounded-xl transition shadow-xs active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak PDF</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- ===== TABEL MAINTENANCE ===== --}}
    <div class="bg-white rounded-2xl sm:rounded-3xl shadow-xs border border-slate-200/60 overflow-hidden relative z-10">
        <div class="px-6 py-4 border-b border-slate-800 bg-slate-900 text-white flex items-center justify-between">
            <div>
                <h2 class="text-sm font-bold text-white">Data Laporan Maintenance</h2>
                <p class="text-xs text-slate-300 mt-0.5"><span x-text="totalCount">{{ $maintenances->count() }}</span> record ditemukan</p>
            </div>
        </div>

        <div x-show="!items || items.length === 0" class="flex flex-col items-center justify-center py-20 px-4 text-center">
            <h3 class="text-sm font-bold text-slate-700 mb-1">Tidak ada data maintenance</h3>
            <p class="text-xs text-slate-400">Coba ubah filter pencarian atau tambah data maintenance baru.</p>
        </div>

        <div x-show="items && items.length > 0" class="overflow-x-auto">
            <table class="w-full text-xs sm:text-sm divide-y divide-slate-100">
                <thead class="bg-slate-50/90 text-slate-600 border-b border-slate-200">
                    <tr class="text-slate-600 text-[11px] font-bold uppercase tracking-wider">
                        <th class="px-4 py-3.5 text-left w-12 font-bold">No</th>
                        <th class="px-4 py-3.5 text-left font-bold">Tanggal</th>
                        <th class="px-4 py-3.5 text-left font-bold">Laboratorium</th>
                        <th class="px-4 py-3.5 text-left font-bold">Barang</th>
                        <th class="px-4 py-3.5 text-left font-bold">Teknisi</th>
                        <th class="px-4 py-3.5 text-center font-bold">Jenis</th>
                        <th class="px-4 py-3.5 text-left font-bold">Kerusakan / Tindakan</th>
                        <th class="px-4 py-3.5 text-right font-bold">Biaya</th>
                        <th class="px-4 py-3.5 text-center font-bold">Status</th>
                        <th class="px-4 py-3.5 text-center font-bold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <template x-for="(m, i) in items" :key="m.id || i">
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-4 py-3.5 text-slate-400" x-text="i + 1"></td>
                            <td class="px-4 py-3.5 text-slate-600 whitespace-nowrap" x-text="m.tanggal"></td>
                            <td class="px-4 py-3.5">
                                <template x-if="m.laboratorium">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700 text-[10px] font-semibold" x-text="m.laboratorium"></span>
                                </template>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="font-semibold text-slate-800 text-xs leading-tight" x-text="m.nama_barang"></div>
                            </td>
                            <td class="px-4 py-3.5 text-slate-600 text-xs whitespace-nowrap" x-text="m.teknisi"></td>
                            <td class="px-4 py-3.5 text-center" x-text="m.jenis"></td>
                            <td class="px-4 py-3.5 max-w-[180px]" x-text="m.deskripsi_kerusakan"></td>
                            <td class="px-4 py-3.5 text-right font-medium text-slate-700 whitespace-nowrap text-xs" x-text="m.biaya"></td>
                            <td class="px-4 py-3.5 text-center" x-text="m.status"></td>
                            <td class="px-4 py-3.5 text-center">
                                <a :href="m.detail_url" class="px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 text-xs font-semibold transition active:scale-95">Detail</a>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection