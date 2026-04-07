<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * AbsoluteSessionTimeout
 *
 * Laravel's built-in session lifetime only implements an IDLE timeout —
 * each request resets the expiry clock. This means an active user could
 * theoretically stay logged in indefinitely by keep-alive requests.
 *
 * This middleware enforces an ABSOLUTE session timeout: regardless of
 * activity, the session is invalidated after a fixed wall-clock duration
 * from first login. This is a separate control from the idle timeout.
 *
 * Recommended values for a payroll system:
 *   - Idle timeout:    15 minutes  (config/session.php → lifetime)
 *   - Absolute timeout: 8 hours   (this middleware → ABSOLUTE_TIMEOUT_SECONDS)
 *
 * Registration: add to the 'web' middleware group in bootstrap/app.php,
 * AFTER the session middleware has run:
 *
 *   $middleware->web(append: [
 *       \App\Http\Middleware\SharePayrollData::class,
 *       \App\Http\Middleware\SecurityHeaders::class,
 *       \App\Http\Middleware\LoadMenuData::class,
 *       \App\Http\Middleware\AbsoluteSessionTimeout::class,  // ← add here
 *   ]);
 */
class AbsoluteSessionTimeout
{
    /**
     * Maximum session age in seconds from time of login.
     * 8 hours = 28800 seconds.
     * Adjust to suit your organisation's security policy.
     */
    protected const ABSOLUTE_TIMEOUT_SECONDS = 28800;

    /**
     * Session key that stores the time the session was first established.
     */
    protected const SESSION_CREATED_AT_KEY = '_session_created_at';

    public function handle(Request $request, Closure $next)
    {
        // Only enforce for authenticated users with an active session
        if (Auth::check()) {
            $createdAt = $request->session()->get(self::SESSION_CREATED_AT_KEY);

            if ($createdAt === null) {
                // First request after login — stamp the absolute start time
                $request->session()->put(self::SESSION_CREATED_AT_KEY, now()->timestamp);
            } elseif ((now()->timestamp - $createdAt) > self::ABSOLUTE_TIMEOUT_SECONDS) {
                // Absolute timeout exceeded — force logout regardless of activity
                $this->forceLogout($request);

                return redirect()->route('login')->with(
                    'status',
                    'Your session has expired. Please sign in again.'
                );
            }
        }

        return $next($request);
    }

    /**
     * Invalidate the session and log the user out cleanly.
     */
    protected function forceLogout(Request $request): void
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}