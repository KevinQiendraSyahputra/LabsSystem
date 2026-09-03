<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Lupa Kata Sandi - LabSystem | Winshark Community</title>

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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 16px;
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

        /* ===== PANEL & CARD ===== */
        .forgot-panel { width: min(100%, 430px); }

        .forgot-card {
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

        .forgot-heading {
            margin: 0 0 6px;
            color: var(--slate-900);
            font-size: 20px;
            font-weight: 800;
            text-align: center;
        }

        .forgot-subheading {
            margin: 0 0 24px;
            color: var(--slate-500);
            font-size: 12.5px;
            font-weight: 500;
            line-height: 1.5;
            text-align: center;
        }

        .form-group { margin-bottom: 16px; }

        .form-label {
            display: block;
            margin: 0 0 6px 2px;
            color: var(--slate-700);
            font-size: 11px;
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
            padding: 0 16px 0 42px;
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

        .btn-submit {
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

        .btn-submit:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 12px 24px rgba(15, 118, 110, .32);
        }

        .btn-submit:disabled {
            opacity: 0.85;
            cursor: not-allowed;
            transform: none;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: var(--slate-600);
        }

        .back-link a {
            color: #0f766e;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .back-link a:hover { text-decoration: underline; }

        /* ===== TOAST NOTIFICATION ===== */
        .toast-notification {
            position: fixed;
            z-index: 99999;
            display: flex;
            align-items: center;
            gap: 12px;
            background: #ffffff;
            padding: 14px 18px;
            border-radius: 14px;
            box-shadow: 0 14px 35px -6px rgba(15, 23, 42, 0.22);
            font-size: 13px;
            color: #1e293b;
            max-width: 90vw;
            transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.4s ease;
        }

        .toast-notification.error {
            border-left: 4px solid #ef4444;
            box-shadow: 0 14px 35px -6px rgba(15, 23, 42, 0.22), 0 0 0 1px rgba(239, 68, 68, 0.15);
        }

        .toast-notification.success {
            border-left: 4px solid #10b981;
            box-shadow: 0 14px 35px -6px rgba(15, 23, 42, 0.22), 0 0 0 1px rgba(16, 185, 129, 0.15);
        }

        .toast-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            font-size: 14px;
            flex-shrink: 0;
        }

        .toast-notification.error .toast-icon {
            background: #fef2f2;
            color: #ef4444;
        }

        .toast-notification.success .toast-icon {
            background: #ecfdf5;
            color: #10b981;
        }

        .toast-content {
            display: flex;
            flex-direction: column;
            text-align: left;
        }

        .toast-title { font-weight: 700; font-size: 13px; }
        .toast-notification.error .toast-title { color: #991b1b; }
        .toast-notification.success .toast-title { color: #065f46; }

        .toast-message {
            font-size: 12px;
            color: #475569;
            font-weight: 500;
            margin-top: 2px;
        }

        .toast-close {
            background: transparent;
            border: 0;
            color: #94a3b8;
            font-size: 14px;
            cursor: pointer;
            padding: 4px;
            margin-left: 6px;
        }

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
    </style>
</head>
<body>

    {{-- SCREEN LOADING INTRO --}}
    <aside id="introScreen" class="intro-screen" aria-label="Layar Pembuka">
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

    {{-- TOAST ERROR --}}
    @if ($errors->any())
        <div id="toastNotification" class="toast-notification error" role="alert">
            <div class="toast-icon">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div class="toast-content">
                <span class="toast-title">Validasi Gagal</span>
                <span class="toast-message">{{ $errors->first() }}</span>
            </div>
            <button type="button" class="toast-close" onclick="closeToast()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    {{-- TOAST SUKSES --}}
    @if (session('status'))
        <div id="toastNotification" class="toast-notification success" role="alert">
            <div class="toast-icon">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="toast-content">
                <span class="toast-title">Permintaan Terkirim</span>
                <span class="toast-message">{{ session('status') }}</span>
            </div>
            <button type="button" class="toast-close" onclick="closeToast()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    <main class="forgot-panel">
        <div class="forgot-card">

            {{-- Brand Logo --}}
            <div class="brand-row">
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

            <h2 class="forgot-heading">Lupa Kata Sandi?</h2>
            <p class="forgot-subheading">
                Masukkan email akun dan nomor WhatsApp Anda. Admin akan memeriksa dan mengirimkan kata sandi baru ke WhatsApp Anda.
            </p>

            <form action="{{ route('password.email') }}" method="POST" id="formForgot">
                @csrf

                {{-- Input Email --}}
                <div class="form-group">
                    <label class="form-label" for="email">Alamat Email Terdaftar</label>
                    <div class="input-shell">
                        <i class="fa-solid fa-envelope input-icon"></i>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                               class="form-input" placeholder="nama@email.com" required autofocus>
                    </div>
                </div>

                {{-- Input No WhatsApp --}}
                <div class="form-group">
                    <label class="form-label" for="whatsapp">Nomor WhatsApp Aktif</label>
                    <div class="input-shell">
                        <i class="fa-brands fa-whatsapp input-icon" style="font-size: 16px;"></i>
                        <input type="tel" id="whatsapp" name="whatsapp" value="{{ old('whatsapp') }}"
                               class="form-input" placeholder="Contoh: 081234567890" required>
                    </div>
                </div>

                <button type="submit" class="btn-submit" id="btnSubmit">
                    <i class="fa-solid fa-paper-plane" id="btnIcon"></i>
                    <span id="btnText">Kirim Permintaan Reset</span>
                </button>
            </form>

            <div class="back-link">
                <a href="{{ route('login') }}">
                    <i class="fa-solid fa-arrow-left"></i> Kembali ke Halaman Login
                </a>
            </div>

        </div>
    </main>

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
            if (toast) {
                setTimeout(() => toast.classList.add('show'), 100);
                setTimeout(() => closeToast(), 5500);
            }

            const intro = document.getElementById('introScreen');
            if (intro) {
                const transitionKey = 'page_transition';
                sessionStorage.removeItem(transitionKey);

                intro.classList.add('no-transition', 'is-active');
                void intro.offsetHeight;
                intro.classList.remove('no-transition');

                setTimeout(function() {
                    intro.classList.remove('is-active');
                    intro.classList.add('is-leaving');
                    setTimeout(() => intro.classList.remove('is-leaving'), 1250);
                }, 800);

                const transitionLinks = document.querySelectorAll('a[href*="register"], a[href*="login"]');
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
            }

            const formForgot = document.getElementById('formForgot');
            const btnSubmit  = document.getElementById('btnSubmit');
            const btnIcon    = document.getElementById('btnIcon');
            const btnText    = document.getElementById('btnText');

            if (formForgot && btnSubmit) {
                formForgot.addEventListener('submit', function() {
                    btnSubmit.disabled = true;
                    btnIcon.className = 'fa-solid fa-circle-notch fa-spin';
                    btnText.textContent = 'Mengirim Permintaan...';
                });
            }
        });
    </script>
</body>
</html>