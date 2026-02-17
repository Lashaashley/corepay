<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Paytypes;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // ✅ Add this
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        // Fetch all payroll types
        $payrollTypes = Paytypes::all();
        
        // Pass to view
        return view('auth.login', compact('payrollTypes'));
    }

    /**
     * Handle an incoming authentication request.
     */
   public function store(LoginRequest $request): RedirectResponse
{
    // Authenticate user
    $request->authenticate();

    $user = Auth::user();

    // ✅ Check if password expired
    if ($user->isPasswordExpired()) {

        session([
            'password_expired_user_id' => $user->id,
            'password_expired_email' => $user->email,
        ]);

        Auth::logout();

        return redirect()->route('password.expired')
            ->with('error', 'Your password has expired. Please update it to continue.');
    }

    // ✅ Check session before regeneration
    Log::info('Before session regenerate', [
        'allowedPayroll' => session('allowedPayroll'),
        'user_id' => session('user_id')
    ]);

    // Regenerate session to prevent session fixation
    $request->session()->regenerate();

    // ✅ Check session after regeneration
    Log::info('After session regenerate', [
        'allowedPayroll' => session('allowedPayroll'),
        'user_id' => session('user_id'),
        'all_session' => session()->all()
    ]);

    // restore if lost
    if (!session()->has('allowedPayroll')) {

        Log::error('Session lost allowedPayroll after regeneration!');

        $userAllowedPayroll = !empty($user->allowedprol)
            ? array_map('intval', explode(',', $user->allowedprol))
            : [];

        session([
            'allowedPayroll' => $userAllowedPayroll,
            'user_id' => $user->id,
            'user_name' => $user->name,
        ]);

        Log::info('Session restored', [
            'allowedPayroll' => session('allowedPayroll')
        ]);
    }

    return redirect()->intended(route('dashboard'));
}


    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
         $user = Auth::user();
            $userAllowedPayroll = !empty($user->allowedprol)
                ? array_map('intval', explode(',', $user->allowedprol))
                : [];

        Auth::guard('web')->logout();

        // Clear session data
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        logAuditTrail(
            $user->id,
            'LOGOUT',
            'users_table',
             "$user->id",
            null,
            null,
            [
                'action' => 'User Log out',
                'user_id' => $user->id,
                'user_allowedprol' => $user->allowedprol,
                'ip_address' => $request->ip()
            ]
        );

        return redirect('/');
    }
}