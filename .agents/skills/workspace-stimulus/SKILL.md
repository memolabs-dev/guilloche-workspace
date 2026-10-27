---
name: workspace-stimulus
description: Créer, corriger ou revoir les contrôleurs Stimulus du prototype, leurs actions, targets, values et leur intégration aux widgets. Utiliser pour interactions locales, dialogues, navigation clavier et problèmes de reconnexion Turbo.
---

# Stimulus et DOM persistant

Synthèse locale. Lire les [références officielles](../../../docs/research/agent-guides-2026-10-27.md), en particulier lifecycle et actions, et le contrôleur voisin avant modification.

- Préférer les attributs déclaratifs actions/targets/values pour le câblage. Une instance peut être reconnectée : initialiser et nettoyer les listeners, timers, observers et widgets selon leur durée de vie. Une action déclarative ne demande pas un second listener manuel.
- `disconnect()` nettoie les ressources ; `turbo:before-cache` prépare séparément un DOM restaurable. Ne pas supposer que la destruction sur déconnexion empêche la mise en cache d'un widget transformé.
- Les paramètres d'action sont typés automatiquement par Stimulus. Valider leur usage ; les données client ne constituent pas une autorisation.
- Le chargement lazy de StimulusBundle répond à la présence du contrôleur dans le DOM, pas à sa visibilité dans le viewport. Vérifier le mécanisme Encore/Bridge installé.
- Pour les targets imbriquées, vérifier les scopes et identifiants ; tous les descendants ne sont pas exclus uniformément.

Un dialogue/menu doit avoir une sémantique appropriée, un nom accessible et un parcours clavier cohérent. Tester ouverture répétée, fermeture, retour du focus, navigation Turbo et retour historique. Ne pas recopier un toggle pédagogique comme composant accessible achevé.

Avec une bibliothèque tierce, identifier son API de création/destruction et éviter une double initialisation entre son auto-init et Stimulus. Tester au moins une entrée par navigation et une restauration de cache. Documenter les incompatibilités constatées plutôt que désactiver Turbo sur toute l'application.
