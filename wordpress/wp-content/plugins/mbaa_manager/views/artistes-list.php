<div class="wrap mbaa-wrap">
    <div class="mbaa-main-content">
        <div class="mbaa-artwork-header">
            <div class="mbaa-artwork-icon">
                <span class="dashicons dashicons-admin-users" style="font-size:40px;height:40px;"></span>
            </div>
            <div class="mbaa-artwork-title-section">
                <h1 class="mbaa-artwork-title">Artistes</h1>
                <span class="mbaa-status-badge">Liste</span>
            </div>
            <div class="mbaa-action-buttons">
                <a href="<?php echo admin_url('admin.php?page=mbaa-artistes&action=add'); ?>" class="mbaa-action-button" title="Ajouter">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            </div>
        </div>
    
    <?php
    //on veut afficher un message pour chaque ajout/ modification ou suppression

    if (isset($_GET['message'])):
        $message = '';
        $type = 'success';
        
        switch ($_GET['message']) {
            case 'added':
                $message = 'Artiste ajouté avec succès.';
                break;
            case 'updated':
                $message = 'Artiste modifié avec succès.';
                break;
            case 'deleted':
                $message = 'Artiste supprimé avec succès.';
                break;
        }
        
        if ($message):
    ?>

    <!-- on implemente le message dans le code html -->
    <div class="notice notice-<?php echo esc_attr($type); ?> is-dismissible">
        <p><?php echo esc_html($message); ?></p>
    </div>
    <?php endif; endif; ?>

    <!-- si aucun artiste trouver alors, la proposition d'ajouter un artiste -->
    
    <?php if (empty($artistes)): ?>
        <p>Aucun artiste trouvé. <a href="<?php echo admin_url('admin.php?page=mbaa-artistes&action=add'); ?>">Ajoutez votre premier artiste</a>.</p>
    <?php else: ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th scope="col">Image</th>
                    <th scope="col" class="column-primary">Nom</th>
                    <th scope="col">Nationalité</th>
                    <th scope="col">Dates</th>
                    <th scope="col">Style artistique</th>
                    <th scope="col">Œuvres</th>
                    <th scope="col">Date de création</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $artiste_manager = new MBAA_Artiste();
                foreach ($artistes as $artiste): 
                    $nb_oeuvres = $artiste_manager->count_oeuvres($artiste->id_artiste);
                    $dates = '';
                    if ($artiste->date_naissance) {
                        $dates = date('Y', strtotime($artiste->date_naissance));
                        if ($artiste->date_deces) {
                            $dates .= ' - ' . date('Y', strtotime($artiste->date_deces));
                        }
                    }
                ?>
                <tr>
                    <td data-colname="Image">
                        <?php if (!empty($artiste->image_url)): ?>
                            <img src="<?php echo esc_url($artiste->image_url); ?>" alt="" style="width:44px;height:44px;object-fit:cover;border-radius:8px;display:block;" />
                        <?php else: ?>
                            <span style="display:block;width:44px;height:44px;border-radius:8px;background:#f1f5f9;"></span>
                        <?php endif; ?>
                    </td>
                    <td class="column-primary" data-colname="Nom">
                        <strong>
                            <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=mbaa-artistes&action=edit&id=' . $artiste->id_artiste), 'edit_artiste_' . $artiste->id_artiste); ?>">
                                <?php echo esc_html($artiste->nom); ?>
                            </a>
                        </strong>
                        <div class="row-actions">
                            <span class="edit">
                                <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=mbaa-artistes&action=edit&id=' . $artiste->id_artiste), 'edit_artiste_' . $artiste->id_artiste); ?>">
                                    Modifier
                                </a> |
                            </span>
                            <span class="pdf">
                                <a href="<?php echo admin_url('admin-post.php?action=mbaa_generate_artiste_pdf&artiste_id=' . $artiste->id_artiste); ?>" 
                                   target="_blank"
                                   title="Générer le PDF de cet artiste">
                                    📄 PDF
                                </a> |
                            </span>
                            <span class="trash">
                                <a href="<?php echo wp_nonce_url(
                                    admin_url('admin.php?page=mbaa-artistes&action=delete&id=' . $artiste->id_artiste),
                                    'delete_artiste_' . $artiste->id_artiste
                                ); ?>" 
                                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet artiste ?');"
                                   class="submitdelete">
                                    Supprimer
                                </a>
                            </span>
                        </div>
                    </td>
                    <td data-colname="Nationalité"><?php echo esc_html($artiste->nationalite ?: '-'); ?></td>
                    <td data-colname="Dates"><?php echo esc_html($dates ?: '-'); ?></td>
                    <td data-colname="Style artistique">-</td> <!-- Champ non disponible -->
                    <td data-colname="Œuvres">
                        <?php if ($nb_oeuvres > 0): ?>
                            <a href="<?php echo admin_url('admin.php?page=mbaa-oeuvres&id_artiste=' . $artiste->id_artiste); ?>">
                                <?php echo esc_html($nb_oeuvres); ?> œuvre<?php echo $nb_oeuvres > 1 ? 's' : ''; ?>
                            </a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td data-colname="Date de création">
                        <?php echo date_i18n('d/m/Y H:i', strtotime($artiste->created_at)); ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    </div>
</div>
