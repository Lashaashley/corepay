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

        /* ── Trusted origins ─────────────────────────────────────────────── */
        $cdn = implode(' ', [
            'https://cdnjs.cloudflare.com',
            'https://cdn.datatables.net',
            'https://cdn.jsdelivr.net',
            'https://cdn-uicons.flaticon.com',
        ]);

        /* ── script-src / script-src-elem ───────────────────────────────── */
        $scriptSrc = implode(' ', array_filter([
            "'self'",
            "'nonce-{$nonce}'",
            "'sha256-g/A5tLJqGSTfVFTaD65HcnsNfrBxU3J+UqgD+z89S1U='",
            $cdn,
            'https://code.jquery.com',
            $viteDevServer,
        ]));

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
            $cdn,
            $viteDevServer,
        ]));

        /* ── font-src ────────────────────────────────────────────────────── */
        $fontSrc = implode(' ', [
            "'self'",
            'https://cdnjs.cloudflare.com',
            'https://cdn-uicons.flaticon.com',
            'https://cdn.jsdelivr.net',
        ]);

        /* ── img-src ─────────────────────────────────────────────────────── */
        $imgSrc = implode(' ', [
            "'self'", 'data:', 'blob:',
            $cdn,
            'https://corepay.zamilicore.com',
            'https://corepay.jubileeKenya.com',
        ]);

        /* ── connect-src ─────────────────────────────────────────────────── */
        $connectSrc = implode(' ', array_filter([
            "'self'",
            'blob:',
            'https://cdn.jsdelivr.net',
            $viteDevServer,
        ]));

        /* ── Build CSP ───────────────────────────────────────────────────── */
        $csp = implode(' ', [
            "default-src 'self';",

            // Scripts — both directives set explicitly so Chrome never
            // falls back to default-src for <script> elements
            "script-src {$scriptSrc};",
            "script-src-elem {$scriptSrc};",

            // Styles — both directives set explicitly so Chrome never
            // falls back to default-src for <link>/<style> elements
            "style-src {$styleSrc};",
            "style-src-elem {$styleSrc};",

            // Inline style attributes (style="...") — unsafe-inline needed
            // for DeskApp vendor scripts and jQuery plugins
            "style-src-attr 'unsafe-inline';",

            "font-src {$fontSrc};",
            "img-src {$imgSrc};",

            // blob: in frame-src allows PDF rendering via blob URLs
            "frame-src 'self' blob:;",
            "worker-src 'self' blob:;",
            "connect-src {$connectSrc};",
            "form-action 'self';",
            "base-uri 'self';",
            "object-src 'none';",
            "frame-ancestors 'self';",
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