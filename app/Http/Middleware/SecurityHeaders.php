<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    protected $sensitiveRoutes = [
        'dashboard*', 'agents*', 'payroll*' , 'rapprove*', 'papprove*',
        'preports*', 'mngprol*', 'musers*', 'vaudit*',
    ];

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Anti-clickjacking
        $response->headers->set('X-Frame-Options', 'DENY');

        // MIME sniffing prevention
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // XSS filter (legacy browsers)
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // HSTS — only uncomment on production with SSL
        // $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        

        // Remove server fingerprinting
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        // CSP
        $response->headers->set('Content-Security-Policy',
            "default-src 'self'; " .
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' " .
                "https://cdnjs.cloudflare.com https://cdn.datatables.net " .
                "https://cdn.jsdelivr.net https://code.jquery.com " .
                "https://fonts.googleapis.com https://cdn-uicons.flaticon.com; " .
            "style-src 'self' 'unsafe-inline' " .
                "https://fonts.googleapis.com https://cdnjs.cloudflare.com " .
                "https://cdn.datatables.net https://cdn-uicons.flaticon.com; " .
            "font-src 'self' https://fonts.gstatic.com " .
                "https://cdnjs.cloudflare.com https://cdn-uicons.flaticon.com; " .
            "img-src 'self' data: blob: https:; " .
            "connect-src 'self'; " .
    "form-action 'self'; " .        // ← fixes the fallback alert
    "base-uri 'self'; " .           // ← fixes wildcard directive alert
    "object-src 'none'; " .
            "frame-ancestors 'none';"
        );

        // No caching for sensitive pages
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