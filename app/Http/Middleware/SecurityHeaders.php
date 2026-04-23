<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    protected array $sensitiveRoutes = [
        '/',          'login',
        'dashboard*', 'agents*',   'payroll*', 'preports*',
        'mngprol*',   'musers*',   'vaudit*',  'pitems*',
        'closep*',    'papprove*', 'rapprove*','analytics*',
        'areports*',  'aimport*',  'nagent*',  'profile*',
    ];

    public function handle(Request $request, Closure $next)
    {
        $nonce = base64_encode(random_bytes(16));
        app()->instance('csp-nonce', $nonce);
        view()->share('cspNonce', $nonce);

        $response = $next($request);

        /* ── Standard security headers ───────────────────────────────────── */
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');

        /* ── Detect Vite dev server ───────────────────────────────────────── */
        $isLocalDev    = app()->environment('local') && config('app.debug');
        $viteDevServer = $isLocalDev
            ? ' http://localhost:5173 http://[::1]:5173 ws://localhost:5173 ws://[::1]:5173'
            : '';

      

        /* ── script-src / script-src-elem ───────────────────────────────── */
        $scriptSrc = implode(' ', array_filter([
            "'self'",
            "'nonce-{$nonce}'",
            "'sha256-g/A5tLJqGSTfVFTaD65HcnsNfrBxU3J+UqgD+z89S1U='",
            $viteDevServer,
        ]));

        /* ── Trusted form-action origins ─────────────────────────────────────── */
$formActionSrc = implode(' ', array_filter([
    "'self'",
    'https://propayuat.jubileeKenya.com',
    'https://corepay.zamilicore.com',
]));

$frameSrc = implode(' ', [
    "'self'",
    'blob:',
    'https://propayuat.jubileeKenya.com',
]);

        /* ── style-src / style-src-elem ──────────────────────────────────
         * unsafe-inline is included here because:
         * 1. SweetAlert2 injects <style> tags at runtime
         * 2. Several jQuery plugins (select2, datatables) inject inline styles
         * 3. The nonce covers our own <style> blocks
         * When a nonce is present, browsers ignore unsafe-inline for
         * nonce-able content — so this only affects un-nonceable plugin styles.
         ─────────────────────────────────────────────────────────────────── */
        $styleSrc = implode(' ', array_filter([
            "'self'",
            "'nonce-{$nonce}'",
            "'unsafe-inline'",   // ✅ covers SweetAlert2 + plugin injected styles
            $viteDevServer,
        ]));

       

        /* ── img-src ─────────────────────────────────────────────────────── */
        $imgSrc = implode(' ', [
            "'self'", 'data:', 'blob:',
            'https://corepay.zamilicore.com',
            'https://corepayuat.jubileeKenya.com',
        ]);

        /* ── connect-src ─────────────────────────────────────────────────── */
        $connectSrc = implode(' ', array_filter([
            "'self'",
            'blob:',
            $viteDevServer,
        ]));

        /* ── Build CSP ───────────────────────────────────────────────────── */
        /* ── Build CSP ───────────────────────────────────────────────────────── */
$csp = implode(' ', [
    "default-src 'self';",
    "script-src {$scriptSrc};",
    "script-src-elem {$scriptSrc};",
    "style-src {$styleSrc};",
    "style-src-elem {$styleSrc};",
    "style-src-attr 'unsafe-inline';",
    "img-src {$imgSrc};",
    "frame-src 'self' blob:;",
    "worker-src 'self' blob:;",
    "connect-src {$connectSrc};",
    "form-action {$formActionSrc};",  
    "base-uri 'self';",
    "object-src 'none';",
    "frame-ancestors 'self';",
    "frame-src {$frameSrc};",
]);

        $response->headers->set('Content-Security-Policy', $csp);

        /* ── No-cache for sensitive routes ───────────────────────────────── */
        foreach ($this->sensitiveRoutes as $pattern) {
            if ($request->is($pattern)) {
                $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, private');
                $response->headers->set('Pragma', 'no-cache');
                break;
            }
        }

        return $response;
    }
}