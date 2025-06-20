<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User; // <-- CORREÇÃO: Usando o modelo User
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // Para a senha
use Illuminate\Validation\Rule; // Para a validação

class ClientController extends Controller
{
    public function index()
    {
        return view('admin.clients.index');
    }

    public function create()
    {
        return view('admin.clients.create');
    }

    public function store(Request $request)
    {
        // Validação para criar um novo usuário/cliente
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone_number' => ['nullable', 'string', 'max:20'],
        ]);

        // Cria o usuário
        User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'phone_number' => $validatedData['phone_number'],
            'password' => Hash::make('password'), // Define uma senha padrão
        ]);

        return redirect()->route('admin.clients.index')->with('success', 'Cliente criado com sucesso.');
    }

    public function edit(User $client) // <-- CORREÇÃO: Usando Route Model Binding com a variável $client
    {
        // O Laravel já encontra o usuário e injeta na variável $client
        return view('admin.clients.edit', compact('client'));
    }

    public function update(Request $request, User $client)
    {
        // Validação para atualizar o usuário/cliente
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            // CORREÇÃO: Usando a regra de 'unique' correta para a tabela 'users'
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($client->id)],
            'phone_number' => ['nullable', 'string', 'max:20'],
        ]);

        // Atualiza os dados do cliente
        $client->update($validatedData);

        return redirect()->route('admin.clients.index')->with('success', 'Cliente atualizado com sucesso.');
    }

    public function show(User $client)
    {
        return view('admin.clients.show', compact('client'));
    }

   
}