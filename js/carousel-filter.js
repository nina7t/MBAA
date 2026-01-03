// Utiliser Slick pour filtrer directement
$(document).ready(function() {
    const $carousel = $(".carousel__list");
    
    // Ajouter des styles pour les slides Slick
    $('<style>')
        .prop('type', 'text/css')
        .html(`
            .carousel__list .slick-slide {
                padding: 0 0.5rem;
            }
            .carousel__list .slick-slide .carousel__item {
                min-height: clamp(400px, 60vh, 600px);
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
        `)
        .appendTo('head');
    
    // Initialiser Slick avec options pour l'apparence
    $carousel.slick({
        slidesToShow: 4,
        slidesToScroll: 1,
        infinite: false,
        speed: 300,
        variableWidth: false,
        centerMode: false,
        focusOnSelect: true,
        responsive: [{
            breakpoint: 1024,
            settings: {
                slidesToShow: 3,
                slidesToScroll: 1,
                infinite: true,
                dots: true,
            },
        },
        {
            breakpoint: 600,
            settings: {
                slidesToShow: 2,
                slidesToScroll: 1,
            },
        },
        {
            breakpoint: 480,
            settings: {
                slidesToShow: 1,
                slidesToScroll: 1,
            },
        }],
    });

    // Gérer les filtres avec Slick
    $('.filtre__list-item[data-filter]').on('click', function(e) {
        e.preventDefault();
        
        const filterValue = $(this).data('filter');
        
        // Retirer la classe active de tous les filtres
        $('.filtre__list-item').removeClass('filtre__list-item--active');
        
        // Ajouter la classe active au filtre cliqué
        $(this).addClass('filtre__list-item--active');
        
        // Filtrer avec Slick
        if (filterValue === 'tous') {
            $carousel.slick('slickUnfilter');
        } else {
            $carousel.slick('slickFilter', function(index, element) {
                const itemFilters = $(element).data('filter');
                return itemFilters && itemFilters.includes(filterValue);
            });
        }
    });
});

