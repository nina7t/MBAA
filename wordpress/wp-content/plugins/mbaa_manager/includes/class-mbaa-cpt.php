<?php
/**
 * MBAA - Configuration des Custom Post Types
 * Crée les CPT pour les œuvres et artistes via CPT UI
 */

if (!defined('ABSPATH')) {
    exit;
}

class MBAA_CPT {
    
    /**
     * Initialiser les CPT
     */
    public static function init() {
        add_action('init', array(__CLASS__, 'register_cpts'), 0);
    }
    
    /**
     * Enregistrer les Custom Post Types
     */
    public static function register_cpts() {
        // CPT pour les Œuvres
        register_post_type('oeuvre', array(
            'label' => 'Œuvres',
            'labels' => array(
                'name' => 'Œuvres',
                'singular_name' => 'Œuvre',
                'menu_name' => 'Œuvres',
                'all_items' => 'Toutes les œuvres',
                'add_new' => 'Ajouter une œuvre',
                'add_new_item' => 'Ajouter une nouvelle œuvre',
                'edit_item' => 'Modifier l\'œuvre',
                'new_item' => 'Nouvelle œuvre',
                'view_item' => 'Voir l\'œuvre',
                'search_items' => 'Rechercher des œuvres',
                'not_found' => 'Aucune œuvre trouvée',
                'not_found_in_trash' => 'Aucune œuvre dans la corbeille',
                'parent_item_colon' => 'Œuvre parente:',
            ),
            'description' => 'Gestion des œuvres du musée',
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => false, // Masqué du menu principal, géré par notre plugin
            'query_var' => true,
            'rewrite' => array('slug' => 'oeuvre'),
            'capability_type' => 'post',
            'has_archive' => 'oeuvres',
            'hierarchical' => false,
            'menu_position' => 25,
            'menu_icon' => 'dashicons-art',
            'supports' => array(
                'title',
                'thumbnail',
                'excerpt',
            ),
            'show_in_rest' => true,
        ));
        
        // CPT pour les Artistes
        register_post_type('artiste', array(
            'label' => 'Artistes',
            'labels' => array(
                'name' => 'Artistes',
                'singular_name' => 'Artiste',
                'menu_name' => 'Artistes',
                'all_items' => 'Tous les artistes',
                'add_new' => 'Ajouter un artiste',
                'add_new_item' => 'Ajouter un nouvel artiste',
                'edit_item' => 'Modifier l\'artiste',
                'new_item' => 'Nouvel artiste',
                'view_item' => 'Voir l\'artiste',
                'search_items' => 'Rechercher des artistes',
                'not_found' => 'Aucun artiste trouvé',
                'not_found_in_trash' => 'Aucun artiste dans la corbeille',
                'parent_item_colon' => 'Artiste parent:',
            ),
            'description' => 'Gestion des artistes du musée',
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => false, // Masqué du menu principal, géré par notre plugin
            'query_var' => true,
            'rewrite' => array('slug' => 'artiste'),
            'capability_type' => 'post',
            'has_archive' => 'artistes',
            'hierarchical' => false,
            'menu_position' => 26,
            'menu_icon' => 'dashicons-groups',
            'supports' => array(
                'title',
                'thumbnail',
                'excerpt',
            ),
            'show_in_rest' => true,
        ));
    }
    
    /**
     * Mettre à jour les règles de rewrite après activation
     */
    public static function flush_rewrite_rules() {
        self::register_cpts();
        flush_rewrite_rules();
    }
}

// Initialiser
MBAA_CPT::init();
