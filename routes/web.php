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
use App\Http\Controllers\UsersController;
use App\Http\Controllers\ModulesController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\RolesReportController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\EmailconfigController;
use App\Http\Controllers\PaytrackerController;
use App\Http\Controllers\PayrollApprovalController;
use App\Http\Controllers\RegistrationApprovalController;
use App\Http\Controllers\NetpayApprovalController;
use App\Http\Controllers\AnalyticsController;
use \App\Http\Controllers\Auth\PasswordExpiredController;
use App\Http\Controllers\TwoFactorController;

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
    Route::get('/2fa/verify',  [TwoFactorController::class, 'showVerify'])->name('2fa.verify');
    Route::post('/2fa/verify', [TwoFactorController::class, 'verify'])->name('2fa.check');
    Route::get('/2fa/setup',   [TwoFactorController::class, 'setup'])->name('2fa.setup');
    Route::post('/2fa/enable', [TwoFactorController::class, 'enable'])->name('2fa.enable');
    Route::get('/2fa/disable', [TwoFactorController::class, 'showDisableForm'])->name('2fa.disable.form');
    Route::post('/2fa/disable',[TwoFactorController::class, 'disable'])->name('2fa.disable');

    
});

  Route::get('/payroll-types', [UsersController::class, 'getPayrollTypes'])->name('getPayroll.types');



