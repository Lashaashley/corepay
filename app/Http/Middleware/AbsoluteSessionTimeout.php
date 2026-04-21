<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AbsoluteSessionTimeout
{
    /**
     * 15 minutes = 900 seconds.
     */
    protected const ABSOLUTE_TIMEOUT_SECONDS = 900;

    protected const SESSION_CREATED_AT_KEY = '_session_created_at';

    public function handle(Request $request, Closure $next)
    {
        // ── Early return for guests ──────────────────────────────────────
        // Never touch the session for unauthenticated requests.
        // Invalidating a guest session destroys the CSRF token on the
        // login page, causing a 419 on form submission.
        if (! Auth::check()) {
            return $next($request);
        }

        $createdAt = $request->session()->get(self::SESSION_CREATED_AT_KEY);

        // Fallback: if stamp is missing (e.g. existing session before this
        // middleware was deployed), stamp it now and start the clock.
        if ($createdAt === null) {
            $request->session()->put(self::SESSION_CREATED_AT_KEY, now()->timestamp);
            return $next($request);
        }

        if ((now()->timestamp - $createdAt) > self::ABSOLUTE_TIMEOUT_SECONDS) {
            $this->forceLogout($request);

            return redirect()->route('login')->with(
                'status',
                'Your session has expired. Please sign in again.'
            );
        }

        return $next($request);
    }

    protected function forceLogout(Request $request): void
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}