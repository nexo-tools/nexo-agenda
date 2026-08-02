<?php

// Guardian: a 500 reaches the operator, exactly once per distinct failure.
//
// Why it exists: the ecosystem has no error tracker by design, so this mail is
// the only signal an operator gets. Two things can quietly break it and neither
// shows up anywhere — the handler not being wired at all, and the dedupe window
// failing, which turns one bad deploy into four thousand emails and a person
// who from then on ignores the inbox.
//
// Copy into tests/Feature/Nexo/.

use App\Mail\OperatorAlert;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

/** Forces the app to report an exception the way the framework would. */
function reportOperatorException(Throwable $e): void
{
    app(ExceptionHandler::class)->report($e);
}

beforeEach(function () {
    // The handler is off in tests by default (a suite must not mail anybody);
    // these tests turn it on deliberately and put a real recipient in place.
    config()->set('nexo.ops_mail', true);
    config()->set('nexo.support_email', 'operator@example.com');
    Cache::flush();
});

it('mails the operator when something breaks', function () {
    Mail::fake();

    reportOperatorException(new RuntimeException('the database went away'));

    Mail::assertQueued(OperatorAlert::class, fn (OperatorAlert $mail): bool => $mail->hasTo('operator@example.com'));
});

it('sends one mail per distinct failure, however many times it happens', function () {
    Mail::fake();

    $throw = fn () => reportOperatorException(new RuntimeException('the database went away'));

    $throw();
    $throw();
    $throw();

    // A loop throwing the same error four thousand times must produce one mail:
    // an operator whose inbox is flooded is an operator who stops reading it.
    Mail::assertQueuedCount(1);
});

it('still reports a different failure inside the same window', function () {
    Mail::fake();

    reportOperatorException(new RuntimeException('the database went away'));
    reportOperatorException(new LogicException('something else entirely'));

    Mail::assertQueuedCount(2);
});

it('says nothing when the operator turned it off', function () {
    Mail::fake();
    config()->set('nexo.ops_mail', false);

    reportOperatorException(new RuntimeException('the database went away'));

    // Off by default: a freshly cloned instance must not start mailing anybody.
    Mail::assertNothingQueued();
});
