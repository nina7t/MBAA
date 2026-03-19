<?php
/**
 * Script de débogage pour l'audioguide
 */

// Simuler l'environnement WordPress pour les tests
if (!defined('ABSPATH')) {
    // Charger WordPress manuellement
    $wp_load = dirname(__FILE__, 3) . '/wp-load.php';
    if (file_exists($wp_load)) {
        require_once($wp_load);
    } else {
        echo "❌ Impossible de charger WordPress\n";
        exit;
    }
}

global $wpdb;

echo "<pre>";
echo "=== DÉBOGAGE AUDIOGUIDE ===\n\n";

// 1. Vérifier la table
$table_name = $wpdb->prefix . 'mbaa_audioguide';
echo "Table: $table_name\n";

$tables = $wpdb->get_results("SHOW TABLES LIKE '$table_name'");
if (empty($tables)) {
    echo "❌ Table non trouvée\n";
    
    // Lister toutes les tables MBAA
    $all_tables = $wpdb->get_results("SHOW TABLES LIKE '%mbaa%'");
    echo "\nTables MBAA disponibles:\n";
    foreach ($all_tables as $table) {
        $table_name = array_values((array)$table)[0];
        echo "- $table_name\n";
    }
    exit;
} else {
    echo "✅ Table trouvée\n";
}

// 2. Structure
echo "\n--- Structure ---\n";
$structure = $wpdb->get_results("DESCRIBE $table_name");
foreach ($structure as $col) {
    echo "- {$col->Field}: {$col->Type}\n";
}

// 3. Nombre d'entrées
$count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
echo "\n--- Nombre d'audioguides ---\n";
echo "Total: $count\n";

// 4. Liste des audioguides
echo "\n--- Liste des audioguides ---\n";
$audioguides = $wpdb->get_results("
    SELECT ag.*, o.titre as oeuvre_titre, a.nom as artiste_nom
    FROM $table_name ag
    LEFT JOIN {$wpdb->prefix}mbaa_oeuvre o ON ag.id_oeuvre = o.id_oeuvre
    LEFT JOIN {$wpdb->prefix}mbaa_artiste a ON o.id_artiste = a.id_artiste
    ORDER BY o.titre ASC
");

if (empty($audioguides)) {
    echo "❌ Aucun audioguide trouvé\n";
} else {
    foreach ($audioguides as $ag) {
        echo "ID: {$ag->id_audioguide}\n";
        echo "  Œuvre: " . ($ag->oeuvre_titre ?? 'N/A') . "\n";
        echo "  Artiste: " . ($ag->artiste_nom ?? 'N/A') . "\n";
        echo "  URL: " . ($ag->fichier_audio_url ?? 'NULL') . "\n";
        echo "  Durée: " . ($ag->duree_secondes ?? 'NULL') . "s\n";
        echo "  Langue: " . ($ag->langue ?? 'NULL') . "\n";
        echo "  ---\n";
    }
}

// 5. Test avec une œuvre spécifique si ID fourni
if (isset($_GET['oeuvre_id'])) {
    $oeuvre_id = intval($_GET['oeuvre_id']);
    echo "\n--- Test pour œuvre ID $oeuvre_id ---\n";
    
    // Vérifier si l'œuvre existe
    $oeuvre = $wpdb->get_row($wpdb->prepare("
        SELECT * FROM {$wpdb->prefix}mbaa_oeuvre WHERE id_oeuvre = %d
    ", $oeuvre_id));
    
    if ($oeuvre) {
        echo "✅ Œuvre trouvée: {$oeuvre->titre}\n";
        
        // Vérifier l'audioguide
        $audioguide = $wpdb->get_row($wpdb->prepare("
            SELECT * FROM $table_name WHERE id_oeuvre = %d
        ", $oeuvre_id));
        
        if ($audioguide) {
            echo "✅ Audioguide trouvé\n";
            echo "URL: {$audioguide->fichier_audio_url}\n";
            
            // Tester si l'URL est accessible
            $url_parts = parse_url($audioguide->fichier_audio_url);
            if ($url_parts && isset($url_parts['path'])) {
                $file_path = $_SERVER['DOCUMENT_ROOT'] . $url_parts['path'];
                echo "Chemin: $file_path\n";
                echo "Fichier existe: " . (file_exists($file_path) ? '✅' : '❌') . "\n";
            }
        } else {
            echo "❌ Aucun audioguide pour cette œuvre\n";
        }
    } else {
        echo "❌ Œuvre ID $oeuvre_id non trouvée\n";
    }
}

echo "\n=== FIN ===\n";
echo "</pre>";
?>
