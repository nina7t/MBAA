<div class="wrap">
    <h1 class="wp-heading-inline">Œuvres</h1>
    <a href="<?php echo admin_url('admin.php?page=mbaa-oeuvres&action=add'); ?>" class="page-title-action">
        Ajouter une œuvre
    </a>
    <hr class="wp-header-end">
    
    <?php
    // Afficher les messages
    if (isset($_GET['message'])):
        $message = '';
        switch ($_GET['message']) {
            case 'added': $message = 'Œuvre ajoutée avec succès.'; break;
            case 'updated': $message = 'Œuvre modifiée avec succès.'; break;
            case 'deleted': $message = 'Œuvre supprimée avec succès.'; break;
        }
        if ($message):
    ?>
    <div class="notice notice-success is-dismissible">
        <p><?php echo esc_html($message); ?></p>
    </div>
    <?php endif; endif; ?>
    
    <?php if (empty($oeuvres)): ?>
        <p>Aucune œuvre trouvée. <a href="<?php echo admin_url('admin.php?page=mbaa-oeuvres&action=add'); ?>">Ajoutez votre première œuvre</a>.</p>
    <?php else: ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th scope="col">Image</th>
                    <th scope="col" class="column-primary">Titre</th>
                    <th scope="col">Artiste</th>
                    <th scope="col">Date</th>
                    <th scope="col">Salle</th>
                    <th scope="col">Galerie</th>
                    <th scope="col">Accueil</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($oeuvres as $oeuvre): ?>
                <tr>
                    <td>
                        <?php if ($oeuvre->image_url): ?>
                            <img src="<?php echo esc_url($oeuvre->image_url); ?>" 
                                 style="width: 50px; height: 50px; object-fit: cover;">
                        <?php else: ?>
                            <span class="dashicons dashicons-format-image" style="font-size: 50px; color: #ddd;"></span>
                        <?php endif; ?>
                    </td>
                    <td class="column-primary" data-colname="Titre">
                        <strong>
                            <a href="<?php echo admin_url('admin.php?page=mbaa-oeuvres&action=edit&id=' . $oeuvre->id_oeuvre); ?>">
                                <?php echo esc_html($oeuvre->titre); ?>
                            </a>
                        </strong>
                        <div class="row-actions">
                            <span class="edit">
                                <a href="<?php echo admin_url('admin.php?page=mbaa-oeuvres&action=edit&id=' . $oeuvre->id_oeuvre); ?>">
                                    Modifier
                                </a> |
                            </span>
                            <span class="pdf">
                                <a href="<?php echo admin_url('admin-post.php?action=mbaa_generate_oeuvre_pdf&oeuvre_id=' . $oeuvre->id_oeuvre); ?>" 
                                   target="_blank"
                                   title="Générer le PDF de cette œuvre">
                                    📄 PDF
                                </a> |
                            </span>
                            <span class="trash">
                                <a href="<?php echo wp_nonce_url(
                                    admin_url('admin.php?page=mbaa-oeuvres&action=delete&id=' . $oeuvre->id_oeuvre),
                                    'delete_oeuvre_' . $oeuvre->id_oeuvre
                                ); ?>" 
                                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette œuvre ?');"
                                   class="submitdelete">
                                    Supprimer
                                </a>
                            </span>
                        </div>
                    </td>
                    <td data-colname="Artiste">
                        <?php if ($oeuvre->artiste_nom): ?>
                            <a href="<?php echo admin_url('admin.php?page=mbaa-artistes&action=edit&id=' . $oeuvre->id_artiste); ?>">
                                <?php echo esc_html($oeuvre->artiste_nom); ?>
                            </a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td data-colname="Date"><?php echo esc_html($oeuvre->date_creation ?: '-'); ?></td>
                    <td data-colname="Salle"><?php echo esc_html($oeuvre->nom_salle ?: '-'); ?></td>
                    <td data-colname="Galerie">
                        <span class="dashicons dashicons-<?php echo $oeuvre->visible_galerie ? 'yes' : 'no'; ?>" 
                              style="color: <?php echo $oeuvre->visible_galerie ? 'green' : 'red'; ?>;"></span>
                    </td>
                    <td data-colname="Accueil">
                        <span class="dashicons dashicons-<?php echo $oeuvre->visible_accueil ? 'yes' : 'no'; ?>" 
                              style="color: <?php echo $oeuvre->visible_accueil ? 'green' : 'red'; ?>;"></span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
