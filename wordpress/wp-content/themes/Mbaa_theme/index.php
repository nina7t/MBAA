<?php
/**
 * index.php — Fichier obligatoire WordPress
 * Redirige vers le template approprié.
 * Ne pas supprimer ce fichier.
 *
 * @package Mbaa_theme
 */

// Rediriger vers la page d'accueil si accès direct
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// WordPress gère lui-même le routing vers les bons templates.
// Ce fichier est requis par WP mais jamais affiché directement
// si front-page.php, page-xxx.php etc. existent.
get_header();
the_content();
get_footer();