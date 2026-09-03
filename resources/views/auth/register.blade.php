<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Daftar Akun - LabSystem | Winshark Community</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
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
            -webkit-user-select: none !important;
            -moz-user-select: none !important;
            -ms-user-select: none !important;
            user-select: none !important;
            -webkit-user-drag: none !important;
            user-drag: none !important;
        }

        body * {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            -webkit-user-drag: none;
            user-drag: none;
        }

        input, select, textarea, [contenteditable="true"], .custom-dropdown-search-input {
            -webkit-user-select: text !important;
            -moz-user-select: text !important;
            -ms-user-select: text !important;
            user-select: text !important;
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

        /* ===== REGISTER SECTION ===== */
        .register-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 16px;
        }

        .register-panel { width: min(100%, 480px); }

        .register-card {
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

        .register-heading {
            margin: 0 0 4px;
            color: var(--slate-900);
            font-size: 20px;
            font-weight: 800;
            text-align: center;
        }

        .register-subheading {
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
            z-index: 2;
        }

        .form-input {
            width: 100%;
            height: 46px;
            padding: 0 15px 0 42px;
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

        /* ===== CUSTOM SEARCHABLE DROPDOWN ===== */
        .custom-dropdown-container {
            position: relative;
            width: 100%;
        }

        .custom-dropdown-trigger {
            width: 100%;
            height: 46px;
            padding: 0 38px 0 42px;
            border: 1.5px solid var(--slate-200);
            border-radius: 12px;
            background: #f8fafc;
            color: var(--slate-900);
            font-size: 13.5px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            text-align: left;
            transition: all .25s ease;
            user-select: none;
        }

        .custom-dropdown-container.active .custom-dropdown-trigger {
            border-color: var(--teal-700);
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(15, 118, 110, 0.1);
        }

        .custom-dropdown-trigger .placeholder {
            color: var(--slate-400);
            font-weight: 500;
        }

        .custom-dropdown-arrow {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--slate-400);
            font-size: 12px;
            transition: transform .4s cubic-bezier(0.16, 1, 0.3, 1);
            pointer-events: none;
        }

        .custom-dropdown-container.active .custom-dropdown-arrow {
            transform: translateY(-50%) rotate(180deg);
            color: var(--teal-700);
        }

        .custom-dropdown-menu {
            position: absolute;
            top: calc(100% + 6px);
            left: 0;
            right: 0;
            background: #ffffff;
            border: 1.5px solid var(--slate-200);
            border-radius: 14px;
            box-shadow: 0 20px 40px -10px rgba(15, 23, 42, 0.22);
            z-index: 10000;
            overflow: hidden;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px) scale(0.97);
            pointer-events: none;
            transform-origin: top center;
            transition: opacity 0.35s cubic-bezier(0.16, 1, 0.3, 1),
                        transform 0.35s cubic-bezier(0.16, 1, 0.3, 1),
                        visibility 0.35s;
        }

        .custom-dropdown-container.active .custom-dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0) scale(1);
            pointer-events: auto;
        }

        .custom-dropdown-search-wrapper {
            padding: 8px 10px;
            background: #f8fafc;
            border-bottom: 1px solid var(--slate-200);
            position: relative;
        }

        .custom-dropdown-search-input {
            width: 100%;
            height: 36px;
            padding: 0 12px 0 32px;
            border: 1.5px solid var(--slate-200);
            border-radius: 8px;
            font-size: 13px;
            font-family: inherit;
            outline: none;
            background: #ffffff;
            transition: all .2s;
        }

        .custom-dropdown-search-input:focus {
            border-color: var(--teal-700);
            box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.1);
        }

        .custom-dropdown-search-icon {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 12px;
            color: var(--slate-400);
        }

        .custom-dropdown-list {
            max-height: 190px;
            overflow-y: auto;
            padding: 6px;
            list-style: none;
        }

        .custom-dropdown-item {
            padding: 9px 14px;
            font-size: 13px;
            font-weight: 600;
            color: var(--slate-700);
            border-radius: 8px;
            cursor: pointer;
            transition: all .2s cubic-bezier(0.16, 1, 0.3, 1);
            margin-bottom: 2px;
        }

        .custom-dropdown-item:hover {
            background-color: var(--teal-700);
            color: #ffffff;
            transform: translateX(4px);
        }

        .custom-dropdown-item.selected {
            background-color: #f1f5f9;
            color: var(--teal-800);
            font-weight: 700;
        }

        .custom-dropdown-empty {
            padding: 14px;
            text-align: center;
            font-size: 12px;
            color: var(--slate-400);
            font-weight: 500;
            display: none;
        }

        .register-button {
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

        .register-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 24px rgba(15, 118, 110, .32);
        }

        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
            padding: 12px 14px;
            border-radius: 12px;
            font-size: 12px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .login-link {
            text-align: center;
            font-size: 12px;
            color: var(--slate-600);
            margin-top: 18px;
        }

        .login-link a {
            color: var(--teal-700);
            text-decoration: none;
            font-weight: 700;
        }

        .login-link a:hover { text-decoration: underline; }
    </style>
