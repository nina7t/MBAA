# Hiérarchie Typographique - Site Musée MBAA

## Vue d'ensemble

Cette hiérarchie typographique a été conçue pour un site de musée avec un impact visuel fort et une lisibilité optimale sur tous les supports.

## Hiérarchie des titres

### H1 - Monumental et Impactant
**Usage :** Titres principaux de page (hero, landing pages)

- **Mobile :** 2rem (32px)
- **Tablette :** 3rem (48px)  
- **Desktop :** 4.5rem (72px)
- **Hero (spécial) :** Jusqu'à 10rem (160px) sur très grands écrans
- **Font-weight :** 700 (Bold)
- **Line-height :** 1.1 (très serré)
- **Letter-spacing :** -0.03em à -0.05em (serré pour les grandes tailles)
- **Text-transform :** uppercase
- **Espacement :** margin-bottom: 2rem (mobile) / 3rem (desktop)

### H2 - Grand et Visible
**Usage :** Sections principales, bannières, titres de cartes importantes

- **Mobile :** 1.75rem (28px)
- **Tablette :** 2.25rem (36px)
- **Desktop :** 3rem (48px)
- **Font-weight :** 700 (Bold)
- **Line-height :** 1.2 (compact)
- **Letter-spacing :** -0.015em
- **Text-transform :** uppercase
- **Espacement :** margin-top: 2rem (mobile) / 3rem (desktop), margin-bottom: 1.5rem (mobile) / 2rem (desktop)

### H3 - Moyen
**Usage :** Sous-sections, titres de cartes, carrousels

- **Mobile :** 1.25rem (20px)
- **Tablette :** 1.5rem (24px)
- **Desktop :** 2rem (32px)
- **Font-weight :** 600-700 (Semibold à Bold selon contexte)
- **Line-height :** 1.2 (compact)
- **Letter-spacing :** -0.01em
- **Espacement :** margin-top: 1.5rem (mobile) / 2rem (desktop), margin-bottom: 1rem (mobile) / 1.5rem (desktop)

### H4 - Petit mais Visible
**Usage :** Sous-titres, labels, métadonnées

- **Mobile :** 1rem (16px)
- **Tablette :** 1.125rem (18px)
- **Desktop :** 1.5rem (24px)
- **Font-weight :** 500-600 (Medium à Semibold)
- **Line-height :** 1.4 (normal)
- **Espacement :** margin-top: 1rem (mobile) / 1.5rem (desktop), margin-bottom: 0.75rem (mobile) / 1rem (desktop)

### H5 - Très Petit
**Usage :** Légendes, métadonnées, informations secondaires

- **Mobile :** 0.875rem (14px)
- **Desktop :** 1rem (16px)
- **Font-weight :** 400 (Normal)
- **Color :** $color-text-secondary (gris)

### H6 - Minimal
**Usage :** Notes, copyright, informations légales

- **Mobile :** 0.75rem (12px)
- **Desktop :** 0.875rem (14px)
- **Font-weight :** 300 (Light)
- **Color :** $color-text-secondary-gray (gris clair)

## Corps de texte

### Paragraphes (p)
- **Mobile :** 0.875rem - 1rem (14px - 16px)
- **Desktop :** 1rem - 1.125rem (16px - 18px)
- **Line-height :** 1.5 (relaxed)
- **Margin-bottom :** 1.5rem (mobile) / 2rem (desktop)

## Classes spécifiques

### `.main__banniere-titre`
Utilise la hiérarchie **H2** - Pour les bannières de section

### `.main__card-title`
Utilise la hiérarchie **H2** - Pour les titres de cartes d'exposition

### `.main__card-subtitle`
Utilise la hiérarchie **H3** - Pour les sous-titres de cartes

### `.carousel__item-title`
Utilise la hiérarchie **H3** - Pour les titres d'événements dans le carrousel

### `.main__explore-card-title`
Utilise la hiérarchie **H3** - Pour les titres de cartes d'exploration

### `.main__visite-card-title`
Utilise la hiérarchie **H3** - Pour les titres de cartes de visite

### `.header__title`
Utilise la hiérarchie **H1** avec tailles spéciales pour le hero (jusqu'à 10rem sur desktop)

## Principes de la hiérarchie

1. **Écarts significatifs** : Chaque niveau a un écart clair avec le suivant (ratio ~1.5x)
2. **Impact visuel** : Les titres principaux restent très grands et bold
3. **Lisibilité** : Line-height et letter-spacing optimisés pour chaque taille
4. **Espacements cohérents** : Les marges renforcent la hiérarchie visuelle
5. **Responsive** : Utilisation de `clamp()` pour une adaptation fluide
6. **Cohérence** : Toutes les pages utilisent la même hiérarchie

## Utilisation des variables SCSS

Toutes les tailles sont définies dans `css/utils/_variables.scss` et utilisent les variables :
- `$font-size-h1-mobile`, `$font-size-h1-tablet`, `$font-size-h1-desktop`
- `$font-size-h2-mobile`, `$font-size-h2-tablet`, `$font-size-h2-desktop`
- etc.

Les espacements verticaux sont également définis :
- `$spacing-h1-bottom`, `$spacing-h2-top`, `$spacing-h2-bottom`, etc.

## Responsive

Toutes les tailles utilisent `clamp()` pour une adaptation fluide :
```scss
font-size: clamp($font-size-h1-mobile, 5vw + 1rem, $font-size-h1-desktop);
```

Cela garantit :
- Un minimum sur mobile
- Une croissance fluide selon la largeur d'écran
- Un maximum sur desktop

