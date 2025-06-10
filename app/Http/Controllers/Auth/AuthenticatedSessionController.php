<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // INÍCIO DA LÓGICA DE REDIRECIONAMENTO
        if ($request->user()->is_admin) {
            return redirect()->intended(route('admin.dashboard'));
        }

        // Para clientes, o comportamento padrão é bom
        return redirect()->intended(route('dashboard'));
        // FIM DA LÓGICA DE REDIRECIONAMENTO
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
    public function redirectTo(Request $request)
    {
        if (Auth::user()->is_admin) {
            return route('admin.dashboard');
        }
        return route('dashboard'); // Para clientes normais
    }
    protected function authenticated(Request $request, $user)
    {
        if ($user->is_admin) {
            return redirect()->route('admin.dashboard');
        }

        // Para clientes, pode redirecionar para o dashboard padrão ou para a página de agendamento
        return redirect()->route('dashboard'); // Ou 'booking.index'
    }
}
