// Turbo 8.0 attend `transition.finished` mais n'observe pas `transition.ready`.
// Quand une navigation en interrompt une autre, `ready` rejette (« Transition was skipped »)
// ou « Transition was aborted because of invalid state », et le navigateur signale un rejet
// non géré (constaté par tests/browser.mjs le 27 octobre 2026). On observe `ready` sans changer le comportement.
// Source : node_modules/@hotwired/turbo/dist/turbo.es2017-esm.js (#viewTransitionStarted).
if (typeof document.startViewTransition === 'function') {
    const start = document.startViewTransition.bind(document);
    document.startViewTransition = (...args) => {
        const transition = start(...args);
        transition.ready.catch(() => {});
        return transition;
    };
}
