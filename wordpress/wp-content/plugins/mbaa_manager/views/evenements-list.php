<div class="wrap">
    <h1 class="wp-heading-inline">Événements</h1>
    <a href="<?php echo admin_url('admin.php?page=mbaa-evenements&action=add'); ?>" class="page-title-action">
        Créer un événement
    </a>
    <hr class="wp-header-end">
    
    <?php
    if (isset($_GET['message'])):
        $message = '';
        switch ($_GET['message']) {
            case 'added': $message = 'Événement créé avec succès.'; break;
            case 'updated': $message = 'Événement modifié avec succès.'; break;
            case 'deleted': $message = 'Événement supprimé avec succès.'; break;
        }
        if ($message):
    ?>
    <div class="notice notice-success is-dismissible">
        <p><?php echo esc_html($message); ?></p>
    </div>
    <?php endif; endif; ?>
    
    <?php if (empty($evenements)): ?>
        <p>Aucun événement trouvé. <a href="<?php echo admin_url('admin.php?page=mbaa-evenements&action=add'); ?>">Créez votre premier événement</a>.</p>
    <?php else: ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th scope="col" class="column-primary">Titre</th>
                    <th scope="col">Type</th>
                    <th scope="col">Date</th>
                    <th scope="col">Heure</th>
                    <th scope="col">Tarif</th>
                    <th scope="col">Capacité</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $today = date('Y-m-d');
                foreach ($evenements as $evenement): 
                    $is_past = $evenement->date_evenement < $today;
                ?>
                <tr<?php echo $is_past ? ' style="opacity: 0.6;"' : ''; ?>>
                    <td class="column-primary" data-colname="Titre">
                        <strong>
                            <a href="<?php echo admin_url('admin.php?page=mbaa-evenements&action=edit&id=' . $evenement->id_evenement); ?>">
                                <?php echo esc_html($evenement->titre); ?>
                            </a>
                        </strong>
                        <?php if ($is_past): ?>
                            <span class="post-state">(passé)</span>
                        <?php endif; ?>
                        <div class="row-actions">
                            <span class="edit">
                                <a href="<?php echo admin_url('admin.php?page=mbaa-evenements&action=edit&id=' . $evenement->id_evenement); ?>">
                                    Modifier
                                </a> |
                            </span>
                            <span class="trash">
                                <a href="<?php echo wp_nonce_url(
                                    admin_url('admin.php?page=mbaa-evenements&action=delete&id=' . $evenement->id_evenement),
                                    'delete_evenement_' . $evenement->id_evenement
                                ); ?>" 
                                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet événement ?');"
                                   class="submitdelete">
                                    Supprimer
                                </a>
                            </span>
                        </div>
                    </td>
                    <td data-colname="Type"><?php echo esc_html($evenement->nom_type ?: '-'); ?></td>
                    <td data-colname="Date"><?php echo date_i18n('d/m/Y', strtotime($evenement->date_evenement)); ?></td>
                    <td data-colname="Heure">
                        <?php 
                        if ($evenement->heure_debut) {
                            echo esc_html(substr($evenement->heure_debut, 0, 5));
                            if ($evenement->heure_fin) {
                                echo ' - ' . esc_html(substr($evenement->heure_fin, 0, 5));
                            }
                        } else {
                            echo '-';
                        }
                        ?>
                    </td>
                    <td data-colname="Tarif">
                        <?php 
                        if ($evenement->est_gratuit) {
                            echo 'Gratuit';
                        } else if ($evenement->prix) {
                            echo number_format($evenement->prix, 2, ',', ' ') . ' €';
                        } else {
                            echo '-';
                        }
                        ?>
                    </td>
                    <td data-colname="Capacité"><?php echo $evenement->capacite_max ? esc_html($evenement->capacite_max) : '-'; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
