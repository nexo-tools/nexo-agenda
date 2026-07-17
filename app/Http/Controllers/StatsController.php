<?php

namespace App\Http\Controllers;

use App\Services\BusinessStats;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StatsController extends Controller
{
    public function __invoke(Request $request, BusinessStats $stats): View
    {
        $business = $request->user()->business;
        $tz = $business->timezone;
        $today = CarbonImmutable::now($tz)->startOfDay();

        $period = $request->string('period')->toString();

        [$from, $to, $label] = match ($period) {
            'month' => [$today->startOfMonth(), $today, __('Este mes')],
            'last_month' => [
                $today->subMonthNoOverflow()->startOfMonth(),
                $today->subMonthNoOverflow()->endOfMonth()->startOfDay(),
                __('Mes pasado'),
            ],
            default => [$today->subDays(29), $today, __('Últimos 30 días')],
        };

        return view('app.stats', [
            'business' => $business,
            'stats' => $stats->forPeriod($business, $from, $to),
            'from' => $from,
            'to' => $to,
            'periodLabel' => $label,
            'period' => $period ?: '30d',
        ]);
    }
}
