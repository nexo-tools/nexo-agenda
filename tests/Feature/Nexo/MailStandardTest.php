<?php

// Guardian: every transactional mail this tool sends wears the family layout, is
// really translated, goes to the queue, and does not invent its own sender.
//
// Why it exists: the mail is the only surface of the ecosystem the person sees
// outside the product — in their inbox, hours later, next to everything else in
// their life. The 2026-08-02 audit found four tools with hand-written HTML that
// had drifted apart and two still sending Laravel's English markdown wrapper, so
// the same user got three different products from one ecosystem. And nothing
// could see it: no test in any repo rendered a mail.
//
// Copy into tests/Feature/Nexo/ and fill nexoMails(). It is a function and not a
// const because a const cannot hold closures, and a mail needs building: pass a
// closure per mail, returning either a Mailable or [notification, notifiable].
//
// Skip-until-migrated: with nexoMails() empty every test here skips with a
// message. A guardian that is red the day it lands teaches everyone to ignore
// it (same pattern as LandingStandardTest).
//
// Pest note: toContain() is variadic — a second argument is another needle, not
// a failure message — so human-readable messages go through toBeTrue()/toBe().

use App\Mail\BookingCancelled;
use App\Mail\BookingChangedByClient;
use App\Mail\BookingConfirmed;
use App\Mail\BookingReminder;
use App\Mail\BookingRescheduled;
use App\Mail\NewBookingReceived;
use App\Mail\NexoIdLinked;
use App\Mail\OperatorAlert;
use App\Mail\PasswordChanged;
use App\Mail\WaitlistJoined;
use App\Mail\WaitlistSlotFreed;
use App\Models\Booking;
use App\Models\User;
use App\Models\WaitlistEntry;
use App\Notifications\ResetPasswordQueued;
use App\Notifications\VerifyEmailQueued;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;

/**
 * label => closure building the mail. Factories are fine: each test runs in a
 * transaction.
 *
 *   'ticket' => fn () => new TicketIssued(Ticket::factory()->create(), 'raw-token'),
 *   'verify' => fn () => [new VerifyEmailQueued, User::factory()->create()],
 *
 * @return array<string, callable>
 */
function nexoMails(): array
{
    $booking = fn () => Booking::factory()->create()->load(['business', 'service', 'professional']);

    return [
        // The operator alert renders like any other mail: it is here because the
        // one mail nobody declared was the one that shipped broken.
        'operator-alert' => fn () => OperatorAlert::fromThrowable(new RuntimeException('something broke'), 'https://example.test/x'),
        'booking-confirmed' => fn () => new BookingConfirmed($booking(), 'raw-management-token'),
        'booking-cancelled' => fn () => new BookingCancelled($booking()),
        'booking-rescheduled' => fn () => new BookingRescheduled($booking()),
        'booking-reminder' => fn () => new BookingReminder($booking()),
        'new-booking-received' => fn () => new NewBookingReceived($booking()),
        'booking-changed-by-client' => fn () => new BookingChangedByClient($booking(), cancelled: true),
        'waitlist-slot-freed' => function () use ($booking) {
            $b = $booking();

            return new WaitlistSlotFreed(waitlistEntryFor($b), $b);
        },
        'waitlist-joined' => function () use ($booking) {
            $b = $booking();

            return new WaitlistJoined(waitlistEntryFor($b)->load(['business', 'service', 'professional']));
        },
        'password-changed' => fn () => new PasswordChanged(User::factory()->create()),
        'nexo-id-linked' => fn () => new NexoIdLinked(User::factory()->create()),
        'reset-password' => fn () => [new ResetPasswordQueued('raw-reset-token'), User::factory()->create()],
        'verify-email' => fn () => [new VerifyEmailQueued, User::factory()->unverified()->create()],
    ];
}

/**
 * Labels exempt from the translation assertion: operator-facing mail that goes
 * to whoever runs the instance, not to a user, and is deliberately written in
 * one language. Keep this list short and justified — it is the escape hatch.
 *
 * @return array<int, string>
 */
function nexoOperatorMails(): array
{
    // Goes to whoever runs the instance, not to a user.
    return ['operator-alert'];
}

/** There is no factory for waitlist entries: they are created by the flow. */
function waitlistEntryFor(Booking $booking): WaitlistEntry
{
    return $booking->business->waitlistEntries()->create([
        'service_id' => $booking->service_id,
        'professional_id' => $booking->professional_id,
        'date' => $booking->starts_at->toDateString(),
        'client_name' => 'Ana',
        'client_email' => 'ana@example.com',
    ]);
}

/**
 * Renders a mail the way its channel would, and reports what the guardian needs.
 *
 * @return array{object: object, html: string, subject: string, from: mixed}
 */
function nexoMailParts(callable $make): array
{
    $made = $make();
    [$mail, $notifiable] = is_array($made) ? $made : [$made, null];

    if ($mail instanceof Mailable) {
        $envelope = method_exists($mail, 'envelope') ? $mail->envelope() : null;

        return [
            'object' => $mail,
            'html' => $mail->render(),
            'subject' => (string) ($envelope?->subject ?? $mail->subject ?? ''),
            'from' => $envelope?->from,
        ];
    }

    // A notification: the mail channel turns toMail() into the message. Our
    // notifications carry a `view` (the framework's markdown wrapper cannot
    // reach this ecosystem's translations), so that is what gets rendered.
    $message = $mail->toMail($notifiable);

    return [
        'object' => $mail,
        'html' => $message->view ? view($message->view, $message->viewData)->render() : '',
        'subject' => (string) $message->subject,
        'from' => $message->from ?? [],
    ];
}

