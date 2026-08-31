@extends('layouts.app')

@section('title', 'Manajemen Pengguna')
@section('page_title', 'Manajemen Pengguna & Hak Akses')
@section('page_subtitle', 'Kelola data pengguna, guru, siswa, serta hak akses sistem peminjaman lab')

@section('content')

@php
    $daftarJabatanKelas = [
        'Jabatan Pengelola / Guru' => [
            'Kepala Lab TKJ',
            'Guru Produktif TKJ',
            'Teknisi Lab TKJ',
            'Staf / Karyawan',
        ],
        'Kelas Siswa TKJ' => [
            'X TKJ 1',
            'X TKJ 2',
            'XI TKJ 1',
            'XI TKJ 2',
            'XII TKJ 1',
            'XII TKJ 2',
        ]
    ];

    $laboratoriumList = $laboratoriumList ?? [
        'Laboratorium TKJ',
        'Laboratorium AKL',
        'Laboratorium Pemasaran',
    ];
@endphp

{{-- Container Utama dengan Alpine.js AJAX State --}}
<div x-data="penggunaIndex()">

    {{-- Header, Filter & Add Button --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4 mb-6">
        <form id="filterForm" @submit.prevent="fetchData()" class="flex flex-col sm:flex-row sm:flex-wrap items-stretch sm:items-center gap-2 flex-1">
            <div class="relative w-full sm:w-80">
                <input type="text" 
                       name="search" 
                       x-model="search"
                       @input.debounce.400ms="fetchData()"
                       placeholder="Cari nama, email, NIS, NIP, kelas..."
                       class="bg-white border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none w-full shadow-sm">
            </div>

            <div class="flex items-center gap-2">
                {{-- Animated Dropdown: Filter Role --}}
                <div class="relative flex-1 sm:flex-none w-full sm:w-52" @click.outside="openRoleFilter = false">
                    <button type="button" @click="openRoleFilter = !openRoleFilter"
                            class="w-full flex items-center justify-between bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-sm font-medium shadow-sm transition focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            :class="{'border-indigo-500 ring-2 ring-indigo-500/20': openRoleFilter}">
                        <span class="truncate text-slate-700" x-text="getRoleLabel()"></span>
                        <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{'rotate-180 text-indigo-600': openRoleFilter}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="openRoleFilter"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                         class="absolute z-30 w-full mt-1.5 bg-white rounded-xl shadow-xl border border-slate-200/80 py-1 overflow-hidden"
                         style="display: none;">
                        <button type="button" @click="selectRole('')"
                                class="w-full text-left px-3.5 py-2 text-xs font-semibold flex items-center justify-between transition hover:bg-indigo-50 hover:text-indigo-600"
                                :class="{'bg-indigo-50/70 text-indigo-600': role === '', 'text-slate-700': role !== ''}">
                            <span>Semua Peran (Role)</span>
                        </button>
                        @foreach(['admin' => 'Admin', 'kepala_lab' => 'Kepala Laboratorium', 'koordinator_lab' => 'Koordinator Lab', 'guru' => 'Guru', 'siswa' => 'Siswa'] as $rVal => $rLabel)
                            <button type="button" @click="selectRole('{{ $rVal }}')"
                                    class="w-full text-left px-3.5 py-2 text-xs font-semibold flex items-center justify-between transition hover:bg-indigo-50 hover:text-indigo-600"
                                    :class="{'bg-indigo-50/70 text-indigo-600': role === '{{ $rVal }}', 'text-slate-700': role !== '{{ $rVal }}'}">
                                <span>{{ $rLabel }}</span>
                                <svg x-show="role === '{{ $rVal }}'" class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            </button>
                        @endforeach
                    </div>
                </div>

                <div x-show="search || role">
                    <button type="button" @click="resetFilters()" class="bg-slate-100 hover:bg-slate-200 text-slate-600 font-medium px-3.5 py-2.5 rounded-xl text-sm transition">
                        Reset
                    </button>
                </div>
            </div>
        </form>

        <div x-data="{ openModal: false, isSubmittingStore: false }">
            <button @click="openModal = true" class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-5 py-2.5 rounded-xl text-sm shadow-sm transition flex items-center justify-center gap-2 active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                Tambah Pengguna Baru
            </button>

            {{-- Modal Tambah Pengguna --}}
            <div x-show="openModal" class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md" style="display: none;" x-transition>
                <div @click.away="if(!isSubmittingStore) openModal = false" class="bg-white rounded-3xl p-6 sm:p-8 w-full max-w-lg shadow-2xl border border-slate-100 max-h-[92vh] overflow-y-auto">
                    <div class="flex items-center justify-between mb-5 pb-3 border-b border-slate-100">
                        <h3 class="font-bold text-base text-slate-800">Tambah Akun Pengguna</h3>
                        <button type="button" @click="openModal = false" :disabled="isSubmittingStore" class="text-slate-400 hover:text-slate-600">✕</button>
                    </div>

                    <form action="{{ route('pengguna.store') }}" method="POST" class="space-y-4 text-xs"
                          @submit="isSubmittingStore = true"
                          x-data="{ selectedRole: 'siswa', openRoleForm: false, openLabForm: false, selectedLab: '' }"
                          @click.outside.of=$el="openRoleForm = false; openLabForm = false">
                        @csrf
                        <div>
                            <label class="block font-semibold text-slate-700 uppercase tracking-wider mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" name="name" required placeholder="Contoh: Muhammad Rizky" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block font-semibold text-slate-700 uppercase tracking-wider mb-1">Alamat Email <span class="text-red-500">*</span></label>
                                <input type="email" name="email" required placeholder="rizky@sekolah.sch.id" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                            </div>
                            
                            {{-- Custom Animated Dropdown Role: Tambah --}}
                            <div class="relative" @click.outside="openRoleForm = false">
                                <label class="block font-semibold text-slate-700 uppercase tracking-wider mb-1">Peran (Role) <span class="text-red-500">*</span></label>
                                <input type="hidden" name="role" :value="selectedRole" required>

                                <button type="button" @click="openRoleForm = !openRoleForm"
                                        class="w-full flex items-center justify-between bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-sm font-medium transition focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                        :class="{'border-indigo-500 ring-2 ring-indigo-500/20 bg-white': openRoleForm}">
                                    <span class="text-slate-800" x-text="{
                                        'admin': 'Admin',
                                        'kepala_lab': 'Kepala Laboratorium',
                                        'koordinator_lab': 'Koordinator Lab',
                                        'guru': 'Guru',
                                        'siswa': 'Siswa'
                                    }[selectedRole] || selectedRole"></span>
                                    <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{'rotate-180 text-indigo-600': openRoleForm}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>

                                <div x-show="openRoleForm"
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                     x-transition:leave="transition ease-in duration-100"
                                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                     x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                                     class="absolute z-30 w-full mt-1.5 bg-white rounded-xl shadow-xl border border-slate-200/80 py-1 overflow-hidden"
                                     style="display: none;">
                                    @foreach(['siswa' => 'Siswa', 'guru' => 'Guru', 'koordinator_lab' => 'Koordinator Laboratorium', 'kepala_lab' => 'Kepala Laboratorium', 'admin' => 'Admin'] as $val => $label)
                                        <button type="button" @click="selectedRole = '{{ $val }}'; openRoleForm = false"
                                                class="w-full text-left px-3.5 py-2 text-xs font-semibold flex items-center justify-between transition hover:bg-indigo-50 hover:text-indigo-600"
                                                :class="{'bg-indigo-50/70 text-indigo-600': selectedRole === '{{ $val }}', 'text-slate-700': selectedRole !== '{{ $val }}'}">
                                            <span>{{ $label }}</span>
                                            <svg x-show="selectedRole === '{{ $val }}'" class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- Field Lab Penugasan: Tambah (Soft Blue Theme) --}}
                        <div x-show="selectedRole === 'kepala_lab' || selectedRole === 'koordinator_lab'"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-2"
                             style="display:none;">
                            <div class="relative bg-indigo-50/50 rounded-2xl p-3.5 border border-indigo-200/70" @click.outside="openLabForm = false">
                                <label class="flex items-center gap-1.5 font-bold text-indigo-900 uppercase tracking-wider mb-2 text-[10.5px]">
                                    <svg class="w-4 h-4 text-indigo-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    <span>Laboratorium Penugasan <span class="text-rose-500">*</span></span>
                                </label>
                                <input type="hidden" name="laboratorium_penugasan" :value="selectedLab">
                                
                                <button type="button" @click="openLabForm = !openLabForm"
                                        class="w-full flex items-center justify-between bg-white border border-indigo-200 rounded-xl p-2.5 text-sm font-medium transition focus:outline-none focus:ring-2 focus:ring-indigo-500 shadow-xs"
                                        :class="{'border-indigo-500 ring-2 ring-indigo-500/20': openLabForm}">
                                    <span :class="selectedLab ? 'text-slate-800 font-bold' : 'text-slate-400'" x-text="selectedLab || 'Pilih Laboratorium'"></span>
                                    <svg class="w-4 h-4 text-indigo-400 transition-transform duration-200" :class="{'rotate-180': openLabForm}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>

                                <div x-show="openLabForm"
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                     x-transition:leave="transition ease-in duration-100"
                                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                     x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                                     class="absolute z-40 w-full left-0 mt-1.5 bg-white rounded-xl shadow-xl border border-slate-200/80 py-1 overflow-hidden"
                                     style="display:none;">
                                    @foreach($laboratoriumList as $labItem)
                                        <button type="button" @click="selectedLab = '{{ $labItem }}'; openLabForm = false"
                                                class="w-full text-left px-3.5 py-2 text-xs font-semibold flex items-center justify-between transition hover:bg-indigo-50 hover:text-indigo-600"
                                                :class="{'bg-indigo-50/70 text-indigo-600': selectedLab === '{{ $labItem }}', 'text-slate-700': selectedLab !== '{{ $labItem }}'}">
                                            <span>{{ $labItem }}</span>
                                            <svg x-show="selectedLab === '{{ $labItem }}'" class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block font-semibold text-slate-700 uppercase tracking-wider mb-1">NIS / NIP</label>
                                <input type="text" name="nomor_induk" placeholder="Contoh: 20241005" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                            </div>

                            {{-- Animated Dropdown: Kelas / Jabatan (Tambah) --}}
                            <div class="relative" x-data="{ openJabatan: false, selectedJabatan: 'X TKJ 1' }" @click.outside="openJabatan = false">
                                <label class="block font-semibold text-slate-700 uppercase tracking-wider mb-1">Kelas / Jabatan</label>
                                <input type="hidden" name="kelas_atau_jabatan" :value="selectedJabatan">

                                <button type="button" @click="openJabatan = !openJabatan"
                                        class="w-full flex items-center justify-between bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-sm font-medium transition focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                        :class="{'border-indigo-500 ring-2 ring-indigo-500/20 bg-white': openJabatan}">
                                    <span class="truncate text-slate-800" x-text="selectedJabatan || 'Pilih Jabatan/Kelas'"></span>
                                    <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{'rotate-180 text-indigo-600': openJabatan}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>

                                <div x-show="openJabatan"
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                     x-transition:leave="transition ease-in duration-100"
                                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                     x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                                     class="absolute z-30 w-full mt-1.5 bg-white rounded-xl shadow-xl border border-slate-200/80 py-1 max-h-56 overflow-y-auto"
                                     style="display: none;">
                                    @foreach($daftarJabatanKelas as $grup => $items)
                                        <div class="px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-400 bg-slate-50/80">{{ $grup }}</div>
                                        @foreach($items as $itemJabatan)
                                            <button type="button" @click="selectedJabatan = '{{ $itemJabatan }}'; openJabatan = false"
                                                    class="w-full text-left px-3.5 py-2 text-xs font-semibold flex items-center justify-between transition hover:bg-indigo-50 hover:text-indigo-600"
                                                    :class="{'bg-indigo-50/70 text-indigo-600': selectedJabatan === '{{ $itemJabatan }}', 'text-slate-700': selectedJabatan !== '{{ $itemJabatan }}'}">
                                                <span>{{ $itemJabatan }}</span>
                                                <svg x-show="selectedJabatan === '{{ $itemJabatan }}'" class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </button>
                                        @endforeach
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block font-semibold text-slate-700 uppercase tracking-wider mb-1">No. WhatsApp / HP</label>
                                <input type="text" name="telepon" placeholder="081234567890" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-700 uppercase tracking-wider mb-1">Password <span class="text-red-500">*</span></label>
                                <input type="password" name="password" required placeholder="Minimal 6 karakter" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-100">
                            <button type="button" @click="openModal = false" :disabled="isSubmittingStore" class="px-4 py-2 border border-slate-300 text-slate-600 rounded-xl font-medium disabled:opacity-50">Batal</button>
                            <button type="submit" :disabled="isSubmittingStore" class="inline-flex items-center gap-2 px-5 py-2 bg-indigo-600 hover:bg-indigo-700 disabled:bg-indigo-400 text-white rounded-xl font-bold transition active:scale-95">
                                <svg x-show="isSubmittingStore" class="animate-spin -ml-1 mr-1 h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24" style="display: none;">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span x-text="isSubmittingStore ? 'Menyimpan...' : 'Simpan Pengguna'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Kontainer Dinamis Data Pengguna --}}
    <div id="penggunaContainer" class="relative transition-opacity duration-200" :class="{'opacity-50 pointer-events-none': isLoading}">

        {{-- 1. Mobile Cards View --}}
        <div class="space-y-3 md:hidden">
            @forelse($users as $u)
                <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-sm" x-data="{ editModal: false, isSubmittingEdit: false }">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-2xl overflow-hidden shadow-sm border border-indigo-100 bg-gradient-to-tr from-indigo-600 to-indigo-800 text-white flex items-center justify-center flex-shrink-0 text-sm font-bold">
                                @if(!empty($u->foto))
                                    <img src="{{ asset('storage/' . $u->foto) }}" alt="{{ $u->name }}" class="w-full h-full object-cover"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <span style="display: none;" class="w-full h-full flex items-center justify-center font-bold text-sm">{{ strtoupper(substr($u->name, 0, 1)) }}</span>
                                @else
                                    <span>{{ strtoupper(substr($u->name, 0, 1)) }}</span>
                                @endif
                            </div>
                            <div>
                                <p class="font-bold text-slate-800 text-sm leading-tight">{{ $u->name }}</p>
                                <p class="text-slate-400 text-xs mt-0.5">{{ $u->email }}</p>
                            </div>
                        </div>

                        <div>
                            @if($u->role == 'admin')
                                <span class="bg-purple-100 text-purple-700 text-[10px] font-bold px-2.5 py-0.5 rounded-full uppercase">Admin</span>
                            @elseif($u->role == 'kepala_lab')
                                <span class="bg-amber-100 text-amber-700 text-[10px] font-bold px-2.5 py-0.5 rounded-full uppercase">Kepala Lab</span>
                            @elseif($u->role == 'koordinator_lab')
                                <span class="bg-emerald-100 text-emerald-700 text-[10px] font-bold px-2.5 py-0.5 rounded-full uppercase">Koordinator</span>
                            @elseif($u->role == 'guru')
                                <span class="bg-indigo-100 text-indigo-700 text-[10px] font-bold px-2.5 py-0.5 rounded-full uppercase">Guru</span>
                            @else
                                <span class="bg-slate-100 text-slate-600 text-[10px] font-bold px-2.5 py-0.5 rounded-full uppercase">Siswa</span>
                            @endif
                            @if($u->laboratorium_penugasan)
                                <div class="text-[9px] text-indigo-600 font-semibold mt-0.5">{{ str_replace('Laboratorium ', '', $u->laboratorium_penugasan) }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-3 pt-3 border-t border-slate-100 grid grid-cols-2 gap-2 text-xs">
                        <div>
                            <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider block">NIS / NIP</span>
                            <span class="font-mono text-slate-700 font-medium">{{ $u->nomor_induk ?? '—' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider block">Kelas / Jabatan</span>
                            <span class="text-slate-700 font-medium truncate block">{{ $u->kelas_atau_jabatan ?? '—' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider block">No. WhatsApp</span>
                            <span class="text-slate-700 font-medium">{{ $u->telepon ?? '—' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider block">Total Pinjam</span>
                            <span class="text-indigo-600 font-bold">{{ $u->peminjamans_count }} kali</span>
                        </div>
                    </div>

                    <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button @click="editModal = true" class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 font-semibold rounded-lg text-xs transition flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                            Edit
                        </button>
                        @if($u->id !== Auth::id())
                            <button type="button" @click="confirmDelete('{{ route('pengguna.destroy', $u->id) }}', '{{ $u->name }}')" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 font-semibold rounded-lg text-xs transition flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Hapus
                            </button>
                        @endif
                    </div>

                    {{-- Modal Edit Pengguna (Mobile) --}}
                    <div x-show="editModal" class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md text-left" style="display: none;" x-transition>
                        <div @click.away="if(!isSubmittingEdit) editModal = false" class="bg-white rounded-3xl p-6 w-full max-w-lg shadow-2xl border border-slate-100 max-h-[92vh] overflow-y-auto">
                            <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl overflow-hidden shadow-sm border border-indigo-100 bg-gradient-to-tr from-indigo-600 to-indigo-800 text-white flex items-center justify-center flex-shrink-0 text-sm font-bold">
                                        @if(!empty($u->foto))
                                            <img src="{{ asset('storage/' . $u->foto) }}" alt="{{ $u->name }}" class="w-full h-full object-cover"
                                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <span style="display: none;" class="w-full h-full flex items-center justify-center font-bold text-xs">{{ strtoupper(substr($u->name, 0, 1)) }}</span>
                                        @else
                                            <span>{{ strtoupper(substr($u->name, 0, 1)) }}</span>
                                        @endif
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-base text-slate-800">Edit Pengguna</h3>
                                        <p class="text-xs text-slate-400">{{ $u->name }}</p>
                                    </div>
                                </div>
                                <button type="button" @click="editModal = false" :disabled="isSubmittingEdit" class="text-slate-400 hover:text-slate-600">✕</button>
                            </div>

                            <form action="{{ route('pengguna.update', $u->id) }}" method="POST" class="space-y-3.5 text-xs"
                                  @submit="isSubmittingEdit = true"
                                  x-data="{ openRoleMob: false, selectedRoleMob: '{{ old('role', $u->role) }}', openLabMob: false, selectedLabMob: '{{ old('laboratorium_penugasan', $u->laboratorium_penugasan) }}' }">
                                @csrf
                                @method('PUT')
                                <div>
                                    <label class="block font-semibold text-slate-700 uppercase tracking-wider mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" value="{{ old('name', $u->name) }}" required class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block font-semibold text-slate-700 uppercase tracking-wider mb-1">Alamat Email <span class="text-red-500">*</span></label>
                                        <input type="email" name="email" value="{{ old('email', $u->email) }}" required class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                    </div>
                                    
                                    {{-- Dropdown Role Modal Edit (Mobile) --}}
                                    <div class="relative" @click.outside="openRoleMob = false">
                                        <label class="block font-semibold text-slate-700 uppercase tracking-wider mb-1">Peran (Role) <span class="text-red-500">*</span></label>
                                        <input type="hidden" name="role" :value="selectedRoleMob" required>

                                        <button type="button" @click="openRoleMob = !openRoleMob"
                                                class="w-full flex items-center justify-between bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-sm font-medium transition focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                                :class="{'border-indigo-500 ring-2 ring-indigo-500/20 bg-white': openRoleMob}">
                                            <span class="text-slate-800" x-text="{
                                                'admin': 'Admin',
                                                'kepala_lab': 'Kepala Laboratorium',
                                                'koordinator_lab': 'Koordinator Lab',
                                                'guru': 'Guru',
                                                'siswa': 'Siswa'
                                            }[selectedRoleMob] || selectedRoleMob"></span>
                                            <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{'rotate-180 text-indigo-600': openRoleMob}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </button>

                                        <div x-show="openRoleMob"
                                             x-transition:enter="transition ease-out duration-150"
                                             x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                             x-transition:leave="transition ease-in duration-100"
                                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                             x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                                             class="absolute z-30 w-full mt-1.5 bg-white rounded-xl shadow-xl border border-slate-200/80 py-1 overflow-hidden"
                                             style="display: none;">
                                            @foreach(['siswa' => 'Siswa', 'guru' => 'Guru', 'koordinator_lab' => 'Koordinator Laboratorium', 'kepala_lab' => 'Kepala Laboratorium', 'admin' => 'Admin'] as $val => $label)
                                                <button type="button" @click="selectedRoleMob = '{{ $val }}'; openRoleMob = false"
                                                        class="w-full text-left px-3.5 py-2 text-xs font-semibold flex items-center justify-between transition hover:bg-indigo-50 hover:text-indigo-600"
                                                        :class="{'bg-indigo-50/70 text-indigo-600': selectedRoleMob === '{{ $val }}', 'text-slate-700': selectedRoleMob !== '{{ $val }}'}">
                                                    <span>{{ $label }}</span>
                                                    <svg x-show="selectedRoleMob === '{{ $val }}'" class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                {{-- Lab Penugasan: Edit Mobile (Soft Blue Theme) --}}
                                <div x-show="selectedRoleMob === 'kepala_lab' || selectedRoleMob === 'koordinator_lab'"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 -translate-y-2"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                     x-transition:leave-end="opacity-0 -translate-y-2"
                                     style="display:none;">
                                    <div class="relative bg-indigo-50/50 rounded-2xl p-3.5 border border-indigo-200/70" @click.outside="openLabMob = false">
                                        <label class="flex items-center gap-1.5 font-bold text-indigo-900 uppercase tracking-wider mb-2 text-[10.5px]">
                                            <svg class="w-4 h-4 text-indigo-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                            </svg>
                                            <span>Laboratorium Penugasan <span class="text-rose-500">*</span></span>
                                        </label>
                                        <input type="hidden" name="laboratorium_penugasan" :value="selectedLabMob">
                                        
                                        <button type="button" @click="openLabMob = !openLabMob"
                                                class="w-full flex items-center justify-between bg-white border border-indigo-200 rounded-xl p-2.5 text-sm font-medium transition focus:outline-none focus:ring-2 focus:ring-indigo-500 shadow-xs"
                                                :class="{'border-indigo-500 ring-2 ring-indigo-500/20': openLabMob}">
                                            <span :class="selectedLabMob ? 'text-slate-800 font-bold' : 'text-slate-400'" x-text="selectedLabMob || 'Pilih Laboratorium'"></span>
                                            <svg class="w-4 h-4 text-indigo-400 transition-transform duration-200" :class="{'rotate-180': openLabMob}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </button>

                                        <div x-show="openLabMob"
                                             x-transition:enter="transition ease-out duration-150"
                                             x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                             x-transition:leave="transition ease-in duration-100"
                                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                             x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                                             class="absolute z-40 w-full left-0 mt-1.5 bg-white rounded-xl shadow-xl border border-slate-200/80 py-1 overflow-hidden"
                                             style="display:none;">
                                            @foreach($laboratoriumList as $labItem)
                                                <button type="button" @click="selectedLabMob = '{{ $labItem }}'; openLabMob = false"
                                                        class="w-full text-left px-3.5 py-2 text-xs font-semibold flex items-center justify-between transition hover:bg-indigo-50 hover:text-indigo-600"
                                                        :class="{'bg-indigo-50/70 text-indigo-600': selectedLabMob === '{{ $labItem }}', 'text-slate-700': selectedLabMob !== '{{ $labItem }}'}">
                                                    <span>{{ $labItem }}</span>
                                                    <svg x-show="selectedLabMob === '{{ $labItem }}'" class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block font-semibold text-slate-700 uppercase tracking-wider mb-1">NIS / NIP</label>
                                        <input type="text" name="nomor_induk" value="{{ old('nomor_induk', $u->nomor_induk) }}" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                    </div>

                                    {{-- Animated Dropdown: Kelas / Jabatan (Edit Mobile) --}}
                                    <div class="relative" x-data="{ openJabatanMob: false, selectedJabatanMob: '{{ old('kelas_atau_jabatan', $u->kelas_atau_jabatan ?? 'X TKJ 1') }}' }" @click.outside="openJabatanMob = false">
                                        <label class="block font-semibold text-slate-700 uppercase tracking-wider mb-1">Kelas / Jabatan</label>
                                        <input type="hidden" name="kelas_atau_jabatan" :value="selectedJabatanMob">

                                        <button type="button" @click="openJabatanMob = !openJabatanMob"
                                                class="w-full flex items-center justify-between bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-sm font-medium transition focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                                :class="{'border-indigo-500 ring-2 ring-indigo-500/20 bg-white': openJabatanMob}">
                                            <span class="truncate text-slate-800" x-text="selectedJabatanMob || 'Pilih Jabatan/Kelas'"></span>
                                            <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{'rotate-180 text-indigo-600': openJabatanMob}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </button>

                                        <div x-show="openJabatanMob"
                                             x-transition:enter="transition ease-out duration-150"
                                             x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                             x-transition:leave="transition ease-in duration-100"
                                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                             x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                                             class="absolute z-30 w-full mt-1.5 bg-white rounded-xl shadow-xl border border-slate-200/80 py-1 max-h-56 overflow-y-auto"
                                             style="display: none;">
                                            @foreach($daftarJabatanKelas as $grup => $items)
                                                <div class="px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-400 bg-slate-50/80">{{ $grup }}</div>
                                                @foreach($items as $itemJabatan)
                                                    <button type="button" @click="selectedJabatanMob = '{{ $itemJabatan }}'; openJabatanMob = false"
                                                            class="w-full text-left px-3.5 py-2 text-xs font-semibold flex items-center justify-between transition hover:bg-indigo-50 hover:text-indigo-600"
                                                            :class="{'bg-indigo-50/70 text-indigo-600': selectedJabatanMob === '{{ $itemJabatan }}', 'text-slate-700': selectedJabatanMob !== '{{ $itemJabatan }}'}">
                                                        <span>{{ $itemJabatan }}</span>
                                                        <svg x-show="selectedJabatanMob === '{{ $itemJabatan }}'" class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                    </button>
                                                @endforeach
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block font-semibold text-slate-700 uppercase tracking-wider mb-1">No. WhatsApp / HP</label>
                                        <input type="text" name="telepon" value="{{ old('telepon', $u->telepon) }}" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                    </div>
                                    <div>
                                        <label class="block font-semibold text-slate-700 uppercase tracking-wider mb-1">Password Baru (Opsional)</label>
                                        <input type="password" name="password" placeholder="Kosongkan jika tetap" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                    </div>
                                </div>

                                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                                    <button type="button" @click="editModal = false" :disabled="isSubmittingEdit" class="px-4 py-2 border border-slate-300 text-slate-600 rounded-xl font-medium disabled:opacity-50">Batal</button>
                                    <button type="submit" :disabled="isSubmittingEdit" class="inline-flex items-center gap-2 px-5 py-2 bg-indigo-600 hover:bg-indigo-700 disabled:bg-indigo-400 text-white rounded-xl font-bold transition active:scale-95">
                                        <svg x-show="isSubmittingEdit" class="animate-spin -ml-1 mr-1 h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24" style="display: none;">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span x-text="isSubmittingEdit ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl p-6 text-center text-slate-400 border border-slate-200/60">
                    Tidak ada data pengguna.
                </div>
            @endforelse
        </div>

        {{-- 2. Desktop Table View (Layar >= md) --}}
        <div class="hidden md:block bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 text-[11px] uppercase tracking-wider font-bold text-slate-600 bg-slate-50/90">
                            <th class="px-5 py-4 text-slate-600 font-bold">Nama & Kontak</th>
                            <th class="px-5 py-4 text-slate-600 font-bold">Nomor Induk (NIS/NIP)</th>
                            <th class="px-5 py-4 text-slate-600 font-bold">Kelas / Jabatan</th>
                            <th class="px-5 py-4 text-center text-slate-600 font-bold">Peran (Role)</th>
                            <th class="px-5 py-4 text-center text-slate-600 font-bold">Total Pinjam</th>
                            <th class="px-5 py-4 text-right text-slate-600 font-bold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @forelse($users as $u)
                            <tr class="hover:bg-slate-50/50 transition-colors" x-data="{ editModal: false, isSubmittingEditDesk: false }">
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-2xl overflow-hidden shadow-sm border border-indigo-100 bg-gradient-to-tr from-indigo-600 to-indigo-800 text-white flex items-center justify-center flex-shrink-0 text-xs font-bold">
                                            @if(!empty($u->foto))
                                                <img src="{{ asset('storage/' . $u->foto) }}" alt="{{ $u->name }}" class="w-full h-full object-cover"
                                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <span style="display: none;" class="w-full h-full flex items-center justify-center font-bold text-xs">{{ strtoupper(substr($u->name, 0, 1)) }}</span>
                                            @else
                                                <span>{{ strtoupper(substr($u->name, 0, 1)) }}</span>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-800">{{ $u->name }}</p>
                                            <p class="text-slate-400 text-[11px]">{{ $u->email }}</p>
                                            @if($u->telepon)<p class="text-slate-400 text-[10px]">WA: {{ $u->telepon }}</p>@endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 font-mono text-slate-600">{{ $u->nomor_induk ?? '—' }}</td>
                                <td class="px-5 py-3.5 text-slate-700 font-medium">{{ $u->kelas_atau_jabatan ?? '—' }}</td>
                                <td class="px-5 py-3.5 text-center">
                                    @if($u->role == 'admin')
                                        <span class="bg-purple-100 text-purple-700 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase">Admin</span>
                                    @elseif($u->role == 'kepala_lab')
                                        <span class="bg-amber-100 text-amber-700 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase">Kepala Lab</span>
                                    @elseif($u->role == 'koordinator_lab')
                                        <span class="bg-emerald-100 text-emerald-700 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase">Koordinator</span>
                                    @elseif($u->role == 'guru')
                                        <span class="bg-indigo-100 text-indigo-700 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase">Guru</span>
                                    @else
                                        <span class="bg-slate-100 text-slate-600 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase">Siswa</span>
                                    @endif
                                    @if($u->laboratorium_penugasan)
                                        <div class="text-[9px] text-indigo-600 font-semibold mt-0.5">{{ str_replace('Laboratorium ', '', $u->laboratorium_penugasan) }}</div>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-center font-bold text-slate-700">{{ $u->peminjamans_count }} kali</td>
                                <td class="px-5 py-3.5 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button @click="editModal = true" title="Edit Pengguna" class="w-8 h-8 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 flex items-center justify-center transition active:scale-95 shadow-sm border border-blue-100/80">
                                            <svg class="w-4 h-4 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                            </svg>
                                        </button>
                                        
                                        @if($u->id !== Auth::id())
                                            <button type="button" @click="confirmDelete('{{ route('pengguna.destroy', $u->id) }}', '{{ $u->name }}')" title="Hapus Pengguna" class="w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center transition active:scale-95 shadow-sm border border-rose-100/80">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>

                                    {{-- Modal Edit Pengguna (Desktop) --}}
                                    <div x-show="editModal" class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md text-left" style="display: none;" x-transition>
                                        <div @click.away="if(!isSubmittingEditDesk) editModal = false" class="bg-white rounded-3xl p-6 sm:p-8 w-full max-w-lg shadow-2xl border border-slate-100 max-h-[92vh] overflow-y-auto">
                                            <div class="flex items-center justify-between mb-5 pb-3 border-b border-slate-100">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-11 h-11 rounded-2xl overflow-hidden shadow-sm border border-indigo-100 bg-gradient-to-tr from-indigo-600 to-indigo-800 text-white flex items-center justify-center flex-shrink-0 text-sm font-bold">
                                                        @if(!empty($u->foto))
                                                            <img src="{{ asset('storage/' . $u->foto) }}" alt="{{ $u->name }}" class="w-full h-full object-cover"
                                                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                        <span style="display: none;" class="w-full h-full flex items-center justify-center font-bold text-xs">{{ strtoupper(substr($u->name, 0, 1)) }}</span>
                                                        @else
                                                            <span>{{ strtoupper(substr($u->name, 0, 1)) }}</span>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <h3 class="font-bold text-base text-slate-800">Edit Pengguna</h3>
                                                        <p class="text-xs text-slate-400">{{ $u->name }}</p>
                                                    </div>
                                                </div>
                                                <button type="button" @click="editModal = false" :disabled="isSubmittingEditDesk" class="text-slate-400 hover:text-slate-600">✕</button>
                                            </div>

                                            <form action="{{ route('pengguna.update', $u->id) }}" method="POST" class="space-y-4 text-xs"
                                                  @submit="isSubmittingEditDesk = true"
                                                  x-data="{ openRole: false, selectedRole: '{{ old('role', $u->role) }}', openLabDesk: false, selectedLabDesk: '{{ old('laboratorium_penugasan', $u->laboratorium_penugasan) }}' }">
                                                @csrf
                                                @method('PUT')
                                                <div>
                                                    <label class="block font-semibold text-slate-700 uppercase tracking-wider mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                                                    <input type="text" name="name" value="{{ old('name', $u->name) }}" required class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                                </div>

                                                <div class="grid grid-cols-2 gap-3">
                                                    <div>
                                                        <label class="block font-semibold text-slate-700 uppercase tracking-wider mb-1">Alamat Email <span class="text-red-500">*</span></label>
                                                        <input type="email" name="email" value="{{ old('email', $u->email) }}" required class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                                    </div>

                                                    {{-- Dropdown Role Modal Edit (Desktop) --}}
                                                    <div class="relative" @click.outside="openRole = false">
                                                        <label class="block font-semibold text-slate-700 uppercase tracking-wider mb-1">Peran (Role) <span class="text-red-500">*</span></label>
                                                        <input type="hidden" name="role" :value="selectedRole" required>

                                                        <button type="button" @click="openRole = !openRole"
                                                                class="w-full flex items-center justify-between bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-sm font-medium transition focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                                                :class="{'border-indigo-500 ring-2 ring-indigo-500/20 bg-white': openRole}">
                                                            <span class="text-slate-800" x-text="{
                                                                'admin': 'Admin',
                                                                'kepala_lab': 'Kepala Lab',
                                                                'koordinator_lab': 'Koordinator Lab',
                                                                'guru': 'Guru',
                                                                'siswa': 'Siswa'
                                                            }[selectedRole] || selectedRole"></span>
                                                            <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{'rotate-180 text-indigo-600': openRole}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                            </svg>
                                                        </button>

                                                        <div x-show="openRole"
                                                             x-transition:enter="transition ease-out duration-150"
                                                             x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                                                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                                             x-transition:leave="transition ease-in duration-100"
                                                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                                             x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                                                             class="absolute z-30 w-full mt-1.5 bg-white rounded-xl shadow-xl border border-slate-200/80 py-1 overflow-hidden"
                                                             style="display: none;">
                                                            @foreach(['siswa' => 'Siswa', 'guru' => 'Guru', 'koordinator_lab' => 'Koordinator Laboratorium', 'kepala_lab' => 'Kepala Laboratorium', 'admin' => 'Admin'] as $val => $label)
                                                                <button type="button" @click="selectedRole = '{{ $val }}'; openRole = false"
                                                                        class="w-full text-left px-3.5 py-2 text-xs font-semibold flex items-center justify-between transition hover:bg-indigo-50 hover:text-indigo-600"
                                                                        :class="{'bg-indigo-50/70 text-indigo-600': selectedRole === '{{ $val }}', 'text-slate-700': selectedRole !== '{{ $val }}'}">
                                                                    <span>{{ $label }}</span>
                                                                    <svg x-show="selectedRole === '{{ $val }}'" class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                                    </svg>
                                                                </button>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Lab Penugasan: Edit Desktop (Soft Blue Theme) --}}
                                                <div x-show="selectedRole === 'kepala_lab' || selectedRole === 'koordinator_lab'"
                                                     x-transition:enter="transition ease-out duration-200"
                                                     x-transition:enter-start="opacity-0 -translate-y-2"
                                                     x-transition:enter-end="opacity-100 translate-y-0"
                                                     x-transition:leave="transition ease-in duration-150"
                                                     x-transition:leave-start="opacity-100 translate-y-0"
                                                     x-transition:leave-end="opacity-0 -translate-y-2"
                                                     style="display:none;">
                                                    <div class="relative bg-indigo-50/50 rounded-2xl p-3.5 border border-indigo-200/70" @click.outside="openLabDesk = false">
                                                        <label class="flex items-center gap-1.5 font-bold text-indigo-900 uppercase tracking-wider mb-2 text-[10.5px]">
                                                            <svg class="w-4 h-4 text-indigo-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                                            </svg>
                                                            <span>Laboratorium Penugasan <span class="text-rose-500">*</span></span>
                                                        </label>
                                                        <input type="hidden" name="laboratorium_penugasan" :value="selectedLabDesk">
                                                        
                                                        <button type="button" @click="openLabDesk = !openLabDesk"
                                                                class="w-full flex items-center justify-between bg-white border border-indigo-200 rounded-xl p-2.5 text-sm font-medium transition focus:outline-none focus:ring-2 focus:ring-indigo-500 shadow-xs"
                                                                :class="{'border-indigo-500 ring-2 ring-indigo-500/20': openLabDesk}">
                                                            <span :class="selectedLabDesk ? 'text-slate-800 font-bold' : 'text-slate-400'" x-text="selectedLabDesk || 'Pilih Laboratorium'"></span>
                                                            <svg class="w-4 h-4 text-indigo-400 transition-transform duration-200" :class="{'rotate-180': openLabDesk}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                            </svg>
                                                        </button>

                                                        <div x-show="openLabDesk"
                                                             x-transition:enter="transition ease-out duration-150"
                                                             x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                                             x-transition:leave="transition ease-in duration-100"
                                                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                                             x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                                                             class="absolute z-40 w-full left-0 mt-1.5 bg-white rounded-xl shadow-xl border border-slate-200/80 py-1 overflow-hidden"
                                                             style="display:none;">
                                                            @foreach($laboratoriumList as $labItem)
                                                                <button type="button" @click="selectedLabDesk = '{{ $labItem }}'; openLabDesk = false"
                                                                        class="w-full text-left px-3.5 py-2 text-xs font-semibold flex items-center justify-between transition hover:bg-indigo-50 hover:text-indigo-600"
                                                                        :class="{'bg-indigo-50/70 text-indigo-600': selectedLabDesk === '{{ $labItem }}', 'text-slate-700': selectedLabDesk !== '{{ $labItem }}'}">
                                                                    <span>{{ $labItem }}</span>
                                                                    <svg x-show="selectedLabDesk === '{{ $labItem }}'" class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                                    </svg>
                                                                </button>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="grid grid-cols-2 gap-3">
                                                    <div>
                                                        <label class="block font-semibold text-slate-700 uppercase tracking-wider mb-1">NIS / NIP</label>
                                                        <input type="text" name="nomor_induk" value="{{ old('nomor_induk', $u->nomor_induk) }}" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                                    </div>

                                                    {{-- Animated Dropdown: Kelas / Jabatan (Edit Desktop) --}}
                                                    <div class="relative" x-data="{ openJabatanDesk: false, selectedJabatanDesk: '{{ old('kelas_atau_jabatan', $u->kelas_atau_jabatan ?? 'X TKJ 1') }}' }" @click.outside="openJabatanDesk = false">
                                                        <label class="block font-semibold text-slate-700 uppercase tracking-wider mb-1">Kelas / Jabatan</label>
                                                        <input type="hidden" name="kelas_atau_jabatan" :value="selectedJabatanDesk">

                                                        <button type="button" @click="openJabatanDesk = !openJabatanDesk"
                                                                class="w-full flex items-center justify-between bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-sm font-medium transition focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                                                :class="{'border-indigo-500 ring-2 ring-indigo-500/20 bg-white': openJabatanDesk}">
                                                            <span class="truncate text-slate-800" x-text="selectedJabatanDesk || 'Pilih Jabatan/Kelas'"></span>
                                                            <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{'rotate-180 text-indigo-600': openJabatanDesk}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                            </svg>
                                                        </button>

                                                        <div x-show="openJabatanDesk"
                                                             x-transition:enter="transition ease-out duration-150"
                                                             x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                                                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                                             x-transition:leave="transition ease-in duration-100"
                                                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                                             x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                                                             class="absolute z-30 w-full mt-1.5 bg-white rounded-xl shadow-xl border border-slate-200/80 py-1 max-h-56 overflow-y-auto"
                                                             style="display: none;">
                                                            @foreach($daftarJabatanKelas as $grup => $items)
                                                                <div class="px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-400 bg-slate-50/80">{{ $grup }}</div>
                                                                @foreach($items as $itemJabatan)
                                                                    <button type="button" @click="selectedJabatanDesk = '{{ $itemJabatan }}'; openJabatanDesk = false"
                                                                            class="w-full text-left px-3.5 py-2 text-xs font-semibold flex items-center justify-between transition hover:bg-indigo-50 hover:text-indigo-600"
                                                                            :class="{'bg-indigo-50/70 text-indigo-600': selectedJabatanDesk === '{{ $itemJabatan }}', 'text-slate-700': selectedJabatanDesk !== '{{ $itemJabatan }}'}">
                                                                        <span>{{ $itemJabatan }}</span>
                                                                        <svg x-show="selectedJabatanDesk === '{{ $itemJabatan }}'" class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                                        </svg>
                                                                    </button>
                                                                @endforeach
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="grid grid-cols-2 gap-3">
                                                    <div>
                                                        <label class="block font-semibold text-slate-700 uppercase tracking-wider mb-1">No. WhatsApp / HP</label>
                                                        <input type="text" name="telepon" value="{{ old('telepon', $u->telepon) }}" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                                    </div>
                                                    <div>
                                                        <label class="block font-semibold text-slate-700 uppercase tracking-wider mb-1">Password Baru (Opsional)</label>
                                                        <input type="password" name="password" placeholder="Kosongkan jika tetap" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                                    </div>
                                                </div>

                                                <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-100">
                                                    <button type="button" @click="editModal = false" :disabled="isSubmittingEditDesk" class="px-4 py-2 border border-slate-300 text-slate-600 rounded-xl font-medium disabled:opacity-50">Batal</button>
                                                    <button type="submit" :disabled="isSubmittingEditDesk" class="inline-flex items-center gap-2 px-5 py-2 bg-indigo-600 hover:bg-indigo-700 disabled:bg-indigo-400 text-white rounded-xl font-bold transition active:scale-95">
                                                        <svg x-show="isSubmittingEditDesk" class="animate-spin -ml-1 mr-1 h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24" style="display: none;">
                                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                        </svg>
                                                        <span x-text="isSubmittingEditDesk ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-8 text-center text-slate-400">Tidak ada data pengguna.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">
            {{ $users->withQueryString()->links() }}
        </div>
    </div>

    {{-- MODAL KONFIRMASI HAPUS TENGAH LAYAR DENGAN SPINNER --}}
    <div x-show="deleteModal" class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-slate-900/75 backdrop-blur-md" style="display: none;"
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
            
            <div class="w-14 h-14 bg-rose-100 text-rose-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-inner border border-rose-200/60">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>

            <h3 class="text-base sm:text-lg font-extrabold text-slate-800">Hapus Pengguna Ini?</h3>
            <p class="text-xs sm:text-sm text-slate-500 mt-1.5 leading-relaxed">
                Anda akan menghapus data akun <span class="font-bold text-slate-800" x-text="deleteUserName"></span>. Tindakan ini tidak dapat dibatalkan.
            </p>

            <form id="globalDeletePenggunaForm" :action="deleteActionUrl" method="POST" class="mt-6 flex items-center justify-center gap-3">
                @csrf
                @method('DELETE')
                
                <button type="button" :disabled="isDeleting" @click="deleteModal = false" class="flex-1 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 disabled:opacity-50 text-slate-700 font-semibold rounded-xl text-xs sm:text-sm transition">
                    Batal
                </button>
                <button type="button" :disabled="isDeleting" @click="submitDeleteAction()" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-rose-600 hover:bg-rose-700 disabled:bg-rose-400 text-white font-bold rounded-xl text-xs sm:text-sm shadow-md shadow-rose-600/20 transition active:scale-95">
                    <svg x-show="isDeleting" class="animate-spin -ml-1 mr-1 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" style="display: none;">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-text="isDeleting ? 'Menghapus...' : 'Ya, Hapus'"></span>
                </button>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
function penggunaIndex() {
    return {
        deleteModal: false,
        isDeleting: false,
        deleteActionUrl: '',
        deleteUserName: '',
        openRoleFilter: false,
        isLoading: false,

        search: @json(request('search', '')),
        role: @json(request('role', '')),

        confirmDelete(url, name) {
            this.deleteActionUrl = url;
            this.deleteUserName = name;
            this.isDeleting = false;
            this.deleteModal = true;
        },

        submitDeleteAction() {
            this.isDeleting = true;
            setTimeout(() => {
                const form = document.getElementById('globalDeletePenggunaForm');
                if (form) form.submit();
            }, 300);
        },

        getRoleLabel() {
            const labels = {
                'admin': 'Admin',
                'kepala_lab': 'Kepala Laboratorium',
                'koordinator_lab': 'Koordinator Lab',
                'guru': 'Guru',
                'siswa': 'Siswa'
            };
            return labels[this.role] || 'Semua Peran (Role)';
        },

        selectRole(r) {
            this.role = r;
            this.openRoleFilter = false;
            this.fetchData();
        },

        resetFilters() {
            this.search = '';
            this.role = '';
            this.fetchData();
        },

        async fetchData() {
            this.isLoading = true;

            const params = new URLSearchParams();
            if (this.search) params.append('search', this.search);
            if (this.role) params.append('role', this.role);

            const url = `{{ route('pengguna.index') }}?${params.toString()}`;
            window.history.pushState({}, '', url);

            try {
                const res = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const htmlText = await res.text();

                const parser = new DOMParser();
                const doc = parser.parseFromString(htmlText, 'text/html');
                const newContainer = doc.getElementById('penggunaContainer');

                if (newContainer) {
                    document.getElementById('penggunaContainer').innerHTML = newContainer.innerHTML;
                }
            } catch (err) {
                console.error("Gagal memuat filter pengguna:", err);
            } finally {
                this.isLoading = false;
            }
        }
    };
}
</script>
@endpush
@endsection