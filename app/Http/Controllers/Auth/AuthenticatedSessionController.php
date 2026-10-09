<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $userId = Auth::id();
        if ($userId) {
            \Illuminate\Support\Facades\Cache::forget('user-is-online-' . $userId);

            // Tutup tiket Customer Service yang aktif seketika saat pengguna logout (tanpa menunggu 2 menit)
            try {
                $activeChats = \App\Models\LiveChat::where('user_id', $userId)
                    ->where('status', 'active')
                    ->get();

                foreach ($activeChats as $chat) {
                    $chat->update([
                        'status'             => 'closed',
                        'admin_typing_until' => null,
                    ]);

                    \App\Models\LiveChatMessage::create([
                        'live_chat_id' => $chat->id,
                        'sender'       => 'system',
                        'message'      => 'Sesi Customer Service telah ditutup otomatis karena pengguna keluar (logout).',
                        'is_read'      => true,
                    ]);

                    \App\Services\DiscordService::closeTicketThread($chat, 'Pengguna (Logout)');
                }
            } catch (\Throwable $e) {}
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
