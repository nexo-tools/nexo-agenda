<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Professional;
use App\Models\Service;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

class Availability
{
    private const STEP_MINUTES = 15;

    /**
     * Bookable start times for a professional on a given day, in the business timezone.
     *
     * @return Collection<int, CarbonImmutable>
     */
    public function slots(Service $service, Professional $professional, CarbonImmutable $day): Collection
    {
        $business = $service->business;
        $tz = $business->timezone;
        $day = $day->setTimezone($tz)->startOfDay();
        $now = CarbonImmutable::now($tz);

        if (! $service->is_active
            || ! $professional->is_active
            || $professional->business_id !== $service->business_id
            || $day->lt($now->startOfDay())
            || $day->gt($now->startOfDay()->addDays($service->max_advance_days))) {
            return collect();
        }

        $isAbsent = $professional->absences()
            ->whereDate('starts_on', '<=', $day)
            ->whereDate('ends_on', '>=', $day)
            ->exists();

        if ($isAbsent) {
            return collect();
        }

        $blocks = $professional->scheduleBlocks()
            ->where('weekday', $day->dayOfWeekIso)
            ->orderBy('start_time')
            ->get();

        if ($blocks->isEmpty()) {
            return collect();
        }

        $occupied = $this->occupiedIntervals($professional, $day);

        $slotLength = $service->duration_minutes + $service->buffer_minutes;
        $minStart = $now->addHours($service->min_notice_hours);
        $slots = collect();

        foreach ($blocks as $block) {
            $cursor = $day->setTimeFromTimeString((string) $block->start_time);
            $blockEnd = $day->setTimeFromTimeString((string) $block->end_time);

            while ($cursor->addMinutes($slotLength)->lte($blockEnd)) {
                $slotEnd = $cursor->addMinutes($slotLength);

                $collides = $occupied->contains(
                    fn (array $interval) => $cursor->lt($interval[1]) && $slotEnd->gt($interval[0])
                );

                if ($cursor->gte($minStart) && ! $collides) {
                    $slots->push($cursor);
                }

                $cursor = $cursor->addMinutes(self::STEP_MINUTES);
            }
        }

        return $slots->unique(fn (CarbonImmutable $slot) => $slot->timestamp)->values();
    }

    /**
     * Slots across many professionals: time (H:i) => id of a professional free at that time.
     *
     * @param  Collection<int, Professional>  $professionals
     * @return Collection<string, int>
     */
    public function slotsForAny(Service $service, Collection $professionals, CarbonImmutable $day): Collection
    {
        return $professionals
            ->flatMap(fn (Professional $professional) => $this->slots($service, $professional, $day)
                ->map(fn (CarbonImmutable $slot) => ['time' => $slot, 'professional_id' => $professional->id]))
            ->sortBy(fn (array $item) => $item['time']->timestamp)
            ->reduce(function (Collection $carry, array $item) {
                $key = $item['time']->format('H:i');

                if (! $carry->has($key)) {
                    $carry->put($key, $item['professional_id']);
                }

                return $carry;
            }, collect());
    }

    /**
     * First day with at least one slot, scanning up to the service booking horizon.
     *
     * @param  Collection<int, Professional>  $professionals
     */
    public function firstAvailableDay(Service $service, Collection $professionals, CarbonImmutable $from): ?CarbonImmutable
    {
        $tz = $service->business->timezone;
        $day = $from->setTimezone($tz)->startOfDay();
        $horizon = CarbonImmutable::now($tz)->startOfDay()->addDays($service->max_advance_days);

        while ($day->lte($horizon)) {
            foreach ($professionals as $professional) {
                if ($this->slots($service, $professional, $day)->isNotEmpty()) {
                    return $day;
                }
            }

            $day = $day->addDay();
        }

        return null;
    }

    /**
     * Occupied [start, end) UTC intervals for a professional on a day, buffers included.
     *
     * @return Collection<int, array{0: CarbonImmutable, 1: CarbonImmutable}>
     */
    private function occupiedIntervals(Professional $professional, CarbonImmutable $day): Collection
    {
        return $professional->bookings()
            ->occupying()
            ->with('service:id,buffer_minutes')
            ->where('starts_at', '<', $day->addDay()->utc())
            ->where('ends_at', '>', $day->subDay()->utc())
            ->get()
            ->map(fn (Booking $booking): array => [
                $booking->starts_at,
                $booking->ends_at->addMinutes($booking->service->buffer_minutes),
            ]);
    }
}
