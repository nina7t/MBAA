<?php
/**
 * Tableau de bord principal du plugin MBAA
 * Remplace le tableau de bord WordPress pour les utilisateurs du plugin
 */

// Récupérer les statistiques détaillées
global $wpdb;
$db = new MBAA_Database();

// Statistiques œuvres
$total_oeuvres = $wpdb->get_var("SELECT COUNT(*) FROM {$db->table_oeuvre}");
$oeuvres_semaine = $wpdb->get_var("SELECT COUNT(*) FROM {$db->table_oeuvre} WHERE creation >= DATE_SUB(NOW(), INTERVAL 7 DAY)");

// Statistiques images
$total_images = $wpdb->get_var("SELECT COUNT(*) FROM {$db->table_oeuvre} WHERE image_url != '' AND image_url IS NOT NULL");

// Calculer le poids total des images (estimation)
$images_result = $wpdb->get_results("SELECT image_url FROM {$db->table_oeuvre} WHERE image_url != '' AND image_url IS NOT NULL");
$total_size = 0;
foreach ($images_result as $img) {
    if (isset($img->image_url) && !empty($img->image_url)) {
        $total_size += 1; // Estimation 1MB par image pour l'exemple
    }
}
$total_size_formatted = size_format($total_size * 1024 * 1024, 2);

// Vues des œuvres (colonne vues si elle existe)
$vues_total = $wpdb->get_var("SELECT SUM(vues) FROM {$db->table_oeuvre} WHERE vues IS NOT NULL");
$vues_semaine = $wpdb->get_var("SELECT SUM(vues) FROM {$db->table_oeuvre} WHERE vues IS NOT NULL");

// Autres stats
$stats = array(
    'artistes' => $wpdb->get_var("SELECT COUNT(*) FROM {$db->table_artiste}"),
    'oeuvres' => $total_oeuvres,
    'evenements' => $wpdb->get_var("SELECT COUNT(*) FROM {$db->table_evenement}"),
    'audioguides' => $wpdb->get_var("SELECT COUNT(*) FROM {$db->table_audioguide}"),
    'expositions' => $wpdb->get_var("SELECT COUNT(*) FROM {$db->table_exposition}")
);

// Événements à venir
$evenements_a_venir = $wpdb->get_results(
    "SELECT * FROM {$db->table_evenement} 
    WHERE date_evenement >= CURDATE() 
    ORDER BY date_evenement ASC 
    LIMIT 5"
);
?>

