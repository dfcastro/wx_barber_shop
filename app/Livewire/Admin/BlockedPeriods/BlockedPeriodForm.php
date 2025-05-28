<?php

namespace App\Livewire\Admin\BlockedPeriods; // Verifique o namespace

use App\Models\BlockedPeriod;
use Livewire\Component;
use Carbon\Carbon;

class BlockedPeriodForm extends Component
{
    public ?BlockedPeriod $blockedPeriodInstance = null; // Para edição

    public $start_datetime; // String no formato YYYY-MM-DDTHH:MM
    public $end_datetime;   // String no formato YYYY-MM-DDTHH:MM
    public $reason = '';

    protected function rules()
    {
        return [
            'start_datetime' => 'required|date_format:Y-m-d\TH:i',
            'end_datetime' => 'required|date_format:Y-m-d\TH:i|after_or_equal:start_datetime',
            'reason' => 'nullable|string|max:255',
        ];
    }

    protected $messages = [
        'start_datetime.required' => 'A data/hora de início é obrigatória.',
        'start_datetime.date_format' => 'Formato inválido para data/hora de início.',
        'end_datetime.required' => 'A data/hora de fim é obrigatória.',
        'end_datetime.date_format' => 'Formato inválido para data/hora de fim.',
        'end_datetime.after_or_equal' => 'A data/hora de fim deve ser igual ou posterior à data/hora de início.',
        'reason.max' => 'O motivo não pode ter mais que 255 caracteres.',
    ];

    public function mount(BlockedPeriod $blockedPeriod = null)
    {
        if ($blockedPeriod && $blockedPeriod->exists) {
            $this->blockedPeriodInstance = $blockedPeriod;
            // Formata para o tipo de input datetime-local
            $this->start_datetime = $blockedPeriod->start_datetime->format('Y-m-d\TH:i');
            $this->end_datetime = $blockedPeriod->end_datetime->format('Y-m-d\TH:i');
            $this->reason = $blockedPeriod->reason;
        }
    }

    public function saveBlockedPeriod()
    {
        $validatedData = $this->validate();

        // Converte de volta para instâncias Carbon para salvar no banco, se necessário,
        // ou o Eloquent pode lidar com isso se o formato for compatível com o cast do model.
        // $validatedData['start_datetime'] = Carbon::parse($validatedData['start_datetime']);
        // $validatedData['end_datetime'] = Carbon::parse($validatedData['end_datetime']);
        // O model já tem casts para datetime, então o formato 'Y-m-d\TH:i' deve ser aceitável.

        if ($this->blockedPeriodInstance) {
            // Edição
            $this->blockedPeriodInstance->update($validatedData);
            session()->flash('success', 'Período de bloqueio atualizado com sucesso!');
        } else {
            // Criação
            BlockedPeriod::create($validatedData);
            session()->flash('success', 'Período de bloqueio adicionado com sucesso!');
        }

        return redirect()->route('admin.blocked-periods.index');
    }

    public function render()
    {
        return view('livewire.admin.blocked-periods.blocked-period-form');
    }
}