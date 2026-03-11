<?php
/**
 * Création de 4 œuvres avec artistes et images des assets
 */

if (!defined('ABSPATH')) {
    // Si on n'est pas dans WordPress, charger WordPress
    $wp_load = dirname(__FILE__) . '/../../../wp-load.php';
    if (file_exists($wp_load)) {
        require_once($wp_load);
    }
}

global $wpdb;

echo "<h1>Création de 4 œuvres avec artistes liés</h1>";

// 1. Créer les artistes
$artistes_data = [
    [
        'nom' => 'Pierre Dubois',
        'biographie' => 'Artiste contemporain français, spécialisé dans les paysages urbains et les scènes de vie quotidienne.',
        'nationalite' => 'Française'
    ],
    [
        'nom' => 'Marie Laurent',
        'biographie' => 'Peintre abstraite connue pour ses compositions colorées et ses explorations des formes géométriques.',
        'nationalite' => 'Française'
    ],
    [
        'nom' => 'Jean Martin',
        'biographie' => 'Sculpteur et peintre, son œuvre explore la relation entre l\'homme et la nature.',
        'nationalite' => 'Belge'
    ],
    [
        'nom' => 'Sophie Chen',
        'biographie' => 'Artiste d\'origine asiatique, mélangeant les techniques traditionnelles avec l\'art contemporain.',
        'nationalite' => 'Française'
    ]
];

$artistes_ids = [];

foreach ($artistes_data as $artiste_data) {
    // Vérifier si l'artiste existe déjà
    $existing = $wpdb->get_row($wpdb->prepare(
        "SELECT id_artiste FROM {$wpdb->prefix}mbaa_artiste WHERE nom = %s",
        $artiste_data['nom']
    ));
    
    if ($existing) {
        $artistes_ids[] = $existing->id_artiste;
        echo "<p style='color: blue;'>✅ Artiste '{$artiste_data['nom']}' existe déjà (ID: {$existing->id_artiste})</p>";
    } else {
        // Créer l'artiste
        $result = $wpdb->insert(
            $wpdb->prefix . 'mbaa_artiste',
            array(
                'nom' => $artiste_data['nom'],
                'biographie' => $artiste_data['biographie'],
                'nationalite' => $artiste_data['nationalite'],
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            )
        );
        
        if ($result) {
            $artistes_ids[] = $wpdb->insert_id;
            echo "<p style='color: green;'>✅ Artiste '{$artiste_data['nom']}' créé avec ID: {$wpdb->insert_id}</p>";
        } else {
            echo "<p style='color: red;'>❌ Erreur création artiste '{$artiste_data['nom']}': " . $wpdb->last_error . "</p>";
        }
    }
}

// 2. Créer les époques, catégories, etc. si nécessaire
$epoques = ['Moderne', 'Contemporain', 'Classique'];
$categories = ['Peinture', 'Sculpture', 'Art contemporain'];
$mediums = ['Huile sur toile', 'Acrylique', 'Aquarelle', 'Sculpture sur bois'];

foreach ($epoques as $epoque) {
    $existing = $wpdb->get_row($wpdb->prepare(
        "SELECT id_epoque FROM {$wpdb->prefix}mbaa_epoque WHERE nom_epoque = %s",
        $epoque
    ));
    if (!$existing) {
        $wpdb->insert($wpdb->prefix . 'mbaa_epoque', ['nom_epoque' => $epoque]);
    }
}

foreach ($categories as $categorie) {
    $existing = $wpdb->get_row($wpdb->prepare(
        "SELECT id_categorie FROM {$wpdb->prefix}mbaa_categorie WHERE nom_categorie = %s",
        $categorie
    ));
    if (!$existing) {
        $wpdb->insert($wpdb->prefix . 'mbaa_categorie', ['nom_categorie' => $categorie]);
    }
}

foreach ($mediums as $medium) {
    $existing = $wpdb->get_row($wpdb->prepare(
        "SELECT id_medium FROM {$wpdb->prefix}mbaa_medium WHERE nom_medium = %s",
        $medium
    ));
    if (!$existing) {
        $wpdb->insert($wpdb->prefix . 'mbaa_medium', ['nom_medium' => $medium]);
    }
}

