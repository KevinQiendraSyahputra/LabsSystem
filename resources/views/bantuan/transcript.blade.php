<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transkrip Layanan CS — {{ $chat->session_code }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; padding: 0 !important; }
            .print-card { box-shadow: none !important; border: 1px solid #e2e8f0 !important; }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased min-h-screen p-4 md:p-8 flex flex-col items-center">

    <div class="w-full max-w-4xl space-y-6">
        
        <!-- Header & Action Bar -->
        <div class="no-print flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-white border border-slate-200 rounded-2xl p-4 md:p-6 shadow-sm">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                        {{ strtoupper($chat->status === 'closed' ? 'Sesi Selesai' : 'Sesi Aktif') }}
                    </span>
                    <span class="text-xs font-mono text-slate-500">{{ $chat->session_code }}</span>
                </div>
                <h1 class="text-lg md:text-xl font-bold text-slate-900">Transkrip Resmi Customer Service</h1>
                <p class="text-xs text-slate-500">Arsip riwayat percakapan layanan konsultasi laboratorium</p>
            </div>
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <a href="{{ route('bantuan.transcript', ['sessionCode' => $chat->session_code, 'download' => 'txt']) }}" class="inline-flex items-center justify-center gap-2 px-3 py-2 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-300 rounded-lg transition-colors flex-1 sm:flex-initial">
                    <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    <span>Unduh TXT</span>
                </a>
                <button onclick="window.print()" class="inline-flex items-center justify-center gap-2 px-3 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors shadow-sm flex-1 sm:flex-initial">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    <span>Cetak PDF</span>
                </button>
            </div>
        </div>

        <!-- Detail Metadata Kartu -->
        <div class="print-card bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-6">
            <div class="border-b border-slate-100 pb-4">
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-500">Informasi Sesi Konsultasi</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs md:text-sm">
                <div class="bg-slate-50 border border-slate-100 rounded-xl p-4 space-y-2">
                    <p class="text-xs font-semibold text-slate-500 uppercase">Data Pengguna</p>
                    <div class="space-y-1">
                        <p><span class="font-semibold text-slate-700">Nama:</span> {{ $chat->user_name ?: ($chat->user->name ?? 'Pengguna') }}</p>
                        <p><span class="font-semibold text-slate-700">Peran:</span> {{ strtoupper($chat->user_role ?: ($chat->user->role ?? 'Siswa')) }}</p>
                        <p><span class="font-semibold text-slate-700">Email:</span> {{ $chat->user_email ?: ($chat->user->email ?? '-') }}</p>
                    </div>
                </div>

                <div class="bg-slate-50 border border-slate-100 rounded-xl p-4 space-y-2">
                    <p class="text-xs font-semibold text-slate-500 uppercase">Waktu & Sesi</p>
                    <div class="space-y-1">
                        <p><span class="font-semibold text-slate-700">Kode Sesi:</span> <code class="font-mono bg-slate-200 px-1.5 py-0.5 rounded">{{ $chat->session_code }}</code></p>
                        <p><span class="font-semibold text-slate-700">Waktu Mulai:</span> {{ $chat->created_at ? $chat->created_at->format('d F Y, H:i:s') . ' WIB' : '-' }}</p>
                        <p><span class="font-semibold text-slate-700">Waktu Ditutup:</span> {{ $chat->updated_at ? $chat->updated_at->format('d F Y, H:i:s') . ' WIB' : '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Percakapan / Log Chat -->
            <div class="space-y-4 pt-2">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                    <h3 class="text-sm font-bold text-slate-800">Riwayat Percakapan ({{ $messages->count() }} Pesan)</h3>
                    <span class="text-xs text-slate-400">Diurutkan secara kronologis</span>
                </div>

                <div class="space-y-3 pt-2">
                    @forelse($messages as $msg)
                        @if($msg->sender === 'system')
                            <div class="flex justify-center my-3">
                                <div class="bg-slate-100 text-slate-600 text-xs px-3 py-1.5 rounded-full border border-slate-200 text-center max-w-xl">
                                    <span class="font-semibold">Sistem:</span> {{ $msg->message }}
                                    <span class="text-[10px] text-slate-400 ml-1 font-mono">{{ $msg->created_at ? $msg->created_at->format('H:i') : '' }}</span>
                                </div>
                            </div>
                        @elseif($msg->sender === 'user')
                            <div class="flex flex-col items-end">
                                <div class="max-w-[85%] md:max-w-[75%] bg-blue-600 text-white p-3 md:p-4 rounded-2xl rounded-tr-none shadow-sm space-y-1">
                                    <div class="flex items-center justify-between gap-4 text-[10px] text-blue-100 font-semibold border-b border-blue-500/40 pb-1">
                                        <span>{{ $chat->user_name ?: 'Pengguna' }}</span>
                                        <span class="font-mono">{{ $msg->created_at ? $msg->created_at->format('H:i') : '' }}</span>
                                    </div>
                                    <p class="text-xs md:text-sm whitespace-pre-wrap leading-relaxed text-blue-50">{{ $msg->message }}</p>
                                </div>
                            </div>
                        @elseif($msg->sender === 'admin')
                            <div class="flex flex-col items-start">
                                <div class="max-w-[85%] md:max-w-[75%] bg-slate-100 text-slate-800 border border-slate-200 p-3 md:p-4 rounded-2xl rounded-tl-none shadow-sm space-y-1">
                                    <div class="flex items-center justify-between gap-4 text-[10px] text-slate-500 font-semibold border-b border-slate-200 pb-1">
                                        <span class="text-indigo-600 font-bold">Admin Customer Service</span>
                                        <span class="font-mono text-slate-400">{{ $msg->created_at ? $msg->created_at->format('H:i') : '' }}</span>
                                    </div>
                                    <p class="text-xs md:text-sm whitespace-pre-wrap leading-relaxed text-slate-700">{{ $msg->message }}</p>
                                </div>
                            </div>
                        @endif
                    @empty
                        <div class="text-center py-8 text-slate-400 text-xs">
                            Tidak ada catatan pesan dalam sesi ini.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="border-t border-slate-100 pt-4 text-center text-xs text-slate-400">
                Dokumen transkrip ini dihasilkan otomatis oleh Sistem Inventaris & Laboratorium TKJ.
            </div>
        </div>

    </div>

</body>
</html>
