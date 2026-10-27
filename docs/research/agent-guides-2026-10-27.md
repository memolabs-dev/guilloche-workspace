# Socle agentique Symfony, Twig et Hotwire

Recherche et intégration du 27 octobre 2026. Périmètre : aider les agents à développer cette application selon les guides des mainteneurs. Les skills sont des consignes de développement, pas une dépendance IA à ajouter au produit.

## Ce qui est effectivement intégré

Cinq skills locaux dans `.agents/skills/`, reliés par `AGENTS.md` :

| Skill | Rôle |
| --- | --- |
| `workspace-symfony-stack` | Versions, Symfony, Twig, Encore, vérifications adaptées |
| `workspace-symfony-ux` | Choix entre composants, comportement local et navigation serveur |
| `workspace-twig-components` | Props, blocs, attributs, cycle de montage et réutilisation |
| `workspace-stimulus` | Actions, ressources, widgets et reconnexions |
| `workspace-turbo` | Drive, Frames, Streams, HTTP et cache |

Ces cinq textes sont des synthèses locales sourcées. Ils ne sont pas publiés par Symfony. Ils deviennent découvrables par les agents qui lisent les skills du projet ; une nouvelle session peut être nécessaire pour recharger leur catalogue. Le présent agent les a écrits et relus directement.

Le pack de Simon André a été téléchargé par l'installateur de skills, puis déplacé hors découverte automatique après revue : `.agent/upstream/symfony-ux-skills/`. Il contient les quatre skills correspondants, leurs neuf références, README, licence MIT et manifeste SHA-256. Commit figé : `1e99301a6255724eca9c49ce9cdb8c241771ab05`. Les originaux restent intacts et consultables, ils ne sont pas des consignes actives.

`python3 scripts/agent-context.py` lit les verrous sans lire les secrets et vérifie l'intégrité de cette copie. Il ne met rien à jour et ne prouve pas la conformité du code aux recommandations.

## Versions et lacunes réelles du prototype

Relevé dans les verrous : Symfony **7.4.21**, Twig **3.30.0**, StimulusBundle et UX Turbo **3.6.0**, EncoreBundle **2.4.2**, Encore npm **7.2.0**, Stimulus JS **3.2.2**, Turbo JS **8.0.23**, Sass **1.106.1**. PHP observé : **8.5.0**.

**TwigComponent n'est pas installé.** Les composants du prototype sont aujourd'hui des macros. LiveComponent, UX Icons, UX Map et Mate sont également absents. Installer leurs skills ou consulter leur documentation ne les installe pas dans l'application. La migration Symfony 8.1 précédemment annoncée n'a pas été exécutée ; aucune affirmation « tout est à la dernière stable » n'est justifiée à ce stade.

## Sources officielles à consulter selon la tâche

