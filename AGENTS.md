# Guilloché Workspace

Concept indépendant publié : dépôt public, démonstration sur https://guilloche.memolabs.dev. Ne jamais y nommer d'entreprise réelle ni le présenter comme le produit d'un tiers. Garder visibles les mentions « concept indépendant » et « données fictives ».

- Symfony, Twig, TwigComponent, Stimulus, Turbo, SCSS et Webpack Encore. Pas de React ni de SPA parallèle, aucun kit UI tiers.
- Fixtures uniquement (`src/Demo/DemoData.php`). Aucune authentification, aucune API externe.
- Un seul writer par checkout.

## Système visuel

Direction « titre guilloché » : encre intaille, rosettes guillochées calculées en PHP (`Ui:Guilloche`), numéros de série, vert de marque pour l'action. Référence : `DESIGN.md` ; contrat de direction : `.impeccable/surfaces/templates-workspace-missions-html-twig.md`.

Architecture : un seul shell (`templates/base.html.twig`) ; SCSS en modules `@use` par couche (`settings`, `base`, `components`, `layout`, `domain`, `pages`, `motion`), un partiel par composant Ui. Les composants ne lisent que des propriétés CSS sémantiques (`settings/_tokens.scss`) ; les primitives de marque restent dans `settings/_brand.scss`. Une couleur nouvelle entre dans les deux thèmes et dans `tests/system-contrast.py`.

Composants : attributs via `attributes.defaults()`, jamais de balise de contrôle Twig dans un attribut HTML. Une prop ne porte pas le nom d'une méthode du composant (elle la masquerait). Les liens qui quittent une Turbo Frame portent `data-turbo-frame="_top"`.

Mouvement : une animation d'entrée ne se rejoue pas à chaque rendu Turbo. Le tracé d'une rosette est demandé explicitement (`:animated="true"`) et retiré par `beforeRender` quand la sélection ne change pas.

## Skills des agents

`.agents/skills/` contient les skills actifs : `workspace-symfony-stack` (point d'entrée), `workspace-symfony-ux`, `workspace-twig-components`, `workspace-stimulus`, `workspace-turbo`. Ce sont des synthèses locales, pas des publications officielles Symfony. Les originaux figés et leur licence sont dans `.agent/upstream/`, hors découverte automatique. Avant une modification de stack, lancer `python3 scripts/agent-context.py`.

## Vérification

Lint Twig, tests PHP (`tests/*.php`), `node tests/browser.mjs`, `python3 tests/system-contrast.py`, build Encore. Des tests verts ne valent pas recette visuelle.