// 3. Créer les 4 œuvres avec les images des assets
$oeuvres_data = [
    [
        'titre' => 'Paris la nuit',
        'description' => 'Une scène nocturne animée de Paris, capturant l\'énergie de la ville qui ne dort jamais.',
        'image' => '/wp-content/themes/Mbaa_theme/asset/Img/tableaux/tableau-collomb-1.png',
        'dimensions' => '80 x 120 cm',
        'technique' => 'Huile sur toile',
        'date_creation' => '2022',
        'artiste_index' => 0,
        'epoque' => 'Contemporain',
        'categorie' => 'Peinture',
        'medium' => 'Huile sur toile'
    ],
    [
        'titre' => 'Composition abstraite',
        'description' => 'Une exploration dynamique des formes et des couleurs, invitant à la contemplation.',
        'image' => '/wp-content/themes/Mbaa_theme/asset/Img/tableaux/tableau-art-contemporain.png',
        'dimensions' => '100 x 100 cm',
        'technique' => 'Acrylique sur toile',
        'date_creation' => '2023',
        'artiste_index' => 1,
        'epoque' => 'Contemporain',
        'categorie' => 'Art contemporain',
        'medium' => 'Acrylique'
    ],
    [
        'titre' => 'Nature méditative',
        'description' => 'Une représentation sereine de la nature, évoquant la paix et la contemplation.',
        'image' => '/wp-content/themes/Mbaa_theme/asset/Img/tableaux/tableau-collomb-2.webp',
        'dimensions' => '60 x 80 cm',
        'technique' => 'Aquarelle sur papier',
        'date_creation' => '2021',
        'artiste_index' => 2,
        'epoque' => 'Moderne',
        'categorie' => 'Peinture',
        'medium' => 'Aquarelle'
    ],
    [
        'titre' => 'Fusion Est-Ouest',
        'description' => 'Une œuvre qui mélange les techniques traditionnelles asiatiques avec l\'art occidental contemporain.',
        'image' => '/wp-content/themes/Mbaa_theme/asset/Img/tableaux/tableau-collomb-3.jpg',
        'dimensions' => '90 x 90 cm',
        'technique' => 'Mixte sur toile',
        'date_creation' => '2023',
        'artiste_index' => 3,
        'epoque' => 'Contemporain',
        'categorie' => 'Art contemporain',
        'medium' => 'Huile sur toile'
    ]
];

echo "<h2>Création des 4 œuvres :</h2>";

