<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\Availability;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BookingManagementController extends Controller
{
    public function __construct(private readonly Availability $availability) {}

    public function show(string $token): View
    {
        $booking = $this->findOrAbort($token);

        return view('public.booking.manage', [
            'booking' => $booking->load(['business', 'service', 'professional']),
            'token' => $token,
        ]);
    }

    public function cancel(string $token): RedirectResponse
    {
        $booking = $this->findOrAbort($token);

        if (! $booking->clientCanManage()) {
            return redirect()->route('booking.manage', $token)
                ->with('status', __('Este turno ya no puede cancelarse desde aquí. Contacta al negocio.'));
        }

        $booking->cancel();

        return redirect()->route('booking.manage', $token)->with('status', __('Tu turno fue cancelado.'));
    }

    public function reschedule(Request $request, string $token): View|RedirectResponse
    {
        $booking = $this->findOrAbort($token)->load(['business', 'service', 'professional']);

        if (! $booking->clientCanManage()) {
            return redirect()->route('booking.manage', $token)
                ->with('status', __('Este turno ya no puede reprogramarse desde aquí. Contacta al negocio.'));
        }

        $business = $booking->business;
        $service = $booking->service;
        $professional = $booking->professional;
        $tz = $business->timezone;

        if ($request->filled('date')) {
            $day = CarbonImmutable::parse($request->string('date')->toString(), $tz)->startOfDay();
        } else {
            $day = $this->availability->firstAvailableDay($service, collect([$professional]), CarbonImmutable::now($tz))
                ?? CarbonImmutable::now($tz)->startOfDay();
        }

        $today = CarbonImmutable::now($tz)->startOfDay();

        return view('public.booking.reschedule', [
            'booking' => $booking,
            'token' => $token,
            'day' => $day,
            'slots' => $this->availability->slots($service, $professional, $day, ignore: $booking),
            'canGoBack' => $day->gt($today),
            'canGoForward' => $day->lt($today->addDays($service->max_advance_days)),
        ]);
    }

    public function update(Request $request, string $token): RedirectResponse
    {
        $booking = $this->findOrAbort($token)->load(['business', 'service', 'professional']);

        if (! $booking->clientCanManage()) {
            return redirect()->route('booking.manage', $token)
                ->with('status', __('Este turno ya no puede reprogramarse desde aquí. Contacta al negocio.'));
        }

        $request->validate(['start' => ['required', 'date_format:Y-m-d H:i']]);

        $start = CarbonImmutable::parse($request->string('start')->toString(), $booking->business->timezone);

        $updated = DB::transaction(function () use ($booking, $start): bool {
            $booking->professional->bookings()->lockForUpdate()->get();

            $isFree = $this->availability
                ->slots($booking->service, $booking->professional, $start->startOfDay(), ignore: $booking)
                ->contains(fn (CarbonImmutable $slot) => $slot->equalTo($start));

            if (! $isFree) {
                return false;
            }

            $booking->forceFill([
                'starts_at' => $start->utc(),
                'ends_at' => $start->addMinutes($booking->service->duration_minutes)->utc(),
                'reminded_at' => null,
            ])->save();

            return true;
        });

        if (! $updated) {
            return redirect()->route('booking.reschedule', [$token, 'date' => $start->toDateString()])
                ->with('slot_taken', true);
        }

        return redirect()->route('booking.manage', $token)->with('status', __('Tu turno fue reprogramado.'));
    }

    private function findOrAbort(string $token): Booking
    {
        $booking = Booking::findByManagementToken($token);

        abort_if($booking === null, 404);

        return $booking;
    }
}
