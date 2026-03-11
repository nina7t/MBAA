<?php
/**
 * Script d'importation des artistes depuis le CSV
 */

// Charger WordPress
require_once('../../../wp-config.php');
require_once('../../../wp-load.php');

// Vérifier si on est en admin
if (!current_user_can('manage_options')) {
    die('Accès non autorisé');
}

// Inclure les classes du plugin
require_once '../includes/class-mbaa-database.php';

global $wpdb;
$db = new MBAA_Database();

echo "<h2>Importation des artistes depuis le CSV</h2>";

// Chemin vers le fichier CSV
$csv_file = __DIR__ . '/base-joconde-extrait.csv';

if (!file_exists($csv_file)) {
    die("Fichier CSV non trouvé : " . $csv_file);
}

// Lire le fichier CSV
$handle = fopen($csv_file, 'r');
if (!$handle) {
    die("Impossible d'ouvrir le fichier CSV");
}

// Lire l'en-tête
$headers = fgetcsv($handle, ';');
$artistes_importes = 0;
$artistes_ignores = 0;
$artistes_en_double = array();

echo "<p>Début de l'importation...</p>";

while (($row = fgetcsv($handle, ';')) !== FALSE) {
    // Vérifier qu'on a assez de colonnes
    if (count($row) < 5) continue;
    
    // L'artiste est dans la colonne "Auteur" (colonne 5, index 4)
    $auteur = trim($row[4]);
    
    // Nettoyer et extraire les noms multiples
    if (empty($auteur)) continue;
    
    // Extraire les noms d'artistes (séparés par ;)
    $noms = explode(';', $auteur);
    
    foreach ($noms as $nom) {
        $nom = trim($nom);
        
        // Nettoyer le nom (enlever guillemets et espaces)
        $nom = preg_replace('/^["\']|["\']$/', '', $nom);
        $nom = trim($nom);
        
        if (empty($nom)) continue;
        
        // Vérifier si déjà importé
        if (in_array(strtolower($nom), $artistes_en_double)) {
            $artistes_ignores++;
            continue;
        }
        
        // Vérifier si existe déjà dans la BDD
        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$db->table_artiste} WHERE LOWER(nom) = %s",
            strtolower($nom)
        ));
        
        if (!$exists) {
            // Insérer le nouvel artiste
            $result = $wpdb->insert(
                $db->table_artiste,
                array(
                    'nom' => $nom,
                    'biographie' => '',
                    'date_creation' => current_time('mysql')
                ),
                array('%s', '%s', '%s')
            );
            
            if ($result) {
                $artistes_importes++;
                $artistes_en_double[] = strtolower($nom);
                echo "<p>✅ Artiste importé : <strong>" . esc_html($nom) . "</strong></p>";
            } else {
                echo "<p>❌ Erreur lors de l'importation de : <strong>" . esc_html($nom) . "</strong></p>";
                echo "<p>Erreur WordPress : " . $wpdb->last_error . "</p>";
            }
        } else {
            $artistes_ignores++;
            echo "<p>⚠️ Artiste déjà existant : <strong>" . esc_html($nom) . "</strong></p>";
        }
    }
}

fclose($handle);

echo "<h3>Résumé de l'importation</h3>";
echo "<ul>";
echo "<li>✅ Artistes importés : <strong>" . $artistes_importes . "</strong></li>";
echo "<li>⚠️ Artistes ignorés (doublons) : <strong>" . $artistes_ignores . "</strong></li>";
echo "</ul>";

echo "<p><a href='" . admin_url('admin.php?page=mbaa-artistes') . "' class='button button-primary'>Voir les artistes</a></p>";
?>
