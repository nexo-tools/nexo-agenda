{{-- Standard ecosystem header. Composes the wordmark, the help link, an optional
     nav slot, and the shared actions (app-switcher, locale, theme, plus an
     account slot).
       <x-nexo-header brand="Nexo Tools" mark="/ecosystem/nexotools.svg">
           <x-slot:nav> ...links... </x-slot:nav>
           <x-slot:actions> ...account menu... </x-slot:actions>
       </x-nexo-header>

     The help link is baked in (2026-08-02): before, each tool placed it wherever
     — ghost button, nav link or nowhere — and the drift was visible crossing
     tools. It renders as a plain text link, first in the nav, on every surface
     that shows this header; the footer keeps its own copy for mobile, where
     .nexo-header__nav is hidden. --}}
@props([
    'brand' => 'Nexo',
    'mark' => '/favicon.svg',
    'home' => '/',
])

<header {{ $attributes->merge(['class' => 'nexo-header']) }}>
    <x-nexo-wordmark :href="$home" :label="$brand" :mark="$mark" />

    <nav class="nexo-header__nav" aria-label="{{ __('nexo.nav.primary') }}">
        <a href="{{ route('help') }}" @if (request()->routeIs('help')) aria-current="page" @endif class="rounded-md px-2 py-1 text-sm font-medium hover:bg-bg-subtle {{ request()->routeIs('help') ? 'text-ink' : 'text-muted' }}">{{ __('nexo.help.title') }}</a>
        @isset($nav){{ $nav }}@endisset
    </nav>

    <div class="nexo-header__spacer"></div>

    <div class="nexo-header__actions">
        <x-nexo-app-switcher />
        <x-nexo-locale-switcher />
        <x-nexo-theme-toggle />
        @isset($actions){{ $actions }}@endisset
    </div>
</header>
