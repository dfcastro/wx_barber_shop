<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlockedPeriod;
use Illuminate\Http\Request;

class BlockedPeriodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.blocked-periods.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Retorna a view que hospedará o formulário de criação
        return view('admin.blocked-periods.create');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BlockedPeriod $blockedPeriod) // Route Model Binding
    {
        // Retorna a view que hospedará o formulário de edição, passando o período a ser editado
        return view('admin.blocked-periods.edit', compact('blockedPeriod'));
    }

    // Os métodos store, update e destroy serão chamados pelas ações do Livewire ou formulários diretos
    // Se o Livewire cuidar de salvar/atualizar, store() e update() podem não ser usados diretamente pela UI Livewire.

    public function store(Request $request)
    {
        // Lógica para salvar se fosse um formulário HTML tradicional
        $validatedData = $request->validate([
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after_or_equal:start_datetime',
            'reason' => 'nullable|string|max:255',
        ]);

        BlockedPeriod::create($validatedData);
        return redirect()->route('admin.blocked-periods.index')->with('success', 'Período de bloqueio criado com sucesso!');
    }

    public function update(Request $request, BlockedPeriod $blockedPeriod)
    {
        // Lógica para atualizar se fosse um formulário HTML tradicional
        $validatedData = $request->validate([
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after_or_equal:start_datetime',
            'reason' => 'nullable|string|max:255',
        ]);

        $blockedPeriod->update($validatedData);
        return redirect()->route('admin.blocked-periods.index')->with('success', 'Período de bloqueio atualizado com sucesso!');
    }
    
    public function destroy(BlockedPeriod $blockedPeriod)
    {
        $blockedPeriod->delete();
        return redirect()->route('admin.blocked-periods.index')->with('success', 'Período bloqueado excluído com sucesso!');
    }
}