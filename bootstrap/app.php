<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(\App\Http\Middleware\SanitizeInput::class);
        $middleware->alias([
            'payroll.selected' => \App\Http\Middleware\EnsurePayrollSelected::class,
             'payroll.access' => \App\Http\Middleware\CheckPayrollAccess::class,
            'throttle.user' => \App\Http\Middleware\ThrottleByUser::class,
            'audit' => \App\Http\Middleware\AuditTrail::class,
            '2fa' => \App\Http\Middleware\TwoFactorMiddleware::class,
        ]);
    })
    ->withMiddleware(function (Middleware $middleware) {
    $middleware->web(append: [
        \App\Http\Middleware\SharePayrollData::class, 
        \App\Http\Middleware\LoadMenuData::class,// ✅ Add to web middleware group
    ]);
    
    $middleware->alias([
        'payroll.selected' => \App\Http\Middleware\EnsurePayrollSelected::class,
    ]);
})
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
