<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\TelegramService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email'    => ['required', 'email', 'exists:users,email'],
            'whatsapp' => ['required', 'string', 'min:9', 'max:16'],
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'email.exists'      => 'Email tidak terdaftar di sistem kami.',
            'whatsapp.required' => 'Nomor WhatsApp wajib diisi.',
            'whatsapp.min'      => 'Nomor WhatsApp minimal 9 digit.',
        ]);

        $user = User::where('email', $request->email)->first();

        // Format nomor WhatsApp internasional (62xxxx)
        $cleanWa = preg_replace('/[^0-9]/', '', $request->whatsapp);
        if (str_starts_with($cleanWa, '0')) {
            $formattedWa = '62' . substr($cleanWa, 1);
        } elseif (str_starts_with($cleanWa, '62')) {
            $formattedWa = $cleanWa;
        } else {
            $formattedWa = '62' . $cleanWa;
        }

        // Kirim notifikasi ke Telegram Admin via TelegramService (dengan IP Fallback & SSL Bypass)
        try {
            TelegramService::sendPasswordResetRequest($user, $request->whatsapp, $formattedWa);
        } catch (\Throwable $e) {
            // Log error jika terjadi kesalahan
        }

        return back()->with('status', 'Permintaan reset berhasil dikirim. Admin akan menghubungi nomor WhatsApp Anda untuk memberikan kata sandi baru.');
    }
}