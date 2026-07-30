// Front-desk live refresh.
//
// This screen used <meta http-equiv="refresh" content="60">: a full reload every
// minute, unstoppable, on a surface whose buttons mark a person as attended or
// no-show. The reload could land mid-tap, and it reset focus and scroll every
// time — WCAG 2.2.1/2.2.4 ask for automatic updates to be controllable.
//
// So: fetch the same URL, swap the appointment list only, and never swap while
// somebody is interacting with it. The receptionist can pause it outright.
document.addEventListener('alpine:init', () => {
    window.Alpine.data('frontdeskRefresh', (seconds = 60) => ({
        paused: false,
        timer: null,

        init() {
            this.timer = setInterval(() => this.tick(), seconds * 1000);
        },

        destroy() {
            clearInterval(this.timer);
        },

        async tick() {
            if (this.paused || document.hidden) {
                return;
            }

            // Somebody has a button focused: swapping now would drop the
            // keyboard where it stood and could steal the tap in flight.
            if (document.activeElement?.closest('#mostrador')) {
                return;
            }

            try {
                const response = await fetch(window.location.href, { headers: { Accept: 'text/html' } });

                if (! response.ok) {
                    return;
                }

                const fresh = new DOMParser().parseFromString(await response.text(), 'text/html');

                for (const id of ['#mostrador', '#reloj']) {
                    const next = fresh.querySelector(id);
                    const current = document.querySelector(id);

                    if (next && current) {
                        current.replaceWith(next);
                    }
                }
            } catch {
                // Offline or a blip: keep the screen as it is and try again next tick.
            }
        },
    }));
});
