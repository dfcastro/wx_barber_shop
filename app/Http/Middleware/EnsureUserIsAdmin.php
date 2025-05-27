<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Importe a facade Auth
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verifica se o usuário está autenticado E se ele é um administrador
        if (Auth::check() && Auth::user()->is_admin) {
            return $next($request); // Se for admin, permite o acesso à rota
        }

        // Se não for admin (ou não estiver logado), redireciona para a home ou página de login
        // ou retorna um erro 403 (Proibido). Para este caso, redirecionar pode ser melhor.
        // abort(403, 'Acesso não autorizado.');
        return redirect('/')->with('error', 'Acesso não autorizado.');
    }
}