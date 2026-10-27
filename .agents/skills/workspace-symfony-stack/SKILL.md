---
name: workspace-symfony-stack
description: Développer ou revoir ce prototype Symfony avec Twig et Encore en s'appuyant sur les versions réellement installées et les guides officiels. À lire avant toute modification PHP, Twig ou du pipeline des assets dans ce dépôt.
---

# Contrat d'application Symfony

Synthèse locale, pas un skill officiel Symfony. Lire `AGENTS.md`, puis exécuter `python3 scripts/agent-context.py` depuis la racine. Les verrous et le runtime priment sur les exemples historiques. Le script donne les versions et contrôle l'intégrité des sources conservées, pas la qualité de l'application.

Consulter [les guides officiels](../../../docs/research/agent-guides-2026-10-27.md) pour le domaine modifié, dans la version installée. Ne pas remplacer une version par `current` sans vérifier sa compatibilité.

- Créer les fondations avec Composer/Flex, puis adapter les fichiers générés. Préserver la structure Symfony et l'injection des services. Garder le rendu et les transitions métier respectivement dans Twig et PHP.
- Twig : échappement HTML actif. Éviter `raw` sur une donnée non maîtrisée ; un échappement HTML ne valide pas une URL. Liens via les routes lorsque pertinent, texte et attributs dans leur contexte.
- Encore 7 utilise ESM et une configuration asynchrone. Garder SCSS/Encore tant qu'une modification de pipeline n'est pas décidée. Tailwind 4 nécessite une entrée CSS adaptée, pas une insertion dans SCSS.
- Routes en lecture seule et fixtures restent le périmètre actuel. Un ajout de mutation demande validation serveur et protections adaptées, pas un simple bouton fonctionnant côté navigateur.

Charger `workspace-twig-components`, `workspace-stimulus` ou `workspace-turbo` selon le changement, `workspace-symfony-ux` pour choisir. Lire uniquement les références nécessaires.

Vérifier proportionnellement : lint Twig/PHP/conteneur, build si assets modifiés, tests HTTP concernés et parcours navigateur affectés. Les tests existants et leurs limites sont dans `.agent/project/evidence.md`. Une commande absente ou non exécutée reste à vérifier.
