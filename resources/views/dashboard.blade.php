@extends('layouts.app')

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
            transform: translateY(20px) scale(0.99);
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

    /* Animation utility classes */
    .animate-enter {
        opacity: 0;
        animation: heroFadeUp 0.65s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    .delay-75  { animation-delay: 75ms; }
    .delay-100 { animation-delay: 100ms; }
    .delay-150 { animation-delay: 150ms; }
    .delay-200 { animation-delay: 200ms; }
    .delay-250 { animation-delay: 250ms; }
    .delay-300 { animation-delay: 300ms; }
    .delay-350 { animation-delay: 350ms; }
    .delay-400 { animation-delay: 400ms; }
    .delay-500 { animation-delay: 500ms; }

    /* Animated progress bar */
    .progress-bar-fill {
        animation: barGrow 1.2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        animation-delay: 400ms;
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
        .animate-enter,
        .progress-bar-fill,
        .shimmer-effect::after {
            animation: none !important;
            opacity: 1 !important;
            transform: none !important;
            width: var(--target-width, 100%) !important;
        }
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
@endphp

<div class="min-h-full bg-slate-50/70 pb-12 sm:pb-16 pb-[calc(3rem+env(safe-area-inset-bottom,0px))]">
    <main class="mx-auto w-full max-w-screen-2xl px-3 sm:px-6 lg:px-8 py-4 sm:py-6 lg:py-8">
        <div class="space-y-4 sm:space-y-6">

            @if(isset($peminjamanTerlambat) && $peminjamanTerlambat > 0)
                <section class="animate-enter flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4 rounded-xl border border-rose-200 bg-rose-50/95 p-3.5 sm:p-4 shadow-sm backdrop-blur transition-all duration-300 hover:shadow-md" role="alert">
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
                    <article class="animate-enter delay-100 group relative flex items-center justify-between overflow-hidden rounded-xl sm:rounded-2xl border border-slate-200/90 bg-white p-3 sm:p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:border-indigo-200">
                        <div class="min-w-0 flex-1 pr-1.5 sm:pr-3">
                            <p class="text-[11px] sm:text-xs font-medium text-slate-500 uppercase tracking-wide truncate">Total Barang</p>
                            <p class="mt-1 sm:mt-1.5 text-xl sm:text-3xl font-bold tabular-nums text-slate-900 group-hover:text-indigo-600 transition-colors">
                                {{ number_format($totalBarangValue) }}
                            </p>
                        </div>
                        <div class="flex h-8 w-8 sm:h-11 sm:w-11 shrink-0 items-center justify-center rounded-lg sm:rounded-xl bg-indigo-50 text-indigo-600 transition-transform duration-300 group-hover:scale-110 group-hover:bg-indigo-600 group-hover:text-white shadow-sm">
                            <svg class="h-4 w-4 sm:h-6 sm:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                    </article>

                    {{-- Kondisi baik --}}
                    <article class="animate-enter delay-150 group relative flex items-center justify-between overflow-hidden rounded-xl sm:rounded-2xl border border-slate-200/90 bg-white p-3 sm:p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:border-emerald-200">
                        <div class="min-w-0 flex-1 pr-1.5 sm:pr-3">
                            <p class="text-[11px] sm:text-xs font-medium text-slate-500 uppercase tracking-wide truncate">Kondisi Baik</p>
                            <p class="mt-1 sm:mt-1.5 text-xl sm:text-3xl font-bold tabular-nums text-emerald-600">
                                {{ number_format($barangBaik ?? 0) }}
                            </p>
                        </div>
                        <div class="flex h-8 w-8 sm:h-11 sm:w-11 shrink-0 items-center justify-center rounded-lg sm:rounded-xl bg-emerald-50 text-emerald-600 transition-transform duration-300 group-hover:scale-110 group-hover:bg-emerald-600 group-hover:text-white shadow-sm">
                            <svg class="h-4 w-4 sm:h-6 sm:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </article>

                    {{-- Perbaikan --}}
                    <article class="animate-enter delay-200 group relative flex items-center justify-between overflow-hidden rounded-xl sm:rounded-2xl border border-slate-200/90 bg-white p-3 sm:p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:border-amber-200">
                        <div class="min-w-0 flex-1 pr-1.5 sm:pr-3">
                            <p class="text-[11px] sm:text-xs font-medium text-slate-500 uppercase tracking-wide truncate">Dalam Perbaikan</p>
                            <p class="mt-1 sm:mt-1.5 text-xl sm:text-3xl font-bold tabular-nums text-amber-600">
                                {{ number_format($barangPerbaikan ?? 0) }}
                            </p>
                        </div>
                        <div class="flex h-8 w-8 sm:h-11 sm:w-11 shrink-0 items-center justify-center rounded-lg sm:rounded-xl bg-amber-50 text-amber-600 transition-transform duration-300 group-hover:scale-110 group-hover:bg-amber-500 group-hover:text-white shadow-sm">
                            <svg class="h-4 w-4 sm:h-6 sm:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            </svg>
                        </div>
                    </article>

                    {{-- Rusak berat --}}
                    <article class="animate-enter delay-250 group relative flex items-center justify-between overflow-hidden rounded-xl sm:rounded-2xl border border-slate-200/90 bg-white p-3 sm:p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:border-rose-200">
                        <div class="min-w-0 flex-1 pr-1.5 sm:pr-3">
                            <p class="text-[11px] sm:text-xs font-medium text-slate-500 uppercase tracking-wide truncate">Rusak Berat</p>
                            <p class="mt-1 sm:mt-1.5 text-xl sm:text-3xl font-bold tabular-nums text-rose-600">
                                {{ number_format($barangRusak ?? 0) }}
                            </p>
                        </div>
                        <div class="flex h-8 w-8 sm:h-11 sm:w-11 shrink-0 items-center justify-center rounded-lg sm:rounded-xl bg-rose-50 text-rose-600 transition-transform duration-300 group-hover:scale-110 group-hover:bg-rose-600 group-hover:text-white shadow-sm">
                            <svg class="h-4 w-4 sm:h-6 sm:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                    </article>

                    {{-- Nilai aset (Span 2 in mobile for long currency display) --}}
                    <article class="animate-enter delay-300 col-span-2 md:col-span-1 lg:col-span-1 group relative flex items-center justify-between overflow-hidden rounded-xl sm:rounded-2xl border border-slate-200/90 bg-white p-3.5 sm:p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:border-slate-300">
                        <div class="min-w-0 flex-1 pr-2 sm:pr-3">
                            <p class="text-[11px] sm:text-xs font-medium text-slate-500 uppercase tracking-wide truncate">Nilai Aset Lab</p>
                            <p class="mt-1 sm:mt-1.5 text-lg sm:text-2xl font-bold tabular-nums text-slate-900 truncate group-hover:text-indigo-600 transition-colors">
                                Rp {{ number_format($totalNilai ?? 0, 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="flex h-8 w-8 sm:h-11 sm:w-11 shrink-0 items-center justify-center rounded-lg sm:rounded-xl bg-slate-100 text-slate-700 transition-transform duration-300 group-hover:scale-110 group-hover:bg-slate-800 group-hover:text-white shadow-sm">
                            <svg class="h-4 w-4 sm:h-6 sm:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </article>

                    {{-- Dipinjam aktif --}}
                    <article class="animate-enter delay-350 group relative flex items-center justify-between overflow-hidden rounded-xl sm:rounded-2xl border border-slate-200/90 bg-white p-3 sm:p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:border-sky-200">
                        <div class="min-w-0 flex-1 pr-1.5 sm:pr-3">
                            <p class="text-[11px] sm:text-xs font-medium text-slate-500 uppercase tracking-wide truncate">Dipinjam Aktif</p>
                            <p class="mt-1 sm:mt-1.5 text-xl sm:text-3xl font-bold tabular-nums text-sky-600">
                                {{ number_format($barangDipinjam ?? 0) }}
                            </p>
                        </div>
                        <div class="flex h-8 w-8 sm:h-11 sm:w-11 shrink-0 items-center justify-center rounded-lg sm:rounded-xl bg-sky-50 text-sky-600 transition-transform duration-300 group-hover:scale-110 group-hover:bg-sky-600 group-hover:text-white shadow-sm">
                            <svg class="h-4 w-4 sm:h-6 sm:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                            </svg>
                        </div>
                    </article>

                    {{-- Maintenance bulan ini --}}
                    <article class="animate-enter delay-400 group relative flex items-center justify-between overflow-hidden rounded-xl sm:rounded-2xl border border-slate-200/90 bg-white p-3 sm:p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:border-violet-200">
                        <div class="min-w-0 flex-1 pr-1.5 sm:pr-3">
                            <p class="text-[11px] sm:text-xs font-medium text-slate-500 uppercase tracking-wide truncate">Maintenance Bulan Ini</p>
                            <p class="mt-1 sm:mt-1.5 text-xl sm:text-3xl font-bold tabular-nums text-violet-600">
                                {{ number_format($maintenanceBulanIni ?? 0) }}
                            </p>
                        </div>
                        <div class="flex h-8 w-8 sm:h-11 sm:w-11 shrink-0 items-center justify-center rounded-lg sm:rounded-xl bg-violet-50 text-violet-600 transition-transform duration-300 group-hover:scale-110 group-hover:bg-violet-600 group-hover:text-white shadow-sm">
                            <svg class="h-4 w-4 sm:h-6 sm:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                            </svg>
                        </div>
                    </article>

                    {{-- Barang baru tahun ini --}}
                    <article class="animate-enter delay-500 group relative flex items-center justify-between overflow-hidden rounded-xl sm:rounded-2xl border border-slate-200/90 bg-white p-3 sm:p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:border-teal-200">
                        <div class="min-w-0 flex-1 pr-1.5 sm:pr-3">
                            <p class="text-[11px] sm:text-xs font-medium text-slate-500 uppercase tracking-wide truncate">Barang Baru (Tahun Ini)</p>
                            <p class="mt-1 sm:mt-1.5 text-xl sm:text-3xl font-bold tabular-nums text-teal-600">
                                {{ number_format($barangBaruTahunIni ?? 0) }}
                            </p>
                        </div>
                        <div class="flex h-8 w-8 sm:h-11 sm:w-11 shrink-0 items-center justify-center rounded-lg sm:rounded-xl bg-teal-50 text-teal-600 transition-transform duration-300 group-hover:scale-110 group-hover:bg-teal-600 group-hover:text-white shadow-sm">
                            <svg class="h-4 w-4 sm:h-6 sm:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                    </article>

                </div>
            </section>

            <section class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6" aria-label="Statistik inventaris">
                
                {{-- Distribusi Kondisi Barang --}}
                <article class="animate-enter delay-300 rounded-xl sm:rounded-2xl border border-slate-200/90 bg-white shadow-sm overflow-hidden flex flex-col justify-between transition-all duration-300 hover:shadow-md">
                    <div class="bg-slate-900 p-4 sm:p-5 border-b border-slate-800 flex items-center justify-between">
                        <div>
                            <h2 class="text-sm sm:text-base font-bold text-white">Distribusi Kondisi Barang</h2>
                            <p class="mt-0.5 text-xs sm:text-sm text-slate-300">Persentase kondisi dari keseluruhan barang inventaris.</p>
                        </div>
                        <span class="flex h-2.5 w-2.5 rounded-full bg-emerald-400 shadow-sm animate-pulse" title="Realtime Data"></span>
                    </div>

                    <div class="space-y-4 p-4 sm:p-5 flex-1">
                        @forelse($kondisiStats ?? [] as $kondisi => $count)
                            @php
                                $percentage = min(round(((int) $count / $safeTotalBarang) * 100, 1), 100);
                            @endphp
                            <div class="group">
                                <div class="mb-1.5 flex items-center justify-between gap-3 text-xs sm:text-sm">
                                    <span class="font-medium text-slate-700 truncate group-hover:text-slate-950 transition-colors">{{ $kondisi }}</span>
                                    <span class="shrink-0 tabular-nums text-slate-500">
                                        <span class="font-semibold text-slate-900">{{ number_format($count) }}</span> unit 
                                        <span class="text-slate-400 font-normal">({{ $percentage }}%)</span>
                                    </span>
                                </div>
                                <div class="relative h-2.5 sm:h-3 w-full overflow-hidden rounded-full bg-slate-100/90 p-0.5 ring-1 ring-inset ring-slate-200/40" aria-hidden="true">
                                    <div class="progress-bar-fill shimmer-effect relative h-full rounded-full transition-all duration-500 {{ $kondisiColors[$kondisi] ?? 'bg-indigo-500' }}" style="--target-width: {{ $percentage }}%; width: {{ $percentage }}%;"></div>
                                </div>
                            </div>
                        @empty
                            <div class="flex h-32 items-center justify-center rounded-lg border border-dashed border-slate-200 text-xs sm:text-sm text-slate-500">
                                Data kondisi barang belum tersedia.
                            </div>
                        @endforelse
                    </div>
                </article>

                {{-- Kategori Barang --}}
                <article class="animate-enter delay-350 rounded-xl sm:rounded-2xl border border-slate-200/90 bg-white shadow-sm overflow-hidden flex flex-col justify-between transition-all duration-300 hover:shadow-md">
                    <div class="bg-slate-900 p-4 sm:p-5 border-b border-slate-800 flex items-center justify-between">
                        <div>
                            <h2 class="text-sm sm:text-base font-bold text-white">Kategori Barang</h2>
                            <p class="mt-0.5 text-xs sm:text-sm text-slate-300">Sebaran kuantitas inventaris berdasarkan kategori alat.</p>
                        </div>
                        <span class="flex h-2.5 w-2.5 rounded-full bg-indigo-400 shadow-sm animate-pulse" title="Realtime Data"></span>
                    </div>

                    <div class="space-y-4 p-4 sm:p-5 flex-1">
                        @forelse($kategoriStats ?? [] as $stat)
                            @php
                                $categoryTotal = (int) ($stat->total ?? 0);
                                $percentage    = min(round(($categoryTotal / $safeTotalBarang) * 100, 1), 100);
                            @endphp
                            <div class="group">
                                <div class="mb-1.5 flex items-center justify-between gap-3 text-xs sm:text-sm">
                                    <span class="font-medium text-slate-700 truncate group-hover:text-slate-950 transition-colors">{{ $stat->kategori }}</span>
                                    <span class="shrink-0 tabular-nums text-slate-500">
                                        <span class="font-semibold text-slate-900">{{ number_format($categoryTotal) }}</span> unit
                                        <span class="text-slate-400 font-normal">({{ $percentage }}%)</span>
                                    </span>
                                </div>
                                <div class="relative h-2.5 sm:h-3 w-full overflow-hidden rounded-full bg-slate-100/90 p-0.5 ring-1 ring-inset ring-slate-200/40" aria-hidden="true">
                                    <div class="progress-bar-fill shimmer-effect relative h-full rounded-full bg-gradient-to-r from-indigo-500 via-indigo-600 to-indigo-700 transition-all duration-500" style="--target-width: {{ $percentage }}%; width: {{ $percentage }}%;"></div>
                                </div>
                            </div>
                        @empty
                            <div class="flex h-32 items-center justify-center rounded-lg border border-dashed border-slate-200 text-xs sm:text-sm text-slate-500">
                                Data kategori barang belum tersedia.
                            </div>
                        @endforelse
                    </div>
                </article>

            </section>

            <section class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6" aria-label="Aktivitas terbaru">

                {{-- Peminjaman aktif --}}
                <article class="animate-enter delay-400 min-w-0 overflow-hidden rounded-xl sm:rounded-2xl border border-slate-200/90 bg-white shadow-sm flex flex-col justify-between transition-all duration-300 hover:shadow-md">
                    <div class="bg-slate-900 p-4 sm:p-5 border-b border-slate-800 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <h2 class="text-sm sm:text-base font-bold text-white">Peminjaman Aktif</h2>
                            <p class="mt-0.5 text-xs text-slate-300 truncate">Barang yang sedang dipinjam saat ini.</p>
                        </div>
                        <a href="{{ route('peminjaman.index') }}" class="group inline-flex items-center gap-1 shrink-0 text-xs sm:text-sm font-semibold text-indigo-300 hover:text-white focus:outline-none focus-visible:underline p-1 transition-colors">
                            Lihat semua <span class="transition-transform duration-200 group-hover:translate-x-1">&rarr;</span>
                        </a>
                    </div>

                    {{-- Mobile View (< md) --}}
                    <div class="divide-y divide-slate-100 md:hidden">
                        @forelse($peminjamanAktif ?? [] as $pinjam)
                            @php
                                $isTerlambat = \Carbon\Carbon::parse($pinjam->tanggal_kembali_rencana)->isPast();
                            @endphp
                            <div class="p-3.5 sm:p-4 space-y-2.5 transition-colors hover:bg-slate-50/60">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0 flex-1">
                                        <p class="font-semibold text-slate-900 text-sm truncate">{{ $pinjam->nama_peminjam }}</p>
                                        <p class="text-xs text-slate-500 mt-0.5 truncate">{{ $pinjam->barang->nama_barang ?? '-' }}</p>
                                    </div>
                                    @if($isTerlambat)
                                        <span class="inline-flex shrink-0 items-center rounded-full border border-rose-200 bg-rose-50 px-2 py-0.5 text-[11px] font-semibold text-rose-700 shadow-sm">
                                            Terlambat
                                        </span>
                                    @else
                                        <span class="inline-flex shrink-0 items-center rounded-full border border-sky-200 bg-sky-50 px-2 py-0.5 text-[11px] font-semibold text-sky-700 shadow-sm">
                                            Dipinjam
                                        </span>
                                    @endif
                                </div>
                                <div class="flex items-center justify-between text-xs pt-1 border-t border-slate-50">
                                    <span class="text-slate-500">Batas Kembali:</span>
                                    <span class="font-medium tabular-nums {{ $isTerlambat ? 'text-rose-600 font-semibold' : 'text-slate-700' }}">
                                        {{ \Carbon\Carbon::parse($pinjam->tanggal_kembali_rencana)->format('d/m/Y') }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="px-4 py-8 text-center text-xs sm:text-sm text-slate-500">
                                Tidak ada peminjaman aktif saat ini.
                            </div>
                        @endforelse
                    </div>

                    {{-- Desktop View (>= md) --}}
                    <div class="dashboard-scrollbar hidden md:block overflow-x-auto">
                        <table class="w-full text-left text-xs sm:text-sm">
                            <thead class="bg-slate-50/90 text-[11px] sm:text-xs font-bold uppercase tracking-wider text-slate-600 border-b border-slate-200">
                                <tr>
                                    <th scope="col" class="px-4 sm:px-5 py-3.5 text-slate-600 font-bold">Peminjam</th>
                                    <th scope="col" class="px-4 sm:px-5 py-3.5 text-slate-600 font-bold">Barang</th>
                                    <th scope="col" class="px-4 sm:px-5 py-3.5 text-slate-600 font-bold">Batas Kembali</th>
                                    <th scope="col" class="px-4 sm:px-5 py-3.5 text-slate-600 font-bold">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($peminjamanAktif ?? [] as $pinjam)
                                    @php
                                        $isTerlambat = \Carbon\Carbon::parse($pinjam->tanggal_kembali_rencana)->isPast();
                                    @endphp
                                    <tr class="transition-colors hover:bg-slate-50/80">
                                        <td class="px-4 sm:px-5 py-3.5 font-medium text-slate-900 truncate max-w-[150px]">{{ $pinjam->nama_peminjam }}</td>
                                        <td class="px-4 sm:px-5 py-3.5 text-slate-600 truncate max-w-[180px]">{{ $pinjam->barang->nama_barang ?? '-' }}</td>
                                        <td class="whitespace-nowrap px-4 sm:px-5 py-3.5 tabular-nums text-slate-600">
                                            {{ \Carbon\Carbon::parse($pinjam->tanggal_kembali_rencana)->format('d/m/Y') }}
                                        </td>
                                        <td class="px-4 sm:px-5 py-3.5">
                                            @if($isTerlambat)
                                                <span class="inline-flex rounded-full border border-rose-200 bg-rose-50 px-2.5 py-0.5 text-xs font-semibold text-rose-700 shadow-sm">
                                                    Terlambat
                                                </span>
                                            @else
                                                <span class="inline-flex rounded-full border border-sky-200 bg-sky-50 px-2.5 py-0.5 text-xs font-semibold text-sky-700 shadow-sm">
                                                    Dipinjam
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 sm:px-5 py-8 text-center text-xs sm:text-sm text-slate-500">
                                            Tidak ada peminjaman aktif saat ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </article>

                {{-- Barang baru ditambahkan --}}
                <article class="animate-enter delay-500 min-w-0 overflow-hidden rounded-xl sm:rounded-2xl border border-slate-200/90 bg-white shadow-sm flex flex-col justify-between transition-all duration-300 hover:shadow-md">
                    <div class="bg-slate-900 p-4 sm:p-5 border-b border-slate-800 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <h2 class="text-sm sm:text-base font-bold text-white">Barang Baru</h2>
                            <p class="mt-0.5 text-xs text-slate-300 truncate">Inventaris yang baru ditambahkan.</p>
                        </div>
                        <a href="{{ route('barang.index') }}" class="group inline-flex items-center gap-1 shrink-0 text-xs sm:text-sm font-semibold text-indigo-300 hover:text-white focus:outline-none focus-visible:underline p-1 transition-colors">
                            Lihat semua <span class="transition-transform duration-200 group-hover:translate-x-1">&rarr;</span>
                        </a>
                    </div>

                    {{-- Mobile View (< md) --}}
                    <div class="divide-y divide-slate-100 md:hidden">
                        @forelse($barangTerbaru ?? [] as $brg)
                            @php
                                $kBadge = $conditionBadge($brg->kondisi);
                            @endphp
                            <div class="p-3.5 sm:p-4 space-y-2.5 transition-colors hover:bg-slate-50/60">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0 flex-1">
                                        <p class="font-semibold text-slate-900 text-sm truncate">{{ $brg->nama_barang }}</p>
                                        <p class="font-mono text-xs text-slate-500 mt-0.5 truncate">{{ $brg->kode_barang }}</p>
                                    </div>
                                    <span class="{{ $kBadge }} inline-flex shrink-0 items-center rounded-full border px-2 py-0.5 text-[11px] font-semibold shadow-sm">
                                        {{ $brg->kondisi }}
                                    </span>
                                </div>
                                <div class="grid grid-cols-2 gap-2 text-xs pt-1 border-t border-slate-50">
                                    <div class="truncate">
                                        <span class="text-slate-400">Kategori: </span>
                                        <span class="font-medium text-slate-700">{{ $brg->kategori }}</span>
                                    </div>
                                    <div class="text-right truncate">
                                        <span class="text-slate-400">Stok: </span>
                                        <span class="font-medium text-slate-900">{{ $brg->jumlah }} {{ $brg->satuan }}</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="px-4 py-8 text-center text-xs sm:text-sm text-slate-500">
                                Belum ada barang baru yang ditambahkan.
                            </div>
                        @endforelse
                    </div>

                    {{-- Desktop View (>= md) --}}
                    <div class="dashboard-scrollbar hidden md:block overflow-x-auto">
                        <table class="w-full text-left text-xs sm:text-sm">
                            <thead class="bg-slate-50/90 text-[11px] sm:text-xs font-bold uppercase tracking-wider text-slate-600 border-b border-slate-200">
                                <tr>
                                    <th scope="col" class="px-4 sm:px-5 py-3.5 text-slate-600 font-bold">Barang</th>
                                    <th scope="col" class="px-4 sm:px-5 py-3.5 text-slate-600 font-bold">Kategori</th>
                                    <th scope="col" class="px-4 sm:px-5 py-3.5 text-slate-600 font-bold">Kondisi</th>
                                    <th scope="col" class="px-4 sm:px-5 py-3.5 text-slate-600 font-bold">Stok</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($barangTerbaru ?? [] as $brg)
                                    @php
                                        $kBadge = $conditionBadge($brg->kondisi);
                                    @endphp
                                    <tr class="transition-colors hover:bg-slate-50/80">
                                        <td class="px-4 sm:px-5 py-3.5">
                                            <p class="font-medium text-slate-900 truncate max-w-[180px]">{{ $brg->nama_barang }}</p>
                                            <p class="font-mono text-xs text-slate-500 mt-0.5 truncate">{{ $brg->kode_barang }}</p>
                                        </td>
                                        <td class="px-4 sm:px-5 py-3.5 text-slate-600 truncate max-w-[120px]">{{ $brg->kategori }}</td>
                                        <td class="px-4 sm:px-5 py-3.5">
                                            <span class="{{ $kBadge }} inline-flex rounded-full border px-2.5 py-0.5 text-xs font-semibold shadow-sm">
                                                {{ $brg->kondisi }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-4 sm:px-5 py-3.5 font-medium text-slate-700 tabular-nums">
                                            {{ $brg->jumlah }} {{ $brg->satuan }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 sm:px-5 py-8 text-center text-xs sm:text-sm text-slate-500">
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
    </main>
</div>
@endsection