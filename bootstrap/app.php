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
        $middleware->alias([
            'payroll.selected' => \App\Http\Middleware\EnsurePayrollSelected::class,
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
