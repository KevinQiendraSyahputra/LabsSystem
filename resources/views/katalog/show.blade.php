@extends('layouts.app')

@section('title', 'Form Pengajuan Pinjam — ' . $barang->nama_barang)
@section('page_title', 'Form Pengajuan Peminjaman Alat')
@section('page_subtitle', 'Lengkapi formulir peminjaman untuk alat laboratorium TKJ')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    {{-- Breadcrumb Navigation --}}
    <nav class="flex items-center gap-2 text-xs sm:text-sm text-slate-500 bg-white/60 backdrop-blur border border-slate-200/80 px-4 py-3 rounded-2xl shadow-sm">
        <a href="{{ route('dashboard') }}" class="hover:text-indigo-600 font-medium transition">Dashboard</a>
        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('katalog.index') }}" class="hover:text-indigo-600 font-medium transition">Katalog Alat</a>
        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-slate-800 font-bold truncate max-w-xs">{{ $barang->nama_barang }}</span>
    </nav>

    {{-- Main 2-Column Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">

        {{-- Left Column (4 cols): Summary Card Alat (Sticky) --}}
        <div class="lg:col-span-4 space-y-6 lg:sticky lg:top-6">
            <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-sm relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-50/50 rounded-full blur-2xl -z-0 pointer-events-none"></div>

                {{-- Image Container --}}
                <div class="w-full h-48 sm:h-56 bg-slate-50 rounded-2xl overflow-hidden mb-5 border border-slate-100 flex items-center justify-center relative">
                    @if($barang->foto)
                        <img src="{{ asset('storage/' . $barang->foto) }}" alt="{{ $barang->nama_barang }}" class="w-full h-full object-cover">
                    @else
                        <div class="flex flex-col items-center justify-center p-6 text-center text-slate-400">
                            <div class="w-14 h-14 bg-indigo-50 text-indigo-500 rounded-2xl flex items-center justify-center mb-2 shadow-inner">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            <span class="text-xs font-medium text-slate-400">Foto alat belum diunggah</span>
                        </div>
                    @endif
                </div>

                {{-- Header info --}}
                <div class="flex items-center justify-between gap-2 mb-2">
                    <span class="bg-indigo-50 text-indigo-700 font-extrabold text-[10px] uppercase tracking-wider px-3 py-1 rounded-full border border-indigo-100">
                        {{ $barang->kategori }}
                    </span>
                    <span class="font-mono text-xs font-bold text-slate-500 bg-slate-100 px-2.5 py-0.5 rounded-lg border border-slate-200/60">
                        {{ $barang->kode_barang }}
                    </span>
                </div>

                <h2 class="text-lg sm:text-xl font-extrabold text-slate-800 leading-snug mt-1">{{ $barang->nama_barang }}</h2>

                {{-- Detail Table --}}
                <div class="mt-5 pt-4 border-t border-slate-100 space-y-2.5 text-xs">
                    <div class="flex justify-between items-center py-1 border-b border-slate-50">
                        <span class="text-slate-400 font-medium">Merk:</span>
                        <span class="font-bold text-slate-700">{{ $barang->merk ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between items-center py-1 border-b border-slate-50">
                        <span class="text-slate-400 font-medium">Lokasi Lab:</span>
                        <span class="font-bold text-slate-700">{{ $barang->lokasi ?? 'Lab TKJ 1' }}</span>
                    </div>
                    <div class="flex justify-between items-center py-1">
                        <span class="text-slate-400 font-medium">Stok Siap Pinjam:</span>
                        <span class="font-black text-emerald-600 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-100">
                            {{ $barang->stok_tersedia }} {{ $barang->satuan }}
                        </span>
                    </div>
                </div>

                @if($barang->deskripsi)
                    <div class="mt-4 pt-4 border-t border-slate-100 text-xs text-slate-500 leading-relaxed bg-slate-50/80 p-3.5 rounded-2xl border border-slate-100">
                        <p class="font-bold text-slate-700 mb-1 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Spesifikasi / Catatan:
                        </p>
                        {{ $barang->deskripsi }}
                    </div>
                @endif
            </div>
        </div>

        {{-- Right Column (8 cols): Formulir Peminjaman --}}
        <div class="lg:col-span-8">
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm relative">
                
                {{-- Form Title --}}
                <div class="mb-6 pb-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h3 class="text-lg sm:text-xl font-extrabold text-slate-800">Formulir Peminjaman Alat</h3>
                        <p class="text-xs text-slate-400 mt-1">Isi rincian keperluan dan rencana tanggal pengembalian alat praktikum</p>
                    </div>
                    <span class="self-start sm:self-auto bg-indigo-50 text-indigo-700 text-xs font-bold px-3 py-1.5 rounded-xl border border-indigo-100 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                        Pengajuan Langsung
                    </span>
                </div>

                @if($errors->any())
                    <div class="mb-6 bg-rose-50 border border-rose-200 p-4 rounded-2xl">
                        <div class="flex items-center gap-2 text-rose-800 font-bold text-xs">
                            <svg class="w-4 h-4 text-rose-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Mohon perbaiki kesalahan berikut:</span>
                        </div>
                        <ul class="text-xs text-rose-700 mt-2 list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('katalog.pinjam', $barang->id) }}" method="POST" class="space-y-5">
                    @csrf

                    {{-- User Profile Card (Read Only) --}}
                    <div class="bg-gradient-to-r from-slate-50 to-indigo-50/30 p-4 sm:p-5 rounded-2xl border border-slate-200/80">
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-2">Informasi Pemohon Peminjaman</span>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                            <div>
                                <span class="text-slate-400 font-medium">Nama Peminjam:</span>
                                <p class="font-bold text-slate-800 mt-0.5 text-sm">{{ Auth::user()->name }}</p>
                            </div>
                            <div>
                                <span class="text-slate-400 font-medium">Kelas / Jabatan:</span>
                                <p class="font-bold text-slate-800 mt-0.5 text-sm">{{ Auth::user()->kelas_atau_jabatan ?? '-' }}</p>
                            </div>
                            <div>
                                <span class="text-slate-400 font-medium">Role Akun:</span>
                                <p class="font-extrabold text-indigo-600 capitalize mt-0.5 text-sm">{{ Auth::user()->role }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Pilih Unit Spesifik --}}
                    @if($barang->jumlah > 1)
                    @php
                        $scannedUnit = request()->query('unit');
                        $kondisiPerUnit = [];
                        if ($barang->kondisi_per_unit) {
                            $kondisiPerUnit = json_decode($barang->kondisi_per_unit, true) ?: [];
                        }
                        
                        $unitsData = [];
                        for($i = 1; $i <= $barang->jumlah; $i++) {
                            $unitKondisi = $kondisiPerUnit[$i] ?? $barang->kondisi;
                            $isBorrowed = in_array($i, $borrowedUnits ?? []);
                            $isDisabled = $unitKondisi !== 'Baik' || $isBorrowed;
                            
                            $statusText = 'Tersedia';
                            if ($unitKondisi !== 'Baik') {
                                $statusText = 'Kondisi ' . $unitKondisi;
                            } elseif ($isBorrowed) {
                                $statusText = 'Sedang Dipinjam';
                            }
                            
                            $unitsData[] = [
                                'id' => $i,
                                'kondisi' => $unitKondisi,
                                'statusText' => $statusText,
                                'disabled' => $isDisabled
                            ];
                        }
                    @endphp
                    <div class="space-y-1.5" x-data="{
                        open: false,
                        selectedUnits: [{{ $scannedUnit ? $scannedUnit : '' }}],
                        units: {{ json_encode($unitsData) }},
                        toggleUnit(id, disabled) {
                            if (disabled) return;
                            const index = this.selectedUnits.indexOf(id);
                            if (index > -1) {
                                this.selectedUnits.splice(index, 1);
                            } else {
                                this.selectedUnits.push(id);
                            }
                            this.selectedUnits.sort((a, b) => a - b);
                            this.syncQuantity();
                        },
                        syncQuantity() {
                            const input = document.getElementById('jumlah_pinjam');
                            const help = document.getElementById('jumlah_pinjam_help');
                            if (!input) return;
                            if (this.selectedUnits.length > 0) {
                                input.value = this.selectedUnits.length;
                                input.readOnly = true;
                                input.classList.add('bg-slate-100', 'text-slate-500');
                                if (help) help.textContent = 'Kunci Unit Terpilih: ' + this.selectedUnits.length + ' unit.';
                            } else {
                                input.value = 1;
                                input.readOnly = false;
                                input.classList.remove('bg-slate-100', 'text-slate-500');
                                if (help) help.textContent = 'Maksimal {{ $barang->stok_tersedia }} {{ $barang->satuan }}';
                            }
                        },
                        init() {
                            this.syncQuantity();
                        }
                    }">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Pilih Unit Spesifik <span class="text-slate-400 font-normal lowercase">(bisa memilih lebih dari 1)</span>
                        </label>

                        <input type="hidden" name="unit_index" :value="selectedUnits.join(',')">

                        <div class="relative">
                            <button type="button" @click="open = !open" 
                                    class="w-full bg-slate-50/80 border border-slate-300 rounded-2xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none flex items-center justify-between text-left cursor-pointer transition hover:bg-slate-100/60 shadow-sm">
                                <span class="text-slate-700 font-medium truncate" x-text="selectedUnits.length > 0 ? selectedUnits.length + ' Unit Terpilih (Unit ' + selectedUnits.join(', ') + ')' : '-- Semua Unit / Pilih Otomatis --'"></span>
                                <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <div x-show="open" @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                                 class="absolute w-full mt-2 bg-white border border-slate-200 rounded-2xl shadow-2xl z-50 max-h-64 overflow-y-auto divide-y divide-slate-100"
                                 style="display: none;">
                                <template x-for="unit in units" :key="unit.id">
                                    <div @click="toggleUnit(unit.id, unit.disabled)" 
                                         :class="{
                                             'bg-slate-50/70 text-slate-400 cursor-not-allowed opacity-60': unit.disabled,
                                             'hover:bg-indigo-50/50 cursor-pointer': !unit.disabled,
                                             'bg-indigo-50/30': selectedUnits.includes(unit.id) && !unit.disabled
                                         }"
                                         class="flex items-center justify-between px-4 py-3 text-xs transition-colors">
                                        <div class="flex items-center gap-3">
                                            <input type="checkbox" :checked="selectedUnits.includes(unit.id)" :disabled="unit.disabled"
                                                   class="w-4 h-4 rounded text-indigo-600 border-slate-300 focus:ring-indigo-500 cursor-pointer disabled:cursor-not-allowed">
                                            <span class="font-bold text-slate-800" :class="{'text-slate-400': unit.disabled}" x-text="'Unit ' + unit.id"></span>
                                            <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold" 
                                                  :class="{
                                                      'bg-emerald-100 text-emerald-700 border border-emerald-200': unit.kondisi === 'Baik',
                                                      'bg-amber-100 text-amber-700 border border-amber-200': unit.kondisi === 'Perawatan',
                                                      'bg-orange-100 text-orange-700 border border-orange-200': unit.kondisi === 'Perbaikan',
                                                      'bg-red-100 text-red-700 border border-red-200': unit.kondisi === 'Rusak Berat',
                                                      'bg-slate-100 text-slate-700 border border-slate-200': unit.kondisi === 'Hilang'
                                                  }" x-text="unit.kondisi"></span>
                                        </div>
                                        <span class="font-bold text-xs" 
                                              :class="{
                                                  'text-emerald-600': !unit.disabled,
                                                  'text-rose-600': unit.disabled && unit.statusText === 'Sedang Dipinjam',
                                                  'text-slate-400': unit.disabled && unit.statusText !== 'Sedang Dipinjam'
                                              }" x-text="unit.statusText"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                        @if($scannedUnit)
                            <p class="text-xs text-emerald-600 font-bold mt-1.5 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Membaca Hasil Scan QR: Unit {{ $scannedUnit }} (Otomatis Terpilih)
                            </p>
                        @endif
                    </div>
                    @endif

                    {{-- Input Grid --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        {{-- Jumlah Pinjam --}}
                        <div>
                            <label for="jumlah_pinjam" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Jumlah Unit Dipinjam <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" id="jumlah_pinjam" name="jumlah_pinjam" value="{{ old('jumlah_pinjam', 1) }}"
                                   min="1" max="{{ max(1, $barang->stok_tersedia) }}" required
                                   class="w-full bg-slate-50/80 border border-slate-300 rounded-2xl px-4 py-3 text-sm font-semibold focus:ring-2 focus:ring-indigo-500 focus:outline-none transition shadow-sm">
                            <p class="text-[11px] text-slate-400 mt-1" id="jumlah_pinjam_help">Maksimal {{ $barang->stok_tersedia }} {{ $barang->satuan }}</p>
                        </div>

                        {{-- No Kontak / WA --}}
                        <div>
                            <label for="kontak" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                WhatsApp / Kontak Aktif
                            </label>
                            <input type="text" id="kontak" name="kontak" value="{{ old('kontak', Auth::user()->telepon) }}"
                                   placeholder="Contoh: 081234567890"
                                   class="w-full bg-slate-50/80 border border-slate-300 rounded-2xl px-4 py-3 text-sm font-semibold focus:ring-2 focus:ring-indigo-500 focus:outline-none transition shadow-sm">
                            <p class="text-[11px] text-slate-400 mt-1">Untuk konfirmasi pengembalian alat</p>
                        </div>

                        {{-- Tanggal Pinjam --}}
                        <div>
                            <label for="tanggal_pinjam" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Tanggal Peminjaman <span class="text-rose-500">*</span>
                            </label>
                            <input type="date" id="tanggal_pinjam" name="tanggal_pinjam" value="{{ old('tanggal_pinjam', now()->toDateString()) }}" required
                                   class="w-full bg-slate-50/80 border border-slate-300 rounded-2xl px-4 py-3 text-sm font-semibold focus:ring-2 focus:ring-indigo-500 focus:outline-none transition shadow-sm">
                        </div>

                        {{-- Tanggal Janji Kembali --}}
                        <div>
                            <label for="tanggal_kembali_rencana" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Rencana Tanggal Pengembalian <span class="text-rose-500">*</span>
                            </label>
                            <input type="date" id="tanggal_kembali_rencana" name="tanggal_kembali_rencana" value="{{ old('tanggal_kembali_rencana', now()->addDays(1)->toDateString()) }}" required
                                   class="w-full bg-slate-50/80 border border-slate-300 rounded-2xl px-4 py-3 text-sm font-semibold focus:ring-2 focus:ring-indigo-500 focus:outline-none transition shadow-sm">
                        </div>
                    </div>

                    {{-- Keperluan --}}
                    <div>
                        <label for="keperluan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Keperluan Praktikum / Alasan Peminjaman <span class="text-rose-500">*</span>
                        </label>
                        <textarea id="keperluan" name="keperluan" rows="3" required
                                  placeholder="Contoh: Praktikum konfigurasi Routing Dinamik OSPF dan Hotspot Mikrotik..."
                                  class="w-full bg-slate-50/80 border border-slate-300 rounded-2xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none transition shadow-sm resize-none">{{ old('keperluan') }}</textarea>
                    </div>

                    {{-- Catatan Tambahan --}}
                    <div>
                        <label for="catatan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Catatan Tambahan <span class="text-slate-400 font-normal lowercase">(opsional)</span>
                        </label>
                        <input type="text" id="catatan" name="catatan" value="{{ old('catatan') }}"
                               placeholder="Contoh: Termasuk kabel adaptor daya & kabel console..."
                               class="w-full bg-slate-50/80 border border-slate-300 rounded-2xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none transition shadow-sm">
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                        <a href="{{ route('katalog.index') }}" class="px-5 py-3 border border-slate-300 text-slate-700 hover:bg-slate-50 font-bold rounded-2xl text-xs sm:text-sm transition">
                            Batal
                        </a>
                        <button type="submit" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-extrabold px-6 py-3 rounded-2xl text-xs sm:text-sm shadow-lg shadow-indigo-600/25 transition active:scale-95 whitespace-nowrap">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Ajukan Peminjaman</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection