<x-public-layout
    :title="$category ? __('nexo.categories.'.$category).' — '.__('Explorar') : __('Explorar negocios')"
    :description="$category
        ? __('Encuentra dónde reservar en :category', ['category' => __('nexo.categories.'.$category)])
        : __('Encuentra dónde reservar tu próximo turno')">
    <header class="mb-5">
        <a href="{{ route('home') }}" class="flex items-center gap-2 text-sm font-semibold">
            <img src="/favicon.svg" alt="" width="24" height="24"> {{ config('app.name') }}
        </a>
        <h1 class="mt-3 text-2xl font-bold">
            {{ $category ? __('nexo.categories.'.$category) : __('Explorar negocios') }}
        </h1>
        <p class="text-sm text-muted">{{ __('Encuentra dónde reservar tu próximo turno') }}</p>
    </header>

    <form method="GET" action="{{ route('directory') }}" x-data class="mb-6 space-y-2">
        <label for="q" class="sr-only">{{ __('Buscar') }}</label>
        <input id="q" type="search" name="q" value="{{ $search }}" placeholder="{{ __('Buscar por nombre…') }}"
               class="w-full rounded-lg border-control bg-surface-raised text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500">

        <div class="flex gap-2">
            <label for="categoria" class="sr-only">{{ __('Rubro') }}</label>
            <select id="categoria" name="categoria" x-on:change="$el.form.requestSubmit()"
                    class="flex-1 rounded-lg border-control bg-surface-raised text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500">
                <option value="">{{ __('Todos los rubros') }}</option>
                @foreach (config('nexo.categories') as $option)
                    <option value="{{ $option }}" @selected($category === $option)>{{ __('nexo.categories.'.$option) }}</option>
                @endforeach
            </select>

            <label for="ciudad" class="sr-only">{{ __('Ciudad') }}</label>
            <select id="ciudad" name="ciudad" x-on:change="$el.form.requestSubmit()"
                    class="flex-1 rounded-lg border-control bg-surface-raised text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500">
                <option value="">{{ __('Todas las ciudades') }}</option>
                @foreach ($cities as $option)
                    <option value="{{ $option }}" @selected($city === $option)>{{ $option }}</option>
                @endforeach
            </select>
        </div>
        <noscript><button class="rounded-lg bg-brand-700 px-4 py-2 text-sm font-semibold text-brand-fg">{{ __('Filtrar') }}</button></noscript>
    </form>

    @if ($businesses->isEmpty())
        <p class="rounded-2xl border border-dashed border-line-strong p-8 text-center text-sm text-muted">
            {{ __('No encontramos negocios con esos filtros.') }}
        </p>
    @else
        <ul class="space-y-3">
            @foreach ($businesses as $business)
                <li>
                    <a href="{{ route('public.business', $business) }}"
                       class="flex items-center gap-3 rounded-2xl bg-surface-raised p-4 shadow-sm hover:ring-2 hover:ring-brand-500">
                        @if ($business->logo_path)
                            <img src="{{ Storage::url($business->logo_path) }}" alt="" class="h-12 w-12 rounded-xl object-contain">
                        @else
                            <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-100 text-lg font-bold text-brand-900 dark:bg-brand-900 dark:text-brand-100">
                                {{ mb_substr($business->name, 0, 1) }}
                            </span>
                        @endif
                        <span class="min-w-0 flex-1">
                            <span class="block truncate font-semibold">{{ $business->name }}</span>
                            <span class="block text-sm text-muted">
                                {{ __('nexo.categories.'.$business->category) }} · {{ $business->city }}
                            </span>
                        </span>
                        @if ($business->rating_count > 0)
                            <span class="text-sm">
                                <span aria-hidden="true" class="text-amber-500">★</span>
                                {{ number_format((float) $business->rating, 1, ',') }}
                                <span class="text-muted">({{ $business->rating_count }})</span>
                            </span>
                        @endif
                    </a>
                </li>
            @endforeach
        </ul>

        <div class="mt-6">{{ $businesses->links() }}</div>
    @endif
</x-public-layout>
