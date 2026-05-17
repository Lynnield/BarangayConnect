<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $nonce = rtrim(strtr(base64_encode(random_bytes(16)), '+/', '-_'), '=');

        View::share('cspNonce', $nonce);
        $request->attributes->set('csp_nonce', $nonce);

        /** @var Response $response */
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', (string) config('security.referrer_policy', 'strict-origin-when-cross-origin'));

        $frame = config('security.frame_options', 'SAMEORIGIN');
        if (is_string($frame) && $frame !== '') {
            $response->headers->set('X-Frame-Options', strtoupper($frame));
        }

        if (config('security.hsts.enabled') && $request->secure()) {
            $maxAge = (int) config('security.hsts.max_age', 63072000);
            $value = 'max-age='.$maxAge;
            if (config('security.hsts.include_subdomains')) {
                $value .= '; includeSubDomains';
            }
            if (config('security.hsts.preload')) {
                $value .= '; preload';
            }
            $response->headers->set('Strict-Transport-Security', $value);
        }

        $cspDirective = $this->buildContentSecurityPolicy($nonce, $request);
        if ($cspDirective !== null) {
            $headerName = config('security.csp.report_only')
                ? 'Content-Security-Policy-Report-Only'
                : 'Content-Security-Policy';

            $response->headers->set($headerName, $cspDirective);
        }

        return $response;
    }

    protected function buildContentSecurityPolicy(string $nonce, Request $request): ?string
    {
        if (! config('security.csp.enabled')) {
            return null;
        }

        $viteDev = config('app.debug') && $this->isLocalRequest($request);

        $scripts = ["'self'", "'nonce-{$nonce}'"];

        /*
         | Tailwind Play CDN evaluates config with eval unless using the pre-built bundle exclusively.
        */
        $scripts[] = "'unsafe-eval'";
        $scripts[] = 'https://cdn.tailwindcss.com';
        $scripts[] = 'https://unpkg.com';
        $scripts[] = 'https://cdn.jsdelivr.net';

        if ($viteDev) {
            $scripts[] = 'http://127.0.0.1:*';
            $scripts[] = 'http://localhost:*';
        }

        $styles = ["'self'", "'unsafe-inline'", 'https://fonts.googleapis.com'];

        if ($viteDev) {
            $styles[] = 'http://127.0.0.1:*';
            $styles[] = 'http://localhost:*';
        }

        $fonts = ["'self'", 'https://fonts.gstatic.com', 'data:'];

        $img = ["'self'", 'data:', 'blob:', 'https:'];

        $connect = ["'self'"];
        if ($viteDev) {
            $connect[] = 'http://127.0.0.1:*';
            $connect[] = 'http://localhost:*';
            $connect[] = 'ws://127.0.0.1:*';
            $connect[] = 'ws://localhost:*';
        }

        $frameOpt = strtoupper((string) config('security.frame_options', 'SAMEORIGIN'));
        $frameAncestors = match ($frameOpt) {
            'DENY', '' => "'none'",
            default => "'self'",
        };

        $parts = [
            "default-src 'self'",
            'script-src '.implode(' ', $scripts),
            "script-src-attr 'unsafe-inline'",
            'style-src '.implode(' ', $styles),
            'font-src '.implode(' ', $fonts),
            'img-src '.implode(' ', $img),
            'connect-src '.implode(' ', $connect),
            'frame-ancestors '.$frameAncestors,
            'base-uri '."'self'",
            "form-action 'self'",
        ];

        if ($request->secure()) {
            $parts[] = 'upgrade-insecure-requests';
        }

        return implode('; ', $parts).';';
    }

    protected function isLocalRequest(Request $request): bool
    {
        $host = strtolower((string) $request->getHost());

        return in_array($host, ['127.0.0.1', 'localhost'], true);
    }
}
