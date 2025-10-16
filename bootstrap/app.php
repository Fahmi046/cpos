<?php

use App\Http\Middleware\CheckRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )

    ->withMiddleware(function (Middleware $middleware): void {
        // Tambahkan alias middleware
        $middleware->alias([
            'checkRole' => CheckRole::class,
        ]);

        // Jika ingin global (opsional)
        // $middleware->append(CheckRole::class);

        // Bisa juga menambahkan ke grup 'web' jika perlu
        // $middleware->appendToGroup('web', [CheckRole::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
