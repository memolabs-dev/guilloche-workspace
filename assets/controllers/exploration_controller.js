import ResultsController from '../behaviors/results_controller.js';

export default class extends ResultsController {
    static targets = ['skeleton', 'content', 'preview', 'previewControls'];

    connect() {
        super.connect();
        this.previewControlsTarget.hidden = false;
        this.syncComparisons();
    }

    start(event) {
        if (!this.isRegionRequest(event)) return;
        super.start(event);
        this.endLoading();
        this.loadingTimer = setTimeout(() => {
            this.skeletonTarget.hidden = false;
            this.contentTarget.classList.add('is-loading');
            if (this.contentTarget.contains(document.activeElement)) {
                this.feedbackTarget.tabIndex = -1;
                this.feedbackTarget.focus({ preventScroll: true });
            }
        }, 140);
    }
    endLoading() {
        clearTimeout(this.loadingTimer);
        this.animation?.cancel();
        if (this.hasSkeletonTarget) this.skeletonTarget.hidden = true;
        if (this.hasContentTarget) this.contentTarget.classList.remove('is-loading');
    }
    syncComparisons() {
        const state = this.frameTarget.querySelector('.exp-workspace')?.dataset;
        if (!state) return;
        for (const link of document.querySelectorAll('[data-exploration-direction]')) {
            const url = new URL(link.href);
            url.search = '';
            for (const [key, value] of Object.entries({ q: state.query, status: state.status, selected: state.selected })) {
                if (value) url.searchParams.set(key, value);
            }
            link.href = url.toString();
        }
    }
    loaded() {
        this.endLoading();
        super.loaded();
        this.syncComparisons();
        if (this.hasFocusTarget && !matchMedia('(prefers-reduced-motion: reduce)').matches) {
            this.animation = this.focusTarget.closest('.exp-detail').animate(
                [{ opacity: 0.4, transform: 'translateY(4px)' }, { opacity: 1, transform: 'translateY(0)' }],
                { duration: 180, easing: 'cubic-bezier(0.16, 1, 0.3, 1)' }
            );
        }
    }
    showError() {
        this.endLoading();
        super.showError();
        this.errorTarget.querySelector('button')?.focus({ preventScroll: true });
    }
    reset() { this.endLoading(); super.reset(); }
    disconnect() { this.endLoading(); }
    preview(event) {
        const state = event.params.state;
        for (const child of this.previewTarget.children) child.hidden = child.dataset.state !== state;
        for (const button of this.element.querySelectorAll('[data-exploration-state-param]')) {
            button.setAttribute('aria-pressed', String(button === event.currentTarget));
        }
    }
}
