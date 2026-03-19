<?php
/**
 * Logique de sauvegarde des œuvres
 * Gère l'enregistrement, la mise à jour et la validation des données
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class MBAA_Oeuvre_Save {
    
    public function __construct() {
        add_action( 'save_post', array( $this, 'save_oeuvre_meta' ), 10, 2 );
        add_action( 'admin_post_mbaa_save_oeuvre_frontend', array( $this, 'handle_oeuvre_form_submission' ) );
        add_action( 'admin_post_mbaa_update_oeuvre_frontend', array( $this, 'handle_oeuvre_update' ) );
        add_action( 'wp_ajax_mbaa_delete_oeuvre', array( $this, 'ajax_delete_oeuvre' ) );
    }
    
    /**
     * Sauvegarde des meta données lors de la sauvegarde du post (compatibilité ACF)
     */
    public function save_oeuvre_meta( $post_id, $post ) {
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        
        if ( $post->post_type !== 'oeuvre' ) {
            return;
        }
        
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
        
        if ( ! isset( $_POST['mbaa_oeuvre_nonce'] ) || 
             ! wp_verify_nonce( $_POST['mbaa_oeuvre_nonce'], 'mbaa_save_oeuvre_meta' ) ) {
            return;
        }
        
        // Si ACF est activé, laisser ACF gérer la sauvegarde automatiquement
        if ( function_exists( 'acf' ) ) {
            // ACF gère déjà la sauvegarde des champs via son propre hook
            return;
        }
        
        // Fallback si ACF n'est pas activé (ancien système)
        $meta_fields = array(
            '_mbaa_description' => 'wp_kses_post',
            '_mbaa_date_creation' => 'sanitize_text_field',
            '_mbaa_provenance' => 'sanitize_text_field',
            '_mbaa_prix_estime' => 'floatval',
            '_mbaa_artiste_id' => 'absint',
            '_mbaa_technique' => 'sanitize_text_field',
            '_mbaa_dimensions' => 'sanitize_text_field',
            '_mbaa_support' => 'sanitize_text_field',
        );
        
        foreach ( $meta_fields as $meta_key => $sanitize_callback ) {
            if ( isset( $_POST[ str_replace( '_mbaa_', 'mbaa_', $meta_key ) ] ) ) {
                $value = $_POST[ str_replace( '_mbaa_', 'mbaa_', $meta_key ) ];
                
                if ( is_callable( $sanitize_callback ) ) {
                    $value = call_user_func( $sanitize_callback, $value );
                }
                
                update_post_meta( $post_id, $meta_key, $value );
            }
        }
    }
    
    /**
     * Gestion de la soumission du formulaire depuis le frontend
     */
    public function handle_oeuvre_form_submission() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'Vous n\'avez pas les permissions nécessaires.', 'mbaa-manager' ) );
        }
        
        if ( ! isset( $_POST['mbaa_oeuvre_nonce'] ) || 
             ! wp_verify_nonce( $_POST['mbaa_oeuvre_nonce'], 'mbaa_save_oeuvre' ) ) {
            wp_die( __( 'Erreur de sécurité. Veuillez réessayer.', 'mbaa-manager' ) );
        }
        
        // Validation des champs obligatoires
        $required_fields = array( 'mbaa_title', 'mbaa_description' );
        foreach ( $required_fields as $field ) {
            if ( empty( $_POST[$field] ) ) {
                wp_die( sprintf( __( 'Le champ %s est obligatoire.', 'mbaa-manager' ), $field ) );
            }
        }
        
        // Création du post
        $post_data = array(
            'post_title'    => sanitize_text_field( $_POST['mbaa_title'] ),
            'post_content'  => wp_kses_post( $_POST['mbaa_description'] ),
            'post_excerpt'  => sanitize_text_field( $_POST['mbaa_excerpt'] ?? '' ),
            'post_status'   => 'draft',
            'post_type'     => 'oeuvre',
            'post_author'   => get_current_user_id(),
        );
        
        $post_id = wp_insert_post( $post_data );
        
        if ( is_wp_error( $post_id ) ) {
            wp_die( __( 'Erreur lors de la création de l\'œuvre.', 'mbaa-manager' ) );
        }
        
        // Sauvegarde des meta données
        $this->save_oeuvre_meta_fields( $post_id, $_POST );
        
        // Gestion de l'image mise en avant
        if ( isset( $_POST['mbaa_featured_image'] ) && ! empty( $_POST['mbaa_featured_image'] ) ) {
            set_post_thumbnail( $post_id, absint( $_POST['mbaa_featured_image'] ) );
        }
        
        // Redirection avec message de succès
        $redirect_url = add_query_arg(
            array(
                'page' => 'mbaa-oeuvres',
                'action' => 'list',
                'message' => 'oeuvre_created',
                'post_id' => $post_id
            ),
            admin_url( 'admin.php' )
        );
        
        wp_redirect( $redirect_url );
        exit;
    }
    
    /**
     * Gestion de la mise à jour d'une œuvre
     */
    public function handle_oeuvre_update() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'Vous n\'avez pas les permissions nécessaires.', 'mbaa-manager' ) );
        }
        
        if ( ! isset( $_POST['mbaa_oeuvre_nonce'] ) || 
             ! wp_verify_nonce( $_POST['mbaa_oeuvre_nonce'], 'mbaa_update_oeuvre' ) ) {
            wp_die( __( 'Erreur de sécurité. Veuillez réessayer.', 'mbaa-manager' ) );
        }
        
        $post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
        
        if ( ! $post_id || get_post_type( $post_id ) !== 'oeuvre' ) {
            wp_die( __( 'Œuvre invalide.', 'mbaa-manager' ) );
        }
        
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            wp_die( __( 'Vous ne pouvez pas modifier cette œuvre.', 'mbaa-manager' ) );
        }
        
        // Validation des champs obligatoires
        $required_fields = array( 'mbaa_title', 'mbaa_description' );
        foreach ( $required_fields as $field ) {
            if ( empty( $_POST[$field] ) ) {
                wp_die( sprintf( __( 'Le champ %s est obligatoire.', 'mbaa-manager' ), $field ) );
            }
        }
        
        // Mise à jour du post
        $post_data = array(
            'ID'            => $post_id,
            'post_title'    => sanitize_text_field( $_POST['mbaa_title'] ),
            'post_content'  => wp_kses_post( $_POST['mbaa_description'] ),
            'post_excerpt'  => sanitize_text_field( $_POST['mbaa_excerpt'] ?? '' ),
        );
        
        $result = wp_update_post( $post_data );
        
        if ( is_wp_error( $result ) ) {
            wp_die( __( 'Erreur lors de la mise à jour de l\'œuvre.', 'mbaa-manager' ) );
        }
        
        // Sauvegarde des meta données
        $this->save_oeuvre_meta_fields( $post_id, $_POST );
        
        // Gestion de l'image mise en avant
        if ( isset( $_POST['mbaa_featured_image'] ) && ! empty( $_POST['mbaa_featured_image'] ) ) {
            set_post_thumbnail( $post_id, absint( $_POST['mbaa_featured_image'] ) );
        } elseif ( isset( $_POST['remove_featured_image'] ) ) {
            delete_post_thumbnail( $post_id );
        }
        
        // Redirection avec message de succès
        $redirect_url = add_query_arg(
            array(
                'page' => 'mbaa-oeuvres',
                'action' => 'list',
                'message' => 'oeuvre_updated',
                'post_id' => $post_id
            ),
            admin_url( 'admin.php' )
        );
        
        wp_redirect( $redirect_url );
        exit;
    }
    
    /**
     * Suppression AJAX d'une œuvre
     */
    public function ajax_delete_oeuvre() {
        check_ajax_referer( 'mbaa_delete_oeuvre_nonce', 'nonce' );
        
        if ( ! current_user_can( 'delete_posts' ) ) {
            wp_send_json_error( __( 'Permission refusée.', 'mbaa-manager' ) );
        }
        
        $post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
        
        if ( ! $post_id || get_post_type( $post_id ) !== 'oeuvre' ) {
            wp_send_json_error( __( 'Œuvre invalide.', 'mbaa-manager' ) );
        }
        
        $result = wp_delete_post( $post_id, true );
        
        if ( $result === false ) {
            wp_send_json_error( __( 'Erreur lors de la suppression.', 'mbaa-manager' ) );
        }
        
        wp_send_json_success( array(
            'message' => __( 'Œuvre supprimée avec succès.', 'mbaa-manager' )
        ) );
    }
    
    /**
     * Publication d'une œuvre
     */
    public function ajax_publish_oeuvre() {
        check_ajax_referer( 'mbaa_publish_oeuvre_nonce', 'nonce' );
        
        if ( ! current_user_can( 'edit_posts' ) ) {
            wp_send_json_error( __( 'Permission refusée.', 'mbaa-manager' ) );
        }
        
        $post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
        
        if ( ! $post_id || get_post_type( $post_id ) !== 'oeuvre' ) {
            wp_send_json_error( __( 'Œuvre invalide.', 'mbaa-manager' ) );
        }
        
        $result = wp_update_post( array(
            'ID' => $post_id,
            'post_status' => 'publish'
        ) );
        
        if ( is_wp_error( $result ) ) {
            wp_send_json_error( __( 'Erreur lors de la publication.', 'mbaa-manager' ) );
        }
        
        wp_send_json_success( array(
            'message' => __( 'Œuvre publiée avec succès.', 'mbaa-manager' ),
            'url' => get_permalink( $post_id )
        ) );
    }
    
    /**
     * Sauvegarde des champs ACF pour une œuvre
     */
    private function save_oeuvre_meta_fields( $post_id, $data ) {
        // Mapping des champs ACF
        $acf_fields = array(
            'mbaa_description' => 'field_mbaa_description',
            'mbaa_date_creation' => 'field_mbaa_date_creation',
            'mbaa_provenance' => 'field_mbaa_provenance',
            'mbaa_prix_estime' => 'field_mbaa_prix_estime',
            'mbaa_artiste_id' => 'field_mbaa_artiste_id',
            'mbaa_technique' => 'field_mbaa_technique',
            'mbaa_dimensions' => 'field_mbaa_dimensions',
            'mbaa_support' => 'field_mbaa_support',
            'mbaa_epoque' => 'field_mbaa_epoque',
            'mbaa_salle' => 'field_mbaa_salle',
            'mbaa_medium' => 'field_mbaa_medium',
            'mbaa_mouvement' => 'field_mbaa_mouvement',
            'mbaa_categorie' => 'field_mbaa_categorie',
        );
        
        foreach ( $acf_fields as $field_name => $field_key ) {
            if ( isset( $data[$field_name] ) ) {
                $value = $data[$field_name];
                
                // Sanitisation selon le type de champ ACF
                switch ( $field_key ) {
                    case 'field_mbaa_description':
                        $value = wp_kses_post( $value );
                        break;
                    case 'field_mbaa_prix_estime':
                        $value = floatval( $value );
                        break;
                    case 'field_mbaa_artiste_id':
                        $value = absint( $value );
                        break;
                    case 'field_mbaa_date_creation':
                        $value = sanitize_text_field( $value );
                        break;
                    default:
                        $value = sanitize_text_field( $value );
                        break;
                }
                
                // Utiliser update_field() d'ACF pour la sauvegarde
                if ( function_exists( 'update_field' ) ) {
                    update_field( $field_key, $value, $post_id );
                } else {
                    // Fallback si ACF n'est pas activé
                    update_post_meta( $post_id, $field_name, $value );
                }
            }
        }
    }
    
    /**
     * Affichage des messages d'administration
     */
    public function display_admin_notices() {
        if ( isset( $_GET['message'] ) ) {
            $message = sanitize_text_field( $_GET['message'] );
            $class = 'notice-success';
            
            switch ( $message ) {
                case 'oeuvre_created':
                    $text = __( 'Œuvre créée avec succès !', 'mbaa-manager' );
                    break;
                case 'oeuvre_updated':
                    $text = __( 'Œuvre mise à jour avec succès !', 'mbaa-manager' );
                    break;
                case 'oeuvre_deleted':
                    $text = __( 'Œuvre supprimée avec succès !', 'mbaa-manager' );
                    break;
                default:
                    $text = __( 'Opération réussie !', 'mbaa-manager' );
                    break;
            }
            
            printf( '<div class="notice %s is-dismissible"><p>%s</p></div>', esc_attr( $class ), esc_html( $text ) );
        }
    }
}

new MBAA_Oeuvre_Save();
