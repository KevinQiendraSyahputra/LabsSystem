@extends('layouts.app')

@section('title', 'Edit Berita — ' . $berita->judul)
@section('page_title', 'Edit Berita / Pengumuman')
@section('page_subtitle', 'Perbarui detail dan sasaran penerima berita')

@section('content')

@php
    $daftarTargetKelas = [
        'Semua Pengguna (Umum / Publik)',
        'X TKJ 1',
        'X TKJ 2',
        'XI TKJ 1',
        'XI TKJ 2',
        'XII TKJ 1',
        'XII TKJ 2',
    ];
@endphp

<div class="w-full flex justify-center py-4">
    <div class="w-full max-w-3xl bg-white rounded-2xl sm:rounded-3xl shadow-sm border border-slate-200/70 overflow-hidden">
        <div class="px-5 py-2.5 sm:py-3 border-b border-slate-800 bg-slate-900 text-white">
            <h2 class="text-sm sm:text-base font-bold text-white uppercase tracking-wider">Form Edit Berita / Pengumuman</h2>
            <p class="text-xs text-slate-300 mt-0.5">Perbarui detail, isi pesan, atau sasaran kelas penerima</p>
        </div>
        <div class="p-6 sm:p-8">
        
        {{-- Breadcrumb --}}
        <div class="mb-6 flex items-center gap-2 text-sm text-slate-500 pb-3 border-b border-slate-100">
            <a href="{{ route('berita.index') }}" class="hover:text-indigo-600 transition-colors">Berita</a>
            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-slate-800 font-semibold">Edit Berita</span>
        </div>

        <form action="{{ route('berita.update', $berita->id) }}" method="POST"
              x-data="{
                  initialJudul: @js(old('judul', $berita->judul)),
                  initialTarget: @js(old('target_kelas', $berita->target_kelas ?? 'Semua Pengguna (Umum / Publik)')),
                  initialIsi: @js(old('isi', $berita->isi)),
                  judul: @js(old('judul', $berita->judul)),
                  selectedTarget: @js(old('target_kelas', $berita->target_kelas ?? 'Semua Pengguna (Umum / Publik)')),
                  isi: @js(old('isi', $berita->isi)),
                  openTarget: false,
                  isSubmitting: false,

                  get hasChanges() {
                      return this.judul.trim() !== this.initialJudul.trim() ||
                             this.selectedTarget !== this.initialTarget ||
                             this.isi.trim() !== this.initialIsi.trim();
                  },

                  handleSubmit(e) {
                      if (!this.hasChanges || this.isSubmitting) {
                          e.preventDefault();
                          return;
                      }
                      this.isSubmitting = true;
                  }
              }"
              @submit="handleSubmit($event)"
              class="space-y-5">
            @csrf
            @method('PUT')

            {{-- Judul Berita --}}
            <div>
                <label for="judul" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Judul Pengumuman <span class="text-red-500">*</span></label>
                <input type="text" id="judul" name="judul" x-model="judul" required
                       placeholder="Masukkan judul pengumuman yang menarik..."
                       class="w-full bg-slate-50 border @error('judul') border-red-400 bg-red-50 @else border-slate-300 @enderror rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white focus:border-transparent transition">
                @error('judul')
                    <p class="mt-1.5 text-xs text-red-500 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Target Kelas Sasaran (Animated Dropdown) --}}
            <div class="relative" @click.outside="openTarget = false">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Ditujukan Untuk (Target Kelas)</label>
                <input type="hidden" name="target_kelas" :value="selectedTarget">

                <button type="button" @click="openTarget = !openTarget"
                        class="w-full flex items-center justify-between bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm font-medium transition focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        :class="{'border-indigo-500 ring-2 ring-indigo-500/20 bg-white': openTarget}">
                    <span class="truncate text-slate-800 font-semibold" x-text="selectedTarget"></span>
                    <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{'rotate-180 text-indigo-600': openTarget}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="openTarget"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                     class="absolute z-30 w-full mt-1.5 bg-white rounded-xl shadow-xl border border-slate-200/80 py-1 max-h-56 overflow-y-auto"
                     style="display: none;">
                    @foreach($daftarTargetKelas as $target)
                        <button type="button" @click="selectedTarget = '{{ $target }}'; openTarget = false"
                                class="w-full text-left px-4 py-2.5 text-xs font-semibold flex items-center justify-between transition hover:bg-indigo-50 hover:text-indigo-600"
                                :class="{'bg-indigo-50/70 text-indigo-600': selectedTarget === '{{ $target }}', 'text-slate-700': selectedTarget !== '{{ $target }}'}">
                            <span>{{ $target }}</span>
                            <svg x-show="selectedTarget === '{{ $target }}'" class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Isi Berita --}}
            <div>
                <label for="isi" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Isi Berita / Pengumuman <span class="text-red-500">*</span></label>
                <textarea id="isi" name="isi" x-model="isi" rows="8" required
                          placeholder="Tuliskan detail pengumuman di sini..."
                          class="w-full bg-slate-50 border @error('isi') border-red-400 bg-red-50 @else border-slate-300 @enderror rounded-xl p-4 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white focus:border-transparent transition resize-none leading-relaxed"></textarea>
                @error('isi')
                    <p class="mt-1.5 text-xs text-red-500 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                <button type="submit"
                        :disabled="!hasChanges || isSubmitting"
                        class="font-bold px-6 py-2.5 rounded-xl text-sm transition-all duration-150 flex items-center justify-center gap-2"
                        :class="hasChanges && !isSubmitting ? 'bg-amber-500 hover:bg-amber-600 text-white cursor-pointer shadow-xs active:scale-95' : 'bg-slate-200 text-slate-400 border border-slate-300 cursor-not-allowed pointer-events-none'">
                    <span x-show="isSubmitting" class="inline-flex items-center gap-2" style="display: none;">
                        <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Menyimpan...</span>
                    </span>
                    <span x-show="!isSubmitting">Simpan Perubahan</span>
                </button>
                <a href="{{ route('berita.index') }}"
                   class="px-5 py-2.5 border border-slate-300 text-slate-600 hover:bg-slate-50 font-semibold rounded-xl text-sm transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
    </div>
</div>

@endsection