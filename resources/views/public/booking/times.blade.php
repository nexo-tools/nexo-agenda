<x-public-layout :title="__('¿Cuándo?').' — '.$business->name" :business="$business">
    <a href="{{ route('public.professional', [$business, $service]) }}" class="text-sm text-brand-700 hover:underline dark:text-brand-400">
        ← {{ __('Cambiar profesional') }}
    </a>
    <p class="mt-3 text-sm text-slate-500">{{ __('Paso 3 de 4') }}</p>
    <h1 class="mb-1 text-xl font-bold">{{ __('¿Cuándo?') }}</h1>
    <p class="mb-5 text-sm text-slate-600 dark:text-slate-400">
        {{ $service->name }} · {{ $chosen?->name ?? __('Cualquier profesional') }}
    </p>

    @if (session('slot_taken'))
        <p class="mb-4 rounded-lg bg-amber-100 px-4 py-3 text-sm text-amber-900" role="alert">
            {{ __('Ese horario acaba de ocuparse. Elige otro, por favor.') }}
        </p>
    @endif

    <div class="mb-4 flex items-center justify-between gap-2">
        @if ($canGoBack)
            <a href="{{ route('public.times', [$business, $service, 'professional' => $chosen?->id ?? 'any', 'date' => $day->subDay()->toDateString()]) }}"
               class="rounded-lg px-3 py-2 text-sm text-brand-700 hover:bg-brand-50 dark:text-brand-400 dark:hover:bg-slate-800"
               aria-label="{{ __('Día anterior') }}">‹</a>
        @else
            <span class="px-3 py-2 text-sm text-slate-300 dark:text-slate-600" aria-hidden="true">‹</span>
        @endif

        <form method="GET" action="{{ route('public.times', [$business, $service]) }}">
            <input type="hidden" name="professional" value="{{ $chosen?->id ?? 'any' }}">
            <label for="date" class="sr-only">{{ __('Fecha') }}</label>
            <input type="date" id="date" name="date" value="{{ $day->toDateString() }}" onchange="this.form.submit()"
                   class="rounded-lg border-slate-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-800">
        </form>

        @if ($canGoForward)
            <a href="{{ route('public.times', [$business, $service, 'professional' => $chosen?->id ?? 'any', 'date' => $day->addDay()->toDateString()]) }}"
               class="rounded-lg px-3 py-2 text-sm text-brand-700 hover:bg-brand-50 dark:text-brand-400 dark:hover:bg-slate-800"
               aria-label="{{ __('Día siguiente') }}">›</a>
        @else
            <span class="px-3 py-2 text-sm text-slate-300 dark:text-slate-600" aria-hidden="true">›</span>
        @endif
    </div>

    <p class="mb-3 text-center font-medium capitalize">{{ $day->isoFormat('dddd D [de] MMMM') }}</p>

    @if (session('status'))
        <p class="mb-4 rounded-lg bg-brand-100 px-4 py-3 text-sm text-brand-900" role="status">{{ session('status') }}</p>
    @endif

    @if ($slots->isEmpty())
        <p class="rounded-2xl border border-dashed border-slate-300 p-6 text-center text-sm text-slate-500 dark:border-slate-700">
            {{ __('No hay horarios disponibles este día.') }}
        </p>
    @else
        <ul class="grid grid-cols-3 gap-2 sm:grid-cols-4">
            @foreach ($slots as $time => $professionalId)
                <li>
                    <a href="{{ route('public.form', [$business, $service, 'professional' => $professionalId, 'start' => $day->toDateString().' '.$time]) }}"
                       class="block rounded-lg bg-white py-2 text-center text-sm font-medium shadow-sm hover:ring-2 hover:ring-brand-500 dark:bg-slate-800">
                        {{ $time }}
                    </a>
                </li>
            @endforeach
        </ul>
    @endif

    <details class="mt-6 rounded-2xl bg-white p-4 shadow-sm dark:bg-slate-800" @if ($errors->any() || $slots->isEmpty()) open @endif>
        <summary class="cursor-pointer text-sm font-medium text-brand-700 dark:text-brand-400">
            {{ $slots->isEmpty() ? __('Avisarme si se libera un horario este día') : __('¿Prefieres otro horario? Súmate a la lista de espera') }}
        </summary>
        <form method="POST" action="{{ route('public.waitlist', [$business, $service]) }}" class="mt-4 space-y-3">
            @csrf
            <input type="hidden" name="professional" value="{{ $chosen?->id ?? 'any' }}">
            <input type="hidden" name="date" value="{{ $day->toDateString() }}">

            <x-field :label="__('Nombre')" name="client_name" required autocomplete="name" />
            <x-field :label="__('Email')" name="client_email" type="email" required autocomplete="email" />
            @error('date')
                <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror

            <x-button>{{ __('Anotarme en la lista de espera') }}</x-button>
            <p class="text-xs text-slate-500">
                {{ __('Si alguien cancela ese día, te avisamos por email al instante.') }}
            </p>
        </form>
    </details>
</x-public-layout>
