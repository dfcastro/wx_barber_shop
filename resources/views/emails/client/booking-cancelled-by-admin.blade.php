@component('mail::message')
# Atualização Sobre Seu Agendamento

Olá, {{ $appointment->user->name }},

Houve uma atualização referente ao seu agendamento para o serviço **{{ $appointment->service->name }}** na WX Barber Shop, que estava marcado para **{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('d/m/Y \à\s H:i') }}**.

Infelizmente, seu agendamento foi **cancelado** pela administração.

@if ($cancellationReason)
**Motivo do Cancelamento:** {{ $cancellationReason }}
@endif

Lamentamos por qualquer inconveniente. Se desejar, você pode tentar fazer um novo agendamento ou entrar em contato conosco.

@component('mail::button', ['url' => route('booking.index') ]) {{-- Link para a página de agendar --}}
Fazer Novo Agendamento
@endcomponent

Atenciosamente,<br>
Equipe WX Barber Shop
@endcomponent