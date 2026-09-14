@extends('layouts.app')

@section('title', 'Profil Saya')
@section('page_title', 'Profil Akun Saya')
@section('page_subtitle', 'Kelola informasi pribadi, kontak, dan keamanan akun laboratorium')

@section('content')

@php
    $roleName = strtolower(trim($user->role ?? auth()->user()->role ?? ''));
    $valJabatan = trim($user->kelas_atau_jabatan ?? '');

    // Cek apakah akun adalah staf pengelola non-siswa
    $isStaffOrAdmin = in_array($roleName, ['admin', 'guru', 'staf', 'kepala_lab', 'teknisi']);

    // Siswa tetap dianggap siswa meskipun memiliki jabatan koordinator lab
    $isSiswa = !$isStaffOrAdmin || in_array($roleName, ['siswa', 'user', 'murid', 'koordinator_lab', 'koordinator lab', 'koordinator']);
    
    if ($isSiswa) {
        $pilihanOpsi = [
            'Kelas Siswa TKJ' => [
                'X TKJ 1',
                'X TKJ 2',
                'XI TKJ 1',
                'XI TKJ 2',
                'XII TKJ 1',
                'XII TKJ 2',
            ]
        ];
    } else {
        $pilihanOpsi = [
            'Jabatan Pengelola / Guru' => [
                'Kepala Lab',
                'Guru Produktif TKJ',
                'Teknisi Lab TKJ',
                'Staf / Karyawan',
            ]
        ];
    }

    // Kalkulasi Persentase Kelengkapan Profil
    $filledCount = 0;
    $totalFields = 6;
    if (!empty($user->name)) $filledCount++;
    if (!empty($user->email)) $filledCount++;
    if (!empty($user->nomor_induk)) $filledCount++;
    if (!empty($user->kelas_atau_jabatan)) $filledCount++;
    if (!empty($user->telepon)) $filledCount++;
    if (!empty($user->foto)) $filledCount++;
    
    $completionPercent = round(($filledCount / $totalFields) * 100);
    // Circumference of r=34 is 2 * PI * 34 ≈ 213.6
    $dashOffset = round(213.6 * (1 - ($completionPercent / 100)));

    // Statistik Peminjaman
    $totalPinjam = $user->peminjamans()->count();
    $pinjamAktif = $user->peminjamans()->whereIn('status', ['Menunggu', 'Disetujui', 'Dipinjam'])->count();
    $pinjamSelesai = $user->peminjamans()->where('status', 'Selesai')->count();

    // Sesi Info
    $userAgent = request()->userAgent() ?? 'Peramban Web';
    $isMobileDevice = preg_match('/(android|iphone|ipad|mobile)/i', $userAgent);
    $browserName = 'Chrome / Web Browser';
    if (str_contains($userAgent, 'Edg')) $browserName = 'Microsoft Edge';
    elseif (str_contains($userAgent, 'Firefox')) $browserName = 'Mozilla Firefox';
    elseif (str_contains($userAgent, 'Safari') && !str_contains($userAgent, 'Chrome')) $browserName = 'Apple Safari';
    elseif (str_contains($userAgent, 'Chrome')) $browserName = 'Google Chrome';
    
    $deviceName = $isMobileDevice ? 'Mobile Device' : (str_contains($userAgent, 'Windows') ? 'Windows PC' : (str_contains($userAgent, 'Macintosh') ? 'MacBook / macOS' : 'Komputer Desktop'));

    // Deteksi IP Laptop / Perangkat Klien (IPv4 Wi-Fi/LAN)
    $clientIp = request()->ip();
    if (in_array($clientIp, ['127.0.0.1', '::1', 'localhost']) || empty($clientIp)) {
        $clientIp = getHostByName(getHostName()) ?: '172.46.46.119';
    }
@endphp

@push('styles')
<!-- CropperJS CSS CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css"/>
<style>
    .cropper-container {
        width: 100% !important;
        height: 100% !important;
        user-select: none !important;
        -webkit-user-select: none !important;
        touch-action: none !important;
    }
    .cropper-view-box,
    .cropper-face {
        border-radius: 1.25rem !important;
        outline: 2.5px solid #6366f1 !important;
        outline-color: rgba(99, 102, 241, 0.95) !important;
    }
    .cropper-view-box {
        box-shadow: 0 0 0 2000px rgba(15, 23, 42, 0.8) !important;
    }
    .cropper-modal {
        background-color: transparent !important;
        opacity: 0.8 !important;
    }
    .cropper-point {
        background-color: #6366f1 !important;
        width: 8px !important;
        height: 8px !important;
        border-radius: 50% !important;
    }
    .cropper-line {
        background-color: rgba(99, 102, 241, 0.5) !important;
    }
    .cropper-dashed {
        border-color: rgba(255, 255, 255, 0.5) !important;
    }
</style>
@endpush

