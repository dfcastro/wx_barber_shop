<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware; // Importe a classe Middleware

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Adicione seu alias de middleware aqui
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
            // Mantenha outros aliases que possam já existir, como 'auth', 'guest', etc.,
            // se eles forem definidos aqui. O Breeze geralmente já cuida do 'auth'.
        ]);

        // Você também pode registrar middlewares globais, de grupo, etc., aqui
        // Exemplo (não necessário para o nosso 'admin' alias):
        // $middleware->web(append: [
        //     OutroMiddleware::class,
        // ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // ...
    })->create();