<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Adiciona o middleware ao grupo 'web'
        // Ele será executado para as rotas que usam este grupo.
        $middleware->web(append: [
            \App\Http\Middleware\CheckAccountIsActive::class, // << ADICIONE ESTA LINHA
        ]);

        // Seus aliases de middleware existentes
        $middleware->alias([
            //'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
            'profile.completed' => \App\Http\Middleware\EnsureProfileIsComplete::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();