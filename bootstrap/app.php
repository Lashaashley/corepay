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
            //'throttle.user'    => \App\Http\Middleware\ThrottleByUser::class, based on deployment
            'audit'            => \App\Http\Middleware\AuditTrail::class,
            '2fa'              => \App\Http\Middleware\TwoFactorMiddleware::class,
        ]);
    })
 // bootstrap/app.php
->withExceptions(function (Exceptions $exceptions) {

    $exceptions->respond(function (\Symfony\Component\HttpFoundation\Response $response) {
        $nonce = base64_encode(random_bytes(16));

        // ✅ Ensure security headers appear on ALL responses
        // including 404s, 500s, and any error page that bypasses web middleware
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');

        // ✅ Minimal CSP for error pages — no nonce needed since
        // error pages have no inline scripts
        if (!$response->headers->has('Content-Security-Policy')) {
            $response->headers->set(
                'Content-Security-Policy',
                implode('; ', [
                    "default-src 'self'",
                    "script-src 'self'",
                    "script-src 'self' 'nonce-{$nonce}'",
                    "style-src 'self' 'nonce-{$nonce}'",
                    "object-src 'none'",
                    "frame-ancestors 'self'",
                    "base-uri 'self'",
                    "form-action 'self'", 
                ])
            );
        }

        return $response;
    });

})->create();