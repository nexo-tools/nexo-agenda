<x-app-layout>
    <x-slot:title>{{ __('Ajustes') }}</x-slot:title>

    <h1 class="text-2xl font-bold">{{ __('Ajustes del negocio') }}</h1>
    <p class="mt-1 text-sm text-muted">
        {{ __('Tu página pública') }}:
        <a href="{{ route('public.business', $business) }}" class="font-medium text-brand-700 hover:underline dark:text-brand-400">
            {{ url('/'.$business->slug) }}
        </a>
    </p>

    <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data" class="mt-6 max-w-lg space-y-4">
        @csrf
        @method('PUT')

        <x-field :label="__('Nombre del negocio')" name="name" :value="$business->name" required />
        <x-select :label="__('Rubro')" name="category" :selected="$business->category"
                  :options="collect(config('nexo.categories'))->mapWithKeys(fn ($c) => [$c => __('nexo.categories.'.$c)])" />
        <x-field :label="__('Ciudad')" name="city" :value="$business->city" required />
        <x-field :label="__('Dirección (opcional)')" name="address" :value="$business->address" />
        <x-field :label="__('WhatsApp (opcional)')" name="whatsapp_phone" type="tel" :value="$business->whatsapp_phone" />

        <div>
            <label for="description" class="mb-1 block text-sm font-medium">{{ __('Descripción (opcional)') }}</label>
            <textarea id="description" name="description" rows="3" maxlength="500"
                      class="w-full rounded-lg border-control bg-surface text-ink shadow-sm focus:border-brand-500 focus:ring-brand-500">{{ old('description', $business->description) }}</textarea>
            @error('description')
                <p class="mt-1 text-sm text-danger">{{ $message }}</p>
            @enderror
        </div>

        <fieldset class="rounded-2xl border border-line p-4">
            <legend class="px-1 text-sm font-semibold">{{ __('Tu marca en la página pública') }}</legend>

            <div class="flex items-center gap-3">
                <div>
                    <label for="brand_color" class="mb-1 block text-sm font-medium">{{ __('Color de acento') }}</label>
                    <input type="color" id="brand_color" name="brand_color"
                           value="{{ old('brand_color', $business->brand_color ?? '#0f766e') }}"
                           class="h-10 w-16 cursor-pointer rounded border-control">
                    @error('brand_color')
                        <p class="mt-1 text-sm text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex-1">
                    <label for="logo" class="mb-1 block text-sm font-medium">{{ __('Logo (opcional, máx. 1 MB)') }}</label>
                    <input type="file" id="logo" name="logo" accept="image/png,image/jpeg,image/webp,image/svg+xml"
                           class="block w-full text-sm text-muted file:mr-3 file:rounded-lg file:border-0 file:bg-primary-subtle file:px-3 file:py-2 file:text-sm file:font-medium file:text-primary-subtle-fg">
                    @error('logo')
                        <p class="mt-1 text-sm text-danger">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            @if ($business->logo_path)
                <div class="mt-3 flex items-center gap-3">
                    <img src="{{ Storage::url($business->logo_path) }}" alt="{{ __('Logo actual') }}" class="h-12 w-12 rounded-lg object-contain">
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="remove_logo" value="1" class="rounded border-control text-brand-600 focus:ring-brand-500">
                        {{ __('Quitar logo') }}
                    </label>
                </div>
            @endif
        </fieldset>

        <label class="flex items-start gap-2 text-sm">
            <input type="hidden" name="in_directory" value="0">
            <input type="checkbox" name="in_directory" value="1" @checked(old('in_directory', $business->in_directory))
                   class="mt-0.5 rounded border-control text-brand-600 focus:ring-brand-500">
            <span>
                {{ __('Aparecer en el directorio público') }}
                <span class="block text-xs text-muted">
                    {{ __('Tu negocio se podrá encontrar en /explorar buscando por rubro y ciudad. Gratis, sin comisiones.') }}
                </span>
            </span>
        </label>

        <x-button>{{ __('Guardar ajustes') }}</x-button>
    </form>
</x-app-layout>
