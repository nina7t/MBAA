<?php
/**
 * Vue du formulaire d'artiste
 */
if (!defined('ABSPATH')) {
    exit;
}

$is_edit = isset($artiste) && !empty($artiste);
$page_title = $is_edit ? 'Modifier l\'artiste' : 'Ajouter un artiste';

// Récupérer le nom de l'utilisateur actuel
$current_user = wp_get_current_user();
$user_firstname = $current_user->display_name ? $current_user->display_name : $current_user->user_login;
?>

<div class="wrap mbaa-wrap">
    <!-- Lien de test pour débogage -->
    <div style="background: #ffe0e0; padding: 10px; margin-bottom: 20px; border-radius: 4px;">
        <strong>DEBUG:</strong> 
        <a href="<?php echo admin_url('admin-post.php?action=mbaa_test_artiste'); ?>" target="_blank">
            Tester la table artiste (ouvre dans nouvel onglet)
        </a>
    </div>
    
    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" id="mbaa-artiste-form" class="mbaa-form">
        <input type="hidden" name="action" value="mbaa_save_artiste">
        <?php wp_nonce_field('mbaa_save_artiste', 'mbaa_artiste_nonce'); ?>
        
        <?php if ($is_edit): ?>
            <input type="hidden" name="id_artiste" value="<?php echo esc_attr($artiste->id_artiste); ?>">
        <?php endif; ?>

        <div class="mbaa-main-content">
            <!-- Artist Header -->
            <div class="mbaa-artwork-header">
                <div class="mbaa-artwork-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                        <circle cx="12" cy="7" r="4" />
                    </svg>
                </div>
                <div class="mbaa-artwork-title-section">
                    <h1 class="mbaa-artwork-title"><?php echo $is_edit ? esc_html($artiste->nom) : 'Nouvel artiste'; ?></h1>
                    <span class="mbaa-status-badge">Fiche artiste</span>
                </div>
                <div class="mbaa-action-buttons">
                    <a href="<?php echo admin_url('admin.php?page=mbaa-artistes'); ?>" class="mbaa-action-button" title="Retour">
                       
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19 12H5M5 12L12 19M5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        
                    </a>
                </div>
            </div>

            <div class="mbaa-form-grid">
                <!-- Informations personnelles -->
                <div class="mbaa-form-section">
                    <h2 class="mbaa-section-title">Informations personnelles</h2>
                    
                    <div class="mbaa-form-group">
                        <label class="mbaa-form-label">Nom complet <span class="mbaa-required">*</span></label>
                        <input type="text" name="nom" id="nom" class="mbaa-form-input" 
                               value="<?php echo $is_edit ? esc_attr($artiste->nom) : ''; ?>" 
                               placeholder="Nom de l'artiste" required>
                    </div>

                    <div class="mbaa-form-group">
                        <label class="mbaa-form-label">Nationalité</label>
                        <input type="text" name="nationalite" id="nationalite" class="mbaa-form-input" 
                               value="<?php echo $is_edit ? esc_attr($artiste->nationalite) : ''; ?>" 
                               placeholder="Ex: Français, Italien...">
                    </div>

                    <div class="mbaa-form-grid" style="grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 0;">
                        <div class="mbaa-form-group">
                            <label class="mbaa-form-label">Date de naissance</label>
                            <input type="date" name="date_naissance" id="date_naissance" class="mbaa-form-input" 
                                   value="<?php echo $is_edit && !empty($artiste->date_naissance) ? esc_attr($artiste->date_naissance) : ''; ?>">
                        </div>
                        <div class="mbaa-form-group">
                            <label class="mbaa-form-label">Date de décès</label>
                            <input type="date" name="date_deces" id="date_deces" class="mbaa-form-input" 
                                   value="<?php echo $is_edit && !empty($artiste->date_deces) ? esc_attr($artiste->date_deces) : ''; ?>">
                        </div>
                    </div>

                    <div class="mbaa-form-group">
                        <label class="mbaa-form-label">Lieu de naissance</label>
                        <input type="text" name="lieu_naissance" id="lieu_naissance" class="mbaa-form-input" 
                               value="<?php echo $is_edit ? esc_attr($artiste->lieu_naissance ?? '') : ''; ?>"
                               placeholder="Ville, Pays" disabled>
                        <p class="mbaa-form-help">Ce champ n'est pas encore disponible dans la base de données</p>
                    </div>
                </div>

                <!-- Présentation et liens -->
                <div class="mbaa-form-section">
                    <h2 class="mbaa-section-title">Présentation et liens</h2>
                    
                    <div class="mbaa-form-group">
                        <label class="mbaa-form-label">Style artistique</label>
                        <input type="text" name="style_art" id="style_art" class="mbaa-form-input" 
                               value="<?php echo $is_edit ? esc_attr($artiste->style_art ?? '') : ''; ?>"
                               placeholder="Ex: Impressionnisme, Surréalisme" disabled>
                        <p class="mbaa-form-help">Ce champ n'est pas encore disponible dans la base de données</p>
                    </div>

                    <div class="mbaa-form-group">
                        <label class="mbaa-form-label">Site Web</label>
                        <input type="text" name="site_web" id="site_web" class="mbaa-form-input" 
                               value="<?php echo $is_edit ? esc_attr($artiste->site_web ?? '') : ''; ?>"
                               placeholder="https://..." disabled>
                        <p class="mbaa-form-help">Ce champ n'est pas encore disponible dans la base de données</p>
                    </div>

                    <div class="mbaa-form-group">
                        <label class="mbaa-form-label">Réseaux Sociaux</label>
                        <input type="text" name="reseaux_sociaux" id="reseaux_sociaux" class="mbaa-form-input" 
                               value="<?php echo $is_edit ? esc_attr($artiste->reseaux_sociaux ?? '') : ''; ?>"
                               placeholder="Instagram, Twitter, etc." disabled>
                        <p class="mbaa-form-help">Ce champ n'est pas encore disponible dans la base de données</p>
                    </div>
                </div>
            </div>

            <!-- Biographie -->
            <div class="mbaa-description-section">
                <h2 class="mbaa-description-title">Biographie</h2>
                <p class="mbaa-description-subtitle">Histoire et parcours de l'artiste</p>
                <?php 
                $content = $is_edit ? $artiste->biographie : '';
                wp_editor($content, 'biographie', array(
                    'textarea_name' => 'biographie',
                    'media_buttons' => false,
                    'textarea_rows' => 10,
                    'teeny' => true,
                    'quicktags' => false
                )); 
                ?>
            </div>

            <!-- Médias -->
            <div class="mbaa-form-section mbaa-media-section">
                <h2 class="mbaa-section-title">Médias</h2>
                
                <div class="mbaa-form-grid" style="gap: 24px;">
                    <div class="mbaa-form-group">
                        <label class="mbaa-form-label">Portrait de l'artiste</label>
                        <div class="mbaa-upload-area" id="upload_image_area">
                            <span class="dashicons dashicons-admin-users" style="font-size: 48px; width: 48px; height: 48px; margin-bottom: 10px; color: #ccc;"></span>
                            <p>Cliquez pour sélectionner une photo</p>
                            <input type="hidden" name="image_url" id="image_url" value="<?php echo $is_edit ? esc_attr($artiste->image_url) : ''; ?>">
                        </div>
                        <div id="image_preview" style="margin-top: 10px;">
                            <?php if ($is_edit && $artiste->image_url): ?>
                                <img src="<?php echo esc_url($artiste->image_url); ?>" style="max-width: 100%; height: auto; border-radius: 8px;">
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="mbaa-form-group">
                        <label class="mbaa-form-label">Audio Biographie (Optionnel)</label>
                        <div class="mbaa-upload-area" id="upload_audio_area">
                            <span class="dashicons dashicons-media-audio" style="font-size: 48px; width: 48px; height: 48px; margin-bottom: 10px; color: #ccc;"></span>
                            <p>Cliquez pour sélectionner un fichier audio</p>
                            <input type="hidden" name="audio_biographie" id="audio_biographie" value="<?php echo $is_edit ? esc_attr($artiste->audio_biographie) : ''; ?>">
                        </div>
                        <div id="audio_preview" style="margin-top: 10px;">
                            <?php if ($is_edit && $artiste->audio_biographie): ?>
                                <audio controls style="width: 100%;">
                                    <source src="<?php echo esc_url($artiste->audio_biographie); ?>" type="audio/mpeg">
                                </audio>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="mbaa-bottom-actions" style="margin-top: 30px; display: flex; gap: 10px;">
                <button type="submit" name="submit" class="button button-primary button-large">Enregistrer l'artiste</button>
                <a href="<?php echo admin_url('admin.php?page=mbaa-artistes'); ?>" class="button button-secondary button-large">Annuler</a>
            </div>
        </div>
    </form>
