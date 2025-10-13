<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservationConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $patient;
    public $reservation;
    public $slot;

    public function __construct($patient, $reservation)
    {
        $this->patient = $patient;
        $this->reservation = $reservation;
        $this->slot = $reservation->slot;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '○○病院 予約完了メール',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mails.reservation_confirmation',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
