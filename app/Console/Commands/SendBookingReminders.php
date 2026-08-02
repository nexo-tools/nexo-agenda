<?php

namespace App\Console\Commands;

use App\Enums\BookingStatus;
use App\Mail\BookingReminder;
use App\Models\Booking;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendBookingReminders extends Command
{
    protected $signature = 'nexo:send-reminders';

    protected $description = 'Send the 24h reminder email for upcoming confirmed bookings';

    public function handle(): int
    {
        $now = CarbonImmutable::now();

        $bookings = Booking::query()
            ->where('status', BookingStatus::Confirmed->value)
            ->whereNull('reminded_at')
            ->whereNotNull('client_email')
            ->where('starts_at', '>', $now)
            ->where('starts_at', '<=', $now->addDay())
            ->with(['business', 'service', 'professional'])
            ->get();

        foreach ($bookings as $booking) {
            // A scheduled command has no request: without the locale kept on
            // the booking, every reminder went out in APP_LOCALE.
            Mail::to($booking->client_email)
                ->locale($booking->locale ?: config('app.locale'))
                ->queue(new BookingReminder($booking));
            $booking->forceFill(['reminded_at' => $now])->save();
        }

        $this->info("Recordatorios enviados: {$bookings->count()}");

        return self::SUCCESS;
    }
}
