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
            <a href="{{ route('frontdesk') }}"
               class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-800">
                {{ __('Modo mostrador') }}
            </a>
            <a href="{{ route('bookings.create', ['date' => $day->toDateString()]) }}"
               class="rounded-lg bg-brand-700 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-800">
                ⊕ {{ __('Turno') }}
            </a>
        </div>
    </div>

    <div class="mt-4 flex flex-wrap items-center justify-between gap-2">
        <div class="flex items-center gap-1">
            <a href="{{ route('dashboard', ['date' => $day->subDay()->toDateString(), 'view' => $view]) }}"
               class="rounded-lg px-3 py-2 text-sm text-brand-700 hover:bg-brand-50 dark:text-brand-400 dark:hover:bg-slate-800"
               aria-label="{{ __('Día anterior') }}">‹</a>

            <form method="GET" action="{{ route('dashboard') }}">
                <input type="hidden" name="view" value="{{ $view }}">
                <label for="date" class="sr-only">{{ __('Fecha') }}</label>
                <input type="date" id="date" name="date" value="{{ $day->toDateString() }}" onchange="this.form.submit()"
                       class="rounded-lg border-slate-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-800">
            </form>

            <a href="{{ route('dashboard', ['date' => $day->addDay()->toDateString(), 'view' => $view]) }}"
               class="rounded-lg px-3 py-2 text-sm text-brand-700 hover:bg-brand-50 dark:text-brand-400 dark:hover:bg-slate-800"
               aria-label="{{ __('Día siguiente') }}">›</a>

            <a href="{{ route('dashboard', ['view' => $view]) }}"
               class="rounded-lg px-3 py-2 text-sm text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800">
                {{ __('Hoy') }}
            </a>
        </div>

        <div class="flex rounded-lg bg-slate-200 p-0.5 text-sm dark:bg-slate-700" role="group">
            <a href="{{ route('dashboard', ['date' => $day->toDateString(), 'view' => 'day']) }}"
               @class(['rounded-md px-3 py-1.5', 'bg-white font-medium shadow-sm dark:bg-slate-900' => $view === 'day'])>
                {{ __('Día') }}
            </a>
            <a href="{{ route('dashboard', ['date' => $day->toDateString(), 'view' => 'week']) }}"
               @class(['rounded-md px-3 py-1.5', 'bg-white font-medium shadow-sm dark:bg-slate-900' => $view === 'week'])>
                {{ __('Semana') }}
            </a>
        </div>
    </div>

    @if ($view === 'day')
        <p class="mt-4 font-medium capitalize">{{ $day->isoFormat('dddd D [de] MMMM YYYY') }}</p>

        @if ($professionals->isEmpty())
            <div class="mt-6 rounded-2xl border border-dashed border-slate-300 p-8 text-center text-slate-500 dark:border-slate-700">
                {{ __('Agrega profesionales y servicios para empezar a recibir reservas.') }}
            </div>
        @endif

        <div class="mt-4 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($professionals as $professional)
                @php($items = $bookings->where('professional_id', $professional->id))
                <section class="rounded-2xl bg-white p-4 shadow-sm dark:bg-slate-800">
                    <h2 class="font-semibold">{{ $professional->name }}</h2>

                    @if ($items->isEmpty())
                        <p class="mt-2 text-sm text-slate-500">{{ __('Sin turnos este día.') }}</p>
                    @endif

                    <ul class="mt-2 space-y-2">
                        @foreach ($items as $booking)
                            <li @class([
                                'rounded-xl border p-3 text-sm',
                                'border-slate-200 dark:border-slate-700' => $booking->status !== \App\Enums\BookingStatus::Cancelled,
                                'border-slate-100 opacity-50 dark:border-slate-700' => $booking->status === \App\Enums\BookingStatus::Cancelled,
                            ])>
                                <div class="flex items-center justify-between gap-2">
                                    <span class="font-semibold">
                                        {{ $booking->starts_at->setTimezone($tz)->format('H:i') }}–{{ $booking->ends_at->setTimezone($tz)->format('H:i') }}
                                    </span>
                                    <span @class([
                                        'rounded px-2 py-0.5 text-xs',
                                        'bg-brand-100 text-brand-900' => $booking->status === \App\Enums\BookingStatus::Confirmed,
                                        'bg-emerald-100 text-emerald-900' => $booking->status === \App\Enums\BookingStatus::Attended,
                                        'bg-red-100 text-red-900' => $booking->status === \App\Enums\BookingStatus::NoShow,
                                        'bg-slate-200 text-slate-600' => $booking->status === \App\Enums\BookingStatus::Cancelled,
                                    ])>
                                        {{ $booking->status->label() }}
                                    </span>
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
                                    <p class="mt-1 text-xs text-slate-500">{{ $booking->note }}</p>
                                @endif

                                @if ($booking->status === \App\Enums\BookingStatus::Confirmed)
                                    <div class="mt-2 flex gap-1">
                                        <form method="POST" action="{{ route('bookings.status', $booking) }}">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="attended">
                                            <button class="rounded-lg px-2 py-1 text-xs text-emerald-700 hover:bg-emerald-50 dark:text-emerald-400 dark:hover:bg-slate-700">
                                                ✓ {{ __('Asistió') }}
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('bookings.status', $booking) }}">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="no_show">
                                            <button class="rounded-lg px-2 py-1 text-xs text-red-700 hover:bg-red-50 dark:text-red-400 dark:hover:bg-slate-700">
                                                ✗ {{ __('No vino') }}
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('bookings.status', $booking) }}"
                                              onsubmit="return confirm(@js(__('¿Cancelar este turno?')))">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="cancelled">
                                            <button class="rounded-lg px-2 py-1 text-xs text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700">
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
                       'rounded-2xl bg-white p-4 shadow-sm hover:ring-2 hover:ring-brand-500 dark:bg-slate-800',
                       'ring-2 ring-brand-300' => $weekDay->isSameDay($day),
                   ])>
                    <p class="text-sm font-semibold capitalize">{{ $weekDay->isoFormat('dddd D') }}</p>
                    <p class="text-xs text-slate-500">
                        {{ trans_choice('{0}Sin turnos|{1}:count turno|[2,*]:count turnos', $items->where('status', '!=', \App\Enums\BookingStatus::Cancelled)->count()) }}
                    </p>
                    <ul class="mt-2 space-y-1 text-xs text-slate-600 dark:text-slate-400">
                        @foreach ($items->take(3) as $booking)
                            <li>{{ $booking->starts_at->setTimezone($tz)->format('H:i') }} {{ $booking->client_name }}</li>
                        @endforeach
                    </ul>
                </a>
            @endfor
        </div>
    @endif
</x-app-layout>
