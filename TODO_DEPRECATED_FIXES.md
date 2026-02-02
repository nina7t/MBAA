# Plan de correction des warnings de dépréciation Sass

## Problèmes identifiés

1. **`css/layout/_entete.scss` ligne 1328** : `darken($bg-or, 10%)` est déprécié
2. **`css/component/_infos.scss` ligne 132** : `darken($color-secondary-gold, 10%)` est déprécié

## Solution

Remplacer les fonctions `darken()` dépréciées par `color.adjust()` du module `sass:color`.

### Modifications à apporter

#### 1. css/layout/\_entete.scss

- Ajouter `@use "sass:color";` après l'import existant
- Ligne 1328: `darken($bg-or, 10%)` → `color.adjust($bg-or, $lightness: -10%)`

#### 2. css/component/\_infos.scss

- Ajouter `@use "sass:color";` après l'import existant
- Ligne 132: `darken($color-secondary-gold, 10%)` → `color.adjust($color-secondary-gold, $lightness: -10%)`

## Commandes de vérification

Après les modifications, compiler le Sass pour vérifier que les warnings sont résolus :

```bash
npm run sass
# ou
npx sass css/styles.scss
```

## Référence

Plus d'informations : https://sass-lang.com/d/color-functions
