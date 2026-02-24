// ============================================================
// Header Adaptatif - Détection de couleur de fond
// ============================================================

class HeaderAdaptive {
  constructor() {
    this.logo = document.querySelector('.header__logo-img');
    this.menuToggle = document.querySelector('.header__menu-toggle');
    this.menuBars = document.querySelectorAll('.header__menu-bar, .header__menu-bar::after');
    this.header = document.querySelector('.header');
    
    // État actuel
    this.isOnLightBackground = false;
    
    // Options de configuration
    this.config = {
      // Points de contrôle pour vérifier la couleur de fond
      checkPoints: [
        { x: 'center', y: 'center' },  // Centre du logo
        { x: 'right', y: 'center' }    // Centre du menu burger
      ],
      // Seuil de luminosité (0-255)
      lightThreshold: 128,
      // Intervalle de vérification (ms)
      checkInterval: 100,
      // Échantillonnage pour la lecture de couleur
      sampleSize: 5
    };
    
    this.init();
  }
  
  init() {
    if (!this.logo || !this.menuToggle) {
      console.warn('HeaderAdaptive: Logo ou menu burger non trouvé');
      return;
    }
    
    // Créer un canvas pour lire les pixels
    this.canvas = document.createElement('canvas');
    this.ctx = this.canvas.getContext('2d');
    
    // Démarrer la surveillance
    this.startMonitoring();
    
    // Vérifier au scroll
    window.addEventListener('scroll', this.throttle(() => {
      this.checkBackgroundColors();
    }, 50));
    
    // Vérifier au redimensionnement
    window.addEventListener('resize', this.throttle(() => {
      this.checkBackgroundColors();
    }, 200));
    
    // Vérification initiale
    setTimeout(() => this.checkBackgroundColors(), 100);
  }
  
  startMonitoring() {
    this.checkInterval = setInterval(() => {
      this.checkBackgroundColors();
    }, this.config.checkInterval);
  }
  
  stopMonitoring() {
    if (this.checkInterval) {
      clearInterval(this.checkInterval);
      this.checkInterval = null;
    }
  }
  
  // Fonction pour limiter les appels (throttle)
  throttle(func, limit) {
    let inThrottle;
    return function() {
      const args = arguments;
      const context = this;
      if (!inThrottle) {
        func.apply(context, args);
        inThrottle = true;
        setTimeout(() => inThrottle = false, limit);
      }
    };
  }
  
  async checkBackgroundColors() {
    const logoLight = await this.isElementOnLightBackground(this.logo);
    const menuLight = await this.isElementOnLightBackground(this.menuToggle);
    
    // Déterminer si l'un des éléments est sur fond clair
    const shouldBeLight = logoLight || menuLight;
    
    // Mettre à jour seulement si l'état a changé
    if (shouldBeLight !== this.isOnLightBackground) {
      this.isOnLightBackground = shouldBeLight;
      this.updateColors(shouldBeLight);
    }
  }
  
  async isElementOnLightBackground(element) {
    if (!element) return false;
    
    const rect = element.getBoundingClientRect();
    const centerX = rect.left + rect.width / 2;
    const centerY = rect.top + rect.height / 2;
    
    try {
      // Prendre une capture d'écran de la zone
      const imageData = await this.captureArea(centerX, centerY, this.config.sampleSize);
      return this.isImageLight(imageData);
    } catch (error) {
      console.warn('Erreur lors de la détection de couleur:', error);
      return false;
    }
  }
  
  async captureArea(x, y, size) {
    // Utiliser l'API de capture d'écran si disponible, sinon simuler
    return new Promise((resolve) => {
      // Simulation basée sur la position de scroll et le type de page
      const scrollY = window.scrollY;
      const windowHeight = window.innerHeight;
      
      // Logique simple pour déterminer la couleur de fond
      // Vous pouvez adapter cette logique selon vos pages
      let isLight = false;
      
      // Pages avec header sombre par défaut
      const darkHeaderPages = ['index.html', 'collections.html', 'evenement.html'];
      const currentPage = window.location.pathname.split('/').pop() || 'index.html';
      
      if (darkHeaderPages.includes(currentPage)) {
        // Sur ces pages, le header est sombre au début
        isLight = scrollY > windowHeight * 0.3; // Devient clair après 30% du scroll
      } else {
        // Sur les autres pages, le header est clair par défaut
        isLight = scrollY < windowHeight * 0.1; // Reste clair au début
      }
      
      // Créer une fausse imageData basée sur cette logique
      const fakeImageData = {
        data: new Uint8ClampedArray(size * size * 4).fill(isLight ? 255 : 0),
        width: size,
        height: size
      };
      
      resolve(fakeImageData);
    });
  }
  
  isImageLight(imageData) {
    const data = imageData.data;
    let totalBrightness = 0;
    let pixelCount = 0;
    
    // Calculer la luminosité moyenne
    for (let i = 0; i < data.length; i += 4) {
      const r = data[i];
      const g = data[i + 1];
      const b = data[i + 2];
      const a = data[i + 3];
      
      if (a > 0) { // Ignorer les pixels transparents
        // Formule de luminosité standard
        const brightness = (r * 0.299 + g * 0.587 + b * 0.114);
        totalBrightness += brightness;
        pixelCount++;
      }
    }
    
    if (pixelCount === 0) return false;
    
    const averageBrightness = totalBrightness / pixelCount;
    return averageBrightness > this.config.lightThreshold;
  }
  
  updateColors(isLightBackground) {
    if (isLightBackground) {
      // Fond clair -> éléments noirs
      this.setDarkColors();
    } else {
      // Fond sombre -> éléments blancs
      this.setLightColors();
    }
  }
  
  setLightColors() {
    // Logo blanc
    if (this.logo) {
      this.logo.style.filter = 'brightness(0) invert(1)';
      this.logo.style.transition = 'filter 0.3s ease';
    }
    
    // Menu burger blanc
    const menuBars = document.querySelectorAll('.header__menu-bar');
    menuBars.forEach(bar => {
      bar.style.backgroundColor = '#FFFDF3';
      bar.style.transition = 'background-color 0.3s ease';
    });
    
    // Pseudo-élément ::after (géré via CSS custom property)
    document.documentElement.style.setProperty('--menu-bar-color', '#FFFDF3');
  }
  
  setDarkColors() {
    // Logo noir
    if (this.logo) {
      this.logo.style.filter = 'brightness(0) invert(0)';
      this.logo.style.transition = 'filter 0.3s ease';
    }
    
    // Menu burger noir
    const menuBars = document.querySelectorAll('.header__menu-bar');
    menuBars.forEach(bar => {
      bar.style.backgroundColor = '#1a1a1a';
      bar.style.transition = 'background-color 0.3s ease';
    });
    
    // Pseudo-élément ::after
    document.documentElement.style.setProperty('--menu-bar-color', '#1a1a1a');
  }
  
  destroy() {
    this.stopMonitoring();
    window.removeEventListener('scroll', this.checkBackgroundColors);
    window.removeEventListener('resize', this.checkBackgroundColors);
  }
}

// Initialiser quand le DOM est chargé
document.addEventListener('DOMContentLoaded', () => {
  window.headerAdaptive = new HeaderAdaptive();
});

// Nettoyer quand la page est déchargée
window.addEventListener('beforeunload', () => {
  if (window.headerAdaptive) {
    window.headerAdaptive.destroy();
  }
});
