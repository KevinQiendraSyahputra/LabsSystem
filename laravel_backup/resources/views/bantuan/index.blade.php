@extends('layouts.app')

@section('title', 'Asisten Lab Virtual — Winshark Community')
@section('page_title', 'Asisten Lab')
@section('page_subtitle', 'Pusat Informasi & Layanan Interaktif Laboratorium TKJ')

@push('styles')
<style>
    /* Scope khusus chat workspace: Mengunci view layout */
    html, body {
        height: 100% !important;
        overflow: hidden !important;
    }
    
    main {
        padding: 0 !important;
        margin: 0 !important;
        height: 100% !important;
        max-height: 100% !important;
        display: flex !important;
        flex-direction: column !important;
        overflow: hidden !important;
    }

    .chat-bubble-content p { margin-bottom: 0.35rem; }
    .chat-bubble-content p:last-child { margin-bottom: 0; }
    .chat-bubble-content ul { list-style-type: disc; padding-left: 1.15rem; margin-top: 0.2rem; margin-bottom: 0.35rem; }
    .chat-bubble-content ol { list-style-type: decimal; padding-left: 1.15rem; margin-top: 0.2rem; margin-bottom: 0.35rem; }
    .chat-bubble-content li { margin-bottom: 0.2rem; }
    .chat-bubble-content a { color: inherit; font-weight: 600; text-decoration: underline; text-underline-offset: 2px; }
    .chat-bubble-content code { background-color: rgba(0,0,0,0.06); padding: 0.1rem 0.3rem; border-radius: 0.25rem; font-family: monospace; font-size: 0.85em; }
    
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    
    #chatMessages::-webkit-scrollbar { width: 4px; }
    #chatMessages::-webkit-scrollbar-track { background: transparent; }
    #chatMessages::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(4px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in { animation: fadeIn 0.18s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
</style>
@endpush

@section('content')

