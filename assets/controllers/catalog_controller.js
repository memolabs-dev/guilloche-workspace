import { Controller } from '@hotwired/stimulus';

/* Démonstrations du catalogue : toast, aperçu en dialogue, états d'une région
   et laboratoire de tracé. Aucune donnée n'est enregistrée. */
export default class extends Controller {
    static targets = ['toast', 'dialog', 'stateCanvas', 'rosette', 'output', 'lab'];

    notify(event) {
        clearTimeout(this.toastTimer);
        this.toastTarget.textContent = event.params.message || 'Action reçue. Aucune donnée enregistrée.';
        this.toastTarget.classList.add('is-visible');
        this.toastTimer = setTimeout(() => this.toastTarget.classList.remove('is-visible'), 2600);
    }

    openDialog() {
        // La fermeture (Échap, bouton method=dialog) et le retour du focus sont natifs.
        if (!this.dialogTarget.open) this.dialogTarget.showModal();
    }

    state(event) {
        for (const panel of this.stateCanvasTarget.children) panel.hidden = panel.dataset.state !== event.params.state;
        for (const button of this.element.querySelectorAll('[data-catalog-state-param]')) {
            button.setAttribute('aria-pressed', String(button === event.currentTarget));
        }
    }

    progress(event) {
        const value = Number(event.target.value);
        this.rosetteTarget.style.setProperty('--p', value);
        this.rosetteTarget.classList.toggle('is-complete', value >= 100);
        this.outputTarget.querySelector('strong').textContent = value;
    }

    replay() {
        const path = this.rosetteTarget.querySelector('.ui-guilloche__trace');
        path.style.animation = 'none';
        path.getBoundingClientRect();
        path.style.animation = '';
    }

    reduceMotion(event) {
        this.element.dataset.reduced = String(event.target.checked);
    }

    beforeCache() {
        clearTimeout(this.toastTimer);
        this.toastTarget.classList.remove('is-visible');
        this.toastTarget.textContent = '';
        if (this.dialogTarget.open) this.dialogTarget.close();
    }

    disconnect() {
        clearTimeout(this.toastTimer);
    }
}
