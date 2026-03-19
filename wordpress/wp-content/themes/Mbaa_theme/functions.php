
<?php
/**
 * functions.php — Thème Mbaa_theme
 * Chargement des assets et configuration de base.
 *
 * @package Mbaa_theme
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// ── Support du thème ──────────────────────────────────────────────────────
add_action( 'after_setup_theme', function () {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', [ 'search-form', 'comment-form', 'gallery', 'caption' ] );
} );

// ── Chargement des styles et scripts ─────────────────────────────────────
add_action( 'wp_enqueue_scripts', function () {

    $theme_uri = get_template_directory_uri();
    $version   = '1.0.0';

    // ── CSS ──────────────────────────────────────────────────────────────

    // Ton CSS compilé depuis SASS (le vrai style du site)
    // IMPORTANT : il remplace le style.css WP vide
    wp_enqueue_style(
        'mbaa-main-style',
   $theme_uri . '/style.css',
        [],
        $version
    );

    // Fonts
    wp_enqueue_style(
        'mbaa-font-styrene',
        'https://db.onlinewebfonts.com/c/5d86f2e8ccc2811b9392aa03b7ce4a63?family=Styrene+B+Regular+Regular',
        [],
        null
    );

    // Leaflet CSS (carte)
    wp_enqueue_style(
        'leaflet',
        'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css',
        [],
        '1.9.4'
    );


    // ── JS ───────────────────────────────────────────────────────────────

    // jQuery (déjà inclus dans WP, on le dé-enregistre pour utiliser le nôtre si besoin)
    // wp_deregister_script('jquery'); // décommenter seulement si conflit

    // Leaflet JS
    wp_enqueue_script(
        'leaflet',
        'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js',
        [],
        '1.9.4',
        true
    );


    // Scripts custom du thème
    wp_enqueue_script( 'mbaa-menu',            $theme_uri . '/js/menu.js',            [], $version, true );
    wp_enqueue_script( 'mbaa-header-adaptive', $theme_uri . '/js/header-adaptive.js', [], $version, true );
    // wp_enqueue_script( 'mbaa-carousel-filter', $theme_uri . '/js/carousel-filter.js', [ 'jquery', 'slick' ], $version, true ); // Désactivé - utilisation de Flickity
    
    // Swiper Carousel
    wp_enqueue_style('swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', [], null);
    wp_enqueue_script('swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', [], null, true);
    wp_enqueue_script('carousel', $theme_uri . '/js/carousel-swiper.js', ['swiper'], null, true);

    // Chargés conditionnellement selon la page
    if ( is_page_template( 'page-collections.php' ) ) {
        wp_enqueue_script( 'mbaa-art-gallery', $theme_uri . '/js/art-gallery.js', [], $version, true );
        wp_enqueue_script( 'mbaa-pedagogical',  $theme_uri . '/js/pedagogical.js',  [], $version, true );
    }

    // Script pour la page Le Musée
    if ( is_page_template( 'page-le-musee.php' ) ) {
        wp_enqueue_script( 'mbaa-musee', $theme_uri . '/js/musee.js', [], $version, true );
    }

    // Scripts pour la fiche œuvre
    if ( is_page_template( 'single-oeuvre.php' ) ) {
        wp_enqueue_script( 'mbaa-menu', $theme_uri . '/js/menu.js', [], $version, true );
        wp_enqueue_script( 'mbaa-qr-code', $theme_uri . '/js/qr-code.js', [], $version, true );
    }

} );

// ── Redirection connexion → page WP login ────────────────────────────────
// Si quelqu'un va sur /connexion/ directement, WP gère la page de login
add_filter( 'login_redirect', function ( $redirect_to, $request, $user ) {
    if ( isset( $user->roles ) && is_array( $user->roles ) ) {
        if ( in_array( 'administrator', $user->roles ) ) {
            return admin_url();
        }
        return home_url( '/visiteur/' );
    }
    return $redirect_to;
}, 10, 3 );

// ── Action newsletter (admin-post) ────────────────────────────────────────
add_action( 'admin_post_mbaa_newsletter_subscribe',        'mbaa_handle_newsletter' );
add_action( 'admin_post_nopriv_mbaa_newsletter_subscribe', 'mbaa_handle_newsletter' );

function mbaa_handle_newsletter() {
    if ( ! isset( $_POST['mbaa_newsletter_nonce'] )
        || ! wp_verify_nonce( $_POST['mbaa_newsletter_nonce'], 'mbaa_newsletter' ) ) {
        wp_die( 'Sécurité invalide.' );
    }

    $email = sanitize_email( $_POST['email'] ?? '' );

    if ( is_email( $email ) ) {
        // Stocker l'email (option WP simple — à remplacer par un vrai système si besoin)
        $subscribers = get_option( 'mbaa_newsletter_subscribers', [] );
        if ( ! in_array( $email, $subscribers ) ) {
            $subscribers[] = $email;
            update_option( 'mbaa_newsletter_subscribers', $subscribers );
        }
    }

    wp_redirect( wp_get_referer() ?: home_url('/') );
    exit;
}

add_action( 'after_setup_theme', function () {
    register_nav_menus( [
        'primary' => 'Menu principal',
        'footer'  => 'Menu footer',
    ] );
} );