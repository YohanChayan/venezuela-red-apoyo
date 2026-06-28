<?php

use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\IdentifyContributor;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // DigitalOcean App Platform terminates TLS at its edge proxy and
        // forwards the request internally over HTTP. Trust the proxy so Laravel
        // honours X-Forwarded-Proto and generates https:// asset URLs.
        $middleware->trustProxies(at: '*');

        $middleware->web(append: [
            IdentifyContributor::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
