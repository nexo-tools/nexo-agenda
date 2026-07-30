<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NewBookingReceived extends Mailable
{
    public function __construct(public readonly Booking $booking) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('New booking: :client — :service', [
                'client' => $this->booking->client_name,
                'service' => $this->booking->service->name,
            ]),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.new-booking-received');
    }
}
