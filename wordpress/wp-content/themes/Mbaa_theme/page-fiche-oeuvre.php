<?php
/**
 * Template Name: Page Fiche Œuvre
 * Affiche une fiche œuvre dynamique depuis l'ID passé en paramètre
 */

if (!defined('ABSPATH')) {
    exit;
}

// Récupérer l'ID de l'œuvre depuis l'URL
$id_oeuvre = isset($_GET['oeuvre_id']) ? intval($_GET['oeuvre_id']) : 0;

// Si pas d'ID, rediriger vers la page collections
if (!$id_oeuvre) {
    wp_redirect(home_url('/collections/'));
    exit;
}

// Inclure le template single-oeuvre qui contient toute la logique
include get_template_directory() . '/single-oeuvre.php';
