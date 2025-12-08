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
npm install# MBAA - Musée des Beaux-Arts

## 📋 Description

Site web du Musée des Beaux-Arts de Besançon (MBAA). Ce projet présente les collections, les événements et les informations pratiques du musée.

## 🚀 Technologies utilisées

- HTML5
- CSS3 / SCSS
- JavaScript
- Slick Carousel
- Google Maps API

## 📁 Structure du projet

```
MBAA/
├── asset/
│   ├── Img/              # Images du site
│   │   ├── Evenement/    # Images des événements
│   │   ├── tableaux/     # Images des œuvres
│   │   ├── svg/          # Icônes et illustrations vectorielles
│   │   └── logo/         # Logos du musée
│   └── fonts/            # Polices personnalisées
├── css/
│   ├── base/             # Styles de base
│   ├── component/        # Composants réutilisables
│   ├── layout/           # Structure des pages
│   ├── utils/            # Variables et utilitaires
│   ├── vendors/          # Librairies externes (Slick)
│   └── styles.scss       # Fichier principal SCSS
├── js/
│   ├── scroll-hero.js    # Animations de scroll
│   └── slick.min.js      # Carrousel
├── index.html            # Page d'accueil
├── collections.html      # Page des collections
└── README.md
```

## 💻 Installation

1. Cloner le repository
```bash
git clone https://github.com/nina7t/MBAA.git
```

2. Accéder au dossier du projet
```bash
cd MBAA
```

3. Ouvrir le fichier `index.html` dans votre navigateur

## 📦 Dépendances

Les dépendances sont incluses dans le projet :
- Slick Carousel (pour les carrousels d'images)
- Polices personnalisées (Clash Display)

## 🔧 Compilation SCSS

Si vous souhaitez modifier les styles SCSS :

```bash
npm install
```

Puis compiler avec :
```bash
npm run sass
```

## 🌐 Déploiement

Le site peut être déployé sur :
- GitHub Pages
- Netlify


## 👥 Auteur

Nina Tonnaire - [@nina7t](https://github.com/nina7t)

## 📄 Licence

Ce projet est sous licence MIT.


---

© 2024 Musée des Beaux-Arts de Besançon
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

