<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureProfileIsComplete
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Verifica se o usuário está logado e se o telefone está vazio
        // E também verifica a flag da sessão, caso o usuário tente navegar para outra página
        // antes de salvar o telefone na página de perfil.
        if ($user && (empty($user->phone_number) || $request->session()->has('profile_incomplete_phone'))) {
            // Se o usuário já está na página de edição de perfil ou tentando deslogar, permite
            if ($request->routeIs('profile.edit') || $request->routeIs('logout')) {
                return $next($request);
            }

            // Redireciona para a página de edição de perfil com uma mensagem
            return redirect()->route('profile.edit')
                ->with('status', 'profile-incomplete') // Mesmo status usado antes
                ->with('info', 'Por favor, adicione seu número de telefone para continuar utilizando o sistema.');
        }

        return $next($request);
    }
}
