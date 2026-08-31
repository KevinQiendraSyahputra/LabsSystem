@extends('layouts.app')

@section('title', $berita->judul)
@section('page_title', 'Detail Berita')
@section('page_subtitle', 'Membaca pengumuman')

@section('content')

<div class="max-w-4xl mx-auto space-y-4 sm:space-y-6" x-data="{ deleteModal: false }">
    {{-- Breadcrumb & Action Bar --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4 no-print">
        <nav class="flex items-center space-x-2 text-xs text-slate-500 min-w-0">
            <a href="{{ route('berita.index') }}" class="hover:text-indigo-600 font-semibold whitespace-nowrap">Berita</a>
            <svg class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-slate-800 font-bold truncate max-w-[220px] sm:max-w-xs md:max-w-md">{{ $berita->judul }}</span>
        </nav>

        @if($berita->user_id === Auth::id() || Auth::user()->isAdmin())
            <div class="flex items-center gap-2 self-start sm:self-auto flex-shrink-0">
                <a href="{{ route('berita.edit', $berita->id) }}"
                   title="Edit Berita"
                   class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-600 border border-blue-200/80 flex items-center justify-center transition active:scale-95 shadow-sm">
                    <svg class="w-4 h-4 sm:w-4.5 sm:h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </a>

                <button type="button"
                        @click="deleteModal = true"
                        title="Hapus Berita"
                        class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200/80 flex items-center justify-center transition active:scale-95 shadow-sm">
                    <svg class="w-4 h-4 sm:w-4.5 sm:h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </div>
        @endif
    </div>

    {{-- Article Container --}}
    <article class="bg-white rounded-2xl sm:rounded-3xl shadow-sm border border-slate-200/60 p-5 sm:p-8">
        {{-- Header --}}
        <header class="mb-5 sm:mb-6 pb-5 sm:pb-6 border-b border-slate-100">
            <div class="flex flex-col gap-2.5">
                <h1 class="text-xl sm:text-2xl md:text-3xl font-extrabold text-slate-900 leading-tight">
                    {{ $berita->judul }}
                </h1>

                {{-- Keterangan Target Kelas --}}
                <div class="flex flex-wrap items-center gap-2 mt-1">
                    <span class="text-xs font-bold text-slate-500">Target Kelas:</span>
                    @if(isset($berita->target_kelas) && $berita->target_kelas)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200/70">
                            {{ $berita->target_kelas }}
                        </span>
                    @elseif(isset($berita->kelas) && $berita->kelas)
                        @if(is_iterable($berita->kelas))
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($berita->kelas as $k)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200/70">
                                        {{ $k->nama_kelas ?? $k }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200/70">
                                {{ $berita->kelas->nama_kelas ?? $berita->kelas }}
                            </span>
                        @endif
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200/60">
                            Semua Kelas
                        </span>
                    @endif
                </div>
            </div>

            {{-- Metadata Info --}}
            <div class="flex flex-wrap items-center gap-2 sm:gap-3 text-[11px] sm:text-xs text-slate-400 mt-4 pt-3 border-t border-slate-100">
                <div class="flex items-center gap-1.5">
                    <div class="w-6 h-6 bg-indigo-100 rounded-full flex items-center justify-center font-bold text-indigo-700 text-xs flex-shrink-0">
                        {{ strtoupper(substr($berita->user->name ?? 'A', 0, 1)) }}
                    </div>
                    <span class="font-bold text-slate-700">{{ $berita->user->name ?? 'Admin Laboratorium TKJ' }}</span>
                </div>
                <span>•</span>
                <time datetime="{{ $berita->created_at }}" class="whitespace-nowrap">{{ $berita->created_at->translatedFormat('d F Y, H:i') }}</time>
                <span>•</span>
                <span class="whitespace-nowrap">{{ $berita->created_at->diffForHumans() }}</span>
            </div>
        </header>

        {{-- Body Content --}}
        <div class="text-slate-700 leading-relaxed text-xs sm:text-sm md:text-base space-y-4 whitespace-pre-line">
            {{ $berita->isi }}
        </div>
    </article>

    {{-- Footer Action --}}
    <div class="pt-2">
        <a href="{{ route('berita.index') }}" class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-bold text-indigo-600 hover:text-indigo-800 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Daftar Berita
        </a>
    </div>

    {{-- MODAL KONFIRMASI HAPUS BERITA DI TENGAH LAYAR --}}
    <template x-teleport="body">
        <div x-show="deleteModal" 
             class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm" 
             style="display: none;"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            <div @click.away="deleteModal = false" 
                 class="bg-white rounded-3xl p-6 sm:p-7 w-full max-w-sm sm:max-w-md shadow-2xl border border-slate-100 text-center"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-90 translate-y-3"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-90 translate-y-3">
                
                {{-- Icon Alert Hapus --}}
                <div class="w-14 h-14 bg-rose-100 text-rose-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-inner border border-rose-200/60">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>

                {{-- Alert Text --}}
                <h3 class="text-base sm:text-lg font-extrabold text-slate-800">Hapus Pengumuman Ini?</h3>
                <p class="text-xs sm:text-sm text-slate-500 mt-1.5 leading-relaxed">
                    Pengumuman <span class="font-bold text-slate-800">"{{ $berita->judul }}"</span> akan dihapus secara permanen dari sistem.
                </p>

                {{-- Actions Form --}}
                <form action="{{ route('berita.destroy', $berita->id) }}" method="POST" class="mt-6 flex items-center justify-center gap-3">
                    @csrf
                    @method('DELETE')
                    <button type="button" 
                            @click="deleteModal = false" 
                            class="flex-1 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-xs sm:text-sm transition">
                        Batal
                    </button>
                    <button type="submit" 
                            class="flex-1 px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl text-xs sm:text-sm shadow-md shadow-rose-600/20 transition active:scale-95">
                        Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </template>

</div>

@endsection