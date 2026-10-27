---
name: workspace-twig-components
description: Concevoir ou revoir des composants réutilisables Twig dans Symfony, avec props, blocs, attributs et variantes. Utiliser pour extraire les composants du design system et distinguer macros existantes et véritables UX Twig Components.
---

# Composants Twig d'application

Synthèse locale, pas un skill officiel. Vérifier `composer.lock` avant toute syntaxe dépendant de TwigComponent. Consulter [la documentation officielle](../../../docs/research/agent-guides-2026-10-27.md).

Le prototype utilise actuellement des macros. Lorsque l'intégration TwigComponent est demandée, passer par `composer require symfony/ux-twig-component` et la recette Flex ; contrôler les changements et tester le rendu. Ne pas fabriquer un moteur de composants.

En UX 3, conserver la configuration `twig_component.defaults` avec un couple namespace/dossier valide. Pour les variantes, vérifier `html_cva()` et le paquet Twig HTML Extra : ne pas recopier l'ancien `cva()` du bundle. Lire le guide de migration 3.0 avant de transposer un exemple 2.x.

- Composant anonyme pour du markup paramétrable ; classe PHP lorsque ses données ou sa logique de présentation le justifient. Exposer des props et blocs explicites, conserver le calcul métier hors du template.
- `mount()` s'exécute avant l'assignation automatique des props. Recevoir les données nécessaires en paramètres ; consulter les hooks appropriés pour les autres moments du cycle.
- Rendre les attributs à un endroit explicite. Utiliser `attributes.defaults(...)` pour les classes de base, plutôt que dupliquer `class="..."` et `{{ attributes }}`. Préserver notamment nom accessible, type, identifiant et attributs Stimulus/Turbo.
- Ne pas compter sur `this.method` pour mettre une valeur en cache ; vérifier le mécanisme `computed` disponible dans la version installée.
- Garder l'échappement Twig. Un composant acceptant du HTML arbitraire ou un lien demande un contrat de confiance explicite. Un attribut `disabled` ne désactive pas un lien HTML.

Les variantes visuelles suivent les tokens du projet. Tester le rendu normal et une variante réellement différente, le contenu vide pertinent et la composition des attributs. Pour un composant interactif, vérifier le clavier et les transitions Turbo avec son contrôleur Stimulus, pas seulement son HTML initial.
