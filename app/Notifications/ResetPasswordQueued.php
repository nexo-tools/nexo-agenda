<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Password reset, in this product's voice and language.
 *
 * The framework default was going out in English with Laravel's markdown
 * wrapper ("Reset Password Notification", "Regards"): those strings live in the
 * framework's own translations, which this project's i18n cannot reach —
 * Spanish is the source language here and the generator translates outward from
 * it. So a Spanish-first product was mailing its users in English, on a
 * template that looked nothing like the rest of its mail.
 *
 * Queued for the family reason (C2): a slow relay must not turn a reset request
 * into a failed one for a link that was in fact created.
 *
 * It stays a MailMessage carrying a custom `view` rather than returning a
 * Mailable: the parent's signature promises a MailMessage, and a Mailable
 * returned here would also have to address itself.
 */
class ResetPasswordQueued extends ResetPassword implements ShouldQueue
{
    use Queueable;

    /** @param  mixed  $notifiable */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('Reset your password'))
            ->view('emails.reset-password', [
                'url' => url(route('password.reset', [
                    'token' => $this->token,
                    'email' => $notifiable->getEmailForPasswordReset(),
                ], false)),
                'expiresIn' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire'),
            ]);
    }
}
