@extends('layouts.app')

@section('title', 'Berita & Pengumuman')
@section('page_title', 'Berita & Pengumuman')
@section('page_subtitle', 'Informasi, jadwal praktikum, dan update terkini Laboratorium TKJ')

@push('styles')
<style>
    .announcement-page {
        --announcement-border: #e2e8f0;
        --announcement-muted: #64748b;
        --announcement-ink: #0f172a;
    }

    .announcement-card {
        transition:
            transform 180ms ease,
            border-color 180ms ease,
            box-shadow 180ms ease;
    }

    @media (hover: hover) and (pointer: fine) {
        .announcement-card:hover {
            transform: translateY(-2px);
            border-color: #cbd5e1;
            box-shadow: 0 14px 36px -28px rgba(15, 23, 42, .55);
        }
    }

    .announcement-excerpt {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .announcement-reveal {
        opacity: 1;
        transform: none;
    }

    .js .announcement-reveal {
        opacity: 0;
        transform: translateY(10px);
    }

    .js .announcement-reveal.is-visible {
        animation: announcementReveal .42s cubic-bezier(.22, 1, .36, 1) forwards;
        animation-delay: var(--reveal-delay, 0ms);
    }

    @keyframes announcementReveal {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .announcement-card {
            transition: none !important;
        }

        .js .announcement-reveal {
            opacity: 1 !important;
            transform: none !important;
            animation: none !important;
        }
    }
</style>
@endpush

@section('content')

<div
    class="announcement-page"
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

    {{-- PAGE INTRO --}}
    <section class="announcement-reveal mb-5 sm:mb-6" style="--reveal-delay: 0ms;">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5 lg:p-6">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">

                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 rounded-md border border-indigo-200 bg-indigo-50 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.12em] text-indigo-700">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                            </svg>
                            Pusat Informasi
                        </span>

                        @if(isset($beritas))
                            <span class="inline-flex items-center rounded-md border border-slate-200 bg-slate-50 px-2.5 py-1 text-[10px] font-medium text-slate-500">
                                {{ number_format($beritas->total()) }} pengumuman
                            </span>
                        @endif
                    </div>

                    <h2 class="mt-3 text-xl font-semibold tracking-tight text-slate-950 sm:text-2xl lg:text-[1.65rem]">
                        Informasi resmi Laboratorium TKJ
                    </h2>

                    <p class="mt-1.5 max-w-2xl text-sm leading-6 text-slate-500">
                        Pantau jadwal, pembaruan perangkat, kegiatan laboratorium, dan informasi penting lainnya dari pengelola.
                    </p>

                    <div class="mt-4 flex items-center gap-3 text-[11px] text-slate-400">
                        <span class="h-px w-8 bg-slate-200"></span>
                        <span>Informasi resmi &amp; terverifikasi</span>
                    </div>
                </div>

                @if(
                    Auth::user()->isAdmin()
                    || (method_exists(Auth::user(), 'isGuru') && Auth::user()->isGuru())
                    || in_array(strtolower(Auth::user()->role), ['guru', 'admin'])
                )
                    <a
                        href="{{ route('berita.create') }}"
                        class="inline-flex min-h-10 shrink-0 items-center justify-center gap-2 self-start rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 lg:self-auto"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tulis Pengumuman
                    </a>
                @endif

            </div>
        </div>
    </section>


    {{-- SECTION TITLE --}}
    <div class="announcement-reveal mb-4 sm:mb-5" style="--reveal-delay: 40ms;">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="h-px flex-1 bg-slate-200"></div>

            <div class="shrink-0 text-center">
                <h3 class="text-sm font-semibold text-slate-900 sm:text-base">
                    Daftar Pengumuman
                </h3>
            </div>

            <div class="h-px flex-1 bg-slate-200"></div>
        </div>

        <p class="mt-1.5 text-center text-xs text-slate-500 sm:text-sm">
            Informasi terbaru ditampilkan lebih dahulu.
        </p>
    </div>


    {{-- ANNOUNCEMENT LIST --}}
    <section class="space-y-3 sm:space-y-4" aria-label="Daftar pengumuman">

        @forelse($beritas as $index => $berita)
            <article
                class="announcement-card announcement-reveal overflow-hidden rounded-2xl border border-slate-200 bg-white"
                style="--reveal-delay: {{ min($index * 55, 275) }}ms;"
            >
                <div class="grid grid-cols-1 md:grid-cols-[150px_minmax(0,1fr)]">

                    {{-- META COLUMN --}}
                    <aside class="flex items-center gap-3 border-b border-slate-100 bg-slate-50/70 px-4 py-4 md:block md:border-b-0 md:border-r md:px-5 md:py-5">

                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-indigo-100 bg-white text-indigo-600 shadow-sm md:h-11 md:w-11">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                            </svg>
                        </div>

                        <div class="min-w-0 md:mt-4">
                            <time
                                datetime="{{ $berita->created_at->toDateString() }}"
                                class="block text-xs font-semibold text-slate-700"
                            >
                                {{ $berita->created_at->translatedFormat('d M Y') }}
                            </time>

                            <p class="mt-0.5 truncate text-[10px] font-medium uppercase tracking-[0.08em] text-slate-400 md:mt-1 md:whitespace-normal">
                                {{ $berita->user->name ?? 'Admin Laboratorium' }}
                            </p>
                        </div>

                    </aside>


                    {{-- CONTENT COLUMN --}}
                    <div class="min-w-0 px-4 py-4 sm:px-5 sm:py-5 lg:px-6">

                        <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">

                            <div class="min-w-0">
                                <a
                                    href="{{ route('berita.show', $berita->id) }}"
                                    class="group block focus:outline-none"
                                >
                                    <h3 class="text-[15px] font-semibold leading-6 text-slate-950 transition-colors group-hover:text-indigo-600 sm:text-base">
                                        {{ $berita->judul }}
                                    </h3>
                                </a>
                            </div>

                            @if(!empty($berita->target_kelas))
                                <span class="inline-flex w-fit shrink-0 items-center rounded-md border border-indigo-200 bg-indigo-50 px-2 py-1 text-[10px] font-semibold uppercase tracking-wide text-indigo-700">
                                    {{ $berita->target_kelas }}
                                </span>
                            @endif

                        </div>

                        <p class="announcement-excerpt mt-2 text-sm leading-6 text-slate-600">
                            {{ strip_tags($berita->isi) }}
                        </p>

                        <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-3">

                            <a
                                href="{{ route('berita.show', $berita->id) }}"
                                class="inline-flex items-center gap-1.5 text-xs font-semibold text-indigo-600 transition hover:text-indigo-800 focus:outline-none focus-visible:underline"
                            >
                                Baca selengkapnya
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-6-6 6 6-6 6"/>
                                </svg>
                            </a>

                            @if($berita->user_id === Auth::id() || Auth::user()->isAdmin())
                                <div class="flex items-center gap-1.5">

                                    <a
                                        href="{{ route('berita.edit', $berita->id) }}"
                                        title="Edit pengumuman"
                                        aria-label="Edit pengumuman {{ $berita->judul }}"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
                                    >
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>

                                    <button
                                        type="button"
                                        @click="confirmDelete(@js(route('berita.destroy', $berita->id)), @js($berita->judul))"
                                        title="Hapus pengumuman"
                                        aria-label="Hapus pengumuman {{ $berita->judul }}"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 transition hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-500 focus-visible:ring-offset-2"
                                    >
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>

                                </div>
                            @endif

                        </div>

                    </div>

                </div>
            </article>

        @empty

            <div class="announcement-reveal rounded-2xl border border-dashed border-slate-300 bg-white px-5 py-12 text-center" style="--reveal-delay: 80ms;">

                <div class="mx-auto flex h-11 w-11 items-center justify-center rounded-lg bg-slate-100 text-slate-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1M19 20a2 2 0 002-2V8a2 2 0 00-2-2h-5M19 20a2 2 0 01-2-2v-1m-4.5-9h3.5m-3.5 3h3.5m-7-3h1.5m-1.5 3h1.5m-1.5 3h7.5"/>
                    </svg>
                </div>

                <h3 class="mt-4 text-sm font-semibold text-slate-800">
                    Belum ada pengumuman
                </h3>

                <p class="mx-auto mt-1 max-w-md text-xs leading-5 text-slate-500 sm:text-sm">
                    Informasi baru dari Laboratorium TKJ akan tampil di halaman ini.
                </p>

                @if(Auth::user()->isAdmin() || in_array(strtolower(Auth::user()->role), ['guru', 'admin']))
                    <a
                        href="{{ route('berita.create') }}"
                        class="mt-4 inline-flex items-center gap-1.5 text-xs font-semibold text-indigo-600 hover:text-indigo-800"
                    >
                        Tulis pengumuman pertama
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-6-6 6 6-6 6"/>
                        </svg>
                    </a>
                @endif

            </div>

        @endforelse

    </section>


    {{-- PAGINATION --}}
    @if($beritas->hasPages())
        <div class="announcement-reveal mt-6" style="--reveal-delay: 80ms;">
            {{ $beritas->links() }}
        </div>
    @endif


    {{-- DELETE MODAL --}}
    <template x-teleport="body">
        <div
            x-show="deleteModal"
            x-cloak
            @keydown.escape.window="deleteModal = false"
            class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-950/45 p-4"
            x-transition.opacity.duration.150ms
        >
            <div
                @click.outside="deleteModal = false"
                role="dialog"
                aria-modal="true"
                aria-labelledby="delete-modal-title"
                class="w-full max-w-sm rounded-2xl border border-slate-200 bg-white p-5 shadow-2xl sm:p-6"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-2 scale-[.98]"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 translate-y-1 scale-[.99]"
            >

                <div class="flex items-start gap-3">

                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-rose-50 text-rose-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </div>

                    <div class="min-w-0">
                        <h3 id="delete-modal-title" class="text-base font-semibold text-slate-950">
                            Hapus pengumuman?
                        </h3>

                        <p class="mt-1 text-sm leading-5 text-slate-500">
                            <span class="font-medium text-slate-700" x-text="deleteTitle"></span>
                            akan dihapus permanen dari sistem.
                        </p>
                    </div>

                </div>

                <form :action="deleteActionUrl" method="POST" class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                    @csrf
                    @method('DELETE')

                    <button
                        type="button"
                        @click="deleteModal = false"
                        class="inline-flex min-h-10 items-center justify-center rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="inline-flex min-h-10 items-center justify-center rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-rose-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-500 focus-visible:ring-offset-2"
                    >
                        Hapus
                    </button>
                </form>

            </div>
        </div>
    </template>

</div>


<script>
    document.documentElement.classList.add('js');

    document.addEventListener('DOMContentLoaded', function () {
        const elements = document.querySelectorAll('.announcement-reveal');

        if (!elements.length) return;

        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        if (reduceMotion || !('IntersectionObserver' in window)) {
            elements.forEach(function (element) {
                element.classList.add('is-visible');
            });

            return;
        }

        const observer = new IntersectionObserver(function (entries, currentObserver) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) return;

                entry.target.classList.add('is-visible');
                currentObserver.unobserve(entry.target);
            });
        }, {
            threshold: 0.08,
            rootMargin: '0px 0px -4% 0px'
        });

        elements.forEach(function (element) {
            observer.observe(element);
        });
    });
</script>

@endsection
