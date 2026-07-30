<nav {{ $attributes->class(['flex items-center gap-1 text-xs']) }} aria-label="{{ __('Idioma') }}">
    @foreach (['es' => 'Español', 'en' => 'English', 'pt' => 'Português'] as $locale => $label)
        <a href="{{ request()->fullUrlWithQuery(['lang' => $locale]) }}"
           @class([
               'rounded px-2 py-1 uppercase',
               'bg-brand-100 font-semibold text-brand-900 dark:bg-brand-900 dark:text-brand-100' => app()->getLocale() === $locale,
               'text-muted hover:text-ink' => app()->getLocale() !== $locale,
           ])
           lang="{{ $locale }}" hreflang="{{ $locale }}" aria-label="{{ $label }}"
           @if (app()->getLocale() === $locale) aria-current="true" @endif>
            {{ $locale }}
        </a>
    @endforeach
</nav>
