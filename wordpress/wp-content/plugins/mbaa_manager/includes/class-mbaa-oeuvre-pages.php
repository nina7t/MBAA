<?php
/**
 * MBAA - Système de pages dédiées pour les œuvres
 * Crée des URLs uniques pour chaque œuvre et génère les QR codes automatiquement
 */

if (!defined('ABSPATH')) {
    exit;
}

class MBAA_Oeuvre_Pages {
    
    /**
     * Initialiser le système
     */
    public static function init() {
        // Ajouter les règles de rewrite
        add_action('init', array(__CLASS__, 'add_rewrite_rules'));
        
        // Gérer les requêtes pour les pages d'œuvres
        add_filter('query_vars', array(__CLASS__, 'add_query_vars'));
        add_action('parse_request', array(__CLASS__, 'parse_request'));
        
        // Charger le template
        add_filter('template_include', array(__CLASS__, 'load_oeuvre_template'));
        
        // Ajouter les Meta boxes pour l'URL publique
        add_action('add_meta_boxes', array(__CLASS__, 'add_meta_boxes'));
        add_action('save_post', array(__CLASS__, 'save_meta_boxes'));
        
        // Ajouter le lien vers la page publique dans la liste
        add_filter('post_row_actions', array(__CLASS__, 'add_view_link'), 10, 2);
    }
    
    /**
     * Ajouter les règles de rewrite pour les œuvres
     */
    public static function add_rewrite_rules() {
        add_rewrite_rule('^oeuvre/([0-9]+)(?:/[^/]+)?/?$', 'index.php?mbaa_oeuvre_id=$matches[1]', 'top');
        
        // Regel: /oeuvres -> liste des œuvres (optionnel)
        add_rewrite_rule('^oeuvres/?$', 'index.php?mbaa_oeuvres_list=1', 'top');
    }
    
    /**
     * Ajouter les variables de requête personnalisées
     */
    public static function add_query_vars($vars) {
        $vars[] = 'mbaa_oeuvre_id';
        $vars[] = 'mbaa_oeuvres_list';
        return $vars;
    }
    
    /**
     * Gérer les requêtes
     */
    public static function parse_request($wp) {
        // Page individuelle d'une œuvre
        if (!empty($wp->query_vars['mbaa_oeuvre_id'])) {
            $wp->set_query_var('mbaa_is_oeuvre_page', true);
        }
        
        // Liste des œuvres
        if (!empty($wp->query_vars['mbaa_oeuvres_list'])) {
            $wp->set_query_var('mbaa_is_oeuvres_list', true);
        }
    }
    
    /**
     * Charger le template approprié
     */
    public static function load_oeuvre_template($template) {
        global $wp;
        
        // Page individuelle d'une œuvre
        if (get_query_var('mbaa_is_oeuvre_page')) {
            $oeuvre_id = get_query_var('mbaa_oeuvre_id');
            
            // Charger le template depuis le plugin
            $plugin_template = MBAA_PLUGIN_DIR . 'views/public/oeuvre-single.php';
            if (file_exists($plugin_template)) {
                return $plugin_template;
            }
        }
        
        // Liste des œuvres
        if (get_query_var('mbaa_is_oeuvres_list')) {
            $plugin_template = MBAA_PLUGIN_DIR . 'views/public/oeuvres-list.php';
            if (file_exists($plugin_template)) {
                return $plugin_template;
            }
        }
        
        return $template;
    }
    
    /**
     * Ajouter des meta boxes pour l'URL publique
     */
    public static function add_meta_boxes() {
        // Pour le moment on n'ajoute pas de CPT donc pas de meta boxes
        // Cette fonction sera utile si on convertit en CPT plus tard
    }
    
    /**
     * Sauvegarder les meta boxes
     */
    public static function save_meta_boxes($post_id) {
        // Pour le moment pas de CPT donc pas de meta boxes
    }
    
    /**
     * Ajouter un lien "Voir" dans la liste des œuvres
     */
    public static function add_view_link($actions, $post) {
        // Pour le moment on n'utilise pas de CPT
        return $actions;
    }
    
