<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Auto Cache-Busting (Buang Cache Lama & Download Terbaru di Semua Device) -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <title>{{ config('app.name', 'Inventaris TKJ') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * { font-family: 'Inter', sans-serif; }
        .auth-bg {
            background: linear-gradient(135deg, #4f46e5 0%, #1e1b4b 50%, #0f172a 100%);
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-12px); }
        }
        .float-anim { animation: float 5s ease-in-out infinite; }
    </style>
</head>
<body class="antialiased min-h-screen flex">

    {{-- Left: Branding Panel --}}
    <div class="hidden lg:flex lg:w-1/2 auth-bg flex-col items-center justify-center p-12 relative overflow-hidden">
        {{-- Decorative circles --}}
        <div class="absolute top-0 left-0 w-64 h-64 bg-white/5 rounded-full -translate-x-1/2 -translate-y-1/2"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-white/5 rounded-full translate-x-1/3 translate-y-1/3"></div>
        <div class="absolute top-1/2 right-0 w-48 h-48 bg-indigo-400/20 rounded-full translate-x-1/2 -translate-y-1/2"></div>

        <div class="relative z-10 text-center">
            <div class="float-anim inline-block">
                <div class="w-28 h-28 bg-white/20 backdrop-blur-sm rounded-3xl flex items-center justify-center mx-auto mb-8 shadow-2xl border border-white/20">
                    <svg class="w-14 h-14 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2v-4M9 21H5a2 2 0 01-2-2v-4m0 0h18"/>
                    </svg>
                </div>
            </div>

            <h1 class="text-4xl font-bold text-white mb-3">Inventaris TKJ</h1>
            <p class="text-indigo-200 text-lg mb-8">Sistem Manajemen Barang</p>
            <p class="text-indigo-200 leading-relaxed max-w-xs mx-auto text-sm">
                Laboratorium Teknik Komputer dan Jaringan<br>
                Platform inventaris yang terintegrasi dan mudah dikelola
            </p>

            <div class="mt-10 grid grid-cols-3 gap-4">
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-3 border border-white/10">
                    <p class="text-2xl font-bold text-white">∞</p>
                    <p class="text-indigo-300 text-xs mt-1">Data Barang</p>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-3 border border-white/10">
                    <p class="text-2xl font-bold text-white">
                        <svg class="w-6 h-6 text-emerald-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </p>
                    <p class="text-indigo-300 text-xs mt-1">Real-time</p>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-3 border border-white/10">
                    <p class="text-2xl font-bold text-white">
                        <svg class="w-6 h-6 text-amber-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </p>
                    <p class="text-indigo-300 text-xs mt-1">Aman</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Right: Auth Form Panel --}}
    <div class="w-full lg:w-1/2 flex flex-col items-center justify-center p-8 bg-slate-50">
        <div class="w-full max-w-md">
            {{-- Mobile logo --}}
            <div class="lg:hidden text-center mb-8">
                <div class="w-16 h-16 bg-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2v-4M9 21H5a2 2 0 01-2-2v-4m0 0h18"/>
                    </svg>
                </div>
                <h1 class="text-xl font-bold text-slate-800">Inventaris TKJ</h1>
            </div>

            {{ $slot }}
        </div>
    </div>

</body>
</html>
