<?php
$db = new MBAA_Database();
global $wpdb;

// Récupérer les données
$epoques = $wpdb->get_results("SELECT * FROM {$db->table_epoque} ORDER BY nom_epoque ASC");
$salles = $wpdb->get_results("SELECT * FROM {$db->table_salle} ORDER BY nom_salle ASC");
$mediums = $wpdb->get_results("SELECT * FROM {$db->table_medium} ORDER BY nom_medium ASC");
$mouvements = $wpdb->get_results("SELECT * FROM {$db->table_mouvement} ORDER BY nom_mouvement ASC");
$categories = $wpdb->get_results("SELECT * FROM {$db->table_categorie} ORDER BY nom_categorie ASC");
$types_evenement = $wpdb->get_results("SELECT * FROM {$db->table_type_evenement} ORDER BY nom_type ASC");
?>

<div class="wrap">
    <h1>Paramètres</h1>
    <p>Gérez les données de référence utilisées dans le plugin (époques, salles, mouvements, etc.).</p>
    
    <div class="mbaa-settings-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-top: 20px;">
        
        <!-- Époques -->
        <div class="mbaa-settings-box">
            <h2>Époques (<?php echo count($epoques); ?>)</h2>
            <table class="widefat">
                <thead>
                    <tr>
                        <th>Nom</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($epoques)): ?>
                        <tr><td colspan="1">Aucune époque trouvée</td></tr>
                    <?php else: ?>
                        <?php foreach ($epoques as $epoque): ?>
                            <tr>
                                <td><?php echo esc_html($epoque->nom_epoque); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Salles -->
        <div class="mbaa-settings-box">
            <h2>Salles (<?php echo count($salles); ?>)</h2>
            <table class="widefat">
                <thead>
                    <tr>
                        <th>Nom</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($salles)): ?>
                        <tr><td colspan="1">Aucune salle trouvée</td></tr>
                    <?php else: ?>
                        <?php foreach ($salles as $salle): ?>
                            <tr>
                                <td><?php echo esc_html($salle->nom_salle); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Mediums -->
        <div class="mbaa-settings-box">
            <h2>Mediums (<?php echo count($mediums); ?>)</h2>
            <table class="widefat">
                <thead>
                    <tr>
                        <th>Nom</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($mediums)): ?>
                        <tr><td colspan="1">Aucun medium trouvé</td></tr>
                    <?php else: ?>
                        <?php foreach ($mediums as $medium): ?>
                            <tr>
                                <td><?php echo esc_html($medium->nom_medium); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Mouvements -->
        <div class="mbaa-settings-box">
            <h2>Mouvements (<?php echo count($mouvements); ?>)</h2>
            <table class="widefat">
                <thead>
                    <tr>
                        <th>Nom</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($mouvements)): ?>
                        <tr><td colspan="1">Aucun mouvement trouvé</td></tr>
                    <?php else: ?>
                        <?php foreach ($mouvements as $mouvement): ?>
                            <tr>
                                <td><?php echo esc_html($mouvement->nom_mouvement); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Catégories -->
        <div class="mbaa-settings-box">
            <h2>Catégories (<?php echo count($categories); ?>)</h2>
            <table class="widefat">
                <thead>
                    <tr>
                        <th>Nom</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($categories)): ?>
                        <tr><td colspan="1">Aucune catégorie trouvée</td></tr>
                    <?php else: ?>
                        <?php foreach ($categories as $categorie): ?>
                            <tr>
                                <td><?php echo esc_html($categorie->nom_categorie); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Types d'événements -->
        <div class="mbaa-settings-box">
            <h2>Types d'événements (<?php echo count($types_evenement); ?>)</h2>
            <table class="widefat">
                <thead>
                    <tr>
                        <th>Nom</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($types_evenement)): ?>
                        <tr><td colspan="1">Aucun type trouvé</td></tr>
                    <?php else: ?>
                        <?php foreach ($types_evenement as $type): ?>
                            <tr>
                                <td><?php echo esc_html($type->nom_type); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
    </div>
    
    <div style="margin-top: 30px; padding: 20px; background: #fff; border: 1px solid #ccd0d4; border-radius: 4px;">
        <h2>Actions de maintenance</h2>
        <p>Ces actions permettent de gérer la base de données du plugin.</p>
        
        <p>
            <strong>Note :</strong> Les données de référence (époques, salles, etc.) sont créées automatiquement lors de l'activation du plugin.
            Pour ajouter ou modifier ces données, vous pouvez utiliser phpMyAdmin ou un outil similaire.
        </p>
        
        <p>
            <strong>Version de la base de données :</strong> <?php echo get_option('mbaa_db_version', '1.0'); ?>
        </p>
    </div>
</div>
