<?php
/**
 * Ajouter 6 artistes en lien pour "Le repas des amis"
 */

if (!defined('ABSPATH')) {
    // Si on n'est pas dans WordPress, charger WordPress
    $wp_load = dirname(__FILE__) . '/../../../wp-load.php';
    if (file_exists($wp_load)) {
        require_once($wp_load);
    }
}

global $wpdb;

echo "<h1>Ajout de 6 artistes en lien pour 'Le repas des amis'</h1>";

// 1. Récupérer l'œuvre "Le repas des amis"
$oeuvre_repas = $wpdb->get_row($wpdb->prepare(
    "SELECT * FROM {$wpdb->prefix}mbaa_oeuvre WHERE titre = %s",
    "Le repas des amis"
));

if (!$oeuvre_repas) {
    echo "<p style='color: red;'>❌ Œuvre 'Le repas des amis' non trouvée</p>";
    exit;
}

echo "<p>Œuvre trouvée : ID {$oeuvre_repas->id_oeuvre} - '{$oeuvre_repas->titre}'</p>";

// 2. Créer 6 artistes avec des œuvres dans les mêmes catégories
$artistes_data = [
    [
        'nom' => 'Claude Monet',
        'biographie' => 'Peintre impressionniste français, fondateur du mouvement impressionniste. Célèbre pour ses séries de nymphéas et ses paysages de Giverny.',
        'nationalite' => 'Française',
        'oeuvre_titre' => 'Les Nymphéas',
        'oeuvre_description' => 'Une série emblématique de peintures représentant le jardin aquatique de Giverny.',
        'oeuvre_image' => '/wp-content/themes/Mbaa_theme/asset/Img/tableaux/tableau-collomb-1.png',
        'oeuvre_dimensions' => '100 x 200 cm',
        'oeuvre_technique' => 'Huile sur toile',
        'oeuvre_date' => '1916'
    ],
    [
        'nom' => 'Vincent van Gogh',
        'biographie' => 'Peintre post-impressionniste néerlandais. Ses œuvres vibrantes et expressives ont profondément influencé l\'art moderne.',
        'nationalite' => 'Néerlandaise',
        'oeuvre_titre' => 'Les Tournesols',
        'oeuvre_description' => 'Nature morte iconique avec des tournesols dans un vase, symbole de la lumière et de la vie.',
        'oeuvre_image' => '/wp-content/themes/Mbaa_theme/asset/Img/tableaux/tableau-art-contemporain.png',
        'oeuvre_dimensions' => '92.1 x 73 cm',
        'oeuvre_technique' => 'Huile sur toile',
        'oeuvre_date' => '1888'
    ],
    [
        'nom' => 'Paul Cézanne',
        'biographie' => 'Peintre post-impressionniste français, considéré comme le père de l\'art moderne. A influencé Picasso et les cubistes.',
        'nationalite' => 'Française',
        'oeuvre_titre' => 'Les Joueurs de cartes',
        'oeuvre_description' => 'Scène de genre intimiste montrant des paysans jouant aux cartes, dans un style géométrique révolutionnaire.',
        'oeuvre_image' => '/wp-content/themes/Mbaa_theme/asset/Img/tableaux/tableau-collomb-2.webp',
        'oeuvre_dimensions' => '65 x 81 cm',
        'oeuvre_technique' => 'Huile sur toile',
        'oeuvre_date' => '1892'
    ],
    [
        'nom' => 'Auguste Renoir',
        'biographie' => 'Peintre impressionniste français, célèbre pour ses scènes de la vie parisienne et ses portraits lumineux.',
        'nationalite' => 'Française',
        'oeuvre_titre' => 'Le Déjeuner des Canotiers',
        'oeuvre_description' => 'Scène animée d\'un déjeuner sur la Seine, capturant la joie de vivre parisienne.',
        'oeuvre_image' => '/wp-content/themes/Mbaa_theme/asset/Img/tableaux/tableau-collomb-3.jpg',
        'oeuvre_dimensions' => '130 x 173 cm',
        'oeuvre_technique' => 'Huile sur toile',
        'oeuvre_date' => '1881'
    ],
    [
        'nom' => 'Edgar Degas',
        'biographie' => 'Peintre et sculpteur impressionniste français, spécialisé dans les scènes de ballet et les courses de chevaux.',
        'nationalite' => 'Française',
        'oeuvre_titre' => 'La Classe de danse',
        'oeuvre_description' => 'Scène de ballet montrant des danseuses répétant, avec une maîtrise exceptionnelle du mouvement.',
        'oeuvre_image' => '/wp-content/themes/Mbaa_theme/asset/Img/tableaux/tableau-collomb-1.png',
        'oeuvre_dimensions' => '85 x 75 cm',
        'oeuvre_technique' => 'Huile sur toile',
        'oeuvre_date' => '1874'
    ],
    [
        'nom' => 'Camille Pissarro',
        'biographie' => 'Peintre impressionniste franco-danois, considéré comme le "doyen" des impressionnistes. Mentor de nombreux jeunes artistes.',
        'nationalite' => 'Française',
        'oeuvre_titre' => 'Boulevard Montmartre',
        'oeuvre_description' => 'Vue animée du boulevard Montmartre à Paris, capturant l\'effervescence de la vie urbaine.',
        'oeuvre_image' => '/wp-content/themes/Mbaa_theme/asset/Img/tableaux/art-contemporain.png',
        'oeuvre_dimensions' => '73 x 92 cm',
        'oeuvre_technique' => 'Huile sur toile',
        'oeuvre_date' => '1897'
    ]
];

