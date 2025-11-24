# Projet MBAA - Musée des Beaux-Arts et d'Archéologie

## Structure du projet

```
PROJET_INDIV/
├── index.html
├── scss/
│   ├── _variables.scss      # Variables (couleurs, typographie, espacements)
│   ├── _base.scss           # Styles de base et reset
│   ├── layout/
│   │   ├── _header.scss     # Styles pour le header
│   │   └── _main.scss       # Styles pour la section main
│   ├── component/
│   │   └── _button.scss     # Styles pour les boutons
│   └── styles.scss          # Fichier principal qui importe tous les autres
├── fonts/                   # Dossier pour les polices (à créer)
└── style.css                # Fichier CSS compilé (généré automatiquement)
```

## Méthode BEM utilisée

Le projet utilise la méthode BEM (Block Element Modifier) :
- **Block** : `home`, `main`
- **Element** : `home__nav-bar`, `home__list`, `main__card`
- **Modifier** : `main__button--savoir`

## Installation et compilation Sass

### Option 1 : Avec npm (recommandé)

1. Installer Sass globalement :
```bash
npm install -g sass
```

2. Compiler le fichier Sass en CSS :
```bash
sass scss/styles.scss style.css
```

3. Pour compiler automatiquement à chaque modification (mode watch) :
```bash
sass --watch scss/styles.scss:style.css
```

### Option 2 : Avec Node.js et package.json

1. Installer les dépendances :
```bash
npm install
```

2. Compiler le Sass :
```bash
npm run sass
```

3. Mode watch (compilation automatique) :
```bash
npm run sass:watch
```

## Typographie - Clash Display Variable

La typographie Clash Display Variable est configurée dans `scss/_variables.scss`.

### Pour utiliser la police :

1. Téléchargez les fichiers de police Clash Display Variable (format WOFF2 ou WOFF)
2. Placez-les dans le dossier `fonts/` à la racine du projet
3. Nommez-les : `ClashDisplay-Variable.woff2` et `ClashDisplay-Variable.woff`

### Sources pour télécharger la police :

- [Fontshare](https://www.fontshare.com/fonts/clash-display)
- [Indian Type Foundry](https://indiantypefoundry.com/fonts/clash-display)

### Alternative temporaire :

Si vous n'avez pas encore les fichiers de police, la police de secours (sans-serif) sera utilisée. Vous pouvez décommenter la ligne dans `_variables.scss` pour utiliser une police système en attendant.

## Structure des fichiers Sass

- **`_variables.scss`** : Définit toutes les variables (couleurs, typographie, espacements, breakpoints)
- **`_base.scss`** : Styles de base, reset CSS, styles pour les éléments HTML de base
- **`layout/_header.scss`** : Styles pour le header (section `.home`)
- **`layout/_main.scss`** : Styles pour la section principale (section `.main`)
- **`component/_button.scss`** : Styles pour les boutons
- **`styles.scss`** : Fichier principal qui importe tous les autres fichiers

## Personnalisation

Vous pouvez modifier les variables dans `scss/_variables.scss` pour changer :
- Les couleurs
- Les espacements
- Les breakpoints pour le responsive
- La typographie

