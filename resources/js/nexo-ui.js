// Alpine components for the Nexo shared chrome. Import this once in the tool's
// app.js (before Alpine.start()); it registers on `alpine:init`.
//
//   import './nexo-ui.js';   // then Alpine.start()
//
// The head theme-init snippet (partials/theme-init.blade.php) must run first so
// <html data-theme> is always stamped before Alpine reads it.

document.addEventListener('alpine:init', () => {
  // Light/dark toggle. Explicit choice persists in localStorage and wins over
  // the OS preference (tokens.css keys dark off [data-theme] and prefers-*).
  Alpine.data('nexoTheme', () => ({
    dark: document.documentElement.getAttribute('data-theme') === 'dark',
    toggle() {
      this.dark = !this.dark;
      const mode = this.dark ? 'dark' : 'light';
      document.documentElement.setAttribute('data-theme', mode);
      try { localStorage.setItem('nexo-theme', mode); } catch (e) { /* private mode */ }
    },
  }));

  // Generic dropdown (app-switcher, locale, account). Closes on outside click
  // and Escape; the trigger owns aria-expanded.
  Alpine.data('nexoMenu', () => ({
    open: false,
    toggle() { this.open = !this.open; },
    close() { this.open = false; },
  }));
});
