<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use App\Models\Paytypes;
use Symfony\Component\HttpFoundation\Response;

class SharePayrollData
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            // Get allowed payroll IDs from session
            $allowedPayrollIds = session('allowedPayroll', []);
            
            Log::info('SharePayrollData middleware', [
                'allowedPayrollIds' => $allowedPayrollIds
            ]);
            
            if (!empty($allowedPayrollIds) && is_array($allowedPayrollIds)) {
                // Fetch payroll names
                $payrollTypes = Paytypes::whereIn('ID', $allowedPayrollIds)->get();
                
                if ($payrollTypes->isNotEmpty()) {
                    $payrollNames = $payrollTypes->pluck('pname')->toArray();
                    $payrollTypesDisplay = implode(', ', $payrollNames);
                    
                    // Share with all views
                    View::share([
                        'payrollTypesDisplay' => $payrollTypesDisplay,
                        'payrollTypesArray' => $payrollNames,
                        'payrollIds' => $allowedPayrollIds,
                        'hasPayrollAccess' => true,
                        'payrollCount' => count($payrollNames)
                    ]);
                } else {
                    View::share([
                        'payrollTypesDisplay' => 'Invalid payroll selection',
                        'hasPayrollAccess' => false
                    ]);
                }
            } else {
                View::share([
                    'payrollTypesDisplay' => 'No payroll types selected',
                    'hasPayrollAccess' => false
                ]);
            }
        } else {
            View::share([
                'payrollTypesDisplay' => 'Not logged in',
                'hasPayrollAccess' => false
            ]);
        }
        
        return $next($request);
    }
}