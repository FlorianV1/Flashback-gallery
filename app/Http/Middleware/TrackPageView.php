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
            try {
                PageView::create([
                    'page' => '/' . ltrim($request->path(), '/'),
                    'ip' => $request->ip(),
                    'user_agent' => mb_substr((string) $request->userAgent(), 0, 255),
                    'referrer' => mb_substr((string) $request->header('referer'), 0, 255),
                ]);
            } catch (\Throwable) {
                // Never let tracking break the response
            }
        }

        return $response;
    }
}
