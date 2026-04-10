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

        // ── Global middleware ──────────────────────────────────────────────
        $middleware->append(\App\Http\Middleware\SanitizeInput::class);

        // ── Web group middleware ───────────────────────────────────────────
        $middleware->web(append: [
            \App\Http\Middleware\SharePayrollData::class,
            \App\Http\Middleware\SecurityHeaders::class,
            \App\Http\Middleware\LoadMenuData::class,
            \App\Http\Middleware\AbsoluteSessionTimeout::class,
        ]);

        // ── Named middleware aliases ───────────────────────────────────────
        $middleware->alias([
            'payroll.selected' => \App\Http\Middleware\EnsurePayrollSelected::class,
            'payroll.access'   => \App\Http\Middleware\CheckPayrollAccess::class,
            // 'throttle.user' => \App\Http\Middleware\ThrottleByUser::class,
            'audit'            => \App\Http\Middleware\AuditTrail::class,
            '2fa'              => \App\Http\Middleware\TwoFactorMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

        $exceptions->respond(function (\Symfony\Component\HttpFoundation\Response $response) {

            /*
             * WHY THIS BLOCK EXISTS
             * ─────────────────────
             * Error responses (404, 500, 419 CSRF mismatch, etc.) bypass the
             * web middleware stack entirely — SecurityHeaders never runs for them.
             * Without this block, error pages have NO security headers at all,
             * and their CSP is just whatever Laravel's default exception handler
             * emits (nothing), meaning the browser falls back to no CSP.
             *
             * This was confirmed causing real breakage: the login page was
             * triggering a PHP error, landing on an error page with only
             * "default-src 'self'" set (from an earlier incomplete version of
             * this handler), which blocked Google Fonts, SweetAlert2, and all
             * inline scripts/styles.
             *
             * The error page CSP here is intentionally simpler than the main
             * middleware CSP — error pages have no CDN assets, no charts, and
             * no SweetAlert2. They only need enough to render Laravel's built-in
             * error views cleanly.
             */

            // Belt-and-suspenders: set standard headers on all error responses
            $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
            $response->headers->set('X-Content-Type-Options', 'nosniff');
            $response->headers->set('X-XSS-Protection', '1; mode=block');
            $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
            $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');

            // Only set CSP if the middleware hasn't already set one.
            // (On normal responses that went through SecurityHeaders, this is a no-op.)
            if (! $response->headers->has('Content-Security-Policy')) {
                $nonce = base64_encode(random_bytes(16));

                /*
                 * Error page CSP — deliberately minimal.
                 *
                 * style-src-elem and script-src-elem are set explicitly to
                 * avoid the Chrome 130+ fallback-to-default-src behaviour
                 * that was blocking inline styles and scripts on error pages.
                 *
                 * No CDN hosts, no unsafe-eval, no SweetAlert2 hashes needed
                 * — error pages only render Laravel's plain HTML views.
                 */
                $errorNonce = "'nonce-{$nonce}'";

                $response->headers->set(
                    'Content-Security-Policy',
                    implode('; ', [
                        "default-src 'self'",
                        "script-src 'self' {$errorNonce}",
                        "script-src-elem 'self' {$errorNonce}",
                        "style-src 'self' {$errorNonce}",
                        "style-src-elem 'self' {$errorNonce}",
                        "font-src 'self'",
                        "img-src 'self' data:",
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