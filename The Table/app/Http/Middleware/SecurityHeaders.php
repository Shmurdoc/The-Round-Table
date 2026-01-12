<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request and add security headers
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Prevent clickjacking
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // XSS Protection
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // MIME type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Referrer Policy
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Content Security Policy (only in production)
        // In development, CSP can interfere with Tailwind CDN and hot reloading
        if (config('app.env') === 'production') {
            $csp = implode('; ', [
                "default-src 'self'",
                "script-src 'self' 'unsafe-inline' 'unsafe-eval' cdn.tailwindcss.com cdn.jsdelivr.net unpkg.com kit.fontawesome.com ka-f.fontawesome.com",
                "style-src 'self' 'unsafe-inline' cdn.tailwindcss.com cdn.jsdelivr.net fonts.googleapis.com ka-f.fontawesome.com",
                "font-src 'self' fonts.gstatic.com cdn.jsdelivr.net ka-f.fontawesome.com",
                "img-src 'self' data: blob: *",
                "connect-src 'self' ka-f.fontawesome.com",
                "frame-ancestors 'self'",
            ]);
            $response->headers->set('Content-Security-Policy', $csp);

            // HSTS (only in production with HTTPS)
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