<div class="wrap">
    <!-- Barre de statistiques en haut -->
    <div class="mbaa-stats-bar">
        <div class="mbaa-stats-item">
            <span class="mbaa-stats-label">Œuvres</span>
            <span class="mbaa-stats-value"><?php echo esc_html($total_oeuvres); ?></span>
        </div>
        <div class="mbaa-stats-item">
            <span class="mbaa-stats-label">Cette semaine</span>
            <span class="mbaa-stats-value">+<?php echo esc_html($oeuvres_semaine); ?></span>
        </div>
        <div class="mbaa-stats-item">
            <span class="mbaa-stats-label">Vues</span>
            <span class="mbaa-stats-value"><?php echo number_format($vues_total ?: 0, 0, ',', ' '); ?></span>
        </div>
        <div class="mbaa-stats-item">
            <span class="mbaa-stats-label">Images</span>
            <span class="mbaa-stats-value"><?php echo esc_html($total_images); ?></span>
        </div>
        <div class="mbaa-stats-item">
            <span class="mbaa-stats-label">Stockage</span>
            <span class="mbaa-stats-value"><?php echo esc_html($total_size_formatted); ?></span>
        </div>
    </div>

    <h1>Tableau de bord - Gestion Musée</h1>
    
    <div class="mbaa-dashboard">
        
        <!-- Statistiques principales -->
        <div class="mbaa-stats-grid">
            <div class="mbaa-stat-box">
                <div class="mbaa-stat-icon">
                    <span class="dashicons dashicons-admin-users"></span>
                </div>
                <div class="mbaa-stat-content">
                    <h3><?php echo esc_html($stats['artistes']); ?></h3>
                    <p>Artistes</p>
                </div>
                <a href="<?php echo admin_url('admin.php?page=mbaa-artistes'); ?>" class="mbaa-stat-link">
                    Voir tous →
                </a>
            </div>
            
            <div class="mbaa-stat-box">
                <div class="mbaa-stat-icon">
                    <span class="dashicons dashicons-art"></span>
                </div>
                <div class="mbaa-stat-content">
                    <h3><?php echo esc_html($stats['oeuvres']); ?></h3>
                    <p>Œuvres</p>
                </div>
                <a href="<?php echo admin_url('admin.php?page=mbaa-oeuvres'); ?>" class="mbaa-stat-link">
                    Voir toutes →
                </a>
            </div>
            
            <div class="mbaa-stat-box">
                <div class="mbaa-stat-icon">
                    <span class="dashicons dashicons-calendar"></span>
                </div>
                <div class="mbaa-stat-content">
                    <h3><?php echo esc_html($stats['evenements']); ?></h3>
                    <p>Événements</p>
                </div>
                <a href="<?php echo admin_url('admin.php?page=mbaa-evenements'); ?>" class="mbaa-stat-link">
                    Voir tous →
                </a>
            </div>
            
            <div class="mbaa-stat-box">
                <div class="mbaa-stat-icon">
                    <span class="dashicons dashicons-media-audio"></span>
                </div>
                <div class="mbaa-stat-content">
                    <h3><?php echo esc_html($stats['audioguides']); ?></h3>
                    <p>Audioguides</p>
                </div>
                <a href="<?php echo admin_url('admin.php?page=mbaa-audioguides'); ?>" class="mbaa-stat-link">
                    Voir tous →
                </a>
            </div>
        </div>
        
        <!-- Actions rapides -->
        <div class="mbaa-quick-actions">
            <h2>Actions rapides</h2>
            <div class="mbaa-actions-grid">
                <a href="<?php echo admin_url('admin.php?page=mbaa-artistes&action=add'); ?>" class="mbaa-action-btn">
                    <span class="dashicons dashicons-plus"></span>
                    Ajouter un artiste
                </a>
                <a href="<?php echo admin_url('admin.php?page=mbaa-oeuvres&action=add'); ?>" class="mbaa-action-btn">
                    <span class="dashicons dashicons-plus"></span>
                    Ajouter une œuvre
                </a>
                <a href="<?php echo admin_url('admin.php?page=mbaa-evenements&action=add'); ?>" class="mbaa-action-btn">
                    <span class="dashicons dashicons-plus"></span>
                    Créer un événement
                </a>
                <a href="<?php echo admin_url('admin.php?page=mbaa-collections'); ?>" class="mbaa-action-btn">
                    <span class="dashicons dashicons-images-alt2"></span>
                    Voir collections
                </a>
            </div>
        </div>
        
        <!-- Événements à venir -->
        <?php if (!empty($evenements_a_venir)): ?>
        <div class="mbaa-upcoming-events">
            <h2>Événements à venir</h2>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Date</th>
                        <th>Heure</th>
                        <th>Lieu</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($evenements_a_venir as $evenement): ?>
                    <tr>
                        <td><strong><?php echo esc_html($evenement->titre); ?></strong></td>
                        <td><?php echo date_i18n('d/m/Y', strtotime($evenement->date_evenement)); ?></td>
                        <td><?php echo esc_html($evenement->heure_debut); ?></td>
                        <td><?php echo esc_html($evenement->lieu_musee); ?></td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=mbaa-evenements&action=edit&id=' . $evenement->id_evenement); ?>">
                                Modifier
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
        
    </div>
</div>

