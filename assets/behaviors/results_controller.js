import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = { loadingMessage: { type: String, default: 'Chargement…' } };
    static targets = ['frame', 'feedback', 'error', 'search', 'clear', 'focus', 'summary'];

    connect() {
        this.handledNetworkErrors = new WeakSet();
        this.reset();
    }
    clearTargetConnected(target) { target.hidden = false; }
    clear() {
        this.searchTarget.value = '';
        this.searchTarget.focus();
    }
    isRegionRequest(event) {
        return event.target === this.frameTarget ||
            (event.target instanceof HTMLFormElement && event.target.closest('turbo-frame') === this.frameTarget);
    }
    start(event) {
        if (!this.isRegionRequest(event)) return;
        this.pendingUrl = event.detail.url.toString();
        this.errorTarget.hidden = true;
        this.feedbackTarget.textContent = this.loadingMessageValue;
    }
    response(event) {
        if (!this.isRegionRequest(event)) return;
        if (!event.detail.fetchResponse.succeeded) {
            event.preventDefault();
            this.showError();
        }
    }
    failure(event) {
        if (!this.isRegionRequest(event)) return;
        // Turbo 8 rethrows the same fetch error after this cancellable event.
        this.handledNetworkErrors.add(event.detail.error);
        event.preventDefault();
        this.showError();
    }
    handledRejection(event) {
        if (this.handledNetworkErrors.has(event.reason)) event.preventDefault();
    }
    missing(event) {
        event.preventDefault();
        this.showError();
    }
    showError() {
        this.feedbackTarget.textContent = '';
        this.errorTarget.hidden = false;
    }
    retry() { window.location.assign(this.pendingUrl || window.location.href); }
    loaded() {
        this.reset();
        const target = this.hasFocusTarget ? this.focusTarget : this.summaryTarget;
        target.focus({ preventScroll: true });
    }
    reset() {
        this.feedbackTarget.textContent = '';
        this.errorTarget.hidden = true;
    }
}
