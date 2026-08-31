@extends('layouts.app')

@section('title', 'Laporan Inventaris')
@section('page_title', 'Laporan Inventaris Aset')
@section('page_subtitle', 'Rekap data barang & nilai aset semua laboratorium')

@section('content')

{{-- Filter & Actions Bar (Real-time Instant Client-Side Filter dengan Animasi Dropdown) --}}
<div class="bg-white rounded-2xl sm:rounded-3xl shadow-sm border border-slate-200/60 p-4 sm:p-5 mb-6 no-print" x-data="laporanFilter()">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 flex-1">
            
            {{-- 1. Animated Dropdown: Laboratorium Filter --}}
            <div class="relative" x-data="{ open: false }">
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Laboratorium</label>
                
                <button type="button" @click="open = !open" @click.outside="open = false"
                        class="w-full bg-slate-50/70 border border-slate-200 text-slate-800 text-xs sm:text-sm rounded-xl px-3 py-2.5 flex items-center justify-between text-left focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                    <span x-text="filterLaboratorium ? filterLaboratorium : 'Semua Laboratorium'" :class="{'font-bold text-indigo-700': filterLaboratorium}"></span>
                    <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 flex-shrink-0" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="open"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                     class="absolute z-50 mt-1 w-full bg-white border border-slate-200 rounded-2xl shadow-xl py-1 overflow-hidden"
                     style="display: none;">
                    <button type="button" @click="selectLaboratorium(''); open = false"
                            class="w-full text-left px-3.5 py-2 text-xs sm:text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 transition flex items-center justify-between"
                            :class="{'font-bold text-indigo-600 bg-indigo-50/50': filterLaboratorium === ''}">
                        <span>Semua Laboratorium</span>
                        <svg x-show="filterLaboratorium === ''" class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </button>
                    @foreach(['Laboratorium TKJ', 'Laboratorium AKL', 'Laboratorium Pemasaran'] as $lab)
                        <button type="button" @click="selectLaboratorium('{{ $lab }}'); open = false"
                                class="w-full text-left px-3.5 py-2 text-xs sm:text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 transition flex items-center justify-between"
                                :class="{'font-bold text-indigo-600 bg-indigo-50/50': filterLaboratorium === '{{ $lab }}'}">
                            <span>{{ $lab }}</span>
                            <svg x-show="filterLaboratorium === '{{ $lab }}'" class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- 2. Animated Dropdown: Kategori Filter --}}
            <div class="relative" x-data="{ open: false }">
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Kategori</label>
                
                <button type="button" @click="open = !open" @click.outside="open = false"
                        class="w-full bg-slate-50/70 border border-slate-200 text-slate-800 text-xs sm:text-sm rounded-xl px-3 py-2.5 flex items-center justify-between text-left focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                    <span x-text="filterKategori ? filterKategori : 'Semua Kategori'" :class="{'font-bold text-indigo-700': filterKategori}"></span>
                    <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 flex-shrink-0" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="open"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                     class="absolute z-50 mt-1 w-full bg-white border border-slate-200 rounded-2xl shadow-xl py-1 overflow-hidden"
                     style="display: none;">
                    <button type="button" @click="selectKategori(''); open = false"
                            class="w-full text-left px-3.5 py-2 text-xs sm:text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 transition flex items-center justify-between"
                            :class="{'font-bold text-indigo-600 bg-indigo-50/50': filterKategori === ''}">
                        <span>Semua Kategori</span>
                        <svg x-show="filterKategori === ''" class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </button>
                    @foreach(['Jaringan', 'Komputer', 'Perangkat Keras', 'Alat Praktik', 'Furniture', 'Lainnya'] as $kat)
                        <button type="button" @click="selectKategori('{{ $kat }}'); open = false"
                                class="w-full text-left px-3.5 py-2 text-xs sm:text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 transition flex items-center justify-between"
                                :class="{'font-bold text-indigo-600 bg-indigo-50/50': filterKategori === '{{ $kat }}'}">
                            <span>{{ $kat }}</span>
                            <svg x-show="filterKategori === '{{ $kat }}'" class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- 3. Animated Dropdown: Kondisi Filter (Mendukung Kondisi Keseluruhan & Per-Unit) --}}
            <div class="relative" x-data="{ open: false }">
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Kondisi</label>
                
                <button type="button" @click="open = !open" @click.outside="open = false"
                        class="w-full bg-slate-50/70 border border-slate-200 text-slate-800 text-xs sm:text-sm rounded-xl px-3 py-2.5 flex items-center justify-between text-left focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                    <span x-text="filterKondisi ? filterKondisi : 'Semua Kondisi'" :class="{'font-bold text-indigo-700': filterKondisi}"></span>
                    <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 flex-shrink-0" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="open"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                     class="absolute z-50 mt-1 w-full bg-white border border-slate-200 rounded-2xl shadow-xl py-1 overflow-hidden"
                     style="display: none;">
                    <button type="button" @click="selectKondisi(''); open = false"
                            class="w-full text-left px-3.5 py-2 text-xs sm:text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 transition flex items-center justify-between"
                            :class="{'font-bold text-indigo-600 bg-indigo-50/50': filterKondisi === ''}">
                        <span>Semua Kondisi</span>
                        <svg x-show="filterKondisi === ''" class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </button>
                    @foreach(['Baik' => 'emerald', 'Perawatan' => 'yellow', 'Perbaikan' => 'orange', 'Rusak Berat' => 'red', 'Hilang' => 'slate'] as $kon => $color)
                        <button type="button" @click="selectKondisi('{{ $kon }}'); open = false"
                                class="w-full text-left px-3.5 py-2 text-xs sm:text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 transition flex items-center justify-between"
                                :class="{'font-bold text-indigo-600 bg-indigo-50/50': filterKondisi === '{{ $kon }}'}">
                            <span class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-{{ $color }}-500"></span>
                                <span>{{ $kon }}</span>
                            </span>
                            <svg x-show="filterKondisi === '{{ $kon }}'" class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- 4. Animated Dropdown: Sumber Dana Filter --}}
            <div class="relative" x-data="{ open: false }">
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Sumber Dana</label>
                
                <button type="button" @click="open = !open" @click.outside="open = false"
                        class="w-full bg-slate-50/70 border border-slate-200 text-slate-800 text-xs sm:text-sm rounded-xl px-3 py-2.5 flex items-center justify-between text-left focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                    <span x-text="filterSumberDana ? filterSumberDana : 'Semua Sumber Dana'" :class="{'font-bold text-indigo-700': filterSumberDana}"></span>
                    <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 flex-shrink-0" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="open"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                     class="absolute z-50 mt-1 w-full bg-white border border-slate-200 rounded-2xl shadow-xl py-1 overflow-hidden"
                     style="display: none;">
                    <button type="button" @click="selectSumberDana(''); open = false"
                            class="w-full text-left px-3.5 py-2 text-xs sm:text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 transition flex items-center justify-between"
                            :class="{'font-bold text-indigo-600 bg-indigo-50/50': filterSumberDana === ''}">
                        <span>Semua Sumber Dana</span>
                        <svg x-show="filterSumberDana === ''" class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </button>
                    @foreach(['BOS', 'Sekolah', 'Hibah', 'Donasi', 'APBN', 'Lainnya'] as $sd)
                        <button type="button" @click="selectSumberDana('{{ $sd }}'); open = false"
                                class="w-full text-left px-3.5 py-2 text-xs sm:text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 transition flex items-center justify-between"
                                :class="{'font-bold text-indigo-600 bg-indigo-50/50': filterSumberDana === '{{ $sd }}'}">
                            <span>{{ $sd }}</span>
                            <svg x-show="filterSumberDana === '{{ $sd }}'" class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex items-center gap-2 self-end sm:self-auto">
            <button type="button" @click="resetFilter()" x-show="filterLaboratorium || filterKategori || filterKondisi || filterSumberDana" x-transition
                    class="bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs sm:text-sm font-bold px-4 py-2.5 rounded-xl transition-colors">
                Reset
            </button>
            {{-- Tombol Cetak PDF (membuka halaman print standalone dengan tanda mengetahui) --}}
            <button type="button"
                    @click="
                        const params = new URLSearchParams();
                        if (filterLaboratorium) params.set('laboratorium', filterLaboratorium);
                        if (filterKategori) params.set('kategori', filterKategori);
                        if (filterKondisi) params.set('kondisi', filterKondisi);
                        window.open('{{ route('laporan.inventaris.pdf') }}?' + params.toString(), '_blank');
                    "
                    class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs sm:text-sm font-bold px-4 sm:px-5 py-2.5 rounded-xl transition flex items-center gap-2 shadow-sm shadow-indigo-600/20 active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                <span>Cetak PDF</span>
            </button>
        </div>
    </div>
