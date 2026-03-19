<?php
$is_edit = isset($evenement) && !empty($evenement);
$page_title = $is_edit ? 'Modifier l\'événement' : 'Créer un événement';

// Récupérer les types d'événements
$evenement_manager = new MBAA_Evenement();
$types_evenement = $evenement_manager->get_types_evenement();

?>

<div class="wrap mbaa-wrap">
    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" class="mbaa-form">
        <input type="hidden" name="action" value="mbaa_save_evenement">
        <?php wp_nonce_field('mbaa_save_evenement', 'mbaa_evenement_nonce'); ?>
        
        <?php if ($is_edit): ?>
            <input type="hidden" name="id_evenement" value="<?php echo esc_attr($evenement->id_evenement); ?>">
        <?php endif; ?>

        <div class="mbaa-main-content">
            <div class="mbaa-artwork-header">
                <div class="mbaa-artwork-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M8 7V3m8 4V3M4 11h16M5 5h14a1 1 0 0 1 1 1v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a1 1 0 0 1 1-1z" />
                    </svg>
                </div>
                <div class="mbaa-artwork-title-section">
                    <h1 class="mbaa-artwork-title"><?php echo esc_html($page_title); ?></h1>
                    <span class="mbaa-status-badge">Fiche événement</span>
                </div>
                <div class="mbaa-action-buttons">
                    <a href="<?php echo admin_url('admin.php?page=mbaa-evenements'); ?>" class="mbaa-action-button" title="Retour">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19 12H5M5 12L12 19M5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                </div>
            </div>

            <div class="mbaa-form-grid">
                <div class="mbaa-form-section">
                    <h2 class="mbaa-section-title">Informations principales</h2>

                    <table class="form-table" role="presentation">
                        <tbody>
                <tr>
                    <th scope="row">
                        <label for="titre">Titre <span class="required">*</span></label>
                    </th>
                    <td>
                        <input type="text" 
                               name="titre" 
                               id="titre" 
                               class="mbaa-form-input" 
                               value="<?php echo $is_edit ? esc_attr($evenement->titre) : ''; ?>" 
                               required>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="id_type_evenement">Type d'événement <span class="required">*</span></label>
                    </th>
                    <td>
                        <select name="id_type_evenement" id="id_type_evenement" class="mbaa-form-input" required>
                            <option value="">-- Sélectionner un type --</option>
                            <?php foreach ($types_evenement as $type): ?>
                                <option value="<?php echo esc_attr($type->id_type_evenement); ?>"
                                    <?php selected($is_edit && $evenement->id_type_evenement == $type->id_type_evenement); ?>>
                                    <?php echo esc_html($type->nom_type); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="date_evenement">Date <span class="required">*</span></label>
                    </th>
                    <td>
                        <input type="date" 
                               name="date_evenement" 
                               id="date_evenement" 
                               class="mbaa-form-input"
                               value="<?php echo $is_edit ? esc_attr($evenement->date_evenement) : ''; ?>" 
                               required>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="heure_debut">Heure de début</label>
                    </th>
                    <td>
                        <input type="time" 
                               name="heure_debut" 
                               id="heure_debut" 
                               class="mbaa-form-input"
                               value="<?php echo $is_edit && $evenement->heure_debut ? esc_attr(substr($evenement->heure_debut, 0, 5)) : ''; ?>">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="heure_fin">Heure de fin</label>
                    </th>
                    <td>
                        <input type="time" 
                               name="heure_fin" 
                               id="heure_fin" 
                               class="mbaa-form-input"
                               value="<?php echo $is_edit && $evenement->heure_fin ? esc_attr(substr($evenement->heure_fin, 0, 5)) : ''; ?>">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="descriptif">Descriptif</label>
                    </th>
                    <td>
                        <?php
                        wp_editor(
                            $is_edit ? $evenement->descriptif : '',
                            'descriptif',
                            array(
                                'textarea_name' => 'descriptif',
                                'textarea_rows' => 8,
                                'media_buttons' => false,
                                'teeny' => true
                            )
                        );
                        ?>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="lieu_musee">Lieu dans le musée</label>
                    </th>
                    <td>
                        <input type="text" 
                               name="lieu_musee" 
                               id="lieu_musee" 
                               class="mbaa-form-input" 
                               value="<?php echo $is_edit ? esc_attr($evenement->lieu_musee) : ''; ?>"
                               placeholder="Ex: Salle d'exposition temporaire">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="public_cible">Public cible</label>
                    </th>
                    <td>
                        <fieldset>
                            <label>
                                <input type="checkbox" 
                                       name="public_adulte" 
                                       value="1" 
                                       <?php checked($is_edit && isset($evenement->public_adulte) && $evenement->public_adulte); ?>>
                                Adultes
                            </label>
                            <br>
                            <label>
                                <input type="checkbox" 
                                       name="public_ado" 
                                       value="1" 
                                       <?php checked($is_edit && isset($evenement->public_ado) && $evenement->public_ado); ?>>
                                Adolescents
                            </label>
                            <br>
                            <label>
                                <input type="checkbox" 
                                       name="public_enfant" 
                                       value="1" 
                                       <?php checked($is_edit && isset($evenement->public_enfant) && $evenement->public_enfant); ?>>
                                Enfants
                            </label>
                            <br>
                            <label>
                                <input type="checkbox" 
                                       name="public_tout_public" 
                                       value="1" 
                                       <?php checked($is_edit && isset($evenement->public_tout_public) && $evenement->public_tout_public); ?>>
                                Tout public
                            </label>
                        </fieldset>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="accessible_handicap">Accessibilité</label>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox" 
                                   name="accessible_handicap" 
                                   value="1" 
                                   <?php checked($is_edit && isset($evenement->accessible_handicap) && $evenement->accessible_handicap); ?>>
                            Accessible aux personnes en situation de handicap
                        </label>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="niveau">Niveau de pratique</label>
                    </th>
                    <td>
                        <fieldset>
                            <label>
                                <input type="checkbox" 
                                       name="niveau_debutant" 
                                       value="1" 
                                       <?php checked($is_edit && isset($evenement->niveau_debutant) && $evenement->niveau_debutant); ?>>
                                Débutant
                            </label>
                            <br>
                            <label>
                                <input type="checkbox" 
                                       name="niveau_intermediaire" 
                                       value="1" 
                                       <?php checked($is_edit && isset($evenement->niveau_intermediaire) && $evenement->niveau_intermediaire); ?>>
                                Intermédiaire
                            </label>
                            <br>
                            <label>
                                <input type="checkbox" 
                                       name="niveau_confirme" 
                                       value="1" 
                                       <?php checked($is_edit && isset($evenement->niveau_confirme) && $evenement->niveau_confirme); ?>>
                                Confirmé
                            </label>
                        </fieldset>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="intervenant">Intervenant</label>
                    </th>
                    <td>
                        <input type="text" 
                               name="intervenant" 
                               id="intervenant" 
                               class="mbaa-form-input" 
                               value="<?php echo $is_edit ? esc_attr($evenement->intervenant) : ''; ?>">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="capacite_max">Capacité maximale</label>
                    </th>
                    <td>
                        <input type="number" 
                               name="capacite_max" 
                               id="capacite_max" 
                               class="mbaa-form-input" 
                               value="<?php echo $is_edit ? esc_attr($evenement->capacite_max) : ''; ?>"
                               min="0"
                               placeholder="Nombre de participants">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="image_url">Image</label>
                    </th>
                    <td>
                        <input type="url" 
                               name="image_url" 
                               id="image_url" 
                               class="mbaa-form-input" 
                               value="<?php echo $is_edit ? esc_attr($evenement->image_url) : ''; ?>">
                        <button type="button" class="button" id="upload_image_button">
                            Choisir une image
                        </button>
                        
                        <?php if ($is_edit && $evenement->image_url): ?>
                        <div class="image-preview" style="margin-top: 10px;">
                            <img src="<?php echo esc_url($evenement->image_url); ?>" 
                                 style="max-width: 300px; height: auto; border: 1px solid #ddd; padding: 5px;">
                        </div>
                        <?php endif; ?>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Tarif</th>
                    <td>
                        <fieldset>
                            <label>
                                <input type="checkbox" 
                                       name="est_gratuit" 
                                       value="1" 
                                       <?php checked($is_edit && $evenement->est_gratuit); ?>>
                                Événement gratuit
                            </label>
                            <br>
                            <div style="margin-top: 10px;">
                                <label for="prix">Prix (€) : </label>
                                <input type="number" 
                                       name="prix" 
                                       id="prix" 
                                       class="mbaa-form-input" 
                                       value="<?php echo $is_edit && $evenement->prix ? esc_attr($evenement->prix) : ''; ?>"
                                       step="0.01"
                                       min="0"
                                       placeholder="0.00">
                                <p class="description">Laissez vide si l'événement est gratuit</p>
                            </div>
                            <div style="margin-top: 10px;">
                                <label for="label_tarif">Label tarifaire : </label>
                                <input type="text" 
                                       name="label_tarif" 
                                       id="label_tarif" 
                                       class="mbaa-form-input" 
                                       value="<?php echo $is_edit ? esc_attr($evenement->label_tarif) : ''; ?>"
                                       placeholder="Ex: Plein tarif, Tarif réduit, Gratuit">
                            </div>
                        </fieldset>
                    </td>
                </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mbaa-bottom-actions" style="margin-top: 30px; display: flex; gap: 10px;">
                <button type="submit" name="submit" class="button button-primary button-large">
                    <?php echo $is_edit ? 'Mettre à jour' : 'Créer'; ?>
                </button>
                <a href="<?php echo admin_url('admin.php?page=mbaa-evenements'); ?>" class="button button-secondary button-large">Annuler</a>
            </div>
        </div>
    </form>
</div>

<script>
jQuery(document).ready(function($) {
    var mediaUploader;
    
    // Gestion du checkbox gratuit
    $('input[name="est_gratuit"]').on('change', function() {
        if ($(this).is(':checked')) {
            $('#prix').prop('disabled', true).val('');
        } else {
            $('#prix').prop('disabled', false);
        }
    }).trigger('change');
    
    // Upload d'image
    $('#upload_image_button').on('click', function(e) {
        e.preventDefault();
        
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }
        
        mediaUploader = wp.media({
            title: 'Choisir une image',
            button: {
                text: 'Utiliser cette image'
            },
            multiple: false
        });
        
        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#image_url').val(attachment.url);
            
            if ($('.image-preview').length === 0) {
                $('#image_url').parent().append('<div class="image-preview" style="margin-top: 10px;"></div>');
            }
            $('.image-preview').html('<img src="' + attachment.url + '" style="max-width: 300px; height: auto; border: 1px solid #ddd; padding: 5px;">');
        });
        
        mediaUploader.open();
    });
});
</script>






