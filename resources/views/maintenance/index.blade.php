@extends('layouts.app')

@section('title', 'Riwayat Maintenance')
@section('page_title', 'Riwayat Maintenance')
@section('page_subtitle', 'Kelola perbaikan & perawatan barang laboratorium')

@section('content')
<div class="space-y-4 sm:space-y-6" x-data="maintenanceIndex()" x-init="init()">

    {{-- ===== PAGE HEADER (Seamless, No Floating Card) ===== --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Riwayat Maintenance</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Kelola perbaikan dan perawatan alat laboratorium</p>
        </div>
        <a href="{{ route('maintenance.create') }}" 
           class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-xs transition active:scale-95 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 shrink-0">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Catat Maintenance</span>
        </a>
    </div>

    {{-- ===== SEARCH, FILTER & DATA CONTAINER ===== --}}
    {{-- overflow-hidden dilepas dari container utama agar dropdown tidak terpotong --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs relative">

        {{-- Filter & Search Header --}}
        <div class="p-4 border-b border-slate-100 bg-slate-50/60 rounded-t-2xl relative z-20">
            <form id="filterMaintenanceForm" @submit.prevent="fetchData()" class="space-y-3">

                {{-- Search Input & Mobile Filter Trigger --}}
                <div class="flex flex-col xs:flex-row items-stretch xs:items-center gap-2">
                    <div class="relative flex-1 min-w-0">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text" 
                               name="search" 
                               id="search" 
                               x-model="search" 
                               @input.debounce.400ms="fetchData()"
                               class="bg-white border border-slate-300 text-slate-900 text-xs sm:text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 block w-full pl-9 pr-3 py-2.5 transition focus:outline-none placeholder:text-slate-400"
                               placeholder="Cari alat atau teknisi..."
                               aria-label="Cari maintenance">
                    </div>

                    {{-- Toggle Filter Mobile --}}
                    <button type="button" @click="mobileFilterOpen = !mobileFilterOpen"
                            class="sm:hidden flex items-center justify-center gap-2 px-3.5 py-2.5 bg-white border border-slate-300 hover:bg-slate-50 rounded-xl text-xs font-bold text-slate-700 transition shrink-0"
                            :class="{'border-indigo-500 text-indigo-700 bg-indigo-50/40': activeFilterCount > 0}"
                            aria-expanded="false"
                            :aria-expanded="mobileFilterOpen">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        <span>Filter</span>
                        <span x-show="activeFilterCount > 0" 
                              x-text="activeFilterCount"
                              class="inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 text-[10px] font-bold bg-indigo-600 text-white rounded-full"></span>
                    </button>
                </div>

                {{-- Dropdowns Filter (Collapsible on Mobile, Horizontal on Tablet/Desktop) --}}
                <div :class="mobileFilterOpen ? 'flex flex-col' : 'hidden sm:flex'" 
                     class="sm:flex-row gap-3 pt-1">

                    {{-- Laboratorium (Locked or Dropdown) --}}
                    @if(!empty($lockedLab))
                        <div class="sm:w-52 bg-white border border-slate-300 text-slate-700 text-xs sm:text-sm rounded-xl px-3 py-2.5 flex items-center justify-between">
                            <span class="font-medium text-slate-800 truncate">{{ $lockedLab }}</span>
                            <span class="text-xs bg-emerald-50 text-emerald-800 border border-emerald-200 font-bold px-2 py-0.5 rounded-md ml-2 shrink-0">Koor</span>
                        </div>
                    @else
                        <div class="sm:w-48 relative" @click.outside="openLab = false">
                            <button type="button" @click="toggleDropdown('lab')"
                                    class="bg-white border border-slate-300 text-xs sm:text-sm rounded-xl px-3 py-2.5 flex items-center justify-between w-full text-left focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:outline-none transition"
                                    aria-haspopup="listbox"
                                    :aria-expanded="openLab">
                                <span x-text="laboratorium || 'Semua Lab'" :class="{'text-slate-700 font-medium': !laboratorium, 'font-bold text-indigo-700': laboratorium}" class="truncate pr-2"></span>
                                <svg class="w-4 h-4 text-slate-400 transition-transform duration-150 shrink-0" :class="{'rotate-180 text-indigo-600': openLab}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div x-show="openLab"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 translate-y-1"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 translate-y-1"
                                 class="absolute left-0 z-50 mt-1 w-full min-w-[200px] bg-white border border-slate-200 rounded-xl shadow-xl py-1 max-h-60 overflow-y-auto"
                                 style="display: none;"
                                 role="listbox">
                                <button type="button" @click="selectFilter('laboratorium', '')"
                                        class="w-full text-left px-3.5 py-2 text-xs sm:text-sm text-slate-700 hover:bg-slate-50 transition flex items-center justify-between"
                                        :class="{'font-bold text-indigo-700 bg-indigo-50/50': laboratorium === ''}"
                                        role="option"
                                        :aria-selected="laboratorium === ''">
                                    <span>Semua Lab</span>
                                    <svg x-show="laboratorium === ''" class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </button>
                                @foreach(['Laboratorium TKJ', 'Laboratorium AKL', 'Laboratorium Pemasaran'] as $lab)
                                    <button type="button" @click="selectFilter('laboratorium', '{{ $lab }}')"
                                            class="w-full text-left px-3.5 py-2 text-xs sm:text-sm text-slate-700 hover:bg-slate-50 transition flex items-center justify-between"
                                            :class="{'font-bold text-indigo-700 bg-indigo-50/50': laboratorium === '{{ $lab }}'}"
                                            role="option"
                                            :aria-selected="laboratorium === '{{ $lab }}'">
                                        <span>{{ $lab }}</span>
                                        <svg x-show="laboratorium === '{{ $lab }}'" class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Jenis --}}
                    <div class="sm:w-40 relative" @click.outside="openJenis = false">
                        <button type="button" @click="toggleDropdown('jenis')"
                                class="bg-white border border-slate-300 text-xs sm:text-sm rounded-xl px-3 py-2.5 flex items-center justify-between w-full text-left focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:outline-none transition"
                                aria-haspopup="listbox"
                                :aria-expanded="openJenis">
                            <span x-text="jenis || 'Semua Jenis'" :class="{'text-slate-700 font-medium': !jenis, 'font-bold text-indigo-700': jenis}" class="truncate pr-2"></span>
                            <svg class="w-4 h-4 text-slate-400 transition-transform duration-150 shrink-0" :class="{'rotate-180 text-indigo-600': openJenis}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="openJenis"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 translate-y-1"
                             class="absolute left-0 z-50 mt-1 w-full min-w-[160px] bg-white border border-slate-200 rounded-xl shadow-xl py-1 max-h-60 overflow-y-auto"
                             style="display: none;"
                             role="listbox">
                            <button type="button" @click="selectFilter('jenis', '')"
                                    class="w-full text-left px-3.5 py-2 text-xs sm:text-sm text-slate-700 hover:bg-slate-50 transition flex items-center justify-between"
                                    :class="{'font-bold text-indigo-700 bg-indigo-50/50': jenis === ''}"
                                    role="option"
                                    :aria-selected="jenis === ''">
                                <span>Semua Jenis</span>
                                <svg x-show="jenis === ''" class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </button>
                            @foreach(['Preventif', 'Korektif', 'Penggantian'] as $j)
                                <button type="button" @click="selectFilter('jenis', '{{ $j }}')"
                                        class="w-full text-left px-3.5 py-2 text-xs sm:text-sm text-slate-700 hover:bg-slate-50 transition flex items-center justify-between"
                                        :class="{'font-bold text-indigo-700 bg-indigo-50/50': jenis === '{{ $j }}'}"
                                        role="option"
                                        :aria-selected="jenis === '{{ $j }}'">
                                    <span>{{ $j }}</span>
                                    <svg x-show="jenis === '{{ $j }}'" class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="sm:w-40 relative" @click.outside="openStatus = false">
                        <button type="button" @click="toggleDropdown('status')"
                                class="bg-white border border-slate-300 text-xs sm:text-sm rounded-xl px-3 py-2.5 flex items-center justify-between w-full text-left focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:outline-none transition"
                                aria-haspopup="listbox"
                                :aria-expanded="openStatus">
                            <span x-text="status || 'Semua Status'" :class="{'text-slate-700 font-medium': !status, 'font-bold text-indigo-700': status}" class="truncate pr-2"></span>
                            <svg class="w-4 h-4 text-slate-400 transition-transform duration-150 shrink-0" :class="{'rotate-180 text-indigo-600': openStatus}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="openStatus"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 translate-y-1"
                             class="absolute left-0 sm:right-0 sm:left-auto z-50 mt-1 w-full min-w-[160px] bg-white border border-slate-200 rounded-xl shadow-xl py-1 max-h-60 overflow-y-auto"
                             style="display: none;"
                             role="listbox">
                            <button type="button" @click="selectFilter('status', '')"
                                    class="w-full text-left px-3.5 py-2 text-xs sm:text-sm text-slate-700 hover:bg-slate-50 transition flex items-center justify-between"
                                    :class="{'font-bold text-indigo-700 bg-indigo-50/50': status === ''}"
                                    role="option"
                                    :aria-selected="status === ''">
                                <span>Semua Status</span>
                                <svg x-show="status === ''" class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </button>
                            @foreach(['Selesai', 'Proses', 'Pending'] as $s)
                                <button type="button" @click="selectFilter('status', '{{ $s }}')"
                                        class="w-full text-left px-3.5 py-2 text-xs sm:text-sm text-slate-700 hover:bg-slate-50 transition flex items-center justify-between"
                                        :class="{'font-bold text-indigo-700 bg-indigo-50/50': status === '{{ $s }}'}"
                                        role="option"
                                        :aria-selected="status === '{{ $s }}'">
                                    <span>{{ $s }}</span>
                                    <svg x-show="status === '{{ $s }}'" class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Reset Filter Button --}}
                    <div class="flex items-center" x-show="activeFilterCount > 0" x-transition>
                        <button type="button" @click="resetFilters()" 
                                class="w-full sm:w-auto inline-flex justify-center items-center px-3.5 py-2.5 text-xs sm:text-sm font-bold text-slate-700 hover:text-slate-900 bg-slate-200/70 hover:bg-slate-200 rounded-xl transition">
                            Reset Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Data Container --}}
        <div id="maintenanceContainer" class="relative z-10 transition-opacity duration-150" :class="{'opacity-50 pointer-events-none': isLoading}">
            
            {{-- Loading State --}}
            <template x-if="isLoading">
                <div class="p-4 sm:p-6 space-y-4">
                    <div class="animate-pulse flex justify-between items-center gap-4">
                        <div class="h-4 bg-slate-200 rounded w-1/3"></div>
                        <div class="h-4 bg-slate-200 rounded w-1/4"></div>
                    </div>
                    <div class="space-y-3 pt-2">
                        <div class="h-10 bg-slate-100 rounded-lg"></div>
                        <div class="h-10 bg-slate-100 rounded-lg"></div>
                        <div class="h-10 bg-slate-100 rounded-lg"></div>
                    </div>
                </div>
            </template>

            {{-- Table & Mobile List --}}
            <div x-show="!isLoading">
                
                {{-- MOBILE VIEW: Compact List Layout --}}
                <div class="block sm:hidden divide-y divide-slate-100">
                    @forelse($maintenances as $maintenance)
                        <div class="p-4 space-y-2 hover:bg-slate-50/70 transition-colors">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0 flex-1">
                                    {{-- Target / Nama Alat --}}
                                    <div class="flex items-center gap-2 flex-wrap">
                                        @if($maintenance->barang)
                                            <a href="{{ route('barang.show', $maintenance->barang_id) }}" class="font-bold text-slate-900 hover:text-indigo-600 text-sm leading-snug truncate">
                                                {{ $maintenance->barang->nama_barang }}
                                            </a>
                                        @else
                                            <span class="font-bold text-slate-900 text-sm leading-snug">Pemeliharaan Fasilitas Lab</span>
                                        @endif
                                    </div>

                                    {{-- Kendala & Deskripsi --}}
                                    <p class="text-xs text-slate-600 mt-1 line-clamp-1">
                                        {{ $maintenance->deskripsi_kerusakan ?: ($maintenance->tindakan ?: 'Perawatan berkala laboratorium') }}
                                    </p>

                                    {{-- Meta: Tanggal · Teknisi · Lab --}}
                                    <p class="text-xs text-slate-500 mt-1 flex items-center gap-2 flex-wrap">
                                        <span>{{ \Carbon\Carbon::parse($maintenance->tanggal_maintenance)->translatedFormat('d M Y') }}</span>
                                        <span>·</span>
                                        <span class="font-semibold text-slate-700">{{ $maintenance->teknisi ?: 'Internal Lab' }}</span>
                                        @if($maintenance->laboratorium)
                                            <span>·</span>
                                            <span>{{ str_replace('Laboratorium ', 'Lab ', $maintenance->laboratorium) }}</span>
                                        @endif
                                    </p>
                                </div>

                                {{-- Status Badge & Link --}}
                                <div class="flex flex-col items-end justify-between shrink-0 gap-2">
                                    @if($maintenance->status === 'Selesai')
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">Selesai</span>
                                    @elseif($maintenance->status === 'Proses')
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">Proses</span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700">Pending</span>
                                    @endif

                                    <a href="{{ route('maintenance.show', $maintenance->id) }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 inline-flex items-center gap-0.5 py-1">
                                        <span>Detail</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 px-4 text-center">
                            <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <p class="text-sm font-bold text-slate-800">Belum ada riwayat maintenance</p>
                            <p class="text-xs text-slate-500 mt-1">Catatan perbaikan dan perawatan alat akan muncul di sini.</p>
                        </div>
                    @endforelse
                </div>

                {{-- DESKTOP & TABLET TABLE VIEW --}}
                <div class="hidden sm:block overflow-x-auto rounded-b-2xl">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50/80">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Laboratorium</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Target / Barang</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Teknisi & Tgl</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Jenis</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Biaya</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-100 text-xs sm:text-sm">
                            @forelse($maintenances as $maintenance)
                            @php
                                $canManageThis = Auth::user()->isAdmin() || (Auth::user()->isKoordinatorLab() && Auth::user()->laboratorium_penugasan === $maintenance->laboratorium);
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-bold text-slate-900 text-xs sm:text-sm">
                                        {{ $maintenance->laboratorium ?: 'Laboratorium TKJ' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($maintenance->barang)
                                        <a href="{{ route('barang.show', $maintenance->barang_id) }}" class="font-bold text-slate-800 hover:text-indigo-600">{{ $maintenance->barang->nama_barang }}</a>
                                        <div class="text-xs font-mono text-slate-500">
                                            {{ $maintenance->barang->kode_barang }}
                                            @if(!empty($maintenance->unit_index))
                                                <span class="text-slate-900 font-bold">• Unit {{ $maintenance->unit_index }}</span>
                                            @endif
                                        </div>
                                    @else
                                        <div class="font-bold text-slate-800">Pemeliharaan Fasilitas Lab</div>
                                        <div class="text-xs text-slate-500">Fasilitas / Ruangan Umum</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-bold text-slate-800">{{ $maintenance->teknisi ?: 'Internal Lab' }}</div>
                                    <div class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($maintenance->tanggal_maintenance)->translatedFormat('d M Y') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-xs font-bold text-slate-900">{{ $maintenance->jenis }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap font-mono font-medium text-slate-700">
                                    {{ $maintenance->biaya ? 'Rp ' . number_format($maintenance->biaya, 0, ',', '.') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-xs font-black text-slate-900">{{ $maintenance->status }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="flex justify-end items-center gap-2">
                                        <a href="{{ route('maintenance.show', $maintenance->id) }}" 
                                           title="Detail Maintenance" 
                                           class="w-8 h-8 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-600 flex items-center justify-center transition active:scale-95"
                                           aria-label="Detail">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                        @if($canManageThis)
                                            <a href="{{ route('maintenance.edit', $maintenance->id) }}" 
                                               title="Edit Maintenance" 
                                               class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition active:scale-95"
                                               aria-label="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                            <button type="button" 
                                                    @click="confirmDelete('{{ route('maintenance.destroy', $maintenance->id) }}', 'Maintenance {{ addslashes($maintenance->barang->nama_barang ?? ($maintenance->laboratorium . ' - Fasilitas')) }}')" 
                                                    title="Hapus Maintenance" 
                                                    class="w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center transition active:scale-95"
                                                    aria-label="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-bold text-slate-800">Belum ada riwayat maintenance</p>
                                    <p class="text-xs text-slate-500 mt-1">Catatan perbaikan dan perawatan alat akan muncul di sini.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($maintenances->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 rounded-b-2xl">
                    {{ $maintenances->withQueryString()->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- MODAL KONFIRMASI HAPUS --}}
    <template x-teleport="body">
        <div x-show="deleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" style="display: none;"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             role="dialog"
             aria-modal="true"
             aria-labelledby="deleteModalTitle">
            
            <div @click.away="if(!isDeleting) deleteModal = false" class="bg-white rounded-2xl p-6 w-full max-w-sm shadow-xl border border-slate-200 text-center"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">
                
                <div class="w-10 h-10 bg-rose-100 text-rose-600 rounded-xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>

                <h2 id="deleteModalTitle" class="text-base font-bold text-slate-900">Hapus Riwayat Maintenance?</h2>
                <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                    Data <strong class="text-slate-800" x-text="deleteItemName"></strong> akan dihapus permanen.
                </p>

                <div class="mt-6 flex items-center justify-center gap-3">
                    <button type="button" 
                            :disabled="isDeleting"
                            @click="deleteModal = false" 
                            class="flex-1 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 disabled:opacity-50 text-slate-700 font-bold rounded-xl text-xs sm:text-sm transition">
                        Batal
                    </button>
                    <button type="button" 
                            :disabled="isDeleting"
                            @click="submitDelete()" 
                            class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-rose-600 hover:bg-rose-700 disabled:bg-rose-400 text-white font-bold rounded-xl text-xs sm:text-sm shadow-xs transition active:scale-95">
                        <svg x-show="isDeleting" class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24" style="display: none;" aria-hidden="true">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="isDeleting ? 'Menghapus...' : 'Ya, Hapus'"></span>
                    </button>
                </div>
            </div>
        </div>
    </template>

    <form id="globalDeleteForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
</div>

<script>
function maintenanceIndex() {
    return {
        deleteModal: false,
        isDeleting: false,
        deleteActionUrl: '',
        deleteItemName: '',

        mobileFilterOpen: false,
        openLab: false,
        openJenis: false,
        openStatus: false,
        isLoading: false,

        search: @json(request('search', '')),
        laboratorium: @json(request('laboratorium', $lockedLab ?? '')),
        jenis: @json(request('jenis', '')),
        status: @json(request('status', '')),

        get activeFilterCount() {
            let count = 0;
            if (this.search) count++;
            if (this.laboratorium && this.laboratorium !== @json($lockedLab ?? '')) count++;
            if (this.jenis) count++;
            if (this.status) count++;
            return count;
        },

        init() {},

        toggleDropdown(type) {
            if (type === 'lab') {
                this.openLab = !this.openLab;
                this.openJenis = false;
                this.openStatus = false;
            } else if (type === 'jenis') {
                this.openJenis = !this.openJenis;
                this.openLab = false;
                this.openStatus = false;
            } else if (type === 'status') {
                this.openStatus = !this.openStatus;
                this.openLab = false;
                this.openJenis = false;
            }
        },

        confirmDelete(url, name) {
            this.deleteActionUrl = url;
            this.deleteItemName = name;
            this.isDeleting = false;
            this.deleteModal = true;
        },

        submitDelete() {
            this.isDeleting = true;
            const form = document.getElementById('globalDeleteForm');
            form.action = this.deleteActionUrl;
            setTimeout(() => {
                form.submit();
            }, 250);
        },

        selectFilter(type, value) {
            this[type] = value;
            this.openLab = false;
            this.openJenis = false;
            this.openStatus = false;
            this.fetchData();
        },

        resetFilters() {
            this.search = '';
            this.laboratorium = @json($lockedLab ?? '');
            this.jenis = '';
            this.status = '';
            this.mobileFilterOpen = false;
            this.fetchData();
        },

        async fetchData() {
            this.isLoading = true;

            const params = new URLSearchParams();
            if (this.search) params.append('search', this.search);
            if (this.laboratorium) params.append('laboratorium', this.laboratorium);
            if (this.jenis) params.append('jenis', this.jenis);
            if (this.status) params.append('status', this.status);

            const url = `{{ route('maintenance.index') }}?${params.toString()}`;
            window.history.pushState({}, '', url);

            try {
                const res = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!res.ok) throw new Error('Network response error');
                const htmlText = await res.text();

                const parser = new DOMParser();
                const doc = parser.parseFromString(htmlText, 'text/html');
                const newContainer = doc.getElementById('maintenanceContainer');

                if (newContainer) {
                    document.getElementById('maintenanceContainer').innerHTML = newContainer.innerHTML;
                } else {
                    window.location.reload();
                }
            } catch (err) {
                console.error("Fetch filter error:", err);
            } finally {
                this.isLoading = false;
            }
        }
    };
}
</script>
@endsection