<?php
/**
 * Classe de gestion de l'interface admin
 * NOTE: Le menu principal est géré par class-mbaa-menu.php
 * Cette classe est conservée pour les méthodes de rendu
 */

if (!defined('ABSPATH')) {
    exit;
}

class MBAA_Admin {
    
    private static $instance = null;
    
    /**
     * Fonction helper pour afficher une icône SVG
     */
    private static function render_icon($icon_name, $class = '', $width = '20', $height = '20') {
        $svg_path = plugins_url('assets/img/svg/' . $icon_name . '.svg', dirname(__FILE__));
        return '<img src="' . esc_url($svg_path) . '" class="' . esc_attr($class) . '" style="width:' . esc_attr($width) . 'px;height:' . esc_attr($height) . 'px;vertical-align:middle;">';
    }
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function __construct() {
        // Le menu est maintenant géré par MBAA_Menu dans class-mbaa-menu.php
        
        // Hooks AJAX pour la recherche d'artistes
        add_action('wp_ajax_mbaa_recherche_artiste', array($this, 'recherche_artiste'));
        add_action('wp_ajax_nopriv_mbaa_recherche_artiste', array($this, 'recherche_artiste'));
        
        // Hooks pour les actions sur les œuvres
        add_action('admin_post_mbaa_save_oeuvre', array($this, 'handle_save_oeuvre'));
        add_action('admin_post_mbaa_delete_oeuvre', array($this, 'handle_delete_oeuvre'));
        add_action('wp_ajax_mbaa_toggle_visible', array($this, 'handle_toggle_visible'));
        
        // Hook pour l'upload de médias via AJAX
        add_action('wp_ajax_mbaa_upload_media', array($this, 'handle_media_upload'));
        
        // Hook pour la sauvegarde des artistes
        add_action('admin_post_mbaa_save_artiste', array($this, 'handle_save_artiste'));
        
        // Hook pour la sauvegarde des événements
        add_action('admin_post_mbaa_save_evenement', array($this, 'handle_save_evenement'));
        
        // Hooks pour la génération de PDF
        add_action('admin_post_mbaa_generate_oeuvre_pdf', array($this, 'handle_generate_oeuvre_pdf'));
        add_action('admin_post_mbaa_generate_artiste_pdf', array($this, 'handle_generate_artiste_pdf'));
        
        // Hook de test pour débogage
        add_action('admin_post_mbaa_test_artiste', array($this, 'test_artiste_table'));
        
        // Hook de test pour les tables d'œuvres
        add_action('admin_post_mbaa_test_oeuvres_tables', array($this, 'test_oeuvres_tables'));
        
        // Hook pour insérer les données par défaut
        add_action('admin_post_mbaa_insert_default_data', array($this, 'insert_default_data_now'));
        
        // Hook pour créer la table galerie manquante
        add_action('admin_post_mbaa_create_galerie_table', array($this, 'create_galerie_table'));
        
        // Hook pour vérifier la structure exacte des tables
        add_action('admin_post_mbaa_check_table_structure', array($this, 'check_table_structure'));

        // Hook pour relancer la création/migration des tables
        add_action('admin_post_mbaa_migrate_tables', array($this, 'migrate_tables_now'));
    }

    /**
     * Relancer la création/migration des tables (dbDelta)
     */
    public function migrate_tables_now() {
        if (!current_user_can('manage_options')) {
            wp_die('Permission refusée.');
        }

        $db = new MBAA_Database();
        $db->create_tables();

        wp_redirect(admin_url('admin.php?page=mbaa-oeuvres&action=add&message=migrated'));
        exit;
    }
    
    /**
     * Rendu du tableau de bord (conservé pour compatibilité)
     */
    public static function render_dashboard() {
        global $wpdb;
        $db = new MBAA_Database();
        
        // Récupérer les statistiques
        $stats = array(
            'artistes' => $wpdb->get_var("SELECT COUNT(*) FROM {$db->table_artiste}"),
            'oeuvres' => $wpdb->get_var("SELECT COUNT(*) FROM {$db->table_oeuvre}"),
            'evenements' => $wpdb->get_var("SELECT COUNT(*) FROM {$db->table_evenement}"),
            'audioguides' => $wpdb->get_var("SELECT COUNT(*) FROM {$db->table_audioguide}"),
            'expositions' => $wpdb->get_var("SELECT COUNT(*) FROM {$db->table_exposition}")
        );
        
        // Événements à venir
        $evenements_a_venir = $wpdb->get_results(
            "SELECT * FROM {$db->table_evenement} 
            WHERE date_evenement >= CURDATE() 
            ORDER BY date_evenement ASC 
            LIMIT 5"
        );
        
        include MBAA_PLUGIN_DIR . 'views/dashboard.php';
    }
    
