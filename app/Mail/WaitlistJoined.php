<?php

namespace App\Mail;

use App\Models\WaitlistEntry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Confirms that somebody is on the waitlist for a given day.
 *
 * Until now joining produced a flash message and nothing else: the person
 * closed the tab with no record that it happened, and the only mail they could
 * ever receive was the one that arrives if a slot frees up — which may never
 * come. This is the receipt.
 */
class WaitlistJoined extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public readonly WaitlistEntry $entry) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('You are on the waitlist at :business', ['business' => $this->entry->business->name]),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.waitlist-joined');
    }
}
