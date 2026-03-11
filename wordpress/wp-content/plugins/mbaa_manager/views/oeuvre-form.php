<?php
/**
 * Vue du formulaire d'œuvre
 */
if (!defined('ABSPATH')) {
    exit;
}

// Charger le script AJAX pour cette page
wp_enqueue_script('jquery');
wp_enqueue_script('mbaa-admin-script');
wp_localize_script('mbaa-admin-script', 'mbaaAjax', array(
    'ajaxurl' => admin_url('admin-ajax.php'),
    'nonce' => wp_create_nonce('mbaa_nonce')
));

$is_edit = isset($oeuvre) && !empty($oeuvre);
$page_title = $is_edit ? 'Modifier l\'œuvre' : 'Ajouter une œuvre';

// Récupérer les données pour les selects
$db = new MBAA_Database();
global $wpdb;

$artistes = $wpdb->get_results("SELECT id_artiste, nom FROM {$db->table_artiste} ORDER BY nom ASC");
$epoques = $wpdb->get_results("SELECT id_epoque, nom_epoque FROM {$db->table_epoque} ORDER BY nom_epoque ASC");
$salles = $wpdb->get_results("SELECT id_salle, nom_salle FROM {$db->table_salle} ORDER BY nom_salle ASC");
$mediums = $wpdb->get_results("SELECT id_medium, nom_medium FROM {$db->table_medium} ORDER BY nom_medium ASC");
$mouvements = $wpdb->get_results("SELECT id_mouvement, nom_mouvement FROM {$db->table_mouvement} ORDER BY nom_mouvement ASC");
$categories = $wpdb->get_results("SELECT id_categorie, nom_categorie FROM {$db->table_categorie} ORDER BY nom_categorie ASC");

$is_online = $is_edit && isset($oeuvre->visible_galerie) && $oeuvre->visible_galerie;

$qr_url = '';
if ($is_edit && !empty($oeuvre->id_oeuvre) && class_exists('MBAA_Oeuvre_Pages')) {
    $qr_url = MBAA_Oeuvre_Pages::get_oeuvre_url((int) $oeuvre->id_oeuvre);
    $qr_table = $wpdb->prefix . 'mbaa_qr_codes';
    $existing_qr = $wpdb->get_var(
        $wpdb->prepare("SELECT url FROM {$qr_table} WHERE id_oeuvre = %d AND type = %s AND statut = %s ORDER BY id_qr DESC LIMIT 1", (int) $oeuvre->id_oeuvre, 'oeuvre', 'actif')
    );
    if (!empty($existing_qr)) {
        $qr_url = $existing_qr;
    }
}
?>


<div class="wrap mbaa-wrap">
    <form method="post" action="" id="mbaa-oeuvre-form" class="mbaa-form">
        <?php wp_nonce_field('mbaa_save_oeuvre', 'mbaa_oeuvre_nonce'); ?>
        <?php if ($is_edit): ?>
            <input type="hidden" name="id_oeuvre" value="<?php echo esc_attr($oeuvre->id_oeuvre); ?>">
        <?php endif; ?>

        <div class="mbaa-main-content">

<div class="mbaa-artwork-header">

<div class="mbaa-artwork-icon">

<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>

</div>

<div class="mbaa-artwork-title-section">
    <h1 class="mbaa-artwork-title"><?php echo $is_edit ? esc_html($oeuvre->titre) : 'Nouvelle oeuvre'; ?></h1>
        <span class="mbaa-status-badge <?php echo $is_online ? '' : 'offline'; ?>">
            <?php echo $is_online ? 'En ligne' : 'Hors ligne'; ?>
        </span>
</div>

