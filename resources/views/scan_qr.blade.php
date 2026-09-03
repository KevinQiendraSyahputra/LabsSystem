@extends('layouts.app')

@section('title', 'Scan QR Code Alat Praktik')
@section('page_title', 'Quick Scanner')
@section('page_subtitle', 'Pindai stiker QR Code pada unit alat laboratorium TJKT')

@push('styles')
<style>
    /* Native Camera Video Styling */
    #cameraVideo {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
        display: block !important;
        background: #090d16;
    }
    @keyframes scanLineAnim {
        0% { top: 12%; opacity: 0; }
        20% { opacity: 1; }
        80% { opacity: 1; }
        100% { top: 88%; opacity: 0; }
    }
    .scan-laser-line {
        animation: scanLineAnim 2.2s ease-in-out infinite;
    }
</style>
@endpush

@section('content')
<div class="max-w-md mx-auto py-2 sm:py-4" x-data="{ alertMsg: '', alertType: 'info', showAlert: false }">

    {{-- Toast Notification --}}
    <div x-show="showAlert"
         x-transition:enter="transition ease-out duration-200 transform"
         x-transition:enter-start="-translate-y-2 opacity-0"
         x-transition:enter-end="translate-y-0 opacity-100"
         x-transition:leave="transition ease-in duration-150 transform"
         x-transition:leave-start="translate-y-0 opacity-100"
         x-transition:leave-end="-translate-y-2 opacity-0"
         class="fixed top-4 inset-x-4 max-w-sm mx-auto z-50 rounded-xl shadow-lg border p-3.5 flex items-start gap-3 bg-white"
         :class="alertType === 'error' ? 'border-rose-200 text-rose-900' : 'border-emerald-200 text-emerald-900'"
         style="display: none;">
        
        <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 mt-0.5"
             :class="alertType === 'error' ? 'bg-rose-100 text-rose-600' : 'bg-emerald-100 text-emerald-600'">
            <svg x-show="alertType === 'error'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <svg x-show="alertType !== 'error'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
        </div>

        <div class="flex-1 min-w-0">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500" x-text="alertType === 'error' ? 'Peringatan' : 'Berhasil'"></p>
            <p class="text-sm font-medium leading-snug mt-0.5" x-text="alertMsg"></p>
        </div>

        <button @click="showAlert = false" class="text-slate-400 hover:text-slate-600 p-1" aria-label="Tutup">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- Main Container --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-sm">
        
        {{-- Header --}}
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 border border-blue-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-base font-bold text-slate-900 leading-tight">Pindai QR Code Alat</h1>
                <p class="text-xs text-slate-500">Arahkan kamera ke stiker kode alat laboratorium</p>
            </div>
        </div>

        {{-- Warning HTTPS Box --}}
        <div id="httpsWarning" class="mt-4 p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-800 hidden">
            <div class="flex items-start gap-2.5">
                <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div class="flex-1">
                    <p class="font-bold">Browser Membutuhkan HTTPS</p>
                    <p class="mt-0.5 text-amber-700">Akses kamera browser memerlukan protokol aman (HTTPS). Silakan klik tombol di bawah.</p>
                    <button type="button" onclick="redirectToHttps()" class="mt-2 inline-flex items-center gap-1.5 px-3 py-1 bg-amber-600 hover:bg-amber-700 text-white rounded-lg font-bold text-xs shadow-xs transition">
                        <span>Pindah ke HTTPS</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Camera Viewport --}}
        <div class="relative w-full aspect-square max-w-[320px] mx-auto mt-5 rounded-2xl overflow-hidden bg-slate-950 flex items-center justify-center shadow-inner">
            
            {{-- Instant Native Video Element --}}
            <video id="cameraVideo" autoplay playsinline muted class="w-full h-full object-cover"></video>
            <canvas id="qrCanvas" class="hidden"></canvas>

            {{-- Viewfinder Reticle (Clean White Brackets) --}}
            <div class="absolute inset-0 pointer-events-none z-10 p-8 flex items-center justify-center">
                <div class="relative w-full h-full">
                    <div class="absolute top-0 left-0 w-6 h-6 border-t-2 border-l-2 border-white rounded-tl-md shadow-sm"></div>
                    <div class="absolute top-0 right-0 w-6 h-6 border-t-2 border-r-2 border-white rounded-tr-md shadow-sm"></div>
                    <div class="absolute bottom-0 left-0 w-6 h-6 border-b-2 border-l-2 border-white rounded-bl-md shadow-sm"></div>
                    <div class="absolute bottom-0 right-0 w-6 h-6 border-b-2 border-r-2 border-white rounded-br-md shadow-sm"></div>
                </div>
            </div>

            {{-- Scanning Laser Line --}}
            <div id="scanLaser" class="scan-laser-line absolute inset-x-8 h-0.5 bg-gradient-to-r from-transparent via-blue-400 to-transparent pointer-events-none z-10 hidden shadow-[0_0_8px_#38bdf8]"></div>

            {{-- Camera Switch Toggle --}}
            <button type="button" onclick="switchCameraMode()"
                    class="absolute top-2.5 right-2.5 z-20 w-8 h-8 rounded-lg bg-slate-900/75 text-white flex items-center justify-center hover:bg-slate-900 transition active:scale-95 shadow-md"
                    title="Ganti Kamera Depan/Belakang" aria-label="Ganti Kamera">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </button>
        </div>

        {{-- Status Feedback --}}
        <div id="scanResult" class="mt-3 text-xs text-slate-500 font-medium flex items-center justify-center gap-2 py-1 text-center min-h-[32px]">
            <svg class="w-3.5 h-3.5 animate-spin text-slate-500 shrink-0" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Membuka kamera...</span>
        </div>

        {{-- Action Buttons (Ambil Foto Langsung, Unggah Gambar Galeri, & Manual Input) --}}
        <div class="mt-4 pt-4 border-t border-slate-100 space-y-2.5">
            
            {{-- Direct Camera Snapshot (Buka Kamera Bawaan HP Langsung) --}}
            <div>
                <input type="file" id="cameraDirectInput" accept="image/*" capture="environment" class="hidden" onchange="scanQrFromImage(event)">
                <button type="button" onclick="document.getElementById('cameraDirectInput').click()" 
                        class="w-full flex items-center justify-center gap-2 bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold border border-blue-200 py-2.5 px-3 rounded-xl text-xs transition active:scale-98">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>Ambil Foto QR dengan Kamera HP</span>
                </button>
            </div>

            {{-- Upload File QR Image --}}
            <div>
                <input type="file" id="qrFileInput" accept="image/*" class="hidden" onchange="scanQrFromImage(event)">
                <button type="button" onclick="document.getElementById('qrFileInput').click()" 
                        class="w-full flex items-center justify-center gap-2 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold border border-slate-200/90 py-2 px-3 rounded-xl text-xs transition active:scale-98">
                    <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>Pilih dari Galeri Foto</span>
                </button>
            </div>

            {{-- Manual Code Input --}}
            <div class="pt-1">
                <label for="manualCode" class="block text-[11px] font-medium text-slate-600 mb-1">
                    Atau ketik kode unit secara manual:
                </label>
                <form onsubmit="event.preventDefault(); checkCodeManual();" class="flex gap-2">
                    <input type="text" id="manualCode" placeholder="Contoh: TKJ-ALT-001"
                           autocomplete="off" spellcheck="false"
                           class="flex-1 bg-white border border-slate-300 focus:border-blue-500 rounded-xl px-3 py-2 text-xs sm:text-sm font-mono uppercase focus:ring-1 focus:ring-blue-500 focus:outline-none transition">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-2 rounded-xl text-xs sm:text-sm transition active:scale-95 shadow-xs">
                        Cari
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

