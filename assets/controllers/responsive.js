import { Controller } from '@hotwired/stimulus'

/* stimulusFetch: 'eager' */
export default class extends Controller {
    static targets = ['toggleButton', 'collapsibleRow']

    static values = {
        phoneMax: { type: Number, default: 767 },
        tabletMax: { type: Number, default: 1023 },
        parameterName: { type: String, default: '_device' },
        currentDevice: { type: String, default: '' },
    }

    #resizeTimeout = null
    #boundResize = null

    connect() {
        this.#detectAndUpdate();

        this.#boundResize = this.#onResize.bind(this);
        window.addEventListener('resize', this.#boundResize);
    }

    disconnect() {
        window.removeEventListener('resize', this.#boundResize);

        if (this.#resizeTimeout) {
            clearTimeout(this.#resizeTimeout);
        }
    }

    toggle(event) {
        const button = event.currentTarget;
        const index = button.dataset.rowIndex;
        const row = this.collapsibleRowTargets.find(r => r.dataset.rowIndex === index);

        if (!row) {
            return;
        }

        row.hidden = !row.hidden;
        button.setAttribute('aria-expanded', String(!row.hidden));
        button.querySelector('.kreyu-dt-toggle-icon').textContent = row.hidden ? '+' : '\u2212';
    }

    #onResize() {
        if (this.#resizeTimeout) {
            clearTimeout(this.#resizeTimeout);
        }

        this.#resizeTimeout = setTimeout(() => this.#detectAndUpdate(), 250);
    }

    #detectAndUpdate() {
        const device = this.#detectDevice();

        if (device === this.currentDeviceValue) {
            return;
        }

        this.currentDeviceValue = device;
        this.#reloadFrame(device);
    }

    #detectDevice() {
        const width = window.innerWidth;

        if (width <= this.phoneMaxValue) {
            return 'phone';
        }

        if (width <= this.tabletMaxValue) {
            return 'tablet';
        }

        return 'desktop';
    }

    #reloadFrame(device) {
        const frame = this.element.closest('turbo-frame');

        if (!frame) {
            return;
        }

        const url = new URL(window.location.href);
        url.searchParams.set(this.parameterNameValue, device);

        frame.src = url.toString();
    }
}
