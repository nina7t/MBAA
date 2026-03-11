<?php
/**
 * Script de diagnostic pour la publication de pages WordPress
 */

// Empêcher l'accès direct
if (!defined('ABSPATH')) {
    die('Accès direct non autorisé');
}

// Activer les erreurs pour le debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Tester la création d'une page via API REST
function test_page_creation() {
    echo "<h2>Test de création de page</h2>";

    // Créer un tableau de données pour la page
    $page_data = array(
        'post_title'    => 'Page de test - ' . date('Y-m-d H:i:s'),
        'post_content'  => 'Contenu de test pour diagnostiquer le problème de publication.',
        'post_status'   => 'draft',
        'post_type'     => 'page',
    );

    // Essayer d'insérer la page
    $page_id = wp_insert_post($page_data, true);

    if (is_wp_error($page_id)) {
        echo "<p style='color: red;'>Erreur lors de l'insertion : " . $page_id->get_error_message() . "</p>";
        echo "<pre>";
        print_r($page_id->get_error_data());
        echo "</pre>";
    } else {
        echo "<p style='color: green;'>Page créée avec succès ! ID : " . $page_id . "</p>";

        // Tester la mise à jour
        $update_data = array(
            'ID' => $page_id,
            'post_status' => 'publish'
        );

        $update_result = wp_update_post($update_data, true);

        if (is_wp_error($update_result)) {
            echo "<p style='color: red;'>Erreur lors de la publication : " . $update_result->get_error_message() . "</p>";
        } else {
            echo "<p style='color: green;'>Page publiée avec succès !</p>";
        }
    }
}

// Tester les hooks de sauvegarde
function test_save_hooks() {
    echo "<h2>Test des hooks de sauvegarde</h2>";

    // Tester si les hooks sont définis
    global $wp_filter;

    $hooks_to_check = array('save_post', 'wp_insert_post', 'wp_update_post');

    foreach ($hooks_to_check as $hook) {
        if (isset($wp_filter[$hook])) {
            echo "<p>Hook '$hook' défini avec " . count($wp_filter[$hook]->callbacks) . " callbacks</p>";
        } else {
            echo "<p>Hook '$hook' non défini</p>";
        }
    }
}

// Afficher les informations système
function system_info() {
    echo "<h2>Informations système</h2>";
    echo "<ul>";
    echo "<li>WordPress version : " . get_bloginfo('version') . "</li>";
    echo "<li>PHP version : " . phpversion() . "</li>";
    echo "<li>Thème actif : " . wp_get_theme()->get('Name') . "</li>";
    echo "</ul>";
}

// Exécuter les tests
system_info();
test_save_hooks();
test_page_creation();

?>
