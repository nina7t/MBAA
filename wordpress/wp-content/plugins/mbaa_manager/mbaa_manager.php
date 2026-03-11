<?php
/**
 * Plugin Name: MBAA Gestion Musée
 * Plugin URI: https://musee-des-beaux-arts-besançon.com
 * Description: Plugin de gestion complète pour musée (Œuvres, Artistes, Événements, Audioguides)
 * Version: 2.0
 * Author: Nina Tonnaire
 * Author URI: https://monsite.com
 * License: GPL2
 * Text Domain: mbaa
 */

// Sécurité : empêche l'accès direct au fichier

if (!defined('ABSPATH')) {
    exit;
}

// On définis les constantes du plugin

define('MBAA_VERSION', '2.0');
define('MBAA_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('MBAA_PLUGIN_URL', plugin_dir_url(__FILE__));
define('MBAA_PLUGIN_FILE', __FILE__);


// on charges les différentes dépendances

require_once MBAA_PLUGIN_DIR . 'includes/class-mbaa-database.php';
require_once MBAA_PLUGIN_DIR . 'includes/class-mbaa-capabilities.php';
require_once MBAA_PLUGIN_DIR . 'includes/class-mbaa-menu.php';
require_once MBAA_PLUGIN_DIR . 'includes/class-mbaa-admin.php';
require_once MBAA_PLUGIN_DIR . 'includes/class-mbaa-artiste.php';
require_once MBAA_PLUGIN_DIR . 'includes/class-mbaa-oeuvre.php';
require_once MBAA_PLUGIN_DIR . 'includes/class-mbaa-evenement.php';
require_once MBAA_PLUGIN_DIR . 'includes/class-mbaa-audioguide.php';
require_once MBAA_PLUGIN_DIR . 'includes/class-mbaa-uppy-integation.php';
require_once MBAA_PLUGIN_DIR . 'includes/class-mbaa-qr-generator.php';
require_once MBAA_PLUGIN_DIR . 'includes/class-mbaa-oeuvre-pages.php';
require_once MBAA_PLUGIN_DIR . 'includes/class-mbaa-contact.php';
require_once MBAA_PLUGIN_DIR . 'includes/class-mbaa-pdf-generator.php';
// require_once MBAA_PLUGIN_DIR . 'includes/class-mbaa-acf-fields.php'; // Désactivé - on passe par l'interface
 require_once MBAA_PLUGIN_DIR . 'includes/class-mbaa-cpt.php';
// Vérifier et ajouter les capacités si elles n'existent pas encore
add_action('init', 'mbaa_check_capabilities', 1);

function mbaa_check_capabilities() {
    // Vérifier si les capacités existent déjà
    $admin = get_role('administrator');
    if ($admin && !$admin->has_cap('mbaa_super_admin')) {
        MBAA_Capabilities::add_capabilities();
    }
}


// Classe principale du plugin

class MBAA_Plugin {
    
    private static $instance = null;
    private $database;
    private $admin;
    
  
    // Singleton

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
  
// Constructeur
   
    private function __construct() {
        $this->database = new MBAA_Database();
        $this->admin = new MBAA_Admin();
        
        // Initialiser le nouveau système de menu
        MBAA_Menu::get_instance();
        
        // Initialiser le formulaire de contact
        new MBAA_Contact();
        
        // Masquer les menus par défaut du thème quand ce plugin est actif
        add_action('admin_menu', array($this, 'hide_default_theme_menus'), 999);
        
        // Hooks d'activation/désactivation
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        
        // Actions
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }
    
    /**
     * Masquer les menus par défaut du thème (Artistes, Œuvres, Événements)
     */
    public function hide_default_theme_menus() {
        // Ne masquer que si les CPT existent (après installation ACF)
        if (post_type_exists('artiste')) {
            remove_menu_page('edit.php?post_type=artiste');
        }
        if (post_type_exists('oeuvre')) {
            remove_menu_page('edit.php?post_type=oeuvre');
        }
        if (post_type_exists('evenement')) {
            remove_menu_page('edit.php?post_type=evenement');
        }
    }
    
  
// Activation du plugin

    public function activate() {
        $this->database->create_tables();
        MBAA_Capabilities::init(); // Initialiser les capacités et rôles
        // MBAA_CPT::flush_rewrite_rules(); // Désactivé - on passe par CPT UI
        flush_rewrite_rules();
    }
    
  
    // Désactivation du plugin

    public function deactivate() {
        flush_rewrite_rules();
    }
    
  
// Charger les scripts admin
 
    public function enqueue_admin_scripts($hook) {
        // Charger le CSS sur toutes les pages admin pour le menu
        wp_enqueue_style(
            'mbaa-admin-style',
            MBAA_PLUGIN_URL . 'assets/css/admin-style.css',
            array(),
            MBAA_VERSION
        );
        
        // Charger les scripts MBAA seulement sur les pages du plugin
        if (strpos($hook, 'mbaa') === false) {
            // Retourner mais garder le CSS chargé
            return;
        }

        // Charger l'uploader de média de WordPress
        wp_enqueue_media();
        
        // Google Fonts Inter
        wp_enqueue_style(
            'mbaa-google-fonts',
            'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap',
            array(),
            MBAA_VERSION
        );
        
        // Ré-enqueue le style admin après les fonts (pour la priorité)
        wp_enqueue_style(
            'mbaa-admin-style-main',
            MBAA_PLUGIN_URL . 'assets/css/admin-style.css',
            array('mbaa-google-fonts'),
            MBAA_VERSION
        );
        
        // Charger les scripts et Uppy
        $this->enqueue_uppy_scripts();
        
        // Charger QRCode.js pour la génération de QR codes
        wp_enqueue_script(
            'qrcode-js',
            'https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js',
            array(),
            '1.5.3',
            true
        );
        
        wp_enqueue_script(
            'mbaa-admin-script',
            MBAA_PLUGIN_URL . 'assets/js/admin-script.js',
            array('jquery'),
            MBAA_VERSION,
            true
        );
        
        // Localiser le script avec les données AJAX (inclure les deux nonces)
        wp_localize_script('mbaa-admin-script', 'mbaaAjax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('mbaa_nonce'),
            'upload_nonce' => wp_create_nonce('mbaa_upload_nonce'),
            'strings' => array(
                'uploadSuccess' => __('Fichier téléversé avec succès !', 'mbaa'),
                'uploadError' => __('Erreur lors du téléversement.', 'mbaa'),
                'fileTooLarge' => __('Le fichier est trop volumineux.', 'mbaa'),
                'invalidFileType' => __('Type de fichier non autorisé.', 'mbaa'),
            )
        ));
    }
    
    /**
     * Charger les scripts et styles Uppy
     */
    private function enqueue_uppy_scripts() {
        $uppy_version = '3.21.0';
        
        // Styles
        wp_enqueue_style('uppy-core', "https://cdn.jsdelivr.net/npm/@uppy/core@{$uppy_version}/dist/style.min.css", array(), $uppy_version);
        wp_enqueue_style('uppy-dashboard', "https://cdn.jsdelivr.net/npm/@uppy/dashboard@{$uppy_version}/dist/style.min.css", array('uppy-core'), $uppy_version);
        
        // Scripts
        wp_enqueue_script('uppy-core', "https://cdn.jsdelivr.net/npm/@uppy/core@{$uppy_version}/dist/Uppy.min.js", array(), $uppy_version, true);
        wp_enqueue_script('uppy-dashboard', "https://cdn.jsdelivr.net/npm/@uppy/dashboard@{$uppy_version}/dist/Dashboard.min.js", array('uppy-core'), $uppy_version, true);
        wp_enqueue_script('uppy-xhr-upload', "https://cdn.jsdelivr.net/npm/@uppy/xhr-upload@{$uppy_version}/dist/XHRUpload.min.js", array('uppy-core'), $uppy_version, true);
    }
}

// Initialiser le plugin
function mbaa_init() {
    return MBAA_Plugin::get_instance();
}

// Démarrer le plugin
add_action('plugins_loaded', 'mbaa_init');

