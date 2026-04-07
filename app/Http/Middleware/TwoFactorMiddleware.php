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

        // If MFA is not required for this user, skip all 2FA checks
        if ($user->MFA !== 'ON') {
            return $next($request);
        }

        $on2faRoutes = $request->routeIs('2fa.*');

        // MFA required but no secret set yet — force setup
        if (!$user->google2fa_secret) {
            if (!$on2faRoutes) {
                return redirect()->route('2fa.setup')
                    ->with('info', 'Your account requires 2FA. Please set it up to continue.');
            }
            return $next($request);
        }

        // MFA required, secret exists, but not verified this session — force verify
        if (!session('2fa_verified')) {
            if (!$on2faRoutes) {
                return redirect()->route('2fa.verify');
            }
            return $next($request);
        }

        return $next($request);
    }
}