<?php
/**
 * Script de debug pour l'upload Uppy
 */

// Charger WordPress
require_once('../../../wp-config.php');

header('Content-Type: application/json');

// Logger toutes les données reçues
error_log('=== DEBUG UPLOAD MBAA ===');
error_log('POST: ' . print_r($_POST, true));
error_log('FILES: ' . print_r($_FILES, true));
error_log('HEADERS: ' . print_r(getallheaders(), true));
error_log('REQUEST: ' . print_r($_REQUEST, true));

// Répondre avec les infos
echo json_encode([
    'success' => true,
    'debug' => [
        'post' => $_POST,
        'files' => $_FILES,
        'headers' => getallheaders(),
        'request' => $_REQUEST
    ]
]);
?>
