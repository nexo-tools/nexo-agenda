{{-- Standard ecosystem header. Composes the wordmark, an optional nav slot, and
     the shared actions (app-switcher, locale, theme, plus an account slot).
       <x-nexo-header brand="Nexo Tools" mark="/ecosystem/nexotools.svg">
           <x-slot:nav> ...links... </x-slot:nav>
           <x-slot:actions> ...account menu... </x-slot:actions>
       </x-nexo-header>
--}}
@props([
    'brand' => 'Nexo',
    'mark' => '/favicon.svg',
    'home' => '/',
])

<header {{ $attributes->merge(['class' => 'nexo-header']) }}>
    <x-nexo-wordmark :href="$home" :label="$brand" :mark="$mark" />

    @isset($nav)
        <nav class="nexo-header__nav" aria-label="{{ __('nexo.nav.primary') }}">{{ $nav }}</nav>
    @endisset

    <div class="nexo-header__spacer"></div>

    <div class="nexo-header__actions">
        <x-nexo-app-switcher />
        <x-nexo-locale-switcher />
        <x-nexo-theme-toggle />
        @isset($actions){{ $actions }}@endisset
    </div>
</header>