<div x-data="labChatBot()"
     x-init="init()"
     class="flex flex-col w-full h-full min-h-0 bg-slate-50 relative select-text border-t sm:border-l border-slate-200">
    
    {{-- 1. ASSISTANT HEADER --}}
    <header class="bg-white border-b border-slate-200 px-3.5 sm:px-6 py-2.5 sm:py-3 flex items-center justify-between shrink-0 z-20">
        <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
            {{-- Mobile Sidebar Drawer Toggle Button --}}
            <button type="button" @click="sidebarOpen = true" class="lg:hidden p-1.5 rounded-xl text-slate-500 hover:bg-slate-100 hover:text-slate-800 transition focus:outline-none shrink-0" aria-label="Buka Menu Navigasi" title="Buka Menu">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            {{-- Avatar Bot Header --}}
            <div class="relative shrink-0">
                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-slate-900 text-indigo-400 flex items-center justify-center border border-slate-800 shadow-xs">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="10" rx="2"/>
                        <circle cx="12" cy="5" r="2"/>
                        <path d="M12 7v4"/>
                        <line x1="8" y1="16" x2="8" y2="16"/>
                        <line x1="16" y1="16" x2="16" y2="16"/>
                    </svg>
                </div>
                <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-emerald-500 border-2 border-white rounded-full"></span>
            </div>

            {{-- Info Asisten --}}
            <div class="min-w-0">
                <h1 class="text-xs sm:text-base font-bold text-slate-900 flex items-center gap-1.5 truncate">
                    <span>Asisten Lab Virtual</span>
                    <span class="hidden sm:inline-flex px-1.5 py-0.2 rounded text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                        {{ ucfirst(Auth::user()->role ?? 'Siswa') }}
                    </span>
                </h1>
                <p class="text-[10px] sm:text-xs text-slate-500 font-medium flex items-center gap-1 mt-0.5 truncate">
                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse shrink-0"></span>
                    <span class="truncate">Online · Siap membantu</span>
                </p>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex items-center gap-1.5 shrink-0 pl-2">
            <a href="https://wa.me/6287874589054?text=Halo+Admin+Lab+TKJ%2C+saya+butuh+bantuan." 
               target="_blank" 
               rel="noopener noreferrer"
               title="WhatsApp Admin"
               class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-slate-200 hover:border-emerald-300 hover:bg-emerald-50 text-slate-700 hover:text-emerald-700 text-xs font-semibold transition-colors">
                <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                <span class="hidden sm:inline">WhatsApp</span>
            </a>
            
            <button type="button" 
                    @click="showClearModal = true" 
                    title="Hapus Percakapan"
                    class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg border border-slate-200 transition-colors">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 6h18m-2 0v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6m3 0V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                </svg>
            </button>
        </div>
    </header>

    {{-- 2. SCROLLABLE CONVERSATION AREA --}}
    <div id="chatMessages" 
         @scroll="handleScroll()"
         class="flex-1 overflow-y-auto px-3 sm:px-6 py-4 space-y-4 scroll-smooth min-h-0 overscroll-contain">
        
        <div class="max-w-3xl mx-auto space-y-4 pb-2">
            
            {{-- Message Bubbles List --}}
            <template x-for="(msg, index) in messages" :key="index">
                <div :class="msg.sender === 'user' ? 'flex items-end justify-end gap-2 animate-fade-in' : 'flex items-end justify-start gap-2 animate-fade-in'">
                    
                    {{-- Avatar Bot (Kiri Pesan Bot) --}}
                    <template x-if="msg.sender === 'bot'">
                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-slate-900 text-indigo-400 flex items-center justify-center shrink-0 border border-slate-700 shadow-2xs mb-0.5">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="10" rx="2"/><circle cx="12" cy="5" r="2"/><path d="M12 7v4"/>
                            </svg>
                        </div>
                    </template>

                    {{-- Konten Bubble Pesan --}}
                    <div :class="msg.sender === 'user' 
                         ? 'flex flex-col items-end max-w-[82%] sm:max-w-[70%]' 
                         : 'flex flex-col items-start max-w-[85%] sm:max-w-[78%]'">
                        
                        <div :class="msg.sender === 'user' 
                             ? 'bg-indigo-600 text-white rounded-2xl rounded-br-xs px-3.5 py-2.5 shadow-2xs text-xs sm:text-sm leading-relaxed chat-bubble-content' 
                             : 'bg-white border border-slate-200 text-slate-800 rounded-2xl rounded-bl-xs px-3.5 py-2.5 shadow-2xs text-xs sm:text-sm leading-relaxed chat-bubble-content'">
                            
                            <div class="break-words" x-html="msg.text"></div>
                            
                            {{-- Smart Suggestions Chips --}}
                            <template x-if="msg.sender === 'bot' && msg.suggestions && msg.suggestions.length > 0">
                                <div class="mt-2.5 pt-2 border-t border-slate-100 flex flex-wrap gap-1.5">
                                    <template x-for="(sug, sIndex) in msg.suggestions" :key="sIndex">
                                        <button type="button" @click="sendQuickReply(sug.action || sug.text)" class="inline-flex items-center px-2 py-1 rounded-md bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200 text-[10px] font-semibold transition-colors">
                                            <span x-text="sug.text"></span>
                                        </button>
                                    </template>
                                </div>
                            </template>
                        </div>

                        {{-- Metadata Timestamp --}}
                        <div :class="msg.sender === 'user' ? 'text-[10px] text-slate-400 mt-1 mr-1 flex items-center gap-1' : 'text-[10px] text-slate-400 mt-1 ml-1'">
                            <span x-text="msg.time"></span>
                            <template x-if="msg.sender === 'user'">
                                <span class="flex items-center gap-0.5">
                                    <span>· Terkirim</span>
                                    <svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                </span>
                            </template>
                            <template x-if="msg.sender === 'bot'">
                                <span>· Asisten Lab</span>
                            </template>
                        </div>

                        {{-- Retry Jika Gagal --}}
                        <template x-if="msg.failed">
                            <div class="mt-1 text-xs text-rose-600 font-medium">
                                Gagal terkirim. <button type="button" @click="retryMessage(msg)" class="underline font-bold">Coba Lagi</button>
                            </div>
                        </template>
                    </div>

                    {{-- Foto Profil User (Kanan Pesan User - Style WhatsApp) --}}
                    <template x-if="msg.sender === 'user'">
                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full overflow-hidden shrink-0 border border-indigo-200 shadow-2xs mb-0.5 bg-indigo-100 flex items-center justify-center">
                            @if(Auth::user() && Auth::user()->foto)
                                <img src="{{ asset('storage/' . Auth::user()->foto) }}" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover">
                            @else
                                <span class="text-[11px] font-bold text-indigo-700">{{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}</span>
                            @endif
                        </div>
                    </template>
                </div>
            </template>

            {{-- Typing Indicator --}}
            <div x-show="isTyping" class="flex items-end justify-start gap-2 animate-fade-in" style="display: none;">
                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-slate-900 text-indigo-400 flex items-center justify-center shrink-0 border border-slate-700 shadow-2xs mb-0.5">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="10" rx="2"/><circle cx="12" cy="5" r="2"/><path d="M12 7v4"/>
                    </svg>
                </div>
                <div class="bg-white border border-slate-200 rounded-2xl rounded-bl-xs px-3.5 py-2.5 shadow-2xs flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-pulse"></span>
                    <span class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-pulse" style="animation-delay:200ms"></span>
                    <span class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-pulse" style="animation-delay:400ms"></span>
                    <span class="text-[11px] text-slate-400 ml-1">Mengetik...</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Floating Scroll to Bottom --}}
    <div x-show="showScrollBottomButton" class="absolute bottom-24 inset-x-0 flex justify-center pointer-events-none z-30" style="display: none;">
        <button type="button" 
                @click="scrollToBottom(true)"
                class="pointer-events-auto bg-slate-900/90 hover:bg-slate-900 text-white text-[11px] font-semibold px-3 py-1.5 rounded-full shadow-lg border border-slate-700 flex items-center gap-1 transition-all active:scale-95">
            <svg class="w-3 h-3 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
            <span>Pesan Terbaru</span>
        </button>
    </div>

    {{-- 3. COMPOSER FOOTER & TOPIC CHIPS --}}
    <footer class="bg-white border-t border-slate-200 px-3.5 sm:px-6 py-2 sm:py-2.5 shrink-0 z-20 pb-[max(0.6rem,env(safe-area-inset-bottom))]">
        <div class="max-w-3xl mx-auto space-y-2">
            
            {{-- Carousel Pilihan Tombol Topik Cepat --}}
            <div class="flex gap-1.5 overflow-x-auto no-scrollbar py-0.5">
                <button type="button" @click="sendQuickReply('pinjam')" class="shrink-0 bg-slate-100 hover:bg-slate-200 hover:text-indigo-600 text-slate-700 text-[11px] font-semibold px-2.5 py-1 rounded-full border border-slate-200/80 transition-colors">📦 Cara Pinjam</button>
                <button type="button" @click="sendQuickReply('status')" class="shrink-0 bg-slate-100 hover:bg-slate-200 hover:text-indigo-600 text-slate-700 text-[11px] font-semibold px-2.5 py-1 rounded-full border border-slate-200/80 transition-colors">📋 Cek Status</button>
                <button type="button" @click="sendQuickReply('kembali')" class="shrink-0 bg-slate-100 hover:bg-slate-200 hover:text-indigo-600 text-slate-700 text-[11px] font-semibold px-2.5 py-1 rounded-full border border-slate-200/80 transition-colors">🔄 Pengembalian</button>
                <button type="button" @click="sendQuickReply('scan')" class="shrink-0 bg-slate-100 hover:bg-slate-200 hover:text-indigo-600 text-slate-700 text-[11px] font-semibold px-2.5 py-1 rounded-full border border-slate-200/80 transition-colors">📷 Scan QR</button>
                <button type="button" @click="sendQuickReply('jam')" class="shrink-0 bg-slate-100 hover:bg-slate-200 hover:text-indigo-600 text-slate-700 text-[11px] font-semibold px-2.5 py-1 rounded-full border border-slate-200/80 transition-colors">⏰ Jam Buka</button>
                <button type="button" @click="sendQuickReply('maintenance')" class="shrink-0 bg-slate-100 hover:bg-slate-200 hover:text-indigo-600 text-slate-700 text-[11px] font-semibold px-2.5 py-1 rounded-full border border-slate-200/80 transition-colors">🛠️ Lapor Rusak</button>
                <button type="button" @click="sendQuickReply('mikrotik')" class="shrink-0 bg-slate-100 hover:bg-slate-200 hover:text-indigo-600 text-slate-700 text-[11px] font-semibold px-2.5 py-1 rounded-full border border-slate-200/80 transition-colors">🌐 MikroTik</button>
            </div>

            {{-- Form Input Chat --}}
            <form @submit.prevent="sendMessage()" class="flex items-end gap-2">
                <div class="flex-1 bg-slate-100 rounded-xl px-3 py-1 border border-slate-200/60 focus-within:border-indigo-500 focus-within:bg-white focus-within:ring-2 focus-within:ring-indigo-500/20 transition-all">
                    <textarea x-ref="messageInput"
                              x-model="inputText"
                              @input="autoGrow()"
                              @keydown="handleKeyDown($event)"
                              placeholder="Tanyakan sesuatu seputar lab..."
                              rows="1"
                              maxlength="400"
                              class="w-full bg-transparent border-0 text-slate-900 text-xs sm:text-sm placeholder-slate-400 focus:outline-none focus:ring-0 p-0 py-1.5 resize-none max-h-28 min-h-[36px] sm:min-h-[40px] leading-relaxed"></textarea>
                </div>
                
                <button type="submit"
                        :disabled="!inputText.trim() || isTyping"
                        class="h-10 w-10 sm:h-11 sm:w-11 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-40 disabled:hover:bg-indigo-600 text-white rounded-xl flex items-center justify-center transition-colors shrink-0 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <template x-if="!isTyping">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 -translate-y-px translate-x-px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/>
                        </svg>
                    </template>
                    <template x-if="isTyping">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </template>
                </button>
            </form>
        </div>
    </footer>

    {{-- MODAL HAPUS PERCAKAPAN --}}
    <template x-teleport="body">
        <div x-show="showClearModal" 
             class="fixed inset-0 z-[999] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" 
             style="display: none;"
             x-transition.opacity.duration.150ms>

            <div @click.away="showClearModal = false" 
                 class="bg-white rounded-2xl p-5 w-full max-w-xs shadow-xl border border-slate-200 text-center"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100">

                <div class="w-10 h-10 bg-rose-50 text-rose-600 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </div>

                <h2 class="text-sm font-bold text-slate-900">Hapus percakapan?</h2>
                <p class="text-xs text-slate-500 mt-1 leading-relaxed">Riwayat obrolan di sesi ini akan dibersihkan.</p>

                <div class="mt-4 flex gap-2">
                    <button type="button" @click="showClearModal = false" class="flex-1 px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-xs transition-colors">
                        Batal
                    </button>
                    <button type="button" @click="confirmResetChat()" class="flex-1 px-3 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl text-xs transition-colors">
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
function labChatBot() {
    return {
        messages: [],
        inputText: '',
        isTyping: false,
        showClearModal: false,
        showScrollBottomButton: false,

        kbFallback: [
            {
                keywords: ['cara pinjam', 'pinjam', 'meminjam', 'minjem', 'sewa', 'ambil barang', 'alur pinjam', 'prosedur pinjam'],
                response: '<strong>Alur Lengkap Peminjaman Alat:</strong><br><ol><li>Masuk ke menu <a href="{{ route("katalog.index") }}">Katalog Alat & Pinjam</a> di sidebar.</li><li>Cari dan pilih alat yang berstatus <em>Tersedia</em>.</li><li>Klik tombol <strong>Pinjam</strong> dan isi formulir keperluan praktikum.</li><li>Tunggu persetujuan pengelola lab, lalu ambil alat di ruang teknisi lab.</li></ol>',
                suggestions: [
                    { text: 'Cek status', action: 'status' },
                    { text: 'Jam operasional', action: 'jam' }
                ]
            },
            {
                keywords: ['status', 'cek status', 'persetujuan', 'disetujui', 'pending', 'menunggu'],
                response: '<strong>Cara Cek Status Peminjaman:</strong><br>Buka menu <a href="{{ route("peminjaman.saya") }}">Peminjaman Saya</a>. Status Anda akan tertera: <em>Menunggu Persetujuan</em>, <em>Disetujui</em>, <em>Sedang Dipinjam</em>, atau <em>Selesai</em>.',
                suggestions: [
                    { text: 'Pengembalian', action: 'kembali' }
                ]
            },
            {
                keywords: ['kembali', 'mengembalikan', 'pengembalian', 'selesai pinjam'],
                response: '<strong>Prosedur Pengembalian Alat:</strong><br><ol><li>Bawa alat praktikum yang dipinjam ke laboratorium dalam kondisi lengkap.</li><li>Buka menu <a href="{{ route("peminjaman.saya") }}">Peminjaman Saya</a> dan klik <strong>Ajukan Pengembalian</strong>.</li><li>Teknisi/Admin akan memverifikasi fisik alat sebelum menutup status peminjaman.</li></ol>',
                suggestions: [
                    { text: 'Lapor kendala', action: 'maintenance' }
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
                    { text: 'Kontak WhatsApp', action: 'admin' }
                ]
            },
            {
                keywords: ['maintenance', 'rusak', 'perbaikan', 'lapor', 'pecah', 'hilang', 'error'],
                response: '<strong>Pelaporan Kendala & Maintenance:</strong><br>Jika menemukan perangkat yang rusak atau bermasalah saat praktikum, segera laporkan ke guru pembimbing atau koordinator laboratorium agar dapat dicatat pada modul <strong>Maintenance</strong>.',
                suggestions: [
                    { text: 'Kontak WhatsApp', action: 'admin' }
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
                    { text: 'Pinjam alat', action: 'pinjam' }
                ]
            },
            {
                keywords: ['lokasi', 'alamat', 'gedung', 'lantai', 'ruang lab'],
                response: '<strong>Lokasi Laboratorium TKJ:</strong><br><ul><li><strong>Lab TKJ 1 (Jaringan & Routing):</strong> Gedung Praktikum Barat - Lantai 2 (Ruang 204).</li><li><strong>Lab TKJ 2 (Hardware & Fiber Optic):</strong> Gedung Praktikum Barat - Lantai 2 (Ruang 205).</li><li><strong>Ruang Server & Teknisi:</strong> Sebelah Lab TKJ 1.</li></ul>',
                suggestions: [
                    { text: 'Jam operasional', action: 'jam' }
                ]
            },
            {
                keywords: ['instagram', 'ig', 'sosmed', 'medsos', 'feed'],
                response: '<strong>Instagram Resmi Lab TKJ:</strong><br>Ikuti dokumentasi dan kegiatan kami di Instagram resmi: <a href="https://instagram.com/winshark_lab" target="_blank">@winshark_lab</a>',
                suggestions: [
                    { text: 'Kontak WhatsApp', action: 'admin' }
                ]
            },
            {
                keywords: ['admin', 'whatsapp', 'wa', 'kontak', 'hubungi', 'telepon'],
                response: '<strong>Kontak Pengelola Lab:</strong><br>Hubungi Admin via WhatsApp di <a href="https://wa.me/6287874589054?text=Halo+Admin+Lab+TKJ%2C+saya+butuh+bantuan." target="_blank">0878-7458-9054</a> atau temui langsung di Ruang Teknisi Lab TKJ.',
                suggestions: [
                    { text: 'Jam operasional', action: 'jam' }
                ]
            },
            {
                keywords: ['profil', 'sandi', 'password', 'foto', 'ubah profil'],
                response: '<strong>Pengaturan Akun & Profil:</strong><br>Anda dapat memperbarui foto profil, nama, dan kata sandi melalui halaman <a href="{{ route("profile.edit") }}">Profil Saya</a>.',
                suggestions: [
                    { text: 'Peminjaman saya', action: 'status' }
                ]
            }
        ],

        init() {
            try {
                const saved = sessionStorage.getItem('lab_chat_messages');
                if (saved) {
                    this.messages = JSON.parse(saved);
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
            } catch(e) {}
        },

        sendWelcome() {
            this.messages = [
                {
                    sender: 'bot',
                    text: 'Halo <strong>{{ Auth::user()->name ?? 'Pengguna' }}</strong>!<br><br>Saya Asisten Lab Virtual. Silakan tanyakan hal seputar peminjaman alat, ketersediaan inventaris, scan QR, atau pilih topik cepat di bawah.',
                    time: this.getTime(),
                    failed: false,
                    suggestions: [
                        { text: 'Cara Pinjam Alat', action: 'pinjam' },
                        { text: 'Cek Status Pinjam', action: 'status' },
                        { text: 'Panduan Scan QR', action: 'scan' }
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

        findFallbackAnswer(query) {
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
            return {
                reply: 'Terima kasih atas pertanyaannya. Jika Anda butuh bantuan mendesak, silakan hubungi <strong>WhatsApp Admin Lab di 0878-7458-9054</strong> atau pilih opsi topik cepat.',
                suggestions: [
                    { text: 'Cara Pinjam Alat', action: 'pinjam' },
                    { text: 'Cek Status', action: 'status' }
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
            this.isTyping = true;

            try {
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

                const fallbackData = this.findFallbackAnswer(text);
                this.messages.push({
                    sender: 'bot',
                    text: data.reply || fallbackData.reply,
                    time: this.getTime(),
                    failed: false,
                    suggestions: fallbackData.suggestions || []
                });

            } catch (err) {
                this.isTyping = false;
                const fallbackData = this.findFallbackAnswer(text);
                this.messages.push({
                    sender: 'bot',
                    text: fallbackData.reply,
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
            const labelMap = {
                'instagram': 'Bagaimana akun Instagram resmi Lab TKJ?',
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
                'admin': 'Saya butuh bantuan kontak WhatsApp admin pengelola'
            };
            const textToSend = labelMap[keyword] || keyword;
            this.sendMessage(textToSend);
        },

        confirmResetChat() {
            this.showClearModal = false;
            this.messages = [];
            this.persistMessages();
            this.sendWelcome();
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