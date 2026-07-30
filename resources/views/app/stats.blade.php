<x-app-layout>
    <x-slot:title>{{ __('Statistics') }}</x-slot:title>

    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-2xl font-bold">{{ __('Statistics') }}</h1>

        <nav class="flex rounded-lg bg-surface-sunken p-0.5 text-sm" aria-label="{{ __('Period') }}">
            @foreach (['30d' => __('30 days'), 'month' => __('This month'), 'last_month' => __('Last month')] as $key => $label)
                <a href="{{ route('stats', ['period' => $key]) }}"
                   @class(['rounded-md px-3 py-1.5', 'bg-surface font-medium shadow-sm' => $period === $key])>
                    {{ $label }}
                </a>
            @endforeach
        </nav>
    </div>

    <p class="mt-1 text-sm text-muted">{{ $periodLabel }} · {{ $from->isoFormat('D MMM') }} – {{ $to->isoFormat('D MMM') }}</p>

    <div class="mt-6 grid grid-cols-2 gap-3 lg:grid-cols-4">
        @foreach ([
            [__('Appointments'), $stats['total'], null],
            [__('Attendances'), $stats['attended'], null],
            [__('No-shows'), $stats['no_shows'], $stats['no_show_rate'].'%'],
            [__('Occupancy'), $stats['occupancy'] !== null ? $stats['occupancy'].'%' : '—', null],
            [__('Visits to your page'), $stats['visits'], null],
            [__('Visit → booking conversion'), $stats['conversion'] !== null ? $stats['conversion'].'%' : '—', null],
            [__('Cancellations'), $stats['cancelled'], null],
        ] as [$label, $value, $hint])
            <div class="rounded-2xl bg-surface-raised p-4 shadow-sm">
                <p class="text-sm text-muted">{{ $label }}</p>
                <p class="mt-1 text-3xl font-bold tabular-nums">{{ $value }}</p>
                @if ($hint)
                    <p class="text-xs text-muted">{{ $hint }} {{ __('of appointments') }}</p>
                @endif
            </div>
        @endforeach
    </div>

    @php($max = max(1, max($stats['per_day'])))
    <section class="mt-6 rounded-2xl bg-surface-raised p-4 shadow-sm" aria-label="{{ __('Appointments per day') }}">
        <h2 class="font-semibold">{{ __('Appointments per day') }}</h2>

        <div class="mt-4 flex h-36 items-end gap-0.5" role="img"
             aria-label="{{ __('Bar chart: appointments per day, maximum :max', ['max' => $max]) }}">
            @foreach ($stats['per_day'] as $date => $count)
                {{-- tabindex, not only title: a title tooltip is hover-only, so on a
                     phone (where this dashboard is read) the per-day number was
                     unreachable without opening the table below. --}}
                <div class="group relative flex h-full flex-1 flex-col justify-end focus:outline-none"
                     tabindex="0"
                     aria-label="{{ \Carbon\CarbonImmutable::parse($date)->isoFormat('ddd D MMM') }}: {{ trans_choice(':count appointment|:count appointments', $count) }}"
                     title="{{ \Carbon\CarbonImmutable::parse($date)->isoFormat('ddd D MMM') }}: {{ trans_choice(':count appointment|:count appointments', $count) }}">
                    <span class="pointer-events-none absolute inset-x-0 -top-1 hidden justify-center text-xs font-semibold tabular-nums text-ink group-hover:flex group-focus:flex">{{ $count }}</span>
                    <div class="w-full rounded-t bg-brand-600 group-focus:ring-2 group-focus:ring-ring dark:bg-brand-400"
                         style="height: {{ $count === 0 ? '2px' : round($count * 100 / $max) .'%' }}; {{ $count === 0 ? 'opacity:.25' : '' }}"></div>
                </div>
            @endforeach
        </div>
        <div class="mt-1 flex justify-between text-xs text-muted">
            <span>{{ $from->isoFormat('D MMM') }}</span>
            <span>{{ $to->isoFormat('D MMM') }}</span>
        </div>

        <details class="mt-3">
            <summary class="cursor-pointer text-xs text-muted">{{ __('View as table') }}</summary>
            <div class="mt-2 max-h-48 overflow-y-auto">
                <table class="w-full text-left text-xs">
                    <thead><tr><th class="py-1 pr-4 font-medium">{{ __('Date') }}</th><th class="py-1 font-medium">{{ __('Appointments') }}</th></tr></thead>
                    <tbody>
                        @foreach ($stats['per_day'] as $date => $count)
                            <tr><td class="py-0.5 pr-4">{{ $date }}</td><td class="py-0.5 tabular-nums">{{ $count }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </details>
    </section>

    <div class="mt-6 grid gap-4 md:grid-cols-2">
        @foreach ([[__('Most booked services'), $stats['top_services']], [__('Most booked professionals'), $stats['top_professionals']]] as [$title, $items])
            <section class="rounded-2xl bg-surface-raised p-4 shadow-sm">
                <h2 class="font-semibold">{{ $title }}</h2>
                @if ($items->isEmpty())
                    <p class="mt-2 text-sm text-muted">{{ __('No data in this period.') }}</p>
                @else
                    <ul class="mt-2 space-y-2 text-sm">
                        @foreach ($items as $name => $count)
                            <li class="flex items-center gap-2">
                                <span class="w-32 truncate">{{ $name }}</span>
                                <span class="h-2 rounded-full bg-brand-600 dark:bg-brand-400"
                                      style="width: {{ round($count * 100 / max(1, $items->max())) }}%"></span>
                                <span class="tabular-nums text-muted">{{ $count }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>
        @endforeach
    </div>
</x-app-layout>
