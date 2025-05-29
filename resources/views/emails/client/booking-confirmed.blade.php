@component('mail::message')
# Agendamento Confirmado!

Olá, {{ $appointment->user->name }},

Boas notícias! Seu agendamento para o serviço **{{ $appointment->service->name }}** na WX Barber Shop foi **confirmado**.

**Detalhes do Agendamento Confirmado:**
- **Serviço:** {{ $appointment->service->name }}
- **Data:** {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('d/m/Y') }}
- **Horário:** {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }}

Estamos ansiosos para recebê-lo!

@component('mail::button', ['url' => $appointmentDetailsUrl])
Ver Meus Agendamentos
@endcomponent

Atenciosamente,<br>
Equipe WX Barber Shop
@endcomponent