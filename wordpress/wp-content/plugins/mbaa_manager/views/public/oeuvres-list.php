<?php
/**
 * Template: Liste des œuvres
 * URL: /oeuvres/
 */

if (!defined('ABSPATH')) {
    exit;
}

// Récupérer les œuvres visibles dans la galerie
$db = new MBAA_Database();
$oeuvres = $db->get_all_oeuvres_with_relations(1); // 1 = visible_galerie

get_header();
?>

<div class="mbaa-oeuvres-list">
    <div class="mbaa-oeuvres-header">
        <div class="mbaa-oeuvres-header-content">
            <h1 class="mbaa-oeuvres-title"><?php _e('Collection du musée', 'mbaa'); ?></h1>
            <p class="mbaa-oeuvres-subtitle"><?php _e('Découvrez les œuvres de notre collection permanente', 'mbaa'); ?></p>
        </div>
    </div>

    <div class="mbaa-oeuvres-container">
        <!-- Filtres -->
        <div class="mbaa-oeuvres-filters">
            <div class="mbaa-filter-group">
                <label for="filter-epoque"><?php _e('Époque', 'mbaa'); ?></label>
                <select id="filter-epoque" class="mbaa-filter-select">
                    <option value=""><?php _e('Toutes les époques', 'mbaa'); ?></option>
                    <?php
                    $epoques = $db->get_all_epoques();
                    foreach ($epoques as $epoque) {
                        echo '<option value="' . esc_attr($epoque['id_epoque']) . '">' . esc_html($epoque['nom_epoque']) . '</option>';
                    }
                    ?>
                </select>
            </div>
            
            <div class="mbaa-filter-group">
                <label for="filter-categorie"><?php _e('Catégorie', 'mbaa'); ?></label>
                <select id="filter-categorie" class="mbaa-filter-select">
                    <option value=""><?php _e('Toutes les catégories', 'mbaa'); ?></option>
                    <?php
                    $categories = $db->get_all_categories();
                    foreach ($categories as $cat) {
                        echo '<option value="' . esc_attr($cat['id_categorie']) . '">' . esc_html($cat['nom_categorie']) . '</option>';
                    }
                    ?>
                </select>
            </div>
            
            <div class="mbaa-filter-group">
                <label for="filter-artiste"><?php _e('Artiste', 'mbaa'); ?></label>
                <select id="filter-artiste" class="mbaa-filter-select">
                    <option value=""><?php _e('Tous les artistes', 'mbaa'); ?></option>
                    <?php
                    $artistes = $db->get_all_artistes();
                    foreach ($artistes as $artiste) {
                        echo '<option value="' . esc_attr($artiste['id_artiste']) . '">' . esc_html($artiste['nom']) . '</option>';
                    }
                    ?>
                </select>
            </div>
            
            <div class="mbaa-search-group">
                <input type="text" id="mbaa-search" class="mbaa-search-input" 
                       placeholder="<?php _e('Rechercher une œuvre...', 'mbaa'); ?>">
            </div>
        </div>

        <!-- Grille des œuvres -->
        <div class="mbaa-oeuvres-grid" id="mbaa-oeuvres-grid">
            <?php if (!empty($oeuvres)): ?>
                <?php foreach ($oeuvres as $oeuvre): ?>
                    <?php 
                    $oeuvre_url = MBAA_Oeuvre_Pages::get_oeuvre_url($oeuvre['id_oeuvre']);
                    ?>
                    <article class="mbaa-oeuvre-card" data-epoque="<?php echo esc_attr($oeuvre['id_epoque']); ?>" 
                             data-categorie="<?php echo esc_attr($oeuvre['id_categorie']); ?>"
                             data-artiste="<?php echo esc_attr($oeuvre['id_artiste']); ?>">
                        <a href="<?php echo esc_url($oeuvre_url); ?>" class="mbaa-oeuvre-card-link">
                            <div class="mbaa-oeuvre-card-image">
                                <?php if (!empty($oeuvre['image_url'])): ?>
                                    <img src="<?php echo esc_url($oeuvre['image_url']); ?>" 
                                         alt="<?php echo esc_attr($oeuvre['titre']); ?>"
                                         loading="lazy">
                                <?php else: ?>
                                    <div class="mbaa-oeuvre-placeholder">
                                        <span class="dashicons dashicons-art"></span>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Badge QR Code -->
                                <div class="mbaa-oeuvre-qr-badge" title="Voir le QR code">
                                    <span class="dashicons dashicons-qrcode"></span>
                                </div>
                            </div>
                            
                            <div class="mbaa-oeuvre-card-content">
                                <h2 class="mbaa-oeuvre-card-title"><?php echo esc_html($oeuvre['titre']); ?></h2>
                                
                                <?php if (!empty($oeuvre['artiste_nom'])): ?>
                                    <p class="mbaa-oeuvre-card-artist"><?php echo esc_html($oeuvre['artiste_nom']); ?></p>
                                <?php endif; ?>
                                
                                <div class="mbaa-oeuvre-card-meta">
                                    <?php if (!empty($oeuvre['date_creation'])): ?>
                                        <span class="mbaa-meta-item">
                                            <span class="dashicons dashicons-calendar-alt"></span>
                                            <?php echo esc_html($oeuvre['date_creation']); ?>
                                        </span>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($oeuvre['medium_nom'])): ?>
                                        <span class="mbaa-meta-item">
                                            <span class="dashicons dashicons-art"></span>
                                            <?php echo esc_html($oeuvre['medium_nom']); ?>
                                        </span>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($oeuvre['salle_nom'])): ?>
                                        <span class="mbaa-meta-item">
                                            <span class="dashicons dashicons-location"></span>
                                            <?php echo esc_html($oeuvre['salle_nom']); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="mbaa-oeuvres-empty">
                    <span class="dashicons dashicons-images-alt2"></span>
                    <h3><?php _e('Aucune œuvre trouvée', 'mbaa'); ?></h3>
                    <p><?php _e('Aucune œuvre n\'est actuellement visible dans la galerie.', 'mbaa'); ?></p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Message si aucun résultat après filtrage -->
        <div class="mbaa-no-results" style="display: none;">
            <span class="dashicons dashicons-search"></span>
            <h3><?php _e('Aucun résultat', 'mbaa'); ?></h3>
            <p><?php _e('Aucune œuvre ne correspond à vos critères de recherche.', 'mbaa'); ?></p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filtrage par époque, catégorie, artiste
    var filters = ['filter-epoque', 'filter-categorie', 'filter-artiste'];
    var searchInput = document.getElementById('mbaa-search');
    var grid = document.getElementById('mbaa-oeuvres-grid');
    var cards = grid.querySelectorAll('.mbaa-oeuvre-card');
    var noResults = document.querySelector('.mbaa-no-results');
    
    function filterOeuvres() {
        var activeFilters = {
            epoque: document.getElementById('filter-epoque').value,
            categorie: document.getElementById('filter-categorie').value,
            artiste: document.getElementById('filter-artiste').value,
            search: searchInput.value.toLowerCase()
        };
        
        var visibleCount = 0;
        
        cards.forEach(function(card) {
            var show = true;
            
            // Filtre époque
            if (activeFilters.epoque && card.dataset.epoque !== activeFilters.epoque) {
                show = false;
            }
            
            // Filtre catégorie
            if (activeFilters.categorie && card.dataset.categorie !== activeFilters.categorie) {
                show = false;
            }
            
            // Filtre artiste
            if (activeFilters.artiste && card.dataset.artiste !== activeFilters.artiste) {
                show = false;
            }
            
            // Recherche textuelle
            if (activeFilters.search) {
                var title = card.querySelector('.mbaa-oeuvre-card-title').textContent.toLowerCase();
                var artist = card.querySelector('.mbaa-oeuvre-card-artist').textContent.toLowerCase();
                if (title.indexOf(activeFilters.search) === -1 && artist.indexOf(activeFilters.search) === -1) {
                    show = false;
                }
            }
            
            card.style.display = show ? '' : 'none';
            if (show) visibleCount++;
        });
        
        // Afficher/masquer le message "aucun résultat"
        if (noResults) {
            noResults.style.display = visibleCount === 0 ? '' : 'none';
        }
    }
    
    // Écouter les changements sur les filtres
    filters.forEach(function(filterId) {
        document.getElementById(filterId).addEventListener('change', filterOeuvres);
    });
    
    // Écouter la recherche avec debounce
    var searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(filterOeuvres, 300);
    });
});
</script>

