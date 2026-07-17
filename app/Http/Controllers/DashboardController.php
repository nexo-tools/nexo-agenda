<?php

namespace App\Http\Controllers;

use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $business = $request->user()->business;
        $tz = $business->timezone;

        $day = $request->filled('date')
            ? CarbonImmutable::parse($request->string('date')->toString(), $tz)->startOfDay()
            : CarbonImmutable::now($tz)->startOfDay();

        $view = $request->string('view')->toString() === 'week' ? 'week' : 'day';

        $from = $view === 'week' ? $day->startOfWeek() : $day;
        $to = $view === 'week' ? $from->addWeek() : $day->addDay();

        $bookings = $business->bookings()
            ->with(['service:id,name,duration_minutes,mode', 'professional:id,name'])
            ->where('starts_at', '>=', $from->utc())
            ->where('starts_at', '<', $to->utc())
            ->orderBy('starts_at')
            ->get();

        return view('app.dashboard', [
            'business' => $business,
            'professionals' => $business->professionals()->where('is_active', true)->orderBy('name')->get(),
            'day' => $day,
            'view' => $view,
            'weekStart' => $from,
            'bookings' => $bookings,
            'tz' => $tz,
        ]);
    }
}
