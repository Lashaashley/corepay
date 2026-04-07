<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    /**
     * Routes that render charts and require unsafe-eval (Highcharts).
     * unsafe-eval is scoped only to these routes — not the login page or
     * any route that doesn't load Highcharts.
     */
    protected array $chartRoutes = [
        'dashboard*', 'analytics*', 'areports*', 'preports*',
    ];

    /**
     * Routes that serve sensitive payroll data — get strict no-cache headers.
     */
    protected array $sensitiveRoutes = [
     '/',          // ← ADD: login page contains a CSRF token in HTML — must not cache
    'login',      // ← ADD: belt-and-suspenders for the POST route
    'dashboard*', 'agents*',   'payroll*', 'preports*',
    'mngprol*',   'musers*',   'vaudit*',  'pitems*',
    'closep*',    'papprove*', 'rapprove*','analytics*',
    'areports*',  'aimport*',  'nagent*',  'profile*',
    ];

    public function handle(Request $request, Closure $next)
    {
        $nonce = base64_encode(random_bytes(16));

        // Share nonce with all Blade views so templates can use
        // nonce="{{ $cspNonce }}" on every inline <script> and <style>
        app()->instance('csp-nonce', $nonce);
        view()->share('cspNonce', $nonce);

        $response = $next($request);

        /* ── Anti-clickjacking ──────────────────────────────── */
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        // NOTE: SAMEORIGIN (not DENY) because PDF modal uses blob: iframes
        // from the same origin. DENY would break that.

        /* ── MIME sniffing prevention ────────────────────────── */
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        /* ── Legacy XSS filter (deprecated but harmless) ─────── */
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

        /* ── HSTS ────────────────────────────────────────────── */
        // SSL certificate is live — enforce HTTPS for 1 year on all subdomains.
        // To submit to the HSTS preload list later, add '; preload' and visit
        // https://hstspreload.org
        $response->headers->set(
            'Strict-Transport-Security',
            'max-age=31536000; includeSubDomains'
        );

        /* ── Content Security Policy ─────────────────────────── */
        /*
         * Trusted CDN sources used across the app:
         *   - cdnjs.cloudflare.com  → jQuery, Highcharts, FontAwesome
         *   - cdn.datatables.net    → DataTables
         *   - cdn.jsdelivr.net      → Bootstrap CSS in reports
         *   - code.jquery.com       → jQuery fallback
         *   - fonts.googleapis.com  → DM Sans, Syne fonts (CSS loader only)
         *   - cdn-uicons.flaticon.com → UI icons
         *   - fonts.gstatic.com     → Google font files (woff2)
         *
         * blob: is required for PDF viewer modals — the app creates
         * a Blob URL from base64 PDF data and loads it in an iframe.
         *
         * unsafe-eval is required by Highcharts. It is scoped to chart
         * routes only — the login page and non-chart pages do NOT get it.
         *
         * All CDN references use HTTPS only. The http://cdnjs.cloudflare.com
         * entry has been removed — fix the source reference in the Blade
         * template that loads it over HTTP.
         *
         * Long-term improvement: move inline JS to .js files and use nonces
         * exclusively. For now, nonces are applied to all inline <script>
         * and <style> blocks in Blade templates.
         *
         * NOTE: Google Fonts are loaded via fonts.googleapis.com (CSS) and
         * fonts.gstatic.com (font files). If you self-host the fonts in a
         * future sprint, both of these entries can be removed entirely.
         */

        // unsafe-eval only on pages that actually render Highcharts
        $unsafeEval = $request->is(...$this->chartRoutes) ? "'unsafe-eval'" : '';

        $trustedScriptSrc = implode(' ', array_filter([
            "'self'",
            "'nonce-{$nonce}'",
            $unsafeEval,
            'https://cdnjs.cloudflare.com',
            // http://cdnjs.cloudflare.com removed — fix the template loading
            // Highcharts/jQuery over HTTP and switch to the https:// URL.
            'https://cdn.datatables.net',
            'https://cdn.jsdelivr.net',
            'https://code.jquery.com',
            'https://fonts.googleapis.com',
            'https://cdn-uicons.flaticon.com',
        ]));

        $trustedStyleSrc = implode(' ', [
            "'self'",
            "'nonce-{$nonce}'",
            "'sha256-47DEQpj8HBSa+/TImW+5JCeuQeRkm5NMpJWZG3hSuFU='",
            "'sha256-97ccnT95oLH/xrRBCS77FjKD4RVFxyD8EM48c6GC4ZI='",
            'https://fonts.googleapis.com',
            'https://cdnjs.cloudflare.com',
            'https://cdn.datatables.net',
            'https://cdn.jsdelivr.net',
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

            // Images — enumerate all trusted hosts explicitly.
            // 'https:' wildcard scheme has been removed (was flagged by DAST).
            // data: is needed for base64 chart images.
            // blob: is needed for generated PDF thumbnails.
            "img-src 'self' data: blob: " .
            "https://cdnjs.cloudflare.com " .
            "https://cdn.datatables.net " .
            "https://cdn.jsdelivr.net " .
            "https://cdn-uicons.flaticon.com " .
            "https://corepay.zamilicore.com;",

            // frame-src must explicitly allow blob: for the PDF modal.
            "frame-src 'self' blob:;",

            // Workers (used by some PDF/Highcharts features)
            "worker-src 'self' blob:;",

            // AJAX/fetch + source maps
            "connect-src 'self' https://cdn.jsdelivr.net;",

            // Restrict form submissions to same origin
            "form-action 'self';",

            // Restrict base tag to same origin
            "base-uri 'self';",

            // Block Flash/Java plugins entirely
            "object-src 'none';",

            // Prevent this app being embedded in another site
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
