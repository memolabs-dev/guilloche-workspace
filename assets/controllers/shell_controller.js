import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['sidebar', 'scrim', 'menu', 'searchDialog', 'searchInput', 'results', 'empty', 'main'];
    connect() {
        this.media = window.matchMedia('(min-width: 961px)');
        this.onWide = event => { if (event.matches) this.closeNav(); };
        this.media.addEventListener('change', this.onWide);
    }
    disconnect() { this.media.removeEventListener('change', this.onWide); }
    toggleNav() {
        const open = this.menuTarget.getAttribute('aria-expanded') !== 'true';
        this.sidebarTarget.classList.toggle('is-open', open);
        this.menuTarget.setAttribute('aria-expanded', String(open));
        this.scrimTarget.hidden = !open;
        this.mainTarget.inert = open;
        document.body.classList.toggle('nav-open', open);
        if (open) this.sidebarTarget.querySelector('a').focus();
    }
    closeNav() {
        if (!this.hasMenuTarget) return;
        const wasOpen = this.menuTarget.getAttribute('aria-expanded') === 'true';
        this.sidebarTarget.classList.remove('is-open');
        this.mainTarget.inert = false;
        document.body.classList.remove('nav-open');
        this.menuTarget.setAttribute('aria-expanded', 'false');
        this.scrimTarget.hidden = true;
        if (wasOpen && !this.media.matches) this.menuTarget.focus();
    }
    shortcut(event) {
        if (event.key === 'Tab' && this.menuTarget.getAttribute('aria-expanded') === 'true') {
            const items = [...this.sidebarTarget.querySelectorAll('a, button')].filter(item => item.getClientRects().length);
            const first = items[0], last = items.at(-1);
            if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
            else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
        }
        if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === 'k') {
            event.preventDefault(); this.openSearch();
        }
        if (event.key === 'Escape') {
            if (this.menuTarget.getAttribute('aria-expanded') === 'true') {
                this.closeNav(); this.menuTarget.focus();
            }
        }
    }
    openSearch() {
        this.searchInputTarget.value = '';
        this.filterSearch();
        if (!this.searchDialogTarget.open) this.searchDialogTarget.showModal();
        this.searchInputTarget.focus();
    }
    closeSearch() { this.searchDialogTarget.close(); }
    filterSearch() {
        const normalize = value => value.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
        const query = normalize(this.searchInputTarget.value.trim());
        let count = 0;
        this.resultsTarget.querySelectorAll('[data-search-item]').forEach(item => {
            item.hidden = !normalize(item.textContent).includes(query);
            if (!item.hidden) count++;
        });
        this.emptyTarget.hidden = count > 0;
    }
    backdrop(event) {
        if (event.target !== this.searchDialogTarget) return;
        const box = this.searchDialogTarget.getBoundingClientRect();
        if (event.clientX < box.left || event.clientX > box.right || event.clientY < box.top || event.clientY > box.bottom) this.closeSearch();
    }
    reset() { this.closeNav(); this.closeSearch(); }
}