</div>

{{-- Header Cetak Resmi (Hanya tampil saat dicetak) --}}
<div class="hidden print:block mb-8 text-center border-b-2 border-slate-900 pb-4">
    <h1 class="text-xl font-bold uppercase tracking-wider text-slate-900">Sistem Manajemen Laboratorium TKJ</h1>
    <h2 class="text-lg font-semibold text-slate-700">Laporan Inventaris & Rekapitulasi Aset</h2>
    <p class="text-xs text-slate-500 mt-1">Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }}</p>
</div>

{{-- Stat Summaries --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6 mb-6 sm:mb-8">
    <div class="bg-white rounded-2xl sm:rounded-3xl p-5 border border-slate-200/60 shadow-sm">
        <p class="text-[10px] sm:text-xs text-slate-400 font-bold uppercase tracking-wider">Total Jenis Barang Terfilter</p>
        <p class="text-2xl sm:text-3xl font-black text-slate-800 mt-1" id="stat-jenis">{{ $barangs->count() }}</p>
    </div>
    <div class="bg-white rounded-2xl sm:rounded-3xl p-5 border border-slate-200/60 shadow-sm">
        <p class="text-[10px] sm:text-xs text-slate-400 font-bold uppercase tracking-wider">Total Unit Barang Terfilter</p>
        <p class="text-2xl sm:text-3xl font-black text-indigo-600 mt-1" id="stat-unit">{{ $barangs->sum('jumlah') }}</p>
    </div>
    <div class="bg-white rounded-2xl sm:rounded-3xl p-5 border border-slate-200/60 shadow-sm">
        <p class="text-[10px] sm:text-xs text-slate-400 font-bold uppercase tracking-wider">Total Nilai Aset Terfilter</p>
        <p class="text-2xl sm:text-3xl font-black text-emerald-600 mt-1" id="stat-nilai">Rp {{ number_format($totalNilai, 0, ',', '.') }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6 sm:mb-8">
    {{-- Rekap per Kategori --}}
    <div class="lg:col-span-2 bg-white rounded-2xl sm:rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-800 bg-slate-900 text-white">
            <h3 class="font-bold text-white text-xs sm:text-sm uppercase tracking-wider">Rekapitulasi Per Kategori</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50/90 text-slate-600 border-b border-slate-200">
                    <tr class="text-[11px] uppercase tracking-wider font-bold text-slate-600">
                        <th class="px-5 py-3.5">Kategori</th>
                        <th class="px-5 py-3.5 text-center">Jumlah Jenis</th>
                        <th class="px-5 py-3.5 text-center">Total Unit</th>
                        <th class="px-5 py-3.5 text-right">Nilai Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs sm:text-sm">
                    @forelse($byKategori as $kategori => $items)
                        <tr class="rekap-kategori-row hover:bg-slate-50/50 transition-colors" data-kategori="{{ $kategori }}">
                            <td class="px-5 py-3.5 font-bold text-slate-800">{{ $kategori }}</td>
                            <td class="px-5 py-3.5 text-center text-slate-600 font-semibold row-kat-jenis">{{ $items->count() }}</td>
                            <td class="px-5 py-3.5 text-center text-slate-600 font-semibold row-kat-unit">{{ $items->sum('jumlah') }}</td>
                            <td class="px-5 py-3.5 text-right font-extrabold text-slate-800 row-kat-nilai">
                                Rp {{ number_format($items->sum(fn($b) => ($b->harga ?? 0) * $b->jumlah), 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-6 text-center text-slate-400 text-xs">Tidak ada data kategori.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Rekap per Kondisi (Dihitung Real-time dari Detail Kondisi Per Unit) --}}
    <div class="bg-white rounded-2xl sm:rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-800 bg-slate-900 text-white">
            <h3 class="font-bold text-white text-xs sm:text-sm uppercase tracking-wider">Status Kondisi Barang</h3>
        </div>
        <div class="p-5 space-y-4">
            @php $totalUnitSemua = $barangs->sum('jumlah') ?: 1; @endphp
            @foreach(['Baik' => 'emerald', 'Perawatan' => 'yellow', 'Perbaikan' => 'orange', 'Rusak Berat' => 'red', 'Hilang' => 'slate'] as $status => $color)
                @php
                    $unitKondisi = $rekapKondisi[$status] ?? 0;
                    $persen = round(($unitKondisi / $totalUnitSemua) * 100);
                @endphp
                <div class="rekap-kondisi-block" data-status="{{ $status }}">
                    <div class="flex justify-between items-center text-xs font-semibold text-slate-600 mb-1.5">
                        <span class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-{{ $color }}-500"></span>
                            <span>{{ $status }}</span>
                        </span>
                        <span class="kondisi-text-unit font-bold text-slate-800">{{ $unitKondisi }} unit ({{ $persen }}%)</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                        <div class="kondisi-bar-fill bg-{{ $color }}-500 h-2 rounded-full transition-all duration-300" style="width: {{ $persen }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Tabel Inventaris Lengkap --}}
<div class="bg-white rounded-2xl sm:rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-800 bg-slate-900 text-white flex items-center justify-between">
        <h3 class="font-bold text-white text-xs sm:text-sm uppercase tracking-wider">Rincian Seluruh Barang</h3>
        <span class="text-xs text-slate-300 font-medium" id="count-terfilter">{{ $barangs->count() }} barang ditampilkan</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse" id="table-laporan">
            <thead class="bg-slate-50/90 text-slate-600 border-b border-slate-200">
                <tr class="text-[10px] sm:text-[11px] uppercase tracking-wider font-bold text-slate-600">
                    <th class="px-4 py-3.5 font-bold">No</th>
                    <th class="px-4 py-3.5 font-bold">Kode</th>
                    <th class="px-4 py-3.5 font-bold">Nama Barang</th>
                    <th class="px-4 py-3.5 font-bold">Laboratorium</th>
                    <th class="px-4 py-3.5 font-bold">Kategori</th>
                    <th class="px-4 py-3.5 text-center font-bold">Kondisi</th>
                    <th class="px-4 py-3.5 text-center font-bold">Jumlah</th>
                    <th class="px-4 py-3.5 font-bold">Lokasi</th>
                    <th class="px-4 py-3.5 text-right font-bold">Harga Satuan</th>
                    <th class="px-4 py-3.5 text-right font-bold">Nilai Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs">
                @forelse($barangs as $index => $b)
                    <tr class="barang-row hover:bg-slate-50/50 transition-colors"
                        data-laboratorium="{{ $b->laboratorium }}"
                        data-kategori="{{ $b->kategori }}"
                        data-kondisi="{{ $b->kondisi }}"
                        data-kondisi-per-unit="{{ $b->kondisi_per_unit }}"
                        data-sumber-dana="{{ $b->sumber_dana }}"
                        data-jumlah="{{ $b->jumlah }}"
                        data-harga="{{ $b->harga ?? 0 }}">
                        <td class="px-4 py-3.5 text-slate-400 font-medium row-number">{{ $index + 1 }}</td>
                        <td class="px-4 py-3.5 font-mono font-bold text-indigo-700">{{ $b->kode_barang }}</td>
                        <td class="px-4 py-3.5">
                            <p class="font-bold text-slate-800">{{ $b->nama_barang }}</p>
                            @if($b->merk)<p class="text-[10px] text-slate-400 mt-0.5">{{ $b->merk }}</p>@endif
                        </td>
                        <td class="px-4 py-3.5">
                            @if($b->laboratorium)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                    {{ str_replace('Laboratorium ', '', $b->laboratorium) }}
                                </span>
                            @else
                                <span class="text-slate-300">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-slate-600 font-medium">{{ $b->kategori }}</td>
                        <td class="px-4 py-3.5 text-center">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold inline-block
                                @if($b->kondisi == 'Baik') bg-emerald-50 text-emerald-700 border border-emerald-200
                                @elseif($b->kondisi == 'Perawatan') bg-yellow-50 text-yellow-700 border border-yellow-200
                                @elseif($b->kondisi == 'Perbaikan') bg-orange-50 text-orange-700 border border-orange-200
                                @elseif($b->kondisi == 'Rusak Berat') bg-red-50 text-red-700 border border-red-200
                                @else bg-slate-50 text-slate-700 border border-slate-200 @endif">
                                {{ $b->kondisi }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-center font-bold text-slate-700">{{ $b->jumlah }} <span class="text-[10px] font-normal text-slate-400">{{ $b->satuan }}</span></td>
                        <td class="px-4 py-3.5 text-slate-600">{{ $b->lokasi ?? '—' }}</td>
                        <td class="px-4 py-3.5 text-right text-slate-600 font-mono">
                            {{ $b->harga ? 'Rp '.number_format($b->harga, 0, ',', '.') : '—' }}
                        </td>
                        <td class="px-4 py-3.5 text-right font-extrabold text-slate-800 font-mono">
                            {{ $b->harga ? 'Rp '.number_format($b->harga * $b->jumlah, 0, ',', '.') : '—' }}
                        </td>
                    </tr>
                @empty
                    <tr id="empty-row">
                        <td colspan="10" class="px-5 py-8 text-center text-slate-400">Tidak ada data inventaris.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
function laporanFilter() {
    return {
        filterLaboratorium: '',
        filterKategori: '',
        filterKondisi: '',
        filterSumberDana: '',

        selectLaboratorium(val) {
            this.filterLaboratorium = val;
            this.applyFilter();
        },

        selectKategori(val) {
            this.filterKategori = val;
            this.applyFilter();
        },

        selectKondisi(val) {
            this.filterKondisi = val;
            this.applyFilter();
        },

        selectSumberDana(val) {
            this.filterSumberDana = val;
            this.applyFilter();
        },

        resetFilter() {
            this.filterLaboratorium = '';
            this.filterKategori = '';
            this.filterKondisi = '';
            this.filterSumberDana = '';
            this.applyFilter();
        },

        applyFilter() {
            const lab = this.filterLaboratorium;
            const kat = this.filterKategori;
            const kon = this.filterKondisi;
            const sd = this.filterSumberDana;

            const rows = document.querySelectorAll('.barang-row');
            let visibleCount = 0;
            let visibleUnits = 0;
            let visibleNilai = 0;

            const katStats = {};
            const konStats = { 'Baik': 0, 'Perawatan': 0, 'Perbaikan': 0, 'Rusak Berat': 0, 'Hilang': 0 };

            rows.forEach(row => {
                const rLab = row.getAttribute('data-laboratorium');
                const rKat = row.getAttribute('data-kategori');
                const rKon = row.getAttribute('data-kondisi');
                const rSd = row.getAttribute('data-sumber-dana');
                const rJumlah = parseInt(row.getAttribute('data-jumlah')) || 0;
                const rHarga = parseFloat(row.getAttribute('data-harga')) || 0;

                // Ambil data kondisi per unit JSON
                const rKondisiPerUnitStr = row.getAttribute('data-kondisi-per-unit');
                let rKondisiPerUnit = {};
                try {
                    rKondisiPerUnit = JSON.parse(rKondisiPerUnitStr) || {};
                } catch(e) {}

                // Cek apakah barang memiliki unit dengan kondisi yang difilter
                let hasMatchingCondition = false;
                if (!kon) {
                    hasMatchingCondition = true;
                } else {
                    // Cek kondisi global
                    if (rKon === kon) hasMatchingCondition = true;
                    // Cek masing-masing unit
                    for (let i = 1; i <= rJumlah; i++) {
                        const unitKon = rKondisiPerUnit[i] || rKon;
                        if (unitKon === kon) {
                            hasMatchingCondition = true;
                            break;
                        }
                    }
                }

                const matchLab = !lab || rLab === lab;
                const matchKat = !kat || rKat === kat;
                const matchKon = hasMatchingCondition;
                const matchSd = !sd || rSd === sd;

                if (matchLab && matchKat && matchKon && matchSd) {
                    row.style.display = '';
                    visibleCount++;
                    visibleUnits += rJumlah;
                    visibleNilai += (rHarga * rJumlah);

                    // Re-calculate category totals
                    if (!katStats[rKat]) {
                        katStats[rKat] = { jenis: 0, unit: 0, nilai: 0 };
                    }
                    katStats[rKat].jenis++;
                    katStats[rKat].unit += rJumlah;
                    katStats[rKat].nilai += (rHarga * rJumlah);

                    // Re-calculate per-unit condition totals
                    for (let i = 1; i <= rJumlah; i++) {
                        const unitKon = rKondisiPerUnit[i] || rKon;
                        if (konStats.hasOwnProperty(unitKon)) {
                            konStats[unitKon]++;
                        }
                    }

                    // Update visible row number
                    const numCell = row.querySelector('.row-number');
                    if (numCell) numCell.textContent = visibleCount;
                } else {
                    row.style.display = 'none';
                }
            });

            // Update Top Stat Summaries
            document.getElementById('stat-jenis').textContent = visibleCount;
            document.getElementById('stat-unit').textContent = visibleUnits;
            document.getElementById('stat-nilai').textContent = 'Rp ' + visibleNilai.toLocaleString('id-ID');
            document.getElementById('count-terfilter').textContent = visibleCount + ' barang ditampilkan';

            // Update Rekap Kategori Rows
            document.querySelectorAll('.rekap-kategori-row').forEach(kRow => {
                const kName = kRow.getAttribute('data-kategori');
                if (katStats[kName]) {
                    kRow.style.display = '';
                    kRow.querySelector('.row-kat-jenis').textContent = katStats[kName].jenis;
                    kRow.querySelector('.row-kat-unit').textContent = katStats[kName].unit;
                    kRow.querySelector('.row-kat-nilai').textContent = 'Rp ' + katStats[kName].nilai.toLocaleString('id-ID');
                } else {
                    kRow.style.display = 'none';
                }
            });

            // Update Rekap Kondisi Progress Bars
            const totalForProgress = visibleUnits || 1;
            document.querySelectorAll('.rekap-kondisi-block').forEach(cBlock => {
                const cStatus = cBlock.getAttribute('data-status');
                const cUnit = konStats[cStatus] || 0;
                const cPercent = Math.round((cUnit / totalForProgress) * 100);

                const txt = cBlock.querySelector('.kondisi-text-unit');
                if (txt) txt.textContent = cUnit + ' unit (' + cPercent + '%)';

                const bar = cBlock.querySelector('.kondisi-bar-fill');
                if (bar) bar.style.width = cPercent + '%';
            });
        }
    }
}
</script>
@endpush

@endsection