@extends('layouts.app')

@section('title', 'Tambah Barang Baru')
@section('page_title', 'Tambah Barang Baru')
@section('page_subtitle', 'Masukkan data inventaris laboratorium yang baru')

@section('content')
<div class="w-full max-w-7xl mx-auto" x-data="imageViewer()">

    {{-- Breadcrumb & Back --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div class="flex items-center gap-2 text-sm text-slate-500">
            <a href="{{ route('dashboard') }}" class="hover:text-indigo-600">Dashboard</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="{{ route('barang.index') }}" class="hover:text-indigo-600">Data Barang</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-slate-800 font-medium">Tambah Barang</span>
        </div>
        <a href="{{ route('barang.index') }}" class="inline-flex items-center gap-2 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 font-medium px-4 py-2 rounded-xl text-sm transition shadow-sm self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Daftar
        </a>
    </div>

    @if ($errors->any())
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-xl shadow-sm">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-semibold text-red-800">Terdapat kesalahan pada input form:</h3>
                    <ul class="mt-1 text-xs text-red-700 list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('barang.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            
            <!-- Left Col - Photo & Info -->
            <div class="xl:col-span-1 space-y-6">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200/60">
                    <h2 class="text-base font-bold text-slate-800 mb-1">Foto Fisik Barang</h2>
                    <p class="text-xs text-slate-400 mb-4">Unggah foto perangkat/alat laboratorium</p>
                    
                    <div class="flex flex-col items-center justify-center w-full">
                        <label for="foto" class="flex flex-col items-center justify-center w-full h-64 border-2 border-slate-300 border-dashed rounded-2xl cursor-pointer bg-slate-50/50 hover:bg-indigo-50/50 hover:border-indigo-400 relative overflow-hidden transition group">
                            
                            <template x-if="!imageUrl">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center px-4">
                                    <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-full flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <p class="mb-1 text-xs text-slate-600"><span class="font-bold text-indigo-600">Klik untuk unggah</span> atau seret foto ke sini</p>
                                    <p class="text-[10px] text-slate-400">PNG, JPG, JPEG, atau WEBP (Maksimal 2MB)</p>
                                </div>
                            </template>

                            <template x-if="imageUrl">
                                <img :src="imageUrl" class="w-full h-full object-cover rounded-2xl" />
                            </template>
                            
                            <input id="foto" name="foto" type="file" class="hidden" accept="image/*" @change="fileChosen" />
                        </label>
                        <template x-if="imageUrl">
                            <button type="button" @click="imageUrl = null; document.getElementById('foto').value=''" class="mt-3 text-xs text-red-600 hover:text-red-800 font-semibold flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Hapus Foto
                            </button>
                        </template>
                    </div>

                    <div class="mt-5 bg-indigo-50/70 rounded-xl p-4 border border-indigo-100/80">
                        <div class="flex items-start gap-3">
                            <div class="w-7 h-7 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xs font-bold text-indigo-900">Kode Barang Otomatis</h3>
                                <p class="text-[11px] text-indigo-700 mt-1 leading-relaxed">
                                    Kode barang akan digenerate otomatis oleh sistem saat disimpan berdasarkan Kategori yang dipilih. 
                                    <span class="block mt-1 font-mono font-semibold text-indigo-800">Contoh: TKJ-JRG-001</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Col - Form -->
            <div class="xl:col-span-2 space-y-6">
                
                <!-- Section 1: Identitas -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200/60">
                    <div class="mb-5 pb-3 border-b border-slate-100">
                        <h2 class="text-base font-bold text-slate-800">1. Identitas Barang</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Informasi nama, kategori, dan identifikasi alat</p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="md:col-span-2">
                            <label for="nama_barang" class="block mb-1.5 text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                Nama Barang <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="nama_barang" name="nama_barang" value="{{ old('nama_barang') }}"
                                   placeholder="Contoh: Router Mikrotik RB750Gr3 / Switch TP-Link 24 Port"
                                   class="bg-slate-50/50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent block w-full p-3 transition placeholder:text-slate-400" required>
                            <p class="text-[11px] text-slate-400 mt-1">Tulis nama barang spesifik beserta tipe/serinya jika ada</p>
                        </div>
                        
                        <!-- Animated Dropdown: Kategori -->
                        <div class="relative" x-data="{ open: false, selected: '{{ old('kategori', '') }}' }" @click.outside="open = false">
                            <label class="block mb-1.5 text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                Kategori <span class="text-red-500">*</span>
                            </label>
                            <input type="hidden" name="kategori" :value="selected" required>
                            
                            <button type="button" @click="open = !open"
                                    class="w-full flex items-center justify-between bg-slate-50/50 border border-slate-300 text-sm rounded-xl p-3 text-left transition focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent hover:bg-slate-100/70"
                                    :class="{'border-indigo-500 ring-2 ring-indigo-500/20': open}">
                                <span :class="selected ? 'text-slate-900 font-medium' : 'text-slate-400'" x-text="selected || '-- Pilih Kategori --'"></span>
                                <svg class="w-4 h-4 text-slate-500 transition-transform duration-200" :class="{'rotate-180 text-indigo-600': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                                 class="absolute z-30 w-full mt-2 bg-white rounded-xl shadow-xl border border-slate-100 py-1.5 max-h-60 overflow-auto focus:outline-none"
                                 style="display: none;">
                                @foreach(['Jaringan', 'Komputer', 'Perangkat Keras', 'Alat Praktik', 'Furniture', 'Lainnya'] as $kat)
                                    <button type="button" @click="selected = '{{ $kat }}'; open = false"
                                            class="w-full text-left px-4 py-2.5 text-sm flex items-center justify-between hover:bg-indigo-50/80 hover:text-indigo-600 transition"
                                            :class="{'bg-indigo-50 text-indigo-600 font-semibold': selected === '{{ $kat }}', 'text-slate-700': selected !== '{{ $kat }}'}">
                                        <span>{{ $kat }}</span>
                                        <svg x-show="selected === '{{ $kat }}'" class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </button>
                                @endforeach
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1">Grup modul perangkat di laboratorium</p>
                        </div>

                        <div>
                            <label for="merk" class="block mb-1.5 text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                Merk / Brand
                            </label>
                            <input type="text" id="merk" name="merk" value="{{ old('merk') }}"
                                   placeholder="Contoh: Mikrotik / Cisco / TP-Link / Asus / Logitech"
                                   class="bg-slate-50/50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent block w-full p-3 transition placeholder:text-slate-400">
                            <p class="text-[11px] text-slate-400 mt-1">Produsen / pembuat barang</p>
                        </div>

                        <div class="md:col-span-2">
                            <label for="nomor_seri" class="block mb-1.5 text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                Nomor Seri / S/N
                            </label>
                            <input type="text" id="nomor_seri" name="nomor_seri" value="{{ old('nomor_seri') }}"
                                   placeholder="Contoh: SN-847291048201 / S/N: 938472910"
                                   class="bg-slate-50/50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent block w-full p-3 font-mono transition placeholder:text-slate-400">
                            <p class="text-[11px] text-slate-400 mt-1">Nomor seri pabrik untuk kemudahan lacak (tracking) perangkat</p>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Kondisi & Lokasi -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200/60">
                    <div class="mb-5 pb-3 border-b border-slate-100">
                        <h2 class="text-base font-bold text-slate-800">2. Kondisi & Lokasi</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Status kelayakan fisik dan posisi penyimpanan</p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="md:col-span-2">
                            <label class="block mb-2 text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                Status Kondisi Barang <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-5 gap-2.5">
                                @foreach([
                                    'Baik'        => ['color' => 'emerald', 'desc' => 'Siap digunakan'],
                                    'Perawatan'   => ['color' => 'amber',   'desc' => 'Perlu di cek'],
                                    'Perbaikan'   => ['color' => 'orange',  'desc' => 'Servis'],
                                    'Rusak Berat' => ['color' => 'red',     'desc' => 'Tidak pakai'],
                                    'Hilang'      => ['color' => 'slate',   'desc' => '-']
                                ] as $k => $info)
                                <label for="kondisi_{{ $loop->index }}" class="relative flex flex-col p-3 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50/30 transition">
                                    <div class="flex items-center gap-2">
                                        <input id="kondisi_{{ $loop->index }}" type="radio" value="{{ $k }}" name="kondisi"
                                               class="w-4 h-4 text-indigo-600 border-slate-300 focus:ring-indigo-500"
                                               {{ old('kondisi', 'Baik') == $k ? 'checked' : '' }} required>
                                        <span class="text-xs font-bold text-slate-800">{{ $k }}</span>
                                    </div>
                                    <span class="text-[10px] text-slate-400 mt-1 pl-6">{{ $info['desc'] }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <label for="lokasi" class="block mb-1.5 text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                Lokasi Penyimpanan <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="lokasi" name="lokasi" value="{{ old('lokasi') }}"
                                   placeholder="Contoh: Lab TKJ 1 / Rak Server / Rak A2 / Ruang Guru"
                                   class="bg-slate-50/50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent block w-full p-3 transition placeholder:text-slate-400" required>
                            <p class="text-[11px] text-slate-400 mt-1">Ruangan/posisi fisik tempat barang diletakkan</p>
                        </div>

                        <div>
                            <label for="penanggung_jawab" class="block mb-1.5 text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                Penanggung Jawab (PIC)
                            </label>
                            <input type="text" id="penanggung_jawab" name="penanggung_jawab" value="{{ old('penanggung_jawab') }}"
                                   placeholder="Contoh: Pak Budi, S.Kom / Kepala Laboratorium"
                                   class="bg-slate-50/50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent block w-full p-3 transition placeholder:text-slate-400">
                            <p class="text-[11px] text-slate-400 mt-1">Nama pengelola yang bertanggung jawab atas barang</p>
                        </div>

                        {{-- *** FIELD LABORATORIUM *** --}}
                        <div class="relative" x-data="{ open: false, selected: '{{ old('laboratorium') }}' }" @click.outside="open = false">
                            <label class="block mb-1.5 text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                Laboratorium
                            </label>
                            <input type="hidden" name="laboratorium" :value="selected">
                            <button type="button" @click="open = !open"
                                    class="w-full flex items-center justify-between bg-slate-50/50 border border-slate-300 text-sm rounded-xl p-3 text-left transition focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent hover:bg-slate-100/70"
                                    :class="{'border-indigo-500 ring-2 ring-indigo-500/20': open}">
                                <span :class="selected ? 'text-slate-900 font-medium' : 'text-slate-400'" x-text="selected || '-'"></span>
                                <svg class="w-4 h-4 text-slate-500 transition-transform duration-200" :class="{'rotate-180 text-indigo-600': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div x-show="open"
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                                 class="absolute z-30 w-full mt-2 bg-white rounded-xl shadow-xl border border-slate-100 py-1.5 overflow-auto focus:outline-none"
                                 style="display: none;">
                                <button type="button" @click="selected = ''; open = false"
                                        class="w-full text-left px-4 py-2.5 text-sm flex items-center justify-between hover:bg-slate-50 transition"
                                        :class="{'bg-slate-50 text-slate-500 font-medium': selected === '', 'text-slate-400': selected !== ''}">
                                    <span>-- Tidak Dikaitkan --</span>
                                </button>
                                @foreach(['Laboratorium TKJ', 'Laboratorium AKL', 'Laboratorium Pemasaran'] as $lab)
                                <button type="button" @click="selected = '{{ $lab }}'; open = false"
                                        class="w-full text-left px-4 py-2.5 text-sm flex items-center justify-between hover:bg-indigo-50/80 hover:text-indigo-600 transition"
                                        :class="{'bg-indigo-50 text-indigo-600 font-semibold': selected === '{{ $lab }}', 'text-slate-700': selected !== '{{ $lab }}'}">
                                    <span>{{ $lab }}</span>
                                    <svg x-show="selected === '{{ $lab }}'" class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </button>
                                @endforeach
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1">Laboratorium tempat barang ini berada</p>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Stok & Satuan -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200/60">
                    <div class="mb-5 pb-3 border-b border-slate-100">
                        <h2 class="text-base font-bold text-slate-800">3. Stok & Satuan</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Jumlah kuantitas dan satuan barang</p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="jumlah" class="block mb-1.5 text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                Jumlah Stok <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="jumlah" name="jumlah" value="{{ old('jumlah', 1) }}" min="1"
                                   placeholder="Contoh: 10"
                                   class="bg-slate-50/50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent block w-full p-3 transition placeholder:text-slate-400" required>
                            <p class="text-[11px] text-slate-400 mt-1">Kuantitas fisik barang yang diterima</p>
                        </div>

                        <!-- Animated Dropdown: Satuan -->
                        <div class="relative" x-data="{ open: false, selected: '{{ old('satuan', 'Unit') }}' }" @click.outside="open = false">
                            <label class="block mb-1.5 text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                Satuan Barang <span class="text-red-500">*</span>
                            </label>
                            <input type="hidden" name="satuan" :value="selected" required>

                            <button type="button" @click="open = !open"
                                    class="w-full flex items-center justify-between bg-slate-50/50 border border-slate-300 text-sm rounded-xl p-3 text-left transition focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent hover:bg-slate-100/70"
                                    :class="{'border-indigo-500 ring-2 ring-indigo-500/20': open}">
                                <span :class="selected ? 'text-slate-900 font-medium' : 'text-slate-400'" x-text="selected || '-- Pilih Satuan --'"></span>
                                <svg class="w-4 h-4 text-slate-500 transition-transform duration-200" :class="{'rotate-180 text-indigo-600': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                                 class="absolute z-30 w-full mt-2 bg-white rounded-xl shadow-xl border border-slate-100 py-1.5 max-h-60 overflow-auto focus:outline-none"
                                 style="display: none;">
                                @foreach(['Unit', 'Meter', 'Box', 'Set', 'Buah', 'Lembar', 'Paket'] as $satuan)
                                    <button type="button" @click="selected = '{{ $satuan }}'; open = false"
                                            class="w-full text-left px-4 py-2.5 text-sm flex items-center justify-between hover:bg-indigo-50/80 hover:text-indigo-600 transition"
                                            :class="{'bg-indigo-50 text-indigo-600 font-semibold': selected === '{{ $satuan }}', 'text-slate-700': selected !== '{{ $satuan }}'}">
                                        <span>{{ $satuan }}</span>
                                        <svg x-show="selected === '{{ $satuan }}'" class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </button>
                                @endforeach
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1">Unit pengukuran (misal: Unit untuk router, Meter untuk kabel)</p>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Data Pembelian & Finansial -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200/60">
                    <div class="mb-5 pb-3 border-b border-slate-100">
                        <h2 class="text-base font-bold text-slate-800">4. Data Perolehan & Finansial (Opsional)</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Informasi anggaran, harga, dan waktu pembelian</p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="tanggal_pembelian" class="block mb-1.5 text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                Tanggal Pembelian / Penerimaan
                            </label>
                            <input type="date" id="tanggal_pembelian" name="tanggal_pembelian" value="{{ old('tanggal_pembelian') }}"
                                   class="bg-slate-50/50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent block w-full p-3 transition">
                            <p class="text-[11px] text-slate-400 mt-1">Tanggal saat barang dibeli/diterima di lab</p>
                        </div>

                        <div>
                            <label for="tahun_pembelian" class="block mb-1.5 text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                Tahun Pembelian
                            </label>
                            <input type="number" id="tahun_pembelian" name="tahun_pembelian" value="{{ old('tahun_pembelian') }}" min="1990" max="{{ date('Y') }}"
                                   placeholder="-"
                                   class="bg-slate-50/50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent block w-full p-3 transition placeholder:text-slate-400">
                            <p class="text-[11px] text-slate-400 mt-1">Tahun perolehan barang</p>
                        </div>

                        <div x-data="{
                            rawHarga: '{{ old('harga', '') }}',
                            formattedHarga: '',
                            formatRupiah(val) {
                                if (!val) return '';
                                let numberString = val.toString().replace(/[^0-9]/g, '');
                                if (!numberString) return '';
                                return numberString.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                            },
                            init() {
                                this.formattedHarga = this.formatRupiah(this.rawHarga);
                            },
                            updateHarga(e) {
                                let clean = e.target.value.replace(/[^0-9]/g, '');
                                this.rawHarga = clean;
                                this.formattedHarga = this.formatRupiah(clean);
                            }
                        }">
                            <label for="harga_display" class="block mb-1.5 text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                Harga Satuan (Rp)
                            </label>
                            <input type="hidden" name="harga" :value="rawHarga">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                                    <span class="text-slate-400 text-sm font-semibold">Rp</span>
                                </div>
                                <input type="text" 
                                       id="harga_display" 
                                       x-model="formattedHarga" 
                                       @input="updateHarga($event)"
                                       placeholder="-"
                                       class="bg-slate-50/50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent block w-full pl-11 p-3 transition placeholder:text-slate-400 font-medium">
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1">Otomatis diformat dengan pemisah ribuan (titik)</p>
                        </div>

                        <!-- Animated Dropdown: Sumber Dana -->
                        <div class="relative" x-data="{ open: false, selected: '{{ old('sumber_dana', '') }}' }" @click.outside="open = false">
                            <label class="block mb-1.5 text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                Sumber Dana
                            </label>
                            <input type="hidden" name="sumber_dana" :value="selected">

                            <button type="button" @click="open = !open"
                                    class="w-full flex items-center justify-between bg-slate-50/50 border border-slate-300 text-sm rounded-xl p-3 text-left transition focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent hover:bg-slate-100/70"
                                    :class="{'border-indigo-500 ring-2 ring-indigo-500/20': open}">
                                <span :class="selected ? 'text-slate-900 font-medium' : 'text-slate-400'" x-text="selected || '-'"></span>
                                <svg class="w-4 h-4 text-slate-500 transition-transform duration-200" :class="{'rotate-180 text-indigo-600': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                                 class="absolute z-30 w-full mt-2 bg-white rounded-xl shadow-xl border border-slate-100 py-1.5 max-h-60 overflow-auto focus:outline-none"
                                 style="display: none;">
                                <button type="button" @click="selected = ''; open = false"
                                        class="w-full text-left px-4 py-2.5 text-sm text-slate-400 hover:bg-slate-50 hover:text-slate-600 transition">
                                    -- Kosongkan Pilihan --
                                </button>
                                @foreach(['BOS', 'Sekolah', 'Hibah', 'Donasi', 'APBN', 'Lainnya'] as $sumber)
                                    <button type="button" @click="selected = '{{ $sumber }}'; open = false"
                                            class="w-full text-left px-4 py-2.5 text-sm flex items-center justify-between hover:bg-indigo-50/80 hover:text-indigo-600 transition"
                                            :class="{'bg-indigo-50 text-indigo-600 font-semibold': selected === '{{ $sumber }}', 'text-slate-700': selected !== '{{ $sumber }}'}">
                                        <span>{{ $sumber }}</span>
                                        <svg x-show="selected === '{{ $sumber }}'" class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </button>
                                @endforeach
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1">Asal bantuan/anggaran pengadaan barang</p>
                        </div>
                    </div>
                </div>

                <!-- Section 5: Deskripsi -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200/60">
                    <div class="mb-5 pb-3 border-b border-slate-100">
                        <h2 class="text-base font-bold text-slate-800">5. Spesifikasi & Deskripsi</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Catatan tambahan atau detail spesifikasi teknis</p>
                    </div>
                    
                    <div>
                        <label for="deskripsi" class="block mb-1.5 text-xs font-semibold text-slate-700 uppercase tracking-wider">Deskripsi Spesifikasi</label>
                        <textarea id="deskripsi" name="deskripsi" rows="4"
                                  placeholder="Contoh: Routerboard 5 Port Gigabit, RAM 256MB, Include Adaptor 24V. Kondisi port 1-5 aktif normal."
                                  class="block p-3 w-full text-sm text-slate-900 bg-slate-50/50 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition placeholder:text-slate-400 resize-none">{{ old('deskripsi') }}</textarea>
                        <p class="text-[11px] text-slate-400 mt-1">Tulis spesifikasi singkat atau catatan kelengkapan barang</p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4">
                    <a href="{{ route('barang.index') }}" class="px-5 py-3 border border-slate-300 text-slate-600 hover:bg-slate-50 font-semibold rounded-xl text-sm transition">
                        Batal
                    </a>
                    <button type="submit" class="text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 font-semibold rounded-xl text-sm px-7 py-3 text-center flex items-center shadow-sm transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002-2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                        Simpan Barang Baru
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    function imageViewer() {
        return {
            imageUrl: '',

            fileChosen(event) {
                this.fileToDataUrl(event, src => this.imageUrl = src)
            },

            fileToDataUrl(event, callback) {
                if (! event.target.files.length) return

                let file = event.target.files[0],
                    reader = new FileReader()

                reader.readAsDataURL(file)
                reader.onload = e => callback(e.target.result)
            },
        }
    }
</script>
@endsection