---
name: Guilloché Workspace
description: Espace dirigeant lu comme un titre fiduciaire, encre intaille sur papier de sécurité.
colors:
  action: "#376b44"
  action-hover: "#2d5a38"
  action-press: "#23482d"
  on-action: "#ffffff"
  action-soft: "#dce9dc"
  action-ink: "#285a35"
  intaglio: "#15301f"
  intaglio-2: "#1c3d29"
  intaglio-ink: "#eef4ee"
  intaglio-ink-2: "#a9c3ae"
  intaglio-line: "#7fae89"
  intaglio-copper: "#dc9150"
  guilloche: "#2d5a3b"
  copper: "#8c4810"
  copper-line: "#c27530"
  copper-soft: "#f4e5d4"
  blue: "#29497a"
  blue-line: "#5b81b6"
  blue-soft: "#e0e8f3"
  green: "#2a6237"
  green-line: "#589a66"
  green-soft: "#dbeadb"
  red: "#a02f29"
  red-line: "#cf645b"
  red-soft: "#f6e0dc"
  neutral: "#4b584f"
  neutral-line: "#97a499"
  neutral-soft: "#e2e8e0"
  paper: "#edf1ea"
  sheet: "#fbfcf9"
  sheet-raised: "#ffffff"
  sheet-sunken: "#e3e9e0"
  ink: "#14261b"
  ink-2: "#435448"
  ink-3: "#56665a"
  rule: "#d3dbcf"
  rule-strong: "#b0bdae"
  control-line: "#7b8b7f"
  focus: "#1f5a33"
typography:
  display: { fontFamily: "'Bodoni Moda Variable', 'Bodoni 72', Didot, 'Times New Roman', serif", fontSize: "clamp(2.25rem, 1.6rem + 2.4vw, 3.25rem)", fontWeight: 500, lineHeight: 1.05, letterSpacing: "-0.015em", fontVariation: "'opsz' 72" }
  headline: { fontFamily: "'Bodoni Moda Variable', 'Bodoni 72', Didot, 'Times New Roman', serif", fontSize: "clamp(1.75rem, 1.4rem + 1.2vw, 2.25rem)", fontWeight: 500, lineHeight: 1.1, letterSpacing: "-0.01em", fontVariation: "'opsz' 48" }
  value: { fontFamily: "'Bodoni Moda Variable', 'Bodoni 72', Didot, 'Times New Roman', serif", fontSize: "2.25rem", fontWeight: 600, lineHeight: 1, fontFeature: "lnum" }
  title: { fontFamily: "Inter, ui-sans-serif, system-ui, -apple-system, 'Segoe UI', sans-serif", fontSize: "1.125rem", fontWeight: 600, lineHeight: 1.2 }
  body: { fontFamily: "Inter, ui-sans-serif, system-ui, -apple-system, 'Segoe UI', sans-serif", fontSize: "0.875rem", fontWeight: 400, lineHeight: 1.55, fontFeature: "cv11, ss01" }
  label: { fontFamily: "Inter, ui-sans-serif, system-ui, -apple-system, 'Segoe UI', sans-serif", fontSize: "0.8125rem", fontWeight: 500, letterSpacing: "normal" }
  serial: { fontFamily: "'Fira Code', ui-monospace, 'SF Mono', Menlo, monospace", fontSize: "0.75rem", fontWeight: 400, fontFeature: "liga 0" }
