<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        
        // تفعيل الـ API ليتعامل مع طلبات الـ Single Page Applications (SPA) والفرونت إند
        $middleware->statefulApi();

        // السماح لمنفذ الفرونت إند بالوصول وتخطي حماية CORS
        $middleware->trustHosts(at: ['localhost:5174']);

        $middleware->alias([
            // Laravel auth
            'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
            'guest' => \Illuminate\Auth\Middleware\RedirectIfAuthenticated::class,
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,

            // Spatie Permission
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();