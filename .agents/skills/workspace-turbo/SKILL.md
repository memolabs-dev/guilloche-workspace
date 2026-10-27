---
name: workspace-turbo
description: Développer ou diagnostiquer la navigation Turbo Drive, les Frames, Streams, formulaires et restaurations de cache de ce prototype Symfony. Utiliser pour réponses serveur partielles, historique ou widgets qui se cassent après navigation.
---

# Turbo et contrat HTTP

Synthèse locale. Consulter [Hotwire et le bundle Symfony](../../../docs/research/agent-guides-2026-10-27.md) pour la fonctionnalité modifiée.

- Drive gère les pages ; Frame isole une région ; Streams applique une ou plusieurs opérations ciblées. Une réponse Frame doit contenir la Frame portant l'identifiant attendu. Un document HTML complet n'est pas obligatoire pour chaque réponse Frame.
- Les filtres et recherches en lecture seule utilisent GET. Après une soumission qui modifie l'état, employer une redirection 303 ou une réponse Stream appropriée ; les erreurs de validation peuvent répondre 422. Un HTML 200 direct après POST n'est pas la solution normale.
- La page vit dans un processus navigateur persistant. Le JavaScript chargé dans le corps peut être réévalué lors des navigations ; charger le bundle avec la stratégie Encore et éviter les initialisations globales dupliquées.
- Avant le cache, rétablir les éléments transitoires et les widgets avec `turbo:before-cache` lorsque nécessaire. Stimulus reste responsable des ressources pendant connexion/déconnexion. Utiliser `data-turbo-temporary` pour les éléments réellement temporaires.

Vérifier l'accès direct à l'URL, filtre vide et non vide, liens dans les Frames, retour historique, dialogue fermé puis réouvert et absence de double listener. Tester les erreurs HTTP concernées. Les flux de mutation doivent aussi être contrôlés côté serveur et protégés selon le contexte.

Ne pas ajouter Mercure pour une simple réponse Stream HTTP. Ne pas désactiver Turbo globalement pour masquer une incompatibilité locale de bibliothèque. Rendre cette incompatibilité explicite avec un cas de reproduction et la plus petite adaptation vérifiée.