rounded: { control: "6px", chip: "6px", surface: "12px", dialog: "16px", pill: "999px" }
spacing: { s-1: "4px", s-2: "8px", s-3: "12px", s-4: "16px", s-5: "20px", s-6: "24px", s-7: "32px", s-8: "40px", s-9: "56px", s-10: "72px" }
components:
  button-primary: { backgroundColor: "{colors.action}", textColor: "{colors.on-action}", rounded: "{rounded.control}", padding: "0 16px", height: "40px" }
  button-primary-hover: { backgroundColor: "{colors.action-hover}" }
  button-primary-active: { backgroundColor: "{colors.action-press}" }
  button-secondary: { backgroundColor: "{colors.sheet-raised}", textColor: "{colors.ink}", rounded: "{rounded.control}", padding: "0 16px", height: "40px" }
  button-soft: { backgroundColor: "{colors.action-soft}", textColor: "{colors.action-ink}", rounded: "{rounded.control}" }
  button-ghost: { textColor: "{colors.action-ink}", rounded: "{rounded.control}" }
  input: { backgroundColor: "{colors.sheet-raised}", textColor: "{colors.ink}", rounded: "{rounded.control}", padding: "0 12px", height: "44px" }
  status-action: { backgroundColor: "{colors.copper-soft}", textColor: "{colors.copper}", rounded: "{rounded.pill}", height: "24px" }
  status-progress: { backgroundColor: "{colors.blue-soft}", textColor: "{colors.blue}", rounded: "{rounded.pill}", height: "24px" }
  status-done: { backgroundColor: "{colors.green-soft}", textColor: "{colors.green}", rounded: "{rounded.pill}", height: "24px" }
  badge: { rounded: "{rounded.chip}", height: "22px", typography: "{typography.label}" }
  mission-sheet: { backgroundColor: "{colors.sheet-raised}", textColor: "{colors.ink}", rounded: "{rounded.surface}", padding: "24px" }
  rail: { backgroundColor: "{colors.intaglio}", textColor: "{colors.intaglio-ink}", width: "256px" }
  dialog: { backgroundColor: "{colors.sheet-raised}", textColor: "{colors.ink}", rounded: "{rounded.dialog}", width: "520px" }
  toast: { backgroundColor: "{colors.intaglio}", textColor: "{colors.intaglio-ink}", rounded: "{rounded.surface}" }
---

# Design System: Guilloché Workspace

## Overview

**Creative North Star: "Le titre guilloché"**

L'espace dirigeant se lit comme un titre fiduciaire : chaque mission est une valeur émise, numérotée, protégée. Encre intaille vert profond sur papier de sécurité vert très pâle, feuilles blanches pour les pièces de travail. Le monde vit dans la matière (rail d'encre, rosettes, bandes guillochées, numéros de série), jamais dans les contrôles, qui restent standards et lisibles.

La densité est celle d'un outil de travail : en-tête de page en Didone, puis listes, filtres et fiches en Inter. La rosette de chaque mission est calculée côté serveur et son trait s'imprime jusqu'à l'avancement ; c'est le seul endroit où le décor porte une donnée. Le système refuse le tableau de bord SaaS gris, accent vert et cartes arrondies indistinctes.

Deux thèmes partagent les mêmes rôles : le sombre est un billet tenu à contre-jour, encres éclaircies, aucun rôle nouveau.

**Key Characteristics:**
- Les composants lisent uniquement des propriétés CSS sémantiques (`settings/_tokens.scss`) ; les primitives de marque (`settings/_brand.scss`) ne sont jamais lues directement.
- `[data-theme='dark']` redéfinit les rôles, rien d'autre.
- Guilloché en trois lieux seulement : rosette, bande, trame du bouton primaire.
- Trois encres de contrôle pour l'état : cuivre (à votre tour), bleu (en cours), vert (terminé).
- Rendu complet sans JavaScript ; le mouvement est une couche optionnelle.

## Colors

Encre intaille et papier de sécurité verts, rehaussés de trois encres de contrôle et du vert de marque pour l'action.

### Primary
- **Vert de marque** (`action`). Bouton primaire, case cochée, interrupteur, curseur, caret. Ses états (`action-hover`, `action-press`) en sont des fonçages ; `action-soft` et `action-ink` servent les variantes douces, contour, fantôme et les liens.

### Secondary
- **Encre intaille** (`intaglio`, `intaglio-2`) : fond du rail et du toast, la matière du titre. Ses encres propres (`intaglio-ink`, `intaglio-ink-2`, `intaglio-line`) et un cuivre éclairci (`intaglio-copper`, point du logo) ne s'emploient que sur elle.
- **Encre guilloché** (`guilloche`) : trait par défaut des rosettes et bandes, surchargé par `--rosette-ink` selon l'état.

### Tertiary
- **Cuivre** (`copper`, `copper-line`, `copper-soft`) : ce qui attend le geste du dirigeant, et les numéros de série.
- **Bleu fiduciaire** (`blue`, `blue-line`, `blue-soft`) : en cours, et les notices d'information.
- **Vert terminé** (`green`, `green-line`, `green-soft`) : terminé, succès.
- **Rouge** (`red`, `red-line`, `red-soft`) : erreur, champ invalide, action destructrice.
Chaque encre a trois rôles : texte (`x`), trait (`x-line`, rosettes et contours), fond (`x-soft`).

