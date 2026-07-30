<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head', ['title' => __('Front desk'), 'noindex' => true])
    </head>
    <body class="min-h-screen bg-bg font-sans text-ink antialiased" x-data="frontdeskRefresh(60)">
        <header class="flex items-center justify-between px-4 py-3 text-sm text-muted">
            <a href="{{ route('dashboard') }}" class="rounded-lg px-3 py-1.5 hover:bg-bg-subtle">← {{ __('Schedule') }}</a>
            <span class="font-semibold text-ink">{{ $business->name }}</span>
            <span class="flex items-center gap-2">
                <span id="reloj" class="tabular-nums">{{ $now->format('H:i') }}</span>
                <button type="button" class="nexo-btn nexo-btn--ghost nexo-btn--sm"
                        x-on:click="paused = ! paused" :aria-pressed="paused"
                        x-text="paused ? @js(__('Auto-refresh paused')) : @js(__('Auto-refreshing'))">{{ __('Auto-refreshing') }}</button>
            </span>
        </header>

        <main id="mostrador" class="grid gap-4 p-4 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($professionals as $professional)
                @php($items = $bookings->where('professional_id', $professional->id)->filter(fn ($b) => $b->status !== \App\Enums\BookingStatus::Cancelled))
                @php($next = $items->first(fn ($b) => $b->status === \App\Enums\BookingStatus::Confirmed && $b->ends_at->gte($now)))
                <section class="rounded-2xl bg-surface p-4">
                    <h2 class="text-lg font-bold">{{ $professional->name }}</h2>

                    @if ($items->isEmpty())
                        <p class="mt-3 text-muted">{{ __('No appointments today.') }}</p>
                    @endif

                    <ul class="mt-3 space-y-3">
                        @foreach ($items as $booking)
                            <li @class([
                                'rounded-xl p-4',
                                'bg-primary-subtle ring-2 ring-ring' => $next && $booking->is($next),
                                'bg-surface-raised' => ! $next || ! $booking->is($next),
                                'opacity-60' => $booking->status !== \App\Enums\BookingStatus::Confirmed,
                            ])>
                                <div class="flex items-center justify-between gap-3">
                                    <span class="text-2xl font-bold tabular-nums">{{ $booking->starts_at->setTimezone($tz)->format('H:i') }}</span>
                                    @if ($booking->status !== \App\Enums\BookingStatus::Confirmed)
                                        <x-status-badge :status="$booking->status" size="md" />
                                    @elseif ($next && $booking->is($next))
                                        <span class="rounded bg-primary px-2 py-1 text-xs font-bold text-primary-fg">{{ __('Next') }}</span>
                                    @endif
                                </div>
                                <p class="mt-1 text-lg">{{ $booking->client_name }}</p>
                                <p class="text-sm text-muted">{{ $booking->service->name }}</p>

                                @if ($booking->status === \App\Enums\BookingStatus::Confirmed)
                                    <div class="mt-3 flex gap-2">
                                        <form method="POST" action="{{ route('bookings.status', $booking) }}" class="flex-1">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="attended">
                                            <button class="w-full rounded-lg bg-success px-3 py-2.5 text-sm font-bold text-success-fg hover:brightness-110">
                                                <x-icon name="check" /> {{ __('Attended') }}
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('bookings.status', $booking) }}" class="flex-1">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="no_show">
                                            <button class="w-full rounded-lg bg-danger px-3 py-2.5 text-sm font-bold text-danger-fg hover:brightness-110">
                                                <x-icon name="x" /> {{ __('Didn\'t show') }}
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endforeach
        </main>
    </body>
</html>
