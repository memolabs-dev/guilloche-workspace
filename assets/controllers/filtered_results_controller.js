import { Controller } from '@hotwired/stimulus';

const SKELETON_DELAY = 140;

/*
 * Région de résultats filtrés servie par une Turbo Frame (missions, documents).
 * Annonce le chargement, montre un skeleton seulement si la requête dépasse 140 ms,
 * garde la vue précédente en cas d'erreur et replace le focus après le rendu.
 * Sans JavaScript, le formulaire GET et les liens rechargent simplement la page.
 */
export default class extends Controller {
    static values = { loadingMessage: { type: String, default: 'Chargement…' } };
    static targets = ['frame', 'feedback', 'error', 'search', 'clear', 'summary', 'focus', 'skeleton', 'content'];

    connect() {
        this.handledErrors = new WeakSet();
        this.reset();
    }

    disconnect() {
        this.stopLoading();
    }

    // Le bouton Effacer n'existe qu'avec JavaScript et seulement quand il y a quelque chose à effacer.
    clearTargetConnected() {
        this.toggleClear();
    }

    toggleClear() {
        if (this.hasClearTarget && this.hasSearchTarget) this.clearTarget.hidden = this.searchTarget.value === '';
    }

    clear() {
        this.searchTarget.value = '';
        this.toggleClear();
        this.searchTarget.focus();
    }

    start(event) {
        if (!this.ownsRequest(event)) return;
        this.pendingUrl = event.detail.url.toString();
        this.errorTarget.hidden = true;
        this.feedbackTarget.textContent = this.loadingMessageValue;
        this.stopLoading();
        this.skeletonTimer = setTimeout(() => this.showSkeleton(), SKELETON_DELAY);
    }

    response(event) {
        if (!this.ownsRequest(event) || event.detail.fetchResponse.succeeded) return;
        event.preventDefault();
        this.showError();
    }

    failure(event) {
        if (!this.ownsRequest(event)) return;
        // Turbo relance la même erreur réseau après cet événement annulable : on la marque comme traitée.
        this.handledErrors.add(event.detail.error);
        event.preventDefault();
        this.showError();
    }

    missing(event) {
        event.preventDefault();
        this.showError();
    }

    handledRejection(event) {
        if (this.handledErrors.has(event.reason)) event.preventDefault();
    }

    /* Turbo remplace toute la région : la fiche ne s'imprime de nouveau que si elle montre une autre mission. */
    beforeRender(event) {
        const current = this.frameTarget.querySelector('[data-selected]')?.dataset.selected;
        const next = event.detail.newFrame.querySelector('[data-selected]')?.dataset.selected;
        if (!next || next !== current) return;
        for (const element of event.detail.newFrame.querySelectorAll('.is-entering, .is-tracing')) {
            element.classList.remove('is-entering', 'is-tracing');
        }
    }

    loaded() {
        this.reset();
        (this.hasFocusTarget ? this.focusTarget : this.summaryTarget).focus({ preventScroll: true });
    }

    retry() {
        window.location.assign(this.pendingUrl || window.location.href);
    }

    reset() {
        this.stopLoading();
        this.feedbackTarget.textContent = '';
        this.errorTarget.hidden = true;
    }

    showSkeleton() {
        if (!this.hasSkeletonTarget) return;
        this.skeletonTarget.hidden = false;
        this.contentTarget.classList.add('is-pending');
        // Le skeleton recouvre le lien qui a lancé la requête : le focus passe sur l'annonce.
        if (this.contentTarget.contains(document.activeElement)) {
            this.feedbackTarget.tabIndex = -1;
            this.feedbackTarget.focus({ preventScroll: true });
        }
    }

    showError() {
        this.stopLoading();
        this.feedbackTarget.textContent = '';
        this.errorTarget.hidden = false;
    }

    stopLoading() {
        clearTimeout(this.skeletonTimer);
        if (this.hasSkeletonTarget) this.skeletonTarget.hidden = true;
        if (this.hasContentTarget) this.contentTarget.classList.remove('is-pending');
    }

    ownsRequest(event) {
        const { target } = event;
        return target === this.frameTarget || (target instanceof HTMLFormElement && target.closest('turbo-frame') === this.frameTarget);
    }
}
