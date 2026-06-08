<?php

namespace App\Http\Middleware;

use App\Models\PageView;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackPageView
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (
            $request->isMethod('GET') &&
            $response->isSuccessful() &&
            ! $request->is('admin*', 'livewire*', 'up', '*/download')
        ) {
            $ip = $request->ip();
            $ua = (string) $request->userAgent();

            $isPrivateIp = ! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
            $isBot = (bool) preg_match('/bot|crawl|spider|slurp|facebookexternalhit/i', $ua);

            if (! $isPrivateIp && ! $isBot) {
                try {
                    PageView::create([
                        'page'       => '/' . ltrim($request->path(), '/'),
                        'ip'         => $ip,
                        'user_agent' => mb_substr($ua, 0, 255),
                        'referrer'   => mb_substr((string) $request->header('referer'), 0, 255),
                    ]);
                } catch (\Throwable) {
                    // Never let tracking break the response
                }
            }
        }

        return $response;
    }
}
