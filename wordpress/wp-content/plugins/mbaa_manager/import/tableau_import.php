<?php
/**
 * Tableau de bord pour gérer les importations depuis le CSV
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

// Traitement des actions
if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'import_artistes':
            import_artistes();
            break;
        case 'import_oeuvres':
            import_oeuvres();
            break;
        case 'vider_tables':
            vider_tables();
            break;
    }
}

function import_artistes() {
    global $wpdb;
    $db = new MBAA_Database();
    
    $csv_file = __DIR__ . '/base-joconde-extrait.csv';
    if (!file_exists($csv_file)) {
        echo "<div class='error'>Fichier CSV non trouvé</div>";
        return;
    }
    
    $handle = fopen($csv_file, 'r');
    $headers = fgetcsv($handle, ';');
    $artistes_importes = 0;
    $artistes_en_double = array();
    
    while (($row = fgetcsv($handle, ';')) !== FALSE) {
        if (count($row) < 5) continue;
        
        $auteur = trim($row[4]); // Colonne Auteur
        if (empty($auteur)) continue;
        
        $noms = explode(';', $auteur);
        foreach ($noms as $nom) {
            $nom = trim(preg_replace('/^["\']|["\']$/', '', $nom));
            if (empty($nom) || in_array(strtolower($nom), $artistes_en_double)) continue;
            
            $exists = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$db->table_artiste} WHERE LOWER(nom) = %s",
                strtolower($nom)
            ));
            
            if (!$exists) {
                $wpdb->insert(
                    $db->table_artiste,
                    array('nom' => $nom, 'biographie' => '', 'date_creation' => current_time('mysql')),
                    array('%s', '%s', '%s')
                );
                $artistes_importes++;
                $artistes_en_double[] = strtolower($nom);
            }
        }
    }
    fclose($handle);
    
    echo "<div class='success'>✅ $artistes_importes artistes importés avec succès</div>";
}

function import_oeuvres() {
    global $wpdb;
    $db = new MBAA_Database();
    
    $csv_file = __DIR__ . '/base-joconde-extrait.csv';
    if (!file_exists($csv_file)) {
        echo "<div class='error'>Fichier CSV non trouvé</div>";
        return;
    }
    
    $handle = fopen($csv_file, 'r');
    $headers = fgetcsv($handle, ';');
    $oeuvres_importees = 0;
    
    while (($row = fgetcsv($handle, ';')) !== FALSE) {
        if (count($row) < 10) continue;
        
        $reference = trim($row[0]);
        $titre = trim($row[35]); // Colonne Titre
        $auteur = trim($row[4]); // Colonne Auteur
        $description = trim($row[12]); // Colonne Description
        $date_creation = trim($row[16]); // Colonne Date_creation
        $technique = trim($row[47]); // Colonne Matériaux_techniques
        $dimensions = trim($row[13]); // Colonne Mesures
        
        if (empty($reference) || empty($titre)) continue;
        
        // Récupérer ou créer l'artiste
        $artiste_id = get_or_create_artiste($auteur);
        
        // Vérifier si l'œuvre existe déjà
        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$db->table_oeuvre} WHERE reference = %s",
            $reference
        ));
        
        if (!$exists) {
            $wpdb->insert(
                $db->table_oeuvre,
                array(
                    'reference' => $reference,
                    'titre' => $titre,
                    'id_artiste' => $artiste_id,
                    'description' => $description,
                    'date_creation' => $date_creation,
                    'technique' => $technique,
                    'dimensions' => $dimensions,
                    'visible_galerie' => 1,
                    'date_creation' => current_time('mysql')
                ),
                array('%s', '%s', '%d', '%s', '%s', '%s', '%s', '%d', '%s')
            );
            $oeuvres_importees++;
        }
    }
    fclose($handle);
    
    echo "<div class='success'>✅ $oeuvres_importees œuvres importées avec succès</div>";
}

function get_or_create_artiste($auteur_nom) {
    global $wpdb;
    $db = new MBAA_Database();
    
    $auteur_nom = trim(preg_replace('/^["\']|["\']$/', '', $auteur_nom));
    if (empty($auteur_nom)) return null;
    
    // Chercher le premier artiste si plusieurs
    $noms = explode(';', $auteur_nom);
    $premier_nom = trim($noms[0]);
    
    $artiste_id = $wpdb->get_var($wpdb->prepare(
        "SELECT id_artiste FROM {$db->table_artiste} WHERE LOWER(nom) = %s LIMIT 1",
        strtolower($premier_nom)
    ));
    
    if (!$artiste_id) {
        $wpdb->insert(
            $db->table_artiste,
            array('nom' => $premier_nom, 'biographie' => '', 'date_creation' => current_time('mysql')),
            array('%s', '%s', '%s')
        );
        $artiste_id = $wpdb->insert_id;
    }
    
    return $artiste_id;
}

function vider_tables() {
    global $wpdb;
    $db = new MBAA_Database();
    
    $wpdb->query("TRUNCATE TABLE {$db->table_artiste}");
    $wpdb->query("TRUNCATE TABLE {$db->table_oeuvre}");
    
    echo "<div class='success'>✅ Tables vidées avec succès</div>";
}

// Récupérer les statistiques
$stats_artistes = $wpdb->get_var("SELECT COUNT(*) FROM {$db->table_artiste}");
$stats_oeuvres = $wpdb->get_var("SELECT COUNT(*) FROM {$db->table_oeuvre}");

// Récupérer les premiers artistes pour aperçu
$artistes = $wpdb->get_results("SELECT id_artiste, nom, date_creation FROM {$db->table_artiste} ORDER BY nom ASC LIMIT 10");
$oeuvres = $wpdb->get_results("SELECT id_oeuvre, titre, id_artiste, date_creation FROM {$db->table_oeuvre} ORDER BY date_creation DESC LIMIT 10");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tableau de bord d'importation MBAA</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { text-align: center; margin-bottom: 30px; }
        .stats { display: flex; justify-content: space-around; margin-bottom: 30px; }
        .stat-box { background: #2B6CA3; color: white; padding: 20px; border-radius: 8px; text-align: center; flex: 1; margin: 0 10px; }
        .stat-number { font-size: 2em; font-weight: bold; }
        .stat-label { margin-top: 5px; }
        .actions { margin-bottom: 30px; }
        .btn { background: #2B6CA3; color: white; padding: 12px 24px; border: none; border-radius: 4px; cursor: pointer; margin: 5px; text-decoration: none; display: inline-block; }
        .btn:hover { background: #1e4d7a; }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
        .section { margin-bottom: 30px; }
        .section h2 { color: #2B6CA3; border-bottom: 2px solid #2B6CA3; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; font-weight: bold; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎨 Tableau de bord d'importation MBAA</h1>
            <p>Gérez l'importation des données depuis votre fichier CSV</p>
        </div>

        <div class="stats">
            <div class="stat-box">
                <div class="stat-number"><?php echo $stats_artistes; ?></div>
                <div class="stat-label">Artistes</div>
            </div>
            <div class="stat-box">
                <div class="stat-number"><?php echo $stats_oeuvres; ?></div>
                <div class="stat-label">Œuvres</div>
            </div>
        </div>

        <div class="section">
            <h2>📥 Actions d'importation</h2>
            <div class="actions">
                <form method="post">
                    <input type="hidden" name="action" value="import_artistes">
                    <button type="submit" class="btn">🎨 Importer les artistes</button>
                </form>
                <form method="post">
                    <input type="hidden" name="action" value="import_oeuvres">
                    <button type="submit" class="btn">🖼️ Importer les œuvres</button>
                </form>
                <form method="post" onsubmit="return confirm('Êtes-vous sûr de vouloir vider toutes les tables ?');">
                    <input type="hidden" name="action" value="vider_tables">
                    <button type="submit" class="btn btn-danger">🗑️ Vider les tables</button>
                </form>
                <a href="<?php echo admin_url('admin.php?page=mbaa-artistes'); ?>" class="btn">👥 Gérer les artistes</a>
                <a href="<?php echo admin_url('admin.php?page=mbaa-oeuvres'); ?>" class="btn">🖼️ Gérer les œuvres</a>
            </div>
        </div>

        <div class="section">
            <h2>🎨 Derniers artistes importés</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Date d'ajout</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($artistes as $artiste): ?>
                    <tr>
                        <td><?php echo $artiste->id_artiste; ?></td>
                        <td><?php echo esc_html($artiste->nom); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($artiste->date_creation)); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="section">
            <h2>🖼️ Dernières œuvres importées</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Titre</th>
                        <th>ID Artiste</th>
                        <th>Date d'ajout</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($oeuvres as $oeuvre): ?>
                    <tr>
                        <td><?php echo $oeuvre->id_oeuvre; ?></td>
                        <td><?php echo esc_html($oeuvre->titre); ?></td>
                        <td><?php echo $oeuvre->id_artiste; ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($oeuvre->date_creation)); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
