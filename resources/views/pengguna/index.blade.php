@extends('layouts.app')

@section('title', 'Kelola Pengguna')
@section('page_title', 'Manajemen Pengguna')
@section('page_subtitle', 'Kelola data pengguna, hak akses, kontak, dan riwayat peminjaman laboratorium')

@section('content')

@php
    $daftarJabatanKelas = [
        'Jabatan Pengelola / Guru' => [
            'Kepala Lab',
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

    $initialStatuses = [];
    foreach ($users as $userItem) {
        $initialStatuses[$userItem->id] = (bool) $userItem->isOnline();
    }
@endphp

{{-- Container Utama dengan Alpine.js AJAX State & Row Selection --}}
<div x-data="{
    initLoading: true,
    isDataReady: false,
    deleteModal: false,
    isDeleting: false,
    deleteActionUrl: '',
    deleteUserName: '',
    bulkDeleteModal: false,
    isBulkDeleting: false,
    _loadingDone: false,
    search: @js(request('search', '')),
    role: @js(request('role', '')),
    perPage: @js((int) request('per_page', 10)),
    openRoleFilter: false,
    selectedRows: [],
    selectAll: false,
    userStatuses: @js($initialStatuses),
    _statusInterval: null,

    init() {
        this.startStatusPolling();
        if (this._loadingDone) return;
        document.body.style.overflow = 'hidden';
        this.verifyAndCompleteLoading();
    },

    startStatusPolling() {
        if (this._statusInterval) clearInterval(this._statusInterval);
        this._statusInterval = setInterval(() => {
            if (document.visibilityState === 'visible') {
                this.checkOnlineStatuses();
            }
        }, 3000);
    },

    async checkOnlineStatuses() {
        const checkboxes = document.querySelectorAll('input[name=\'user_select[]\']');
        const ids = Array.from(checkboxes).map(cb => cb.value).filter(Boolean);
        if (ids.length === 0) return;

        try {
            const url = '{{ route('pengguna.online-statuses') }}?ids=' + ids.join(',');
            const res = await fetch(url, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (res.ok) {
                const data = await res.json();
                if (data && data.online_statuses) {
                    this.userStatuses = { ...this.userStatuses, ...data.online_statuses };
                }
            }
        } catch (e) {
            // Silent error on polling
        }
    },

    verifyAndCompleteLoading() {
        const startTime = Date.now();
        const minDuration = 600;

        const checkReady = () => {
            const tableBody = document.querySelector('tbody');
            const mobileCards = document.querySelector('#mobileCardsContainer');
            const images = Array.from(document.querySelectorAll('#usersDataContainer img'));
            
            const isDomMounted = (tableBody && tableBody.children.length > 0) || (mobileCards && mobileCards.children.length > 0);
            const areImagesLoaded = images.length === 0 || images.every(img => img.complete);
            const elapsedTime = Date.now() - startTime;

            if (isDomMounted && areImagesLoaded && elapsedTime >= minDuration) {
                this._loadingDone = true;
                this.isDataReady = true;
                this.initLoading = false;
                document.body.style.overflow = '';
            } else {
                setTimeout(checkReady, 60);
            }
        };

        setTimeout(() => {
            if (!this._loadingDone) {
                this._loadingDone = true;
                this.isDataReady = true;
                this.initLoading = false;
                document.body.style.overflow = '';
            }
        }, 1800);

        setTimeout(checkReady, 100);
    },

    toggleSelectAll(event) {
        const checkboxes = document.querySelectorAll('input[name=\'user_select[]\']');
        if (event.target.checked) {
            this.selectedRows = Array.from(checkboxes).map(cb => cb.value);
            this.selectAll = true;
        } else {
            this.selectedRows = [];
            this.selectAll = false;
        }
    },

    toggleRow(id) {
        const idx = this.selectedRows.indexOf(String(id));
        if (idx > -1) {
            this.selectedRows.splice(idx, 1);
        } else {
            this.selectedRows.push(String(id));
        }
        const checkboxes = document.querySelectorAll('input[name=\'user_select[]\']');
        this.selectAll = checkboxes.length > 0 && this.selectedRows.length === checkboxes.length;
    },

    confirmBulkDelete() {
        if (this.selectedRows.length === 0) return;
        this.isBulkDeleting = false;
        this.bulkDeleteModal = true;
    },

    setPerPage(val) {
        this.perPage = parseInt(val, 10) || 10;
        this.fetchData(null, false);
    },

    async fetchData(customUrl = null, showLoading = false) {
        if (showLoading) {
            this.initLoading = true;
            this._loadingDone = false;
            document.body.style.overflow = 'hidden';
        }

        let url = customUrl;
        if (!url) {
            const params = new URLSearchParams();
            if (this.search) params.append('search', this.search);
            if (this.role) params.append('role', this.role);
            if (this.perPage) params.append('per_page', this.perPage);
            url = '{{ route('pengguna.index') }}' + (params.toString() ? '?' + params.toString() : '');
        }
        window.history.pushState({}, '', url);

        try {
            const res = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const html = await res.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newContainer = doc.getElementById('usersDataContainer');

            if (newContainer) {
                const currentContainer = document.getElementById('usersDataContainer');
                if (currentContainer) {
                    currentContainer.innerHTML = newContainer.innerHTML;
                    this.selectedRows = [];
                    this.selectAll = false;
                    if (window.Alpine) {
                        window.Alpine.initTree(currentContainer);
                    }
                    this.checkOnlineStatuses();
                }
            }
        } catch (err) {
            console.error('Gagal memuat data pengguna secara live:', err);
        } finally {
            if (showLoading) {
                this.verifyAndCompleteLoading();
            }
        }
    },

    selectRole(r) {
        this.role = r;
        this.openRoleFilter = false;
        this.fetchData(null, false);
    },

    resetFilters() {
        this.search = '';
        this.role = '';
        this.perPage = 10;
        this.openRoleFilter = false;
        this.fetchData(null, false);
    },

    confirmDelete(url, name) {
        this.deleteActionUrl = url;
        this.deleteUserName = name;
        this.isDeleting = false;
        this.deleteModal = true;
    }
}" @open-delete-user.window="confirmDelete($event.detail.url, $event.detail.name)">

    {{-- Screen Loading Memuat Data Satu Halaman --}}
    <div x-show="initLoading"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 lg:left-64 z-[9999] flex items-center justify-center bg-slate-50/90 dark:bg-slate-950/90 backdrop-blur-md"
         style="display: none;">
        <div class="inline-flex items-center gap-3.5 px-6 py-4 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 rounded-2xl shadow-xl border border-slate-200/90 dark:border-slate-800">
            <svg class="animate-spin h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <div class="text-left">
                <div class="text-xs sm:text-sm font-extrabold tracking-wide text-slate-900 dark:text-white">Memuat Data Pengguna</div>
                <div class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">Mohon tunggu sebentar...</div>
            </div>
        </div>
    </div>

    {{-- PAGE HEADER (Gentelella v4 Style) --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Data Master</div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">Kelola Pengguna</h1>
        </div>
        
        <div class="flex flex-wrap items-center gap-2.5" x-data="{ openModal: false, isSubmittingStore: false }">
            
            {{-- Tombol Hapus Massal --}}
            <button type="button" 
                    x-show="selectedRows.length > 0" 
                    @click="confirmBulkDelete()" 
                    class="h-9 px-3.5 inline-flex items-center gap-1.5 rounded-xl border border-rose-300 dark:border-rose-700 bg-white dark:bg-slate-900 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 text-xs font-semibold shadow-2xs transition-colors" 
                    style="display: none;">
                <svg class="w-3.5 h-3.5 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                <span>Hapus (<span x-text="selectedRows.length"></span>)</span>
            </button>

            {{-- Tombol Batal Pilihan --}}
            <button type="button" 
                    x-show="selectedRows.length > 0" 
                    @click="selectedRows = []; selectAll = false" 
                    class="h-9 px-3.5 inline-flex items-center gap-1.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 text-xs font-semibold shadow-2xs transition-colors active:scale-95" 
                    style="display: none;">
                <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                <span>Batal</span>
            </button>

            {{-- Tombol Segarkan --}}
            <button type="button" @click="fetchData(null, true)" class="h-9 px-3.5 inline-flex items-center gap-1.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 shadow-xs transition active:scale-95">
                <svg class="w-3.5 h-3.5 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <span>Segarkan</span>
            </button>

            {{-- Tombol Tambah Pengguna --}}
            <button type="button" @click="openModal = true" class="h-9 px-4 inline-flex items-center gap-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-sm transition active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah Pengguna</span>
            </button>

            {{-- Modal Tambah Pengguna --}}
            <div x-show="openModal" class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md" style="display: none;" x-transition>
                <div @click.away="if(!isSubmittingStore) openModal = false" class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 w-full max-w-lg shadow-2xl border border-slate-100 dark:border-slate-800 max-h-[92vh] overflow-y-auto">
                    <div class="flex items-center justify-between mb-5 pb-3 border-b border-slate-100 dark:border-slate-800">
                        <div>
                            <div class="text-[10px] font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">Formulir Akun</div>
                            <h3 class="font-bold text-base text-slate-800 dark:text-white">Tambah Pengguna Baru</h3>
                        </div>
                        <button type="button" @click="openModal = false" :disabled="isSubmittingStore" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form action="{{ route('pengguna.store') }}" method="POST" class="space-y-4 text-xs"
                          @submit="isSubmittingStore = true"
                          x-data="{ selectedRole: 'siswa', openRoleForm: false, openLabForm: false, selectedLab: '' }"
                          @click.outside.of=$el="openRoleForm = false; openLabForm = false">
                        @csrf
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                            <input type="text" name="name" required placeholder="Contoh: Muhammad Rizky" class="w-full bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 rounded-xl p-2.5 text-xs sm:text-sm text-slate-800 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-indigo-500 focus:outline-none placeholder-slate-400 dark:placeholder-slate-500">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Alamat Email <span class="text-rose-500">*</span></label>
                                <input type="email" name="email" required placeholder="rizky@sekolah.sch.id" class="w-full bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 rounded-xl p-2.5 text-xs sm:text-sm text-slate-800 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-indigo-500 focus:outline-none placeholder-slate-400 dark:placeholder-slate-500">
                            </div>
                            
                            {{-- Dropdown Role: Tambah --}}
                            <div class="relative" @click.outside="openRoleForm = false">
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Peran (Role) <span class="text-rose-500">*</span></label>
                                <input type="hidden" name="role" :value="selectedRole" required>

                                <button type="button" @click="openRoleForm = !openRoleForm"
                                        class="w-full flex items-center justify-between bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 rounded-xl p-2.5 text-xs sm:text-sm font-medium transition focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                        :class="{'border-indigo-500 ring-2 ring-indigo-500/20 bg-white dark:bg-slate-800': openRoleForm}">
                                    <span class="text-slate-800 dark:text-slate-200" x-text="{
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
                                     class="absolute z-30 w-full mt-1.5 bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-200/80 dark:border-slate-700 py-1 overflow-hidden"
                                     style="display: none;">
                                    @foreach(['siswa' => 'Siswa', 'guru' => 'Guru', 'koordinator_lab' => 'Koordinator Laboratorium', 'kepala_lab' => 'Kepala Laboratorium', 'admin' => 'Admin'] as $val => $label)
                                        <button type="button" @click="selectedRole = '{{ $val }}'; openRoleForm = false"
                                                class="w-full text-left px-3.5 py-2 text-xs font-semibold flex items-center justify-between transition hover:bg-indigo-50 dark:hover:bg-indigo-950/50 hover:text-indigo-600 dark:hover:text-indigo-400"
                                                :class="{'bg-indigo-50/70 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400': selectedRole === '{{ $val }}', 'text-slate-700 dark:text-slate-300': selectedRole !== '{{ $val }}'}">
                                            <span>{{ $label }}</span>
                                            <svg x-show="selectedRole === '{{ $val }}'" class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- Field Lab Penugasan: Tambah --}}
                        <div x-show="selectedRole === 'koordinator_lab'"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-2"
                             style="display:none;">
                            <div class="relative bg-indigo-50/50 dark:bg-indigo-950/30 rounded-2xl p-3.5 border border-indigo-200/70 dark:border-indigo-800/60" @click.outside="openLabForm = false">
                                <label class="flex items-center gap-1.5 font-bold text-indigo-900 dark:text-indigo-300 uppercase tracking-wider mb-2 text-[10.5px]">
                                    <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    <span>Laboratorium Penugasan <span class="text-rose-500">*</span></span>
                                </label>
                                <input type="hidden" name="laboratorium_penugasan" :value="selectedLab">
                                
                                <button type="button" @click="openLabForm = !openLabForm"
                                        class="w-full flex items-center justify-between bg-white dark:bg-slate-800 border border-indigo-200 dark:border-indigo-800/80 rounded-xl p-2.5 text-xs sm:text-sm font-medium transition focus:outline-none focus:ring-2 focus:ring-indigo-500 shadow-xs"
                                        :class="{'border-indigo-500 ring-2 ring-indigo-500/20': openLabForm}">
                                    <span :class="selectedLab ? 'text-slate-800 dark:text-white font-bold' : 'text-slate-400 dark:text-slate-500'" x-text="selectedLab || 'Pilih Laboratorium'"></span>
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
                                     class="absolute z-40 w-full left-0 mt-1.5 bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-200/80 dark:border-slate-700 py-1 overflow-hidden"
                                     style="display:none;">
                                    @foreach($laboratoriumList as $labItem)
                                        <button type="button" @click="selectedLab = '{{ $labItem }}'; openLabForm = false"
                                                class="w-full text-left px-3.5 py-2 text-xs font-semibold flex items-center justify-between transition hover:bg-indigo-50 dark:hover:bg-indigo-950/50 hover:text-indigo-600 dark:hover:text-indigo-400"
                                                :class="{'bg-indigo-50/70 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400': selectedLab === '{{ $labItem }}', 'text-slate-700 dark:text-slate-300': selectedLab !== '{{ $labItem }}'}">
                                            <span>{{ $labItem }}</span>
                                            <svg x-show="selectedLab === '{{ $labItem }}'" class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">NIS / NIP</label>
                                <input type="text" name="nomor_induk" placeholder="Contoh: 20241005" class="w-full bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 rounded-xl p-2.5 text-xs sm:text-sm text-slate-800 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-indigo-500 focus:outline-none placeholder-slate-400 dark:placeholder-slate-500">
                            </div>

                            {{-- Dropdown: Kelas / Jabatan (Tambah) --}}
                            <div class="relative" x-data="{ openJabatan: false, selectedJabatan: 'Guru Produktif TKJ' }" x-show="selectedRole === 'siswa' || selectedRole === 'guru' || selectedRole === 'kepala_lab'" @click.outside="openJabatan = false">
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                                    <span x-text="selectedRole === 'siswa' ? 'Kelas' : 'Jabatan / Posisi Guru'"></span>
                                    <span x-show="selectedRole === 'siswa'" class="text-rose-500">*</span>
                                </label>
                                <input type="hidden" name="kelas_atau_jabatan" :value="selectedJabatan">

                                <button type="button" @click="openJabatan = !openJabatan"
                                        class="w-full flex items-center justify-between bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 rounded-xl p-2.5 text-xs sm:text-sm font-medium transition focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                        :class="{'border-indigo-500 ring-2 ring-indigo-500/20 bg-white dark:bg-slate-800': openJabatan}">
                                    <span class="truncate text-slate-800 dark:text-slate-200" x-text="selectedJabatan || 'Pilih Jabatan/Kelas'"></span>
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
                                     class="absolute z-30 w-full mt-1.5 bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-200/80 dark:border-slate-700 py-1 max-h-56 overflow-y-auto"
                                     style="display: none;">
                                    @foreach($daftarJabatanKelas as $grup => $items)
                                        <div class="px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 bg-slate-50/80 dark:bg-slate-800/80">{{ $grup }}</div>
                                        @foreach($items as $itemJabatan)
                                            <button type="button" @click="selectedJabatan = '{{ $itemJabatan }}'; openJabatan = false"
                                                    class="w-full text-left px-3.5 py-2 text-xs font-semibold flex items-center justify-between transition hover:bg-indigo-50 dark:hover:bg-indigo-950/50 hover:text-indigo-600 dark:hover:text-indigo-400"
                                                    :class="{'bg-indigo-50/70 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400': selectedJabatan === '{{ $itemJabatan }}', 'text-slate-700 dark:text-slate-300': selectedJabatan !== '{{ $itemJabatan }}'}">
                                                <span>{{ $itemJabatan }}</span>
                                                <svg x-show="selectedJabatan === '{{ $itemJabatan }}'" class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">No. WhatsApp / HP</label>
                                <input type="text" name="telepon" placeholder="081234567890" class="w-full bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 rounded-xl p-2.5 text-xs sm:text-sm text-slate-800 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-indigo-500 focus:outline-none placeholder-slate-400 dark:placeholder-slate-500">
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Password <span class="text-rose-500">*</span></label>
                                <input type="password" name="password" required placeholder="Minimal 6 karakter" class="w-full bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 rounded-xl p-2.5 text-xs sm:text-sm text-slate-800 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-indigo-500 focus:outline-none placeholder-slate-400 dark:placeholder-slate-500">
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-100 dark:border-slate-800">
                            <button type="button" @click="openModal = false" :disabled="isSubmittingStore" class="px-4 py-2 border border-slate-300 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl font-medium disabled:opacity-50 transition">Batal</button>
                            <button type="submit" :disabled="isSubmittingStore" class="inline-flex items-center gap-2 px-5 py-2 bg-indigo-600 hover:bg-indigo-700 disabled:bg-indigo-400 text-white rounded-xl font-bold transition active:scale-95 shadow-sm">
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

    {{-- CARD UTAMA DATA PENGGUNA --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xs overflow-hidden">
        
        {{-- Card Header --}}
        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white">Semua Pengguna</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Daftar seluruh akun pengguna laboratorium, kontak, dan hak akses.</p>
            </div>
        </div>

        {{-- Toolbar: Search & Filters --}}
        <div class="p-4 bg-slate-50/50 dark:bg-slate-850/50 border-b border-slate-100 dark:border-slate-800 flex flex-col md:flex-row md:items-center justify-between gap-3">
            <form id="filterForm" @submit.prevent="fetchData()" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 flex-1">
                
                {{-- Search Input (Gentelella Style with Icon) --}}
                <div class="relative w-full sm:w-80">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" 
                           name="search" 
                           x-model="search" 
                           placeholder="Cari nama, email, NIS, NIP..." 
                           class="w-full pl-9 pr-3.5 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition shadow-2xs">
                </div>

                {{-- Filter Role Dropdown --}}
                <div class="relative w-full sm:w-56" @click.outside="openRoleFilter = false">
                    <input type="hidden" name="role" :value="role">
                    <button type="button" @click.stop="openRoleFilter = !openRoleFilter"
                            class="w-full flex items-center justify-between bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2 text-xs sm:text-sm font-semibold shadow-2xs transition hover:bg-slate-50 dark:hover:bg-slate-750 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500"
                            :class="{'border-indigo-500 ring-2 ring-indigo-500/20 bg-indigo-50/10 dark:bg-indigo-950/20': openRoleFilter}">
                        <span class="truncate text-slate-750 dark:text-slate-200" x-text="{
                            '': 'Semua Peran (Role)',
                            'admin': 'Admin',
                            'kepala_lab': 'Kepala Lab',
                            'koordinator_lab': 'Koordinator Lab',
                            'guru': 'Guru',
                            'siswa': 'Siswa'
                        }[role] || 'Semua Peran (Role)'">Semua Peran (Role)</span>
                        <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500 transition-transform duration-200 shrink-0 ml-1.5" :class="{'rotate-180 text-indigo-600': openRoleFilter}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="openRoleFilter"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1.5 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-1.5 scale-95"
                         class="absolute left-0 z-50 w-full mt-1.5 bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-200 dark:border-slate-700 py-1 overflow-hidden"
                         style="display: none;">
                        <button type="button" @click="selectRole('')"
                                class="w-full text-left px-3.5 py-2 text-xs font-semibold flex items-center justify-between transition hover:bg-slate-50 dark:hover:bg-slate-700/60 text-slate-700 dark:text-slate-200"
                                :class="{'bg-slate-100 dark:bg-slate-700/80 font-bold': role === ''}">
                            <span>Semua Peran (Role)</span>
                            <svg x-show="role === ''" class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </button>
                        @foreach(['admin' => 'Admin', 'kepala_lab' => 'Kepala Laboratorium', 'koordinator_lab' => 'Koordinator Lab', 'guru' => 'Guru', 'siswa' => 'Siswa'] as $rVal => $rLabel)
                            <button type="button" @click="selectRole('{{ $rVal }}')"
                                    class="w-full text-left px-3.5 py-2 text-xs font-semibold flex items-center justify-between transition hover:bg-slate-50 dark:hover:bg-slate-700/60 text-slate-700 dark:text-slate-200"
                                    :class="{'bg-slate-100 dark:bg-slate-700/80 font-bold': role === '{{ $rVal }}'}">
                                <span>{{ $rLabel }}</span>
                                <svg x-show="role === '{{ $rVal }}'" class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Action Filter Buttons --}}
                <div class="flex items-center gap-2">
                    <button type="submit" class="h-9 px-4 inline-flex items-center gap-1.5 rounded-xl bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 dark:hover:bg-slate-600 text-white text-xs font-bold transition active:scale-95 shadow-2xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <span>Filter</span>
                    </button>

                    <div x-show="search || role">
                        <button type="button" @click="resetFilters()" class="h-9 px-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-750 text-xs font-semibold transition">
                            Reset
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Kontainer Dinamis Data Pengguna (Live Update) --}}
        <div id="usersDataContainer" @click="const a = $event.target.closest('a'); if (a && a.href && (a.closest('nav') || a.closest('.pagination'))) { $event.preventDefault(); fetchData(a.href, false); }">
            
            {{-- 1. Mobile Cards View (md:hidden) --}}
            <div id="mobileCardsContainer" class="p-4 space-y-3 md:hidden">
                @forelse($users as $u)
                    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 shadow-2xs transition"
                         :class="{'ring-2 ring-rose-500/50 bg-rose-50/10 dark:bg-rose-950/20': selectedRows.includes('{{ $u->id }}')}"
                         x-data="{ editModal: false, isSubmittingEdit: false }">
                        
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <input type="checkbox" name="user_select[]" value="{{ $u->id }}"
                                       @change="toggleRow('{{ $u->id }}')"
                                       :checked="selectedRows.includes('{{ $u->id }}')"
                                       class="rounded border-slate-300 dark:border-slate-700 text-indigo-600 focus:ring-indigo-500 h-4 w-4">
                                
                                <div class="w-10 h-10 rounded-full overflow-hidden shrink-0 border border-slate-200 dark:border-slate-700 bg-gradient-to-tr from-slate-700 to-slate-900 text-white flex items-center justify-center text-xs font-bold shadow-2xs">
                                    @if(!empty($u->foto))
                                        <img src="{{ asset('storage/' . $u->foto) }}" alt="{{ $u->name }}" class="w-full h-full object-cover"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <span style="display: none;" class="w-full h-full flex items-center justify-center font-bold text-xs">{{ strtoupper(substr($u->name, 0, 1)) }}</span>
                                    @else
                                        <span>{{ strtoupper(substr($u->name, 0, 1)) }}</span>
                                    @endif
                                </div>
                                
                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <p class="font-bold text-slate-900 dark:text-white text-xs sm:text-sm truncate">{{ $u->name }}</p>
                                        <span x-show="userStatuses[{{ $u->id }}] ?? {{ $u->isOnline() ? 'true' : 'false' }}" class="inline-flex items-center gap-1 text-[10px] font-semibold text-emerald-600 dark:text-emerald-400 shrink-0" {!! $u->isOnline() ? '' : 'style="display:none;"' !!}>
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                            <span>Aktif</span>
                                        </span>
                                        <span x-show="!(userStatuses[{{ $u->id }}] ?? {{ $u->isOnline() ? 'true' : 'false' }})" class="inline-flex items-center gap-1 text-[10px] font-medium text-slate-400 dark:text-slate-500 shrink-0" {!! $u->isOnline() ? 'style="display:none;"' : '' !!}>
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                                            <span>Terdaftar</span>
                                        </span>
                                    </div>
                                    <p class="text-slate-400 dark:text-slate-500 text-[11px] truncate">{{ $u->email }}</p>
                                </div>
                            </div>

                            <div class="text-right shrink-0">
                                @php
                                    $isKepalaLabUser = $u->role === 'kepala_lab' || ($u->role === 'guru' && (
                                        str_contains(strtolower($u->kelas_atau_jabatan ?? ''), 'kepala lab') ||
                                        str_contains(strtolower($u->kelas_atau_jabatan ?? ''), 'kepala laboratorium')
                                    ));
                                @endphp

                                @if($isKepalaLabUser)
                                    <div class="inline-flex flex-col items-end gap-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md border text-[10px] font-bold tracking-wide uppercase bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-800 shadow-2xs">
                                            Guru
                                        </span>
                                        <span class="inline-flex items-center gap-1 text-[9.5px] font-extrabold text-indigo-700 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-950/50 border border-indigo-200/80 dark:border-indigo-800/60 px-1.5 py-0.5 rounded-md shadow-2xs">
                                            <svg class="w-2.5 h-2.5 text-indigo-600 dark:text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                                            </svg>
                                            <span>Kepala Lab</span>
                                        </span>
                                    </div>
                                @elseif($u->role === 'admin')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md border text-[10px] font-bold tracking-wide uppercase bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border-indigo-200 dark:border-indigo-800 shadow-2xs">
                                        Admin
                                    </span>
                                @elseif($u->role === 'koordinator_lab')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md border text-[10px] font-bold tracking-wide uppercase bg-cyan-50 dark:bg-cyan-950/60 text-cyan-700 dark:text-cyan-300 border-cyan-200 dark:border-cyan-800 shadow-2xs">
                                        Koordinator
                                    </span>
                                @elseif($u->role === 'guru')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md border text-[10px] font-bold tracking-wide uppercase bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-800 shadow-2xs">
                                        Guru
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md border text-[10px] font-bold tracking-wide uppercase bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800 shadow-2xs">
                                        Siswa
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-800/80 grid grid-cols-2 gap-2 text-xs">
                            <div>
                                <span class="text-slate-400 dark:text-slate-500 text-[10px] uppercase font-bold tracking-wider block">NIS / NIP</span>
                                <span class="font-mono text-slate-700 dark:text-slate-300 font-medium">{{ $u->nomor_induk ?? '—' }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 dark:text-slate-500 text-[10px] uppercase font-bold tracking-wider block">Kelas / Jabatan</span>
                                <span class="text-slate-700 dark:text-slate-300 font-medium truncate block">{{ $u->kelas_atau_jabatan ?? '—' }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 dark:text-slate-500 text-[10px] uppercase font-bold tracking-wider block">No. WhatsApp</span>
                                <span class="text-slate-700 dark:text-slate-300 font-medium">{{ $u->telepon ?? '—' }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 dark:text-slate-500 text-[10px] uppercase font-bold tracking-wider block">Total Pinjam</span>
                                <span class="text-slate-900 dark:text-white font-bold">{{ $u->peminjamans_count }} kali</span>
                            </div>
                        </div>

                        <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-end gap-2">
                            <button @click="editModal = true" class="px-3 py-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold rounded-lg text-xs transition flex items-center gap-1.5 border border-slate-200 dark:border-slate-700">
                                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                </svg>
                                <span>Edit</span>
                            </button>
                            @if($u->id !== Auth::id())
                                <button type="button" @click.stop="$dispatch('open-delete-user', { url: '{{ route('pengguna.destroy', $u->id) }}', name: '{{ addslashes($u->name) }}' })" class="px-3 py-1.5 bg-rose-50 dark:bg-rose-950/40 hover:bg-rose-100 dark:hover:bg-rose-900/50 text-rose-600 dark:text-rose-400 font-semibold rounded-lg text-xs transition flex items-center gap-1.5 border border-rose-100 dark:border-rose-800/50">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    <span>Hapus</span>
                                </button>
                            @endif
                        </div>

                        {{-- Modal Edit Pengguna (Mobile) --}}
                        <div x-show="editModal" class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md text-left" style="display: none;" x-transition>
                            <div @click.away="if(!isSubmittingEdit) editModal = false" class="bg-white dark:bg-slate-900 rounded-3xl p-6 w-full max-w-lg shadow-2xl border border-slate-100 dark:border-slate-800 max-h-[92vh] overflow-y-auto">
                                <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100 dark:border-slate-800">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full overflow-hidden shadow-2xs border border-indigo-100 dark:border-indigo-900 bg-gradient-to-tr from-indigo-600 to-indigo-800 text-white flex items-center justify-center shrink-0 text-xs font-bold">
                                            @if(!empty($u->foto))
                                                <img src="{{ asset('storage/' . $u->foto) }}" alt="{{ $u->name }}" class="w-full h-full object-cover"
                                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <span style="display: none;" class="w-full h-full flex items-center justify-center font-bold text-xs">{{ strtoupper(substr($u->name, 0, 1)) }}</span>
                                            @else
                                                <span>{{ strtoupper(substr($u->name, 0, 1)) }}</span>
                                            @endif
                                        </div>
                                        <div>
                                            <h3 class="font-bold text-base text-slate-800 dark:text-white">Edit Pengguna</h3>
                                            <p class="text-xs text-slate-400 dark:text-slate-400">{{ $u->name }}</p>
                                        </div>
                                    </div>
                                    <button type="button" @click="editModal = false" :disabled="isSubmittingEdit" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>

                                <form action="{{ route('pengguna.update', $u->id) }}" method="POST" class="space-y-3.5 text-xs"
                                      @submit="isSubmittingEdit = true"
                                      x-data="{
                                          openRoleMob: false,
                                          selectedRoleMob: '{{ old('role', $u->role) }}',
                                          openLabMob: false,
                                          selectedLabMob: '{{ old('laboratorium_penugasan', $u->laboratorium_penugasan) }}',
                                          openJabatanMob: false,
                                          selectedJabatanMob: '{{ old('kelas_atau_jabatan', $u->kelas_atau_jabatan ?? '') }}',
                                          name: @js(old('name', $u->name)),
                                          email: @js(old('email', $u->email)),
                                          nomor_induk: @js(old('nomor_induk', $u->nomor_induk ?? '')),
                                          telepon: @js(old('telepon', $u->telepon ?? '')),
                                          password: '',
                                          initial: {
                                              name: @js(old('name', $u->name)),
                                              email: @js(old('email', $u->email)),
                                              role: '{{ old('role', $u->role) }}',
                                              nomor_induk: @js(old('nomor_induk', $u->nomor_induk ?? '')),
                                              telepon: @js(old('telepon', $u->telepon ?? '')),
                                              selectedJabatanMob: '{{ old('kelas_atau_jabatan', $u->kelas_atau_jabatan ?? '') }}',
                                              selectedLabMob: '{{ old('laboratorium_penugasan', $u->laboratorium_penugasan ?? '') }}'
                                          },
                                          get isChanged() {
                                              return this.name !== this.initial.name ||
                                                     this.email !== this.initial.email ||
                                                     this.selectedRoleMob !== this.initial.role ||
                                                     this.nomor_induk !== this.initial.nomor_induk ||
                                                     this.telepon !== this.initial.telepon ||
                                                     this.selectedJabatanMob !== this.initial.selectedJabatanMob ||
                                                     this.selectedLabMob !== this.initial.selectedLabMob ||
                                                     this.password.trim().length > 0;
                                          }
                                      }">
                                    @csrf
                                    @method('PUT')
                                    <div>
                                        <label class="block font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                                        <input type="text" name="name" x-model="name" required class="w-full bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 rounded-xl p-2.5 text-xs sm:text-sm text-slate-800 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Alamat Email <span class="text-rose-500">*</span></label>
                                            <input type="email" name="email" x-model="email" required class="w-full bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 rounded-xl p-2.5 text-xs sm:text-sm text-slate-800 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                        </div>
                                        
                                        {{-- Dropdown Role Modal Edit (Mobile) --}}
                                        <div class="relative" @click.outside="openRoleMob = false">
                                            <label class="block font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Peran (Role) <span class="text-rose-500">*</span></label>
                                            <input type="hidden" name="role" :value="selectedRoleMob" required>

                                            <button type="button" @click="openRoleMob = !openRoleMob"
                                                    class="w-full flex items-center justify-between bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 rounded-xl p-2.5 text-xs sm:text-sm font-medium transition focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                                    :class="{'border-indigo-500 ring-2 ring-indigo-500/20 bg-white dark:bg-slate-800': openRoleMob}">
                                                <span class="text-slate-800 dark:text-slate-200" x-text="{
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
                                                 class="absolute z-30 w-full mt-1.5 bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-200/80 dark:border-slate-700 py-1 overflow-hidden"
                                                 style="display: none;">
                                                @foreach(['siswa' => 'Siswa', 'guru' => 'Guru', 'koordinator_lab' => 'Koordinator Laboratorium', 'kepala_lab' => 'Kepala Laboratorium', 'admin' => 'Admin'] as $val => $label)
                                                    <button type="button" @click="selectedRoleMob = '{{ $val }}'; openRoleMob = false"
                                                            class="w-full text-left px-3.5 py-2 text-xs font-semibold flex items-center justify-between transition hover:bg-indigo-50 dark:hover:bg-indigo-950/50 hover:text-indigo-600 dark:hover:text-indigo-400"
                                                            :class="{'bg-indigo-50/70 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400': selectedRoleMob === '{{ $val }}', 'text-slate-700 dark:text-slate-300': selectedRoleMob !== '{{ $val }}'}">
                                                        <span>{{ $label }}</span>
                                                        <svg x-show="selectedRoleMob === '{{ $val }}'" class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Lab Penugasan: Edit Mobile --}}
                                    <div x-show="selectedRoleMob === 'koordinator_lab'"
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0 -translate-y-2"
                                         x-transition:enter-end="opacity-100 translate-y-0"
                                         x-transition:leave="transition ease-in duration-150"
                                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                         x-transition:leave-end="opacity-0 -translate-y-2"
                                         style="display:none;">
                                        <div class="relative bg-indigo-50/50 dark:bg-indigo-950/30 rounded-2xl p-3.5 border border-indigo-200/70 dark:border-indigo-800/60" @click.outside="openLabMob = false">
                                            <label class="flex items-center gap-1.5 font-bold text-indigo-900 dark:text-indigo-300 uppercase tracking-wider mb-2 text-[10.5px]">
                                                <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                                </svg>
                                                <span>Laboratorium Penugasan <span class="text-rose-500">*</span></span>
                                            </label>
                                            <input type="hidden" name="laboratorium_penugasan" :value="selectedLabMob">
                                            
                                            <button type="button" @click="openLabMob = !openLabMob"
                                                    class="w-full flex items-center justify-between bg-white dark:bg-slate-800 border border-indigo-200 dark:border-indigo-800/80 rounded-xl p-2.5 text-xs sm:text-sm font-medium transition focus:outline-none focus:ring-2 focus:ring-indigo-500 shadow-xs"
                                                    :class="{'border-indigo-500 ring-2 ring-indigo-500/20': openLabMob}">
                                                <span :class="selectedLabMob ? 'text-slate-800 dark:text-white font-bold' : 'text-slate-400 dark:text-slate-500'" x-text="selectedLabMob || 'Pilih Laboratorium'"></span>
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
                                                 class="absolute z-40 w-full left-0 mt-1.5 bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-200/80 dark:border-slate-700 py-1 overflow-hidden"
                                                 style="display:none;">
                                                @foreach($laboratoriumList as $labItem)
                                                    <button type="button" @click="selectedLabMob = '{{ $labItem }}'; openLabMob = false"
                                                            class="w-full text-left px-3.5 py-2 text-xs font-semibold flex items-center justify-between transition hover:bg-indigo-50 dark:hover:bg-indigo-950/50 hover:text-indigo-600 dark:hover:text-indigo-400"
                                                            :class="{'bg-indigo-50/70 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400': selectedLabMob === '{{ $labItem }}', 'text-slate-700 dark:text-slate-300': selectedLabMob !== '{{ $labItem }}'}">
                                                        <span>{{ $labItem }}</span>
                                                        <svg x-show="selectedLabMob === '{{ $labItem }}'" class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">NIS / NIP</label>
                                            <input type="text" name="nomor_induk" x-model="nomor_induk" class="w-full bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 rounded-xl p-2.5 text-xs sm:text-sm text-slate-800 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                        </div>

                                        {{-- Dropdown: Kelas / Jabatan (Edit Mobile) --}}
                                        <div class="relative" x-show="selectedRoleMob === 'siswa' || selectedRoleMob === 'guru' || selectedRoleMob === 'kepala_lab'" @click.outside="openJabatanMob = false">
                                            <label class="block font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                                                <span x-text="selectedRoleMob === 'siswa' ? 'Kelas' : 'Jabatan / Posisi Guru'"></span>
                                                <span x-show="selectedRoleMob === 'siswa'" class="text-rose-500">*</span>
                                            </label>
                                            <input type="hidden" name="kelas_atau_jabatan" :value="selectedJabatanMob">

                                            <button type="button" @click="openJabatanMob = !openJabatanMob"
                                                    class="w-full flex items-center justify-between bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 rounded-xl p-2.5 text-xs sm:text-sm font-medium transition focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                                    :class="{'border-indigo-500 ring-2 ring-indigo-500/20 bg-white dark:bg-slate-800': openJabatanMob}">
                                                <span class="truncate text-slate-800 dark:text-slate-200" x-text="selectedJabatanMob || 'Pilih Jabatan/Kelas'"></span>
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
                                                 class="absolute z-30 w-full mt-1.5 bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-200/80 dark:border-slate-700 py-1 max-h-56 overflow-y-auto"
                                                 style="display: none;">
                                                @foreach($daftarJabatanKelas as $grup => $items)
                                                    <div class="px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 bg-slate-50/80 dark:bg-slate-800/80">{{ $grup }}</div>
                                                    @foreach($items as $itemJabatan)
                                                        <button type="button" @click="selectedJabatanMob = '{{ $itemJabatan }}'; openJabatanMob = false"
                                                                class="w-full text-left px-3.5 py-2 text-xs font-semibold flex items-center justify-between transition hover:bg-indigo-50 dark:hover:bg-indigo-950/50 hover:text-indigo-600 dark:hover:text-indigo-400"
                                                                :class="{'bg-indigo-50/70 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400': selectedJabatanMob === '{{ $itemJabatan }}', 'text-slate-700 dark:text-slate-300': selectedJabatan !== '{{ $itemJabatan }}'}">
                                                            <span>{{ $itemJabatan }}</span>
                                                            <svg x-show="selectedJabatanMob === '{{ $itemJabatan }}'" class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                            <label class="block font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">No. WhatsApp / HP</label>
                                            <input type="text" name="telepon" x-model="telepon" class="w-full bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 rounded-xl p-2.5 text-xs sm:text-sm text-slate-800 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                        </div>
                                        <div>
                                            <label class="block font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Password Baru (Opsional)</label>
                                            <input type="password" name="password" x-model="password" placeholder="Kosongkan jika tetap" class="w-full bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 rounded-xl p-2.5 text-xs sm:text-sm text-slate-800 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-indigo-500 focus:outline-none placeholder-slate-400 dark:placeholder-slate-500">
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                                        <button type="button" @click="editModal = false" :disabled="isSubmittingEdit" class="px-4 py-2 border border-slate-300 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl font-medium disabled:opacity-50 transition">Batal</button>
                                        <button type="submit" :disabled="isSubmittingEdit || !isChanged" class="inline-flex items-center gap-2 px-5 py-2 bg-indigo-600 hover:bg-indigo-700 disabled:bg-slate-300 dark:disabled:bg-slate-800 disabled:text-slate-500 disabled:cursor-not-allowed disabled:hover:bg-slate-300 text-white rounded-xl font-bold transition active:scale-95 shadow-sm">
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
                    <div class="p-8 text-center text-slate-400 dark:text-slate-500">
                        Tidak ada data pengguna yang sesuai.
                    </div>
                @endforelse
            </div>

            {{-- 2. Desktop Table View (hidden md:block) --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse" :class="{'has-selection': selectedRows.length > 0}">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/70 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 border-b border-slate-100 dark:border-slate-800">
                            <th class="py-3 px-4 w-10 text-center">
                                <input type="checkbox" @change="toggleSelectAll($event)" :checked="selectAll" class="rounded border-slate-300 dark:border-slate-700 text-indigo-600 focus:ring-indigo-500 h-4 w-4">
                            </th>
                            <th class="py-3 px-4 font-bold">Nama & Kontak</th>
                            <th class="py-3 px-4 font-bold">Nomor Induk</th>
                            <th class="py-3 px-4 font-bold">Kelas / Jabatan</th>
                            <th class="py-3 px-4 font-bold">Peran (Role)</th>
                            <th class="py-3 px-4 font-bold">Status</th>
                            <th class="py-3 px-4 font-bold">Total Pinjam</th>
                            <th class="py-3 px-4 text-right font-bold w-20">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 text-xs sm:text-sm">
                        @forelse($users as $u)
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors"
                                :class="{'bg-rose-50/20 dark:bg-rose-950/15': selectedRows.includes('{{ $u->id }}')}"
                                x-data="{ editModal: false, isSubmittingEditDesk: false }">
                                
                                {{-- Row Checkbox --}}
                                <td class="py-3.5 px-4 text-center">
                                    <input type="checkbox" name="user_select[]" value="{{ $u->id }}"
                                           @change="toggleRow('{{ $u->id }}')"
                                           :checked="selectedRows.includes('{{ $u->id }}')"
                                           class="rounded border-slate-300 dark:border-slate-700 text-indigo-600 focus:ring-indigo-500 h-4 w-4">
                                </td>

                                {{-- Cell Customer: Avatar + Name + Email --}}
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full overflow-hidden shrink-0 border border-slate-200 dark:border-slate-700 bg-gradient-to-tr from-slate-700 to-slate-900 text-white flex items-center justify-center text-[11px] font-bold shadow-2xs">
                                            @if(!empty($u->foto))
                                                <img src="{{ asset('storage/' . $u->foto) }}" alt="{{ $u->name }}" class="w-full h-full object-cover"
                                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <span style="display: none;" class="w-full h-full flex items-center justify-center font-bold text-[11px]">{{ strtoupper(substr($u->name, 0, 1)) }}</span>
                                            @else
                                                <span>{{ strtoupper(substr($u->name, 0, 1)) }}</span>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-bold text-slate-900 dark:text-white leading-tight truncate">{{ $u->name }}</div>
                                            <div class="text-slate-400 dark:text-slate-500 text-xs mt-0.5 flex items-center gap-2 truncate">
                                                <span>{{ $u->email }}</span>
                                                @if($u->telepon)
                                                    <span class="text-slate-300 dark:text-slate-600">•</span>
                                                    <span>{{ $u->telepon }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Cell Mono: Nomor Induk --}}
                                <td class="py-3.5 px-4 font-mono font-medium text-slate-700 dark:text-slate-300 text-xs">
                                    {{ $u->nomor_induk ?? '—' }}
                                </td>

                                {{-- Kelas / Jabatan --}}
                                <td class="py-3.5 px-4 text-slate-700 dark:text-slate-300 font-medium">
                                    <div>{{ $u->kelas_atau_jabatan ?? '—' }}</div>
                                    @if($u->role !== 'admin' && $u->laboratorium_penugasan)
                                        <div class="text-[11px] text-indigo-600 dark:text-indigo-400 font-semibold">{{ $u->laboratorium_penugasan }}</div>
                                    @endif
                                </td>

                                {{-- Chip Role (Gentelella Style) --}}
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @php
                                        $isKepalaLabUser = $u->role === 'kepala_lab' || ($u->role === 'guru' && (
                                            str_contains(strtolower($u->kelas_atau_jabatan ?? ''), 'kepala lab') ||
                                            str_contains(strtolower($u->kelas_atau_jabatan ?? ''), 'kepala laboratorium')
                                        ));
                                    @endphp

                                    @if($isKepalaLabUser)
                                        <div class="inline-flex flex-col items-start gap-1">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md border text-[10.5px] font-bold tracking-wide uppercase bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-800 shadow-2xs">
                                                Guru
                                            </span>
                                            <span class="inline-flex items-center gap-1 text-[10px] font-extrabold text-indigo-700 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-950/50 border border-indigo-200/80 dark:border-indigo-800/60 px-1.5 py-0.5 rounded-md shadow-2xs">
                                                <svg class="w-3 h-3 text-indigo-600 dark:text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                                                </svg>
                                                <span>Kepala Laboratorium</span>
                                            </span>
                                        </div>
                                    @elseif($u->role === 'admin')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md border text-[10.5px] font-bold tracking-wide uppercase bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border-indigo-200 dark:border-indigo-800 shadow-2xs">
                                            Admin
                                        </span>
                                    @elseif($u->role === 'koordinator_lab')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md border text-[10.5px] font-bold tracking-wide uppercase bg-cyan-50 dark:bg-cyan-950/60 text-cyan-700 dark:text-cyan-300 border-cyan-200 dark:border-cyan-800 shadow-2xs">
                                            Koordinator
                                        </span>
                                    @elseif($u->role === 'guru')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md border text-[10.5px] font-bold tracking-wide uppercase bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-800 shadow-2xs">
                                            Guru
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md border text-[10.5px] font-bold tracking-wide uppercase bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800 shadow-2xs">
                                            Siswa
                                        </span>
                                    @endif
                                </td>

                                {{-- Status Pulsing Dot --}}
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span x-show="userStatuses[{{ $u->id }}] ?? {{ $u->isOnline() ? 'true' : 'false' }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-600 dark:text-emerald-400" {!! $u->isOnline() ? '' : 'style="display:none;"' !!}>
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                        <span>Aktif</span>
                                    </span>
                                    <span x-show="!(userStatuses[{{ $u->id }}] ?? {{ $u->isOnline() ? 'true' : 'false' }})" class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-500 dark:text-slate-400" {!! $u->isOnline() ? 'style="display:none;"' : '' !!}>
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400 dark:bg-slate-600"></span>
                                        <span>Terdaftar</span>
                                    </span>
                                </td>

                                {{-- Total Pinjam --}}
                                <td class="py-3.5 px-4 font-bold text-slate-800 dark:text-slate-200">
                                    {{ $u->peminjamans_count }} <span class="text-xs font-normal text-slate-400 dark:text-slate-500">kali</span>
                                </td>

                                {{-- Actions --}}
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center justify-end gap-1">
                                        <button type="button" @click="editModal = true" title="Edit Pengguna" class="w-7 h-7 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300 flex items-center justify-center transition active:scale-95 shadow-2xs">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                            </svg>
                                        </button>

                                        @if($u->id !== Auth::id())
                                            <button type="button" @click.stop="$dispatch('open-delete-user', { url: '{{ route('pengguna.destroy', $u->id) }}', name: '{{ addslashes($u->name) }}' })" title="Hapus Pengguna" class="w-7 h-7 rounded-lg border border-rose-200 dark:border-rose-900/60 bg-rose-50/50 dark:bg-rose-950/30 hover:bg-rose-100 dark:hover:bg-rose-900/50 text-rose-600 dark:text-rose-400 flex items-center justify-center transition active:scale-95 shadow-2xs">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>

                                    {{-- Modal Edit Pengguna (Desktop) --}}
                                    <div x-show="editModal" class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md text-left" style="display: none;" x-transition>
                                        <div @click.away="if(!isSubmittingEditDesk) editModal = false" class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 w-full max-w-lg shadow-2xl border border-slate-100 dark:border-slate-800 max-h-[92vh] overflow-y-auto">
                                            <div class="flex items-center justify-between mb-5 pb-3 border-b border-slate-100 dark:border-slate-800">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-10 h-10 rounded-full overflow-hidden shadow-2xs border border-indigo-100 dark:border-indigo-900 bg-gradient-to-tr from-indigo-600 to-indigo-800 text-white flex items-center justify-center shrink-0 text-xs font-bold">
                                                        @if(!empty($u->foto))
                                                            <img src="{{ asset('storage/' . $u->foto) }}" alt="{{ $u->name }}" class="w-full h-full object-cover"
                                                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                            <span style="display: none;" class="w-full h-full flex items-center justify-center font-bold text-xs">{{ strtoupper(substr($u->name, 0, 1)) }}</span>
                                                        @else
                                                            <span>{{ strtoupper(substr($u->name, 0, 1)) }}</span>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <h3 class="font-bold text-base text-slate-800 dark:text-white">Edit Pengguna</h3>
                                                        <p class="text-xs text-slate-400 dark:text-slate-400">{{ $u->name }}</p>
                                                    </div>
                                                </div>
                                                <button type="button" @click="editModal = false" :disabled="isSubmittingEditDesk" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                </button>
                                            </div>

                                            <form action="{{ route('pengguna.update', $u->id) }}" method="POST" class="space-y-4 text-xs"
                                                  @submit="isSubmittingEditDesk = true"
                                                  x-data="{
                                                      openRole: false,
                                                      selectedRole: '{{ old('role', $u->role) }}',
                                                      openLabDesk: false,
                                                      selectedLabDesk: '{{ old('laboratorium_penugasan', $u->laboratorium_penugasan) }}',
                                                      openJabatanDesk: false,
                                                      selectedJabatanDesk: '{{ old('kelas_atau_jabatan', $u->kelas_atau_jabatan ?? '') }}',
                                                      name: @js(old('name', $u->name)),
                                                      email: @js(old('email', $u->email)),
                                                      nomor_induk: @js(old('nomor_induk', $u->nomor_induk ?? '')),
                                                      telepon: @js(old('telepon', $u->telepon ?? '')),
                                                      password: '',
                                                      initial: {
                                                          name: @js(old('name', $u->name)),
                                                          email: @js(old('email', $u->email)),
                                                          role: '{{ old('role', $u->role) }}',
                                                          nomor_induk: @js(old('nomor_induk', $u->nomor_induk ?? '')),
                                                          telepon: @js(old('telepon', $u->telepon ?? '')),
                                                          selectedJabatanDesk: '{{ old('kelas_atau_jabatan', $u->kelas_atau_jabatan ?? '') }}',
                                                          selectedLabDesk: '{{ old('laboratorium_penugasan', $u->laboratorium_penugasan ?? '') }}'
                                                      },
                                                      get isChanged() {
                                                          return this.name !== this.initial.name ||
                                                                 this.email !== this.initial.email ||
                                                                 this.selectedRole !== this.initial.role ||
                                                                 this.nomor_induk !== this.initial.nomor_induk ||
                                                                 this.telepon !== this.initial.telepon ||
                                                                 this.selectedJabatanDesk !== this.initial.selectedJabatanDesk ||
                                                                 this.selectedLabDesk !== this.initial.selectedLabDesk ||
                                                                 this.password.trim().length > 0;
                                                      }
                                                  }">
                                                @csrf
                                                @method('PUT')
                                                <div>
                                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                                                    <input type="text" name="name" x-model="name" required class="w-full bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 rounded-xl p-2.5 text-xs sm:text-sm text-slate-800 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                                </div>

                                                <div class="grid grid-cols-2 gap-3">
                                                    <div>
                                                        <label class="block font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Alamat Email <span class="text-rose-500">*</span></label>
                                                        <input type="email" name="email" x-model="email" required class="w-full bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 rounded-xl p-2.5 text-xs sm:text-sm text-slate-800 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                                    </div>

                                                    {{-- Dropdown Role Modal Edit (Desktop) --}}
                                                    <div class="relative" @click.outside="openRole = false">
                                                        <label class="block font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Peran (Role) <span class="text-rose-500">*</span></label>
                                                        <input type="hidden" name="role" :value="selectedRole" required>

                                                        <button type="button" @click="openRole = !openRole"
                                                                class="w-full flex items-center justify-between bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 rounded-xl p-2.5 text-xs sm:text-sm font-medium transition focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                                                :class="{'border-indigo-500 ring-2 ring-indigo-500/20 bg-white dark:bg-slate-800': openRole}">
                                                            <span class="text-slate-800 dark:text-slate-200" x-text="{
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
                                                             class="absolute z-30 w-full mt-1.5 bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-200/80 dark:border-slate-700 py-1 overflow-hidden"
                                                             style="display: none;">
                                                            @foreach(['siswa' => 'Siswa', 'guru' => 'Guru', 'koordinator_lab' => 'Koordinator Laboratorium', 'kepala_lab' => 'Kepala Laboratorium', 'admin' => 'Admin'] as $val => $label)
                                                                <button type="button" @click="selectedRole = '{{ $val }}'; openRole = false"
                                                                        class="w-full text-left px-3.5 py-2 text-xs font-semibold flex items-center justify-between transition hover:bg-indigo-50 dark:hover:bg-indigo-950/50 hover:text-indigo-600 dark:hover:text-indigo-400"
                                                                        :class="{'bg-indigo-50/70 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400': selectedRole === '{{ $val }}', 'text-slate-700 dark:text-slate-300': selectedRole !== '{{ $val }}'}">
                                                                    <span>{{ $label }}</span>
                                                                    <svg x-show="selectedRole === '{{ $val }}'" class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                                    </svg>
                                                                </button>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Lab Penugasan: Edit Desktop --}}
                                                <div x-show="selectedRole === 'koordinator_lab'"
                                                     x-transition:enter="transition ease-out duration-200"
                                                     x-transition:enter-start="opacity-0 -translate-y-2"
                                                     x-transition:enter-end="opacity-100 translate-y-0"
                                                     x-transition:leave="transition ease-in duration-150"
                                                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                                     x-transition:leave-end="opacity-0 -translate-y-2"
                                                     style="display:none;">
                                                    <div class="relative bg-indigo-50/50 dark:bg-indigo-950/30 rounded-2xl p-3.5 border border-indigo-200/70 dark:border-indigo-800/60" @click.outside="openLabDesk = false">
                                                        <label class="flex items-center gap-1.5 font-bold text-indigo-900 dark:text-indigo-300 uppercase tracking-wider mb-2 text-[10.5px]">
                                                            <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                                            </svg>
                                                            <span>Laboratorium Penugasan <span class="text-rose-500">*</span></span>
                                                        </label>
                                                        <input type="hidden" name="laboratorium_penugasan" :value="selectedLabDesk">
                                                        
                                                        <button type="button" @click="openLabDesk = !openLabDesk"
                                                                class="w-full flex items-center justify-between bg-white dark:bg-slate-800 border border-indigo-200 dark:border-indigo-800/80 rounded-xl p-2.5 text-xs sm:text-sm font-medium transition focus:outline-none focus:ring-2 focus:ring-indigo-500 shadow-xs"
                                                                :class="{'border-indigo-500 ring-2 ring-indigo-500/20': openLabDesk}">
                                                            <span :class="selectedLabDesk ? 'text-slate-800 dark:text-white font-bold' : 'text-slate-400 dark:text-slate-500'" x-text="selectedLabDesk || 'Pilih Laboratorium'"></span>
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
                                                             class="absolute z-40 w-full left-0 mt-1.5 bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-200/80 dark:border-slate-700 py-1 overflow-hidden"
                                                             style="display:none;">
                                                            @foreach($laboratoriumList as $labItem)
                                                                <button type="button" @click="selectedLabDesk = '{{ $labItem }}'; openLabDesk = false"
                                                                        class="w-full text-left px-3.5 py-2 text-xs font-semibold flex items-center justify-between transition hover:bg-indigo-50 dark:hover:bg-indigo-950/50 hover:text-indigo-600 dark:hover:text-indigo-400"
                                                                        :class="{'bg-indigo-50/70 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400': selectedLabDesk === '{{ $labItem }}', 'text-slate-700 dark:text-slate-300': selectedLabDesk !== '{{ $labItem }}'}">
                                                                    <span>{{ $labItem }}</span>
                                                                    <svg x-show="selectedLabDesk === '{{ $labItem }}'" class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                                    </svg>
                                                                </button>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="grid grid-cols-2 gap-3">
                                                    <div>
                                                        <label class="block font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">NIS / NIP</label>
                                                        <input type="text" name="nomor_induk" x-model="nomor_induk" class="w-full bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 rounded-xl p-2.5 text-xs sm:text-sm text-slate-800 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                                    </div>

                                                    {{-- Dropdown: Kelas / Jabatan (Edit Desktop) --}}
                                                    <div class="relative" x-show="selectedRole === 'siswa' || selectedRole === 'guru' || selectedRole === 'kepala_lab'" @click.outside="openJabatanDesk = false">
                                                        <label class="block font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                                                            <span x-text="selectedRole === 'siswa' ? 'Kelas' : 'Jabatan / Posisi Guru'"></span>
                                                            <span x-show="selectedRole === 'siswa'" class="text-rose-500">*</span>
                                                        </label>
                                                        <input type="hidden" name="kelas_atau_jabatan" :value="selectedJabatanDesk">

                                                        <button type="button" @click="openJabatanDesk = !openJabatanDesk"
                                                                class="w-full flex items-center justify-between bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 rounded-xl p-2.5 text-xs sm:text-sm font-medium transition focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                                                :class="{'border-indigo-500 ring-2 ring-indigo-500/20 bg-white dark:bg-slate-800': openJabatanDesk}">
                                                            <span class="truncate text-slate-800 dark:text-slate-200" x-text="selectedJabatanDesk || 'Pilih Jabatan/Kelas'"></span>
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
                                                             class="absolute z-30 w-full mt-1.5 bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-200/80 dark:border-slate-700 py-1 max-h-56 overflow-y-auto"
                                                             style="display: none;">
                                                            @foreach($daftarJabatanKelas as $grup => $items)
                                                                <div class="px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 bg-slate-50/80 dark:bg-slate-800/80">{{ $grup }}</div>
                                                                @foreach($items as $itemJabatan)
                                                                    <button type="button" @click="selectedJabatanDesk = '{{ $itemJabatan }}'; openJabatanDesk = false"
                                                                            class="w-full text-left px-3.5 py-2 text-xs font-semibold flex items-center justify-between transition hover:bg-indigo-50 dark:hover:bg-indigo-950/50 hover:text-indigo-600 dark:hover:text-indigo-400"
                                                                            :class="{'bg-indigo-50/70 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400': selectedJabatanDesk === '{{ $itemJabatan }}', 'text-slate-700 dark:text-slate-300': selectedJabatan !== '{{ $itemJabatan }}'}">
                                                                        <span>{{ $itemJabatan }}</span>
                                                                        <svg x-show="selectedJabatanDesk === '{{ $itemJabatan }}'" class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                                        <label class="block font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">No. WhatsApp / HP</label>
                                                        <input type="text" name="telepon" x-model="telepon" class="w-full bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 rounded-xl p-2.5 text-xs sm:text-sm text-slate-800 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                                    </div>
                                                    <div>
                                                        <label class="block font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Password Baru (Opsional)</label>
                                                        <input type="password" name="password" x-model="password" placeholder="Kosongkan jika tetap" class="w-full bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 rounded-xl p-2.5 text-xs sm:text-sm text-slate-800 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-indigo-500 focus:outline-none placeholder-slate-400 dark:placeholder-slate-500">
                                                    </div>
                                                </div>

                                                <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-100 dark:border-slate-800">
                                                    <button type="button" @click="editModal = false" :disabled="isSubmittingEditDesk" class="px-4 py-2 border border-slate-300 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl font-medium disabled:opacity-50 transition">Batal</button>
                                                    <button type="submit" :disabled="isSubmittingEditDesk || !isChanged" class="inline-flex items-center gap-2 px-5 py-2 bg-indigo-600 hover:bg-indigo-700 disabled:bg-slate-300 dark:disabled:bg-slate-800 disabled:text-slate-500 disabled:cursor-not-allowed disabled:hover:bg-slate-300 text-white rounded-xl font-bold transition active:scale-95 shadow-sm">
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
                                <td colspan="8" class="px-5 py-12 text-center">
                                    <div class="w-12 h-12 bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 rounded-2xl flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    </div>
                                    <p class="font-extrabold text-slate-800 dark:text-white text-sm sm:text-base">Tidak ada data pengguna</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-sm mx-auto">Tidak ditemukan pengguna yang sesuai dengan kata kunci atau filter peran yang dipilih.</p>
                                    <div x-show="search || role">
                                        <button type="button" @click="resetFilters()" class="inline-flex items-center gap-1.5 mt-4 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition active:scale-95">
                                            <span>Reset Filter</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- CARD FOOTER (Gentelella v4 Paging & Info) --}}
            <div class="px-4 sm:px-5 py-3.5 sm:py-4 border-t border-slate-100 dark:border-slate-800 flex flex-col md:flex-row md:items-center justify-between gap-3 sm:gap-4 bg-slate-50/40 dark:bg-slate-850/40">
                <div class="flex flex-wrap items-center justify-between sm:justify-start gap-2.5 sm:gap-4 text-xs text-slate-500 dark:text-slate-400">
                    <div>
                        Menampilkan <span class="font-bold text-slate-700 dark:text-slate-200">{{ $users->firstItem() ?? 0 }}–{{ $users->lastItem() ?? 0 }}</span> dari <span class="font-bold text-slate-700 dark:text-slate-200">{{ $users->total() }}</span> pengguna
                    </div>

                    <span class="text-slate-300 dark:text-slate-700 hidden sm:inline">•</span>

                    {{-- Selector Jumlah Tampilan (Show Per Page) --}}
                    <div class="flex items-center gap-1.5">
                        <label for="perPageSelect" class="text-slate-500 dark:text-slate-400 font-medium">Tampilkan:</label>
                        <select id="perPageSelect" 
                                x-model="perPage" 
                                @change="setPerPage($event.target.value)"
                                class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-xs font-semibold rounded-xl px-2.5 py-1.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 shadow-2xs cursor-pointer">
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <span class="text-slate-500 dark:text-slate-400 font-medium">baris</span>
                    </div>
                </div>
                
                <div class="shrink-0 flex items-center justify-between sm:justify-end">
                    @if ($users->hasPages())
                        <nav role="navigation" aria-label="Navigasi Halaman" class="flex items-center gap-1.5 sm:gap-2">
                            {{-- Previous Page Link --}}
                            @if ($users->onFirstPage())
                                <span aria-disabled="true" class="h-8 w-8 sm:h-9 sm:w-9 inline-flex items-center justify-center rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-850 text-slate-300 dark:text-slate-600 text-xs cursor-not-allowed select-none">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                </span>
                            @else
                                <a href="{{ $users->previousPageUrl() }}" rel="prev" aria-label="Sebelumnya" class="h-8 w-8 sm:h-9 sm:w-9 inline-flex items-center justify-center rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 text-xs font-semibold shadow-2xs transition active:scale-95">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                </a>
                            @endif

                            {{-- First Page (if far from current) --}}
                            @if ($users->currentPage() > 3)
                                <a href="{{ $users->url(1) }}" class="h-8 w-8 sm:h-9 sm:w-9 inline-flex items-center justify-center rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 text-xs font-semibold shadow-2xs transition active:scale-95">
                                    1
                                </a>
                                @if ($users->currentPage() > 4)
                                    <span class="h-8 w-8 sm:h-9 sm:w-9 inline-flex items-center justify-center text-xs font-bold text-slate-400 dark:text-slate-500 select-none">...</span>
                                @endif
                            @endif

                            {{-- Page Numbers Range --}}
                            @foreach ($users->getUrlRange(max(1, $users->currentPage() - 2), min($users->lastPage(), $users->currentPage() + 2)) as $page => $url)
                                @if ($page == $users->currentPage())
                                    <span aria-current="page" class="h-8 w-8 sm:h-9 sm:w-9 inline-flex items-center justify-center rounded-xl bg-indigo-600 text-white text-xs font-bold shadow-xs select-none">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="h-8 w-8 sm:h-9 sm:w-9 inline-flex items-center justify-center rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 text-xs font-semibold shadow-2xs transition active:scale-95">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach

                            {{-- Last Page (if far from current) --}}
                            @if ($users->currentPage() < $users->lastPage() - 2)
                                @if ($users->currentPage() < $users->lastPage() - 3)
                                    <span class="h-8 w-8 sm:h-9 sm:w-9 inline-flex items-center justify-center text-xs font-bold text-slate-400 dark:text-slate-500 select-none">...</span>
                                @endif
                                <a href="{{ $users->url($users->lastPage()) }}" class="h-8 w-8 sm:h-9 sm:w-9 inline-flex items-center justify-center rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 text-xs font-semibold shadow-2xs transition active:scale-95">
                                    {{ $users->lastPage() }}
                                </a>
                            @endif

                            {{-- Next Page Link --}}
                            @if ($users->hasMorePages())
                                <a href="{{ $users->nextPageUrl() }}" rel="next" aria-label="Berikutnya" class="h-8 w-8 sm:h-9 sm:w-9 inline-flex items-center justify-center rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 text-xs font-semibold shadow-2xs transition active:scale-95">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            @else
                                <span aria-disabled="true" class="h-8 w-8 sm:h-9 sm:w-9 inline-flex items-center justify-center rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-850 text-slate-300 dark:text-slate-600 text-xs cursor-not-allowed select-none">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </span>
                            @endif
                        </nav>
                    @endif
                </div>
            </div>

        </div>
    </div>

    {{-- MODAL KONFIRMASI HAPUS TUNGGAL TENGAH LAYAR DENGAN SPINNER --}}
    <div x-show="deleteModal" class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-slate-900/75 backdrop-blur-md" style="display: none;"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div @click.away="if(!isDeleting) deleteModal = false" class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-7 w-full max-w-sm sm:max-w-md shadow-2xl border border-slate-100 dark:border-slate-800 text-center"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-90 translate-y-3"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-90 translate-y-3">
            
            <div class="w-14 h-14 bg-rose-100 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-inner border border-rose-200/60 dark:border-rose-800/60">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>

            <h3 class="text-base sm:text-lg font-extrabold text-slate-800 dark:text-white">Hapus Pengguna Ini?</h3>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1.5 leading-relaxed">
                Anda akan menghapus data akun <span class="font-bold text-slate-800 dark:text-slate-200" x-text="deleteUserName"></span>. Tindakan ini tidak dapat dibatalkan.
            </p>

            <form id="globalDeletePenggunaForm" :action="deleteActionUrl" method="POST" @submit="isDeleting = true" class="mt-6 flex items-center justify-center gap-3">
                @csrf
                @method('DELETE')
                
                <button type="button" :disabled="isDeleting" @click="deleteModal = false" class="flex-1 px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 disabled:opacity-50 text-slate-700 dark:text-slate-200 font-semibold rounded-xl text-xs sm:text-sm transition">
                    Batal
                </button>
                <button type="submit" :disabled="isDeleting" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-rose-600 hover:bg-rose-700 disabled:bg-rose-400 text-white font-bold rounded-xl text-xs sm:text-sm shadow-md shadow-rose-600/20 transition active:scale-95">
                    <svg x-show="isDeleting" class="animate-spin -ml-1 mr-1 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" style="display: none;">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-text="isDeleting ? 'Menghapus...' : 'Ya, Hapus'"></span>
                </button>
            </form>
        </div>
    </div>

    {{-- MODAL KONFIRMASI HAPUS BANYAK (BULK DELETE) --}}
    <div x-show="bulkDeleteModal" class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-slate-900/75 backdrop-blur-md" style="display: none;"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div @click.away="if(!isBulkDeleting) bulkDeleteModal = false" class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-7 w-full max-w-sm sm:max-w-md shadow-2xl border border-slate-100 dark:border-slate-800 text-center"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-90 translate-y-3"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-90 translate-y-3">
            
            <div class="w-14 h-14 bg-rose-100 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-inner border border-rose-200/60 dark:border-rose-800/60">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>

            <h3 class="text-base sm:text-lg font-extrabold text-slate-800 dark:text-white">
                Hapus <span x-text="selectedRows.length"></span> Pengguna Terpilih?
            </h3>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1.5 leading-relaxed">
                Anda akan menghapus data <span class="font-bold text-slate-800 dark:text-slate-200" x-text="selectedRows.length + ' akun pengguna'"></span> yang telah dicentang. Tindakan ini tidak dapat dibatalkan.
            </p>

            <form action="{{ route('pengguna.bulk-delete') }}" method="POST" @submit="isBulkDeleting = true" class="mt-6">
                @csrf
                <template x-for="id in selectedRows" :key="id">
                    <input type="hidden" name="ids[]" :value="id">
                </template>
                
                <div class="flex items-center justify-center gap-3">
                    <button type="button" :disabled="isBulkDeleting" @click="bulkDeleteModal = false" class="flex-1 px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 disabled:opacity-50 text-slate-700 dark:text-slate-200 font-semibold rounded-xl text-xs sm:text-sm transition">
                        Batal
                    </button>
                    <button type="submit" :disabled="isBulkDeleting" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-rose-600 hover:bg-rose-700 disabled:bg-rose-400 text-white font-bold rounded-xl text-xs sm:text-sm shadow-md shadow-rose-600/20 transition active:scale-95">
                        <svg x-show="isBulkDeleting" class="animate-spin -ml-1 mr-1 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" style="display: none;">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="isBulkDeleting ? 'Menghapus...' : 'Ya, Hapus Semua'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection