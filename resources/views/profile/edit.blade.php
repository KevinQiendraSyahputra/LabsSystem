@extends('layouts.app')

@section('title', 'Profil Saya')
@section('page_title', 'Profil Akun Saya')
@section('page_subtitle', 'Kelola informasi pribadi, kontak, dan keamanan kata sandi')

@section('content')

@php
    $roleName = strtolower(trim($user->role ?? auth()->user()->role ?? ''));
    $valJabatan = trim($user->kelas_atau_jabatan ?? '');

    // Cek apakah akun adalah staf pengelola non-siswa asli
    $isStaffOrAdmin = in_array($roleName, ['admin', 'guru', 'staf', 'kepala_lab', 'teknisi']);

    // Siswa tetap dianggap siswa meskipun memiliki jabatan koordinator lab atau role koordinator
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
@endphp

@push('styles')
<!-- CropperJS CSS CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css"/>
<style>
    /* Styling Cropper Modern & Responsif */
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

<div class="max-w-4xl mx-auto space-y-6"
     x-data="{
        showModalFoto: false,
        showPreviewModal: false,
        showCropModal: false,
        imageSrcToCrop: '',
        cropperInstance: null,
        isUploading: false,

        init() {
            // Lock body scroll saat ada modal yang terbuka di semua device
            this.$watch('showCropModal', value => this.toggleBodyScroll(value));
            this.$watch('showModalFoto', value => this.toggleBodyScroll(value));
            this.$watch('showPreviewModal', value => this.toggleBodyScroll(value));
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

                    // Inisialisasi setelah gambar selesai dimuat ke elemen img
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

            // 1. Ekspor data gambar sebagai Base64 WebP (Didukung 100% semua browser & HP)
            const base64Data = canvas.toDataURL('image/webp', 0.90) || canvas.toDataURL('image/jpeg', 0.90);
            const base64Input = document.getElementById('avatarBase64Input');
            if (base64Input) {
                base64Input.value = base64Data;
            }

            // 2. Fallback DataTransfer untuk file input (jika browser mendukung)
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

            // Submit form upload foto
            const form = document.getElementById('avatarFormUpload');
            if (form) {
                form.submit();
            }
        }
     }">

    {{-- Info Card Atas --}}
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/60 shadow-sm flex flex-col sm:flex-row items-center gap-6">
        
        {{-- Avatar dengan Logo Kamera & Drop-up Trigger --}}
        <div class="relative flex-shrink-0">
            <div @click="openFullPreview()"
                 class="w-24 h-24 sm:w-28 sm:h-28 rounded-3xl overflow-hidden shadow-xl border-4 border-indigo-50 bg-gradient-to-tr from-indigo-600 to-indigo-800 text-white flex items-center justify-center font-black text-3xl sm:text-4xl cursor-pointer hover:opacity-95 transition active:scale-95 group"
                 title="Klik untuk melihat foto full">
                @if(!empty($user->foto))
                    <img src="{{ $user->foto_url ?? asset('uploads/' . $user->foto) }}" alt="{{ $user->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                         onerror="if(!this.dataset.retry){this.dataset.retry='1'; this.src='{{ asset('storage/' . $user->foto) }}?v={{ time() }}';}else{this.style.display='none'; this.nextElementSibling.style.display='flex';}">
                    <span style="display: none;" class="w-full h-full flex items-center justify-center font-black text-3xl sm:text-4xl">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                @else
                    <span>{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                @endif
            </div>

            {{-- Tombol Kamera Bulat (Camera Badge Button) --}}
            <button type="button" @click="showModalFoto = true"
                    class="absolute -bottom-1 -right-1 w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-indigo-600 hover:bg-indigo-700 text-white border-2 border-white shadow-lg flex items-center justify-center transition active:scale-90 cursor-pointer group z-10"
                    title="Ubah Foto Profil">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </button>
        </div>

        <div class="text-center sm:text-left flex-1">
            {{-- Nama & Badge Role --}}
            <div class="flex flex-col sm:flex-row items-center justify-center sm:justify-start gap-1.5 sm:gap-2.5 mb-1.5 sm:mb-1">
                <h2 class="text-xl font-bold text-slate-800">{{ $user->name }}</h2>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider
                    @if($user->role == 'admin') bg-purple-100 text-purple-700 border border-purple-200/60
                    @elseif($user->role == 'guru') bg-indigo-100 text-indigo-700 border border-indigo-200/60
                    @else bg-emerald-100 text-emerald-700 border border-emerald-200/60 @endif">
                    {{ $user->role }}
                </span>
            </div>
            <p class="text-xs text-slate-500">{{ $user->email }}</p>
            
            {{-- Detail Identitas --}}
            <div class="flex flex-col sm:flex-row sm:flex-wrap items-center sm:items-start justify-center sm:justify-start gap-1.5 sm:gap-4 mt-3.5 text-xs text-slate-600">
                @if($user->nomor_induk)
                    <span><strong>NIS/NIP:</strong> {{ $user->nomor_induk }}</span>
                @endif
                @if($user->kelas_atau_jabatan)
                    <span><strong>{{ $isSiswa ? 'Kelas' : 'Jabatan' }}:</strong> {{ $user->kelas_atau_jabatan }}</span>
                @endif
                @if($user->telepon)
                    <span><strong>WhatsApp:</strong> {{ $user->telepon }}</span>
                @endif
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
             class="relative z-10 max-w-sm w-full bg-white rounded-3xl overflow-hidden shadow-2xl border border-slate-200/80 p-5 sm:p-6 text-center">
            
            <button type="button" @click="showPreviewModal = false"
                    class="absolute top-4 right-4 z-20 w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-700 flex items-center justify-center transition active:scale-90">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <div class="mb-4 text-left pr-8">
                <h4 class="text-slate-800 font-extrabold text-base">{{ $user->name }}</h4>
                <p class="text-xs text-indigo-600 font-semibold mt-0.5">{{ $user->kelas_atau_jabatan ?? ucfirst($user->role) }}</p>
            </div>

            <div class="w-full aspect-square rounded-2xl overflow-hidden bg-slate-100 border border-slate-200/80 flex items-center justify-center shadow-inner relative">
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
                        class="py-2.5 px-4 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-bold transition active:scale-95">
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
             class="relative z-10 max-w-md w-full bg-white rounded-3xl overflow-hidden shadow-2xl p-4 sm:p-6 space-y-3.5 sm:space-y-4 my-auto">
            
            <div class="text-center pb-2 border-b border-slate-100 relative">
                <h3 class="text-base font-extrabold text-slate-800">Sesuaikan Foto Profil</h3>
                <p class="text-xs text-slate-400 mt-0.5">Geser foto untuk memposisikan & gunakan tombol zoom/putar</p>
                <button type="button" @click="closeCropModal()" class="absolute top-0 right-0 text-slate-400 hover:text-slate-600 p-1 rounded-xl">
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
                        class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition active:scale-90 font-bold text-sm">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"/>
                    </svg>
                </button>

                <button type="button" @click="zoomImage(0.1)" title="Perbesar (+)"
                        class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition active:scale-90 font-bold text-sm">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                </button>

                <button type="button" @click="rotateImage(-90)" title="Putar Kiri (90°)"
                        class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition active:scale-90">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a5 5 0 015 5v2m-15-7l3-3m-3 3l3 3"/>
                    </svg>
                </button>

                <button type="button" @click="rotateImage(90)" title="Putar Kanan (90°)"
                        class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition active:scale-90">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 10H11a5 5 0 00-5 5v2m15-7l-3-3m3 3l-3 3"/>
                    </svg>
                </button>

                <button type="button" @click="resetCropper()" title="Reset Posisi"
                        class="px-3 h-9 sm:h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold flex items-center justify-center transition active:scale-90">
                    Reset
                </button>
            </div>

            {{-- Action Buttons --}}
            <div class="flex gap-2.5 pt-1">
                <button type="button" @click="closeCropModal()" :disabled="isUploading"
                        class="flex-1 py-3 rounded-xl border border-slate-300 text-slate-600 font-bold text-xs hover:bg-slate-50 transition active:scale-95 disabled:opacity-50">
                    Batal
                </button>
                <button type="button" @click="applyCropAndUpload()" :disabled="isUploading"
                        class="flex-1 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md transition active:scale-95 flex items-center justify-center gap-1.5 disabled:opacity-50">
                    <template x-if="isUploading">
                        <div class="flex items-center gap-1.5">
                            <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span>Menyimpan...</span>
                        </div>
                    </template>
                    <template x-if="!isUploading">
                        <div class="flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span>Simpan & Terapkan</span>
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
             class="relative w-full sm:max-w-sm bg-white rounded-t-[32px] sm:rounded-3xl shadow-2xl p-6 z-10 space-y-4">
            
            <div class="w-12 h-1.5 bg-slate-200 rounded-full mx-auto sm:hidden mb-2"></div>

            <div class="text-center pb-2">
                <h3 class="text-base font-extrabold text-slate-800">Foto Profil</h3>
                <p class="text-xs text-slate-400 mt-0.5">Pilih tindakan untuk foto akun Anda</p>
            </div>

            <div class="space-y-2">
                @if(!empty($user->foto))
                    <button type="button" @click="showModalFoto = false; showPreviewModal = true"
                            class="w-full flex items-center justify-center gap-3 py-3.5 px-4 rounded-2xl bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-xs sm:text-sm transition active:scale-95">
                        <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <span>Lihat Foto Lengkap</span>
                    </button>
                @endif

                <button type="button" @click="triggerFileInput()"
                        class="w-full flex items-center justify-center gap-3 py-3.5 px-4 rounded-2xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-xs sm:text-sm transition active:scale-95">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>Ganti Foto Profil</span>
                </button>

                @if(!empty($user->foto))
                    <button type="button" onclick="if(confirm('Yakin ingin menghapus foto profil Anda?')) { document.getElementById('avatarFormDelete').submit(); }"
                            class="w-full flex items-center justify-center gap-3 py-3.5 px-4 rounded-2xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs sm:text-sm transition active:scale-95">
                        <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        <span>Hapus Foto</span>
                    </button>
                @endif

                <button type="button" @click="showModalFoto = false"
                        class="w-full py-3 px-4 rounded-2xl border border-slate-200 text-slate-600 font-bold text-xs sm:text-sm hover:bg-slate-50 transition active:scale-95">
                    Batal
                </button>
            </div>
        </div>
    </div>

    {{-- Form Edit Data Profil --}}
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/60 shadow-sm">
        <div class="mb-6 pb-4 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-800">Informasi Pribadi</h3>
            <p class="text-xs text-slate-400 mt-0.5">Perbarui nama, kontak, nomor induk, atau kelas Anda</p>
        </div>

        <form method="post" action="{{ route('profile.update') }}" class="space-y-4 text-xs">
            @csrf
            @method('patch')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    @error('name')<p class="text-red-500 text-[11px] mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Alamat Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    @error('email')<p class="text-red-500 text-[11px] mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Nomor Induk (NIS / NIP)</label>
                    <input type="text" name="nomor_induk" value="{{ old('nomor_induk', $user->nomor_induk) }}"
                           placeholder="Contoh: 202410101"
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>

                @if($roleName === 'admin')
                    {{-- Khusus Admin Murni: Bisa mengatur jabatannya sendiri --}}
                    <div class="relative" x-data="{ openJabatan: false, selectedJabatan: '{{ old('kelas_atau_jabatan', $user->kelas_atau_jabatan ?? 'Kepala Lab') }}' }" @click.outside="openJabatan = false">
                        <label class="block font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Jabatan / Keterangan
                        </label>
                        <input type="hidden" name="kelas_atau_jabatan" :value="selectedJabatan" required>

                        <button type="button" @click="openJabatan = !openJabatan"
                                class="w-full flex items-center justify-between bg-slate-50 border border-slate-300 rounded-xl p-3 text-sm font-medium transition focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                :class="{'border-indigo-500 ring-2 ring-indigo-500/20 bg-white': openJabatan}">
                            <span class="truncate text-slate-800" x-text="selectedJabatan || 'Pilih Jabatan'"></span>
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
                             class="absolute z-30 w-full mt-1.5 bg-white rounded-xl shadow-xl border border-slate-200/80 py-1 max-h-56 overflow-y-auto"
                             style="display: none;">
                            @foreach($pilihanOpsi as $grup => $items)
                                <div class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400 bg-slate-50/80">{{ $grup }}</div>
                                @foreach($items as $item)
                                    <button type="button" @click="selectedJabatan = '{{ $item }}'; openJabatan = false"
                                            class="w-full text-left px-3.5 py-2 text-xs font-semibold flex items-center justify-between transition hover:bg-indigo-50 hover:text-indigo-600"
                                            :class="{'bg-indigo-50/70 text-indigo-600': selectedJabatan === '{{ $item }}', 'text-slate-700': selectedJabatan !== '{{ $item }}'}">
                                        <span>{{ $item }}</span>
                                        <svg x-show="selectedJabatan === '{{ $item }}'" class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </button>
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                @else
                    {{-- Siswa & Koordinator Lab: Terkunci Read-Only & Teks Label Menjadi KELAS --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block font-semibold text-slate-700 uppercase tracking-wider">
                                {{ $isSiswa ? 'KELAS' : 'JABATAN' }}
                            </label>
                        </div>
                        <div class="relative">
                            <input type="text" value="{{ $user->kelas_atau_jabatan ?: ($isSiswa ? 'Belum Diatur Kelas' : 'Staf') }}" disabled
                                   class="w-full bg-slate-100 border border-slate-200 text-slate-600 font-bold rounded-xl p-3 pr-9 text-sm cursor-not-allowed select-none">
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1 leading-tight">Hubungi Admin lab jika terdapat perubahan {{ $isSiswa ? 'kelas' : 'jabatan' }}.</p>
                    </div>
                @endif

                <div>
                    <label class="block font-semibold text-slate-700 uppercase tracking-wider mb-1.5">No. WhatsApp / HP</label>
                    <input type="text" name="telepon" value="{{ old('telepon', $user->telepon) }}"
                           placeholder="Contoh: 081234567890"
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
            </div>

            <div class="flex items-center justify-end pt-3">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-6 py-2.5 rounded-xl text-sm transition shadow-sm active:scale-95">
                    Simpan Profil & Foto
                </button>
            </div>
        </form>
    </div>

    {{-- Form Ubah Password --}}
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/60 shadow-sm">
        <div class="mb-6 pb-4 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-800">Keamanan & Kata Sandi</h3>
            <p class="text-xs text-slate-400 mt-0.5">Pastikan akun Anda menggunakan kata sandi yang panjang dan aman</p>
        </div>

        <form method="post" action="{{ route('password.update') }}" class="space-y-4 text-xs">
            @csrf
            @method('put')

            <div>
                <label class="block font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Kata Sandi Saat Ini</label>
                <input type="password" name="current_password" required
                        placeholder="Masukan kata sandi saat ini"
                       class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                @error('current_password', 'updatePassword')<p class="text-red-500 text-[11px] mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Kata Sandi Baru</label>
                    <input type="password" name="password" required placeholder="Minimal 6 karakter"
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    @error('password', 'updatePassword')<p class="text-red-500 text-[11px] mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Konfirmasi Kata Sandi Baru</label>
                    <input type="password" name="password_confirmation" required placeholder="Ulangi kata sandi baru"
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
            </div>

            <div class="flex items-center justify-end pt-3">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-6 py-2.5 rounded-xl text-sm transition shadow-sm">
                    Perbarui Kata Sandi
                </button>
            </div>
        </form>
    </div>

</div>

@push('scripts')
<!-- CropperJS JS CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
@endpush

@endsection