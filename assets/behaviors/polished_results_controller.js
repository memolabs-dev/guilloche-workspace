import ResultsController from './results_controller.js';

export default class extends ResultsController {
    static targets = ['skeleton', 'content'];
    start(event) {
        if (!this.isRegionRequest(event)) return;
        super.start(event);
        this.endLoading();
        if (!this.hasSkeletonTarget) return;
        this.loadingTimer = setTimeout(() => {
            this.skeletonTarget.hidden = false;
            this.contentTarget.classList.add('is-pending');
            if (this.contentTarget.contains(document.activeElement)) {
                this.feedbackTarget.tabIndex = -1;
                this.feedbackTarget.focus({ preventScroll: true });
            }
        }, 140);
    }
    endLoading() {
        clearTimeout(this.loadingTimer);
        if (this.hasSkeletonTarget) this.skeletonTarget.hidden = true;
        if (this.hasContentTarget) this.contentTarget.classList.remove('is-pending');
    }
    // Turbo remplace toute la région à chaque rendu. La fiche ne s'imprime (CSS .is-entering)
    // que si elle montre une autre mission ; sinon le rendu reste immobile, sans clignotement.
    beforeRender(event) {
        const current = this.frameTarget.querySelector('[data-selected]')?.dataset.selected;
        const next = event.detail.newFrame.querySelector('[data-selected]')?.dataset.selected;
        if (!next || next !== current) return;
        for (const element of event.detail.newFrame.querySelectorAll('.is-entering, .is-tracing')) {
            element.classList.remove('is-entering', 'is-tracing');
        }
    }
    loaded() {
        this.endLoading();
        super.loaded();
    }
    showError() { this.endLoading(); super.showError(); }
    reset() { this.endLoading(); super.reset(); }
    disconnect() { this.endLoading(); }
}
