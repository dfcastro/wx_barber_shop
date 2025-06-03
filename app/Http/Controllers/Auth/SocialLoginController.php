<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request; // Importar Request
use Illuminate\Support\Facades\Log; // Para logs

class SocialLoginController extends Controller
{
    /**
     * Redireciona o usuário para a página de autenticação do provedor OAuth.
     *
     * @param  string $provider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToProvider(string $provider)
    {
        Log::info("[SocialLogin] redirectToProvider chamado. Provedor recebido: " . $provider); // << LOG 1

        if (!in_array($provider, ['google'])) { // Adicione outros provedores aqui no futuro, ex: 'facebook'
            Log::warning("[SocialLogin] Provedor não suportado: " . $provider); // << LOG 2
            return redirect('/login')->with('error', 'Provedor de login social não suportado.');
        }

        try {
            Log::info("[SocialLogin] Tentando redirecionar para o driver do provedor: " . $provider); // << LOG 3
            return Socialite::driver($provider)->redirect();
        } catch (\Exception $e) {
            Log::error("[SocialLogin] Erro ao redirecionar para o provedor {$provider}: " . $e->getMessage()); // << LOG 4
            // Para depuração mais detalhada, você pode logar o stack trace:
            // Log::error($e->getTraceAsString());
            return redirect('/login')->with('error', 'Não foi possível conectar com o serviço de login. Por favor, tente novamente.');
        }
    }
    /**
     * Obtém as informações do usuário do provedor OAuth.
     *
     * @param  string $provider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleProviderCallback(string $provider, Request $request)
    {
        if (!in_array($provider, ['google'])) {
            return redirect('/login')->with('error', 'Provedor de login social não suportado.');
        }

        try {
            if ($request->has('error')) {
                Log::warning("Provedor {$provider} retornou um erro: " . $request->input('error_description', $request->input('error')));
                return redirect('/login')->with('error', 'Acesso negado pelo provedor de login.');
            }

            $socialUser = Socialite::driver($provider)->user();

            $user = User::where('provider_name', $provider)
                ->where('provider_id', $socialUser->getId())
                ->first();

            if (!$user) {
                $user = User::where('email', $socialUser->getEmail())->first();

                if ($user) {
                    $user->provider_name = $provider;
                    $user->provider_id = $socialUser->getId();
                    $user->provider_avatar = $socialUser->getAvatar();
                    // Não atualizamos o phone_number aqui, pois ele pode já ter um
                    $user->save();
                } else {
                    $user = User::create([
                        'name' => $socialUser->getName(),
                        'email' => $socialUser->getEmail(),
                        'password' => Hash::make(Str::random(24)),
                        'provider_name' => $provider,
                        'provider_id' => $socialUser->getId(),
                        'provider_avatar' => $socialUser->getAvatar(),
                        'email_verified_at' => now(),
                        // phone_number será nulo aqui se não vier do provedor (o que é o caso comum)
                    ]);
                }
            }

            Auth::login($user, true);

            // >>> INÍCIO DA LÓGICA PARA VERIFICAR TELEFONE <<<
            if (empty($user->phone_number)) {
                // Coloca uma flag na sessão para indicar que o perfil precisa ser completado.
                // Isso pode ser usado pelo middleware ou na view do perfil.
                session(['profile_incomplete_phone' => true]);

                // Redireciona para a página de edição de perfil com uma mensagem.
                return redirect()->route('profile.edit')
                    ->with('status', 'profile-incomplete') // Um status para identificar a razão
                    ->with('info', 'Bem-vindo(a)! Por favor, complete seu cadastro adicionando um número de telefone para continuar.');
            }
            // Remove a flag se o telefone já estiver preenchido (caso raro aqui, mas bom para consistência)
            session()->forget('profile_incomplete_phone');
            // >>> FIM DA LÓGICA PARA VERIFICAR TELEFONE <<<

            return redirect()->intended('/dashboard');

        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            Log::error("Erro de estado inválido com o provedor {$provider} (InvalidStateException): " . $e->getMessage());
            return redirect('/login')->with('error', 'Ocorreu um erro de validação com o login social. Por favor, tente novamente.');
        } catch (\Exception $e) {
            Log::error("Erro no callback do provedor {$provider}: " . $e->getMessage() . ' Stack: ' . $e->getTraceAsString());
            return redirect('/login')->with('error', 'Não foi possível realizar o login social. Por favor, tente novamente.');
        }
    }
}