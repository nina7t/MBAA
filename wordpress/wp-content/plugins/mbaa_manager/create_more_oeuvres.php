<?php
/**
 * Création d'œuvres supplémentaires en base de données
 */

if (!defined('ABSPATH')) {
    // Si on n'est pas dans WordPress, charger WordPress
    $wp_load = dirname(__FILE__) . '/../../../wp-load.php';
    if (file_exists($wp_load)) {
        require_once($wp_load);
    }
}

global $wpdb;

echo "<h1>Création d'œuvres supplémentaires</h1>";

// 1. Créer des artistes supplémentaires
$artistes_data = [
    [
        'nom' => 'Frida Kahlo',
        'biographie' => 'Artiste mexicaine emblématique du surréalisme, connue pour ses autoportraits intenses et son exploration de l\'identité, la douleur et la culture mexicaine.',
        'nationalite' => 'Mexicaine'
    ],
    [
        'nom' => 'Salvador Dalí',
        'biographie' => 'Maître du surréalisme espagnol, célèbre pour ses œuvres oniriques et ses moustaches iconiques. Ses peintures explorent l\'inconscient et le monde des rêves.',
        'nationalite' => 'Espagnole'
    ],
    [
        'nom' => 'Gustav Klimt',
        'biographie' => 'Peintre autrichien, figure majeure de l\'Art nouveau. Ses œuvres sont caractérisées par l\'utilisation de l\'or et des motifs décoratifs complexes.',
        'nationalite' => 'Autrichienne'
    ],
    [
        'nom' => 'Tamara de Lempicka',
        'biographie' => 'Peintre polonaise Art déco, connue pour ses portraits élégants et ses formes stylisées. Elle incarne le glamour et la modernité des années 1920-1930.',
        'nationalite' => 'Polonaise'
    ],
    [
        'nom' => 'Yves Klein',
        'biographie' => 'Artiste français du Nouveau réalisme, inventeur de l\'IKB (International Klein Blue). Ses œuvres explorent le vide, la couleur et l\'immatériel.',
        'nationalite' => 'Française'
    ],
    [
        'nom' => 'Andy Warhol',
        'biographie' => 'Pape du Pop art américain, célèbre pour ses sérigraphies de célébrités et ses Campbell\'s Soup Cans. Il a révolutionné l\'art en s\'inspirant de la culture de masse.',
        'nationalite' => 'Américaine'
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

// 2. Créer les œuvres avec les images des assets
$oeuvres_data = [
    [
        'titre' => 'Les deux Frida',
        'description' => 'Autoportrait double représentant la dualité de l\'artiste, avec deux Frida se tenant la main, symbolisant son cœur brisé et son héritage mixte.',
        'image' => '/wp-content/themes/Mbaa_theme/asset/Img/tableaux/tableau-collomb-1.png',
        'dimensions' => '173 x 173 cm',
        'technique' => 'Huile sur toile',
        'date_creation' => '1939',
        'artiste_index' => 0,
        'epoque' => 'Moderne',
        'categorie' => 'Peinture',
        'medium' => 'Huile sur toile'
    ],
    [
        'titre' => 'La persistance de la mémoire',
        'description' => 'Œuvre surréaliste emblématique avec des montres molles, explorant la relativité du temps et l\'inconscient dans un paysage désertique.',
        'image' => '/wp-content/themes/Mbaa_theme/asset/Img/tableaux/tableau-art-contemporain.png',
        'dimensions' => '24 x 33 cm',
        'technique' => 'Huile sur toile',
        'date_creation' => '1931',
        'artiste_index' => 1,
        'epoque' => 'Moderne',
        'categorie' => 'Peinture',
        'medium' => 'Huile sur toile'
    ],
    [
        'titre' => 'Le baiser',
        'description' => 'Chef-d\'œuvre Art nouveau représentant un couple enlacé dans un décor doré, symbolisant l\'amour éternel et la beauté sensuelle.',
        'image' => '/wp-content/themes/Mbaa_theme/asset/Img/tableaux/tableau-collomb-2.webp',
        'dimensions' => '180 x 180 cm',
        'technique' => 'Huile et feuille d\'or sur toile',
        'date_creation' => '1908',
        'artiste_index' => 2,
        'epoque' => 'Moderne',
        'categorie' => 'Peinture',
        'medium' => 'Huile sur toile'
    ],
    [
        'titre' => 'La jeune fille en vert',
        'description' => 'Portrait Art déco emblématique d\'une jeune femme élégante, incarnant le glamour et la modernité des années 1920.',
        'image' => '/wp-content/themes/Mbaa_theme/asset/Img/tableaux/tableau-collomb-3.jpg',
        'dimensions' => '100 x 65 cm',
        'technique' => 'Huile sur toile',
        'date_creation' => '1927',
        'artiste_index' => 3,
        'epoque' => 'Moderne',
        'categorie' => 'Peinture',
        'medium' => 'Huile sur toile'
    ],
    [
        'titre' => 'Anthropométrie de l\'époque bleue',
        'description' => 'Performance artistique où des modèles féminins couverts de peinture bleue laissent leur empreinte sur la toile, explorant le corps et l\'immatériel.',
        'image' => '/wp-content/themes/Mbaa_theme/asset/Img/tableaux/tableau-collomb-1.png',
        'dimensions' => 'Variable',
        'technique' => 'Peinture IKB sur toile',
        'date_creation' => '1960',
        'artiste_index' => 4,
        'epoque' => 'Contemporain',
        'categorie' => 'Art contemporain',
        'medium' => 'Peinture'
    ],
    [
        'titre' => 'Campbell\'s Soup Cans',
        'description' => 'Série de 32 sérigraphies représentant les différentes saveurs de soupes Campbell, critique de la société de consommation et célébration de l\'ordinaire.',
        'image' => '/wp-content/themes/Mbaa_theme/asset/Img/tableaux/art-contemporain.png',
        'dimensions' => '51 x 41 cm (chaque)',
        'technique' => 'Sérigraphie sur toile',
        'date_creation' => '1962',
        'artiste_index' => 5,
        'epoque' => 'Contemporain',
        'categorie' => 'Art contemporain',
        'medium' => 'Sérigraphie'
    ],
    [
        'titre' => 'Autoportrait au collier d\'épines',
        'description' => 'Autoportrait poignant de Frida Kahlo avec un collier d\'épines, symbolisant la douleur et la souffrance transformées en force créatrice.',
        'image' => '/wp-content/themes/Mbaa_theme/asset/Img/tableaux/tableau-collomb-2.webp',
        'dimensions' => '65 x 49 cm',
        'technique' => 'Huile sur toile',
        'date_creation' => '1940',
        'artiste_index' => 0,
        'epoque' => 'Moderne',
        'categorie' => 'Peinture',
        'medium' => 'Huile sur toile'
    ],
    [
        'titre' => 'Le grand masturbateur',
        'description' => 'Œuvre surréaliste complexe explorant les désirs et les angoisses de l\'inconscient, avec des formes biomorphiques et symboliques.',
        'image' => '/wp-content/themes/Mbaa_theme/asset/Img/tableaux/tableau-collomb-3.jpg',
        'dimensions' => '110 x 80 cm',
        'technique' => 'Huile sur toile',
        'date_creation' => '1929',
        'artiste_index' => 1,
        'epoque' => 'Moderne',
        'categorie' => 'Peinture',
        'medium' => 'Huile sur toile'
    ],
    [
        'titre' => 'Portrait d\'Adele Bloch-Bauer',
        'description' => 'Portrait somptueux décoré d\'or et de motifs complexes, considéré comme l\'un des chefs-d\'œuvre de Klimt et de l\'Art nouveau.',
        'image' => '/wp-content/themes/Mbaa_theme/asset/Img/tableaux/tableau-collomb-1.png',
        'dimensions' => '138 x 138 cm',
        'technique' => 'Huile et feuille d\'or sur toile',
        'date_creation' => '1907',
        'artiste_index' => 2,
        'epoque' => 'Moderne',
        'categorie' => 'Peinture',
        'medium' => 'Huile sur toile'
    ],
    [
        'titre' => 'La musique',
        'description' => 'Composition Art déco élégante avec une figure féminine et un instrument de musique, incarnant l\'harmonie et la modernité des années 1920.',
        'image' => '/wp-content/themes/Mbaa_theme/asset/Img/tableaux/art-contemporain.png',
        'dimensions' => '100 x 65 cm',
        'technique' => 'Huile sur toile',
        'date_creation' => '1928',
        'artiste_index' => 3,
        'epoque' => 'Moderne',
        'categorie' => 'Peinture',
        'medium' => 'Huile sur toile'
    ]
];

echo "<h2>Création des œuvres supplémentaires :</h2>";

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
        echo "<p style='color: orange;'>⚠️ Œuvre '{$oeuvre_data['titre']}' existe déjà</p>";
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
            
            // Créer le QR code
            $wpdb->insert(
                $wpdb->prefix . 'mbaa_qr_codes',
                array(
                    'id_oeuvre' => $oeuvre_id,
                    'code_qr' => 'QR_' . $oeuvre_id . '_' . uniqid(),
                    'url' => home_url('/collections/?oeuvre_id=' . $oeuvre_id),
                    'type' => 'oeuvre',
                    'statut' => 'actif',
                    'creation' => current_time('mysql'),
                    'mis_a_jour' => current_time('mysql')
                )
            );
        } else {
            echo "<p style='color: red;'>❌ Erreur création œuvre '{$oeuvre_data['titre']}': " . $wpdb->last_error . "</p>";
        }
    }
}

