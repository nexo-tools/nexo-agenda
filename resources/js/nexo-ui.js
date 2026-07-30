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
  //
  // The panels declare role="menu"/"menuitem", and that role is a promise: arrow
  // navigation, Home/End, and focus returning to the trigger on close. Without
  // it a screen-reader user is told to press arrows and nothing happens.
  // Wire it up with:  x-data="nexoMenu" @keydown="onKeydown($event)"
  Alpine.data('nexoMenu', () => ({
    open: false,
    toggle() {
      this.open = !this.open;
      if (this.open) this.$nextTick(() => this.focusItem(0));
    },
    close({ restoreFocus = true } = {}) {
      if (!this.open) return;
      this.open = false;
      if (restoreFocus) this.$el.querySelector('[aria-haspopup]')?.focus();
    },
    items() {
      return Array.from(this.$el.querySelectorAll('[role="menuitem"]'))
        .filter((el) => !el.hasAttribute('disabled') && el.offsetParent !== null);
    },
    focusItem(index) {
      const items = this.items();
      if (!items.length) return;
      // Wrap around: past the end returns to the first item, and vice versa.
      items[(index + items.length) % items.length].focus();
    },
    moveFocus(step) {
      const items = this.items();
      const current = items.indexOf(document.activeElement);
      this.focusItem(current === -1 ? (step > 0 ? 0 : items.length - 1) : current + step);
    },
    onKeydown(event) {
      if (event.key === 'Escape') { this.close(); return; }
      if (!this.open) return;
      const handlers = {
        ArrowDown: () => this.moveFocus(1),
        ArrowUp: () => this.moveFocus(-1),
        Home: () => this.focusItem(0),
        End: () => this.focusItem(this.items().length - 1),
        // Tabbing out of a menu closes it, but the browser keeps the focus move.
        Tab: () => this.close({ restoreFocus: false }),
      };
      const handler = handlers[event.key];
      if (!handler) return;
      if (event.key !== 'Tab') event.preventDefault();
      handler();
    },
  }));
});
