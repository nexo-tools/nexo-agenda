<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Tells the owner that a client cancelled or rescheduled from the management
 * link.
 *
 * The gap the audit named: the client's side of that flow was fully covered by
 * mail, and the owner — the person who has to fill the hole in their day —
 * found out only by opening the dashboard. A cancellation at 22:00 for a 9:00
 * appointment is exactly the case where an email matters.
 *
 * One class for both events, because for the owner they are the same news with
 * a different verb, and two nearly identical mailables drift apart.
 */
class BookingChangedByClient extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Booking $booking,
        public readonly bool $cancelled,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->cancelled
                ? __('Cancelled by the client: :client', ['client' => $this->booking->client_name])
                : __('Rescheduled by the client: :client', ['client' => $this->booking->client_name]),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.booking-changed-by-client');
    }
}
