<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // Para gerar hash da senha
use Illuminate\Support\Str;          // Para gerar string aleatória
use Illuminate\Auth\Events\Registered; // Para disparar o evento de registro
use Illuminate\Validation\Rules;       // Para regras de validação de senha (se necessário)


class ClientController extends Controller
{
    public function index()
    {
        return view('admin.clients.index');
    }

    public function show(User $client)
    {
        // Carregar os agendamentos do cliente, ordenados pela data do agendamento (mais recentes primeiro)
        // Também carregar o serviço relacionado a cada agendamento para exibir o nome do serviço
        $client->load(['appointments' => function ($query) {
            $query->with('service:id,name')->orderBy('appointment_time', 'desc');
        }]);

        if ($client->is_admin) {
            // Redirecionar ou mostrar erro se tentar ver detalhes de um admin por esta rota
            // return redirect()->route('admin.clients.index')->with('error', 'Não é permitido ver detalhes de administradores por esta interface.');
        }

        return view('admin.clients.show', compact('client'));
    }

    /**
     * Mostra o formulário para editar o cliente especificado.
     *
     * @param  \App\Models\User $client
     * @return \Illuminate\View\View
     */
    public function edit(User $client)
    {
        // Garante que não estamos tentando editar um admin por esta interface
        if ($client->is_admin) {
            return redirect()->route('admin.clients.index')->with('error', 'Não é permitido editar administradores por esta interface.');
        }
        return view('admin.clients.edit', compact('client'));
    }

    /**
     * Atualiza o cliente especificado no banco de dados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User $client
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $client)
    {
        // Garante que não estamos tentando editar um admin por esta interface
        if ($client->is_admin) {
            return redirect()->route('admin.clients.index')->with('error', 'Não é permitido editar administradores por esta interface.');
        }

        // Validação dos dados (vamos refinar isso com um Form Request depois)
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:20'], // Pode ser 'required' se você preferir
        ]);

        $client->name = $validatedData['name'];
        $client->phone_number = $validatedData['phone_number'];
        $client->save();

        return redirect()->route('admin.clients.show', $client)->with('success', 'Cliente atualizado com sucesso!');
    }

    /**
     * Mostra o formulário para criar um novo cliente.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.clients.create');
    }

    /**
     * Armazena um novo cliente criado pelo admin.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'phone_number' => ['required', 'string', 'max:20'], // Ou suas regras específicas
            // Não pedimos senha ao admin, vamos gerar uma ou deixar o cliente definir
        ]);

        // Gera uma senha aleatória forte (o cliente pode mudá-la via "Esqueci minha senha")
        $generatedPassword = Str::random(12); // Ou use Rules\Password::defaults() para complexidade

        $client = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'phone_number' => $validatedData['phone_number'],
            'password' => Hash::make($generatedPassword), // Define a senha aleatória
            'is_admin' => false, // Garante que é um cliente
            'is_active' => true,  // Cliente criado pelo admin já nasce ativo
            'email_verified_at' => now(), // Admin está criando, podemos considerar verificado
        ]);

        event(new Registered($client)); // Dispara o evento de registro

        // Notificar o cliente sobre a criação da conta e a senha temporária ou como proceder?
        // Isso pode ser feito com um Mailable. Por agora, vamos focar na criação.

        return redirect()->route('admin.clients.index')->with('message', 'Cliente ' . $client->name . ' criado com sucesso!');
    }
}