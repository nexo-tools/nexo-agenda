{{-- Stamp <html data-theme> before first paint (no FOUC). Include in <head>
     BEFORE the stylesheet. Reads the shared `nexo-theme` cookie (scoped to the
     parent domain by the toggle in js/nexo-ui.js, so "dark in one tool = dark in
     all"), else the OS preference.

     STRICT CSP: this is an inline <script>. It is allow-listed by its exact
     sha256 hash in the CSP (App\Http\Middleware\SecurityHeaders + public/.htaccess,
     kept in sync) — no 'unsafe-inline' for scripts. Recompute the hash if you edit
     the snippet below. Current hash:
       sha256-QY4re+NFw+ChK0c8H/EaTpktoUisSWU0fL7V6J43umM=
     The snippet stays a single line so the hashed bytes are exactly the script
     body, with no surrounding whitespace. --}}
<script>(function(){try{var m=document.cookie.match(/(?:^|; )nexo-theme=([^;]+)/);var mode=(m&&m[1])||(matchMedia('(prefers-color-scheme: dark)').matches?'dark':'light');document.documentElement.setAttribute('data-theme',mode);}catch(e){}})();</script>
