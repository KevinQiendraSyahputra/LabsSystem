@extends('layouts.app')

@section('title', 'Beranda - Winshark Community')
@section('page_title', 'Selamat Datang, ' . Auth::user()->name)
@section('page_subtitle', 'Sistem Peminjaman Laboratorium TKJ • Winshark Community')

@push('styles')
<style>
    @keyframes kenBurnsSlow {
        0% { transform: scale(1); }
        50% { transform: scale(1.10); }
        100% { transform: scale(1); }
    }

    .animate-ken-burns {
        animation: kenBurnsSlow 10s ease-in-out infinite alternate;
    }

    @keyframes csPulseRing {
        0% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(79, 70, 229, 0.5);
        }
        70% {
            transform: scale(1);
            box-shadow: 0 0 0 10px rgba(79, 70, 229, 0);
        }
        100% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(79, 70, 229, 0);
        }
    }

    .cs-glow-pulse {
        animation: csPulseRing 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    /* Animasi masuk halaman */
    [data-page-reveal] {
        will-change: opacity, transform;
    }

    .page-reveal-ready {
        opacity: 0;
        transform: translateY(12px);
    }

    .page-reveal-ready.page-reveal-visible {
        opacity: 1;
        transform: translateY(0);
        transition:
            opacity 460ms cubic-bezier(0.22, 1, 0.36, 1),
            transform 460ms cubic-bezier(0.22, 1, 0.36, 1);
        transition-delay: var(--page-delay, 0ms);
    }

    @media (max-width: 639px) {
        .page-reveal-ready {
            transform: translateY(8px);
        }
    }

    /* Number flow counter */
    .number-flow {
        display: inline-block;
        min-width: 1ch;
        font-variant-numeric: tabular-nums;
        font-feature-settings: "tnum" 1;
        will-change: transform, opacity;
    }

    .number-flow.number-flow-running {
        animation: numberFlowPop 520ms cubic-bezier(0.22, 1, 0.36, 1) both;
    }

    @keyframes numberFlowPop {
        0% {
            opacity: 0.45;
            transform: translateY(5px) scale(0.985);
        }
        55% {
            opacity: 1;
            transform: translateY(-1px) scale(1.015);
        }
        100% {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    /* Struktur kepengurusan */
    .org-chart {
        --org-line: #cbd5e1;
        --org-line-strong: #94a3b8;
    }

    .org-card {
        box-shadow: 0 8px 30px -24px rgba(15, 23, 42, 0.5);
        transition: transform 200ms cubic-bezier(0.16, 1, 0.3, 1), border-color 200ms ease, box-shadow 200ms ease;
    }

    @media (hover: hover) and (pointer: fine) {
        .org-card:hover {
            transform: translateY(-4px);
            border-color: #cbd5e1;
            box-shadow: 0 20px 40px -20px rgba(15, 23, 42, 0.45);
        }
    }

    .org-photo {
        position: relative;
        overflow: hidden;
        background:
            linear-gradient(180deg, rgba(248, 250, 252, 0.98), rgba(241, 245, 249, 0.98));
    }

    .org-photo::after {
        content: '';
        position: absolute;
        inset: 0;
        pointer-events: none;
        border-radius: inherit;
        box-shadow: inset 0 0 0 1px rgba(148, 163, 184, 0.16);
    }

    .org-line {
        position: absolute;
        z-index: 0;
        background: var(--org-line);
        pointer-events: none;
    }

    .org-line-v {
        width: 1.5px;
    }

    .org-line-h {
        height: 1.5px;
    }

    .org-dot {
        position: absolute;
        z-index: 1;
        width: 7px;
        height: 7px;
        border: 2px solid #fff;
        border-radius: 9999px;
        background: var(--org-line-strong);
        box-shadow: 0 0 0 1px rgba(148, 163, 184, 0.35);
    }

    /* Progressive enhancement */
    .org-chart.org-animate-ready .org-reveal {
        opacity: 0;
        transform: translateY(14px);
    }

    .org-chart.org-animate-ready .org-line-v {
        transform: scaleY(0);
        transform-origin: top;
    }

    .org-chart.org-animate-ready .org-line-h {
        transform: scaleX(0);
        transform-origin: center;
    }

    .org-chart.org-animate-ready .org-dot {
        opacity: 0;
        transform: scale(0.4);
    }

    @keyframes orgReveal {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes orgDrawY {
        to { transform: scaleY(1); }
    }

    @keyframes orgDrawX {
        to { transform: scaleX(1); }
    }

    @keyframes orgDotIn {
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .org-chart.org-chart-visible .org-reveal {
        animation: orgReveal 520ms cubic-bezier(0.22, 1, 0.36, 1) forwards;
        animation-delay: var(--org-delay, 0ms);
    }

    .org-chart.org-chart-visible .org-line-v {
        animation: orgDrawY 420ms cubic-bezier(0.22, 1, 0.36, 1) forwards;
        animation-delay: var(--org-delay, 0ms);
    }

    .org-chart.org-chart-visible .org-line-h {
        animation: orgDrawX 480ms cubic-bezier(0.22, 1, 0.36, 1) forwards;
        animation-delay: var(--org-delay, 0ms);
    }

    .org-chart.org-chart-visible .org-dot {
        animation: orgDotIn 260ms ease forwards;
        animation-delay: var(--org-delay, 0ms);
    }

    @media (max-width: 1023px) {
        .org-desktop-branch {
            display: none !important;
        }
    }

    @media (min-width: 1024px) {
        .org-mobile-stem {
            display: none !important;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .animate-ken-burns,
        .cs-glow-pulse,
        .number-flow.number-flow-running {
            animation: none !important;
        }

        .org-chart.org-animate-ready .org-reveal,
        .org-chart.org-animate-ready .org-dot,
        .org-chart.org-animate-ready .org-line-v,
        .org-chart.org-animate-ready .org-line-h {
            opacity: 1 !important;
            transform: none !important;
            animation: none !important;
        }

        [data-page-reveal],
        .page-reveal-ready,
        .page-reveal-ready.page-reveal-visible {
            opacity: 1 !important;
            transform: none !important;
            transition: none !important;
        }

        .org-card {
            transition: none !important;
        }
    }
</style>
@endpush

@section('content')

{{-- Alert Peminjaman Terlambat Jika Ada --}}
@if(isset($myPinjamanTerlambatCount) && $myPinjamanTerlambatCount > 0)
<div data-page-reveal style="--page-delay: 0ms;" class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-2xl flex flex-wrap sm:flex-nowrap items-center gap-3 shadow-xs">
    <div class="w-10 h-10 bg-rose-100 rounded-xl flex items-center justify-center flex-shrink-0">
        <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
    </div>
    <div class="flex-1 min-w-0">
        <p class="font-bold text-sm text-rose-900">Perhatian: Anda memiliki {{ $myPinjamanTerlambatCount }} alat yang melewati batas waktu kembali!</p>
        <p class="text-xs sm:text-sm text-rose-700 mt-0.5">Harap segera mengembalikan alat tersebut ke teknisi laboratorium TKJ.</p>
    </div>
    <a href="{{ route('peminjaman.saya') }}" class="w-full sm:w-auto bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold px-4 py-2 rounded-xl transition-colors whitespace-nowrap text-center">
        Cek Alat
    </a>
</div>
@endif

{{-- Hero Section dengan Background Image Slideshow & Overlay --}}
<div data-page-reveal style="--page-delay: 30ms;" class="relative overflow-hidden rounded-3xl p-6 sm:p-10 text-white shadow-md mb-8 border border-slate-800 bg-slate-900 [isolation:isolate]"
     x-data="{
        currentSlide: 1,
        totalSlides: 3,
        init() {
            setInterval(() => {
                this.currentSlide = this.currentSlide >= this.totalSlides ? 1 : this.currentSlide + 1;
            }, 6000);
        }
     }">

    {{-- 1. Lapisan Paling Dasar: Foto dengan Animasi Ken Burns --}}
    <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
        <img src="{{ asset('uploads/hero1.webp') }}" 
             alt="Hero 1"
             class="absolute inset-0 w-full h-full object-cover transition-opacity duration-1000 ease-in-out"
             :class="currentSlide === 1 ? 'opacity-100 animate-ken-burns' : 'opacity-0'">

        <img src="{{ asset('uploads/hero2.webp') }}" 
             alt="Hero 2"
             class="absolute inset-0 w-full h-full object-cover transition-opacity duration-1000 ease-in-out"
             :class="currentSlide === 2 ? 'opacity-100 animate-ken-burns' : 'opacity-0'">

        <img src="{{ asset('uploads/hero3.webp') }}" 
             alt="Hero 3"
             class="absolute inset-0 w-full h-full object-cover transition-opacity duration-1000 ease-in-out"
             :class="currentSlide === 3 ? 'opacity-100 animate-ken-burns' : 'opacity-0'">
    </div>

    {{-- 2. Lapisan Tengah: Deep Slate Gradient Overlay --}}
    <div class="absolute inset-0 z-[1] bg-gradient-to-r from-slate-950/95 via-slate-900/90 to-slate-900/85 pointer-events-none"></div>

    {{-- 3. Lapisan Atas: Konten Teks & Tombol --}}
    <div class="relative z-[2] max-w-2xl">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-xl bg-white p-1 shadow-md flex items-center justify-center overflow-hidden">
                <img src="{{ asset('uploads/Logo/Logo_winshark.webp') }}" 
                     data-fallback="{{ asset('uploads/Logo/Logo_winshark.jpeg') }}"
                     onerror="this.onerror=null;if(this.dataset.fallback)this.src=this.dataset.fallback;"
                     alt="Winshark" 
                     class="w-full h-full object-cover rounded-lg">
            </div>
            <span class="bg-white/10 backdrop-blur-md px-3.5 py-1 rounded-full text-xs font-semibold text-slate-200 border border-white/10 shadow-xs">
                Winshark Community • Lab TKJ
            </span>
        </div>
        <h2 class="text-2xl sm:text-4xl font-extrabold leading-tight text-white tracking-tight">
            Pusat Peminjaman & Praktikum Laboratorium TKJ
        </h2>
        <p class="text-slate-200 text-sm sm:text-base mt-3 leading-relaxed max-w-xl">
            Eksplorasi perangkat jaringan, komputer, dan alat praktik laboratorium. Ajukan peminjaman secara mandiri atau pindai QR Code alat secara instan.
        </p>
        <div class="flex flex-col sm:flex-row sm:items-center gap-3 mt-6">
            <a href="{{ route('katalog.index') }}" class="bg-white hover:bg-slate-100 text-slate-900 font-bold px-6 py-3 rounded-xl text-xs sm:text-sm shadow-sm transition-all duration-150 flex items-center justify-center gap-2 active:scale-95 focus-visible:ring-2 focus-visible:ring-white">
                <svg class="w-4 h-4 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Jelajahi Katalog Alat
            </a>
            <a href="{{ route('scan.qr') }}" class="bg-slate-800/80 hover:bg-slate-800 text-white font-semibold px-5 py-3 rounded-xl text-xs sm:text-sm border border-white/20 backdrop-blur-sm transition flex items-center justify-center gap-2 active:scale-95 focus-visible:ring-2 focus-visible:ring-white">
                <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                </svg>
                Scan QR Code
            </a>
        </div>
    </div>
</div>

{{-- Stat Mini Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6 mb-8">
    <div data-page-reveal style="--page-delay: 40ms;" class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex items-center gap-4">
        <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">Alat Siap Digunakan</p>
            <p class="text-2xl font-black text-slate-900 mt-0.5"><span class="number-flow" data-number-flow data-number-target="{{ (int) ($barangTersedia ?? 0) }}">{{ (int) ($barangTersedia ?? 0) }}</span> <span class="text-xs font-medium text-slate-500">jenis alat</span></p>
        </div>
    </div>

    <div data-page-reveal style="--page-delay: 100ms;" class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex items-center gap-4">
        <div class="w-12 h-12 bg-sky-50 rounded-xl flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
            </svg>
        </div>
        <div>
            <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">Pinjaman Aktif Saya</p>
            <p class="text-2xl font-black text-sky-700 mt-0.5"><span class="number-flow" data-number-flow data-number-target="{{ isset($myPinjamanAktif) ? (int) $myPinjamanAktif->count() : 0 }}">{{ isset($myPinjamanAktif) ? (int) $myPinjamanAktif->count() : 0 }}</span> <span class="text-xs font-medium text-slate-500">alat</span></p>
        </div>
    </div>

    <div data-page-reveal style="--page-delay: 160ms;" class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex items-center gap-4">
        <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <div>
            <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">Riwayat Selesai</p>
            <p class="text-2xl font-black text-slate-900 mt-0.5"><span class="number-flow" data-number-flow data-number-target="{{ (int) ($myPinjamanSelesaiCount ?? 0) }}">{{ (int) ($myPinjamanSelesaiCount ?? 0) }}</span> <span class="text-xs font-medium text-slate-500">kali pinjam</span></p>
        </div>
    </div>
</div>

{{-- Peminjaman Aktif Saya & Pengumuman --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8 mb-12">
    
    {{-- Kolom 1-2: Alat Sedang Anda Pinjam --}}
    <div data-page-reveal style="--page-delay: 40ms;" class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-200/90 overflow-hidden flex flex-col">
        <div class="px-5 sm:px-6 py-4 bg-slate-900 text-white border-b border-slate-800 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-white text-sm sm:text-base">Alat Sedang Anda Pinjam</h3>
                <p class="text-xs sm:text-sm text-slate-300 mt-0.5">Peralatan yang saat ini menjadi tanggung jawab peminjaman Anda</p>
            </div>
            <a href="{{ route('peminjaman.saya') }}" class="text-xs sm:text-sm font-semibold text-indigo-300 hover:text-white transition">
                Lihat Semua →
            </a>
        </div>

        {{-- TAMPILAN MOBILE: LIST KARTU --}}
        <div class="block md:hidden divide-y divide-slate-100">
            @forelse($myPinjamanAktif ?? [] as $pinjam)
                <div class="p-4 space-y-2.5 border-l-4 border-l-indigo-600">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="font-bold text-slate-900 text-sm leading-snug">{{ $pinjam->barang->nama_barang ?? 'Barang' }}</p>
                            <p class="font-mono text-xs text-slate-500 mt-0.5">{{ $pinjam->barang->kode_barang ?? '-' }}</p>
                        </div>
                        @if($pinjam->status === 'Menunggu Persetujuan')
                            <span class="bg-amber-100 text-amber-800 text-xs font-bold px-2.5 py-1 rounded-full shrink-0">
                                Menunggu
                            </span>
                        @elseif(method_exists($pinjam, 'isTerlambat') && $pinjam->isTerlambat())
                            <span class="bg-rose-100 text-rose-700 text-xs font-bold px-2.5 py-1 rounded-full shrink-0">
                                Terlambat
                            </span>
                        @else
                            <span class="bg-sky-100 text-sky-700 text-xs font-bold px-2.5 py-1 rounded-full shrink-0">
                                Dipinjam
                            </span>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-xs bg-slate-50 p-2.5 rounded-xl">
                        <div>
                            <span class="text-slate-500 block">Jumlah Unit:</span>
                            <span class="font-bold text-slate-800">{{ $pinjam->jumlah_pinjam }} {{ $pinjam->barang->satuan ?? 'Unit' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 block">Batas Kembali:</span>
                            <span class="font-bold {{ (method_exists($pinjam, 'isTerlambat') && $pinjam->isTerlambat()) ? 'text-rose-700' : 'text-slate-800' }}">
                                {{ \Carbon\Carbon::parse($pinjam->tanggal_kembali_rencana)->format('d M Y') }}
                            </span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-4 py-8 text-center text-slate-500">
                    <p class="font-medium text-xs sm:text-sm">Anda tidak memiliki alat yang sedang dipinjam saat ini.</p>
                    <a href="{{ route('katalog.index') }}" class="text-indigo-600 font-semibold text-xs sm:text-sm hover:underline mt-1.5 inline-block">
                        Pinjam alat praktikum sekarang →
                    </a>
                </div>
            @endforelse
        </div>

        {{-- TAMPILAN DESKTOP: TABEL LENGKAP --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50/90 text-slate-600 border-b border-slate-200">
                    <tr class="text-[11px] sm:text-xs uppercase tracking-wider font-bold text-slate-600">
                        <th class="px-5 py-3.5 font-bold">Nama Alat</th>
                        <th class="px-5 py-3.5 text-center font-bold">Jumlah</th>
                        <th class="px-5 py-3.5 font-bold">Tgl Pinjam</th>
                        <th class="px-5 py-3.5 font-bold">Batas Kembali</th>
                        <th class="px-5 py-3.5 text-center font-bold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs sm:text-sm">
                    @forelse($myPinjamanAktif ?? [] as $pinjam)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-5 py-3.5">
                                <p class="font-semibold text-slate-800">{{ $pinjam->barang->nama_barang ?? 'Barang' }}</p>
                                <p class="font-mono text-xs text-slate-500">{{ $pinjam->barang->kode_barang ?? '-' }}</p>
                            </td>
                            <td class="px-5 py-3.5 text-center font-medium text-slate-700">
                                {{ $pinjam->jumlah_pinjam }} {{ $pinjam->barang->satuan ?? 'Unit' }}
                            </td>
                            <td class="px-5 py-3.5 text-slate-600">
                                {{ \Carbon\Carbon::parse($pinjam->tanggal_pinjam)->format('d M Y') }}
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="font-semibold {{ (method_exists($pinjam, 'isTerlambat') && $pinjam->isTerlambat()) ? 'text-rose-700' : 'text-slate-700' }}">
                                    {{ \Carbon\Carbon::parse($pinjam->tanggal_kembali_rencana)->format('d M Y') }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                @if($pinjam->status === 'Menunggu Persetujuan')
                                    <span class="bg-amber-100 text-amber-800 text-xs font-bold px-2.5 py-1 rounded-full">
                                        Menunggu Persetujuan
                                    </span>
                                @elseif(method_exists($pinjam, 'isTerlambat') && $pinjam->isTerlambat())
                                    <span class="bg-rose-100 text-rose-700 text-xs font-bold px-2.5 py-1 rounded-full">
                                        Terlambat
                                    </span>
                                @else
                                    <span class="bg-sky-100 text-sky-700 text-xs font-bold px-2.5 py-1 rounded-full">
                                        Dipinjam
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-slate-500">
                                <p class="font-medium text-xs sm:text-sm">Anda tidak memiliki alat yang sedang dipinjam saat ini.</p>
                                <a href="{{ route('katalog.index') }}" class="text-indigo-600 font-semibold text-xs sm:text-sm hover:underline mt-1 inline-block">
                                    Pinjam alat praktikum sekarang →
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Kolom 3: Pengumuman Laboratorium --}}
    <div data-page-reveal style="--page-delay: 100ms;" class="bg-white rounded-2xl shadow-xs border border-slate-200 p-5 sm:p-6 flex flex-col">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
            <h3 class="font-bold text-slate-900 text-sm sm:text-base">Pengumuman Lab</h3>
            <a href="{{ route('berita.index') }}" class="text-xs sm:text-sm text-indigo-600 font-semibold hover:underline">Semua</a>
        </div>
        <div class="divide-y divide-slate-100 space-y-3 flex-1 min-w-0">
            @forelse($beritaTerbaru ?? [] as $b)
                <div class="pt-3 first:pt-0 min-w-0">
                    <a href="{{ route('berita.show', $b->id) }}" class="group block min-w-0">
                        <div class="flex items-start justify-between gap-2 mb-1">
                            <h4 class="text-xs sm:text-sm font-bold text-slate-800 group-hover:text-indigo-600 transition line-clamp-2 break-words leading-snug flex-1">{{ $b->judul }}</h4>
                            @if($b->target_kelas && !str_contains($b->target_kelas, 'Semua'))
                                <span class="bg-indigo-50 text-indigo-700 border border-indigo-200 text-[10px] font-bold px-2 py-0.5 rounded-md shrink-0 whitespace-nowrap">
                                    {{ $b->target_kelas }}
                                </span>
                            @endif
                        </div>
                        <p class="text-xs sm:text-sm text-slate-500 mt-1 line-clamp-2 leading-relaxed break-words">{{ strip_tags($b->isi) }}</p>
                        <span class="text-[11px] text-slate-400 mt-1.5 block">{{ \Carbon\Carbon::parse($b->created_at)->translatedFormat('d M Y') }} • {{ $b->user->name ?? 'Admin' }}</span>
                    </a>
                </div>
            @empty
                <div class="py-8 text-center text-slate-500 space-y-1">
                    <p class="text-xs sm:text-sm font-medium">Belum ada pengumuman baru untuk kelas Anda.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

{{-- STRUKTUR KEPENGURUSAN WINSHARK COMMUNITY --}}
<section id="kepengurusan"
         class="org-chart mt-14 sm:mt-16 lg:mt-20"
         data-org-chart
         aria-labelledby="org-title">

    <div class="relative overflow-hidden rounded-3xl border border-slate-200/90 bg-gradient-to-b from-white via-slate-50/40 to-white p-4 shadow-sm sm:p-6 lg:p-8">
        {{-- Header --}}
        <div class="org-reveal relative z-10 mx-auto max-w-2xl text-center" style="--org-delay: 60ms;">
            <span class="inline-flex items-center gap-2 rounded-full border border-blue-200/90 bg-blue-50/80 px-3.5 py-1.5 text-[10px] font-bold uppercase tracking-[0.16em] text-blue-800 shadow-2xs sm:text-xs">
                <svg class="h-3.5 w-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Struktur Organisasi
            </span>

            <h3 id="org-title" class="mt-3 text-2xl font-black tracking-tight text-slate-900 sm:text-3xl lg:text-[2rem]">
                Kepengurusan Komunitas Winshark
            </h3>

            <p class="mx-auto mt-2 max-w-xl text-xs leading-6 text-slate-500 sm:text-sm">
                Tim yang bertanggung jawab atas tata kelola laboratorium, administrasi, infrastruktur, publikasi, dan operasional Winshark Community.
            </p>
        </div>

        <div class="mt-8 sm:mt-10">
            {{-- Tier 1: Pimpinan Utama --}}
            <div class="org-reveal flex items-center justify-center gap-3" style="--org-delay: 180ms;">
                <span class="h-px w-8 bg-slate-200 sm:w-16 lg:w-24"></span>
                <span class="inline-flex items-center gap-1.5 rounded-full border border-blue-200 bg-blue-50 px-3 py-1 text-[10px] font-bold uppercase tracking-[0.14em] text-blue-800 sm:px-3.5 sm:text-xs shadow-2xs">
                    <svg class="h-3.5 w-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                    Pimpinan Utama
                </span>
                <span class="h-px w-8 bg-slate-200 sm:w-16 lg:w-24"></span>
            </div>

            <div class="relative mx-auto max-w-3xl pt-10 sm:pt-12">
                {{-- Garis struktur pimpinan --}}
                <span class="org-line org-line-v left-1/2 top-0 h-5 -translate-x-1/2" style="--org-delay: 300ms;"></span>
                <span class="org-dot left-1/2 top-[17px] -translate-x-1/2" style="--org-delay: 650ms;"></span>
                <span class="org-line org-line-h left-1/4 right-1/4 top-5" style="--org-delay: 520ms;"></span>
                <span class="org-line org-line-v left-1/4 top-5 h-5 -translate-x-1/2" style="--org-delay: 760ms;"></span>
                <span class="org-line org-line-v left-3/4 top-5 h-5 -translate-x-1/2" style="--org-delay: 850ms;"></span>

                <div class="relative z-10 grid grid-cols-2 gap-3.5 sm:gap-6">
                    {{-- Ketua --}}
                    <article class="org-card org-reveal group h-full rounded-2xl border border-slate-200/90 bg-white p-4 text-center sm:rounded-3xl sm:p-5" style="--org-delay: 980ms;">
                        <div class="org-photo mx-auto h-32 w-28 rounded-2xl border border-blue-200/80 sm:h-40 sm:w-32 lg:h-44 lg:w-36">
                            <img src="{{ asset('uploads/pengurus/Varo.webp') }}"
                                 data-fallback="{{ asset('uploads/pengurus/Varo.png') }}"
                                 data-default="{{ asset('uploads/Logo/Logo_winshark.jpeg') }}"
                                 onerror="if(!this.dataset.retried && this.dataset.fallback){this.dataset.retried='1';this.src=this.dataset.fallback;}else{this.onerror=null;this.src=this.dataset.default;}"
                                 alt="Varo - Ketua Umum"
                                 loading="lazy"
                                 class="h-full w-full object-contain object-bottom p-1">
                        </div>

                        <div class="mt-3.5 flex flex-col items-center">
                            <span class="inline-flex items-center gap-1.5 rounded-full border border-blue-200/90 bg-gradient-to-r from-blue-50 to-indigo-50/80 px-2.5 py-0.5 text-[10px] font-extrabold uppercase tracking-wider text-blue-800 shadow-2xs sm:px-3 sm:py-1 sm:text-[11px] whitespace-nowrap">
                                <span class="h-1.5 w-1.5 rounded-full bg-blue-600 shrink-0"></span>
                                Ketua Umum
                            </span>
                            <h4 class="mt-2.5 text-base font-extrabold tracking-tight text-slate-900 sm:text-lg group-hover:text-blue-600 transition-colors">Varo</h4>
                            <div class="mt-1 flex items-center justify-center">
                                <span class="inline-block rounded-md bg-slate-100/90 px-2.5 py-0.5 text-[10px] sm:text-xs font-semibold text-slate-600">Winshark Community</span>
                            </div>
                        </div>
                    </article>

                    {{-- Wakil Ketua --}}
                    <article class="org-card org-reveal group h-full rounded-2xl border border-slate-200/90 bg-white p-4 text-center sm:rounded-3xl sm:p-5" style="--org-delay: 1080ms;">
                        <div class="org-photo mx-auto h-32 w-28 rounded-2xl border border-blue-200/80 sm:h-40 sm:w-32 lg:h-44 lg:w-36">
                            <img src="{{ asset('uploads/pengurus/ishak.webp') }}"
                                 data-fallback="{{ asset('uploads/pengurus/ishak.png') }}"
                                 data-default="{{ asset('uploads/Logo/Logo_winshark.jpeg') }}"
                                 onerror="if(!this.dataset.retried && this.dataset.fallback){this.dataset.retried='1';this.src=this.dataset.fallback;}else{this.onerror=null;this.src=this.dataset.default;}"
                                 alt="Ishak - Wakil Ketua"
                                 loading="lazy"
                                 class="h-full w-full object-contain object-bottom p-1">
                        </div>

                        <div class="mt-3.5 flex flex-col items-center">
                            <span class="inline-flex items-center gap-1.5 rounded-full border border-blue-200/90 bg-gradient-to-r from-blue-50 to-indigo-50/80 px-2.5 py-0.5 text-[10px] font-extrabold uppercase tracking-wider text-blue-800 shadow-2xs sm:px-3 sm:py-1 sm:text-[11px] whitespace-nowrap">
                                <span class="h-1.5 w-1.5 rounded-full bg-blue-600 shrink-0"></span>
                                Wakil Ketua
                            </span>
                            <h4 class="mt-2.5 text-base font-extrabold tracking-tight text-slate-900 sm:text-lg group-hover:text-blue-600 transition-colors">Ishak</h4>
                            <div class="mt-1 flex items-center justify-center">
                                <span class="inline-block rounded-md bg-slate-100/90 px-2.5 py-0.5 text-[10px] sm:text-xs font-semibold text-slate-600">Winshark Community</span>
                            </div>
                        </div>
                    </article>
                </div>
            </div>

            {{-- Penghubung Tier 1 ke Tier 2 --}}
            <div class="relative mx-auto h-20 sm:h-24">
                <span class="org-line org-line-v left-1/2 top-0 h-full -translate-x-1/2" style="--org-delay: 1220ms;"></span>
                <span class="org-dot left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2" style="--org-delay: 1500ms;"></span>
            </div>

            {{-- Tier 2: Divisi & Operasional (4 Pilar) --}}
            <div class="org-reveal flex items-center justify-center gap-3" style="--org-delay: 1580ms;">
                <span class="h-px w-8 bg-slate-200 sm:w-16 lg:w-24"></span>
                <span class="inline-flex items-center gap-1.5 rounded-full border border-blue-200 bg-blue-50 px-3 py-1 text-[10px] font-bold uppercase tracking-[0.14em] text-blue-800 sm:px-3.5 sm:text-xs shadow-2xs">
                    <svg class="h-3.5 w-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    Divisi &amp; Operasional
                </span>
                <span class="h-px w-8 bg-slate-200 sm:w-16 lg:w-24"></span>
            </div>

            <div class="relative mx-auto max-w-5xl pt-10 sm:pt-12">
                {{-- Garis desktop: bercabang ke 4 divisi --}}
                <div class="org-desktop-branch absolute inset-x-0 top-0 h-10" aria-hidden="true">
                    <span class="org-line org-line-v left-1/2 top-0 h-5 -translate-x-1/2" style="--org-delay: 1720ms;"></span>
                    <span class="org-dot left-1/2 top-[17px] -translate-x-1/2" style="--org-delay: 1940ms;"></span>
                    <span class="org-line org-line-h left-[12.5%] right-[12.5%] top-5" style="--org-delay: 1900ms;"></span>
                    <span class="org-line org-line-v left-[12.5%] top-5 h-5 -translate-x-1/2" style="--org-delay: 2150ms;"></span>
                    <span class="org-line org-line-v left-[37.5%] top-5 h-5 -translate-x-1/2" style="--org-delay: 2230ms;"></span>
                    <span class="org-line org-line-v left-[62.5%] top-5 h-5 -translate-x-1/2" style="--org-delay: 2310ms;"></span>
                    <span class="org-line org-line-v left-[87.5%] top-5 h-5 -translate-x-1/2" style="--org-delay: 2390ms;"></span>
                </div>

                {{-- Garis mobile/tablet: stem sederhana --}}
                <div class="org-mobile-stem absolute inset-x-0 top-0 h-8" aria-hidden="true">
                    <span class="org-line org-line-v left-1/2 top-0 h-8 -translate-x-1/2" style="--org-delay: 1720ms;"></span>
                    <span class="org-dot left-1/2 top-[25px] -translate-x-1/2" style="--org-delay: 1980ms;"></span>
                </div>

                <div class="relative z-10 grid grid-cols-2 gap-3 sm:gap-4 md:grid-cols-2 lg:grid-cols-4">
                    {{-- Sekretariat --}}
                    <article class="org-card org-reveal group flex h-full flex-col items-center rounded-2xl border border-slate-200/90 bg-white p-3.5 text-center sm:p-4.5" style="--org-delay: 2480ms;">
                        <div class="org-photo h-28 w-full max-w-[145px] rounded-xl border border-blue-100 sm:h-32 sm:max-w-[160px] lg:h-36">
                            <img src="{{ asset('uploads/pengurus/rehan-titan.webp') }}"
                                 data-fallback="{{ asset('uploads/pengurus/rehan-titan.png') }}"
                                 data-default="{{ asset('uploads/Logo/Logo_winshark.jpeg') }}"
                                 onerror="if(!this.dataset.retried && this.dataset.fallback){this.dataset.retried='1';this.src=this.dataset.fallback;}else{this.onerror=null;this.src=this.dataset.default;}"
                                 alt="Rehan dan Titan - Sekretariat"
                                 loading="lazy"
                                 class="h-full w-full object-contain object-bottom p-1">
                        </div>
                        <div class="mt-3 flex flex-1 flex-col items-center justify-between w-full min-w-0">
                            <span class="inline-flex items-center gap-1 sm:gap-1.5 rounded-full border border-blue-200/90 bg-gradient-to-r from-blue-50 to-indigo-50/80 px-2 sm:px-2.5 py-0.5 sm:py-1 text-[9px] sm:text-[10px] font-extrabold uppercase tracking-wider text-blue-800 shadow-2xs whitespace-nowrap">
                                <span class="h-1.5 w-1.5 rounded-full bg-blue-600 shrink-0"></span>
                                Sekretariat
                            </span>
                            <div class="mt-2 mb-1 w-full min-w-0">
                                <h4 class="text-xs font-extrabold tracking-tight text-slate-900 sm:text-sm group-hover:text-blue-600 transition-colors truncate">Rehan &amp; Titan</h4>
                                <p class="mt-0.5 text-[10px] font-medium text-slate-500 sm:text-xs truncate">Administrasi &amp; Surat</p>
                            </div>
                        </div>
                    </article>

                    {{-- Keuangan --}}
                    <article class="org-card org-reveal group flex h-full flex-col items-center rounded-2xl border border-slate-200/90 bg-white p-3.5 text-center sm:p-4.5" style="--org-delay: 2580ms;">
                        <div class="org-photo h-28 w-full max-w-[145px] rounded-xl border border-blue-100 sm:h-32 sm:max-w-[160px] lg:h-36">
                            <img src="{{ asset('uploads/pengurus/efril-aulia.webp') }}"
                                 data-fallback="{{ asset('uploads/pengurus/efril-aulia.png') }}"
                                 data-default="{{ asset('uploads/Logo/Logo_winshark.jpeg') }}"
                                 onerror="if(!this.dataset.retried && this.dataset.fallback){this.dataset.retried='1';this.src=this.dataset.fallback;}else{this.onerror=null;this.src=this.dataset.default;}"
                                 alt="Efril dan Aulia - Keuangan"
                                 loading="lazy"
                                 class="h-full w-full object-contain object-bottom p-1">
                        </div>
                        <div class="mt-3 flex flex-1 flex-col items-center justify-between w-full min-w-0">
                            <span class="inline-flex items-center gap-1 sm:gap-1.5 rounded-full border border-blue-200/90 bg-gradient-to-r from-blue-50 to-indigo-50/80 px-2 sm:px-2.5 py-0.5 sm:py-1 text-[9px] sm:text-[10px] font-extrabold uppercase tracking-wider text-blue-800 shadow-2xs whitespace-nowrap">
                                <span class="h-1.5 w-1.5 rounded-full bg-blue-600 shrink-0"></span>
                                Keuangan
                            </span>
                            <div class="mt-2 mb-1 w-full min-w-0">
                                <h4 class="text-xs font-extrabold tracking-tight text-slate-900 sm:text-sm group-hover:text-blue-600 transition-colors truncate">Efril &amp; Aulia</h4>
                                <p class="mt-0.5 text-[10px] font-medium text-slate-500 sm:text-xs truncate">Bendahara I &amp; II</p>
                            </div>
                        </div>
                    </article>

                    {{-- Media & Desain --}}
                    <article class="org-card org-reveal group flex h-full flex-col items-center rounded-2xl border border-slate-200/90 bg-white p-3.5 text-center sm:p-4.5" style="--org-delay: 2680ms;">
                        <div class="org-photo h-28 w-full max-w-[145px] rounded-xl border border-blue-100 sm:h-32 sm:max-w-[160px] lg:h-36">
                            <img src="{{ asset('uploads/pengurus/fabes-jaki.webp') }}"
                                 data-fallback="{{ asset('uploads/pengurus/fabes-jaki.png') }}"
                                 data-default="{{ asset('uploads/Logo/Logo_winshark.jpeg') }}"
                                 onerror="if(!this.dataset.retried && this.dataset.fallback){this.dataset.retried='1';this.src=this.dataset.fallback;}else{this.onerror=null;this.src=this.dataset.default;}"
                                 alt="Fabes dan Zaki - Media dan Desain"
                                 loading="lazy"
                                 class="h-full w-full object-contain object-bottom p-1">
                        </div>
                        <div class="mt-3 flex flex-1 flex-col items-center justify-between w-full min-w-0">
                            <span class="inline-flex items-center gap-1 sm:gap-1.5 rounded-full border border-blue-200/90 bg-gradient-to-r from-blue-50 to-indigo-50/80 px-2 sm:px-2.5 py-0.5 sm:py-1 text-[9px] sm:text-[10px] font-extrabold uppercase tracking-wider text-blue-800 shadow-2xs whitespace-nowrap">
                                <span class="h-1.5 w-1.5 rounded-full bg-blue-600 shrink-0"></span>
                                Media &amp; Desain
                            </span>
                            <div class="mt-2 mb-1 w-full min-w-0">
                                <h4 class="text-xs font-extrabold tracking-tight text-slate-900 sm:text-sm group-hover:text-blue-600 transition-colors truncate">Fabes &amp; Zaki</h4>
                                <p class="mt-0.5 text-[10px] font-medium text-slate-500 sm:text-xs truncate">Kreatif &amp; Publikasi</p>
                            </div>
                        </div>
                    </article>

                    {{-- Infrastruktur --}}
                    <article class="org-card org-reveal group flex h-full flex-col items-center rounded-2xl border border-slate-200/90 bg-white p-3.5 text-center sm:p-4.5" style="--org-delay: 2780ms;">
                        <div class="org-photo h-28 w-full max-w-[145px] rounded-xl border border-blue-100 sm:h-32 sm:max-w-[160px] lg:h-36">
                            <img src="{{ asset('uploads/pengurus/maulana.webp') }}"
                                 data-fallback="{{ asset('uploads/pengurus/maulana.png') }}"
                                 data-default="{{ asset('uploads/Logo/Logo_winshark.jpeg') }}"
                                 onerror="if(!this.dataset.retried && this.dataset.fallback){this.dataset.retried='1';this.src=this.dataset.fallback;}else{this.onerror=null;this.src=this.dataset.default;}"
                                 alt="Maulana - Infrastruktur"
                                 loading="lazy"
                                 class="h-full w-full object-contain object-bottom p-1">
                        </div>
                        <div class="mt-3 flex flex-1 flex-col items-center justify-between w-full min-w-0">
                            <span class="inline-flex items-center gap-1 sm:gap-1.5 rounded-full border border-blue-200/90 bg-gradient-to-r from-blue-50 to-indigo-50/80 px-2 sm:px-2.5 py-0.5 sm:py-1 text-[9px] sm:text-[10px] font-extrabold uppercase tracking-wider text-blue-800 shadow-2xs whitespace-nowrap">
                                <span class="h-1.5 w-1.5 rounded-full bg-blue-600 shrink-0"></span>
                                Infrastruktur
                            </span>
                            <div class="mt-2 mb-1 w-full min-w-0">
                                <h4 class="text-xs font-extrabold tracking-tight text-slate-900 sm:text-sm group-hover:text-blue-600 transition-colors truncate">Maulana</h4>
                                <p class="mt-0.5 text-[10px] font-medium text-slate-500 sm:text-xs truncate">Koord Infrastruktur</p>
                            </div>
                        </div>
                    </article>
                </div>
            </div>

            {{-- Penghubung Tier 2 ke Tier 3 (Pengelola Web) --}}
            <div class="relative mx-auto h-20 sm:h-24">
                <span class="org-line org-line-v left-1/2 top-0 h-full -translate-x-1/2" style="--org-delay: 2900ms;"></span>
                <span class="org-dot left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2" style="--org-delay: 3100ms;"></span>
            </div>

            {{-- Tier 3: Sistem & IT (Pengelola Web di Bagian Bawah) --}}
            <div class="relative mx-auto max-w-xs">
                <div class="flex justify-center">
                    <article class="org-card org-reveal group flex w-full max-w-[240px] flex-col items-center rounded-2xl border border-slate-200/90 bg-white p-4 text-center sm:max-w-[260px] sm:p-5" style="--org-delay: 3250ms;">
                        <div class="org-photo h-32 w-full max-w-[155px] rounded-xl border border-blue-100 sm:h-36 sm:max-w-[170px] lg:h-40">
                            <img src="{{ asset('uploads/pengurus/pengelola.jpeg') }}"
                                 data-fallback="{{ asset('uploads/Logo/Logo_winshark.jpeg') }}"
                                 data-default="{{ asset('uploads/Logo/Logo banner.jpeg') }}"
                                 onerror="if(!this.dataset.retried && this.dataset.fallback){this.dataset.retried='1';this.src=this.dataset.fallback;}else{this.onerror=null;this.src=this.dataset.default;}"
                                 alt="Pengelola Web - Sistem dan IT"
                                 loading="lazy"
                                 class="h-full w-full object-cover object-top">
                        </div>
                        <div class="mt-3.5 flex flex-1 flex-col items-center justify-between w-full min-w-0">
                            <span class="inline-flex items-center gap-1.5 rounded-full border border-blue-200/90 bg-gradient-to-r from-blue-50 to-indigo-50/80 px-2.5 sm:px-3 py-0.5 sm:py-1 text-[9px] sm:text-[10px] font-extrabold uppercase tracking-wider text-blue-800 shadow-2xs whitespace-nowrap">
                                <span class="h-1.5 w-1.5 rounded-full bg-blue-600 shrink-0 animate-pulse"></span>
                                Sistem &amp; IT
                            </span>
                            <div class="mt-2 mb-1 w-full min-w-0">
                                <h4 class="text-xs font-extrabold tracking-tight text-slate-900 sm:text-sm group-hover:text-blue-600 transition-colors truncate">Pengelola Web</h4>
                                <p class="mt-0.5 text-[10px] font-medium text-slate-500 sm:text-xs truncate">Operasional Sistem &amp; Dev</p>
                            </div>
                        </div>
                    </article>
                </div>
            </div>

        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        /* Animasi masuk halaman */
        const revealItems = document.querySelectorAll('[data-page-reveal]');

        if (!reduceMotion && 'IntersectionObserver' in window) {
            revealItems.forEach(function (item) {
                item.classList.add('page-reveal-ready');
            });

            const revealObserver = new IntersectionObserver(function (entries, observer) {
                entries.forEach(function (entry) {
                    if (!entry.isIntersecting) return;

                    entry.target.classList.add('page-reveal-visible');
                    observer.unobserve(entry.target);
                });
            }, {
                threshold: 0.08,
                rootMargin: '0px 0px -4% 0px'
            });

            revealItems.forEach(function (item) {
                revealObserver.observe(item);
            });
        } else {
            revealItems.forEach(function (item) {
                item.classList.add('page-reveal-visible');
            });
        }

        /* Number flow statistik */
        const numberItems = document.querySelectorAll('[data-number-flow]');

        function formatNumber(value) {
            return new Intl.NumberFormat('id-ID').format(value);
        }

        function runNumberFlow(element) {
            if (element.dataset.numberAnimated === 'true') return;

            const target = Number(element.dataset.numberTarget || 0);
            element.dataset.numberAnimated = 'true';

            if (reduceMotion || !Number.isFinite(target) || target <= 0) {
                element.textContent = formatNumber(Math.max(0, Number.isFinite(target) ? target : 0));
                return;
            }

            const duration = Math.min(1250, Math.max(700, 620 + (Math.log10(target + 1) * 170)));
            const startTime = performance.now();
            element.textContent = '0';
            element.classList.add('number-flow-running');

            function easeOutCubic(t) {
                return 1 - Math.pow(1 - t, 3);
            }

            function frame(now) {
                const progress = Math.min((now - startTime) / duration, 1);
                const eased = easeOutCubic(progress);
                const current = Math.round(target * eased);
                element.textContent = formatNumber(current);

                if (progress < 1) {
                    requestAnimationFrame(frame);
                } else {
                    element.textContent = formatNumber(target);
                    window.setTimeout(function () {
                        element.classList.remove('number-flow-running');
                    }, 560);
                }
            }

            requestAnimationFrame(frame);
        }

        if (!reduceMotion && 'IntersectionObserver' in window) {
            const numberObserver = new IntersectionObserver(function (entries, observer) {
                entries.forEach(function (entry) {
                    if (!entry.isIntersecting) return;

                    runNumberFlow(entry.target);
                    observer.unobserve(entry.target);
                });
            }, {
                threshold: 0.55,
                rootMargin: '0px 0px -2% 0px'
            });

            numberItems.forEach(function (item) {
                numberObserver.observe(item);
            });
        } else {
            numberItems.forEach(runNumberFlow);
        }

        /* Animasi struktur organisasi */
        const charts = document.querySelectorAll('[data-org-chart]');

        if (!charts.length) return;

        charts.forEach(function (chart) {
            chart.classList.add('org-animate-ready');
        });

        if (reduceMotion || !('IntersectionObserver' in window)) {
            charts.forEach(function (chart) {
                chart.classList.add('org-chart-visible');
            });
            return;
        }

        const orgObserver = new IntersectionObserver(function (entries, observer) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) return;

                entry.target.classList.add('org-chart-visible');
                observer.unobserve(entry.target);
            });
        }, {
            threshold: 0.10,
            rootMargin: '0px 0px -5% 0px'
        });

        charts.forEach(function (chart) {
            orgObserver.observe(chart);
        });
    });
</script>

{{-- ===== FLOATING CUSTOMER SERVICE WIDGET ===== --}}
<aside x-data="{ csOpen: false, hovered: false }"
       @click.outside="csOpen = false"
       @keydown.escape.window="csOpen = false"
       aria-label="Layanan Bantuan Customer Service"
       class="fixed bottom-4 right-4 sm:bottom-6 sm:right-6 bottom-[calc(1rem+env(safe-area-inset-bottom,0px))] right-[calc(1rem+env(safe-area-inset-right,0px))] z-[9990] flex flex-col items-end no-print select-none">

    {{-- DROP-UP POPUP MENU --}}
    <div x-show="csOpen"
         x-transition:enter="transition ease-[cubic-bezier(0.16,1,0.3,1)] duration-300 transform"
         x-transition:enter-start="opacity-0 translate-y-6 scale-90"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
         class="mb-3 w-72 sm:w-80 rounded-2xl bg-white/95 backdrop-blur-xl border border-slate-200/90 p-3.5 shadow-2xl shadow-indigo-950/20 ring-1 ring-black/5"
         style="display: none;"
         role="menu"
         aria-orientation="vertical">

        {{-- Header Menu --}}
        <div class="flex items-center justify-between px-2 pb-2.5 mb-2 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                <span class="text-xs font-bold text-slate-800">Layanan Bantuan Lab</span>
            </div>
            <span class="text-[10px] font-medium text-emerald-600 bg-emerald-50 border border-emerald-200/70 px-2 py-0.5 rounded-full">
                Online
            </span>
        </div>

        <div class="space-y-1.5">
            {{-- Option 1: Pusat Bantuan AI / FAQ --}}
            <a href="{{ route('bantuan.index') }}"
               @click="csOpen = false"
               role="menuitem"
               class="group flex items-center gap-3 p-2.5 rounded-xl transition-all duration-150 hover:bg-indigo-50/80 active:scale-[0.98]">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-700 text-white shadow-md shadow-indigo-500/25 transition-transform duration-200 group-hover:scale-105">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center justify-between">
                        <p class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-indigo-600 transition-colors">Pusat Bantuan Lab</p>
                        <svg class="h-4 w-4 text-slate-400 group-hover:text-indigo-600 transition-all group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                    <p class="text-[11px] text-slate-500 truncate mt-0.5">Panduan sistem & live chat otomatis</p>
                </div>
            </a>

            {{-- Option 2: WhatsApp Teknisi Lab --}}
            <a href="https://wa.me/6287874589054?text=Halo+Admin+CS+Lab+TKJ%2C+saya+butuh+bantuan+terkait+sistem+peminjaman."
               target="_blank"
               rel="noopener noreferrer"
               @click="csOpen = false"
               role="menuitem"
               class="group flex items-center gap-3 p-2.5 rounded-xl transition-all duration-150 hover:bg-emerald-50/80 active:scale-[0.98]">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-md shadow-emerald-500/25 transition-transform duration-200 group-hover:scale-105">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center justify-between">
                        <p class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-emerald-600 transition-colors">WhatsApp Teknisi</p>
                        <svg class="h-4 w-4 text-slate-400 group-hover:text-emerald-600 transition-all group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                    <p class="text-[11px] text-slate-500 truncate mt-0.5">Chat langsung dengan pengurus lab</p>
                </div>
            </a>
        </div>
    </div>

    {{-- TOMBOL TRIGGER UTAMA --}}
    <button @click="csOpen = !csOpen"
            @mouseenter="hovered = true"
            @mouseleave="hovered = false"
            type="button"
            :aria-expanded="csOpen"
            aria-haspopup="true"
            aria-label="Buka layanan bantuan customer service"
            class="relative flex h-12 w-12 sm:h-14 sm:w-14 items-center justify-center rounded-full bg-gradient-to-tr from-slate-900 via-indigo-950 to-slate-900 text-white shadow-xl shadow-indigo-950/30 ring-2 ring-white/90 transition-all duration-300 active:scale-95 hover:scale-105 hover:ring-indigo-400 focus:outline-none focus-visible:ring-4 focus-visible:ring-indigo-400 cs-glow-pulse">

        {{-- Live Online Dot Indicator --}}
        <span class="absolute -top-0.5 -right-0.5 flex h-3.5 w-3.5 z-10">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-emerald-500 border-2 border-white shadow-xs"></span>
        </span>

        {{-- Ikon Headset / CS Saat Tertutup --}}
        <div x-show="!csOpen" 
             x-transition:enter="transition duration-200 transform"
             x-transition:enter-start="opacity-0 rotate-45 scale-75"
             x-transition:enter-end="opacity-100 rotate-0 scale-100"
             class="flex items-center justify-center">
            <svg class="h-6 w-6 sm:h-7 sm:w-7 text-indigo-200 transition-transform duration-300" 
                 fill="none" 
                 viewBox="0 0 24 24" 
                 stroke="currentColor" 
                 stroke-width="1.8" 
                 stroke-linecap="round" 
                 stroke-linejoin="round"
                 aria-hidden="true">
                <path d="M3 18v-6a9 9 0 0 1 18 0v6"></path>
                <path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"></path>
                <path d="M14 19h7"></path>
            </svg>
        </div>

        {{-- Ikon Close (X) Saat Terbuka --}}
        <div x-show="csOpen" 
             x-transition:enter="transition duration-200 transform"
             x-transition:enter-start="opacity-0 -rotate-45 scale-75"
             x-transition:enter-end="opacity-100 rotate-0 scale-100"
             style="display: none;"
             class="flex items-center justify-center">
            <svg class="h-6 w-6 sm:h-6.5 sm:w-6.5 text-slate-200 transition-transform duration-200" 
                 fill="none" 
                 viewBox="0 0 24 24" 
                 stroke="currentColor" 
                 stroke-width="2.2" 
                 stroke-linecap="round" 
                 stroke-linejoin="round"
                 aria-hidden="true">
                <path d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </div>
    </button>
</aside>

@endsection