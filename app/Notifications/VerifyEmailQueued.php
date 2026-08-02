<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Email verification, in this product's voice and language — same two reasons
 * as {@see ResetPasswordQueued}.
 *
 * Verification is new here: until 2026-08-02 an account was created and used
 * without ever confirming the address, which meant a typo in the email locked
 * somebody out of their own reset link with no way back.
 */
class VerifyEmailQueued extends VerifyEmail implements ShouldQueue
{
    use Queueable;

    /** @param  mixed  $notifiable */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('Verify your email'))
            ->view('emails.verify-email', ['url' => $this->verificationUrl($notifiable)]);
    }
}
