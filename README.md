# Guilloché Workspace

Un espace dirigeant (missions, pièces, facturation) et son design system, construits avec la stack Symfony côté serveur : Twig Components, Stimulus, Turbo et SCSS. Concept indépendant, données entièrement fictives, aucun compte ni API externe.

- Démonstration : https://guilloche.memolabs.dev
- Design system : https://guilloche.memolabs.dev/design-system
- Guide de la stack, dans l'application : https://guilloche.memolabs.dev/comprendre-la-stack

## L'idée

L'espace se lit comme un titre fiduciaire : encre intaille sur papier de sécurité, numéros de série, et une rosette guillochée par mission. La rosette est calculée en PHP à partir de l'identifiant de la mission, et son trait s'imprime jusqu'à l'avancement. Le système complet (rôles de couleur, typographie, composants, mouvement) est décrit dans [docs/design-system.md](docs/design-system.md).

## Architecture

```
src/
  Controller/        un contrôleur fin par rubrique, paramètres d'URL par #[MapQueryParameter]
  Workspace/         domaine : Mission, Document, Invoice (readonly), enums d'état, filtres,
                     WorkspaceRepository (interface) et ses fixtures en mémoire
  Twig/              extension par attributs (#[AsTwigFilter], #[AsTwigFunction])
  Twig/Components/   Ui\Guilloche, seul composant à classe PHP : il calcule ses courbes
templates/
  components/Ui/     primitives anonymes ({% props %}) : Button, Field, Input, Status, Badge,
                     Notice, Dialog, Icon, Skeleton, RegionState, Guilloche
  components/Mission/, components/Document/   composants métier anonymes
  <rubrique>/        une page par action de contrôleur
assets/
  controllers/       Stimulus : filtered_results, shell, theme, catalog
  styles/            SCSS en couches @use : settings, base, components, layout, domain, pages, motion
tests/
  Controller/, Workspace/, Twig/   PHPUnit (WebTestCase, KernelTestCase, tests unitaires)
  e2e/browser.mjs    parcours de bout en bout dans Chromium, par le protocole DevTools
  contrast.py        contraste des rôles de couleur, dans les deux thèmes
```

Quelques choix :

- **Turbo Frames et GET.** Les filtres et la sélection d'une mission vivent dans l'URL. Une Frame ne remplace que la liste et la fiche ; sans JavaScript, la même URL rend la page complète. Une sélection hors du filtre redirige vers l'URL canonique, un paramètre non scalaire répond 400.
- **Composants.** Les primitives sont des composants anonymes : leurs props sont déclarées, et `attributes.defaults()` fusionne les classes et attributs Stimulus de l'appelant.
- **Thèmes.** Les composants ne lisent que des propriétés CSS sémantiques ; le thème sombre redéfinit les rôles, jamais les composants.
- **Mouvement.** Le skeleton n'apparaît qu'après 140 ms de requête. Une animation d'entrée ne se rejoue pas quand Turbo re-rend un contenu inchangé. `prefers-reduced-motion` retire le tracé et les déplacements.
- **Encore plutôt qu'AssetMapper.** Les styles sont en Sass et le JavaScript passe par Babel : Encore compile les deux en une étape.

## Démarrer

PHP 8.4, Composer, Node 22.

```sh
composer install
npm ci
npm run build
symfony serve        # ou : php -S 127.0.0.1:8087 -t public
```

Image de production (FrankenPHP, `APP_ENV=prod`) :

```sh
docker build -t guilloche-workspace .
docker run --rm -p 8080:80 guilloche-workspace
```

## Qualité

```sh
php bin/phpunit                                   # tests fonctionnels, de composants et unitaires
vendor/bin/phpstan analyse                        # niveau max (après bin/console cache:warmup)
vendor/bin/php-cs-fixer check                     # règles @Symfony
php bin/console lint:twig templates
php bin/console lint:container
python3 tests/contrast.py                         # contrastes WCAG des rôles de couleur
node tests/e2e/browser.mjs                        # Chromium réel, serveur actif sur :8087
```

Le parcours e2e couvre les mises en page de 390 à 1440 px, les filtres et la sélection par Turbo, l'historique, les erreurs réseau et la reprise, le skeleton, le mouvement réduit, les dialogues, le thème et le fonctionnement sans JavaScript.

La CI (GitHub Actions) lance les contrôles PHP et la compilation des assets ; l'image n'est publiée sur GHCR que si tout est vert.

## Consommation réelle du kit Guilloche

Les lignes de missions utilisent maintenant `<twig:Ui:Rosette :seed="mission.reference" />`, provenant de la recette `memolabs-dev/guilloche`. Le calculateur `Guilloche\Signature\RosetteGeometry` et le subscriber `Guilloche\Signature\AutoGeometrySubscriber` sont apportés par Composer ; le hook `PreRenderEvent` calcule les contours dès la réponse HTTP, sans exiger une prop `paths` ni du JavaScript. Les rosettes plus grandes de la page d'accueil restent des éléments de marque historiques, indépendants du nouveau kit neutre.

Le paquet est actuellement référencé comme dépôt VCS et figé au commit `7eccdff` dans `composer.json` / `composer.lock`, dans l'attente d'un tag stable et de la publication Packagist. Ne pas actualiser la branche `dev-main` sans revue du contrat et E2E.

La CI bloque désormais la publication GHCR tant que les tests PHP, les assets **et** le scénario navigateur Chromium n'ont pas tous réussi.
