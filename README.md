# Guilloché Workspace

Concept indépendant de Guillaume Flambard : un espace dirigeant (missions, pièces, facturation) et son design system, construits avec la stack Symfony côté serveur. Données entièrement fictives, aucun compte, aucune API externe.

Démonstration : https://guilloche.memolabs.dev · design system : https://guilloche.memolabs.dev/design-system

## L'idée

L'espace se lit comme un titre fiduciaire. Encre intaille sur papier de sécurité, numéros de série, et une rosette guillochée par mission, calculée en PHP à partir de son identifiant, dont le trait s'imprime jusqu'à l'avancement. Le système complet est décrit dans [DESIGN.md](DESIGN.md) et démontré sur `/design-system`.

## Stack

- Symfony 7.4, Twig 3, Symfony UX TwigComponent 3.6 : composants `Ui:*` (Button, Field, Status, Dialog, Guilloche…) et métier `Mission:*`, `Document:*`, attributs fusionnés par `attributes.defaults()`.
- Turbo 8 : Frames pour les filtres GET et la sélection, transitions de page par l'API View Transitions.
- Stimulus 3 : chargement différé et skeleton après 140 ms, focus, thème, dialogues natifs.
- SCSS par couches `@use` (`settings`, `base`, `components`, `layout`, `domain`, `pages`, `motion`), rôles sémantiques en propriétés CSS, thèmes clair et sombre.
- Webpack Encore, polices Fontsource (Bodoni Moda, Inter, Fira Code).

Tout fonctionne au clavier et sans JavaScript ; `prefers-reduced-motion` est respecté.

## Démarrer

PHP 8.4+, Composer, Node 22+.

```sh
composer install
npm ci
npm run build
php -S 127.0.0.1:8087 -t public
```

Ou l'image de production (FrankenPHP, `APP_ENV=prod`) :

```sh
docker build -t guilloche-workspace .
docker run --rm -p 8080:80 guilloche-workspace
```

## Tests

```sh
php bin/console lint:twig templates
php tests/smoke.php && php tests/missions.php && php tests/documents.php && php tests/system.php
node tests/browser.mjs        # Chromium réel via agent-browser, serveur actif sur 8087
python3 tests/system-contrast.py
```

`tests/browser.mjs` couvre les mises en page de 390 à 1440 px, les filtres et la sélection par Turbo, l'historique, les erreurs réseau et la reprise, le skeleton, le mouvement réduit, les dialogues, le thème, le parcours sans JavaScript et l'absence de rejeu des animations à chaque rendu.

## Déploiement

GitHub Actions construit l'image sur GHCR à chaque push sur `main`. Le serveur du lab la tire et la sert derrière Traefik.
