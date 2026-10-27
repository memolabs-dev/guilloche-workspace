import { Controller } from '@hotwired/stimulus';

const KEY = 'guilloche-theme';

/* Thème clair/sombre. Le thème initial est posé dans <head> pour éviter un flash ;
   ce contrôleur synchronise le bouton et révèle le nouveau thème depuis le bouton. */
export default class extends Controller {
    static targets = ['button'];

    buttonTargetConnected() { this.sync(); }

    toggle(event) {
        const next = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
        const apply = () => {
            document.documentElement.dataset.theme = next;
            this.sync();
        };
        try { localStorage.setItem(KEY, next); } catch { /* Persistance facultative. */ }

        const reduced = matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (!document.startViewTransition || reduced) return apply();

        const box = event.currentTarget.getBoundingClientRect();
        const x = box.left + box.width / 2;
        const y = box.top + box.height / 2;
        const radius = Math.hypot(Math.max(x, innerWidth - x), Math.max(y, innerHeight - y));
        document.documentElement.classList.add('is-theme-reveal');
        const transition = document.startViewTransition(apply);
        transition.ready.then(() => {
            document.documentElement.animate(
                { clipPath: [`circle(0 at ${x}px ${y}px)`, `circle(${radius}px at ${x}px ${y}px)`] },
                { duration: 520, easing: 'cubic-bezier(.16,1,.3,1)', pseudoElement: '::view-transition-new(root)' },
            );
        }).catch(() => {});
        transition.finished.finally(() => document.documentElement.classList.remove('is-theme-reveal'));
    }

    sync() {
        const dark = document.documentElement.dataset.theme === 'dark';
        this.buttonTarget.setAttribute('aria-pressed', String(dark));
    }
}
