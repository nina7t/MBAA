<?php

// Classe de gestion des capacités et rôles


if (!defined('ABSPATH')) {
    exit;
}

class MBAA_Capabilities {
    
    
    // Initialiser les capacités
    
    public static function init() {
        // Ajouter les capacités à l'activation du plugin
        register_activation_hook(MBAA_PLUGIN_FILE, array(__CLASS__, 'add_capabilities'));
        
        // Retirer les capacités à la désactivation
        register_deactivation_hook(MBAA_PLUGIN_FILE, array(__CLASS__, 'remove_capabilities'));
    }
    
    
    // Ajouter les capacités pour le plugin
    
    public static function add_capabilities() {
        // Obtenir le rôle Administrator
        $admin = get_role('administrator');
        
        if ($admin) {
            // Capacités pour le Super Admin (toutes les capacités)
            $admin->add_cap('mbaa_super_admin');
            $admin->add_cap('mbaa_can_access_dashboard');
            $admin->add_cap('mbaa_can_access_artistes');
            $admin->add_cap('mbaa_can_access_oeuvres');
            $admin->add_cap('mbaa_can_access_evenements');
            $admin->add_cap('mbaa_can_access_audioguides');
            $admin->add_cap('mbaa_can_access_collections');
            $admin->add_cap('mbaa_can_access_expositions');
            $admin->add_cap('mbaa_can_access_statistiques');
            $admin->add_cap('mbaa_can_access_parametres');
            $admin->add_cap('mbaa_can_validate');
            $admin->add_cap('mbaa_can_export_import');
            $admin->add_cap('mbaa_can_manage_users');
        }
        
        // Créer le rôle Gestionnaire de contenu
        self::create_gestionnaire_role();
        
        // Créer le rôle Contributeur musée
        self::create_contributeur_role();
    }
    
    
    // Retirer les capacités
    
    public static function remove_capabilities() {
        // Obtenir le rôle Administrator
        $admin = get_role('administrator');
        
        if ($admin) {
            $admin->remove_cap('mbaa_super_admin');
            $admin->remove_cap('mbaa_can_access_dashboard');
            $admin->remove_cap('mbaa_can_access_artistes');
            $admin->remove_cap('mbaa_can_access_oeuvres');
            $admin->remove_cap('mbaa_can_access_evenements');
            $admin->remove_cap('mbaa_can_access_audioguides');
            $admin->remove_cap('mbaa_can_access_collections');
            $admin->remove_cap('mbaa_can_access_expositions');
            $admin->remove_cap('mbaa_can_access_statistiques');
            $admin->remove_cap('mbaa_can_access_parametres');
            $admin->remove_cap('mbaa_can_validate');
            $admin->remove_cap('mbaa_can_export_import');
            $admin->remove_cap('mbaa_can_manage_users');
        }
        
        // Supprimer le rôle Gestionnaire de contenu
        remove_role('mbaa_gestionnaire');
        
        // Supprimer le rôle Contributeur musée
        remove_role('mbaa_contributeur');
    }
    




    // Créer le rôle Gestionnaire de contenu

    private static function create_gestionnaire_role() {
        // Supprimer le rôle s'il existe déjà
        remove_role('mbaa_gestionnaire');
        
        // Ajouter le rôle avec les capacités
        add_role(
            'mbaa_gestionnaire',
            'Gestionnaire de contenu',
            array(
                // Lecture de base
                'read' => true,
                
                // Accès au plugin
                'mbaa_can_access_dashboard' => true,
                'mbaa_can_access_artistes' => true,
                'mbaa_can_access_oeuvres' => true,
                'mbaa_can_access_evenements' => true,
                'mbaa_can_access_audioguides' => true,
                'mbaa_can_access_collections' => true,
                'mbaa_can_access_expositions' => true,
                'mbaa_can_access_statistiques' => true,
                
                // Peut créer et modifier (sans validation complète)
                'upload_files' => true,
                'edit_posts' => true,
                'edit_others_posts' => false,
                'delete_posts' => false,
                'publish_posts' => false,
            )
        );
    }
    
    
    // Créer le rôle Contributeur musée
    
    private static function create_contributeur_role() {
        // Supprimer le rôle s'il existe déjà
        remove_role('mbaa_contributeur');
        
        // Ajouter le rôle avec les capacités limitées
        add_role(
            'mbaa_contributeur',
            'Contributeur musée',
            array(
                // Lecture de base
                'read' => true,
                
                // Accès au plugin (tableau de bord uniquement)
                'mbaa_can_access_dashboard' => true,
                'mbaa_can_access_artistes' => true,
                'mbaa_can_access_oeuvres' => true,
                'mbaa_can_access_evenements' => true,
                'mbaa_can_access_audioguides' => true,
                'mbaa_can_access_collections' => true,
                'mbaa_can_access_statistiques' => true,
                
                // Capacités limitées (peut soumettre pour validation)
                'upload_files' => true,
                'edit_posts' => true,
                'edit_others_posts' => false,
                'delete_posts' => false,
                'publish_posts' => false,
            )
        );
    }
    
    
    // Vérifier si l'utilisateur est Super Admin
    
    public static function is_super_admin($user = null) {
        if (!$user) {
            $user = wp_get_current_user();
        }
        
        // Administrator avec la capacité speciale OU email spécifique
        if (user_can($user, 'administrator') && user_can($user, 'mbaa_super_admin')) {
            return true;
        }
        
        return false;
    }
    
    
    // Vérifier si l'utilisateur peut accéder au tableau de bord
    
    public static function can_access_dashboard($user = null) {
        if (!$user) {
            $user = wp_get_current_user();
        }
        
        return user_can($user, 'mbaa_can_access_dashboard');
    }
    
    
    // Vérifier si l'utilisateur peut valider du contenu
    
    public static function can_validate($user = null) {
        if (!$user) {
            $user = wp_get_current_user();
        }
        
        return user_can($user, 'mbaa_can_validate');
    }
}

