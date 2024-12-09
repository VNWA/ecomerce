<?php

use App\Http\Middleware\DatabaseConnection;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(DatabaseConnection::class);
        $middleware->web(append: [
            HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
            DatabaseConnection::class
        ]);
        $middleware->alias([
            'throttle.json' => \App\Http\Middleware\ThrottleJsonMiddleware::class,
            'cors' => \App\Http\Middleware\Cors::class, // Thêm dòng này
            'customer' => \App\Http\Middleware\AuthCustomer::class, // Thêm dòng này
            'vnwa.api' => \App\Http\Middleware\VnwaApi::class, // Thêm dòng này
        ]);
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {

        //
    })->create();
