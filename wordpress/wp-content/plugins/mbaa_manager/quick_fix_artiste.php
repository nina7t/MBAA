<?php
/**
 * Quick fix pour associer un artiste aux œuvres existantes
 */

if (!defined('ABSPATH')) {
    // Si on n'est pas dans WordPress, charger WordPress
    $wp_load = dirname(__FILE__) . '/../../../wp-load.php';
    if (file_exists($wp_load)) {
        require_once($wp_load);
    }
}

global $wpdb;

echo "<h1>Quick Fix - Association Artiste</h1>";

// 1. Trouver ou créer l'artiste "Paul Collomb"
$artiste = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}mbaa_artiste WHERE nom LIKE '%Collomb%'");

if (!$artiste) {
    echo "<p style='color: orange;'>Création de l'artiste 'Paul Collomb'...</p>";
    $result = $wpdb->insert(
        $wpdb->prefix . 'mbaa_artiste',
        array(
            'nom' => 'Paul Collomb',
            'biographie' => 'Artiste français contemporain connu pour ses natures mortes et paysages.',
            'nationalite' => 'Française',
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        )
    );
    
    if ($result) {
        $artiste_id = $wpdb->insert_id;
        echo "<p style='color: green;'>✅ Artiste créé avec ID: $artiste_id</p>";
    } else {
        echo "<p style='color: red;'>❌ Erreur création artiste: " . $wpdb->last_error . "</p>";
        exit;
    }
} else {
    $artiste_id = $artiste->id_artiste;
    echo "<p style='color: green;'>✅ Artiste 'Paul Collomb' trouvé avec ID: $artiste_id</p>";
}

// 2. Mettre à jour les œuvres 2 et 3
$oeuvres_to_update = [2, 3];

foreach ($oeuvres_to_update as $oeuvre_id) {
    echo "<h3>Mise à jour de l'œuvre $oeuvre_id:</h3>";
    
    // Vérifier l'œuvre existe
    $oeuvre = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}mbaa_oeuvre WHERE id_oeuvre = %d",
        $oeuvre_id
    ));
    
    if (!$oeuvre) {
        echo "<p style='color: red;'>❌ Œuvre $oeuvre_id non trouvée</p>";
        continue;
    }
    
    echo "<p>État actuel: Artiste ID = " . ($oeuvre->id_artiste ?? 'NULL') . "</p>";
    
    // Mettre à jour l'artiste
    $result = $wpdb->update(
        $wpdb->prefix . 'mbaa_oeuvre',
        array('id_artiste' => $artiste_id),
        array('id_oeuvre' => $oeuvre_id),
        array('%d'),
        array('%d')
    );
    
    if ($result !== false) {
        echo "<p style='color: green;'>✅ Œuvre $oeuvre_id mise à jour avec l'artiste $artiste_id</p>";
    } else {
        echo "<p style='color: red;'>❌ Erreur mise à jour œuvre $oeuvre_id: " . $wpdb->last_error . "</p>";
    }
}

// 3. Tester la recherche d'œuvres similaires
echo "<h2>Test de la recherche d'œuvres similaires:</h2>";

$test_oeuvre_id = 2;
$similaires = $wpdb->get_results($wpdb->prepare("
    SELECT o.*, a.nom AS artiste_nom
    FROM {$wpdb->prefix}mbaa_oeuvre o
    LEFT JOIN {$wpdb->prefix}mbaa_artiste a ON o.id_artiste = a.id_artiste
    WHERE o.id_oeuvre != %d 
    AND o.visible_galerie = 1
    AND o.id_artiste = %d
    ORDER BY RAND()
    LIMIT 6
", $test_oeuvre_id, $artiste_id));

echo "<p><strong>Œuvre de référence: ID $test_oeuvre_id</strong></p>";
echo "<p><strong>Œuvres similaires trouvées: " . count($similaires) . "</strong></p>";

foreach ($similaires as $sim) {
    echo "<p>📎 Œuvre ID: {$sim->id_oeuvre} | Titre: {$sim->titre} | Artiste: {$sim->artiste_nom}</p>";
}

// 4. Afficher les liens pour tester
echo "<h2>Liens pour tester:</h2>";
echo "<p><a href='" . home_url('/collections/?oeuvre_id=2') . "' target='_blank'>🔗 Voir l'œuvre 2 (Le repas des amis)</a></p>";
echo "<p><a href='" . home_url('/collections/?oeuvre_id=3') . "' target='_blank'>🔗 Voir l'œuvre 3 (Venise)</a></p>";

echo "<h2 style='color: green;'>✅ Terminé !</h2>";
echo "<p>Les œuvres 2 et 3 ont maintenant le même artiste. Testez les liens ci-dessus.</p>";
echo "<p><small>Vous pouvez supprimer ce fichier après utilisation.</small></p>";
?>
