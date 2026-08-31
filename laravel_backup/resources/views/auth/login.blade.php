@php
    $isFailedLogin = $errors->any() || session('error') || old('email');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    
    <!-- Auto Cache-Busting (Buang Cache Lama & Download Terbaru di Semua Device) -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <title>Login - LabSystem | Winshark Community</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body, body * {
            -webkit-user-select: none !important;
            -moz-user-select: none !important;
            -ms-user-select: none !important;
            user-select: none !important;
            -webkit-user-drag: none !important;
            user-drag: none !important;
        }

        input, select, textarea, [contenteditable="true"] {
            -webkit-user-select: text !important;
            -moz-user-select: text !important;
            -ms-user-select: text !important;
            user-select: text !important;
        }

        :root {
            --slate-900: #0f172a;
            --slate-800: #1e293b;
            --slate-700: #334155;
            --slate-600: #475569;
            --slate-500: #64748b;
            --slate-400: #94a3b8;
            --slate-200: #e2e8f0;
            --slate-100: #f1f5f9;
            --cyan-400: #38bdf8;
            --teal-700: #0f766e;
            --teal-800: #115e59;
            --blue-900: #0f274a;
            --ease-out: cubic-bezier(0.16, 1, 0.3, 1);
            --shadow-card: 0 16px 40px -12px rgba(15, 23, 42, 0.12);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            width: 100%;
            min-height: 100vh;
            background: radial-gradient(circle at 18% 3%, rgba(56, 201, 239, .08), transparent 24rem),
                        linear-gradient(180deg, #f7fafd 0%, #ffffff 48%, #f7fafc 100%);
            color: var(--slate-900);
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        /* ===== INTRO / LOADING SCREEN ===== */
        .intro-screen {
            position: fixed;
            inset: 0;
            z-index: 20000;
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

        .intro-screen.is-active {
            transform: translate3d(0, 0, 0);
            pointer-events: auto;
        }

        .intro-screen.is-leaving {
            transform: translate3d(0, -100%, 0);
            pointer-events: none;
        }

        .intro-content {
            width: min(320px, 86vw);
            text-align: center;
            animation: introContentIn .72s var(--ease-out) both;
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

        .intro-title {
            margin: 0;
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -1px;
        }

        .intro-title span { color: var(--cyan-400); }

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

        /* ===== SPLIT DUAL BANNER LAYOUT ===== */
        .login-wrapper {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        .login-banner-side {
            display: none;
            width: 50%;
            position: relative;
            background-color: #071938;
            overflow: hidden;
            border-radius: 0;
            box-shadow: 0 20px 45px rgba(15, 23, 42, 0.16);
            isolation: isolate;
        }

        .desktop-dual-banner {
            position: absolute;
            top: 0;
            bottom: 0;
            width: calc(50% + 42px);
            display: flex;
            align-items: center;
            background-size: cover;
            background-repeat: no-repeat;
            overflow: hidden;
        }

        .desktop-banner-left {
            left: 0;
            z-index: 2;
            justify-content: flex-start;
            padding: 40px 85px 40px 44px;
            clip-path: polygon(0 0, 100% 0, calc(100% - 84px) 100%, 0 100%);
            background-image:
                linear-gradient(135deg, rgba(15, 23, 42, 0.92) 0%, rgba(30, 58, 138, 0.88) 60%, rgba(15, 118, 110, 0.82) 100%),
                url("{{ asset('uploads/banner/banner-kiri.webp') }}");
            background-position: center;
        }

        .desktop-banner-right {
            right: 0;
            z-index: 1;
            justify-content: flex-end;
            padding: 42px 40px 42px 92px;
            clip-path: polygon(84px 0, 100% 0, 100% 100%, 0 100%);
            background-image:
                linear-gradient(90deg, rgba(23, 37, 84, 0.98) 0%, rgba(30, 58, 138, 0.84) 45%, rgba(30, 58, 138, 0.55) 100%),
                url("{{ asset('uploads/banner/banner-kanan.webp') }}");
            background-position: center;
        }

        .desktop-center-seam {
            position: absolute;
            z-index: 5;
            top: -10%;
            left: 50%;
            width: 3px;
            height: 120%;
            transform: translateX(-50%) rotate(9.3deg);
            background: linear-gradient(to bottom, rgba(255, 255, 255, 0), rgba(255, 255, 255, 0.22), rgba(255, 255, 255, 0));
            pointer-events: none;
        }

        .desktop-banner-content {
            width: 100%;
            max-width: 315px;
            position: relative;
            z-index: 4;
            color: #ffffff;
            text-align: left;
        }

        .desktop-banner-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #38bdf8;
            margin-bottom: 16px;
        }

        .desktop-banner-left h1 {
            margin: 0 0 20px 0;
            font-size: 23px;
            line-height: 1.35;
            font-weight: 800;
            color: #ffffff;
        }

        .desktop-banner-left h1 span { color: #38bdf8; }

        .desktop-feature-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 22px;
        }

        .desktop-feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(4px);
        }

        .desktop-feature-icon {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            background: linear-gradient(135deg, #0284c7, #0f766e);
            color: #ffffff;
            display: grid;
            place-items: center;
            font-size: 13px;
            flex-shrink: 0;
        }

        .desktop-feature-text {
            font-size: 12.5px;
            font-weight: 700;
            color: #f1f5f9;
        }

        /* RIGHT SIDE FORM */
        .login-form-side {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            background-color: transparent;
        }

        .login-panel { width: min(100%, 420px); }

        .login-card {
            width: 100%;
            padding: 36px 30px;
            border: 1px solid #e2e8f0;
            border-radius: 24px;
            background: #ffffff;
            box-shadow: var(--shadow-card);
        }

        .brand-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 20px;
        }

        .school-logo-wrap {
            width: 48px;
            height: 48px;
            display: grid;
            place-items: center;
            border-radius: 14px;
            background: #ffffff;
            box-shadow: 0 4px 14px rgba(15, 23, 42, .08);
            border: 1px solid #f1f5f9;
            overflow: hidden;
            padding: 2px;
        }

        .school-logo { width: 100%; height: 100%; object-fit: cover; border-radius: 12px; }

        .brand-name {
            color: #0f285c;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.6px;
        }

        .login-heading {
            margin: 0 0 4px;
            color: var(--slate-900);
            font-size: 20px;
            font-weight: 800;
            text-align: center;
        }

        .login-subheading {
            margin: 0 0 24px;
            color: var(--slate-500);
            font-size: 12.5px;
            font-weight: 500;
            text-align: center;
        }

        .form-group { margin-bottom: 16px; }

        .form-label {
            display: block;
            margin: 0 0 6px 2px;
            color: var(--slate-700);
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .input-shell { position: relative; }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--slate-400);
            font-size: 14px;
            pointer-events: none;
        }

        .form-input {
            width: 100%;
            height: 48px;
            padding: 0 42px 0 42px;
            border: 1.5px solid var(--slate-200);
            border-radius: 12px;
            outline: 0;
            background: #f8fafc;
            color: var(--slate-900);
            font-size: 13.5px;
            font-weight: 600;
            transition: all .2s ease;
        }

        .input-shell:focus-within .form-input {
            border-color: var(--teal-700);
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(15, 118, 110, 0.1);
        }

        .input-shell:focus-within .input-icon { color: var(--teal-700); }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 6px;
            transform: translateY(-50%);
            width: 36px;
            height: 36px;
            display: grid;
            place-items: center;
            border: 0;
            background: transparent;
            color: var(--slate-400);
            font-size: 14px;
            cursor: pointer;
        }

        .login-button {
            width: 100%;
            height: 48px;
            margin-top: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: 0;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--teal-700), var(--teal-800));
            color: #ffffff;
            box-shadow: 0 8px 20px rgba(15, 118, 110, .25);
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
        }

        .login-button:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 12px 24px rgba(15, 118, 110, .32);
        }

        .login-button:disabled {
            opacity: 0.85;
            cursor: not-allowed;
            transform: none;
        }

        .demo-accounts {
            margin-top: 24px;
            padding-top: 16px;
            border-top: 1px solid #f1f5f9;
            font-size: 11px;
            color: var(--slate-500);
            text-align: center;
            line-height: 1.6;
        }

        /* ===== TOAST NOTIFICATION ===== */
        .toast-notification {
            position: fixed;
            z-index: 99999;
            display: flex;
            align-items: center;
            gap: 12px;
            background: #ffffff;
            border-left: 4px solid #ef4444;
            padding: 14px 18px;
            border-radius: 14px;
            box-shadow: 0 14px 35px -6px rgba(15, 23, 42, 0.22), 0 0 0 1px rgba(239, 68, 68, 0.15);
            font-size: 13px;
            color: #1e293b;
            max-width: 90vw;
            transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.4s ease;
            pointer-events: auto;
        }

        .toast-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #fef2f2;
            color: #ef4444;
            display: grid;
            place-items: center;
            font-size: 14px;
            flex-shrink: 0;
        }

        .toast-content {
            display: flex;
            flex-direction: column;
            text-align: left;
        }

        .toast-title { font-weight: 700; font-size: 13px; color: #991b1b; }
        .toast-message { font-size: 12px; color: #475569; font-weight: 500; margin-top: 2px; }

        .toast-close {
            background: transparent;
            border: 0;
            color: #94a3b8;
            font-size: 14px;
            cursor: pointer;
            padding: 4px;
            margin-left: 6px;
        }

        .toast-close:hover { color: #334155; }

        @media (max-width: 991px) {
            .toast-notification {
                top: 20px;
                left: 50%;
                width: calc(100% - 32px);
                max-width: 400px;
                transform: translate(-50%, -150%);
                opacity: 0;
            }
            .toast-notification.show {
                transform: translate(-50%, 0);
                opacity: 1;
            }
        }

        @media (min-width: 992px) {
            .login-banner-side { display: block; }
            .login-form-side { width: 50%; }
            .toast-notification {
                bottom: 24px;
                right: 24px;
                width: auto;
                min-width: 320px;
                max-width: 420px;
                transform: translateY(150%);
                opacity: 0;
            }
            .toast-notification.show {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @media (hover: none) or (pointer: coarse) {
            *:hover {
                transform: none !important;
            }
            .login-button:hover:not(:disabled) {
                transform: none !important;
                box-shadow: 0 8px 20px rgba(15, 118, 110, .25) !important;
            }
            a, button, [role="button"], input {
                -webkit-tap-highlight-color: transparent !important;
            }
        }
    </style>
</head>
<body>

    {{-- TOAST NOTIFIKASI ERROR --}}
    @if ($errors->any() || session('error'))
        <div id="toastNotification" class="toast-notification" role="alert">
            <div class="toast-icon">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div class="toast-content">
                <span class="toast-title">Gagal Masuk</span>
                <span class="toast-message">{{ $errors->first() ?? session('error') }}</span>
            </div>
            <button type="button" class="toast-close" onclick="closeToast()" aria-label="Tutup notifikasi">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    {{-- SCREEN LOADING INTRO --}}
    <aside id="introScreen" class="intro-screen is-active no-transition" aria-label="Layar Pembuka">
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

    {{-- MAIN CONTAINER SPLIT LAYOUT --}}
    <div class="login-wrapper">

        <section class="login-banner-side">
            <div class="desktop-dual-banner desktop-banner-left">
                <div class="desktop-banner-content">
                    <div class="desktop-banner-tag">
                        <i class="fa-solid fa-circle-nodes"></i> Winshark
                    </div>
                    <h1>Eksplorasi Praktikum & <span>Manajemen Lab TKJ</span></h1>
                    <div class="desktop-feature-list">
                        <div class="desktop-feature-item">
                            <div class="desktop-feature-icon"><i class="fa-solid fa-qrcode"></i></div>
                            <span class="desktop-feature-text">Quick Scan QR</span>
                        </div>
                        <div class="desktop-feature-item">
                            <div class="desktop-feature-icon"><i class="fa-solid fa-boxes-stacked"></i></div>
                            <span class="desktop-feature-text">Katalog & Pinjam</span>
                        </div>
                        <div class="desktop-feature-item">
                            <div class="desktop-feature-icon"><i class="fa-solid fa-chart-line"></i></div>
                            <span class="desktop-feature-text">Laporan Terintegrasi</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="desktop-center-seam"></div>
            <div class="desktop-dual-banner desktop-banner-right"></div>
        </section>

        <main class="login-form-side">
            <div class="login-panel">
                <div class="login-card">
                    
                    <div class="brand-row md:hidden">
                        <div class="school-logo-wrap">
                            <img src="{{ asset('uploads/Logo/Logo_winshark.webp') }}"
                                 alt="Winshark Logo"
                                 class="school-logo"
                                 onerror="if(!this.dataset.retry){this.dataset.retry='1'; this.src='{{ asset('uploads/Logo/Logo_winshark.jpeg') }}';}else{this.onerror=null; this.src='{{ asset('uploads/Logo/Logo banner.jpeg') }}';}">
                        </div>
                        <div class="brand-name">
                            Lab<span style="color: #0f766e;">System</span>
                        </div>
                    </div>

                    <h2 class="login-heading">Selamat Datang</h2>
                    <p class="login-subheading">Masuk untuk meminjam alat & mengelola laboratorium</p>

                    <form action="{{ route('login') }}" method="POST" id="formLogin">
                        @csrf

                        <div class="form-group">
                            <label class="form-label" for="email">Username / Email / NIS</label>
                            <div class="input-shell">
                                <i class="fa-solid fa-user input-icon"></i>
                                <input type="text" id="email" name="email" value="{{ old('email') }}"
                                       class="form-input" placeholder="Masukkan username atau email" required autofocus>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="password">Kata Sandi</label>
                            <div class="input-shell">
                                <i class="fa-solid fa-lock input-icon"></i>
                                <input type="password" id="password" name="password"
                                       class="form-input" placeholder="Masukkan kata sandi" required>
                                <button type="button" class="toggle-password" id="btnTogglePassword" aria-label="Tampilkan kata sandi">
                                    <i class="fa-solid fa-eye" id="eyeIcon"></i>
                                </button>
                            </div>
                        </div>

                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; font-size: 12px; color: var(--slate-600);">
                            <label style="display: flex; align-items: center; gap: 6px; cursor: pointer;">
                                <input type="checkbox" name="remember" style="accent-color: var(--teal-700);">
                                <span>Ingat Saya</span>
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" style="color: #0f766e; font-weight: 600; text-decoration: none;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                                    Lupa kata sandi?
                                </a>
                            @endif
                        </div>

                        <button type="submit" class="login-button" id="btnSubmit">
                            <i class="fa-solid fa-arrow-right-to-bracket" id="btnIcon"></i>
                            <span id="btnText">Masuk ke Sistem</span>
                        </button>
                    </form>

                    <div style="text-align: center; font-size: 12px; color: var(--slate-600); margin-top: 18px;">
                        Belum memiliki akun? <a href="{{ route('register') }}" style="color: #0f766e; font-weight: 700; text-decoration: none;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">Daftar di sini</a>
                    </div>
                </div>
            </div>
        </main>

    </div>

    <script>
        function closeToast() {
            const toast = document.getElementById('toastNotification');
            if (toast) {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 400);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const toast = document.getElementById('toastNotification');
            const intro = document.getElementById('introScreen');

            if (intro) {
                const transitionKey = 'page_transition';
                sessionStorage.removeItem(transitionKey);
                sessionStorage.removeItem('dashboard_intro');

                // Force reflow
                void intro.offsetHeight;
                intro.classList.remove('no-transition');

                // Intro screen menunggu sebentar lalu meluncur kembali ke atas (slide up)
                setTimeout(function() {
                    intro.classList.remove('is-active');
                    intro.classList.add('is-leaving');
                    setTimeout(() => intro.classList.remove('is-leaving'), 1250);
                }, 550);

                // Tampilkan Toast error tepat saat intro mulai meluncur ke atas
                if (toast) {
                    setTimeout(() => toast.classList.add('show'), 650);
                    setTimeout(() => closeToast(), 5500);
                }

                const transitionLinks = document.querySelectorAll('a[href*="register"], a[href*="forgot-password"]');
                transitionLinks.forEach(link => {
                    link.addEventListener('click', function(e) {
                        const href = this.getAttribute('href');
                        if (!href || href.startsWith('#') || href.startsWith('javascript:')) return;

                        e.preventDefault();
                        sessionStorage.setItem(transitionKey, 'true');

                        intro.classList.remove('is-leaving', 'no-transition');
                        intro.classList.add('is-active');

                        setTimeout(function() {
                            window.location.href = href;
                        }, 1150);
                    });
                });
            } else if (toast) {
                setTimeout(() => toast.classList.add('show'), 150);
                setTimeout(() => closeToast(), 5000);
            }
        });

        const btnToggle = document.getElementById('btnTogglePassword');
        const inputPass = document.getElementById('password');
        const eyeIcon   = document.getElementById('eyeIcon');

        if (btnToggle && inputPass && eyeIcon) {
            btnToggle.addEventListener('click', function() {
                const isPass = inputPass.type === 'password';
                inputPass.type = isPass ? 'text' : 'password';
                eyeIcon.className = isPass ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye';
            });
        }

        const formLogin = document.getElementById('formLogin');
        const btnSubmit = document.getElementById('btnSubmit');
        const btnIcon   = document.getElementById('btnIcon');
        const btnText   = document.getElementById('btnText');

        if (formLogin && btnSubmit && btnIcon && btnText) {
            let isSubmitting = false;

            formLogin.addEventListener('submit', function(e) {
                if (isSubmitting) return;
                e.preventDefault();
                isSubmitting = true;

                // 1. Tampilkan spinner animasi pada tombol masuk
                btnSubmit.disabled = true;
                btnIcon.className = 'fa-solid fa-circle-notch fa-spin';
                btnText.textContent = 'Memverifikasi...';

                // 2. Set flag di sessionStorage agar dashboard menampilkan transisi slide out
                sessionStorage.setItem('dashboard_intro', 'true');

                // 3. Setelah spinner beberapa saat (600ms), munculkan intro screen loading
                setTimeout(function() {
                    const intro = document.getElementById('introScreen');
                    if (intro) {
                        intro.classList.remove('is-leaving', 'no-transition');
                        intro.classList.add('is-active');
                    }

                    // 4. Setelah layar tertutup intro screen, kirim form ke server
                    setTimeout(function() {
                        formLogin.submit();
                    }, 800);
                }, 600);
            });
        }
    </script>
</body>
</html>