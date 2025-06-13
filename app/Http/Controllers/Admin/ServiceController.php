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
        // 1. Validação dos dados brutos que chegam
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|string', // Validamos como texto por causa da máscara
            'duration_minutes' => 'required|integer|min:1',
        ]);

        // 2. Limpeza do campo de preço para salvar no banco
        $price = $validatedData['price'];
        // Remove 'R$', espaços, e o ponto de milhar. Troca a vírgula por ponto decimal.
        $priceSanitized = str_replace(['R$', ' ', '.'], '', $price);
        $priceSanitized = str_replace(',', '.', $priceSanitized);

        // 3. Criação do serviço com o preço limpo
        Service::create([
            'name' => $validatedData['name'],
            'description' => $validatedData['description'],
            'price' => (float) $priceSanitized, // Converte para número (float)
            'duration_minutes' => $validatedData['duration_minutes'],
        ]);

        return redirect()->route('admin.services.index')
            ->with('success', 'Serviço criado com sucesso.');
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
    public function update(Request $request, Service $service)
    {
        // 1. Validação dos dados
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|string',
            'duration_minutes' => 'required|integer|min:1',
        ]);

        // 2. Limpeza do campo de preço
        $price = $validatedData['price'];
        $priceSanitized = str_replace(['R$', ' ', '.'], '', $price);
        $priceSanitized = str_replace(',', '.', $priceSanitized);

        // 3. Atualiza o serviço com os dados validados e limpos
        $service->update([
            'name' => $validatedData['name'],
            'description' => $validatedData['description'],
            'price' => (float) $priceSanitized,
            'duration_minutes' => $validatedData['duration_minutes'],
        ]);

        return redirect()->route('admin.services.index')
            ->with('success', 'Serviço atualizado com sucesso.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service) // Route Model Binding
    {
        $service->delete();
        return redirect()->route('admin.services.index')->with('success', 'Serviço excluído com sucesso (via Controller)!');
    }
}
