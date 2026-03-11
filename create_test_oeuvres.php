<?php
/**
 * Script pour créer des œuvres de test dans la base de données
 */

// Connexion à la base de données WordPress
$host = 'localhost';
$dbname = 'mbaa_db';
$username = 'root';
$password = 'root';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h1>Création d'œuvres de test</h1>";
    
    // 1. Vérifier si l'artiste "Paul Collomb" existe
    $stmt = $pdo->prepare("SELECT id_artiste FROM wp_mbaa_artiste WHERE nom LIKE ?");
    $stmt->execute(['%Collomb%']);
    $artiste = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$artiste) {
        echo "<p style='color: orange;'>Création de l'artiste 'Paul Collomb'...</p>";
        $stmt = $pdo->prepare("INSERT INTO wp_mbaa_artiste (nom, biographie, nationalite, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
        $stmt->execute(['Paul Collomb', 'Artiste français contemporain', 'Française']);
        $artiste_id = $pdo->lastInsertId();
        echo "<p style='color: green;'>✅ Artiste créé avec ID: $artiste_id</p>";
    } else {
        $artiste_id = $artiste['id_artiste'];
        echo "<p style='color: green;'>✅ Artiste 'Paul Collomb' trouvé avec ID: $artiste_id</p>";
    }
    
    // 2. Créer deux œuvres avec le même artiste
    $oeuvres = [
        [
            'titre' => 'Nature morte aux fruits',
            'description' => 'Une magnifique composition de fruits peinte avec une technique maîtrisée.',
            'image_url' => '/wp-content/themes/Mbaa_theme/asset/Img/tableaux/tableau-collomb-1.png',
            'dimensions' => '60 x 80 cm',
            'technique' => 'Huile sur toile',
            'date_creation' => '2020',
            'visible_galerie' => 1
        ],
        [
            'titre' => 'Paysage méditerranéen',
            'description' => 'Un paysage ensoleillé inspiré de la côte méditerranéenne.',
            'image_url' => '/wp-content/themes/Mbaa_theme/asset/Img/tableaux/tableau-collomb-2.webp',
            'dimensions' => '50 x 70 cm',
            'technique' => 'Huile sur toile',
            'date_creation' => '2021',
            'visible_galerie' => 1
        ]
    ];
    
    echo "<h2>Création des œuvres:</h2>";
    
    foreach ($oeuvres as $index => $oeuvre_data) {
        // Vérifier si l'œuvre existe déjà
        $stmt = $pdo->prepare("SELECT id_oeuvre FROM wp_mbaa_oeuvre WHERE titre = ?");
        $stmt->execute([$oeuvre_data['titre']]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existing) {
            echo "<p style='color: orange;'>L'œuvre '{$oeuvre_data['titre']}' existe déjà (ID: {$existing['id_oeuvre']})</p>";
            $oeuvre_id = $existing['id_oeuvre'];
            
            // Mettre à jour l'artiste si nécessaire
            $stmt = $pdo->prepare("UPDATE wp_mbaa_oeuvre SET id_artiste = ?, visible_galerie = 1 WHERE id_oeuvre = ?");
            $stmt->execute([$artiste_id, $oeuvre_id]);
            echo "<p style='color: blue;'>🔄 Œuvre mise à jour avec l'artiste $artiste_id</p>";
        } else {
            // Créer l'œuvre
            $stmt = $pdo->prepare("
                INSERT INTO wp_mbaa_oeuvre (
                    titre, description, image_url, dimensions, technique, 
                    date_creation, id_artiste, visible_galerie, visible_accueil,
                    created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, NOW(), NOW())
            ");
            
            $stmt->execute([
                $oeuvre_data['titre'],
                $oeuvre_data['description'],
                $oeuvre_data['image_url'],
                $oeuvre_data['dimensions'],
                $oeuvre_data['technique'],
                $oeuvre_data['date_creation'],
                $artiste_id,
                $oeuvre_data['visible_galerie']
            ]);
            
            $oeuvre_id = $pdo->lastInsertId();
            echo "<p style='color: green;'>✅ Œuvre '{$oeuvre_data['titre']}' créée avec ID: $oeuvre_id</p>";
        }
        
        // Créer un QR code pour cette œuvre
        $stmt = $pdo->prepare("SELECT id_qr FROM wp_mbaa_qr_codes WHERE id_oeuvre = ?");
        $stmt->execute([$oeuvre_id]);
        $qr_existing = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$qr_existing) {
            $qr_url = "http://localhost:8080/Mbaa/wordpress/collections/?oeuvre_id=$oeuvre_id";
            $stmt = $pdo->prepare("
                INSERT INTO wp_mbaa_qr_codes (
                    id_oeuvre, code_qr, url, type, statut, creation, mis_a_jour
                ) VALUES (?, ?, ?, ?, ?, NOW(), NOW())
            ");
            $stmt->execute([$oeuvre_id, "QR_" . $oeuvre_id . "_" . uniqid(), $qr_url, 'oeuvre', 'actif']);
            echo "<p style='color: blue;'>📱 QR code créé pour l'œuvre $oeuvre_id</p>";
        }
    }
    
    echo "<h2>Test de la recherche d'œuvres similaires:</h2>";
    
    // Tester la requête utilisée dans le template
    $test_oeuvre_id = $oeuvre_id; // Utiliser la dernière œuvre créée
    $stmt = $pdo->prepare("
        SELECT o.*, a.nom AS artiste_nom
        FROM wp_mbaa_oeuvre o
        LEFT JOIN wp_mbaa_artiste a ON o.id_artiste = a.id_artiste
        WHERE o.id_oeuvre != ? 
        AND o.visible_galerie = 1
        AND o.id_artiste = ?
        ORDER BY RAND()
        LIMIT 6
    ");
    $stmt->execute([$test_oeuvre_id, $artiste_id]);
    $similaires = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p><strong>Œuvre de référence: ID $test_oeuvre_id (Artiste ID: $artiste_id)</strong></p>";
    echo "<p><strong>Œuvres similaires trouvées: " . count($similaires) . "</strong></p>";
    
    foreach ($similaires as $sim) {
        echo "<p>📎 Œuvre ID: {$sim['id_oeuvre']} | Titre: {$sim['titre']} | Artiste: {$sim['artiste_nom']}</p>";
    }
    
    echo "<h2>Liens pour tester:</h2>";
    echo "<p><a href='http://localhost:8080/Mbaa/wordpress/collections/?oeuvre_id=$test_oeuvre_id' target='_blank'>🔗 Voir l'œuvre $test_oeuvre_id</a></p>";
    
    // Trouver l'autre œuvre
    $other_oeuvre = null;
    foreach ($similaires as $sim) {
        $other_oeuvre = $sim;
        break;
    }
    
    if ($other_oeuvre) {
        echo "<p><a href='http://localhost:8080/Mbaa/wordpress/collections/?oeuvre_id={$other_oeuvre['id_oeuvre']}' target='_blank'>🔗 Voir l'œuvre {$other_oeuvre['id_oeuvre']} ({$other_oeuvre['titre']})</a></p>";
    }
    
    echo "<h2 style='color: green;'>✅ Terminé !</h2>";
    echo "<p>Les œuvres ont été créées avec le même artiste. Vous pouvez maintenant tester les liens ci-dessus.</p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Erreur de base de données: " . $e->getMessage() . "</p>";
}
?>
