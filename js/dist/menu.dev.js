"use strict";

document.addEventListener("DOMContentLoaded", function () {
  var toggle = document.querySelector(".header__menu-toggle");
  var nav = document.querySelector(".header__nav");
  var header = document.querySelector(".header");
  var headerContainer = document.querySelector(".header__container");
  if (!header || !headerContainer) return; // ==========================================
  // GESTION DU MENU BURGER (MOBILE) - CORRIGÉ
  // ==========================================

  if (toggle && nav) {
    var links = nav.querySelectorAll("a");
    var isMenuOpen = false;

    var openMenu = function openMenu() {
      isMenuOpen = true;
      toggle.setAttribute("aria-expanded", "true");
      nav.setAttribute("aria-hidden", "false");
      document.body.classList.add("is-nav-open");
      header.classList.add("header--menu-open"); // Empêcher le scroll du body

      document.body.style.overflow = "hidden";
    };

    var closeMenu = function closeMenu() {
      isMenuOpen = false;
      toggle.setAttribute("aria-expanded", "false");
      nav.setAttribute("aria-hidden", "true");
      document.body.classList.remove("is-nav-open");
      header.classList.remove("header--menu-open"); // Réactiver le scroll du body

      document.body.style.overflow = "";
    };

    var syncWithViewport = function syncWithViewport() {
      if (window.innerWidth >= 1024) {
        // Desktop : menu toujours visible
        nav.setAttribute("aria-hidden", "false");
        toggle.setAttribute("aria-expanded", "false");
        document.body.classList.remove("is-nav-open");
        header.classList.remove("header--menu-open");
        document.body.style.overflow = "";
        isMenuOpen = false;
      } else if (isMenuOpen) {// Mobile : garder l'état actuel si le menu est ouvert
        // Ne rien faire, laisser le menu ouvert
      } else {
        // Mobile : s'assurer que le menu est fermé par défaut
        closeMenu();
      }
    }; // Toggle du menu au clic


    toggle.addEventListener("click", function (e) {
      e.preventDefault();
      e.stopPropagation();
      e.stopImmediatePropagation();

      if (isMenuOpen) {
        closeMenu();
      } else {
        openMenu();
      }
    }); // Fallback: ensure toggle is always clickable on mobile
    // This fixes issues where the menu can't be closed

    toggle.addEventListener("touchend", function (e) {
      e.preventDefault();
      e.stopPropagation();

      if (isMenuOpen) {
        closeMenu();
      } else {
        openMenu();
      }
    }, {
      passive: false
    }); // Fermer le menu au clic sur un lien (mobile uniquement)

    links.forEach(function (link) {
      link.addEventListener("click", function () {
        if (window.innerWidth < 1024 && isMenuOpen) {
          closeMenu();
        }
      });
    }); // Fermer avec la touche Escape

    document.addEventListener("keydown", function (event) {
      if (event.key === "Escape" && isMenuOpen) {
        closeMenu();
      }
    }); // Fermer si on clique en dehors du menu (mobile uniquement)

    document.addEventListener("click", function (e) {
      if (window.innerWidth < 1024 && isMenuOpen) {
        // Si on clique en dehors du nav et du toggle
        if (!nav.contains(e.target) && !toggle.contains(e.target)) {
          closeMenu();
        }
      }
    }); // Gérer le redimensionnement

    var resizeTimeout;
    window.addEventListener("resize", function () {
      clearTimeout(resizeTimeout);
      resizeTimeout = setTimeout(function () {
        syncWithViewport();
      }, 150);
    }); // Initialisation

    syncWithViewport();
  } // ==========================================
  // GESTION DU SCROLL (DESKTOP UNIQUEMENT)
  // ==========================================


  var ticking = false;
  var lastScrollY = 0;

  var handleScroll = function handleScroll() {
    var scrollY = window.scrollY || window.pageYOffset; // Éviter les calculs inutiles si le scroll n'a pas changé

    if (scrollY === lastScrollY) return;
    lastScrollY = scrollY;

    if (!ticking) {
      window.requestAnimationFrame(function () {
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
  }; // ==========================================
  // GESTION DU PADDING POUR LE HEADER FIXE
  // ==========================================


  var updateContentPadding = function updateContentPadding() {
    if (window.innerWidth >= 1024) {
      var containerHeight = headerContainer.offsetHeight; // Trouver l'élément à qui appliquer le padding

      var hero = header.querySelector(".header__hero");
      var main = document.querySelector("main");
      var firstContentAfterHeader = header.nextElementSibling; // Priorité : hero > main > premier élément après header

      var targetElement = hero || main || firstContentAfterHeader;

      if (targetElement) {
        // Appliquer le padding seulement si ce n'est pas déjà fait
        var currentPadding = parseInt(window.getComputedStyle(targetElement).paddingTop);

        if (Math.abs(currentPadding - containerHeight) > 5) {
          // Tolérance de 5px
          targetElement.style.paddingTop = "".concat(containerHeight, "px");
        }
      }
    } else {
      // Mobile : retirer tous les paddings ajoutés
      var _hero = header.querySelector(".header__hero");

      var _main = document.querySelector("main");

      var _firstContentAfterHeader = header.nextElementSibling;
      [_hero, _main, _firstContentAfterHeader].forEach(function (el) {
        if (el && el.style.paddingTop) {
          el.style.paddingTop = "";
        }
      });
    }
  }; // ==========================================
  // INITIALISATION
  // ==========================================


  var initialize = function initialize() {
    // Nettoyer l'ancien listener si on réinitialise
    window.removeEventListener("scroll", handleScroll);

    if (window.innerWidth >= 1024) {
      // Desktop : activer le scroll listener
      window.addEventListener("scroll", handleScroll, {
        passive: true
      });
      handleScroll(); // Vérifier l'état initial

      updateContentPadding();
    } else {
      // Mobile : retirer la classe scrolled et le padding
      header.classList.remove("header--scrolled");
      updateContentPadding();
    }
  }; // ==========================================
  // OBSERVER LES CHANGEMENTS DE TAILLE
  // ==========================================


  var resizeObserver = new ResizeObserver(function (entries) {
    var _iteratorNormalCompletion = true;
    var _didIteratorError = false;
    var _iteratorError = undefined;

    try {
      for (var _iterator = entries[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
        var entry = _step.value;

        if (window.innerWidth >= 1024) {
          // Utiliser un debounce pour éviter trop d'appels
          requestAnimationFrame(function () {
            updateContentPadding();
          });
        }
      }
    } catch (err) {
      _didIteratorError = true;
      _iteratorError = err;
    } finally {
      try {
        if (!_iteratorNormalCompletion && _iterator["return"] != null) {
          _iterator["return"]();
        }
      } finally {
        if (_didIteratorError) {
          throw _iteratorError;
        }
      }
    }
  });
  resizeObserver.observe(headerContainer); // ==========================================
  // ÉCOUTER LE REDIMENSIONNEMENT
  // ==========================================

  var resizeTimer;
  window.addEventListener("resize", function () {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function () {
      initialize();
    }, 150);
  }); // ==========================================
  // LANCEMENT INITIAL
  // ==========================================

  initialize(); // ==========================================
  // GESTION DE L'OVERLAY DE RECHERCHE (OPTIONNEL)
  // ==========================================

  var searchIcon = document.querySelector(".header__nav-icon[alt='Rechercher']");
  var searchOverlay = document.querySelector(".search-overlay");
  var searchClose = document.querySelector(".search-overlay__close");
  var searchInput = document.querySelector(".search-overlay__input");

  if (searchIcon && searchOverlay) {
    var openSearch = function openSearch() {
      searchOverlay.classList.add("is-active");
      document.body.style.overflow = "hidden"; // Focus sur l'input après l'animation

      setTimeout(function () {
        if (searchInput) searchInput.focus();
      }, 300);
    };

    var closeSearch = function closeSearch() {
      searchOverlay.classList.remove("is-active");
      document.body.style.overflow = "";
    }; // Ouvrir au clic sur l'icône


    searchIcon.addEventListener("click", function (e) {
      e.preventDefault();
      openSearch();
    }); // Fermer au clic sur le bouton close

    if (searchClose) {
      searchClose.addEventListener("click", closeSearch);
    } // Fermer au clic sur l'overlay (en dehors du contenu)


    searchOverlay.addEventListener("click", function (e) {
      if (e.target === searchOverlay) {
        closeSearch();
      }
    }); // Fermer avec Escape

    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape" && searchOverlay.classList.contains("is-active")) {
        closeSearch();
      }
    });
  }
});