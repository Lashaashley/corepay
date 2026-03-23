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

        // ── Global middleware (runs on every request) ──────────
        $middleware->append(\App\Http\Middleware\SanitizeInput::class);

        // ── Web group middleware (runs on all web routes) ──────
        $middleware->web(append: [
            \App\Http\Middleware\SharePayrollData::class,
            \App\Http\Middleware\SecurityHeaders::class,   // ← security headers
            \App\Http\Middleware\LoadMenuData::class,
        ]);

        // ── Named middleware aliases ───────────────────────────
        $middleware->alias([
            'payroll.selected' => \App\Http\Middleware\EnsurePayrollSelected::class,
            'payroll.access'   => \App\Http\Middleware\CheckPayrollAccess::class,
            'throttle.user'    => \App\Http\Middleware\ThrottleByUser::class,
            'audit'            => \App\Http\Middleware\AuditTrail::class,
            '2fa'              => \App\Http\Middleware\TwoFactorMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();