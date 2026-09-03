@extends('layouts.app')

@section('title', 'Catat Maintenance')
@section('page_title', 'Catat Maintenance')
@section('page_subtitle', 'Catat data perawatan atau perbaikan barang laboratorium')

@section('content')
<div class="max-w-4xl mx-auto space-y-4 sm:space-y-6" x-data="maintenanceCreate()">

    {{-- Header Halaman Asli --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Catat Maintenance</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Catat data perawatan atau perbaikan barang laboratorium</p>
        </div>
        <a href="{{ route('maintenance.index') }}" class="bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold px-3.5 py-2 rounded-xl text-xs transition-colors shadow-2xs">
            Kembali
        </a>
    </div>

    {{-- Card Form Utama --}}
    <div class="bg-white shadow-xs border border-slate-200/80 rounded-2xl sm:rounded-3xl relative z-10">
        <div class="px-5 py-2.5 sm:py-3 border-b border-slate-800 bg-slate-900 text-white rounded-t-2xl sm:rounded-t-3xl">
            <h2 class="text-sm sm:text-base font-bold text-white uppercase tracking-wider">Form Catat Maintenance</h2>
            <p class="text-xs text-slate-300 mt-0.5">Isi rincian perbaikan, teknisi, dan barang yang dirawat</p>
        </div>
        <div class="p-5 sm:p-8">
        <form id="createMaintenanceForm" action="{{ route('maintenance.store') }}" method="POST" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 gap-y-4 sm:grid-cols-2 sm:gap-x-6">
                
                {{-- 0. Dropdown: Pilihan Laboratorium --}}
                @if(!empty($lockedLab))
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Laboratorium <span class="text-rose-500">*</span></label>
                        <input type="hidden" name="laboratorium" value="{{ $lockedLab }}">
                        <div class="w-full bg-emerald-50/70 border border-emerald-300/80 text-emerald-950 text-xs sm:text-sm rounded-xl px-3.5 py-2.5 flex items-center justify-between">
                            <div class="flex items-center gap-2.5 truncate">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                                <div>
                                    <span class="font-bold text-slate-900 text-xs sm:text-sm block">{{ $lockedLab }}</span>
                                    <span class="text-[11px] text-emerald-700 font-medium">Terkunci sesuai penugasan Koordinator Laboratorium</span>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold bg-emerald-100 text-emerald-800 border border-emerald-300 shrink-0">
                                Koordinator
                            </span>
                        </div>
                    </div>
                @else
                    <div class="sm:col-span-2 relative" @click.outside="closeIf('laboratorium')">
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Laboratorium <span class="text-rose-500">*</span></label>
                        <input type="hidden" name="laboratorium" :value="selectedLaboratorium" required>

                        <button type="button" @click="toggle('laboratorium')"
                                class="w-full bg-slate-50/60 hover:bg-slate-50 border border-slate-200 text-slate-800 text-xs sm:text-sm rounded-xl px-3.5 py-2.5 flex items-center justify-between text-left focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white focus:outline-none transition-colors">
                            <span x-text="selectedLaboratorium" class="font-semibold text-slate-800"></span>
                            <svg class="w-4 h-4 text-slate-400 transition-transform duration-150 shrink-0" :class="{'rotate-180 text-indigo-600': openDropdown === 'laboratorium'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="openDropdown === 'laboratorium'"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="absolute left-0 right-0 z-50 mt-1 bg-white border border-slate-200 rounded-xl shadow-lg py-1 overflow-hidden"
                             style="display: none;">
                            @foreach(['Laboratorium TKJ', 'Laboratorium AKL', 'Laboratorium Pemasaran'] as $lab)
                                <button type="button" @click="selectedLaboratorium = '{{ $lab }}'; openDropdown = null"
                                        class="w-full text-left px-3.5 py-2.5 text-xs sm:text-sm text-slate-700 hover:bg-slate-50 transition-colors flex items-center justify-between"
                                        :class="{'font-semibold text-indigo-600 bg-indigo-50/50': selectedLaboratorium === '{{ $lab }}'}">
                                    <span>{{ $lab }}</span>
                                    <svg x-show="selectedLaboratorium === '{{ $lab }}'" class="w-4 h-4 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- 1. Dropdown: Barang Laboratorium --}}
                <div class="sm:col-span-2 relative" @click.outside="closeIf('barang')">
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Barang Laboratorium <span class="text-slate-400 font-normal lowercase">(opsional)</span></label>
                        <button type="button" x-show="selectedBarangId" @click="selectBarang('')" class="text-[11px] font-semibold text-rose-600 hover:underline">
                            Kosongkan (Maintenance Ruangan / Fasilitas Umum)
                        </button>
                    </div>
                    <input type="hidden" name="barang_id" :value="selectedBarangId">

                    <button type="button" @click="toggle('barang')"
                            class="w-full bg-slate-50/60 hover:bg-slate-50 border border-slate-200 text-slate-800 text-xs sm:text-sm rounded-xl px-3.5 py-2.5 flex items-center justify-between text-left focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white focus:outline-none transition-colors">
                        <span x-text="currentBarangLabel" :class="{'font-semibold text-slate-900': selectedBarangId, 'text-slate-500': !selectedBarangId}" class="truncate pr-2"></span>
                        <svg class="w-4 h-4 text-slate-400 transition-transform duration-150 shrink-0" :class="{'rotate-180 text-indigo-600': openDropdown === 'barang'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="openDropdown === 'barang'"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="absolute left-0 right-0 z-50 mt-1 max-h-60 bg-white border border-slate-200 rounded-xl shadow-lg py-1 overflow-y-auto"
                         style="display: none;">
                        
                        <button type="button" @click="selectBarang('')"
                                class="w-full text-left px-3.5 py-2.5 text-xs sm:text-sm text-slate-700 hover:bg-slate-50 border-b border-slate-100 transition-colors flex items-center justify-between"
                                :class="{'font-semibold text-indigo-600 bg-indigo-50/50': !selectedBarangId}">
                            <div>
                                <div class="font-semibold text-slate-800">Tanpa Barang Khusus (Maintenance Fasilitas / Ruangan Lab)</div>
                                <div class="text-[11px] text-slate-500">Pemeliharaan AC, instalasi kabel, kelistrikan, meja/kursi, kebersihan dsb.</div>
                            </div>
                            <svg x-show="!selectedBarangId" class="w-4 h-4 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </button>

                        <template x-for="b in barangsList" :key="b.id">
                            <button type="button" @click="selectBarang(b.id)"
                                    class="w-full text-left px-3.5 py-2.5 text-xs sm:text-sm text-slate-700 hover:bg-slate-50 transition-colors flex items-center justify-between"
                                    :class="{'font-semibold text-indigo-600 bg-indigo-50/50': selectedBarangId == b.id}">
                                <div class="truncate pr-2">
                                    <div x-text="b.nama" class="font-semibold text-slate-900 truncate"></div>
                                    <div class="text-[11px] text-slate-500 font-mono" x-text="`${b.kode} • Kondisi: ${b.kondisi}`"></div>
                                </div>
                                <svg x-show="selectedBarangId == b.id" class="w-4 h-4 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- 2. Multi-Select Target Unit --}}
                <div class="sm:col-span-2 relative" x-show="currentBarang && currentBarang.jumlah > 1" @click.outside="closeIf('unit')">
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Target Unit Barang</label>
                        <span class="text-[10px] text-indigo-600 font-semibold" x-text="`${selectedUnits.length} unit terpilih`"></span>
                    </div>
                    <input type="hidden" name="unit_index" :value="selectedUnitsString">

                    <button type="button" @click="toggle('unit')"
                            class="w-full bg-slate-50/60 hover:bg-slate-50 border border-slate-200 text-slate-800 text-xs sm:text-sm rounded-xl px-3.5 py-2.5 flex items-center justify-between text-left focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white focus:outline-none transition-colors">
                        <span x-text="currentUnitLabel" :class="{'font-semibold text-indigo-700': selectedUnits.length > 0}" class="truncate pr-2"></span>
                        <svg class="w-4 h-4 text-slate-400 transition-transform duration-150 shrink-0" :class="{'rotate-180 text-indigo-600': openDropdown === 'unit'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="openDropdown === 'unit'"
                         @click.stop
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="absolute left-0 right-0 z-50 mt-1 max-h-60 bg-white border border-slate-200 rounded-xl shadow-lg p-2.5 space-y-1"
                         style="display: none;">
                        
                        <div class="flex items-center justify-between px-2 py-1.5 border-b border-slate-100 mb-1">
                            <span class="text-[11px] font-semibold text-slate-500">Centang unit (Kondisi Baik):</span>
                            <button type="button" @click.stop="selectedUnits = []" class="text-[10px] font-semibold text-rose-600 hover:underline">Reset</button>
                        </div>

                        <div class="max-h-44 overflow-y-auto space-y-1 pr-1">
                            <template x-for="u in unitsAvailableForMaintenance" :key="u.index">
                                <label class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs sm:text-sm hover:bg-slate-50 cursor-pointer transition-colors select-none"
                                       :class="{'bg-indigo-50/70 font-semibold text-indigo-900': selectedUnits.includes(String(u.index))}">
                                    <input type="checkbox" :value="String(u.index)" x-model="selectedUnits" @click.stop
                                           class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                                    <div class="flex items-center gap-2 flex-1">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                                        <span x-text="`Unit ${u.index} (${currentBarang.kode}-${u.index})`"></span>
                                    </div>
                                </label>
                            </template>
                        </div>

                        <div x-show="unitsAvailableForMaintenance.length === 0" class="px-3 py-3 text-center text-xs text-slate-500">
                            Tidak ada unit berkondisi Baik yang tersedia.
                        </div>
                    </div>
                </div>

                {{-- Nama Teknisi --}}
                <div class="sm:col-span-2">
                    <label for="teknisi" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Nama Teknisi <span class="text-rose-500">*</span></label>
                    <input type="text" name="teknisi" id="teknisi" value="{{ old('teknisi') }}" required 
                           class="bg-slate-50/60 focus:bg-white border border-slate-200 text-slate-800 text-xs sm:text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:outline-none block w-full px-3.5 py-2.5 transition-colors" 
                           placeholder="Contoh: PT. Optik Solusindo / Teknisi Lab">
                </div>

                {{-- Tanggal Maintenance --}}
                <div>
                    <label for="tanggal_maintenance" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Tanggal Maintenance <span class="text-rose-500">*</span></label>
                    <input type="date" name="tanggal_maintenance" id="tanggal_maintenance" value="{{ old('tanggal_maintenance', date('Y-m-d')) }}" required 
                           class="bg-slate-50/60 focus:bg-white border border-slate-200 text-slate-800 text-xs sm:text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:outline-none block w-full px-3.5 py-2.5 transition-colors">
                </div>

                {{-- Jenis Maintenance --}}
                <div class="relative" @click.outside="closeIf('jenis')">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Jenis Maintenance <span class="text-rose-500">*</span></label>
                    <input type="hidden" name="jenis" :value="selectedJenis" required>

                    <button type="button" @click="toggle('jenis')"
                            class="w-full bg-slate-50/60 hover:bg-slate-50 border border-slate-200 text-slate-800 text-xs sm:text-sm rounded-xl px-3.5 py-2.5 flex items-center justify-between text-left focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white focus:outline-none transition-colors">
                        <span x-text="currentJenisLabel" class="font-semibold text-slate-800"></span>
                        <svg class="w-4 h-4 text-slate-400 transition-transform duration-150 shrink-0" :class="{'rotate-180 text-indigo-600': openDropdown === 'jenis'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="openDropdown === 'jenis'"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="absolute left-0 right-0 z-50 mt-1 bg-white border border-slate-200 rounded-xl shadow-lg py-1 overflow-hidden"
                         style="display: none;">
                        @foreach(['Preventif' => 'Preventif (Perawatan Rutin)', 'Korektif' => 'Korektif (Perbaikan)', 'Penggantian' => 'Penggantian Suku Cadang'] as $val => $lbl)
                            <button type="button" @click="selectedJenis = '{{ $val }}'; openDropdown = null"
                                    class="w-full text-left px-3.5 py-2.5 text-xs sm:text-sm text-slate-700 hover:bg-slate-50 transition-colors flex items-center justify-between"
                                    :class="{'font-semibold text-indigo-600 bg-indigo-50/50': selectedJenis === '{{ $val }}'}">
                                <span>{{ $lbl }}</span>
                                <svg x-show="selectedJenis === '{{ $val }}'" class="w-3.5 h-3.5 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Status --}}
                <div class="sm:col-span-2 relative" @click.outside="closeIf('status')">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Status <span class="text-rose-500">*</span></label>
                    <input type="hidden" name="status" :value="selectedStatus" required>

                    <button type="button" @click="toggle('status')"
                            class="w-full bg-slate-50/60 hover:bg-slate-50 border border-slate-200 text-slate-800 text-xs sm:text-sm rounded-xl px-3.5 py-2.5 flex items-center justify-between text-left focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white focus:outline-none transition-colors">
                        <span x-text="currentStatusLabel" class="font-semibold text-slate-800"></span>
                        <svg class="w-4 h-4 text-slate-400 transition-transform duration-150 shrink-0" :class="{'rotate-180 text-indigo-600': openDropdown === 'status'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="openDropdown === 'status'"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="absolute left-0 right-0 z-50 mt-1 bg-white border border-slate-200 rounded-xl shadow-lg py-1 overflow-hidden"
                         style="display: none;">
                        @foreach(['Proses' => 'Sedang Proses', 'Selesai' => 'Selesai (Otomatis perbarui kondisi menjadi Baik)', 'Pending' => 'Pending (Menunggu)'] as $val => $lbl)
                            <button type="button" @click="selectedStatus = '{{ $val }}'; openDropdown = null"
                                    class="w-full text-left px-3.5 py-2.5 text-xs sm:text-sm text-slate-700 hover:bg-slate-50 transition-colors flex items-center justify-between"
                                    :class="{'font-semibold text-indigo-600 bg-indigo-50/50': selectedStatus === '{{ $val }}'}">
                                <span>{{ $lbl }}</span>
                                <svg x-show="selectedStatus === '{{ $val }}'" class="w-3.5 h-3.5 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </button>
                        @endforeach
                    </div>
                    <p class="mt-1 text-[11px] text-slate-500">Catatan: Jika status "Selesai", kondisi unit terpilih otomatis menjadi "Baik".</p>
                </div>

                {{-- Deskripsi Kerusakan --}}
                <div class="sm:col-span-2">
                    <label for="deskripsi_kerusakan" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Deskripsi Kerusakan / Perawatan <span class="text-rose-500">*</span></label>
                    <textarea id="deskripsi_kerusakan" name="deskripsi_kerusakan" rows="3" required 
                              class="bg-slate-50/60 focus:bg-white border border-slate-200 text-slate-800 text-xs sm:text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:outline-none block w-full p-3 transition-colors placeholder:text-slate-400" 
                              placeholder="Contoh: Kabel power terkelupas, pembersihan debu kipas pendingin, konektor kendor...">{{ old('deskripsi_kerusakan') }}</textarea>
                </div>

                {{-- Tindakan --}}
                <div class="sm:col-span-2">
                    <label for="tindakan" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Tindakan yang Dilakukan</label>
                    <textarea id="tindakan" name="tindakan" rows="3" 
                              class="bg-slate-50/60 focus:bg-white border border-slate-200 text-slate-800 text-xs sm:text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:outline-none block w-full p-3 transition-colors placeholder:text-slate-400" 
                              placeholder="Contoh: Penggantian kabel power baru, solder ulang konektor, dan pengujian listrik...">{{ old('tindakan') }}</textarea>
                </div>

                {{-- Biaya Maintenance --}}
                <div class="sm:col-span-2">
                    <label for="biaya_display" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Biaya Maintenance (Rp)</label>
                    <input type="hidden" name="biaya" :value="rawBiaya">
                    <input type="text" 
                           id="biaya_display" 
                           x-model="displayBiaya"
                           @input="formatRupiah($event)"
                           class="bg-slate-50/60 focus:bg-white border border-slate-200 text-slate-800 text-xs sm:text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:outline-none block w-full px-3.5 py-2.5 font-mono font-semibold transition-colors placeholder:text-slate-400" 
                           placeholder="Contoh: 150000 atau ketik 0">
                    <p class="text-[11px] text-slate-500 mt-1">Ketik angka untuk format rupiah otomatis atau tanda <strong>-</strong> jika tanpa biaya.</p>
                </div>

                {{-- Catatan --}}
                <div class="sm:col-span-2">
                    <label for="catatan" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Catatan Tambahan</label>
                    <textarea id="catatan" name="catatan" rows="2" 
                              class="bg-slate-50/60 focus:bg-white border border-slate-200 text-slate-800 text-xs sm:text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:outline-none block w-full p-3 transition-colors placeholder:text-slate-400" 
                              placeholder="Contoh: Masa garansi servis 1 bulan dari vendor, perlu kalibrasi ulang bulan depan...">{{ old('catatan') }}</textarea>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <a href="{{ route('maintenance.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold transition-colors">
                    Batal
                </a>
                <button type="button" 
                        @click="openModal()"
                        class="inline-flex justify-center items-center gap-1.5 px-5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition-colors active:scale-95 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>Simpan Data Maintenance</span>
                </button>
            </div>
        </form>
    </div>

    {{-- Modal Konfirmasi Simpan --}}
    <template x-teleport="body">
        <div x-show="confirmModal" 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" 
             style="display: none;"
             x-transition.opacity.duration.150ms>
            
            <div @click.away="if(!isSubmitting) confirmModal = false" 
                 class="bg-white rounded-2xl p-5 sm:p-6 w-full max-w-sm shadow-xl border border-slate-200 text-center"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100">
                
                <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>

                <h2 class="text-base font-bold text-slate-900">Simpan Data Maintenance?</h2>
                <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                    Pastikan data perawatan atau perbaikan barang yang dimasukkan sudah benar.
                </p>

                <div class="mt-5 flex items-center justify-center gap-2.5">
                    <button type="button" 
                            :disabled="isSubmitting"
                            @click="confirmModal = false" 
                            class="flex-1 px-3.5 py-2 bg-slate-100 hover:bg-slate-200 disabled:opacity-50 text-slate-700 font-semibold rounded-xl text-xs sm:text-sm transition-colors">
                        Batal
                    </button>
                    <button type="button" 
                            :disabled="isSubmitting"
                            @click="submitStore()" 
                            class="flex-1 inline-flex items-center justify-center gap-1.5 px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 disabled:bg-indigo-400 text-white font-bold rounded-xl text-xs sm:text-sm shadow-sm transition-colors active:scale-95">
                        <svg x-show="isSubmitting" class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24" style="display: none;">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="isSubmitting ? 'Menyimpan...' : 'Ya, Simpan'"></span>
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
function maintenanceCreate() {
    return {
        confirmModal: false,
        isSubmitting: false,
        openDropdown: null,
        selectedLaboratorium: @json(old('laboratorium', $lockedLab ?? 'Laboratorium TKJ')),
        selectedBarangId: @json(old('barang_id', '')),
        selectedUnits: @json(old('unit_index') ? (is_array(old('unit_index')) ? old('unit_index') : explode(',', old('unit_index'))) : []),
        selectedJenis: @json(old('jenis', 'Preventif')),
        selectedStatus: @json(old('status', 'Proses')),
        displayBiaya: '',
        rawBiaya: @json(old('biaya', '')),

        init() {
            this.formatInitialBiaya();
        },

        formatInitialBiaya() {
            if (this.rawBiaya && !isNaN(this.rawBiaya) && Number(this.rawBiaya) > 0) {
                this.displayBiaya = 'Rp ' + Number(this.rawBiaya).toLocaleString('id-ID');
            } else if (this.rawBiaya === '0') {
                this.displayBiaya = 'Rp 0';
            } else {
                this.displayBiaya = '-';
                this.rawBiaya = '';
            }
        },

        formatRupiah(event) {
            let val = event.target.value.trim();
            if (val === '' || val === '-') {
                this.displayBiaya = '-';
                this.rawBiaya = '';
                return;
            }

            let numberString = val.replace(/[^0-9]/g, '');
            if (numberString === '') {
                this.displayBiaya = '-';
                this.rawBiaya = '';
                return;
            }

            let formatted = Number(numberString).toLocaleString('id-ID');
            this.displayBiaya = 'Rp ' + formatted;
            this.rawBiaya = numberString;
        },

        barangsList: [
            @foreach($barangs as $b)
            {
                id: '{{ $b->id }}',
                nama: @json($b->nama_barang),
                kode: @json($b->kode_barang),
                kondisi: @json($b->kondisi),
                jumlah: {{ (int) $b->jumlah }},
                kondisi_per_unit: {!! $b->kondisi_per_unit ?: '{}' !!}
            },
            @endforeach
        ],

        get currentBarang() {
            return this.barangsList.find(b => String(b.id) === String(this.selectedBarangId)) || null;
        },

        get currentBarangLabel() {
            if (!this.currentBarang) return 'Tanpa Barang Khusus (Maintenance Fasilitas / Ruangan Lab)';
            return `${this.currentBarang.nama} (${this.currentBarang.kode}) - Kondisi: ${this.currentBarang.kondisi}`;
        },

        get selectedUnitsString() {
            return this.selectedUnits.join(',');
        },

        get unitsAvailableForMaintenance() {
            if (!this.currentBarang || this.currentBarang.jumlah <= 1) return [];
            
            const list = [];
            for (let i = 1; i <= this.currentBarang.jumlah; i++) {
                const k = (this.currentBarang.kondisi_per_unit && this.currentBarang.kondisi_per_unit[i]) || this.currentBarang.kondisi;
                if (k === 'Baik') {
                    list.push({ index: i, kondisi: k });
                }
            }
            return list;
        },

        get currentUnitLabel() {
            if (this.selectedUnits.length === 0) return 'Seluruh Unit (General)';
            const sorted = [...this.selectedUnits].map(Number).sort((a, b) => a - b);
            return `Unit ${sorted.join(', ')} (${sorted.length} unit dipilih)`;
        },

        get currentJenisLabel() {
            const labels = {
                'Preventif': 'Preventif (Perawatan Rutin)',
                'Korektif': 'Korektif (Perbaikan)',
                'Penggantian': 'Penggantian Suku Cadang'
            };
            return labels[this.selectedJenis] || 'Pilih Jenis';
        },

        get currentStatusLabel() {
            const labels = {
                'Proses': 'Sedang Proses',
                'Selesai': 'Selesai (Otomatis perbarui kondisi menjadi Baik)',
                'Pending': 'Pending (Menunggu)'
            };
            return labels[this.selectedStatus] || 'Pilih Status';
        },

        toggle(name) {
            this.openDropdown = (this.openDropdown === name) ? null : name;
        },

        closeIf(name) {
            if (this.openDropdown === name) {
                this.openDropdown = null;
            }
        },

        selectBarang(id) {
            this.selectedBarangId = id;
            this.selectedUnits = [];
            this.openDropdown = null;
        },

        openModal() {
            const form = document.getElementById('createMaintenanceForm');
            if (form && !form.checkValidity()) {
                form.reportValidity();
                return;
            }
            this.isSubmitting = false;
            this.confirmModal = true;
        },

        submitStore() {
            this.isSubmitting = true;
            setTimeout(() => {
                const form = document.getElementById('createMaintenanceForm');
                if (form) form.submit();
            }, 250);
        }
    };
}
</script>
@endsection