<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Models\Business;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BusinessStats
{
    /**
     * Record an anonymous, cookieless visit to the business public page.
     * One row per visitor per day; the hash rotates daily and stores no PII.
     */
    public function recordVisit(Business $business, Request $request): void
    {
        $date = CarbonImmutable::now($business->timezone)->toDateString();

        $hash = hash('sha256', implode('|', [
            config('app.key'),
            (string) $request->ip(),
            (string) $request->userAgent(),
            $date,
        ]));

        DB::table('page_visits')->insertOrIgnore([
            'business_id' => $business->id,
            'date' => $date,
            'visitor_hash' => $hash,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Aggregated stats for the period [$from, $to] (business-local dates).
     *
     * @return array<string, mixed>
     */
    public function forPeriod(Business $business, CarbonImmutable $from, CarbonImmutable $to): array
    {
        $tz = $business->timezone;
        $fromUtc = $from->startOfDay()->utc();
        $toUtc = $to->addDay()->startOfDay()->utc();

        $bookings = $business->bookings()
            ->with('service:id,name', 'professional:id,name')
            ->where('starts_at', '>=', $fromUtc)
            ->where('starts_at', '<', $toUtc)
            ->get();

        $active = $bookings->filter(fn ($b) => $b->status !== BookingStatus::Cancelled);
        $attended = $bookings->where('status', BookingStatus::Attended);
        $noShows = $bookings->where('status', BookingStatus::NoShow);

        $visits = (int) DB::table('page_visits')
            ->where('business_id', $business->id)
            ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->count();

        $perDay = $active
            ->groupBy(fn ($b) => $b->starts_at->setTimezone($tz)->toDateString())
            ->map->count();

        $days = [];
        for ($day = $from; $day->lte($to); $day = $day->addDay()) {
            $days[$day->toDateString()] = $perDay->get($day->toDateString(), 0);
        }

        return [
            'total' => $active->count(),
            'attended' => $attended->count(),
            'no_shows' => $noShows->count(),
            'cancelled' => $bookings->count() - $active->count(),
            'no_show_rate' => $active->count() > 0 ? round($noShows->count() * 100 / $active->count()) : 0,
            'visits' => $visits,
            'conversion' => $visits > 0 ? round($active->count() * 100 / $visits) : null,
            'top_services' => $active->groupBy('service.name')->map->count()->sortDesc()->take(5),
            'top_professionals' => $active->groupBy('professional.name')->map->count()->sortDesc()->take(5),
            'per_day' => $days,
            'occupancy' => $this->occupancy($business, $from, $to, $active->sum(
                fn ($b) => (int) $b->starts_at->diffInMinutes($b->ends_at)
            )),
        ];
    }

    /**
     * Booked minutes vs. scheduled minutes across the period (absences subtracted).
     */
    private function occupancy(Business $business, CarbonImmutable $from, CarbonImmutable $to, int $bookedMinutes): ?int
    {
        $available = 0;

        $professionals = $business->professionals()
            ->where('is_active', true)
            ->with(['scheduleBlocks', 'absences'])
            ->get();

        foreach ($professionals as $professional) {
            $minutesPerWeekday = $professional->scheduleBlocks
                ->groupBy('weekday')
                ->map(fn ($blocks) => $blocks->sum(function ($block) {
                    [$sh, $sm] = explode(':', (string) $block->start_time);
                    [$eh, $em] = explode(':', (string) $block->end_time);

                    return ((int) $eh * 60 + (int) $em) - ((int) $sh * 60 + (int) $sm);
                }));

            for ($day = $from; $day->lte($to); $day = $day->addDay()) {
                $isAbsent = $professional->absences->contains(fn ($a) => $a->starts_on->lte($day) && $a->ends_on->gte($day));

                if (! $isAbsent) {
                    $available += (int) $minutesPerWeekday->get($day->dayOfWeekIso, 0);
                }
            }
        }

        return $available > 0 ? min(100, (int) round($bookedMinutes * 100 / $available)) : null;
    }
}
