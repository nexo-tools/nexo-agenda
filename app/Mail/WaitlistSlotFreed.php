<?php

namespace App\Mail;

use App\Models\Booking;
use App\Models\WaitlistEntry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WaitlistSlotFreed extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly WaitlistEntry $entry,
        public readonly Booking $booking,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('A time slot just opened at :business!', ['business' => $this->booking->business->name]),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.waitlist-slot-freed');
    }
}