/** Renders every declared mail under a given locale. */
function nexoRenderAll(string $locale): array
{
    $previous = app()->getLocale();
    app()->setLocale($locale);

    try {
        $out = [];
        foreach (nexoMails() as $label => $make) {
            $out[$label] = nexoMailParts($make);
        }

        return $out;
    } finally {
        app()->setLocale($previous);
    }
}

it('declares the mails this tool sends', function () {
    if (nexoMails() === []) {
        test()->markTestSkipped('No mail migrated to the family layout yet — fill nexoMails() (see templates/nexo-mail/README.md).');
    }

    expect(nexoMails())->not->toBeEmpty();
});

it('renders every mail inside the family layout', function () {
    if (nexoMails() === []) {
        test()->markTestSkipped('No mail migrated to the family layout yet.');
    }

    foreach (nexoRenderAll('es') as $label => $parts) {
        // The marker is the first line of the layout's <body>: if it is missing,
        // this mail is rendering something else.
        expect(str_contains($parts['html'], '<!-- nexo-mail -->'))->toBeTrue(
            "Mail [{$label}] does not render the family layout — wrap the view in <x-nexo-mail::layout>."
        );
    }
});

it('translates every subject for real', function () {
    if (nexoMails() === []) {
        test()->markTestSkipped('No mail migrated to the family layout yet.');
    }

    $es = nexoRenderAll('es');
    $en = nexoRenderAll('en');
    $operator = nexoOperatorMails();

    foreach ($es as $label => $parts) {
        if (in_array($label, $operator, true)) {
            continue;
        }

        expect($parts['subject'])->not->toBe('', "Mail [{$label}] has an empty subject.");

        // A subject built from a hardcoded string reads the same in every
        // language, which is exactly how "Verify Email Address" survived in a
        // Spanish-first product for months.
        expect($parts['subject'])->not->toBe(
            $en[$label]['subject'],
            "Mail [{$label}] has the same subject in es and en — it is not going through __()."
        );
    }
});

it('renders the body in the requested locale, not the app default', function () {
    if (nexoMails() === []) {
        test()->markTestSkipped('No mail migrated to the family layout yet.');
    }

    $es = nexoRenderAll('es');
    $en = nexoRenderAll('en');
    $operator = nexoOperatorMails();

    foreach ($es as $label => $parts) {
        if (in_array($label, $operator, true)) {
            continue;
        }

        expect($parts['html'])->not->toBe(
            $en[$label]['html'],
            "Mail [{$label}] renders identically in es and en — its body is not translated (or the strings are hardcoded)."
        );
    }
});

it('queues every mail', function () {
    if (nexoMails() === []) {
        test()->markTestSkipped('No mail migrated to the family layout yet.');
    }

    foreach (nexoMails() as $label => $make) {
        $made = $make();
        $mail = is_array($made) ? $made[0] : $made;

        // Shared hosting cannot hold a worker, so mail is drained by the
        // scheduler. A mail sent in-request puts the relay's latency in front of
        // the user's response and a relay timeout turns into a failed action.
        expect($mail instanceof ShouldQueue)->toBeTrue(
            "Mail [{$label}] is not ShouldQueue — see templates/nexo-mail/README.md, rule 2."
        );
    }
});

it('never overrides the global sender', function () {
    if (nexoMails() === []) {
        test()->markTestSkipped('No mail migrated to the family layout yet.');
    }

    foreach (nexoRenderAll('es') as $label => $parts) {
        // One From per tool, set by the instance (MAIL_FROM_ADDRESS). A mailable
        // that hardcodes its own sender silently ignores the deploy's config and
        // is invisible until a person reads the header of a delivered mail.
        expect(empty($parts['from']))->toBeTrue("Mail [{$label}] overrides the global From — remove it and let the instance decide (rule 1).");
    }
});

it('writes its Spanish in the family register', function () {
    if (nexoMails() === []) {
        test()->markTestSkipped('No mail migrated to the family layout yet.');
    }

    // Tuteo neutro, sin voseo rioplatense (STANDARD.md "Contenido base",
    // decidido 2026-07-29). El usuario cruza de una tool a otra en un clic y el
    // registro no puede cambiar en el camino — y el correo, que se lee fuera
    // del producto y días después, es donde más se nota.
    // Solo formas con tilde en la última sílaba: aceptar [aá] haría que el check
    // gritara también por el tuteo correcto ("olvidas", "copia y pega").
    $voseo = '/\b(podés|tenés|querés|olvidás|encontrás|escribinos|decinos|pedila|restablecela|copiá|pegá|cambiá|confirmá|verificá|elegí|fuiste vos)\b/iu';

    $offenders = [];
    foreach (nexoRenderAll('es') as $label => $parts) {
        if (preg_match($voseo, strip_tags($parts['html']), $m)) {
            $offenders[] = "{$label}: \"{$m[0]}\"";
        }
    }

    expect($offenders)->toBe([], "Voseo in Spanish mail copy (the family register is tuteo neutro):\n".implode("\n", $offenders));
});

it('drains the mail queue from the scheduler', function () {
    if (nexoMails() === []) {
        test()->markTestSkipped('No mail migrated to the family layout yet.');
    }

    $console = (string) file_get_contents(base_path('routes/console.php'));

    // The other half of rule 2: queued mail that nobody drains never leaves, and
    // the app looks perfectly healthy while it happens (it did, in nexoid).
    expect(str_contains($console, 'queue:work'))->toBeTrue(
        'routes/console.php schedules no queue drain — queued mail would never be sent. See templates/nexo-mail/README.md, rule 2.'
    );
});