// Récupérer les caractéristiques de l'œuvre "Le repas des amis" pour créer des œuvres similaires
$id_epoque_similaire = $oeuvre_repas->id_epoque;
$id_categorie_similaire = $oeuvre_repas->id_categorie;
$id_medium_similaire = $oeuvre_repas->id_medium;

echo "<h2>Création des 6 artistes et leurs œuvres :</h2>";

foreach ($artistes_data as $index => $artiste_data) {
    // 1. Créer l'artiste
    $existing_artiste = $wpdb->get_row($wpdb->prepare(
        "SELECT id_artiste FROM {$wpdb->prefix}mbaa_artiste WHERE nom = %s",
        $artiste_data['nom']
    ));
    
    if ($existing_artiste) {
        $id_artiste = $existing_artiste->id_artiste;
        echo "<p style='color: blue;'>✅ Artiste '{$artiste_data['nom']}' existe déjà (ID: $id_artiste)</p>";
    } else {
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
            $id_artiste = $wpdb->insert_id;
            echo "<p style='color: green;'>✅ Artiste '{$artiste_data['nom']}' créé (ID: $id_artiste)</p>";
        } else {
            echo "<p style='color: red;'>❌ Erreur création artiste '{$artiste_data['nom']}': " . $wpdb->last_error . "</p>";
            continue;
        }
    }
    
    // 2. Créer une œuvre pour cet artiste avec les mêmes caractéristiques
    $existing_oeuvre = $wpdb->get_row($wpdb->prepare(
        "SELECT id_oeuvre FROM {$wpdb->prefix}mbaa_oeuvre WHERE titre = %s",
        $artiste_data['oeuvre_titre']
    ));
    
    if ($existing_oeuvre) {
        echo "<p style='color: orange;'>⚠️ Œuvre '{$artiste_data['oeuvre_titre']}' existe déjà</p>";
    } else {
        $result = $wpdb->insert(
            $wpdb->prefix . 'mbaa_oeuvre',
            array(
                'titre' => $artiste_data['oeuvre_titre'],
                'description' => $artiste_data['oeuvre_description'],
                'image_url' => $artiste_data['oeuvre_image'],
                'dimensions' => $artiste_data['oeuvre_dimensions'],
                'technique' => $artiste_data['oeuvre_technique'],
                'date_creation' => $artiste_data['oeuvre_date'],
                'id_artiste' => $id_artiste,
                'id_epoque' => $id_epoque_similaire, // Même époque que "Le repas des amis"
                'id_categorie' => $id_categorie_similaire, // Même catégorie
                'id_medium' => $id_medium_similaire, // Même medium
                'visible_galerie' => 1,
                'visible_accueil' => 1,
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            )
        );
        
        if ($result) {
            $id_oeuvre = $wpdb->insert_id;
            echo "<p style='color: green;'>✅ Œuvre '{$artiste_data['oeuvre_titre']}' créée (ID: $id_oeuvre)</p>";
            
            // Créer le QR code
            $wpdb->insert(
                $wpdb->prefix . 'mbaa_qr_codes',
                array(
                    'id_oeuvre' => $id_oeuvre,
                    'code_qr' => 'QR_' . $id_oeuvre . '_' . uniqid(),
                    'url' => home_url('/collections/?oeuvre_id=' . $id_oeuvre),
                    'type' => 'oeuvre',
                    'statut' => 'actif',
                    'creation' => current_time('mysql'),
                    'mis_a_jour' => current_time('mysql')
                )
            );
        } else {
            echo "<p style='color: red;'>❌ Erreur création œuvre '{$artiste_data['oeuvre_titre']}': " . $wpdb->last_error . "</p>";
        }
    }
}

// 3. Afficher le lien pour tester "Le repas des amis"
echo "<h2>Lien pour tester 'Le repas des amis' :</h2>";
echo "<p><a href='" . home_url('/collections/?oeuvre_id=' . $oeuvre_repas->id_oeuvre) . "' target='_blank'>🔗 Voir 'Le repas des amis' avec ses 6 artistes en lien</a></p>";

echo "<h2 style='color: green;'>✅ Terminé !</h2>";
echo "<p>Les 6 artistes ont été créés avec des œuvres dans les mêmes catégories que 'Le repas des amis'.</p>";
echo "<p>Sur la page de 'Le repas des amis', vous devriez maintenant voir 6 artistes dans la section 'Artistes en lien'.</p>";
echo "<p><small>Vous pouvez supprimer ce fichier après utilisation.</small></p>";
?>
