<?php
/**
 * Plugin Name: Générateur de QR Code
 * Plugin URI: https://monsite.com
 * Description: Génère des QR codes 
 * Version: 1.3
 * Author: Nina tonnaire
 * Author URI: https://monsite.com
 * License: GPL2
 */

// Sécurité : empêcher l'accès direct au fichier
if (!defined('ABSPATH')) {
    exit;
}

// 1. AJOUTER UNE PAGE DANS LE MENU ADMIN
function qr_code_ajouter_menu() {
    add_menu_page(
        'Générateur QR Code',           // Titre de la page
        'QR Code',                      // Nom dans le menu
        'manage_options',               // Capacité requise (admin seulement)
        'generateur-qr-code',           // Slug unique de la page
        'qr_code_page_contenu',         // Fonction qui affiche le contenu
        'dashicons-smartphone',         // Icône (smartphone)
        30                              // Position dans le menu
    );
}
add_action('admin_menu', 'qr_code_ajouter_menu');


// 2. AFFICHER LE CONTENU DE LA PAGE
function qr_code_page_contenu() {
    ?>
    <div class="wrap">
        <h1>Générateur de QR Code</h1>
        <p>Générez un QR code personnalisé en quelques secondes !</p>
        
        <div style="background: white; padding: 20px; border-radius: 8px; max-width: 600px; margin-top: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            
            <!-- Formulaire de génération -->
            <h2>Entrez votre texte ou URL</h2>
            <input type="text" id="qr-input" placeholder="Exemple : https://monsite.com ou Bonjour !" 
                   style="width: 100%; padding: 10px; font-size: 16px; border: 2px solid #ddd; border-radius: 5px;">
            
            <button id="generer-qr" class="button button-primary" 
                    style="margin-top: 15px; padding: 10px 20px; font-size: 16px;">
                Générer le QR Code
            </button>
            
            <!-- Zone d'affichage du QR code -->
            <div id="qr-resultat" style="margin-top: 30px; text-align: center; display: none;">
                <h3>Votre QR Code :</h3>
                <img id="qr-image" src="" alt="QR Code" style="max-width: 100%; border: 1px solid #ddd; padding: 10px; background: white;">
                
                <div style="margin-top: 15px;">
                    <button id="telecharger-qr" class="button button-secondary">
                        Télécharger le QR Code
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Section : Liste des articles avec QR codes -->
        <div style="background: white; padding: 20px; border-radius: 8px; margin-top: 30px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h2>QR Codes de vos articles</h2>
            <p>Générez rapidement des QR codes pour vos articles existants :</p>
            
            <?php
            // Récupérer les 5 derniers articles
            $args = array(
                'post_type' => 'post',
                'posts_per_page' => 5,
                'post_status' => 'publish'
            );
            $articles = get_posts($args);
            
            if ($articles) {
                echo '<table class="wp-list-table widefat fixed striped">';
                echo '<thead><tr><th>Article</th><th>QR Code</th><th>Actions</th></tr></thead>';
                echo '<tbody>';
                
                foreach ($articles as $article) {
                    $url = get_permalink($article->ID);
                    $qr_url = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($url);
                    
                    echo '<tr>';
                    echo '<td><strong>' . esc_html($article->post_title) . '</strong></td>';
                    echo '<td><img src="' . esc_url($qr_url) . '" width="80" height="80"></td>';
                    echo '<td><a href="' . esc_url($qr_url) . '" download="qr-' . $article->ID . '.png" class="button button-small">Télécharger</a></td>';
                    echo '</tr>';
                }
                
                echo '</tbody></table>';
            } else {
                echo '<p>Aucun article trouvé.</p>';
            }
            ?>
        </div>
    </div>
    
    <!-- JavaScript pour la génération dynamique -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('qr-input');
        const boutonGenerer = document.getElementById('generer-qr');
        const resultat = document.getElementById('qr-resultat');
        const qrImage = document.getElementById('qr-image');
        const boutonTelecharger = document.getElementById('telecharger-qr');
        
        // Fonction pour générer le QR code
        function genererQR() {
            const texte = input.value.trim();
            
            if (texte === '') {
                alert('Veuillez entrer du texte ou une URL !');
                return;
            }
            
            // Construire l'URL de l'API
            const apiUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=400x400&data=' + encodeURIComponent(texte);
            
            // Afficher l'image
            qrImage.src = apiUrl;
            resultat.style.display = 'block';
        }
        
        // Fonction pour télécharger le QR code
        function telechargerQR() {
            const qrUrl = qrImage.src;
            
            // Créer un lien de téléchargement
            const link = document.createElement('a');
            link.href = qrUrl;
            link.download = 'qrcode.png';
            
            // Déclencher le téléchargement
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
        
        // Événements
        boutonGenerer.addEventListener('click', genererQR);
        boutonTelecharger.addEventListener('click', telechargerQR);
        
        // Générer avec la touche Entrée
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                genererQR();
            }
        });
    });
    </script>
    
    <style>
        #qr-input:focus {
            outline: none;
            border-color: #2271b1;
            box-shadow: 0 0 0 1px #2271b1;
        }
        
        #qr-resultat {
            animation: fadeIn 0.5s;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
    <?php
}


// 3. AJOUTER UNE META BOX SUR LES ARTICLES (BONUS)
function qr_code_ajouter_meta_box() {
    add_meta_box(
        'qr_code_article_box',
        'QR Code de cet article',
        'qr_code_afficher_meta_box',
        'post',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'qr_code_ajouter_meta_box');


function qr_code_afficher_meta_box($post) {
    $url = get_permalink($post->ID);
    
    // Si l'article n'est pas encore publié, utiliser un texte par défaut
    if (!$url || $post->post_status !== 'publish') {
        echo '<p style="color: #999;">Le QR code sera disponible après la publication de l\'article.</p>';
        return;
    }
    
    $qr_url = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($url);
    ?>
    
    <div style="text-align: center;">
        <img src="<?php echo esc_url($qr_url); ?>" alt="QR Code" style="max-width: 100%; height: auto; border: 1px solid #ddd; padding: 5px;">
        <p style="margin-top: 10px;">
            <a href="<?php echo esc_url($qr_url); ?>" download="qr-article-<?php echo $post->ID; ?>.png" class="button button-primary button-small">
                Télécharger
            </a>
        </p>
        <p style="font-size: 11px; color: #666;">
            Scannez ce QR code pour accéder directement à l'article
        </p>
    </div>
    
    <?php
}

?>