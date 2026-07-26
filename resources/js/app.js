import Alpine from 'alpinejs';
import './nexo-ui.js'; // registers the shared chrome components on alpine:init
import './nexo-beacon.js'; // cookieless pageview ping (no-op unless the beacon metas are present)

window.Alpine = Alpine;
Alpine.start();
