<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BranchesController;
use App\Http\Controllers\StaticController;
use App\Http\Controllers\DeptController;
use App\Http\Controllers\BanksController;
use App\Http\Controllers\CompbController;
use App\Http\Controllers\PaytypesController;
use App\Http\Controllers\AgentsController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\PitemsController;
use App\Http\Controllers\WhholdingController;
use App\Http\Controllers\Managepayroll;
use App\Http\Controllers\AutoCalcController;
use App\Http\Controllers\SummaryController;
use App\Http\Controllers\PayslipController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PayimportController;
use App\Http\Controllers\DeductionImportController;
use App\Http\Controllers\ExcelGenerationController;
use App\Http\Controllers\PeriodClosingController;
use App\Http\Controllers\BulkPayslipController;


use Illuminate\Support\Facades\Route;
use App\Models\Paytypes;


Route::get('/', function () {
    $payrollTypes = Paytypes::all();
    return view('login', compact('payrollTypes'));
});

Route::get('/dashboard', function () {
    return view('dashboard');
    
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->group(function () {
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo.update');
});
// Protect routes that require payroll selection
Route::middleware(['auth', 'payroll.selected'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    Route::get('/payroll', function () {
        return view('payroll.index');
    })->name('payroll.index');

    Route::get('agents', [AgentsController::class, 'index'])->name('agents.index');
    Route::get('areports', [AgentsController::class, 'aindex'])->name('areports.index');
    Route::get('aimport', [AgentsController::class, 'impindex'])->name('aimport.index');
     Route::get('payimport', [PayimportController::class, 'index'])->name('payimport.index');
    Route::get('/agents/data', [AgentsController::class, 'getData'])->name('agents.data');
    Route::get('/agents/{id}', [AgentsController::class, 'show'])->name('agents.show');
    Route::post('/agents/{id}/terminate', [AgentsController::class, 'terminate'])->name('agents.terminate');
     Route::post('/reports/full-staff', [ReportController::class, 'fullStaffReport'])
        ->name('reports.full-staff');
        // routes/web.php
Route::post('/reports/overall-summary', [ReportController::class, 'overallSummary'])->name('reports.overall-summary');
// routes/web.php
Route::post('/reports/payroll-items', [ReportController::class, 'payrollItems'])->name('reports.payroll-items');
// routes/web.php
Route::post('/reports/payroll-summary', [ReportController::class, 'payrollSummary'])->name('reports.payroll-summary');
// routes/web.php
Route::post('/reports/bank-advice', [ReportController::class, 'bankAdvice'])->name('reports.bank-advice');
// routes/web.php
Route::post('/reports/variance', [ReportController::class, 'variance'])->name('reports.variance');
Route::post('/generate-ift-report', [ExcelGenerationController::class, 'generateIFTReport'])
    ->name('generate.ift.report');
    // routes/web.php
Route::post('/generate-eft-report', [ExcelGenerationController::class, 'generateEFTReport'])
    ->name('generate.eft.report');
    // routes/web.php
Route::post('/generate-rtgs-report', [ExcelGenerationController::class, 'generateRTGSReport'])
    ->name('generate.rtgs.report');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// routes/web.php
Route::post('/reports/payroll-variance', [ReportController::class, 'payrollVariance'])->name('reports.payroll-variance');
        Route::prefix('import')->name('import.')->group(function () {
        //Route::get('/employees', [ImportController::class, 'showImportPage'])->name('employees');
        Route::post('/employees', [ImportController::class, 'importEmployees'])->name('employees.upload');
        Route::get('/template', [ImportController::class, 'downloadTemplate'])->name('template');
    });

    Route::prefix('payroll')->name('payroll.')->group(function () {
        Route::get('/deductions', [Managepayroll::class, 'index'])->name('deductions.index');
        Route::get('/deductions/data', [Managepayroll::class, 'getDeductions'])->name('deductions.data');
        Route::get('/deductions/html', [Managepayroll::class, 'getDeductionsHtml'])->name('deductions.html');
       Route::get('/staff/search', [Managepayroll::class, 'searchStaff'])->name('staff.search');
       Route::post('/submit', [Managepayroll::class, 'submitPayroll'])->name('submit');

    });
    Route::prefix('summary')->name('summary.')->group(function () {
        Route::get('/data', [SummaryController::class, 'getSummaryData'])->name('data');
        Route::post('/clear-cache', [SummaryController::class, 'clearCache'])->name('clear-cache');
    });
    Route::get('preports', [SummaryController::class, 'index'])->name('preports.index');
    Route::get('/preports/search', [SummaryController::class, 'search'])->name('preports.search');
    // routes/web.php
Route::post('/payslip/generate', [PayslipController::class, 'generate'])->name('payslip.generate');
    
    
    // Add other protected routes
});

Route::get('static', [StaticController::class, 'create'])->name('staticinfo');
Route::post('static', [StaticController::class, 'store'])->name('staticinfo.store');
Route::get('/staticinfo/getall', [StaticController::class, 'getAll'])->name('staticinfo.getall');
Route::post('static/{id}', [StaticController::class, 'update'])->name('staticinfo.update');

Route::get('branches', [BranchesController::class, 'create'])->name('branches');
Route::post('branches', [BranchesController::class, 'store'])->name('branches.store');
Route::get('/branches/getall', [BranchesController::class, 'getAll'])->name('branches.getall');
Route::get('/branches/get-dropdown', [BranchesController::class, 'getAllBranches'])->name('branches.getDropdown');
Route::post('branches/{id}', [BranchesController::class, 'update'])->name('branches.update');
Route::delete('branches/{id}', [BranchesController::class, 'destroy'])->name('branches.destroy');

Route::get('depts', [DeptController::class, 'create'])->name('depts');
Route::post('depts', [DeptController::class, 'store'])->name('depts.store');
Route::get('/depts/getall', [DeptController::class, 'getAll'])->name('depts.getall');

Route::get('banks', [BanksController::class, 'create'])->name('banks');
Route::post('banks', [BanksController::class, 'store'])->name('banks.store');
Route::get('/banks/getall', [BanksController::class, 'getAll'])->name('banks.getall');

Route::get('compb', [CompbController::class, 'create'])->name('compb');
Route::post('compb', [CompbController::class, 'store'])->name('compb.store');
Route::get('/compb/getall', [CompbController::class, 'getAll'])->name('compb.getall');

Route::get('paytypes', [PaytypesController::class, 'create'])->name('paytypes');
Route::post('paytypes', [PaytypesController::class, 'store'])->name('paytypes.store');
Route::get('/paytypes/getall', [PaytypesController::class, 'getAll'])->name('paytypes.getall');
Route::post('pmodes/{id}', [PaytypesController::class, 'update'])->name('pmodes.update');


Route::get('pitems', [PitemsController::class, 'index'])->name('pitems.index');
Route::post('pitems', [PitemsController::class, 'store'])->name('pitems.store');
Route::post('pitems/update', [PitemsController::class, 'update'])->name('pitems.update');

Route::get('ritems', [WhholdingController::class, 'index'])->name('ritems.index');
Route::get('/ritems/get-wht', [WhholdingController::class, 'show'])->name('ritems.getwithholding');
Route::post('ritems/update', [WhholdingController::class, 'update'])->name('ritems.update');
Route::get('/ritems/getcodes', [WhholdingController::class, 'getcodes'])->name('ritems.getcodes');
Route::post('whgroups/store', [WhholdingController::class, 'storeGroup'])
    ->name('whgroups.store');
    Route::post('whgroups/delete', [WhholdingController::class, 'deleteGroup'])
    ->name('whgroups.delete');

Route::get('mngprol', [Managepayroll::class, 'index'])->name('mngprol.index');
Route::post('/toggle-status', [Managepayroll::class, 'toggleStatus'])->name('toggle.status');
Route::get('/mngprol/getcodes', [Managepayroll::class, 'getAllpitems'])->name('mngprol.getcodes');
Route::post('/staff/search/details', [Managepayroll::class, 'searchStaffDetails'])
     ->name('staff.search.details');
     Route::post('/fetch/items', [Managepayroll::class, 'fetchItems'])->name('fetch.items');
     Route::post('/close-period', [PeriodClosingController::class, 'closePeriod'])
    ->name('period.close');
    Route::get('closep', [PeriodClosingController::class, 'index'])->name('closep.index');

     Route::middleware(['auth'])->prefix('deductions')->name('deductions.')->group(function () {
    Route::get('/import', [DeductionImportController::class, 'index'])->name('import');
    Route::post('/import', [DeductionImportController::class, 'import'])->name('import.process');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/autocalc', [AutoCalcController::class, 'processTotals'])
        ->name('autocalc.process');
});

Route::get('/bulk-payslips', [BulkPayslipController::class, 'index'])->name('bulk.payslips.index');
Route::post('/bulk-payslips/generate', [BulkPayslipController::class, 'generate'])->name('bulk.payslips.generate');
Route::get('/bulk-payslips/progress/{jobId}', [BulkPayslipController::class, 'progress'])->name('bulk.payslips.progress');




//Route::get('mngprol', [Managepayroll::class, 'showPayrollPeriod']);


 

require __DIR__.'/auth.php';
