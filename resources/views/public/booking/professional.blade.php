<x-public-layout :title="__('¿Con quién?').' — '.$business->name" :business="$business">
    <a href="{{ route('public.business', $business) }}" class="text-sm text-brand-700 hover:underline dark:text-brand-400">
        ← {{ $business->name }}
    </a>
    <p class="mt-3 text-sm text-muted">{{ __('Paso 2 de 4') }}</p>
    <h1 class="mb-1 text-xl font-bold">{{ __('¿Con quién?') }}</h1>
    <p class="mb-5 text-sm text-muted">
        {{ $service->name }} · {{ $service->duration_minutes }} min
    </p>

    @if ($professionals->isEmpty())
        <p class="rounded-2xl border border-dashed border-line-strong p-6 text-center text-sm text-muted">
            {{ __('Este negocio todavía no tiene profesionales disponibles.') }}
        </p>
    @else
        <form method="GET" action="{{ route('public.times', [$business, $service]) }}" class="space-y-3">
            <label class="flex cursor-pointer items-center gap-3 rounded-2xl bg-surface-raised p-4 shadow-sm has-[:checked]:ring-2 has-[:checked]:ring-brand-500 has-[:focus-visible]:ring-2 has-[:focus-visible]:ring-brand-500">
                <input type="radio" name="professional" value="any" checked class="text-brand-600 focus:ring-brand-500">
                <span class="font-medium">{{ __('Cualquiera disponible') }}</span>
            </label>

            @foreach ($professionals as $professional)
                <label class="flex cursor-pointer items-center gap-3 rounded-2xl bg-surface-raised p-4 shadow-sm has-[:checked]:ring-2 has-[:checked]:ring-brand-500 has-[:focus-visible]:ring-2 has-[:focus-visible]:ring-brand-500">
                    <input type="radio" name="professional" value="{{ $professional->id }}" class="text-brand-600 focus:ring-brand-500">
                    <span class="font-medium">{{ $professional->name }}</span>
                </label>
            @endforeach

            <x-button>{{ __('Continuar') }}</x-button>
        </form>
    @endif
</x-public-layout>
