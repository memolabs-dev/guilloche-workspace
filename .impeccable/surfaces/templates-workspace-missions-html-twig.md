---
version: 1
slug: "templates-workspace-missions-html-twig"
primary_target: "templates/workspace/missions.html.twig"
related_targets: ["templates/workspace/design_system.html.twig"]
---

Scope: tout le prototype (shell, Missions, Documents, vue d'ensemble, facturation, guide, catalogue /design-system). Mode: Operate (catalogue: Operate avec vitrine). Visiteur: dirigeant d'Atelier Rivage ; lecteur secondaire: équipe produit d'un éditeur SaaS.
Job: retrouver une mission, lire la prochaine étape, ouvrir le document. Contraintes: Symfony/Twig/Stimulus/Turbo/SCSS, composants maison, fixtures, sans JS fonctionnel, reduced motion.

## Direction contract

THESIS: L'espace dirigeant se lit comme un titre fiduciaire : chaque mission est une valeur émise, numérotée, protégée. Refuse le dashboard SaaS gris + accent vert + cartes arrondies.
OWN-WORLD: Encre intaille vert profond (#16301f) sur papier de sécurité vert très pâle, blanc pour les pièces de travail. Encre cuivre pour « À votre tour », bleu fiduciaire pour « En cours », vert de marque pour l'action. Lignes guillochées fines (0.6px), micro-texte en filet, numéros de série Fira Code espacés. Bodoni Moda pour titres et chiffres de valeur, Inter pour l'interface.
STORY: Le dirigeant voit ce qui attend son geste (cuivre), ce qui avance (rosaces partiellement tracées), puis ouvre la pièce.
FIRST VIEWPORT: Rail vert intaille avec bande guillochée, liste de missions dont chaque ligne porte sa rosette tracée au prorata, fiche de lecture façon titre : bandeau guilloché, rosette large avec pourcentage en Didone, numéro de série, prochaine étape, pièces.
SIGNATURE: Rosette guillochée unique par mission, calculée en PHP (hypotrochoïdes, graine = id), dont le trait se dessine jusqu'à l'avancement ; bouton primaire à trame guillochée qui moire au survol.
RISKS: dérive luxe bancaire / costume de billet ; densité de traits qui brouille la lecture ; poids HTML des SVG. Garde-fous : guilloché seulement en rosette, bande et bouton primaire, jamais derrière du texte courant ; contrôles standards.
seed: a43fbc89 (pick)
