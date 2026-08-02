<?php

namespace App\Http\Controllers;

use App\Mail\WaitlistJoined;
use App\Models\Business;
use App\Models\Service;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class WaitlistController extends Controller
{
    public function store(Request $request, Business $business, Service $service): RedirectResponse
    {
        abort_unless($service->is_active, 404);

        $validated = $request->validate([
            'professional' => ['required', 'string'],
            'date' => ['required', 'date_format:Y-m-d'],
            'client_name' => ['required', 'string', 'max:255'],
            'client_email' => ['required', 'email', 'max:255'],
        ]);

        $professionalId = null;

        if ($validated['professional'] !== 'any') {
            $professionalId = $business->professionals()
                ->where('is_active', true)
                ->findOrFail((int) $validated['professional'])
                ->id;
        }

        $today = CarbonImmutable::now($business->timezone)->toDateString();
        $horizon = CarbonImmutable::now($business->timezone)->addDays($service->max_advance_days)->toDateString();

        if ($validated['date'] < $today || $validated['date'] > $horizon) {
            return back()->withErrors(['date' => __('That date is not available for the waitlist.')]);
        }

        $alreadyListed = $business->waitlistEntries()
            ->where('service_id', $service->id)
            ->where('professional_id', $professionalId)
            ->whereDate('date', $validated['date'])
            ->where('client_email', $validated['client_email'])
            ->exists();

        if (! $alreadyListed) {
            $entry = $business->waitlistEntries()->create([
                'service_id' => $service->id,
                'professional_id' => $professionalId,
                'date' => $validated['date'],
                'client_name' => $validated['client_name'],
                'client_email' => $validated['client_email'],
            ]);

            // The receipt. Joining used to produce a flash message and nothing
            // else: the person closed the tab with no record it had happened,
            // and the only mail they could ever get was one that may never come.
            Mail::to($entry->client_email)
                ->locale(app()->getLocale())
                ->queue(new WaitlistJoined($entry->load(['business', 'service', 'professional'])));
        }

        return redirect()
            ->route('public.times', [$business, $service, 'professional' => $validated['professional'], 'date' => $validated['date']])
            ->with('status', __('Done! We\'ll email you if a time frees up that day.'));
    }
}
