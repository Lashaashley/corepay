<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Models\Paytypes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PayrollComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        // ✅ Add debug logging
        Log::info('PayrollComposer executing', [
            'authenticated' => Auth::check(),
            'session_allowedPayroll' => session('allowedPayroll'),
            'all_session_keys' => array_keys(session()->all())
        ]);

        // Check if user is authenticated
        if (!Auth::check()) {
            $view->with([
                'payrollTypesDisplay' => 'Not logged in',
                'hasPayrollAccess' => false
            ]);
            return;
        }

        // ✅ Get allowed payroll IDs from session - use session() helper correctly
        $allowedPayrollIds = session('allowedPayroll');
        
        // ✅ Add more detailed logging
        Log::info('Payroll IDs retrieved', [
            'allowedPayrollIds' => $allowedPayrollIds,
            'type' => gettype($allowedPayrollIds),
            'is_array' => is_array($allowedPayrollIds),
            'is_empty' => empty($allowedPayrollIds)
        ]);
        
        // Verify session variable exists and is valid
        if (!$allowedPayrollIds || !is_array($allowedPayrollIds) || empty($allowedPayrollIds)) {
            Log::warning('No valid payroll selection in session', [
                'user_id' => Auth::id(),
                'session_data' => session()->all()
            ]);
            
            $view->with([
                'payrollTypesDisplay' => 'No payroll types selected',
                'hasPayrollAccess' => false
            ]);
            return;
        }
        
        // Fetch payroll names from database
        try {
            $payrollTypes = Paytypes::whereIn('ID', $allowedPayrollIds)->get();
            
            Log::info('Payroll types fetched', [
                'count' => $payrollTypes->count(),
                'types' => $payrollTypes->pluck('pname')->toArray()
            ]);
            
            if ($payrollTypes->isEmpty()) {
                $view->with([
                    'payrollTypesDisplay' => 'Invalid payroll selection',
                    'hasPayrollAccess' => false
                ]);
                return;
            }
            
            $payrollNames = $payrollTypes->pluck('pname')->toArray();
            $payrollTypesDisplay = implode(', ', $payrollNames);
            
            $view->with([
                'payrollTypesDisplay' => $payrollTypesDisplay,
                'payrollTypesArray' => $payrollNames,
                'payrollIds' => $allowedPayrollIds,
                'hasPayrollAccess' => true,
                'payrollCount' => count($payrollNames)
            ]);
            
            Log::info('Payroll data passed to view', [
                'payrollTypesDisplay' => $payrollTypesDisplay,
                'hasPayrollAccess' => true
            ]);
            
        } catch (\Exception $e) {
            // Log error and provide fallback
            Log::error('Payroll Composer Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            $view->with([
                'payrollTypesDisplay' => 'Error loading payroll types',
                'hasPayrollAccess' => false
            ]);
        }
    }
}