@push('scripts')
{{-- Bundled Local Zero-Latency Library --}}
<script src="{{ asset('js/jsQR.min.js') }}"></script>

<script>
    const resultBox = document.getElementById('scanResult');
    const video = document.getElementById('cameraVideo');
    const canvas = document.getElementById('qrCanvas');
    const ctx = canvas.getContext('2d', { willReadFrequently: true });

    let currentStream = null;
    let isScanning = true;
    let isProcessing = false;
    let currentFacingMode = "environment";
    let animationFrameId = null;
    let barcodeDetector = null;

    const icons = {
        success: `<svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>`,
        error: `<svg class="w-3.5 h-3.5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>`,
        warning: `<svg class="w-3.5 h-3.5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>`,
        loading: `<svg class="w-3.5 h-3.5 animate-spin text-slate-500 shrink-0" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>`
    };

    // Hardware accelerated Native BarcodeDetector (Chrome/Edge/Android)
    if ('BarcodeDetector' in window) {
        try {
            barcodeDetector = new BarcodeDetector({ formats: ['qr_code'] });
        } catch(e) {}
    }

    function setStatus(type, message) {
        if (!resultBox) return;
        if (type === 'loading') {
            resultBox.className = 'mt-3 text-xs text-slate-500 font-medium flex items-center justify-center gap-2 py-1 text-center min-h-[32px]';
            resultBox.innerHTML = `${icons.loading} <span>${message}</span>`;
        } else if (type === 'ready') {
            resultBox.className = 'mt-3 text-xs text-slate-500 font-medium flex items-center justify-center gap-2 py-1 text-center min-h-[32px]';
            resultBox.innerHTML = `<span>${message}</span>`;
        } else if (type === 'success') {
            resultBox.className = 'mt-3 text-xs text-emerald-700 font-medium flex items-center justify-center gap-2 py-1 text-center min-h-[32px]';
            resultBox.innerHTML = `${icons.success} <span>${message}</span>`;
        } else if (type === 'error') {
            resultBox.className = 'mt-3 text-xs text-rose-700 font-medium flex items-center justify-center gap-2 py-1 text-center min-h-[32px]';
            resultBox.innerHTML = `${icons.error} <span>${message}</span>`;
        } else if (type === 'warning') {
            resultBox.className = 'mt-3 text-xs text-amber-700 font-medium flex items-center justify-center gap-2 py-1 text-center min-h-[32px]';
            resultBox.innerHTML = `${icons.warning} <span>${message}</span>`;
        }
    }

    function triggerSlideToast(message, type = 'error') {
        const alpineEl = document.querySelector('[x-data]');
        if (alpineEl && alpineEl._x_dataStack) {
            const data = alpineEl._x_dataStack[0];
            data.alertMsg = message;
            data.alertType = type;
            data.showAlert = true;

            setTimeout(() => {
                data.showAlert = false;
            }, 4500);
        }
    }

    function redirectToHttps() {
        if (window.location.protocol !== 'https:') {
            window.location.href = 'https:' + window.location.href.substring(window.location.protocol.length);
        }
    }

    function stopCameraStream() {
        isScanning = false;
        if (animationFrameId) {
            cancelAnimationFrame(animationFrameId);
            animationFrameId = null;
        }
        if (currentStream) {
            try {
                currentStream.getTracks().forEach(track => {
                    track.stop();
                });
            } catch(e) {}
            currentStream = null;
        }
        if (video) {
            video.srcObject = null;
        }
        document.getElementById('scanLaser')?.classList.add('hidden');
    }

    window.stopQrCamera = stopCameraStream;

    function safelyRedirect(targetUrl) {
        stopCameraStream();
        setTimeout(() => {
            window.location.href = targetUrl;
        }, 150);
    }

    async function startInstantCamera(facingMode = "environment") {
        currentFacingMode = facingMode;
        stopCameraStream();

        // 1. Cek HTTPS
        const isLocal = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1' || window.location.hostname === '::1';
        if (window.location.protocol !== 'https:' && !isLocal) {
            const warningEl = document.getElementById('httpsWarning');
            if (warningEl) warningEl.classList.remove('hidden');
        }

        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            setStatus('warning', 'Akses kamera membutuhkan HTTPS atau browser tidak mendukung.');
            return;
        }

        setStatus('loading', 'Membuka sensor kamera...');

        // 2. Multi-tier progressive constraints
        const constraintTiers = [
            { video: { facingMode: { ideal: facingMode } }, audio: false },
            { video: { facingMode: facingMode }, audio: false },
            { video: true, audio: false }
        ];

        let streamObtained = null;
        let lastError = null;

        for (const constraint of constraintTiers) {
            try {
                streamObtained = await navigator.mediaDevices.getUserMedia(constraint);
                if (streamObtained) break;
            } catch (err) {
                lastError = err;
            }
        }

        if (streamObtained) {
            currentStream = streamObtained;
            attachAndPlayStream(currentStream);
        } else {
            handleCameraError(lastError);
        }
    }

    function attachAndPlayStream(stream) {
        video.muted = true;
        video.defaultMuted = true;
        video.playsInline = true;
        video.setAttribute('playsinline', 'true');
        video.setAttribute('webkit-playsinline', 'true');
        video.srcObject = stream;

        const onReady = () => {
            video.play().then(() => {
                isScanning = true;
                isProcessing = false;
                document.getElementById('scanLaser')?.classList.remove('hidden');
                setStatus('ready', 'Arahkan kamera ke QR Code unit');
                scanLoop();
            }).catch(e => {
                setStatus('warning', 'Sentuh kartu kamera untuk memulai live preview');
            });
        };

        if (video.readyState >= video.HAVE_METADATA) {
            onReady();
        } else {
            video.onloadedmetadata = onReady;
        }
    }

    async function scanLoop() {
        if (!isScanning) return;

        if (video.readyState === video.HAVE_ENOUGH_DATA) {
            // Jalur 1: Hardware BarcodeDetector (Chrome Native C++ Engine)
            if (barcodeDetector) {
                try {
                    const barcodes = await barcodeDetector.detect(video);
                    if (barcodes && barcodes.length > 0 && isScanning && !isProcessing) {
                        onScanSuccess(barcodes[0].rawValue);
                        return;
                    }
                } catch(e) {}
            }

            // Jalur 2: Local jsQR Engine (Fallback Presisi)
            if (window.jsQR && isScanning && !isProcessing) {
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                const code = jsQR(imageData.data, imageData.width, imageData.height, {
                    inversionAttempts: "dontInvert"
                });
                if (code && code.data && isScanning && !isProcessing) {
                    onScanSuccess(code.data);
                    return;
                }
            }
        }

        if (isScanning && !isProcessing) {
            animationFrameId = requestAnimationFrame(scanLoop);
        }
    }

    function handleCameraError(err) {
        const errorName = err ? (err.name || '') : '';
        const errorMsg = err ? (err.message || String(err)) : '';
        let hint = "Izin kamera belum aktif di sistem Android atau browser.";

        if (errorName === 'NotAllowedError' || errorName === 'PermissionDeniedError') {
            hint = "Akses ditolak. Pastikan izin kamera aktif di: <b>Setelan HP &gt; Aplikasi &gt; Chrome &gt; Izin &gt; Kamera (Izinkan)</b> serta di ikon gembok browser.";
        } else if (errorName === 'NotFoundError' || errorName === 'DevicesNotFoundError') {
            hint = "Kamera tidak ditemukan pada perangkat ini.";
        } else if (errorName === 'NotReadableError' || errorName === 'TrackStartError') {
            hint = "Kamera sedang digunakan aplikasi lain (WhatsApp/Instagram/Kamera). Silakan tutup aplikasi lain.";
        } else if (errorName === 'OverconstrainedError') {
            hint = "Format sensor kamera tidak kompatibel.";
        }

        if (resultBox) {
            resultBox.className = 'mt-3 text-xs text-amber-800 font-medium flex flex-col items-center justify-center gap-1.5 py-1 text-center';
            resultBox.innerHTML = `
                <div class="flex items-center gap-1.5 font-bold text-amber-900">
                    ${icons.warning} <span>Live Stream Kamera Terkendala</span>
                </div>
                <p class="text-[11px] text-slate-600 max-w-xs leading-relaxed">${hint}</p>
                <div class="flex flex-wrap items-center justify-center gap-2 mt-1">
                    <button type="button" onclick="startInstantCamera('${currentFacingMode}')" class="px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-[11px] font-bold transition active:scale-95 shadow-xs">
                        Coba Buka Live Kamera
                    </button>
                    <button type="button" onclick="document.getElementById('cameraDirectInput').click()" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-[11px] font-bold transition active:scale-95 shadow-xs">
                        📸 Ambil Foto QR Langsung
                    </button>
                </div>
                ${errorName ? `<p class="text-[10px] text-slate-400 font-mono mt-1">(${errorName})</p>` : ''}
            `;
        }
    }

    function switchCameraMode() {
        const nextFacing = (currentFacingMode === "environment") ? "user" : "environment";
        startInstantCamera(nextFacing);
    }

    function scanQrFromImage(event) {
        const file = event.target.files[0];
        if (!file) return;

        setStatus('loading', 'Membaca gambar QR Code...');

        const reader = new FileReader();
        reader.onload = function(e) {
            const img = new Image();
            img.onload = async function() {
                // 1. Coba BarcodeDetector
                if (barcodeDetector) {
                    try {
                        const barcodes = await barcodeDetector.detect(img);
                        if (barcodes && barcodes.length > 0) {
                            onScanSuccess(barcodes[0].rawValue);
                            return;
                        }
                    } catch(e) {}
                }

                // 2. Coba jsQR
                if (window.jsQR) {
                    canvas.width = img.width;
                    canvas.height = img.height;
                    ctx.drawImage(img, 0, 0, img.width, img.height);
                    const imageData = ctx.getImageData(0, 0, img.width, img.height);
                    const code = jsQR(imageData.data, imageData.width, imageData.height);
                    if (code && code.data) {
                        onScanSuccess(code.data);
                        return;
                    }
                }

                setStatus('error', 'QR Code tidak terdeteksi pada foto/gambar.');
                triggerSlideToast('Tidak dapat membaca QR Code pada gambar yang dipilih.', 'error');
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }

    function onScanSuccess(decodedText) {
        if (isProcessing) return;
        isProcessing = true;
        isScanning = false;

        setStatus('loading', `Memeriksa kode <code class="font-mono text-slate-900">${decodedText}</code>...`);

        fetch(`{{ url('/api/check-qr') }}?code=${encodeURIComponent(decodedText)}`)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    const targetUrl = data.redirect || `{{ url('/peminjaman/create') }}?kode_barang=${encodeURIComponent(decodedText)}`;
                    setStatus('success', `${data.barang?.nama_barang || 'Alat'} siap. Mengalihkan...`);
                    triggerSlideToast(data.message || 'Barang siap dipinjam', 'success');
                    setTimeout(() => { safelyRedirect(targetUrl); }, 500);

                } else if (data.status === 'unavailable') {
                    setStatus('error', data.message);
                    triggerSlideToast(data.message, 'error');
                    setTimeout(() => {
                        isProcessing = false;
                        isScanning = true;
                        scanLoop();
                    }, 3000);

                } else {
                    setStatus('warning', data.message || 'Alat tidak terdaftar.');
                    triggerSlideToast(data.message || 'Alat tidak terdaftar di sistem.', 'error');
                    setTimeout(() => {
                        isProcessing = false;
                        isScanning = true;
                        scanLoop();
                    }, 2500);
                }
            })
            .catch(() => {
                setStatus('error', 'Gagal memeriksa database.');
                triggerSlideToast('Terjadi gangguan jaringan saat verifikasi.', 'error');
                setTimeout(() => {
                    isProcessing = false;
                    isScanning = true;
                    scanLoop();
                }, 2500);
            });
    }

    function checkCodeManual() {
        const codeInput = document.getElementById('manualCode');
        const code = codeInput ? codeInput.value.trim() : '';
        if (code) {
            onScanSuccess(code);
        }
    }

    window.addEventListener('beforeunload', stopCameraStream);
    window.addEventListener('popstate', stopCameraStream);

    // Buka Kamera Seketika Saat Halaman Dimuat
    if (document.readyState === 'loading') {
        window.addEventListener('DOMContentLoaded', () => startInstantCamera("environment"));
    } else {
        startInstantCamera("environment");
    }
</script>
@endpush
@endsection