document.addEventListener("DOMContentLoaded", () => {
  const toggle = document.querySelector(".header__menu-toggle");
  const nav = document.querySelector(".header__nav");
  const header = document.querySelector(".header");
  const headerContainer = document.querySelector(".header__container");

  if (!header || !headerContainer) return;

  // ==========================================
  // GESTION DU MENU BURGER (MOBILE)
  // ==========================================
  if (toggle && nav) {
    toggle.addEventListener('click', () => {
      const isExpanded = toggle.getAttribute('aria-expanded') === 'true';

      toggle.setAttribute('aria-expanded', !isExpanded);
      nav.setAttribute('aria-hidden', isExpanded);

      if (!isExpanded) {
        header.classList.add('header--menu-open');
        document.body.style.overflow = 'hidden';
      } else {
        header.classList.remove('header--menu-open');
        document.body.style.overflow = '';
      }
    });

    // Fermer le menu avec Escape
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && toggle.getAttribute('aria-expanded') === 'true') {
        toggle.click();
      }
    });

    // Fermer le menu au clic sur un lien (mobile)
    const links = document.querySelectorAll('.header__nav-link, .header__nav-link-fr');
    links.forEach(link => {
      link.addEventListener('click', () => {
        if (window.innerWidth < 1024) {
          setTimeout(() => {
            if (toggle.getAttribute('aria-expanded') === 'true') {
              toggle.click();
            }
          }, 300);
        }
      });
    });

    // Adapter le menu au redimensionnement
    let resizeTimerBurger;
    window.addEventListener('resize', () => {
      clearTimeout(resizeTimerBurger);
      resizeTimerBurger = setTimeout(() => {
        if (window.innerWidth >= 1024) {
          toggle.setAttribute('aria-expanded', 'false');
          nav.setAttribute('aria-hidden', 'true');
          header.classList.remove('header--menu-open');
          document.body.style.overflow = '';
        }
      }, 150);
    });
  }

  // ==========================================
  // NAVBAR VISIBLE UNIQUEMENT AU SCROLL VERS LE HAUT
  // ==========================================
  let lastScrollYHide = 0;
  const scrollThreshold = 10;

  const handleNavVisibility = (scrollY) => {
    const scrollDelta = scrollY - lastScrollYHide;

    if (Math.abs(scrollDelta) < scrollThreshold) return;

    if (scrollDelta < 0) {
      // Scroll vers le HAUT → afficher
      headerContainer.style.transform = "";
    } else {
      // Scroll vers le BAS → cacher (sauf menu mobile ouvert)
      if (!header.classList.contains("header--menu-open")) {
        headerContainer.style.transform = "translateY(-150%)";
      }
    }

    lastScrollYHide = scrollY;
  };

  // ==========================================
  // GESTION DU SCROLL (DESKTOP UNIQUEMENT)
  // ==========================================
  let ticking = false;
  let lastScrollY = 0;

  const handleScroll = () => {
    const scrollY = window.scrollY || window.pageYOffset;

    if (scrollY === lastScrollY) return;
    lastScrollY = scrollY;

    if (!ticking) {
      window.requestAnimationFrame(() => {
        if (window.innerWidth >= 1024) {
          // Classe scrolled après 100px
          if (scrollY > 100) {
            header.classList.add("header--scrolled");
          } else {
            header.classList.remove("header--scrolled");
          }

          // Visibilité selon direction du scroll
          handleNavVisibility(scrollY);
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

      const hero = header.querySelector(".header__hero");
      const main = document.querySelector("main");
      const firstContentAfterHeader = header.nextElementSibling;

      const targetElement = hero || main || firstContentAfterHeader;

      if (targetElement) {
        const currentPadding = parseInt(
          window.getComputedStyle(targetElement).paddingTop,
        );
        if (Math.abs(currentPadding - containerHeight) > 5) {
          targetElement.style.paddingTop = `${containerHeight}px`;
        }
      }
    } else {
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
    window.removeEventListener("scroll", handleScroll);

    if (window.innerWidth >= 1024) {
      window.addEventListener("scroll", handleScroll, { passive: true });
      handleScroll();
      updateContentPadding();
    } else {
      header.classList.remove("header--scrolled");
      // Réinitialiser le transform sur mobile
      headerContainer.style.transform = "";
      updateContentPadding();
    }
  };

  // ==========================================
  // OBSERVER LES CHANGEMENTS DE TAILLE
  // ==========================================
  const resizeObserver = new ResizeObserver(() => {
    if (window.innerWidth >= 1024) {
      requestAnimationFrame(() => {
        updateContentPadding();
      });
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
      setTimeout(() => {
        if (searchInput) searchInput.focus();
      }, 300);
    };

    const closeSearch = () => {
      searchOverlay.classList.remove("is-active");
      document.body.style.overflow = "";
    };

    searchIcon.addEventListener("click", (e) => {
      e.preventDefault();
      openSearch();
    });

    if (searchClose) {
      searchClose.addEventListener("click", closeSearch);
    }

    searchOverlay.addEventListener("click", (e) => {
      if (e.target === searchOverlay) {
        closeSearch();
      }
    });

    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape" && searchOverlay.classList.contains("is-active")) {
        closeSearch();
      }
    });
  }
});