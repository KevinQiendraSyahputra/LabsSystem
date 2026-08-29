@extends('layouts.app')

@section('title', 'Edit Maintenance')
@section('page_title', 'Edit Maintenance')
@section('page_subtitle', 'Perbarui data perawatan atau perbaikan barang laboratorium')

@section('content')

@php
    $initialUnits = [];
    if (old('unit_index')) {
        $initialUnits = is_array(old('unit_index')) ? old('unit_index') : explode(',', old('unit_index'));
    } elseif (!empty($maintenance->unit_index)) {
        $initialUnits = is_array($maintenance->unit_index) ? $maintenance->unit_index : explode(',', (string) $maintenance->unit_index);
    }
    $initialUnits = array_values(array_filter(array_map('trim', $initialUnits)));
@endphp

<div class="max-w-3xl mx-auto space-y-4 sm:space-y-6" x-data="maintenanceEdit()">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Edit Data Maintenance</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Perbarui data perawatan atau perbaikan barang laboratorium</p>
        </div>
        <a href="{{ route('maintenance.show', $maintenance->id) }}" class="bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 font-bold px-3.5 py-2 rounded-xl text-xs transition shadow-sm">
            Kembali
        </a>
    </div>

    <div class="bg-white shadow-sm border border-slate-200/60 rounded-2xl sm:rounded-3xl p-5 sm:p-8">
        <form id="editMaintenanceForm" action="{{ route('maintenance.update', $maintenance->id) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-y-4 sm:grid-cols-2 sm:gap-x-6">
                
                {{-- 0. Dropdown: Pilihan Laboratorium --}}
                @if(!empty($lockedLab))
                    {{-- Koordinator Lab: Terkunci ke lab penugasan --}}
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Laboratorium <span class="text-rose-500">*</span></label>
                        <input type="hidden" name="laboratorium" value="{{ $lockedLab }}">
                        <div class="w-full bg-emerald-50/70 border border-emerald-300/80 text-emerald-950 text-xs sm:text-sm rounded-xl px-3.5 py-3 flex items-center justify-between shadow-xs">
                            <div class="flex items-center gap-2.5">
                                <span class="w-8 h-8 rounded-lg @if($lockedLab === 'Laboratorium AKL') bg-emerald-100 text-emerald-700 @elseif($lockedLab === 'Laboratorium Pemasaran') bg-amber-100 text-amber-700 @else bg-indigo-100 text-indigo-700 @endif flex items-center justify-center shadow-xs flex-shrink-0">
                                    @if($lockedLab === 'Laboratorium AKL')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                    @elseif($lockedLab === 'Laboratorium Pemasaran')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    @endif
                                </span>
                                <div>
                                    <span class="font-extrabold text-slate-900 text-sm block">{{ $lockedLab }}</span>
                                    <span class="text-[11px] text-emerald-700 font-semibold">Terkunci sesuai penugasan Koordinator Laboratorium</span>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                Koordinator
                            </span>
                        </div>
                    </div>
                @else
                    {{-- Admin / Kepala Lab: Bisa pilih seluruh lab --}}
                    <div class="sm:col-span-2 relative" @click.outside="closeIf('laboratorium')">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Laboratorium <span class="text-rose-500">*</span></label>
                        <input type="hidden" name="laboratorium" :value="selectedLaboratorium" required>

                        <button type="button" @click="toggle('laboratorium')"
                                class="w-full bg-slate-50/70 border border-slate-200 text-slate-800 text-xs sm:text-sm rounded-xl px-3.5 py-2.5 flex items-center justify-between text-left focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                            <div class="flex items-center gap-2.5">
                                <template x-if="selectedLaboratorium === 'Laboratorium TKJ'">
                                    <span class="w-6 h-6 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center shadow-xs flex-shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    </span>
                                </template>
                                <template x-if="selectedLaboratorium === 'Laboratorium AKL'">
                                    <span class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shadow-xs flex-shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                    </span>
                                </template>
                                <template x-if="selectedLaboratorium === 'Laboratorium BD'">
                                    <span class="w-6 h-6 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center shadow-xs flex-shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                    </span>
                                </template>
                                <span x-text="selectedLaboratorium" class="font-bold text-slate-800"></span>
                            </div>
                            <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 flex-shrink-0" :class="{'rotate-180': openDropdown === 'laboratorium'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="openDropdown === 'laboratorium'"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                             x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                             class="absolute left-0 right-0 z-50 mt-1 bg-white border border-slate-200 rounded-2xl shadow-2xl py-1 overflow-hidden"
                             style="display: none;">
                            
                            {{-- TKJ --}}
                            <button type="button" @click="selectedLaboratorium = 'Laboratorium TKJ'; openDropdown = null"
                                    class="w-full text-left px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 hover:bg-slate-50 transition flex items-center justify-between"
                                    :class="{'font-bold text-indigo-700 bg-indigo-50': selectedLaboratorium === 'Laboratorium TKJ'}">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-7 h-7 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    </span>
                                    <div>
                                        <div class="font-bold text-slate-900">Laboratorium TKJ</div>
                                        <div class="text-[11px] text-slate-500">Teknik Komputer & Jaringan</div>
                                    </div>
                                </div>
                                <svg x-show="selectedLaboratorium === 'Laboratorium TKJ'" class="w-4 h-4 text-indigo-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </button>

                            {{-- AKL --}}
                            <button type="button" @click="selectedLaboratorium = 'Laboratorium AKL'; openDropdown = null"
                                    class="w-full text-left px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 hover:bg-slate-50 transition flex items-center justify-between"
                                    :class="{'font-bold text-emerald-800 bg-emerald-50': selectedLaboratorium === 'Laboratorium AKL'}">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                    </span>
                                    <div>
                                        <div class="font-bold text-slate-900">Laboratorium AKL</div>
                                        <div class="text-[11px] text-slate-500">Akuntansi & Keuangan Lembaga</div>
                                    </div>
                                </div>
                                <svg x-show="selectedLaboratorium === 'Laboratorium AKL'" class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </button>

                            {{-- Pemasaran --}}
                            <button type="button" @click="selectedLaboratorium = 'Laboratorium Pemasaran'; openDropdown = null"
                                    class="w-full text-left px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 hover:bg-slate-50 transition flex items-center justify-between"
                                    :class="{'font-bold text-amber-800 bg-amber-50': selectedLaboratorium === 'Laboratorium Pemasaran'}">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-7 h-7 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                    </span>
                                    <div>
                                        <div class="font-bold text-slate-900">Laboratorium Pemasaran</div>
                                        <div class="text-[11px] text-slate-500">Bisnis Daring & Pemasaran</div>
                                    </div>
                                </div>
                                <svg x-show="selectedLaboratorium === 'Laboratorium Pemasaran'" class="w-4 h-4 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </button>
                        </div>
                    </div>
                @endif

                {{-- 1. Dropdown: Barang (Opsional) --}}
                <div class="sm:col-span-2 relative" @click.outside="closeIf('barang')">
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Barang Laboratorium <span class="text-slate-400 font-normal lowercase">(opsional)</span></label>
                        <button type="button" x-show="selectedBarangId" @click="selectBarang('')" class="text-[11px] font-bold text-rose-600 hover:underline">
                            Kosongkan (Maintenance Ruangan / Fasilitas Umum)
                        </button>
                    </div>
                    <input type="hidden" name="barang_id" :value="selectedBarangId">

                    <button type="button" @click="toggle('barang')"
                            class="w-full bg-slate-50/70 border border-slate-200 text-slate-800 text-xs sm:text-sm rounded-xl px-3.5 py-2.5 flex items-center justify-between text-left focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                        <span x-text="currentBarangLabel" :class="{'font-bold text-indigo-900': selectedBarangId, 'text-slate-500': !selectedBarangId}"></span>
                        <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 flex-shrink-0" :class="{'rotate-180': openDropdown === 'barang'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="openDropdown === 'barang'"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                         class="absolute left-0 right-0 z-50 mt-1 max-h-60 bg-white border border-slate-200 rounded-2xl shadow-2xl py-1 overflow-y-auto"
                         style="display: none;">
                        
                        {{-- Opsi Tanpa Barang (Maintenance Fasilitas / Umum) --}}
                        <button type="button" @click="selectBarang('')"
                                class="w-full text-left px-3.5 py-2.5 text-xs sm:text-sm text-slate-700 hover:bg-slate-100 border-b border-slate-100 transition flex items-center justify-between"
                                :class="{'font-bold text-indigo-600 bg-indigo-50/50': !selectedBarangId}">
                            <div class="flex items-center gap-2.5">
                                <span class="w-7 h-7 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                </span>
                                <div>
                                    <div class="font-bold text-slate-800">Tanpa Barang Khusus (Maintenance Fasilitas / Ruangan Lab)</div>
                                    <div class="text-[11px] text-slate-400">Pemeliharaan AC, instalasi kabel, kelistrikan, meja/kursi, kebersihan dsb.</div>
                                </div>
                            </div>
                            <svg x-show="!selectedBarangId" class="w-4 h-4 text-indigo-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </button>

                        <template x-for="b in barangsList" :key="b.id">
                            <button type="button" @click="selectBarang(b.id)"
                                    class="w-full text-left px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 hover:bg-slate-50 transition flex items-center justify-between"
                                    :class="{'font-bold text-indigo-700 bg-indigo-50': selectedBarangId == b.id}">
                                <div>
                                    <div x-text="b.nama" class="font-bold text-slate-900"></div>
                                    <div class="text-[11px] text-slate-500 font-mono" x-text="`${b.kode} • Kondisi: ${b.kondisi}`"></div>
                                </div>
                                <svg x-show="selectedBarangId == b.id" class="w-4 h-4 text-indigo-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- 2. Multi-Select Dropdown: Target Unit Barang --}}
                <div class="sm:col-span-2 relative" x-show="currentBarang && currentBarang.jumlah > 1" @click.outside="closeIf('unit')">
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Target Unit Barang</label>
                        <span class="text-[10px] text-indigo-600 font-semibold" x-text="`${selectedUnits.length} unit terpilih`"></span>
                    </div>
                    <input type="hidden" name="unit_index" :value="selectedUnitsString">

                    <button type="button" @click="toggle('unit')"
                            class="w-full bg-slate-50/70 border border-slate-200 text-slate-800 text-xs sm:text-sm rounded-xl px-3.5 py-2.5 flex items-center justify-between text-left focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                        <span x-text="currentUnitLabel" :class="{'font-bold text-indigo-700': selectedUnits.length > 0}"></span>
                        <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 flex-shrink-0" :class="{'rotate-180': openDropdown === 'unit'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="openDropdown === 'unit'"
                         @click.stop
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                         class="absolute left-0 right-0 z-50 mt-1 max-h-64 bg-white border border-slate-200 rounded-2xl shadow-2xl p-2.5 space-y-1"
                         style="display: none;">
                        
                        <div class="flex items-center justify-between px-2 py-1.5 border-b border-slate-100 mb-1">
                            <span class="text-[11px] font-bold text-slate-400">Centang unit yang diperbaiki:</span>
                            <button type="button" @click.stop="selectedUnits = []" class="text-[10px] font-bold text-rose-600 hover:underline">Reset</button>
                        </div>

                        <div class="max-h-48 overflow-y-auto space-y-1 pr-1">
                            <template x-for="u in unitsNeedingMaintenance" :key="u.index">
                                <label class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs sm:text-sm hover:bg-indigo-50 cursor-pointer transition select-none"
                                       :class="{'bg-indigo-50/80 font-bold text-indigo-900': selectedUnits.includes(String(u.index))}">
                                    <input type="checkbox" :value="String(u.index)" x-model="selectedUnits" @click.stop
                                           class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                                    <div class="flex items-center gap-2 flex-1">
                                        <span class="w-2 h-2 rounded-full flex-shrink-0"
                                              :class="{
                                                'bg-yellow-500': u.kondisi === 'Perawatan',
                                                'bg-orange-500': u.kondisi === 'Perbaikan',
                                                'bg-red-500': u.kondisi === 'Rusak Berat',
                                                'bg-slate-500': u.kondisi === 'Hilang',
                                                'bg-emerald-500': u.kondisi === 'Baik'
                                              }"></span>
                                        <span x-text="`Unit ${u.index} (${currentBarang.kode}-${u.index}) • Kondisi: ${u.kondisi}`"></span>
                                    </div>
                                </label>
                            </template>
                        </div>

                        <div x-show="unitsNeedingMaintenance.length === 0" class="px-3 py-3 text-center text-xs text-slate-400">
                            Semua unit saat ini dalam kondisi Baik.
                        </div>
                    </div>
                </div>

                {{-- Nama Teknisi (Wajib Diisi) --}}
                <div class="sm:col-span-2">
                    <label for="teknisi" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Teknisi <span class="text-rose-500">*</span></label>
                    <input type="text" name="teknisi" id="teknisi" x-model="formFields.teknisi" required class="bg-slate-50/70 border border-slate-200 text-slate-800 text-xs sm:text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none block w-full px-3.5 py-2.5" placeholder="Contoh: PT. Optik Solusindo / Teknisi Lab">
                </div>

                {{-- Tanggal Maintenance --}}
                <div>
                    <label for="tanggal_maintenance" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tanggal Maintenance <span class="text-rose-500">*</span></label>
                    <input type="date" name="tanggal_maintenance" id="tanggal_maintenance" x-model="formFields.tanggal_maintenance" required class="bg-slate-50/70 border border-slate-200 text-slate-800 text-xs sm:text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none block w-full px-3.5 py-2.5">
                </div>

                {{-- 3. Dropdown: Jenis --}}
                <div class="relative" @click.outside="closeIf('jenis')">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Jenis Maintenance <span class="text-rose-500">*</span></label>
                    <input type="hidden" name="jenis" :value="selectedJenis" required>

                    <button type="button" @click="toggle('jenis')"
                            class="w-full bg-slate-50/70 border border-slate-200 text-slate-800 text-xs sm:text-sm rounded-xl px-3.5 py-2.5 flex items-center justify-between text-left focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                        <span x-text="currentJenisLabel" class="font-bold text-indigo-700">{{ old('jenis', $maintenance->jenis) }}</span>
                        <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 flex-shrink-0" :class="{'rotate-180': openDropdown === 'jenis'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="openDropdown === 'jenis'"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                         class="absolute left-0 right-0 z-50 mt-1 bg-white border border-slate-200 rounded-2xl shadow-2xl py-1 overflow-hidden"
                         style="display: none;">
                        @foreach(['Preventif' => 'Preventif (Perawatan Rutin)', 'Korektif' => 'Korektif (Perbaikan)', 'Penggantian' => 'Penggantian Suku Cadang'] as $val => $lbl)
                            <button type="button" @click="selectedJenis = '{{ $val }}'; openDropdown = null"
                                    class="w-full text-left px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 hover:bg-slate-50 transition flex items-center justify-between"
                                    :class="{'font-bold text-indigo-700 bg-indigo-50': selectedJenis === '{{ $val }}'}">
                                <span>{{ $lbl }}</span>
                                <svg x-show="selectedJenis === '{{ $val }}'" class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- 4. Dropdown: Status --}}
                <div class="sm:col-span-2 relative" @click.outside="closeIf('status')">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Status <span class="text-rose-500">*</span></label>
                    <input type="hidden" name="status" :value="selectedStatus" required>

                    <button type="button" @click="toggle('status')"
                            class="w-full bg-slate-50/70 border border-slate-200 text-slate-800 text-xs sm:text-sm rounded-xl px-3.5 py-2.5 flex items-center justify-between text-left focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                        <span x-text="currentStatusLabel" class="font-bold text-indigo-700">{{ old('status', $maintenance->status) }}</span>
                        <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 flex-shrink-0" :class="{'rotate-180': openDropdown === 'status'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="openDropdown === 'status'"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                         class="absolute left-0 right-0 z-50 mt-1 bg-white border border-slate-200 rounded-2xl shadow-2xl py-1 overflow-hidden"
                         style="display: none;">
                        @foreach(['Selesai' => 'Selesai (Otomatis perbarui kondisi menjadi Baik)', 'Proses' => 'Sedang Proses', 'Pending' => 'Pending (Menunggu)'] as $val => $lbl)
                            <button type="button" @click="selectedStatus = '{{ $val }}'; openDropdown = null"
                                    class="w-full text-left px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 hover:bg-slate-50 transition flex items-center justify-between"
                                    :class="{'font-bold text-indigo-700 bg-indigo-50': selectedStatus === '{{ $val }}'}">
                                <span>{{ $lbl }}</span>
                                <svg x-show="selectedStatus === '{{ $val }}'" class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </button>
                        @endforeach
                    </div>
                    <p class="mt-1 text-[11px] text-slate-400">Catatan: Jika status "Selesai", kondisi unit terpilih otomatis menjadi "Baik".</p>
                </div>

                {{-- Deskripsi Kerusakan --}}
                <div class="sm:col-span-2">
                    <label for="deskripsi_kerusakan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Deskripsi Kerusakan / Perawatan <span class="text-rose-500">*</span></label>
                    <textarea id="deskripsi_kerusakan" name="deskripsi_kerusakan" rows="3" required x-model="formFields.deskripsi_kerusakan" class="bg-slate-50/70 border border-slate-200 text-slate-800 text-xs sm:text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none block w-full p-3">{{ old('deskripsi_kerusakan', $maintenance->deskripsi_kerusakan) }}</textarea>
                </div>

                {{-- Tindakan --}}
                <div class="sm:col-span-2">
                    <label for="tindakan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tindakan yang Dilakukan</label>
                    <textarea id="tindakan" name="tindakan" rows="3" x-model="formFields.tindakan" class="bg-slate-50/70 border border-slate-200 text-slate-800 text-xs sm:text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none block w-full p-3">{{ old('tindakan', $maintenance->tindakan) }}</textarea>
                </div>

                {{-- Biaya Maintenance (Otomatis Format Rp. 12.345) --}}
                <div class="sm:col-span-2">
                    <label for="biaya_display" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Biaya Maintenance (Rp)</label>
                    <input type="hidden" name="biaya" :value="formFields.rawBiaya">
                    <input type="text" 
                           id="biaya_display" 
                           x-model="formFields.displayBiaya"
                           @input="formatRupiah($event)"
                           class="bg-slate-50/70 border border-slate-200 text-slate-800 text-xs sm:text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none block w-full px-3.5 py-2.5 font-mono font-bold" 
                           placeholder="Rp. 0 atau -">
                </div>

                {{-- Catatan --}}
                <div class="sm:col-span-2">
                    <label for="catatan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Catatan Tambahan</label>
                    <textarea id="catatan" name="catatan" rows="2" x-model="formFields.catatan" class="bg-slate-50/70 border border-slate-200 text-slate-800 text-xs sm:text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none block w-full p-3">{{ old('catatan', $maintenance->catatan) }}</textarea>
                </div>
            </div>

            {{-- Submit Action Buttons --}}
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <a href="{{ route('maintenance.show', $maintenance->id) }}" class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 text-xs font-bold transition">
                    Batal
                </a>
                <button type="button" 
                        :disabled="!isDirty"
                        @click="openModal()"
                        class="inline-flex justify-center items-center gap-1.5 px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-white transition active:scale-95 shadow-md"
                        :class="isDirty ? 'bg-amber-500 hover:bg-amber-600 shadow-amber-500/20 cursor-pointer' : 'bg-slate-300 hover:bg-slate-300 shadow-none cursor-not-allowed opacity-60'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Perbarui Data
                </button>
            </div>
        </form>
    </div>

    {{-- Modal Konfirmasi Update --}}
    <template x-teleport="body">
        <div x-show="confirmModal" 
             class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-md" 
             style="display: none;"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            <div @click.away="if(!isSubmitting) confirmModal = false" 
                 class="bg-white rounded-3xl p-6 sm:p-7 w-full max-w-sm sm:max-w-md shadow-2xl border border-slate-100 text-center"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-90 translate-y-3"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-90 translate-y-3">
                
                <div class="w-14 h-14 bg-amber-100 text-amber-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-inner border border-amber-200/60">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>

                <h3 class="text-base sm:text-lg font-extrabold text-slate-800">Perbarui Data Maintenance?</h3>
                <p class="text-xs sm:text-sm text-slate-500 mt-1.5 leading-relaxed">
                    Apakah Anda yakin ingin menyimpan perubahan data riwayat maintenance ini?
                </p>

                <div class="mt-6 flex items-center justify-center gap-3">
                    <button type="button" 
                            :disabled="isSubmitting"
                            @click="confirmModal = false" 
                            class="flex-1 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 disabled:opacity-50 text-slate-700 font-semibold rounded-xl text-xs sm:text-sm transition">
                        Batal
                    </button>
                    <button type="button" 
                            :disabled="isSubmitting"
                            @click="submitUpdate()" 
                            class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-amber-500 hover:bg-amber-600 disabled:bg-amber-400 text-white font-bold rounded-xl text-xs sm:text-sm shadow-md shadow-amber-500/20 transition active:scale-95">
                        <svg x-show="isSubmitting" class="animate-spin -ml-1 mr-1 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" style="display: none;">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="isSubmitting ? 'Menyimpan...' : 'Ya, Perbarui'"></span>
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
function maintenanceEdit() {
    return {
        confirmModal: false,
        isSubmitting: false,
        openDropdown: null,
        selectedLaboratorium: '{{ old('laboratorium', $lockedLab ?: ($maintenance->laboratorium ?: 'Laboratorium TKJ')) }}',
        selectedBarangId: '{{ old('barang_id', $maintenance->barang_id) }}',
        selectedUnits: @json($initialUnits),
        selectedJenis: '{{ old('jenis', $maintenance->jenis) }}',
        selectedStatus: '{{ old('status', $maintenance->status) }}',

        formFields: {
            teknisi: @json(old('teknisi', $maintenance->teknisi)),
            tanggal_maintenance: @json(old('tanggal_maintenance', \Carbon\Carbon::parse($maintenance->tanggal_maintenance)->format('Y-m-d'))),
            deskripsi_kerusakan: @json(old('deskripsi_kerusakan', $maintenance->deskripsi_kerusakan)),
            tindakan: @json(old('tindakan', $maintenance->tindakan ?? '')),
            displayBiaya: '',
            rawBiaya: @json(old('biaya', $maintenance->biaya ? (string) intval($maintenance->biaya) : '')),
            catatan: @json(old('catatan', $maintenance->catatan ?? ''))
        },

        initialState: {},

        init() {
            this.formatInitialBiaya();
            this.initialState = {
                laboratorium: this.selectedLaboratorium,
                barang_id: String(this.selectedBarangId || ''),
                units: [...this.selectedUnits].map(String).sort().join(','),
                jenis: this.selectedJenis,
                status: this.selectedStatus,
                teknisi: String(this.formFields.teknisi || '').trim(),
                tanggal_maintenance: String(this.formFields.tanggal_maintenance || '').trim(),
                deskripsi_kerusakan: String(this.formFields.deskripsi_kerusakan || '').trim(),
                tindakan: String(this.formFields.tindakan || '').trim(),
                rawBiaya: String(this.formFields.rawBiaya || '').trim(),
                catatan: String(this.formFields.catatan || '').trim()
            };
        },

        formatInitialBiaya() {
            if (this.formFields.rawBiaya && !isNaN(this.formFields.rawBiaya) && Number(this.formFields.rawBiaya) > 0) {
                this.formFields.displayBiaya = 'Rp. ' + Number(this.formFields.rawBiaya).toLocaleString('id-ID');
            } else if (this.formFields.rawBiaya === '0') {
                this.formFields.displayBiaya = 'Rp. 0';
            } else {
                this.formFields.displayBiaya = '-';
                this.formFields.rawBiaya = '';
            }
        },

        formatRupiah(event) {
            let val = event.target.value.trim();
            if (val === '' || val === '-') {
                this.formFields.displayBiaya = '-';
                this.formFields.rawBiaya = '';
                return;
            }

            let numberString = val.replace(/[^0-9]/g, '');
            if (numberString === '') {
                this.formFields.displayBiaya = '-';
                this.formFields.rawBiaya = '';
                return;
            }

            let formatted = Number(numberString).toLocaleString('id-ID');
            this.formFields.displayBiaya = 'Rp. ' + formatted;
            this.formFields.rawBiaya = numberString;
        },

        get isDirty() {
            if (!this.initialState.laboratorium) return false;
            const currentUnits = [...this.selectedUnits].map(String).sort().join(',');
            return this.selectedLaboratorium !== this.initialState.laboratorium ||
                   String(this.selectedBarangId || '') !== this.initialState.barang_id ||
                   currentUnits !== this.initialState.units ||
                   this.selectedJenis !== this.initialState.jenis ||
                   this.selectedStatus !== this.initialState.status ||
                   String(this.formFields.teknisi || '').trim() !== this.initialState.teknisi ||
                   String(this.formFields.tanggal_maintenance || '').trim() !== this.initialState.tanggal_maintenance ||
                   String(this.formFields.deskripsi_kerusakan || '').trim() !== this.initialState.deskripsi_kerusakan ||
                   String(this.formFields.tindakan || '').trim() !== this.initialState.tindakan ||
                   String(this.formFields.rawBiaya || '').trim() !== this.initialState.rawBiaya ||
                   String(this.formFields.catatan || '').trim() !== this.initialState.catatan;
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

        get unitsNeedingMaintenance() {
            if (!this.currentBarang || this.currentBarang.jumlah <= 1) return [];
            
            const list = [];
            for (let i = 1; i <= this.currentBarang.jumlah; i++) {
                const k = (this.currentBarang.kondisi_per_unit && this.currentBarang.kondisi_per_unit[i]) || this.currentBarang.kondisi;
                if (k !== 'Baik' || this.selectedUnits.map(String).includes(String(i))) {
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
            if (!this.isDirty) return;
            const form = document.getElementById('editMaintenanceForm');
            if (form && !form.checkValidity()) {
                form.reportValidity();
                return;
            }
            this.isSubmitting = false;
            this.confirmModal = true;
        },

        submitUpdate() {
            this.isSubmitting = true;
            setTimeout(() => {
                const form = document.getElementById('editMaintenanceForm');
                if (form) form.submit();
            }, 300);
        }
    };
}
</script>
@endsection