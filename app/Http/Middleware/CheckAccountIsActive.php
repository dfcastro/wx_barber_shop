<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User; // Importar o modelo User
use Symfony\Component\HttpFoundation\Response;

class CheckAccountIsActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verifica apenas se há um usuário autenticado
        if (Auth::check()) {
            $user = Auth::user();

            // Garante que $user é uma instância do nosso modelo User (que tem 'is_active')
            // e verifica se a conta está inativa.
            if ($user instanceof User && !$user->is_active) {
                $userEmailForLog = $user->email; // Para log antes de deslogar

                Auth::logout(); // Desloga o usuário

                $request->session()->invalidate(); // Invalida a sessão atual
                $request->session()->regenerateToken(); // Regenera o token da sessão por segurança

                Log::info("[MiddlewareCheckAccountIsActive] Usuário {$userEmailForLog} tentou acessar uma rota com conta INATIVA e foi deslogado.");

                // Redireciona para a página de login com uma mensagem de erro
                return redirect()->route('login')
                                 ->with('error', 'Sua conta foi desativada. Por favor, entre em contato com o suporte.');
            }
        }

        return $next($request); // Permite que a requisição continue se a conta estiver ativa ou se não houver usuário logado
    }
}