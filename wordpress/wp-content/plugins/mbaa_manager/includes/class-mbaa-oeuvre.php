<?php
/**
 * Classe de gestion des œuvres
 */

if (!defined('ABSPATH')) {
    exit;
}

class MBAA_Oeuvre {
    
    private $wpdb;
    private $table_name;
    private $db;
    
    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->db = new MBAA_Database();
        $this->table_name = $this->db->table_oeuvre;
    }
    
    /**
     * Récupérer toutes les œuvres avec informations liées
     */
    public function get_all_oeuvres($filters = array()) {
        $where_clauses = array();
        $where_values = array();
        
        // Filtres
        if (!empty($filters['id_artiste'])) {
            $where_clauses[] = "o.id_artiste = %d";
            $where_values[] = $filters['id_artiste'];
        }
        
        if (!empty($filters['id_salle'])) {
            $where_clauses[] = "o.id_salle = %d";
            $where_values[] = $filters['id_salle'];
        }
        
        if (!empty($filters['visible_galerie'])) {
            $where_clauses[] = "o.visible_galerie = %d";
            $where_values[] = $filters['visible_galerie'];
        }
        
        $where_sql = !empty($where_clauses) ? 'WHERE ' . implode(' AND ', $where_clauses) : '';
        
        $sql = "SELECT o.*, 
                a.nom as artiste_nom,
                e.nom_epoque,
                s.nom_salle,
                m.nom_medium,
                mv.nom_mouvement,
                c.nom_categorie
                FROM {$this->table_name} o
                LEFT JOIN {$this->db->table_artiste} a ON o.id_artiste = a.id_artiste
                LEFT JOIN {$this->db->table_epoque} e ON o.id_epoque = e.id_epoque
                LEFT JOIN {$this->db->table_salle} s ON o.id_salle = s.id_salle
                LEFT JOIN {$this->db->table_medium} m ON o.id_medium = m.id_medium
                LEFT JOIN {$this->db->table_mouvement} mv ON o.id_mouvement = mv.id_mouvement
                LEFT JOIN {$this->db->table_categorie} c ON o.id_categorie = c.id_categorie
                {$where_sql}
                ORDER BY o.creation DESC";
        
        if (!empty($where_values)) {
            $sql = $this->wpdb->prepare($sql, $where_values);
        }
        
        return $this->wpdb->get_results($sql);
    }
    
    /**
     * Récupérer une œuvre par son ID
     */
    public function get_oeuvre($id) {
        $sql = $this->wpdb->prepare(
            "SELECT o.*, 
            a.nom as artiste_nom,
            e.nom_epoque,
            s.nom_salle,
            m.nom_medium,
            mv.nom_mouvement,
            c.nom_categorie
            FROM {$this->table_name} o
            LEFT JOIN {$this->db->table_artiste} a ON o.id_artiste = a.id_artiste
            LEFT JOIN {$this->db->table_epoque} e ON o.id_epoque = e.id_epoque
            LEFT JOIN {$this->db->table_salle} s ON o.id_salle = s.id_salle
            LEFT JOIN {$this->db->table_medium} m ON o.id_medium = m.id_medium
            LEFT JOIN {$this->db->table_mouvement} mv ON o.id_mouvement = mv.id_mouvement
            LEFT JOIN {$this->db->table_categorie} c ON o.id_categorie = c.id_categorie
            WHERE o.id_oeuvre = %d",
            $id
        );
        
        return $this->wpdb->get_row($sql);
    }
    
    /**
     * Rechercher des œuvres
     */
    public function search_oeuvres($search_term) {
        return $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT o.*, a.nom as artiste_nom
                FROM {$this->table_name} o
                LEFT JOIN {$this->db->table_artiste} a ON o.id_artiste = a.id_artiste
                WHERE o.titre LIKE %s 
                OR o.description LIKE %s 
                OR a.nom LIKE %s
                ORDER BY o.titre ASC",
                '%' . $this->wpdb->esc_like($search_term) . '%',
                '%' . $this->wpdb->esc_like($search_term) . '%',
                '%' . $this->wpdb->esc_like($search_term) . '%'
            )
        );
    }
    
    /**
     * Sauvegarder une œuvre
     */
    public function save_oeuvre($data) {
        $oeuvre_data = array(
            'titre' => sanitize_text_field($data['titre']),
            'date_creation' => !empty($data['date_creation']) ? sanitize_text_field($data['date_creation']) : null,
            'description' => wp_kses_post($data['description']),
            'image_url' => !empty($data['image_url']) ? esc_url_raw($data['image_url']) : null,
            'dimensions' => !empty($data['dimensions']) ? sanitize_text_field($data['dimensions']) : null,
            'numero_inventaire' => !empty($data['numero_inventaire']) ? sanitize_text_field($data['numero_inventaire']) : null,
            'technique' => !empty($data['technique']) ? sanitize_textarea_field($data['technique']) : null,
            'id_artiste' => !empty($data['id_artiste']) ? intval($data['id_artiste']) : null,
            'id_epoque' => !empty($data['id_epoque']) ? intval($data['id_epoque']) : null,
            'id_salle' => !empty($data['id_salle']) ? intval($data['id_salle']) : null,
            'id_medium' => !empty($data['id_medium']) ? intval($data['id_medium']) : null,
            'id_mouvement' => !empty($data['id_mouvement']) ? intval($data['id_mouvement']) : null,
            'id_categorie' => !empty($data['id_categorie']) ? intval($data['id_categorie']) : null,
            'visible_galerie' => isset($data['visible_galerie']) ? 1 : 0,
            'visible_accueil' => isset($data['visible_accueil']) ? 1 : 0,
            'audio_url' => !empty($data['audio_url']) ? esc_url_raw($data['audio_url']) : null,
            'provenance' => !empty($data['provenance']) ? sanitize_text_field($data['provenance']) : null
        );
        
        $format = array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%s', '%s');
        
        if (!empty($data['id_oeuvre'])) {
            // Mise à jour
            $result = $this->wpdb->update(
                $this->table_name,
                $oeuvre_data,
                array('id_oeuvre' => intval($data['id_oeuvre'])),
                $format,
                array('%d')
            );
            
            $oeuvre_id = intval($data['id_oeuvre']);
            
            // Mettre à jour le QR code si nécessaire
            if ($result !== false && class_exists('MBAA_Oeuvre_Pages')) {
                $this->update_qr_code($oeuvre_id);
            }
            
            return $oeuvre_id;
        } else {
            // Création
            $oeuvre_data['creation'] = current_time('mysql');
            $oeuvre_data['mis_a_jour'] = current_time('mysql');
            
            $result = $this->wpdb->insert(
                $this->table_name,
                $oeuvre_data,
                $format
            );
            
            $oeuvre_id = $this->wpdb->insert_id;
            
            // Créer automatiquement le QR code lors de la création de l'œuvre
            if ($result !== false && $oeuvre_id && class_exists('MBAA_Oeuvre_Pages')) {
                $this->create_qr_code($oeuvre_id);
            }
            
            return $oeuvre_id;
        }
    }
    
    /**
     * Créer automatiquement un QR code pour une œuvre
     */
    private function create_qr_code($oeuvre_id) {
        global $wpdb;
        $table_qr = $wpdb->prefix . 'mbaa_qr_codes';
        
        // Vérifier si un QR code existe déjà
        $existing = $wpdb->get_row(
            $wpdb->prepare("SELECT id_qr FROM {$table_qr} WHERE id_oeuvre = %d", $oeuvre_id)
        );
        
        if ($existing) {
            return $existing->id_qr; // Déjà existant
        }
        
        // Générer l'URL unique
        $url = MBAA_Oeuvre_Pages::get_oeuvre_url($oeuvre_id);
        $code_qr = 'QR_' . $oeuvre_id . '_' . uniqid();
        
        $wpdb->insert(
            $table_qr,
            array(
                'id_oeuvre' => $oeuvre_id,
                'code_qr' => $code_qr,
                'url' => $url,
                'type' => 'oeuvre',
                'statut' => 'actif'
            ),
            array('%d', '%s', '%s', '%s', '%s')
        );
        
        return $wpdb->insert_id;
    }
    
    /**
     * Mettre à jour le QR code d'une œuvre
     */
    private function update_qr_code($oeuvre_id) {
        global $wpdb;
        $table_qr = $wpdb->prefix . 'mbaa_qr_codes';
        
        // Récupérer le QR code existant
        $existing = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table_qr} WHERE id_oeuvre = %d", $oeuvre_id)
        );
        
        // Générer l'URL actuelle
        $url = MBAA_Oeuvre_Pages::get_oeuvre_url($oeuvre_id);
        
        if ($existing) {
            // Mettre à jour l'URL
            $wpdb->update(
                $table_qr,
                array('url' => $url, 'statut' => 'actif'),
                array('id_qr' => $existing->id_qr),
                array('%s', '%s'),
                array('%d')
            );
        } else {
            // Créer nouveau
            $code_qr = 'QR_' . $oeuvre_id . '_' . uniqid();
            $wpdb->insert(
                $table_qr,
                array(
                    'id_oeuvre' => $oeuvre_id,
                    'code_qr' => $code_qr,
                    'url' => $url,
                    'type' => 'oeuvre',
                    'statut' => 'actif'
                ),
                array('%d', '%s', '%s', '%s', '%s')
            );
        }
    }
    
    /**
     * Supprimer une œuvre
     */
    public function delete_oeuvre($id) {
        // Supprimer d'abord les audioguides associés
        $this->wpdb->delete(
            $this->db->table_audioguide,
            array('id_oeuvre' => $id),
            array('%d')
        );
        
        // Supprimer les relations avec les expositions
        $this->wpdb->delete(
            $this->db->table_oeuvre_exposition,
            array('id_oeuvre' => $id),
            array('%d')
        );
        
        // Supprimer l'œuvre
        return $this->wpdb->delete(
            $this->table_name,
            array('id_oeuvre' => $id),
            array('%d')
        );
    }
    
    /**
     * Récupérer les œuvres pour la galerie publique
     */
    public function get_galerie_oeuvres() {
        $sql = "SELECT o.*, a.nom as artiste_nom
                FROM {$this->table_name} o
                LEFT JOIN {$this->db->table_artiste} a ON o.id_artiste = a.id_artiste
                WHERE o.visible_galerie = 1
                ORDER BY o.creation DESC";
        
        return $this->wpdb->get_results($sql);
    }
    
    /**
     * Récupérer les œuvres pour l'accueil
     */
    public function get_accueil_oeuvres($limit = 6) {
        return $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT o.*, a.nom as artiste_nom
                FROM {$this->table_name} o
                LEFT JOIN {$this->db->table_artiste} a ON o.id_artiste = a.id_artiste
                WHERE o.visible_accueil = 1
                ORDER BY o.creation DESC
                LIMIT %d",
                $limit
            )
        );
    }
    
    /**
     * Récupérer les œuvres pour un select dropdown
     */
    public function get_oeuvres_for_select() {
        return $this->wpdb->get_results(
            "SELECT id_oeuvre, titre FROM {$this->table_name} ORDER BY titre ASC"
        );
    }
}
