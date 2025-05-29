<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingCancelledByAdminToClient extends Mailable
{
    use Queueable, SerializesModels;

    public Appointment $appointment;
    public ?string $cancellationReason; // Opcional: para adicionar um motivo do cancelamento

    /**
     * Create a new message instance.
     */
    public function __construct(Appointment $appointment, ?string $cancellationReason = null)
    {
        $this->appointment = $appointment;
        $this->cancellationReason = $cancellationReason;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Aviso Sobre Seu Agendamento na WX Barber Shop',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.client.booking-cancelled-by-admin',
            with: [
                'appointmentDetailsUrl' => route('client.appointments.index'),
            ]
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