<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Mail\BookingCancelled;
use App\Mail\BookingConfirmed;
use App\Models\Booking;
use App\Services\WaitlistNotifier;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AppBookingController extends Controller
{
    public function create(Request $request): View
    {
        $business = $request->user()->business;

        return view('app.bookings.create', [
            'business' => $business,
            'services' => $business->services()->where('is_active', true)->orderBy('name')->get(),
            'professionals' => $business->professionals()->where('is_active', true)->orderBy('name')->get(),
            'suggestedDate' => $request->string('date')->toString() ?: CarbonImmutable::now($business->timezone)->toDateString(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $business = $request->user()->business;

        $validated = $request->validate([
            'service_id' => ['required', Rule::exists('services', 'id')->where('business_id', $business->id)],
            'professional_id' => ['required', Rule::exists('professionals', 'id')->where('business_id', $business->id)],
            'date' => ['required', 'date_format:Y-m-d'],
            'time' => ['required', 'date_format:H:i'],
            'client_name' => ['required', 'string', 'max:255'],
            'client_email' => ['nullable', 'email', 'max:255'],
            'client_phone' => ['nullable', 'string', 'max:30'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $service = $business->services()->findOrFail($validated['service_id']);
        $professional = $business->professionals()->findOrFail($validated['professional_id']);
        $start = CarbonImmutable::parse($validated['date'].' '.$validated['time'], $business->timezone);
        $end = $start->addMinutes($service->duration_minutes);
        $token = Booking::newManagementToken();

        $booking = DB::transaction(function () use ($business, $service, $professional, $validated, $start, $end, $token): ?Booking {
            $professional->bookings()->lockForUpdate()->get();

            // Owners can book outside public rules (walk-ins, favors); only real
            // collisions with other bookings are rejected.
            $collision = $professional->bookings()
                ->occupying()
                ->where('starts_at', '<', $end->utc())
                ->where('ends_at', '>', $start->utc())
                ->exists();

            if ($collision) {
                return null;
            }

            return $business->bookings()->create([
                'professional_id' => $professional->id,
                'service_id' => $service->id,
                'client_name' => $validated['client_name'],
                'client_email' => $validated['client_email'] ?? null,
                'client_phone' => $validated['client_phone'] ?? null,
                'note' => $validated['note'] ?? null,
                'starts_at' => $start->utc(),
                'ends_at' => $end->utc(),
                'management_token' => $token['hash'],
            ]);
        });

        if ($booking === null) {
            return back()->withInput()->withErrors(['time' => __('That time overlaps another appointment.')]);
        }

        if ($booking->client_email !== null) {
            $booking->setRelations(['business' => $business, 'service' => $service, 'professional' => $professional]);
            Mail::to($booking->client_email)->send(new BookingConfirmed($booking, $token['token']));
        }

        return redirect()
            ->route('dashboard', ['date' => $start->toDateString()])
            ->with('status', __('Appointment created.'));
    }

    public function updateStatus(Request $request, Booking $booking): RedirectResponse
    {
        Gate::authorize('manage', $booking);

        $validated = $request->validate([
            'status' => ['required', Rule::enum(BookingStatus::class)],
        ]);

        if ($validated['status'] === BookingStatus::Cancelled->value) {
            $booking->cancel();

            if ($booking->client_email !== null) {
                Mail::to($booking->client_email)->send(new BookingCancelled($booking->load(['business', 'service', 'professional'])));
            }

            app(WaitlistNotifier::class)->bookingCancelled($booking);
        } else {
            $booking->update(['status' => $validated['status']]);
        }

        return back()->with('status', __('Appointment updated.'));
    }
}
