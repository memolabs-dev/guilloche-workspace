---
name: workspace-symfony-ux
description: Choisir entre Twig Components, Stimulus, Turbo et éventuellement LiveComponent dans ce prototype Symfony. Utiliser pour une décision d'architecture UI ou une fonctionnalité qui combine ces outils.
---

# Choisir la plus petite composition utile

Synthèse locale fondée sur [les sources officielles](../../../docs/research/agent-guides-2026-10-27.md). Le catalogue des skills upstream est conservé hors découverte automatique dans `.agent/upstream/` car sa relecture a identifié des contradictions documentées.

Exécuter `python3 scripts/agent-context.py` avant de présumer qu'un bundle existe.

| Besoin | Outil | Skill à lire |
| --- | --- | --- |
| HTML réutilisable avec props et blocs | TwigComponent | `workspace-twig-components` |
| Interaction locale ou adaptation d'un widget JS | Stimulus | `workspace-stimulus` |
| Navigation, filtrage, pagination serveur | Turbo Drive ou Frame | `workspace-turbo` |
| Plusieurs fragments modifiés par une réponse | Turbo Streams | `workspace-turbo` |
| État réactif et interactions serveur fréquentes | Étudier LiveComponent | Documentation officielle et version du bundle avant installation |

Un filtre GET dans une Frame ne demande pas automatiquement LiveComponent. Les bibliothèques UI fournissent du style et/ou du comportement ; TwigComponent organise leur réutilisation. Elles ne remplacent pas l'une l'autre.

Le prototype possède des macros historiques ; TwigComponent 3.6 est installé et sert à Missions, Documents et au catalogue. Ne pas présenter `<twig:...>` comme opérationnel sans Composer/Flex et test de rendu. LiveComponent, UX Icons et UX Map sont des options documentées, pas des dépendances actuelles.

Pour le design, appliquer Hallmark au périmètre demandé et aux contraintes d'une application de travail. Le propriétaire a refusé le dashboard actuel. Évaluer une bibliothèque pour ses composants et sa personnalisation avant de reprendre ses mises en page par défaut. Conserver les observations sourcées ; les écrans privés restent inconnus.
