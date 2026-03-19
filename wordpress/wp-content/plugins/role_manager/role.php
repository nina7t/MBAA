<?php
/**
 * Plugin Name: Gestionnaire de Rôles Custom
 * Description: Masque certains menus admin selon les rôles utilisateurs
 * Version: 1.0
 * Author: TONNAIRE Nina
 */

// Pour la sécurité on empêche l'accès direct au fichier 
if (!defined('ABSPATH')) {
    exit;
}

// Enregistrer les styles CSS
function role_manager_enqueue_styles() {
    wp_enqueue_style(
        'role-manager-admin-style',
        plugin_dir_url(__FILE__) . 'assets/css/admin-style.css',
        array(),
        '1.0'
    );
}
add_action('admin_enqueue_scripts', 'role_manager_enqueue_styles');

// 1. AJOUTER UNE PAGE DANS LE MENU ADMIN
function gestionnaire_de_role_ajouter_menu() {
    add_menu_page(
        'Gestionnaire de rôle',              // Titre de la page
        'Rôle',                              // Nom dans le menu
        'manage_options',                    // Capacité requise (admin seulement)
        'gestionnaire-de-role',              // Slug unique de la page
        'gestionnaire_role_page_contenu',    // Fonction qui affiche le contenu
        'dashicons-admin-users',             // Icône
        2                                   // Position dans le menu (après Tableau de bord)
    );
}
// Priorité précoce (5) pour s'assurer que le menu est ajouté avant les autres plugins
add_action('admin_menu', 'gestionnaire_de_role_ajouter_menu', 5);

// 2. CONTENU DE LA PAGE D'ADMINISTRATION
function gestionnaire_role_page_contenu() {
    global $wp_roles;
    $roles = isset($wp_roles) ? $wp_roles->get_names() : array();
    ?>
    <div class="role-wrap">
        <div class="container">
            <!-- Header -->
            <header class="role-header">
                <div class="role-header-content">
                    <div class="role-logo">
                        <span class="role-logo-text">Gestion des rôles utilisateurs</span>
                    </div>
                </div>
            </header>

            <!-- Page Header -->
            <div class="role-page-header">
                <div class="role-page-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <div class="role-page-title-section">
                    <h1 class="role-page-title"><?php echo esc_html(get_admin_page_title()); ?></h1>
                    <p class="role-subtitle">Gestion des permissions et rôles WordPress</p>
                </div>
            </div>

            <!-- Main Content -->
            <main class="role-main-content">
                <!-- Configuration Card -->
                <div class="role-card">
                    <h2 class="role-card-title">Configuration actuelle</h2>
                    
                    <div class="role-card" style="margin-top: 0; margin-bottom: 24px; background: var(--mbaa-gray-100);">
                        <ul class="role-list">
                            <li>
                                <span class="role-badge primary">Administrateurs</span>
                                <span>Accès complet à tout</span>
                            </li>
                            <li>
                                <span class="role-badge">Autres rôles</span>
                                <div>
                                    <p style="margin-bottom: 8px;">Accès limité à :</p>
                                    <ul style="margin-left: 0; padding-left: 20px;">
                                        <li>Galerie photo (Médias)</li>
                                        <li>Plugin mbaa_manager</li>
                                        <li>Pages</li>
                                        <li>Articles</li>
                                        <li>Tableau de bord</li>
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Menus masqués Card -->
                <div class="role-card">
                    <h2 class="role-card-title">Menus masqués pour les non-administrateurs</h2>
                    <ul class="role-list">
                        <li><span class="role-badge">Extensions</span></li>
                        <li><span class="role-badge">Apparence</span></li>
                        <li><span class="role-badge">Outils</span></li>
                        <li><span class="role-badge">Réglages</span></li>
                        <li><span class="role-badge">Utilisateurs</span></li>
                        <li><span class="role-badge">Commentaires</span></li>
                    </ul>
                </div>

                <!-- Rôles disponibles Card -->
                <div class="role-card">
                    <h2 class="role-card-title">Rôles WordPress disponibles</h2>
                    <?php if (!empty($roles)) : ?>
                        <ul class="role-list">
                            <?php foreach ($roles as $role_slug => $role_name) : ?>
                                <li>
                                    <span class="role-badge success"><?php echo esc_html($role_name); ?></span>
                                    <code><?php echo esc_html($role_slug); ?></code>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else : ?>
                        <p style="color: var(--mbaa-gray-500);">Aucun rôle disponible.</p>
                    <?php endif; ?>
                </div>

                <!-- Informations Card -->
                <div class="role-info-box">
                    <h3>Informations</h3>
                    <p>La redirection automatique vers la galerie média est activée pour les non-administrateurs.</p>
                    <p>La barre d'administration est masquée en front-end pour les non-administrateurs.</p>
                    <p>L'accès direct via URL est bloqué pour les pages sensibles.</p>
                </div>
            </main>
        </div>
    </div>
    <?php
}

// 3. MASQUER LES MENUS POUR LES NON-ADMIN
function mgr_masquer_menus_admin() {
    // Si c'est un admin, on ne fait rien 
    if (current_user_can('administrator')) {
        return;
    }

    // Pour tous les autres utilisateurs
    remove_menu_page('plugins.php');              // Extensions 
    remove_menu_page('themes.php');               // Apparence
    remove_menu_page('tools.php');                // Outils
    remove_menu_page('options-general.php');      // Réglages
    remove_menu_page('users.php');                // Utilisateurs
    remove_menu_page('edit-comments.php');        // Commentaires
    
    // Décommenter si besoin :
    // remove_menu_page('edit.php?post_type=page');  // Pages
    // remove_menu_page('edit.php');                 // Articles
    // remove_menu_page('index.php');                // Tableau de bord
}
// Priorité haute (999) pour s'exécuter après tous les autres plugins
add_action('admin_menu', 'mgr_masquer_menus_admin', 999);

// 4. MASQUER LA BARRE D'ADMIN EN FRONT-END
function mgr_masquer_admin_bar() {
    if (!current_user_can('administrator') && !is_admin()) {
        show_admin_bar(false);
    }
}
add_action('after_setup_theme', 'mgr_masquer_admin_bar');

// 5. BLOQUER L'ACCÈS VIA URL DIRECTE
function mgr_bloquer_acces_pages() {
    global $pagenow;
    
    if (!current_user_can('administrator')) {
        // Liste des pages à bloquer
        $pages_bloquees = array(
            'plugins.php', 
            'themes.php', 
            'tools.php', 
            'options-general.php',
            'users.php',
            'edit-comments.php',
            'plugin-editor.php',     // Éditeur de plugin
            'theme-editor.php'       // Éditeur de thème
        );
        
        if (in_array($pagenow, $pages_bloquees)) {
            wp_die(
                '<h1>Accès refusé</h1><p>Vous n\'avez pas les permissions nécessaires pour accéder à cette page.</p>',
                'Accès refusé',
                array('response' => 403)
            );
        }
    }
}
add_action('admin_init', 'mgr_bloquer_acces_pages');

// 6. REDIRECTION APRÈS CONNEXION
function mgr_redirection_apres_login($redirect_to, $request, $user) {
    // Vérifier que l'objet user existe et a des rôles
    if (isset($user->roles) && is_array($user->roles)) {
        // Si ce n'est pas un administrateur
        if (!in_array('administrator', $user->roles)) {
            // Rediriger vers la galerie média
            return admin_url('upload.php');
        }
    }
    return $redirect_to;
}
add_filter('login_redirect', 'mgr_redirection_apres_login', 10, 3);

