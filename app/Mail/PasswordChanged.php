<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * "Your password changed" — the security notice every account in the ecosystem
 * owes its owner (family rule C5).
 *
 * It is not a courtesy: a password reset is exactly what an attacker who took
 * over an inbox does first, and this mail is the only signal the real owner
 * gets while they can still act. That is why it says what to do next instead of
 * congratulating anybody.
 */
class PasswordChanged extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public User $user) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: __('Your password changed'));
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.password-changed',
            with: ['resetUrl' => route('password.request')],
        );
    }
}