    /**
     * Rendu de la page Artistes
     */
    public static function render_artistes_page() {
        error_log('MBAA DEBUG: render_artistes_page appelé');
        
        $artiste_manager = new MBAA_Artiste();
        
        // Gestion des actions (plus de gestion directe du formulaire)
        if (isset($_GET['action'])) {
            error_log('MBAA DEBUG: Action détectée: ' . $_GET['action']);
            switch ($_GET['action']) {
                case 'edit':
                    if (isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'edit_artiste_' . $_GET['id'])) {
                        $artiste = $artiste_manager->get_artiste(intval($_GET['id']));
                        include MBAA_PLUGIN_DIR . 'views/artiste-form.php';
                        return;
                    }
                    break;
                    
                case 'delete':
                    if (isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'delete_artiste_' . $_GET['id'])) {
                        $artiste_manager->delete_artiste(intval($_GET['id']));
                        wp_redirect(admin_url('admin.php?page=mbaa-artistes&message=deleted'));
                        exit;
                    }
                    break;
                    
                case 'add':
                    error_log('MBAA DEBUG: Inclusion formulaire ajout');
                    include MBAA_PLUGIN_DIR . 'views/artiste-form.php';
                    return;
            }
        }
        
        $artistes = $artiste_manager->get_all_artistes();
        error_log('MBAA DEBUG: Nombre d\'artistes trouvés = ' . count($artistes));
        error_log('MBAA DEBUG: Artistes = ' . print_r($artistes, true));
        
        include MBAA_PLUGIN_DIR . 'views/artistes-list.php';
    }
    
    /**
     * Rendu de la page Œuvres
     */
    public static function render_oeuvres_page() {
        global $wpdb;
        
        if (isset($_GET['action'])) {
            switch ($_GET['action']) {
                case 'add':
                    include MBAA_PLUGIN_DIR . 'views/form-oeuvre.php';
                    return;
                    
                case 'edit':
                    include MBAA_PLUGIN_DIR . 'views/form-oeuvre.php';
                    return;
            }
        }
        
        // Par défaut, afficher la liste
        include MBAA_PLUGIN_DIR . 'views/list-oeuvres.php';
    }
    
    /**
     * Gestion de la sauvegarde d'une œuvre
     */
    public function handle_save_oeuvre() {
        if (!current_user_can('manage_options')) {
            wp_die(__('Permission refusée.', 'mbaa-manager'));
        }

        if (!isset($_POST['mbaa_save_oeuvre_nonce']) ||
            !wp_verify_nonce($_POST['mbaa_save_oeuvre_nonce'], 'mbaa_save_oeuvre')) {
            wp_die(__('Erreur de sécurité. Veuillez réessayer.', 'mbaa-manager'));
        }

        if (empty($_POST['titre'])) {
            wp_redirect(admin_url('admin.php?page=mbaa-oeuvres&message=missing_title'));
            exit;
        }

        // DEBUG
        error_log('MBAA DEBUG: $_POST = ' . print_r($_POST, true));
        error_log('MBAA DEBUG: id_artiste reçu = ' . ($_POST['id_artiste'] ?? 'NULL'));

        global $wpdb;
        $oeuvre_table = $wpdb->prefix . 'mbaa_oeuvre';

        // ── Vérifier les colonnes optionnelles ───────────────────────────────────
        $has_etat       = $wpdb->get_var($wpdb->prepare("SHOW COLUMNS FROM {$oeuvre_table} LIKE %s", 'etat'))       !== null;
        $has_situation  = $wpdb->get_var($wpdb->prepare("SHOW COLUMNS FROM {$oeuvre_table} LIKE %s", 'situation'))  !== null;
        $has_audio_url  = $wpdb->get_var($wpdb->prepare("SHOW COLUMNS FROM {$oeuvre_table} LIKE %s", 'audio_url'))  !== null;
        $has_statut     = $wpdb->get_var($wpdb->prepare("SHOW COLUMNS FROM {$oeuvre_table} LIKE %s", 'statut'))     !== null;
        $has_provenance = $wpdb->get_var($wpdb->prepare("SHOW COLUMNS FROM {$oeuvre_table} LIKE %s", 'provenance')) !== null;

        // ── FIX #1 : Convertir id_artiste en entier (jamais string vide) ─────────
        //    - Si le champ est vide/absent → NULL en base (comportement attendu)
        //    - Si le champ a une valeur    → absint() garantit un entier propre
        $id_artiste_raw = isset($_POST['id_artiste']) ? trim($_POST['id_artiste']) : '';
        $id_artiste     = ($id_artiste_raw !== '') ? absint($id_artiste_raw) : null;

        error_log('MBAA DEBUG: id_artiste traité = ' . var_export($id_artiste, true));

        // ── Préparation des données ──────────────────────────────────────────────
        $data = [
            'titre'              => sanitize_text_field($_POST['titre']),
            'description'        => wp_kses_post($_POST['description'] ?? ''),
            'image_url'          => esc_url_raw($_POST['image_url'] ?? ''),
            'date_creation'      => sanitize_text_field($_POST['date_creation'] ?? ''),
            'dimensions'         => sanitize_text_field($_POST['dimensions'] ?? ''),
            'numero_inventaire'  => sanitize_text_field($_POST['numero_inventaire'] ?? ''),
            'technique'          => sanitize_text_field($_POST['technique'] ?? ''),
            'id_artiste'         => $id_artiste,   // ← valeur propre (int ou null)
            'id_epoque'          => !empty($_POST['id_epoque'])    ? absint($_POST['id_epoque'])    : null,
            'id_salle'           => !empty($_POST['id_salle'])     ? absint($_POST['id_salle'])     : null,
            'id_medium'          => !empty($_POST['id_medium'])    ? absint($_POST['id_medium'])    : null,
            'id_mouvement'       => !empty($_POST['id_mouvement']) ? absint($_POST['id_mouvement']) : null,
            'id_categorie'       => !empty($_POST['id_categorie']) ? absint($_POST['id_categorie']) : null,
            'visible_galerie'    => isset($_POST['visible_galerie']) ? 1 : 0,
            'visible_accueil'    => isset($_POST['visible_accueil']) ? 1 : 0,
        ];

        // Colonnes optionnelles
        if ($has_etat)       { $data['etat']       = isset($_POST['etat']) ? absint($_POST['etat']) : 1; }
        if ($has_situation)  { $data['situation']  = sanitize_text_field($_POST['situation'] ?? 'exposee'); }
        if ($has_audio_url)  { $data['audio_url']  = esc_url_raw($_POST['audio_url'] ?? ''); }
        if ($has_statut)     { $data['statut']     = sanitize_text_field($_POST['statut'] ?? 'permanente'); }
        if ($has_provenance) { $data['provenance'] = sanitize_text_field($_POST['provenance'] ?? ''); }

        // ── FIX #2 : Construire le tableau de formats dynamiquement ─────────────
        //    Nécessaire car on peut avoir supprimé des colonnes optionnelles.
        //    Un mauvais format provoque la perte silencieuse de données entières.
        $integer_fields = [
            'id_artiste', 'id_epoque', 'id_salle', 'id_medium',
            'id_mouvement', 'id_categorie', 'etat',
            'visible_galerie', 'visible_accueil',
        ];

        $formats = [];
        foreach ($data as $col => $val) {
            if (in_array($col, $integer_fields, true)) {
                // NULL est géré nativement par wpdb ; on passe quand même %d
                // pour que wpdb sache que c'est un entier quand la valeur est présente
                $formats[] = is_null($val) ? '%s' : '%d';
            } else {
                $formats[] = '%s';
            }
        }

        // ── Insertion ou mise à jour ─────────────────────────────────────────────
        $is_edit = isset($_POST['id']) && !empty($_POST['id']);

        if ($is_edit) {
            $oeuvre_id = absint($_POST['id']);

            $result = $wpdb->update(
                $oeuvre_table,
                $data,
                ['id_oeuvre' => $oeuvre_id],
                $formats,        // ← tableau de formats explicites (FIX #2)
                ['%d']
            );

            error_log('MBAA DEBUG: update result = ' . var_export($result, true));
            error_log('MBAA DEBUG: last_query = ' . $wpdb->last_query);
            error_log('MBAA DEBUG: last_error = ' . $wpdb->last_error);

            if ($result !== false) {
                $this->generate_qr_code($oeuvre_id);
                $this->save_relations_manuelles($oeuvre_id);
                $this->save_audioguide($oeuvre_id);
                wp_redirect(admin_url('admin.php?page=mbaa-oeuvres&message=updated'));
            } else {
                wp_redirect(admin_url('admin.php?page=mbaa-oeuvres&message=error'));
            }

        } else {
            $result = $wpdb->insert(
                $oeuvre_table,
                $data,
                $formats         // ← tableau de formats explicites (FIX #2)
            );

            error_log('MBAA DEBUG: insert result = ' . var_export($result, true));
            error_log('MBAA DEBUG: last_query = ' . $wpdb->last_query);
            error_log('MBAA DEBUG: last_error = ' . $wpdb->last_error);

            if ($result !== false) {
                $oeuvre_id = $wpdb->insert_id;
                $this->generate_qr_code($oeuvre_id);
                $this->save_relations_manuelles($oeuvre_id);
                $this->save_audioguide($oeuvre_id);
                wp_redirect(admin_url('admin.php?page=mbaa-oeuvres&action=edit&id=' . $oeuvre_id . '&message=added'));
            } else {
                wp_redirect(admin_url('admin.php?page=mbaa-oeuvres&message=error'));
            }
        }

        exit;
    }
    
    /**
     * Sauvegarder les relations manuelles d'une œuvre
     */
    private function save_relations_manuelles($oeuvre_id) {
        global $wpdb;
        
        // Supprimer les anciennes relations
        $wpdb->delete($wpdb->prefix . 'mbaa_oeuvres_similaires', array('oeuvre_id' => $oeuvre_id), array('%d'));
        $wpdb->delete($wpdb->prefix . 'mbaa_artistes_liens', array('oeuvre_id' => $oeuvre_id), array('%d'));
        $wpdb->delete($wpdb->prefix . 'mbaa_oeuvre_themes', array('oeuvre_id' => $oeuvre_id), array('%d'));
        
        // Sauvegarder les œuvres similaires
        if (isset($_POST['oeuvres_similaires']) && is_array($_POST['oeuvres_similaires'])) {
            foreach ($_POST['oeuvres_similaires'] as $index => $oeuvre_similaire_id) {
                $wpdb->insert($wpdb->prefix . 'mbaa_oeuvres_similaires', array(
                    'oeuvre_id' => $oeuvre_id,
                    'oeuvre_similaire_id' => absint($oeuvre_similaire_id),
                    'ordre' => $index + 1
                ));
            }
        }
        
        // Sauvegarder les artistes liés
        if (isset($_POST['artistes_liens']) && is_array($_POST['artistes_liens'])) {
            foreach ($_POST['artistes_liens'] as $index => $artiste_id) {
                $wpdb->insert($wpdb->prefix . 'mbaa_artistes_liens', array(
                    'oeuvre_id' => $oeuvre_id,
                    'artiste_id' => absint($artiste_id),
                    'ordre' => $index + 1
                ));
            }
        }
        
        // Sauvegarder les thèmes
        if (isset($_POST['themes']) && is_array($_POST['themes'])) {
            foreach ($_POST['themes'] as $theme_id) {
                $wpdb->insert($wpdb->prefix . 'mbaa_oeuvre_themes', array(
                    'oeuvre_id' => $oeuvre_id,
                    'theme_id' => absint($theme_id)
                ));
            }
        }
    }
    
    /**
     * Gestion de la suppression d'une œuvre
     */
    public function handle_delete_oeuvre() {
        if (!current_user_can('delete_posts')) {
            wp_die(__('Permission refusée.', 'mbaa-manager'));
        }
        
        $oeuvre_id = isset($_GET['id']) ? absint($_GET['id']) : 0;
        
        if (!$oeuvre_id) {
            wp_die(__('ID d\'œuvre invalide.', 'mbaa-manager'));
        }
        
        // Vérification du nonce
        $nonce_name = 'mbaa_delete_oeuvre_' . $oeuvre_id;
        if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], $nonce_name)) {
            wp_die(__('Erreur de sécurité. Veuillez réessayer.', 'mbaa-manager'));
        }
        
        global $wpdb;
        
        // Suppression de l'œuvre
        $result = $wpdb->delete(
            $wpdb->prefix . 'mbaa_oeuvre',
            array('id_oeuvre' => $oeuvre_id),
            array('%d')
        );
        
        // Suppression du QR code associé
        $wpdb->delete(
            $wpdb->prefix . 'mbaa_qr_codes',
            array('id_oeuvre' => $oeuvre_id),
            array('%d')
        );
        
        if ($result !== false) {
            wp_redirect(admin_url('admin.php?page=mbaa-oeuvres&message=deleted'));
        } else {
            wp_redirect(admin_url('admin.php?page=mbaa-oeuvres&message=delete_error'));
        }
        
        exit;
    }
    
    /**
     * Gestion du toggle de visibilité via AJAX
     */
    public function handle_toggle_visible() {
        check_ajax_referer('mbaa_toggle_visible_nonce', 'nonce');
        
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(__('Permission refusée.', 'mbaa-manager'));
        }
        
        $oeuvre_id = isset($_POST['oeuvre_id']) ? absint($_POST['oeuvre_id']) : 0;
        $visible = isset($_POST['visible']) ? absint($_POST['visible']) : 0;
        
        if (!$oeuvre_id) {
            wp_send_json_error(__('ID d\'œuvre invalide.', 'mbaa-manager'));
        }
        
        global $wpdb;
        
        $result = $wpdb->update(
            $wpdb->prefix . 'mbaa_oeuvre',
            array('visible_galerie' => $visible),
            array('id_oeuvre' => $oeuvre_id),
            array('%d'),
            array('%d')
        );
        
        if ($result !== false) {
            wp_send_json_success(array(
                'message' => $visible ? 'Œuvre publiée avec succès.' : 'Œuvre dépubliée avec succès.'
            ));
        } else {
            wp_send_json_error(__('Erreur lors de la mise à jour.', 'mbaa-manager'));
        }
    }
    
    /**
     * Génération d'un QR code pour une œuvre
     */
    private function generate_qr_code($oeuvre_id) {
        global $wpdb;
        
        // Récupération de l'œuvre
        $oeuvre = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}mbaa_oeuvre WHERE id_oeuvre = %d",
            $oeuvre_id
        ));
        
        if (!$oeuvre) {
            return false;
        }
        
        // Génération de l'URL publique (même logique que le front)
        $public_url = class_exists('MBAA_Oeuvre_Pages')
            ? MBAA_Oeuvre_Pages::get_oeuvre_url($oeuvre_id)
            : home_url('/oeuvre/' . $oeuvre_id . '/' . sanitize_title($oeuvre->titre) . '/');

        $table_qr = $wpdb->prefix . 'mbaa_qr_codes';

        // Vérification si le QR code existe déjà
        $existing_qr = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table_qr} WHERE id_oeuvre = %d AND type = %s LIMIT 1", $oeuvre_id, 'oeuvre')
        );

        if ($existing_qr) {
            $wpdb->update(
                $table_qr,
                array(
                    'url' => $public_url,
                    'type' => 'oeuvre',
                    'statut' => 'actif',
                    'mis_a_jour' => current_time('mysql'),
                ),
                array('id_qr' => $existing_qr->id_qr),
                array('%s', '%s', '%s', '%s'),
                array('%d')
            );
        } else {
            $wpdb->insert(
                $table_qr,
                array(
                    'id_oeuvre' => $oeuvre_id,
                    'code_qr' => 'QR_' . $oeuvre_id . '_' . uniqid(),
                    'url' => $public_url,
                    'type' => 'oeuvre',
                    'statut' => 'actif',
                    'scans_total' => 0,
                    'creation' => current_time('mysql'),
                    'mis_a_jour' => current_time('mysql'),
                ),
                array('%d', '%s', '%s', '%s', '%s', '%d', '%s', '%s')
            );
        }
        
        return true;
    }
    
    /**
     * Sauvegarder l'audioguide d'une œuvre
     */
    private function save_audioguide($oeuvre_id) {
        global $wpdb;
        
        // Vérifier si l'audioguide est activé
        if (!isset($_POST['has_audioguide']) || $_POST['has_audioguide'] != '1') {
            // Supprimer l'audioguide existant si décoché
            $wpdb->delete(
                $wpdb->prefix . 'mbaa_audioguide',
                array('id_oeuvre' => $oeuvre_id),
                array('%d')
            );
            return;
        }
        
        // Vérifier si un fichier audio est fourni
        if (empty($_POST['audioguide_fichier'])) {
            return;
        }
        
        $audioguide_data = array(
            'id_oeuvre' => $oeuvre_id,
            'fichier_audio_url' => esc_url_raw($_POST['audioguide_fichier']),
            'duree_secondes' => !empty($_POST['audioguide_duree']) ? absint($_POST['audioguide_duree']) : null,
            'langue' => !empty($_POST['audioguide_langue']) ? sanitize_text_field($_POST['audioguide_langue']) : 'fr',
            'transcription' => !empty($_POST['audioguide_transcription']) ? wp_kses_post($_POST['audioguide_transcription']) : null
        );
        
        // Vérifier si un audioguide existe déjà pour cette œuvre
        $existing = $wpdb->get_row($wpdb->prepare(
            "SELECT id_audioguide FROM {$wpdb->prefix}mbaa_audioguide WHERE id_oeuvre = %d",
            $oeuvre_id
        ));
        
        if ($existing) {
            // Mettre à jour l'audioguide existant
            $wpdb->update(
                $wpdb->prefix . 'mbaa_audioguide',
                $audioguide_data,
                array('id_audioguide' => $existing->id_audioguide),
                array('%s', '%d', '%s', '%s'),
                array('%d')
            );
        } else {
            // Créer un nouvel audioguide
            $wpdb->insert(
                $wpdb->prefix . 'mbaa_audioguide',
                $audioguide_data,
                array('%d', '%s', '%d', '%s', '%s')
            );
        }
    }
    
    /**
     * Gestion de la sauvegarde d'un artiste
     */
    public function handle_save_artiste() {
        // DEBUG: Log toutes les données reçues
        error_log('MBAA DEBUG: handle_save_artiste appelé');
        error_log('MBAA DEBUG: $_POST = ' . print_r($_POST, true));
        
        if (!current_user_can('manage_options')) {
            error_log('MBAA DEBUG: Permission refusée');
            wp_die(__('Permission refusée.', 'mbaa-manager'));
        }
        
        if (!isset($_POST['mbaa_artiste_nonce']) || 
            !wp_verify_nonce($_POST['mbaa_artiste_nonce'], 'mbaa_save_artiste')) {
            error_log('MBAA DEBUG: Nonce invalide - ' . ($_POST['mbaa_artiste_nonce'] ?? 'non défini'));
            wp_die(__('Erreur de sécurité. Veuillez réessayer.', 'mbaa-manager'));
        }
        
        // Validation des champs obligatoires
        if (empty($_POST['nom'])) {
            error_log('MBAA DEBUG: Nom manquant');
            wp_redirect(admin_url('admin.php?page=mbaa-artistes&message=missing_name'));
            exit;
        }
        
        global $wpdb;
        error_log('MBAA DEBUG: Table utilisée: ' . $wpdb->prefix . 'mbaa_artiste');
        
        // Préparation des données - ADAPTÉE À LA STRUCTURE RÉELLE
        $data = array(
            'nom' => sanitize_text_field($_POST['nom']),
            'biographie' => wp_kses_post($_POST['biographie'] ?? ''),
            'date_naissance' => !empty($_POST['date_naissance']) ? sanitize_text_field($_POST['date_naissance']) : null,
            'date_deces' => !empty($_POST['date_deces']) ? sanitize_text_field($_POST['date_deces']) : null,
            'nationalite' => sanitize_text_field($_POST['nationalite'] ?? ''),
            'image_url' => esc_url_raw($_POST['image_url'] ?? ''),
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        );
        
        error_log('MBAA DEBUG: Données préparées = ' . print_r($data, true));
        
        // Détermination si c'est une création ou une mise à jour
        $is_edit = isset($_POST['id_artiste']) && !empty($_POST['id_artiste']);
        error_log('MBAA DEBUG: Mode édition = ' . ($is_edit ? 'oui' : 'non'));
        
        if ($is_edit) {
            // Mise à jour
            $data['updated_at'] = current_time('mysql');
            unset($data['created_at']); // Ne pas mettre à jour la date de création
            
            error_log('MBAA DEBUG: Tentative de mise à jour ID = ' . $_POST['id_artiste']);
            
            $result = $wpdb->update(
                $wpdb->prefix . 'mbaa_artiste',
                $data,
                array('id_artiste' => absint($_POST['id_artiste'])),
                array('%s', '%s', '%s', '%s', '%s', '%s', '%s'), // 7 champs
                array('%d')
            );
            
            error_log('MBAA DEBUG: Résultat mise à jour = ' . var_export($result, true));
            error_log('MBAA DEBUG: Erreur WordPress = ' . $wpdb->last_error);
            
            if ($result !== false) {
                error_log('MBAA DEBUG: Redirection vers updated');
                wp_redirect(admin_url('admin.php?page=mbaa-artistes&message=updated'));
            } else {
                error_log('MBAA DEBUG: Redirection vers error');
                wp_redirect(admin_url('admin.php?page=mbaa-artistes&message=error'));
            }
        } else {
            // Création
            error_log('MBAA DEBUG: Tentative de création');
            
            $result = $wpdb->insert(
                $wpdb->prefix . 'mbaa_artiste',
                $data,
                array('%s', '%s', '%s', '%s', '%s', '%s', '%s') // 7 champs
            );
            
            error_log('MBAA DEBUG: Résultat création = ' . var_export($result, true));
            error_log('MBAA DEBUG: ID inséré = ' . $wpdb->insert_id);
            error_log('MBAA DEBUG: Erreur WordPress = ' . $wpdb->last_error);
            
            if ($result !== false) {
                error_log('MBAA DEBUG: Redirection vers added');
                wp_redirect(admin_url('admin.php?page=mbaa-artistes&message=added'));
            } else {
                error_log('MBAA DEBUG: Redirection vers error');
                wp_redirect(admin_url('admin.php?page=mbaa-artistes&message=error'));
            }
        }
        
        exit;
    }
    
    /**
     * Sauvegarder un événement via admin-post (évite le "headers already sent")
     */
    public function handle_save_evenement() {
        if (!current_user_can('manage_options') && !current_user_can('mbaa_can_access_evenements')) {
            wp_die(__('Permission refusée.', 'mbaa'));
        }

        if (!isset($_POST['mbaa_evenement_nonce']) ||
            !wp_verify_nonce($_POST['mbaa_evenement_nonce'], 'mbaa_save_evenement')) {
            wp_die(__('Erreur de sécurité. Veuillez réessayer.', 'mbaa'));
        }

        if (empty($_POST['titre'])) {
            wp_redirect(admin_url('admin.php?page=mbaa-evenements&action=add&error=missing_title'));
            exit;
        }

        $evenement_manager = new MBAA_Evenement();
        $result = $evenement_manager->save_evenement($_POST);

        if (!$result) {
            wp_redirect(admin_url('admin.php?page=mbaa-evenements&action=add&error=save_failed'));
            exit;
        }

        $is_edit = !empty($_POST['id_evenement']);
        $message = $is_edit ? 'updated' : 'added';

        wp_redirect(admin_url('admin.php?page=mbaa-evenements&message=' . $message));
        exit;
    }

    /**
     * Fonction de test pour la table artiste
     */
    public function test_artiste_table() {
        echo "<h1>DIAGNOSTIC TABLE ARTISTE</h1>";
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'mbaa_artiste';
        
        echo "<h2>1. Vérification de la table</h2>";
        // Vérifier si la table existe
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'");
        echo "<p><strong>Table existe:</strong> " . ($table_exists ? '✅ OUI' : '❌ NON') . "</p>";
        echo "<p><strong>Nom de la table:</strong> $table_name</p>";
        
        if ($table_exists) {
            echo "<h2>2. Structure de la table</h2>";
            // Vérifier la structure
            $structure = $wpdb->get_results("DESCRIBE $table_name");
            echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
            echo "<tr><th>Champ</th><th>Type</th><th>Null</th><th>Key</th></tr>";
            foreach ($structure as $col) {
                echo "<tr>";
                echo "<td>{$col->Field}</td>";
                echo "<td>{$col->Type}</td>";
                echo "<td>{$col->Null}</td>";
                echo "<td>{$col->Key}</td>";
                echo "</tr>";
            }
            echo "</table>";
            
            echo "<h2>3. Contenu de la table</h2>";
            // Compter les artistes
            $count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
            echo "<p><strong>Nombre d'artistes:</strong> $count</p>";
            
            if ($count > 0) {
                echo "<h3>Artistes dans la table:</h3>";
                // Lister les artistes
                $artistes = $wpdb->get_results("SELECT * FROM $table_name LIMIT 5");
                echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
                echo "<tr><th>ID</th><th>Nom</th><th>Nationalité</th><th>Creation</th></tr>";
                foreach ($artistes as $artiste) {
                    echo "<tr>";
                    echo "<td>{$artiste->id_artiste}</td>";
                    echo "<td>{$artiste->nom}</td>";
                    echo "<td>{$artiste->nationalite}</td>";
                    echo "<td>{$artiste->creation}</td>";
                    echo "</tr>";
                }
                echo "</table>";
            }
            
            echo "<h2>4. Test d'insertion</h2>";
            // Test d'insertion - ADAPTÉ À LA STRUCTURE RÉELLE
            $test_data = array(
                'nom' => 'TEST_ARTISTE_' . time(),
                'biographie' => 'Test biographie',
                'date_naissance' => null,
                'date_deces' => null,
                'nationalite' => 'Test',
                'image_url' => null,
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            );
            
            $result = $wpdb->insert($table_name, $test_data);
            $insert_id = $wpdb->insert_id;
            $last_error = $wpdb->last_error;
            
            echo "<p><strong>Résultat insertion:</strong> " . ($result !== false ? '✅ SUCCÈS' : '❌ ÉCHEC') . "</p>";
            if ($result !== false) {
                echo "<p><strong>ID inséré:</strong> $insert_id</p>";
                
                // Vérifier l'insertion
                $verify = $wpdb->get_row("SELECT * FROM $table_name WHERE id_artiste = $insert_id");
                if ($verify) {
                    echo "<p><strong>✅ Vérification OK - Artiste bien inséré!</strong></p>";
                    // Supprimer le test
                    $wpdb->delete($table_name, array('id_artiste' => $insert_id));
                    echo "<p><strong>🧹 Test nettoyé</strong></p>";
                } else {
                    echo "<p><strong>❌ Vérification échouée</strong></p>";
                }
            } else {
                echo "<p><strong>Erreur SQL:</strong> $last_error</p>";
            }
        } else {
            echo "<h2>❌ TABLE INEXISTANTE</h2>";
            echo "<p>La table n'existe pas. Il faut la créer.</p>";
            
            // Vérifier si d'autres tables existent
            $all_tables = $wpdb->get_results("SHOW TABLES LIKE '{$wpdb->prefix}mbaa_%'");
            echo "<h3>Autres tables MBAA trouvées:</h3>";
            if ($all_tables) {
                foreach ($all_tables as $table) {
                    $table_name_array = (array)$table;
                    echo "<p>" . reset($table_name_array) . "</p>";
                }
            } else {
                echo "<p>Aucune table MBAA trouvée</p>";
            }
        }
        
        echo "<h2>5. Informations système</h2>";
        echo "<p><strong>WordPress prefix:</strong> {$wpdb->prefix}</p>";
        echo "<p><strong>MBAA_Database table_artiste:</strong> ";
        $db = new MBAA_Database();
        echo $db->table_artiste . "</p>";
        
        echo "<p><a href='" . admin_url('admin.php?page=mbaa-artistes') . "'>← Retour à la page artistes</a></p>";
        
        wp_die('Diagnostic terminé');
    }
    
    /**
     * Fonction de test pour les tables d'œuvres
     */
    public function test_oeuvres_tables() {
        echo "<h1>DIAGNOSTIC TABLES ŒUVRES</h1>";
        
        global $wpdb;
        
        $tables_to_check = array(
            'epoque' => $wpdb->prefix . 'mbaa_epoque',
            'salle' => $wpdb->prefix . 'mbaa_salle',
            'medium' => $wpdb->prefix . 'mbaa_medium',
            'mouvement' => $wpdb->prefix . 'mbaa_mouvement_artistique',
            'categorie' => $wpdb->prefix . 'mbaa_categorie',
            'galerie' => $wpdb->prefix . 'mbaa_galerie'
        );
        
        foreach ($tables_to_check as $name => $table_name) {
            echo "<h2>Table: $name</h2>";
            
            // Vérifier si la table existe
            $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'");
            echo "<p><strong>Table existe:</strong> " . ($table_exists ? '✅ OUI' : '❌ NON') . "</p>";
            echo "<p><strong>Nom complet:</strong> $table_name</p>";
            
            if ($table_exists) {
                // Compter les enregistrements
                $count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
                echo "<p><strong>Nombre d'enregistrements:</strong> $count</p>";
                
                if ($count > 0) {
                    // Afficher quelques exemples
                    $samples = $wpdb->get_results("SELECT * FROM $table_name LIMIT 3");
                    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
                    
                    // Déterminer les noms de champs ID et nom selon la table
                    $id_field = 'id';
                    $nom_field = 'nom';
                    
                    if ($name === 'mouvement') {
                        $id_field = 'id';
                        $nom_field = 'nom';
                    }
                    
                    echo "<tr><th>ID</th><th>Nom</th></tr>";
                    foreach ($samples as $sample) {
                        echo "<tr>";
                        echo "<td>" . ($sample->$id_field ?? 'N/A') . "</td>";
                        echo "<td>" . ($sample->$nom_field ?? 'N/A') . "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                }
            }
            echo "<hr>";
        }
        
        echo "<p><a href='" . admin_url('admin.php?page=mbaa-oeuvres&action=add') . "'>← Retour au formulaire œuvre</a></p>";
        
        wp_die('Diagnostic terminé');
    }
    
    /**
     * Insérer les données par défaut maintenant
     */
    public function insert_default_data_now() {
        echo "<h1>INSERTION DONNÉES PAR DÉFAUT</h1>";
        
        if (!current_user_can('manage_options')) {
            wp_die('Permission refusée.');
        }
        
        $db = new MBAA_Database();
        
        echo "<h2>Insertion des données par défaut...</h2>";
        
        try {
            // Appeler la méthode privée via réflexion
            $reflection = new ReflectionClass($db);
            $method = $reflection->getMethod('insert_default_data');
            $method->setAccessible(true);
            $method->invoke($db);
            
            echo "<p style='color: green;'><strong>✅ Données par défaut insérées avec succès !</strong></p>";
            
            // Vérifier les résultats
            global $wpdb;
            $tables_to_check = array(
                'epoque' => $db->table_epoque,
                'salle' => $db->table_salle,
                'medium' => $db->table_medium,
                'mouvement' => $db->table_mouvement,
                'categorie' => $db->table_categorie,
                'galerie' => $db->table_galerie
            );
            
            echo "<h3>Vérification des données insérées :</h3>";
            foreach ($tables_to_check as $name => $table_name) {
                $count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
                echo "<p><strong>$name:</strong> $count enregistrements</p>";
            }
            
        } catch (Exception $e) {
            echo "<p style='color: red;'><strong>❌ Erreur:</strong> " . $e->getMessage() . "</p>";
        }
        
        echo "<p><a href='" . admin_url('admin.php?page=mbaa-oeuvres&action=add') . "'>← Retour au formulaire œuvre</a></p>";
        
        wp_die('Insertion terminée');
    }
    
    /**
     * Créer la table galerie manquante
     */
    public function create_galerie_table() {
        echo "<h1>CRÉATION TABLE GALERIE</h1>";
        
        if (!current_user_can('manage_options')) {
            wp_die('Permission refusée.');
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'mbaa_galerie';
        
        echo "<h2>Création de la table galerie...</h2>";
        
        // Vérifier si la table existe
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'");
        echo "<p><strong>Table existe:</strong> " . ($table_exists ? 'OUI' : 'NON') . "</p>";
        
        if (!$table_exists) {
            // Créer la table
            $sql = "CREATE TABLE $table_name (
                id int(11) NOT NULL AUTO_INCREMENT,
                nom varchar(255) NOT NULL,
                description text DEFAULT NULL,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY nom (nom)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
            
            $result = $wpdb->query($sql);
            
            if ($result !== false) {
                echo "<p style='color: green;'><strong>✅ Table galerie créée avec succès !</strong></p>";
                
                // Insérer les données par défaut
                $galeries = array(
                    array('nom' => 'Galerie principale', 'description' => 'Galerie d\'exposition principale'),
                    array('nom' => 'Galerie temporaire', 'description' => 'Galerie pour expositions temporaires'),
                    array('nom' => 'Galerie extérieure', 'description' => 'Espace d\'exposition extérieur')
                );
                
                foreach ($galeries as $galerie) {
                    $galerie['created_at'] = current_time('mysql');
                    $galerie['updated_at'] = current_time('mysql');
                    $wpdb->insert($table_name, $galerie);
                }
                
                echo "<p style='color: green;'><strong>✅ Données par défaut insérées !</strong></p>";
                
                // Vérifier
                $count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
                echo "<p><strong>Nombre de galeries:</strong> $count</p>";
                
            } else {
                echo "<p style='color: red;'><strong>❌ Erreur lors de la création:</strong> " . $wpdb->last_error . "</p>";
            }
        } else {
            echo "<p style='color: orange;'><strong>⚠️ La table existe déjà</strong></p>";
        }
        
        echo "<p><a href='" . admin_url('admin.php?page=mbaa-oeuvres&action=add') . "'>← Retour au formulaire œuvre</a></p>";
        
        wp_die('Création terminée');
    }
    
    /**
     * Vérifier la structure exacte des tables
     */
    public function check_table_structure() {
        echo "<h1>STRUCTURE EXACTE DES TABLES</h1>";
        
        global $wpdb;
        
        $tables_to_check = array(
            'epoque' => $wpdb->prefix . 'mbaa_epoque',
            'salle' => $wpdb->prefix . 'mbaa_salle',
            'medium' => $wpdb->prefix . 'mbaa_medium',
            'mouvement' => $wpdb->prefix . 'mbaa_mouvement_artistique',
            'categorie' => $wpdb->prefix . 'mbaa_categorie',
            'galerie' => $wpdb->prefix . 'mbaa_galerie'
        );
        
        foreach ($tables_to_check as $name => $table_name) {
            echo "<h2>Table: $name</h2>";
            
            // Vérifier si la table existe
            $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'");
            if (!$table_exists) {
                echo "<p style='color: red;'><strong>❌ Table n'existe pas</strong></p><hr>";
                continue;
            }
            
            // Obtenir la structure complète
            $structure = $wpdb->get_results("DESCRIBE $table_name");
            echo "<h3>Structure des champs :</h3>";
            echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
            echo "<tr><th>Champ</th><th>Type</th><th>Null</th><th>Key</th></tr>";
            foreach ($structure as $col) {
                echo "<tr>";
                echo "<td><strong>{$col->Field}</strong></td>";
                echo "<td>{$col->Type}</td>";
                echo "<td>{$col->Null}</td>";
                echo "<td>{$col->Key}</td>";
                echo "</tr>";
            }
            echo "</table>";
            
            // Tester différentes requêtes
            echo "<h3>Test des requêtes possibles :</h3>";
            
            $test_queries = array(
                "SELECT id, nom FROM $table_name LIMIT 3",
                "SELECT id_epoque, nom_epoque FROM $table_name LIMIT 3",
                "SELECT id_salle, nom_salle FROM $table_name LIMIT 3",
                "SELECT id_medium, nom_medium FROM $table_name LIMIT 3",
                "SELECT id_mouvement, nom_mouvement FROM $table_name LIMIT 3",
                "SELECT id_categorie, nom_categorie FROM $table_name LIMIT 3",
                "SELECT * FROM $table_name LIMIT 3"
            );
            
            foreach ($test_queries as $i => $query) {
                $result = $wpdb->get_results($query);
                if ($result && !empty($result)) {
                    echo "<p style='color: green;'><strong>✅ Requête $i fonctionne :</strong> $query</p>";
                    echo "<table border='1' style='border-collapse: collapse; margin: 5px 0;'>";
                    $first_row = (array)$result[0];
                    echo "<tr>";
                    foreach (array_keys($first_row) as $field) {
                        echo "<th>$field</th>";
                    }
                    echo "</tr>";
                    foreach ($result as $row) {
                        echo "<tr>";
                        foreach ((array)$row as $value) {
                            echo "<td>" . ($value ?? 'NULL') . "</td>";
                        }
                        echo "</tr>";
                    }
                    echo "</table>";
                    break; // Arrêter après la première requête qui fonctionne
                }
            }
            
            echo "<hr>";
        }
        
        echo "<p><a href='" . admin_url('admin.php?page=mbaa-oeuvres&action=add') . "'>← Retour au formulaire œuvre</a></p>";
        
        wp_die('Vérification terminée');
    }
    
    /**
     * Gestion de l'upload de médias via AJAX
     */
    public function handle_media_upload() {
        check_ajax_referer('mbaa_upload_media_nonce', 'nonce');
        
        // Vérifier les permissions - plus permissif pour le musée
        if (!current_user_can('upload_files') && !current_user_can('mbaa_can_access_oeuvres')) {
            wp_send_json_error(__('Permission refusée.', 'mbaa-manager'));
        }
        
        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            wp_send_json_error(__('Erreur lors de l\'upload du fichier.', 'mbaa-manager'));
        }
        
        $file = $_FILES['file'];
        
        // Validation du type de fichier
        $allowed_types = array(
            'image/jpeg', 'image/png', 'image/gif', 'image/webp',
            'audio/mpeg', 'audio/wav', 'audio/mp4', 'audio/ogg'
        );
        
        $file_type = wp_check_filetype_and_ext($file['tmp_name'], $file['name']);
        if (!$file_type['ext'] || !in_array($file_type['type'], $allowed_types)) {
            wp_send_json_error(__('Type de fichier non autorisé.', 'mbaa-manager'));
        }
        
        // Validation de la taille (max 10MB)
        if ($file['size'] > 10 * 1024 * 1024) {
            wp_send_json_error(__('Fichier trop volumineux (max 10MB).', 'mbaa-manager'));
        }
        
        // Upload via WordPress
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        
        $upload_overrides = array(
            'test_form' => false,
            'mimes' => array(
                'jpg|jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'webp' => 'image/webp',
                'mp3' => 'audio/mpeg',
                'wav' => 'audio/wav',
                'm4a' => 'audio/mp4',
                'ogg' => 'audio/ogg'
            )
        );
        
        $uploaded_file = wp_handle_upload($file, $upload_overrides);
        
        if (isset($uploaded_file['error'])) {
            wp_send_json_error($uploaded_file['error']);
        }
        
        // Création de l'attachment dans la médiathèque
        $attachment = array(
            'post_mime_type' => $uploaded_file['type'],
            'post_title' => sanitize_file_name($file['name']),
            'post_content' => '',
            'post_status' => 'inherit'
        );
        
        $attachment_id = wp_insert_attachment($attachment, $uploaded_file['file']);
        
        if (is_wp_error($attachment_id)) {
            wp_send_json_error(__('Erreur lors de la création de l\'attachment.', 'mbaa-manager'));
        }
        
        // Génération des métadonnées pour les images
        if (strpos($uploaded_file['type'], 'image/') === 0) {
            $attachment_data = wp_generate_attachment_metadata($attachment_id, $uploaded_file['file']);
            wp_update_attachment_metadata($attachment_id, $attachment_data);
        }
        
        $attachment_url = wp_get_attachment_url($attachment_id);
        
        wp_send_json_success(array(
            'url' => $attachment_url,
            'attachment_id' => $attachment_id,
            'filename' => $file['name']
        ));
    }
    
    /**
     * Rendu de la page Agenda (calendrier visuel identique à la page publique)
     */
    public static function render_agenda_page() {
        $evenement_manager = new MBAA_Evenement();
        $evenements = $evenement_manager->get_all_evenements(array('a_venir' => true));
        $types_evenement = $evenement_manager->get_types_evenement();
        
        include MBAA_PLUGIN_DIR . 'views/agenda-calendar.php';
    }
    
    /**
     * Rendu de la page Événements
     */
    public static function render_evenements_page() {
        $evenement_manager = new MBAA_Evenement();
        // La sauvegarde est gérée par handle_save_evenement() via admin-post.php
        
        if (isset($_GET['action'])) {
            switch ($_GET['action']) {
                case 'edit':
                    $evenement = $evenement_manager->get_evenement($_GET['id']);
                    include MBAA_PLUGIN_DIR . 'views/evenement-form.php';
                    return;
                    
                case 'delete':
                    if (isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'delete_evenement_' . $_GET['id'])) {
                        $evenement_manager->delete_evenement($_GET['id']);
                        wp_redirect(admin_url('admin.php?page=mbaa-evenements&message=deleted'));
                        exit;
                    }
                    break;
            }
        }
        
        if (isset($_GET['action']) && $_GET['action'] === 'add') {
            include MBAA_PLUGIN_DIR . 'views/evenement-form.php';
        } else {
            $evenements = $evenement_manager->get_all_evenements();
            include MBAA_PLUGIN_DIR . 'views/evenements-list.php';
        }
    }
    
    /**
     * Rendu de la page Audioguides
     */
    public static function render_audioguides_page() {
        $audioguide_manager = new MBAA_Audioguide();
        
        if (isset($_GET['action']) && in_array($_GET['action'], array('add', 'edit'))) {
            if (isset($_POST['mbaa_audioguide_nonce']) && wp_verify_nonce($_POST['mbaa_audioguide_nonce'], 'mbaa_save_audioguide')) {
                $audioguide_manager->save_audioguide($_POST);
                wp_redirect(admin_url('admin.php?page=mbaa-audioguides&message=added'));
                exit;
            }
        }
        
        if (isset($_GET['action'])) {
            switch ($_GET['action']) {
                case 'edit':
                    $audioguide = $audioguide_manager->get_audioguide($_GET['id']);
                    include MBAA_PLUGIN_DIR . 'views/audioguide-form.php';
                    return;
                    
                case 'delete':
                    if (isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'delete_audioguide_' . $_GET['id'])) {
                        $audioguide_manager->delete_audioguide($_GET['id']);
                        wp_redirect(admin_url('admin.php?page=mbaa-audioguides&message=deleted'));
                        exit;
                    }
                    break;
            }
        }
        
        if (isset($_GET['action']) && $_GET['action'] === 'add') {
            include MBAA_PLUGIN_DIR . 'views/audioguide-form.php';
        } else {
            $audioguides = $audioguide_manager->get_all_audioguides();
            include MBAA_PLUGIN_DIR . 'views/audioguides-list.php';
        }
    }
    
    /**
     * Rendu de la page Paramètres
     */
    public static function render_parametres_page() {
        include MBAA_PLUGIN_DIR . 'views/parametres.php';
    }
    
    /**
     * Rendu de la page Collections (regroupement œuvres par statut)
     * Affiche les œuvres sous forme de cards cliquables
     */
    public static function render_collections_page() {
        global $wpdb;
        $db = new MBAA_Database();
        
        // Récupérer les œuvres par statut
        $oeuvres_permanentes = $db->get_oeuvres_by_statut('permanente');
        $oeuvres_reserve = $db->get_oeuvres_by_statut('reserve');
        $oeuvres_pret = $db->get_oeuvres_by_statut('pret');
        $oeuvres_restauration = $db->get_oeuvres_by_statut('restauration');
        
        // Stats pour la barre
        $total_oeuvres = $wpdb->get_var("SELECT COUNT(*) FROM {$db->table_oeuvre}");
        
        echo '<div class="wrap">';
        
        // Barre de statistiques
        echo '<div class="mbaa-stats-bar">';
        echo '<div class="mbaa-stats-item">';
        echo '<span class="mbaa-stats-label">Collections</span>';
        echo '<span class="mbaa-stats-value">' . esc_html($total_oeuvres) . ' œuvres</span>';
        echo '</div>';
        echo '<div class="mbaa-stats-item">';
        echo '<span class="mbaa-stats-label">Permanente</span>';
        echo '<span class="mbaa-stats-value">' . count($oeuvres_permanentes) . '</span>';
        echo '</div>';
        echo '<div class="mbaa-stats-item">';
        echo '<span class="mbaa-stats-label">En réserve</span>';
        echo '<span class="mbaa-stats-value">' . count($oeuvres_reserve) . '</span>';
        echo '</div>';
        echo '<div class="mbaa-stats-item">';
        echo '<span class="mbaa-stats-label">En prêt</span>';
        echo '<span class="mbaa-stats-value">' . count($oeuvres_pret) . '</span>';
        echo '</div>';
        echo '<div class="mbaa-stats-item">';
        echo '<span class="mbaa-stats-label">Restauration</span>';
        echo '<span class="mbaa-stats-value">' . count($oeuvres_restauration) . '</span>';
        echo '</div>';
        echo '</div>';
        
        echo '<div class="mbaa-main-content">';
        echo '<div class="mbaa-artwork-header">';
        echo '<div class="mbaa-artwork-icon">';
        echo '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">';
        echo '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />';
        echo '</svg>';
        echo '</div>';
        echo '<div class="mbaa-artwork-title-section">';
        echo '<h1 class="mbaa-artwork-title">Collections</h1>';
        echo '<span class="mbaa-status-badge">Vue d\'ensemble des œuvres par statut</span>';
        echo '</div>';
        echo '</div>';
        
        // Onglets de filtrage
        echo '<div class="mbaa-collection-tabs">';
        echo '<a href="#" class="mbaa-tab active" data-filter="all">Toutes</a>';
        echo '<a href="#" class="mbaa-tab" data-filter="permanente">' . self::render_icon('circle-green', '', '14', '14') . ' Exposition Permanente</a>';
        echo '<a href="#" class="mbaa-tab" data-filter="reserve">' . self::render_icon('box', '', '14', '14') . ' En Réserve</a>';
        echo '<a href="#" class="mbaa-tab" data-filter="pret">' . self::render_icon('refresh', '', '14', '14') . ' En Prêt</a>';
        echo '<a href="#" class="mbaa-tab" data-filter="restauration">' . self::render_icon('wrench', '', '14', '14') . ' En Restauration</a>';
        echo '</div>';
        
        echo '<div class="mbaa-collections-grid">';
        
        // Afficher les cards pour chaque statut
        self::render_oeuvre_cards($oeuvres_permanentes, 'permanente');
        self::render_oeuvre_cards($oeuvres_reserve, 'reserve');
        self::render_oeuvre_cards($oeuvres_pret, 'pret');
        self::render_oeuvre_cards($oeuvres_restauration, 'restauration');
        
        echo '</div>'; // fin collections-grid
        echo '</div>'; // fin main-content
        echo '</div>'; // fin wrap
    }
    
    /**
     * Afficher les cards d'œuvres pour un statut donné
     */
    private static function render_oeuvre_cards($oeuvres, $statut) {
        foreach ($oeuvres as $oeuvre) {
            $image_url = !empty($oeuvre['image_url']) ? $oeuvre['image_url'] : '';
            $titre = !empty($oeuvre['titre']) ? $oeuvre['titre'] : 'Sans titre';
            $artiste_nom = !empty($oeuvre['artiste_nom']) ? $oeuvre['artiste_nom'] : 'Artiste inconnu';
            
            echo '<div class="mbaa-oeuvre-card" data-statut="' . esc_attr($statut) . '">';
            echo '<div class="mbaa-oeuvre-card-image">';
            if ($image_url) {
                echo '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($titre) . '" loading="lazy">';
            } else {
                echo '<div class="mbaa-oeuvre-placeholder">';
                echo '<span class="dashicons dashicons-art"></span>';
                echo '</div>';
            }
            echo '</div>';
            echo '<div class="mbaa-oeuvre-card-content">';
            echo '<h3 class="mbaa-oeuvre-card-title">' . esc_html($titre) . '</h3>';
            echo '<p class="mbaa-oeuvre-card-artist">' . esc_html($artiste_nom) . '</p>';
            
            // Afficher les infos courtes
            if (!empty($oeuvre['date_creation'])) {
                echo '<p class="mbaa-oeuvre-card-info">' . self::render_icon('calendar', '', '14', '14') . ' ' . esc_html($oeuvre['date_creation']) . '</p>';
            }
            if (!empty($oeuvre['medium_nom'])) {
                echo '<p class="mbaa-oeuvre-card-info">' . self::render_icon('palette', '', '14', '14') . ' ' . esc_html($oeuvre['medium_nom']) . '</p>';
            }
            if (!empty($oeuvre['salle_nom'])) {
                echo '<p class="mbaa-oeuvre-card-info">' . self::render_icon('location', '', '14', '14') . ' ' . esc_html($oeuvre['salle_nom']) . '</p>';
            }
            
            echo '<a href="' . admin_url('admin.php?page=mbaa-oeuvres&action=edit&id=' . $oeuvre['id_oeuvre']) . '" class="mbaa-oeuvre-card-link">';
            echo 'Voir la fiche →';
            echo '</a>';
            echo '</div>';
            echo '</div>';
        }
    }
    
    /**
     * Rendu de la page Expositions
     */
    public static function render_expositions_page() {
        global $wpdb;
        $db = new MBAA_Database();
        
        // Récupérer les expositions des 5 dernières années
        $cinq_ans = date('Y-m-d', strtotime('-5 years'));
        $expositions = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT e.*, 
                        COUNT(v.id_visite) as nombre_visiteurs
                FROM {$db->table_exposition} e
                LEFT JOIN {$db->table_visite} v ON e.id_exposition = v.id_exposition
                WHERE e.date_debut >= %s 
                GROUP BY e.id_exposition
                ORDER BY e.date_debut DESC",
                $cinq_ans
            ),
            ARRAY_A
        );
        
        // Statistiques
        $total_expositions = count($expositions);
        $expositions_en_cours = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$db->table_exposition} WHERE date_debut <= %s AND date_fin >= %s",
                current_time('mysql'),
                current_time('mysql')
            )
        );
        
        $total_visiteurs = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$db->table_visite} v
                JOIN {$db->table_exposition} e ON v.id_exposition = e.id_exposition
                WHERE e.date_debut >= %s",
                $cinq_ans
            )
        );
        
        echo '<div class="wrap">';
        
        // Barre de statistiques
        echo '<div class="mbaa-stats-bar">';
        echo '<div class="mbaa-stats-item">';
        echo '<span class="mbaa-stats-label">Expositions</span>';
        echo '<span class="mbaa-stats-value">' . esc_html($total_expositions) . '</span>';
        echo '</div>';
        echo '<div class="mbaa-stats-item">';
        echo '<span class="mbaa-stats-label">En cours</span>';
        echo '<span class="mbaa-stats-value">' . esc_html($expositions_en_cours) . '</span>';
        echo '</div>';
        echo '<div class="mbaa-stats-item">';
        echo '<span class="mbaa-stats-label">Visiteurs totaux</span>';
        echo '<span class="mbaa-stats-value">' . number_format($total_visiteurs) . '</span>';
        echo '</div>';
        echo '</div>';
        
        echo '<div class="mbaa-main-content">';
        echo '<div class="mbaa-artwork-header">';
        echo '<div class="mbaa-artwork-icon">';
        echo '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">';
        echo '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />';
        echo '</svg>';
        echo '</div>';
        echo '<div class="mbaa-artwork-title-section">';
        echo '<h1 class="mbaa-artwork-title">Expositions</h1>';
        echo '<span class="mbaa-status-badge">Anciennes et futures expositions du musée</span>';
        echo '</div>';
        echo '</div>';
        
        // Bouton d'ajout pour les admins
        if (current_user_can('mbaa_can_access_expositions') && current_user_can('publish_posts')) {
            echo '<div class="mbaa-page-actions">';
            echo '<a href="#" class="button button-primary">' . __('Ajouter une exposition', 'mbaa') . '</a>';
            echo '</div>';
        }
        
        if (!empty($expositions)) {
            echo '<div class="mbaa-expositions-grid">';
            foreach ($expositions as $expo) {
                self::render_exposition_card($expo);
            }
            echo '</div>';
        } else {
            echo '<div class="mbaa-empty-state">';
            echo '<span class="dashicons dashicons-images-alt2"></span>';
            echo '<h3>Aucune exposition</h3>';
            echo '<p>Aucune exposition n\'a été créée pour le moment.</p>';
            echo '</div>';
        }
        
        echo '</div>';
        echo '</div>';
    }
    
    /**
     * Afficher une card d'exposition
     */
    private static function render_exposition_card($expo) {
        $date_debut = !empty($expo['date_debut']) ? $expo['date_debut'] : '';
        $date_fin = !empty($expo['date_fin']) ? $expo['date_fin'] : '';
        $titre = !empty($expo['titre']) ? $expo['titre'] : 'Sans titre';
        $description = !empty($expo['description']) ? $expo['description'] : '';
        $histoire = !empty($expo['histoire']) ? $expo['histoire'] : '';
        $image_url = !empty($expo['image_url']) ? $expo['image_url'] : '';
        $statut = !empty($expo['statut']) ? $expo['statut'] : 'à venir';
        $nombre_visiteurs = !empty($expo['nombre_visiteurs']) ? intval($expo['nombre_visiteurs']) : 0;
        
        // Déterminer le badge de statut
        $aujourdhui = current_time('mysql');
        $badge_texte = '';
        $badge_classe = '';
        
        if ($date_debut && strtotime($date_debut) > strtotime($aujourdhui)) {
            $badge_texte = 'À venir';
            $badge_classe = 'badge-future';
        } elseif ($date_debut && $date_fin && strtotime($date_debut) <= strtotime($aujourdhui) && strtotime($date_fin) >= strtotime($aujourdhui)) {
            $badge_texte = 'En cours';
            $badge_classe = 'badge-current';
        } else {
            $badge_texte = 'Terminée';
            $badge_classe = 'badge-past';
        }
        
        echo '<div class="mbaa-expo-card">';
        echo '<div class="mbaa-expo-card-image">';
        if ($image_url) {
            echo '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($titre) . '">';
        } else {
            echo '<div class="mbaa-oeuvre-placeholder">';
            echo '<span class="dashicons dashicons-images-alt2"></span>';
            echo '</div>';
        }
        echo '<span class="mbaa-expo-card-badge ' . esc_attr($badge_classe) . '">' . esc_html($badge_texte) . '</span>';
        echo '</div>';
        
        echo '<div class="mbaa-expo-card-content">';
        echo '<h3 class="mbaa-expo-card-title">' . esc_html($titre) . '</h3>';
        
        if ($date_debut && $date_fin) {
            echo '<p class="mbaa-expo-card-dates">';
            echo date_i18n('d M Y', strtotime($date_debut)) . ' - ' . date_i18n('d M Y', strtotime($date_fin));
            echo '</p>';
        }
        
        // Histoire de l'exposition (texte descriptif)
        if ($histoire) {
            echo '<div class="mbaa-expo-card-histoire">';
            echo '<h4>Histoire de l\'exposition</h4>';
            echo '<p>' . wp_trim_words($histoire, 25, '...') . '</p>';
            echo '</div>';
        } elseif ($description) {
            echo '<div class="mbaa-expo-card-histoire">';
            echo '<h4>Description</h4>';
            echo '<p>' . wp_trim_words($description, 25, '...') . '</p>';
            echo '</div>';
        }
        
        // Nombre de visiteurs
        if ($badge_texte === 'Terminée' || $badge_texte === 'En cours') {
            echo '<div class="mbaa-expo-card-visiteurs">';
            echo '<span class="dashicons dashicons-groups"></span>';
            echo '<span class="visiteur-count">' . number_format($nombre_visiteurs) . '</span>';
            echo '<span class="visiteur-label">visiteur' . ($nombre_visiteurs > 1 ? 's' : '') . '</span>';
            echo '</div>';
        }
        
        echo '<div class="mbaa-expo-card-actions">';
        echo '<a href="#" class="button button-secondary">Voir détails</a>';
        if (current_user_can('mbaa_can_access_expositions')) {
            echo '<a href="#" class="button">Modifier</a>';
        }
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
    
    /**
     * Rendu de la page Statistiques
     */
    public static function render_statistiques_page() {
        global $wpdb;
        $db = new MBAA_Database();
        
        // Récupérer les statistiques de base
        $stats = array(
            'total_artistes' => $wpdb->get_var("SELECT COUNT(*) FROM {$db->table_artiste}"),
            'total_oeuvres' => $wpdb->get_var("SELECT COUNT(*) FROM {$db->table_oeuvre}"),
            'total_evenements' => $wpdb->get_var("SELECT COUNT(*) FROM {$db->table_evenement}"),
            'total_expositions' => $wpdb->get_var("SELECT COUNT(*) FROM {$db->table_exposition}"),
            'total_audioguides' => $wpdb->get_var("SELECT COUNT(*) FROM {$db->table_audioguide}"),
        );
        
        echo '<div class="mbaa-wrap">';
        echo '<div class="mbaa-main-content">';
        echo '<div class="mbaa-artwork-header">';
        echo '<div class="mbaa-artwork-icon">';
        echo '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">';
        echo '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />';
        echo '</svg>';
        echo '</div>';
        echo '<div class="mbaa-artwork-title-section">';
        echo '<h1 class="mbaa-artwork-title">Statistiques</h1>';
        echo '<span class="mbaa-status-badge">Vue d\'ensemble du musée</span>';
        echo '</div>';
        echo '</div>';
        
        echo '<div class="mbaa-stats-overview">';
        echo '<div class="mbaa-stat-card">';
        echo '<div class="mbaa-stat-number">' . $stats['total_artistes'] . '</div>';
        echo '<div class="mbaa-stat-label">Artistes</div>';
        echo '</div>';
        echo '<div class="mbaa-stat-card">';
        echo '<div class="mbaa-stat-number">' . $stats['total_oeuvres'] . '</div>';
        echo '<div class="mbaa-stat-label">Œuvres</div>';
        echo '</div>';
        echo '<div class="mbaa-stat-card">';
        echo '<div class="mbaa-stat-number">' . $stats['total_evenements'] . '</div>';
        echo '<div class="mbaa-stat-label">Événements</div>';
        echo '</div>';
        echo '<div class="mbaa-stat-card">';
        echo '<div class="mbaa-stat-number">' . $stats['total_expositions'] . '</div>';
        echo '<div class="mbaa-stat-label">Expositions</div>';
        echo '</div>';
        echo '<div class="mbaa-stat-card">';
        echo '<div class="mbaa-stat-number">' . $stats['total_audioguides'] . '</div>';
        echo '<div class="mbaa-stat-label">Audioguides</div>';
        echo '</div>';
        echo '</div>';
        
        echo '<div class="mbaa-stats-grid">';
        
        // Card Œuvres Populaires (si la colonne existe)
        echo '<div class="mbaa-stat-box">';
        echo '<div class="mbaa-stat-icon">' . self::render_icon('chart', '', '32', '32') . '</div>';
        echo '<div class="mbaa-stat-content">';
        echo '<h3 style="font-family: \'Clash Display\', sans-serif; font-size: 18px; font-weight: 600;">Œuvres Populaires</h3>';
        echo '<p>Tracking des vues et scans QR</p>';
        echo '<a href="' . admin_url('admin.php?page=mbaa-populaires') . '" class="mbaa-stat-link">Voir le classement →</a>';
        echo '</div>';
        echo '</div>';
        
        // Card QR Codes
        echo '<div class="mbaa-stat-box">';
        echo '<div class="mbaa-stat-icon">' . self::render_icon('qr-code', '', '32', '32') . '</div>';
        echo '<div class="mbaa-stat-content">';
        echo '<h3 style="font-family: \'Clash Display\', sans-serif; font-size: 18px; font-weight: 600;">QR Codes</h3>';
        echo '<p>Générer et télécharger des QR codes</p>';
        echo '<a href="' . admin_url('admin.php?page=mbaa-qrcodes') . '" class="mbaa-stat-link">Gérer les QR codes →</a>';
        echo '</div>';
        echo '</div>';
        
        echo '</div>';
        
        echo '</div>';
        echo '</div>';
    }
    
    // =========================================================================
    // NOUVELLES PAGES - TÂCHES 6 & 7
    // =========================================================================
    
    /**
     * Rendu de la page Œuvres Populaires
     */
    public static function render_populaires_page() {
        global $wpdb;
        $db = new MBAA_Database();
        
        // Récupérer les œuvres populaires avec évolution
        $oeuvres_populaires = $db->get_popular_oeuvres_with_evolution(10);
        
        // Statistiques
        $total_vues = $wpdb->get_var("SELECT SUM(vues) FROM {$db->table_oeuvre}");
        $total_qr = count($db->get_qr_codes());
        
        echo '<div class="wrap">';
        
        // Barre de statistiques
        echo '<div class="mbaa-stats-bar">';
        echo '<div class="mbaa-stats-item">';
        echo '<span class="mbaa-stats-label">Total Vues</span>';
        echo '<span class="mbaa-stats-value">' . number_format($total_vues ?: 0, 0, ',', ' ') . '</span>';
        echo '</div>';
        echo '<div class="mbaa-stats-item">';
        echo '<span class="mbaa-stats-label">QR Codes</span>';
        echo '<span class="mbaa-stats-value">' . esc_html($total_qr) . '</span>';
        echo '</div>';
        echo '</div>';
        
        echo '<div class="mbaa-main-content">';
        echo '<div class="mbaa-artwork-header">';
        echo '<div class="mbaa-artwork-icon">';
        echo '<span class="dashicons dashicons-star-filled" style="font-size:40px;height:40px;"></span>';
        echo '</div>';
        echo '<div class="mbaa-artwork-title-section">';
        echo '<h1 class="mbaa-artwork-title">Œuvres Populaires</h1>';
        echo '<span class="mbaa-status-badge">Classement des œuvres les plus consultées</span>';
        echo '</div>';
        echo '</div>';
        
        // Top 3 en cards
        if (!empty($oeuvres_populaires) && count($oeuvres_populaires) >= 3) {
            echo '<h2 style="margin-bottom:20px;">' . self::render_icon('trophy', '', '24', '24') . ' Top 3</h2>';
            echo '<div class="mbaa-stats-grid">';
            
            for ($i = 0; $i < 3; $i++) {
                if (isset($oeuvres_populaires[$i])) {
                    $oeuvre = $oeuvres_populaires[$i];
                    $badge = '';
                    $badge_icon = '';
                    switch ($oeuvre['statut'] ?? 'permanente') {
                        case 'permanente':
                            $badge = 'mbaa-collection-header permanente';
                            $badge_icon = self::render_icon('circle-green', '', '14', '14');
                            break;
                        case 'reserve':
                            $badge = 'mbaa-collection-header reserve';
                            $badge_icon = self::render_icon('box', '', '14', '14');
                            break;
                        case 'pret':
                            $badge = 'mbaa-collection-header pret';
                            $badge_icon = self::render_icon('refresh', '', '14', '14');
                            break;
                        case 'restauration':
                            $badge = 'mbaa-collection-header restauration';
                            $badge_icon = self::render_icon('wrench', '', '14', '14');
                            break;
                    }
                    
                    echo '<div class="mbaa-stat-box" style="position:relative;">';
                    echo '<div style="position:absolute;top:-10px;right:-10px;background:#c9a227;color:#fff;width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:bold;font-size:18px;z-index:1;">' . ($i + 1) . '</div>';
                    
                    if (!empty($oeuvre['image_url'])) {
                        echo '<img src="' . esc_url($oeuvre['image_url']) . '" style="width:100%;height:150px;object-fit:cover;border-radius:8px;margin-bottom:12px;">';
                    } else {
                        echo '<div style="width:100%;height:150px;background:#f5f5f5;border-radius:8px;margin-bottom:12px;display:flex;align-items:center;justify-content:center;"><span class="dashicons dashicons-art" style="font-size:48px;color:#ccc;"></span></div>';
                    }
                    
                    echo '<h3 style="margin:0 0 8px;font-size:16px;">' . esc_html($oeuvre['titre']) . '</h3>';
                    echo '<p style="margin:0 0 8px;color:#666;font-size:13px;">' . esc_html($oeuvre['artiste_nom'] ?? 'Artiste inconnu') . '</p>';
                    echo '<div style="display:flex;justify-content:space-between;align-items:center;">';
                    echo '<span style="font-size:24px;font-weight:bold;color:#c9a227;">' . number_format($oeuvre['total_vues'] ?: 0) . ' vues</span>';
                    echo '<span>' . $badge_icon . ' ' . ucfirst($oeuvre['statut'] ?? 'permanente') . '</span>';
                    echo '</div>';
                    echo '</div>';
                }
            }
            echo '</div>';
        }
        
        // Classement complet
        echo '<h2 style="margin:30px 0 20px;">' . self::render_icon('chart', '', '24', '24') . ' Classement Complet</h2>';
        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr><th>Rang</th><th>Œuvre</th><th>Artiste</th><th>Statut</th><th>Vues</th><th>Vues semaine</th></tr></thead>';
        echo '<tbody>';
        
        $rang = 1;
        foreach ($oeuvres_populaires as $oeuvre) {
            $badge_icon = '';
            switch ($oeuvre['statut'] ?? 'permanente') {
                case 'permanente': $badge_icon = self::render_icon('circle-green', '', '14', '14'); break;
                case 'reserve': $badge_icon = self::render_icon('box', '', '14', '14'); break;
                case 'pret': $badge_icon = self::render_icon('refresh', '', '14', '14'); break;
                case 'restauration': $badge_icon = self::render_icon('wrench', '', '14', '14'); break;
            }
            
            echo '<tr>';
            echo '<td><strong>#' . $rang . '</strong></td>';
            echo '<td>' . esc_html($oeuvre['titre']) . '</td>';
            echo '<td>' . esc_html($oeuvre['artiste_nom'] ?? 'Artiste inconnu') . '</td>';
            echo '<td>' . $badge_icon . ' ' . ucfirst($oeuvre['statut'] ?? 'permanente') . '</td>';
            echo '<td><strong>' . number_format($oeuvre['total_vues'] ?: 0) . '</strong></td>';
            echo '<td>' . number_format($oeuvre['vues_semaine'] ?? 0) . '</td>';
            echo '</tr>';
            $rang++;
        }
        
        echo '</tbody></table>';
        echo '</div></div>';
    }
    
    /**
     * Rendu de la page Bibliothèque Média
     */
    public static function render_media_library_page() {
        ?>
        <div class="wrap mbaa-admin">
            <h1 class="wp-heading-inline"><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <div class="mbaa-dashboard-stats">
                <div class="mbaa-stat-card">
                    <h3>Bibliothèque Média</h3>
                    <p>Gérez tous vos fichiers médias (images, vidéos, documents)</p>
                </div>
            </div>
            
            <div class="mbaa-media-browser">
                <div class="mbaa-media-header">
                    <button type="button" class="button button-primary" id="mbaa-upload-media">
                        Ajouter des fichiers
                    </button>
                    <div class="mbaa-media-filters">
                        <select id="mbaa-media-filter">
                            <option value="all">Tous les médias</option>
                            <option value="image">Images</option>
                            <option value="video">Vidéos</option>
                            <option value="audio">Audio</option>
                            <option value="document">Documents</option>
                        </select>
                        <input type="search" id="mbaa-media-search" placeholder="Rechercher des médias...">
                    </div>
                </div>
                
                <div class="mbaa-media-grid" id="mbaa-media-container">
                    <!-- Les médias seront chargés ici via AJAX -->
                </div>
                
                <div class="mbaa-media-pagination">
                    <button type="button" class="button" id="mbaa-load-more">Charger plus</button>
                </div>
            </div>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            // Charger les médias au chargement de la page
            loadMedia();
            
            function loadMedia(page = 1) {
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'mbaa_load_media',
                        page: page,
                        filter: $('#mbaa-media-filter').val(),
                        search: $('#mbaa-media-search').val(),
                        nonce: '<?php echo wp_create_nonce('mbaa_media_nonce'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#mbaa-media-container').html(response.data.html);
                        }
                    }
                });
            }
            
            // Filtres et recherche
            $('#mbaa-media-filter, #mbaa-media-search').on('change keyup', function() {
                loadMedia();
            });
        });
        </script>
        <?php
    }
    
    /**
     * Rendu de la page QR Codes
     */
    public static function render_qrcodes_page() {
        global $wpdb;
        $db = new MBAA_Database();
        
        // Récupérer les QR codes
        $qr_codes = $db->get_qr_codes();
        
        // Statistiques de scans
        $stats = $db->get_scan_statistics('all');
        
        echo '<div class="wrap">';
        
        // Barre de statistiques
        echo '<div class="mbaa-stats-bar">';
        echo '<div class="mbaa-stats-item">';
        echo '<span class="mbaa-stats-label">QR Codes</span>';
        echo '<span class="mbaa-stats-value">' . count($qr_codes) . '</span>';
        echo '</div>';
        echo '<div class="mbaa-stats-item">';
        echo '<span class="mbaa-stats-label">Total Scans</span>';
        echo '<span class="mbaa-stats-value">' . number_format(isset($stats['total_scans']) ? $stats['total_scans'] : 0, 0, ',', ' ') . '</span>';
        echo '</div>';
        echo '<div class="mbaa-stats-item">';
        $stats_week = $db->get_scan_statistics('week');
        echo '<span class="mbaa-stats-label">Scans semaine</span>';
        echo '<span class="mbaa-stats-value">' . number_format(isset($stats_week['total_scans']) ? $stats_week['total_scans'] : 0, 0, ',', ' ') . '</span>';
        echo '</div>';
        echo '</div>';
        
        echo '<div class="mbaa-main-content">';
        echo '<div class="mbaa-artwork-header">';
        echo '<div class="mbaa-artwork-icon">';
        echo '<span class="dashicons dashicons-qrcode" style="font-size:40px;height:40px;"></span>';
        echo '</div>';
        echo '<div class="mbaa-artwork-title-section">';
        echo '<h1 class="mbaa-artwork-title">QR Codes</h1>';
        echo '<span class="mbaa-status-badge">Génération et suivi des QR codes</span>';
        echo '</div>';
        echo '</div>';
        
        // Actions
        echo '<div class="mbaa-page-actions">';
        echo '<a href="#" class="button button-primary">' . self::render_icon('plus', '', '14', '14') . ' Générer QR Codes</a>';
        echo '<a href="#" class="button">' . self::render_icon('download', '', '14', '14') . ' Tout télécharger</a>';
        echo '</div>';
        
        // Liste des QR codes
        if (!empty($qr_codes)) {
            echo '<table class="wp-list-table widefat fixed striped">';
            echo '<thead><tr><th>QR Code</th><th>Œuvre</th><th>URL</th><th>Scans</th><th>Actions</th></tr></thead>';
            echo '<tbody>';
            
            foreach ($qr_codes as $qr) {
                $oeuvre = null;
                if (!empty($qr['id_oeuvre'])) {
                    $oeuvre = $db->get_oeuvre_with_relations($qr['id_oeuvre']);
                }
                
                echo '<tr>';
                echo '<td>';
                // Simulated QR code display
                echo '<div style="width:80px;height:80px;background:#f5f5f5;border-radius:8px;display:flex;align-items:center;justify-content:center;">';
                echo '<span class="dashicons dashicons-qrcode" style="font-size:40px;color:#666;"></span>';
                echo '</div>';
                echo '</td>';
                echo '<td>' . ($oeuvre ? esc_html($oeuvre['titre']) : 'N/A') . '</td>';
                echo '<td><code style="font-size:11px;max-width:200px;overflow:hidden;text-overflow:ellipsis;display:block;">' . esc_url($qr['url']) . '</code></td>';
                echo '<td><strong>' . number_format($qr['scans_total'] ?: 0) . '</strong></td>';
                echo '<td>';
                echo '<a href="#" class="button button-small">' . self::render_icon('download', '', '14', '14') . '</a> ';
                echo '<a href="#" class="button button-small">' . self::render_icon('pencil', '', '14', '14') . '</a> ';
                echo '<a href="#" class="button button-small button-link-delete">' . self::render_icon('poubelle-de-recyclage', '', '14', '14') . '</a>';
                echo '</td>';
                echo '</tr>';
            }
            
            echo '</tbody></table>';
        } else {
            echo '<div class="mbaa-empty-state">';
            echo '<span class="dashicons dashicons-qrcode"></span>';
            echo '<h3>Aucun QR code</h3>';
            echo '<p>Générez des QR codes pour vos œuvres.</p>';
            echo '</div>';
        }
        
        echo '</div></div>';
    }
    
    /**
     * Rendu de la page Notifications
     */
    public static function render_notifications_page() {
        global $wpdb;
        $db = new MBAA_Database();
        $current_user_id = get_current_user_id();
        
        // Marquer toutes comme lues
        if (isset($_POST['mark_all_read'])) {
            $db->mark_all_notifications_read($current_user_id);
        }
        
        // Récupérer les notifications
        $notifications = $db->get_notifications($current_user_id, 50);
        $unread_count = $db->count_unread_notifications($current_user_id);
        
        echo '<div class="wrap">';
        
        // Barre de statistiques
        echo '<div class="mbaa-stats-bar">';
        echo '<div class="mbaa-stats-item">';
        echo '<span class="mbaa-stats-label">Non lues</span>';
        echo '<span class="mbaa-stats-value" style="color:#ef4444;">' . esc_html($unread_count) . '</span>';
        echo '</div>';
        echo '<div class="mbaa-stats-item">';
        echo '<span class="mbaa-stats-label">Total</span>';
        echo '<span class="mbaa-stats-value">' . count($notifications) . '</span>';
        echo '</div>';
        echo '</div>';
        
        echo '<div class="mbaa-main-content">';
        echo '<div class="mbaa-artwork-header">';
        echo '<div class="mbaa-artwork-icon">';
        echo '<span class="dashicons dashicons-bell" style="font-size:40px;height:40px;"></span>';
        echo '</div>';
        echo '<div class="mbaa-artwork-title-section">';
        echo '<h1 class="mbaa-artwork-title">Notifications</h1>';
        echo '<span class="mbaa-status-badge">Vos notifications</span>';
        echo '</div>';
        echo '</div>';
        
        // Actions
        echo '<div class="mbaa-page-actions">';
        echo '<form method="post" style="display:inline;">';
        echo '<input type="hidden" name="mark_all_read" value="1">';
        echo '<button type="submit" class="button">' . self::render_icon('check', '', '14', '14') . ' Tout marquer comme lu</button>';
        echo '</form>';
        echo '</div>';
        
        // Liste des notifications
        if (!empty($notifications)) {
            echo '<div class="mbaa-notifications-list">';
            
            foreach ($notifications as $notif) {
                $icon = self::render_icon('megaphone', '', '20', '20');
                switch ($notif['type_notification']) {
                    case 'validation': $icon = self::render_icon('check-circle', '', '20', '20'); break;
                    case 'oeuvre': $icon = self::render_icon('palette', '', '20', '20'); break;
                    case 'artiste': $icon = self::render_icon('user', '', '20', '20'); break;
                    case 'evenement': $icon = self::render_icon('calendar', '', '20', '20'); break;
                }
                
                echo '<div class="mbaa-notification-item' . (!$notif['lue'] ? ' unread' : '') . '">';
                echo '<div class="mbaa-notif-icon">' . $icon . '</div>';
                echo '<div class="mbaa-notif-content">';
                echo '<h4>' . esc_html($notif['titre']) . '</h4>';
                echo '<p>' . esc_html($notif['message']) . '</p>';
                echo '<span class="mbaa-notif-date">' . human_time_diff(strtotime($notif['creation']), current_time('timestamp')) . ' ago</span>';
                echo '</div>';
                echo '<div class="mbaa-notif-actions">';
                if (!$notif['lue']) {
                    echo '<a href="#" class="button button-small">' . self::render_icon('check', '', '14', '14') . '</a>';
                }
                echo '<a href="#" class="button button-small button-link-delete">' . self::render_icon('poubelle-de-recyclage', '', '14', '14') . '</a>';
                echo '</div>';
                echo '</div>';
            }
            
            echo '</div>';
        } else {
            echo '<div class="mbaa-empty-state">';
            echo '<span class="dashicons dashicons-bell"></span>';
            echo '<h3>Aucune notification</h3>';
            echo '<p>Vous êtes à jour !</p>';
            echo '</div>';
        }
        
        echo '</div></div>';
    }
    
    /**
     * Rendu de la page Utilisateurs (Super Admin)
     */
    public static function render_utilisateurs_page() {
        global $wpdb;
        $db = new MBAA_Database();
        
        // Récupérer les utilisateurs WordPress avec des rôles MBAA
        $users = get_users(array(
            'role__in' => array('administrator', 'mbaa_gestionnaire', 'mbaa_contributeur'),
            'orderby' => 'display_name',
            'order' => 'ASC'
        ));
        
        echo '<div class="wrap">';
        
        // Barre de statistiques
        echo '<div class="mbaa-stats-bar">';
        echo '<div class="mbaa-stats-item">';
        echo '<span class="mbaa-stats-label">Total</span>';
        echo '<span class="mbaa-stats-value">' . count($users) . '</span>';
        echo '</div>';
        echo '<div class="mbaa-stats-item">';
        echo '<span class="mbaa-stats-label">Admins</span>';
        echo '<span class="mbaa-stats-value">' . count(array_filter($users, fn($u) => $u->has_role('administrator'))) . '</span>';
        echo '</div>';
        echo '<div class="mbaa-stats-item">';
        echo '<span class="mbaa-stats-label">Contributeurs</span>';
        echo '<span class="mbaa-stats-value">' . count(array_filter($users, fn($u) => $u->has_role('mbaa_contributeur'))) . '</span>';
        echo '</div>';
        echo '</div>';
        
        echo '<div class="mbaa-main-content">';
        echo '<div class="mbaa-artwork-header">';
        echo '<div class="mbaa-artwork-icon">';
        echo '<span class="dashicons dashicons-users" style="font-size:40px;height:40px;"></span>';
        echo '</div>';
        echo '<div class="mbaa-artwork-title-section">';
        echo '<h1 class="mbaa-artwork-title">Utilisateurs</h1>';
        echo '<span class="mbaa-status-badge">Gestion des utilisateurs du plugin</span>';
        echo '</div>';
        echo '</div>';
        
        // Cards utilisateurs
        echo '<div class="mbaa-users-grid">';
        
        foreach ($users as $user) {
            $role = 'Contributeur';
            $role_class = 'mbaa-contributeur';
            if ($user->has_role('administrator')) {
                $role = 'Super Admin';
                $role_class = 'mbaa-admin';
            } elseif ($user->has_role('mbaa_gestionnaire')) {
                $role = 'Gestionnaire';
                $role_class = 'mbaa-gestionnaire';
            }
            
            $last_login = get_user_meta($user->ID, 'last_login', true);
            $status_icon = $last_login && (time() - strtotime($last_login) < 3600) ? self::render_icon('circle-green', '', '12', '12') : self::render_icon('circle-outline', '', '12', '12');
            
            echo '<div class="mbaa-user-card">';
            echo '<div class="mbaa-user-avatar">';
            echo get_avatar($user->ID, 80, '', '', array('force_default' => true));
            echo '</div>';
            echo '<div class="mbaa-user-info">';
            echo '<h3>' . esc_html($user->display_name) . '</h3>';
            echo '<span class="mbaa-user-role ' . $role_class . '">' . $role . '</span>';
            echo '<p class="mbaa-user-status">' . $status_icon . ' ' . ($last_login ? 'Il y a ' . human_time_diff(strtotime($last_login)) : 'Jamais') . '</p>';
            echo '</div>';
            echo '<div class="mbaa-user-actions">';
            echo '<a href="#" class="button button-small">' . self::render_icon('pencil', '', '14', '14') . '</a>';
            echo '<a href="#" class="button button-small button-link-delete">' . self::render_icon('poubelle-de-recyclage', '', '14', '14') . '</a>';
            echo '</div>';
            echo '</div>';
        }
        
        echo '</div>';
        echo '</div></div>';
    }
    
    /**
     * Rendu de la page Historique (Super Admin)
     */
    public static function render_historique_page() {
        global $wpdb;
        $db = new MBAA_Database();
        
        // Récupérer l'historique
        $historique = $db->get_audit_log(array(), 50);
        
        echo '<div class="wrap">';
        
        // Barre de statistiques
        echo '<div class="mbaa-stats-bar">';
        echo '<div class="mbaa-stats-item">';
        echo '<span class="mbaa-stats-label">Total actions</span>';
        echo '<span class="mbaa-stats-value">' . count($historique) . '</span>';
        echo '</div>';
        echo '</div>';
        
        echo '<div class="mbaa-main-content">';
        echo '<div class="mbaa-artwork-header">';
        echo '<div class="mbaa-artwork-icon">';
        echo '<span class="dashicons dashicons-backup" style="font-size:40px;height:40px;"></span>';
        echo '</div>';
        echo '<div class="mbaa-artwork-title-section">';
        echo '<h1 class="mbaa-artwork-title">Historique</h1>';
        echo '<span class="mbaa-status-badge">Journal des modifications</span>';
        echo '</div>';
        echo '</div>';
        
        // Filtres
        echo '<div class="mbaa-filters" style="margin-bottom:24px;padding:16px;background:#fff;border:1px solid #e5e5e5;border-radius:12px;">';
        echo '<form method="get" style="display:flex;gap:12px;flex-wrap:wrap;align-items:center;">';
        echo '<input type="hidden" name="page" value="mbaa-historique">';
        echo '<label>Utilisateur: <select name="user_id"><option value="">Tous</option>';
        $users = get_users(array('role__in' => array('administrator', 'mbaa_gestionnaire', 'mbaa_contributeur')));
        foreach ($users as $u) {
            echo '<option value="' . $u->ID . '">' . esc_html($u->display_name) . '</option>';
        }
        echo '</select></label>';
        echo '<label>Action: <select name="action"><option value="">Toutes</option>';
        echo '<option value="ajout">Ajout</option>';
        echo '<option value="modification">Modification</option>';
        echo '<option value="suppression">Suppression</option>';
        echo '</select></label>';
        echo '<label>Type: <select name="element_type"><option value="">Tous</option>';
        echo '<option value="oeuvre">Œuvre</option>';
        echo '<option value="artiste">Artiste</option>';
        echo '<option value="evenement">Événement</option>';
        echo '</select></label>';
        echo '<button type="submit" class="button button-primary">Filtrer</button>';
        echo '<a href="?page=mbaa-historique" class="button">Réinitialiser</a>';
        echo '</form>';
        echo '</div>';
        
        // Tableau historique
        if (!empty($historique)) {
            echo '<table class="wp-list-table widefat fixed striped">';
            echo '<thead><tr><th>Date</th><th>Utilisateur</th><th>Action</th><th>Élément</th><th>Titre</th></tr></thead>';
            echo '<tbody>';
            
            foreach ($historique as $entry) {
                $user = get_userdata($entry['user_id']);
                $user_name = $user ? $user->display_name : 'N/A';
                
                $action_icon = self::render_icon('pencil', '', '16', '16');
                switch ($entry['action']) {
                    case 'ajout': $action_icon = self::render_icon('plus', '', '16', '16'); break;
                    case 'modification': $action_icon = self::render_icon('pencil', '', '16', '16'); break;
                    case 'suppression': $action_icon = self::render_icon('poubelle-de-recyclage', '', '16', '16'); break;
                }
                
                echo '<tr>';
                echo '<td>' . date_i18n('d/m/Y H:i', strtotime($entry['creation'])) . '</td>';
                echo '<td>' . esc_html($user_name) . '</td>';
                echo '<td>' . $action_icon . ' ' . ucfirst($entry['action']) . '</td>';
                echo '<td>' . ucfirst($entry['element_type']) . '</td>';
                echo '<td>' . esc_html($entry['element_titre'] ?? 'N/A') . '</td>';
                echo '</tr>';
            }
            
            echo '</tbody></table>';
        } else {
            echo '<div class="mbaa-empty-state">';
            echo '<span class="dashicons dashicons-backup"></span>';
            echo '<h3>Aucun historique</h3>';
            echo '<p>Les modifications apparaîtront ici.</p>';
            echo '</div>';
        }
        
        echo '</div></div>';
    }
    
    /**
     * Recherche d'artistes via AJAX
     */
    public function recherche_artiste() {
        // Arrêter toute sortie de buffer
        if (ob_get_length()) ob_clean();
        
        // Debug log
        error_log('MBAA: recherche_artiste appelée');
        
        // Vérifier le nonce (accepter les deux noms de nonce pour compatibilité)
        $nonce_valid = false;
        if (isset($_POST['nonce'])) {
            if (wp_verify_nonce($_POST['nonce'], 'mbaa_nonce') || wp_verify_nonce($_POST['nonce'], 'mbaa_recherche_artiste_nonce')) {
                $nonce_valid = true;
            }
        }
        
        if (!$nonce_valid) {
            error_log('MBAA: nonce invalide');
            wp_send_json_error('Sécurité invalide');
            wp_die();
        }
        
        // Accepter les deux noms de paramètres
        $nom = '';
        if (isset($_POST['nom'])) {
            $nom = sanitize_text_field($_POST['nom']);
        } elseif (isset($_POST['query'])) {
            $nom = sanitize_text_field($_POST['query']);
        }
        
        error_log('MBAA: recherche pour: ' . $nom);
        
        if (empty($nom) || strlen($nom) < 2) {
            wp_send_json_error('Recherche trop courte');
            wp_die();
        }
        
        global $wpdb;
        
        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT id_artiste, nom, nationalite FROM {$wpdb->prefix}mbaa_artiste 
             WHERE nom LIKE %s 
             ORDER BY nom ASC 
             LIMIT 10",
            '%' . $wpdb->esc_like($nom) . '%'
        ));
        
        error_log('MBAA: résultats trouvés: ' . count($results));
        
        if ($results) {
            wp_send_json_success($results);
        } else {
            wp_send_json_success(array());
        }
        wp_die();
    }

    /**
     * Générer un PDF pour une œuvre
     */
    public function handle_generate_oeuvre_pdf() {
        if (!current_user_can('manage_options') && !current_user_can('mbaa_can_access_oeuvres')) {
            wp_die(__('Permission refusée.', 'mbaa'));
        }

        $oeuvre_id = isset($_GET['oeuvre_id']) ? intval($_GET['oeuvre_id']) : 0;

        if (!$oeuvre_id) {
            wp_die(__('ID d\'œuvre manquant.', 'mbaa'));
        }

        $pdf_generator = new MBAA_PDF_Generator();
        $pdf_path = $pdf_generator->generate_oeuvre_pdf($oeuvre_id);

        if ($pdf_path) {
            $filename = 'oeuvre-' . get_post_field('post_name', $oeuvre_id) . '.pdf';
            $pdf_generator->download_pdf($pdf_path, $filename);
        } else {
            wp_die(__('Erreur lors de la génération du PDF.', 'mbaa'));
        }
    }

    /**
     * Générer un PDF pour un artiste
     */
    public function handle_generate_artiste_pdf() {
        if (!current_user_can('manage_options') && !current_user_can('mbaa_can_access_artistes')) {
            wp_die(__('Permission refusée.', 'mbaa'));
        }

        $artiste_id = isset($_GET['artiste_id']) ? intval($_GET['artiste_id']) : 0;

        if (!$artiste_id) {
            wp_die(__('ID d\'artiste manquant.', 'mbaa'));
        }

        $pdf_generator = new MBAA_PDF_Generator();
        $pdf_path = $pdf_generator->generate_artiste_pdf($artiste_id);

        if ($pdf_path) {
            // Récupérer le nom de l'artiste pour le nom du fichier
            $artiste_manager = new MBAA_Artiste();
            $artiste = $artiste_manager->get_artiste($artiste_id);
            $filename = $artiste ? 'artiste-' . sanitize_title($artiste->nom) . '.pdf' : 'artiste-' . $artiste_id . '.pdf';
            $pdf_generator->download_pdf($pdf_path, $filename);
        } else {
            wp_die(__('Erreur lors de la génération du PDF.', 'mbaa'));
        }
    }
}