<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Paytypes;
use App\Support\SafeRedirect;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(Request $request): View
    {
        // Show session-expired message if redirected here by the
        // client-side session timeout widget (?expired=1)
        if ($request->query('expired')) {
            session()->flash(
                'status',
                'Your session expired due to inactivity. Please sign in again.'
            );
        }

        $payrollTypes = Paytypes::all();

        return view('auth.login', compact('payrollTypes'));
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // All authentication, status checks, payroll validation, and
        // initial session seeding happen inside LoginRequest::authenticate()
        $request->authenticate();

        $user = Auth::user();

        // ── Password expiry check ─────────────────────────────────────────────
        // Must happen before session regeneration so that if we log the user
        // out here, we haven't already issued them a valid regenerated session.
        if ($user->isPasswordExpired()) {
            // Store just enough to identify the user on the password-change form.
            // Do NOT store anything that grants access to the application.
            session([
                'password_expired_user_id' => $user->id,
                'password_expired_email'   => $user->email,
            ]);

            Auth::logout();

            return redirect()->route('password.expired')
                ->with('error', 'Your password has expired. Please update it to continue.');
        }

        // ── Session regeneration ──────────────────────────────────────────────
        // Prevents session fixation. Laravel migrates existing session data
        // across regenerate(), so allowedPayroll etc. set in authenticate()
        // are preserved. The verbose debug logs around this have been removed
        // from production — use Laravel Telescope or APP_DEBUG locally instead.
        $request->session()->regenerate();

        // ── Session integrity check ───────────────────────────────────────────
        // In rare cases (misconfigured session drivers, race conditions) session
        // data can be lost across regenerate(). Guard against this defensively.
        if (! session()->has('allowedPayroll')) {
            Log::warning('Session lost allowedPayroll after regeneration — restoring', [
                'user_id' => $user->id,
                'ip'      => $request->ip(),
            ]);

            $userAllowedPayroll = ! empty($user->allowedprol)
                ? array_map('intval', explode(',', $user->allowedprol))
                : [];

            session([
                'allowedPayroll' => $userAllowedPayroll,
                'user_id'        => $user->id,
                'user_name'      => $user->name,
                'user_email'     => $user->email,
            ]);
        }

        

        return redirect(SafeRedirect::intended('dashboard'));
    }

    /**
     * Destroy an authenticated session (logout).
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();

        Auth::guard('web')->logout();

        // Both steps are required:
        //   invalidate()       → destroys the current session entirely
        //   regenerateToken()  → issues a new CSRF token
        // invalidate() alone does not regenerate the CSRF token.
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Audit trail logged AFTER session destruction — a failure in
        // logAuditTrail() must never prevent the session being cleared.
        // user_allowedprol removed from log — permission sets should not
        // appear in log files. IP and user ID are sufficient.
        if ($user) {
            logAuditTrail(
                $user->id,
                'LOGOUT',
                'users_table',
                (string) $user->id,
                null,
                null,
                [
                    'action'     => 'User logged out',
                    'user_id'    => $user->id,
                    'ip_address' => $request->ip(),
                ]
            );
        }

        // Always redirect to a fixed destination.
        // redirect('/') would also work but using the named route means
        // a future URL change only needs updating in one place.
        return redirect()->route('login');
    }
}