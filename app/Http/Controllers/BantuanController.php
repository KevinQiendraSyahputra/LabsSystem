<?php

namespace App\Http\Controllers;

use App\Models\LiveChat;
use App\Models\LiveChatMessage;
use App\Services\DiscordService;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BantuanController extends Controller
{
    /**
     * Dapatkan status jam operasional layanan Customer Service.
     * Jam operasional: Senin – Jumat, 08.00 – 17.00 WIB.
     * Sabtu & Minggu: Libur / Tutup.
     */
    public static function getOperationalStatus(): array
    {
        $now = now()->setTimezone('Asia/Jakarta');
        $dayOfWeek = (int) $now->dayOfWeekIso; // 1 (Senin) .. 7 (Minggu)
        $currentHourMin = $now->format('H:i');

        // Hari kerja: Senin (1) s/d Jumat (5)
        $isWeekday = $dayOfWeek >= 1 && $dayOfWeek <= 5;
        // Rentang jam: 08:00 sampai 17:00
        $isOpenTime = $currentHourMin >= '08:00' && $currentHourMin < '17:00';
        $isOpen = $isWeekday && $isOpenTime;

        $statusText = $isOpen ? 'Buka' : 'Tutup';
        
        if ($isOpen) {
            $message = 'Layanan Customer Service sedang beroperasi (08.00 – 17.00 WIB).';
        } elseif (!$isWeekday) {
            $message = 'Layanan Customer Service saat ini libur di akhir pekan (Buka kembali Senin pukul 08.00 WIB).';
        } else {
            $message = 'Layanan Customer Service saat ini di luar jam operasional (Buka pukul 08.00 – 17.00 WIB).';
        }

        return [
            'is_open'     => $isOpen,
            'status_text' => $statusText,
            'message'     => $message,
            'current_time'=> $currentHourMin . ' WIB',
            'schedule'    => [
                'senin_kamis' => '08.00 – 17.00 WIB',
                'jumat'       => '08.00 – 17.00 WIB',
                'weekend'     => 'Libur',
            ],
        ];
    }

    /**
     * Tampilkan halaman Bantuan / Customer Service Chatbot.
     */
    public function index()
    {
        $activeSession = null;
        $initialMessages = [];
        $activeAdminName = null;
        $operationalStatus = self::getOperationalStatus();

        if (Auth::check()) {
            $activeSession = LiveChat::where('user_id', Auth::id())
                ->where('status', 'active')
                ->latest()
                ->first();

            if ($activeSession) {
                // Auto-sync pesan Discord terbaru sebelum render tampilan
                try {
                    DiscordService::processIncomingDiscordUpdates($activeSession->session_code);
                } catch (\Throwable $e) {}

                $hasAdminNameCol = \Illuminate\Support\Facades\Schema::hasColumn('live_chats', 'admin_name');
                $hasSenderNameCol = \Illuminate\Support\Facades\Schema::hasColumn('live_chat_messages', 'sender_name');
                $activeAdminName = $hasAdminNameCol ? $activeSession->admin_name : null;
                $initialMessages = LiveChatMessage::where('live_chat_id', $activeSession->id)
                    ->orderBy('id', 'asc')
                    ->get()
                    ->map(function ($m) use ($hasSenderNameCol) {
                        return [
                            'id'          => $m->id,
                            'sender'      => $m->sender,
                            'sender_name' => $hasSenderNameCol ? $m->sender_name : null,
                            'text'        => $m->sender === 'user' ? htmlspecialchars($m->message) : $m->message,
                            'time'        => $m->created_at->format('H:i'),
                            'is_system'   => $m->sender === 'system',
                            'failed'      => false,
                        ];
                    });
            }
        }

        return view('bantuan.index', [
            'initialCsSession'  => $activeSession ? $activeSession->session_code : null,
            'initialAdminName'  => $activeAdminName,
            'initialMessages'   => $initialMessages,
            'operationalStatus' => $operationalStatus,
        ]);
    }

    /**
     * Memulai Sesi Live Chat dengan Customer Service (Admin via Telegram & Discord)
     */
    public function startCsSession(Request $request)
    {
        $operational = self::getOperationalStatus();
        if (!$operational['is_open']) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Layanan Customer Service saat ini sedang di luar jam operasional (08.00 – 17.00 WIB, Senin – Jumat). Anda tetap dapat menggunakan Asisten Lab Otomatis.',
            ], 403);
        }

        $user = Auth::user();
        
        // Tutup sesi aktif lama milik user ini jika ada dan tutup tiket di Discord
        if ($user) {
            $oldActiveChats = LiveChat::where('user_id', $user->id)
                ->where('status', 'active')
                ->get();

            foreach ($oldActiveChats as $oldChat) {
                $oldChat->update(['status' => 'closed', 'admin_typing_until' => null]);
                try {
                    DiscordService::closeTicketThread($oldChat, 'Sistem (Buka Sesi Baru)');
                } catch (\Throwable $e) {}
            }
        }

        $sessionCode = 'CS-' . date('Ymd') . '-' . strtoupper(Str::random(5));

        $chat = LiveChat::create([
            'session_code' => $sessionCode,
            'user_id'      => $user ? $user->id : null,
            'user_name'    => $user ? $user->name : ($request->input('name') ?: 'Tamu'),
            'user_email'   => $user ? $user->email : ($request->input('email') ?: '-'),
            'user_role'    => $user ? $user->role : 'Tamu',
            'status'       => 'active',
        ]);

        // Catat pesan sambutan dari Asisten Lab Virtual
        $greetingText = 'Sesi Customer Service langsung telah aktif (' . $sessionCode . '). Pesan Anda telah terhubung dengan Admin Pengelola Lab. Mohon menunggu sejenak sementara admin kami membalas pesan Anda. Anda dapat langsung menuliskan pertanyaan atau kendala yang dihadapi di bawah ini.';
        
        $msgPayload = [
            'live_chat_id' => $chat->id,
            'sender'       => 'bot',
            'message'      => $greetingText,
            'is_read'      => true,
        ];
        if (\Illuminate\Support\Facades\Schema::hasColumn('live_chat_messages', 'sender_name')) {
            $msgPayload['sender_name'] = 'Asisten Lab Virtual';
        }
        $greeting = LiveChatMessage::create($msgPayload);

        // Inisialisasi Ticket Thread di Discord jika aktif
        try {
            DiscordService::createTicketThread($chat);
        } catch (\Throwable $e) {
            // Abaikan kesalahan Discord agar sesi tetap berjalan
        }

        return response()->json([
            'status'        => 'success',
            'session_code'  => $chat->session_code,
            'greeting_id'   => $greeting->id,
            'greeting_text' => $greetingText,
            'sender_name'   => 'Asisten Lab Virtual',
            'admin_name'    => null,
            'created_at'    => $chat->created_at->format('H:i'),
        ]);
    }

    /**
     * Mengirim pesan dari pengguna ke Customer Service (Telegram Admin & Discord Ticket)
     */
    public function sendCsMessage(Request $request)
    {
        $request->validate([
            'session_code' => 'required|string',
            'message'      => 'required|string|max:1000',
        ]);

        $operational = self::getOperationalStatus();
        if (!$operational['is_open']) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Layanan Customer Service telah melewati jam operasional (08.00 – 17.00 WIB, Senin – Jumat). Sesi obrolan ditangguhkan.',
            ], 403);
        }

        $chat = LiveChat::where('session_code', $request->session_code)
            ->where('status', 'active')
            ->first();

        if (!$chat) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Sesi chat tidak ditemukan atau telah ditutup.',
            ], 404);
        }

        $msgText = trim($request->input('message'));

        $chatMessage = LiveChatMessage::create([
            'live_chat_id' => $chat->id,
            'sender'       => 'user',
            'message'      => $msgText,
            'is_read'      => false,
        ]);

        // Perbarui aktivitas terakhir pengguna dan indikator pengetikan CS
        $chat->update([
            'admin_typing_until' => now()->addSeconds(15),
        ]);
        $chat->touch();

        // 1. Teruskan ke Telegram Bot Admin
        $telegramResult = ['ok' => false];
        try {
            $telegramResult = TelegramService::sendLiveChatMessageToAdmin($chat, $chatMessage);
        } catch (\Throwable $e) {}

        // 2. Teruskan ke Thread Tiket Discord
        try {
            DiscordService::sendTicketMessage($chat, $chatMessage);
        } catch (\Throwable $e) {}

        return response()->json([
            'status'     => 'success',
            'message_id' => $chatMessage->id,
            'time'       => $chatMessage->created_at->format('H:i'),
            'telegram'   => $telegramResult['ok'] ?? false,
        ]);
    }

    /**
     * Polling pesan baru dari Customer Service / Admin (Sinkronisasi Telegram & Discord)
     */
    public function pollMessages(Request $request)
    {
        $sessionCode = $request->query('session_code');
        $lastId      = (int) $request->query('last_id', 0);
        if ($lastId > 1000000000) {
            $lastId = 0;
        }

        if (empty($sessionCode)) {
            return response()->json(['messages' => []]);
        }

        // 1. Auto-Sync balasan dari Discord Ticket Bot khusus untuk sesi ini
        try {
            DiscordService::processIncomingDiscordUpdates($sessionCode);
        } catch (\Throwable $e) {}

        // 2. Auto-Sync balasan dari Telegram Bot (throttled agar responsif dan tidak membebani koneksi)
        try {
            $tgLockKey = 'tg_poll_lock_' . $sessionCode;
            if (!\Illuminate\Support\Facades\Cache::has($tgLockKey)) {
                \Illuminate\Support\Facades\Cache::put($tgLockKey, true, 4);
                TelegramService::processIncomingTelegramUpdates();
            }
        } catch (\Throwable $e) {}

        $chat = LiveChat::where('session_code', $sessionCode)->first();

        if (!$chat) {
            return response()->json(['messages' => [], 'status' => 'not_found']);
        }

        // 3. Logika auto-close jika tidak ada percakapan selama 5 menit
        if ($chat->status === 'active') {
            $lastMsg = LiveChatMessage::where('live_chat_id', $chat->id)->latest('id')->first();
            $lastActivity = $lastMsg ? $lastMsg->created_at : $chat->created_at;

            if ($lastActivity && $lastActivity <= now()->subMinutes(5)) {
                $chat->update([
                    'status'             => 'closed',
                    'admin_typing_until' => null,
                ]);

                LiveChatMessage::create([
                    'live_chat_id' => $chat->id,
                    'sender'       => 'system',
                    'message'      => 'Sesi Customer Service telah ditutup otomatis oleh sistem karena tidak ada percakapan selama 5 menit.',
                    'is_read'      => false,
                ]);

                try {
                    DiscordService::closeTicketThread($chat, 'Sistem (Inaktivitas 5 Menit)');
                } catch (\Throwable $e) {}
            }
        }

        $newMessages = LiveChatMessage::where('live_chat_id', $chat->id)
            ->where('sender', '!=', 'user') // Ambil semua balasan admin/sistem sesi ini
            ->orderBy('id', 'asc')
            ->get();

        // Tandai sudah dibaca
        LiveChatMessage::where('live_chat_id', $chat->id)
            ->where('is_read', false)
            ->where('sender', '!=', 'user')
            ->update(['is_read' => true]);

        $hasAdminNameCol = \Illuminate\Support\Facades\Schema::hasColumn('live_chats', 'admin_name');
        $hasSenderNameCol = \Illuminate\Support\Facades\Schema::hasColumn('live_chat_messages', 'sender_name');

        $formatted = $newMessages->map(function ($m) use ($hasSenderNameCol) {
            return [
                'id'          => $m->id,
                'sender'      => $m->sender === 'admin' ? 'admin' : ($m->sender === 'bot' ? 'bot' : 'system'),
                'sender_name' => $hasSenderNameCol ? $m->sender_name : null,
                'text'        => htmlspecialchars($m->message),
                'time'        => $m->created_at->format('H:i'),
                'is_system'   => $m->sender === 'system',
            ];
        });

        $isAdminTyping = false;
        if ($chat->admin_typing_until && now()->lt($chat->admin_typing_until)) {
            $isAdminTyping = true;
        }

        return response()->json([
            'status'      => $chat->status,
            'admin_name'  => $hasAdminNameCol ? $chat->admin_name : null,
            'is_typing'   => $isAdminTyping,
            'messages'    => $formatted,
            'sessionCode' => $chat->session_code,
        ]);
    }

    /**
     * Mengakhiri sesi obrolan Customer Service (Hanya dapat dilakukan oleh Admin / CS)
     */
    public function closeCsSession(Request $request)
    {
        $sessionCode = $request->input('session_code') ?: $request->query('session_code');
        $reason      = $request->input('reason') ?: $request->query('reason');
        $user        = Auth::user();

        // Hanya admin / CS internal yang berwenang menutup sesi
        $isInternalAdmin = $user && in_array(strtolower($user->role ?? ''), ['admin', 'kepala_lab', 'koordinator_lab']);

        if (!$isInternalAdmin) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Sesi Customer Service hanya dapat diselesaikan dan ditutup oleh Admin/Customer Service.',
            ], 403);
        }

        $chat = null;
        if (!empty($sessionCode)) {
            $chat = LiveChat::where('session_code', $sessionCode)->first();
        }

        if (!$chat && $user) {
            $chat = LiveChat::where('user_id', $user->id)
                ->where('status', 'active')
                ->latest()
                ->first();
        }

        if ($chat) {
            $closedByLabel = 'Admin CS (' . ($user->name ?? 'Admin') . ')';
            $systemCloseText = 'Sesi Customer Service telah diselesaikan dan ditutup oleh Admin CS (' . ($user->name ?? 'Admin') . ').';

            if (!empty($reason) && str_contains(strtolower($reason), 'inaktif')) {
                $closedByLabel = 'Sistem (Inaktivitas 5 Menit)';
                $systemCloseText = 'Sesi Customer Service telah ditutup otomatis karena tidak ada percakapan selama 5 menit.';
            }

            $chat->update([
                'status'             => 'closed',
                'admin_typing_until' => null,
            ]);

            LiveChatMessage::create([
                'live_chat_id' => $chat->id,
                'sender'       => 'system',
                'message'      => $systemCloseText,
                'is_read'      => true,
            ]);

            // 1. Kirim notifikasi ke Telegram Admin
            try {
                date_default_timezone_set('Asia/Jakarta');
                $waktu = date('d-m-Y H:i:s') . ' WIB';
                $senderName = $chat->user_name ?: ($chat->user->name ?? 'Pengguna');
                $senderRole = strtoupper($chat->user_role ?: ($chat->user->role ?? 'Siswa'));

                $closeMsg = "<b>SESI LIVE CHAT DITUTUP</b>\n"
                    . "━━━━━━━━━━━━━━━━━━━━\n"
                    . "<b>ID Sesi:</b> <code>{$chat->session_code}</code>\n"
                    . "<b>Pengguna:</b> " . htmlspecialchars($senderName) . " (" . htmlspecialchars($senderRole) . ")\n"
                    . "<b>Ditutup Oleh:</b> " . htmlspecialchars($closedByLabel) . "\n"
                    . "<b>Waktu Tutup:</b> {$waktu}\n"
                    . "━━━━━━━━━━━━━━━━━━━━\n"
                    . "<i>" . htmlspecialchars($systemCloseText) . "</i>";

                TelegramService::sendCsMessage($closeMsg);
            } catch (\Throwable $e) {}

            // 2. Tutup channel tiket di Discord dan kirim transkrip log
            try {
                DiscordService::closeTicketThread($chat, $closedByLabel);
            } catch (\Throwable $e) {}
        }

        return response()->json(['status' => 'closed']);
    }

    /**
     * Tampilkan atau Unduh Halaman Transkrip Tiket Customer Service
     */
    public function viewTranscript(Request $request, string $sessionCode)
    {
        $chat = LiveChat::where('session_code', $sessionCode)->firstOrFail();
        $messages = LiveChatMessage::where('live_chat_id', $chat->id)
            ->orderBy('id', 'asc')
            ->get();

        // Opsi unduh file teks .txt
        if ($request->query('download') === 'txt') {
            $senderName = $chat->user_name ?: ($chat->user->name ?? 'Pengguna');
            $senderRole = strtoupper($chat->user_role ?: ($chat->user->role ?? 'Siswa'));
            $waktuBuka  = $chat->created_at ? $chat->created_at->format('d/m/Y H:i:s') . ' WIB' : '-';
            $waktuTutup = $chat->updated_at ? $chat->updated_at->format('d/m/Y H:i:s') . ' WIB' : '-';

            $txt = "========================================================\n";
            $txt .= "TRANSKRIP LAYANAN CUSTOMER SERVICE — LABORATORIUM TKJ\n";
            $txt .= "========================================================\n";
            $txt .= "Kode Sesi   : {$chat->session_code}\n";
            $txt .= "Pengguna    : {$senderName} ({$senderRole})\n";
            $txt .= "Email       : " . ($chat->user_email ?: '-') . "\n";
            $txt .= "Waktu Buka  : {$waktuBuka}\n";
            $txt .= "Waktu Tutup : {$waktuTutup}\n";
            $txt .= "Total Pesan : " . $messages->count() . " Pesan\n";
            $txt .= "========================================================\n\n";

            foreach ($messages as $m) {
                $time = $m->created_at ? $m->created_at->format('H:i:s') : '--:--:--';
                $label = 'Pengguna';
                if ($m->sender === 'admin') {
                    $label = 'Admin CS';
                } elseif ($m->sender === 'system') {
                    $label = 'Sistem';
                }
                $txt .= "[{$time}] {$label}:\n" . strip_tags($m->message) . "\n\n";
            }

            return response($txt, 200, [
                'Content-Type'        => 'text/plain; charset=utf-8',
                'Content-Disposition' => "attachment; filename=\"transcript-{$chat->session_code}.txt\"",
            ]);
        }

        return view('bantuan.transcript', [
            'chat'     => $chat,
            'messages' => $messages,
        ]);
    }
}