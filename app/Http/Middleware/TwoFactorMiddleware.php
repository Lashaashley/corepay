<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TwoFactorMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // If user has 2FA enabled and hasn't passed it this session
        if ($user->google2fa_secret && !session('2fa_verified')) {
            // Don't redirect if already on the verify route (avoid loop)
            if (!$request->routeIs('2fa.verify') && !$request->routeIs('2fa.check')) {
                return redirect()->route('2fa.verify');
            }
        }

        return $next($request);
    }
}