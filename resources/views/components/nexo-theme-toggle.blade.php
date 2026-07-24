{{-- Light/dark toggle. Needs nexo-ui.js (Alpine) + partials/theme-init in head.
     i18n keys: nexo.theme.light / nexo.theme.dark (add to lang files). --}}
<button
    type="button"
    class="nexo-btn nexo-btn--ghost nexo-btn--icon"
    x-data="nexoTheme"
    @click="toggle()"
    :aria-label="dark ? @js(__('nexo.theme.light')) : @js(__('nexo.theme.dark'))"
>
    {{-- Sun (shown in dark mode: click to go light) --}}
    <svg x-show="dark" x-cloak width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <circle cx="12" cy="12" r="4" />
        <path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4" />
    </svg>
    {{-- Moon (shown in light mode: click to go dark) --}}
    <svg x-show="!dark" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z" />
    </svg>
</button>
