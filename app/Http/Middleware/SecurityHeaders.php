<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    /**
     * Routes that render Highcharts charts — these get unsafe-eval in script-src.
     * All other routes (including login) do NOT get unsafe-eval.
     */
    protected array $chartRoutes = [
        'dashboard*', 'analytics*', 'areports*', 'preports*',
    ];

    /**
     * Routes serving sensitive data — receive no-store cache headers.
     * Login page is included because it embeds a CSRF token in the HTML body.
     */
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

        /* ── CSP ─────────────────────────────────────────────────────────── */

        /*
         * WHY style-src-elem AND script-src-elem ARE NOW EXPLICIT
         * ─────────────────────────────────────────────────────────
         * Chrome 130+ (and the CSP Level 3 spec) introduced granular fetch
         * directives that split style-src and script-src into element-level
         * and attribute-level controls:
         *
         *   style-src-elem   → controls <link rel="stylesheet"> and <style> tags
         *   style-src-attr   → controls style="..." inline attributes
         *   script-src-elem  → controls <script src="..."> and inline <script>
         *   script-src-attr  → controls onclick="..." inline event handlers
         *
         * When these granular directives are NOT set, Chrome falls back to
         * the parent directive (style-src / script-src). HOWEVER, Chrome 130+
         * changed fallback behaviour: if style-src is set but style-src-elem
         * is not, some versions fall back to default-src instead of style-src
         * for <link> elements. This caused the error:
         *
         *   "style-src-elem was not explicitly set, so default-src is used
         *    as a fallback. The action has been blocked."
         *
         * The fix: set style-src-elem and script-src-elem explicitly with the
         * same values as style-src and script-src respectively.
         *
         * style-src-attr is intentionally NOT set (falls back to style-src).
         * SweetAlert2 hash entries are added here for its injected <style> tags.
         *
         * TODO: Remove sha256 hash entries once SweetAlert2 is upgraded to
         * v11 and the cspNonce option is used instead (see sweetalert-csp-fix.blade.php).
         *
         * TODO: Remove fonts.googleapis.com and fonts.gstatic.com once fonts
         * are self-hosted (see cdn-assets.blade.php).
         */

        $unsafeEval = $request->is(...$this->chartRoutes) ? "'unsafe-eval'" : '';

        // Hosts allowed to serve <script> tags
        $scriptHosts = array_filter([
            "'self'",
            "'nonce-{$nonce}'",
            $unsafeEval,
            "'sha256-g/A5tLJqGSTfVFTaD65HcnsNfrBxU3J+UqgD+z89S1U='",
            'https://cdnjs.cloudflare.com',
            'https://cdn.datatables.net',
            'https://cdn.jsdelivr.net',
            'https://code.jquery.com',
            'https://code.highcharts.com',
            'https://cdn-uicons.flaticon.com',
        ]);

        // Hosts allowed to serve stylesheets + nonce for inline <style> blocks
        // SweetAlert2 injected style hashes included here.
        // Remove sha256 entries after upgrading to SweetAlert2 v11 + cspNonce.
        $styleHosts = array_filter([
    "'self'",
    "'nonce-{$nonce}'",
     "'sha256-47DEQpj8HBSa+/TImW+5JCeuQeRkm5NMpJWZG3hSuFU='",
    "'sha256-97ccnT95oLH/xrRBCS77FjKD4RVFxyD8EM48c6GC4ZI='",
    "'sha256-F8cu7BKRl4BoblXMReCG+tDSra+yGY8oApi037xhk/8='",          // ✅ This covers SweetAlert2's injected <style> tags
    'https://cdnjs.cloudflare.com',
    'https://cdn.datatables.net',
    'https://cdn.jsdelivr.net',
    'https://cdn-uicons.flaticon.com',
]);

        $fontHosts = [
            "'self'",   
            'https://cdnjs.cloudflare.com',
            'https://cdn-uicons.flaticon.com',
            'https://cdn.jsdelivr.net',
        ];

        $trustedScriptSrc = implode(' ', $scriptHosts);
        $trustedStyleSrc  = implode(' ', $styleHosts);
        $trustedFontSrc   = implode(' ', $fontHosts);

        $imgSrc = implode(' ', [
            "'self'", 'data:', 'blob:',
            'https://cdnjs.cloudflare.com',
            'https://cdn.datatables.net',
            'https://cdn.jsdelivr.net',
            'https://cdn-uicons.flaticon.com',
            'https://corepay.zamilicore.com',
            'https://corepay.jubileeKenya.com',
        ]);

        $csp = implode(' ', [
            "default-src 'self';",

            // Script controls
            "script-src {$trustedScriptSrc};",
            // script-src-elem mirrors script-src — explicit to avoid Chrome
            // fallback-to-default-src behaviour on <script> elements
            "script-src-elem {$trustedScriptSrc};",

            // Style controls
            "style-src {$trustedStyleSrc};",
            // style-src-elem mirrors style-src — this is the directive that
            // was missing and caused the Google Fonts / inline style block
            "style-src-elem {$trustedStyleSrc};",

            // style-src-attr: intentionally omitted — falls back to style-src.
            // Inline style attributes (style="...") are governed by style-src.
            // If you need to tighten this further, add:
            // "style-src-attr 'none';" to block all inline style attributes.

            "font-src {$trustedFontSrc};",
            "style-src-attr 'unsafe-inline';", 
            "img-src {$imgSrc};",
            "frame-src 'self' blob:;",
            "worker-src 'self' blob:;",
            "connect-src 'self' https://cdn.jsdelivr.net;",
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