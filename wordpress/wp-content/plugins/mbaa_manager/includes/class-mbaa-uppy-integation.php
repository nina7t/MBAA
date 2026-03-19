<?php
/**
 * MBAA Plugin - Intégration Uppy
 * Fonctions PHP pour charger Uppy et gérer les uploads
 */

// Sécurité WordPress
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enqueue Uppy Scripts et Styles
 */
function mbaa_enqueue_uppy_assets($hook) {
    // Charger uniquement sur les pages admin du plugin MBAA
    if (strpos($hook, 'mbaa') === false) {
        return;
    }

    // Version Uppy
    $uppy_version = '3.21.0'; // Dernière version stable

    // === STYLES UPPY ===
    
    // Core Uppy CSS
    wp_enqueue_style(
        'uppy-core',
        "https://cdn.jsdelivr.net/npm/@uppy/core@{$uppy_version}/dist/style.min.css",
        array(),
        $uppy_version
    );

    // Dashboard CSS
    wp_enqueue_style(
        'uppy-dashboard',
        "https://cdn.jsdelivr.net/npm/@uppy/dashboard@{$uppy_version}/dist/style.min.css",
        array('uppy-core'),
        $uppy_version
    );

    // Image Editor CSS
    wp_enqueue_style(
        'uppy-image-editor',
        "https://cdn.jsdelivr.net/npm/@uppy/image-editor@{$uppy_version}/dist/style.min.css",
        array('uppy-core'),
        $uppy_version
    );

    // Webcam CSS
    wp_enqueue_style(
        'uppy-webcam',
        "https://cdn.jsdelivr.net/npm/@uppy/webcam@{$uppy_version}/dist/style.min.css",
        array('uppy-core'),
        $uppy_version
    );

    // Audio CSS
    wp_enqueue_style(
        'uppy-audio',
        "https://cdn.jsdelivr.net/npm/@uppy/audio@{$uppy_version}/dist/style.min.css",
        array('uppy-core'),
        $uppy_version
    );

    // Styles personnalisés musée
    wp_enqueue_style(
        'mbaa-uppy-custom',
        plugin_dir_url(dirname(__FILE__)) . 'assets/css/mbaa-uppy-styles.css',
        array('uppy-dashboard'),
        '1.0.0'
    );

    // === SCRIPTS UPPY ===
    
    // Core Uppy JS
    wp_enqueue_script(
        'uppy-core',
        "https://cdn.jsdelivr.net/npm/@uppy/core@{$uppy_version}/dist/Uppy.min.js",
        array(),
        $uppy_version,
        true
    );

    // Dashboard JS
    wp_enqueue_script(
        'uppy-dashboard',
        "https://cdn.jsdelivr.net/npm/@uppy/dashboard@{$uppy_version}/dist/Dashboard.min.js",
        array('uppy-core'),
        $uppy_version,
        true
    );

    // XHR Upload
    wp_enqueue_script(
        'uppy-xhr-upload',
        "https://cdn.jsdelivr.net/npm/@uppy/xhr-upload@{$uppy_version}/dist/XHRUpload.min.js",
        array('uppy-core'),
        $uppy_version,
        true
    );

    // Image Editor
    wp_enqueue_script(
        'uppy-image-editor',
        "https://cdn.jsdelivr.net/npm/@uppy/image-editor@{$uppy_version}/dist/ImageEditor.min.js",
        array('uppy-core'),
        $uppy_version,
        true
    );

    // Webcam
    wp_enqueue_script(
        'uppy-webcam',
        "https://cdn.jsdelivr.net/npm/@uppy/webcam@{$uppy_version}/dist/Webcam.min.js",
        array('uppy-core'),
        $uppy_version,
        true
    );

    // Audio
    wp_enqueue_script(
        'uppy-audio',
        "https://cdn.jsdelivr.net/npm/@uppy/audio@{$uppy_version}/dist/Audio.min.js",
        array('uppy-core'),
        $uppy_version,
        true
    );

    // URL (pour import depuis web)
    wp_enqueue_script(
        'uppy-url',
        "https://cdn.jsdelivr.net/npm/@uppy/url@{$uppy_version}/dist/Url.min.js",
        array('uppy-core'),
        $uppy_version,
        true
    );

    // Compressor (optimisation images)
    wp_enqueue_script(
        'uppy-compressor',
        "https://cdn.jsdelivr.net/npm/@uppy/compressor@{$uppy_version}/dist/Compressor.min.js",
        array('uppy-core'),
        $uppy_version,
        true
    );

    // Script personnalisé avec Uppy
    wp_enqueue_script(
        'mbaa-admin-uppy',
        plugin_dir_url(dirname(__FILE__)) . 'assets/js/mbaa-admin-uppy.js',
        array('jquery', 'uppy-core', 'uppy-dashboard', 'uppy-xhr-upload'),
        '1.0.0',
        true
    );

    // Localiser le script avec les données AJAX
    wp_localize_script('mbaa-admin-uppy', 'mbaaAjax', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('mbaa_upload_nonce'),
        'companionUrl' => null, // Optionnel : URL Companion pour sources externes
        'strings' => array(
            'uploadSuccess' => __('Fichier téléversé avec succès !', 'mbaa'),
            'uploadError' => __('Erreur lors du téléversement.', 'mbaa'),
            'fileTooLarge' => __('Le fichier est trop volumineux.', 'mbaa'),
            'invalidFileType' => __('Type de fichier non autorisé.', 'mbaa'),
        )
    ));
}
add_action('admin_enqueue_scripts', 'mbaa_enqueue_uppy_assets');

