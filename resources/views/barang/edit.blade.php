@extends('layouts.app')

@section('title', 'Edit Barang — ' . $barang->nama_barang)
@section('page_title', 'Edit Data Inventaris')
@section('page_subtitle', 'Perbarui detail spesifikasi, stok, dan foto barang laboratorium')

@section('content')
<div class="max-w-7xl mx-auto space-y-4 sm:space-y-6" 
     x-data="{
        ...imageViewer('{{ $barang->foto ? asset('storage/'.$barang->foto) : '' }}'),
        confirmModal: false
     }">

    {{-- Breadcrumb & Actions Bar --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4 no-print">
        <nav class="flex items-center space-x-2 text-xs text-slate-500">
            <a href="{{ route('barang.index') }}" class="hover:text-indigo-600">Inventaris Barang</a>
            <span>/</span>
            <a href="{{ route('barang.show', $barang->id) }}" class="hover:text-indigo-600 font-bold text-slate-700">{{ $barang->kode_barang }}</a>
            <span>/</span>
            <span class="text-slate-400">Edit</span>
        </nav>

        <div class="flex items-center gap-2">
            <a href="{{ route('barang.show', $barang->id) }}" class="bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 font-semibold px-3 sm:px-4 py-2 rounded-xl text-xs flex items-center gap-1.5 transition shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali
            </a>
        </div>
    </div>

    {{-- Header Banner Card --}}
    <div class="bg-white p-4 sm:p-6 rounded-2xl sm:rounded-3xl shadow-sm border border-slate-200/60 flex items-center gap-3.5">
        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-amber-50 text-amber-600 rounded-xl sm:rounded-2xl flex items-center justify-center flex-shrink-0 shadow-sm border border-amber-100/80">
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
        <div class="bg-red-50 border border-red-200 p-4 rounded-2xl shadow-sm">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-xl bg-red-100 text-red-600 flex items-center justify-center flex-shrink-0">
                    <svg class="h-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-xs sm:text-sm font-bold text-red-800">Terdapat error pada isian form:</h3>
                    <ul class="mt-1 text-xs text-red-700 list-disc list-inside space-y-0.5">
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
                <div class="bg-indigo-50/80 p-4 sm:p-5 rounded-2xl sm:rounded-3xl border border-indigo-100 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-[10px] font-bold text-indigo-600 uppercase tracking-wider block">Kode Barang</span>
                        <div class="text-lg sm:text-xl font-mono font-extrabold text-indigo-950 mt-0.5">{{ $barang->kode_barang }}</div>
                    </div>
                    <span class="text-[10px] font-bold bg-indigo-200/60 text-indigo-800 px-2 py-0.5 rounded-lg uppercase">Read only</span>
                </div>

                <!-- Foto Barang Card -->
                <div class="bg-white p-4 sm:p-5 rounded-2xl sm:rounded-3xl shadow-sm border border-slate-200/60">
                    <h2 class="text-xs sm:text-sm font-bold text-slate-800 uppercase tracking-wider mb-3 pb-2 border-b border-slate-100">Foto Alat Unit</h2>
                    
                    <div class="flex flex-col items-center justify-center w-full">
                        <label for="foto" class="flex flex-col items-center justify-center w-full h-48 sm:h-60 border-2 border-slate-200 border-dashed rounded-2xl cursor-pointer bg-slate-50 hover:bg-slate-100 relative overflow-hidden group transition">
                            
                            <template x-if="!imageUrl">
                                <div class="flex flex-col items-center justify-center p-4 text-center">
                                    <div class="w-10 h-10 rounded-xl bg-white text-slate-400 border border-slate-200 flex items-center justify-center mb-2 shadow-sm">
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
                            <div class="mt-2.5 text-center">
                                <button type="button" @click="imageUrl = ''; document.getElementById('foto').value = '';" class="text-xs text-rose-600 hover:text-rose-800 font-bold flex items-center gap-1 mx-auto">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    Hapus & Pilih Ulang
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Right Col - Form Fields --}}
            <div class="lg:col-span-2 space-y-4 sm:space-y-6">
                
                {{-- Section 1: Identitas --}}
                <div class="bg-white p-4 sm:p-6 lg:p-7 rounded-2xl sm:rounded-3xl shadow-sm border border-slate-200/60">
                    <h2 class="text-xs sm:text-sm font-bold text-slate-800 mb-4 pb-2 border-b border-slate-100 uppercase tracking-wider">Identitas Barang</h2>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 sm:gap-5">
                        <div class="sm:col-span-2">
                            <label for="nama_barang" class="block mb-1.5 text-xs font-bold text-slate-700">Nama Barang <span class="text-rose-500">*</span></label>
                            <input type="text" id="nama_barang" name="nama_barang" value="{{ old('nama_barang', $barang->nama_barang) }}" class="bg-slate-50 border border-slate-200 text-slate-800 text-xs sm:text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none block w-full px-3 py-2.5" required>
                        </div>
                        
                        <!-- Animated Dropdown: Kategori -->
                        <div class="relative" x-data="{ open: false, selected: '{{ old('kategori', $barang->kategori) }}' }">
                            <label class="block mb-1.5 text-xs font-bold text-slate-700">Kategori <span class="text-rose-500">*</span></label>
                            <input type="hidden" name="kategori" :value="selected" required>

                            <button type="button" @click="open = !open" @click.outside="open = false" 
                                    class="bg-slate-50 border border-slate-200 text-slate-800 text-xs sm:text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none flex items-center justify-between w-full px-3 py-2.5 text-left transition">
                                <span x-text="selected ? selected : 'Pilih Kategori'" :class="{'text-slate-400': !selected}"></span>
                                <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
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
                                @foreach(['Jaringan', 'Komputer', 'Perangkat Keras', 'Alat Praktik', 'Furniture', 'Lainnya'] as $kat)
                                    <button type="button" @click="selected = '{{ $kat }}'; open = false" 
                                            class="w-full text-left px-3.5 py-2 text-xs sm:text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 transition flex items-center justify-between"
                                            :class="{'font-bold text-indigo-600 bg-indigo-50/50': selected === '{{ $kat }}'}">
                                        <span>{{ $kat }}</span>
                                        <svg x-show="selected === '{{ $kat }}'" class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <label for="merk" class="block mb-1.5 text-xs font-bold text-slate-700">Merk / Brand</label>
                            <input type="text" id="merk" name="merk" value="{{ old('merk', $barang->merk) }}" class="bg-slate-50 border border-slate-200 text-slate-800 text-xs sm:text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none block w-full px-3 py-2.5">
                        </div>

                        <div class="sm:col-span-2">
                            <label for="nomor_seri" class="block mb-1.5 text-xs font-bold text-slate-700">Nomor Seri / S/N</label>
                            <input type="text" id="nomor_seri" name="nomor_seri" value="{{ old('nomor_seri', $barang->nomor_seri) }}" class="bg-slate-50 border border-slate-200 text-slate-800 text-xs sm:text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none block w-full px-3 py-2.5 font-mono">
                        </div>
                    </div>
                </div>

                {{-- Section 2: Kondisi & Lokasi --}}
                <div class="bg-white p-4 sm:p-6 lg:p-7 rounded-2xl sm:rounded-3xl shadow-sm border border-slate-200/60">
                    <h2 class="text-xs sm:text-sm font-bold text-slate-800 mb-4 pb-2 border-b border-slate-100 uppercase tracking-wider">Kondisi & Lokasi</h2>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 sm:gap-5">
                        <div class="sm:col-span-2">
                            <label for="kondisi" class="block mb-2 text-xs font-bold text-slate-700">Kondisi Utama Alat <span class="text-rose-500">*</span></label>
                            <div class="grid grid-cols-2 sm:grid-cols-5 gap-2">
                                @foreach([
                                    'Baik' => 'emerald',
                                    'Perawatan' => 'yellow',
                                    'Perbaikan' => 'orange',
                                    'Rusak Berat' => 'red',
                                    'Hilang' => 'slate'
                                ] as $k => $color)
                                <label for="kondisi_{{ $k }}" class="flex items-center gap-2 p-2 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-slate-100/80 cursor-pointer transition">
                                    <input id="kondisi_{{ $k }}" type="radio" value="{{ $k }}" name="kondisi" class="w-3.5 h-3.5 text-indigo-600 focus:ring-indigo-500 border-slate-300" {{ old('kondisi', $barang->kondisi) == $k ? 'checked' : '' }} required>
                                    <span class="text-xs font-medium text-slate-700 truncate">{{ $k }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <label for="lokasi" class="block mb-1.5 text-xs font-bold text-slate-700">Lokasi / Ruangan <span class="text-rose-500">*</span></label>
                            <input type="text" id="lokasi" name="lokasi" value="{{ old('lokasi', $barang->lokasi) }}" class="bg-slate-50 border border-slate-200 text-slate-800 text-xs sm:text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none block w-full px-3 py-2.5" required>
                        </div>

                        <div>
                            <label for="penanggung_jawab" class="block mb-1.5 text-xs font-bold text-slate-700">Penanggung Jawab</label>
                            <input type="text" id="penanggung_jawab" name="penanggung_jawab" value="{{ old('penanggung_jawab', $barang->penanggung_jawab) }}" class="bg-slate-50 border border-slate-200 text-slate-800 text-xs sm:text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none block w-full px-3 py-2.5">
                        </div>

                        {{-- *** FIELD LABORATORIUM *** --}}
                        <div class="sm:col-span-2">
                            <div class="relative" x-data="{ open: false, selected: '{{ old('laboratorium', $barang->laboratorium) }}' }" @click.outside="open = false">
                                <label class="block mb-1.5 text-xs font-bold text-slate-700">Pilih Laboratorium</label>
                                <input type="hidden" name="laboratorium" :value="selected">
                                <button type="button" @click="open = !open"
                                        class="bg-slate-50 border border-slate-200 text-slate-800 text-xs sm:text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none flex items-center justify-between w-full px-3 py-2.5 text-left transition hover:bg-slate-100/70"
                                        :class="{'border-indigo-500 ring-2 ring-indigo-500/20': open}">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                        <span :class="selected ? 'text-slate-800 font-bold' : 'text-slate-400'" x-text="selected || '-- Pilih Laboratorium (Opsional) --'"></span>
                                    </div>
                                    <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{'rotate-180 text-indigo-600': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div x-show="open"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                     x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                                     class="absolute z-50 mt-1.5 w-full bg-white border border-slate-200 rounded-2xl shadow-xl py-1.5 overflow-hidden"
                                     style="display: none;">
                                    <button type="button" @click="selected = ''; open = false"
                                            class="w-full text-left px-3.5 py-2.5 text-xs sm:text-sm text-slate-400 hover:bg-slate-50 transition flex items-center justify-between"
                                            :class="{'font-medium bg-slate-50/50 text-slate-600': selected === ''}">
                                        <span>-- Tidak Dikaitkan / Umum --</span>
                                        <svg x-show="selected === ''" class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </button>
                                    @foreach(\App\Models\Barang::$laboratoriumList as $lab)
                                        <button type="button" @click="selected = '{{ $lab }}'; open = false"
                                                class="w-full text-left px-3.5 py-2.5 text-xs sm:text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 transition flex items-center justify-between"
                                                :class="{'font-bold text-indigo-600 bg-indigo-50/50': selected === '{{ $lab }}'}">
                                            <span class="flex items-center gap-2">
                                                <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                                                {{ $lab }}
                                            </span>
                                            <svg x-show="selected === '{{ $lab }}'" class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        </button>
                                    @endforeach
                                </div>
                                <p class="text-[11px] text-slate-400 mt-1">Pilih laboratorium tempat unit barang ini ditempatkan</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section 3: Stok & Satuan --}}
                <div class="bg-white p-4 sm:p-6 lg:p-7 rounded-2xl sm:rounded-3xl shadow-sm border border-slate-200/60">
                    <h2 class="text-xs sm:text-sm font-bold text-slate-800 mb-4 pb-2 border-b border-slate-100 uppercase tracking-wider">Stok & Satuan</h2>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 sm:gap-5">
                        <div>
                            <label for="jumlah" class="block mb-1.5 text-xs font-bold text-slate-700">Jumlah Stok <span class="text-rose-500">*</span></label>
                            <input type="number" id="jumlah" name="jumlah" value="{{ old('jumlah', $barang->jumlah) }}" min="0" class="bg-slate-50 border border-slate-200 text-slate-800 text-xs sm:text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none block w-full px-3 py-2.5" required>
                        </div>

                        <!-- Animated Dropdown: Satuan -->
                        <div class="relative" x-data="{ open: false, selected: '{{ old('satuan', $barang->satuan) }}' }">
                            <label class="block mb-1.5 text-xs font-bold text-slate-700">Satuan <span class="text-rose-500">*</span></label>
                            <input type="hidden" name="satuan" :value="selected" required>

                            <button type="button" @click="open = !open" @click.outside="open = false" 
                                    class="bg-slate-50 border border-slate-200 text-slate-800 text-xs sm:text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none flex items-center justify-between w-full px-3 py-2.5 text-left transition">
                                <span x-text="selected ? selected : 'Pilih Satuan'" :class="{'text-slate-400': !selected}"></span>
                                <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
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
                                @foreach(['Unit', 'Meter', 'Box', 'Set', 'Buah', 'Lembar', 'Paket'] as $satuan)
                                    <button type="button" @click="selected = '{{ $satuan }}'; open = false" 
                                            class="w-full text-left px-3.5 py-2 text-xs sm:text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 transition flex items-center justify-between"
                                            :class="{'font-bold text-indigo-600 bg-indigo-50/50': selected === '{{ $satuan }}'}">
                                        <span>{{ $satuan }}</span>
                                        <svg x-show="selected === '{{ $satuan }}'" class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section 4: Pembelian & Finansial --}}
                <div class="bg-white p-4 sm:p-6 lg:p-7 rounded-2xl sm:rounded-3xl shadow-sm border border-slate-200/60">
                    <h2 class="text-xs sm:text-sm font-bold text-slate-800 mb-4 pb-2 border-b border-slate-100 uppercase tracking-wider">Data Pengadaan (Opsional)</h2>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 sm:gap-5">
                        <div>
                            <label for="tanggal_pembelian" class="block mb-1.5 text-xs font-bold text-slate-700">Tanggal Pembelian</label>
                            <input type="date" id="tanggal_pembelian" name="tanggal_pembelian" value="{{ old('tanggal_pembelian', $barang->tanggal_pembelian) }}" class="bg-slate-50 border border-slate-200 text-slate-800 text-xs sm:text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none block w-full px-3 py-2.5">
                        </div>

                        <div>
                            <label for="tahun_pembelian" class="block mb-1.5 text-xs font-bold text-slate-700">Tahun Pembelian</label>
                            <input type="number" id="tahun_pembelian" name="tahun_pembelian" value="{{ old('tahun_pembelian', $barang->tahun_pembelian) }}" min="1990" max="{{ date('Y') }}" class="bg-slate-50 border border-slate-200 text-slate-800 text-xs sm:text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none block w-full px-3 py-2.5">
                        </div>

                        <div x-data="{
                            rawHarga: '{{ old('harga', $barang->harga ? (int)$barang->harga : '') }}',
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
                            <label for="harga_display" class="block mb-1.5 text-xs font-bold text-slate-700">Harga Satuan (Rp)</label>
                            <input type="hidden" name="harga" :value="rawHarga">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <span class="text-slate-400 text-xs font-bold">Rp</span>
                                </div>
                                <input type="text" 
                                       id="harga_display" 
                                       x-model="formattedHarga" 
                                       @input="updateHarga($event)"
                                       placeholder="Contoh: 450.000" 
                                       class="bg-slate-50 border border-slate-200 text-slate-800 text-xs sm:text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none block w-full pl-9 pr-3 py-2.5 font-medium">
                            </div>
                        </div>

                        <!-- Animated Dropdown: Sumber Dana -->
                        <div class="relative" x-data="{ open: false, selected: '{{ old('sumber_dana', $barang->sumber_dana) }}' }">
                            <label class="block mb-1.5 text-xs font-bold text-slate-700">Sumber Dana</label>
                            <input type="hidden" name="sumber_dana" :value="selected">

                            <button type="button" @click="open = !open" @click.outside="open = false" 
                                    class="bg-slate-50 border border-slate-200 text-slate-800 text-xs sm:text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none flex items-center justify-between w-full px-3 py-2.5 text-left transition">
                                <span x-text="selected ? selected : 'Pilih Sumber Dana'" :class="{'text-slate-400': !selected}"></span>
                                <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
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
                                @foreach(['BOS', 'Sekolah', 'Hibah', 'Donasi', 'APBN', 'Lainnya'] as $sumber)
                                    <button type="button" @click="selected = '{{ $sumber }}'; open = false" 
                                            class="w-full text-left px-3.5 py-2 text-xs sm:text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 transition flex items-center justify-between"
                                            :class="{'font-bold text-indigo-600 bg-indigo-50/50': selected === '{{ $sumber }}'}">
                                        <span>{{ $sumber }}</span>
                                        <svg x-show="selected === '{{ $sumber }}'" class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section 5: Deskripsi --}}
                <div class="bg-white p-4 sm:p-6 lg:p-7 rounded-2xl sm:rounded-3xl shadow-sm border border-slate-200/60">
                    <h2 class="text-xs sm:text-sm font-bold text-slate-800 mb-3 pb-2 border-b border-slate-100 uppercase tracking-wider">Deskripsi / Catatan Alat</h2>
                    <div>
                        <textarea id="deskripsi" name="deskripsi" rows="3" placeholder="Tambahkan spesifikasi rinci atau catatan..." class="block p-3 w-full text-xs sm:text-sm text-slate-800 bg-slate-50 rounded-xl sm:rounded-2xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none">{{ old('deskripsi', $barang->deskripsi) }}</textarea>
                    </div>
                </div>

                {{-- Action Trigger Button --}}
                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('barang.show', $barang->id) }}" class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 text-xs font-bold transition">
                        Batal
                    </a>
                    <button type="button" 
                            @click="confirmModal = true"
                            class="bg-amber-500 hover:bg-amber-600 text-white font-bold px-5 py-2.5 rounded-xl text-xs flex items-center gap-1.5 shadow-md shadow-amber-500/20 transition active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    </form>

    {{-- MODAL KONFIRMASI SIMPAN PERUBAHAN (TEPAT DI TENGAH LAYAR) --}}
    <template x-teleport="body">
        <div x-show="confirmModal" 
             class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm" 
             style="display: none;"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            <div @click.away="confirmModal = false" 
                 class="bg-white rounded-3xl p-6 sm:p-7 w-full max-w-sm sm:max-w-md shadow-2xl border border-slate-100 text-center"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-90 translate-y-3"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-90 translate-y-3">
                
                {{-- Icon Alert --}}
                <div class="w-14 h-14 bg-amber-100 text-amber-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-inner border border-amber-200/60">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>

                {{-- Alert Text --}}
                <h3 class="text-base sm:text-lg font-extrabold text-slate-800">Simpan Perubahan Barang?</h3>
                <p class="text-xs sm:text-sm text-slate-500 mt-1.5 leading-relaxed">
                    Apakah Anda yakin ingin memperbarui data untuk inventaris <span class="font-bold text-slate-800 font-mono">{{ $barang->kode_barang }}</span> (<span class="font-bold text-slate-800">{{ $barang->nama_barang }}</span>)?
                </p>

                {{-- Actions --}}
                <div class="mt-6 flex items-center justify-center gap-3">
                    <button type="button" 
                            @click="confirmModal = false" 
                            class="flex-1 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-xs sm:text-sm transition">
                        Batal
                    </button>
                    <button type="button" 
                            @click="document.getElementById('editBarangForm').submit()" 
                            class="flex-1 px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-xl text-xs sm:text-sm shadow-md shadow-amber-500/20 transition active:scale-95">
                        Ya, Simpan
                    </button>
                </div>
            </div>
        </div>
    </template>

</div>

<script>
    function imageViewer(initialUrl = '') {
        return {
            imageUrl: initialUrl,

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