<div class="mbaa-action-buttons">
                    <a href="<?php echo admin_url('admin.php?page=mbaa-oeuvres'); ?>" class="mbaa-action-button" title="Retour">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19 12H5M5 12L12 19M5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                    <?php if ($is_edit): ?>
                    <button type="button" class="mbaa-action-button danger" title="Supprimer" onclick="if(confirm('Êtes-vous sûr de vouloir supprimer cette œuvre ?')) { window.location.href = '<?php echo wp_nonce_url(admin_url('admin.php?page=mbaa-oeuvres&action=delete&id=' . $oeuvre->id_oeuvre), 'delete_oeuvre_' . $oeuvre->id_oeuvre, '_wpnonce'); ?>'; }">
                        <span class="dashicons dashicons-trash"></span>
                    </button>
                    <?php endif; ?>
                </div>
            </div>

            <div class="mbaa-form-grid">
                <!-- Informations principales -->
                <div class="mbaa-form-section">
                    <h2 class="mbaa-section-title">Informations principales</h2>
                    
                    <div class="mbaa-form-group">
                        <label class="mbaa-form-label">Titre <span class="mbaa-required">*</span></label>
                        <input type="text" name="titre" id="titre" class="mbaa-form-input" 
                               value="<?php echo $is_edit ? esc_attr($oeuvre->titre) : ''; ?>" 
                               placeholder="Titre de l'œuvre" required>
                    </div>

                    <div class="mbaa-form-group">
                        <label class="mbaa-form-label">Artiste <span class="mbaa-required">*</span></label>
                        <input type="text" id="nom_artiste" name="nom_artiste" class="mbaa-form-input" 
                               placeholder="Tapez le nom de l'artiste..." 
                               value="<?php echo $is_edit ? esc_html($oeuvre->nom_artiste ?? '') : ''; ?>" 
                               autocomplete="off" required>
                        <div id="suggestions" class="suggestions-container"></div>
                        <!-- Champ select caché pour l'ID de l'artiste -->
                        <select name="id_artiste" id="id_artiste" class="mbaa-form-input" style="display: none;" required>
                            <option value="">-- Sélectionner un artiste --</option>
                            <?php foreach ($artistes as $artiste): ?>
                                <option value="<?php echo esc_attr($artiste->id_artiste); ?>"
                                    <?php selected($is_edit && $oeuvre->id_artiste == $artiste->id_artiste); ?>>
                                    <?php echo esc_html($artiste->nom); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mbaa-form-group">
                        <label class="mbaa-form-label">Date de création</label>
                        <input type="text" name="date_creation" id="date_creation" class="mbaa-form-input" 
                               value="<?php echo $is_edit ? esc_attr($oeuvre->date_creation) : ''; ?>"
                               placeholder="Ex: 1889, XVIIe siècle, années 1920">
                        <p class="mbaa-form-hint">Peut être une année, un siècle, une période approximative</p>
                    </div>

                    <div class="mbaa-form-group">
                        <label class="mbaa-form-label">Technique <span class="mbaa-required">*</span></label>
                        <input type="text" name="technique" id="technique" class="mbaa-form-input" 
                               value="<?php echo $is_edit ? esc_attr($oeuvre->technique) : ''; ?>"
                               placeholder="Ex: Peinture à l'huile" required>
                    </div>

                    <div class="mbaa-form-group">
                        <label class="mbaa-form-label">Support / Médium <span class="mbaa-required">*</span></label>
                        <select name="id_medium" id="id_medium" class="mbaa-form-input" required>
                            <option value="">-- Sélectionner un support --</option>
                            <?php foreach ($mediums as $medium): ?>
                                <option value="<?php echo esc_attr($medium->id_medium); ?>"
                                    <?php selected($is_edit && $oeuvre->id_medium == $medium->id_medium); ?>>
                                    <?php echo esc_html($medium->nom_medium); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mbaa-form-group">
                        <label class="mbaa-form-label">Provenance</label>
                        <input type="text" name="provenance" id="provenance" class="mbaa-form-input" 
                               value="<?php echo $is_edit && isset($oeuvre->provenance) ? esc_attr($oeuvre->provenance) : ''; ?>"
                               placeholder="Ex: Don de l'artiste, 1950">
                    </div>

                    <div class="mbaa-form-group">
                        <label class="mbaa-form-label">Dimensions</label>
                        <input type="text" name="dimensions" id="dimensions" class="mbaa-form-input" 
                               value="<?php echo $is_edit ? esc_attr($oeuvre->dimensions) : ''; ?>"
                               placeholder="Ex: 73 × 92 cm">
                        <p class="mbaa-form-hint">Format : Hauteur × Largeur (en cm)</p>
                    </div>
                </div>

                <!-- Classification et localisation -->
                <div class="mbaa-form-section">
                    <h2 class="mbaa-section-title">Classification et localisation</h2>
                    
                    <div class="mbaa-form-group">
                        <label class="mbaa-form-label">Époque</label>
                        <select name="id_epoque" id="id_epoque" class="mbaa-form-input">
                            <option value="">-- Sélectionner une époque --</option>
                            <?php foreach ($epoques as $epoque): ?>
                                <option value="<?php echo esc_attr($epoque->id_epoque); ?>"
                                    <?php selected($is_edit && $oeuvre->id_epoque == $epoque->id_epoque); ?>>
                                    <?php echo esc_html($epoque->nom_epoque); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mbaa-form-group">
                        <label class="mbaa-form-label">Mouvement artistique</label>
                        <select name="id_mouvement" id="id_mouvement" class="mbaa-form-input">
                            <option value="">-- Sélectionner un mouvement --</option>
                            <?php foreach ($mouvements as $mouvement): ?>
                                <option value="<?php echo esc_attr($mouvement->id_mouvement); ?>"
                                    <?php selected($is_edit && $oeuvre->id_mouvement == $mouvement->id_mouvement); ?>>
                                    <?php echo esc_html($mouvement->nom_mouvement); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mbaa-form-group">
                        <label class="mbaa-form-label">Catégorie</label>
                        <select name="id_categorie" id="id_categorie" class="mbaa-form-input">
                            <option value="">-- Sélectionner une catégorie --</option>
                            <?php foreach ($categories as $categorie): ?>
                                <option value="<?php echo esc_attr($categorie->id_categorie); ?>"
                                    <?php selected($is_edit && $oeuvre->id_categorie == $categorie->id_categorie); ?>>
                                    <?php echo esc_html($categorie->nom_categorie); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mbaa-form-group">
                        <label class="mbaa-form-label">Lieu d'exposition (Salle)</label>
                        <select name="id_salle" id="id_salle" class="mbaa-form-input">
                            <option value="">-- Sélectionner une salle --</option>
                            <?php foreach ($salles as $salle): ?>
                                <option value="<?php echo esc_attr($salle->id_salle); ?>"
                                    <?php selected($is_edit && $oeuvre->id_salle == $salle->id_salle); ?>>
                                    <?php echo esc_html($salle->nom_salle); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mbaa-form-group">
                        <label class="mbaa-form-label">Numéro d'inventaire</label>
                        <input type="text" name="numero_inventaire" id="numero_inventaire" class="mbaa-form-input" 
                               value="<?php echo $is_edit ? esc_attr($oeuvre->numero_inventaire) : ''; ?>"
                               placeholder="Ex: INV.889.1.1">
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="mbaa-description-section">
                <h2 class="mbaa-description-title">Description et analyse</h2>
                <p class="mbaa-description-subtitle">Texte de présentation de l'œuvre</p>
                <?php 
                $content = $is_edit ? $oeuvre->description : '';
                wp_editor($content, 'description', array(
                    'textarea_name' => 'description',
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
                        <label class="mbaa-form-label">
                            <?php echo '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:8px;"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>'; ?>
                            Image de l'œuvre
                        </label>
                        <div id="uppy-image-upload" class="mbaa-uppy-area"></div>
                        <input type="hidden" name="image_url" id="image_url" value="<?php echo $is_edit ? esc_attr($oeuvre->image_url) : ''; ?>">
                        <div id="image_preview" style="margin-top: 10px;">
                            <?php if ($is_edit && $oeuvre->image_url): ?>
                                <img src="<?php echo esc_url($oeuvre->image_url); ?>" style="max-width: 100%; height: auto; border-radius: 8px;">
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="mbaa-form-group">
                        <label class="mbaa-form-label">
                            <?php echo '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:8px;"><path d="M3 18v-6a9 9 0 0 1 18 0v6"></path><path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"></path></svg>'; ?>
                            Fichier Audio (Audioguide)
                        </label>
                        <div id="uppy-audio-upload" class="mbaa-uppy-area"></div>
                        <input type="hidden" name="audio_url" id="audio_url" value="<?php echo $is_edit ? esc_attr($oeuvre->audio_url) : ''; ?>">
                        <div id="audio_preview" style="margin-top: 10px;">
                            <?php if ($is_edit && $oeuvre->audio_url): ?>
                                <audio controls style="width: 100%;">
                                    <source src="<?php echo esc_url($oeuvre->audio_url); ?>" type="audio/mpeg">
                                </audio>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Relations manuelles -->
            <?php echo "<!-- DEBUG: Début section Relations -->"; ?>
            <div class="mbaa-form-section">
                <h2 class="mbaa-section-title">Relations et suggestions</h2>
                <p class="mbaa-description-subtitle">Configurez manuellement les œuvres similaires et artistes liés à afficher sur la fiche œuvre</p>
                
                <?php echo "<!-- DEBUG: Début form-grid -->"; ?>
                <div class="mbaa-form-grid" style="gap: 24px;">
                    <!-- Œuvres similaires -->
                    <div class="mbaa-form-group">
                        <label class="mbaa-form-label">Œuvres similaires</label>
                        <p class="mbaa-form-hint">Sélectionnez les œuvres qui seront affichées dans la section "Ses autres œuvres"</p>
                        
                        <?php
                        echo "<!-- DEBUG: Début requête œuvres disponibles -->";
                        // Récupérer toutes les œuvres sauf celle en cours d'édition
                        $oeuvres_disponibles = $wpdb->get_results($wpdb->prepare(
                            "SELECT o.id_oeuvre, o.titre, a.nom as artiste_nom 
                             FROM {$wpdb->prefix}mbaa_oeuvre o
                             LEFT JOIN {$wpdb->prefix}mbaa_artiste a ON o.id_artiste = a.id_artiste
                             WHERE o.visible_galerie = 1" . ($is_edit ? " AND o.id_oeuvre != %d" : "") . "
                             ORDER BY o.titre ASC",
                            $is_edit ? $oeuvre->id_oeuvre : 0
                        ));
                        echo "<!-- DEBUG: " . count($oeuvres_disponibles) . " œuvres trouvées -->";
                        
                        // Récupérer les œuvres similaires déjà sélectionnées
                        $oeuvres_similaires_selectionnees = [];
                        if ($is_edit) {
                            $similaires = $wpdb->get_results($wpdb->prepare(
                                "SELECT oeuvre_similaire_id FROM wp_mbaa_oeuvres_similaires WHERE oeuvre_id = %d ORDER BY ordre",
                                $oeuvre->id_oeuvre
                            ));
                            foreach ($similaires as $sim) {
                                $oeuvres_similaires_selectionnees[] = $sim->oeuvre_similaire_id;
                            }
                        }
                        ?>
                        
                        <div class="mbaa-multi-select-container">
                            <select name="oeuvres_similaires[]" id="oeuvres_similaires" class="mbaa-form-input" multiple style="height: 150px;">
                                <?php foreach ($oeuvres_disponibles as $oeuv): ?>
                                    <option value="<?php echo esc_attr($oeuv->id_oeuvre); ?>"
                                        <?php echo in_array($oeuv->id_oeuvre, $oeuvres_similaires_selectionnees) ? 'selected' : ''; ?>>
                                        <?php echo esc_html($oeuv->titre); ?> - <?php echo esc_html($oeuv->artiste_nom); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="mbaa-form-hint">Maintenez Ctrl/Cmd pour sélectionner plusieurs œuvres</p>
                        </div>
                    </div>

                    <!-- Artistes liés -->
                    <div class="mbaa-form-group">
                        <label class="mbaa-form-label">Artistes liés</label>
                        <p class="mbaa-form-hint">Sélectionnez les artistes qui seront affichés dans la section "Artistes en lien"</p>
                        
                        <?php
                        // Récupérer les artistes liés déjà sélectionnés
                        $artistes_liens_selectionnes = [];
                        if ($is_edit) {
                            $liens = $wpdb->get_results($wpdb->prepare(
                                "SELECT artiste_id FROM wp_mbaa_artistes_liens WHERE oeuvre_id = %d ORDER BY ordre",
                                $oeuvre->id_oeuvre
                            ));
                            foreach ($liens as $lien) {
                                $artistes_liens_selectionnes[] = $lien->artiste_id;
                            }
                        }
                        ?>
                        
                        <div class="mbaa-multi-select-container">
                            <select name="artistes_liens[]" id="artistes_liens" class="mbaa-form-input" multiple style="height: 150px;">
                                <?php foreach ($artistes as $artiste): ?>
                                    <?php if (!$is_edit || $artiste->id_artiste != $oeuvre->id_artiste): ?>
                                        <option value="<?php echo esc_attr($artiste->id_artiste); ?>"
                                            <?php echo in_array($artiste->id_artiste, $artistes_liens_selectionnes) ? 'selected' : ''; ?>>
                                            <?php echo esc_html($artiste->nom); ?>
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                            <p class="mbaa-form-hint">Maintenez Ctrl/Cmd pour sélectionner plusieurs artistes</p>
                        </div>
                    </div>
                </div>

                <!-- Thèmes -->
                <div class="mbaa-form-group">
                    <label class="mbaa-form-label">Thèmes et mots-clés</label>
                    <p class="mbaa-form-hint">Sélectionnez les thèmes associés à cette œuvre</p>
                    
                    <?php
                    // Récupérer les thèmes
                    $themes = $wpdb->get_results("SELECT id_theme, nom_theme FROM wp_mbaa_themes ORDER BY nom_theme ASC");
                    
                    // Récupérer les thèmes déjà sélectionnés
                    $themes_selectionnes = [];
                    if ($is_edit) {
                        $th = $wpdb->get_results($wpdb->prepare(
                            "SELECT theme_id FROM wp_mbaa_oeuvre_themes WHERE oeuvre_id = %d",
                            $oeuvre->id_oeuvre
                        ));
                        foreach ($th as $t) {
                            $themes_selectionnes[] = $t->theme_id;
                        }
                    }
                    ?>
                    
                    <div class="mbaa-multi-select-container">
                        <select name="themes[]" id="themes" class="mbaa-form-input" multiple style="height: 120px;">
                            <?php foreach ($themes as $theme): ?>
                                <option value="<?php echo esc_attr($theme->id_theme); ?>"
                                    <?php echo in_array($theme->id_theme, $themes_selectionnes) ? 'selected' : ''; ?>>
                                    <?php echo esc_html($theme->nom_theme); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="mbaa-form-hint">Maintenez Ctrl/Cmd pour sélectionner plusieurs thèmes</p>
                    </div>
                </div>
            </div>
            <?php echo "<!-- DEBUG: Fin section Relations -->"; ?>

            <!-- QR Code -->
            <div class="mbaa-qr-section mbaa-form-section" style="background: #f8f9fa; border: 2px solid #c9a227; margin-bottom: 2rem;">
                <h2 class="mbaa-section-title mbaa-qr-title" style="color: #c9a227;">📱 QR Code</h2>
                <p class="mbaa-qr-description" style="margin-bottom: 20px;">Générez un QR code pour cette œuvre. Les visiteurs pourront scanner ce code pour accéder à la page de l'œuvre.</p>
                
                <div class="mbaa-qr-controls" style="display: flex; gap: 20px; flex-wrap: wrap;">
                    <div class="mbaa-qr-url-wrapper" style="flex: 1; min-width: 250px;">
                        <label class="mbaa-form-label mbaa-qr-url-label">URL du QR code:</label>
                        <input type="url" id="mbaa_qr_url" class="mbaa-form-input mbaa-qr-url-input" placeholder="https://votre-site.com/oeuvre/..." value="<?php echo esc_attr($qr_url ?? ''); ?>">
                        <p class="mbaa-form-hint mbaa-qr-url-hint">Entrez l'URL publique de cette œuvre</p>
                    </div>
                    <div class="mbaa-qr-size-wrapper" style="min-width: 120px;">
                        <label class="mbaa-form-label mbaa-qr-size-label">Taille:</label>
                        <select id="mbaa_qr_size" class="mbaa-form-input mbaa-qr-size-select">
                            <option value="150">150px</option>
                            <option value="200">200px</option>
                            <option value="250" selected>250px</option>
                            <option value="300">300px</option>
                            <option value="400">400px</option>
                        </select>
                    </div>
                    <div class="mbaa-qr-actions">
                        <button type="button" id="mbaa_generate_qr_btn" class="button button-primary mbaa-qr-generate-btn" style="background: #c9a227; border-color: #c9a227;">
                         Générer le QR Code
                        </button>
                    </div>
                </div>
                
                <div id="mbaa_qr_preview_container" class="mbaa-qr-preview-container" style="display: none; margin-top: 20px; padding: 20px; background: #fff; border-radius: 8px; text-align: center;">
                    <canvas id="mbaa_qr_canvas" class="mbaa-qr-canvas"></canvas>
                    <div class="mbaa-qr-buttons" style="margin-top: 15px;">
                        <button type="button" id="mbaa_download_qr_btn" class="button button-primary mbaa-qr-download-btn">
                             Télécharger PNG
                        </button>
                        <button type="button" id="mbaa_save_qr_btn" class="button mbaa-qr-save-btn">
                             Enregistrer
                        </button>
                    </div>
                    <p id="mbaa_qr_status" class="mbaa-qr-status" style="margin-top: 10px; color: #666; font-size: 12px;"></p>
                </div>
            </div>

            <script>
            jQuery(document).ready(function($) {
                var currentQrData = null;
                
                // Generate QR code
                $('#mbaa_generate_qr_btn').on('click', function() {
                    var url = $('#mbaa_qr_url').val();
                    var size = parseInt($('#mbaa_qr_size').val());
                    
                    if (!url) {
                        alert('Veuillez entrer une URL');
                        return;
                    }
                    
                    // Validate URL
                    try {
                        new URL(url);
                    } catch(e) {
                        alert('Veuillez entrer une URL valide');
                        return;
                    }
                    
                    // Generate with QRCode.js
                    var canvas = document.getElementById('mbaa_qr_canvas');
                    canvas.width = size;
                    canvas.height = size;
                    
                    QRCode.toCanvas(canvas, url, {
                        width: size,
                        margin: 2,
                        color: {
                            dark: '#000000',
                            light: '#ffffff'
                        }
                    }, function(error) {
                        if (error) {
                            console.error(error);
                            alert('Erreur lors de la génération du QR code');
                        } else {
                            $('#mbaa_qr_preview_container').show();
                            currentQrData = { url: url, size: size };
                            $('#mbaa_qr_status').text('QR code généré ! Vous pouvez le télécharger ou l\'enregistrer.');
                        }
                    });
                });
                
                // Download QR code
                $('#mbaa_download_qr_btn').on('click', function() {
                    if (!currentQrData) return;
                    
                    var canvas = document.getElementById('mbaa_qr_canvas');
                    var link = document.createElement('a');
                    link.download = 'qrcode-oeuvre.png';
                    link.href = canvas.toDataURL('image/png');
                    link.click();
                });
                
                // Save QR code to database
                $('#mbaa_save_qr_btn').on('click', function() {
                    if (!currentQrData) return;
                    
                    var id_oeuvre = $('input[name="id_oeuvre"]').val();
                    if (!id_oeuvre) {
                        alert('Veuillez d\'abord enregistrer l\'œuvre');
                        return;
                    }
                    
                    $.ajax({
                        url: mbaaAjax.ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'mbaa_save_qr',
                            nonce: (mbaaAjax.upload_nonce || mbaaAjax.nonce),
                            id_oeuvre: id_oeuvre,
                            url: currentQrData.url
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#mbaa_qr_status').html('<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:4px;"><path d="M20 6L9 17l-5-5"></path></svg> ' + response.data.message);
                            } else {
                                $('#mbaa_qr_status').text('Erreur: ' + response.data);
                            }
                        },
                        error: function() {
                            $('#mbaa_qr_status').text('Erreur de communication');
                        }
                    });
                });
            });
            </script>

            <!-- Options de visibilité -->
            <div class="mbaa-form-section">
                <h2 class="mbaa-section-title">Paramètres d'affichage</h2>
                <div class="mbaa-form-group">
                    <label>
                        <input type="checkbox" name="visible_galerie" <?php checked($is_edit ? $oeuvre->visible_galerie : 1); ?>>
                        Visible dans la galerie publique
                    </label>
                </div>
                <div class="mbaa-form-group">
                    <label>
                        <input type="checkbox" name="visible_accueil" <?php checked($is_edit ? $oeuvre->visible_accueil : 0); ?>>
                        Mettre en avant sur la page d'accueil
                    </label>
                </div>
            </div>

            <!-- Actions -->
            <div class="mbaa-bottom-actions" style="margin-top: 30px; display: flex; gap: 10px;">
                <button type="submit" name="submit" class="button button-primary button-large">Enregistrer l'œuvre</button>
                <a href="<?php echo admin_url('admin.php?page=mbaa-oeuvres'); ?>" class="button button-secondary button-large">Annuler</a>
            </div>
        </div>
    </form>
