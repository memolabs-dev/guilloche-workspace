# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Users

Dirigeants de petites entreprises qui suivent les missions confiées à leur cabinet d'accompagnement et les pièces associées. Démonstration publique d'une intégration frontend Symfony.

## Product Purpose

Retrouver une mission, comprendre la prochaine étape et ouvrir son document associé.

## Operating Context

Fixtures d'Atelier Rivage (entreprise fictive) dans DemoData : missions, documents et facturation.

## Capabilities and Constraints

Symfony 7.4, TwigComponent, SCSS/Encore, Stimulus et Turbo. Composants maison, pas de React ni kit UI tiers. Lecture seule, aucune authentification, API externe, paiement ni décision enregistrée. Concept indépendant : il ne reproduit ni ne prétend reproduire un produit existant. Le code et les tests fonctionnels existants sont conservés.

## Evidence on Hand

Fixtures, tests HTTP et navigateur, contrastes mesurés (tests/system-contrast.py). Le système livré est décrit dans DESIGN.md.

## Product Principles

- Une action ne promet que son comportement réel.
- La tâche reste utilisable au clavier et sans JavaScript.
- Le mouvement exprime état et continuité ; réduction des animations respectée.

## Open Decisions

Direction « titre guilloché » retenue le 27 octobre 2026 pour tout le prototype.

## Références de finition

Base UI, shadcn/ui, daisyUI et Chakra UI comme niveau de soin attendu, sans remplacer la stack Symfony ni installer de kit visuel.
