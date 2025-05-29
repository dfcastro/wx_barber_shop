<?php

namespace App\Mail;

use App\Models\Appointment; // Importe o model Appointment
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingRequestedToClient extends Mailable
{
    use Queueable, SerializesModels;

    public Appointment $appointment; // Propriedade pública para passar o agendamento para a view

    /**
     * Create a new message instance.
     */
    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Sua Solicitação de Agendamento na WX Barber Shop Foi Recebida!',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Usará a view Markdown que especificamos
        return new Content(
            markdown: 'emails.client.booking-requested',
            with: [ // Dados que serão passados para a view do e-mail
                'appointmentDetailsUrl' => route('client.appointments.index'), // Exemplo de link
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}