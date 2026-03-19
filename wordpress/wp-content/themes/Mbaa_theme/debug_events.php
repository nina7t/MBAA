<?php
// Script de debug pour les événements
global $wpdb;

echo "<h2>Debug Événements MBAA</h2>";

// Vérifier les tables
$tables = $wpdb->get_results("SHOW TABLES LIKE '%mbaa%'");
echo "<h3>Tables trouvées :</h3>";
foreach ($tables as $table) {
    foreach ($table as $value) {
        echo "- $value<br>";
    }
}

// Vérifier la table événements
$table_evenements = $wpdb->prefix . 'mbaa_evenements';
$table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_evenements'") === $table_evenements;

echo "<h3>Table $table_evenements existe : " . ($table_exists ? 'OUI' : 'NON') . "</h3>";

if ($table_exists) {
    $count = $wpdb->get_var("SELECT COUNT(*) FROM $table_evenements");
    echo "<h3>Nombre d'événements : $count</h3>";
    
    $events = $wpdb->get_results("SELECT * FROM $table_evenements ORDER BY date_evenement ASC LIMIT 5");
    echo "<h3>5 premiers événements :</h3>";
    echo "<pre>";
    print_r($events);
    echo "</pre>";
} else {
    echo "<p style='color: red;'>La table n'existe pas. Veuillez importer le SQL.</p>";
}

// Vérifier les types
$table_types = $wpdb->prefix . 'mbaa_types_evenements';
$types_exist = $wpdb->get_var("SHOW TABLES LIKE '$table_types'") === $table_types;

echo "<h3>Table $table_types existe : " . ($types_exist ? 'OUI' : 'NON') . "</h3>";

if ($types_exist) {
    $types = $wpdb->get_results("SELECT * FROM $table_types");
    echo "<h3>Types d'événements :</h3>";
    echo "<pre>";
    print_r($types);
    echo "</pre>";
}
?>
