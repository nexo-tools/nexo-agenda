<x-public-layout :title="__('Centro de ayuda')">
    <x-slot:meta>
        <meta name="description" content="{{ __('Preguntas frecuentes sobre reservas, cancelaciones y cómo registrar tu negocio.') }}">
    </x-slot:meta>

    <header class="mb-6">
        <h1 class="text-2xl font-bold">{{ __('Centro de ayuda') }}</h1>
        <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
            {{ __('Encuentra respuestas rápidas o escríbenos.') }}
        </p>
    </header>

    @php
        $faqs = [
            [__('¿Necesito una cuenta para reservar?'), __('No. Reservas con tu nombre, email y teléfono; te enviamos un enlace para gestionar tu turno.')],
            [__('¿Cómo cancelo o reprogramo mi turno?'), __('Abre el enlace que te enviamos por email y usa los botones para cancelar o reprogramar, dentro del plazo que fija el negocio.')],
            [__('No me llegó el email de confirmación.'), __('Revisa las carpetas de spam o promociones. Si no aparece, contacta al negocio directamente.')],
            [__('¿Cómo registro mi negocio?'), __('Crea una cuenta gratis, agrega tus servicios y tu equipo, y comparte tu página pública de reservas.')],
            [__('¿Tiene costo?'), __('Nexo Agenda es open source y self-hosted: sin comisiones ni cuotas por cliente.')],
            [__('¿Cómo aparezco en el directorio?'), __('Actívalo desde los ajustes de tu negocio para aparecer en la página de exploración.')],
        ];
    @endphp

    <div class="space-y-3">
        @foreach ($faqs as [$question, $answer])
            <details class="rounded-2xl bg-white p-4 shadow-sm dark:bg-slate-800">
                <summary class="cursor-pointer font-semibold">{{ $question }}</summary>
                <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">{{ $answer }}</p>
            </details>
        @endforeach
    </div>

    <section class="mt-8 rounded-2xl bg-brand-50 p-5 text-center dark:bg-slate-800">
        <p class="font-semibold">{{ __('¿No encontraste lo que buscabas?') }}</p>
        <a href="{{ route('contact') }}"
           class="mt-3 inline-flex items-center justify-center rounded-lg bg-brand-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2">
            {{ __('Escríbenos') }}
        </a>
    </section>
</x-public-layout>
