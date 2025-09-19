<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to set secure API headers and handle CORS.
 * Includes protections against XSS, MIME type sniffing, and clickjacking.
 * Also manages CORS settings based on allowed origins defined in the .env file.
 */
class SecureApiHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        # Block XSS attacks
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        # Block MIME type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        # Block page from being framed
        $response->headers->set('X-Frame-Options', 'DENY');

        # CORS settings
        $allowedOrigins = explode(',', env('ALLOWED_ORIGINS', 'http://localhost:3000'));

        # Get the origin of the request
        $origin = $request->headers->get('Origin');

        # CORS Policy - Allow only specific defined on .env
        if (in_array($origin, $allowedOrigins)) {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
        }

        # Allow credentials (cookies, authorization headers, etc.)
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');

        # Allow specific headers
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');

        # Allow cookies and credentials for sanctum
        $response->headers->set('Access-Control-Allow-Credentials', 'true');

        # Handle preflight requests from react
        $isRequestingOptions = $request->getMethod() === 'OPTIONS';

        if ($isRequestingOptions) {
            return response('', Response::HTTP_NO_CONTENT)
                ->withHeaders($response->headers->all());
        }

        return $response;
    }
}
