@extends('layouts.app')

@section('title', 'Dashboard - LabSystem')
@section('page_title', 'Dashboard')
@section('page_subtitle', 'Winshark Community • ' . (Auth::user()->laboratorium_penugasan ?? 'Laboratorium TKJ'))

@section('content')
<style>
    /* Smooth custom scrollbars */
    .dashboard-scrollbar {
        scrollbar-width: thin;
        scrollbar-color: #cbd5e1 transparent;
        -webkit-overflow-scrolling: touch;
    }
    .dashboard-scrollbar::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    .dashboard-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .dashboard-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 9999px;
    }
    .dashboard-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* Keyframes for elegant entrance */
    @keyframes heroFadeUp {
        0% {
            opacity: 0;
            transform: translateY(22px) scale(0.985);
        }
        100% {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    @keyframes barGrow {
        0% {
            width: 0%;
        }
        100% {
            width: var(--target-width, 0%);
        }
    }

    @keyframes shimmerWave {
        0% {
            transform: translateX(-100%);
        }
        100% {
            transform: translateX(200%);
        }
    }

    @keyframes pulseSubtle {
        0%, 100% {
            opacity: 1;
            transform: scale(1);
        }
        50% {
            opacity: 0.85;
            transform: scale(0.98);
        }
    }

    /* Initial state before reveal (GPU-accelerated, anti-flicker) */
    .dashboard-reveal-container .animate-enter {
        opacity: 0;
        transform: translateY(18px) scale(0.985);
        will-change: opacity, transform;
    }

    @media (max-width: 639px) {
        .dashboard-reveal-container .animate-enter {
            transform: translateY(12px) scale(0.99);
        }
    }

    /* Active entrance triggered right as loading screen slides */
    .dashboard-reveal-container.is-revealed .animate-enter {
        animation: heroFadeUp 0.65s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    /* Cascading staggered entrance delays */
    .dashboard-reveal-container.is-revealed .delay-0   { animation-delay: 0ms; }
    .dashboard-reveal-container.is-revealed .delay-75  { animation-delay: 60ms; }
    .dashboard-reveal-container.is-revealed .delay-100 { animation-delay: 100ms; }
    .dashboard-reveal-container.is-revealed .delay-150 { animation-delay: 150ms; }
    .dashboard-reveal-container.is-revealed .delay-200 { animation-delay: 200ms; }
    .dashboard-reveal-container.is-revealed .delay-250 { animation-delay: 250ms; }
    .dashboard-reveal-container.is-revealed .delay-300 { animation-delay: 300ms; }
    .dashboard-reveal-container.is-revealed .delay-350 { animation-delay: 350ms; }
    .dashboard-reveal-container.is-revealed .delay-400 { animation-delay: 400ms; }
    .dashboard-reveal-container.is-revealed .delay-450 { animation-delay: 450ms; }
    .dashboard-reveal-container.is-revealed .delay-500 { animation-delay: 500ms; }
    .dashboard-reveal-container.is-revealed .delay-550 { animation-delay: 550ms; }
    .dashboard-reveal-container.is-revealed .delay-600 { animation-delay: 600ms; }

    /* Animated progress bar */
    .dashboard-reveal-container.is-revealed .progress-bar-fill {
        animation: barGrow 1.2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        animation-delay: 350ms;
    }

    .shimmer-effect::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 60%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.35), transparent);
        animation: shimmerWave 2.8s infinite ease-in-out;
    }

    /* Accessibility: respect user preference */
    @media (prefers-reduced-motion: reduce) {
        .dashboard-reveal-container .animate-enter,
        .dashboard-reveal-container.is-revealed .animate-enter,
        .progress-bar-fill,
        .shimmer-effect::after {
            animation: none !important;
            opacity: 1 !important;
            transform: none !important;
            width: var(--target-width, 100%) !important;
        }
    }

    /* Donut Chart Keyframe & Hover Animations */
    @keyframes donutSpinIn {
        0% {
            stroke-dashoffset: 238.761;
            opacity: 0;
            transform: rotate(-90deg) scale(0.92);
        }
        100% {
            opacity: 1;
            transform: rotate(-90deg) scale(1);
        }
    }

    @keyframes centerContentFade {
        0% {
            opacity: 0;
            transform: scale(0.92);
        }
        100% {
            opacity: 1;
            transform: scale(1);
        }
    }

    .dashboard-reveal-container.is-revealed .donut-svg-canvas {
        animation: donutSpinIn 0.85s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        transform-origin: center;
    }

    .donut-center-animate {
        animation: centerContentFade 0.2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    .donut-slice {
        transition: stroke-width 0.25s cubic-bezier(0.16, 1, 0.3, 1),
                    opacity 0.25s cubic-bezier(0.16, 1, 0.3, 1),
                    filter 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        transform-origin: center;
    }
</style>

@php
    $totalBarangValue = (int) ($totalBarang ?? 0);
    $safeTotalBarang  = max($totalBarangValue, 1);

    $kondisiColors = [
        'Baik'        => 'bg-gradient-to-r from-emerald-500 to-teal-500',
        'Perawatan'   => 'bg-gradient-to-r from-amber-500 to-yellow-500',
        'Perbaikan'   => 'bg-gradient-to-r from-orange-500 to-amber-500',
        'Rusak Berat' => 'bg-gradient-to-r from-rose-500 to-red-600',
        'Hilang'      => 'bg-gradient-to-r from-slate-500 to-gray-600',
    ];

    $conditionBadge = function ($condition) {
        return match ($condition) {
            'Baik'        => 'bg-emerald-50 text-emerald-700 border-emerald-200/80',
            'Perawatan'   => 'bg-amber-50 text-amber-700 border-amber-200/80',
            'Perbaikan'   => 'bg-orange-50 text-orange-700 border-orange-200/80',
            'Rusak Berat' => 'bg-rose-50 text-rose-700 border-rose-200/80',
            'Hilang'      => 'bg-slate-100 text-slate-700 border-slate-200',
            default       => 'bg-slate-100 text-slate-700 border-slate-200',
        };
    };

    // Donut Chart Data Calculation (Radius = 38, Circumference = 238.761)
    $circ = 238.761;

    // 1. Donut Kondisi
    $donutKondisi = [];
    $kondisiPalette = [
        'Baik'        => '#10b981',
        'Perawatan'   => '#f59e0b',
        'Perbaikan'   => '#f97316',
        'Rusak Berat' => '#ef4444',
        'Hilang'      => '#64748b',
    ];
    $cumKondisi = 0;
    foreach ($kondisiStats ?? [] as $kNama => $kCount) {
        $kCountInt = (int) $kCount;
        $pct = $totalBarangValue > 0 ? round(($kCountInt / $totalBarangValue) * 100, 1) : 0;
        $dash = round(($pct / 100) * $circ, 3);
        $offset = round(-($cumKondisi / 100) * $circ, 3);
        $cumKondisi += $pct;

        $donutKondisi[] = [
            'label'     => $kNama,
            'count'     => $kCountInt,
            'percent'   => $pct,
            'color'     => $kondisiPalette[$kNama] ?? '#6366f1',
            'dashArray' => "{$dash} {$circ}",
            'offset'    => $offset,
        ];
    }

    // 2. Donut Kategori
    $donutKategori = [];
    $kategoriPalette = [
        '#6366f1', // Indigo
        '#8b5cf6', // Violet
        '#0ea5e9', // Sky
        '#14b8a6', // Teal
        '#f59e0b', // Amber
        '#ec4899', // Pink
    ];
    $cumKategori = 0;
    $katIdx = 0;
    foreach ($kategoriStats ?? [] as $kat) {
        $kNama = $kat->kategori;
        $kCountInt = (int) ($kat->total ?? 0);
        $pct = $totalBarangValue > 0 ? round(($kCountInt / $totalBarangValue) * 100, 1) : 0;
        $dash = round(($pct / 100) * $circ, 3);
        $offset = round(-($cumKategori / 100) * $circ, 3);
        $cumKategori += $pct;
        $color = $kategoriPalette[$katIdx % count($kategoriPalette)];
        $katIdx++;

        $donutKategori[] = [
            'label'     => $kNama,
            'count'     => $kCountInt,
            'percent'   => $pct,
            'color'     => $color,
            'dashArray' => "{$dash} {$circ}",
            'offset'    => $offset,
        ];
    }
@endphp

<div id="dashboardAdminContainer" x-data x-init="$nextTick(() => { $el.classList.add('is-revealed'); if(typeof window.initGentelellaDashboardCharts === 'function') { try { window.initGentelellaDashboardCharts(); } catch(e) {} } })" class="dashboard-reveal-container min-h-full bg-slate-50/70 pb-12 sm:pb-16 pb-[calc(3rem+env(safe-area-inset-bottom,0px))]">
    <div class="mx-auto w-full max-w-screen-2xl px-3 sm:px-6 lg:px-8 py-4 sm:py-6 lg:py-8">
        <div class="space-y-4 sm:space-y-6">

            @if(isset($peminjamanTerlambat) && $peminjamanTerlambat > 0)
                <section class="animate-enter delay-0 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4 rounded-xl border border-rose-200 bg-rose-50/95 p-3.5 sm:p-4 shadow-sm backdrop-blur transition-all duration-300 hover:shadow-md" role="alert">
                    <div class="flex items-start gap-3 min-w-0">
                        <div class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-rose-100 text-rose-700 shadow-inner">
                            <svg class="h-5 w-5 animate-pulse" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h2 class="text-sm sm:text-base font-semibold text-rose-900 leading-snug">
                                {{ $peminjamanTerlambat }} peminjaman melewati batas pengembalian
                            </h2>
                            <p class="mt-0.5 text-xs sm:text-sm text-rose-700 leading-relaxed">
                                Periksa daftar peminjaman aktif dan segera tindak lanjuti barang yang belum dikembalikan.
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('peminjaman.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center min-h-[40px] shrink-0 rounded-lg border border-rose-300 bg-white px-4 py-2 text-xs sm:text-sm font-semibold text-rose-700 shadow-sm transition-all duration-200 active:scale-[0.98] hover:bg-rose-100 hover:shadow focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-500 focus-visible:ring-offset-2">
                        Periksa Data
                    </a>
                </section>
            @endif

            <section aria-labelledby="ringkasan-heading">
                <div class="mb-3 px-1 animate-enter delay-75">
                    <div class="inline-flex items-center gap-1.5 rounded-md bg-indigo-50 px-2.5 py-1 text-[11px] font-bold uppercase tracking-wider text-indigo-700 shadow-xs mb-2">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-600"></span>
                        </span>
                        SISTEM MANAJEMEN TKJ
                    </div>
                    <h2 id="ringkasan-heading" class="text-base sm:text-lg font-bold text-slate-900">
                        Ringkasan Inventaris
                    </h2>
                    <p class="mt-1 text-xs sm:text-sm text-slate-500">
                        Ikhtisar status aset dan pemakaian sarana laboratorium.
                    </p>
                </div>

                <!-- 2 columns on mobile, 3 on tablet, 4 on desktop -->
                <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2.5 sm:gap-4">

                    {{-- Total barang --}}
                    <article class="animate-enter delay-100 group relative flex flex-col justify-between overflow-hidden rounded-xl sm:rounded-2xl border border-slate-200/90 bg-white p-3.5 sm:p-5 shadow-xs transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:border-indigo-300">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0 flex-1">
                                <p class="text-[10px] sm:text-xs font-semibold text-slate-500 uppercase tracking-wider truncate">Total Barang</p>
                                <div class="mt-1 sm:mt-1.5 flex items-baseline gap-1.5 flex-wrap">
                                    <span class="text-xl sm:text-2xl lg:text-3xl font-extrabold tabular-nums text-slate-900 group-hover:text-indigo-600 transition-colors">
                                        {{ number_format($totalBarangValue) }}
                                    </span>
                                    <span class="text-[10px] sm:text-xs font-semibold text-slate-400">Unit</span>
                                </div>
                                <p class="mt-1 text-[10px] sm:text-[11px] text-slate-400 font-medium truncate">Keseluruhan inventaris</p>
                            </div>
                            <div class="flex h-9 w-9 sm:h-11 sm:w-11 shrink-0 items-center justify-center rounded-lg sm:rounded-xl bg-indigo-50 text-indigo-600 transition-transform duration-300 group-hover:scale-110 group-hover:bg-indigo-600 group-hover:text-white shadow-xs">
                                <svg class="h-4.5 w-4.5 sm:h-5 sm:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </div>
                        </div>
                        {{-- Mini Sparkline --}}
                        <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-end justify-between gap-1 h-5">
                            <div class="w-1.5 rounded-full bg-indigo-200 h-2"></div>
                            <div class="w-1.5 rounded-full bg-indigo-300 h-3"></div>
                            <div class="w-1.5 rounded-full bg-indigo-200 h-2.5"></div>
                            <div class="w-1.5 rounded-full bg-indigo-400 h-4"></div>
                            <div class="w-1.5 rounded-full bg-indigo-300 h-3.5"></div>
                            <div class="w-1.5 rounded-full bg-indigo-500 h-5"></div>
                            <div class="w-1.5 rounded-full bg-indigo-600 h-4.5"></div>
                        </div>
                    </article>

                    {{-- Kondisi baik --}}
                    <article class="animate-enter delay-150 group relative flex flex-col justify-between overflow-hidden rounded-xl sm:rounded-2xl border border-slate-200/90 bg-white p-3.5 sm:p-5 shadow-xs transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:border-emerald-300">
                        @php
                            $pctBaik = $totalBarangValue > 0 ? round((($barangBaik ?? 0) / $totalBarangValue) * 100) : 0;
                        @endphp
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0 flex-1">
                                <p class="text-[10px] sm:text-xs font-semibold text-slate-500 uppercase tracking-wider truncate">Kondisi Baik</p>
                                <div class="mt-1 sm:mt-1.5 flex items-baseline gap-1.5 flex-wrap">
                                    <span class="text-xl sm:text-2xl lg:text-3xl font-extrabold tabular-nums text-emerald-600">
                                        {{ number_format($barangBaik ?? 0) }}
                                    </span>
                                    <span class="inline-flex items-center text-[10px] sm:text-xs font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">
                                        {{ $pctBaik }}%
                                    </span>
                                </div>
                                <p class="mt-1 text-[10px] sm:text-[11px] text-slate-400 font-medium truncate">Siap pakai di laboratorium</p>
                            </div>
                            <div class="flex h-9 w-9 sm:h-11 sm:w-11 shrink-0 items-center justify-center rounded-lg sm:rounded-xl bg-emerald-50 text-emerald-600 transition-transform duration-300 group-hover:scale-110 group-hover:bg-emerald-600 group-hover:text-white shadow-xs">
                                <svg class="h-4.5 w-4.5 sm:h-5 sm:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        {{-- Thin Progress Bar --}}
                        <div class="mt-3 pt-2.5 border-t border-slate-100 space-y-1">
                            <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-emerald-500 h-1.5 rounded-full transition-all duration-500" style="width: {{ $pctBaik }}%"></div>
                            </div>
                        </div>
                    </article>

                    {{-- Perbaikan --}}
                    <article class="animate-enter delay-200 group relative flex flex-col justify-between overflow-hidden rounded-xl sm:rounded-2xl border border-slate-200/90 bg-white p-3.5 sm:p-5 shadow-xs transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:border-amber-300">
                        @php
                            $pctPerbaikan = $totalBarangValue > 0 ? round((($barangPerbaikan ?? 0) / $totalBarangValue) * 100) : 0;
                        @endphp
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0 flex-1">
                                <p class="text-[10px] sm:text-xs font-semibold text-slate-500 uppercase tracking-wider truncate">Dalam Perbaikan</p>
                                <div class="mt-1 sm:mt-1.5 flex items-baseline gap-1.5 flex-wrap">
                                    <span class="text-xl sm:text-2xl lg:text-3xl font-extrabold tabular-nums text-amber-600">
                                        {{ number_format($barangPerbaikan ?? 0) }}
                                    </span>
                                    <span class="inline-flex items-center text-[10px] sm:text-xs font-bold text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded border border-amber-200">
                                        {{ $pctPerbaikan }}%
                                    </span>
                                </div>
                                <p class="mt-1 text-[10px] sm:text-[11px] text-slate-400 font-medium truncate">Dalam proses teknisi</p>
                            </div>
                            <div class="flex h-9 w-9 sm:h-11 sm:w-11 shrink-0 items-center justify-center rounded-lg sm:rounded-xl bg-amber-50 text-amber-600 transition-transform duration-300 group-hover:scale-110 group-hover:bg-amber-500 group-hover:text-white shadow-xs">
                                <svg class="h-4.5 w-4.5 sm:h-5 sm:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                </svg>
                            </div>
                        </div>
                        {{-- Thin Progress Bar --}}
                        <div class="mt-3 pt-2.5 border-t border-slate-100 space-y-1">
                            <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-amber-500 h-1.5 rounded-full transition-all duration-500" style="width: {{ $pctPerbaikan }}%"></div>
                            </div>
                        </div>
                    </article>

                    {{-- Rusak berat --}}
                    <article class="animate-enter delay-250 group relative flex flex-col justify-between overflow-hidden rounded-xl sm:rounded-2xl border border-slate-200/90 bg-white p-3.5 sm:p-5 shadow-xs transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:border-rose-300">
                        @php
                            $pctRusak = $totalBarangValue > 0 ? round((($barangRusak ?? 0) / $totalBarangValue) * 100) : 0;
                        @endphp
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0 flex-1">
                                <p class="text-[10px] sm:text-xs font-semibold text-slate-500 uppercase tracking-wider truncate">Rusak Berat</p>
                                <div class="mt-1 sm:mt-1.5 flex items-baseline gap-1.5 flex-wrap">
                                    <span class="text-xl sm:text-2xl lg:text-3xl font-extrabold tabular-nums text-rose-600">
                                        {{ number_format($barangRusak ?? 0) }}
                                    </span>
                                    <span class="inline-flex items-center text-[10px] sm:text-xs font-bold text-rose-600 bg-rose-50 px-1.5 py-0.5 rounded border border-rose-200">
                                        {{ $pctRusak }}%
                                    </span>
                                </div>
                                <p class="mt-1 text-[10px] sm:text-[11px] text-slate-400 font-medium truncate">Perlu penggantian/tindakan</p>
                            </div>
                            <div class="flex h-9 w-9 sm:h-11 sm:w-11 shrink-0 items-center justify-center rounded-lg sm:rounded-xl bg-rose-50 text-rose-600 transition-transform duration-300 group-hover:scale-110 group-hover:bg-rose-600 group-hover:text-white shadow-xs">
                                <svg class="h-4.5 w-4.5 sm:h-5 sm:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                        </div>
                        {{-- Thin Progress Bar --}}
                        <div class="mt-3 pt-2.5 border-t border-slate-100 space-y-1">
                            <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-rose-500 h-1.5 rounded-full transition-all duration-500" style="width: {{ $pctRusak }}%"></div>
                            </div>
                        </div>
                    </article>

                    {{-- Nilai aset (Span 2 on mobile for neat currency display) --}}
                    <article class="animate-enter delay-300 col-span-2 md:col-span-1 lg:col-span-1 group relative flex flex-col justify-between overflow-hidden rounded-xl sm:rounded-2xl border border-slate-200/90 bg-white p-3.5 sm:p-5 shadow-xs transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:border-slate-300">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0 flex-1">
                                <p class="text-[10px] sm:text-xs font-semibold text-slate-500 uppercase tracking-wider truncate">Nilai Aset Lab</p>
                                <div class="mt-1 sm:mt-1.5">
                                    <span class="text-base sm:text-xl lg:text-2xl font-extrabold tabular-nums text-slate-900 truncate block group-hover:text-indigo-600 transition-colors">
                                        Rp {{ number_format($totalNilai ?? 0, 0, ',', '.') }}
                                    </span>
                                </div>
                                <p class="mt-1 text-[10px] sm:text-[11px] text-slate-400 font-medium truncate">Total valuasi barang</p>
                            </div>
                            <div class="flex h-9 w-9 sm:h-11 sm:w-11 shrink-0 items-center justify-center rounded-lg sm:rounded-xl bg-slate-100 text-slate-700 transition-transform duration-300 group-hover:scale-110 group-hover:bg-slate-800 group-hover:text-white shadow-xs">
                                <svg class="h-4.5 w-4.5 sm:h-5 sm:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                            <span>Akumulasi Nilai</span>
                            <span class="font-semibold text-slate-700">{{ number_format($totalBarangValue) }} Unit</span>
                        </div>
                    </article>

                    {{-- Dipinjam aktif --}}
                    <article class="animate-enter delay-350 group relative flex flex-col justify-between overflow-hidden rounded-xl sm:rounded-2xl border border-slate-200/90 bg-white p-3.5 sm:p-5 shadow-xs transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:border-sky-300">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0 flex-1">
                                <p class="text-[10px] sm:text-xs font-semibold text-slate-500 uppercase tracking-wider truncate">Dipinjam Aktif</p>
                                <div class="mt-1 sm:mt-1.5 flex items-baseline gap-1.5 flex-wrap">
                                    <span class="text-xl sm:text-2xl lg:text-3xl font-extrabold tabular-nums text-sky-600">
                                        {{ number_format($barangDipinjam ?? 0) }}
                                    </span>
                                    <span class="text-[10px] sm:text-xs font-semibold text-slate-400">Unit</span>
                                </div>
                                <p class="mt-1 text-[10px] sm:text-[11px] text-slate-400 font-medium truncate">Dalam status peminjaman</p>
                            </div>
                            <div class="flex h-9 w-9 sm:h-11 sm:w-11 shrink-0 items-center justify-center rounded-lg sm:rounded-xl bg-sky-50 text-sky-600 transition-transform duration-300 group-hover:scale-110 group-hover:bg-sky-600 group-hover:text-white shadow-xs">
                                <svg class="h-4.5 w-4.5 sm:h-5 sm:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                </svg>
                            </div>
                        </div>
                        {{-- Mini Sparkline --}}
                        <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-end justify-between gap-1 h-5">
                            <div class="w-1.5 rounded-full bg-sky-200 h-3"></div>
                            <div class="w-1.5 rounded-full bg-sky-300 h-2"></div>
                            <div class="w-1.5 rounded-full bg-sky-200 h-4"></div>
                            <div class="w-1.5 rounded-full bg-sky-400 h-3"></div>
                            <div class="w-1.5 rounded-full bg-sky-300 h-5"></div>
                            <div class="w-1.5 rounded-full bg-sky-500 h-4"></div>
                            <div class="w-1.5 rounded-full bg-sky-600 h-4.5"></div>
                        </div>
                    </article>

                    {{-- Maintenance bulan ini --}}
                    <article class="animate-enter delay-400 group relative flex flex-col justify-between overflow-hidden rounded-xl sm:rounded-2xl border border-slate-200/90 bg-white p-3.5 sm:p-5 shadow-xs transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:border-violet-300">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0 flex-1">
                                <p class="text-[10px] sm:text-xs font-semibold text-slate-500 uppercase tracking-wider truncate">Maintenance Bulan Ini</p>
                                <div class="mt-1 sm:mt-1.5 flex items-baseline gap-1.5 flex-wrap">
                                    <span class="text-xl sm:text-2xl lg:text-3xl font-extrabold tabular-nums text-violet-600">
                                        {{ number_format($maintenanceBulanIni ?? 0) }}
                                    </span>
                                    <span class="text-[10px] sm:text-xs font-semibold text-slate-400">Kegiatan</span>
                                </div>
                                <p class="mt-1 text-[10px] sm:text-[11px] text-slate-400 font-medium truncate">Aktivitas perawatan rutin</p>
                            </div>
                            <div class="flex h-9 w-9 sm:h-11 sm:w-11 shrink-0 items-center justify-center rounded-lg sm:rounded-xl bg-violet-50 text-violet-600 transition-transform duration-300 group-hover:scale-110 group-hover:bg-violet-600 group-hover:text-white shadow-xs">
                                <svg class="h-4.5 w-4.5 sm:h-5 sm:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                            <span>Bulan {{ now()->translatedFormat('F') }}</span>
                            <span class="font-semibold text-violet-700">{{ $maintenanceBulanIni ?? 0 }} Terjadwal</span>
                        </div>
                    </article>

                    {{-- Barang baru tahun ini --}}
                    <article class="animate-enter delay-450 group relative flex flex-col justify-between overflow-hidden rounded-xl sm:rounded-2xl border border-slate-200/90 bg-white p-3.5 sm:p-5 shadow-xs transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:border-teal-300">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0 flex-1">
                                <p class="text-[10px] sm:text-xs font-semibold text-slate-500 uppercase tracking-wider truncate">Barang Baru</p>
                                <div class="mt-1 sm:mt-1.5 flex items-baseline gap-1.5 flex-wrap">
                                    <span class="text-xl sm:text-2xl lg:text-3xl font-extrabold tabular-nums text-teal-600">
                                        {{ number_format($barangBaruTahunIni ?? 0) }}
                                    </span>
                                    <span class="text-[10px] sm:text-xs font-semibold text-slate-400">Unit</span>
                                </div>
                                <p class="mt-1 text-[10px] sm:text-[11px] text-slate-400 font-medium truncate">Pengadaan tahun {{ date('Y') }}</p>
                            </div>
                            <div class="flex h-9 w-9 sm:h-11 sm:w-11 shrink-0 items-center justify-center rounded-lg sm:rounded-xl bg-teal-50 text-teal-600 transition-transform duration-300 group-hover:scale-110 group-hover:bg-teal-600 group-hover:text-white shadow-xs">
                                <svg class="h-4.5 w-4.5 sm:h-5 sm:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                            <span>Tahun {{ date('Y') }}</span>
                            <span class="font-semibold text-teal-700">+{{ $barangBaruTahunIni ?? 0 }} Aset</span>
                        </div>
                    </article>

                </div>
            </section>

            @include('dashboard.chart-hover')

            <section class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6" aria-label="Aktivitas terbaru">

                {{-- Peminjaman aktif (Gentelella v4 Hybrid List / Table) --}}
                <article class="animate-enter delay-550 min-w-0 overflow-hidden rounded-xl sm:rounded-2xl border border-slate-200/90 bg-white shadow-xs flex flex-col justify-between transition-all duration-300 hover:shadow-md">
                    <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between gap-3 bg-slate-50/50">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <h2 class="text-sm sm:text-base font-bold text-slate-900 tracking-tight">Peminjaman Aktif</h2>
                                <span class="inline-flex items-center rounded-full bg-sky-50 px-2 py-0.5 text-[10px] sm:text-xs font-semibold text-sky-700 border border-sky-200">
                                    {{ count($peminjamanAktif ?? []) }} Terkini
                                </span>
                            </div>
                            <p class="mt-0.5 text-xs text-slate-500 truncate">Daftar peminjaman barang laboratorium yang sedang berjalan.</p>
                        </div>
                        <a href="{{ route('peminjaman.index') }}" class="group inline-flex items-center gap-1.5 shrink-0 text-xs sm:text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition-colors p-1">
                            Lihat Semua <span class="transition-transform duration-200 group-hover:translate-x-1">&rarr;</span>
                        </a>
                    </div>

                    {{-- Mobile View (< md) --}}
                    <div class="divide-y divide-slate-100 md:hidden">
                        @forelse($peminjamanAktif ?? [] as $pinjam)
                            @php
                                $isTerlambat = \Carbon\Carbon::parse($pinjam->tanggal_kembali_rencana)->isPast();
                                $initials = strtoupper(substr($pinjam->nama_peminjam ?? 'U', 0, 2));
                            @endphp
                            <div class="p-3.5 space-y-2.5 transition-colors hover:bg-slate-50/70">
                                <div class="flex items-start justify-between gap-2.5">
                                    <div class="flex items-center gap-2.5 min-w-0 flex-1">
                                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-indigo-500 to-indigo-700 text-white text-[11px] font-bold shadow-xs">
                                            {{ $initials }}
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="font-semibold text-slate-900 text-xs sm:text-sm truncate">{{ $pinjam->nama_peminjam }}</p>
                                            <p class="text-[11px] sm:text-xs text-slate-500 truncate">{{ $pinjam->barang->nama_barang ?? '-' }}</p>
                                        </div>
                                    </div>
                                    @if($isTerlambat)
                                        <span class="inline-flex shrink-0 items-center rounded-full border border-rose-200 bg-rose-50 px-2 py-0.5 text-[10px] font-semibold text-rose-700">
                                            Terlambat
                                        </span>
                                    @else
                                        <span class="inline-flex shrink-0 items-center rounded-full border border-sky-200 bg-sky-50 px-2 py-0.5 text-[10px] font-semibold text-sky-700">
                                            Dipinjam
                                        </span>
                                    @endif
                                </div>
                                <div class="flex items-center justify-between text-[11px] pt-1.5 border-t border-slate-50">
                                    <span class="text-slate-400">Batas Kembali:</span>
                                    <span class="font-semibold tabular-nums {{ $isTerlambat ? 'text-rose-600' : 'text-slate-700' }}">
                                        {{ \Carbon\Carbon::parse($pinjam->tanggal_kembali_rencana)->format('d/m/Y') }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="px-4 py-8 text-center text-xs text-slate-500">
                                Tidak ada peminjaman aktif saat ini.
                            </div>
                        @endforelse
                    </div>

                    {{-- Desktop View (>= md) --}}
                    <div class="dashboard-scrollbar hidden md:block overflow-x-auto">
                        <table class="w-full text-left text-xs sm:text-sm">
                            <thead class="bg-slate-50/80 text-[11px] font-semibold uppercase tracking-wider text-slate-500 border-b border-slate-100">
                                <tr>
                                    <th scope="col" class="px-4 sm:px-5 py-3">Peminjam</th>
                                    <th scope="col" class="px-4 sm:px-5 py-3">Barang</th>
                                    <th scope="col" class="px-4 sm:px-5 py-3">Batas Kembali</th>
                                    <th scope="col" class="px-4 sm:px-5 py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($peminjamanAktif ?? [] as $pinjam)
                                    @php
                                        $isTerlambat = \Carbon\Carbon::parse($pinjam->tanggal_kembali_rencana)->isPast();
                                        $initials = strtoupper(substr($pinjam->nama_peminjam ?? 'U', 0, 2));
                                    @endphp
                                    <tr class="transition-colors hover:bg-slate-50/80">
                                        <td class="px-4 sm:px-5 py-3.5">
                                            <div class="flex items-center gap-2.5">
                                                <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-indigo-500 to-indigo-700 text-white text-[10px] font-bold shadow-xs">
                                                    {{ $initials }}
                                                </div>
                                                <span class="font-semibold text-slate-900 truncate max-w-[140px]">{{ $pinjam->nama_peminjam }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 sm:px-5 py-3.5 text-slate-600 truncate max-w-[160px]">{{ $pinjam->barang->nama_barang ?? '-' }}</td>
                                        <td class="whitespace-nowrap px-4 sm:px-5 py-3.5 tabular-nums text-slate-600 font-medium">
                                            {{ \Carbon\Carbon::parse($pinjam->tanggal_kembali_rencana)->format('d/m/Y') }}
                                        </td>
                                        <td class="px-4 sm:px-5 py-3.5">
                                            @if($isTerlambat)
                                                <span class="inline-flex rounded-full border border-rose-200 bg-rose-50 px-2.5 py-0.5 text-xs font-semibold text-rose-700">
                                                    Terlambat
                                                </span>
                                            @else
                                                <span class="inline-flex rounded-full border border-sky-200 bg-sky-50 px-2.5 py-0.5 text-xs font-semibold text-sky-700">
                                                    Dipinjam
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 sm:px-5 py-8 text-center text-xs text-slate-500">
                                            Tidak ada peminjaman aktif saat ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </article>

                {{-- Barang baru ditambahkan (Gentelella v4 Style) --}}
                <article class="animate-enter delay-600 min-w-0 overflow-hidden rounded-xl sm:rounded-2xl border border-slate-200/90 bg-white shadow-xs flex flex-col justify-between transition-all duration-300 hover:shadow-md">
                    <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between gap-3 bg-slate-50/50">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <h2 class="text-sm sm:text-base font-bold text-slate-900 tracking-tight">Barang Baru</h2>
                                <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] sm:text-xs font-semibold text-emerald-700 border border-emerald-200">
                                    {{ count($barangTerbaru ?? []) }} Data
                                </span>
                            </div>
                            <p class="mt-0.5 text-xs text-slate-500 truncate">Inventaris laboratorium yang baru ditambahkan.</p>
                        </div>
                        <a href="{{ route('barang.index') }}" class="group inline-flex items-center gap-1.5 shrink-0 text-xs sm:text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition-colors p-1">
                            Lihat Semua <span class="transition-transform duration-200 group-hover:translate-x-1">&rarr;</span>
                        </a>
                    </div>

                    {{-- Mobile View (< md) --}}
                    <div class="divide-y divide-slate-100 md:hidden">
                        @forelse($barangTerbaru ?? [] as $brg)
                            @php
                                $kBadge = $conditionBadge($brg->kondisi);
                                $initials = strtoupper(substr($brg->nama_barang ?? 'B', 0, 2));
                            @endphp
                            <div class="p-3.5 space-y-2.5 transition-colors hover:bg-slate-50/70">
                                <div class="flex items-start justify-between gap-2.5">
                                    <div class="flex items-center gap-2.5 min-w-0 flex-1">
                                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-emerald-500 to-teal-700 text-white text-[10px] font-bold shadow-xs">
                                            {{ $initials }}
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="font-semibold text-slate-900 text-xs sm:text-sm truncate">{{ $brg->nama_barang }}</p>
                                            <p class="font-mono text-[11px] text-slate-500 truncate">{{ $brg->kode_barang }}</p>
                                        </div>
                                    </div>
                                    <span class="{{ $kBadge }} inline-flex shrink-0 items-center rounded-full border px-2 py-0.5 text-[10px] font-semibold">
                                        {{ $brg->kondisi }}
                                    </span>
                                </div>
                                <div class="grid grid-cols-2 gap-2 text-[11px] pt-1.5 border-t border-slate-50">
                                    <div class="truncate">
                                        <span class="text-slate-400">Kategori: </span>
                                        <span class="font-medium text-slate-700">{{ $brg->kategori }}</span>
                                    </div>
                                    <div class="text-right truncate">
                                        <span class="text-slate-400">Stok: </span>
                                        <span class="font-semibold text-slate-900">{{ $brg->jumlah }} {{ $brg->satuan }}</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="px-4 py-8 text-center text-xs text-slate-500">
                                Belum ada barang baru yang ditambahkan.
                            </div>
                        @endforelse
                    </div>

                    {{-- Desktop View (>= md) --}}
                    <div class="dashboard-scrollbar hidden md:block overflow-x-auto">
                        <table class="w-full text-left text-xs sm:text-sm">
                            <thead class="bg-slate-50/80 text-[11px] font-semibold uppercase tracking-wider text-slate-500 border-b border-slate-100">
                                <tr>
                                    <th scope="col" class="px-4 sm:px-5 py-3">Barang</th>
                                    <th scope="col" class="px-4 sm:px-5 py-3">Kategori</th>
                                    <th scope="col" class="px-4 sm:px-5 py-3">Kondisi</th>
                                    <th scope="col" class="px-4 sm:px-5 py-3">Stok</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($barangTerbaru ?? [] as $brg)
                                    @php
                                        $kBadge = $conditionBadge($brg->kondisi);
                                        $initials = strtoupper(substr($brg->nama_barang ?? 'B', 0, 2));
                                    @endphp
                                    <tr class="transition-colors hover:bg-slate-50/80">
                                        <td class="px-4 sm:px-5 py-3.5">
                                            <div class="flex items-center gap-2.5">
                                                <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-emerald-500 to-teal-700 text-white text-[10px] font-bold shadow-xs">
                                                    {{ $initials }}
                                                </div>
                                                <div>
                                                    <p class="font-semibold text-slate-900 truncate max-w-[160px]">{{ $brg->nama_barang }}</p>
                                                    <p class="font-mono text-[11px] text-slate-400 truncate">{{ $brg->kode_barang }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 sm:px-5 py-3.5 text-slate-600 truncate max-w-[120px] font-medium">{{ $brg->kategori }}</td>
                                        <td class="px-4 sm:px-5 py-3.5">
                                            <span class="{{ $kBadge }} inline-flex rounded-full border px-2.5 py-0.5 text-xs font-semibold">
                                                {{ $brg->kondisi }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-4 sm:px-5 py-3.5 font-bold text-slate-800 tabular-nums">
                                            {{ $brg->jumlah }} <span class="font-normal text-slate-500 text-xs">{{ $brg->satuan }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 sm:px-5 py-8 text-center text-xs text-slate-500">
                                            Belum ada barang baru yang ditambahkan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </article>

            </section>

        </div>
    </div>
</div>

<script>
(function() {
    function initDashboardEntrance() {
        var container = document.getElementById('dashboardAdminContainer');
        if (!container) return;

        var hasIntro = window.__HAS_INTRO_SCREEN || (sessionStorage.getItem('dashboard_intro') === 'true');
        var introEl = document.getElementById('introScreen');
        var isIntroActive = introEl && (introEl.classList.contains('is-active') || introEl.style.display !== 'none');

        var revealed = false;
        function triggerReveal() {
            if (revealed) return;
            revealed = true;
            if (container) {
                container.classList.add('is-revealed');
            }

            // Re-inisialisasi chart animasi agar mulai menggambar kurva tepat saat dashboard tersingkap
            if (typeof window.initGentelellaDashboardCharts === 'function') {
                requestAnimationFrame(function() {
                    try { window.initGentelellaDashboardCharts(); } catch(e) {}
                });
            }
        }

        if (hasIntro || isIntroActive) {
            // Ketika loading screen mulai ngeslide ke atas (di awal atau pertengahan slide), trigger animasi masuk
            var slideTriggered = false;
            var onSlideStart = function() {
                if (slideTriggered) return;
                slideTriggered = true;
                setTimeout(triggerReveal, 100);
            };

            window.addEventListener('intro-slide-start', onSlideStart, { once: true });
            window.addEventListener('intro-slide-mid', triggerReveal, { once: true });

            // Fallback safety: jika event terlewat karena timing, tetap tampilkan elemen
            setTimeout(function() {
                triggerReveal();
            }, 1200);
        } else {
            // Akses langsung / refresh biasa / SPA: jalankan animasi masuk seketika
            triggerReveal();
            requestAnimationFrame(function() {
                triggerReveal();
            });
        }
    }

    window.initDashboardEntrance = initDashboardEntrance;

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDashboardEntrance);
    } else {
        initDashboardEntrance();
    }
})();
</script>
@endsection