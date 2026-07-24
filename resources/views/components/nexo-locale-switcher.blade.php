{{-- Locale switcher (es/en/pt). Default hrefs use ?lang=<code>; tools whose
     locale routing differs pass a prepared :locales map (code => label) and set
     the href convention when applying. Needs nexo-ui.js (`nexoMenu`).
     i18n: nexo.locale.label --}}
@props([
    'locales' => ['es' => 'Español', 'en' => 'English', 'pt' => 'Português'],
    'current' => null,
])
@php $current = $current ?? app()->getLocale(); @endphp

<div class="nexo-menu" x-data="nexoMenu" @keydown.escape="close()" @click.outside="close()">
    <button
        type="button"
        class="nexo-btn nexo-btn--ghost"
        @click="toggle()"
        :aria-expanded="open"
        aria-haspopup="true"
        aria-label="{{ __('nexo.locale.label') }}"
    >
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <circle cx="12" cy="12" r="9" /><path d="M3 12h18M12 3a15 15 0 0 1 0 18M12 3a15 15 0 0 0 0 18" />
        </svg>
        <span>{{ strtoupper($current) }}</span>
    </button>

    <div class="nexo-menu__panel" x-show="open" x-cloak x-transition role="menu">
        @foreach ($locales as $code => $label)
            <a
                href="{{ request()->fullUrlWithQuery(['lang' => $code]) }}"
                class="nexo-menu__item {{ $code === $current ? 'nexo-menu__item--current' : '' }}"
                role="menuitem"
                @if ($code === $current) aria-current="true" @endif
            >{{ $label }}</a>
        @endforeach
    </div>
</div>