</head>
<body>

    {{-- SCREEN LOADING INTRO ANIMATION --}}
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

    {{-- REGISTER FORM CONTAINER --}}
    <main class="register-section">
        <div class="register-panel">
            <div class="register-card">
                
                {{-- Logo & Brand --}}
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

                <h2 class="register-heading">Daftar Akun Baru</h2>
                <p class="register-subheading">Lengkapi formulir pendaftaran untuk meminjam alat lab</p>

                {{-- Alert Error --}}
                @if ($errors->any())
                    <div class="alert-error">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                {{-- Form Register --}}
                <form action="{{ route('register') }}" method="POST" id="formRegister">
                    @csrf

                    <div class="form-group">
                        <label class="form-label" for="name">Nama Lengkap</label>
                        <div class="input-shell">
                            <i class="fa-solid fa-user input-icon"></i>
                            <input type="text" id="name" name="name" value="{{ old('name') }}"
                                   class="form-input" placeholder="Masukkan nama lengkap Anda" required autofocus>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="email">Alamat Email</label>
                        <div class="input-shell">
                            <i class="fa-solid fa-envelope input-icon"></i>
                            <input type="email" id="email" name="email" value="{{ old('email') }}"
                                   class="form-input" placeholder="Masukkan alamat email aktif" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="nomor_induk">NIS (Nomor Induk Siswa)</label>
                        <div class="input-shell">
                            <i class="fa-solid fa-id-card input-icon"></i>
                            <input type="text" id="nomor_induk" name="nomor_induk" value="{{ old('nomor_induk') }}"
                                   class="form-input" placeholder="Masukkan Nomor Induk Siswa" required>
                        </div>
                    </div>

                    {{-- SEARCHABLE DROPDOWN KELAS --}}
                    <div class="form-group">
                        <label class="form-label" for="kelas_input_display">Kelas</label>
                        <div class="custom-dropdown-container" id="kelasDropdown">
                            <i class="fa-solid fa-graduation-cap input-icon"></i>
                            
                            <input type="hidden" name="kelas_atau_jabatan" id="kelas_atau_jabatan" value="{{ old('kelas_atau_jabatan') }}" required>
                            
                            <div class="custom-dropdown-trigger" id="dropdownTrigger">
                                <span id="selectedKelasText" class="{{ old('kelas_atau_jabatan') ? '' : 'placeholder' }}">
                                    {{ old('kelas_atau_jabatan') ?: 'Pilih atau cari kelas...' }}
                                </span>
                                <i class="fa-solid fa-chevron-down custom-dropdown-arrow"></i>
                            </div>

                            <div class="custom-dropdown-menu" id="dropdownMenu">
                                <div class="custom-dropdown-search-wrapper">
                                    <i class="fa-solid fa-magnifying-glass custom-dropdown-search-icon"></i>
                                    <input type="text" id="searchKelasInput" class="custom-dropdown-search-input" placeholder="Ketik nama kelas..." autocomplete="off">
                                </div>
                                <ul class="custom-dropdown-list" id="kelasList">
                                    @php
                                        $listKelas = isset($daftarKelas) && count($daftarKelas) > 0 ? $daftarKelas : ['X TKJ 1', 'X TKJ 2', 'XI TKJ 1', 'XI TKJ 2', 'XII TKJ 1', 'XII TKJ 2'];
                                    @endphp
                                    @foreach($listKelas as $item)
                                        @php
                                            $kelasName = is_object($item) ? ($item->nama_kelas ?? $item->name ?? $item->kelas) : $item;
                                        @endphp
                                        <li class="custom-dropdown-item {{ old('kelas_atau_jabatan') == $kelasName ? 'selected' : '' }}" data-value="{{ $kelasName }}">
                                            {{ $kelasName }}
                                        </li>
                                    @endforeach
                                </ul>
                                <div class="custom-dropdown-empty" id="kelasEmpty">Kelas tidak ditemukan</div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="telepon">No. WhatsApp / HP</label>
                        <div class="input-shell">
                            <i class="fa-solid fa-phone input-icon"></i>
                            <input type="text" id="telepon" name="telepon" value="{{ old('telepon') }}"
                                   class="form-input" placeholder="Contoh: 081234567890" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password">Kata Sandi</label>
                        <div class="input-shell">
                            <i class="fa-solid fa-lock input-icon"></i>
                            <input type="password" id="password" name="password"
                                   class="form-input" placeholder="Minimal 8 karakter" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password_confirmation">Konfirmasi Kata Sandi</label>
                        <div class="input-shell">
                            <i class="fa-solid fa-lock input-icon"></i>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                   class="form-input" placeholder="Ulangi kata sandi" required>
                        </div>
                    </div>

                    <button type="submit" class="register-button">
                        <i class="fa-solid fa-user-plus"></i>
                        <span>Daftar Akun Baru</span>
                    </button>
                </form>

                <div class="login-link">
                    Sudah memiliki akun? <a href="{{ route('login') }}">Masuk di sini</a>
                </div>

            </div>
        </div>
    </main>

    <script>
        // Custom Dropdown Logic
        document.addEventListener('DOMContentLoaded', function() {
            const dropdown = document.getElementById('kelasDropdown');
            const trigger = document.getElementById('dropdownTrigger');
            const hiddenInput = document.getElementById('kelas_atau_jabatan');
            const selectedText = document.getElementById('selectedKelasText');
            const searchInput = document.getElementById('searchKelasInput');
            const items = document.querySelectorAll('.custom-dropdown-item');
            const emptyNotice = document.getElementById('kelasEmpty');

            if (trigger && dropdown) {
                trigger.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const isActive = dropdown.classList.toggle('active');
                    if (isActive && searchInput) {
                        searchInput.value = '';
                        filterList('');
                        setTimeout(() => searchInput.focus(), 100);
                    }
                });

                items.forEach(item => {
                    item.addEventListener('click', function(e) {
                        e.stopPropagation();
                        const val = this.getAttribute('data-value');
                        hiddenInput.value = val;
                        selectedText.textContent = val;
                        selectedText.classList.remove('placeholder');

                        items.forEach(i => i.classList.remove('selected'));
                        this.classList.add('selected');

                        dropdown.classList.remove('active');
                    });
                });

                if (searchInput) {
                    searchInput.addEventListener('input', function() {
                        filterList(this.value.toLowerCase().trim());
                    });
                }

                function filterList(keyword) {
                    let visibleCount = 0;
                    items.forEach(item => {
                        const text = item.textContent.toLowerCase();
                        if (text.includes(keyword)) {
                            item.style.display = 'block';
                            visibleCount++;
                        } else {
                            item.style.display = 'none';
                        }
                    });
                    if (emptyNotice) {
                        emptyNotice.style.display = visibleCount === 0 ? 'block' : 'none';
                    }
                }

                document.addEventListener('click', function(e) {
                    if (!dropdown.contains(e.target)) {
                        dropdown.classList.remove('active');
                    }
                });
            }
        });

        // Intro Screen Transition & Intercept
        document.addEventListener('DOMContentLoaded', function() {
            const intro = document.getElementById('introScreen');
            if (!intro) return;

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

            const transitionLinks = document.querySelectorAll('a[href*="register"], a[href*="login"], a[href*="forgot-password"]');
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
        });
    </script>
</body>
</html>