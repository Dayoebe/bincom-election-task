import './bootstrap';

window.addEventListener('load', () => {
    if (import.meta.env.PROD && 'serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js').catch(() => {});
    }
}, { once: true });

document.addEventListener('alpine:init', () => {
    window.Alpine.data('appShell', () => ({
        deferredInstallPrompt: null,
        canInstall: false,
        init() {
            const standalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;

            if (! standalone) {
                window.addEventListener('beforeinstallprompt', (event) => {
                    event.preventDefault();
                    this.deferredInstallPrompt = event;
                    this.canInstall = true;
                });
            }

            window.addEventListener('appinstalled', () => {
                this.deferredInstallPrompt = null;
                this.canInstall = false;
            });
        },
        async install() {
            if (! this.deferredInstallPrompt) {
                return;
            }

            this.deferredInstallPrompt.prompt();
            await this.deferredInstallPrompt.userChoice;

            this.deferredInstallPrompt = null;
            this.canInstall = false;
        },
    }));

    window.Alpine.data('pollingUnitComposer', () => ({
        showMetadata: true,
        showPartyGrid: true,
        partyTotal: 0,
        partyCount: 0,
        recalc() {
            const fields = Array.from(this.$root.querySelectorAll('[data-party-score]'));

            this.partyCount = fields.length;
            this.partyTotal = fields.reduce((sum, field) => {
                const value = Number.parseInt(field.value || '0', 10);

                return sum + (Number.isNaN(value) ? 0 : value);
            }, 0);
        },
    }));
});
