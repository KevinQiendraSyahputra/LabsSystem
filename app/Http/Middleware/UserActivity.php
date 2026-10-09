<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class UserActivity
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $userId = Auth::id();
            // User dianggap aktif/online jika ada aktivitas dalam 45 detik terakhir
            $expiresAt = now()->addSeconds(45);
            Cache::put('user-is-online-' . $userId, true, $expiresAt);
            Cache::put('user-last-seen-' . $userId, now()->timestamp, now()->addDays(7));

            // Jika user sedang membuka halaman website, perbarui aktivitas live chat miliknya jika ada
            try {
                \App\Models\LiveChat::where('user_id', $userId)
                    ->where('status', 'active')
                    ->update([
                        'updated_at' => now(),
                    ]);
            } catch (\Throwable $e) {}
        }

        // Auto-close tiket yang tidak ada percakapan selama 5 menit (dijalankan berkala setiap 15 detik)
        try {
            if (!Cache::has('auto_close_tickets_lock')) {
                Cache::put('auto_close_tickets_lock', true, 15);
                \App\Services\DiscordService::autoCloseInactiveTickets(5);
            }
        } catch (\Throwable $e) {}

        return $next($request);
    }
}