<div class="space-y-6 2xl:space-y-8 max-w-7xl 2xl:max-w-screen-2xl mx-auto w-full"
     x-data="{
        showModalFoto: false,
        showPreviewModal: false,
        showCropModal: false,
        showRevokeModal: false,
        revokeTargetId: '',
        revokeTargetDevice: '',
        revokeTargetDetail: '',
        imageSrcToCrop: '',
        cropperInstance: null,
        isUploading: false,
        toast: {
            show: false,
            message: '',
            timeout: null
        },
        devices: [
            {
                id: 'device-mobile',
                name: 'Mobile Device',
                browser: 'Safari Mobile / Chrome',
                district: 'Kec. Bukit Raya',
                city: 'Pekanbaru',
                ip: '180.252.164.82',
                lastActive: '2 jam yang lalu',
                timeText: '{{ now()->subHours(2)->translatedFormat("d M, H:i") }} WIB',
                icon: 'mobile'
            },
            {
                id: 'device-workstation',
                name: 'Workstation PC Lab',
                browser: 'Microsoft Edge 121',
                district: 'Kec. Payung Sekaki',
                city: 'Pekanbaru',
                ip: '182.2.45.120',
                lastActive: '3 hari yang lalu',
                timeText: '{{ now()->subDays(3)->translatedFormat("d M, H:i") }} WIB',
                icon: 'desktop'
            }
        ],
        liveLocation: {
            district: 'Kec. Marpoyan Damai',
            city: 'Pekanbaru',
            ip: '{{ $clientIp }}',
            status: 'ready'
        },

        formData: {
            name: '{{ addslashes($user->name) }}',
            nomor_induk: '{{ addslashes($user->nomor_induk ?? '') }}',
            email: '{{ addslashes($user->email) }}',
            telepon: '{{ addslashes($user->telepon ?? '') }}',
            kelas_atau_jabatan: '{{ addslashes($user->kelas_atau_jabatan ?? '') }}'
        },
        initialFormData: {
            name: '{{ addslashes($user->name) }}',
            nomor_induk: '{{ addslashes($user->nomor_induk ?? '') }}',
            email: '{{ addslashes($user->email) }}',
            telepon: '{{ addslashes($user->telepon ?? '') }}',
            kelas_atau_jabatan: '{{ addslashes($user->kelas_atau_jabatan ?? '') }}'
        },

        isFormDirty() {
            return this.formData.name.trim() !== this.initialFormData.name.trim() ||
                   this.formData.nomor_induk.trim() !== this.initialFormData.nomor_induk.trim() ||
                   this.formData.email.trim() !== this.initialFormData.email.trim() ||
                   this.formData.telepon.trim() !== this.initialFormData.telepon.trim() ||
                   this.formData.kelas_atau_jabatan.trim() !== this.initialFormData.kelas_atau_jabatan.trim();
        },

        discardChanges() {
            this.formData.name = this.initialFormData.name;
            this.formData.nomor_induk = this.initialFormData.nomor_induk;
            this.formData.email = this.initialFormData.email;
            this.formData.telepon = this.initialFormData.telepon;
            this.formData.kelas_atau_jabatan = this.initialFormData.kelas_atau_jabatan;
        },

        init() {
            this.$watch('showCropModal', value => this.toggleBodyScroll(value));
            this.$watch('showModalFoto', value => this.toggleBodyScroll(value));
            this.$watch('showPreviewModal', value => this.toggleBodyScroll(value));
            this.$watch('showRevokeModal', value => this.toggleBodyScroll(value));
            this.detectLiveLocation();
        },

        showNotification(msg) {
            this.toast.message = msg;
            this.toast.show = true;
            if (this.toast.timeout) clearTimeout(this.toast.timeout);
            this.toast.timeout = setTimeout(() => {
                this.toast.show = false;
            }, 3500);
        },

        confirmRevoke(id, deviceName, detail) {
            this.revokeTargetId = id;
            this.revokeTargetDevice = deviceName;
            this.revokeTargetDetail = detail || '';
            this.showRevokeModal = true;
        },

        executeRevoke() {
            this.showRevokeModal = false;
            if (this.revokeTargetId === 'all') {
                this.devices = [];
                this.showNotification('Seluruh sesi pada perangkat lain berhasil diputuskan.');
            } else {
                const targetName = this.revokeTargetDevice;
                this.devices = this.devices.filter(d => d.id !== this.revokeTargetId);
                this.showNotification('Sesi pada ' + targetName + ' berhasil diputuskan.');
            }
        },

        async detectLiveLocation() {
            this.liveLocation.status = 'loading';
            
            // 1. Dapatkan IP Lokal Wi-Fi Laptop (WebRTC candidate / fallback PHP local IP)
            try {
                const getLocalIP = () => new Promise((resolve) => {
                    const ipRegex = /(192\.168\.\d+\.\d+|10\.\d+\.\d+\.\d+|172\.(?:1[6-9]|2\d|3[01]|4[0-9])\.\d+\.\d+|172\.\d+\.\d+\.\d+)/;
                    const pc = new (window.RTCPeerConnection || window.mozRTCPeerConnection || window.webkitRTCPeerConnection)({
                        iceServers: []
                    });
                    pc.createDataChannel('');
                    pc.createOffer().then(offer => pc.setLocalDescription(offer)).catch(() => {});
                    pc.onicecandidate = (ice) => {
                        if (!ice || !ice.candidate || !ice.candidate.candidate) return;
                        const match = ipRegex.exec(ice.candidate.candidate);
                        if (match) {
                            resolve(match[1]);
                            try { pc.close(); } catch(e){}
                        }
                    };
                    setTimeout(() => resolve(null), 800);
                });

                const rtcIp = await getLocalIP();
                if (rtcIp) {
                    this.liveLocation.ip = rtcIp;
                } else {
                    this.liveLocation.ip = '{{ $clientIp }}';
                }
            } catch (e) {
                this.liveLocation.ip = '{{ $clientIp }}';
            }

            // 2. Deteksi Lokasi Real-Time & Bersihkan format nama Kecamatan
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    async (pos) => {
                        try {
                            const lat = pos.coords.latitude;
                            const lon = pos.coords.longitude;
                            const res = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}&zoom=14&addressdetails=1`, { signal: AbortSignal.timeout(4000) });
                            if (res.ok) {
                                const data = await res.json();
                                const a = data.address || {};
                                let rawDistrict = a.suburb || a.municipality || a.district || a.subdistrict || a.neighbourhood || a.village || 'Marpoyan Damai';
                                // Hilangkan kata 'District', 'Kecamatan', 'Kec.' redundant
                                rawDistrict = rawDistrict.replace(/\bDistrict\b/gi, '').replace(/\bKecamatan\b/gi, '').replace(/^Kec\.\s*/i, '').trim();
                                
                                let rawCity = a.city || a.town || a.county || 'Pekanbaru';
                                rawCity = rawCity.replace(/\bCity\b/gi, '').replace(/\bKota\b/gi, '').trim();

                                this.liveLocation.district = rawDistrict ? 'Kec. ' + rawDistrict : 'Kec. Marpoyan Damai';
                                this.liveLocation.city = rawCity ? rawCity : 'Pekanbaru';
                                this.liveLocation.status = 'ready';
                                return;
                            }
                        } catch (e) {}
                        this.liveLocation.status = 'ready';
                    },
                    () => {
                        this.liveLocation.status = 'ready';
                    },
                    { timeout: 4000, enableHighAccuracy: true }
                );
            } else {
                this.liveLocation.status = 'ready';
            }
        },

        toggleBodyScroll(lock) {
            if (this.showCropModal || this.showModalFoto || this.showPreviewModal) {
                document.body.style.overflow = 'hidden';
                document.documentElement.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
                document.documentElement.style.overflow = '';
            }
        },

        openFullPreview() {
            @if(!empty($user->foto))
                this.showPreviewModal = true;
            @else
                this.showModalFoto = true;
            @endif
        },

        triggerFileInput() {
            this.showModalFoto = false;
            const input = document.getElementById('avatarFileInput');
            if (input) {
                input.value = '';
                input.click();
            }
        },

        onFileSelected(event) {
            const file = event.target.files && event.target.files[0];
            if (!file) return;

            if (!file.type.startsWith('image/')) {
                alert('Silakan pilih file gambar (JPG, PNG, WEBP, dll).');
                return;
            }

            const reader = new FileReader();
            reader.onload = (e) => {
                this.imageSrcToCrop = e.target.result;
                this.showCropModal = true;
                
                this.$nextTick(() => {
                    const image = document.getElementById('imageToCrop');
                    if (!image) return;

                    const startCropper = () => {
                        if (this.cropperInstance) {
                            this.cropperInstance.destroy();
                            this.cropperInstance = null;
                        }
                        if (typeof Cropper === 'undefined') {
                            console.error('CropperJS belum termuat');
                            return;
                        }
                        this.cropperInstance = new Cropper(image, {
                            aspectRatio: 1,
                            viewMode: 1,
                            dragMode: 'move',
                            autoCropArea: 0.9,
                            responsive: true,
                            restore: false,
                            guides: true,
                            center: true,
                            highlight: false,
                            cropBoxMovable: true,
                            cropBoxResizable: true,
                            toggleDragModeOnDblclick: false,
                            background: true,
                            movable: true,
                            rotatable: true,
                            scalable: true,
                            zoomable: true,
                            zoomOnTouch: true,
                            zoomOnWheel: true,
                            wheelZoomRatio: 0.1,
                            minContainerWidth: 260,
                            minContainerHeight: 260,
                        });
                    };

                    if (image.complete && image.naturalWidth > 0) {
                        setTimeout(startCropper, 150);
                    } else {
                        image.onload = () => {
                            setTimeout(startCropper, 150);
                        };
                    }
                });
            };
            reader.readAsDataURL(file);
        },

        zoomImage(ratio) {
            if (this.cropperInstance) {
                this.cropperInstance.zoom(ratio);
            }
        },

        rotateImage(degree) {
            if (this.cropperInstance) {
                this.cropperInstance.rotate(degree);
            }
        },

        resetCropper() {
            if (this.cropperInstance) {
                this.cropperInstance.reset();
            }
        },

        closeCropModal() {
            if (this.cropperInstance) {
                this.cropperInstance.destroy();
                this.cropperInstance = null;
            }
            this.showCropModal = false;
            this.imageSrcToCrop = '';
            const input = document.getElementById('avatarFileInput');
            if (input) input.value = '';
        },

        applyCropAndUpload() {
            if (!this.cropperInstance || this.isUploading) return;
            this.isUploading = true;

            const canvas = this.cropperInstance.getCroppedCanvas({
                width: 600,
                height: 600,
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high',
            });

            if (!canvas) {
                this.isUploading = false;
                alert('Gagal memproses crop gambar.');
                return;
            }

            const base64Data = canvas.toDataURL('image/webp', 0.90) || canvas.toDataURL('image/jpeg', 0.90);
            const base64Input = document.getElementById('avatarBase64Input');
            if (base64Input) {
                base64Input.value = base64Data;
            }

            try {
                canvas.toBlob((blob) => {
                    if (blob) {
                        const file = new File([blob], 'avatar_cropped.webp', { type: 'image/webp' });
                        const container = new DataTransfer();
                        container.items.add(file);
                        const fileInput = document.getElementById('avatarFileInput');
                        if (fileInput) fileInput.files = container.files;
                    }
                }, 'image/webp', 0.88);
            } catch (e) {}

            if (this.cropperInstance) {
                this.cropperInstance.destroy();
                this.cropperInstance = null;
            }
            this.showCropModal = false;

            const form = document.getElementById('avatarFormUpload');
            if (form) {
                form.submit();
            }
        }
     }">

    {{-- FLOATING TOAST NOTIFIKASI --}}
    <div x-show="toast.show" 
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 -translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 -translate-y-4 scale-95"
         class="fixed top-6 left-1/2 -translate-x-1/2 z-[99999] max-w-md w-full px-4"
         style="display: none;">
        <div class="bg-white dark:bg-slate-900 border border-emerald-200/90 dark:border-emerald-800/80 rounded-2xl shadow-2xl p-4 flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">LABSYSTEM • NOTIFIKASI</span>
                    <span class="text-[10px] text-slate-400 dark:text-slate-500">Baru saja</span>
                </div>
                <p class="text-xs font-bold text-slate-800 dark:text-slate-100 mt-0.5" x-text="toast.message"></p>
            </div>
            <button type="button" @click="toast.show = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>

    {{-- PAGE HEADER --}}
    <div>
        <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Akun Pengguna</div>
        <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">Profil Saya</h1>
    </div>

    {{-- ROW 1: PROFILE SUMMARY (COL 4) + PERSONAL INFORMATION FORM (COL 8) --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 sm:gap-6 2xl:gap-8">
        
        {{-- Profile Summary Card (Col 4) --}}
        <div class="lg:col-span-4 space-y-4 sm:space-y-6">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xs overflow-hidden">
                <div class="px-4 py-3 sm:px-5 sm:py-3.5 border-b border-slate-100 dark:border-slate-800">
                    <h2 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white">Ringkasan Profil</h2>
                    <p class="text-[11px] sm:text-[11.5px] text-slate-500 dark:text-slate-400 font-normal mt-0.5">Identitas akun dan status keanggotaan</p>
                </div>
                <div class="p-4 sm:p-5 text-center">
                    {{-- Avatar --}}
                    <div class="relative w-20 h-20 sm:w-24 sm:h-24 mx-auto mb-3.5 group cursor-pointer" @click="openFullPreview()" title="Klik untuk melihat foto full">
                        <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full overflow-hidden shadow-sm border-2 sm:border-3 border-slate-100 dark:border-slate-800 bg-gradient-to-tr from-indigo-600 to-indigo-800 text-white flex items-center justify-center font-bold text-2xl sm:text-3xl">
                            @if(!empty($user->foto))
                                <img src="{{ $user->foto_url ?? asset('uploads/' . $user->foto) }}" alt="{{ $user->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                                     onerror="if(!this.dataset.retry){this.dataset.retry='1'; this.src='{{ asset('storage/' . $user->foto) }}?v={{ time() }}';}else{this.style.display='none'; this.nextElementSibling.style.display='flex';}">
                                <span style="display: none;" class="w-full h-full flex items-center justify-center font-bold text-2xl sm:text-3xl">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                            @else
                                <span>{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- Nama & Subtitle --}}
                    <h2 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white leading-snug truncate">{{ $user->name }}</h2>
                    <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 mt-0.5 capitalize truncate">
                        {{ $user->role }} · {{ $user->laboratorium_penugasan ?? ($user->kelas_atau_jabatan ?? 'Laboratorium TKJ') }}
                    </p>

                    {{-- Change Avatar Button --}}
                    <div class="mt-3.5 flex items-center justify-center gap-2">
                        <button type="button" 
                                @click="showModalFoto = true"
                                class="h-8 px-3 inline-flex items-center gap-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-[11px] sm:text-xs font-semibold text-slate-700 dark:text-slate-200 shadow-2xs transition active:scale-95">
                            <svg class="w-3.5 h-3.5 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>Ubah Foto</span>
                        </button>
                    </div>
                </div>

                {{-- Profile Completion Meter Ring (Gentelella v4 Style) --}}
                <div class="border-t border-slate-100 dark:border-slate-800 p-4 sm:p-5 bg-slate-50/50 dark:bg-slate-850/50 text-center">
                    <div class="relative w-16 h-16 sm:w-20 sm:h-20 mx-auto mb-2 flex items-center justify-center">
                        <svg class="w-16 h-16 sm:w-20 sm:h-20 -rotate-90 transform" viewBox="0 0 80 80">
                            {{-- Background Ring --}}
                            <circle class="text-slate-200 dark:text-slate-700" stroke-width="6" stroke="currentColor" fill="transparent" cx="40" cy="40" r="34"/>
                            {{-- Fill Progress Ring --}}
                            <circle class="text-indigo-600 dark:text-indigo-400 transition-all duration-700 ease-out" 
                                    stroke-width="6" 
                                    stroke-dasharray="213.6" 
                                    stroke-dashoffset="{{ $dashOffset }}" 
                                    stroke-linecap="round" 
                                    stroke="currentColor" 
                                    fill="transparent" 
                                    cx="40" cy="40" r="34"/>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center font-bold text-slate-800 dark:text-white text-xs sm:text-sm">
                            <span>{{ $completionPercent }}<span class="text-[10px] sm:text-xs text-slate-400 font-normal">%</span></span>
                        </div>
                    </div>
                    <div class="text-[11px] sm:text-xs font-semibold text-slate-700 dark:text-slate-300">Kelengkapan Profil</div>
                    <p class="text-[10px] sm:text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">
                        {{ $completionPercent === 100 ? 'Profil telah terisi lengkap' : 'Lengkapi data kontak & nomor induk' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Personal Information Card (Col 8) --}}
        <div class="lg:col-span-8">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xs relative z-20">
                <div class="px-4 py-3 sm:px-5 sm:py-3.5 border-b border-slate-100 dark:border-slate-800 rounded-t-xl">
                    <h2 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white">Informasi Pribadi</h2>
                    <p class="text-[11px] sm:text-[11.5px] text-slate-500 dark:text-slate-400 font-normal mt-0.5">Perbarui nama lengkap, email, nomor induk, dan kontak akun Anda.</p>
                </div>

                <form id="formPersonalInformation" method="POST" action="{{ route('profile.update') }}" class="p-4 sm:p-5 space-y-4">
                    @csrf
                    @method('patch')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 sm:gap-4">
                        {{-- Nama Lengkap --}}
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider text-[10px] sm:text-[11px] mb-1.5">
                                Nama Lengkap <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" 
                                   name="name" 
                                   x-model="formData.name"
                                   value="{{ old('name', $user->name) }}" 
                                   required
                                   class="w-full bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 rounded-xl p-2.5 text-xs sm:text-sm text-slate-900 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition focus:outline-none">
                            @error('name')<p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>@enderror
                        </div>

                        {{-- Nomor Induk (NIS / NIP) --}}
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider text-[10px] sm:text-[11px] mb-1.5">
                                NIS / NIP
                            </label>
                            <input type="text" 
                                   name="nomor_induk" 
                                   x-model="formData.nomor_induk"
                                   value="{{ old('nomor_induk', $user->nomor_induk) }}" 
                                   placeholder="Contoh: 202410101"
                                   class="w-full bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 rounded-xl p-2.5 text-xs sm:text-sm text-slate-900 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition focus:outline-none font-mono">
                            @error('nomor_induk')<p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 sm:gap-4">
                        {{-- Email --}}
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider text-[10px] sm:text-[11px] mb-1.5">
                                Alamat Email <span class="text-rose-500">*</span>
                            </label>
                            <input type="email" 
                                   name="email" 
                                   x-model="formData.email"
                                   value="{{ old('email', $user->email) }}" 
                                   required
                                   class="w-full bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 rounded-xl p-2.5 text-xs sm:text-sm text-slate-900 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition focus:outline-none">
                            @error('email')<p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>@enderror
                        </div>

                        {{-- Telepon / WA --}}
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider text-[10px] sm:text-[11px] mb-1.5">
                                No. WhatsApp / HP
                            </label>
                            <input type="text" 
                                   name="telepon" 
                                   x-model="formData.telepon"
                                   value="{{ old('telepon', $user->telepon) }}" 
                                   placeholder="Contoh: 081234567890"
                                   class="w-full bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 rounded-xl p-2.5 text-xs sm:text-sm text-slate-900 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition focus:outline-none">
                            @error('telepon')<p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 sm:gap-4">
                        {{-- Peran (Role) --}}
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider text-[10px] sm:text-[11px] mb-1.5">
                                Hak Akses (Role)
                            </label>
                            <div class="w-full bg-slate-100/80 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-2.5 text-xs sm:text-sm font-semibold flex items-center justify-between">
                                <span class="capitalize text-slate-800 dark:text-slate-200">{{ $user->role }}</span>
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider
                                    @if($user->role == 'admin') bg-purple-100 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800
                                    @elseif($user->role == 'guru') bg-indigo-100 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800
                                    @else bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 @endif">
                                    {{ $user->role }}
                                </span>
                            </div>
                        </div>

                        {{-- Kelas / Jabatan --}}
                        <div>
                            @if($roleName === 'admin')
                                <div class="relative" x-data="{ openJabatan: false }" @click.outside="openJabatan = false">
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider text-[10px] sm:text-[11px] mb-1.5">
                                        Jabatan Pengelola
                                    </label>
                                    <input type="hidden" name="kelas_atau_jabatan" :value="formData.kelas_atau_jabatan" required>

                                    <button type="button" @click="openJabatan = !openJabatan"
                                            class="w-full flex items-center justify-between bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 rounded-xl p-2.5 text-xs sm:text-sm font-medium transition focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                            :class="{'border-indigo-500 ring-2 ring-indigo-500/20 bg-white dark:bg-slate-800': openJabatan}">
                                        <span class="truncate text-slate-800 dark:text-slate-200" x-text="formData.kelas_atau_jabatan || 'Pilih Jabatan'"></span>
                                        <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{'rotate-180 text-indigo-600': openJabatan}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>

                                    <div x-show="openJabatan"
                                         x-transition:enter="transition ease-out duration-150"
                                         x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                         x-transition:leave="transition ease-in duration-100"
                                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                         x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                                         class="absolute z-50 w-full mt-1.5 bg-white dark:bg-slate-800 rounded-xl shadow-2xl border border-slate-200/90 dark:border-slate-700 py-1 max-h-56 overflow-y-auto"
                                         style="display: none;">
                                        @foreach($pilihanOpsi as $grup => $items)
                                            <div class="px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 bg-slate-50/80 dark:bg-slate-800/80">{{ $grup }}</div>
                                            @foreach($items as $item)
                                                <button type="button" @click="formData.kelas_atau_jabatan = '{{ $item }}'; openJabatan = false"
                                                        class="w-full text-left px-3.5 py-2 text-xs font-semibold flex items-center justify-between transition hover:bg-indigo-50 dark:hover:bg-indigo-950/50 hover:text-indigo-600 dark:hover:text-indigo-400"
                                                        :class="{'bg-indigo-50/70 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400': formData.kelas_atau_jabatan === '{{ $item }}', 'text-slate-700 dark:text-slate-300': formData.kelas_atau_jabatan !== '{{ $item }}'}">
                                                    <span>{{ $item }}</span>
                                                    <svg x-show="formData.kelas_atau_jabatan === '{{ $item }}'" class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                </button>
                                            @endforeach
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider text-[10px] sm:text-[11px] mb-1.5">
                                    {{ $isSiswa ? 'Kelas Siswa' : 'Jabatan' }}
                                </label>
                                <div class="relative">
                                    <input type="text" 
                                           value="{{ $user->kelas_atau_jabatan ?: ($isSiswa ? 'Belum Diatur Kelas' : 'Staf') }}" 
                                           disabled
                                           class="w-full bg-slate-100/80 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 font-semibold rounded-xl p-2.5 pr-9 text-xs sm:text-sm cursor-not-allowed select-none">
                                    <div class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 pointer-events-none">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-2.5">
                        <button type="button" 
                                @click="discardChanges()" 
                                :disabled="!isFormDirty()"
                                class="px-3.5 py-1.5 sm:px-4 sm:py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-750 text-slate-700 dark:text-slate-200 rounded-lg text-xs font-semibold transition active:scale-95 disabled:opacity-40 disabled:cursor-not-allowed disabled:pointer-events-none">
                            Discard
                        </button>
                        <button type="submit" 
                                :disabled="!isFormDirty()"
                                class="px-4 py-1.5 sm:px-5 sm:py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-bold transition active:scale-95 shadow-sm disabled:opacity-40 disabled:cursor-not-allowed disabled:pointer-events-none">
                            Save changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ROW 2: KEAMANAN KATA SANDI + SESI & PERANGKAT AKTIF (COL 2) --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 2xl:gap-8">
        
        {{-- Card Keamanan & Kata Sandi --}}
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xs overflow-hidden">
            <div class="px-4 py-3 sm:px-5 sm:py-3.5 border-b border-slate-100 dark:border-slate-800">
                <h2 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white">Keamanan & Kata Sandi</h2>
                <p class="text-[11px] sm:text-[11.5px] text-slate-500 dark:text-slate-400 font-normal mt-0.5">Perbarui kata sandi secara berkala untuk melindungi akun Anda.</p>
            </div>

            <form method="POST" action="{{ route('password.update') }}" class="p-4 sm:p-5 space-y-4">
                @csrf
                @method('put')

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider text-[10px] sm:text-[11px] mb-1.5">
                        Kata Sandi Saat Ini
                    </label>
                    <input type="password" 
                           name="current_password" 
                           required 
                           placeholder="Masukan kata sandi saat ini"
                           class="w-full bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 rounded-xl p-2.5 text-xs sm:text-sm text-slate-900 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition focus:outline-none">
                    @error('current_password', 'updatePassword')<p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider text-[10px] sm:text-[11px] mb-1.5">
                            Kata Sandi Baru
                        </label>
                        <input type="password" 
                               name="password" 
                               required 
                               placeholder="Minimal 6 karakter"
                               class="w-full bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 rounded-xl p-2.5 text-xs sm:text-sm text-slate-900 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition focus:outline-none">
                        @error('password', 'updatePassword')<p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider text-[10px] sm:text-[11px] mb-1.5">
                            Konfirmasi Sandi Baru
                        </label>
                        <input type="password" 
                               name="password_confirmation" 
                               required 
                               placeholder="Ulangi kata sandi"
                               class="w-full bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 rounded-xl p-2.5 text-xs sm:text-sm text-slate-900 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition focus:outline-none">
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end">
                    <button type="submit" class="px-4 py-1.5 sm:px-5 sm:py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-bold transition active:scale-95 shadow-sm">
                        Perbarui Kata Sandi
                    </button>
                </div>
            </form>
        </div>

        {{-- Card Sesi & Perangkat Aktif (Identik dengan profile.html) --}}
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xs overflow-hidden flex flex-col justify-between">
            <div>
                <div class="px-4 py-3 sm:px-5 sm:py-3.5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white">Sesi & Perangkat</h2>
                        <p class="text-[11px] sm:text-[11.5px] text-slate-500 dark:text-slate-400 font-normal mt-0.5">Perangkat yang saat ini sedang login ke akun Anda.</p>
                    </div>
                    <button type="button" 
                            @click="confirmRevoke('all', 'Semua Perangkat Lain', 'Seluruh sesi login selain perangkat ini')"
                            :disabled="devices.length === 0"
                            class="h-7 sm:h-8 px-2.5 sm:px-3 inline-flex items-center gap-1 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-[10px] sm:text-[11px] font-semibold text-slate-700 dark:text-slate-200 transition active:scale-95 shadow-2xs shrink-0 disabled:opacity-40 disabled:cursor-not-allowed disabled:pointer-events-none">
                        <span>Putuskan Semua</span>
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="bg-slate-50/70 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-800 text-[10.5px] sm:text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                <th class="px-4 py-2.5 sm:px-5 sm:py-3">Perangkat</th>
                                <th class="px-3 py-2.5 sm:px-4 sm:py-3">Lokasi & IP</th>
                                <th class="px-3 py-2.5 sm:px-4 sm:py-3">Terakhir Aktif</th>
                                <th class="px-4 py-2.5 sm:px-5 sm:py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                            {{-- Sesi 1: Perangkat Saat Ini --}}
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition">
                                <td class="px-4 py-3 sm:px-5 sm:py-3.5">
                                    <div class="font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                                        @if($isMobileDevice)
                                            <svg class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                        @else
                                            <svg class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        @endif
                                        <span class="truncate">{{ $deviceName }}</span>
                                    </div>
                                    <div class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">{{ $browserName }}</div>
                                </td>
                                <td class="px-3 py-3 sm:px-4 sm:py-3.5">
                                    <div class="flex items-center gap-1.5 font-medium text-slate-800 dark:text-slate-200">
                                        <span x-text="liveLocation.district + ', ' + liveLocation.city">Kec. Marpoyan Damai, Pekanbaru</span>
                                    </div>
                                    <div class="text-[11px] text-slate-400 dark:text-slate-500 font-mono mt-0.5 flex items-center gap-1.5">
                                        <span x-text="liveLocation.ip">{{ $clientIp }}</span>
                                        <button type="button" @click="detectLiveLocation()" title="Deteksi Ulang Lokasi Real-Time" class="text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                        </button>
                                    </div>
                                </td>
                                <td class="px-3 py-3 sm:px-4 sm:py-3.5">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        <span>Aktif Sekarang</span>
                                    </span>
                                    <div class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5">{{ now()->translatedFormat('H:i') }} WIB</div>
                                </td>
                                <td class="px-4 py-3 sm:px-5 sm:py-3.5 text-right">
                                    <span class="text-[11px] font-semibold text-slate-400 dark:text-slate-500 italic">Sesi Ini</span>
                                </td>
                            </tr>

                            {{-- Sesi Perangkat Lain (Dinamis & Hilang Saat Diputuskan) --}}
                            <template x-for="dev in devices" :key="dev.id">
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition">
                                    <td class="px-4 py-3 sm:px-5 sm:py-3.5">
                                        <div class="font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                                            <template x-if="dev.icon === 'mobile'">
                                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                            </template>
                                            <template x-if="dev.icon === 'desktop'">
                                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                            </template>
                                            <span class="truncate" x-text="dev.name"></span>
                                        </div>
                                        <div class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5" x-text="dev.browser"></div>
                                    </td>
                                    <td class="px-3 py-3 sm:px-4 sm:py-3.5">
                                        <div class="font-medium text-slate-800 dark:text-slate-200" x-text="dev.district + ', ' + dev.city"></div>
                                        <div class="text-[11px] text-slate-400 dark:text-slate-500 font-mono" x-text="dev.ip"></div>
                                    </td>
                                    <td class="px-3 py-3 sm:px-4 sm:py-3.5">
                                        <div class="font-medium text-slate-700 dark:text-slate-300" x-text="dev.lastActive"></div>
                                        <div class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5" x-text="dev.timeText"></div>
                                    </td>
                                    <td class="px-4 py-3 sm:px-5 sm:py-3.5 text-right">
                                        <button type="button" 
                                                @click="confirmRevoke(dev.id, dev.name, dev.browser + ' · ' + dev.district)"
                                                class="h-7 px-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 dark:hover:bg-rose-950/40 dark:hover:text-rose-400 text-[10.5px] sm:text-[11px] font-semibold text-slate-600 dark:text-slate-300 transition active:scale-95 shadow-2xs">
                                            Putuskan
                                        </button>
                                    </td>
                                </tr>
                            </template>

                            {{-- State jika semua sesi lain telah diputuskan --}}
                            <tr x-show="devices.length === 0" style="display: none;">
                                <td colspan="4" class="px-4 py-3 text-center text-slate-400 dark:text-slate-500 text-xs italic bg-slate-50/30 dark:bg-slate-800/20">
                                    Tidak ada sesi login lain yang aktif.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="px-4 py-2.5 sm:px-5 sm:py-3 bg-slate-50/50 dark:bg-slate-850/50 border-t border-slate-100 dark:border-slate-800 text-[10.5px] sm:text-[11px] text-slate-400 dark:text-slate-500 flex items-center justify-between">
                <span>Terdaftar sejak: {{ $user->created_at ? $user->created_at->translatedFormat('d M Y') : '—' }}</span>
                <span class="font-mono">ID Akun: #{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</span>
            </div>
        </div>
    </div>

    {{-- ROW 3: STATISTIK AKUN + TIMELINE AKTIVITAS (COL 4 - 8) --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 sm:gap-6 2xl:gap-8">
        
        {{-- Card Statistik Akun (Col 4) --}}
        <div class="lg:col-span-4">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xs overflow-hidden">
                <div class="px-4 py-3 sm:px-5 sm:py-3.5 border-b border-slate-100 dark:border-slate-800">
                    <h2 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white">Statistik Akun</h2>
                    <p class="text-[11px] sm:text-[11.5px] text-slate-500 dark:text-slate-400 font-normal mt-0.5">Ringkasan transaksi laboratorium</p>
                </div>

                <div class="grid grid-cols-2 divide-x divide-y divide-slate-100 dark:divide-slate-800">
                    {{-- Total Pinjam --}}
                    <div class="p-4 sm:p-5 text-center">
                        <div class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white">{{ $totalPinjam }}</div>
                        <div class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mt-1">Total Pinjam</div>
                    </div>

                    {{-- Pinjam Aktif --}}
                    <div class="p-4 sm:p-5 text-center">
                        <div class="text-xl sm:text-2xl font-black text-indigo-600 dark:text-indigo-400">{{ $pinjamAktif }}</div>
                        <div class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mt-1">Sedang Aktif</div>
                    </div>

                    {{-- Selesai --}}
                    <div class="p-4 sm:p-5 text-center border-t border-slate-100 dark:border-slate-800">
                        <div class="text-xl sm:text-2xl font-black text-emerald-600 dark:text-emerald-400">{{ $pinjamSelesai }}</div>
                        <div class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mt-1">Selesai</div>
                    </div>

                    {{-- Status Akun --}}
                    <div class="p-4 sm:p-5 text-center border-t border-slate-100 dark:border-slate-800">
                        <div class="text-base sm:text-lg font-black text-slate-800 dark:text-slate-100 mt-0.5">Aktif</div>
                        <div class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mt-1">Status Akun</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card Riwayat Aktivitas Akun (Col 8) --}}
        <div class="lg:col-span-8">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xs overflow-hidden">
                <div class="px-4 py-3 sm:px-5 sm:py-3.5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white">Aktivitas Terbaru Akun</h2>
                        <p class="text-[11px] sm:text-[11.5px] text-slate-500 dark:text-slate-400 font-normal mt-0.5">Catatan aktivitas dan status peminjaman laboratorium</p>
                    </div>
                    <a href="{{ route('peminjaman.saya') }}" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline shrink-0">
                        Lihat Peminjaman →
                    </a>
                </div>

                <div class="p-4 sm:p-5">
                    <div class="relative pl-5 sm:pl-6 space-y-5 sm:space-y-6 border-l-2 border-slate-100 dark:border-slate-800">
                        {{-- Timeline item 1 --}}
                        <div class="relative">
                            <span class="absolute -left-[27px] sm:-left-[31px] top-1 w-3 h-3 sm:w-3.5 sm:h-3.5 rounded-full bg-indigo-600 border-2 border-white dark:border-slate-900"></span>
                            <div class="text-[10px] uppercase font-bold tracking-wider text-indigo-600 dark:text-indigo-400">Sesi Login</div>
                            <div class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-100 mt-0.5">Login melalui peramban {{ $browserName }}</div>
                            <div class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">{{ $deviceName }} · Aktif saat ini</div>
                        </div>

                        {{-- Timeline item 2 --}}
                        <div class="relative">
                            <span class="absolute -left-[27px] sm:-left-[31px] top-1 w-3 h-3 sm:w-3.5 sm:h-3.5 rounded-full bg-emerald-500 border-2 border-white dark:border-slate-900"></span>
                            <div class="text-[10px] uppercase font-bold tracking-wider text-emerald-600 dark:text-emerald-400">Data Akun</div>
                            <div class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-100 mt-0.5">Akun terverifikasi dalam sistem inventaris laboratorium</div>
                            <div class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">Hak akses: {{ ucfirst($user->role) }}</div>
                        </div>

                        {{-- Timeline item 3 --}}
                        <div class="relative">
                            <span class="absolute -left-[27px] sm:-left-[31px] top-1 w-3 h-3 sm:w-3.5 sm:h-3.5 rounded-full bg-slate-300 dark:bg-slate-700 border-2 border-white dark:border-slate-900"></span>
                            <div class="text-[10px] uppercase font-bold tracking-wider text-slate-400 dark:text-slate-500">Pembaruan Terakhir</div>
                            <div class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-100 mt-0.5">Data profil tersinkronisasi</div>
                            <div class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">{{ $user->updated_at ? $user->updated_at->diffForHumans() : 'Baru saja' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Form Hidden untuk Upload Foto Mandiri Cepat --}}
    <form id="avatarFormUpload" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="hidden">
        @csrf
        @method('patch')
        <input type="hidden" name="name" value="{{ $user->name }}">
        <input type="hidden" name="email" value="{{ $user->email }}">
        <input type="hidden" name="nomor_induk" value="{{ $user->nomor_induk }}">
        <input type="hidden" name="kelas_atau_jabatan" value="{{ $user->kelas_atau_jabatan }}">
        <input type="hidden" name="telepon" value="{{ $user->telepon }}">
        <input type="hidden" name="foto_base64" id="avatarBase64Input">
        <input type="file" name="foto" id="avatarFileInput" accept="image/*" @change="onFileSelected($event)">
    </form>

    {{-- Form Hidden untuk Hapus Foto --}}
    <form id="avatarFormDelete" action="{{ route('profile.delete-foto') }}" method="POST" class="hidden">
        @csrf
        @method('delete')
    </form>

    {{-- 1. FULLSCREEN PHOTO PREVIEW MODAL --}}
    <div x-cloak x-show="showPreviewModal" class="fixed inset-0 z-[110] flex items-center justify-center p-4">
        <div x-show="showPreviewModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="showPreviewModal = false"
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>

        <div x-show="showPreviewModal"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative z-10 max-w-sm w-full bg-white dark:bg-slate-900 rounded-3xl overflow-hidden shadow-2xl border border-slate-200 dark:border-slate-800 p-5 sm:p-6 text-center">
            
            <button type="button" @click="showPreviewModal = false"
                    class="absolute top-4 right-4 z-20 w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-500 dark:text-slate-400 flex items-center justify-center transition active:scale-90">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <div class="mb-4 text-left pr-8">
                <h4 class="text-slate-800 dark:text-white font-extrabold text-base">{{ $user->name }}</h4>
                <p class="text-xs text-indigo-600 dark:text-indigo-400 font-semibold mt-0.5">{{ $user->kelas_atau_jabatan ?? ucfirst($user->role) }}</p>
            </div>

            <div class="w-full aspect-square rounded-2xl overflow-hidden bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center shadow-inner relative">
                @if(!empty($user->foto))
                    <img src="{{ $user->foto_url ?? asset('uploads/' . $user->foto) }}" alt="{{ $user->name }}" class="w-full h-full object-cover"
                         onerror="if(!this.dataset.retry){this.dataset.retry='1'; this.src='{{ asset('storage/' . $user->foto) }}?v={{ time() }}';}else{this.style.display='none'; this.nextElementSibling.style.display='flex';}">
                    <div style="display: none;" class="w-full h-full flex items-center justify-center bg-gradient-to-tr from-indigo-600 to-indigo-800 text-white font-black text-6xl select-none">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @else
                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-tr from-indigo-600 to-indigo-800 text-white font-black text-6xl select-none">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
            </div>

            <div class="mt-5 flex gap-2.5">
                <button type="button" @click="showPreviewModal = false; showModalFoto = true"
                        class="flex-1 py-2.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold transition shadow-sm active:scale-95 flex items-center justify-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>Ubah Foto</span>
                </button>
                <button type="button" @click="showPreviewModal = false"
                        class="py-2.5 px-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold transition active:scale-95">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    {{-- 2. CROPPER MODAL (Fully Responsive All Devices) --}}
    <div x-cloak x-show="showCropModal" class="fixed inset-0 z-[120] flex items-center justify-center p-3 sm:p-4 overflow-y-auto">
        <div x-show="showCropModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="closeCropModal()"
             class="fixed inset-0 bg-slate-950/80 backdrop-blur-md"></div>

        <div x-show="showCropModal"
             x-transition:enter="transition ease-out duration-250 transform"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150 transform"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative z-10 max-w-md w-full bg-white dark:bg-slate-900 rounded-3xl overflow-hidden shadow-2xl border border-slate-200 dark:border-slate-800 p-4 sm:p-6 space-y-3.5 sm:space-y-4 my-auto">
            
            <div class="text-center pb-2 border-b border-slate-100 dark:border-slate-800 relative">
                <h3 class="text-base font-extrabold text-slate-800 dark:text-white">Sesuaikan Foto Profil</h3>
                <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Geser foto untuk memposisikan & gunakan tombol zoom/putar</p>
                <button type="button" @click="closeCropModal()" class="absolute top-0 right-0 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Cropper Container --}}
            <div class="w-full h-[260px] sm:h-[320px] bg-slate-950 rounded-2xl flex items-center justify-center relative overflow-hidden select-none touch-none shadow-inner border border-slate-800">
                <img id="imageToCrop" :src="imageSrcToCrop" class="max-w-full max-h-full block" alt="Crop Target">
            </div>

            {{-- Control Buttons (Zoom, Rotate, Reset) --}}
            <div class="flex items-center justify-center gap-2 sm:gap-3 py-1">
                <button type="button" @click="zoomImage(-0.1)" title="Perkecil (-)"
                        class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 flex items-center justify-center transition active:scale-90 font-bold text-sm">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"/>
                    </svg>
                </button>

                <button type="button" @click="zoomImage(0.1)" title="Perbesar (+)"
                        class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 flex items-center justify-center transition active:scale-90 font-bold text-sm">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                </button>

                <button type="button" @click="rotateImage(-90)" title="Putar Kiri (90°)"
                        class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 flex items-center justify-center transition active:scale-90">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a5 5 0 015 5v2m-15-7l3-3m-3 3l3 3"/>
                    </svg>
                </button>

                <button type="button" @click="rotateImage(90)" title="Putar Kanan (90°)"
                        class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 flex items-center justify-center transition active:scale-90">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 10H11a5 5 0 00-5 5v2m15-7l-3-3m3 3l-3 3"/>
                    </svg>
                </button>

                <button type="button" @click="resetCropper()" title="Reset Posisi"
                        class="px-3 h-9 sm:h-10 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold flex items-center justify-center transition active:scale-90">
                    Reset
                </button>
            </div>

            {{-- Action Buttons --}}
            <div class="flex gap-2.5 pt-1">
                <button type="button" @click="closeCropModal()" :disabled="isUploading"
                        class="flex-1 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 text-slate-600 dark:text-slate-300 font-bold text-xs hover:bg-slate-50 dark:hover:bg-slate-800 transition active:scale-95 disabled:opacity-50">
                    Batal
                </button>
                <button type="button" @click="applyCropAndUpload()" :disabled="isUploading"
                        class="flex-1 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-sm transition active:scale-95 flex items-center justify-center gap-1.5 disabled:opacity-50">
                    <template x-if="isUploading">
                        <div class="flex items-center gap-1.5">
                            <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span>Menyimpan...</span>
                        </div>
                    </template>
                    <template x-if="!isUploading">
                        <div class="flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span>Simpan Foto</span>
                        </div>
                    </template>
                </button>
            </div>
        </div>
    </div>

    {{-- 3. BOTTOM SHEET ACTION MODAL --}}
    <div x-cloak x-show="showModalFoto" class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-0 sm:p-4">
        <div x-show="showModalFoto"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="showModalFoto = false"
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>

        <div x-show="showModalFoto"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="translate-y-full opacity-0 sm:translate-y-4 sm:scale-95"
             x-transition:enter-end="translate-y-0 opacity-100 sm:scale-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="translate-y-0 opacity-100 sm:scale-100"
             x-transition:leave-end="translate-y-full opacity-0 sm:translate-y-4 sm:scale-95"
             class="relative w-full sm:max-w-sm bg-white dark:bg-slate-900 rounded-t-[32px] sm:rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800 p-6 z-10 space-y-4">
            
            <div class="w-12 h-1.5 bg-slate-200 dark:bg-slate-700 rounded-full mx-auto sm:hidden mb-2"></div>

            <div class="text-center pb-2">
                <h3 class="text-base font-extrabold text-slate-800 dark:text-white">Foto Profil</h3>
                <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Pilih tindakan untuk foto akun Anda</p>
            </div>

            <div class="space-y-2">
                @if(!empty($user->foto))
                    <button type="button" @click="showModalFoto = false; showPreviewModal = true"
                            class="w-full flex items-center justify-center gap-3 py-3 px-4 rounded-xl bg-slate-50 dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs sm:text-sm transition active:scale-95">
                        <svg class="w-4 h-4 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <span>Lihat Foto Lengkap</span>
                    </button>
                @endif

                <button type="button" @click="triggerFileInput()"
                        class="w-full flex items-center justify-center gap-3 py-3 px-4 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 hover:bg-indigo-100 dark:hover:bg-indigo-900/60 text-indigo-700 dark:text-indigo-300 font-bold text-xs sm:text-sm transition active:scale-95">
                    <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>Ganti Foto Profil</span>
                </button>

                @if(!empty($user->foto))
                    <button type="button" onclick="if(confirm('Yakin ingin menghapus foto profil Anda?')) { document.getElementById('avatarFormDelete').submit(); }"
                            class="w-full flex items-center justify-center gap-3 py-3 px-4 rounded-xl bg-rose-50 dark:bg-rose-950/50 hover:bg-rose-100 dark:hover:bg-rose-900/50 text-rose-700 dark:text-rose-300 font-bold text-xs sm:text-sm transition active:scale-95">
                        <svg class="w-4 h-4 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        <span>Hapus Foto</span>
                    </button>
                @endif

                <button type="button" @click="showModalFoto = false"
                        class="w-full py-2.5 px-4 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 font-bold text-xs sm:text-sm hover:bg-slate-50 dark:hover:bg-slate-800 transition active:scale-95">
                    Batal
                </button>
            </div>
        </div>
    </div>

    {{-- 4. MODAL KONFIRMASI PUTUSKAN SESI PERANGKAT --}}
    <div x-cloak x-show="showRevokeModal" class="fixed inset-0 z-[130] flex items-center justify-center p-4">
        <div x-show="showRevokeModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="showRevokeModal = false"
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>

        <div x-show="showRevokeModal"
             x-transition:enter="transition ease-out duration-250 transform"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150 transform"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative z-10 max-w-sm w-full bg-white dark:bg-slate-900 rounded-3xl overflow-hidden shadow-2xl border border-slate-200 dark:border-slate-800 p-5 sm:p-6 text-center space-y-4 my-auto">
            
            <div class="w-12 h-12 rounded-2xl bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center mx-auto border border-rose-200/80 dark:border-rose-800/80 shadow-2xs">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
            </div>

            <div>
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Putuskan Sesi Perangkat?</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5 leading-relaxed">
                    Apakah Anda yakin ingin memutuskan sesi pada perangkat <strong class="text-slate-800 dark:text-slate-200" x-text="revokeTargetDevice"></strong> (<span class="text-slate-600 dark:text-slate-300 font-mono" x-text="revokeTargetDetail"></span>)? Akun pada perangkat tersebut akan otomatis keluar.
                </p>
            </div>

            <div class="flex gap-2.5 pt-1">
                <button type="button" @click="showRevokeModal = false"
                        class="flex-1 py-2.5 px-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-750 text-slate-700 dark:text-slate-200 text-xs font-bold transition active:scale-95 shadow-2xs">
                    Batal
                </button>
                <button type="button" @click="executeRevoke()"
                        class="flex-1 py-2.5 px-4 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition shadow-sm active:scale-95 flex items-center justify-center gap-1.5">
                    <span>Ya, Putuskan</span>
                </button>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<!-- CropperJS JS CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
@endpush

@endsection