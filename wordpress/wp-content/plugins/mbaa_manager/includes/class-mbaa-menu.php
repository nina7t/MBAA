<?php

//Classe de gestion du menu admin
//NOTE: Gère uniquement l'inscription des menus et leur organisation

//Les méthodes de rendu sont dans class-mbaa-admin.php

if (!defined('ABSPATH')) {
    exit;
}

class MBAA_Menu {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
private function __construct() {
        // Réorganiser les menus - priorité précoce
        add_action('admin_menu', array($this, 'register_menus'), 5);
        
        // Masquer les menus WordPress par défaut - priorité très haute
        add_action('admin_menu', array($this, 'hide_default_menus'), 1);
        
        // Rediriger le tableau de bord WP pour non-Super-Admins
        add_action('load-index.php', array($this, 'redirect_dashboard'));
        
        // Masquer le menu WP Dashboard pour non-Super-Admins
        add_action('admin_menu', array($this, 'hide_wp_dashboard'), 999);
    }
    
  
    // Masquer les menus WordPress par défaut
    
    public function hide_default_menus() {
        // Masquer le menu Articles de WordPress
        remove_menu_page('edit.php');
        
        // Masquer les menus de commentaires, médias
        remove_menu_page('edit-comments.php');
        remove_menu_page('upload.php');
        // NOTE: Menu Pages conservé pour permettre la création des pages WordPress nécessaires
        
        // Masquer le menu Extensions (Plugins) SEULEMENT pour les non-Super Admins
        if (!$this->is_super_admin()) {
            remove_menu_page('plugins.php');
        }
    }
    
  
    // Vérifier si l'utilisateur est Super Admin
    
    private function is_super_admin() {
        return current_user_can('administrator') && current_user_can('mbaa_super_admin');
    }
    
  
    // Vérifier si l'utilisateur peut accéder au plugin
    
    private function can_access_mbaa() {
        return current_user_can('mbaa_can_access_dashboard');
    }
    

    // Rediriger le tableau de bord WP vers MBAA pour non-Super-Admins
    
    public function redirect_dashboard() {
        // Ne pas redirire pour les Super Admins
        if ($this->is_super_admin()) {
            return;
        }
        
        // Ne pas redirire si c'est déjà une page MBAA
        if (isset($_GET['page']) && strpos($_GET['page'], 'mbaa-') === 0) {
            return;
        }
        
        // Ne pas redirire pour les pages AJAX, les installations, etc.
        if (wp_doing_ajax() || defined('IFRAME_REQUEST')) {
            return;
        }
        
        // Obtenir la page actuelle
        $current_page = isset($_GET['page']) ? $_GET['page'] : '';
        
        // Si pas de page spécifiée (dashboard par défaut) ou si c'est index.php
        if (empty($current_page) || $current_page === 'index') {
            wp_redirect(admin_url('admin.php?page=mbaa-dashboard'));
            exit;
        }
    }
    
  
    // Masquer le tableau de bord WP pour non-Super-Admins
    
    public function hide_wp_dashboard() {
        // Pour tous les utilisateurs qui ne sont PAS Super Admin
        if (!$this->is_super_admin()) {
            remove_menu_page('index.php');
        }
    }
    
  
    // Enregistrer tous les menus admin
    