</div>



<script>
jQuery(document).ready(function($) {
    console.log('MBAA: Script chargé');
    
    // Vérifier si mbaaAjax est défini
    if (typeof mbaaAjax === 'undefined') {
        console.error('MBAA: mbaaAjax non défini');
        // Définir manuellement pour tester
        mbaaAjax = {
            ajaxurl: '<?php echo admin_url('admin-ajax.php'); ?>',
            nonce: '<?php echo wp_create_nonce('mbaa_nonce'); ?>'
        };
        console.log('MBAA: mbaaAjax défini manuellement:', mbaaAjax);
    }
    
    // Script de recherche d'artistes
    const inputArtiste = $('#nom_artiste');
    const suggestionsDiv = $('#suggestions');
    const selectArtiste = $('#id_artiste');
    
    console.log('MBAA: Éléments trouvés:', {
        input: inputArtiste.length,
        suggestions: suggestionsDiv.length,
        select: selectArtiste.length
    });
    
    inputArtiste.on('input', function() {
        const recherche = $(this).val();
        console.log('Recherche:', recherche);
        
        if (recherche.length >= 2) {
            console.log('Appel AJAX pour:', recherche);
            $.ajax({
                url: mbaaAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'mbaa_recherche_artiste',
                    nom: recherche,
                    nonce: mbaaAjax.nonce
                },
                dataType: 'json',
                success: function(response) {
                    console.log('Réponse AJAX:', response);
                    if (response.success) {
                        afficherSuggestions(response.data);
                    } else {
                        console.error('Erreur AJAX:', response.data);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Erreur AJAX complète:', error, xhr.responseText);
                    // Essayer de parser la réponse manuellement
                    try {
                        var responseText = xhr.responseText;
                        // Extraire le JSON de la réponse (enlever le HTML parasite)
                        var jsonMatch = responseText.match(/\{.*\}/);
                        if (jsonMatch) {
                            var response = JSON.parse(jsonMatch[0]);
                            console.log('Réponse JSON extraite:', response);
                            if (response.success) {
                                afficherSuggestions(response.data);
                            }
                        } else {
                            console.error('Aucun JSON trouvé dans la réponse');
                        }
                    } catch(e) {
                        console.error('Impossible de parser la réponse:', e, xhr.responseText);
                    }
                }
            });
        } else {
            suggestionsDiv.empty();
        }
    });
    
    function afficherSuggestions(artistes) {
        suggestionsDiv.empty();
        
        artistes.forEach(artiste => {
            const div = $('<div>')
                .addClass('suggestion-item')
                .text(artiste.nom)
                .on('click', function() {
                    inputArtiste.val(artiste.nom);
                    selectArtiste.val(artiste.id_artiste);
                    suggestionsDiv.empty();
                    console.log('MBAA: Artiste sélectionné:', artiste);
                });
            suggestionsDiv.append(div);
        });
    }
    
    // Fermer les suggestions en cliquant ailleurs
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.mbaa-form-group').length) {
            suggestionsDiv.empty();
        }
    });
});
</script>

