@extends('layouts.app')

@section('title', 'Edit Barang — ' . $barang->nama_barang)
@section('page_title', 'Edit Data Inventaris')
@section('page_subtitle', 'Perbarui detail spesifikasi, stok, dan foto barang laboratorium')

@section('content')
<div class="max-w-7xl mx-auto space-y-4 sm:space-y-6" 
     x-data="editBarangHandler()">

    {{-- Breadcrumb & Actions Bar --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 no-print">
        <nav class="flex items-center space-x-2 text-xs text-slate-500">
            <a href="{{ route('barang.index') }}" class="hover:text-indigo-600 transition">Inventaris Barang</a>
            <span class="text-slate-300">/</span>
            <a href="{{ route('barang.show', $barang->id) }}" class="hover:text-indigo-600 font-bold text-slate-700 font-mono">{{ $barang->kode_barang }}</a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-400">Edit</span>
        </nav>

        <div class="flex items-center gap-2">
            <a href="{{ route('barang.show', $barang->id) }}" class="bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 font-semibold px-4 py-2 rounded-xl text-xs flex items-center gap-1.5 transition shadow-xs">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    {{-- Header Banner Card --}}
    <div class="bg-white p-4 sm:p-6 rounded-2xl sm:rounded-3xl shadow-xs border border-slate-200/60 flex items-center gap-4">
        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-amber-50 text-amber-600 rounded-xl sm:rounded-2xl flex items-center justify-center shrink-0 shadow-xs border border-amber-100/80">
            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
        </div>
        <div class="min-w-0">
            <h1 class="text-lg sm:text-2xl font-extrabold text-slate-800 leading-tight truncate">Edit Data Barang</h1>
            <p class="text-[11px] sm:text-xs text-slate-500 mt-0.5 truncate">Perbarui data: <span class="font-bold text-slate-700">{{ $barang->nama_barang }}</span></p>
        </div>
    </div>

    {{-- Error Alert --}}
    @if ($errors->any())
        <div class="bg-rose-50 border border-rose-200 p-4 rounded-2xl shadow-xs">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                    <svg class="h-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-xs sm:text-sm font-bold text-rose-800">Terdapat kendala pada isian form:</h3>
                    <ul class="mt-1 text-xs text-rose-700 list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form id="editBarangForm" action="{{ route('barang.update', $barang->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
            
            {{-- Left Col - Photo & Kode Barang --}}
            <div class="lg:col-span-1 space-y-4 sm:space-y-6">
                <!-- Kode Barang Tag Card -->
                <div class="bg-indigo-50/80 p-4 sm:p-5 rounded-2xl sm:rounded-3xl border border-indigo-100 shadow-xs flex items-center justify-between">
                    <div>
                        <span class="text-[10px] font-bold text-indigo-600 uppercase tracking-wider block">Kode Barang</span>
                        <div class="text-lg sm:text-xl font-mono font-extrabold text-indigo-950 mt-0.5">{{ $barang->kode_barang }}</div>
                    </div>
                    <span class="text-[10px] font-bold bg-indigo-200/60 text-indigo-800 px-2 py-0.5 rounded-lg uppercase">Read only</span>
                </div>

                <!-- Foto Barang Card -->
                <div class="bg-white p-4 sm:p-5 rounded-2xl sm:rounded-3xl shadow-xs border border-slate-200/60">
                    <h2 class="text-xs sm:text-sm font-bold text-slate-800 uppercase tracking-wider mb-3 pb-2 border-b border-slate-100">Foto Alat Unit</h2>
                    
                    <div class="flex flex-col items-center justify-center w-full">
                        <label for="foto" class="flex flex-col items-center justify-center w-full h-48 sm:h-60 border-2 border-slate-200 border-dashed rounded-2xl cursor-pointer bg-slate-50 hover:bg-slate-100 relative overflow-hidden group transition">
                            
                            <template x-if="!imageUrl">
                                <div class="flex flex-col items-center justify-center p-4 text-center">
                                    <div class="w-10 h-10 rounded-xl bg-white text-slate-400 border border-slate-200 flex items-center justify-center mb-2 shadow-xs">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <p class="text-xs font-bold text-slate-700">Upload Foto Baru</p>
                                    <p class="text-[10px] text-slate-400 mt-0.5">PNG, JPG, JPEG (Max. 2MB)</p>
                                </div>
                            </template>

                            <template x-if="imageUrl">
                                <img :src="imageUrl" class="w-full h-full object-cover" />
                            </template>
                            
                            <input id="foto" name="foto" type="file" class="hidden" accept="image/*" @change="fileChosen" />
                        </label>

                        <template x-if="imageUrl">
                            <div class="mt-3 text-center">
                                <button type="button" @click="resetImage()" class="text-xs text-rose-600 hover:text-rose-800 font-bold flex items-center gap-1 mx-auto transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    <span>Hapus & Pilih Ulang</span>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Right Col - Form Fields --}}
            <div class="lg:col-span-2 space-y-4 sm:space-y-6">
                
                {{-- Section 1: Identitas (z-50 dan bebas overflow-hidden) --}}
                <div class="bg-white rounded-2xl sm:rounded-3xl shadow-xs border border-slate-200/80 relative z-50">
                    <div class="px-5 py-3 border-b border-slate-800 bg-slate-900 text-white rounded-t-2xl sm:rounded-t-3xl">
                        <h2 class="text-xs sm:text-sm font-bold text-white uppercase tracking-wider">Identitas Barang</h2>
                        <p class="text-[11px] text-slate-300 mt-0.5">Informasi nama, kategori, dan nomor seri</p>
                    </div>
                    <div class="p-4 sm:p-6 lg:p-7">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="sm:col-span-2">
                                <label for="nama_barang" class="block mb-2 text-xs font-bold text-slate-700">Nama Barang <span class="text-rose-500">*</span></label>
                                <input type="text" id="nama_barang" name="nama_barang" x-model="form.nama_barang" class="bg-slate-50 border border-slate-300 text-slate-800 text-xs sm:text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:outline-none block w-full px-3.5 py-2.5 transition" required>
                            </div>
                            
                            <!-- Animated Dropdown: Kategori -->
                            <div class="relative z-50" x-data="{ open: false }" @click.outside="open = false">
                                <label class="block mb-2 text-xs font-bold text-slate-700">Kategori <span class="text-rose-500">*</span></label>
                                <input type="hidden" name="kategori" :value="form.kategori" required>

                                <button type="button" @click="open = !open" 
                                        class="bg-slate-50 border border-slate-300 text-slate-800 text-xs sm:text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:outline-none flex items-center justify-between w-full px-3.5 py-2.5 text-left transition shadow-xs">
                                    <span x-text="form.kategori ? form.kategori : 'Pilih Kategori'" :class="{'text-slate-400': !form.kategori, 'font-bold text-indigo-700': form.kategori}"></span>
                                    <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 shrink-0 ml-2" :class="{'rotate-180 text-indigo-600': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>

                                <div x-show="open" 
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                     x-transition:leave="transition ease-in duration-100"
                                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                     x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                                     class="absolute left-0 right-0 z-50 mt-1.5 w-full bg-white border border-slate-200 rounded-2xl shadow-xl py-1 max-h-56 overflow-y-auto"
                                     style="display: none;">
                                    @foreach(['Jaringan', 'Komputer', 'Perangkat Keras', 'Alat Praktik', 'Furniture', 'Lainnya'] as $kat)
                                        <button type="button" @click="form.kategori = '{{ $kat }}'; open = false" 
                                                class="w-full text-left px-4 py-2.5 text-xs sm:text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 transition flex items-center justify-between"
                                                :class="{'font-bold text-indigo-600 bg-indigo-50/50': form.kategori === '{{ $kat }}'}">
                                            <span>{{ $kat }}</span>
                                            <svg x-show="form.kategori === '{{ $kat }}'" class="w-3.5 h-3.5 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            <div>
                                <label for="merk" class="block mb-2 text-xs font-bold text-slate-700">Merk / Brand</label>
                                <input type="text" id="merk" name="merk" x-model="form.merk" class="bg-slate-50 border border-slate-300 text-slate-800 text-xs sm:text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:outline-none block w-full px-3.5 py-2.5 transition">
                            </div>

                            <div class="sm:col-span-2">
                                <label for="nomor_seri" class="block mb-2 text-xs font-bold text-slate-700">Nomor Seri / S/N</label>
                                <input type="text" id="nomor_seri" name="nomor_seri" x-model="form.nomor_seri" class="bg-slate-50 border border-slate-300 text-slate-800 text-xs sm:text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:outline-none block w-full px-3.5 py-2.5 font-mono transition">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section 2: Kondisi & Lokasi (z-40 dan bebas overflow-hidden) --}}
                <div class="bg-white rounded-2xl sm:rounded-3xl shadow-xs border border-slate-200/80 relative z-40">
                    <div class="px-5 py-3 border-b border-slate-800 bg-slate-900 text-white rounded-t-2xl sm:rounded-t-3xl">
                        <h2 class="text-xs sm:text-sm font-bold text-white uppercase tracking-wider">Kondisi & Lokasi</h2>
                        <p class="text-[11px] text-slate-300 mt-0.5">Status fisik dan lokasi penempatan alat</p>
                    </div>
                    <div class="p-4 sm:p-6 lg:p-7">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="sm:col-span-2">
                                <label class="block mb-2 text-xs font-bold text-slate-700">Kondisi Utama Alat <span class="text-rose-500">*</span></label>
                                <div class="grid grid-cols-2 sm:grid-cols-5 gap-2">
                                    @foreach([
                                        'Baik' => 'emerald',
                                        'Perawatan' => 'yellow',
                                        'Perbaikan' => 'orange',
                                        'Rusak Berat' => 'red',
                                        'Hilang' => 'slate'
                                    ] as $k => $color)
                                    <label for="kondisi_{{ $k }}" class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-slate-100/80 cursor-pointer transition select-none"
                                           :class="{'border-indigo-500 bg-indigo-50/40': form.kondisi === '{{ $k }}'}">
                                        <input id="kondisi_{{ $k }}" type="radio" value="{{ $k }}" name="kondisi" x-model="form.kondisi" class="w-3.5 h-3.5 text-indigo-600 focus:ring-indigo-500 border-slate-300" required>
                                        <span class="text-xs font-medium text-slate-700 truncate">{{ $k }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>

                            <div>
                                <label for="lokasi" class="block mb-2 text-xs font-bold text-slate-700">Lokasi / Ruangan <span class="text-rose-500">*</span></label>
                                <input type="text" id="lokasi" name="lokasi" x-model="form.lokasi" class="bg-slate-50 border border-slate-300 text-slate-800 text-xs sm:text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:outline-none block w-full px-3.5 py-2.5 transition" required>
                            </div>

                            <div>
                                <label for="penanggung_jawab" class="block mb-2 text-xs font-bold text-slate-700">Penanggung Jawab</label>
                                <input type="text" id="penanggung_jawab" name="penanggung_jawab" x-model="form.penanggung_jawab" class="bg-slate-50 border border-slate-300 text-slate-800 text-xs sm:text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:outline-none block w-full px-3.5 py-2.5 transition">
                            </div>

                            {{-- Field Laboratorium Dropdown --}}
                            <div class="sm:col-span-2">
                                <div class="relative z-40" x-data="{ open: false }" @click.outside="open = false">
                                    <label class="block mb-2 text-xs font-bold text-slate-700">Pilih Laboratorium</label>
                                    <input type="hidden" name="laboratorium" :value="form.laboratorium">
                                    <button type="button" @click="open = !open"
                                            class="bg-slate-50 border border-slate-300 text-slate-800 text-xs sm:text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:outline-none flex items-center justify-between w-full px-3.5 py-2.5 text-left transition hover:bg-slate-100/70 shadow-xs"
                                            :class="{'border-indigo-500 ring-2 ring-indigo-500/20 bg-white': open}">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                            </svg>
                                            <span :class="form.laboratorium ? 'text-slate-800 font-bold' : 'text-slate-400'" x-text="form.laboratorium || '-- Pilih Laboratorium (Opsional) --'"></span>
                                        </div>
                                        <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 shrink-0 ml-2" :class="{'rotate-180 text-indigo-600': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    <div x-show="open"
                                         x-transition:enter="transition ease-out duration-150"
                                         x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                         x-transition:leave="transition ease-in duration-100"
                                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                         x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                                         class="absolute left-0 right-0 z-50 mt-1.5 w-full bg-white border border-slate-200 rounded-2xl shadow-xl py-1 max-h-56 overflow-y-auto"
                                         style="display: none;">
                                        <button type="button" @click="form.laboratorium = ''; open = false"
                                                class="w-full text-left px-4 py-2.5 text-xs sm:text-sm text-slate-400 hover:bg-slate-50 transition flex items-center justify-between"
                                                :class="{'font-medium bg-slate-50/50 text-slate-600': form.laboratorium === ''}">
                                            <span>-- Tidak Dikaitkan / Umum --</span>
                                            <svg x-show="form.laboratorium === ''" class="w-3.5 h-3.5 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        </button>
                                        @foreach(\App\Models\Barang::$laboratoriumList as $lab)
                                            <button type="button" @click="form.laboratorium = '{{ $lab }}'; open = false"
                                                    class="w-full text-left px-4 py-2.5 text-xs sm:text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 transition flex items-center justify-between"
                                                    :class="{'font-bold text-indigo-600 bg-indigo-50/50': form.laboratorium === '{{ $lab }}'}">
                                                <span class="flex items-center gap-2">
                                                    <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                                                    {{ $lab }}
                                                </span>
                                                <svg x-show="form.laboratorium === '{{ $lab }}'" class="w-3.5 h-3.5 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            </button>
                                        @endforeach
                                    </div>
                                    <p class="text-[11px] text-slate-400 mt-1">Pilih laboratorium tempat unit barang ini ditempatkan</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section 3: Stok & Satuan (z-30 dan bebas overflow-hidden) --}}
                <div class="bg-white rounded-2xl sm:rounded-3xl shadow-xs border border-slate-200/80 relative z-30">
                    <div class="px-5 py-3 border-b border-slate-800 bg-slate-900 text-white rounded-t-2xl sm:rounded-t-3xl">
                        <h2 class="text-xs sm:text-sm font-bold text-white uppercase tracking-wider">Stok & Satuan</h2>
                        <p class="text-[11px] text-slate-300 mt-0.5">Jumlah kuantitas dan unit satuan alat</p>
                    </div>
                    <div class="p-4 sm:p-6 lg:p-7">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="jumlah" class="block mb-2 text-xs font-bold text-slate-700">Jumlah Stok <span class="text-rose-500">*</span></label>
                                <input type="number" id="jumlah" name="jumlah" x-model="form.jumlah" min="0" class="bg-slate-50 border border-slate-300 text-slate-800 text-xs sm:text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:outline-none block w-full px-3.5 py-2.5 transition" required>
                            </div>

                            <!-- Animated Dropdown: Satuan -->
                            <div class="relative z-30" x-data="{ open: false }" @click.outside="open = false">
                                <label class="block mb-2 text-xs font-bold text-slate-700">Satuan <span class="text-rose-500">*</span></label>
                                <input type="hidden" name="satuan" :value="form.satuan" required>

                                <button type="button" @click="open = !open" 
                                        class="bg-slate-50 border border-slate-300 text-slate-800 text-xs sm:text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:outline-none flex items-center justify-between w-full px-3.5 py-2.5 text-left transition shadow-xs">
                                    <span x-text="form.satuan ? form.satuan : 'Pilih Satuan'" :class="{'text-slate-400': !form.satuan, 'font-bold text-indigo-700': form.satuan}"></span>
                                    <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 shrink-0 ml-2" :class="{'rotate-180 text-indigo-600': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>

                                <div x-show="open" 
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                     x-transition:leave="transition ease-in duration-100"
                                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                     x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                                     class="absolute left-0 right-0 z-50 mt-1.5 w-full bg-white border border-slate-200 rounded-2xl shadow-xl py-1 max-h-56 overflow-y-auto"
                                     style="display: none;">
                                    @foreach(['Unit', 'Meter', 'Box', 'Set', 'Buah', 'Lembar', 'Paket'] as $satuan)
                                        <button type="button" @click="form.satuan = '{{ $satuan }}'; open = false" 
                                                class="w-full text-left px-4 py-2.5 text-xs sm:text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 transition flex items-center justify-between"
                                                :class="{'font-bold text-indigo-600 bg-indigo-50/50': form.satuan === '{{ $satuan }}'}">
                                            <span>{{ $satuan }}</span>
                                            <svg x-show="form.satuan === '{{ $satuan }}'" class="w-3.5 h-3.5 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section 4: Pembelian & Finansial (z-20 dan bebas overflow-hidden) --}}
                <div class="bg-white rounded-2xl sm:rounded-3xl shadow-xs border border-slate-200/80 relative z-20">
                    <div class="px-5 py-3 border-b border-slate-800 bg-slate-900 text-white rounded-t-2xl sm:rounded-t-3xl">
                        <h2 class="text-xs sm:text-sm font-bold text-white uppercase tracking-wider">Data Pengadaan (Opsional)</h2>
                        <p class="text-[11px] text-slate-300 mt-0.5">Informasi anggaran, harga perolehan, dan sumber dana</p>
                    </div>
                    <div class="p-4 sm:p-6 lg:p-7">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="tanggal_pembelian" class="block mb-2 text-xs font-bold text-slate-700">Tanggal Pembelian</label>
                                <input type="date" id="tanggal_pembelian" name="tanggal_pembelian" x-model="form.tanggal_pembelian" class="bg-slate-50 border border-slate-300 text-slate-800 text-xs sm:text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:outline-none block w-full px-3.5 py-2.5 transition">
                            </div>

                            <div>
                                <label for="tahun_pembelian" class="block mb-2 text-xs font-bold text-slate-700">Tahun Pembelian</label>
                                <input type="number" id="tahun_pembelian" name="tahun_pembelian" x-model="form.tahun_pembelian" min="1990" max="{{ date('Y') }}" class="bg-slate-50 border border-slate-300 text-slate-800 text-xs sm:text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:outline-none block w-full px-3.5 py-2.5 transition">
                            </div>

                            <div>
                                <label for="harga_display" class="block mb-2 text-xs font-bold text-slate-700">Harga Satuan (Rp)</label>
                                <input type="hidden" name="harga" :value="form.harga">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                                        <span class="text-slate-400 text-xs font-bold">Rp</span>
                                    </div>
                                    <input type="text" 
                                           id="harga_display" 
                                           x-model="formattedHarga" 
                                           @input="updateHarga($event)"
                                           placeholder="Contoh: 450.000" 
                                           class="bg-slate-50 border border-slate-300 text-slate-800 text-xs sm:text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:outline-none block w-full pl-9 pr-3.5 py-2.5 font-medium transition">
                                </div>
                            </div>

                            <!-- Animated Dropdown: Sumber Dana -->
                            <div class="relative z-20" x-data="{ open: false }" @click.outside="open = false">
                                <label class="block mb-2 text-xs font-bold text-slate-700">Sumber Dana</label>
                                <input type="hidden" name="sumber_dana" :value="form.sumber_dana">

                                <button type="button" @click="open = !open" 
                                        class="bg-slate-50 border border-slate-300 text-slate-800 text-xs sm:text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:outline-none flex items-center justify-between w-full px-3.5 py-2.5 text-left transition shadow-xs">
                                    <span x-text="form.sumber_dana ? form.sumber_dana : 'Pilih Sumber Dana'" :class="{'text-slate-400': !form.sumber_dana, 'font-bold text-indigo-700': form.sumber_dana}"></span>
                                    <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 shrink-0 ml-2" :class="{'rotate-180 text-indigo-600': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>

                                <div x-show="open" 
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                     x-transition:leave="transition ease-in duration-100"
                                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                     x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                                     class="absolute left-0 right-0 z-50 mt-1.5 w-full bg-white border border-slate-200 rounded-2xl shadow-xl py-1 max-h-56 overflow-y-auto"
                                     style="display: none;">
                                    @foreach(['BOS', 'Sekolah', 'Hibah', 'Donasi', 'APBN', 'Lainnya'] as $sumber)
                                        <button type="button" @click="form.sumber_dana = '{{ $sumber }}'; open = false" 
                                                class="w-full text-left px-4 py-2.5 text-xs sm:text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 transition flex items-center justify-between"
                                                :class="{'font-bold text-indigo-600 bg-indigo-50/50': form.sumber_dana === '{{ $sumber }}'}">
                                            <span>{{ $sumber }}</span>
                                            <svg x-show="form.sumber_dana === '{{ $sumber }}'" class="w-3.5 h-3.5 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section 5: Deskripsi & Tombol Aksi (Anti-Cropping Container) --}}
                <div class="bg-white rounded-2xl sm:rounded-3xl shadow-xs border border-slate-200/80 overflow-hidden relative z-10">
                    <div class="px-5 py-3 border-b border-slate-800 bg-slate-900 text-white">
                        <h2 class="text-xs sm:text-sm font-bold text-white uppercase tracking-wider">Deskripsi / Catatan Alat</h2>
                        <p class="text-[11px] text-slate-300 mt-0.5">Spesifikasi teknis tambahan atau riwayat inventaris</p>
                    </div>
                    <div class="p-4 sm:p-6 lg:p-7 space-y-4">
                        <div>
                            <textarea id="deskripsi" name="deskripsi" x-model="form.deskripsi" rows="3" placeholder="Tambahkan spesifikasi rinci atau catatan..." class="block p-3.5 w-full text-xs sm:text-sm text-slate-800 bg-slate-50 rounded-xl sm:rounded-2xl border border-slate-300 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:outline-none transition"></textarea>
                        </div>

                        {{-- Footer Aksi: Rapi di Dalam Container Card --}}
                        <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                            <div class="text-xs">
                                <template x-if="!isDirty">
                                    <span class="inline-flex items-center gap-1.5 text-slate-400 font-medium">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                        <span>Tidak ada perubahan data</span>
                                    </span>
                                </template>
                                <template x-if="isDirty">
                                    <span class="inline-flex items-center gap-1.5 text-amber-600 font-bold">
                                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                                        <span>Perubahan belum disimpan</span>
                                    </span>
                                </template>
                            </div>

                            <div class="flex items-center justify-end gap-2.5">
                                <a href="{{ route('barang.show', $barang->id) }}" class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 text-xs font-bold transition shadow-xs">
                                    Batal
                                </a>
                                <button type="button" 
                                        :disabled="!isDirty"
                                        @click="if(isDirty) confirmModal = true"
                                        class="px-5 py-2.5 rounded-xl text-xs font-bold flex items-center justify-center gap-2 transition active:scale-95 shadow-xs"
                                        :class="isDirty ? 'bg-amber-500 hover:bg-amber-600 text-white cursor-pointer shadow-amber-500/20' : 'bg-slate-200 text-slate-400 cursor-not-allowed'">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    <span>Simpan Perubahan</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- MODAL KONFIRMASI SIMPAN PERUBAHAN --}}
    <template x-teleport="body">
        <div x-show="confirmModal" 
             class="fixed inset-0 z-[999999] flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs" 
             style="display: none;"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            <div @click.away="confirmModal = false" 
                 class="bg-white rounded-3xl p-6 sm:p-7 w-full max-w-sm sm:max-w-md shadow-2xl border border-slate-100 text-center"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">
                
                <div class="w-12 h-12 bg-amber-100 text-amber-600 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-amber-200/60">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>

                <h3 class="text-base sm:text-lg font-extrabold text-slate-800">Simpan Perubahan Barang?</h3>
                <p class="text-xs sm:text-sm text-slate-500 mt-1 leading-relaxed">
                    Perubahan data inventaris <span class="font-bold text-slate-800 font-mono">{{ $barang->kode_barang }}</span> (<span class="font-bold text-slate-800">{{ $barang->nama_barang }}</span>) akan disimpan ke sistem.
                </p>

                <div class="mt-6 flex items-center justify-center gap-3">
                    <button type="button" 
                            @click="confirmModal = false" 
                            class="flex-1 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-xs sm:text-sm transition">
                        Batal
                    </button>
                    <button type="button" 
                            @click="document.getElementById('editBarangForm').submit()" 
                            class="flex-1 px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-xl text-xs sm:text-sm shadow-xs transition active:scale-95">
                        Ya, Simpan
                    </button>
                </div>
            </div>
        </div>
    </template>

</div>

@push('scripts')
<script>
function editBarangHandler() {
    const rawInitHarga = @json(old('harga', $barang->harga ? (string)(int)$barang->harga : ''));
    const initialValues = {
        nama_barang: @json(old('nama_barang', $barang->nama_barang ?? '')),
        kategori: @json(old('kategori', $barang->kategori ?? '')),
        merk: @json(old('merk', $barang->merk ?? '')),
        nomor_seri: @json(old('nomor_seri', $barang->nomor_seri ?? '')),
        kondisi: @json(old('kondisi', $barang->kondisi ?? '')),
        lokasi: @json(old('lokasi', $barang->lokasi ?? '')),
        penanggung_jawab: @json(old('penanggung_jawab', $barang->penanggung_jawab ?? '')),
        laboratorium: @json(old('laboratorium', $barang->laboratorium ?? '')),
        jumlah: String(@json(old('jumlah', $barang->jumlah ?? ''))),
        satuan: @json(old('satuan', $barang->satuan ?? '')),
        tanggal_pembelian: @json(old('tanggal_pembelian', $barang->tanggal_pembelian ? \Carbon\Carbon::parse($barang->tanggal_pembelian)->format('Y-m-d') : '')),
        tahun_pembelian: String(@json(old('tahun_pembelian', $barang->tahun_pembelian ?? ''))),
        harga: rawInitHarga,
        sumber_dana: @json(old('sumber_dana', $barang->sumber_dana ?? '')),
        deskripsi: @json(old('deskripsi', $barang->deskripsi ?? ''))
    };

    return {
        confirmModal: false,
        imageUrl: @json($barang->foto ? asset('storage/'.$barang->foto) : ''),
        hasNewPhoto: false,
        initialSnapshot: JSON.stringify(initialValues),
        
        form: { ...initialValues },
        formattedHarga: '',

        init() {
            this.formattedHarga = this.formatRupiah(this.form.harga);
        },

        formatRupiah(val) {
            if (!val) return '';
            const numberString = val.toString().replace(/[^0-9]/g, '');
            if (!numberString) return '';
            return numberString.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        },

        updateHarga(e) {
            const clean = e.target.value.replace(/[^0-9]/g, '');
            this.form.harga = clean;
            this.formattedHarga = this.formatRupiah(clean);
        },

        fileChosen(event) {
            const files = event.target.files;
            if (!files.length) return;
            const reader = new FileReader();
            reader.readAsDataURL(files[0]);
            reader.onload = e => {
                this.imageUrl = e.target.result;
                this.hasNewPhoto = true;
            };
        },

        resetImage() {
            this.imageUrl = '';
            document.getElementById('foto').value = '';
            this.hasNewPhoto = true;
        },

        get isDirty() {
            if (this.hasNewPhoto) return true;
            const currentSnapshot = JSON.stringify(this.form);
            return currentSnapshot !== this.initialSnapshot;
        }
    };
}
</script>
@endpush
@endsection