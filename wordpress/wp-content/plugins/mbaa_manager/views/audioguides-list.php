<div class="wrap">
    <h1 class="wp-heading-inline">Audioguides</h1>
    <a href="<?php echo admin_url('admin.php?page=mbaa-audioguides&action=add'); ?>" class="page-title-action">
        Ajouter un audioguide
    </a>
    <hr class="wp-header-end">
    
    <?php
    if (isset($_GET['message'])):
        $message = '';
        switch ($_GET['message']) {
            case 'added': $message = 'Audioguide ajouté avec succès.'; break;
            case 'updated': $message = 'Audioguide modifié avec succès.'; break;
            case 'deleted': $message = 'Audioguide supprimé avec succès.'; break;
        }
        if ($message):
    ?>
    <div class="notice notice-success is-dismissible">
        <p><?php echo esc_html($message); ?></p>
    </div>
    <?php endif; endif; ?>
    
    <?php if (empty($audioguides)): ?>
        <p>Aucun audioguide trouvé. <a href="<?php echo admin_url('admin.php?page=mbaa-audioguides&action=add'); ?>">Ajoutez votre premier audioguide</a>.</p>
    <?php else: ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th scope="col" class="column-primary">Œuvre</th>
                    <th scope="col">Artiste</th>
                    <th scope="col">Langue</th>
                    <th scope="col">Durée</th>
                    <th scope="col">Fichier audio</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $audioguide_manager = new MBAA_Audioguide();
                foreach ($audioguides as $audioguide): 
                ?>
                <tr>
                    <td class="column-primary" data-colname="Œuvre">
                        <strong>
                            <a href="<?php echo admin_url('admin.php?page=mbaa-audioguides&action=edit&id=' . $audioguide->id_audioguide); ?>">
                                <?php echo esc_html($audioguide->oeuvre_titre); ?>
                            </a>
                        </strong>
                        <div class="row-actions">
                            <span class="edit">
                                <a href="<?php echo admin_url('admin.php?page=mbaa-audioguides&action=edit&id=' . $audioguide->id_audioguide); ?>">
                                    Modifier
                                </a> |
                            </span>
                            <span class="view">
                                <a href="<?php echo admin_url('admin.php?page=mbaa-oeuvres&action=edit&id=' . $audioguide->id_oeuvre); ?>">
                                    Voir l'œuvre
                                </a> |
                            </span>
                            <span class="trash">
                                <a href="<?php echo wp_nonce_url(
                                    admin_url('admin.php?page=mbaa-audioguides&action=delete&id=' . $audioguide->id_audioguide),
                                    'delete_audioguide_' . $audioguide->id_audioguide
                                ); ?>" 
                                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet audioguide ?');"
                                   class="submitdelete">
                                    Supprimer
                                </a>
                            </span>
                        </div>
                    </td>
                    <td data-colname="Artiste"><?php echo esc_html($audioguide->artiste_nom ?: '-'); ?></td>
                    <td data-colname="Langue"><?php echo esc_html(strtoupper($audioguide->langue)); ?></td>
                    <td data-colname="Durée">
                        <?php echo $audioguide_manager->format_duree($audioguide->duree_secondes); ?>
                    </td>
                    <td data-colname="Fichier audio">
                        <?php if ($audioguide->fichier_audio_url): ?>
                            <audio controls style="max-width: 300px;">
                                <source src="<?php echo esc_url($audioguide->fichier_audio_url); ?>" type="audio/mpeg">
                                Votre navigateur ne supporte pas l'élément audio.
                            </audio>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
