// Nexo beacon emitter — cookieless, opt-in pageview ping for the ecosystem.
//
// Shareable nexo-ui asset: copy this file verbatim into any Nexo tool (or
// alvarocdev) that opts in, and import it once from the tool's app.js. It is a
// no-op unless the page renders the beacon meta tags — which a tool only outputs
// when its own beacon is enabled — and it ALWAYS respects Do Not Track / Global
// Privacy Control. CSP-safe: no inline script, it ships inside the app bundle.
//
// Blade wiring (see resources/views/partials/beacon.blade.php):
//   <meta name="nexo:beacon-endpoint" content="https://nexotools.alvarocdev.com/beacon">
//   <meta name="nexo:beacon-origin" content="nexolinks">
//   <meta name="nexo:beacon-ref" content="nexoshort">   {{-- optional, alvarocdev attribution --}}

(function () {
    function meta(name) {
        const el = document.querySelector(`meta[name="${name}"]`);
        return el ? el.getAttribute('content') : null;
    }

    function send() {
        // Respect Do Not Track / Global Privacy Control before anything else.
        const dnt = navigator.doNotTrack || window.doNotTrack || navigator.msDoNotTrack;
        if (dnt === '1' || dnt === 'yes' || navigator.globalPrivacyControl === true) return;

        const endpoint = meta('nexo:beacon-endpoint');
        const origin = meta('nexo:beacon-origin');
        // No metas => this instance did not opt in; do nothing.
        if (!endpoint || !origin || typeof navigator.sendBeacon !== 'function') return;

        const payload = { origin, path: location.pathname, event: 'pageview' };
        const ref = meta('nexo:beacon-ref');
        if (ref) payload.ref = ref;

        try {
            navigator.sendBeacon(endpoint, new Blob([JSON.stringify(payload)], { type: 'application/json' }));
        } catch (e) {
            /* analytics must never break the page */
        }
    }

    if (document.readyState === 'complete') {
        send();
    } else {
        window.addEventListener('load', send, { once: true });
    }
})();
