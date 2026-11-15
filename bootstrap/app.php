<?php

use App\Http\Middleware\TrackPageView;
use Bugsnag\BugsnagLaravel\OomBootstrapper;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Must run before Application::configure() so Bugsnag can raise the memory
// limit and still deliver the event when the app runs out of memory.
(new OomBootstrapper)->bootstrap();

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(TrackPageView::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
