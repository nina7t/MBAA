"use strict";

// Flickity Carousel avec filtres pour MBAA
document.addEventListener("DOMContentLoaded", function () {
  // Attendre que Flickity soit chargé
  if (typeof Flickity === "undefined") {
    console.error("Flickity non chargé");
    return;
  }

  var carouselList = document.querySelector(".carousel__list");
  var filterItems = document.querySelectorAll(".filtre__list-item[data-filter]");
  var prevButton = document.querySelector(".carousel__arrow--prev");
  var nextButton = document.querySelector(".carousel__arrow--next"); // Vérifier si le carousel existe

  if (!carouselList) {
    console.log("Carousel non trouvé");
    return;
  } // Styles dynamiques pour l'effet de flou et les dimensions


  var style = document.createElement("style");
  style.textContent = "\n        .carousel__list .flickity-slider {\n            transition: transform 0.3s ease;\n        }\n        \n        .carousel__list .carousel__item__container {\n            width: 33.333%;\n            padding: 0 10px;\n            transition: opacity 0.3s ease, filter 0.3s ease, transform 0.3s ease;\n            opacity: 0.5;\n            filter: blur(2px);\n            transform: scale(0.95);\n        }\n        \n        /* Slide active - pleine taille et nettet\xE9 */\n        .carousel__list .carousel__item__container.is-selected {\n            opacity: 1;\n            filter: none;\n            transform: scale(1);\n            z-index: 10;\n        }\n        \n        /* Slides adjacentes */\n        .carousel__list .carousel__item__container.is-selected + .carousel__item__container,\n        .carousel__list .carousel__item__container.is-selected ~ .carousel__item__container:nth-last-child(-n+2) {\n            opacity: 0.8;\n            filter: blur(1px);\n            transform: scale(0.97);\n        }\n        \n        /* Toutes les autres slides */\n        .carousel__list .carousel__item__container {\n            opacity: 0.4;\n            filter: blur(3px);\n            transform: scale(0.9);\n        }\n        \n        .carousel__list .carousel__item__container .carousel__item {\n            min-height: clamp(500px, 55vh, 650px);\n            height: auto;\n            padding: clamp(1.5rem, 4vw, 2.5rem);\n            background-size: cover;\n            background-position: center;\n            border-radius: 10px;\n            color: #fffdf3;\n            position: relative;\n            display: flex;\n            flex-direction: column;\n            justify-content: space-between;\n        }\n        \n        .carousel__list .carousel__item__container .carousel__item::after {\n            content: '';\n            position: absolute;\n            top: 0;\n            left: 0;\n            right: 0;\n            bottom: 0;\n            background: rgba(43, 43, 43, 0.4);\n            border-radius: 10px;\n            z-index: 0;\n        }\n        \n        .carousel__list .carousel__item__container .carousel__item > * {\n            position: relative;\n            z-index: 1;\n        }\n        \n        .carousel__list .carousel__item__container .carousel__item-title {\n            font-size: clamp(1.5rem, 3vw, 2.5rem);\n            font-weight: 700;\n            margin-bottom: 1rem;\n            color: #FFFDF3;\n        }\n        \n        .carousel__list .carousel__item__container .carousel__item-text {\n            font-size: clamp(0.875rem, 2vw, 1.125rem);\n            line-height: 1.5;\n            margin-bottom: 1rem;\n        }\n        \n        .carousel__list .carousel__item__container .carousel__item-badge {\n            font-size: clamp(0.75rem, 1.5vw, 0.875rem);\n            padding: 0.5rem 1rem;\n            border-radius: 4px;\n            display: inline-block;\n            margin-bottom: 1rem;\n        }\n        \n        .carousel__list .carousel__item__container .carousel__item-badge--free {\n            background: rgba(71, 86, 79, 0.9);\n        }\n        \n        .carousel__list .carousel__item__container .carousel__item-badge--price {\n            background: rgba(149, 126, 24, 0.9);\n        }\n        \n        .carousel__list .carousel__item__container .carousel__item-meta {\n            font-size: clamp(0.75rem, 1.5vw, 0.875rem);\n            display: flex;\n            flex-direction: column;\n            gap: 0.5rem;\n        }\n        \n        @media (max-width: 1023px) {\n            .carousel__list .carousel__item__container {\n                width: 50%;\n            }\n        }\n        \n        @media (max-width: 767px) {\n            .carousel__list .carousel__item__container {\n                width: 100%;\n            }\n        }\n        \n        /* Navigation arrows */\n        .carousel__arrow {\n            background: none;\n            border: none;\n            cursor: pointer;\n            padding: 10px;\n            transition: transform 0.2s ease;\n        }\n        \n        .carousel__arrow:hover {\n            transform: scale(1.1);\n        }\n        \n        .carousel__arrow:active {\n            transform: scale(0.95);\n        }\n        \n        .carousel__arrow img {\n            width: 40px;\n            height: 40px;\n        }\n        \n        /* Filtre actif */\n        .filtre__list-item--active {\n            background: rgba(149, 126, 24, 0.2);\n            font-weight: 600;\n        }\n    ";
  document.head.appendChild(style); // Initialiser Flickity

  var flkty = new Flickity(carouselList, {
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
    groupCells: false
  }); // Navigation avec les boutons personnalisés

  if (prevButton) {
    prevButton.addEventListener("click", function () {
      flkty.previous();
    });
  }

  if (nextButton) {
    nextButton.addEventListener("click", function () {
      flkty.next();
    });
  } // Gestion des filtres


  filterItems.forEach(function (item) {
    item.addEventListener("click", function (e) {
      e.preventDefault();
      var filterValue = this.dataset.filter; // Retirer la classe active de tous les filtres

      filterItems.forEach(function (f) {
        f.classList.remove("filtre__list-item--active");
      }); // Ajouter la classe active au filtre cliqué

      this.classList.add("filtre__list-item--active"); // Filtrer les slides

      var allContainers = carouselList.querySelectorAll(".carousel__item__container");
      allContainers.forEach(function (container) {
        var itemFilters = container.dataset.filter;

        if (filterValue === "tous") {
          container.style.display = "";
        } else if (itemFilters && itemFilters.includes(filterValue)) {
          container.style.display = "";
        } else {
          container.style.display = "none";
        }
      }); // Recalculer les dimensions de Flickity après le filtrage

      flkty.reloadItems();
      flkty.resize();
    });
  }); // Keyboard navigation

  document.addEventListener("keydown", function (e) {
    if (e.key === "ArrowLeft") {
      flkty.previous();
    } else if (e.key === "ArrowRight") {
      flkty.next();
    }
  });
  console.log("Flickity carousel initialized successfully!");
});