/**
 * Handler AJAX pour upload de fichiers via Uppy
 */
function mbaa_handle_uppy_upload() {
    // Nonce: Uppy envoie le nonce dans le header X-WP-Nonce
    $nonce = null;
    if (isset($_SERVER['HTTP_X_WP_NONCE']) && is_string($_SERVER['HTTP_X_WP_NONCE'])) {
        $nonce = sanitize_text_field(wp_unslash($_SERVER['HTTP_X_WP_NONCE']));
    } elseif (isset($_REQUEST['nonce'])) {
        $nonce = sanitize_text_field(wp_unslash($_REQUEST['nonce']));
    }

    if (!$nonce || !wp_verify_nonce($nonce, 'mbaa_upload_nonce')) {
        wp_send_json_error(__('Erreur de sécurité.', 'mbaa'));
    }

    if (!current_user_can('upload_files') && !current_user_can('mbaa_can_access_oeuvres')) {
        wp_send_json_error(__('Vous n\'avez pas les permissions nécessaires.', 'mbaa'));
    }

    $file = null;
    if (isset($_FILES['file'])) {
        $file = $_FILES['file'];
    } elseif (isset($_FILES['files'])) {
        // Compat: certains configs utilisent files[]
        $files = $_FILES['files'];
        if (is_array($files) && isset($files['name'][0])) {
            $file = array(
                'name' => $files['name'][0],
                'type' => $files['type'][0],
                'tmp_name' => $files['tmp_name'][0],
                'error' => $files['error'][0],
                'size' => $files['size'][0],
            );
        }
    }

    if (empty($file) || !isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        wp_send_json_error(__('Aucun fichier reçu.', 'mbaa'));
    }

    $result = mbaa_process_upload_file($file);
    if (is_array($result) && isset($result['error'])) {
        wp_send_json_error($result['error']);
    }

    wp_send_json_success($result);
}
add_action('wp_ajax_mbaa_uppy_upload', 'mbaa_handle_uppy_upload');

/**
 * Traiter un fichier uploadé
 */
function mbaa_process_upload_file($file) {
    // Validation du type de fichier
    $allowed_types = array(
        // Images haute résolution
        'image/jpeg',
        'image/jpg',
        'image/png',
        'image/tiff',
        'image/webp',
        'image/gif',
        // Audio
        'audio/mpeg',
        'audio/mp3',
        'audio/wav',
        'audio/x-wav',
        'audio/m4a',
        'audio/x-m4a',
        'audio/ogg',
        'audio/flac',
    );

    $file_type = wp_check_filetype($file['name']);
    $mime_type = $file['type'];

    if (!in_array($mime_type, $allowed_types) && !in_array($file_type['type'], $allowed_types)) {
        return array('error' => __('Type de fichier non autorisé.', 'mbaa'));
    }

    // Validation de la taille
    $max_size = 100 * 1024 * 1024; // 100 MB max
    if ($file['size'] > $max_size) {
        return array('error' => __('Le fichier est trop volumineux (max: 100 MB).', 'mbaa'));
    }

    // Upload du fichier dans la bibliothèque WordPress
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');
    require_once(ABSPATH . 'wp-admin/includes/image.php');

    // Préparer le fichier pour wp_handle_upload
    $file_array = array(
        'name' => $file['name'],
        'type' => $file['type'],
        'tmp_name' => $file['tmp_name'],
        'error' => $file['error'],
        'size' => $file['size']
    );

    // Utiliser wp_handle_upload
    $upload_overrides = array(
        'test_form' => false,
        'test_size' => true,
        'test_upload' => true,
    );

    $uploaded_file = wp_handle_upload($file_array, $upload_overrides);

    if (isset($uploaded_file['error'])) {
        return array('error' => $uploaded_file['error']);
    }

    // Créer l'attachment
    $attachment = array(
        'post_mime_type' => $uploaded_file['type'],
        'post_title'     => sanitize_file_name(pathinfo($file['name'], PATHINFO_FILENAME)),
        'post_content'   => '',
        'post_status'    => 'inherit'
    );

    $attachment_id = wp_insert_attachment($attachment, $uploaded_file['file']);

    if (is_wp_error($attachment_id)) {
        return array('error' => $attachment_id->get_error_message());
    }

    // Générer les métadonnées
    $attachment_data = wp_generate_attachment_metadata($attachment_id, $uploaded_file['file']);
    wp_update_attachment_metadata($attachment_id, $attachment_data);

    // Ajouter des métadonnées personnalisées pour le musée
    update_post_meta($attachment_id, '_mbaa_upload_source', 'uppy');
    update_post_meta($attachment_id, '_mbaa_upload_date', current_time('mysql'));
    update_post_meta($attachment_id, '_mbaa_uploaded_by', get_current_user_id());

    // Si c'est une image, ajouter les dimensions
    if (strpos($uploaded_file['type'], 'image') !== false) {
        $image_data = getimagesize($uploaded_file['file']);
        if ($image_data) {
            update_post_meta($attachment_id, '_mbaa_original_width', $image_data[0]);
            update_post_meta($attachment_id, '_mbaa_original_height', $image_data[1]);
        }
    }

    // Retourner les données
    return array(
        'id'  => $attachment_id,
        'url' => $uploaded_file['url'],
        'type' => $uploaded_file['type'],
        'filename' => basename($uploaded_file['file']),
        'filesize' => filesize($uploaded_file['file']),
        'success' => true // Ajout d'un flag de succès explicite
    );
}
add_action('wp_ajax_mbaa_uppy_upload', 'mbaa_handle_uppy_upload');

