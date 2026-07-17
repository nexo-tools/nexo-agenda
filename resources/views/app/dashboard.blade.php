<x-app-layout>
    <x-slot:title>{{ __('Agenda') }}</x-slot:title>

    <h1 class="text-2xl font-bold">{{ $business->name }}</h1>
    <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
        {{ __('Tu página pública') }}:
        <a href="{{ url('/'.$business->slug) }}" class="font-medium text-brand-700 hover:underline dark:text-brand-400">
            {{ url('/'.$business->slug) }}
        </a>
    </p>

    <div class="mt-8 rounded-2xl border border-dashed border-slate-300 p-8 text-center text-slate-500 dark:border-slate-700">
        {{ __('Aquí vivirá tu agenda. Próximo paso: crea tus servicios.') }}
    </div>
</x-app-layout>
