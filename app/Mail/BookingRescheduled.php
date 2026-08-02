<?php

namespace App\Mail;

use App\Models\Booking;
use App\Services\IcsFile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingRescheduled extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Booking $booking) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('Your appointment at :business was rescheduled', ['business' => $this->booking->business->name]),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.booking-rescheduled');
    }

    /** @return array<int, Attachment> */
    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => (new IcsFile)->forBooking($this->booking), 'turno.ics')
                ->withMime('text/calendar'),
        ];
    }
}
