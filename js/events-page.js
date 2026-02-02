/**
 * Events Page JavaScript
 * Gestion complète de la page événements
 */

document.addEventListener("DOMContentLoaded", function () {
  // ==========================================
  // RECHERCHE D'ÉVÉNEMENTS
  // ==========================================
  const searchForm = document.querySelector(".events-search__form");
  const searchInput = document.querySelector(".events-search__input");

  if (searchForm && searchInput) {
    let searchTimeout;

    searchInput.addEventListener("input", function (e) {
      clearTimeout(searchTimeout);
      const query = e.target.value.trim();

      searchTimeout = setTimeout(() => {
        if (query.length >= 2) {
          performSearch(query);
        } else if (query.length === 0) {
          resetEventsDisplay();
        }
      }, 300);
    });

    searchForm.addEventListener("submit", function (e) {
      e.preventDefault();
      const query = searchInput.value.trim();
      if (query.length >= 2) {
        performSearch(query);
      }
    });
  }

  // ==========================================
  // FILTRES
  // ==========================================
  const filterTags = document.querySelectorAll(".events-filters__tag");
  const filterSelects = document.querySelectorAll(".events-filters__select");
  const clearFiltersBtn = document.querySelector(".events-filters__clear");

  filterTags.forEach((tag) => {
    tag.addEventListener("click", function () {
      this.classList.toggle("events-filters__tag--active");
      applyFilters();
    });
  });

  filterSelects.forEach((select) => {
    select.addEventListener("change", applyFilters);
  });

  if (clearFiltersBtn) {
    clearFiltersBtn.addEventListener("click", clearAllFilters);
  }

  // ==========================================
  // FAVORIS
  // ==========================================
  const favoriteBtns = document.querySelectorAll(".event-card__favorite");

  favoriteBtns.forEach((btn) => {
    btn.addEventListener("click", function (e) {
      e.preventDefault();
      e.stopPropagation();
      this.classList.toggle("event-card__favorite--active");

      // Animation de confirmation
      if (this.classList.contains("event-card__favorite--active")) {
        this.style.transform = "scale(1.2)";
        setTimeout(() => {
          this.style.transform = "";
        }, 200);
      }

      // Sauvegarder dans localStorage
      saveFavorites();
    });
  });

  // Charger les favoris au démarrage
  loadFavorites();

  // ==========================================
  // CHANGEMENT DE VUE (Liste/Calendrier)
  // ==========================================
  const viewToggleBtns = document.querySelectorAll(".events-view-toggle__btn");
  const calendarView = document.querySelector(".events-calendar-view");
  const gridView = document.querySelector(".events-grid__grid");

  viewToggleBtns.forEach((btn) => {
    btn.addEventListener("click", function () {
      const view = this.dataset.view;

      // Update active state
      viewToggleBtns.forEach((b) =>
        b.classList.remove("events-view-toggle__btn--active"),
      );
      this.classList.add("events-view-toggle__btn--active");

      // Show/hide views
      if (view === "calendar" && calendarView) {
        calendarView.classList.add("events-calendar-view--active");
        if (gridView) gridView.style.display = "none";
      } else {
        if (calendarView)
          calendarView.classList.remove("events-calendar-view--active");
        if (gridView) gridView.style.display = "";
      }
    });
  });

  // ==========================================
  // ANIMATIONS AU SCROLL
  // ==========================================
  const animateOnScroll = () => {
    const cards = document.querySelectorAll(".event-card");

    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry, index) => {
          if (entry.isIntersecting) {
            setTimeout(() => {
              entry.target.style.opacity = "1";
              entry.target.style.transform = "translateY(0)";
            }, index * 50);
          }
        });
      },
      { threshold: 0.1, rootMargin: "0px 0px -50px 0px" },
    );

    cards.forEach((card) => {
      card.style.opacity = "0";
      card.style.transform = "translateY(30px)";
      card.style.transition = "opacity 0.5s ease, transform 0.5s ease";
      observer.observe(card);
    });
  };

  setTimeout(animateOnScroll, 100);

  // ==========================================
  // NOTIFICATIONS
  // ==========================================
  const notificationForms = document.querySelectorAll(
    ".notification-card__form, .notification-banner__form",
  );

  notificationForms.forEach((form) => {
    form.addEventListener("submit", function (e) {
      e.preventDefault();
      const input = this.querySelector('input[type="email"]');
      const button = this.querySelector('button[type="submit"]');

      if (input && input.value) {
        // Simuler l'inscription
        const originalText = button.textContent;
        button.textContent = "✓ Inscrit !";
        button.style.backgroundColor = "#4caf50";

        setTimeout(() => {
          button.textContent = originalText;
          button.style.backgroundColor = "";
          input.value = "";
        }, 3000);
      }
    });
  });

  // ==========================================
  // COMPTEUR D'ÉVÉNEMENTS
  // ==========================================
  const updateEventCounter = () => {
    const counter = document.querySelector(".events-grid__count");
    const visibleCards = document.querySelectorAll(
      '.event-card:not([style*="display: none"])',
    );

    if (counter && visibleCards.length > 0) {
      const totalText = counter.textContent.match(/\d+$/);
      if (totalText) {
        counter.textContent = `${visibleCards.length} événement${visibleCards.length > 1 ? "s" : ""} sur ${totalText[0]}`;
      }
    }
  };

  // ==========================================
  // FONCTIONS UTILITAIRES
  // ==========================================
  function performSearch(query) {
    const cards = document.querySelectorAll(".event-card");
    let visibleCount = 0;

    cards.forEach((card) => {
      const title =
        card.querySelector(".event-card__title")?.textContent?.toLowerCase() ||
        "";
      const excerpt =
        card
          .querySelector(".event-card__excerpt")
          ?.textContent?.toLowerCase() || "";
      const category = card.dataset.category || "";

      if (
        title.includes(query.toLowerCase()) ||
        excerpt.includes(query.toLowerCase()) ||
        category.includes(query.toLowerCase())
      ) {
        card.style.display = "";
        visibleCount++;
      } else {
        card.style.display = "none";
      }
    });

    updateEventCounter();
    toggleEmptyState(visibleCount);
  }

  function resetEventsDisplay() {
    const cards = document.querySelectorAll(".event-card");
    cards.forEach((card) => {
      card.style.display = "";
    });
    updateEventCounter();
    toggleEmptyState(cards.length);
  }

  function applyFilters() {
    const activeTags = Array.from(
      document.querySelectorAll(".events-filters__tag--active"),
    ).map((tag) => tag.dataset.filter);

    const dateFilter =
      document.querySelector('.events-filters__select[name="date"]')?.value ||
      "all";
    const priceFilter =
      document.querySelector('.events-filters__select[name="price"]')?.value ||
      "all";

    const cards = document.querySelectorAll(".event-card");
    let visibleCount = 0;

    cards.forEach((card) => {
      let show = true;

      // Filtre par tag
      const cardCategory = card.dataset.category || "";
      if (activeTags.length > 0 && !activeTags.includes(cardCategory)) {
        show = false;
      }

      // Filtre par prix
      if (priceFilter === "gratuit") {
        const price =
          card
            .querySelector(".event-card__price")
            ?.textContent?.toLowerCase() || "";
        if (!price.includes("gratuit") && !price.includes("0")) {
          show = false;
        }
      }

      card.style.display = show ? "" : "none";
      if (show) visibleCount++;
    });

    updateEventCounter();
    toggleEmptyState(visibleCount);
  }

  function clearAllFilters() {
    // Reset tags
    document.querySelectorAll(".events-filters__tag").forEach((tag) => {
      tag.classList.remove("events-filters__tag--active");
    });

    // Reset selects
    document.querySelectorAll(".events-filters__select").forEach((select) => {
      select.value = "all";
    });

    // Reset search
    if (searchInput) searchInput.value = "";

    resetEventsDisplay();
  }

  function toggleEmptyState(visibleCount) {
    const emptyState = document.querySelector(".events-empty");
    const grid = document.querySelector(".events-grid__grid");

    if (emptyState) {
      emptyState.style.display = visibleCount === 0 ? "block" : "none";
    }
    if (grid) {
      grid.style.opacity = visibleCount === 0 ? "0.3" : "1";
    }
  }

  function saveFavorites() {
    const favorites = Array.from(
      document.querySelectorAll(".event-card__favorite--active"),
    ).map((btn) => btn.closest(".event-card")?.dataset.id || "");

    localStorage.setItem("mbaa_favorites", JSON.stringify(favorites));
  }

  function loadFavorites() {
    const saved = localStorage.getItem("mbaa_favorites");
    if (saved) {
      const favorites = JSON.parse(saved);
      favorites.forEach((id) => {
        const card = document.querySelector(`.event-card[data-id="${id}"]`);
        const btn = card?.querySelector(".event-card__favorite");
        if (btn) btn.classList.add("event-card__favorite--active");
      });
    }
  }

  // ==========================================
  // INTERACTIONS CARD HOVER
  // ==========================================
  const cards = document.querySelectorAll(".event-card");

  cards.forEach((card) => {
    card.addEventListener("mouseenter", function () {
      this.style.zIndex = "10";
    });

    card.addEventListener("mouseleave", function () {
      this.style.zIndex = "";
    });
  });

  console.log("Events Page JS loaded successfully");
});
