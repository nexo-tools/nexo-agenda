<x-app-layout>
    <x-slot:title>{{ __('Agenda') }}</x-slot:title>

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold">{{ __('Agenda') }}</h1>
            <a href="{{ route('public.business', $business) }}" class="text-sm text-brand-700 hover:underline dark:text-brand-400">
                {{ url('/'.$business->slug) }}
            </a>
        </div>
        <div class="flex items-center gap-2">
            <x-button :href="route('frontdesk')" size="inline" variant="ghost">{{ __('Modo mostrador') }}</x-button>
            <x-button :href="route('bookings.create', ['date' => $day->toDateString()])" size="inline">
                ⊕ {{ __('Turno') }}
            </x-button>
        </div>
    </div>

    <div class="mt-4 flex flex-wrap items-center justify-between gap-2">
        <div class="flex items-center gap-1">
            <a href="{{ route('dashboard', ['date' => $day->subDay()->toDateString(), 'view' => $view]) }}"
               class="rounded-lg px-3 py-2 text-sm text-primary hover:bg-primary-subtle"
               aria-label="{{ __('Día anterior') }}">‹</a>

            <form method="GET" action="{{ route('dashboard') }}" x-data class="flex items-center gap-1">
                <input type="hidden" name="view" value="{{ $view }}">
                <label for="date" class="sr-only">{{ __('Fecha') }}</label>
                <input type="date" id="date" name="date" value="{{ $day->toDateString() }}" x-on:change="$el.form.requestSubmit()"
                       class="rounded-lg border-control bg-surface-raised text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500">
                <noscript><button class="nexo-btn nexo-btn--ghost nexo-btn--sm">{{ __('Ir') }}</button></noscript>
            </form>

            <a href="{{ route('dashboard', ['date' => $day->addDay()->toDateString(), 'view' => $view]) }}"
               class="rounded-lg px-3 py-2 text-sm text-primary hover:bg-primary-subtle"
               aria-label="{{ __('Día siguiente') }}">›</a>

            <a href="{{ route('dashboard', ['view' => $view]) }}"
               class="rounded-lg px-3 py-2 text-sm text-muted hover:bg-bg-subtle">
                {{ __('Hoy') }}
            </a>
        </div>

        <div class="flex rounded-lg bg-surface-sunken p-0.5 text-sm" role="group">
            <a href="{{ route('dashboard', ['date' => $day->toDateString(), 'view' => 'day']) }}"
               @class(['rounded-md px-3 py-1.5', 'bg-surface font-medium shadow-sm' => $view === 'day'])>
                {{ __('Día') }}
            </a>
            <a href="{{ route('dashboard', ['date' => $day->toDateString(), 'view' => 'week']) }}"
               @class(['rounded-md px-3 py-1.5', 'bg-surface font-medium shadow-sm' => $view === 'week'])>
                {{ __('Semana') }}
            </a>
        </div>
    </div>

    @if ($view === 'day')
        <p class="mt-4 font-medium capitalize">{{ $day->isoFormat('dddd D [de] MMMM YYYY') }}</p>

        @if ($professionals->isEmpty())
            <div class="mt-6 rounded-2xl border border-dashed border-line-strong p-8 text-center text-muted">
                {{ __('Agrega profesionales y servicios para empezar a recibir reservas.') }}
            </div>
        @endif

        <div class="mt-4 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($professionals as $professional)
                @php($items = $bookings->where('professional_id', $professional->id))
                <section class="rounded-2xl bg-surface-raised p-4 shadow-sm">
                    <h2 class="font-semibold">{{ $professional->name }}</h2>

                    @if ($items->isEmpty())
                        <p class="mt-2 text-sm text-muted">{{ __('Sin turnos este día.') }}</p>
                    @endif

                    <ul class="mt-2 space-y-2">
                        @foreach ($items as $booking)
                            <li @class([
                                'rounded-xl border p-3 text-sm',
                                'border-line' => $booking->status !== \App\Enums\BookingStatus::Cancelled,
                                'border-line opacity-50' => $booking->status === \App\Enums\BookingStatus::Cancelled,
                            ])>
                                <div class="flex items-center justify-between gap-2">
                                    <span class="font-semibold">
                                        {{ $booking->starts_at->setTimezone($tz)->format('H:i') }}–{{ $booking->ends_at->setTimezone($tz)->format('H:i') }}
                                    </span>
                                    <x-status-badge :status="$booking->status" />
                                </div>
                                <p class="mt-1">
                                    {{ $booking->client_name }} · {{ $booking->service->name }}
                                    @if ($booking->client_phone)
                                        <a href="https://wa.me/{{ preg_replace('/\D/', '', $booking->client_phone) }}?text={{ urlencode(__('Hola :name, te recordamos tu turno de :service el :date a las :time en :business. ¡Te esperamos!', ['name' => $booking->client_name, 'service' => $booking->service->name, 'date' => $booking->starts_at->setTimezone($tz)->isoFormat('dddd D/M'), 'time' => $booking->starts_at->setTimezone($tz)->format('H:i'), 'business' => $business->name])) }}"
                                           class="ml-1 text-brand-700 hover:underline dark:text-brand-400" rel="noopener" target="_blank">
                                            ✆ WhatsApp
                                        </a>
                                    @endif
                                </p>
                                @if ($booking->note)
                                    <p class="mt-1 text-xs text-muted">{{ $booking->note }}</p>
                                @endif

                                @if ($booking->status === \App\Enums\BookingStatus::Confirmed)
                                    <div class="mt-2 flex gap-1">
                                        <form method="POST" action="{{ route('bookings.status', $booking) }}">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="attended">
                                            <button class="nexo-btn nexo-btn--sm text-success hover:bg-success-subtle">
                                                ✓ {{ __('Asistió') }}
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('bookings.status', $booking) }}">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="no_show">
                                            <button class="nexo-btn nexo-btn--sm text-danger hover:bg-danger-subtle">
                                                ✗ {{ __('No vino') }}
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('bookings.status', $booking) }}"
                                              x-data x-on:submit="if (! confirm(@js(__('¿Cancelar este turno?')))) $event.preventDefault()">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="cancelled">
                                            <button class="nexo-btn nexo-btn--sm text-muted hover:bg-bg-subtle">
                                                {{ __('Cancelar') }}
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endforeach
        </div>
    @else
        <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            @for ($i = 0; $i < 7; $i++)
                @php($weekDay = $weekStart->addDays($i))
                @php($items = $bookings->filter(fn ($b) => $b->starts_at->setTimezone($tz)->isSameDay($weekDay)))
                <a href="{{ route('dashboard', ['date' => $weekDay->toDateString(), 'view' => 'day']) }}"
                   @class([
                       'rounded-2xl bg-surface-raised p-4 shadow-sm hover:ring-2 hover:ring-brand-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500',
                       'ring-2 ring-brand-300' => $weekDay->isSameDay($day),
                   ])>
                    <p class="text-sm font-semibold capitalize">{{ $weekDay->isoFormat('dddd D') }}</p>
                    <p class="text-xs text-muted">
                        {{ trans_choice('{0}Sin turnos|{1}:count turno|[2,*]:count turnos', $items->where('status', '!=', \App\Enums\BookingStatus::Cancelled)->count()) }}
                    </p>
                    <ul class="mt-2 space-y-1 text-xs text-muted">
                        @foreach ($items->take(3) as $booking)
                            <li>{{ $booking->starts_at->setTimezone($tz)->format('H:i') }} {{ $booking->client_name }}</li>
                        @endforeach
                    </ul>
                </a>
            @endfor
        </div>
    @endif
</x-app-layout>