### Neutral
- **Papier de sécurité** (`paper`) : fond de page. **Feuille** (`sheet`, `sheet-raised`, `sheet-sunken`) : en-têtes de fiche, surfaces de travail, creux et états désactivés.
- **Encres de texte** (`ink`, `ink-2`, `ink-3`) : texte, secondaire, métadonnées et libellés.
- **Filets** (`rule`, `rule-strong`, `control-line`) : séparations, contours appuyés, contour des champs (3:1 au moins). `focus` dessine l'anneau de focus.

### Named Rules
**The Role Only Rule.** Un composant lit un rôle, jamais une valeur. Une couleur nouvelle entre par `_tokens.scss` dans les deux thèmes, puis dans `tests/contrast.py` (48 paires vérifiées aujourd'hui, toutes conformes).
**The Ink Means State Rule.** Cuivre, bleu et vert disent l'état d'une mission et rien d'autre ; l'action reste au vert de marque.

## Typography

**Display Font:** Bodoni Moda Variable (repli Bodoni 72, Didot)
**Body Font:** Inter (interface)
**Label/Mono Font:** Fira Code, numéros de série et micro-texte seulement

**Character:** Une Didone à axe optique pour ce qui a valeur de titre ou de chiffre émis, une grotesque neutre pour tout le travail.

### Hierarchy
- **Display** (500, clamp 2.25 à 3.25rem, 1.05, opsz 72) : titre de page, un par écran.
- **Headline** (500, clamp 1.75 à 2.25rem, 1.1, opsz 48) : titre de la fiche mission ; titres de dialogue en Bodoni 500 à 1.375rem.
- **Value** (600, 2.25rem, chiffres alignés) : pourcentage au centre de la rosette.
- **Title** (Inter 600, 1.125rem) : prochaine étape, en-têtes de région.
- **Body** (Inter 400, 0.875rem, 1.55, cv11 ss01) : tout le texte courant ; paragraphes de contexte à 62ch.
- **Label** (Inter 500, 0.8125rem, casse de phrase) : libellés, métadonnées, en `ink-3`.
- **Serial** (Fira Code 400, 0.75rem, ligatures coupées, cuivre) : « N° OWN-2026-042 ».

### Named Rules
**The Sentence Case Rule.** Libellés en casse de phrase, sans espacement de lettres ni capitales.
**The Serial Only Rule.** Fira Code n'écrit que des numéros de série, du code et le micro-texte. Les dates et nombres de l'interface restent en Inter, chiffres tabulaires.

## Layout

Mobile d'abord, paliers à 600, 960 et 1200px. Au delà de 960px, rail fixe de 256px et contenu jusqu'à 1304px (1240px utiles), marges 40/32/72px ; en dessous, le rail devient un tiroir (avec JavaScript) ou reste en tête de page (sans). Rythme sur base 4 (`s-1` à `s-10`). Les compositions répondent à leur conteneur : la liste des missions passe d'une ligne à deux zones sous 560px, l'espace missions place liste et fiche côte à côte dès 880px (fiche 360 à 440px, collante à 88px), sinon la fiche remplace la liste avec un retour explicite. Cibles tactiles de 44px partout, y compris par extension invisible des boutons de 40px.

## Elevation & Depth

Hybride : tonalité d'abord (papier, feuille, feuille surélevée), ombres vertes très diffuses ensuite. Les ombres teintées d'encre restent discrètes en clair et deviennent noires en sombre.

### Shadow Vocabulary
- **Posée** (`--shadow-1`) : bouton primaire, ligne survolée, pastille d'interrupteur.
- **Feuille** (`--shadow-2`) : fiche mission, ligne sélectionnée, survol du primaire.
- **Flottante** (`--shadow-3`) : dialogue et toast uniquement.

### Named Rules
**The Paper Stack Rule.** La profondeur dit le rôle d'une feuille (posée, sélectionnée, flottante), jamais la décoration.

## Shapes

Rayons de marque, attribués par rôle : contrôles et étiquettes à 6px, surfaces à 12px, dialogue à 16px, pilule pour statuts, compteurs et barres. Filets de 1px, pointillés pour les faits d'une fiche et les champs désactivés. La forme signature est la rosette : épitrochoïde normalisée dans un carré de 1000 unités, plus une contre-courbe décalée d'un demi-lobe.

## Components

### Buttons
- **Shape:** coins de contrôle (6px), hauteur 40px (32px petit, 48px grand), Inter 600.
- **Primary:** vert de marque plein, texte blanc, ombre posée et reflet intérieur ; une trame guillochée à 10 % moire au survol (déplacement du masque, 16 %).
- **Hover / Focus:** fond `action-hover`, pression à 98 %, anneau de focus 2px `focus` décalé de 3px. Icône de fin glissée de 3px.
- **Variants:** intention primary, secondary, danger ; apparence solid, soft, outline, ghost. Un lien reste un lien. Chargement : spinner décoratif, texte accessible, bouton désactivé.

### Chips
- **Status:** pilule de 24px, encre sur fond doux, le texte porte le sens. « À votre tour » appelle par un contour cuivre qui s'élargit trois fois puis se tait. Aucun point coloré décoratif.
- **Badge:** étiquette libre à 6px ; le ton (couleur) est indépendant de la variante (solid, soft, outline).

### Cards / Containers
- **Fiche mission:** feuille surélevée, filet `rule`, rayon 12px, ombre Feuille. En-tête sur `sheet` avec bande guillochée de 36px à l'encre de l'état, série, titre Headline ; rosette de 148px et pourcentage Value ; faits en liste pointillée ; prochaine étape sur un lavis de 10 % de l'encre d'état ; pièces du dossier.
- **Ligne de mission:** rosette 44px tracée au prorata, titre, étape, statut, pourcentage, échéance tabulaire, chevron. Sélection : feuille surélevée, ombre Feuille, repère de 3px qui voyage par View Transition.

### Inputs / Fields
- **Style:** natifs, 44px, contour `control-line`, fond `sheet-raised`, coins 6px. Label, aide et erreur reliés explicitement.
- **Focus:** contour `focus` et halo de 3px à 22 %.
- **Error / Disabled:** contour rouge et halo à 14 %, message en rouge ; désactivé en pointillés sur `sheet-sunken`. Cases et interrupteurs natifs habillés au vert de marque.

### Navigation
- **Rail:** encre intaille, bande guillochée en tête, liens Inter 500 de 44px en `intaglio-ink-2`, actif sur lavis et filet intérieur, compteur cuivre. Micro-texte de sécurité en pied, aria-hidden.

### Ui:Guilloche (signature)
Courbes calculées en PHP (graine = id de la mission), SVG complet servi sans JavaScript. Avancement par `pathLength=100` et `stroke-dasharray`. Épaisseurs en unités du dessin (`--k = 1000 / taille`), car `vector-effect: non-scaling-stroke` casse `pathLength` (il reste réservé à la bande). Le tracé ne s'anime qu'avec `:animated="true"` (`is-tracing`) ; quand Turbo rend la même sélection, `is-entering` et `is-tracing` sont retirés pour éviter tout rejeu.

### Dialog, Toast, Skeleton
Dialogue natif `<dialog>` avec bande guillochée, titre Bodoni, entrée et sortie par `@starting-style` sans minuteur. Toast sur encre intaille, monté du bas. Squelette en lignes hachurées qui défilent, affiché après 140ms de requête seulement.

### Motion
Durées : 120 (retour), 140 (seuil du squelette), 180 (état), 280 (entrée), 520 (impression de fiche, révélation du thème), 1100ms (tracé). Navigation Turbo Drive par `<meta name="view-transition">`, fondu enchaîné natif du contenu ; rail et barre haute immobiles. Le thème se révèle en cercle depuis son bouton. Mouvement réduit : plus de tracé, plus d'impression de fiche, plus de mouvement spatial ; opacité et couleur restent.

## Do's and Don'ts

### Do:
- **Do** lire un rôle sémantique et l'ajouter dans les deux thèmes, puis à `tests/contrast.py`.
- **Do** donner une rosette à toute valeur d'avancement, graine = id, trait à l'encre de l'état.
- **Do** réserver la trame guillochée au bouton primaire plein, un par contexte.
- **Do** garder les libellés en casse de phrase, sans espacement de lettres.
- **Do** rendre chaque écran lisible sans JavaScript et sans mouvement.

### Don't:
- **Don't** placer du guilloché derrière du texte courant : bande, rosette ou bouton, rien d'autre.
- **Don't** utiliser `vector-effect: non-scaling-stroke` sur une rosette.
- **Don't** employer cuivre, bleu ou vert pour autre chose qu'un état.
- **Don't** ajouter de point de statut décoratif, de surtitre ou de libellé en capitales espacées.
- **Don't** rejouer le tracé ou l'impression quand la sélection ne change pas.
