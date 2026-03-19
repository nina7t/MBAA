<?php
/**
 * Classe de gestion des audioguides
 */

if (!defined('ABSPATH')) {
    exit;
}

class MBAA_Audioguide {
    
    private $wpdb;
    private $table_name;
    private $db;
    
    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->db = new MBAA_Database();
        $this->table_name = $this->db->table_audioguide;
    }
    
    /**
     * Récupérer tous les audioguides
     */
    public function get_all_audioguides() {
        $sql = "SELECT a.*, o.titre as oeuvre_titre, ar.nom as artiste_nom
                FROM {$this->table_name} a
                INNER JOIN {$this->db->table_oeuvre} o ON a.id_oeuvre = o.id_oeuvre
                LEFT JOIN {$this->db->table_artiste} ar ON o.id_artiste = ar.id_artiste
                ORDER BY o.titre ASC";
        
        return $this->wpdb->get_results($sql);
    }
    
    /**
     * Récupérer un audioguide par son ID
     */
    public function get_audioguide($id) {
        return $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT a.*, o.titre as oeuvre_titre
                FROM {$this->table_name} a
                INNER JOIN {$this->db->table_oeuvre} o ON a.id_oeuvre = o.id_oeuvre
                WHERE a.id_audioguide = %d",
                $id
            )
        );
    }
    
    /**
     * Récupérer l'audioguide d'une œuvre
     */
    public function get_audioguide_by_oeuvre($id_oeuvre) {
        return $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->table_name} WHERE id_oeuvre = %d",
                $id_oeuvre
            )
        );
    }
    
    /**
     * Sauvegarder un audioguide
     */
    public function save_audioguide($data) {
        // Vérifier si l'œuvre existe
        $oeuvre_exists = $this->wpdb->get_var(
            $this->wpdb->prepare(
                "SELECT COUNT(*) FROM {$this->db->table_oeuvre} WHERE id_oeuvre = %d",
                intval($data['id_oeuvre'])
            )
        );
        
        if (!$oeuvre_exists) {
            return new WP_Error('invalid_oeuvre', 'L\'œuvre sélectionnée n\'existe pas.');
        }
        
        $audioguide_data = array(
            'id_oeuvre' => intval($data['id_oeuvre']),
            'fichier_audio_url' => esc_url_raw($data['fichier_audio_url']),
            'duree_secondes' => !empty($data['duree_secondes']) ? intval($data['duree_secondes']) : null,
            'langue' => !empty($data['langue']) ? sanitize_text_field($data['langue']) : 'fr',
            'transcription' => !empty($data['transcription']) ? wp_kses_post($data['transcription']) : null
        );
        
        $format = array('%d', '%s', '%d', '%s', '%s');
        
        if (!empty($data['id_audioguide'])) {
            // Mise à jour
            $result = $this->wpdb->update(
                $this->table_name,
                $audioguide_data,
                array('id_audioguide' => intval($data['id_audioguide'])),
                $format,
                array('%d')
            );
            
            return $result !== false ? intval($data['id_audioguide']) : false;
        } else {
            // Vérifier s'il existe déjà un audioguide pour cette œuvre
            $existing = $this->get_audioguide_by_oeuvre($data['id_oeuvre']);
            
            if ($existing) {
                return new WP_Error('audioguide_exists', 'Un audioguide existe déjà pour cette œuvre.');
            }
            
            // Création
            $result = $this->wpdb->insert(
                $this->table_name,
                $audioguide_data,
                $format
            );
            
            return $result !== false ? $this->wpdb->insert_id : false;
        }
    }
    
    /**
     * Supprimer un audioguide
     */
    public function delete_audioguide($id) {
        return $this->wpdb->delete(
            $this->table_name,
            array('id_audioguide' => $id),
            array('%d')
        );
    }
    
    /**
     * Formater la durée en format lisible
     */
    public function format_duree($secondes) {
        if (empty($secondes)) {
            return '-';
        }
        
        $minutes = floor($secondes / 60);
        $secondes = $secondes % 60;
        
        return sprintf('%d:%02d', $minutes, $secondes);
    }
}
