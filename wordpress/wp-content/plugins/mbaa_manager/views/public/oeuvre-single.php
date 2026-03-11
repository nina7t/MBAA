<?php
/**
 * Template: Page individuelle d'une œuvre
 * URL: /oeuvre/{id}/
 */

if (!defined('ABSPATH')) {
    exit;
}

// Récupérer l'ID de l'œuvre depuis l'URL
$oeuvre_id = get_query_var('mbaa_oeuvre_id');

if (!$oeuvre_id) {
    wp_die(__('Œuvre non trouvée.', 'mbaa'));
}

// Récupérer les données de l'œuvre
$oeuvre = MBAA_Oeuvre_Pages::get_oeuvre_data($oeuvre_id);

if (!$oeuvre) {
    wp_die(__('Œuvre non trouvée.', 'mbaa'));
}

// Tracking des scans si activé
$db = new MBAA_Database();
$qr_table = $wpdb->prefix . 'mbaa_qr_codes';

// Récupérer le QR code associé pour le tracking
$qr_code = $wpdb->get_row(
    $wpdb->prepare("SELECT * FROM {$qr_table} WHERE id_oeuvre = %d AND statut = 'actif'", $oeuvre_id)
);

// Enregistrer le scan si QR code existant
if ($qr_code) {
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
    $db->record_scan($qr_code->id_qr, $ip_address, $user_agent);
}

// Incrémenter les vues de l'œuvre
$wpdb->query(
    $wpdb->prepare("UPDATE {$db->table_oeuvre} SET vues = vues + 1 WHERE id_oeuvre = %d", $oeuvre_id)
);

get_header();
?>