    /**
     * Obtenir l'URL publique d'une œuvre
     */
    public static function get_oeuvre_url($oeuvre_id) {
        global $wpdb;
        $db = new MBAA_Database();
        $titre = $wpdb->get_var(
            $wpdb->prepare("SELECT titre FROM {$db->table_oeuvre} WHERE id_oeuvre = %d", $oeuvre_id)
        );

        $slug = $titre ? sanitize_title($titre) : '';

        if ($slug) {
            return home_url('/oeuvre/' . $oeuvre_id . '/' . $slug . '/');
        }

        return home_url('/oeuvre/' . $oeuvre_id . '/');
    }
    
    /**
     * Obtenir les données d'une œuvre pour l'affichage public (compatibilité CPT/ACF)
     */
    public static function get_oeuvre_data($oeuvre_id) {
        // Essayer d'abord depuis les CPT si ACF est activé
        if (function_exists('get_field') && post_type_exists('oeuvre')) {
            return self::get_oeuvre_data_from_cpt($oeuvre_id);
        }
        
        // Fallback vers les tables personnalisées (ancien système)
        return self::get_oeuvre_data_from_tables($oeuvre_id);
    }
    
    /**
     * Obtenir les données depuis les CPT/ACF
     */
    private static function get_oeuvre_data_from_cpt($oeuvre_id) {
        $post = get_post($oeuvre_id);
        
        if (!$post || $post->post_type !== 'oeuvre') {
            return null;
        }
        
        // Récupérer les champs ACF
        $data = array(
            'id_oeuvre' => $oeuvre_id,
            'titre' => $post->post_title,
            'description' => get_field('mbaa_description', $oeuvre_id),
            'date_creation' => get_field('mbaa_date_creation', $oeuvre_id),
            'provenance' => get_field('mbaa_provenance', $oeuvre_id),
            'prix_estime' => get_field('mbaa_prix_estime', $oeuvre_id),
            'technique' => get_field('mbaa_technique', $oeuvre_id),
            'dimensions' => get_field('mbaa_dimensions', $oeuvre_id),
            'support' => get_field('mbaa_support', $oeuvre_id),
            'epoque' => get_field('mbaa_epoque', $oeuvre_id),
            'salle' => get_field('mbaa_salle', $oeuvre_id),
            'medium' => get_field('mbaa_medium', $oeuvre_id),
            'mouvement' => get_field('mbaa_mouvement', $oeuvre_id),
            'categorie' => get_field('mbaa_categorie', $oeuvre_id),
            'image_url' => get_the_post_thumbnail_url($oeuvre_id, 'large'),
        );
        
        // Récupérer les infos de l'artiste
        $artiste_id = get_field('mbaa_artiste_id', $oeuvre_id);
        if ($artiste_id) {
            $artiste_post = get_post($artiste_id);
            if ($artiste_post) {
                $data['artiste_nom'] = $artiste_post->post_title;
                $data['artiste_biographie'] = get_field('mbaa_biographie', $artiste_id);
                $data['artiste_image'] = get_the_post_thumbnail_url($artiste_id, 'large');
            }
        }
        
        return $data;
    }
    
    /**
     * Obtenir les données depuis les tables personnalisées (ancien système)
     */
    private static function get_oeuvre_data_from_tables($oeuvre_id) {
        global $wpdb;
        $db = new MBAA_Database();
        
        $sql = "
            SELECT 
                o.*,
                a.nom AS artiste_nom,
                a.biographie AS artiste_biographie,
                a.image_url AS artiste_image,
                e.nom_epoque AS epoque_nom,
                s.nom_salle AS salle_nom,
                s.etage AS salle_etage,
                m.nom_medium AS medium_nom,
                mo.nom_mouvement AS mouvement_nom,
                c.nom_categorie AS categorie_nom
            FROM {$db->table_oeuvre} o
            LEFT JOIN {$db->table_artiste} a ON o.id_artiste = a.id_artiste
            LEFT JOIN {$db->table_epoque} e ON o.id_epoque = e.id_epoque
            LEFT JOIN {$db->table_salle} s ON o.id_salle = s.id_salle
            LEFT JOIN {$db->table_medium} m ON o.id_medium = m.id_medium
            LEFT JOIN {$db->table_mouvement} mo ON o.id_mouvement = mo.id_mouvement
            LEFT JOIN {$db->table_categorie} c ON o.id_categorie = c.id_categorie
            WHERE o.id_oeuvre = %d
        ";
        
        return $wpdb->get_row($wpdb->prepare($sql, $oeuvre_id), ARRAY_A);
    }
}

// Initialiser
MBAA_Oeuvre_Pages::init();

