/**
 * UX Improvements JavaScript
 * Améliorations UX pour la page événements
 */

document.addEventListener("DOMContentLoaded", function () {
  // ==========================================
  // BOUTON RETOUR EN HAUT
  // ==========================================
  const backToTopBtn = document.querySelector("[data-back-to-top]");
  const header = document.querySelector("#header");

  if (backToTopBtn) {
    // Afficher/masquer selon le scroll
    function updateBackToTopVisibility() {
      const headerBottom = header ? header.offsetHeight : 200;

      if (window.scrollY > headerBottom) {
        backToTopBtn.classList.add("is-visible");
      } else {
        backToTopBtn.classList.remove("is-visible");
      }
    }

    // Initial check
    updateBackToTopVisibility();

    // Listen for scroll
    window.addEventListener("scroll", updateBackToTopVisibility, {
      passive: true,
    });

    // Click handler
    backToTopBtn.addEventListener("click", function (e) {
      e.preventDefault();

      if (typeof LocomotiveScroll !== "undefined") {
        // If Locomotive Scroll is available, use it
        const scrollInstance = document.querySelector(
          "[data-scroll-container]",
        );
        if (scrollInstance && window.locomotiveScroll) {
          window.locomotiveScroll.scrollTo(0, { duration: 0.5 });
        } else {
          window.scrollTo({ top: 0, behavior: "smooth" });
        }
      } else {
        window.scrollTo({ top: 0, behavior: "smooth" });
      }
    });
  }

  // ==========================================
  // ANIMATIONS D'ENTRÉE POUR LES ÉLÉMENTS
  // ==========================================
  const animateOnScroll = () => {
    const elements = document.querySelectorAll(
      ".Atelier, .Conférences, .Porterie, .Jazz, .bibliotheque",
    );

    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.style.opacity = "1";
            entry.target.style.transform = "translateY(0)";
          }
        });
      },
      { threshold: 0.1 },
    );

    elements.forEach((el) => {
      el.style.opacity = "0";
      el.style.transform = "translateY(30px)";
      el.style.transition = "opacity 0.6s ease, transform 0.6s ease";
      observer.observe(el);
    });
  };

  // Delay animation start
  setTimeout(animateOnScroll, 100);

  // ==========================================
  // GESTION DES BADGES ÉVÉNEMENTS
  // ==========================================
  const addEventBadges = () => {
    const eventCards = document.querySelectorAll('[class*="__container"]');

    eventCards.forEach((card) => {
      // Check if badge already exists
      if (card.querySelector(".event-badge")) return;

      // Add sample badges based on context
      const title =
        card.querySelector('[class*="__title"]')?.textContent?.toLowerCase() ||
        "";

      // Example badges (these would be dynamic in production)
      const badges = [
        { pattern: "atelier", type: "nouveau", text: "Nouveau" },
        { pattern: "jazz", type: "populaire", text: "Populaire" },
        { pattern: "conférence", type: "gratuit", text: "Gratuit" },
      ];

      badges.forEach((badge) => {
        if (title.includes(badge.pattern)) {
          const badgeEl = document.createElement("div");
          badgeEl.className = `event-badge event-badge--${badge.type}`;
          badgeEl.textContent = badge.text;
          card.style.position = "relative";

          // Insert at the beginning of the container
          const firstChild = card.firstElementChild;
          if (firstChild) {
            card.insertBefore(badgeEl, firstChild);
          } else {
            card.appendChild(badgeEl);
          }
        }
      });
    });
  };

  addEventBadges();

  // ==========================================
  // SMOOTH SCROLL POUR LES ANCRES
  // ==========================================
  const smoothScrollAnchors = () => {
    const anchors = document.querySelectorAll('a[href^="#"]');

    anchors.forEach((anchor) => {
      anchor.addEventListener("click", function (e) {
        const targetId = this.getAttribute("href");
        if (targetId === "#") return;

        const target = document.querySelector(targetId);
        if (target) {
          e.preventDefault();

          if (typeof LocomotiveScroll !== "undefined") {
            const scrollContainer = document.querySelector(
              "[data-scroll-container]",
            );
            if (scrollContainer && window.locomotiveScroll) {
              window.locomotiveScroll.scrollTo(target, { duration: 0.8 });
            } else {
              target.scrollIntoView({ behavior: "smooth", block: "start" });
            }
          } else {
            target.scrollIntoView({ behavior: "smooth", block: "start" });
          }
        }
      });
    });
  };

  smoothScrollAnchors();

  // ==========================================
  // INDICATEUR DE POSITION DANS LE FIL D'ARIANE
  // ==========================================
  const updateBreadcrumb = () => {
    const breadcrumbCurrent = document.querySelector(".breadcrumb__current");
    if (!breadcrumbCurrent) return;

    // Update based on scroll position
    const sections = [
      "atelier",
      "conférences",
      "porterie",
      "jazz",
      "bibliotheque",
    ];
    let currentSection = "Événements";

    sections.forEach((section) => {
      const sectionEl = document.querySelector(`.${section}`);
      if (sectionEl) {
        const rect = sectionEl.getBoundingClientRect();
        if (rect.top < 200 && rect.bottom > 0) {
          currentSection = section.charAt(0).toUpperCase() + section.slice(1);
        }
      }
    });

    breadcrumbCurrent.textContent = currentSection;
  };

  window.addEventListener("scroll", updateBreadcrumb, { passive: true });

  // ==========================================
  // INTERACTION AVEC LES CARTES ÉVÉNEMENTS
  // ==========================================
  const enhanceEventCards = () => {
    const cards = document.querySelectorAll('[class*="__container"]');

    cards.forEach((card) => {
      card.style.cursor = "pointer";

      // Add hover effect indicator
      card.addEventListener("mouseenter", function () {
        this.style.transform = "translateY(-4px)";
      });

      card.addEventListener("mouseleave", function () {
        this.style.transform = "translateY(0)";
      });
    });
  };

  enhanceEventCards();

  // ==========================================
  // GESTION DU FORMULAIRE NEWSLETTER
  // ==========================================
  const notificationForm = document.querySelector(".notification-banner__form");
  if (notificationForm) {
    notificationForm.addEventListener("submit", function (e) {
      e.preventDefault();
      const input = this.querySelector(".notification-banner__input");
      const email = input?.value;

      if (email) {
        // Show success message
        const button = this.querySelector(".notification-banner__button");
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
  }

  console.log("UX Improvements loaded successfully");
});