| Domaine | Sources primaires | Conséquence pour le projet |
| --- | --- | --- |
| Symfony | [Bonnes pratiques 7.4](https://symfony.com/doc/7.4/best_practices.html), [installation 7.4](https://symfony.com/doc/7.4/setup.html), [releases](https://symfony.com/releases) | Utiliser la doc de la branche installée, Composer/Flex et l'injection des services ; revalider avant migration. |
| Twig 3 | [Templates](https://twig.symfony.com/doc/3.x/templates.html), [escape](https://twig.symfony.com/doc/3.x/filters/escape.html) | Échappement contextuel, contrats de données, héritage et composition. |
| TwigComponent | [Documentation](https://symfony.com/bundles/ux-twig-component), [migration UX 3](https://github.com/symfony/ux/blob/3.x/UPGRADE-3.0.md) | Configuration de la version réelle, props et attributs sans doublons. |
| LiveComponent, optionnel | [Documentation](https://symfony.com/bundles/ux-live-component) | Étudier writable props, actions, validation et chargement seulement si une réactivité serveur le justifie. |
| Stimulus | [Controllers/scopes](https://stimulus.hotwired.dev/reference/controllers), [actions](https://stimulus.hotwired.dev/reference/actions), [lifecycle](https://stimulus.hotwired.dev/reference/lifecycle-callbacks), [StimulusBundle](https://symfony.com/bundles/StimulusBundle) | Comportements déclaratifs, reconnexion et nettoyage des ressources. |
| Turbo | [Drive](https://turbo.hotwired.dev/handbook/drive), [Frames](https://turbo.hotwired.dev/handbook/frames), [Streams](https://turbo.hotwired.dev/handbook/streams), [cache et JS](https://turbo.hotwired.dev/handbook/building), [UX Turbo](https://symfony.com/bundles/ux-turbo) | Respecter le contrat HTTP et le DOM persistant. |
| Encore | [Installation](https://symfony.com/doc/current/frontend/encore/installation.html), [configuration](https://symfony.com/doc/current/frontend/encore/simple-example.html) | Encore 7 : ESM, `type: module`, configuration asynchrone. Ces guides sont actuellement cohérents avec notre version npm. |
| Accessibilité | [WAI Authoring Practices](https://www.w3.org/WAI/ARIA/apg/), documentation des composants choisis | Les styles et l'ARIA seuls ne prouvent pas le comportement clavier. Tests adaptés au composant livré. |

`current` est une URL mobile. Les guides UX et Hotwire sont comparés aux verrous et aux migrations lors d'un changement ; les références ci-dessus ne figent pas leurs versions futures.

## Ce qui est officiel, mainteneur ou communautaire

**Symfony UX Skills de Simon André.** [L'annonce Symfony du 6 mars](https://symfony.com/blog/introducing-ai-skills-for-symfony-ux) présente [son dépôt personnel](https://github.com/smnandre/symfony-ux-skills). C'est une ressource de mainteneur présentée officiellement, pas un dépôt publié sous l'organisation Symfony. Sa couverture déclarée vise UX 2.22 à 2.28+, antérieure à nos bundles 3.6. Les sept skills disponibles couvrent UX, Stimulus, Turbo, TwigComponent, LiveComponent, Icons et Map. Les trois derniers sont catalogués mais non activés dans le prototype.

**Symfony AI Mate.** [Outil officiel de diagnostic](https://symfony.com/doc/current/ai/components/mate.html), actuellement CLI. [Ses sept skills](https://symfony.com/doc/current/ai/components/mate/skills.html) couvrent environnement PHP, versions, triage de requête, profiler, conteneur, dotenv et logs. La release observée est `v1.0.0-BETA1` du 26 octobre. Le projet n'en a pas besoin pour appliquer les bonnes pratiques frontend : installation non réalisée. Si retenu plus tard, utiliser la procédure Composer de la version choisie et `vendor/bin/mate skills:install --dry-run`, puis les commandes gérées de Mate. Ne pas modifier à la main ses dossiers générés.

**Toolkit upstream.** Le [skill de `symfony/ux`](https://github.com/symfony/ux/blob/3.x/.agents/skills/symfony-ux-toolkit-kit/SKILL.md) sert à contribuer aux recettes de la bibliothèque. Ses exigences de parité Shadcn/Flowbite et de publication upstream ne définissent pas le design de notre application.

**EasyAdmin.** [Skill livré officiellement avec sa version Composer](https://symfony.com/doc/current/EasyAdminBundle/ai-coding-agents.html), installable par `easyadmin:ai:install` lorsque le bundle existe. Pas de raison d'installer EasyAdmin dans ce prototype pour obtenir ce skill.

Les arbres consultés de Twig, Stimulus et Turbo n'ont pas révélé de skill général d'application publié par leurs organisations. C'est un résultat de recherche limité, pas une preuve d'absence universelle. Les packs `Kocal/symfony-contribution-skills`, `fatonh/symfony-skills` et `dev-toolings/superpowers-symfony` sont communautaires ; les premiers visent notamment la contribution au framework. Aucun n'a été ajouté.

## Pourquoi le pack upstream n'est pas activé tel quel

Une revue indépendante a lu les quatre skills et leurs neuf références. Aucun mécanisme malveillant repéré, mais plusieurs prescriptions contredisent les guides primaires :

| Prescription upstream erronée | Règle vérifiée |
| --- | --- |
| Lazy Stimulus déclenché par visibilité | Déclenché par présence du contrôleur dans le DOM. [Bundle](https://symfony.com/bundles/StimulusBundle#lazy-stimulus-controllers) |
| Paramètres d'action toujours chaînes | Typage automatique selon la valeur. [Actions](https://stimulus.hotwired.dev/reference/actions#action-parameters) |
| Tous les contrôleurs enfants isolent les targets | Examiner les scopes imbriqués du même identifiant. [Scopes](https://stimulus.hotwired.dev/reference/controllers#nested-scopes) |
| `mount()` après affectation des props | Appelé avant l'affectation automatique. [Mount](https://symfony.com/bundles/ux-twig-component#the-mount-method) |
| Classes littérales et `attributes` automatiquement fusionnés | Utiliser la fusion explicite par `attributes.defaults`. [Attributs](https://symfony.com/bundles/ux-twig-component#defaults-merging) |
| Cache par `this.method` | Employer le mécanisme `computed` selon version. [Computed](https://symfony.com/bundles/ux-twig-component#computed-properties) |
| Scripts du body jamais réévalués | Ils peuvent l'être lors du rendu Turbo. [Scripts](https://turbo.hotwired.dev/handbook/building#working-with-script-elements) |
| Succès POST en HTML 200 avec Drive | Redirection 303 ou Stream ; erreur de validation 422. [HTTP](https://turbo.hotwired.dev/handbook/drive#redirecting-after-a-form-submission) |

Les exemples pédagogiques contiennent aussi des interactions clavier incomplètes, `raw` sans contrat de confiance et des soumissions bloquées systématiquement par `:prevent`. Ils ne sont pas considérés comme des composants prêts à livrer.

UX 3 impose notamment PHP 8.4 et `twig_component.defaults`, remplace `cva()` par `html_cva()` et retire certains anciens helpers. Cela ne rend pas tout exemple 2.x faux, mais impose un contrôle ciblé. Les skills actifs sont des synthèses courtes des guides, pas des copies de ces exemples.

## Skills des bibliothèques candidates

| Bibliothèque | Ressource du mainteneur confirmée | Statut ici |
| --- | --- | --- |
| daisyUI | [Skill et installation officielle](https://daisyui.com/docs/skill/) dans `saadeghi/daisyui` | Catalogué ; à intégrer si daisyUI est choisi. |
| Preline | [Agent Skills](https://preline.co/docs/agent-skills.html), `preline-theme-generator` et `preline-mcp` | Catalogués ; la présence d'un skill MCP ne justifie pas de connecter ce service. |
| Web Awesome | [Skill et ressources IA](https://webawesome.com/docs/ai), distribués avec npm | Catalogué ; à utiliser pour la version choisie. |
| Flowbite | [Guide TypeUI](https://flowbite.com/docs/getting-started/typeui/) | Ressource liée, pas preuve d'un skill général Flowbite officiel. |
| Bootstrap, Tabler, Bulma, UIkit, Basecoat, Tailwind Plus | Guides officiels identifiés ; aucun skill officiel spécifique confirmé dans cette recherche | Ne pas substituer un pack d'agrégateur en le qualifiant d'officiel. |

Le [comparatif UI](component-libraries-2026-10-27.md) donne les conséquences d'un choix. Les skills des candidats ne sont pas chargés en masse : cela mélangerait plusieurs systèmes CSS et des consignes concurrentes avant la décision.

## Fonctionnement attendu d'un agent

Lire le contrat local et l'inventaire des versions ; choisir le skill correspondant ; consulter le guide primaire utile ; modifier la plus petite tranche cohérente ; vérifier le comportement concerné, notamment les restaurations Turbo ; noter la preuve et les limites. Pour la refonte, utiliser Hallmark dans le contexte d'un écran de travail, avec les tokens du projet et une composition choisie pour le parcours.

Ce socle est une aide à la décision. Il ne certifie ni la conformité globale de l'application ni la future qualité visuelle. Le design a été refusé et reste à refaire ; l'installation du bundle TwigComponent et la migration Symfony restent explicites dans l'état du projet.
