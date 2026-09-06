@extends('layouts.app')

@section('title', 'Berita & Pengumuman')
@section('page_title', 'Berita & Pengumuman')
@section('page_subtitle', 'Informasi, jadwal praktikum, dan update terkini Laboratorium TKJ')

@section('content')

<div class="space-y-4 sm:space-y-6"
    x-data="{
        deleteModal: false,
        deleteActionUrl: '',
        deleteTitle: '',
        confirmDelete(url, title) {
            this.deleteActionUrl = url;
            this.deleteTitle = title;
            this.deleteModal = true;
        }
    }"
>
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="inline-flex items-center gap-1.5 rounded-md bg-indigo-50 px-2.5 py-1 text-[11px] font-bold uppercase tracking-wider text-indigo-700 shadow-xs mb-2">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-600"></span>
                </span>
                Pusat Informasi & Pengumuman
            </div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Pengumuman Laboratorium</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Jadwal praktikum, pembaruan alat, dan pengumuman resmi Laboratorium TKJ</p>
        </div>

        @if(
            Auth::user()->isAdmin()
            || (method_exists(Auth::user(), 'isGuru') && Auth::user()->isGuru())
            || in_array(strtolower(Auth::user()->role), ['guru', 'admin'])
        )
            <a href="{{ route('berita.create') }}"
               class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-xs transition active:scale-95">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                <span>Tulis Pengumuman</span>
            </a>
        @endif
    </div>

    {{-- Quick Metric Pills --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
        <div class="bg-white rounded-2xl border border-slate-200/80 p-4 flex items-center gap-3.5 shadow-xs">
            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1M19 20a2 2 0 002-2V8a2 2 0 00-2-2h-5M19 20a2 2 0 01-2-2v-1m-4.5-9h3.5m-3.5 3h3.5m-7-3h1.5m-1.5 3h1.5m-1.5 3h7.5"/>
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Total Pengumuman</p>
                <p class="text-xl sm:text-2xl font-black text-slate-900 mt-0.5">{{ number_format($beritas->total()) }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 p-4 flex items-center gap-3.5 shadow-xs">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Status Informasi</p>
                <p class="text-xs sm:text-sm font-bold text-slate-800 mt-0.5">Resmi &amp; Terverifikasi</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 p-4 flex items-center gap-3.5 shadow-xs">
            <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Pembaruan Terkini</p>
                <p class="text-xs sm:text-sm font-bold text-slate-800 mt-0.5 truncate">
                    {{ $beritas->first() ? $beritas->first()->created_at->diffForHumans() : 'Belum Ada' }}
                </p>
            </div>
        </div>
    </div>

    {{-- Announcements Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5" aria-label="Daftar pengumuman">
        @forelse($beritas as $berita)
            <article class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-xs hover:shadow-md hover:border-indigo-200 transition p-5 sm:p-6 flex flex-col justify-between">
                <div>
                    {{-- Card Header: Target Badge & Date --}}
                    <div class="flex items-center justify-between gap-3">
                        @if(empty($berita->target_kelas) || str_contains(strtolower($berita->target_kelas), 'semua'))
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                <span>Semua Pengguna</span>
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200/80">
                                <span class="w-1.5 h-1.5 rounded-full bg-indigo-600"></span>
                                <span>{{ $berita->target_kelas }}</span>
                            </span>
                        @endif

                        <time datetime="{{ $berita->created_at->toDateString() }}" class="text-[11px] text-slate-400 font-medium flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>{{ $berita->created_at->translatedFormat('d M Y') }}</span>
                        </time>
                    </div>

                    {{-- Title & Body Preview --}}
                    <div class="mt-3">
                        <a href="{{ route('berita.show', $berita->id) }}" class="group block">
                            <h2 class="text-base sm:text-lg font-bold text-slate-900 group-hover:text-indigo-600 transition leading-snug line-clamp-2">
                                {{ $berita->judul }}
                            </h2>
                        </a>
                        <p class="mt-2 text-xs sm:text-sm text-slate-500 leading-relaxed line-clamp-3">
                            {{ strip_tags($berita->isi) }}
                        </p>
                    </div>
                </div>

                {{-- Card Footer: Author & Action Buttons --}}
                <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2 min-w-0">
                        <div class="w-7 h-7 rounded-full bg-slate-100 text-slate-700 font-bold text-xs flex items-center justify-center shrink-0 border border-slate-200/60">
                            {{ strtoupper(substr($berita->user->name ?? 'A', 0, 1)) }}
                        </div>
                        <span class="text-xs font-semibold text-slate-700 truncate max-w-[130px] sm:max-w-[160px]">
                            {{ $berita->user->name ?? 'Admin Laboratorium' }}
                        </span>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ route('berita.show', $berita->id) }}"
                           class="inline-flex items-center gap-1 text-xs font-bold text-indigo-600 hover:text-indigo-800 transition">
                            <span>Detail</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>

                        @if($berita->user_id === Auth::id() || Auth::user()->isAdmin())
                            <div class="flex items-center gap-1.5 pl-2 border-l border-slate-200">
                                <a href="{{ route('berita.edit', $berita->id) }}"
                                   title="Edit Pengumuman"
                                   aria-label="Edit pengumuman {{ $berita->judul }}"
                                   class="w-7 h-7 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 flex items-center justify-center transition active:scale-95 shadow-2xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>

                                <button type="button"
                                        @click="confirmDelete(@js(route('berita.destroy', $berita->id)), @js($berita->judul))"
                                        title="Hapus Pengumuman"
                                        aria-label="Hapus pengumuman {{ $berita->judul }}"
                                        class="w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center transition active:scale-95 shadow-2xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </article>
        @empty
            <div class="col-span-full rounded-2xl sm:rounded-3xl border border-dashed border-slate-300 bg-white px-5 py-12 text-center">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1M19 20a2 2 0 002-2V8a2 2 0 00-2-2h-5M19 20a2 2 0 01-2-2v-1m-4.5-9h3.5m-3.5 3h3.5m-7-3h1.5m-1.5 3h1.5m-1.5 3h7.5"/>
                    </svg>
                </div>

                <h3 class="mt-4 text-sm sm:text-base font-bold text-slate-800">
                    Belum Ada Pengumuman
                </h3>

                <p class="mx-auto mt-1 max-w-md text-xs sm:text-sm text-slate-500 leading-relaxed">
                    Informasi dan jadwal praktikum terbaru dari laboratorium akan ditampilkan di sini.
                </p>

                @if(
                    Auth::user()->isAdmin()
                    || (method_exists(Auth::user(), 'isGuru') && Auth::user()->isGuru())
                    || in_array(strtolower(Auth::user()->role), ['guru', 'admin'])
                )
                    <a href="{{ route('berita.create') }}"
                       class="mt-4 inline-flex items-center gap-1.5 text-xs font-bold text-indigo-600 hover:text-indigo-800">
                        <span>Tulis Pengumuman Pertama</span>
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-6-6 6 6-6 6"/>
                        </svg>
                    </a>
                @endif
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($beritas->hasPages())
        <div class="pt-2">
            {{ $beritas->links() }}
        </div>
    @endif

    {{-- Delete Modal --}}
    <template x-teleport="body">
        <div x-show="deleteModal"
             x-cloak
             @keydown.escape.window="deleteModal = false"
             class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-950/45 p-4"
             x-transition.opacity.duration.150ms>
            <div @click.outside="deleteModal = false"
                 role="dialog"
                 aria-modal="true"
                 aria-labelledby="delete-modal-title"
                 class="w-full max-w-sm rounded-3xl border border-slate-100 bg-white p-6 shadow-2xl text-center"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">

                <div class="w-12 h-12 bg-rose-100 text-rose-600 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-rose-200/60">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>

                <h3 id="delete-modal-title" class="text-base sm:text-lg font-extrabold text-slate-900">
                    Hapus Pengumuman?
                </h3>

                <p class="mt-1 text-xs sm:text-sm text-slate-500 leading-relaxed">
                    Pengumuman <span class="font-bold text-slate-800" x-text="deleteTitle"></span> akan dihapus secara permanen dari sistem.
                </p>

                <form :action="deleteActionUrl" method="POST" class="mt-6 flex items-center justify-center gap-3">
                    @csrf
                    @method('DELETE')

                    <button type="button"
                            @click="deleteModal = false"
                            class="flex-1 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-xs sm:text-sm transition">
                        Batal
                    </button>

                    <button type="submit"
                            class="flex-1 px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl text-xs sm:text-sm shadow-xs transition active:scale-95">
                        Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </template>
</div>
@endsection