<style>
/* Styles pour la liste des œuvres */
.mbaa-oeuvres-list {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.mbaa-oeuvres-header {
    background: linear-gradient(135deg, #1a1a1a 0%, #333 100%);
    color: #fff;
    padding: 60px 20px;
    text-align: center;
}

.mbaa-oeuvres-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0 0 10px;
}

.mbaa-oeuvres-subtitle {
    font-size: 1.1rem;
    opacity: 0.9;
    margin: 0;
}

.mbaa-oeuvres-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 40px 20px;
}

/* Filtres */
.mbaa-oeuvres-filters {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
    margin-bottom: 40px;
    padding: 25px;
    background: #f9f9f9;
    border-radius: 12px;
}

.mbaa-filter-group {
    flex: 1;
    min-width: 150px;
}

.mbaa-filter-group label {
    display: block;
    font-weight: 600;
    margin-bottom: 8px;
    color: #333;
}

.mbaa-filter-select,
.mbaa-search-input {
    width: 100%;
    padding: 10px 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 14px;
    background: #fff;
}

.mbaa-search-group {
    flex: 2;
    min-width: 250px;
}

/* Grille */
.mbaa-oeuvres-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 30px;
}

/* Card œuvre */
.mbaa-oeuvre-card {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    transition: transform 0.3s, box-shadow 0.3s;
}

