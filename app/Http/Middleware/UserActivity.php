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
        }

        return $next($request);
    }
}
