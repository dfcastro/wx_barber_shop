<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request; // Importar Request
use Illuminate\Support\Facades\Validator; // Para validação inline, se não usar Form Request

class ClientController extends Controller
{
    public function index()
    {
        return view('admin.clients.index');
    }

    public function show(User $client)
    {
        // $client->load('appointments'); // Descomente se quiser carregar agendamentos
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
}