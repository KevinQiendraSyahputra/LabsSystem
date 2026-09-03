<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Auto Cache-Busting -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <title>@yield('title', 'Sistem Peminjaman Lab TKJ')</title>

    <!-- Google Fonts: Outfit (Headings), Figtree (Body), JetBrains Mono (Code) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700&family=Outfit:wght@600;700;800;900&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            future: {
                hoverOnlyWhenSupported: true,
            },
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Figtree', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'sans-serif'],
                        heading: ['Outfit', '-apple-system', 'BlinkMacSystemFont', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>

    <!-- Cropper.js CDN (Global for Profile & Photo Uploads) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }

        /* Disable hover animations & sticky highlights on non-desktop (touch/mobile) devices */
        @media (hover: none) or (pointer: coarse) {
            *:hover {
                transform: none !important;
            }

            a, button, [role="button"], tr, .org-card, .group {
                -webkit-tap-highlight-color: transparent !important;
            }

            .hover\:-translate-y-1:hover,
            .hover\:-translate-y-0\.5:hover,
            .hover\:-translate-y-2:hover,
            .hover\:scale-105:hover,
            .hover\:scale-102:hover,
            .hover\:scale-110:hover,
            .group:hover .group-hover\:scale-105,
            .group:hover .group-hover\:scale-110,
            .group:hover .group-hover\:-translate-y-1,
            .group:hover .group-hover\:-translate-y-0\.5,
            .org-card:hover {
                transform: none !important;
            }
        }

        /* Anti Drag & Text Protection */
        img, a, button, [role="button"] {
            -webkit-user-select: none !important;
            -moz-user-select: none !important;
            -ms-user-select: none !important;
            user-select: none !important;
            -webkit-user-drag: none !important;
            user-drag: none !important;
        }
        input, select, textarea, [contenteditable="true"], .custom-dropdown-search-input {
            -webkit-user-select: text !important;
            -moz-user-select: text !important;
            -ms-user-select: text !important;
            user-select: text !important;
        }

        body { font-family: 'Figtree', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
        h1, h2, h3, h4, .font-heading { font-family: 'Outfit', 'Figtree', -apple-system, BlinkMacSystemFont, sans-serif; }
        
        /* Modern Professional Sidebar Styling */
        .sidebar-group-label {
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #94a3b8;
            padding: 0.85rem 0.85rem 0.35rem;
        }

        .sidebar-link {
            font-size: 0.84rem;
            font-weight: 500;
            letter-spacing: -0.01em;
            color: #cbd5e1;
            border-radius: 0.65rem;
            padding: 0.55rem 0.85rem;
            transition: all 0.15s ease-out;
            position: relative;
        }

        .sidebar-link:hover:not(.active) {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.08);
        }

        .sidebar-link.active {
            color: #ffffff;
            font-weight: 600;
            background: rgba(255, 255, 255, 0.14);
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.15);
        }

        .sidebar-link.active svg {
            color: #ffffff;
        }

        .sidebar-scrollable::-webkit-scrollbar { width: 4px; }
        .sidebar-scrollable::-webkit-scrollbar-track { background: transparent; }
        .sidebar-scrollable::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.12); border-radius: 99px; }
        .sidebar-scrollable::-webkit-scrollbar-thumb:hover { background: rgba(255, 255, 255, 0.25); }

        .sidebar-scrollable {
            overscroll-behavior: contain;
        }

        /* STANDARD RESPONSIVE BASE */
        html {
            font-size: 16px;
            -webkit-text-size-adjust: 100%;
            scroll-behavior: smooth;
        }

        .overflow-x-auto {
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
        }

        img, svg, video, iframe {
            max-width: 100%;
            height: auto;
        }

        @media print {
            aside, header, footer, .no-print { display: none !important; }
            .print-content { margin: 0 !important; padding: 0 !important; }
        }

        /* INTRO SCREEN */
        .intro-screen {
            position: fixed;
            inset: 0;
            z-index: 200000;
            display: grid;
            place-items: center;
            padding: 24px;
            background: radial-gradient(circle at 22% 14%, rgba(56, 201, 239, .24), transparent 19rem),
                        radial-gradient(circle at 84% 84%, rgba(31, 124, 114, .28), transparent 22rem),
                        linear-gradient(145deg, #071938 0%, #0e2f67 54%, #176c70 100%);
            color: #fff;
            transform: translate3d(0, -100%, 0);
            transition: transform 1.2s cubic-bezier(.76, 0, .24, 1);
            box-shadow: 0 18px 45px rgba(7, 25, 56, .22);
            pointer-events: none;
        }

        .intro-screen.no-transition { transition: none !important; }
        .intro-screen.is-active { transform: translate3d(0, 0, 0); pointer-events: auto; }
        .intro-screen.is-leaving { transform: translate3d(0, -100%, 0); pointer-events: none; }

        .intro-content {
            width: min(320px, 86vw);
            text-align: center;
            animation: introContentIn .72s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        @keyframes introContentIn {
            from { opacity: 0; transform: translateY(18px) scale(.96); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .intro-symbol {
            width: 84px;
            height: 84px;
            margin: 0 auto 16px;
            border-radius: 20px;
            display: grid;
            place-items: center;
            background: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            padding: 4px;
        }

        .intro-logo-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 16px;
        }

        .intro-title { margin: 0; font-size: 28px; font-weight: 800; letter-spacing: -1px; }
        .intro-title span { color: #38bdf8; }
        .intro-subtitle {
            margin: 6px 0 20px;
            color: rgba(255, 255, 255, .72);
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .14em;
            text-transform: uppercase;
        }

        .intro-loader {
            width: 100%;
            height: 3px;
            border-radius: 99px;
            overflow: hidden;
            background: rgba(255, 255, 255, .15);
        }

        .intro-loader::after {
            content: '';
            display: block;
            width: 42%;
            height: 100%;
            border-radius: inherit;
            background: linear-gradient(90deg, transparent, #7ee6f4, #fff, transparent);
            animation: introLoad 2.2s ease-in-out infinite;
        }

        @keyframes introLoad {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(280%); }
        }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-100 antialiased text-slate-800" x-data="{ sidebarOpen: false }">

{{-- SCREEN LOADING INTRO --}}
<aside id="introScreen" class="intro-screen" style="display: none;" aria-label="Layar Pembuka">
    <div class="intro-content">
        <div class="intro-symbol">
            <img src="{{ asset('uploads/Logo/Logo_winshark.webp') }}"
                 alt="Logo Winshark"
                 class="intro-logo-img"
                 onerror="if(!this.dataset.retry){this.dataset.retry='1'; this.src='{{ asset('uploads/Logo/Logo_winshark.jpeg') }}';}else{this.onerror=null; this.src='{{ asset('uploads/Logo/Logo banner.jpeg') }}';}">
        </div>
        <h1 class="intro-title">Lab<span>System</span></h1>
        <p class="intro-subtitle">Winshark Community • Laboratorium TKJ</p>
        <div class="intro-loader"></div>
    </div>
</aside>
<script>
    (function() {
        if (sessionStorage.getItem('dashboard_intro') === 'true') {
            var el = document.getElementById('introScreen');
            if (el) {
                el.style.display = 'grid';
                el.classList.add('no-transition', 'is-active');
            }
        }
    })();
</script>

{{-- TOAST NOTIFIKASI GLOBAL --}}
@if (session('success') || session('error') || session('status') || $errors->any())
<div x-data="{ 
        show: true,
        progress: 100,
        init() {
            let duration = 4500;
            let interval = 50;
            let step = (interval / duration) * 100;
            let timer = setInterval(() => {
                this.progress -= step;
                if (this.progress <= 0) {
                    clearInterval(timer);
                    this.show = false;
                }
            }, interval);
        }
    }"
    x-show="show"
    x-transition:enter="transform ease-out duration-300 transition"
    x-transition:enter-start="translate-x-full opacity-0 sm:scale-95"
    x-transition:enter-end="translate-x-0 opacity-100 sm:scale-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-x-0"
    x-transition:leave-end="opacity-0 translate-x-full"
    class="fixed top-4 right-4 sm:top-6 sm:right-6 z-[999999] max-w-sm sm:max-w-md w-[calc(100%-2rem)] sm:w-auto shadow-2xl rounded-3xl bg-white/95 backdrop-blur-md border border-slate-200/80 overflow-hidden flex flex-col pointer-events-auto"
    role="alert"
    style="display: none;">
    
    <div class="p-4 flex items-start gap-3.5">
        @if (session('success'))
            <div class="w-10 h-10 rounded-2xl bg-emerald-500 text-white flex items-center justify-center flex-shrink-0 shadow-md shadow-emerald-500/20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
        @elseif (session('error') || $errors->any())
            <div class="w-10 h-10 rounded-2xl bg-rose-500 text-white flex items-center justify-center flex-shrink-0 shadow-md shadow-rose-500/20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
        @else
            <div class="w-10 h-10 rounded-2xl bg-indigo-500 text-white flex items-center justify-center flex-shrink-0 shadow-md shadow-indigo-500/20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        @endif

        <div class="flex-1 pt-0.5 min-w-0">
            <div class="flex items-center justify-between gap-2">
                <span class="text-[10px] font-extrabold uppercase tracking-wider {{ session('error') || $errors->any() ? 'text-rose-700' : 'text-emerald-700' }}">
                    LabSystem &bull; {{ session('error') || $errors->any() ? 'Peringatan' : 'Notifikasi' }}
                </span>
                <span class="text-[9px] text-slate-400 font-medium">Baru saja</span>
            </div>
            
            <p class="text-xs sm:text-sm font-bold text-slate-800 mt-0.5 leading-snug">
                @if (session('success'))
                    {{ session('success') }}
                @elseif (session('error'))
                    {{ session('error') }}
                @elseif (session('status'))
                    {{ session('status') }}
                @elseif ($errors->any())
                    {{ $errors->first() }}
                @endif
            </p>
        </div>

        <button type="button" @click="show = false" class="text-slate-400 hover:text-slate-600 transition -mr-1 -mt-1 p-1.5 rounded-xl hover:bg-slate-100 flex-shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <div class="h-1 w-full bg-slate-100">
        <div class="h-full transition-all duration-75 ease-linear {{ session('error') || $errors->any() ? 'bg-rose-500' : 'bg-emerald-500' }}"
             :style="`width: ${progress}%`"></div>
    </div>
</div>
@endif

<div class="min-h-screen flex flex-col lg:flex-row relative">

    {{-- Mobile overlay --}}
    <div x-cloak
         x-show="sidebarOpen"
         @click="sidebarOpen = false"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-40 lg:hidden"></div>

    {{-- ====== SIDEBAR ====== --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
           class="-translate-x-full fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 border-r border-slate-800 text-white flex flex-col shadow-2xl transition-transform duration-300 ease-in-out lg:sticky lg:top-0 lg:h-screen lg:translate-x-0">

        {{-- 1. HEADER --}}
        <div class="px-5 py-4 border-b border-slate-800 flex items-center justify-between flex-shrink-0">
            <a href="{{ route('dashboard') }}" 
               @if(request()->routeIs('dashboard')) @click.prevent="sidebarOpen = false" @endif
               class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center p-1 shadow-md flex-shrink-0 overflow-hidden">
                    <img src="{{ asset('uploads/Logo/Logo_winshark.webp') }}"
                         alt="Winshark Logo"
                         class="w-full h-full object-cover rounded-lg"
                         onerror="if(!this.dataset.retry){this.dataset.retry='1'; this.src='{{ asset('uploads/Logo/Logo_winshark.jpeg') }}';}else{this.onerror=null; this.src='{{ asset('uploads/Logo/Logo banner.jpeg') }}';}">
                </div>
                <div>
                    <p class="font-extrabold text-white text-sm leading-tight tracking-tight">Lab<span class="text-indigo-400">System</span></p>
                    <p class="text-slate-400 text-[10px] font-medium tracking-wide">Winshark Community</p>
                </div>
            </a>
            <button @click="sidebarOpen = false" class="lg:hidden p-1 text-slate-400 hover:text-white focus:outline-none">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- 2. NAV MENU --}}
        <nav id="sidebarNav" class="flex-1 py-2 px-2 space-y-0.5 overflow-y-auto sidebar-scrollable">

            {{-- Dashboard --}}
            <div class="sidebar-group-label flex items-center gap-2 text-slate-400">
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                <span>Utama</span>
            </div>
            <a href="{{ route('dashboard') }}"
               @if(request()->routeIs('dashboard')) @click.prevent="sidebarOpen = false" @endif
               class="sidebar-link flex items-center gap-3 {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span>{{ Auth::user()->isAdmin() ? 'Dashboard Admin' : 'Beranda' }}</span>
            </a>

            @if(Auth::user()->isAdmin())
                {{-- MENU KHUSUS ADMIN --}}
                <div class="sidebar-group-label flex items-center gap-2 text-slate-400 mt-2">
                    <svg class="w-3.5 h-3.5 text-indigo-400/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <span>Inventaris</span>
                </div>
                <a href="{{ route('barang.index') }}"
                   @if(request()->routeIs('barang.index') || request()->routeIs('barang.show') || request()->routeIs('barang.edit')) @click.prevent="sidebarOpen = false" @endif
                   class="sidebar-link flex items-center gap-3 {{ (request()->routeIs('barang.index') || request()->routeIs('barang.show') || request()->routeIs('barang.edit')) ? 'active' : '' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <span>Data Barang</span>
                </a>
                <a href="{{ route('barang.create') }}"
                   @if(request()->routeIs('barang.create')) @click.prevent="sidebarOpen = false" @endif
                   class="sidebar-link flex items-center gap-3 {{ request()->routeIs('barang.create') ? 'active' : '' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Tambah Barang</span>
                </a>

                <div class="sidebar-group-label flex items-center gap-2 text-slate-400 mt-2">
                    <svg class="w-3.5 h-3.5 text-indigo-400/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                    <span>Transaksi</span>
                </div>
                <a href="{{ route('peminjaman.index') }}"
                   @if(request()->routeIs('peminjaman.index')) @click.prevent="sidebarOpen = false" @endif
                   class="sidebar-link flex items-center justify-between {{ request()->routeIs('peminjaman.index') || request()->routeIs('peminjaman.create') || request()->routeIs('peminjaman.show') ? 'active' : '' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                        <span>Kelola Peminjaman</span>
                    </div>
                    @php
                        if (request()->routeIs('peminjaman.*')) {
                            session(['peminjaman_last_read_at' => now()->toDateTimeString()]);
                        }

                        $lastReadAt = session('peminjaman_last_read_at');

                        if (request()->routeIs('peminjaman.*')) {
                            $pendingCount = 0;
                        } else {
                            $pendingQuery = \App\Models\Peminjaman::where(function($q) {
                                $q->where('status', 'like', '%Menunggu%')->orWhereNull('status');
                            });

                            if ($lastReadAt) {
                                $pendingQuery->where('created_at', '>', $lastReadAt);
                            }
                            $pendingCount = $pendingQuery->count();
                        }
                    @endphp
                    @if($pendingCount > 0)
                        <span class="bg-rose-500 text-white font-extrabold text-[10px] px-2 py-0.5 rounded-full shadow-xs animate-pulse ml-auto">
                            {{ $pendingCount }}
                        </span>
                    @endif
                </a>

                <div class="sidebar-group-label flex items-center gap-2 text-slate-400 mt-2">
                    <svg class="w-3.5 h-3.5 text-indigo-400/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>Maintenance</span>
                </div>
                <a href="{{ route('maintenance.index') }}"
                   @if(request()->routeIs('maintenance.index') || request()->routeIs('maintenance.show')) @click.prevent="sidebarOpen = false" @endif
                   class="sidebar-link flex items-center gap-3 {{ (request()->routeIs('maintenance.index') || request()->routeIs('maintenance.show')) ? 'active' : '' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>Riwayat Perbaikan</span>
                </a>

                <div class="sidebar-group-label flex items-center gap-2 text-slate-400 mt-2">
                    <svg class="w-3.5 h-3.5 text-indigo-400/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>Laporan</span>
                </div>
                <a href="{{ route('laporan.index') }}"
                   @if(request()->routeIs('laporan.index')) @click.prevent="sidebarOpen = false" @endif
                   class="sidebar-link flex items-center gap-3 {{ request()->routeIs('laporan.index') ? 'active' : '' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>Laporan Inventaris</span>
                </a>
                <a href="{{ route('laporan.maintenance') }}"
                   @if(request()->routeIs('laporan.maintenance*')) @click.prevent="sidebarOpen = false" @endif
                   class="sidebar-link flex items-center gap-3 {{ request()->routeIs('laporan.maintenance*') ? 'active' : '' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                    <span>Laporan Maintenance</span>
                </a>

                <div class="sidebar-group-label flex items-center gap-2 text-slate-400 mt-2">
                    <svg class="w-3.5 h-3.5 text-indigo-400/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <span>Pengguna</span>
                </div>
                <a href="{{ route('pengguna.index') }}"
                   @if(request()->routeIs('pengguna.*')) @click.prevent="sidebarOpen = false" @endif
                   class="sidebar-link flex items-center gap-3 {{ request()->routeIs('pengguna.*') ? 'active' : '' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <span>Kelola Pengguna</span>
                </a>

            @elseif(Auth::user()->isKepalaLab())
                {{-- MENU KHUSUS KEPALA LAB --}}
                <div class="sidebar-group-label flex items-center gap-2 text-slate-400 mt-2">
                    <svg class="w-3.5 h-3.5 text-amber-400/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>Laporan</span>
                </div>
                <a href="{{ route('laporan.maintenance') }}"
                   @if(request()->routeIs('laporan.maintenance*')) @click.prevent="sidebarOpen = false" @endif
                   class="sidebar-link flex items-center gap-3 {{ request()->routeIs('laporan.maintenance*') ? 'active' : '' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                    <span>Laporan Maintenance</span>
                </a>
                <div class="sidebar-group-label flex items-center gap-2 text-slate-400 mt-2">
                    <svg class="w-3.5 h-3.5 text-amber-400/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>Maintenance</span>
                </div>
                <a href="{{ route('maintenance.index') }}"
                   @if(request()->routeIs('maintenance.index') || request()->routeIs('maintenance.show')) @click.prevent="sidebarOpen = false" @endif
                   class="sidebar-link flex items-center gap-3 {{ (request()->routeIs('maintenance.index') || request()->routeIs('maintenance.show')) ? 'active' : '' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>Data Maintenance</span>
                </a>

            @elseif(Auth::user()->isKoordinatorLab())
                {{-- MENU KHUSUS KOORDINATOR LAB --}}
                @php $userLabLabel = Auth::user()->laboratorium_penugasan ?: 'Laboratorium TKJ'; @endphp
                
                {{-- 1. Layanan Praktikum --}}
                <div class="sidebar-group-label flex items-center gap-2 text-slate-400 mt-2">
                    <svg class="w-3.5 h-3.5 text-indigo-400/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <span>Layanan Praktikum</span>
                </div>
                <a href="{{ route('katalog.index') }}"
                   @if(request()->routeIs('katalog.*')) @click.prevent="sidebarOpen = false" @endif
                   class="sidebar-link flex items-center gap-3 {{ request()->routeIs('katalog.*') ? 'active' : '' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <span>Katalog Alat & Pinjam</span>
                </a>

                <a href="{{ route('peminjaman.saya') }}"
                   @if(request()->routeIs('peminjaman.saya')) @click.prevent="sidebarOpen = false" @endif
                   class="sidebar-link flex items-center gap-3 {{ request()->routeIs('peminjaman.saya') ? 'active' : '' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <span>Peminjaman Saya</span>
                </a>

                {{-- Scan QR Code --}}
                <a href="{{ route('scan.qr') }}"
                   @if(request()->routeIs('scan.qr')) @click.prevent="sidebarOpen = false" @endif
                   class="sidebar-link flex items-center gap-3 {{ request()->routeIs('scan.qr') ? 'active' : '' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                    </svg>
                    <span>Scan QR Code</span>
                </a>

                {{-- 2. Fitur Maintenance --}}
                <div class="sidebar-group-label flex items-center gap-2 text-slate-400 mt-3">
                    <svg class="w-3.5 h-3.5 text-emerald-400/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>Maintenance {{ $userLabLabel }}</span>
                </div>
                <a href="{{ route('maintenance.index') }}"
                   @if(request()->routeIs('maintenance.index') || request()->routeIs('maintenance.show')) @click.prevent="sidebarOpen = false" @endif
                   class="sidebar-link flex items-center gap-3 {{ (request()->routeIs('maintenance.index') || request()->routeIs('maintenance.show')) ? 'active' : '' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                    <span>Riwayat Maintenance</span>
                </a>
                <a href="{{ route('maintenance.create') }}"
                   @if(request()->routeIs('maintenance.create')) @click.prevent="sidebarOpen = false" @endif
                   class="sidebar-link flex items-center gap-3 {{ request()->routeIs('maintenance.create') ? 'active' : '' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Catat Maintenance</span>
                </a>
                <a href="{{ route('laporan.maintenance') }}"
                   @if(request()->routeIs('laporan.maintenance*')) @click.prevent="sidebarOpen = false" @endif
                   class="sidebar-link flex items-center gap-3 {{ request()->routeIs('laporan.maintenance*') ? 'active' : '' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                    <span>Laporan Maintenance</span>
                </a>

            @else
                {{-- MENU KHUSUS USER (SISWA / GURU) --}}
                <div class="sidebar-group-label flex items-center gap-2 text-slate-400 mt-2">
                    <svg class="w-3.5 h-3.5 text-indigo-400/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <span>Layanan Praktikum</span>
                </div>
                <a href="{{ route('katalog.index') }}"
                   @if(request()->routeIs('katalog.*')) @click.prevent="sidebarOpen = false" @endif
                   class="sidebar-link flex items-center gap-3 {{ request()->routeIs('katalog.*') ? 'active' : '' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <span>Katalog Alat & Pinjam</span>
                </a>

                <a href="{{ route('peminjaman.saya') }}"
                   @if(request()->routeIs('peminjaman.saya')) @click.prevent="sidebarOpen = false" @endif
                   class="sidebar-link flex items-center gap-3 {{ request()->routeIs('peminjaman.saya') ? 'active' : '' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <span>Peminjaman Saya</span>
                </a>

                {{-- Scan QR Code --}}
                <a href="{{ route('scan.qr') }}"
                   @if(request()->routeIs('scan.qr')) @click.prevent="sidebarOpen = false" @endif
                   class="sidebar-link flex items-center gap-3 {{ request()->routeIs('scan.qr') ? 'active' : '' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                    </svg>
                    <span>Scan QR Code</span>
                </a>
            @endif

            {{-- Informasi & Berita --}}
            <div class="sidebar-group-label flex items-center gap-2 text-slate-400 mt-2">
                <svg class="w-3.5 h-3.5 text-indigo-400/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                </svg>
                <span>Informasi</span>
            </div>
            <a href="{{ route('berita.index') }}"
               @if(request()->routeIs('berita.*')) @click.prevent="sidebarOpen = false" @endif
               class="sidebar-link flex items-center gap-3 {{ request()->routeIs('berita.*') ? 'active' : '' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                </svg>
                <span>Pengumuman Lab</span>
            </a>

            {{-- Butuh Bantuan --}}
            <div class="sidebar-group-label flex items-center gap-2 text-slate-400 mt-2">
                <svg class="w-3.5 h-3.5 text-indigo-400/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <span>Bantuan</span>
            </div>
            <a href="{{ route('bantuan.index') }}"
               @if(request()->routeIs('bantuan.*')) @click.prevent="sidebarOpen = false" @endif
               class="sidebar-link flex items-center gap-3 {{ request()->routeIs('bantuan.*') ? 'active' : '' }}">
                <svg class="w-4 h-4 flex-shrink-0 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <span>Butuh Bantuan</span>
            </a>

            {{-- Profil --}}
            <div class="sidebar-group-label flex items-center gap-2 text-slate-400 mt-2">
                <svg class="w-3.5 h-3.5 text-indigo-400/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span>Akun</span>
            </div>
            <a href="{{ route('profile.edit') }}"
               @if(request()->routeIs('profile.*')) @click.prevent="sidebarOpen = false" @endif
               class="sidebar-link flex items-center gap-3 {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span>Profil Saya</span>
            </a>

        </nav>

        {{-- 3. USER INFO & LOGOUT --}}
        <div class="px-4 py-4 border-t border-slate-800 flex-shrink-0">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 bg-slate-800 rounded-full flex items-center justify-center flex-shrink-0 shadow-sm border border-slate-700 overflow-hidden">
                    @if(Auth::user()->foto)
                        <img src="{{ Auth::user()->foto_url ?? asset('uploads/' . Auth::user()->foto) }}" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover"
                             onerror="if(!this.dataset.retry){this.dataset.retry='1'; this.src='{{ asset('storage/' . Auth::user()->foto) }}?v={{ time() }}';}else{this.style.display='none'; this.nextElementSibling.style.display='flex';}">
                        <span style="display: none;" class="text-white font-bold text-sm">{{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}</span>
                    @else
                        <span class="text-white font-bold text-sm">{{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}</span>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-white truncate">{{ Auth::user()->name ?? 'Guest' }}</p>
                    <p class="text-xs text-slate-400 capitalize font-medium mt-0.5">{{ Auth::user()->role ?? 'Siswa' }}</p>
                </div>
            </div>
            @auth
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-slate-800 text-xs font-semibold transition-all duration-150">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Logout
                    </button>
                </form>
            @endauth
        </div>
    </aside>

    {{-- ====== MAIN CONTENT ====== --}}
    <div class="flex-1 flex flex-col min-w-0 min-h-screen @if(request()->routeIs('bantuan*')) h-[100dvh] max-h-[100dvh] overflow-hidden @endif">

        {{-- Topbar (Sembunyikan di halaman Live Chat / Bantuan agar menjadi workspace full-bleed sesuai Foto 1) --}}
        @unless(request()->routeIs('bantuan*'))
        <header class="bg-white/95 backdrop-blur-md border-b border-slate-200/90 px-4 lg:px-6 py-4 flex items-center justify-between sticky top-0 z-40 shadow-xs no-print flex-shrink-0">
            <div class="flex items-center gap-3">
                <button @click="sidebarOpen = true" class="lg:hidden p-1.5 rounded-lg text-slate-500 hover:bg-slate-100 focus:outline-none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <div>
                    <h1 id="pageTitle" class="text-base lg:text-lg font-bold text-slate-800 leading-tight">@yield('page_title', 'Dashboard')</h1>
                    <p id="pageSubtitle" class="text-[10px] lg:text-xs text-slate-500 hidden sm:block">@yield('page_subtitle', 'Winshark Community • ' . (Auth::user()->laboratorium_penugasan ?? 'Laboratorium TKJ'))</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                @if(!Auth::user()->isAdmin())
                    <a href="{{ route('scan.qr') }}" class="hidden sm:inline-flex items-center gap-1.5 bg-slate-100 hover:bg-slate-200 text-slate-800 px-3 py-1.5 rounded-xl text-xs font-semibold transition border border-slate-200">
                        <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                        </svg>
                        Scan QR
                    </a>
                @endif

                @php
                    /*
                     * NOTIFIKASI PENGUMUMAN
                     * Hanya pengumuman yang BELUM dibaca yang ditampilkan di lonceng.
                     * Status dibaca disimpan di users.read_beritas dalam bentuk JSON.
                     */
                    $notificationUser = Auth::user();

                    $readInts = [];
                    if ($notificationUser && !empty($notificationUser->read_beritas)) {
                        $decodedReadBeritas = json_decode($notificationUser->read_beritas, true);

                        if (is_array($decodedReadBeritas)) {
                            $readInts = array_values(array_unique(array_map('intval', $decodedReadBeritas)));
                        }
                    }

                    $unreadBeritaQuery = \App\Models\Berita::query()
                        ->with('user')
                        ->forUser($notificationUser);

                    if (!empty($readInts)) {
                        $unreadBeritaQuery->whereNotIn('id', $readInts);
                    }

                    $displayUnreadCount = (clone $unreadBeritaQuery)->count();

                    // Batasi isi dropdown agar header tetap ringan.
                    // Semua pengumuman tetap tersedia di halaman berita.
                    $globalUnreadBeritas = (clone $unreadBeritaQuery)
                        ->latest()
                        ->take(5)
                        ->get();
                @endphp

                <div class="relative"
                     x-data="{ open: false }"
                     @keydown.escape.window="open = false">

                    <button @click="open = !open"
                            type="button"
                            :aria-expanded="open"
                            aria-haspopup="true"
                            aria-label="{{ $displayUnreadCount > 0 ? 'Notifikasi Pengumuman, ' . $displayUnreadCount . ' belum dibaca' : 'Notifikasi Pengumuman' }}"
                            title="Pengumuman & Notifikasi"
                            class="w-9 h-9 bg-slate-100 rounded-full flex items-center justify-center relative hover:bg-slate-200 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 shadow-xs">
                        <svg class="w-4 h-4 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 00-5-5.917V4a1 1 0 10-2 0v1.083A6 6 0 006 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>

                        @if($displayUnreadCount > 0)
                            <span class="absolute -top-1 -right-1 min-w-[18px] h-[18px] bg-rose-600 text-white text-[10px] font-extrabold rounded-full flex items-center justify-center px-1 shadow border-2 border-white">
                                {{ $displayUnreadCount > 99 ? '99+' : $displayUnreadCount }}
                            </span>
                        @endif
                    </button>

                    <div x-cloak
                         x-show="open"
                         @click.outside="open = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                         class="absolute right-0 mt-2 w-[min(20rem,calc(100vw-2rem))] sm:w-80 bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden z-50">

                        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between gap-3 bg-slate-50/80">
                            <div class="min-w-0">
                                <span class="block font-bold text-slate-800 text-xs uppercase tracking-wider">
                                    Pengumuman Lab
                                </span>

                                <span class="block mt-0.5 text-[10px] text-slate-400">
                                    {{ $displayUnreadCount > 0 ? $displayUnreadCount . ' belum dibaca' : 'Tidak ada informasi baru' }}
                                </span>
                            </div>

                            @if($displayUnreadCount > 0)
                                <form action="{{ route('berita.markAllRead') }}" method="POST" class="inline shrink-0">
                                    @csrf
                                    <button type="submit"
                                            class="text-[10px] font-semibold text-indigo-600 hover:text-indigo-800 hover:underline focus:outline-none">
                                        Tandai semua
                                    </button>
                                </form>
                            @else
                                <span class="shrink-0 text-[10px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-1 rounded-md border border-emerald-200">
                                    Semua dibaca
                                </span>
                            @endif
                        </div>

                        <div class="divide-y divide-slate-100 max-h-72 overflow-y-auto overscroll-contain">
                            @forelse($globalUnreadBeritas as $gBerita)
                                <a href="{{ route('berita.show', $gBerita->id) }}"
                                   @click="open = false"
                                   class="group block px-4 py-3 bg-white hover:bg-slate-50 transition-colors">

                                    <div class="flex items-start gap-2.5">
                                        <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-indigo-600"
                                              aria-hidden="true"></span>

                                        <div class="min-w-0 flex-1">
                                            <p class="text-xs font-semibold text-slate-800 group-hover:text-indigo-700 truncate">
                                                {{ $gBerita->judul }}
                                            </p>

                                            <p class="text-[10px] text-slate-500 mt-1 line-clamp-2 leading-relaxed">
                                                {{ strip_tags($gBerita->isi) }}
                                            </p>

                                            <div class="flex items-center justify-between gap-2 mt-1.5">
                                                <span class="text-[9px] text-slate-400">
                                                    {{ $gBerita->created_at->diffForHumans() }}
                                                </span>

                                                @if(
                                                    isset($gBerita->target_kelas)
                                                    && $gBerita->target_kelas
                                                    && !str_contains($gBerita->target_kelas, 'Semua')
                                                )
                                                    <span class="max-w-[120px] truncate text-[9px] bg-indigo-50 text-indigo-700 border border-indigo-200/60 font-semibold px-1.5 py-0.5 rounded-md">
                                                        {{ $gBerita->target_kelas }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <div class="px-5 py-7 text-center">
                                    <div class="mx-auto w-9 h-9 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                    <p class="mt-2.5 text-xs font-semibold text-slate-700">
                                        Semua sudah dibaca
                                    </p>
                                    <p class="mt-1 text-[10px] leading-4 text-slate-400">
                                        Pengumuman baru akan muncul di sini.
                                    </p>
                                </div>
                            @endforelse
                        </div>

                        <div class="px-4 py-2.5 bg-slate-50 border-t border-slate-100 text-center">
                            <a href="{{ route('berita.index') }}"
                               @click="open = false"
                               class="inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                                Lihat Semua Pengumuman
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-6-6 6 6-6 6"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        @endunless

        {{-- Page Content --}}
        <main id="mainContent" class="flex-1 @if(request()->routeIs('bantuan*')) p-0 flex flex-col min-h-0 overflow-hidden @else p-4 lg:p-6 @endif print-content w-full transition-opacity duration-150">
            @yield('content')
        </main>

        {{-- Footer (Hanya tampil di luar halaman Bantuan agar tidak memicu scroll berlebih pada chat) --}}
        @if(!request()->routeIs('bantuan*'))
        <footer class="bg-white border-t border-slate-200 px-6 py-3 text-center no-print flex-shrink-0">
            <p class="text-xs text-slate-500 font-medium">© {{ date('Y') }} Winshark Community • Sistem Manajemen & Peminjaman Laboratorium TKJ</p>
        </footer>
        @endif
    </div>
</div>

{{-- Top Loading Progress Bar --}}
<div id="spaProgressBar" class="fixed top-0 left-0 right-0 h-1 bg-indigo-600 z-[9999999] transition-all duration-200 opacity-0 pointer-events-none" style="width: 0%;"></div>



<script>
    document.addEventListener('DOMContentLoaded', () => {
        const intro = document.getElementById('introScreen');
        if (intro) {
            const shouldShowIntro = sessionStorage.getItem('dashboard_intro') === 'true';
            if (shouldShowIntro) {
                sessionStorage.removeItem('dashboard_intro');
                intro.style.display = 'grid';
                void intro.offsetHeight;
                intro.classList.remove('no-transition');

                setTimeout(() => {
                    intro.classList.remove('is-active');
                    intro.classList.add('is-leaving');
                    setTimeout(() => {
                        intro.classList.remove('is-leaving');
                        intro.style.display = 'none';
                    }, 1250);
                }, 750);
            } else {
                intro.style.display = 'none';
            }
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
        const sidebarNav = document.getElementById('sidebarNav');
        if (sidebarNav) {
            const savedScroll = sessionStorage.getItem('sidebar_scroll_pos');
            if (savedScroll !== null) {
                sidebarNav.scrollTop = parseInt(savedScroll, 10);
            } else {
                const activeMenu = sidebarNav.querySelector('.sidebar-link.active');
                if (activeMenu) {
                    activeMenu.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
                }
            }

            sidebarNav.addEventListener('scroll', () => {
                sessionStorage.setItem('sidebar_scroll_pos', sidebarNav.scrollTop);
            });
        }
    });

    (function() {
        let isNavigating = false;

        function showProgressBar() {
            const bar = document.getElementById('spaProgressBar');
            if (!bar) return;
            bar.style.transition = 'width 0.2s ease, opacity 0.1s ease';
            bar.style.opacity = '1';
            bar.style.width = '35%';
            setTimeout(() => { if (isNavigating && bar) bar.style.width = '75%'; }, 120);
        }

        function finishProgressBar() {
            const bar = document.getElementById('spaProgressBar');
            if (!bar) return;
            bar.style.width = '100%';
            setTimeout(() => {
                bar.style.opacity = '0';
                setTimeout(() => { bar.style.width = '0%'; }, 200);
            }, 120);
        }

        function normalizeUrl(rawUrl) {
            try {
                const u = new URL(rawUrl, window.location.origin);
                const cleanPath = u.pathname.replace(/\/+$/, '') || '/';
                return u.origin + cleanPath + u.search;
            } catch(e) {
                return rawUrl;
            }
        }

        function updateActiveSidebar(targetUrl) {
            try {
                const targetU = new URL(targetUrl, window.location.origin);
                const targetPath = targetU.pathname.replace(/\/+$/, '') || '/';

                const links = Array.from(document.querySelectorAll('#sidebarNav .sidebar-link'));
                links.forEach(l => l.classList.remove('active'));

                // Exact match terlebih dahulu
                let matchedLink = links.find(link => {
                    const href = link.getAttribute('href');
                    if (!href) return false;
                    const linkPath = new URL(href, window.location.origin).pathname.replace(/\/+$/, '') || '/';
                    return linkPath === targetPath;
                });

                // Fallback prefix match untuk sub-halaman
                if (!matchedLink && targetPath !== '/' && targetPath !== '/dashboard') {
                    matchedLink = links.find(link => {
                        const href = link.getAttribute('href');
                        if (!href) return false;
                        const linkPath = new URL(href, window.location.origin).pathname.replace(/\/+$/, '') || '/';
                        return linkPath !== '/' && linkPath !== '/dashboard' && targetPath.startsWith(linkPath + '/');
                    });
                }

                if (matchedLink) {
                    matchedLink.classList.add('active');
                }
            } catch(e) {}
        }

        async function loadPage(url, push = true) {
            const targetNormalized = normalizeUrl(url);
            const currentNormalized = normalizeUrl(window.location.href);

            if (push && targetNormalized === currentNormalized) {
                return;
            }

            if (isNavigating) return;

            // Jika tujuan atau halaman asal adalah Bantuan/Chat (/bantuan), lakukan reload standar agar layout 100% sinkron
            if (url.includes('/bantuan') || window.location.pathname.includes('/bantuan')) {
                window.location.href = url;
                return;
            }

            isNavigating = true;
            showProgressBar();

            // 1. Matikan polling background timer agar tidak reload halaman sendiri
            if (window._laporanMaintenanceInterval) {
                clearInterval(window._laporanMaintenanceInterval);
                window._laporanMaintenanceInterval = null;
            }

            // 2. Matikan kamera/hardware stream
            if (typeof window.stopQrCamera === 'function') {
                try { window.stopQrCamera(); } catch(e) {}
            }
            if (navigator.mediaDevices) {
                document.querySelectorAll('video').forEach(v => {
                    if (v.srcObject) {
                        try {
                            v.srcObject.getTracks().forEach(track => track.stop());
                            v.srcObject = null;
                        } catch(e) {}
                    }
                });
            }

            const currentMain = document.getElementById('mainContent') || document.querySelector('main');
            if (currentMain) {
                currentMain.style.opacity = '0.7';
            }

            try {
                const response = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-SPA-Navigation': 'true'
                    }
                });

                if (!response.ok) {
                    window.location.href = url;
                    return;
                }

                const html = await response.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                const newMain = doc.getElementById('mainContent') || doc.querySelector('main');

                if (!newMain || !currentMain) {
                    window.location.href = url;
                    return;
                }

                document.title = doc.title;

                const newTitle = doc.getElementById('pageTitle');
                const currentTitle = document.getElementById('pageTitle');
                if (newTitle && currentTitle) currentTitle.innerHTML = newTitle.innerHTML;

                const newSubtitle = doc.getElementById('pageSubtitle');
                const currentSubtitle = document.getElementById('pageSubtitle');
                if (newSubtitle && currentSubtitle) currentSubtitle.innerHTML = newSubtitle.innerHTML;

                if (window.Alpine && typeof window.Alpine.destroyTree === 'function') {
                    try { window.Alpine.destroyTree(currentMain); } catch(e) {}
                }

                // Sinkronkan class CSS container & main agar tata letak halaman selalu sinkron
                currentMain.className = newMain.className;
                if (newMain.parentElement && currentMain.parentElement) {
                    currentMain.parentElement.className = newMain.parentElement.className;
                }

                currentMain.innerHTML = newMain.innerHTML;
                currentMain.style.opacity = '1';

                const externalScripts = doc.querySelectorAll('script[src]');
                for (const scriptTag of externalScripts) {
                    const src = scriptTag.getAttribute('src');
                    if (src && !document.querySelector(`script[src="${src}"]`)) {
                        const s = document.createElement('script');
                        s.src = src;
                        document.head.appendChild(s);
                    }
                }

                const introEl = document.getElementById('introScreen');
                if (introEl) {
                    introEl.style.display = 'none';
                    introEl.classList.remove('is-active', 'no-transition');
                }

                const inlineScripts = currentMain.querySelectorAll('script');
                inlineScripts.forEach(oldScript => {
                    const newScript = document.createElement('script');
                    Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                    newScript.appendChild(document.createTextNode(oldScript.innerHTML));
                    oldScript.parentNode.replaceChild(newScript, oldScript);
                });

                if (window.Alpine) {
                    try {
                        window.Alpine.initTree(currentMain);
                    } catch (e) {
                        console.error('Alpine init error:', e);
                    }
                }

                if (push) {
                    window.history.pushState({ spa: true, url: targetNormalized }, '', targetNormalized);
                }
                updateActiveSidebar(targetNormalized);

                window.scrollTo({ top: 0, behavior: 'instant' });

                const bodyEl = document.querySelector('body');
                if (bodyEl && bodyEl._x_dataStack && bodyEl._x_dataStack[0]) {
                    bodyEl._x_dataStack[0].sidebarOpen = false;
                }

            } catch (err) {
                console.error('SPA Navigation error, full reload:', err);
                window.location.href = url;
            } finally {
                isNavigating = false;
                finishProgressBar();
                if (currentMain) currentMain.style.opacity = '1';
            }
        }

        document.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (!link) return;

            const href = link.getAttribute('href');
            if (!href || href.startsWith('#') || href.startsWith('javascript:') || href.startsWith('mailto:') || href.startsWith('tel:')) return;
            if (link.hasAttribute('download') || link.getAttribute('target') === '_blank') return;
            if (link.closest('.no-spa') || link.hasAttribute('data-no-spa')) return;

            try {
                const urlObj = new URL(link.href, window.location.origin);
                if (urlObj.origin !== window.location.origin) return;

                // Abaikan SPA jika navigasi menuju atau berasal dari halaman bantuan/pdf/export/logout
                if (urlObj.pathname.includes('/pdf') 
                 || urlObj.pathname.includes('/export') 
                 || urlObj.pathname.includes('/logout') 
                 || urlObj.pathname.includes('/bantuan')
                 || window.location.pathname.includes('/bantuan')) return;

                const targetNormalized = normalizeUrl(link.href);
                const currentNormalized = normalizeUrl(window.location.href);

                if (targetNormalized === currentNormalized) {
                    e.preventDefault();
                    return;
                }

                e.preventDefault();
                loadPage(targetNormalized, true);
            } catch(err) {}
        });

        window.addEventListener('popstate', function(e) {
            if (window.location.pathname.includes('/bantuan')) {
                window.location.href = window.location.href;
                return;
            }
            loadPage(window.location.href, false);
        });
    })();
</script>

@stack('scripts')
</body>
</html>