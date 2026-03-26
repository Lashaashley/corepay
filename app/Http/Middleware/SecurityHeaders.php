<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    /**
     * Routes that serve sensitive payroll data — get strict no-cache headers.
     */
    protected array $sensitiveRoutes = [
        'dashboard*', 'agents*',   'payroll*', 'preports*',
        'mngprol*',   'musers*',   'vaudit*',  'pitems*',
        'closep*',    'papprove*', 'rapprove*','analytics*',
        'areports*',  'aimport*',  'nagent*',  'profile*',
    ];

    public function handle(Request $request, Closure $next)
    {

    $nonce = base64_encode(random_bytes(16));
    
    // Share it with all Blade views
    app()->instance('csp-nonce', $nonce);
    view()->share('cspNonce', $nonce);

        $response = $next($request);

        /* ── Anti-clickjacking ──────────────────────────────── */
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        // NOTE: Using SAMEORIGIN instead of DENY because your PDF modal
        // renders blob: iframes from the same origin. DENY would break that.

        /* ── MIME sniffing prevention ────────────────────────── */
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        /* ── Legacy XSS filter ───────────────────────────────── */
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        /* ── Referrer policy ─────────────────────────────────── */
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        /* ── Permissions policy ──────────────────────────────── */
        $response->headers->set(
            'Permissions-Policy',
            'camera=(), microphone=(), geolocation=(), payment=()'
        );

        /* ── Remove server fingerprinting ────────────────────── */
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        /* ── HSTS — uncomment ONLY after SSL certificate is live ─ */
        // $response->headers->set(
        //     'Strict-Transport-Security',
        //     'max-age=31536000; includeSubDomains; preload'
        // );

        /* ── Content Security Policy ─────────────────────────── */
        /*
         * Trusted CDN sources used across the app:
         *   - cdnjs.cloudflare.com  → jQuery, Highcharts, FontAwesome
         *   - cdn.datatables.net    → DataTables
         *   - cdn.jsdelivr.net      → Bootstrap CSS in reports
         *   - code.jquery.com       → jQuery fallback
         *   - fonts.googleapis.com  → DM Sans, Syne fonts
         *   - cdn-uicons.flaticon.com → UI icons
         *   - fonts.gstatic.com     → Google font files
         *
         * blob: is required for PDF viewer modals — the app creates
         * a Blob URL from base64 PDF data and loads it in an iframe.
         *
         * unsafe-inline is required because blade files use <style>/<script> blocks.
         * unsafe-eval is required by Highcharts chart library.
         *
         * Long-term improvement: move inline JS to .js files and use nonces.
         * For now this is the correct production config for this architecture.
         */

        $trustedScriptSrc = implode(' ', [
            "'self'",
            "'nonce-{$nonce}'",
            "'unsafe-eval'",
            'https://cdnjs.cloudflare.com',
            'http://cdnjs.cloudflare.com',    // ← temp: remove once HTTP src fixed to HTTPS
            'https://cdn.datatables.net',
            'https://cdn.jsdelivr.net',
            'https://code.jquery.com',
            'https://fonts.googleapis.com',
            'https://cdn-uicons.flaticon.com',
            $viteDevSrc ?? '',
        ]);

        $trustedStyleSrc = implode(' ', [
            "'self'",
            "'nonce-{$nonce}'",
            'https://fonts.googleapis.com',
            'https://cdnjs.cloudflare.com',
            'https://cdn.datatables.net',
            'https://cdn.jsdelivr.net',       // ← Bootstrap in reports
            'https://cdn-uicons.flaticon.com',
        ]);

        $trustedFontSrc = implode(' ', [
            "'self'",
            'https://fonts.gstatic.com',
            'https://cdnjs.cloudflare.com',
            'https://cdn-uicons.flaticon.com',
            'https://cdn.jsdelivr.net',
        ]);

        $csp = implode(' ', [
            // Catch-all fallback
            "default-src 'self';",

            // JavaScript
            "script-src {$trustedScriptSrc};",

            // CSS
            "style-src {$trustedStyleSrc};",

            // Fonts
            "font-src {$trustedFontSrc};",

            // Images — allow data: for base64 images, blob: for generated images
            "img-src 'self' data: blob: https:;",

            // ── CRITICAL FIX ──────────────────────────────────────
            // frame-src must explicitly allow blob: for the PDF modal.
            // Without this, blob: falls back to default-src 'self' which
            // does NOT include blob: and blocks the iframe.
            "frame-src 'self' blob:;",

            // Workers (used by some PDF/Highcharts features)
            "worker-src 'self' blob:;",

            // AJAX/fetch + source maps
            // jsdelivr needed for Bootstrap CSS source map (DevTools)
            "connect-src 'self' https://cdn.jsdelivr.net;",

            // Explicitly restrict form submissions to same origin
            "form-action 'self';",

            // Explicitly restrict base tag to same origin
            "base-uri 'self';",

            // Block Flash/Java plugins entirely
            "object-src 'none';",

            // Prevent this app being embedded in another site
            // (works alongside X-Frame-Options)
            "frame-ancestors 'self';",
        ]);

        $response->headers->set('Content-Security-Policy', $csp);

        /* ── No-cache for sensitive routes ───────────────────── */
        foreach ($this->sensitiveRoutes as $pattern) {
            if ($request->is($pattern)) {
                $response->headers->set(
                    'Cache-Control',
                    'no-store, no-cache, must-revalidate, private'
                );
                $response->headers->set('Pragma', 'no-cache');
                break;
            }
        }

        return $response;
    }
}