</div>

<style>
.mbaa-upload-area {
    border: 2px dashed #ccc;
    padding: 30px;
    text-align: center;
    background: #fafafa;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s;
}
.mbaa-upload-area:hover {
    border-color: #c9a227;
    background: #f0f0f0;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Image Upload
    $('#upload_image_area').on('click', function() {
        var imageUploader = wp.media({
            title: 'Choisir une photo',
            button: { text: 'Utiliser cette photo' },
            multiple: false
        }).on('select', function() {
            var attachment = imageUploader.state().get('selection').first().toJSON();
            $('#image_url').val(attachment.url);
            $('#image_preview').html('<img src="' + attachment.url + '" style="max-width: 100%; height: auto; border-radius: 8px;">');
        }).open();
    });

    // Audio Upload
    $('#upload_audio_area').on('click', function() {
        var audioUploader = wp.media({
            title: 'Choisir un fichier audio',
            button: { text: 'Utiliser cet audio' },
            multiple: false,
            library: { type: 'audio' }
        }).on('select', function() {
            var attachment = audioUploader.state().get('selection').first().toJSON();
            $('#audio_biographie').val(attachment.url);
            $('#audio_preview').html('<audio controls style="width: 100%;"><source src="' + attachment.url + '" type="audio/mpeg"></audio>');
        }).open();
    });

    // Debug pour les dates
    $('#mbaa-artiste-form').on('submit', function(e) {
        var dateNaissance = $('#date_naissance').val();
        var dateDeces = $('#date_deces').val();
        
        console.log('Date de naissance:', dateNaissance);
        console.log('Date de décès:', dateDeces);
        
        // Les dates vides sont valides
        if (dateNaissance === '' || dateDeces === '') {
            console.log('Dates vides acceptées');
        }
    });
});
</script>
