<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckPayrollAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        // Get payroll type from request (could be in body, query, or route)
        $payrollType = $request->input('payroll_type') 
                    ?? $request->input('period')
                    ?? $request->route('payroll_type');

        // If no specific payroll type, validate against session allowedPayroll
        if (!$payrollType) {
            $sessionPayrolls = session('allowedPayroll', []);
            
            // Re-validate session against database
            $dbPayrolls = DB::table('user_payroll_access')
                ->where('user_id', $user->id)
                ->where('is_active', 1)
                ->pluck('payroll_type')
                ->toArray();
            
            // Session and DB must match
            if (empty($dbPayrolls)) {
                Log::warning("User {$user->id} has no payroll access in database but has session data", [
                    'user_id' => $user->id,
                    'session_payrolls' => $sessionPayrolls,
                    'ip' => $request->ip()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'No payroll access configured'
                ], 403);
            }
            
            // Update session if it's stale
            if (array_diff($sessionPayrolls, $dbPayrolls) || array_diff($dbPayrolls, $sessionPayrolls)) {
                session(['allowedPayroll' => $dbPayrolls]);
                
                Log::info("Session payroll access updated for user {$user->id}", [
                    'old' => $sessionPayrolls,
                    'new' => $dbPayrolls
                ]);
            }
            
            return $next($request);
        }

        // Validate specific payroll type access
        $hasAccess = DB::table('user_payroll_access')
            ->where('user_id', $user->id)
            ->where('payroll_type', $payrollType)
            ->where('is_active', 1)
            ->exists();

        if (!$hasAccess) {
            Log::warning("Unauthorized payroll access attempt", [
                'user_id' => $user->id,
                'payroll_type' => $payrollType,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to this payroll type'
            ], 403);
        }

        return $next($request);
    }
}
