<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditTrail
{
    /**
     * Handle an incoming request.
     * Automatically logs sensitive operations
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Only log POST, PUT, PATCH, DELETE
        if (!in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            return $response;
        }

        // Only log successful requests (2xx responses)
        if ($response->status() >= 200 && $response->status() < 300) {
            $this->logRequest($request, $response);
        }

        return $response;
    }

    protected function logRequest(Request $request, $response)
    {
        $user = Auth::user();
        
        $sensitiveFields = ['password', 'password_confirmation', 'token', 'api_key'];
        $inputData = $request->except($sensitiveFields);

        logAuditTrail(
            $user ? $user->id : null,
            'OTHER',
            'http_request',
            $request->path(),
            null,
            null,
            [
                'method' => $request->method(),
                'route' => $request->path(),
                'input' => $inputData,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'response_code' => $response->status()
            ]
        );
    }
}