---
description: Repository Information Overview
alwaysApply: true
---

# MBAA - Musée des Beaux-Arts et d'Archéologie Information

## Summary
The **MBAA project** is a comprehensive static website for the **Musée des Beaux-Arts et d'Archéologie de Besançon**. It provides visitors with information about museum collections, temporary exhibitions, events, and practical details. The project emphasizes a modern UI with smooth animations and a structured Sass architecture following the **BEM (Block Element Modifier)** methodology.

## Structure
- **Root**: Contains main HTML pages (`index.html`, `collections.html`, `evenement.html`, `admin.html`, etc.).
- **`css/`**: Organized Sass project structure following a modular pattern:
  - `base/`: Global styles and resets.
  - `component/`: Reusable UI components (buttons, cards, etc.).
  - `layout/`: Page-specific structural styles (header, footer, main).
  - `utils/`: Variables (colors, fonts), mixins, and helper classes.
  - `vendors/`: External styles for third-party libraries like Slick Carousel.
- **`js/`**: Functional JavaScript files for interactions:
  - `locomotive-init.js`: Initialization of smooth scrolling.
  - `event-calendar.js`: Event management functionality.
  - `gallery-filter.js`: Filtering mechanisms for museum pieces.
  - `reservation.js`: Logic for the reservation system.
- **`asset/`**: Static resources including `Img/` for artworks and icons, and `fonts/` for custom typography (e.g., Clash Display).

## Language & Runtime
**Language**: HTML5, CSS3 / SCSS, JavaScript  
**Version**: Node.js (v16+ recommended for Sass compatibility)  
**Build System**: npm scripts for SCSS compilation  
**Package Manager**: npm

## Dependencies
**Main Dependencies**:
- **jQuery** (^1.7.4): Used for DOM manipulation and legacy support.
- **locomotive-scroll** (^4.1.4): Provides smooth inertial scrolling.
- **Slick Carousel**: Used for image galleries and sliders (included in `vendors/` and `js/`).

**Development Dependencies**:
- **sass** (^1.69.0): Primary tool for compiling SCSS into CSS.

## Build & Installation
```bash
# Install dependencies
npm install

# Compile Sass to CSS
npm run sass

# Watch for SCSS changes during development
npm run sass:watch

# Compile compressed CSS for production
npm run sass:prod
```

## Main Files & Resources
- **Entry Point**: `index.html` (Home page)
- **Styles**: `css/styles.scss` (Main SCSS entry point)
- **Assets**: `asset/Img/` (Multimedia content)
- **Configuration**: `package.json` (Dependency and script management)

## Testing & Validation
- **Validation**: No automated test suite (e.g., Jest or Cypress) is currently configured.
- **Quality Checks**: The project relies on manual browser testing and Sass compilation checks.
- **Deployment**: Configured for static hosting on platforms like GitHub Pages or Netlify.
