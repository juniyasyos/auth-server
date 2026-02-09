<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectToFrontend
{
    /**
     * Frontend port to redirect to.
     */
    protected string $frontendPort = '3100';

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Don't redirect API routes
        if ($request->is('api/*') || $request->is('sso/*')) {
            return $next($request);
        }

        // Don't redirect Filament admin routes
        if ($request->is('admin/*')) {
            return $next($request);
        }

        // Check if frontend is accessible on port 3100
        if ($this->isFrontendAccessible()) {
            $frontendUrl = $this->getFrontendUrl($request->getPathInfo());
            return redirect($frontendUrl);
        }

        // If frontend not accessible, continue with Laravel
        return $next($request);
    }

    /**
     * Check if frontend is accessible on the configured port.
     */
    protected function isFrontendAccessible(): bool
    {
        $frontendHost = $this->getFrontendHost();

        try {
            $fp = @fsockopen($frontendHost, (int)$this->frontendPort, $errno, $errstr, 2);
            if (is_resource($fp)) {
                fclose($fp);
                return true;
            }
        } catch (\Exception $e) {
            // Frontend not accessible
        }

        return false;
    }

    /**
     * Get the frontend host.
     */
    protected function getFrontendHost(): string
    {
        $host = env('FRONTEND_HOST', 'localhost');

        // If running in Docker or on different host, use the same as current request
        if (env('FRONTEND_HOST') === null) {
            // Use same host as current request
            return request()->getHost();
        }

        return $host;
    }

    /**
     * Get the frontend URL.
     */
    protected function getFrontendUrl(string $path = ''): string
    {
        $scheme = 'http';
        $host = $this->getFrontendHost();
        $port = $this->frontendPort;

        // Use https if current request is https
        if (request()->secure()) {
            $scheme = 'https';
        }

        // Construct URL
        $baseUrl = "{$scheme}://{$host}:{$port}";

        // Remove leading slash for concatenation
        if ($path === '/' || $path === '') {
            return $baseUrl;
        }

        return $baseUrl . $path;
    }
}
