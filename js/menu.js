document.addEventListener("DOMContentLoaded", () => {
  const toggle = document.querySelector(".header__menu-toggle");
  const nav = document.querySelector(".header__nav");
  const header = document.querySelector(".header");
  if (!toggle || !nav || !header) return;

  const links = nav.querySelectorAll("a");

  const setMenuState = (isOpen) => {
    toggle.setAttribute("aria-expanded", isOpen ? "true" : "false");
    nav.setAttribute("aria-hidden", isOpen ? "false" : "true");
    document.body.classList.toggle("is-nav-open", isOpen);
    
    // Ajouter/retirer la classe pour cacher le header en mobile
    if (window.innerWidth < 1024) {
      header.classList.toggle("header--menu-open", isOpen);
    } else {
      header.classList.remove("header--menu-open");
    }
  };

  const syncWithViewport = () => {
    if (window.innerWidth >= 860) {
      // Menu toujours visible sur desktop
      nav.setAttribute("aria-hidden", "false");
      toggle.setAttribute("aria-expanded", "false");
      document.body.classList.remove("is-nav-open");
      header.classList.remove("header--menu-open");
    } else {
      // On referme par défaut sur mobile
      setMenuState(false);
    }
  };

  toggle.addEventListener("click", () => {
    const isOpen = toggle.getAttribute("aria-expanded") === "true";
    setMenuState(!isOpen);
  });

  links.forEach((link) => {
    link.addEventListener("click", () => {
      if (window.innerWidth < 860) {
        setMenuState(false);
      }
    });
  });

  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape" && toggle.getAttribute("aria-expanded") === "true") {
      setMenuState(false);
    }
  });

  window.addEventListener("resize", syncWithViewport);
  syncWithViewport();
});

// Navigation bar fixe avec effet semi-opaque au scroll
document.addEventListener("DOMContentLoaded", () => {
  const header = document.querySelector(".header");
  const headerContainer = document.querySelector(".header__container");
  if (!header || !headerContainer) return;

  // Fonction pour calculer et appliquer le padding-top au hero ou au contenu principal
  const updateContentPadding = () => {
    if (window.innerWidth >= 1024) {
      const containerHeight = headerContainer.offsetHeight;
      
      // Si le header contient un hero, appliquer le padding au hero
      const hero = header.querySelector(".header__hero");
      if (hero) {
        hero.style.paddingTop = `${containerHeight}px`;
      } else {
        // Sinon, appliquer au main ou au premier élément après le header
        const main = document.querySelector("main");
        const firstContentAfterHeader = header.nextElementSibling;
        const targetElement = main || firstContentAfterHeader;
        
        if (targetElement) {
          targetElement.style.paddingTop = `${containerHeight}px`;
        }
      }
      
      // Ne pas appliquer de padding au body pour éviter l'espace blanc
      document.body.style.paddingTop = "0";
    } else {
      // Retirer le padding en mobile
      const hero = header.querySelector(".header__hero");
      if (hero) {
        hero.style.paddingTop = "0";
      } else {
        const main = document.querySelector("main");
        const firstContentAfterHeader = header.nextElementSibling;
        const targetElement = main || firstContentAfterHeader;
        
        if (targetElement) {
          targetElement.style.paddingTop = "0";
        }
      }
      document.body.style.paddingTop = "0";
    }
  };

  // Fonction pour gérer l'effet de scroll avec requestAnimationFrame pour optimiser les performances
  let ticking = false;
  
  const handleScroll = () => {
    if (!ticking) {
      window.requestAnimationFrame(() => {
        const scrollY = window.scrollY || window.pageYOffset;
        
        // Seuil de scroll (50px par défaut, ajustable)
        if (scrollY > 50) {
          header.classList.add("header--scrolled");
        } else {
          header.classList.remove("header--scrolled");
        }
        
        ticking = false;
      });
      
      ticking = true;
    }
  };

  // Écouter le scroll uniquement sur desktop (>= 1024px)
  const checkViewportAndAddListener = () => {
    if (window.innerWidth >= 1024) {
      window.addEventListener("scroll", handleScroll, { passive: true });
      // Vérifier l'état initial au chargement
      handleScroll();
      // Mettre à jour le padding-top du contenu
      updateContentPadding();
    } else {
      // Retirer la classe si on passe en mobile
      header.classList.remove("header--scrolled");
      updateContentPadding();
    }
  };

  // Vérifier au chargement et au redimensionnement
  checkViewportAndAddListener();
  
  // Observer les changements de taille du container (au cas où le contenu change)
  const resizeObserver = new ResizeObserver(() => {
    if (window.innerWidth >= 1024) {
      updateContentPadding();
    }
  });
  
  resizeObserver.observe(headerContainer);
  
  window.addEventListener("resize", checkViewportAndAddListener);
});

