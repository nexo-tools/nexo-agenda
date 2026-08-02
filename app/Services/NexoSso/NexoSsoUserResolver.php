<?php

declare(strict_types=1);

namespace App\Services\NexoSso;

use App\Mail\NexoIdLinked;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * Maps verified OIDC claims to a local user. ADAPTATION POINT: newUser() is the
 * one method each tool tunes to its own users table (extra columns, defaults).
 */
class NexoSsoUserResolver
{
    /** @param  array<string, mixed>  $claims */
    public function resolve(array $claims): User
    {
        $sub = (string) ($claims['sub'] ?? '');
        if ($sub === '') {
            throw new NexoSsoException('Missing sub claim.');
        }

        // Returning user: match by stable sub, even if the email changed. (AC-LINK-3)
        $user = User::query()->where('nexo_id_sub', $sub)->first();
        if ($user !== null) {
            return $user;
        }

        $email = (string) ($claims['email'] ?? '');
        if ($email === '') {
            throw new NexoSsoException('Missing email claim (is the email scope granted?).');
        }

        // Existing local account: link only on a provider-verified email —
        // otherwise an attacker could claim someone else's account. (AC-LINK-2)
        $existing = User::query()->where('email', $email)->first();
        if ($existing !== null) {
            if (($claims['email_verified'] ?? false) !== true) {
                throw new NexoSsoLinkRefusedException('Email not verified by the identity provider.');
            }

            $firstLink = $existing->nexo_id_sub === null;

            $existing->forceFill([
                'nexo_id_sub' => $sub,
                // The provider just asserted this address is verified, so a
                // local account that never confirmed it is confirmed now.
                'email_verified_at' => $existing->email_verified_at ?? now(),
            ])->save();

            // The tool where the link happens is the one that tells the owner,
            // never nexo-id: this app knows which account was just connected.
            // Only on the first link — signing in again is not news.
            if ($firstLink) {
                Mail::to($existing->email)
                    ->locale(app()->getLocale())
                    ->queue(new NexoIdLinked($existing));
            }

            return $existing;
        }

        // First login, no local account: create one from the claims. (AC-LINK-1)
        $user = $this->newUser($claims);
        $user->forceFill(['nexo_id_sub' => $sub])->save();

        return $user;
    }

    /**
     * ADAPTATION POINT — align with this tool's users table.
     *
     * @param  array<string, mixed>  $claims
     */
    protected function newUser(array $claims): User
    {
        $user = new User;
        $user->forceFill([
            'name' => (string) ($claims['name'] ?? Str::before((string) $claims['email'], '@')),
            'email' => (string) $claims['email'],
            // Random local password: this account authenticates via SSO. If the
            // tool also offers local login, "forgot password" still works.
            'password' => Str::password(40),
            'email_verified_at' => ($claims['email_verified'] ?? false) === true ? now() : null,
        ]);
        $user->save();

        return $user;
    }
}
