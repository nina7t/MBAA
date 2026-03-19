<?php

 // Classe de gestion des artistes


if (!defined('ABSPATH')) {
    exit;
}

class MBAA_Artiste {
    
    private $wpdb;
    private $table_name;
    
    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $db = new MBAA_Database();
        $this->table_name = $db->table_artiste;
    }
    
    
    /**
     * Récupérer tous les artistes
     */
    public function get_all_artistes($orderby = 'nom', $order = 'ASC') {
        error_log('MBAA DEBUG: get_all_artistes appelé');
        error_log('MBAA DEBUG: Table = ' . $this->table_name);
        
        // Note: prepare ne supporte pas les identifiants de colonnes, donc on utilise une approche différente
        $allowed_orderby = array('id_artiste', 'nom', 'date_naissance', 'creation');
        $allowed_order = array('ASC', 'DESC');
        
        if (!in_array($orderby, $allowed_orderby)) {
            $orderby = 'nom';
        }
        if (!in_array($order, $allowed_order)) {
            $order = 'ASC';
        }
        
        $sql = "SELECT * FROM {$this->table_name} ORDER BY {$orderby} {$order}";
        error_log('MBAA DEBUG: SQL = ' . $sql);
        
        $results = $this->wpdb->get_results($sql);
        error_log('MBAA DEBUG: Résultats SQL = ' . print_r($results, true));
        error_log('MBAA DEBUG: Erreur SQL = ' . $this->wpdb->last_error);
        
        return $results;
    }
    
    
    /**
     * Récupérer un artiste par son ID
     */
    public function get_artiste($id) {
        return $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->table_name} WHERE id_artiste = %d",
                $id
            )
        );
    }
    
    
    /**
     * Rechercher des artistes
     */
    public function search_artistes($search_term) {
        return $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->table_name} 
                WHERE nom LIKE %s 
                OR biographie LIKE %s 
                ORDER BY nom ASC",
                '%' . $this->wpdb->esc_like($search_term) . '%',
                '%' . $this->wpdb->esc_like($search_term) . '%'
            )
        );
    }
    
    
     // Sauvegarder un artiste (création ou mise à jour)
  
    public function save_artiste($data) {
        $artiste_data = array(
            'nom' => sanitize_text_field($data['nom']),
            'biographie' => wp_kses_post($data['biographie']),
            'date_naissance' => !empty($data['date_naissance']) ? sanitize_text_field($data['date_naissance']) : null,
            'date_deces' => !empty($data['date_deces']) ? sanitize_text_field($data['date_deces']) : null,
            'nationalite' => !empty($data['nationalite']) ? sanitize_text_field($data['nationalite']) : null,
            'image_url' => !empty($data['image_url']) ? esc_url_raw($data['image_url']) : null,
            'lieu_naissance' => !empty($data['lieu_naissance']) ? sanitize_text_field($data['lieu_naissance']) : null,
            'site_web' => !empty($data['site_web']) ? esc_url_raw($data['site_web']) : null,
            'reseaux_sociaux' => !empty($data['reseaux_sociaux']) ? sanitize_text_field($data['reseaux_sociaux']) : null,
            'style_art' => !empty($data['style_art']) ? sanitize_text_field($data['style_art']) : null,
            'visible' => isset($data['visible']) ? 1 : 0,
            'audio_biographie' => !empty($data['audio_biographie']) ? esc_url_raw($data['audio_biographie']) : null
        );
        
        $format = array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s');
        
        // Mise à jour ou création
        if (!empty($data['id_artiste'])) {
            // Mise à jour
            $artiste_data['mis_a_jour'] = current_time('mysql');
            
            $result = $this->wpdb->update(
                $this->table_name,
                $artiste_data,
                array('id_artiste' => intval($data['id_artiste'])),
                $format,
                array('%d')
            );
            
            return $result !== false ? intval($data['id_artiste']) : false;
        } else {
            // Création
            $artiste_data['creation'] = current_time('mysql');
            $artiste_data['mis_a_jour'] = current_time('mysql');
            
            $result = $this->wpdb->insert(
                $this->table_name,
                $artiste_data,
                $format
            );
            
            return $result !== false ? $this->wpdb->insert_id : false;
        }
    }
    
    
    /**
     * Supprimer un artiste
     */
    public function delete_artiste($id) {
        // Vérifier si l'artiste a des œuvres associées
        $db = new MBAA_Database();
        $count = $this->wpdb->get_var(
            $this->wpdb->prepare(
                "SELECT COUNT(*) FROM {$db->table_oeuvre} WHERE id_artiste = %d",
                $id
            )
        );
        
        if ($count > 0) {
            // Ne pas supprimer si des œuvres sont associées
            return new WP_Error('has_oeuvres', 'Impossible de supprimer cet artiste car il a des œuvres associées.');
        }
        
        return $this->wpdb->delete(
            $this->table_name,
            array('id_artiste' => $id),
            array('%d')
        );
    }
    
    
    /**
     * Compter le nombre d'œuvres d'un artiste
     */
    public function count_oeuvres($id_artiste) {
        $db = new MBAA_Database();
        return $this->wpdb->get_var(
            $this->wpdb->prepare(
                "SELECT COUNT(*) FROM {$db->table_oeuvre} WHERE id_artiste = %d",
                $id_artiste
            )
        );
    }
    
    
    /**
     * Récupérer les œuvres d'un artiste
     */
    public function get_oeuvres($id_artiste) {
        $db = new MBAA_Database();
        return $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT * FROM {$db->table_oeuvre} WHERE id_artiste = %d ORDER BY date_creation DESC",
                $id_artiste
            )
        );
    }
    
    
     // Récupérer les artistes pour un select dropdown
  
    public function get_artistes_for_select() {
        return $this->wpdb->get_results(
            "SELECT id_artiste, nom FROM {$this->table_name} ORDER BY nom ASC"
        );
    }
}
