<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EnsurePayrollSelected
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            Log::warning('EnsurePayrollSelected: User not authenticated', [
                'ip' => $request->ip(),
                'url' => $request->fullUrl()
            ]);
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Check if user is super admin (bypass check)
        if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return $next($request);
        }

        // Verify payroll session exists
        $allowedPayroll = session('allowedPayroll');
        
        if (!$allowedPayroll || !is_array($allowedPayroll) || empty($allowedPayroll)) {
            Log::warning('EnsurePayrollSelected: No payroll selection', [
                'user_id' => $user->id,
                'session_data' => session()->all()
            ]);
            
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect()->route('login')->withErrors([
                'error' => 'Please select payroll types to continue.'
            ]);
        }

        return $next($request);
    }
}