/**
 * Augmenter les limites d'upload pour les œuvres haute résolution
 */
function mbaa_increase_upload_limits($size) {
    // 100 MB pour les images haute résolution
    return 100 * 1024 * 1024; // 100 MB
}
add_filter('upload_size_limit', 'mbaa_increase_upload_limits');

/**
 * Ajouter des types MIME supplémentaires
 */
function mbaa_custom_mime_types($mimes) {
    // TIFF pour reproductions haute qualité
    $mimes['tif'] = 'image/tiff';
    $mimes['tiff'] = 'image/tiff';
    
    // FLAC pour audio haute qualité
    $mimes['flac'] = 'audio/flac';
    
    // WebP pour images optimisées web
    $mimes['webp'] = 'image/webp';
    
    return $mimes;
}
add_filter('upload_mimes', 'mbaa_custom_mime_types');

/**
 * Configuration des tailles d'images pour le musée
 */
function mbaa_custom_image_sizes() {
    // Miniature exposition (pour galeries)
    add_image_size('mbaa-exposition-thumb', 400, 400, true);
    
    // Vue moyenne (pour fiches œuvres)
    add_image_size('mbaa-oeuvre-medium', 800, 800, false);
    
    // Vue détaillée (pour zoom)
    add_image_size('mbaa-oeuvre-large', 1920, 1920, false);
    
    // Plein écran (présentation)
    add_image_size('mbaa-oeuvre-fullscreen', 2560, 2560, false);
}
add_action('after_setup_theme', 'mbaa_custom_image_sizes');

/**
 * Ajouter les tailles personnalisées au sélecteur
 */
function mbaa_custom_image_size_names($sizes) {
    return array_merge($sizes, array(
        'mbaa-exposition-thumb' => __('Miniature Exposition', 'mbaa'),
        'mbaa-oeuvre-medium' => __('Vue Moyenne Œuvre', 'mbaa'),
        'mbaa-oeuvre-large' => __('Vue Détaillée Œuvre', 'mbaa'),
        'mbaa-oeuvre-fullscreen' => __('Plein Écran', 'mbaa'),
    ));
}
add_filter('image_size_names_choose', 'mbaa_custom_image_size_names');

/**
 * Système de toast notifications (optionnel)
 */
function mbaa_enqueue_toast_system() {
    ?>
    <script>
    var mbaaToast = {
        success: function(message) {
            this.show(message, 'success');
        },
        error: function(message) {
            this.show(message, 'error');
        },
        warning: function(message) {
            this.show(message, 'warning');
        },
        info: function(message) {
            this.show(message, 'info');
        },
        show: function(message, type) {
            var toast = jQuery('<div class="mbaa-toast mbaa-toast-' + type + '">' + message + '</div>');
            
            jQuery('body').append(toast);
            
            setTimeout(function() {
                toast.addClass('mbaa-toast-show');
            }, 100);
            
            setTimeout(function() {
                toast.removeClass('mbaa-toast-show');
                setTimeout(function() {
                    toast.remove();
                }, 300);
            }, 4000);
        }
    };
    </script>

    <?php
}
add_action('admin_footer', 'mbaa_enqueue_toast_system');

/**
 * Logger les uploads pour audit musée
 */
function mbaa_log_upload($attachment_id) {
    $file = get_attached_file($attachment_id);
    $user = wp_get_current_user();
    
    $log_entry = sprintf(
        "[%s] Utilisateur: %s (ID: %d) | Fichier: %s | ID Attachment: %d\n",
        current_time('Y-m-d H:i:s'),
        $user->display_name,
        $user->ID,
        basename($file),
        $attachment_id
    );
    
    $log_file = WP_CONTENT_DIR . '/mbaa-upload-logs.txt';
    error_log($log_entry, 3, $log_file);
}
add_action('add_attachment', 'mbaa_log_upload');