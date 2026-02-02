// Utiliser Slick pour filtrer directement

$(document).ready(function () {
  const $carousel = $(".carousel__list");

  // Ajouter des styles pour les slides Slick
  $("<style>")
    .prop("type", "text/css")
    .html(
      `
            .carousel__list .slick-slide {
                transition: filter 0.4s ease, opacity 0.4s ease, transform 0.4s ease;
            }
            
            /* Slides coupées (hors centre ET hors visibles principales) */
            .carousel__list .slick-slide {
                filter: blur(4px);
                opacity: 0.5;
                transform: scale(0.96);
            }
            
            /* Slides visibles (les 3) */
            .carousel__list .slick-active {
                filter: none;
                opacity: 1;
                transform: scale(1);
            }
            
            .carousel__list .slick-slide {
                padding: 0 0.5rem;
            }
            
            .carousel__list .slick-slide .carousel__item {
                min-height: clamp(600px, 65vh, 750px);
                height: auto;
                padding: clamp(1.5rem, 4vw, 2.5rem);
            }
            
            .carousel__list .slick-slide .carousel__item-title {
                color: #FFFDF3;
            }
            
            @media (min-width: 768px) {
                .carousel__list .slick-slide {
                    padding: 0 1rem;
                }
            }
            
            /* Styles pour les dots de navigation des filtres */
            .filtre__list-item {
                position: relative;
            }
            
            .filtre__list-item::before {
                content: '';
                position: absolute;
                left: -20px;
                top: 50%;
                transform: translateY(-50%);
                width: 8px;
                height: 8px;
                border-radius: 50%;
                background-color: transparent;
                opacity: 0;
                transition: all 0.3s ease;
            }
            
            .filtre__list-item:hover::before {
                opacity: 0.5;
                background-color: #1a1a1a;
            }
            
            .filtre__list-item.filtre__list-item--active::before {
                opacity: 1;
                background-color: #957e18;
            }
            
            .filtre__list-item:active::before {
                opacity: 1;
                background-color: #1a1a1a;
            }
            
            /* Pour le menu de navigation principal */
            .header__nav-list--main .header__nav-link {
                position: relative;
            }
            
            .header__nav-list--main .header__nav-link::before {
                content: '';
                position: absolute;
                left: -16px;
                top: 50%;
                transform: translateY(-50%);
                width: 6px;
                height: 6px;
                border-radius: 50%;
                background-color: #957e18;
                opacity: 0;
                transition: all 0.3s ease;
            }
            
            .header__nav-list--main .header__nav-link:hover::before {
                opacity: 1;
            }
            
            .header__nav-list--main .header__nav-link:active::before {
                opacity: 1;
                background-color: #1a1a1a;
            }
            
            /* Navigation secondaire */
            .header__nav-list--secondary .header__nav-link {
                position: relative;
            }
            
            .header__nav-list--secondary .header__nav-link::before {
                content: '';
                position: absolute;
                left: -12px;
                top: 50%;
                transform: translateY(-50%);
                width: 6px;
                height: 6px;
                border-radius: 50%;
                background-color: #957e18;
                opacity: 0;
                transition: all 0.3s ease;
            }
            
            .header__nav-list--secondary .header__nav-link:hover::before {
                opacity: 1;
            }
            
            .header__nav-list--secondary .header__nav-link:active::before {
                opacity: 1;
                background-color: #1a1a1a;
            }
        `,
    )
    .appendTo("head");

  // Initialiser Slick avec options pour l'apparence
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

    responsive: [
      {
        breakpoint: 1440,
        settings: {
          slidesToShow: 3,
          centerPadding: "80px",
        },
      },
      {
        breakpoint: 1024,
        settings: {
          slidesToShow: 2,
          centerPadding: "60px",
        },
      },
      {
        breakpoint: 768,
        settings: {
          slidesToShow: 1,
          centerPadding: "40px",
        },
      },
    ],
  });

  // Gérer les filtres avec Slick
  $(".filtre__list-item[data-filter]").on("click", function (e) {
    e.preventDefault();

    const filterValue = $(this).data("filter");

    // Retirer la classe active de tous les filtres
    $(".filtre__list-item").removeClass("filtre__list-item--active");

    // Ajouter la classe active au filtre cliqué
    $(this).addClass("filtre__list-item--active");

    // Filtrer avec Slick

    if (filterValue === "tous") {
      $carousel.slick("slickUnfilter");
    } else {
      $carousel.slick("slickFilter", function (index, element) {
        const itemFilters = $(element).data("filter");
        return itemFilters && itemFilters.includes(filterValue);
      });
    }
  });
});
