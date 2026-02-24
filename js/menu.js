document.addEventListener("DOMContentLoaded", () => {
  const toggle = document.querySelector(".header__menu-toggle");
  const nav = document.querySelector(".header__nav");
  const header = document.querySelector(".header");
  const headerContainer = document.querySelector(".header__container");

  if (!header || !headerContainer) return;

  // ==========================================
  // GESTION DU MENU BURGER (MOBILE) - CORRIGÉ
  // ==========================================
  if (toggle && nav) {
    const links = nav.querySelectorAll("a");
    let isMenuOpen = false;

    const openMenu = () => {
      isMenuOpen = true;
      toggle.setAttribute("aria-expanded", "true");
      nav.setAttribute("aria-hidden", "false");
      document.body.classList.add("is-nav-open");
      header.classList.add("header--menu-open");

      // Empêcher le scroll du body
      document.body.style.overflow = "hidden";
    };

    const closeMenu = () => {
      isMenuOpen = false;
      toggle.setAttribute("aria-expanded", "false");
      nav.setAttribute("aria-hidden", "true");
      document.body.classList.remove("is-nav-open");
      header.classList.remove("header--menu-open");

      // Réactiver le scroll du body
      document.body.style.overflow = "";
    };

    const syncWithViewport = () => {
      if (window.innerWidth >= 1024) {
        // Desktop : menu toujours visible
        nav.setAttribute("aria-hidden", "false");
        toggle.setAttribute("aria-expanded", "false");
        document.body.classList.remove("is-nav-open");
        header.classList.remove("header--menu-open");
        document.body.style.overflow = "";
        isMenuOpen = false;
      } else if (isMenuOpen) {
        // Mobile : garder l'état actuel si le menu est ouvert
        // Ne rien faire, laisser le menu ouvert
      } else {
        // Mobile : s'assurer que le menu est fermé par défaut
        closeMenu();
      }
    };

    // Toggle du menu au clic
    toggle.addEventListener("click", (e) => {
      e.preventDefault();
      e.stopPropagation();
      e.stopImmediatePropagation();

      if (isMenuOpen) {
        closeMenu();
      } else {
        openMenu();
      }
    });

    // Fallback: ensure toggle is always clickable on mobile
    // This fixes issues where the menu can't be closed
    toggle.addEventListener(
      "touchend",
      (e) => {
        e.preventDefault();
        e.stopPropagation();

        if (isMenuOpen) {
          closeMenu();
        } else {
          openMenu();
        }
      },
      { passive: false },
    );

    // Fermer le menu au clic sur un lien (mobile uniquement)
    links.forEach((link) => {
      link.addEventListener("click", () => {
        if (window.innerWidth < 1024 && isMenuOpen) {
          closeMenu();
        }
      });
    });

    // Fermer avec la touche Escape
    document.addEventListener("keydown", (event) => {
      if (event.key === "Escape" && isMenuOpen) {
        closeMenu();
      }
    });

    // Fermer si on clique en dehors du menu (mobile uniquement)
    document.addEventListener("click", (e) => {
      if (window.innerWidth < 1024 && isMenuOpen) {
        // Si on clique en dehors du nav et du toggle
        if (!nav.contains(e.target) && !toggle.contains(e.target)) {
          closeMenu();
        }
      }
    });

    // Gérer le redimensionnement
    let resizeTimeout;
    window.addEventListener("resize", () => {
      clearTimeout(resizeTimeout);
      resizeTimeout = setTimeout(() => {
        syncWithViewport();
      }, 150);
    });

    // Initialisation
    syncWithViewport();
  }

  // ==========================================
  // GESTION DU SCROLL (DESKTOP UNIQUEMENT)
  // ==========================================
  let ticking = false;
  let lastScrollY = 0;

  const handleScroll = () => {
    const scrollY = window.scrollY || window.pageYOffset;

    // Éviter les calculs inutiles si le scroll n'a pas changé
    if (scrollY === lastScrollY) return;
    lastScrollY = scrollY;

    if (!ticking) {
      window.requestAnimationFrame(() => {
        if (window.innerWidth >= 1024) {
          // Seuil de scroll (100px pour une meilleure transition)
          if (scrollY > 100) {
            header.classList.add("header--scrolled");
          } else {
            header.classList.remove("header--scrolled");
          }
        }
        ticking = false;
      });

      ticking = true;
    }
  };

  // ==========================================
  // GESTION DU PADDING POUR LE HEADER FIXE
  // ==========================================
  const updateContentPadding = () => {
    if (window.innerWidth >= 1024) {
      const containerHeight = headerContainer.offsetHeight;

      // Trouver l'élément à qui appliquer le padding
      const hero = header.querySelector(".header__hero");
      const main = document.querySelector("main");
      const firstContentAfterHeader = header.nextElementSibling;

      // Priorité : hero > main > premier élément après header
      const targetElement = hero || main || firstContentAfterHeader;

      if (targetElement) {
        // Appliquer le padding seulement si ce n'est pas déjà fait
        const currentPadding = parseInt(
          window.getComputedStyle(targetElement).paddingTop,
        );
        if (Math.abs(currentPadding - containerHeight) > 5) {
          // Tolérance de 5px
          targetElement.style.paddingTop = `${containerHeight}px`;
        }
      }
    } else {
      // Mobile : retirer tous les paddings ajoutés
      const hero = header.querySelector(".header__hero");
      const main = document.querySelector("main");
      const firstContentAfterHeader = header.nextElementSibling;

      [hero, main, firstContentAfterHeader].forEach((el) => {
        if (el && el.style.paddingTop) {
          el.style.paddingTop = "";
        }
      });
    }
  };

  // ==========================================
  // INITIALISATION
  // ==========================================
  const initialize = () => {
    // Nettoyer l'ancien listener si on réinitialise
    window.removeEventListener("scroll", handleScroll);

    if (window.innerWidth >= 1024) {
      // Desktop : activer le scroll listener
      window.addEventListener("scroll", handleScroll, { passive: true });
      handleScroll(); // Vérifier l'état initial
      updateContentPadding();
    } else {
      // Mobile : retirer la classe scrolled et le padding
      header.classList.remove("header--scrolled");
      updateContentPadding();
    }
  };

  // ==========================================
  // OBSERVER LES CHANGEMENTS DE TAILLE
  // ==========================================
  const resizeObserver = new ResizeObserver((entries) => {
    for (let entry of entries) {
      if (window.innerWidth >= 1024) {
        // Utiliser un debounce pour éviter trop d'appels
        requestAnimationFrame(() => {
          updateContentPadding();
        });
      }
    }
  });

  resizeObserver.observe(headerContainer);

  // ==========================================
  // ÉCOUTER LE REDIMENSIONNEMENT
  // ==========================================
  let resizeTimer;
  window.addEventListener("resize", () => {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(() => {
      initialize();
    }, 150);
  });

  // ==========================================
  // LANCEMENT INITIAL
  // ==========================================
  initialize();

  // ==========================================
  // GESTION DE L'OVERLAY DE RECHERCHE (OPTIONNEL)
  // ==========================================
  const searchIcon = document.querySelector(
    ".header__nav-icon[alt='Rechercher']",
  );
  const searchOverlay = document.querySelector(".search-overlay");
  const searchClose = document.querySelector(".search-overlay__close");
  const searchInput = document.querySelector(".search-overlay__input");

  if (searchIcon && searchOverlay) {
    const openSearch = () => {
      searchOverlay.classList.add("is-active");
      document.body.style.overflow = "hidden";
      // Focus sur l'input après l'animation
      setTimeout(() => {
        if (searchInput) searchInput.focus();
      }, 300);
    };

    const closeSearch = () => {
      searchOverlay.classList.remove("is-active");
      document.body.style.overflow = "";
    };

    // Ouvrir au clic sur l'icône
    searchIcon.addEventListener("click", (e) => {
      e.preventDefault();
      openSearch();
    });

    // Fermer au clic sur le bouton close
    if (searchClose) {
      searchClose.addEventListener("click", closeSearch);
    }

    // Fermer au clic sur l'overlay (en dehors du contenu)
    searchOverlay.addEventListener("click", (e) => {
      if (e.target === searchOverlay) {
        closeSearch();
      }
    });

    // Fermer avec Escape
    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape" && searchOverlay.classList.contains("is-active")) {
        closeSearch();
      }
    });
  }
});
