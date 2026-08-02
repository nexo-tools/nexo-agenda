<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Throwable;

/**
 * "Something broke" — to whoever runs this instance.
 *
 * The ecosystem has no error tracker and deliberately no third party watching
 * its users (that is the whole point of the product), so the only way an
 * operator learns about a 500 is the log file they never open. This is the
 * cheapest honest alternative: the exception, where it happened, and enough
 * context to start.
 *
 * Deliberately NOT translated: it goes to the operator, not to a user.
 *
 * Queued like every other mail, with one difference that matters — if the queue
 * itself is what is broken, this mail never leaves. That is a known limit and
 * the reason the log line stays: this is a nudge, not a monitoring system.
 */
class OperatorAlert extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $exceptionClass,
        public readonly string $message,
        public readonly string $file,
        public readonly int $line,
        public readonly ?string $url,
        public readonly string $trace,
    ) {}

    public static function fromThrowable(Throwable $e, ?string $url): self
    {
        return new self(
            exceptionClass: $e::class,
            message: $e->getMessage(),
            file: $e->getFile(),
            line: $e->getLine(),
            url: $url,
            // The first frames are what tells you where to look; the rest is
            // noise in an inbox.
            trace: implode("\n", array_slice(explode("\n", $e->getTraceAsString()), 0, 12)),
        );
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '['.config('app.name').'] '.class_basename($this->exceptionClass).': '.mb_substr($this->message, 0, 80),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.operator-alert');
    }
}
