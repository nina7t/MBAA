<?php
/**
 * Template Name: Fiche Oeuvre
 * Template pour la fiche détaillée d'une œuvre du MBAA
 * Remplace fiche_oeuvre.html
 *
 * @package Mbaa_theme
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// ── Données dynamiques depuis la BDD MBAA ──────────────────────────────────
global $wpdb;

// Récupération de l'ID de l'œuvre depuis l'URL
$id_oeuvre = isset( $_GET['oeuvre_id'] ) ? intval( $_GET['oeuvre_id'] ) : 0;

// Requête principale pour l'œuvre
$oeuvre = $wpdb->get_row( $wpdb->prepare(
    "SELECT o.*, a.nom AS artiste_nom, a.biographie AS artiste_bio,
            m.nom_medium, e.nom_epoque, s.nom_salle
     FROM {$wpdb->prefix}mbaa_oeuvre o
     LEFT JOIN {$wpdb->prefix}mbaa_artiste a ON o.id_artiste = a.id_artiste
     LEFT JOIN {$wpdb->prefix}mbaa_medium m  ON o.id_medium  = m.id_medium
     LEFT JOIN {$wpdb->prefix}mbaa_epoque e  ON o.id_epoque  = e.id_epoque
     LEFT JOIN {$wpdb->prefix}mbaa_salle s   ON o.id_salle   = s.id_salle
     WHERE o.id_oeuvre = %d",
    $id_oeuvre
), ARRAY_A );

// Audioguide associé à l'œuvre
$audioguide = $wpdb->get_row( $wpdb->prepare(
    "SELECT * FROM {$wpdb->prefix}mbaa_audioguide WHERE id_oeuvre = %d LIMIT 1",
    $id_oeuvre
), ARRAY_A );

// Incrémenter le compteur de vues si l'œuvre existe
if ( $oeuvre ) {
    $wpdb->query( $wpdb->prepare(
        "UPDATE {$wpdb->prefix}mbaa_oeuvre SET vues = vues + 1 WHERE id_oeuvre = %d",
        $id_oeuvre
    ));
}

// Helper — URL assets du thème
$assets = get_template_directory_uri() . '/asset';

get_header();
?>

<?php wp_body_open(); ?>

<!-- ═══════════════════════════════════════════════════
     HEADER / HERO
═══════════════════════════════════════════════════ -->
<header class="header header--fiche-oeuvre">
  <!-- ====== NAVBAR (preserved) ====== -->
  <div class="header__container">
    <a class="header__logo-link" href="<?php echo esc_url( home_url('/') ); ?>">
      <img class="header__logo-img" src="<?php echo esc_url( $assets . '/Img/logo/logo-mat-small.png' ); ?>" alt="Logo MBAA" />
    </a>
    <button class="header__menu-toggle" aria-label="menu" aria-expanded="false" aria-controls="headerNav">
      <span class="header__menu-bar"></span>
      <span class="header__menu-bar"></span>
      <span class="header__menu-bar"></span>
    </button>
    <nav id="headerNav" class="header__nav">
      <ul class="header__nav-list">
        <li><a href="<?php echo esc_url( home_url('/') ); ?>" class="header__nav-link">Accueil</a></li>
        <li><a href="<?php echo esc_url( home_url('/musee-histoire/') ); ?>" class="header__nav-link">Le musée</a></li>
        <li><a href="<?php echo esc_url( home_url('/collections/') ); ?>" class="header__nav-link">Collections</a></li>
        <li><a href="<?php echo esc_url( home_url('/evenements/') ); ?>" class="header__nav-link">Événements</a></li>
        <li><a href="<?php echo esc_url( home_url('/infos-pratiques/') ); ?>" class="header__nav-link">Infos pratiques</a></li>
      </ul>
    </nav>
  </div>

  <!-- ====== HERO SECTION (preserved) ====== -->
  <div class="hero hero--oeuvre">
    <div class="hero__container">
      <div class="hero__breadcrumb">
        <a href="<?php echo esc_url( home_url('/') ); ?>">Accueil</a>
        <span class="hero__breadcrumb-separator">›</span>
        <a href="<?php echo esc_url( home_url('/collections/') ); ?>">Collections</a>
        <span class="hero__breadcrumb-separator">›</span>
        <span class="hero__breadcrumb-current">
          <?php echo esc_html( $oeuvre['titre'] ?? 'Œuvre inconnue' ); ?>
        </span>
      </div>
      
      <div class="hero__content">
        <h1 class="hero__title">
          <?php echo esc_html( $oeuvre['titre'] ?? 'Œuvre inconnue' ); ?>
        </h1>
        <p class="hero__subtitle">
          <?php echo esc_html( $oeuvre['artiste_nom'] ?? 'Artiste inconnu' ); ?>
        </p>
      </div>
      
      <div class="hero__scroll">
        <div class="hero__scroll-content">
          <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 5v14M5 12l7 7 7-7" />
          </svg>
          <span>Défiler</span>
        </div>
      </div>
    </div>
  </div>
</header>

<!-- ═══════════════════════════════════════════════════
     MAIN CONTENT
═══════════════════════════════════════════════════ -->
<main class="fiche-oeuvre">
  <?php if ( $oeuvre ): ?>
    <!-- Œuvre Principale -->
    <section class="oeuvre-main">
      <div class="oeuvre-main__container">
        <div class="oeuvre-main__grid">
          <!-- Image principale -->
          <div class="oeuvre-main__image">
            <div class="oeuvre-main__image-wrapper">
              <img 
                src="<?php echo esc_url( $assets . '/Img/oeuvres/' . ( $oeuvre['image_principale'] ?? 'placeholder-oeuvre.jpg' ) ); ?>" 
                alt="<?php echo esc_attr( $oeuvre['titre'] ); ?>"
                loading="lazy"
              />
              <div class="oeuvre-main__image-overlay">
                <button class="oeuvre-main__zoom-btn" aria-label="Zoom">
                  <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.35-4.35M8 11h6M11 8v6"/>
                  </svg>
                </button>
              </div>
            </div>
            
            <!-- Galerie d'images -->
            <?php if ( ! empty( $oeuvre['images_supplementaires'] ) ): ?>
              <div class="oeuvre-main__gallery">
                <h3 class="oeuvre-main__gallery-title">Autres vues</h3>
                <div class="oeuvre-main__gallery-grid">
                  <?php 
                  $images = explode( ',', $oeuvre['images_supplementaires'] );
                  foreach ( $images as $image ):
                    $image = trim( $image );
                    if ( $image ):
                  ?>
                    <div class="oeuvre-main__gallery-item">
                      <img 
                        src="<?php echo esc_url( $assets . '/Img/oeuvres/' . $image ); ?>" 
                        alt="<?php echo esc_attr( $oeuvre['titre'] ); ?>"
                        loading="lazy"
                      />
                    </div>
                  <?php 
                    endif;
                  endforeach; 
                  ?>
                </div>
              </div>
            <?php endif; ?>
          </div>
          
          <!-- Informations détaillées -->
          <div class="oeuvre-main__info">
            <div class="oeuvre-main__header">
              <h2 class="oeuvre-main__title"><?php echo esc_html( $oeuvre['titre'] ); ?></h2>
              <h3 class="oeuvre-main__artist"><?php echo esc_html( $oeuvre['artiste_nom'] ); ?></h3>
            </div>
            
            <!-- Fiche technique -->
            <div class="oeuvre-main__fiche">
              <h3 class="oeuvre-main__fiche-title">Fiche technique</h3>
              <dl class="oeuvre-main__fiche-list">
                <div class="oeuvre-main__fiche-item">
                  <dt>Artiste</dt>
                  <dd><?php echo esc_html( $oeuvre['artiste_nom'] ); ?></dd>
                </div>
                
                <?php if ( $oeuvre['date_creation'] ): ?>
                <div class="oeuvre-main__fiche-item">
                  <dt>Date</dt>
                  <dd><?php echo esc_html( $oeuvre['date_creation'] ); ?></dd>
                </div>
                <?php endif; ?>
                
                <?php if ( $oeuvre['nom_medium'] ): ?>
                <div class="oeuvre-main__fiche-item">
                  <dt>Medium</dt>
                  <dd><?php echo esc_html( $oeuvre['nom_medium'] ); ?></dd>
                </div>
                <?php endif; ?>
                
                <?php if ( $oeuvre['dimensions'] ): ?>
                <div class="oeuvre-main__fiche-item">
                  <dt>Dimensions</dt>
                  <dd><?php echo esc_html( $oeuvre['dimensions'] ); ?></dd>
                </div>
                <?php endif; ?>
                
                <?php if ( $oeuvre['nom_epoque'] ): ?>
                <div class="oeuvre-main__fiche-item">
                  <dt>Époque</dt>
                  <dd><?php echo esc_html( $oeuvre['nom_epoque'] ); ?></dd>
                </div>
                <?php endif; ?>
                
                <?php if ( $oeuvre['nom_salle'] ): ?>
                <div class="oeuvre-main__fiche-item">
                  <dt>Localisation</dt>
                  <dd><?php echo esc_html( $oeuvre['nom_salle'] ); ?></dd>
                </div>
                <?php endif; ?>
                
                <?php if ( $oeuvre['reference_inventory'] ): ?>
                <div class="oeuvre-main__fiche-item">
                  <dt>Référence</dt>
                  <dd><?php echo esc_html( $oeuvre['reference_inventory'] ); ?></dd>
                </div>
                <?php endif; ?>
              </dl>
            </div>
            
            <!-- Description -->
            <?php if ( $oeuvre['description'] ): ?>
            <div class="oeuvre-main__description">
              <h3 class="oeuvre-main__description-title">Description</h3>
              <div class="oeuvre-main__description-text">
                <?php echo wp_kses_post( $oeuvre['description'] ); ?>
              </div>
            </div>
            <?php endif; ?>
            
            <!-- Actions -->
            <div class="oeuvre-main__actions">
              <button class="oeuvre-main__btn oeuvre-main__btn--favorite" data-oeuvre-id="<?php echo esc_attr( $oeuvre['id_oeuvre'] ); ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                </svg>
                Ajouter aux favoris
              </button>
              
              <button class="oeuvre-main__btn oeuvre-main__btn--share">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="18" cy="5" r="3"/>
                  <circle cx="6" cy="12" r="3"/>
                  <circle cx="18" cy="19" r="3"/>
                  <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/>
                  <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/>
                </svg>
                Partager
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>
    
    <!-- Audioguide -->
    <?php if ( $audioguide ): ?>
    <section class="audioguide">
      <div class="audioguide__container">
        <div class="audioguide__header">
          <h2 class="audioguide__title">Audioguide</h2>
          <p class="audioguide__subtitle">Écoutez le commentaire de l'expert</p>
        </div>
        
        <div class="audioguide__player">
          <div class="audioguide__cover">
            <img 
              src="<?php echo esc_url( $assets . '/Img/oeuvres/' . ( $oeuvre['image_principale'] ?? 'placeholder-oeuvre.jpg' ) ); ?>" 
              alt="<?php echo esc_attr( $oeuvre['titre'] ); ?>"
            />
          </div>
          
          <div class="audioguide__controls">
            <h3 class="audioguide__track-title"><?php echo esc_html( $audioguide['titre'] ); ?></h3>
            <p class="audioguide__track-duration"><?php echo esc_html( $audioguide['duree'] ?? '00:00' ); ?></p>
            
            <audio controls class="audioguide__audio">
              <source src="<?php echo esc_url( $assets . '/audio/' . $audioguide['fichier_audio'] ); ?>" type="audio/mpeg">
              Votre navigateur ne supporte pas l'élément audio.
            </audio>
            
            <div class="audioguide__transcript">
              <button class="audioguide__transcript-btn">Voir la transcription</button>
              <div class="audioguide__transcript-content" style="display: none;">
                <?php echo wp_kses_post( $audioguide['transcription'] ); ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <?php endif; ?>
    
    <!-- Biographie de l'artiste -->
    <?php if ( $oeuvre['artiste_bio'] ): ?>
    <section class="artiste-bio">
      <div class="artiste-bio__container">
        <div class="artiste-bio__header">
          <h2 class="artiste-bio__title"><?php echo esc_html( $oeuvre['artiste_nom'] ); ?></h2>
          <p class="artiste-bio__subtitle">Biographie</p>
        </div>
        
        <div class="artiste-bio__content">
          <div class="artiste-bio__text">
            <?php echo wp_kses_post( $oeuvre['artiste_bio'] ); ?>
          </div>
        </div>
      </div>
    </section>
    <?php endif; ?>
    
    <!-- Œuvres similaires -->
    <section class="oeuvres-similaires">
      <div class="oeuvres-similaires__container">
        <div class="oeuvres-similaires__header">
          <h2 class="oeuvres-similaires__title">Œuvres similaires</h2>
          <p class="oeuvres-similaires__subtitle">Découvrez d'autres œuvres qui pourraient vous intéresser</p>
        </div>
        
        <div class="oeuvres-similaires__grid">
          <?php
          // Récupérer des œuvres du même artiste ou de la même époque
          $similaires = $wpdb->get_results( $wpdb->prepare(
              "SELECT o.*, a.nom AS artiste_nom
               FROM {$wpdb->prefix}mbaa_oeuvre o
               LEFT JOIN {$wpdb->prefix}mbaa_artiste a ON o.id_artiste = a.id_artiste
               WHERE o.id_oeuvre != %d 
               AND (o.id_artiste = %d OR o.id_epoque = %d)
               AND o.visible_galerie = 1
               ORDER BY RAND()
               LIMIT 6",
              $id_oeuvre,
              $oeuvre['id_artiste'] ?? 0,
              $oeuvre['id_epoque'] ?? 0
          ), ARRAY_A );
          
          foreach ( $similaires as $similaire ):
          ?>
            <div class="oeuvre-card">
              <a href="<?php echo esc_url( add_query_arg('oeuvre_id', $similaire['id_oeuvre'], home_url('/fiche-oeuvre/')) ); ?>" class="oeuvre-card__link">
                <div class="oeuvre-card__image">
                  <img 
                    src="<?php echo esc_url( $assets . '/Img/oeuvres/' . ( $similaire['image_principale'] ?? 'placeholder-oeuvre.jpg' ) ); ?>" 
                    alt="<?php echo esc_attr( $similaire['titre'] ); ?>"
                    loading="lazy"
                  />
                </div>
                <div class="oeuvre-card__content">
                  <h3 class="oeuvre-card__title"><?php echo esc_html( $similaire['titre'] ); ?></h3>
                  <p class="oeuvre-card__artist"><?php echo esc_html( $similaire['artiste_nom'] ); ?></p>
                </div>
              </a>
            </div>
          <?php endforeach; ?>
          
          <?php if ( empty( $similaires ) ): ?>
            <p class="oeuvres-similaires__empty">Aucune œuvre similaire trouvée pour le moment.</p>
          <?php endif; ?>
        </div>
      </div>
    </section>
    
  <?php else: ?>
    <!-- Œuvre non trouvée -->
    <section class="oeuvre-not-found">
      <div class="oeuvre-not-found__container">
        <div class="oeuvre-not-found__content">
          <h1 class="oeuvre-not-found__title">Œuvre non trouvée</h1>
          <p class="oeuvre-not-found__text">
            L'œuvre que vous recherchez n'existe pas ou a été retirée de la collection.
          </p>
          <div class="oeuvre-not-found__actions">
            <a href="<?php echo esc_url( home_url('/collections/') ); ?>" class="oeuvre-not-found__btn">
              Retour aux collections
            </a>
          </div>
        </div>
      </div>
    </section>
  <?php endif; ?>
</main>

<!-- ═══════════════════════════════════════════════════
     FOOTER (preserved)
═══════════════════════════════════════════════════ -->
<footer class="footer">
  <div class="footer__contain-form">
    <div class="footer__logo-container">
      <a class="footer__logo-link" href="#">
        <img class="footer__logo-img" src="<?php echo esc_url( $assets . '/Img/logo/logo-mat-small.png' ); ?>" alt="Logo MBAA" />
        <h2 class="footer__title">Suivez-nous pour recevoir la newsletter</h2>
      </a>
      <form class="footer__form" method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
        <?php wp_nonce_field('mbaa_newsletter', 'mbaa_newsletter_nonce'); ?>
        <input type="hidden" name="action" value="mbaa_newsletter_subscribe" />
        <input class="footer__input" type="email" name="email" placeholder="Entrez votre adresse e-mail" required />
        <button class="footer__button" type="submit">S'abonner</button>
      </form>
    </div>

    <section class="footer__nav">
      <ul class="footer__nav-list">
        <li class="footer__nav-item">
          <h4 class="footer__nav-title">Le musée</h4>
        </li>
        <li class="footer__nav-item">
          <a class="footer__nav-link" href="<?php echo esc_url( home_url('/musee-histoire/') ); ?>">Présentation & histoire</a>
        </li>
        <li class="footer__nav-item">
          <a class="footer__nav-link" href="<?php echo esc_url( home_url('/collections/') ); ?>">Collections</a>
        </li>
      </ul>

      <ul class="footer__nav-list">
        <li class="footer__nav-item">
          <h4 class="footer__nav-title">Programmation</h4>
        </li>
        <li class="footer__nav-item">
          <a class="footer__nav-link" href="<?php echo esc_url( home_url('/evenements/') ); ?>">Événements</a>
        </li>
        <li class="footer__nav-item">
          <a class="footer__nav-link" href="<?php echo esc_url( home_url('/collections/') ); ?>">Collections</a>
        </li>
      </ul>

      <ul class="footer__nav-list">
        <li class="footer__nav-item">
          <h4 class="footer__nav-title">Contact</h4>
        </li>
        <li class="footer__nav-item">
          <a class="footer__nav-link" href="#">Horaires et tarifs</a>
        </li>
        <li class="footer__nav-item">
          <a class="footer__nav-link" href="#">Nous contacter</a>
        </li>
        <li class="footer__nav-item">
          <a class="footer__nav-link" href="#">Accessibilité</a>
        </li>
      </ul>
    </section>

    <section class="footer__social">
      <h2>Nos réseaux sociaux</h2>
      <section class="footer__social-media">
        <a class="footer__social-link" href="#" aria-label="Facebook">
          <img class="footer__social-icon" src="<?php echo esc_url( $assets . '/Img/svg/facebook.svg' ); ?>" alt="Facebook" />
        </a>
        <a class="footer__social-link" href="#" aria-label="Instagram">
          <img class="footer__social-icon" src="<?php echo esc_url( $assets . '/Img/svg/insta.svg' ); ?>" alt="Instagram" />
        </a>
        <a class="footer__social-link" href="#" aria-label="Linkdin">
          <img class="footer__social-icon" src="<?php echo esc_url( $assets . '/Img/svg/linkdin.svg' ); ?>" alt="Twitter" />
        </a>
        <a class="footer__social-link" href="#" aria-label="Tiktok">
          <img class="footer__social-icon" src="<?php echo esc_url( $assets . '/Img/svg/tiktok.svg' ); ?>" alt="YouTube" />
        </a>
      </section>
    </section>

    <section class="footer__links">
      <a class="footer__link" href="#">Mentions légales</a>
      <a class="footer__link" href="#">Politique de confidentialité</a>
    </section>
  </div>
</footer>

<?php get_footer(); ?>
