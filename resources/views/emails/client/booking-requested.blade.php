@component('mail::message')
# Olá, {{ $appointment->user->name }}!

Sua solicitação de agendamento para o serviço **{{ $appointment->service->name }}** na WX Barber Shop foi recebida com sucesso.

**Detalhes do Agendamento:**
- **Serviço:** {{ $appointment->service->name }}
- **Data:** {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('d/m/Y') }}
- **Horário:** {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }}
- **Status:** {{ ucfirst($appointment->status) }}

Seu agendamento está pendente de aprovação. Entraremos em contato em breve ou você pode verificar o status na sua área de "Meus Agendamentos".

@component('mail::button', ['url' => $appointmentDetailsUrl])
Ver Meus Agendamentos
@endcomponent

Obrigado por escolher a WX Barber Shop!<br>
Atenciosamente,<br>
Equipe WX Barber Shop
@endcomponent