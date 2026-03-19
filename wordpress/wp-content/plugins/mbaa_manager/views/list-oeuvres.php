<?php
/**
 * Vue liste des œuvres en grille de cards
 */

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;
$oeuvres = $wpdb->get_results("
    SELECT o.*, a.nom as artiste_nom, s.nom_salle as salle_nom
    FROM {$wpdb->prefix}mbaa_oeuvre o
    LEFT JOIN {$wpdb->prefix}mbaa_artiste a ON o.id_artiste = a.id_artiste
    LEFT JOIN {$wpdb->prefix}mbaa_salle s ON o.id_salle = s.id_salle
    ORDER BY o.titre ASC
");
?>

<div class="wrap">
    <h1 class="wp-heading-inline">Œuvres</h1>
    <a href="<?php echo esc_url(admin_url('admin.php?page=mbaa-oeuvres&action=add')); ?>" class="page-title-action">
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
        <div class="mbaa-empty-state">
            <p>Aucune œuvre trouvée. <a href="<?php echo esc_url(admin_url('admin.php?page=mbaa-oeuvres&action=add')); ?>">Ajoutez votre première œuvre</a>.</p>
        </div>
    <?php else: ?>
        
        <!-- Barre de recherche et filtres -->
        <div class="mbaa-controls">
            <div class="mbaa-search">
                <input type="text" id="mbaa-search-input" placeholder="Rechercher une œuvre..." class="regular-text">
            </div>
            <div class="mbaa-filters">
                <button class="button mbaa-filter active" data-filter="all">Tous</button>
                <button class="button mbaa-filter" data-filter="published">Publiés</button>
                <button class="button mbaa-filter" data-filter="draft">Non publiés</button>
            </div>
        </div>
        
        <!-- Grille de cards -->
        <div class="mbaa-oeuvres-grid" id="mbaa-oeuvres-grid">
            <?php foreach ($oeuvres as $oeuvre): ?>
                <?php
                $is_published = $oeuvre->visible_galerie;
                $description_short = substr(strip_tags($oeuvre->description), 0, 100);
                if (strlen($description_short) >= 100) {
                    $description_short .= '...';
                }
                $edit_url = admin_url('admin.php?page=mbaa-oeuvres&action=edit&id=' . $oeuvre->id_oeuvre);
                $delete_url = wp_nonce_url(admin_url('admin-post.php?action=mbaa_delete_oeuvre&id=' . $oeuvre->id_oeuvre), 'mbaa_delete_oeuvre_' . $oeuvre->id_oeuvre);
                ?>
                <div class="mbaa-oeuvre-card" data-id="<?php echo esc_attr($oeuvre->id_oeuvre); ?>" data-status="<?php echo $is_published ? 'published' : 'draft'; ?>" data-title="<?php echo esc_attr(strtolower($oeuvre->titre)); ?>">
                    
                    <!-- Image -->
                    <div class="mbaa-card-image">
                        <?php if ($oeuvre->image_url): ?>
                            <img src="<?php echo esc_url($oeuvre->image_url); ?>" alt="<?php echo esc_attr($oeuvre->titre); ?>" loading="lazy">
                        <?php else: ?>
                            <div class="mbaa-no-image">
                                <span class="dashicons dashicons-image"></span>
                                <p>Pas d'image</p>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Badge de statut -->
                        <div class="mbaa-status-badge <?php echo $is_published ? 'published' : 'draft'; ?>">
                            <?php echo $is_published ? 'Publié' : 'Brouillon'; ?>
                        </div>
                    </div>
                    
                    <!-- Contenu -->
                    <div class="mbaa-card-content">
                        <h3 class="mbaa-card-title"><?php echo esc_html($oeuvre->titre); ?></h3>
                        
                        <div class="mbaa-card-meta">
                            <?php if ($oeuvre->artiste_nom): ?>
                                <span class="mbaa-artist">
                                    <span class="dashicons dashicons-art"></span>
                                    <?php echo esc_html($oeuvre->artiste_nom); ?>
                                </span>
                            <?php endif; ?>
                            
                            <?php if ($oeuvre->date_creation): ?>
                                <span class="mbaa-date">
                                    <span class="dashicons dashicons-calendar"></span>
                                    <?php echo esc_html(date('Y', strtotime($oeuvre->date_creation))); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($oeuvre->description): ?>
                            <div class="mbaa-card-description">
                                <?php echo esc_html($description_short); ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="mbaa-card-locations">
                            <?php if ($oeuvre->salle_nom): ?>
                                <span class="mbaa-location">Salle: <?php echo esc_html($oeuvre->salle_nom); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="mbaa-card-actions">
                        <button type="button" class="button button-primary bg-or mbaa-toggle-publish" data-id="<?php echo esc_attr($oeuvre->id_oeuvre); ?>" data-current="<?php echo $is_published ? '1' : '0'; ?>">
                            <?php echo $is_published ? 'Dépublier' : 'Publier'; ?>
                        </button>
                        
                        <a href="<?php echo esc_url($edit_url); ?>" class="button">
                            <span class="dashicons dashicons-edit"></span>
                            Modifier
                        </a>
                        
                        <button type="button" class="button mbaa-delete-oeuvre" data-id="<?php echo esc_attr($oeuvre->id_oeuvre); ?>" data-title="<?php echo esc_attr($oeuvre->titre); ?>" data-delete-url="<?php echo esc_url($delete_url); ?>">
                            <span class="dashicons dashicons-trash"></span>
                            Supprimer
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
    <?php endif; ?>
</div>

<!-- Script de confirmation de suppression -->
<script>
jQuery(document).ready(function($) {
    // Recherche en temps réel
    $('#mbaa-search-input').on('input', function() {
        var searchTerm = $(this).val().toLowerCase();
        $('.mbaa-oeuvre-card').each(function() {
            var title = $(this).data('title');
            if (title.includes(searchTerm)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
    
    // Filtres par statut
    $('.mbaa-filter').on('click', function() {
        $('.mbaa-filter').removeClass('active');
        $(this).addClass('active');
        
        var filter = $(this).data('filter');
        $('.mbaa-oeuvre-card').each(function() {
            if (filter === 'all') {
                $(this).show();
            } else {
                var status = $(this).data('status');
                $(this).toggle(status === filter);
            }
        });
    });
    
    // Toggle publication
    $('.mbaa-toggle-publish').on('click', function() {
        var $button = $(this);
        var oeuvreId = $button.data('id');
        var currentStatus = $button.data('current');
        var newStatus = currentStatus === '1' ? '0' : '1';
        
        $button.prop('disabled', true);
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'mbaa_toggle_visible',
                oeuvre_id: oeuvreId,
                visible: newStatus,
                nonce: '<?php echo wp_create_nonce('mbaa_toggle_visible_nonce'); ?>'
            },
            success: function(response) {
                if (response.success) {
                    $button.data('current', newStatus);
                    var $card = $button.closest('.mbaa-oeuvre-card');
                    var $badge = $card.find('.mbaa-status-badge');
                    
                    if (newStatus === '1') {
                        $button.text('Dépublier').removeClass('button-primary').addClass('button-primary');
                        $badge.removeClass('draft').addClass('published').text('Publié');
                        $card.data('status', 'published');
                    } else {
                        $button.text('Publier').removeClass('button-primary').addClass('button-secondary');
                        $badge.removeClass('published').addClass('draft').text('Brouillon');
                        $card.data('status', 'draft');
                    }
                } else {
                    alert('Erreur: ' + response.data);
                }
            },
            error: function() {
                alert('Erreur lors de la mise à jour du statut.');
            },
            complete: function() {
                $button.prop('disabled', false);
            }
        });
    });
    
    // Suppression avec confirmation
    $('.mbaa-delete-oeuvre').on('click', function() {
        var $button = $(this);
        var oeuvreId = $button.data('id');
        var oeuvreTitle = $button.data('title');
        var deleteUrl = $button.data('delete-url');
        
        if (confirm('Êtes-vous sûr de vouloir supprimer l\'œuvre "' + oeuvreTitle + '" ? Cette action est irréversible.')) {
            if (deleteUrl) {
                window.location.href = deleteUrl;
            }
        }
    });
});
</script>
