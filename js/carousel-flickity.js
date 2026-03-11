// Flickity Carousel avec filtres pour MBAA

document.addEventListener("DOMContentLoaded", function () {
  // Attendre que Flickity soit chargé
  if (typeof Flickity === "undefined") {
    console.error("Flickity non chargé");
    return;
  }

  const carouselList = document.querySelector(".carousel__list");
  const filterItems = document.querySelectorAll(
    ".filtre__list-item[data-filter]",
  );
  const prevButton = document.querySelector(".carousel__arrow--prev");
  const nextButton = document.querySelector(".carousel__arrow--next");

  // Vérifier si le carousel existe
  if (!carouselList) {
    console.log("Carousel non trouvé");
    return;
  }

  // Styles dynamiques pour l'effet de flou et les dimensions
  const style = document.createElement("style");
  style.textContent = `
        .carousel__list .flickity-slider {
            transition: transform 0.3s ease;
        }
        
        .carousel__list .carousel__item__container {
            width: 33.333%;
            padding: 0 10px;
            transition: opacity 0.3s ease, filter 0.3s ease, transform 0.3s ease;
            opacity: 0.5;
            filter: blur(2px);
            transform: scale(0.95);
        }
        
        /* Slide active - pleine taille et netteté */
        .carousel__list .carousel__item__container.is-selected {
            opacity: 1;
            filter: none;
            transform: scale(1);
            z-index: 10;
        }
        
        /* Slides adjacentes */
        .carousel__list .carousel__item__container.is-selected + .carousel__item__container,
        .carousel__list .carousel__item__container.is-selected ~ .carousel__item__container:nth-last-child(-n+2) {
            opacity: 0.8;
            filter: blur(1px);
            transform: scale(0.97);
        }
        
        /* Toutes les autres slides */
        .carousel__list .carousel__item__container {
            opacity: 0.4;
            filter: blur(3px);
            transform: scale(0.9);
        }
        
        .carousel__list .carousel__item__container .carousel__item {
            min-height: clamp(500px, 55vh, 650px);
            height: auto;
            padding: clamp(1.5rem, 4vw, 2.5rem);
            background-size: cover;
            background-position: center;
            border-radius: 10px;
            color: #fffdf3;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        .carousel__list .carousel__item__container .carousel__item::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(43, 43, 43, 0.4);
            border-radius: 10px;
            z-index: 0;
        }
        
        .carousel__list .carousel__item__container .carousel__item > * {
            position: relative;
            z-index: 1;
        }
        
        .carousel__list .carousel__item__container .carousel__item-title {
            font-size: clamp(1.5rem, 3vw, 2.5rem);
            font-weight: 700;
            margin-bottom: 1rem;
            color: #FFFDF3;
        }
        
        .carousel__list .carousel__item__container .carousel__item-text {
            font-size: clamp(0.875rem, 2vw, 1.125rem);
            line-height: 1.5;
            margin-bottom: 1rem;
        }
        
        .carousel__list .carousel__item__container .carousel__item-badge {
            font-size: clamp(0.75rem, 1.5vw, 0.875rem);
            padding: 0.5rem 1rem;
            border-radius: 4px;
            display: inline-block;
            margin-bottom: 1rem;
        }
        
        .carousel__list .carousel__item__container .carousel__item-badge--free {
            background: rgba(71, 86, 79, 0.9);
        }
        
        .carousel__list .carousel__item__container .carousel__item-badge--price {
            background: rgba(149, 126, 24, 0.9);
        }
        
        .carousel__list .carousel__item__container .carousel__item-meta {
            font-size: clamp(0.75rem, 1.5vw, 0.875rem);
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        @media (max-width: 1023px) {
            .carousel__list .carousel__item__container {
                width: 50%;
            }
        }
        
        @media (max-width: 767px) {
            .carousel__list .carousel__item__container {
                width: 100%;
            }
        }
        
        /* Navigation arrows */
        .carousel__arrow {
            background: none;
            border: none;
            cursor: pointer;
            padding: 10px;
            transition: transform 0.2s ease;
        }
        
        .carousel__arrow:hover {
            transform: scale(1.1);
        }
        
        .carousel__arrow:active {
            transform: scale(0.95);
        }
        
        .carousel__arrow img {
            width: 40px;
            height: 40px;
        }
        
        /* Filtre actif */
        .filtre__list-item--active {
            background: rgba(149, 126, 24, 0.2);
            font-weight: 600;
        }
    `;
  document.head.appendChild(style);

  // Initialiser Flickity
  let flkty = new Flickity(carouselList, {
    cellAlign: "center",
    contain: true,
    wrapAround: true,
    prevNextButtons: false,
    pageDots: false,
    autoPlay: false,
    dragThreshold: 10,
    selectedAttraction: 0.1,
    friction: 0.8,
    adaptiveHeight: false,
    groupCells: false,
  });

  // Navigation avec les boutons personnalisés
  if (prevButton) {
    prevButton.addEventListener("click", function () {
      flkty.previous();
    });
  }

  if (nextButton) {
    nextButton.addEventListener("click", function () {
      flkty.next();
    });
  }

  // Gestion des filtres
  filterItems.forEach(function (item) {
    item.addEventListener("click", function (e) {
      e.preventDefault();

      const filterValue = this.dataset.filter;

      // Retirer la classe active de tous les filtres
      filterItems.forEach(function (f) {
        f.classList.remove("filtre__list-item--active");
      });

      // Ajouter la classe active au filtre cliqué
      this.classList.add("filtre__list-item--active");

      // Filtrer les slides
      const allContainers = carouselList.querySelectorAll(
        ".carousel__item__container",
      );

      allContainers.forEach(function (container) {
        const itemFilters = container.dataset.filter;

        if (filterValue === "tous") {
          container.style.display = "";
        } else if (itemFilters && itemFilters.includes(filterValue)) {
          container.style.display = "";
        } else {
          container.style.display = "none";
        }
      });

      // Recalculer les dimensions de Flickity après le filtrage
      flkty.reloadItems();
      flkty.resize();
    });
  });

  // Keyboard navigation
  document.addEventListener("keydown", function (e) {
    if (e.key === "ArrowLeft") {
      flkty.previous();
    } else if (e.key === "ArrowRight") {
      flkty.next();
    }
  });

  console.log("Flickity carousel initialized successfully!");
});
