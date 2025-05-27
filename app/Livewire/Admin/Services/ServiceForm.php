<?php

namespace App\Livewire\Admin\Services;

use App\Models\Service;
use Livewire\Component;

class ServiceForm extends Component
{
    public ?Service $serviceInstance = null; // Para armazenar a instância do serviço em edição

    public string $name = '';
    public $price = '';
    public $duration_minutes = '';

    protected function rules() // Transformado em método para validação única de nome
    {
        return [
            'name' => 'required|string|max:255',
            // Se estiver editando, o nome pode ser o mesmo do serviço atual.
            // Se for um novo, deve ser único. Adicionaremos essa lógica se necessário.
            // Por agora, uma validação simples.
            'price' => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:1',
        ];
    }

    protected $messages = [
        'name.required' => 'O campo nome é obrigatório.',
        'price.required' => 'O campo preço é obrigatório.',
        'price.numeric' => 'O preço deve ser um número.',
        'duration_minutes.required' => 'O campo duração é obrigatório.',
        'duration_minutes.integer' => 'A duração deve ser um número inteiro.',
    ];

    // O método mount é chamado quando o componente é inicializado.
    // Tornamos $service opcional. Se for passado, estamos no modo de edição.
    public function mount(Service $service = null)
    {
        if ($service && $service->exists) { // Verifica se $service não é nulo e existe no BD
            $this->serviceInstance = $service;
            $this->name = $service->name;
            $this->price = $service->price;
            $this->duration_minutes = $service->duration_minutes;
        }
    }

    public function saveService()
    {
        $validatedData = $this->validate(); // Valida os dados

        if ($this->serviceInstance) {
            // Modo de Edição: Atualiza o serviço existente
            $this->serviceInstance->update($validatedData);
            session()->flash('success', 'Serviço atualizado com sucesso!');
        } else {
            // Modo de Criação: Cria um novo serviço
            Service::create($validatedData);
            session()->flash('success', 'Serviço adicionado com sucesso!');
        }

        // Limpa os campos do formulário apenas se for um novo serviço,
        // ou podemos sempre redirecionar, o que limpa o estado.
        // Para simplificar, vamos sempre redirecionar.
        // $this->reset(['name', 'price', 'duration_minutes', 'serviceInstance']);

        return redirect()->route('admin.services.index');
    }

    public function render()
    {
        return view('livewire.admin.services.service-form');
    }
}