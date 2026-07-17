<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Mail\BookingConfirmed;
use App\Mail\NewBookingReceived;
use App\Models\Booking;
use App\Models\Business;
use App\Models\Professional;
use App\Models\Service;
use App\Services\Availability;
use App\Services\BusinessStats;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class PublicBookingController extends Controller
{
    public function __construct(private readonly Availability $availability) {}

    public function business(Request $request, Business $business): View
    {
        app(BusinessStats::class)->recordVisit($business, $request);

        return view('public.business', [
            'business' => $business,
            'services' => $business->services()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function professional(Business $business, Service $service): View
    {
        abort_unless($service->is_active, 404);

        return view('public.booking.professional', [
            'business' => $business,
            'service' => $service,
            'professionals' => $this->activeProfessionals($business),
        ]);
    }

    public function times(Request $request, Business $business, Service $service): View|RedirectResponse
    {
        abort_unless($service->is_active, 404);

        $professionals = $this->activeProfessionals($business);
        $chosen = $this->resolveProfessional($professionals, $request->string('professional')->toString());
        $pool = $chosen === null ? $professionals : collect([$chosen]);
        $tz = $business->timezone;

        if ($request->filled('date')) {
            $day = CarbonImmutable::parse($request->string('date')->toString(), $tz)->startOfDay();
        } else {
            $day = $this->availability->firstAvailableDay($service, $pool, CarbonImmutable::now($tz))
                ?? CarbonImmutable::now($tz)->startOfDay();
        }

        $today = CarbonImmutable::now($tz)->startOfDay();
        $horizon = $today->addDays($service->max_advance_days);

        return view('public.booking.times', [
            'business' => $business,
            'service' => $service,
            'chosen' => $chosen,
            'day' => $day,
            'slots' => $chosen === null
                ? $this->availability->slotsForAny($service, $pool, $day)
                : $this->availability->slots($service, $chosen, $day)
                    ->mapWithKeys(fn (CarbonImmutable $slot) => [$slot->format('H:i') => $chosen->id]),
            'canGoBack' => $day->gt($today),
            'canGoForward' => $day->lt($horizon),
        ]);
    }

    public function form(Request $request, Business $business, Service $service): View|RedirectResponse
    {
        abort_unless($service->is_active, 404);

        $professional = $business->professionals()
            ->where('is_active', true)
            ->findOrFail($request->integer('professional'));

        $start = CarbonImmutable::parse($request->string('start')->toString(), $business->timezone);

        if (! $this->slotIsFree($service, $professional, $start)) {
            return redirect()
                ->route('public.times', [$business, $service, 'professional' => $professional->id, 'date' => $start->toDateString()])
                ->with('slot_taken', true);
        }

        return view('public.booking.form', [
            'business' => $business,
            'service' => $service,
            'professional' => $professional,
            'start' => $start,
        ]);
    }

    public function store(StoreBookingRequest $request, Business $business, Service $service): RedirectResponse
    {
        abort_unless($service->is_active, 404);

        $professional = $business->professionals()
            ->where('is_active', true)
            ->findOrFail($request->integer('professional_id'));

        $start = CarbonImmutable::parse($request->string('start')->toString(), $business->timezone);
        $token = Booking::newManagementToken();

        $booking = DB::transaction(function () use ($request, $business, $service, $professional, $start, $token): ?Booking {
            // Serialize concurrent bookings for the same professional before re-checking.
            $professional->bookings()->lockForUpdate()->get();

            if (! $this->slotIsFree($service, $professional, $start)) {
                return null;
            }

            return $business->bookings()->create([
                'professional_id' => $professional->id,
                'service_id' => $service->id,
                'client_name' => $request->validated('client_name'),
                'client_email' => $request->validated('client_email'),
                'client_phone' => $request->validated('client_phone') ?: null,
                'note' => $request->validated('note') ?: null,
                'starts_at' => $start->utc(),
                'ends_at' => $start->addMinutes($service->duration_minutes)->utc(),
                'management_token' => $token['hash'],
            ]);
        });

        if ($booking === null) {
            return redirect()
                ->route('public.times', [$business, $service, 'professional' => $professional->id, 'date' => $start->toDateString()])
                ->with('slot_taken', true);
        }

        $booking->setRelations(['business' => $business, 'service' => $service, 'professional' => $professional]);
        Mail::to($booking->client_email)->send(new BookingConfirmed($booking, $token['token']));
        Mail::to($business->user->email)->send(new NewBookingReceived($booking));

        return redirect()->route('booking.manage', $token['token']);
    }

    /** @return Collection<int, Professional> */
    private function activeProfessionals(Business $business)
    {
        return $business->professionals()->where('is_active', true)->orderBy('name')->get();
    }

    /** @param  Collection<int, Professional>  $professionals */
    private function resolveProfessional($professionals, string $param): ?Professional
    {
        if ($param === '' || $param === 'any') {
            return null;
        }

        return $professionals->firstWhere('id', (int) $param);
    }

    private function slotIsFree(Service $service, Professional $professional, CarbonImmutable $start): bool
    {
        return $this->availability
            ->slots($service, $professional, $start->startOfDay())
            ->contains(fn (CarbonImmutable $slot) => $slot->equalTo($start));
    }
}
