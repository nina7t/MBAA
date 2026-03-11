<?php
/**
 * Script de test pour vérifier l'upload MBAA
 */

// Charger WordPress
require_once('../../../wp-config.php');

echo "<h1>Test Upload MBAA</h1>";

// Vérifier les permissions
echo "<h2>Permissions utilisateur actuel :</h2>";
$current_user = wp_get_current_user();
echo "ID: " . $current_user->ID . "<br>";
echo "Nom: " . $current_user->display_name . "<br>";
echo "Rôles: " . implode(', ', $current_user->roles) . "<br>";
echo "upload_files: " . (current_user_can('upload_files') ? 'OUI' : 'NON') . "<br>";
echo "mbaa_can_access_oeuvres: " . (current_user_can('mbaa_can_access_oeuvres') ? 'OUI' : 'NON') . "<br>";

// Vérifier les hooks AJAX
echo "<h2>Hooks AJAX enregistrés :</h2>";
global $wp_filter;
if (isset($wp_filter['wp_ajax_mbaa_uppy_upload'])) {
    echo "mbaa_uppy_upload: ENREGISTRÉ<br>";
} else {
    echo "mbaa_uppy_upload: NON ENREGISTRÉ<br>";
}

if (isset($wp_filter['wp_ajax_mbaa_upload_media'])) {
    echo "mbaa_upload_media: ENREGISTRÉ<br>";
} else {
    echo "mbaa_upload_media: NON ENREGISTRÉ<br>";
}

// Vérifier les MIME types
echo "<h2>Types MIME supportés :</h2>";
$mimes = get_allowed_mime_types();
echo "WebP: " . (isset($mimes['webp']) ? 'OUI (' . $mimes['webp'] . ')' : 'NON') . "<br>";
echo "JPEG: " . (isset($mimes['jpg']) ? 'OUI (' . $mimes['jpg'] . ')' : 'NON') . "<br>";
echo "PNG: " . (isset($mimes['png']) ? 'OUI (' . $mimes['png'] . ')' : 'NON') . "<br>";

// Vérifier les limites d'upload
echo "<h2>Limites d'upload :</h2>";
echo "upload_max_filesize (PHP): " . ini_get('upload_max_filesize') . "<br>";
echo "post_max_size (PHP): " . ini_get('post_max_size') . "<br>";
echo "upload_size_limit (WP): " . size_format(wp_max_upload_size()) . "<br>";

echo "<h2>Derniers uploads MBAA :</h2>";
$log_file = WP_CONTENT_DIR . '/mbaa-upload-logs.txt';
if (file_exists($log_file)) {
    $logs = file_get_contents($log_file);
    echo "<pre>" . htmlspecialchars($logs) . "</pre>";
} else {
    echo "Aucun log trouvé.";
}
?>