foreach ($oeuvres_data as $index => $oeuvre_data) {
    // Récupérer les IDs
    $id_artiste = $artistes_ids[$oeuvre_data['artiste_index']];
    
    $id_epoque = $wpdb->get_var($wpdb->prepare(
        "SELECT id_epoque FROM {$wpdb->prefix}mbaa_epoque WHERE nom_epoque = %s",
        $oeuvre_data['epoque']
    ));
    
    $id_categorie = $wpdb->get_var($wpdb->prepare(
        "SELECT id_categorie FROM {$wpdb->prefix}mbaa_categorie WHERE nom_categorie = %s",
        $oeuvre_data['categorie']
    ));
    
    $id_medium = $wpdb->get_var($wpdb->prepare(
        "SELECT id_medium FROM {$wpdb->prefix}mbaa_medium WHERE nom_medium = %s",
        $oeuvre_data['medium']
    ));
    
    // Vérifier si l'œuvre existe déjà
    $existing = $wpdb->get_row($wpdb->prepare(
        "SELECT id_oeuvre FROM {$wpdb->prefix}mbaa_oeuvre WHERE titre = %s",
        $oeuvre_data['titre']
    ));
    
    if ($existing) {
        // Mettre à jour l'œuvre existante
        $result = $wpdb->update(
            $wpdb->prefix . 'mbaa_oeuvre',
            array(
                'titre' => $oeuvre_data['titre'],
                'description' => $oeuvre_data['description'],
                'image_url' => $oeuvre_data['image'],
                'dimensions' => $oeuvre_data['dimensions'],
                'technique' => $oeuvre_data['technique'],
                'date_creation' => $oeuvre_data['date_creation'],
                'id_artiste' => $id_artiste,
                'id_epoque' => $id_epoque,
                'id_categorie' => $id_categorie,
                'id_medium' => $id_medium,
                'visible_galerie' => 1,
                'visible_accueil' => 1,
                'updated_at' => current_time('mysql')
            ),
            array('id_oeuvre' => $existing->id_oeuvre),
            array('%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%d', '%d', '%d', '%s'),
            array('%d')
        );
        
        if ($result !== false) {
            $oeuvre_id = $existing->id_oeuvre;
            echo "<p style='color: blue;'>🔄 Œuvre '{$oeuvre_data['titre']}' mise à jour (ID: $oeuvre_id)</p>";
        } else {
            echo "<p style='color: red;'>❌ Erreur mise à jour œuvre '{$oeuvre_data['titre']}': " . $wpdb->last_error . "</p>";
            continue;
        }
    } else {
        // Créer l'œuvre
        $result = $wpdb->insert(
            $wpdb->prefix . 'mbaa_oeuvre',
            array(
                'titre' => $oeuvre_data['titre'],
                'description' => $oeuvre_data['description'],
                'image_url' => $oeuvre_data['image'],
                'dimensions' => $oeuvre_data['dimensions'],
                'technique' => $oeuvre_data['technique'],
                'date_creation' => $oeuvre_data['date_creation'],
                'id_artiste' => $id_artiste,
                'id_epoque' => $id_epoque,
                'id_categorie' => $id_categorie,
                'id_medium' => $id_medium,
                'visible_galerie' => 1,
                'visible_accueil' => 1,
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            )
        );
        
        if ($result) {
            $oeuvre_id = $wpdb->insert_id;
            echo "<p style='color: green;'>✅ Œuvre '{$oeuvre_data['titre']}' créée (ID: $oeuvre_id)</p>";
        } else {
            echo "<p style='color: red;'>❌ Erreur création œuvre '{$oeuvre_data['titre']}': " . $wpdb->last_error . "</p>";
            continue;
        }
    }
    
    // Créer le QR code
    $qr_url = home_url('/collections/?oeuvre_id=' . $oeuvre_id);
    $existing_qr = $wpdb->get_row($wpdb->prepare(
        "SELECT id_qr FROM {$wpdb->prefix}mbaa_qr_codes WHERE id_oeuvre = %d",
        $oeuvre_id
    ));
    
    if (!$existing_qr) {
        $wpdb->insert(
            $wpdb->prefix . 'mbaa_qr_codes',
            array(
                'id_oeuvre' => $oeuvre_id,
                'code_qr' => 'QR_' . $oeuvre_id . '_' . uniqid(),
                'url' => $qr_url,
                'type' => 'oeuvre',
                'statut' => 'actif',
                'creation' => current_time('mysql'),
                'mis_a_jour' => current_time('mysql')
            )
        );
    }
}

// 4. Afficher les liens pour tester
echo "<h2>Liens pour tester les œuvres :</h2>";

$created_oeuvres = $wpdb->get_results(
    "SELECT o.id_oeuvre, o.titre, a.nom as artiste_nom 
     FROM {$wpdb->prefix}mbaa_oeuvre o 
     LEFT JOIN {$wpdb->prefix}mbaa_artiste a ON o.id_artiste = a.id_artiste 
     WHERE o.titre IN ('Paris la nuit', 'Composition abstraite', 'Nature méditative', 'Fusion Est-Ouest')
     ORDER BY o.id_oeuvre"
);

foreach ($created_oeuvres as $oeuvre) {
    echo "<p><a href='" . home_url('/collections/?oeuvre_id=' . $oeuvre->id_oeuvre) . "' target='_blank'>🔗 Voir '{$oeuvre->titre}' par {$oeuvre->artiste_nom}</a></p>";
}

echo "<h2 style='color: green;'>✅ Terminé !</h2>";
echo "<p>Les 4 œuvres ont été créées avec des artistes différents. Chaque œuvre aura des œuvres similaires et des artistes en lien basés sur les mêmes catégories/époques.</p>";
echo "<p><small>Vous pouvez supprimer ce fichier après utilisation.</small></p>";
?>
