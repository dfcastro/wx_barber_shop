@component('mail::message')
# Nova Solicitação de Agendamento!

Uma nova solicitação de agendamento foi feita:

**Detalhes:**
- **Cliente:** {{ $appointment->user->name }} ({{ $appointment->user->email }})
- **Serviço:** {{ $appointment->service->name }}
- **Data:** {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('d/m/Y') }}
- **Horário:** {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }}

Por favor, acesse o painel administrativo para aprovar ou gerenciar este agendamento.

@component('mail::button', ['url' => $adminAppointmentsUrl])
Ver Painel de Agendamentos
@endcomponent

Atenciosamente,<br>
Sistema WX Barber Shop
@endcomponent