<div class="mbaa-oeuvre-single">
    <!-- En-tête avec image -->
    <div class="mbaa-oeuvre-hero">
        <?php if (!empty($oeuvre['image_url'])): ?>
            <div class="mbaa-oeuvre-hero-image">
                <img src="<?php echo esc_url($oeuvre['image_url']); ?>" alt="<?php echo esc_attr($oeuvre['titre']); ?>">
            </div>
        <?php endif; ?>
        
        <div class="mbaa-oeuvre-hero-overlay"></div>
        
        <div class="mbaa-oeuvre-hero-content">
            <h1 class="mbaa-oeuvre-title"><?php echo esc_html($oeuvre['titre']); ?></h1>
            <?php if (!empty($oeuvre['artiste_nom'])): ?>
                <p class="mbaa-oeuvre-artiste">
                    <span class="mbaa-label">Artiste:</span>
                    <?php echo esc_html($oeuvre['artiste_nom']); ?>
                </p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Informations principales -->
    <div class="mbaa-oeuvre-container">
        <div class="mbaa-oeuvre-grid">
            <!-- Colonne gauche: Détails -->
            <div class="mbaa-oeuvre-details">
                <div class="mbaa-oeuvre-section">
                    <h2 class="mbaa-section-title">Informations</h2>
                    
                    <div class="mbaa-oeuvre-info-list">
                        <?php if (!empty($oeuvre['date_creation'])): ?>
                            <div class="mbaa-oeuvre-info-item">
                                <span class="mbaa-info-label">Date de création:</span>
                                <span class="mbaa-info-value"><?php echo esc_html($oeuvre['date_creation']); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($oeuvre['technique'])): ?>
                            <div class="mbaa-oeuvre-info-item">
                                <span class="mbaa-info-label">Technique:</span>
                                <span class="mbaa-info-value"><?php echo esc_html($oeuvre['technique']); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($oeuvre['medium_nom'])): ?>
                            <div class="mbaa-oeuvre-info-item">
                                <span class="mbaa-info-label">Support/Médium:</span>
                                <span class="mbaa-info-value"><?php echo esc_html($oeuvre['medium_nom']); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($oeuvre['dimensions'])): ?>
                            <div class="mbaa-oeuvre-info-item">
                                <span class="mbaa-info-label">Dimensions:</span>
                                <span class="mbaa-info-value"><?php echo esc_html($oeuvre['dimensions']); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($oeuvre['numero_inventaire'])): ?>
                            <div class="mbaa-oeuvre-info-item">
                                <span class="mbaa-info-label">N° Inventaire:</span>
                                <span class="mbaa-info-value"><?php echo esc_html($oeuvre['numero_inventaire']); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($oeuvre['provenance'])): ?>
                            <div class="mbaa-oeuvre-info-item">
                                <span class="mbaa-info-label">Provenance:</span>
                                <span class="mbaa-info-value"><?php echo esc_html($oeuvre['provenance']); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($oeuvre['epoque_nom'])): ?>
                            <div class="mbaa-oeuvre-info-item">
                                <span class="mbaa-info-label">Époque:</span>
                                <span class="mbaa-info-value"><?php echo esc_html($oeuvre['epoque_nom']); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($oeuvre['mouvement_nom'])): ?>
                            <div class="mbaa-oeuvre-info-item">
                                <span class="mbaa-info-label">Mouvement:</span>
                                <span class="mbaa-info-value"><?php echo esc_html($oeuvre['mouvement_nom']); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($oeuvre['salle_nom'])): ?>
                            <div class="mbaa-oeuvre-info-item">
                                <span class="mbaa-info-label">Localisation:</span>
                                <span class="mbaa-info-value">
                                    <?php echo esc_html($oeuvre['salle_nom']); ?>
                                    <?php if (!empty($oeuvre['salle_etage'])): ?>
                                        (<?php echo esc_html($oeuvre['salle_etage']); ?>)
                                    <?php endif; ?>
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Section Description -->
                <?php if (!empty($oeuvre['description'])): ?>
                    <div class="mbaa-oeuvre-section">
                        <h2 class="mbaa-section-title">Description et analyse</h2>
                        <div class="mbaa-oeuvre-description">
                            <?php echo wp_kses_post($oeuvre['description']); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Colonne droite: Sidebar -->
            <div class="mbaa-oeuvre-sidebar">
                <!-- QR Code -->
                <?php if ($qr_code): ?>
                    <div class="mbaa-oeuvre-qr-section">
                        <h3><?php echo '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:8px;"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>'; ?>Scanner ce QR code</h3>
                        <p>Scannez ce code pour retrouver cette œuvre sur votre téléphone.</p>
                        <div class="mbaa-qr-display">
                            <canvas id="qr-display-canvas"></canvas>
                        </div>
                        <a href="<?php echo esc_url($qr_code->url); ?>" class="button button-primary" target="_blank">
                            <?php echo '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:4px;"><path d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>'; ?>Ouvrir le lien
                        </a>
                        <?php if ($qr_code->scans_total > 0): ?>
                            <p class="mbaa-qr-stats">
                                <small><?php echo number_format($qr_code->scans_total); ?> scans</small>
                            </p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Audio guide -->
                <?php if (!empty($oeuvre['audio_url'])): ?>
                    <div class="mbaa-oeuvre-audio-section">
                        <h3><?php echo '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:8px;"><path d="M3 18v-6a9 9 0 0 1 18 0v6"></path><path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"></path></svg>'; ?>Audioguide</h3>
                        <audio controls class="mbaa-audio-player">
                            <source src="<?php echo esc_url($oeuvre['audio_url']); ?>" type="audio/mpeg">
                            Votre navigateur ne supporte pas l'audio.
                        </audio>
                    </div>
                <?php endif; ?>

                <!-- Partage -->
                <div class="mbaa-oeuvre-share-section">
                    <h3>Partager cette œuvre</h3>
                    <div class="mbaa-share-buttons">
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(MBAA_Oeuvre_Pages::get_oeuvre_url($oeuvre_id)); ?>" 
                           class="mbaa-share-btn mbaa-share-facebook" 
                           target="_blank" 
                           rel="noopener">
                            Facebook
                        </a>
                        <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(MBAA_Oeuvre_Pages::get_oeuvre_url($oeuvre_id)); ?>" 
                           class="mbaa-share-btn mbaa-share-twitter" 
                           target="_blank" 
                           rel="noopener">
                            Twitter
                        </a>
                        <a href="mailto:?subject=<?php echo urlencode('Œuvre: ' . $oeuvre['titre']); ?>&body=<?php echo urlencode('Découvrez cette œuvre: ' . MBAA_Oeuvre_Pages::get_oeuvre_url($oeuvre_id)); ?>" 
                           class="mbaa-share-btn mbaa-share-email">
                            Email
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Artiste -->
    <?php if (!empty($oeuvre['artiste_nom'])): ?>
        <div class="mbaa-oeuvre-artiste-section">
            <div class="mbaa-oeuvre-container">
                <h2 class="mbaa-section-title">À propos de l'artiste</h2>
                <div class="mbaa-artiste-card">
                    <?php if (!empty($oeuvre['artiste_image'])): ?>
                        <div class="mbaa-artiste-image">
                            <img src="<?php echo esc_url($oeuvre['artiste_image']); ?>" alt="<?php echo esc_attr($oeuvre['artiste_nom']); ?>">
                        </div>
                    <?php endif; ?>
                    <div class="mbaa-artiste-info">
                        <h3><?php echo esc_html($oeuvre['artiste_nom']); ?></h3>
                        <?php if (!empty($oeuvre['artiste_biographie'])): ?>
                            <p><?php echo wp_kses_post($oeuvre['artiste_biographie']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Script pour afficher le QR code -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if ($qr_code): ?>
    // Afficher le QR code côté client
    if (typeof QRCode !== 'undefined') {
        var canvas = document.getElementById('qr-display-canvas');
        if (canvas) {
            QRCode.toCanvas(canvas, '<?php echo esc_js($qr_code->url); ?>', {
                width: 200,
                margin: 2,
                color: {
                    dark: '#000000',
                    light: '#ffffff'
                }
            });
        }
    }
    <?php endif; ?>
});
</script>