    public function register_menus() {
        // Supprimer d'abord tous les menus MBAA potentiels (pour éviter les doublons)
        remove_menu_page('mbaa-dashboard');
        remove_menu_page('mbaa-artistes');
        remove_menu_page('mbaa-oeuvres');
        remove_menu_page('mbaa-evenements');
        remove_menu_page('mbaa-collections');
        remove_menu_page('mbaa-expositions');
        remove_menu_page('mbaa-statistiques');
        remove_menu_page('mbaa-parametres');
        
        // Vérifier si l'utilisateur peut accéder au plugin
        if (!$this->can_access_mbaa()) {
            return;
        }
        
        // Menu principal "Gestion Musée" - visible pour tous les rôles du plugin
        add_menu_page(
            'Gestion Musée',
            'Gestion Musée',
            'mbaa_can_access_dashboard',
            'mbaa-dashboard',
            array('MBAA_Admin', 'render_dashboard'),
            'dashicons-bank',
            2
        );
        
        // Sous-menu Tableau de bord (page principale)
        add_submenu_page(
            'mbaa-dashboard',
            'Tableau de bord',
            'Tableau de bord',
            'mbaa_can_access_dashboard',
            'mbaa-dashboard',
            array('MBAA_Admin', 'render_dashboard')
        );
        
        // Menu principal Artistes
        add_menu_page(
            'Artistes',
            'Artistes',
            'mbaa_can_access_artistes',
            'mbaa-artistes',
            array('MBAA_Admin', 'render_artistes_page'),
            'dashicons-groups',
            3
        );
        
        // Menu principal Œuvres
        add_menu_page(
            'Œuvres',
            'Œuvres',
            'mbaa_can_access_oeuvres',
            'mbaa-oeuvres',
            array('MBAA_Admin', 'render_oeuvres_page'),
            'dashicons-art',
            4
        );
        
        // Menu principal Agenda
        add_menu_page(
            'Agenda',
            'Agenda',
            'mbaa_can_access_evenements',
            'mbaa-agenda',
            array('MBAA_Admin', 'render_agenda_page'),
            'dashicons-calendar-alt',
            5
        );
        
        // Menu principal Événements
        add_menu_page(
            'Événements',
            'Événements',
            'mbaa_can_access_evenements',
            'mbaa-evenements',
            array('MBAA_Admin', 'render_evenements_page'),
            'dashicons-calendar',
            5
        );
        
        // Menu principal Audioguides
        add_menu_page(
            'Audioguides',
            'Audioguides',
            'mbaa_can_access_audioguides',
            'mbaa-audioguides',
            array('MBAA_Admin', 'render_audioguides_page'),
            'dashicons-headphones',
            6
        );
        
        // Menu principal QR Codes
        add_menu_page(
            'QR Codes',
            'QR Codes',
            'mbaa_can_access_statistiques',
            'mbaa-qrcodes',
            array('MBAA_Admin', 'render_qrcodes_page'),
            'dashicons-qrcode',
            7
        );
        
        // Menu principal Bibliothèque Média
        add_menu_page(
            'Bibliothèque Média',
            'Bibliothèque Média',
            'mbaa_can_access_oeuvres',
            'mbaa-media',
            array('MBAA_Admin', 'render_media_library_page'),
            'dashicons-format-gallery',
            8
        );
        
        // Menu principal Collections
        add_menu_page(
            'Collections',
            'Collections',
            'mbaa_can_access_collections',
            'mbaa-collections',
            array('MBAA_Admin', 'render_collections_page'),
            'dashicons-category',
            9
        );
        
        // Menu principal Expositions
        add_menu_page(
            'Expositions',
            'Expositions',
            'mbaa_can_access_expositions',
            'mbaa-expositions',
            array('MBAA_Admin', 'render_expositions_page'),
            'dashicons-museum',
            10
        );
        
        // Menu principal Outils (Super Admin uniquement)
        if ($this->is_super_admin()) {
            add_menu_page(
                'Administration',
                'Administration',
                'mbaa_super_admin',
                'mbaa-admin',
                '',
                'dashicons-admin-settings',
                11
            );
            
            // Sous-menu Paramètres sous Administration
            add_submenu_page(
                'mbaa-admin',
                'Paramètres',
                'Paramètres',
                'mbaa_super_admin',
                'mbaa-parametres',
                array('MBAA_Admin', 'render_parametres_page')
            );
            
            // Sous-menu Utilisateurs sous Administration
            add_submenu_page(
                'mbaa-admin',
                'Utilisateurs',
                'Utilisateurs',
                'mbaa_super_admin',
                'mbaa-utilisateurs',
                array('MBAA_Admin', 'render_utilisateurs_page')
            );
            
            // Sous-menu Historique sous Administration
            add_submenu_page(
                'mbaa-admin',
                'Historique',
                'Historique',
                'mbaa_super_admin',
                'mbaa-historique',
                array('MBAA_Admin', 'render_historique_page')
            );
            
            // Sous-menu Statistiques sous Administration
            add_submenu_page(
                'mbaa-admin',
                'Statistiques',
                'Statistiques',
                'mbaa_can_access_statistiques',
                'mbaa-statistiques',
                array('MBAA_Admin', 'render_statistiques_page')
            );
            
            // Sous-menu Notifications sous Administration
            add_submenu_page(
                'mbaa-admin',
                'Notifications',
                'Notifications',
                'mbaa_can_access_dashboard',
                'mbaa-notifications',
                array('MBAA_Admin', 'render_notifications_page')
            );
            
            // Sous-menu Œuvres Populaires sous Administration
            add_submenu_page(
                'mbaa-admin',
                'Œuvres Populaires',
                'Œuvres Populaires',
                'mbaa_can_access_statistiques',
                'mbaa-populaires',
                array('MBAA_Admin', 'render_populaires_page')
            );
        }
    }
}

