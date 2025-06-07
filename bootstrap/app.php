<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use App\Http\Middleware\IsSuperAdmin;
use App\Http\Middleware\SetUserLocale;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        
        $middleware->api([
            \Illuminate\Http\Middleware\HandleCors::class, // ✅ Middleware de CORS
        ]);

        // Alias para middleware personalizados (antes en Kernel.php)
        $middleware->alias([
            'superadmin' => IsSuperAdmin::class,
            'locale' => \App\Http\Middleware\SetUserLocale::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