<style>
/* Styles pour la page œuvre */
.mbaa-oeuvre-single {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    color: #333;
    line-height: 1.6;
}

.mbaa-oeuvre-hero {
    position: relative;
    height: 400px;
    overflow: hidden;
    background: #1a1a1a;
}

.mbaa-oeuvre-hero-image img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    object-position: center;
}

.mbaa-oeuvre-hero-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 200px;
    background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
}

.mbaa-oeuvre-hero-content {
    position: absolute;
    bottom: 40px;
    left: 40px;
    right: 40px;
    color: #fff;
}

.mbaa-oeuvre-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0 0 10px;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
}

.mbaa-oeuvre-artiste {
    font-size: 1.25rem;
    margin: 0;
    opacity: 0.9;
}

.mbaa-oeuvre-artiste .mbaa-label {
    font-weight: 400;
    opacity: 0.8;
}

.mbaa-oeuvre-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 40px 20px;
}

.mbaa-oeuvre-grid {
    display: grid;
    grid-template-columns: 1fr 350px;
    gap: 40px;
}

.mbaa-oeuvre-section {
    margin-bottom: 40px;
}

.mbaa-section-title {
    font-size: 1.5rem;
    font-weight: 600;
    margin: 0 0 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #c9a227;
}

.mbaa-oeuvre-info-list {
    display: grid;
    gap: 15px;
}

.mbaa-oeuvre-info-item {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid #eee;
}

.mbaa-info-label {
    font-weight: 600;
    color: #666;
}

.mbaa-info-value {
    text-align: right;
}

.mbaa-oeuvre-description {
    font-size: 1.1rem;
    line-height: 1.8;
}

/* Sidebar */
.mbaa-oeuvre-sidebar {
    display: flex;
    flex-direction: column;
    gap: 30px;
}

.mbaa-oeuvre-sidebar > div {
    background: #f9f9f9;
    padding: 25px;
    border-radius: 12px;
}

.mbaa-oeuvre-sidebar h3 {
    margin: 0 0 15px;
    font-size: 1.1rem;
}

.mbaa-qr-display {
    text-align: center;
    margin: 15px 0;
}

.mbaa-qr-display canvas {
    max-width: 100%;
    border-radius: 8px;
}

.mbaa-qr-stats {
    text-align: center;
    color: #666;
    margin-top: 10px;
}

.mbaa-audio-player {
    width: 100%;
    margin-top: 10px;
}

.mbaa-share-buttons {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.mbaa-share-btn {
    flex: 1;
    min-width: 80px;
    padding: 10px 15px;
    text-align: center;
    text-decoration: none;
    border-radius: 6px;
    font-size: 0.9rem;
    color: #fff;
    transition: opacity 0.3s;
}

.mbaa-share-btn:hover {
    opacity: 0.8;
}

.mbaa-share-facebook { background: #1877f2; }
.mbaa-share-twitter { background: #1da1f2; }
.mbaa-share-email { background: #666; }

/* Section Artiste */
.mbaa-oeuvre-artiste-section {
    background: #f5f5f5;
    padding: 60px 0;
}

.mbaa-artiste-card {
    display: flex;
    gap: 30px;
    align-items: flex-start;
}

.mbaa-artiste-image {
    flex-shrink: 0;
    width: 150px;
    height: 150px;
    border-radius: 50%;
    overflow: hidden;
}

.mbaa-artiste-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.mbaa-artiste-info h3 {
    margin: 0 0 10px;
    font-size: 1.5rem;
}

/* Responsive */
@media (max-width: 768px) {
    .mbaa-oeuvre-hero {
        height: 250px;
    }
    
    .mbaa-oeuvre-hero-content {
        left: 20px;
        right: 20px;
        bottom: 20px;
    }
    
    .mbaa-oeuvre-title {
        font-size: 1.75rem;
    }
    
    .mbaa-oeuvre-grid {
        grid-template-columns: 1fr;
    }
    
    .mbaa-artiste-card {
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
}
</style>

<?php get_footer(); ?>