.mbaa-oeuvre-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.mbaa-oeuvre-card-link {
    text-decoration: none;
    color: inherit;
    display: block;
}

.mbaa-oeuvre-card-image {
    position: relative;
    height: 250px;
    overflow: hidden;
    background: #f5f5f5;
}

.mbaa-oeuvre-card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s;
}

.mbaa-oeuvre-card:hover .mbaa-oeuvre-card-image img {
    transform: scale(1.05);
}

.mbaa-oeuvre-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.mbaa-oeuvre-placeholder .dashicons {
    font-size: 64px;
    width: 64px;
    height: 64px;
    color: #ccc;
}

.mbaa-oeuvre-qr-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    width: 40px;
    height: 40px;
    background: rgba(255,255,255,0.9);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s;
}

.mbaa-oeuvre-card:hover .mbaa-oeuvre-qr-badge {
    opacity: 1;
}

.mbaa-oeuvre-qr-badge .dashicons {
    color: #c9a227;
}

.mbaa-oeuvre-card-content {
    padding: 20px;
}

.mbaa-oeuvre-card-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin: 0 0 8px;
    color: #333;
}

.mbaa-oeuvre-card-artist {
    font-size: 1rem;
    color: #666;
    margin: 0 0 15px;
}

.mbaa-oeuvre-card-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
}

.mbaa-meta-item {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 0.85rem;
    color: #888;
}

.mbaa-meta-item .dashicons {
    font-size: 16px;
    width: 16px;
    height: 16px;
}

/* Message vide */
.mbaa-oeuvres-empty,
.mbaa-no-results {
    grid-column: 1 / -1;
    text-align: center;
    padding: 60px 20px;
    color: #666;
}

.mbaa-oeuvres-empty .dashicons,
.mbaa-no-results .dashicons {
    font-size: 64px;
    width: 64px;
    height: 64px;
    color: #ccc;
    display: block;
    margin: 0 auto 20px;
}

/* Responsive */
@media (max-width: 768px) {
    .mbaa-oeuvres-filters {
        flex-direction: column;
    }
    
    .mbaa-oeuvres-grid {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 20px;
    }
    
    .mbaa-oeuvres-header {
        padding: 40px 20px;
    }
    
    .mbaa-oeuvres-title {
        font-size: 2rem;
    }
}
</style>

<?php get_footer(); ?>