// Protect routes that require payroll selection
Route::middleware(['auth', 'payroll.selected', '2fa'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/payroll', function () {
        return view('payroll.index');
    })->name('payroll.index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::put('/users/{id}/update', [UsersController::class, 'update'])->name('update.user');
    Route::put('/users/{id}/changepassword', [UsersController::class, 'changepassword'])->name('change.pass');
    Route::get('/users/{id}/edit', [UsersController::class, 'edit'])->name('get.user');
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo.update');
    
    Route::get('agents', [AgentsController::class, 'index'])->name('agents.index');
    Route::get('areports', [AgentsController::class, 'aindex'])->name('areports.index');
    Route::get('aimport', [AgentsController::class, 'impindex'])->name('aimport.index');
    Route::get('nagent', [AgentsController::class, 'newganet'])->name('nagent.index');
     Route::get('payimport', [PayimportController::class, 'index'])->name('payimport.index');
    Route::get('/agents/data', [AgentsController::class, 'getData'])->name('agents.data');
     Route::get('/agent/{id}/edit', [AgentsController::class, 'editagent'])->name('get.agent');
    Route::get('/agents/{id}', [AgentsController::class, 'show'])->name('agents.show');
    Route::post('/agents/{id}/terminate', [AgentsController::class, 'terminate'])->name('agents.terminate');
    Route::post('agent/{id}', [AgentsController::class, 'update'])->name('agent.update');
    Route::post('regagent/{id}', [AgentsController::class, 'regupdate'])->name('regagent.update');

     Route::post('/reports/full-staff', [ReportController::class, 'fullStaffReport'])
        ->name('reports.full-staff');
        // routes/web.php
Route::post('/reports/overall-summary', [ReportController::class, 'overallSummary'])->name('reports.overall-summary');
// routes/web.php
Route::post('/reports/payroll-items', [ReportController::class, 'payrollItems'])->name('reports.payroll-items');

Route::post('/reports/earnings', [ReportController::class, 'EarningsReport'])->name('reports.earnings');
Route::post('/reports/netpay', [ReportController::class, 'NetpayReport'])->name('reports.netpay');
// routes/web.php
Route::post('/reports/payroll-summary', [ReportController::class, 'payrollSummary'])->name('reports.payroll-summary');
Route::post('payroll-summary/excel', [ReportController::class, 'generatePayrollSummaryExcel']) ->name('payroll.summary.excel');
// routes/web.php
Route::post('/reports/bank-advice', [ReportController::class, 'bankAdvice'])->name('reports.bank-advice');
// routes/web.php
Route::post('/reports/variance', [ReportController::class, 'variance'])->name('reports.variance');
Route::get('/reports/netpay/excel', [ReportController::class, 'NetpayReportExcel'])
    ->name('reports.netpay.excel');

    Route::get('/reports/earnings/excel', [ReportController::class, 'EarningsReportExcel'])
    ->name('reports.earnings.excel');


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
    Route::get('/import/duplicate-report', [ImportController::class, 'downloadDuplicateReport'])
     ->name('import.duplicate.report');

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
    
    
    Route::get('static', [StaticController::class, 'create'])->name('staticinfo');
Route::post('static', [StaticController::class, 'store'])->name('staticinfo.store');
Route::get('/staticinfo/getall', [StaticController::class, 'getAll'])->name('staticinfo.getall');
Route::post('static/{id}', [StaticController::class, 'update'])->name('staticinfo.update');

Route::get('branches', [BranchesController::class, 'create'])->name('branches');
Route::post('branches', [BranchesController::class, 'store'])->name('branches.store');
Route::get('/branches/getall', [BranchesController::class, 'getAll'])->name('branches.getall');
Route::get('/branches/get-dropdown', [BranchesController::class, 'getAllBranches'])->name('branches.getDropdown');
Route::get('/depts/get-dropdown', [DeptController::class, 'getAllDepts'])->name('depts.getDropdown');
Route::post('branches/{id}', [BranchesController::class, 'update'])->name('branches.update');
Route::delete('branches/{id}', [BranchesController::class, 'destroy'])->name('branches.destroy');

Route::get('depts', [DeptController::class, 'create'])->name('depts');
Route::post('depts', [DeptController::class, 'store'])->name('depts.store');
Route::post('depts/{id}', [DeptController::class, 'update'])->name('depts.update');
Route::get('/depts/getall', [DeptController::class, 'getAll'])->name('depts.getall');
Route::get('/classes/by-campus', [DeptController::class, 'getClassesByCampus'])->name('classes.getByCampus');

Route::get('banks', [BanksController::class, 'create'])->name('banks');
Route::post('banks', [BanksController::class, 'store'])->name('banks.store');
Route::post('banks/{id}', [BanksController::class, 'update'])->name('banks.update');
Route::get('/banks/getall', [BanksController::class, 'getAll'])->name('banks.getall');
Route::get('/banks/get-dropdown', [BanksController::class, 'getAllBanks'])->name('banks.getDropdown');
Route::get('/brbanks/get-dropdown', [BanksController::class, 'getBranchesDepts'])->name('brbranches.getDropdown');
Route::get('/bankbranches/get-dropdown', [BanksController::class, 'getBranchesByBank'])->name('branches.getByBank');
Route::get('/codes/get-dropdown', [BanksController::class, 'getCodesBank'])->name('codes.getByBank');


Route::get('compb', [CompbController::class, 'create'])->name('compb');
Route::post('compb', [CompbController::class, 'store'])->name('compb.store');
Route::get('/compb/getall', [CompbController::class, 'getAll'])->name('compb.getall');
Route::post('compb/{id}', [CompbController::class, 'update'])->name('compb.update');

Route::get('/econfig/getall', [EmailconfigController::class, 'getAll'])->name('econfig.getall');
Route::post('econfig/{id}', [EmailconfigController::class, 'update'])->name('econfig.update');

Route::get('paytypes', [PaytypesController::class, 'create'])->name('paytypes');
Route::post('paytypes', [PaytypesController::class, 'store'])->name('paytypes.store');
Route::get('/paytypes/getall', [PaytypesController::class, 'getAll'])->name('paytypes.getall');
Route::post('pmodes/{id}', [PaytypesController::class, 'update'])->name('pmodes.update');
Route::get('/paytypes/get-dropdown', [PaytypesController::class, 'getAllpaytypes'])->name('paytypes.getDropdown');

Route::post('agents', [AgentsController::class, 'registerAgent'])->name('agents.store');
Route::post('2registration', [AgentsController::class, 'registrationdetails'])->name('2registration.store');

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
Route::get('/deductions/download/missing-employees/{token?}', [DeductionImportController::class, 'downloadMissingEmployees'])
    ->name('deductions.download.missing.employees');

Route::middleware(['auth'])->group(function () {
    Route::get('/autocalc', [AutoCalcController::class, 'processTotals'])
        ->name('autocalc.process');
});

Route::prefix('bulk-payslips')->group(function () {
    // Show form
    Route::get('/', [BulkPayslipController::class, 'index'])
        ->name('bulk.payslips.index');
    
    // Generate payslips
    Route::post('/generate', [BulkPayslipController::class, 'generate'])
        ->name('bulk.payslips.generate');
    
    // Get progress
    Route::get('/progress/{jobId}', [BulkPayslipController::class, 'progress'])
        ->name('bulk.payslips.progress');
    
    // Download as ZIP
    Route::get('/download/zip/{jobId}', [BulkPayslipController::class, 'downloadZip'])
        ->name('bulk.payslips.download.zip');
    
    // List files
    Route::get('/list/{jobId}', [BulkPayslipController::class, 'listFiles'])
        ->name('bulk.payslips.list');
    
    // Download single file
    Route::get('/download/{jobId}/{workNo}', [BulkPayslipController::class, 'downloadSingle'])
        ->name('bulk.payslips.download.single');
    
    // Cleanup (can be called via cron)
    Route::post('/cleanup', [BulkPayslipController::class, 'cleanup'])
        ->name('bulk.payslips.cleanup');
});


Route::get('newuser', [UsersController::class, 'index'])->name('newuser.index');
Route::get('musers', [UsersController::class, 'indexfun'])->name('musers.indexfun');
Route::get('/musers/data', [UsersController::class, 'getData'])->name('musers.data');
Route::prefix('users')->name('newuser.')->group(function () {
    
    Route::post('/store', [UsersController::class, 'store'])->name('store');
    Route::put('/{id}', [UsersController::class, 'update'])->name('update');
    Route::delete('/{id}', [UsersController::class, 'destroy'])->name('destroy');
});
Route::get('massign', [ModulesController::class, 'index'])->name('massign.index');
Route::prefix('modules')->name('modules.')->middleware('auth')->group(function () {
    Route::post('/get-user-modules', [ModulesController::class, 'getUserModules'])->name('getUserModules');
    Route::post('/get-role-modules', [ModulesController::class, 'getRoleModules'])->name('getRoleModules');
    Route::post('/assign', [ModulesController::class, 'assignModules'])->name('assign');
    Route::post('/save', [ModulesController::class, 'saveModules'])->name('save');
    Route::post('/remove', [ModulesController::class, 'removeModule'])->name('remove');
});

// routes/web.php
Route::get('/payroll/deductions/priorities', [PitemsController::class, 'getDeductionPriorities'])
    ->name('payroll.deductions.priorities');

Route::post('/payroll/deductions/update-priorities', [PitemsController::class, 'updateDeductionPriorities'])
    ->name('payroll.deductions.update-priorities');


    Route::get('vaudit', [AuditController::class, 'index'])->name('vaudit.index');
    // In your routes file (web.php)
Route::get('/audit/view-pdf', [AuditController::class, 'viewPdf'])->name('audit.viewPdf');
Route::get('/audit/export-pdf', [AuditController::class, 'exportPdf'])->name('audit.exportPdf');
    Route::middleware(['auth'])->prefix('audit')->name('audit.')->group(function () {
   // Route::get('/', [AuditController::class, 'index'])->name('index');
    Route::get('/data', [AuditController::class, 'getData'])->name('getData');
    Route::get('/detail/{id}', [AuditController::class, 'getDetail'])->name('detail');
    Route::get('/export-excel', [AuditController::class, 'exportExcel'])->name('exportExcel');
    Route::get('/export-pdf', [AuditController::class, 'exportPdf'])->name('exportPdf');
});

Route::get('roles', [RolesController::class, 'index'])->name('roles.index');
Route::post('roles', [RolesController::class, 'store'])->name('roles.store');
Route::get('/roles/getall', [RolesController::class, 'getAll'])->name('roles.getall');
Route::post('roles/{id}', [RolesController::class, 'update'])->name('roles.update');
Route::get('/roles/get-dropdown', [RolesController::class, 'getAllBranches'])->name('roles.getDropdown');
//Route::get('mngprol', [Managepayroll::class, 'showPayrollPeriod']);
Route::get('/roles/report', [RolesReportController::class, 'generateReport'])->name('roles.report');
Route::get('/roles/report/download', [RolesReportController::class, 'downloadReport'])->name('roles.report.download');

 
Route::get('papprove', [PaytrackerController::class, 'index'])->name('papprove.index');
Route::post('/payroll/approve', [PayrollApprovalController::class, 'approvePayroll'])
    ->name('payroll.approve');

// In web.php
Route::get('rapprove', [RegistrationApprovalController::class, 'index'])->name('rapprove.index');
Route::prefix('registration')->group(function () {
    Route::get('/approvals', [RegistrationApprovalController::class, 'index'])->name('registration.approvals.index');
});
Route::get('/registration-approvals/{id}', [RegistrationApprovalController::class, 'show'])
    ->name('registration.approvals.show');
    Route::post('/registration-approvals/{id}/approve', [RegistrationApprovalController::class, 'approve'])
    ->name('registration.approvals.approve');

Route::post('/registration-approvals/{id}/reject', [RegistrationApprovalController::class, 'reject'])
    ->name('registration.approvals.reject');

// In web.php
Route::prefix('netpay')->group(function () {
    Route::post('/notify-approver', [NetpayApprovalController::class, 'notifyApprover'])->name('netpay.notify.approver');
    Route::post('/approve', [NetpayApprovalController::class, 'approve'])->name('netpay.approve');
    Route::post('/reject', [NetpayApprovalController::class, 'reject'])->name('netpay.reject');
});

// In web.php
Route::prefix('analytics')->group(function () {
    Route::get('/', [AnalyticsController::class, 'index'])->name('analytics.index');
    Route::post('/dashboard-data', [AnalyticsController::class, 'getDashboardData'])->name('analytics.dashboard.data');
    Route::post('/compare-periods', [AnalyticsController::class, 'comparePeriods'])->name('analytics.compare.periods');
    Route::post('/date-range', [AnalyticsController::class, 'getDateRangeAnalysis'])->name('analytics.date.range');
});
Route::get('/password-expired', [PasswordExpiredController::class, 'show'])
    ->name('password.expired');

Route::post('/password-expired', [PasswordExpiredController::class, 'update'])
    ->name('password.expired.update');
// Session keep-alive ping
Route::post('/session/ping', function () {
    // Touching the session is enough to reset its expiry
    session(['last_ping' => now()]);
    return response()->json(['ok' => true]);
})->middleware('auth')->name('session.ping');

});






require __DIR__.'/auth.php';
