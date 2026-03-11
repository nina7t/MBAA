<?php
/**
 * Classe de gestion des événements
 */

if (!defined('ABSPATH')) {
    exit;
}

class MBAA_Evenement {
    
    private $wpdb;
    private $table_name;
    private $db;
    
    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->db = new MBAA_Database();
        $this->table_name = $this->db->table_evenement;
    }
    
    /**
     * Récupérer tous les événements
     */
    public function get_all_evenements($filters = array()) {
        $where_clauses = array();
        $where_values = array();
        
        // Filtre par type
        if (!empty($filters['id_type_evenement'])) {
            $where_clauses[] = "e.id_type_evenement = %d";
            $where_values[] = $filters['id_type_evenement'];
        }
        
        // Filtre par date (événements à venir)
        if (!empty($filters['a_venir'])) {
            $where_clauses[] = "e.date_evenement >= CURDATE()";
        }
        
        // Filtre par date (événements passés)
        if (!empty($filters['passes'])) {
            $where_clauses[] = "e.date_evenement < CURDATE()";
        }
        
        $where_sql = !empty($where_clauses) ? 'WHERE ' . implode(' AND ', $where_clauses) : '';
        
        $sql = "SELECT e.*, t.nom_type, t.categorie as type_categorie
                FROM {$this->table_name} e
                LEFT JOIN {$this->db->table_type_evenement} t ON e.id_type_evenement = t.id_type_evenement
                {$where_sql}
                ORDER BY e.date_evenement DESC, e.heure_debut ASC";
        
        if (!empty($where_values)) {
            $sql = $this->wpdb->prepare($sql, $where_values);
        }
        
        return $this->wpdb->get_results($sql);
    }
    
    /**
     * Récupérer un événement par son ID
     */
    public function get_evenement($id) {
        return $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT e.*, t.nom_type, t.categorie as type_categorie
                FROM {$this->table_name} e
                LEFT JOIN {$this->db->table_type_evenement} t ON e.id_type_evenement = t.id_type_evenement
                WHERE e.id_evenement = %d",
                $id
            )
        );
    }
    
    /**
     * Récupérer les événements à venir
     */
    public function get_upcoming_evenements($limit = 10) {
        return $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT e.*, t.nom_type
                FROM {$this->table_name} e
                LEFT JOIN {$this->db->table_type_evenement} t ON e.id_type_evenement = t.id_type_evenement
                WHERE e.date_evenement >= CURDATE()
                ORDER BY e.date_evenement ASC, e.heure_debut ASC
                LIMIT %d",
                $limit
            )
        );
    }
    
    /**
     * Sauvegarder un événement
     */
    public function save_evenement($data) {
        $evenement_data = array(
            'titre' => sanitize_text_field($data['titre']),
            'descriptif' => wp_kses_post($data['descriptif']),
            'date_evenement' => sanitize_text_field($data['date_evenement']),
            'heure_debut' => !empty($data['heure_debut']) ? sanitize_text_field($data['heure_debut']) : null,
            'heure_fin' => !empty($data['heure_fin']) ? sanitize_text_field($data['heure_fin']) : null,
            'id_type_evenement' => !empty($data['id_type_evenement']) ? intval($data['id_type_evenement']) : null,
            'est_gratuit' => isset($data['est_gratuit']) ? 1 : 0,
            'prix' => !empty($data['prix']) ? floatval($data['prix']) : null,
            'label_tarif' => !empty($data['label_tarif']) ? sanitize_text_field($data['label_tarif']) : null,
            'image_url' => !empty($data['image_url']) ? esc_url_raw($data['image_url']) : null,
            'capacite_max' => !empty($data['capacite_max']) ? intval($data['capacite_max']) : null,
            'lieu_musee' => !empty($data['lieu_musee']) ? sanitize_text_field($data['lieu_musee']) : null,
            'public_cible' => !empty($data['public_cible']) ? sanitize_text_field($data['public_cible']) : null,
            'public_ado' => isset($data['public_ado']) ? 1 : 0,
            'public_enfant' => isset($data['public_enfant']) ? 1 : 0,
            'public_adulte' => isset($data['public_adulte']) ? 1 : 0,
            'public_tout_public' => isset($data['public_tout_public']) ? 1 : 0,
            'accessible_handicap' => isset($data['accessible_handicap']) ? 1 : 0,
            'niveau_debutant' => isset($data['niveau_debutant']) ? 1 : 0,
            'niveau_intermediaire' => isset($data['niveau_intermediaire']) ? 1 : 0,
            'niveau_confirme' => isset($data['niveau_confirme']) ? 1 : 0,
            'intervenant' => !empty($data['intervenant']) ? sanitize_text_field($data['intervenant']) : null
        );
        
        $format = array('%s', '%s', '%s', '%s', '%s', '%d', '%d', '%f', '%s', '%s', '%d', '%s', '%s', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%s');
        
        if (!empty($data['id_evenement'])) {
            // Mise à jour
            $result = $this->wpdb->update(
                $this->table_name,
                $evenement_data,
                array('id_evenement' => intval($data['id_evenement'])),
                $format,
                array('%d')
            );
            
            return $result !== false ? intval($data['id_evenement']) : false;
        } else {
            // Création
            $result = $this->wpdb->insert(
                $this->table_name,
                $evenement_data,
                $format
            );
            
            return $result !== false ? $this->wpdb->insert_id : false;
        }
    }
    
    /**
     * Supprimer un événement
     */
    public function delete_evenement($id) {
        return $this->wpdb->delete(
            $this->table_name,
            array('id_evenement' => $id),
            array('%d')
        );
    }
    
    /**
     * Récupérer les types d'événements
     */
    public function get_types_evenement() {
        return $this->wpdb->get_results(
            "SELECT * FROM {$this->db->table_type_evenement} ORDER BY nom_type ASC"
        );
    }
    
    /**
     * Rechercher des événements
     */
    public function search_evenements($search_term) {
        return $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT e.*, t.nom_type
                FROM {$this->table_name} e
                LEFT JOIN {$this->db->table_type_evenement} t ON e.id_type_evenement = t.id_type_evenement
                WHERE e.titre LIKE %s 
                OR e.descriptif LIKE %s
                ORDER BY e.date_evenement DESC",
                '%' . $this->wpdb->esc_like($search_term) . '%',
                '%' . $this->wpdb->esc_like($search_term) . '%'
            )
        );
    }
}
