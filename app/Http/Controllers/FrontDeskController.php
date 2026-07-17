<?php

namespace App\Http\Controllers;

use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FrontDeskController extends Controller
{
    public function __invoke(Request $request): View
    {
        $business = $request->user()->business;
        $tz = $business->timezone;
        $now = CarbonImmutable::now($tz);
        $today = $now->startOfDay();

        $bookings = $business->bookings()
            ->with(['service:id,name,duration_minutes', 'professional:id,name'])
            ->where('starts_at', '>=', $today->utc())
            ->where('starts_at', '<', $today->addDay()->utc())
            ->orderBy('starts_at')
            ->get();

        return view('app.frontdesk', [
            'business' => $business,
            'professionals' => $business->professionals()->where('is_active', true)->orderBy('name')->get(),
            'bookings' => $bookings,
            'now' => $now,
            'tz' => $tz,
        ]);
    }
}
