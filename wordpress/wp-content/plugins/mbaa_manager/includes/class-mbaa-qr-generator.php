<?php
 
 if (!defined('ABSPATH')) {
     exit;
 }
 
 class MBAA_QR_Generator {
     public static function init() {
         add_action('wp_ajax_mbaa_save_qr', array(__CLASS__, 'ajax_save_qr'));
     }
 
     public static function ajax_save_qr() {
         $nonce = null;
         if (isset($_POST['nonce'])) {
             $nonce = sanitize_text_field(wp_unslash($_POST['nonce']));
         } elseif (isset($_SERVER['HTTP_X_WP_NONCE'])) {
             $nonce = sanitize_text_field(wp_unslash($_SERVER['HTTP_X_WP_NONCE']));
         }
 
         if (!$nonce || (!wp_verify_nonce($nonce, 'mbaa_upload_nonce') && !wp_verify_nonce($nonce, 'mbaa_nonce'))) {
             wp_send_json_error(__('Erreur de sécurité.', 'mbaa'));
         }
 
         if (!current_user_can('manage_options') && !current_user_can('mbaa_can_access_oeuvres')) {
             wp_send_json_error(__('Permission refusée.', 'mbaa'));
         }
 
         $oeuvre_id = isset($_POST['id_oeuvre']) ? absint($_POST['id_oeuvre']) : 0;
         if (!$oeuvre_id) {
             wp_send_json_error(__('ID d\'œuvre invalide.', 'mbaa'));
         }
 
         $url = null;
         if (isset($_POST['url'])) {
             $url = esc_url_raw(wp_unslash($_POST['url']));
         }
 
         if (!$url && class_exists('MBAA_Oeuvre_Pages')) {
             $url = MBAA_Oeuvre_Pages::get_oeuvre_url($oeuvre_id);
         }
 
         if (empty($url)) {
             wp_send_json_error(__('URL invalide.', 'mbaa'));
         }
 
         global $wpdb;
         $table_qr = $wpdb->prefix . 'mbaa_qr_codes';
 
         $existing = $wpdb->get_row(
             $wpdb->prepare("SELECT * FROM {$table_qr} WHERE id_oeuvre = %d AND type = %s LIMIT 1", $oeuvre_id, 'oeuvre')
         );
 
         if ($existing) {
             $wpdb->update(
                 $table_qr,
                 array(
                     'url' => $url,
                     'statut' => 'actif',
                     'mis_a_jour' => current_time('mysql'),
                 ),
                 array('id_qr' => $existing->id_qr),
                 array('%s', '%s', '%s'),
                 array('%d')
             );
 
             wp_send_json_success(array(
                 'message' => __('QR code mis à jour.', 'mbaa'),
                 'url' => $url,
                 'id_qr' => (int) $existing->id_qr,
             ));
         }
 
         $wpdb->insert(
             $table_qr,
             array(
                 'id_oeuvre' => $oeuvre_id,
                 'code_qr' => 'QR_' . $oeuvre_id . '_' . uniqid(),
                 'url' => $url,
                 'type' => 'oeuvre',
                 'statut' => 'actif',
                 'scans_total' => 0,
                 'creation' => current_time('mysql'),
                 'mis_a_jour' => current_time('mysql'),
             ),
             array('%d', '%s', '%s', '%s', '%s', '%d', '%s', '%s')
         );
 
         $id_qr = (int) $wpdb->insert_id;
         if (!$id_qr) {
             wp_send_json_error(__('Erreur lors de la création du QR code.', 'mbaa'));
         }
 
         wp_send_json_success(array(
             'message' => __('QR code enregistré.', 'mbaa'),
             'url' => $url,
             'id_qr' => $id_qr,
         ));
     }
 }
 
 MBAA_QR_Generator::init();
