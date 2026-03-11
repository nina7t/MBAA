<?php
/**
 * Formulaire d'ajout/modification d'œuvre
 */

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;

// Récupération des données pour l'édition
$is_edit = isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id']);
$oeuvre = null;

if ($is_edit) {
    $id = absint($_GET['id']);
    $oeuvre = $wpdb->get_row($wpdb->prepare("
        SELECT o.*, a.nom as artiste_nom 
        FROM {$wpdb->prefix}mbaa_oeuvre o 
        LEFT JOIN {$wpdb->prefix}mbaa_artiste a ON o.id_artiste = a.id_artiste 
        WHERE o.id_oeuvre = %d
    ", $id));
    
    if (!$oeuvre) {
        wp_die('Œuvre introuvable.');
    }
}

// Récupération des listes déroulantes - NOMS DE TABLES CORRIGÉS
// Alias id/nom pour conserver le rendu HTML existant (option value=$obj->id / label=$obj->nom)
$artistes = $wpdb->get_results("SELECT id_artiste as id, nom FROM {$wpdb->prefix}mbaa_artiste ORDER BY nom ASC");
$epoques = $wpdb->get_results("SELECT id_epoque as id, nom_epoque as nom FROM {$wpdb->prefix}mbaa_epoque ORDER BY nom_epoque ASC");
$salles = $wpdb->get_results("SELECT id_salle as id, nom_salle as nom FROM {$wpdb->prefix}mbaa_salle ORDER BY nom_salle ASC");
$mediums = $wpdb->get_results("SELECT id_medium as id, nom_medium as nom FROM {$wpdb->prefix}mbaa_medium ORDER BY nom_medium ASC");
$mouvements = $wpdb->get_results("SELECT id_mouvement as id, nom_mouvement as nom FROM {$wpdb->prefix}mbaa_mouvement_artistique ORDER BY nom_mouvement ASC");
$categories = $wpdb->get_results("SELECT id_categorie as id, nom_categorie as nom FROM {$wpdb->prefix}mbaa_categorie ORDER BY nom_categorie ASC");
$galeries = $wpdb->get_results("SELECT id, nom FROM {$wpdb->prefix}mbaa_galerie ORDER BY nom ASC");

// Récupération du QR code si édition
$qr_code = null;
if ($is_edit && $oeuvre) {
    $qr_code = $wpdb->get_row($wpdb->prepare("
        SELECT * FROM {$wpdb->prefix}mbaa_qr_codes WHERE id_oeuvre = %d
    ", $oeuvre->id_oeuvre));
}

// Récupération de l'audioguide si édition
$audioguide = null;
if ($is_edit && $oeuvre) {
    $audioguide = $wpdb->get_row($wpdb->prepare("
        SELECT * FROM {$wpdb->prefix}mbaa_audioguide WHERE id_oeuvre = %d
    ", $oeuvre->id_oeuvre));
}

// URL publique de l'œuvre
$public_url = '';
if ($is_edit && $oeuvre) {
    $public_url = home_url('/oeuvre/' . $oeuvre->id_oeuvre . '/' . sanitize_title($oeuvre->titre));
}
?>

<div class="wrap mbaa-wrap">
    <!-- Liens de test pour débogage -->
    <div style="background: #e3f2fd; padding: 10px; margin-bottom: 20px; border-radius: 4px;">
        <strong>DEBUG:</strong> 
        <a href="<?php echo admin_url('admin-post.php?action=mbaa_check_table_structure'); ?>" target="_blank" style="margin-right: 15px; color: #9c27b0;">
            Vérifier structure tables
        </a>
        <a href="<?php echo admin_url('admin-post.php?action=mbaa_test_oeuvres_tables'); ?>" target="_blank" style="margin-right: 15px;">
            Tester les tables d'œuvres
        </a>
        <a href="<?php echo admin_url('admin-post.php?action=mbaa_create_galerie_table'); ?>" target="_blank" style="margin-right: 15px; color: #ff9800;">
            Créer table galerie
        </a>
        <a href="<?php echo admin_url('admin-post.php?action=mbaa_insert_default_data'); ?>" target="_blank" style="color: #d32f2f;">
            Insérer données par défaut
        </a>
    </div>
    
    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" id="mbaa-oeuvre-form" class="mbaa-form">
        <input type="hidden" name="action" value="mbaa_save_oeuvre">
        <?php if ($is_edit): ?>
            <input type="hidden" name="id" value="<?php echo esc_attr($oeuvre->id_oeuvre); ?>">
        <?php endif; ?>
        <?php wp_nonce_field('mbaa_save_oeuvre', 'mbaa_save_oeuvre_nonce'); ?>

        <div class="mbaa-main-content">
            <!-- Œuvre Header -->
            <div class="mbaa-artwork-header">
                <div class="mbaa-artwork-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                        <circle cx="8.5" cy="8.5" r="1.5"/>
                        <polyline points="21 15 16 10 5 21"/>
                    </svg>
                </div>
                <div class="mbaa-artwork-title-section">
                    <h1 class="mbaa-artwork-title"><?php echo $is_edit ? esc_html($oeuvre->titre) : 'Nouvelle œuvre'; ?></h1>
                    <span class="mbaa-status-badge">Fiche œuvre</span>
                </div>
                <div class="mbaa-action-buttons">
                    <a href="<?php echo admin_url('admin.php?page=mbaa-oeuvres'); ?>" class="mbaa-action-button" title="Retour">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19 12H5M5 12L12 19M5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                </div>
            </div>

            <div class="mbaa-form-grid">
                <!-- Informations principales -->
                <div class="mbaa-form-section">
                    <h2 class="mbaa-section-title">Informations principales</h2>
                    
                    <div class="mbaa-form-group">
                        <label class="mbaa-form-label">Titre de l'œuvre <span class="mbaa-required">*</span></label>
                        <input type="text" name="titre" id="titre" class="mbaa-form-input" 
                               value="<?php echo $is_edit ? esc_attr($oeuvre->titre) : ''; ?>" 
                               placeholder="Titre de l'œuvre" required>
                    </div>

                    <div class="mbaa-form-group">
                        <label class="mbaa-form-label">Description</label>
                        <textarea name="description" id="description" class="mbaa-form-input" rows="6" placeholder="Description détaillée de l'œuvre"><?php 
                            echo $is_edit ? esc_textarea($oeuvre->description) : ''; 
                        ?></textarea>
                    </div>

                    <div class="mbaa-form-grid" style="grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 0;">
                        <div class="mbaa-form-group">
                            <label class="mbaa-form-label">Date de création</label>
                            <input type="date" name="date_creation" id="date_creation" class="mbaa-form-input" 
                                   value="<?php echo $is_edit ? esc_attr($oeuvre->date_creation) : ''; ?>">
                        </div>
                        <div class="mbaa-form-group">
                            <label class="mbaa-form-label">Numéro d'inventaire</label>
                            <input type="text" name="numero_inventaire" id="numero_inventaire" class="mbaa-form-input" 
                                   value="<?php echo $is_edit ? esc_attr($oeuvre->numero_inventaire) : ''; ?>"
                                   placeholder="Numéro unique">
                        </div>
                    </div>

                    <div class="mbaa-form-group">
                        <label class="mbaa-form-label">Dimensions</label>
                        <input type="text" name="dimensions" id="dimensions" class="mbaa-form-input" 
                               value="<?php echo $is_edit ? esc_attr($oeuvre->dimensions) : ''; ?>"
                               placeholder="Ex: 50x70 cm">
                    </div>

                    <div class="mbaa-form-group">
                        <label class="mbaa-form-label">Technique</label>
                        <input type="text" name="technique" id="technique" class="mbaa-form-input" 
                               value="<?php echo $is_edit ? esc_attr($oeuvre->technique) : ''; ?>"
                               placeholder="Huile sur toile, acrylique, etc.">
                    </div>

                    <div class="mbaa-form-group">
                        <label class="mbaa-form-label">Provenance</label>
                        <input type="text" name="provenance" id="provenance" class="mbaa-form-input" 
                               value="<?php echo $is_edit ? esc_attr($oeuvre->provenance) : ''; ?>"
                               placeholder="Origine de l'œuvre">
                    </div>

                    <div class="mbaa-form-group">
                        <label class="mbaa-form-label">État</label>
                        <select name="etat" id="etat" class="mbaa-form-input">
                            <?php $etat_value = $is_edit ? intval($oeuvre->etat ?? 1) : 1; ?>
                            <option value="1" <?php selected($etat_value, 1); ?>>Active</option>
                            <option value="0" <?php selected($etat_value, 0); ?>>Inactive</option>
                        </select>
                    </div>

                    <div class="mbaa-form-group">
                        <label class="mbaa-form-label">Situation</label>
                        <?php $situation_value = $is_edit ? ($oeuvre->situation ?? 'exposee') : 'exposee'; ?>
                        <select name="situation" id="situation" class="mbaa-form-input">
                            <option value="exposee" <?php selected($situation_value, 'exposee'); ?>>Exposée</option>
                            <option value="restauration" <?php selected($situation_value, 'restauration'); ?>>En restauration</option>
                            <option value="pret" <?php selected($situation_value, 'pret'); ?>>En prêt</option>
                            <option value="reserve" <?php selected($situation_value, 'reserve'); ?>>En réserve</option>
                            <option value="depot" <?php selected($situation_value, 'depot'); ?>>En dépôt</option>
                            <option value="autre" <?php selected($situation_value, 'autre'); ?>>Autre</option>
                        </select>
                    </div>
                </div>

                <!-- Associations -->
                <div class="mbaa-form-section">
                    <h2 class="mbaa-section-title">Associations</h2>
                    
                    <div class="mbaa-form-group">
                        <label class="mbaa-form-label">Artiste</label>
                        <div class="mbaa-artist-search-container">
                            <input type="text" name="artiste_search" id="artiste_search" class="mbaa-form-input" 
                                   placeholder="Tapez 2+ lettres pour rechercher un artiste..."
                                   value="<?php echo $is_edit && $oeuvre->id_artiste ? esc_html($oeuvre->artiste_nom ?? '') : ''; ?>">
                            <input type="hidden" name="id_artiste" id="id_artiste" 
                                   value="<?php echo $is_edit && !empty($oeuvre->id_artiste) ? esc_attr($oeuvre->id_artiste) : ''; ?>">
                            <div id="artiste_search_results" class="mbaa-search-results"></div>
                        </div>
                        <p class="mbaa-form-help">Commencez à taper pour rechercher un artiste existant</p>
                    </div>

                    <div class="mbaa-form-grid" style="grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 0;">
                        <div class="mbaa-form-group">
                            <label class="mbaa-form-label">Époque</label>
                            <select name="id_epoque" id="id_epoque" class="mbaa-form-input">
                                <option value="">Sélectionner une époque</option>
                                <?php foreach ($epoques as $epoque): ?>
                                    <option value="<?php echo esc_attr($epoque->id); ?>" 
                                            <?php echo $is_edit && $oeuvre->id_epoque == $epoque->id ? 'selected' : ''; ?>>
                                        <?php echo esc_html($epoque->nom); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mbaa-form-group">
                            <label class="mbaa-form-label">Mouvement</label>
                            <select name="id_mouvement" id="id_mouvement" class="mbaa-form-input">
                                <option value="">Sélectionner un mouvement</option>
                                <?php foreach ($mouvements as $mouvement): ?>
                                    <option value="<?php echo esc_attr($mouvement->id); ?>" 
                                            <?php echo $is_edit && $oeuvre->id_mouvement == $mouvement->id ? 'selected' : ''; ?>>
                                        <?php echo esc_html($mouvement->nom); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mbaa-form-grid" style="grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 0;">
                        <div class="mbaa-form-group">
                            <label class="mbaa-form-label">Salle</label>
                            <select name="id_salle" id="id_salle" class="mbaa-form-input">
                                <option value="">Sélectionner une salle</option>
                                <?php foreach ($salles as $salle): ?>
                                    <option value="<?php echo esc_attr($salle->id); ?>" 
                                            <?php echo $is_edit && $oeuvre->id_salle == $salle->id ? 'selected' : ''; ?>>
                                        <?php echo esc_html($salle->nom); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mbaa-form-group">
                            <label class="mbaa-form-label">Galerie</label>
                            <select name="id_galerie" id="id_galerie" class="mbaa-form-input">
                                <option value="">Sélectionner une galerie</option>
                                <?php foreach ($galeries as $galerie): ?>
                                    <option value="<?php echo esc_attr($galerie->id); ?>" 
                                            <?php echo $is_edit && $oeuvre->id_galerie == $galerie->id ? 'selected' : ''; ?>>
                                        <?php echo esc_html($galerie->nom); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mbaa-form-grid" style="grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 0;">
                        <div class="mbaa-form-group">
                            <label class="mbaa-form-label">Medium</label>
                            <select name="id_medium" id="id_medium" class="mbaa-form-input">
                                <option value="">Sélectionner un medium</option>
                                <?php foreach ($mediums as $medium): ?>
                                    <option value="<?php echo esc_attr($medium->id); ?>" 
                                            <?php echo $is_edit && $oeuvre->id_medium == $medium->id ? 'selected' : ''; ?>>
                                        <?php echo esc_html($medium->nom); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mbaa-form-group">
                            <label class="mbaa-form-label">Catégorie</label>
                            <select name="id_categorie" id="id_categorie" class="mbaa-form-input">
                                <option value="">Sélectionner une catégorie</option>
                                <?php foreach ($categories as $categorie): ?>
                                    <option value="<?php echo esc_attr($categorie->id); ?>" 
                                            <?php echo $is_edit && $oeuvre->id_categorie == $categorie->id ? 'selected' : ''; ?>>
                                        <?php echo esc_html($categorie->nom); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Médias -->
            <div class="mbaa-form-section mbaa-media-section">
                <h2 class="mbaa-section-title">Médias</h2>
                
                <div class="mbaa-form-grid" style="gap: 24px;">
                    <div class="mbaa-form-group">
                        <label class="mbaa-form-label">Image principale</label>
                        <div class="mbaa-upload-area" id="upload_main_image_area">
                            <span class="dashicons dashicons-image" style="font-size: 48px; width: 48px; height: 48px; margin-bottom: 10px; color: #ccc;"></span>
                            <p>Cliquez pour sélectionner une image</p>
                            <input type="hidden" name="image_url" id="image_url" value="<?php echo $is_edit ? esc_url($oeuvre->image_url) : ''; ?>">
                        </div>
                        <div id="main_image_preview" style="margin-top: 10px;">
                            <?php if ($is_edit && $oeuvre->image_url): ?>
                                <img src="<?php echo esc_url($oeuvre->image_url); ?>" style="max-width: 100%; height: auto; border-radius: 8px;">
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="mbaa-form-group">
                        <label class="mbaa-form-label">Galerie d'images</label>
                        <div class="mbaa-upload-area" id="upload_gallery_area">
                            <span class="dashicons dashicons-images-alt2" style="font-size: 48px; width: 48px; height: 48px; margin-bottom: 10px; color: #ccc;"></span>
                            <p>Glissez-déposez ou cliquez pour ajouter des images</p>
                            <input type="hidden" name="gallery_urls" id="gallery_urls" value="<?php echo $is_edit ? esc_attr($oeuvre->gallery_urls ?? '') : ''; ?>">
                        </div>
                        <div id="gallery_preview" style="margin-top: 10px;">
                            <?php 
                            if ($is_edit && !empty($oeuvre->gallery_urls)) {
                                $gallery_urls = json_decode($oeuvre->gallery_urls, true) ?: [];
                                foreach ($gallery_urls as $url) {
                                    echo '<img src="' . esc_url($url) . '" style="max-width: 100px; height: auto; border-radius: 4px; margin: 2px;">';
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>

                <div class="mbaa-form-group" style="margin-top: 24px;">
                    <label class="mbaa-form-label">Fichiers audio</label>
                    <div class="mbaa-upload-area" id="upload_audio_area">
                        <span class="dashicons dashicons-media-audio" style="font-size: 48px; width: 48px; height: 48px; margin-bottom: 10px; color: #ccc;"></span>
                        <p>Glissez-déposez ou cliquez pour ajouter des fichiers audio</p>
                        <input type="hidden" name="audio_urls" id="audio_urls" value="<?php echo $is_edit ? esc_attr($oeuvre->audio_urls ?? '') : ''; ?>">
                    </div>
                    <div id="audio_preview" style="margin-top: 10px;">
                        <?php 
                        if ($is_edit && !empty($oeuvre->audio_urls)) {
                            $audio_urls = json_decode($oeuvre->audio_urls, true) ?: [];
                            foreach ($audio_urls as $url) {
                                $filename = basename($url);
                                echo '<div style="margin: 5px 0; padding: 8px; background: #f5f5f5; border-radius: 4px;">';
                                echo '<span class="dashicons dashicons-media-audio"></span> ' . esc_html($filename);
                                echo '</div>';
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>

            <!-- Audioguide -->
            <div class="mbaa-form-section">
                <h2 class="mbaa-section-title">Audioguide</h2>
                
                <div class="mbaa-form-group">
                    <label class="mbaa-form-label">
                        <input type="checkbox" name="has_audioguide" value="1" 
                               <?php echo $is_edit && $audioguide ? 'checked' : ''; ?>>
                        Activer l'audioguide pour cette œuvre
                    </label>
                    <p class="mbaa-form-help">Cocher pour ajouter un audioguide à cette œuvre</p>
                </div>

                <div id="audioguide_fields" style="<?php echo ($is_edit && $audioguide) || !$is_edit ? '' : 'display: none;'; ?>">
                    <div class="mbaa-form-group">
                        <label class="mbaa-form-label">Fichier audio</label>
                        <div class="mbaa-upload-area" id="upload_audioguide_area">
                            <span class="dashicons dashicons-media-audio" style="font-size: 48px; width: 48px; height: 48px; margin-bottom: 10px; color: #ccc;"></span>
                            <p>Cliquez pour sélectionner un fichier audio</p>
                            <input type="hidden" name="audioguide_fichier" id="audioguide_fichier" value="<?php echo $is_edit && $audioguide ? esc_url($audioguide->fichier_audio_url) : ''; ?>">
                        </div>
                        <div id="audioguide_preview" style="margin-top: 10px;">
                            <?php if ($is_edit && $audioguide && $audioguide->fichier_audio_url): ?>
                                <audio controls style="width: 100%; max-width: 300px;">
                                    <source src="<?php echo esc_url($audioguide->fichier_audio_url); ?>" type="audio/mpeg">
                                    Votre navigateur ne supporte pas l'élément audio.
                                </audio>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="mbaa-form-grid" style="grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 0;">
                        <div class="mbaa-form-group">
                            <label class="mbaa-form-label">Durée (secondes)</label>
                            <input type="number" name="audioguide_duree" id="audioguide_duree" class="mbaa-form-input" 
                                   value="<?php echo $is_edit && $audioguide ? esc_attr($audioguide->duree_secondes) : ''; ?>"
                                   placeholder="Ex: 120">
                        </div>
                        <div class="mbaa-form-group">
                            <label class="mbaa-form-label">Langue</label>
                            <select name="audioguide_langue" id="audioguide_langue" class="mbaa-form-input">
                                <option value="fr" <?php echo $is_edit && $audioguide && $audioguide->langue === 'fr' ? 'selected' : ''; ?>>Français</option>
                                <option value="en" <?php echo $is_edit && $audioguide && $audioguide->langue === 'en' ? 'selected' : ''; ?>>English</option>
                                <option value="de" <?php echo $is_edit && $audioguide && $audioguide->langue === 'de' ? 'selected' : ''; ?>>Deutsch</option>
                                <option value="es" <?php echo $is_edit && $audioguide && $audioguide->langue === 'es' ? 'selected' : ''; ?>>Español</option>
                            </select>
                        </div>
                    </div>

                    <div class="mbaa-form-group">
                        <label class="mbaa-form-label">Transcription</label>
                        <textarea name="audioguide_transcription" id="audioguide_transcription" class="mbaa-form-input" rows="4" placeholder="Transcription du contenu audio"><?php 
                            echo $is_edit && $audioguide ? esc_textarea($audioguide->transcription) : ''; 
                        ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Visibilité -->
            <div class="mbaa-form-section">
                <h2 class="mbaa-section-title">Visibilité</h2>
                
                <div class="mbaa-form-grid" style="grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="mbaa-form-group">
                        <label class="mbaa-form-label">
                            <input type="checkbox" name="visible_galerie" value="1" 
                                   <?php echo $is_edit && $oeuvre->visible_galerie ? 'checked' : ''; ?>>
                            Visible en galerie
                        </label>
                        <p class="mbaa-form-help">Cocher pour rendre visible dans la galerie publique</p>
                    </div>
                    
                    <div class="mbaa-form-group">
                        <label class="mbaa-form-label">
                            <input type="checkbox" name="visible_accueil" value="1" 
                                   <?php echo $is_edit && $oeuvre->visible_accueil ? 'checked' : ''; ?>>
                            Visible en page d'accueil
                        </label>
                        <p class="mbaa-form-help">Cocher pour afficher sur la page d'accueil</p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="mbaa-bottom-actions" style="margin-top: 30px; display: flex; gap: 10px;">
                <button type="submit" name="submit" class="button button-primary button-large">
                    <?php echo $is_edit ? 'Mettre à jour l\'œuvre' : 'Enregistrer l\'œuvre'; ?>
                </button>
                <a href="<?php echo admin_url('admin.php?page=mbaa-oeuvres'); ?>" class="button button-secondary button-large">Annuler</a>
            </div>
        </div>
    </form>
    
    <!-- Section QR Code (uniquement en édition et après sauvegarde) -->
    <?php if ($is_edit && $qr_code): ?>
        <div class="mbaa-form-section" style="margin-top: 30px;">
            <h2 class="mbaa-section-title">QR Code de l'œuvre</h2>
            <div class="mbaa-qr-section">
                <div class="mbaa-qr-image">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?php echo urlencode($public_url); ?>" 
                         alt="QR Code de l'œuvre" style="border: 1px solid #ddd; border-radius: 8px;">
                </div>
                <div class="mbaa-qr-info">
                    <h3>QR Code généré</h3>
                    <p><strong>URL publique:</strong></p>
                    <div class="mbaa-url-display">
                        <input type="text" value="<?php echo esc_url($public_url); ?>" readonly class="regular-text" style="font-family: monospace; font-size: 12px;">
                        <button type="button" class="button mbaa-copy-url" data-url="<?php echo esc_url($public_url); ?>">Copier</button>
                    </div>
                    <p class="description">
                        Ce QR code pointe vers la page publique de l'œuvre. 
                        Les visiteurs peuvent le scanner pour accéder directement aux informations de l'œuvre.
                    </p>
                    <div class="mbaa-qr-actions">
                        <a href="https://api.qrserver.com/v1/create-qr-code/?size=500x500&data=<?php echo urlencode($public_url); ?>" 
                           class="button" download="qr-code-<?php echo esc_attr($oeuvre->id_oeuvre); ?>.png" target="_blank">
                            Télécharger le QR Code (HD)
                        </a>
                        <a href="<?php echo esc_url($public_url); ?>" class="button" target="_blank">
                            Voir la page publique
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Script pour l'upload de médias (style artiste) -->
<script>
jQuery(document).ready(function($) {

    // ── Image principale ──────────────────────────────────────────────────────
    $('#upload_main_image_area').on('click', function() {
        var uploader = wp.media({
            title: 'Choisir une image',
            button: { text: 'Utiliser cette image' },
            multiple: false
        }).on('select', function() {
            var a = uploader.state().get('selection').first().toJSON();
            $('#image_url').val(a.url);
            $('#main_image_preview').html('<img src="' + a.url + '" style="max-width:100%;height:auto;border-radius:8px;">');
        }).open();
    });

    // ── Galerie d'images ──────────────────────────────────────────────────────
    $('#upload_gallery_area').on('click', function() {
        var uploader = wp.media({
            title: 'Choisir des images',
            button: { text: 'Utiliser ces images' },
            multiple: true
        }).on('select', function() {
            var attachments = uploader.state().get('selection').toJSON();
            var urls = [], html = '';
            attachments.forEach(function(a) {
                urls.push(a.url);
                html += '<img src="' + a.url + '" style="max-width:100px;height:auto;border-radius:4px;margin:2px;">';
            });
            $('#gallery_urls').val(JSON.stringify(urls));
            $('#gallery_preview').html(html);
        }).open();
    });

    // ── Audio ─────────────────────────────────────────────────────────────────
    $('#upload_audio_area').on('click', function() {
        var uploader = wp.media({
            title: 'Choisir des fichiers audio',
            button: { text: 'Utiliser ces fichiers' },
            multiple: true,
            library: { type: 'audio' }
        }).on('select', function() {
            var attachments = uploader.state().get('selection').toJSON();
            var urls = [], html = '';
            attachments.forEach(function(a) {
                urls.push(a.url);
                var fname = a.filename || a.url.split('/').pop();
                html += '<div style="margin:5px 0;padding:8px;background:#f5f5f5;border-radius:4px;">'
                      + '<span class="dashicons dashicons-media-audio"></span> ' + fname + '</div>';
            });
            $('#audio_urls').val(JSON.stringify(urls));
            $('#audio_preview').html(html);
        }).open();
    });

    // ── Audioguide ────────────────────────────────────────────────────────────
    $('#upload_audioguide_area').on('click', function() {
        var uploader = wp.media({
            title: 'Choisir un fichier audio pour l\'audioguide',
            button: { text: 'Utiliser ce fichier' },
            multiple: false,
            library: { type: 'audio' }
        }).on('select', function() {
            var a = uploader.state().get('selection').first().toJSON();
            $('#audioguide_fichier').val(a.url);
            $('#audioguide_preview').html(
                '<audio controls style="width:100%;max-width:300px;">'
                + '<source src="' + a.url + '" type="audio/mpeg">'
                + 'Votre navigateur ne supporte pas l\'élément audio.</audio>'
            );
        }).open();
    });

    // ── Afficher/masquer champs audioguide ────────────────────────────────────
    $('input[name="has_audioguide"]').on('change', function() {
        $('#audioguide_fields').toggle($(this).is(':checked'));
    });

    // ── Copier l'URL ──────────────────────────────────────────────────────────
    $('.mbaa-copy-url').on('click', function() {
        var url = $(this).data('url');
        var tmp = $('<input>');
        $('body').append(tmp);
        tmp.val(url).select();
        document.execCommand('copy');
        tmp.remove();
        $(this).text('Copié!').addClass('success');
        setTimeout(function() { $('.mbaa-copy-url').text('Copier').removeClass('success'); }, 2000);
    });

    // ── FIX #3 : Recherche d'artiste (variables déclarées en dehors du handler)
    //    Avant le fix, $hiddenInput était déclaré dans le callback 'input',
    //    ce qui causait des problèmes de portée dans certains navigateurs.
    // ─────────────────────────────────────────────────────────────────────────
    var searchTimeout;
    var $artisteSearch  = $('#artiste_search');
    var $results        = $('#artiste_search_results');
    var $hiddenArtiste  = $('#id_artiste');   // ← déclaré UNE FOIS, en dehors

    $artisteSearch.on('input', function() {
        var query = $(this).val().trim();

        clearTimeout(searchTimeout);

        if (query.length < 2) {
            $results.empty().hide();
            if (query.length === 0) {
                // FIX #4 : On efface l'ID seulement si le champ est TOTALEMENT vide
                $hiddenArtiste.val('');
            }
            return;
        }

        searchTimeout = setTimeout(function() {
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'mbaa_recherche_artiste',
                    query: query,
                    nonce: '<?php echo wp_create_nonce('mbaa_recherche_artiste_nonce'); ?>'
                },
                success: function(response) {
                    $results.empty();
                    if (response.success && response.data.length > 0) {
                        $results.show();
                        response.data.forEach(function(artiste) {
                            var label = artiste.nom + (artiste.nationalite ? ' (' + artiste.nationalite + ')' : '');
                            $('<div class="mbaa-search-result-item">')
                                .text(label)
                                .data('id',  artiste.id_artiste)
                                .data('nom', artiste.nom)
                                .on('click', function() {
                                    // ← on utilise $hiddenArtiste (closure sur la variable externe)
                                    $artisteSearch.val($(this).data('nom'));
                                    $hiddenArtiste.val($(this).data('id'));
                                    $results.empty().hide();

                                    // Confirmation visuelle
                                    $artisteSearch.css('border-color', '#4caf50');
                                    setTimeout(function() { $artisteSearch.css('border-color', ''); }, 1500);
                                })
                                .appendTo($results);
                        });
                    } else {
                        $results.hide();
                    }
                },
                error: function() {
                    $results.empty().hide();
                }
            });
        }, 300);
    });

    // Fermer les résultats si clic ailleurs
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.mbaa-artist-search-container').length) {
            $results.empty().hide();
        }
    });

    // ── FIX #5 : Validation avant soumission ─────────────────────────────────
    //    Si le champ texte artiste est rempli mais l'ID est vide,
    //    on prévient l'utilisateur (l'artiste n'a pas été sélectionné
    //    dans la liste — sa saisie libre serait ignorée).
    $('#mbaa-oeuvre-form').on('submit', function(e) {
        var artisteNom = $artisteSearch.val().trim();
        var artisteId  = $hiddenArtiste.val().trim();

        if (artisteNom !== '' && artisteId === '') {
            e.preventDefault();
            alert(
                '⚠️ Artiste non sélectionné.\n\n'
                + 'Vous avez saisi "' + artisteNom + '" mais n\'avez pas cliqué sur un résultat de la liste.\n'
                + 'Veuillez taper le nom et cliquer sur le bon artiste dans la liste déroulante.'
            );
            $artisteSearch.focus().css('border-color', '#e53935');
            return false;
        }
    });

});
</script>

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

/* Styles pour la recherche d'artiste */
.mbaa-artist-search-container {
    position: relative;
}

.mbaa-search-results {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #ddd;
    border-top: none;
    max-height: 200px;
    overflow-y: auto;
    z-index: 1000;
    display: none;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.mbaa-search-result-item {
    padding: 10px 15px;
    cursor: pointer;
    border-bottom: 1px solid #f0f0f0;
    transition: background-color 0.2s;
}

.mbaa-search-result-item:hover {
    background-color: #f8f9fa;
}

.mbaa-search-result-item:last-child {
    border-bottom: none;
}
</style>
