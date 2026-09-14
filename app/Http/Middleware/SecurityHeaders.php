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
 
        /* â”€â”€ Standard security headers â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
 
       /* â”€â”€ Detect Vite dev server â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
// ws:// is ONLY valid in connect-src, not script-src or style-src
$isLocalDev        = app()->environment('local') && config('app.debug');
$viteDevHttp       = $isLocalDev ? 'http://localhost:5173' : '';
$viteDevConnectSrc = $isLocalDev
    ? 'http://localhost:5173 ws://localhost:5173 ws://[::1]:5173'
    : '';
 
      
 
        /* â”€â”€ script-src / script-src-elem â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
        $scriptSrc = implode(' ', array_filter([
            "'self'",
            "'nonce-{$nonce}'",
            "'sha256-g/A5tLJqGSTfVFTaD65HcnsNfrBxU3J+UqgD+z89S1U='",
            $viteDevHttp,
            'https://cdn.jsdelivr.net',
        ]));
 
        /* â”€â”€ Trusted form-action origins â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
$formActionSrc = implode(' ', array_filter([
    "'self'",
    'https://corepay.jubileekenya.com',
    'https://corepay.zamilicore.com',
]));
 
$frameSrc = implode(' ', [
    "'self'",
    'blob:',
    'https://corepay.jubileekenya.com',
]);
 
        $styleSrc = implode(' ', array_filter([
            "'self'",
            "'nonce-{$nonce}'",
            "'unsafe-inline'",   // âœ… covers SweetAlert2 + plugin injected styles
            $viteDevHttp,
        ]));
 
       
 
        /* â”€â”€ img-src â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
        $imgSrc = implode(' ', [
            "'self'", 'data:', 'blob:',
            'https://corepay.zamilicore.com',
            'https://corepay.jubileekenya.com',
        ]);
 
        /* â”€â”€ connect-src â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
        $connectSrc = implode(' ', array_filter([
            "'self'",
            'blob:',
            $viteDevConnectSrc,
        ]));
 
 
/* â”€â”€ Build CSP â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
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
    "object-src 'self' blob:;",      
    "connect-src {$connectSrc};",
    "form-action {$formActionSrc};",
    "base-uri 'self';",
    "frame-ancestors 'self';",
]);
 
        $response->headers->set('Content-Security-Policy', $csp);
 
        /* â”€â”€ No-cache for sensitive routes â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
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