// 3. Afficher les liens pour tester
echo "<h2>Liens pour tester les nouvelles œuvres :</h2>";

$created_oeuvres = $wpdb->get_results(
    "SELECT o.id_oeuvre, o.titre, a.nom as artiste_nom 
     FROM {$wpdb->prefix}mbaa_oeuvre o 
     LEFT JOIN {$wpdb->prefix}mbaa_artiste a ON o.id_artiste = a.id_artiste 
     WHERE o.titre IN ('Les deux Frida', 'La persistance de la mémoire', 'Le baiser', 'La jeune fille en vert', 'Anthropométrie de l\'époque bleue', 'Campbell\'s Soup Cans', 'Autoportrait au collier d\'épines', 'Le grand masturbateur', 'Portrait d\'Adele Bloch-Bauer', 'La musique')
     ORDER BY o.id_oeuvre"
);

foreach ($created_oeuvres as $oeuvre) {
    echo "<p><a href='" . home_url('/collections/?oeuvre_id=' . $oeuvre->id_oeuvre) . "' target='_blank'>🔗 Voir '{$oeuvre->titre}' par {$oeuvre->artiste_nom}</a></p>";
}

echo "<h2 style='color: green;'>✅ Terminé !</h2>";
echo "<p>10 œuvres supplémentaires ont été créées avec 6 artistes célèbres.</p>";
echo "<p>Chaque œuvre a des œuvres similaires et des artistes en lien basés sur les mêmes catégories/époques.</p>";
echo "<p><small>Vous pouvez supprimer ce fichier après utilisation.</small></p>";

// 4. Afficher des statistiques
$total_oeuvres = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}mbaa_oeuvre");
$total_artistes = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}mbaa_artiste");

echo "<h3>📊 Statistiques de la collection :</h3>";
echo "<p><strong>Total œuvres :</strong> $total_oeuvres</p>";
echo "<p><strong>Total artistes :</strong> $total_artistes</p>";
echo "<p><strong>Nouvelles œuvres créées :</strong> 10</p>";
echo "<p><strong>Nouveaux artistes créés :</strong> 6</p>";
?>
