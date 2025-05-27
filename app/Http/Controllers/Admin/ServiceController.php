<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Remove o dd('chegou aqui');
        // Busca todos os serviços do banco de dados, ordenados pelo nome
        $services = Service::orderBy('name')->get();

        // Retorna uma view, passando a lista de serviços para ela
        // Vamos criar esta view em: resources/views/admin/services/index.blade.php
        return view('admin.services.index', compact('services'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Simplesmente retorna a view que conterá o formulário de criação.
        // O formulário em si será um componente Livewire dentro desta view.
        return view('admin.services.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service) // Usamos Route Model Binding aqui
    {
        // O Laravel automaticamente busca o Service pelo ID passado na URL
        // e injeta a instância do Service aqui. Se não encontrar, ele dará um erro 404.
        // Retorna a view de edição, passando o serviço encontrado.
        return view('admin.services.edit', compact('service'));
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service) // Route Model Binding
    {
        // O Service já é injetado graças ao Route Model Binding
        $serviceName = $service->name; // Pega o nome antes de deletar, para a mensagem
        $service->delete();

        return redirect()->route('admin.services.index')->with('success', "Serviço '{$serviceName}' deletado com sucesso via Controller!");
    }
}