<?php
// Charger Uppy seulement si nécessaire
if (class_exists('MBAA_Uppy_Integration')) {
    ?>
<script>
jQuery(document).ready(function($) {
    // Vérifier si Uppy est disponible
    if (typeof Uppy === 'undefined') {
        console.log('MBAA: Uppy non disponible, skip upload');
        return;
    }
    
    // Initialiser Uppy pour les images
    var uppyImage = new Uppy.Core({
        restrictions: {
            maxFileSize: 100 * 1024 * 1024, // 100 MB
            allowedFileTypes: ['image/*']
        }
    });
    
    uppyImage.use(Uppy.Dashboard, {
        inline: true,
        target: '#uppy-image-upload',
        width: '100%',
        height: 250,
        showProgressDetails: true,
        note: 'Images uniquement (JPG, PNG, WebP, TIFF)',
        proudlyDisplayPoweredByUppy: false
    });
    
    uppyImage.use(Uppy.XHRUpload, {
        endpoint: mbaaAjax.ajaxurl + '?action=mbaa_upload_media&nonce=' + mbaaAjax.upload_nonce,
        formData: true,
        fieldName: 'files[]'
    });
    
    uppyImage.on('upload-success', function(file, response) {
        if (response.body && response.body.success) {
            var data = response.body.data;
            // Gérer le cas où plusieurs fichiers sont uploadés
            if (Array.isArray(data)) {
                data = data[0];
            }
            if (!data.error) {
                $('#image_url').val(data.url);
                $('#image_preview').html('<img src="' + data.url + '" style="max-width: 100%; height: auto; border-radius: 8px;">');
            } else {
                alert(data.error);
            }
        }
    });
    
    uppyImage.on('error', function(error) {
        console.error('Uppy error:', error);
    });
});
</script>
<?php } ?>



