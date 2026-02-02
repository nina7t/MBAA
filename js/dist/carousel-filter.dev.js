"use strict";

// Utiliser Slick pour filtrer directement
$(document).ready(function () {
  var $carousel = $(".carousel__list"); // Ajouter des styles pour les slides Slick

  $("<style>").prop("type", "text/css").html("\n            .carousel__list .slick-slide {\n                transition: filter 0.4s ease, opacity 0.4s ease, transform 0.4s ease;\n            }\n            \n            /* Slides coup\xE9es (hors centre ET hors visibles principales) */\n            .carousel__list .slick-slide {\n                filter: blur(4px);\n                opacity: 0.5;\n                transform: scale(0.96);\n            }\n            \n            /* Slides visibles (les 3) */\n            .carousel__list .slick-active {\n                filter: none;\n                opacity: 1;\n                transform: scale(1);\n            }\n            \n            .carousel__list .slick-slide {\n                padding: 0 0.5rem;\n            }\n            \n            .carousel__list .slick-slide .carousel__item {\n                min-height: clamp(600px, 65vh, 750px);\n                height: auto;\n                padding: clamp(1.5rem, 4vw, 2.5rem);\n            }\n            \n            .carousel__list .slick-slide .carousel__item-title {\n                color: #FFFDF3;\n            }\n            \n            @media (min-width: 768px) {\n                .carousel__list .slick-slide {\n                    padding: 0 1rem;\n                }\n            }\n            \n            /* Styles pour les dots de navigation des filtres */\n            .filtre__list-item {\n                position: relative;\n            }\n            \n            .filtre__list-item::before {\n                content: '';\n                position: absolute;\n                left: -20px;\n                top: 50%;\n                transform: translateY(-50%);\n                width: 8px;\n                height: 8px;\n                border-radius: 50%;\n                background-color: transparent;\n                opacity: 0;\n                transition: all 0.3s ease;\n            }\n            \n            .filtre__list-item:hover::before {\n                opacity: 0.5;\n                background-color: #1a1a1a;\n            }\n            \n            .filtre__list-item.filtre__list-item--active::before {\n                opacity: 1;\n                background-color: #957e18;\n            }\n            \n            .filtre__list-item:active::before {\n                opacity: 1;\n                background-color: #1a1a1a;\n            }\n            \n            /* Pour le menu de navigation principal */\n            .header__nav-list--main .header__nav-link {\n                position: relative;\n            }\n            \n            .header__nav-list--main .header__nav-link::before {\n                content: '';\n                position: absolute;\n                left: -16px;\n                top: 50%;\n                transform: translateY(-50%);\n                width: 6px;\n                height: 6px;\n                border-radius: 50%;\n                background-color: #957e18;\n                opacity: 0;\n                transition: all 0.3s ease;\n            }\n            \n            .header__nav-list--main .header__nav-link:hover::before {\n                opacity: 1;\n            }\n            \n            .header__nav-list--main .header__nav-link:active::before {\n                opacity: 1;\n                background-color: #1a1a1a;\n            }\n            \n            /* Navigation secondaire */\n            .header__nav-list--secondary .header__nav-link {\n                position: relative;\n            }\n            \n            .header__nav-list--secondary .header__nav-link::before {\n                content: '';\n                position: absolute;\n                left: -12px;\n                top: 50%;\n                transform: translateY(-50%);\n                width: 6px;\n                height: 6px;\n                border-radius: 50%;\n                background-color: #957e18;\n                opacity: 0;\n                transition: all 0.3s ease;\n            }\n            \n            .header__nav-list--secondary .header__nav-link:hover::before {\n                opacity: 1;\n            }\n            \n            .header__nav-list--secondary .header__nav-link:active::before {\n                opacity: 1;\n                background-color: #1a1a1a;\n            }\n        ").appendTo("head"); // Initialiser Slick avec options pour l'apparence

  $carousel.slick({
    slidesToShow: 3,
    slidesToScroll: 1,
    infinite: true,
    speed: 300,
    centerMode: true,
    centerPadding: "120px",
    focusOnSelect: true,
    arrows: true,
    prevArrow: $(".carousel__arrow--prev"),
    nextArrow: $(".carousel__arrow--next"),
    dots: false,
    responsive: [{
      breakpoint: 1440,
      settings: {
        slidesToShow: 3,
        centerPadding: "80px"
      }
    }, {
      breakpoint: 1024,
      settings: {
        slidesToShow: 2,
        centerPadding: "60px"
      }
    }, {
      breakpoint: 768,
      settings: {
        slidesToShow: 1,
        centerPadding: "40px"
      }
    }]
  }); // Gérer les filtres avec Slick

  $(".filtre__list-item[data-filter]").on("click", function (e) {
    e.preventDefault();
    var filterValue = $(this).data("filter"); // Retirer la classe active de tous les filtres

    $(".filtre__list-item").removeClass("filtre__list-item--active"); // Ajouter la classe active au filtre cliqué

    $(this).addClass("filtre__list-item--active"); // Filtrer avec Slick

    if (filterValue === "tous") {
      $carousel.slick("slickUnfilter");
    } else {
      $carousel.slick("slickFilter", function (index, element) {
        var itemFilters = $(element).data("filter");
        return itemFilters && itemFilters.includes(filterValue);
      });
    }
  });
});