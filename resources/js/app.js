import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.data('appShell', () => ({
    navOpen: false,
    activePalette: 0,
    paletteNames: [
        'Red',
        'Orange',
        'Amber',
        'Yellow',
        'Lime',
        'Green',
        'Emerald',
        'Teal',
        'Cyan',
        'Sky',
        'Blue',
        'Indigo',
        'Violet',
        'Purple',
        'Fuchsia',
        'Pink',
        'Rose',
        'Slate',
        'Gray',
        'Zinc',
        'Neutral',
        'Stone',
        'Taupe',
        'Mauve',
        'Mist',
        'Olive',
    ],
    init() {
        this.paletteTimer = setInterval(() => {
            this.activePalette = (this.activePalette + 1) % this.paletteNames.length;
        }, 2200);
    },
    setActive(index) {
        this.activePalette = index;
    },
    closeNav() {
        this.navOpen = false;
    },
}));

Alpine.data('pollingUnitComposer', () => ({
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

Alpine.start();
