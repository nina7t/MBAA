<?php
/**
 * Classe de gestion de la base de données
 */

if (!defined('ABSPATH')) {
    exit;
}

class MBAA_Database {
    
    private $wpdb;
    private $charset_collate;
    
    // Noms des tables
    public $table_artiste;
    public $table_oeuvre;
    public $table_epoque;
    public $table_salle;
    public $table_medium;
    public $table_mouvement;
    public $table_categorie;
    public $table_audioguide;
    public $table_evenement;
    public $table_type_evenement;
    public $table_exposition;
    public $table_oeuvre_exposition;
    public $table_notifications;
    public $table_audit_log;
    public $table_qr_codes;
    public $table_scan_tracking;
    public $table_technique;
    
    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->charset_collate = $wpdb->get_charset_collate();
        
        // Définir les noms de tables avec préfixe WordPress
        $this->table_artiste = $wpdb->prefix . 'mbaa_artiste';
        $this->table_oeuvre = $wpdb->prefix . 'mbaa_oeuvre';
        $this->table_epoque = $wpdb->prefix . 'mbaa_epoque';
        $this->table_salle = $wpdb->prefix . 'mbaa_salle';
        $this->table_medium = $wpdb->prefix . 'mbaa_medium';
        $this->table_mouvement = $wpdb->prefix . 'mbaa_mouvement_artistique';
        $this->table_categorie = $wpdb->prefix . 'mbaa_categorie';
        $this->table_galerie = $wpdb->prefix . 'mbaa_galerie';
        $this->table_audioguide = $wpdb->prefix . 'mbaa_audioguide';
        $this->table_evenement = $wpdb->prefix . 'mbaa_evenement';
        $this->table_type_evenement = $wpdb->prefix . 'mbaa_type_evenement';
        $this->table_exposition = $wpdb->prefix . 'mbaa_exposition';
        $this->table_oeuvre_exposition = $wpdb->prefix . 'mbaa_oeuvre_exposition';
        $this->table_notifications = $wpdb->prefix . 'mbaa_notifications';
        $this->table_audit_log = $wpdb->prefix . 'mbaa_audit_log';
        $this->table_qr_codes = $wpdb->prefix . 'mbaa_qr_codes';
        $this->table_scan_tracking = $wpdb->prefix . 'mbaa_scan_tracking';
        $this->table_technique = $wpdb->prefix . 'mbaa_technique';
    }
    
    //Créer toutes les tables
     
    public function create_tables() {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        // Table ARTISTE
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_artiste} (
            id_artiste bigint(20) NOT NULL AUTO_INCREMENT,
            nom varchar(255) NOT NULL,
            biographie text,
            date_naissance date,
            date_deces date,
            nationalite varchar(100),
            image_url varchar(500),
            lieu_naissance varchar(255),
            site_web varchar(500),
            reseaux_sociaux varchar(255),
            style_art varchar(255),
            audio_biographie varchar(500),
            visible tinyint(1) DEFAULT 1,
            creation datetime DEFAULT CURRENT_TIMESTAMP,
            mis_a_jour datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id_artiste),
            KEY nom (nom)
        ) {$this->charset_collate};";
        dbDelta($sql);
        
        // Table EPOQUE
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_epoque} (
            id_epoque bigint(20) NOT NULL AUTO_INCREMENT,
            nom_epoque varchar(100) NOT NULL,
            description text,
            date_debut int(11),
            date_fin int(11),
            PRIMARY KEY (id_epoque)
        ) {$this->charset_collate};";
        dbDelta($sql);
        
        // Table SALLE
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_salle} (
            id_salle bigint(20) NOT NULL AUTO_INCREMENT,
            nom_salle varchar(255) NOT NULL,
            description text,
            etage varchar(50),
            capacite int(11),
            PRIMARY KEY (id_salle)
        ) {$this->charset_collate};";
        dbDelta($sql);
        
        // Table MEDIUM
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_medium} (
            id_medium bigint(20) NOT NULL AUTO_INCREMENT,
            nom_medium varchar(100) NOT NULL,
            description text,
            PRIMARY KEY (id_medium)
        ) {$this->charset_collate};";
        dbDelta($sql);
        
        // Table MOUVEMENT_ARTISTIQUE
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_mouvement} (
            id_mouvement bigint(20) NOT NULL AUTO_INCREMENT,
            nom_mouvement varchar(100) NOT NULL,
            description text,
            periode_debut int(11),
            periode_fin int(11),
            PRIMARY KEY (id_mouvement)
        ) {$this->charset_collate};";
        dbDelta($sql);
        
        // Table CATEGORIE
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_categorie} (
            id_categorie bigint(20) NOT NULL AUTO_INCREMENT,
            nom_categorie varchar(100) NOT NULL,
            description_categorie text,
            PRIMARY KEY (id_categorie)
        ) {$this->charset_collate};";
        dbDelta($sql);
        
        // Table GALERIE
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_galerie} (
            id int(11) NOT NULL AUTO_INCREMENT,
            nom varchar(255) NOT NULL,
            description text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY nom (nom)
        ) {$this->charset_collate};";
        dbDelta($sql);
        
        // Table TYPE_EVENEMENT
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_type_evenement} (
            id_type_evenement bigint(20) NOT NULL AUTO_INCREMENT,
            nom_type varchar(100) NOT NULL,
            categorie varchar(50),
            PRIMARY KEY (id_type_evenement)
        ) {$this->charset_collate};";
        dbDelta($sql);
        
        // Table EXPOSITION
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_exposition} (
            id_exposition bigint(20) NOT NULL AUTO_INCREMENT,
            titre varchar(255) NOT NULL,
            description text,
            date_debut date NOT NULL,
            date_fin date NOT NULL,
            statut varchar(50) DEFAULT 'à venir',
            nombre_visiteurs int(11) DEFAULT 0,
            image_url varchar(500),
            PRIMARY KEY (id_exposition),
            KEY statut (statut),
            KEY dates (date_debut, date_fin)
        ) {$this->charset_collate};";
        dbDelta($sql);
        
        // Table OEUVRE
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_oeuvre} (
            id_oeuvre bigint(20) NOT NULL AUTO_INCREMENT,
            titre varchar(255) NOT NULL,
            date_creation varchar(50),
            description text,
            image_url varchar(500),
            dimensions varchar(100),
            numero_inventaire varchar(100),
            technique text,
            id_artiste bigint(20),
            id_epoque bigint(20),
            id_salle bigint(20),
            id_medium bigint(20),
            id_mouvement bigint(20),
            id_categorie bigint(20),
            etat tinyint(1) DEFAULT 1,
            situation varchar(100) DEFAULT 'exposee',
            visible_galerie tinyint(1) DEFAULT 1,
            visible_accueil tinyint(1) DEFAULT 0,
            audio_url varchar(500),
            provenance varchar(255),
            statut varchar(50) DEFAULT 'permanente',
            vues int(11) DEFAULT 0,
            creation datetime DEFAULT CURRENT_TIMESTAMP,
            mis_a_jour datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id_oeuvre),
            KEY id_artiste (id_artiste),
            KEY id_epoque (id_epoque),
            KEY id_salle (id_salle),
            KEY etat (etat),
            KEY situation (situation),
            KEY visible_galerie (visible_galerie),
            KEY visible_accueil (visible_accueil),
            KEY statut (statut),
            FOREIGN KEY (id_artiste) REFERENCES {$this->table_artiste}(id_artiste) ON DELETE SET NULL,
            FOREIGN KEY (id_epoque) REFERENCES {$this->table_epoque}(id_epoque) ON DELETE SET NULL,
            FOREIGN KEY (id_salle) REFERENCES {$this->table_salle}(id_salle) ON DELETE SET NULL,
            FOREIGN KEY (id_medium) REFERENCES {$this->table_medium}(id_medium) ON DELETE SET NULL,
            FOREIGN KEY (id_mouvement) REFERENCES {$this->table_mouvement}(id_mouvement) ON DELETE SET NULL,
            FOREIGN KEY (id_categorie) REFERENCES {$this->table_categorie}(id_categorie) ON DELETE SET NULL
        ) {$this->charset_collate};";
        dbDelta($sql);
        
        // Table AUDIOGUIDE
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_audioguide} (
            id_audioguide bigint(20) NOT NULL AUTO_INCREMENT,
            id_oeuvre bigint(20) NOT NULL,
            fichier_audio_url varchar(500) NOT NULL,
            duree_secondes int(11),
            langue varchar(10) DEFAULT 'fr',
            transcription text,
            PRIMARY KEY (id_audioguide),
            KEY id_oeuvre (id_oeuvre),
            FOREIGN KEY (id_oeuvre) REFERENCES {$this->table_oeuvre}(id_oeuvre) ON DELETE CASCADE
        ) {$this->charset_collate};";
        dbDelta($sql);
        
        // Table EVENEMENT
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_evenement} (
            id_evenement bigint(20) NOT NULL AUTO_INCREMENT,
            titre varchar(255) NOT NULL,
            descriptif text,
            date_evenement date NOT NULL,
            heure_debut time,
            heure_fin time,
            id_type_evenement bigint(20),
            est_gratuit tinyint(1) DEFAULT 0,
            prix decimal(10,2),
            label_tarif varchar(100),
            image_url varchar(500),
            capacite_max int(11),
            lieu_musee varchar(255),
            public_cible varchar(100),
            public_ado tinyint(1) DEFAULT 0,
            public_enfant tinyint(1) DEFAULT 0,
            public_adulte tinyint(1) DEFAULT 1,
            public_tout_public tinyint(1) DEFAULT 0,
            accessible_handicap tinyint(1) DEFAULT 0,
            niveau_debutant tinyint(1) DEFAULT 0,
            niveau_intermediaire tinyint(1) DEFAULT 0,
            niveau_confirme tinyint(1) DEFAULT 0,
            intervenant varchar(255),
            PRIMARY KEY (id_evenement),
            KEY date_evenement (date_evenement),
            KEY id_type_evenement (id_type_evenement),
            FOREIGN KEY (id_type_evenement) REFERENCES {$this->table_type_evenement}(id_type_evenement) ON DELETE SET NULL
        ) {$this->charset_collate};";
        dbDelta($sql);
        
        // Table OEUVRE_EXPOSITION (table de liaison)
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_oeuvre_exposition} (
            id_oeuvre bigint(20) NOT NULL,
            id_exposition bigint(20) NOT NULL,
            ordre_affichage int(11),
            PRIMARY KEY (id_oeuvre, id_exposition),
            FOREIGN KEY (id_oeuvre) REFERENCES {$this->table_oeuvre}(id_oeuvre) ON DELETE CASCADE,
            FOREIGN KEY (id_exposition) REFERENCES {$this->table_exposition}(id_exposition) ON DELETE CASCADE
        ) {$this->charset_collate};";
        dbDelta($sql);
        
        // Table NOTIFICATIONS
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_notifications} (
            id_notification bigint(20) NOT NULL AUTO_INCREMENT,
            type_notification varchar(50) NOT NULL,
            titre varchar(255) NOT NULL,
            message text,
            user_id bigint(20) NOT NULL,
            lue tinyint(1) DEFAULT 0,
            element_type varchar(50),
            element_id bigint(20),
            creation datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id_notification),
            KEY user_id (user_id),
            KEY lue (lue),
            KEY creation (creation)
        ) {$this->charset_collate};";
        dbDelta($sql);
        
        // Table AUDIT_LOG (historique des modifications)
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_audit_log} (
            id_log bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            action varchar(50) NOT NULL,
            element_type varchar(50) NOT NULL,
            element_id bigint(20),
            element_titre varchar(255),
            old_data text,
            new_data text,
            creation datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id_log),
            KEY user_id (user_id),
            KEY action (action),
            KEY element_type (element_type),
            KEY creation (creation)
        ) {$this->charset_collate};";
        dbDelta($sql);

        
        // Table QR_CODES
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_qr_codes} (
            id_qr bigint(20) NOT NULL AUTO_INCREMENT,
            id_oeuvre bigint(20),
            code_qr varchar(255) NOT NULL,
            url varchar(500) NOT NULL,
            type varchar(50) DEFAULT 'oeuvre',
            statut varchar(50) DEFAULT 'actif',
            scans_total int(11) DEFAULT 0,
            creation datetime DEFAULT CURRENT_TIMESTAMP,
            mis_a_jour datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id_qr),
            KEY id_oeuvre (id_oeuvre),
            KEY type (type),
            KEY statut (statut)
        ) {$this->charset_collate};";
        dbDelta($sql);
        
        // Table SCAN_TRACKING
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_scan_tracking} (
            id_scan bigint(20) NOT NULL AUTO_INCREMENT,
            id_qr bigint(20) NOT NULL,
            scan_date datetime DEFAULT CURRENT_TIMESTAMP,
            ip_address varchar(45),
            user_agent text,
            device_type varchar(50),
            location varchar(255),
            PRIMARY KEY (id_scan),
            KEY id_qr (id_qr),
            KEY scan_date (scan_date)
        ) {$this->charset_collate};";
        dbDelta($sql);

        // Table TECHNIQUE
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_technique} (
            id_technique bigint(20) NOT NULL AUTO_INCREMENT,
            nom_technique varchar(100) NOT NULL,
            description text,
            image_url varchar(500),
            ordre int(11) DEFAULT 0,
            PRIMARY KEY (id_technique),
            KEY ordre (ordre)
        ) {$this->charset_collate};";
        dbDelta($sql);

        // Ajouter explicitement les clés étrangères (car dbDelta les ignore souvent)
        $this->add_foreign_keys();
        
        // Migration: Ajouter les colonnes de public et accessibilité si elles n'existent pas
        $this->migrate_evenement_add_columns();
        
        // Insérer des données de base
        $this->insert_default_data();
        
        // Mettre à jour la version de la base de données
        update_option('mbaa_db_version', MBAA_VERSION);
    }
    
    /**
     * Migration: Ajouter les colonnes de public et accessibilité si elles n'existent pas
     */
    private function migrate_evenement_add_columns() {
        // Vérifier si la colonne public_ado existe déjà
        $columns = $this->wpdb->get_results("SHOW COLUMNS FROM {$this->table_evenement} LIKE 'public_ado'");
        
        if (empty($columns)) {
            $this->wpdb->query("ALTER TABLE {$this->table_evenement} ADD COLUMN public_ado tinyint(1) DEFAULT 0 AFTER public_cible");
            $this->wpdb->query("ALTER TABLE {$this->table_evenement} ADD COLUMN public_enfant tinyint(1) DEFAULT 0 AFTER public_ado");
            $this->wpdb->query("ALTER TABLE {$this->table_evenement} ADD COLUMN public_adulte tinyint(1) DEFAULT 1 AFTER public_enfant");
            $this->wpdb->query("ALTER TABLE {$this->table_evenement} ADD COLUMN public_tout_public tinyint(1) DEFAULT 0 AFTER public_adulte");
            $this->wpdb->query("ALTER TABLE {$this->table_evenement} ADD COLUMN accessible_handicap tinyint(1) DEFAULT 0 AFTER public_tout_public");
        }
        
        // Vérifier si les colonnes de niveau existent
        $niveau_cols = $this->wpdb->get_results("SHOW COLUMNS FROM {$this->table_evenement} LIKE 'niveau_debutant'");
        
        if (empty($niveau_cols)) {
            $this->wpdb->query("ALTER TABLE {$this->table_evenement} ADD COLUMN niveau_debutant tinyint(1) DEFAULT 0 AFTER accessible_handicap");
            $this->wpdb->query("ALTER TABLE {$this->table_evenement} ADD COLUMN niveau_intermediaire tinyint(1) DEFAULT 0 AFTER niveau_debutant");
            $this->wpdb->query("ALTER TABLE {$this->table_evenement} ADD COLUMN niveau_confirme tinyint(1) DEFAULT 0 AFTER niveau_intermediaire");
        }
    }
    
    /**
     * Ajouter explicitement les clés étrangères
     */
    private function add_foreign_keys() {
        // Table OEUVRE
        $this->wpdb->query("ALTER TABLE {$this->table_oeuvre} 
            ADD CONSTRAINT fk_oeuvre_artiste FOREIGN KEY (id_artiste) REFERENCES {$this->table_artiste}(id_artiste) ON DELETE SET NULL,
            ADD CONSTRAINT fk_oeuvre_epoque FOREIGN KEY (id_epoque) REFERENCES {$this->table_epoque}(id_epoque) ON DELETE SET NULL,
            ADD CONSTRAINT fk_oeuvre_salle FOREIGN KEY (id_salle) REFERENCES {$this->table_salle}(id_salle) ON DELETE SET NULL,
            ADD CONSTRAINT fk_oeuvre_medium FOREIGN KEY (id_medium) REFERENCES {$this->table_medium}(id_medium) ON DELETE SET NULL,
            ADD CONSTRAINT fk_oeuvre_mouvement FOREIGN KEY (id_mouvement) REFERENCES {$this->table_mouvement}(id_mouvement) ON DELETE SET NULL,
            ADD CONSTRAINT fk_oeuvre_categorie FOREIGN KEY (id_categorie) REFERENCES {$this->table_categorie}(id_categorie) ON DELETE SET NULL");

        // Table AUDIOGUIDE
        $this->wpdb->query("ALTER TABLE {$this->table_audioguide} 
            ADD CONSTRAINT fk_audioguide_oeuvre FOREIGN KEY (id_oeuvre) REFERENCES {$this->table_oeuvre}(id_oeuvre) ON DELETE CASCADE");

        // Table EVENEMENT
        $this->wpdb->query("ALTER TABLE {$this->table_evenement} 
            ADD CONSTRAINT fk_evenement_type FOREIGN KEY (id_type_evenement) REFERENCES {$this->table_type_evenement}(id_type_evenement) ON DELETE SET NULL");

        // Table OEUVRE_EXPOSITION
        $this->wpdb->query("ALTER TABLE {$this->table_oeuvre_exposition} 
            ADD CONSTRAINT fk_lien_oeuvre FOREIGN KEY (id_oeuvre) REFERENCES {$this->table_oeuvre}(id_oeuvre) ON DELETE CASCADE,
            ADD CONSTRAINT fk_lien_exposition FOREIGN KEY (id_exposition) REFERENCES {$this->table_exposition}(id_exposition) ON DELETE CASCADE");
    }

    //Insérer des données par défaut
     
    private function insert_default_data() {
        // Vérifier si des données existent déjà
        $count = $this->wpdb->get_var("SELECT COUNT(*) FROM {$this->table_epoque}");
        
        if ($count > 0) {
            return; // Données déjà présentes
        }
        
        // Insérer les époques par défaut
        $epoques = array(
            array('nom' => 'Préhistoire', 'date_debut' => -3000000, 'date_fin' => -3000),
            array('nom' => 'Antiquité', 'date_debut' => -3000, 'date_fin' => 476),
            array('nom' => 'Moyen Âge', 'date_debut' => 476, 'date_fin' => 1492),
            array('nom' => 'Renaissance', 'date_debut' => 1492, 'date_fin' => 1610),
            array('nom' => 'XVIIe siècle', 'date_debut' => 1610, 'date_fin' => 1715),
            array('nom' => 'XVIIIe siècle', 'date_debut' => 1715, 'date_fin' => 1815),
            array('nom' => 'XIXe siècle', 'date_debut' => 1815, 'date_fin' => 1914),
            array('nom' => 'XXe siècle', 'date_debut' => 1914, 'date_fin' => 2000),
            array('nom' => 'XXIe siècle', 'date_debut' => 2000, 'date_fin' => null)
        );
        
        foreach ($epoques as $epoque) {
            $this->wpdb->insert($this->table_epoque, $epoque);
        }
        
        // Insérer les mouvements artistiques par défaut
        $mouvements = array(
            'Impressionnisme',
            'Réalisme',
            'Romantisme',
            'Cubisme',
            'Surréalisme',
            'Expressionnisme',
            'Art abstrait',
            'Pop Art',
            'Art contemporain'
        );
        
        foreach ($mouvements as $mouvement) {
            $this->wpdb->insert($this->table_mouvement, array('nom' => $mouvement));
        }
        
        // Insérer les mediums par défaut
        $mediums = array(
            'Huile sur toile',
            'Acrylique sur toile',
            'Aquarelle',
            'Gouache',
            'Sculpture',
            'Bronze',
            'Marbre',
            'Photographie',
            'Installation',
            'Vidéo',
            'Dessin',
            'Gravure'
        );
        
        foreach ($mediums as $medium) {
            $this->wpdb->insert($this->table_medium, array('nom' => $medium));
        }
        
        // Insérer les catégories par défaut
        $categories = array(
            array('nom' => 'Beaux-arts', 'description' => 'Peinture, sculpture, dessin'),
            array('nom' => 'Arts décoratifs', 'description' => 'Mobilier, céramique, verrerie'),
            array('nom' => 'Photographie', 'description' => 'Art photographique'),
            array('nom' => 'Art contemporain', 'description' => 'Œuvres contemporaines')
        );
        
        foreach ($categories as $categorie) {
            $this->wpdb->insert($this->table_categorie, $categorie);
        }
        
        // Insérer les types d'événements par défaut
        $types_evenement = array(
            array('nom' => 'Visite guidée', 'categorie' => 'Visite'),
            array('nom' => 'Atelier peinture', 'categorie' => 'Atelier'),
            array('nom' => 'Atelier sculpture', 'categorie' => 'Atelier'),
            array('nom' => 'Conférence', 'categorie' => 'Conférence'),
            array('nom' => 'Spectacle', 'categorie' => 'Spectacle'),
            array('nom' => 'Atelier enfants', 'categorie' => 'Atelier')
        );
        
        foreach ($types_evenement as $type) {
            $this->wpdb->insert($this->table_type_evenement, $type);
        }
        
        // Insérer quelques salles par défaut
        $salles = array(
            array('nom' => '16ieme', 'etage' => 'Rez-de-chaussée'),
            array('nom' => '17ieme', 'etage' => '1er étage'),
            array('nom' => '18ieme', 'etage' => '2ème étage'),
            array('nom' => '19ieme', 'etage' => '2ème étage'),
            array('nom' => '20ieme', 'etage' => '3ème étage')
        );
        
        foreach ($salles as $salle) {
            $this->wpdb->insert($this->table_salle, $salle);
        }
        
        // Insérer quelques galeries par défaut
        $galeries = array(
            array('nom' => 'Galerie principale', 'description' => 'Galerie d\'exposition principale'),
            array('nom' => 'Galerie temporaire', 'description' => 'Galerie pour expositions temporaires'),
            array('nom' => 'Galerie extérieure', 'description' => 'Espace d\'exposition extérieur')
        );
        
        foreach ($galeries as $galerie) {
            $this->wpdb->insert($this->table_galerie, $galerie);
        }
        
        // Insérer les techniques par défaut
        $techniques = array(
            array(
                'nom_technique' => 'Peinture à l\'huile',
                'description' => 'Technique de peinture utilisant des pigments mélangés à de l\'huile',
                'image_url' => get_template_directory_uri() . '/asset/Img/techniques/huile.jpg',
                'ordre' => 1
            ),
            array(
                'nom_technique' => 'Aquarelle',
                'description' => 'Peinture à base d\'eau transparente et lumineuse',
                'image_url' => get_template_directory_uri() . '/asset/Img/techniques/aquarelle.jpg',
                'ordre' => 2
            ),
            array(
                'nom_technique' => 'Sculpture',
                'description' => 'Art de créer des formes en trois dimensions',
                'image_url' => get_template_directory_uri() . '/asset/Img/techniques/sculpture.jpg',
                'ordre' => 3
            ),
            array(
                'nom_technique' => 'Craie grasse',
                'description' => 'Bâtonnet de pigment mélangé à de la cire',
                'image_url' => get_template_directory_uri() . '/asset/Img/techniques/craie-grasse.jpg',
                'ordre' => 4
            ),
            array(
                'nom_technique' => 'Fusain',
                'description' => 'Bâtonnet de bois carbonisé pour le dessin',
                'image_url' => get_template_directory_uri() . '/asset/Img/techniques/fusain.jpg',
                'ordre' => 5
            ),
            array(
                'nom_technique' => 'Encre',
                'description' => 'Technique de dessin utilisant de l\'encre noire ou colorée',
                'image_url' => get_template_directory_uri() . '/asset/Img/techniques/encre.jpg',
                'ordre' => 6
            )
        );
        
        foreach ($techniques as $technique) {
            $this->wpdb->insert($this->table_technique, $technique);
        }
    }
    
    //Supprimer toutes les tables
     
    public function drop_tables() {
        $tables = array(
            $this->table_oeuvre_exposition,
            $this->table_audioguide,
            $this->table_evenement,
            $this->table_oeuvre,
            $this->table_artiste,
            $this->table_exposition,
            $this->table_type_evenement,
            $this->table_categorie,
            $this->table_mouvement,
            $this->table_medium,
            $this->table_salle,
            $this->table_epoque
        );
        
        foreach ($tables as $table) {
            $this->wpdb->query("DROP TABLE IF EXISTS {$table}");
        }
        
        delete_option('mbaa_db_version');
    }
    
    // =========================================================================
    // MÉTHODES DE SÉLECTION  (pour Liste des déroulantes)
    // =========================================================================
    
    // est une constante WordPress qui indique que les résultats de la requête doivent être retournés sous forme de tableau associatif 
    //(clés = noms des colonnes).

//ARRAY_A est le plus lisible car on accède aux données par le nom des colonnes

    //Liste de tous les artistes (triés par nom)
     
    public function get_all_artistes() {
        return $this->wpdb->get_results(
            "SELECT * FROM {$this->table_artiste} ORDER BY nom ASC",
            ARRAY_A
        );
    }
    
    //Liste de toutes les époques (triées par date de début)
     
    public function get_all_epoques() {
        return $this->wpdb->get_results(
            "SELECT * FROM {$this->table_epoque} ORDER BY date_debut ASC",
            ARRAY_A
        );
    }

    
    //Liste de toutes les salles (triées par nom)
     
    public function get_all_salles() {
        return $this->wpdb->get_results(
            "SELECT * FROM {$this->table_salle} ORDER BY nom_salle ASC",
            ARRAY_A
        );
    }
    
    //Liste de tous les mediums (triés par nom)
     
    public function get_all_mediums() {
        return $this->wpdb->get_results(
            "SELECT * FROM {$this->table_medium} ORDER BY nom_medium ASC",
            ARRAY_A
        );
    }
    
    //Liste de tous les mouvements artistiques (triés par nom)
     
    public function get_all_mouvements() {
        return $this->wpdb->get_results(
            "SELECT * FROM {$this->table_mouvement} ORDER BY nom_mouvement ASC",
            ARRAY_A
        );
    }
    
    //Liste de toutes les catégories (triées par nom)
     
    public function get_all_categories() {
        return $this->wpdb->get_results(
            "SELECT * FROM {$this->table_categorie} ORDER BY nom_categorie ASC",
            ARRAY_A
        );
    }
    
    //Liste de tous les types d'événements (triés par nom)
     
    public function get_all_types_evenement() {
        return $this->wpdb->get_results(
            "SELECT * FROM {$this->table_type_evenement} ORDER BY nom_type ASC",
            ARRAY_A
        );
    }
    
    //Liste de toutes les expositions (triées par date de début)
     
    public function get_all_expositions() {
        return $this->wpdb->get_results(
            "SELECT * FROM {$this->table_exposition} ORDER BY date_debut DESC",
            ARRAY_A
        );
    }
    
    // =========================================================================
    // MÉTHODES AVEC JOINTURES (SELECT avec JOIN)
    // =========================================================================
    
    //Récupérer une œuvre avec toutes ses relations (artiste, époque, salle, etc.)
     
    public function get_oeuvre_with_relations($id_oeuvre) {
        $sql = "
            SELECT 
                o.*,
                a.nom AS artiste_nom,
                a.nationalite AS artiste_nationalite,
                e.nom_epoque AS epoque_nom,
                s.nom_salle AS salle_nom,
                s.etage AS salle_etage,
                m.nom_medium AS medium_nom,
                mo.nom_mouvement AS mouvement_nom,
                c.nom_categorie AS categorie_nom
            FROM {$this->table_oeuvre} o
            LEFT JOIN {$this->table_artiste} a ON o.id_artiste = a.id_artiste
            LEFT JOIN {$this->table_epoque} e ON o.id_epoque = e.id_epoque
            LEFT JOIN {$this->table_salle} s ON o.id_salle = s.id_salle
            LEFT JOIN {$this->table_medium} m ON o.id_medium = m.id_medium
            LEFT JOIN {$this->table_mouvement} mo ON o.id_mouvement = mo.id_mouvement
            LEFT JOIN {$this->table_categorie} c ON o.id_categorie = c.id_categorie
            WHERE o.id_oeuvre = %d
        ";
        
        return $this->wpdb->get_row($this->wpdb->prepare($sql, $id_oeuvre), ARRAY_A);
    }
    
    //Récupérer toutes les œuvres avec leurs relations
     
    public function get_all_oeuvres_with_relations($limit = -1, $visible_galerie = null) {
        $where = '';
        $params = array();
        
        if ($visible_galerie !== null) {
            $where = ' WHERE o.visible_galerie = %d';
            $params[] = $visible_galerie;
        }
        
        $sql = "
            SELECT 
                o.*,
                a.nom AS artiste_nom,
                a.nationalite AS artiste_nationalite,
                e.nom_epoque AS epoque_nom,
                s.nom_salle AS salle_nom,
                s.etage AS salle_etage,
                m.nom_medium AS medium_nom,
                mo.nom_mouvement AS mouvement_nom,
                c.nom_categorie AS categorie_nom
            FROM {$this->table_oeuvre} o
            LEFT JOIN {$this->table_artiste} a ON o.id_artiste = a.id_artiste
            LEFT JOIN {$this->table_epoque} e ON o.id_epoque = e.id_epoque
            LEFT JOIN {$this->table_salle} s ON o.id_salle = s.id_salle
            LEFT JOIN {$this->table_medium} m ON o.id_medium = m.id_medium
            LEFT JOIN {$this->table_mouvement} mo ON o.id_mouvement = mo.id_mouvement
            LEFT JOIN {$this->table_categorie} c ON o.id_categorie = c.id_categorie
            {$where}
            ORDER BY o.titre ASC
        ";
        
        if ($limit > 0) {
            return $this->wpdb->get_results(
                $this->wpdb->prepare($sql . " LIMIT %d", $params + array($limit)),
                ARRAY_A
            );
        }
        
        if (!empty($params)) {
            return $this->wpdb->get_results($this->wpdb->prepare($sql, $params), ARRAY_A);
        }
        
        return $this->wpdb->get_results($sql, ARRAY_A);
    }
    
    //Récupérer un événement avec son type
     
    public function get_evenement_with_type($id_evenement) {
        $sql = "
            SELECT 
                e.*,
                te.nom_type AS type_nom,
                te.categorie AS type_categorie
            FROM {$this->table_evenement} e
            LEFT JOIN {$this->table_type_evenement} te ON e.id_type_evenement = te.id_type_evenement
            WHERE e.id_evenement = %d
        ";
        
        return $this->wpdb->get_row($this->wpdb->prepare($sql, $id_evenement), ARRAY_A);
    }
    
    //Récupérer tous les événements avec leurs types
     
    public function get_all_evenements_with_types($statut = null) {
        $where = '';
        $params = array();
        
        if ($statut) {
            $where = ' WHERE e.date_evenement >= %s';
            $params[] = current_time('mysql');
        }
        
        $sql = "
            SELECT 
                e.*,
                te.nom_type AS type_nom,
                te.categorie AS type_categorie
            FROM {$this->table_evenement} e
            LEFT JOIN {$this->table_type_evenement} te ON e.id_type_evenement = te.id_type_evenement
            {$where}
            ORDER BY e.date_evenement ASC
        ";
        
        if (!empty($params)) {
            return $this->wpdb->get_results($this->wpdb->prepare($sql, $params), ARRAY_A);
        }
        
        return $this->wpdb->get_results($sql, ARRAY_A);
    }
    
    //Récupérer un audioguide avec son œuvre
     
    public function get_audioguide_with_oeuvre($id_audioguide) {
        $sql = "
            SELECT 
                ag.*,
                o.titre AS oeuvre_titre,
                o.image_url AS oeuvre_image,
                a.nom AS artiste_nom
            FROM {$this->table_audioguide} ag
            LEFT JOIN {$this->table_oeuvre} o ON ag.id_oeuvre = o.id_oeuvre
            LEFT JOIN {$this->table_artiste} a ON o.id_artiste = a.id_artiste
            WHERE ag.id_audioguide = %d
        ";
        
        return $this->wpdb->get_row($this->wpdb->prepare($sql, $id_audioguide), ARRAY_A);
    }
    
    //Récupérer tous les audioguides avec leurs œuvres
     
    public function get_all_audioguides_with_oeuvres() {
        $sql = "
            SELECT 
                ag.*,
                o.titre AS oeuvre_titre,
                o.image_url AS oeuvre_image,
                a.nom AS artiste_nom
            FROM {$this->table_audioguide} ag
            LEFT JOIN {$this->table_oeuvre} o ON ag.id_oeuvre = o.id_oeuvre
            LEFT JOIN {$this->table_artiste} a ON o.id_artiste = a.id_artiste
            ORDER BY o.titre ASC
        ";
        
        return $this->wpdb->get_results($sql, ARRAY_A);
    }
    
    //Récupérer les audioguides d'une œuvre spécifique
     
    public function get_audioguides_by_oeuvre($id_oeuvre) {
        $sql = "
            SELECT 
                ag.*,
                o.titre AS oeuvre_titre
            FROM {$this->table_audioguide} ag
            LEFT JOIN {$this->table_oeuvre} o ON ag.id_oeuvre = o.id_oeuvre
            WHERE ag.id_oeuvre = %d
            ORDER BY ag.langue ASC
        ";
        
        return $this->wpdb->get_results($this->wpdb->prepare($sql, $id_oeuvre), ARRAY_A);
    }
    
    //Récupérer une exposition avec ses œuvres
     
    public function get_exposition_with_oeuvres($id_exposition) {
        $sql = "
            SELECT 
                exp.*,
                GROUP_CONCAT(o.id_oeuvre ORDER BY oe.ordre_affichage SEPARATOR ',') AS oeuvre_ids,
                GROUP_CONCAT(o.titre ORDER BY oe.ordre_affichage SEPARATOR '|||') AS oeuvre_titres,
                GROUP_CONCAT(o.image_url ORDER BY oe.ordre_affichage SEPARATOR '|||') AS oeuvre_images
            FROM {$this->table_exposition} exp
            LEFT JOIN {$this->table_oeuvre_exposition} oe ON exp.id_exposition = oe.id_exposition
            LEFT JOIN {$this->table_oeuvre} o ON oe.id_oeuvre = o.id_oeuvre
            WHERE exp.id_exposition = %d
            GROUP BY exp.id_exposition
        ";
        
        return $this->wpdb->get_row($this->wpdb->prepare($sql, $id_exposition), ARRAY_A);
    }
    
    //Récupérer toutes les expositions avec le nombre d'œuvres
     
    public function get_all_expositions_with_oeuvres_count() {
        $sql = "
            SELECT 
                exp.*,
                COUNT(oe.id_oeuvre) AS nombre_oeuvres
            FROM {$this->table_exposition} exp
            LEFT JOIN {$this->table_oeuvre_exposition} oe ON exp.id_exposition = oe.id_exposition
            GROUP BY exp.id_exposition
            ORDER BY exp.date_debut DESC
        ";
        
        return $this->wpdb->get_results($sql, ARRAY_A);
    }
    
    //Récupérer les œuvres d'une exposition
     
    public function get_oeuvres_by_exposition($id_exposition) {
        $sql = "
            SELECT 
                o.*,
                oe.ordre_affichage,
                a.nom AS artiste_nom
            FROM {$this->table_oeuvre} o
            LEFT JOIN {$this->table_oeuvre_exposition} oe ON o.id_oeuvre = oe.id_oeuvre
            LEFT JOIN {$this->table_artiste} a ON o.id_artiste = a.id_artiste
            WHERE oe.id_exposition = %d
            ORDER BY oe.ordre_affichage ASC
        ";
        
        return $this->wpdb->get_results($this->wpdb->prepare($sql, $id_exposition), ARRAY_A);
    }
    
    // =========================================================================
    // MÉTHODES DE RECHERCHE
    // =========================================================================
    
    // Rechercher des œuvres (par titre, description, artiste)
    public function search_oeuvres($search_term) {
        $search_term = '%' . $this->wpdb->esc_like($search_term) . '%';
        
        $sql = "
            SELECT 
                o.*,
                a.nom AS artiste_nom,
                e.nom_epoque AS epoque_nom,
                s.nom_salle AS salle_nom
            FROM {$this->table_oeuvre} o
            LEFT JOIN {$this->table_artiste} a ON o.id_artiste = a.id_artiste
            LEFT JOIN {$this->table_epoque} e ON o.id_epoque = e.id_epoque
            LEFT JOIN {$this->table_salle} s ON o.id_salle = s.id_salle
            WHERE o.titre LIKE %s
               OR o.description LIKE %s
               OR a.nom LIKE %s
            ORDER BY o.titre ASC
        ";
        
        return $this->wpdb->get_results(
            $this->wpdb->prepare($sql, $search_term, $search_term, $search_term),
            ARRAY_A
        );
    }
    
    //Rechercher des artistes
    
    public function search_artistes($search_term) {
        $search_term = '%' . $this->wpdb->esc_like($search_term) . '%';
        
        $sql = "
            SELECT *
            FROM {$this->table_artiste}
            WHERE nom LIKE %s
               OR nationalite LIKE %s
            ORDER BY nom ASC
        ";
        
        return $this->wpdb->get_results(
            $this->wpdb->prepare($sql, $search_term, $search_term),
            ARRAY_A
        );
    }
    
    //Rechercher des événements
     
    public function search_evenements($search_term) {
        $search_term = '%' . $this->wpdb->esc_like($search_term) . '%';
        
        $sql = "
            SELECT 
                e.*,
                te.nom_type AS type_nom
            FROM {$this->table_evenement} e
            LEFT JOIN {$this->table_type_evenement} te ON e.id_type_evenement = te.id_type_evenement
            WHERE e.titre LIKE %s
               OR e.descriptif LIKE %s
               OR te.nom_type LIKE %s
            ORDER BY e.date_evenement ASC
        ";
        
        return $this->wpdb->get_results(
            $this->wpdb->prepare($sql, $search_term, $search_term, $search_term),
            ARRAY_A
        );
    }
    
    //Récupérer les œuvres d'un artiste spécifique
     
    public function get_oeuvres_by_artiste($id_artiste) {
        $sql = "
            SELECT 
                o.*,
                e.nom_epoque AS epoque_nom,
                s.nom_salle AS salle_nom,
                m.nom_medium AS medium_nom
            FROM {$this->table_oeuvre} o
            LEFT JOIN {$this->table_epoque} e ON o.id_epoque = e.id_epoque
            LEFT JOIN {$this->table_salle} s ON o.id_salle = s.id_salle
            LEFT JOIN {$this->table_medium} m ON o.id_medium = m.id_medium
            WHERE o.id_artiste = %d
            ORDER BY o.titre ASC
        ";
        
        return $this->wpdb->get_results($this->wpdb->prepare($sql, $id_artiste), ARRAY_A);
    }
    
    //Récupérer les œuvres par catégorie
     
    public function get_oeuvres_by_categorie($id_categorie) {
        $sql = "
            SELECT 
                o.*,
                a.nom AS artiste_nom,
                e.nom_epoque AS epoque_nom,
                s.nom_salle AS salle_nom
            FROM {$this->table_oeuvre} o
            LEFT JOIN {$this->table_artiste} a ON o.id_artiste = a.id_artiste
            LEFT JOIN {$this->table_epoque} e ON o.id_epoque = e.id_epoque
            LEFT JOIN {$this->table_salle} s ON o.id_salle = s.id_salle
            WHERE o.id_categorie = %d
            ORDER BY o.titre ASC
        ";
        
        return $this->wpdb->get_results($this->wpdb->prepare($sql, $id_categorie), ARRAY_A);
    }
    
    //Récupérer les œuvres par salle
     
    public function get_oeuvres_by_salle($id_salle) {
        $sql = "
            SELECT 
                o.*,
                a.nom AS artiste_nom,
                e.nom_epoque AS epoque_nom,
                m.nom_medium AS medium_nom
            FROM {$this->table_oeuvre} o
            LEFT JOIN {$this->table_artiste} a ON o.id_artiste = a.id_artiste
            LEFT JOIN {$this->table_epoque} e ON o.id_epoque = e.id_epoque
            LEFT JOIN {$this->table_medium} m ON o.id_medium = m.id_medium
            WHERE o.id_salle = %d
            ORDER BY o.titre ASC
        ";
        
        return $this->wpdb->get_results($this->wpdb->prepare($sql, $id_salle), ARRAY_A);
    }
    
    /**
     * Récupérer les œuvres par statut
     * Utilisé pour la page Collections
     */
    public function get_oeuvres_by_statut($statut) {
        $sql = "
            SELECT 
                o.*,
                a.nom AS artiste_nom,
                e.nom_epoque AS epoque_nom,
                s.nom_salle AS salle_nom,
                m.nom_medium AS medium_nom
            FROM {$this->table_oeuvre} o
            LEFT JOIN {$this->table_artiste} a ON o.id_artiste = a.id_artiste
            LEFT JOIN {$this->table_epoque} e ON o.id_epoque = e.id_epoque
            LEFT JOIN {$this->table_salle} s ON o.id_salle = s.id_salle
            LEFT JOIN {$this->table_medium} m ON o.id_medium = m.id_medium
            WHERE o.statut = %s
            ORDER BY o.titre ASC
        ";
        
        return $this->wpdb->get_results($this->wpdb->prepare($sql, $statut), ARRAY_A);
    }
    
    // =========================================================================
    // MÉTHODES UTILITAIRES
    // =========================================================================
    
    // Récupérer un enregistrement par l'ID
     
    public function get_single($table, $id_column, $id) {
        $allowed_tables = array(
            $this->table_artiste,
            $this->table_oeuvre,
            $this->table_epoque,
            $this->table_salle,
            $this->table_medium,
            $this->table_mouvement,
            $this->table_categorie,
            $this->table_audioguide,
            $this->table_evenement,
            $this->table_type_evenement,
            $this->table_exposition
        );
        
        if (!in_array($table, $allowed_tables)) {
            return null;
        }
        
        $sql = $this->wpdb->prepare(
            "SELECT * FROM {$table} WHERE {$id_column} = %d",
            $id
        );
        
        return $this->wpdb->get_row($sql, ARRAY_A);
    }
    
    //Compter les enregistrements d'une table
     
    public function count_records($table) {
        $allowed_tables = array(
            $this->table_artiste,
            $this->table_oeuvre,
            $this->table_epoque,
            $this->table_salle,
            $this->table_medium,
            $this->table_mouvement,
            $this->table_categorie,
            $this->table_audioguide,
            $this->table_evenement,
            $this->table_type_evenement,
            $this->table_exposition
        );
        
        if (!in_array($table, $allowed_tables)) {
            return 0;
        }
        
        return $this->wpdb->get_var("SELECT COUNT(*) FROM {$table}");
    }
    
    //Récupérer des œuvres aléatoires (pour la page d'accueil)
     
    public function get_random_oeuvres($limit = 6) {
        $sql = "
            SELECT 
                o.*,
                a.nom AS artiste_nom,
                a.nationalite AS artiste_nationalite
            FROM {$this->table_oeuvre} o
            LEFT JOIN {$this->table_artiste} a ON o.id_artiste = a.id_artiste
            WHERE o.visible_accueil = 1
            ORDER BY RAND()
            LIMIT %d
        ";
        
        return $this->wpdb->get_results($this->wpdb->prepare($sql, $limit), ARRAY_A);
    }
    
    //Récupérer les prochaines expositions
     
    public function get_upcoming_expositions($limit = 3) {
        $sql = "
            SELECT *
            FROM {$this->table_exposition}
            WHERE date_debut >= %s
            ORDER BY date_debut ASC
            LIMIT %d
        ";
        
        return $this->wpdb->get_results(
            $this->wpdb->prepare($sql, current_time('mysql'), $limit),
            ARRAY_A
        );
    }
    
    //Récupérer les événements à venir
     
    public function get_upcoming_evenements($limit = 5) {
        $sql = "
            SELECT 
                e.*,
                te.nom_type AS type_nom
            FROM {$this->table_evenement} e
            LEFT JOIN {$this->table_type_evenement} te ON e.id_type_evenement = te.id_type_evenement
            WHERE e.date_evenement >= %s
            ORDER BY e.date_evenement ASC
            LIMIT %d
        ";
        
        return $this->wpdb->get_results(
            $this->wpdb->prepare($sql, current_time('mysql'), $limit),
            ARRAY_A
        );
    }
    
    // =========================================================================
    // MÉTHODE DE RÉINSTALLATION DES DONNÉES (pour débogage)
    // =========================================================================
    
    /**
     * Réinsérer toutes les données par défaut dans les tables de référence.
     * 
     * Cette méthode est utile lors du développement ou si les données ont été
     * accidentellement supprimées. Elle :
     * 1. Vide les tables de référence (époques, mediums, mouvements, etc.)
     * 2. Insère les données par défaut
     * 
     * @return string Message de confirmation ou d'erreur
     */
    public function reinstaller_donnees_par_defaut() {
        // Vérifier que WordPress est chargé
        if (!defined('ABSPATH')) {
            return 'Erreur : WordPress n\'est pas chargé.';
        }
        
        try {
            // =================================================================
            // ÉTAPE 1 : Vider les tables de référence
            // =================================================================
            // On utilise TRUNCATE pour réinitialiser les auto-increment
            $this->wpdb->query("TRUNCATE TABLE {$this->table_epoque}");
            $this->wpdb->query("TRUNCATE TABLE {$this->table_medium}");
            $this->wpdb->query("TRUNCATE TABLE {$this->table_mouvement}");
            $this->wpdb->query("TRUNCATE TABLE {$this->table_categorie}");
            $this->wpdb->query("TRUNCATE TABLE {$this->table_type_evenement}");
            $this->wpdb->query("TRUNCATE TABLE {$this->table_salle}");
            
            // =================================================================
            // ÉTAPE 2 : Insérer les époques
            // =================================================================
            $epoques = array(
                array('nom_epoque' => 'Préhistoire', 'date_debut' => -3000000, 'date_fin' => -3000),
                array('nom_epoque' => 'Antiquité', 'date_debut' => -3000, 'date_fin' => 476),
                array('nom_epoque' => 'Moyen Âge', 'date_debut' => 476, 'date_fin' => 1492),
                array('nom_epoque' => 'Renaissance', 'date_debut' => 1492, 'date_fin' => 1610),
                array('nom_epoque' => 'XVIIe siècle', 'date_debut' => 1610, 'date_fin' => 1715),
                array('nom_epoque' => 'XVIIIe siècle', 'date_debut' => 1715, 'date_fin' => 1815),
                array('nom_epoque' => 'XIXe siècle', 'date_debut' => 1815, 'date_fin' => 1914),
                array('nom_epoque' => 'XXe siècle', 'date_debut' => 1914, 'date_fin' => 2000),
                array('nom_epoque' => 'XXIe siècle', 'date_debut' => 2000, 'date_fin' => null)
            );
            
            foreach ($epoques as $epoque) {
                $this->wpdb->insert($this->table_epoque, $epoque);
            }
            
            // =================================================================
            // ÉTAPE 3 : Insérer les mouvements artistiques
            // =================================================================
            $mouvements = array(
                'Impressionnisme',
                'Réalisme',
                'Romantisme',
                'Cubisme',
                'Surréalisme',
                'Expressionnisme',
                'Art abstrait',
                'Pop Art',
                'Art contemporain'
            );
            
            foreach ($mouvements as $mouvement) {
                $this->wpdb->insert($this->table_mouvement, array('nom_mouvement' => $mouvement));
            }
            
            // =================================================================
            // ÉTAPE 4 : Insérer les mediums (techniques)
            // =================================================================
            $mediums = array(
                ' Huile sur toile',
                ' Acrylique sur toile',
                ' Aquarelle',
                ' Gouache',
                ' Sculpture',
                ' Bronze',
                ' Marbre',
                ' Photographie',
                ' Installation',
                ' Vidéo',
                ' Dessin',
                ' Gravure',
                ' Encre',
                ' Pastel sec',
                ' Craie Grasse',
                ' Collage',
                ' Sanguine',
                ' Mine de plomb',
                ' Fusain',
                ' Céramique',
                ' Verre'
            );
            
            foreach ($mediums as $medium) {
                $this->wpdb->insert($this->table_medium, array('nom_medium' => $medium));
            }
            
            // =================================================================
            // ÉTAPE 5 : Insérer les catégories
            // =================================================================
            $categories = array(
                array('nom_categorie' => 'Beaux-arts', 'description_categorie' => 'Peinture, sculpture, dessin'),
                array('nom_categorie' => 'Archéologie', 'description_categorie' => 'Mosaïque, vase, bijou'),
                array('nom_categorie' => 'Arts décoratifs', 'description_categorie' => 'Mobilier, céramique, verrerie'),
                array('nom_categorie' => 'Photographie', 'description_categorie' => 'Art photographique'),
                array('nom_categorie' => 'Art contemporain', 'description_categorie' => 'Œuvres contemporaines')
            );
            
            foreach ($categories as $categorie) {
                $this->wpdb->insert($this->table_categorie, $categorie);
            }
            
            // =================================================================
            // ÉTAPE 6 : Insérer les types d'événements
            // =================================================================
            $types_evenement = array(
                array('nom_type' => 'Visite guidée', 'categorie' => 'Visite'),
                array('nom_type' => 'Visite de groupe', 'categorie' => 'Visite'),
                array('nom_type' => 'Visite nocturne', 'categorie' => 'Visite'),
                array('nom_type' => 'Atelier poterie', 'categorie' => 'Atelier'),
                array('nom_type' => 'Atelier peinture', 'categorie' => 'Atelier'),
                array('nom_type' => 'Atelier sculpture', 'categorie' => 'Atelier'),
                array('nom_type' => 'Atelier enfants', 'categorie' => 'Atelier'),
                array('nom_type' => 'Conférence', 'categorie' => 'Conférence'),
                array('nom_type' => 'Performance', 'categorie' => 'Performance'),
                array('nom_type' => 'Spectacle', 'categorie' => 'Spectacle'),
                array('nom_type' => 'Concert', 'categorie' => 'Concert')
            );
            
            foreach ($types_evenement as $type) {
                $this->wpdb->insert($this->table_type_evenement, $type);
            }
            
            // =================================================================
            // ÉTAPE 7 : Insérer les salles
            // =================================================================
            $salles = array(
                array('nom_salle' => 'Salle 16èmes', 'etage' => 'Rez-de-chaussée'),
                array('nom_salle' => 'Salle 17èmes', 'etage' => '1er étage'),
                array('nom_salle' => 'Salle 18èmes', 'etage' => '1er étage'),
                array('nom_salle' => 'Salle 19èmes', 'etage' => '2ème étage'),
                array('nom_salle' => 'Salle 20èmes', 'etage' => '3ème étage'),
                array('nom_salle' => 'Salle 15ieme', 'etage' => 'Rez-de-chaussée')
            );
            
            foreach ($salles as $salle) {
                $this->wpdb->insert($this->table_salle, $salle);
            }
            
            // =================================================================
            // Compter le nombre d'enregistrements insérés
            // =================================================================
            $nb_epoques = $this->wpdb->get_var("SELECT COUNT(*) FROM {$this->table_epoque}");
            $nb_mouvements = $this->wpdb->get_var("SELECT COUNT(*) FROM {$this->table_mouvement}");
            $nb_mediums = $this->wpdb->get_var("SELECT COUNT(*) FROM {$this->table_medium}");
            $nb_categories = $this->wpdb->get_var("SELECT COUNT(*) FROM {$this->table_categorie}");
            $nb_types = $this->wpdb->get_var("SELECT COUNT(*) FROM {$this->table_type_evenement}");
            $nb_salles = $this->wpdb->get_var("SELECT COUNT(*) FROM {$this->table_salle}");
            
            // =================================================================
            // Retourner un message de confirmation
            // =================================================================
            return sprintf(
                '✅ Données réinstallées avec succès !<br><br>
                <strong>Époques :</strong> %d<br>
                <strong>Mouvements :</strong> %d<br>
                <strong>Mediums :</strong> %d<br>
                <strong>Catégories :</strong> %d<br>
                <strong>Types d\'événements :</strong> %d<br>
                <strong>Salles :</strong> %d<br><br>
                <em>Vous pouvez maintenant fermer cette page et rafraîchir vos formulaires.</em>',
                $nb_epoques, $nb_mouvements, $nb_mediums, $nb_categories, $nb_types, $nb_salles
            );
            
        } catch (Exception $e) {
            // En cas d'erreur, retourner le message d'erreur
            return '❌ Erreur lors de la réinstallation : ' . $e->getMessage();
        }
    }
    
    // =========================================================================
    // MÉTHODES NOTIFICATIONS
    // =========================================================================
    
    /**
     * Créer une notification
     */
    public function create_notification($type, $titre, $message, $user_id, $element_type = null, $element_id = null) {
        return $this->wpdb->insert($this->table_notifications, array(
            'type_notification' => $type,
            'titre' => $titre,
            'message' => $message,
            'user_id' => $user_id,
            'element_type' => $element_type,
            'element_id' => $element_id,
            'creation' => current_time('mysql')
        ));
    }
    
    /**
     * Récupérer les notifications d'un utilisateur
     */
    public function get_notifications($user_id, $limit = 20) {
        return $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->table_notifications} WHERE user_id = %d ORDER BY creation DESC LIMIT %d",
                $user_id,
                $limit
            ),
            ARRAY_A
        );
    }
    
    /**
     * Récupérer les notifications non lues
     */
    public function get_unread_notifications($user_id) {
        return $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->table_notifications} WHERE user_id = %d AND lue = 0 ORDER BY creation DESC",
                $user_id
            ),
            ARRAY_A
        );
    }
    
    /**
     * Marquer une notification comme lue
     */
    public function mark_notification_read($id_notification) {
        return $this->wpdb->update(
            $this->table_notifications,
            array('lue' => 1),
            array('id_notification' => $id_notification)
        );
    }
    
    /**
     * Marquer toutes les notifications comme lues
     */
    public function mark_all_notifications_read($user_id) {
        return $this->wpdb->update(
            $this->table_notifications,
            array('lue' => 1),
            array('user_id' => $user_id, 'lue' => 0)
        );
    }
    
    /**
     * Compter les notifications non lues
     */
    public function count_unread_notifications($user_id) {
        return $this->wpdb->get_var(
            $this->wpdb->prepare(
                "SELECT COUNT(*) FROM {$this->table_notifications} WHERE user_id = %d AND lue = 0",
                $user_id
            )
        );
    }
    
    /**
     * Supprimer une notification
     */
    public function delete_notification($id_notification) {
        return $this->wpdb->delete($this->table_notifications, array('id_notification' => $id_notification));
    }
    
    // =========================================================================
    // MÉTHODES AUDIT LOG (historique)
    // =========================================================================
    
    /**
     * Enregistrer une action dans l'historique
     */
    public function log_action($user_id, $action, $element_type, $element_id, $element_titre, $old_data = null, $new_data = null) {
        return $this->wpdb->insert($this->table_audit_log, array(
            'user_id' => $user_id,
            'action' => $action,
            'element_type' => $element_type,
            'element_id' => $element_id,
            'element_titre' => $element_titre,
            'old_data' => $old_data ? json_encode($old_data) : null,
            'new_data' => $new_data ? json_encode($new_data) : null,
            'creation' => current_time('mysql')
        ));
    }
    
    /**
     * Récupérer l'historique
     */
    public function get_audit_log($filters = array(), $limit = 50) {
        $where = '';
        $params = array();
        
        if (!empty($filters['user_id'])) {
            $where .= ' AND user_id = %d';
            $params[] = $filters['user_id'];
        }
        
        if (!empty($filters['action'])) {
            $where .= ' AND action = %s';
            $params[] = $filters['action'];
        }
        
        if (!empty($filters['element_type'])) {
            $where .= ' AND element_type = %s';
            $params[] = $filters['element_type'];
        }
        
        if (!empty($filters['date_from'])) {
            $where .= ' AND creation >= %s';
            $params[] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $where .= ' AND creation <= %s';
            $params[] = $filters['date_to'];
        }
        
        $sql = "SELECT * FROM {$this->table_audit_log} WHERE 1=1 {$where} ORDER BY creation DESC LIMIT %d";
        $params[] = $limit;
        
        return $this->wpdb->get_results($this->wpdb->prepare($sql, $params), ARRAY_A);
    }
    
    // =========================================================================
    // MÉTHODES QR CODES
    // =========================================================================
    
    /**
     * Créer un QR code pour une œuvre
     */
    public function create_qr_code($id_oeuvre, $url, $type = 'oeuvre') {
        $code_qr = 'QR_' . $id_oeuvre . '_' . uniqid();
        
        $this->wpdb->insert($this->table_qr_codes, array(
            'id_oeuvre' => $id_oeuvre,
            'code_qr' => $code_qr,
            'url' => $url,
            'type' => $type,
            'creation' => current_time('mysql')
        ));
        
        return $this->wpdb->insert_id;
    }
    
    /**
     * Récupérer les QR codes
     */
    public function get_qr_codes($filters = array()) {
        $where = '';
        $params = array();
        
        if (!empty($filters['id_oeuvre'])) {
            $where .= ' AND id_oeuvre = %d';
            $params[] = $filters['id_oeuvre'];
        }
        
        if (!empty($filters['type'])) {
            $where .= ' AND type = %s';
            $params[] = $filters['type'];
        }
        
        if (!empty($filters['statut'])) {
            $where .= ' AND statut = %s';
            $params[] = $filters['statut'];
        }
        
        $sql = "SELECT * FROM {$this->table_qr_codes} WHERE 1=1 {$where} ORDER BY creation DESC";
        
        return !empty($params) ? $this->wpdb->get_results($this->wpdb->prepare($sql, $params), ARRAY_A) : $this->wpdb->get_results($sql, ARRAY_A);
    }
    
    /**
     * Récupérer un QR code par ID
     */
    public function get_qr_code($id_qr) {
        return $this->wpdb->get_row(
            $this->wpdb->prepare("SELECT * FROM {$this->table_qr_codes} WHERE id_qr = %d", $id_qr),
            ARRAY_A
        );
    }
    
    /**
     * Mettre à jour le nombre de scans
     */
    public function increment_scan_count($id_qr) {
        $this->wpdb->query(
            $this->wpdb->prepare("UPDATE {$this->table_qr_codes} SET scans_total = scans_total + 1 WHERE id_qr = %d", $id_qr)
        );
    }
    
    /**
     * Supprimer un QR code
     */
    public function delete_qr_code($id_qr) {
        return $this->wpdb->delete($this->table_qr_codes, array('id_qr' => $id_qr));
    }
    
    // =========================================================================
    // MÉTHODES SCAN TRACKING
    // =========================================================================
    
    /**
     * Enregistrer un scan de QR code
     */
    public function record_scan($id_qr, $ip_address = null, $user_agent = null) {
        // Détecter le type d'appareil
        $device_type = 'desktop';
        if (preg_match('/mobile/i', $user_agent)) {
            $device_type = 'mobile';
        } elseif (preg_match('/tablet/i', $user_agent)) {
            $device_type = 'tablet';
        }
        
        $this->wpdb->insert($this->table_scan_tracking, array(
            'id_qr' => $id_qr,
            'scan_date' => current_time('mysql'),
            'ip_address' => $ip_address,
            'user_agent' => substr($user_agent, 0, 255),
            'device_type' => $device_type
        ));
        
        // Incrémenter le compteur du QR code
        $this->increment_scan_count($id_qr);
        
        // Incrémenter les vues de l'œuvre associée
        $qr_code = $this->get_qr_code($id_qr);
        if ($qr_code && !empty($qr_code['id_oeuvre'])) {
            $this->wpdb->query(
                $this->wpdb->prepare("UPDATE {$this->table_oeuvre} SET vues = vues + 1 WHERE id_oeuvre = %d", $qr_code['id_oeuvre'])
            );
        }
    }
    
    /**
     * Récupérer les scans d'un QR code
     */
    public function get_scans($id_qr, $period = 'all') {
        $date_condition = '';
        
        switch ($period) {
            case 'week':
                $date_condition = ' AND scan_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)';
                break;
            case 'month':
                $date_condition = ' AND scan_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)';
                break;
            case 'quarter':
                $date_condition = ' AND scan_date >= DATE_SUB(NOW(), INTERVAL 90 DAY)';
                break;
            case 'year':
                $date_condition = ' AND scan_date >= DATE_SUB(NOW(), INTERVAL 365 DAY)';
                break;
        }
        
        return $this->wpdb->get_results(
            $this->wpdb->prepare("SELECT * FROM {$this->table_scan_tracking} WHERE id_qr = %d {$date_condition} ORDER BY scan_date DESC", $id_qr),
            ARRAY_A
        );
    }
    
    /**
     * Compter les scans par période
     */
    public function count_scans($id_qr, $period = 'all') {
        return count($this->get_scans($id_qr, $period));
    }
    
    /**
     * Statistiques de scans globales
     */
    public function get_scan_statistics($period = 'all') {
        $date_condition = '';
        
        switch ($period) {
            case 'week':
                $date_condition = ' WHERE scan_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)';
                break;
            case 'month':
                $date_condition = ' WHERE scan_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)';
                break;
            case 'quarter':
                $date_condition = ' WHERE scan_date >= DATE_SUB(NOW(), INTERVAL 90 DAY)';
                break;
            case 'year':
                $date_condition = ' WHERE scan_date >= DATE_SUB(NOW(), INTERVAL 365 DAY)';
                break;
        }
        
        return $this->wpdb->get_row(
            "SELECT COUNT(*) as total_scans, COUNT(DISTINCT id_qr) as unique_qr_codes FROM {$this->table_scan_tracking} {$date_condition}",
            ARRAY_A
        );
    }
    
    // =========================================================================
    // MÉTHODES ŒUVRES POPULAIRES
    // =========================================================================
    
    /**
     * Récupérer les œuvres les plus vues
     */
    public function get_popular_oeuvres($limit = 10) {
        return $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT o.*, a.nom as artiste_nom, o.vues as total_vues 
                FROM {$this->table_oeuvre} o 
                LEFT JOIN {$this->table_artiste} a ON o.id_artiste = a.id_artiste 
                ORDER BY o.vues DESC 
                LIMIT %d",
                $limit
            ),
            ARRAY_A
        );
    }
    
    /**
     * Récupérer les œuvres populaires avec évolution
     */
    public function get_popular_oeuvres_with_evolution($limit = 10) {
        $sql = $this->wpdb->prepare(
            "SELECT o.*, a.nom as artiste_nom, o.vues as total_vues,
            (SELECT COUNT(*) FROM {$this->table_scan_tracking} st 
             JOIN {$this->table_qr_codes} qr ON st.id_qr = qr.id_qr 
             WHERE qr.id_oeuvre = o.id_oeuvre 
             AND st.scan_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)) as vues_semaine
            FROM {$this->table_oeuvre} o 
            LEFT JOIN {$this->table_artiste} a ON o.id_artiste = a.id_artiste 
            ORDER BY o.vues DESC 
            LIMIT %d",
            $limit
        );
        
        return $this->wpdb->get_results($sql, ARRAY_A);
    }
    
    // =========================================================================
    // MÉTHODES VALIDATION
    // =========================================================================
    
    /**
     * Récupérer les contenus en attente de validation
     */
    public function get_pending_validation($element_type = null) {
        $where = '';
        if ($element_type) {
            $where = $this->wpdb->prepare("WHERE element_type = %s", $element_type);
        }
        
        return $this->wpdb->get_results(
            "SELECT * FROM {$this->table_notifications} WHERE type_notification = 'validation' {$where} ORDER BY creation DESC", ARRAY_A );
    }
}
