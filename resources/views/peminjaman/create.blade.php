@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-4 sm:space-y-6" x-data="peminjamanCreate()">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Catat Peminjaman</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Catat data peminjaman peralatan laboratorium TKJ</p>
        </div>
        <a href="{{ route('peminjaman.index') }}" class="bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 font-bold px-3.5 py-2 rounded-xl text-xs transition shadow-sm">
            Kembali
        </a>
    </div>

    <div class="bg-white shadow-sm border border-slate-200/60 rounded-2xl sm:rounded-3xl p-5 sm:p-8">
        <form action="{{ route('peminjaman.store') }}" method="POST" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 gap-y-4 sm:grid-cols-2 sm:gap-x-6">
                
                {{-- 1. Dropdown Custom: Pilih Barang --}}
                <div class="sm:col-span-2 relative" @click.outside="closeIf('barang')">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Barang <span class="text-rose-500">*</span></label>
                    <input type="hidden" name="barang_id" :value="selectedBarangId" required>

                    <button type="button" @click="toggle('barang')"
                            class="w-full bg-slate-50/70 border border-slate-200 text-slate-800 text-xs sm:text-sm rounded-xl px-3.5 py-2.5 flex items-center justify-between text-left focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                        <span x-text="currentBarangLabel" :class="{'font-bold text-indigo-900': selectedBarangId, 'text-slate-400': !selectedBarangId}">-- Pilih Barang --</span>
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
                        <template x-for="b in barangsList" :key="b.id">
                            <button type="button" @click="selectBarang(b.id)"
                                    class="w-full text-left px-3.5 py-2.5 text-xs sm:text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 transition flex items-center justify-between"
                                    :class="{'font-bold text-indigo-600 bg-indigo-50/50': selectedBarangId == b.id}">
                                <div>
                                    <div x-text="b.nama" class="font-bold text-slate-800"></div>
                                    <div class="text-[11px] text-slate-400 font-mono" x-text="`${b.kode} • Stok Tersedia: ${b.stok_tersedia} ${b.satuan}`"></div>
                                </div>
                                <svg x-show="selectedBarangId == b.id" class="w-4 h-4 text-indigo-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </button>
                        </template>
                    </div>
                    @error('barang_id')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 2. Dropdown Multi-Select: Target Unit Barang (Hanya Muncul Jika Multi-Unit) --}}
                <div class="sm:col-span-2 relative" x-show="currentBarang && currentBarang.jumlah > 1" @click.outside="closeIf('unit')">
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Pilih Unit yang Dipinjam</label>
                        <span class="text-[10px] text-indigo-600 font-semibold" x-text="`${selectedUnits.length} unit dipilih`"></span>
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
                            <span class="text-[11px] font-bold text-slate-400">Pilih unit (Kondisi Baik):</span>
                            <button type="button" @click.stop="resetUnits()" class="text-[10px] font-bold text-rose-600 hover:underline">Reset</button>
                        </div>

                        <div class="max-h-48 overflow-y-auto space-y-1 pr-1">
                            <template x-for="u in unitsAvailable" :key="u.index">
                                <label class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs sm:text-sm hover:bg-indigo-50 cursor-pointer transition select-none"
                                       :class="{'bg-indigo-50/80 font-bold text-indigo-900': selectedUnits.includes(String(u.index))}">
                                    <input type="checkbox" :value="String(u.index)" x-model="selectedUnits" @change="syncJumlahWithUnits()" @click.stop
                                           class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                                    <div class="flex items-center gap-2 flex-1">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 flex-shrink-0"></span>
                                        <span x-text="`Unit ${u.index} (${currentBarang.kode}-${u.index})`"></span>
                                    </div>
                                </label>
                            </template>
                        </div>

                        <div x-show="unitsAvailable.length === 0" class="px-3 py-3 text-center text-xs text-slate-400">
                            Tidak ada unit berkondisi Baik yang tersedia untuk dipinjam.
                        </div>
                    </div>
                </div>

                {{-- Nama Peminjam --}}
                <div class="sm:col-span-2">
                    <label for="nama_peminjam" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Peminjam <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_peminjam" id="nama_peminjam" value="{{ old('nama_peminjam') }}" required class="bg-slate-50/70 border border-slate-200 text-slate-800 text-xs sm:text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none block w-full px-3.5 py-2.5" placeholder="Nama lengkap siswa atau guru">
                    @error('nama_peminjam')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Kelas / Jabatan --}}
                <div class="sm:col-span-2">
                    <label for="kelas_atau_jabatan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Kelas atau Jabatan</label>
                    <input type="text" name="kelas_atau_jabatan" id="kelas_atau_jabatan" value="{{ old('kelas_atau_jabatan') }}" class="bg-slate-50/70 border border-slate-200 text-slate-800 text-xs sm:text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none block w-full px-3.5 py-2.5" placeholder="Contoh: XII TKJ 1 / Guru Kejuruan TKJ">
                    @error('kelas_atau_jabatan')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Keperluan --}}
                <div class="sm:col-span-2">
                    <label for="keperluan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Keperluan <span class="text-rose-500">*</span></label>
                    <textarea id="keperluan" name="keperluan" rows="3" required class="bg-slate-50/70 border border-slate-200 text-slate-800 text-xs sm:text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none block w-full p-3" placeholder="Jelaskan kebutuhan peminjaman alat untuk praktikum atau tugas...">{{ old('keperluan') }}</textarea>
                    @error('keperluan')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Jumlah Pinjam --}}
                <div>
                    <label for="jumlah_pinjam" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Jumlah Pinjam <span class="text-rose-500">*</span></label>
                    <input type="number" name="jumlah_pinjam" id="jumlah_pinjam" x-model="jumlahPinjam" min="1" :max="maxPinjam" required 
                           :readonly="currentBarang && currentBarang.jumlah > 1 && selectedUnits.length > 0"
                           class="bg-slate-50/70 border border-slate-200 text-slate-800 text-xs sm:text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none block w-full px-3.5 py-2.5 font-mono"
                           :class="{'bg-slate-100 cursor-not-allowed': currentBarang && currentBarang.jumlah > 1 && selectedUnits.length > 0}">
                    <span class="text-[10px] text-slate-400 mt-1 block" x-text="currentBarang ? `Maksimal: ${maxPinjam} ${currentBarang.satuan}` : ''"></span>
                    @error('jumlah_pinjam')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tanggal Pinjam --}}
                <div>
                    <label for="tanggal_pinjam" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tanggal Pinjam <span class="text-rose-500">*</span></label>
                    <input type="date" name="tanggal_pinjam" id="tanggal_pinjam" value="{{ old('tanggal_pinjam', date('Y-m-d')) }}" required class="bg-slate-50/70 border border-slate-200 text-slate-800 text-xs sm:text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none block w-full px-3.5 py-2.5">
                    @error('tanggal_pinjam')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tanggal Rencana Kembali --}}
                <div class="sm:col-span-2">
                    <label for="tanggal_kembali_rencana" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Rencana Tanggal Pengembalian <span class="text-rose-500">*</span></label>
                    <input type="date" name="tanggal_kembali_rencana" id="tanggal_kembali_rencana" value="{{ old('tanggal_kembali_rencana') }}" required class="bg-slate-50/70 border border-slate-200 text-slate-800 text-xs sm:text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none block w-full px-3.5 py-2.5">
                    @error('tanggal_kembali_rencana')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Catatan Tambahan --}}
                <div class="sm:col-span-2">
                    <label for="catatan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Catatan Tambahan</label>
                    <textarea id="catatan" name="catatan" rows="2" class="bg-slate-50/70 border border-slate-200 text-slate-800 text-xs sm:text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none block w-full p-3" placeholder="Informasi tambahan terkait peminjaman barang...">{{ old('catatan') }}</textarea>
                    @error('catatan')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <a href="{{ route('peminjaman.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 text-xs font-bold transition">
                    Batal
                </a>
                <button type="submit" class="inline-flex justify-center items-center gap-1.5 px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-md shadow-indigo-600/20 transition active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Catat Peminjaman
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function peminjamanCreate() {
    return {
        openDropdown: null,
        selectedBarangId: @json(old('barang_id', request('barang_id', ''))),
        selectedUnits: @json(old('unit_index') ? (is_array(old('unit_index')) ? old('unit_index') : explode(',', old('unit_index'))) : (request('unit') ? [request('unit')] : [])),
        jumlahPinjam: @json(old('jumlah_pinjam', 1)),

        barangsList: [
            @foreach($barangs as $b)
            {
                id: '{{ $b->id }}',
                nama: @json($b->nama_barang),
                kode: @json($b->kode_barang),
                jumlah: {{ (int) $b->jumlah }},
                satuan: @json($b->satuan ?? 'Unit'),
                kondisi: @json($b->kondisi),
                stok_tersedia: {{ (int) ($b->stok_tersedia ?? $b->jumlah) }},
                kondisi_per_unit: {!! $b->kondisi_per_unit ?: '{}' !!}
            },
            @endforeach
        ],

        get currentBarang() {
            return this.barangsList.find(b => String(b.id) === String(this.selectedBarangId)) || null;
        },

        get currentBarangLabel() {
            if (!this.currentBarang) return '-- Pilih Barang --';
            return `${this.currentBarang.nama} (${this.currentBarang.kode}) - Stok Tersedia: ${this.currentBarang.stok_tersedia} ${this.currentBarang.satuan}`;
        },

        get maxPinjam() {
            return this.currentBarang ? Math.max(1, this.currentBarang.stok_tersedia) : 1;
        },

        get selectedUnitsString() {
            return this.selectedUnits.join(',');
        },

        get unitsAvailable() {
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
            if (this.selectedUnits.length === 0) return '-- Pilih Unit Spesifik --';
            const sorted = [...this.selectedUnits].map(Number).sort((a, b) => a - b);
            return `Unit ${sorted.join(', ')} (${sorted.length} unit dipilih)`;
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
            this.jumlahPinjam = 1;
            this.openDropdown = null;
        },

        syncJumlahWithUnits() {
            if (this.selectedUnits.length > 0) {
                this.jumlahPinjam = this.selectedUnits.length;
            } else {
                this.jumlahPinjam = 1;
            }
        },

        resetUnits() {
            this.selectedUnits = [];
            this.jumlahPinjam = 1;
        }
    };
}
</script>
@endpush
@endsection