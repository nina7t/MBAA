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
        init: updateVisualEffect,
        slideChange: updateVisualEffect,
      }
    });
  }

  initCarousel();
});