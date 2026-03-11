document.addEventListener("DOMContentLoaded", function () {
  const carouselEl = document.querySelector(".carousel__list");
  if (!carouselEl) return;

  let swiperInstance = null;

  // --- La fonction qui gère le "Net vs Flou" ---
  function updateVisualEffect() {
    if (!swiperInstance) return;

    const slides = swiperInstance.slides;
    slides.forEach((s) => s.classList.remove("is-clear"));

    // Index de la slide au centre
    const center = swiperInstance.activeIndex;

    // On rend nettes : celle du centre + ses deux voisines immédiates
    // Résultat : [Flou] [NET] [NET] [NET] [Flou]
    const clearIndices = [center - 1, center, center + 1];

    clearIndices.forEach((i) => {
      if (slides[i]) slides[i].classList.add("is-clear");
    });
  }

  // --- Fonction de filtrage ---
  function filterSlides(filterValue) {
    if (!swiperInstance) return;

    const slides = swiperInstance.slides;
    
    // Réinitialiser toutes les slides
    slides.forEach(slide => {
      slide.style.display = '';
    });

    if (filterValue === 'tous' || filterValue === 'toutVoir') {
      // Tout afficher
      swiperInstance.update();
      return;
    }

    // Filtrer les slides
    let visibleCount = 0;
    slides.forEach((slide, index) => {
      const itemFilters = slide.dataset.filter;
      if (itemFilters && itemFilters.includes(filterValue)) {
        slide.style.display = '';
        visibleCount++;
      } else {
        slide.style.display = 'none';
      }
    });

    // Mettre à jour Swiper après le filtrage
    swiperInstance.update();
    
    // Si aucune slide visible, afficher un message
    const emptyMsg = document.querySelector('.carousel-empty');
    if (visibleCount === 0 && !emptyMsg) {
      const emptyDiv = document.createElement('div');
      emptyDiv.className = 'carousel-empty';
      emptyDiv.innerHTML = '<p>Aucun événement trouvé pour ce filtre.</p>';
      carouselEl.parentNode.insertBefore(emptyDiv, carouselEl.nextSibling);
    } else if (visibleCount > 0 && emptyMsg) {
      emptyMsg.remove();
    }
  }

  // --- Gestion des clics sur les filtres ---
  function initFilters() {
    const filterButtons = document.querySelectorAll(".filtre__list-item[data-filter]");
    
    filterButtons.forEach(button => {
      button.addEventListener("click", function(e) {
        e.preventDefault();
        
        // Retirer la classe active de tous les filtres
        filterButtons.forEach(btn => btn.classList.remove("filtre__list-item--active"));
        
        // Ajouter la classe active au filtre cliqué
        this.classList.add("filtre__list-item--active");
        
        // Appliquer le filtre
        const filterValue = this.dataset.filter;
        filterSlides(filterValue);
      });
    });
  }

  function initCarousel() {
    if (swiperInstance) swiperInstance.destroy(true, true);

    swiperInstance = new Swiper(".carousel__list", {
      centeredSlides: true,
      grabCursor: true,
      loop: true, // Très important pour l'effet infini
      speed: 700,
      
      // Configuration des "PerView"
      breakpoints: {
        // Mobile
        320: { slidesPerView: 1.3, spaceBetween: 10 },
        // Tablette (3 visibles)
        768: { slidesPerView: 3, spaceBetween: 20 },
        // Desktop (On en veut 5 !)
        1024: { 
          slidesPerView: 5, // Affiche exactement 5 images
          spaceBetween: 30 
        }
      },

      navigation: {
        prevEl: ".carousel__arrow--prev",
        nextEl: ".carousel__arrow--next",
      },

      on: {
        init: () => {
          updateVisualEffect();
          initFilters();
        },
        slideChange: updateVisualEffect,
      }
    });
  }

  initCarousel();
});
