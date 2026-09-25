@extends('layouts.app')

@section('title', 'Pusat Bantuan & Asisten Lab — Winshark Community')
@section('page_title', 'Pusat Bantuan Lab')
@section('page_subtitle', 'Layanan Informasi & Asisten Interaktif Laboratorium TKJ')

@push('styles')
<style>
    /* Scope khusus chat workspace: Mengunci view layout ke batas bawah layar */
    html, body {
        height: 100vh !important;
        height: 100dvh !important;
        max-height: 100dvh !important;
        overflow: hidden !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    
    body > div.min-h-screen {
        height: 100vh !important;
        height: 100dvh !important;
        max-height: 100dvh !important;
        overflow: hidden !important;
    }

    #mainLayoutWrapper {
        height: 100vh !important;
        height: 100dvh !important;
        max-height: 100dvh !important;
        min-height: 0 !important;
        overflow: hidden !important;
        display: flex !important;
        flex-direction: column !important;
    }

    main#mainContent {
        height: 100% !important;
        max-height: 100% !important;
        min-height: 0 !important;
        padding: 0 !important;
        margin: 0 !important;
        width: 100% !important;
        display: flex !important;
        flex-direction: column !important;
        overflow: hidden !important;
        flex: 1 1 0% !important;
    }

    .chat-bubble-content p { margin-bottom: 0.35rem; }
    .chat-bubble-content p:last-child { margin-bottom: 0; }
    .chat-bubble-content ul { list-style-type: disc; padding-left: 1.15rem; margin-top: 0.2rem; margin-bottom: 0.35rem; }
    .chat-bubble-content ol { list-style-type: decimal; padding-left: 1.15rem; margin-top: 0.2rem; margin-bottom: 0.35rem; }
    .chat-bubble-content li { margin-bottom: 0.2rem; }
    .chat-bubble-content a { color: inherit; font-weight: 600; text-decoration: underline; text-underline-offset: 2px; }
    .chat-bubble-content code { background-color: rgba(0,0,0,0.06); padding: 0.1rem 0.3rem; border-radius: 0.25rem; font-family: monospace; font-size: 0.85em; }
    .dark .chat-bubble-content code { background-color: rgba(255,255,255,0.1); }
    
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    
    #chatMessages::-webkit-scrollbar { width: 5px; }
    #chatMessages::-webkit-scrollbar-track { background: transparent; }
    #chatMessages::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    #chatMessages::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    .dark #chatMessages::-webkit-scrollbar-thumb { background: #334155; }
    .dark #chatMessages::-webkit-scrollbar-thumb:hover { background: #475569; }

    .help-sidebar-scroll::-webkit-scrollbar { width: 4px; }
    .help-sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
    .help-sidebar-scroll::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    .dark .help-sidebar-scroll::-webkit-scrollbar-thumb { background: #334155; }

    [x-cloak] { display: none !important; }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(6px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in { animation: fadeIn 0.2s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
</style>
@endpush

@section('content')

<div x-data="labChatBot('{{ $initialCsSession }}')"
     x-init="init()"
     class="flex flex-col w-full max-w-full h-full min-h-0 flex-1 bg-slate-50 dark:bg-slate-950 relative select-text overflow-hidden">
    
    {{-- 1. ASSISTANT / CS HEADER --}}
    <header class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 px-3 sm:px-6 py-2.5 sm:py-3.5 flex items-center justify-between shrink-0 z-20 shadow-2xs gap-2 w-full max-w-full min-w-0">
        <div class="flex items-center gap-2 sm:gap-3 min-w-0 flex-1">
            {{-- Mobile Sidebar Drawer Toggle Button --}}
            <button type="button" 
                    onclick="window.openSidebarDrawer()"
                    @click="window.openSidebarDrawer()" 
                    class="lg:hidden p-1.5 sm:p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:white transition-colors focus:outline-none shrink-0 border border-slate-200 dark:border-slate-800 shadow-2xs cursor-pointer active:scale-95" 
                    aria-label="Buka Menu Navigasi" 
                    title="Buka Menu">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            {{-- Avatar Header (Dinamis: Bot vs CS Langsung) --}}
            <div class="relative shrink-0">
                <template x-if="!isCsMode">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-sky-50 dark:bg-sky-950/40 text-sky-600 dark:text-sky-400 flex items-center justify-center border border-sky-200 dark:border-sky-800/80 shadow-2xs">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M12 2a2 2 0 0 1 2 2v2a2 2 0 0 1-2 2 2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z"/>
                            <rect x="3" y="8" width="18" height="12" rx="3"/>
                            <circle cx="9" cy="13" r="1.5" fill="currentColor"/>
                            <circle cx="15" cy="13" r="1.5" fill="currentColor"/>
                            <path d="M9 17h6"/>
                        </svg>
                    </div>
                </template>
                <template x-if="isCsMode">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center border border-indigo-200 dark:border-indigo-800/80 shadow-2xs">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                        </svg>
                    </div>
                </template>
                <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 sm:w-3 sm:h-3 bg-emerald-500 border-2 border-white dark:border-slate-900 rounded-full"></span>
            </div>

            {{-- Info Asisten / CS --}}
            <div class="min-w-0 flex-1">
                <div class="flex items-center gap-1 sm:gap-1.5 flex-wrap">
                    <h1 class="text-xs sm:text-base font-bold text-slate-900 dark:text-white truncate">
                        <span x-text="isCsMode ? 'Customer Service Langsung' : 'Asisten Lab'"></span>
                    </h1>
                    <span :class="isCsMode ? 'bg-indigo-50 dark:bg-indigo-950/50 text-indigo-700 dark:text-indigo-300 border-indigo-200 dark:border-indigo-800/80' : 'bg-sky-50 dark:bg-sky-950/50 text-sky-700 dark:text-sky-300 border-sky-200 dark:border-sky-800/80'"
                          class="inline-flex px-1.5 py-0.2 sm:px-2 sm:py-0.5 rounded text-[9px] sm:text-[10px] font-semibold border shrink-0">
                        {{ ucfirst(Auth::user()->role ?? 'Siswa') }}
                    </span>
                    <template x-if="isCsMode && sessionCode">
                        <span class="inline-flex px-1.5 py-0.2 sm:px-2 sm:py-0.5 rounded text-[9px] sm:text-[10px] font-mono font-bold bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800/80 shrink-0"
                              x-text="sessionCode">
                        </span>
                    </template>
                </div>
                <p class="text-[9px] sm:text-[11px] text-slate-500 dark:text-slate-400 font-medium flex items-center gap-1 mt-0.5 truncate">
                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse shrink-0"></span>
                    <span class="truncate" x-text="isCsMode ? 'Terhubung dengan Admin Pengelola via Telegram' : 'Online • Bantuan Otomatis & CS'"></span>
                </p>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex items-center gap-1 sm:gap-2 shrink-0">
            {{-- Tombol Beralih ke CS Langsung jika belum aktif --}}
            <template x-if="!isCsMode">
                <button type="button" 
                        @click="showCsConfirmModal = true" 
                        title="Hubungi Customer Service Langsung"
                        class="inline-flex items-center gap-1 sm:gap-1.5 px-2.5 py-1.5 sm:px-3 sm:py-1.5 rounded-xl border border-indigo-200 dark:border-indigo-800 hover:border-indigo-400 dark:hover:border-indigo-600 bg-indigo-50/70 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 text-[11px] sm:text-xs font-bold transition-all shadow-2xs active:scale-95">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-indigo-600 dark:text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <span>Hubungi CS</span>
                </button>
            </template>

            {{-- Tombol Akhiri Sesi CS jika mode CS aktif --}}
            <template x-if="isCsMode">
                <button type="button" 
                        @click="showCloseCsModal = true" 
                        title="Akhiri Sesi CS Langsung"
                        class="inline-flex items-center gap-1 sm:gap-1.5 px-2.5 py-1.5 sm:px-3 sm:py-1.5 rounded-xl border border-amber-200 dark:border-amber-800 hover:border-amber-300 dark:hover:border-amber-700 bg-amber-50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 text-[11px] sm:text-xs font-bold transition-all shadow-2xs active:scale-95">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-amber-600 dark:text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    <span>Tutup Sesi CS</span>
                </button>
            </template>
            
            <button type="button" 
                    @click="showClearModal = true" 
                    title="Bersihkan Percakapan"
                    class="h-7 w-7 sm:h-8.5 sm:w-8.5 flex items-center justify-center text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-xl border border-slate-200 dark:border-slate-800 transition-colors shadow-2xs">
                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M3 6h18m-2 0v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6m3 0V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                </svg>
            </button>
        </div>
    </header>

    {{-- 2. MAIN SPLIT VIEW (CHAT + SIDE PANEL ON DESKTOP) --}}
    <div class="flex-1 flex min-h-0 overflow-hidden w-full max-w-full">
        
        {{-- KOLOM UTAMA: PERCAKAPAN CHAT --}}
        <div class="flex-1 flex flex-col min-h-0 bg-slate-50 dark:bg-slate-950 relative w-full max-w-full min-w-0 overflow-hidden">
            
            {{-- Banner Status CS Mode --}}
            <template x-if="isCsMode">
                <div class="bg-indigo-600 text-white px-3 sm:px-6 py-1.5 sm:py-2 text-[11px] sm:text-xs font-semibold flex items-center justify-between gap-2 shadow-xs shrink-0 z-10">
                    <div class="flex items-center gap-2 truncate">
                        <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse shrink-0"></span>
                        <span class="truncate">Mode Customer Service Aktif • Pesan Anda terhubung langsung dengan Admin Pengelola.</span>
                    </div>
                    <button type="button" @click="showCloseCsModal = true" class="underline shrink-0 text-white/90 hover:text-white font-bold ml-2">
                        Kembali ke Bot
                    </button>
                </div>
            </template>

            {{-- Scrollable Conversation Stream --}}
            <div id="chatMessages" 
                 @scroll="handleScroll()"
                 class="flex-1 overflow-y-auto px-3 sm:px-6 lg:px-8 py-4 sm:py-6 space-y-4 sm:space-y-5 scroll-smooth min-h-0 overscroll-contain w-full max-w-full min-w-0">
                
                <div class="w-full space-y-4 sm:space-y-5 pb-4 min-w-0">
                    
                    {{-- Message Bubbles List --}}
                    <template x-for="(msg, index) in messages" :key="index">
                        <div>
                            {{-- Pesan Tipe System --}}
                            <template x-if="msg.sender === 'system'">
                                <div class="flex justify-center my-2 w-full animate-fade-in">
                                    <div class="bg-slate-200/80 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 rounded-xl px-3.5 py-1.5 text-[11px] sm:text-xs text-center max-w-[90%] sm:max-w-[75%] font-medium">
                                        <span x-html="msg.text"></span>
                                    </div>
                                </div>
                            </template>

                            {{-- Pesan Tipe User / Bot / Admin --}}
                            <template x-if="msg.sender !== 'system'">
                                <div :class="msg.sender === 'user' ? 'flex items-start justify-end gap-2 sm:gap-2.5 animate-fade-in w-full min-w-0' : 'flex items-start justify-start gap-2 sm:gap-2.5 animate-fade-in w-full min-w-0'">
                                    
                                    {{-- Avatar Pengirim Bot / Admin CS --}}
                                    <template x-if="msg.sender === 'bot' || msg.sender === 'admin'">
                                        <div :class="msg.sender === 'admin' ? 'bg-indigo-600 text-white border-indigo-700' : 'bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-300 dark:border-slate-700'"
                                             class="w-7 h-7 sm:w-8 sm:h-8 rounded-full flex items-center justify-center shrink-0 border shadow-2xs mt-0.5">
                                            <template x-if="msg.sender === 'admin'">
                                                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                </svg>
                                            </template>
                                            <template x-if="msg.sender === 'bot'">
                                                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                    <path d="M12 2a2 2 0 0 1 2 2v2a2 2 0 0 1-2 2 2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z"/>
                                                    <rect x="3" y="8" width="18" height="12" rx="3"/>
                                                    <circle cx="9" cy="13" r="1.5" fill="currentColor"/>
                                                    <circle cx="15" cy="13" r="1.5" fill="currentColor"/>
                                                    <path d="M9 17h6"/>
                                                </svg>
                                            </template>
                                        </div>
                                    </template>

                                    {{-- Konten Bubble Pesan --}}
                                    <div :class="msg.sender === 'user' 
                                         ? 'flex flex-col items-end min-w-0 max-w-[85%] sm:max-w-[78%] lg:max-w-[72%]' 
                                         : 'flex flex-col items-start min-w-0 max-w-[88%] sm:max-w-[82%] lg:max-w-[78%]'">
                                        
                                        {{-- Header Nama & Waktu di ATAS Bubble --}}
                                        <div :class="msg.sender === 'user' ? 'text-[9.5px] sm:text-[10.5px] text-slate-400 dark:text-slate-500 mb-1 mr-1.5 flex items-center gap-1.5 justify-end' : 'text-[9.5px] sm:text-[10.5px] mb-1 ml-1.5 flex items-center gap-1.5 justify-start'">
                                            <template x-if="msg.sender === 'admin'">
                                                <span class="text-indigo-600 dark:text-indigo-400 font-bold text-[10px] sm:text-xs">Admin Pengelola Lab</span>
                                            </template>
                                            <template x-if="msg.sender === 'bot'">
                                                <span class="text-slate-700 dark:text-slate-300 font-bold text-[10px] sm:text-xs">Asisten Lab Virtual</span>
                                            </template>
                                            <template x-if="msg.sender === 'user'">
                                                <span class="text-slate-500 dark:text-slate-400 font-semibold">Anda</span>
                                            </template>
                                            <span class="text-slate-400 dark:text-slate-500 font-normal" x-text="msg.time"></span>
                                        </div>

                                        {{-- Bubble Pesan --}}
                                        <div :class="msg.sender === 'user' 
                                             ? 'bg-blue-600 text-white rounded-[1.35rem] px-4 py-2.5 sm:px-5 sm:py-3 shadow-xs text-xs sm:text-sm leading-relaxed chat-bubble-content break-words [overflow-wrap:anywhere] max-w-full font-normal' 
                                             : (msg.sender === 'admin' 
                                                ? 'bg-indigo-50 dark:bg-indigo-950/60 border border-indigo-200 dark:border-indigo-800/80 text-slate-900 dark:text-slate-100 rounded-[1.35rem] px-4 py-2.5 sm:px-5 sm:py-3 shadow-2xs text-xs sm:text-sm leading-relaxed chat-bubble-content break-words [overflow-wrap:anywhere] max-w-full font-normal'
                                                : 'bg-slate-200/90 dark:bg-slate-800 border border-slate-300/40 dark:border-slate-700/60 text-slate-900 dark:text-slate-100 rounded-[1.35rem] px-4 py-2.5 sm:px-5 sm:py-3 shadow-2xs text-xs sm:text-sm leading-relaxed chat-bubble-content break-words [overflow-wrap:anywhere] max-w-full font-normal')">
                                            
                                            <div class="break-words [overflow-wrap:anywhere]" x-html="msg.text"></div>
                                            
                                            {{-- Smart Suggestions Chips di Bawah Pesan Bot --}}
                                            <template x-if="msg.sender === 'bot' && msg.suggestions && msg.suggestions.length > 0">
                                                <div class="mt-2.5 pt-2.5 border-t border-slate-300/60 dark:border-slate-700/70 flex flex-wrap gap-1.5 sm:gap-2">
                                                    <template x-for="(sug, sIndex) in msg.suggestions" :key="sIndex">
                                                        <button type="button" 
                                                                @click="handleSuggestionClick(sug)" 
                                                                class="inline-flex items-center gap-1 px-2.5 py-1 sm:px-3 sm:py-1.5 rounded-full bg-white dark:bg-slate-900 hover:bg-sky-50 dark:hover:bg-sky-950/50 hover:text-sky-700 dark:hover:text-sky-300 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-700 text-[10.5px] sm:text-xs font-semibold transition-all active:scale-95 shadow-2xs max-w-full">
                                                            <svg class="w-2.5 h-2.5 text-slate-400 dark:text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                                            </svg>
                                                            <span class="truncate" x-text="sug.text"></span>
                                                        </button>
                                                    </template>
                                                </div>
                                            </template>
                                        </div>

                                        {{-- Status Terkirim / Gagal di Bawah Bubble --}}
                                        <template x-if="msg.sender === 'user'">
                                            <div class="text-[9px] sm:text-[10px] text-slate-400 dark:text-slate-500 mt-1 mr-1.5 flex items-center gap-1">
                                                <span>Terkirim</span>
                                                <svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                            </div>
                                        </template>

                                        {{-- Retry Jika Gagal --}}
                                        <template x-if="msg.failed">
                                            <div class="mt-1 text-[11px] sm:text-xs text-rose-600 dark:text-rose-400 font-medium">
                                                Gagal terkirim. <button type="button" @click="retryMessage(msg)" class="underline font-bold">Coba Lagi</button>
                                            </div>
                                        </template>
                                    </div>

                                    {{-- Foto Profil User (Kanan Pesan User) --}}
                                    <template x-if="msg.sender === 'user'">
                                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full overflow-hidden shrink-0 border border-slate-200 dark:border-slate-800 shadow-2xs mt-0.5 bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                                            @if(Auth::user() && Auth::user()->foto)
                                                <img src="{{ asset('storage/' . Auth::user()->foto) }}" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover">
                                            @else
                                                <span class="text-[10px] sm:text-xs font-bold text-slate-700 dark:text-slate-200">{{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}</span>
                                            @endif
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </template>

                    {{-- Typing Indicator (Dinamis: Bot Otomatis vs Customer Service Admin) --}}
                    <div x-show="isTyping || isCsTyping" class="flex items-start justify-start gap-2 sm:gap-2.5 animate-fade-in w-full min-w-0" style="display: none;">
                        <template x-if="isCsMode || isCsTyping">
                            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-indigo-600 text-white flex items-center justify-center shrink-0 border border-indigo-700 shadow-2xs mt-0.5">
                                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                        </template>
                        <template x-if="!isCsMode && !isCsTyping">
                            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center shrink-0 border border-slate-300 dark:border-slate-700 shadow-2xs mt-0.5">
                                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M12 2a2 2 0 0 1 2 2v2a2 2 0 0 1-2 2 2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z"/>
                                    <rect x="3" y="8" width="18" height="12" rx="3"/>
                                    <circle cx="9" cy="13" r="1.5" fill="currentColor"/>
                                    <circle cx="15" cy="13" r="1.5" fill="currentColor"/>
                                    <path d="M9 17h6"/>
                                </svg>
                            </div>
                        </template>
                        <div :class="isCsMode || isCsTyping ? 'bg-indigo-50/90 dark:bg-indigo-950/60 border border-indigo-200 dark:border-indigo-800/80 text-indigo-900 dark:text-indigo-200' : 'bg-slate-200/90 dark:bg-slate-800 border border-slate-300/40 dark:border-slate-700/60 text-slate-700 dark:text-slate-300'"
                             class="rounded-[1.35rem] px-4 py-2.5 sm:px-5 sm:py-3 shadow-2xs flex items-center gap-1.5">
                            <span :class="isCsMode || isCsTyping ? 'bg-indigo-500' : 'bg-sky-500'" class="w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full animate-pulse"></span>
                            <span :class="isCsMode || isCsTyping ? 'bg-indigo-500' : 'bg-sky-500'" class="w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full animate-pulse" style="animation-delay:200ms"></span>
                            <span :class="isCsMode || isCsTyping ? 'bg-indigo-500' : 'bg-sky-500'" class="w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full animate-pulse" style="animation-delay:400ms"></span>
                            <span class="text-[11px] sm:text-xs ml-1.5 font-medium" x-text="isCsMode || isCsTyping ? 'Customer Service sedang mengetik balasan...' : 'Asisten sedang menyusun jawaban...'"></span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Floating Scroll to Bottom Button --}}
            <div x-show="showScrollBottomButton" class="absolute bottom-28 inset-x-0 flex justify-center pointer-events-none z-30" style="display: none;">
                <button type="button" 
                        @click="scrollToBottom(true)"
                        class="pointer-events-auto bg-slate-900/90 dark:bg-slate-800/90 hover:bg-slate-900 dark:hover:bg-slate-700 text-white text-xs font-semibold px-3.5 py-1.5 sm:px-4 sm:py-2 rounded-full shadow-lg border border-slate-700 dark:border-slate-600 flex items-center gap-1.5 transition-all active:scale-95">
                    <svg class="w-3.5 h-3.5 text-indigo-300 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                    <span>Gulir ke Pesan Terbaru</span>
                </button>
            </div>

            {{-- 3. COMPOSER FOOTER & TOPIC CHIPS --}}
            <footer class="bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 px-3 sm:px-6 lg:px-8 py-2.5 sm:py-3.5 shrink-0 z-20 pb-[max(0.75rem,env(safe-area-inset-bottom))] shadow-xs w-full max-w-full min-w-0 overflow-x-hidden">
                <div class="w-full space-y-2 sm:space-y-2.5 min-w-0">
                    
                    {{-- Carousel Pilihan Tombol Topik Cepat --}}
                    <div class="flex gap-1.5 sm:gap-2 overflow-x-auto no-scrollbar py-0.5 w-full min-w-0">
                        {{-- Tombol CS Langsung --}}
                        <template x-if="!isCsMode">
                            <button type="button" @click="showCsConfirmModal = true" class="shrink-0 inline-flex items-center gap-1 sm:gap-1.5 bg-indigo-50 dark:bg-indigo-950/60 hover:bg-indigo-100 dark:hover:bg-indigo-900/80 text-indigo-700 dark:text-indigo-300 text-[11px] sm:text-xs font-bold px-3 py-1 sm:px-3.5 sm:py-1.5 rounded-full border border-indigo-200 dark:border-indigo-800 transition-colors">
                                <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5 text-indigo-600 dark:text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                <span>Hubungi CS Langsung</span>
                            </button>
                        </template>

                        <button type="button" @click="sendQuickReply('pinjam')" class="shrink-0 inline-flex items-center gap-1 sm:gap-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 hover:text-indigo-600 dark:hover:text-indigo-400 text-slate-700 dark:text-slate-300 text-[11px] sm:text-xs font-semibold px-2.5 py-1 sm:px-3 sm:py-1.5 rounded-full border border-slate-200 dark:border-slate-700 transition-colors">
                            <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5 text-indigo-600 dark:text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            <span>Cara Pinjam</span>
                        </button>
                        <button type="button" @click="sendQuickReply('status')" class="shrink-0 inline-flex items-center gap-1 sm:gap-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 hover:text-indigo-600 dark:hover:text-indigo-400 text-slate-700 dark:text-slate-300 text-[11px] sm:text-xs font-semibold px-2.5 py-1 sm:px-3 sm:py-1.5 rounded-full border border-slate-200 dark:border-slate-700 transition-colors">
                            <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5 text-sky-600 dark:text-sky-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                            <span>Cek Status</span>
                        </button>
                        <button type="button" @click="sendQuickReply('kembali')" class="shrink-0 inline-flex items-center gap-1 sm:gap-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 hover:text-indigo-600 dark:hover:text-indigo-400 text-slate-700 dark:text-slate-300 text-[11px] sm:text-xs font-semibold px-2.5 py-1 sm:px-3 sm:py-1.5 rounded-full border border-slate-200 dark:border-slate-700 transition-colors">
                            <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            <span>Pengembalian</span>
                        </button>
                        <button type="button" @click="sendQuickReply('scan')" class="shrink-0 inline-flex items-center gap-1 sm:gap-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 hover:text-indigo-600 dark:hover:text-indigo-400 text-slate-700 dark:text-slate-300 text-[11px] sm:text-xs font-semibold px-2.5 py-1 sm:px-3 sm:py-1.5 rounded-full border border-slate-200 dark:border-slate-700 transition-colors">
                            <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5 text-purple-600 dark:text-purple-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                            <span>Scan QR</span>
                        </button>
                        <button type="button" @click="sendQuickReply('jam')" class="shrink-0 inline-flex items-center gap-1 sm:gap-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 hover:text-indigo-600 dark:hover:text-indigo-400 text-slate-700 dark:text-slate-300 text-[11px] sm:text-xs font-semibold px-2.5 py-1 sm:px-3 sm:py-1.5 rounded-full border border-slate-200 dark:border-slate-700 transition-colors">
                            <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5 text-amber-600 dark:text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Jam Buka</span>
                        </button>
                    </div>

                    {{-- Form Input Chat --}}
                    <form @submit.prevent="sendMessage()" class="flex items-end gap-1.5 sm:gap-2 w-full min-w-0">
                        <div class="flex-1 min-w-0 bg-slate-50 dark:bg-slate-800/90 rounded-2xl px-3.5 sm:px-4.5 py-2 sm:py-2.5 border border-slate-300 dark:border-slate-700 focus-within:border-indigo-600 dark:focus-within:border-indigo-500 focus-within:bg-white dark:focus-within:bg-slate-800 focus-within:ring-2 focus-within:ring-indigo-600/15 transition-all shadow-2xs">
                            <textarea x-ref="messageInput"
                                      x-model="inputText"
                                      @input="autoGrow()"
                                      @keydown="handleKeyDown($event)"
                                      :placeholder="isCsMode ? 'Tuliskan pesan langsung ke Customer Service...' : 'Tanyakan hal seputar peminjaman atau lab...'"
                                      rows="1"
                                      maxlength="500"
                                      class="w-full bg-transparent border-0 text-slate-900 dark:text-slate-100 text-xs sm:text-sm placeholder:text-[11px] sm:placeholder:text-xs placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-0 p-0 py-0.5 resize-none max-h-24 sm:max-h-28 min-h-[32px] sm:min-h-[38px] leading-relaxed"></textarea>
                        </div>
                        
                        <button type="submit"
                                :disabled="!inputText.trim() || isTyping"
                                aria-label="Kirim Pesan"
                                class="h-10 w-10 sm:h-11 sm:w-11 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-40 disabled:hover:bg-indigo-600 text-white rounded-2xl flex items-center justify-center transition-all shrink-0 focus:outline-none focus:ring-2 focus:ring-indigo-500 shadow-md shadow-indigo-600/20 active:scale-95">
                            <template x-if="!isTyping">
                                <svg class="w-4 h-4 sm:w-4.5 sm:h-4.5 -translate-y-px translate-x-px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/>
                                </svg>
                            </template>
                            <template x-if="isTyping">
                                <svg class="w-4 h-4 sm:w-4.5 sm:h-4.5 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </template>
                        </button>
                    </form>
                </div>
            </footer>
        </div>

        {{-- KOLOM KANAN: PUSAT INFORMASI & PANDUAN CEPAT (DESKTOP ONLY) --}}
        <aside class="hidden lg:flex flex-col w-84 xl:w-96 2xl:w-[26rem] bg-white dark:bg-slate-900 border-l border-slate-200 dark:border-slate-800 h-full min-h-0 help-sidebar-scroll overflow-y-auto p-4 sm:p-5 xl:p-6 space-y-5 xl:space-y-6 shrink-0">
            
            {{-- Panel CS Card --}}
            <div class="bg-indigo-50/70 dark:bg-indigo-950/30 border border-indigo-200 dark:border-indigo-800/60 rounded-2xl p-4 sm:p-4.5 space-y-3 shadow-2xs">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-indigo-600 text-white flex items-center justify-center shrink-0 shadow-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-slate-900 dark:text-white">Customer Service Langsung</h4>
                        <p class="text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400">Terhubung dengan Admin via Telegram</p>
                    </div>
                </div>
                <p class="text-[11.5px] text-slate-600 dark:text-slate-300 leading-relaxed">
                    Ajukan pertanyaan atau konsultasi langsung dengan pengelola lab tanpa keluar dari antarmuka web ini.
                </p>
                <template x-if="!isCsMode">
                    <button type="button" 
                            @click="showCsConfirmModal = true" 
                            class="w-full py-2 sm:py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold flex items-center justify-center gap-1.5 transition-colors shadow-xs active:scale-95">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <span>Mulai Chat CS</span>
                    </button>
                </template>
                <template x-if="isCsMode">
                    <button type="button" 
                            @click="showCloseCsModal = true" 
                            class="w-full py-2 sm:py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-bold flex items-center justify-center gap-1.5 transition-colors shadow-xs active:scale-95">
                        <span>Tutup Sesi CS</span>
                    </button>
                </template>
            </div>

            {{-- Panel 1: Status & Jam Operasional Lab --}}
            <div class="bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 sm:p-4.5 space-y-3 shadow-2xs">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white">Status Operasional</span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] sm:text-[11px] font-bold bg-emerald-100 dark:bg-emerald-950/50 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60 shrink-0">
                        <span class="w-1.5 h-1.5 bg-emerald-600 rounded-full animate-pulse"></span>
                        Buka
                    </span>
                </div>
                <div class="text-[11.5px] sm:text-xs text-slate-600 dark:text-slate-300 space-y-2 pt-1">
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-slate-500 dark:text-slate-400 font-medium shrink-0">Senin – Kamis</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-200 text-right whitespace-nowrap">07.00 – 16.00 WIB</span>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-slate-500 dark:text-slate-400 font-medium shrink-0">Jumat</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-200 text-right whitespace-nowrap">07.00 – 15.30 WIB</span>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-slate-500 dark:text-slate-400 font-medium shrink-0">Sabtu & Minggu</span>
                        <span class="font-semibold text-rose-600 dark:text-rose-400 text-right whitespace-nowrap">Libur</span>
                    </div>
                </div>
                <div class="pt-2.5 border-t border-slate-200/80 dark:border-slate-700/80 text-[10.5px] sm:text-[11px] text-slate-500 dark:text-slate-400 flex items-start gap-1.5 leading-relaxed">
                    <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span class="break-words">Gedung Praktikum Barat, Lt. 2 (R. 204 & 205)</span>
                </div>
            </div>

            {{-- Panel 2: Pertanyaan & Topik Sering Ditanyakan --}}
            <div class="space-y-3">
                <h3 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider">Topik Sering Ditanyakan</h3>
                <div class="space-y-2">
                    <button type="button" @click="sendQuickReply('pinjam')" class="w-full text-left p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 hover:bg-indigo-50/80 dark:hover:bg-indigo-950/40 border border-slate-200 dark:border-slate-800 transition-colors group">
                        <p class="text-xs font-bold text-slate-800 dark:text-slate-200 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">Bagaimana prosedur pinjam alat?</p>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Alur pengajuan dan pengambilan unit di lab.</p>
                    </button>
                    <button type="button" @click="sendQuickReply('status')" class="w-full text-left p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 hover:bg-indigo-50/80 dark:hover:bg-indigo-950/40 border border-slate-200 dark:border-slate-800 transition-colors group">
                        <p class="text-xs font-bold text-slate-800 dark:text-slate-200 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">Cara cek status persetujuan?</p>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Melihat daftar alat yang sedang aktif dipinjam.</p>
                    </button>
                    <button type="button" @click="sendQuickReply('kembali')" class="w-full text-left p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 hover:bg-indigo-50/80 dark:hover:bg-indigo-950/40 border border-slate-200 dark:border-slate-800 transition-colors group">
                        <p class="text-xs font-bold text-slate-800 dark:text-slate-200 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">Prosedur pengembalian alat?</p>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Tata cara pengembalian fisik dan verifikasi data.</p>
                    </button>
                </div>
            </div>
        </aside>
    </div>

    {{-- MODAL 1: KONFIRMASI HUBUNGI CUSTOMER SERVICE LANGSUNG --}}
    <div x-show="showCsConfirmModal" 
         x-cloak
         @keydown.escape.window="showCsConfirmModal = false"
         class="fixed inset-0 z-[999] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" 
         style="display: none;" 
         x-transition.opacity.duration.150ms>

        <div @click.away="showCsConfirmModal = false" 
             class="bg-white dark:bg-slate-900 rounded-2xl p-6 w-full max-w-sm sm:max-w-md shadow-xl border border-slate-200 dark:border-slate-800 text-center"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">

            <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 rounded-2xl flex items-center justify-center mx-auto mb-3.5 border border-indigo-100 dark:border-indigo-800/60">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
            </div>

            <h2 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white">Hubungi Customer Service Langsung?</h2>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">
                Apakah Anda ingin berkomunikasi langsung dengan Customer Service / Pengelola Lab? Pesan Anda akan langsung diteruskan ke bot pengelola lab dan admin dapat membalasnya seketika ke layar ini.
            </p>

            <div class="mt-6 flex gap-3">
                <button type="button" 
                        @click="showCsConfirmModal = false" 
                        class="flex-1 px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold rounded-xl text-xs sm:text-sm transition-colors">
                    Batal
                </button>
                <button type="button" 
                        @click="startCsLiveChat()" 
                        class="flex-1 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-xs sm:text-sm transition-colors shadow-xs active:scale-95">
                    Mulai Obrolan CS
                </button>
            </div>
        </div>
    </div>

    {{-- MODAL 2: KONFIRMASI TUTUP SESI CUSTOMER SERVICE --}}
    <div x-show="showCloseCsModal" 
         x-cloak
         @keydown.escape.window="showCloseCsModal = false"
         class="fixed inset-0 z-[999] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" 
         style="display: none;" 
         x-transition.opacity.duration.150ms>

        <div @click.away="showCloseCsModal = false" 
             class="bg-white dark:bg-slate-900 rounded-2xl p-6 w-full max-w-sm shadow-xl border border-slate-200 dark:border-slate-800 text-center"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">

            <div class="w-12 h-12 bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 rounded-2xl flex items-center justify-center mx-auto mb-3.5 border border-amber-100 dark:border-amber-800/60">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
            </div>

            <h2 class="text-base font-bold text-slate-900 dark:text-white">Akhiri Sesi CS Langsung?</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5 leading-relaxed">
                Sesi obrolan dengan Customer Service akan ditutup dan Anda akan kembali ke mode Asisten Lab Otomatis.
            </p>

            <div class="mt-5 flex gap-3">
                <button type="button" @click="showCloseCsModal = false" class="flex-1 px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold rounded-xl text-xs transition-colors">
                    Batal
                </button>
                <button type="button" @click="closeCsLiveChat()" class="flex-1 px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-xl text-xs transition-colors shadow-xs">
                    Tutup Sesi
                </button>
            </div>
        </div>
    </div>

    {{-- MODAL 3: BERSIHKAN RIWAYAT PERCAKAPAN --}}
    <div x-show="showClearModal" 
         x-cloak
         @keydown.escape.window="showClearModal = false"
         class="fixed inset-0 z-[999] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" 
         style="display: none;" 
         x-transition.opacity.duration.150ms>

        <div @click.away="showClearModal = false" 
             class="bg-white dark:bg-slate-900 rounded-2xl p-6 w-full max-w-sm shadow-xl border border-slate-200 dark:border-slate-800 text-center"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">

            <div class="w-12 h-12 bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 rounded-2xl flex items-center justify-center mx-auto mb-3.5 border border-rose-100 dark:border-rose-800/60">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>

            <h2 class="text-base font-bold text-slate-900 dark:text-white">Bersihkan Percakapan?</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5 leading-relaxed">Riwayat sesi tanya jawab saat ini akan dihapus dan dimulai ulang.</p>

            <div class="mt-5 flex gap-3">
                <button type="button" @click="showClearModal = false" class="flex-1 px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold rounded-xl text-xs transition-colors">
                    Batal
                </button>
                <button type="button" @click="confirmResetChat()" class="flex-1 px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl text-xs transition-colors shadow-xs">
                    Hapus Riwayat
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function labChatBot(serverCsSession = null) {
    return {
        messages: [],
        inputText: '',
        isTyping: false,
        isCsTyping: false,
        isCsMode: false,
        sessionCode: serverCsSession || null,
        lastMessageId: 0,
        pollingInterval: null,
        showCsConfirmModal: false,
        showCloseCsModal: false,
        showClearModal: false,
        showScrollBottomButton: false,
        csrfToken: '{{ csrf_token() }}',

        kbFallback: [
            {
                keywords: ['cara pinjam', 'pinjam', 'meminjam', 'minjem', 'sewa', 'ambil barang', 'alur pinjam', 'prosedur pinjam'],
                response: '<strong>Alur Lengkap Peminjaman Alat:</strong><br><ol><li>Masuk ke menu <a href="{{ route("katalog.index") }}">Katalog Alat & Pinjam</a> di sidebar.</li><li>Cari dan pilih alat praktikum yang berstatus <em>Tersedia</em>.</li><li>Klik tombol <strong>Pinjam</strong> dan isi formulir keperluan praktikum.</li><li>Tunggu persetujuan pengelola lab, lalu ambil alat di ruang teknisi lab.</li></ol>',
                suggestions: [
                    { text: 'Cek status pinjam', action: 'status' },
                    { text: 'Jam operasional lab', action: 'jam' },
                    { text: 'Hubungi Customer Service Langsung', action: 'cs_direct' }
                ]
            },
            {
                keywords: ['status', 'cek status', 'persetujuan', 'disetujui', 'pending', 'menunggu'],
                response: '<strong>Cara Cek Status Peminjaman:</strong><br>Buka menu <a href="{{ route("peminjaman.saya") }}">Peminjaman Saya</a>. Status Anda akan tertera: <em>Menunggu Persetujuan</em>, <em>Disetujui</em>, <em>Sedang Dipinjam</em>, atau <em>Selesai</em>.',
                suggestions: [
                    { text: 'Prosedur pengembalian', action: 'kembali' },
                    { text: 'Hubungi Customer Service Langsung', action: 'cs_direct' }
                ]
            },
            {
                keywords: ['kembali', 'mengembalikan', 'pengembalian', 'selesai pinjam'],
                response: '<strong>Prosedur Pengembalian Alat:</strong><br><ol><li>Bawa alat praktikum yang dipinjam ke laboratorium dalam kondisi lengkap.</li><li>Buka menu <a href="{{ route("peminjaman.saya") }}">Peminjaman Saya</a> dan klik <strong>Ajukan Pengembalian</strong>.</li><li>Teknisi/Admin akan memverifikasi fisik alat sebelum menutup status peminjaman.</li></ol>',
                suggestions: [
                    { text: 'Lapor kendala fisik', action: 'maintenance' }
                ]
            },
            {
                keywords: ['scan', 'qr', 'barcode', 'kamera'],
                response: '<strong>Fitur Scan QR Code:</strong><br>Gunakan menu <a href="{{ route("scan.qr") }}">Scan QR Code</a> untuk memindai stiker kode QR pada unit peralatan laboratorium. Anda dapat langsung melihat spesifikasi, riwayat, dan kondisi barang seketika.',
                suggestions: [
                    { text: 'Cara pinjam alat', action: 'pinjam' }
                ]
            },
            {
                keywords: ['jam', 'buka', 'tutup', 'operasional', 'jadwal'],
                response: '<strong>Jam Operasional Laboratorium TKJ:</strong><br><ul><li><strong>Senin – Kamis:</strong> 07.00 – 16.00 WIB</li><li><strong>Jumat:</strong> 07.00 – 15.30 WIB</li><li><strong>Sabtu & Minggu:</strong> Libur</li></ul>',
                suggestions: [
                    { text: 'Hubungi Customer Service Langsung', action: 'cs_direct' }
                ]
            },
            {
                keywords: ['maintenance', 'rusak', 'perbaikan', 'lapor', 'pecah', 'hilang', 'error'],
                response: '<strong>Pelaporan Kendala & Maintenance:</strong><br>Jika menemukan perangkat yang rusak atau bermasalah saat praktikum, segera laporkan ke guru pembimbing atau koordinator laboratorium agar dapat dicatat pada modul <strong>Maintenance</strong>.',
                suggestions: [
                    { text: 'Hubungi Customer Service Langsung', action: 'cs_direct' }
                ]
            },
            {
                keywords: ['mikrotik', 'router', 'cisco', 'switch', 'winbox', 'ip address', 'reset router'],
                response: '<strong>Panduan Perangkat Jaringan (MikroTik):</strong><br><ul><li><strong>Default IP MikroTik:</strong> <code>192.168.88.1</code> (User: <code>admin</code>, Pass: kosong).</li><li><strong>Akses Aplikasi:</strong> Buka software <em>Winbox</em> lalu scan MAC Address di tab Neighbors.</li><li><strong>Reset Routerboard:</strong> Tahan tombol reset 5 detik saat colok power hingga lampu ACT berkedip.</li></ul>',
                suggestions: [
                    { text: 'Standar kabel UTP', action: 'crimping' }
                ]
            },
            {
                keywords: ['crimping', 'kabel utp', 'straight', 'cross', 'rj45', 'urutan kabel'],
                response: '<strong>Standar Urutan Warna Kabel UTP (T568B):</strong><br><ol><li>Putih-Oranye</li><li>Oranye</li><li>Putih-Hijau</li><li>Biru</li><li>Putih-Biru</li><li>Hijau</li><li>Putih-Cokelat</li><li>Cokelat</li></ol><em>Gunakan kabel tester untuk memastikan pin 1–8 menyala berurutan.</em>',
                suggestions: [
                    { text: 'Panduan MikroTik', action: 'mikrotik' }
                ]
            },
            {
                keywords: ['fiber', 'fiber optic', 'splicer', 'cleaver', 'otdr', 'red laser', 'vfl'],
                response: '<strong>Peralatan Fiber Optic Lab TKJ:</strong><br><ul><li><strong>Fusion Splicer:</strong> Alat penyambung serat optik presisi.</li><li><strong>Fiber Cleaver:</strong> Pemotong serat kaca 90 derajat.</li><li><strong>Visual Fault Locator (VFL):</strong> Laser merah untuk mendeteksi kabel putus.</li><li><strong>Optical Power Meter (OPM):</strong> Pengukur redaman daya sinyal optik.</li></ul>',
                suggestions: [
                    { text: 'Pinjam alat praktikum', action: 'pinjam' }
                ]
            },
            {
                keywords: ['lokasi', 'alamat', 'gedung', 'lantai', 'ruang lab'],
                response: '<strong>Lokasi Laboratorium TKJ:</strong><br><ul><li><strong>Lab TKJ 1 (Jaringan & Routing):</strong> Gedung Praktikum Barat - Lantai 2 (Ruang 204).</li><li><strong>Lab TKJ 2 (Hardware & Fiber Optic):</strong> Gedung Praktikum Barat - Lantai 2 (Ruang 205).</li><li><strong>Ruang Server & Teknisi:</strong> Sebelah Lab TKJ 1.</li></ul>',
                suggestions: [
                    { text: 'Jam operasional lab', action: 'jam' }
                ]
            },
            {
                keywords: ['admin', 'cs', 'customer service', 'operator', 'petugas', 'teknisi', 'bantuan langsung'],
                response: '<strong>Layanan Customer Service Langsung:</strong><br>Anda dapat berkomunikasi dua arah secara langsung dengan Admin/Pengelola Laboratorium.',
                suggestions: [
                    { text: 'Hubungi Customer Service Langsung', action: 'cs_direct' }
                ]
            }
        ],

        init() {
            if (this.sessionCode) {
                this.isCsMode = true;
                this.startPolling();
            }

            try {
                const saved = sessionStorage.getItem('lab_chat_messages');
                const savedCs = sessionStorage.getItem('lab_chat_cs_session');

                if (savedCs && !this.sessionCode) {
                    this.sessionCode = savedCs;
                    this.isCsMode = true;
                    this.startPolling();
                }

                if (saved) {
                    const parsed = JSON.parse(saved);
                    this.messages = parsed.map(m => {
                        if (m.sender === 'bot' && m.text) {
                            m.text = this.cleanAndFormatMessage(m.text);
                        }
                        return m;
                    });
                } else {
                    this.sendWelcome();
                }
            } catch(e) {
                this.sendWelcome();
            }
            this.scrollToBottom();
        },

        persistMessages() {
            try {
                sessionStorage.setItem('lab_chat_messages', JSON.stringify(this.messages));
                if (this.sessionCode) {
                    sessionStorage.setItem('lab_chat_cs_session', this.sessionCode);
                } else {
                    sessionStorage.removeItem('lab_chat_cs_session');
                }
            } catch(e) {}
        },

        sendWelcome() {
            this.messages = [
                {
                    sender: 'bot',
                    text: 'Halo <strong>{{ Auth::user()->name ?? 'Pengguna' }}</strong>!<br><br>Selamat datang di <strong>Asisten Lab Virtual</strong>. Saya siap membantu Anda seputar prosedur peminjaman alat praktikum, inventaris, jadwal operasional, hingga panduan perangkat.<br><br>Jika Anda membutuhkan komunikasi dua arah langsung dengan pengelola lab, Anda juga dapat menekan opsi <strong>Hubungi CS Langsung</strong> di bawah.',
                    time: this.getTime(),
                    failed: false,
                    suggestions: [
                        { text: 'Hubungi Customer Service Langsung', action: 'cs_direct' },
                        { text: 'Cara Pinjam Alat', action: 'pinjam' },
                        { text: 'Cek Status Pinjam', action: 'status' },
                        { text: 'Prosedur Pengembalian', action: 'kembali' },
                        { text: 'Panduan Scan QR', action: 'scan' },
                        { text: 'Jadwal & Jam Buka', action: 'jam' }
                    ]
                }
            ];
            this.persistMessages();
            this.scrollToBottom();
        },

        getTime() {
            return new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        },

        autoGrow() {
            const el = this.$refs.messageInput;
            if (el) {
                el.style.height = 'auto';
                el.style.height = Math.min(el.scrollHeight, 112) + 'px';
            }
        },

        handleKeyDown(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.sendMessage();
            }
        },

        handleScroll() {
            const el = document.getElementById('chatMessages');
            if (!el) return;
            const distanceToBottom = el.scrollHeight - el.scrollTop - el.clientHeight;
            this.showScrollBottomButton = distanceToBottom > 120;
        },

        scrollToBottom(force = false) {
            this.$nextTick(() => {
                const el = document.getElementById('chatMessages');
                if (el) {
                    if (force || !this.showScrollBottomButton) {
                        el.scrollTop = el.scrollHeight;
                    }
                }
            });
        },

        handleSuggestionClick(sug) {
            if (sug.action === 'cs_direct') {
                this.showCsConfirmModal = true;
            } else {
                this.sendQuickReply(sug.action || sug.text);
            }
        },

        async startCsLiveChat() {
            this.showCsConfirmModal = false;
            this.isTyping = true;

            try {
                const res = await fetch('{{ route("bantuan.start-cs") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({})
                });

                const data = await res.json();
                this.isTyping = false;

                if (data.status === 'success' && data.session_code) {
                    this.sessionCode = data.session_code;
                    this.isCsMode = true;

                    this.messages.push({
                        sender: 'system',
                        text: '<strong>Sesi Customer Service Langsung Aktif (' + this.sessionCode + ')</strong><br>Pesan yang Anda ketik akan langsung diteruskan ke bot pengelola lab.',
                        time: this.getTime()
                    });

                    this.persistMessages();
                    this.scrollToBottom(true);
                    this.startPolling();
                }
            } catch (err) {
                this.isTyping = false;
                alert('Gagal memulai sesi Customer Service. Silakan periksa koneksi internet Anda.');
            }
        },

        startPolling() {
            this.stopPolling();
            this.pollMessages();
            this.pollingInterval = setInterval(() => {
                if (this.isCsMode && this.sessionCode) {
                    this.pollMessages();
                }
            }, 2000);
        },

        stopPolling() {
            if (this.pollingInterval) {
                clearInterval(this.pollingInterval);
                this.pollingInterval = null;
            }
        },

        async pollMessages() {
            if (!this.sessionCode) return;

            try {
                const url = '{{ route("bantuan.poll") }}?session_code=' + encodeURIComponent(this.sessionCode) + '&last_id=' + this.lastMessageId;
                const res = await fetch(url, { credentials: 'same-origin' });
                if (!res.ok) return;

                const data = await res.json();
                if (data && data.messages && data.messages.length > 0) {
                    let hasNew = false;
                    data.messages.forEach(m => {
                        if (m.id > this.lastMessageId) {
                            this.lastMessageId = m.id;
                            this.messages.push({
                                sender: m.sender,
                                text: m.text,
                                time: m.time || this.getTime(),
                                failed: false
                            });
                            hasNew = true;

                            // Jika admin membalas, sembunyikan animasi typing
                            if (m.sender === 'admin') {
                                this.isCsTyping = false;
                            }
                        }
                    });

                    if (hasNew) {
                        this.persistMessages();
                        this.scrollToBottom(true);
                    }
                }

                // Sinkronisasi status sedang mengetik dari CS
                if (data && typeof data.is_typing !== 'undefined') {
                    this.isCsTyping = Boolean(data.is_typing);
                }

                if (data && data.status === 'closed' && this.isCsMode) {
                    this.isCsMode = false;
                    this.isCsTyping = false;
                    this.sessionCode = null;
                    this.stopPolling();
                    this.persistMessages();
                }
            } catch (e) {}
        },

        async closeCsLiveChat() {
            this.showCloseCsModal = false;
            const code = this.sessionCode;
            this.isCsMode = false;
            this.isCsTyping = false;
            this.sessionCode = null;
            this.stopPolling();

            if (code) {
                try {
                    await fetch('{{ route("bantuan.close-cs") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken
                        },
                        body: JSON.stringify({ session_code: code })
                    });
                } catch (e) {}
            }

            this.messages.push({
                sender: 'system',
                text: 'Sesi Customer Service telah ditutup. Anda telah kembali ke mode Asisten Lab Otomatis.',
                time: this.getTime()
            });

            this.persistMessages();
            this.scrollToBottom(true);
        },

        findFallbackAnswer(query, exactOnly = false) {
            const q = query.toLowerCase().trim();
            for (const item of this.kbFallback) {
                for (const kw of item.keywords) {
                    if (q.includes(kw)) {
                        return {
                            reply: item.response,
                            suggestions: item.suggestions || []
                        };
                    }
                }
            }
            if (exactOnly) return null;

            return {
                reply: 'Terima kasih atas pertanyaannya. Jika Anda membutuhkan bantuan langsung dari pengelola, silakan pilih menu <strong>Hubungi Customer Service Langsung</strong>.',
                suggestions: [
                    { text: 'Hubungi Customer Service Langsung', action: 'cs_direct' },
                    { text: 'Cara Pinjam Alat', action: 'pinjam' },
                    { text: 'Cek Status Pinjam', action: 'status' }
                ]
            };
        },

        async sendMessage(overrideText = null) {
            const text = (overrideText !== null ? overrideText : this.inputText).trim();
            if (!text || this.isTyping) return;

            if (overrideText === null) {
                this.inputText = '';
                if (this.$refs.messageInput) {
                    this.$refs.messageInput.style.height = 'auto';
                }
            }

            const userMsg = {
                sender: 'user',
                text: this.escapeHtml(text),
                rawText: text,
                time: this.getTime(),
                failed: false
            };

            this.messages.push(userMsg);
            this.persistMessages();
            this.scrollToBottom(true);

            // JIKA MODE CS AKTIF: Kirim langsung ke backend Laravel untuk diteruskan ke Telegram Bot
            if (this.isCsMode && this.sessionCode) {
                this.isTyping = false;
                this.isCsTyping = true;
                try {
                    const res = await fetch('{{ route("bantuan.send-cs") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            session_code: this.sessionCode,
                            message: text
                        })
                    });

                    const data = await res.json();

                    if (data.status === 'success' && data.message_id) {
                        this.lastMessageId = Math.max(this.lastMessageId, data.message_id);
                    } else if (data.status === 'error') {
                        userMsg.failed = true;
                        this.isCsTyping = false;
                    }
                } catch (err) {
                    this.isCsTyping = false;
                    userMsg.failed = true;
                }

                this.persistMessages();
                this.scrollToBottom(true);
                return;
            }

            // JIKA MODE BOT OTOMATIS:
            try {
                const localMatch = this.findFallbackAnswer(text, true);

                if (localMatch) {
                    this.isTyping = false;
                    this.messages.push({
                        sender: 'bot',
                        text: this.cleanAndFormatMessage(localMatch.reply),
                        time: this.getTime(),
                        failed: false,
                        suggestions: localMatch.suggestions || []
                    });
                } else {
                    const controller = new AbortController();
                    const timeoutId = setTimeout(() => controller.abort(), 7000);

                    const response = await fetch('https://bot-lab-tkj-production.up.railway.app/api/chat', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ message: text }),
                        signal: controller.signal
                    });

                    clearTimeout(timeoutId);

                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}`);
                    }

                    const data = await response.json();
                    this.isTyping = false;

                    const defaultFallback = this.findFallbackAnswer(text, false);
                    const rawReply = (data && data.reply) ? data.reply : defaultFallback.reply;
                    const formattedReply = this.cleanAndFormatMessage(rawReply);

                    this.messages.push({
                        sender: 'bot',
                        text: formattedReply,
                        time: this.getTime(),
                        failed: false,
                        suggestions: defaultFallback.suggestions || []
                    });
                }

            } catch (err) {
                this.isTyping = false;
                const fallbackData = this.findFallbackAnswer(text, false);
                this.messages.push({
                    sender: 'bot',
                    text: this.cleanAndFormatMessage(fallbackData.reply),
                    time: this.getTime(),
                    failed: false,
                    suggestions: fallbackData.suggestions || []
                });
            }

            this.persistMessages();
            this.scrollToBottom(true);
        },

        retryMessage(msg) {
            if (!msg || !msg.rawText) return;
            msg.failed = false;
            this.sendMessage(msg.rawText);
        },

        sendQuickReply(keyword) {
            if (keyword === 'cs_direct') {
                this.showCsConfirmModal = true;
                return;
            }

            const labelMap = {
                'pinjam': 'Bagaimana alur dan cara meminjam alat laboratorium?',
                'status': 'Bagaimana cara mengecek status persetujuan peminjaman?',
                'kembali': 'Bagaimana prosedur pengembalian alat praktikum?',
                'scan': 'Bagaimana cara melakukan scan QR Code alat?',
                'jam': 'Kapan jadwal dan jam operasional laboratorium?',
                'maintenance': 'Bagaimana cara melaporkan alat yang rusak?',
                'mikrotik': 'Bagaimana panduan akses router MikroTik dan kabel jaringan?',
                'crimping': 'Bagaimana standar urutan warna kabel UTP?',
                'fiber': 'Apa saja peralatan fiber optic di lab TKJ?',
                'lokasi': 'Dimana lokasi laboratorium TKJ?',
                'profil': 'Bagaimana cara mengubah profil dan sandi akun?',
                'admin': 'Saya butuh bantuan kontak customer service pengelola'
            };
            const textToSend = labelMap[keyword] || keyword;
            this.sendMessage(textToSend);
        },

        confirmResetChat() {
            this.showClearModal = false;
            this.stopPolling();
            this.isCsMode = false;
            this.isCsTyping = false;
            this.sessionCode = null;
            this.messages = [];
            this.persistMessages();
            this.sendWelcome();
        },

        cleanAndFormatMessage(text) {
            if (!text) return '';
            
            // 1. Strip raw Unicode emojis according to AGENTS.md Rule 4
            let cleaned = text
                .replace(/[\u{1F300}-\u{1F6FF}\u{1F900}-\u{1F9FF}\u{1FA70}-\u{1FAFF}\u{2600}-\u{26FF}\u{2700}-\u{27BF}\u{1F1E6}-\u{1F1FF}]/gu, '')
                .trim();

            // 2. Format common markdown-like bullet points or asterisks if returned as plain text
            cleaned = cleaned.replace(/^[\*\-]\s+(.+)$/gm, '<li class="ml-4 list-disc">$1</li>');
            cleaned = cleaned.replace(/^\d+\.\s+(.+)$/gm, '<li class="ml-4 list-decimal">$1</li>');

            return cleaned;
        },

        escapeHtml(str) {
            return str
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;')
                .replace(/\n/g, '<br>');
        }
    };
}
</script>
@endsection