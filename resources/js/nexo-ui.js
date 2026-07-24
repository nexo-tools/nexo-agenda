// Alpine components for the Nexo shared chrome. Import this once in the tool's
// app.js (before Alpine.start()); it registers on `alpine:init`.
//
//   import './nexo-ui.js';   // then Alpine.start()
//
// The head theme-init snippet (partials/theme-init.blade.php) must run first so
// <html data-theme> is always stamped before Alpine reads it.

document.addEventListener('alpine:init', () => {
  // Light/dark toggle. The explicit choice persists in the `nexo-theme` cookie,
  // scoped to the parent domain so it crosses every ecosystem tool (dark in one =
  // dark in all), and wins over the OS preference (tokens.css keys dark off
  // [data-theme] and prefers-*). theme-init.blade.php reads that cookie on load.
  Alpine.data('nexoTheme', () => ({
    dark: document.documentElement.getAttribute('data-theme') === 'dark',
    toggle() {
      this.dark = !this.dark;
      const mode = this.dark ? 'dark' : 'light';
      document.documentElement.setAttribute('data-theme', mode);
      // On *.alvarocdev.com, scope the cookie to the parent domain; on localhost
      // the regex doesn't match, so it stays host-only and dev is unaffected.
      const domain = /(^|\.)alvarocdev\.com$/.test(location.hostname) ? '; domain=.alvarocdev.com' : '';
      try { document.cookie = 'nexo-theme=' + mode + '; path=/; max-age=31536000; SameSite=Lax' + domain; } catch (e) { /* private mode */ }
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
