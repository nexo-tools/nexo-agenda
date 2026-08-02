<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Mail\PasswordChanged;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Mail;

/**
 * Turns the framework's PasswordReset event into the security notice the
 * account owner needs (family rule C5). Auto-discovered by the event
 * dispatcher: the type hint is the registration.
 */
class SendPasswordChangedNotice
{
    public function handle(PasswordReset $event): void
    {
        $user = $event->user;

        if (! $user instanceof User) {
            return;
        }

        Mail::to($user->email)
            ->locale(app()->getLocale())
            ->queue(new PasswordChanged($user));
    }
}
