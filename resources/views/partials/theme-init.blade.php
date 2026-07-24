{{-- Stamp <html data-theme> before first paint (no FOUC). Include in <head>
     BEFORE the stylesheet. Reads the persisted choice, else the OS preference.

     STRICT CSP: this is an inline <script>. If the tool ships a strict
     `script-src` (no 'unsafe-inline'), add this script's sha256 hash to the CSP
     (middleware + .htaccess, kept in sync) instead of weakening to 'unsafe-inline'.
     Recompute the hash if you edit the snippet. (nexo-tools does exactly this.) --}}
<script>
    (function () {
        try {
            var stored = localStorage.getItem('nexo-theme');
            var mode = stored || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.setAttribute('data-theme', mode);
        } catch (e) { /* private mode: fall back to CSS prefers-color-scheme */ }
    })();
</script>
