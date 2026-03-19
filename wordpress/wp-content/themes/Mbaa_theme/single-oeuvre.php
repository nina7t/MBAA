<?php
/**
 * Template Name: Fiche Oeuvre
 * Template pour la fiche détaillée d'une œuvre du MBAA
 * Basé exactement sur fiche_oeuvre.html
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

// Œuvres similaires (relations manuelles en priorité)
$oeuvres_similaires = [];

// D'abord vérifier les relations manuelles
if ($oeuvre) {
    $manuels = $wpdb->get_results( $wpdb->prepare(
        "SELECT o.*, a.nom AS artiste_nom
         FROM {$wpdb->prefix}mbaa_oeuvres_similaires os
         JOIN {$wpdb->prefix}mbaa_oeuvre o ON os.oeuvre_similaire_id = o.id_oeuvre
         LEFT JOIN {$wpdb->prefix}mbaa_artiste a ON o.id_artiste = a.id_artiste
         WHERE os.oeuvre_id = %d
         ORDER BY os.ordre ASC",
        $id_oeuvre
    ), ARRAY_A );
    
    if (!empty($manuels)) {
        $oeuvres_similaires = $manuels;
    }
}

// ── FIX #6 : Récupération de l'ID depuis l'URL ou query var WordPress ────────
// Le template original utilise $_GET['oeuvre_id'] mais les URLs générées
// sont au format /oeuvre/2/slug — WordPress les réécrit via ses query vars.
// On vérifie les deux sources pour être robuste.
$id_oeuvre = 0;
if ( isset($_GET['oeuvre_id']) && intval($_GET['oeuvre_id']) > 0 ) {
    $id_oeuvre = intval($_GET['oeuvre_id']);
} elseif ( get_query_var('oeuvre_id') ) {
    $id_oeuvre = intval( get_query_var('oeuvre_id') );
}

// Si pas de relations manuelles, utiliser la logique automatique
if (empty($oeuvres_similaires)) {

    // FIX #7 : on ne construit la clause WHERE que pour les valeurs NON-NULL/NON-ZÉRO
    // Avant le fix, passer id_artiste = 0 produisait "o.id_artiste = 0"
    // ce qui ne matchait rien (aucun artiste n'a l'ID 0).

    $id_artiste  = ! empty($oeuvre['id_artiste'])  ? intval($oeuvre['id_artiste'])  : null;
    $id_epoque   = ! empty($oeuvre['id_epoque'])   ? intval($oeuvre['id_epoque'])   : null;
    $id_categorie= ! empty($oeuvre['id_categorie'])? intval($oeuvre['id_categorie']): null;
    $id_medium   = ! empty($oeuvre['id_medium'])   ? intval($oeuvre['id_medium'])   : null;

    // Construire dynamiquement les conditions OR
    $conditions = [];
    $params     = [ $id_oeuvre ]; // premier paramètre : exclusion de l'œuvre actuelle

    if ( $id_artiste ) {
        $conditions[] = 'o.id_artiste = %d';
        $params[]     = $id_artiste;
    }
    if ( $id_epoque ) {
        $conditions[] = 'o.id_epoque = %d';
        $params[]     = $id_epoque;
    }
    if ( $id_categorie ) {
        $conditions[] = 'o.id_categorie = %d';
        $params[]     = $id_categorie;
    }
    if ( $id_medium ) {
        $conditions[] = 'o.id_medium = %d';
        $params[]     = $id_medium;
    }

    if ( ! empty($conditions) ) {
        // Paramètres pour ORDER BY (priorité artiste)
        $order_val = $id_artiste ? $id_artiste : 0;
        $params[]  = $order_val;

        $where_or = implode(' OR ', $conditions);

        $sql = $wpdb->prepare(
            "SELECT o.*, a.nom AS artiste_nom
             FROM {$wpdb->prefix}mbaa_oeuvre o
             LEFT JOIN {$wpdb->prefix}mbaa_artiste a ON o.id_artiste = a.id_artiste
             WHERE o.id_oeuvre != %d
               AND o.visible_galerie = 1
               AND ( {$where_or} )
             ORDER BY
               CASE WHEN o.id_artiste = %d THEN 1 ELSE 2 END,
               RAND()
             LIMIT 6",
            ...$params
        );

        $oeuvres_similaires = $wpdb->get_results( $sql, ARRAY_A );
    }
    // Si aucune condition valide (tous les IDs sont NULL), $oeuvres_similaires reste []
}

// Artistes en lien (relations manuelles en priorité)
$artistes_en_lien = [];

// D'abord vérifier les relations manuelles
if ($oeuvre) {
    $manuels_artistes = $wpdb->get_results( $wpdb->prepare(
        "SELECT a.*, COUNT(o.id_oeuvre) as nb_oeuvres
         FROM {$wpdb->prefix}mbaa_artistes_liens al
         JOIN {$wpdb->prefix}mbaa_artiste a ON al.artiste_id = a.id_artiste
         LEFT JOIN {$wpdb->prefix}mbaa_oeuvre o ON a.id_artiste = o.id_artiste
         WHERE al.oeuvre_id = %d
         GROUP BY a.id_artiste
         ORDER BY al.ordre ASC",
        $id_oeuvre
    ), ARRAY_A );
    
    if (!empty($manuels_artistes)) {
        $artistes_en_lien = $manuels_artistes;
    }
}

// Si pas de relations manuelles, utiliser la logique automatique
if (empty($artistes_en_lien) && !empty($oeuvre['id_artiste'])) {
    // Chercher d'autres artistes avec des œuvres dans la même époque/catégorie/medium
    $artistes_en_lien = $wpdb->get_results( $wpdb->prepare(
        "SELECT a.*, COUNT(o.id_oeuvre) as nb_oeuvres
         FROM {$wpdb->prefix}mbaa_artiste a
         LEFT JOIN {$wpdb->prefix}mbaa_oeuvre o ON a.id_artiste = o.id_artiste
         WHERE a.id_artiste != %d
         AND o.visible_galerie = 1
         AND (
           o.id_epoque = %d
           OR o.id_categorie = %d  
           OR o.id_medium = %d
         )
         AND o.id_oeuvre IS NOT NULL
         GROUP BY a.id_artiste
         ORDER BY RAND()
         LIMIT 6",
        $oeuvre['id_artiste'],
        $oeuvre['id_epoque'] ?? 0,
        $oeuvre['id_categorie'] ?? 0,
        $oeuvre['id_medium'] ?? 0
    ), ARRAY_A );
}


// Si pas de relations manuelles, utiliser la logique automatique
if (empty($artistes_en_lien)) {
    $artistes_en_lien = $wpdb->get_results( $wpdb->prepare(
        "SELECT DISTINCT a.*, COUNT(o.id_oeuvre) as nb_oeuvres
         FROM {$wpdb->prefix}mbaa_artiste a
         LEFT JOIN {$wpdb->prefix}mbaa_oeuvre o ON a.id_artiste = o.id_artiste
         WHERE o.visible_galerie = 1
         AND a.id_artiste != %d
         AND (
           o.id_epoque = %d
           OR o.id_categorie = %d
           OR o.id_medium = %d
         )
         GROUP BY a.id_artiste
         ORDER BY 
           CASE WHEN o.id_epoque = %d THEN 1 ELSE 2 END,
           RAND()
         LIMIT 6",
        $oeuvre['id_artiste'] ?? 0,
        $oeuvre['id_epoque'] ?? 0,
        $oeuvre['id_categorie'] ?? 0,
        $oeuvre['id_medium'] ?? 0,
        $oeuvre['id_epoque'] ?? 0
    ), ARRAY_A );
}

// Helper — URL assets du thème
$assets = get_template_directory_uri() . '/asset';

get_header();
?>

<?php wp_body_open(); ?>

<!-- ====== HEADER (identique à fiche_oeuvre.html) ====== -->
<header class="header">
  <!-- ====== NAVBAR (preserved) ====== -->
  <div class="header__container">
    <a class="header__logo-link" href="<?php echo esc_url( home_url('/') ); ?>">
      <img class="header__logo-img" src="<?php echo esc_url( $assets . '/Img/logo/logo-mat-small.png' ); ?>" alt="Logo MBAA" />
    </a>
    <button class="header__menu-toggle" aria-label="menu" aria-expanded="false" aria-controls="headerNav">
      <span class="header__menu-bar"></span>
    </button>
    <nav class="header__nav" id="headerNav" aria-hidden="true">
      <ul class="header__nav-list header__nav-list--main">
        <li class="header__nav-item">
          <a class="header__nav-link" href="<?php echo esc_url( home_url('/infos-pratiques/') ); ?>">Infos pratiques</a>
        </li>
        <li class="header__nav-item">
          <a class="header__nav-link" href="<?php echo esc_url( home_url('/collections/') ); ?>">Collections</a>
        </li>
        <li class="header__nav-item">
          <a class="header__nav-link" href="<?php echo esc_url( home_url('/evenements/') ); ?>">Évènement</a>
        </li>
        <li class="header__nav-item">
          <a class="header__nav-link" href="<?php echo esc_url( home_url('/le-musee/') ); ?>">Le musée</a>
        </li>
        <li class="header__nav-item">
          <a class="header__nav-link" href="<?php echo esc_url( home_url('/infos-pratiques/') ); ?>">Contact</a>
        </li>
      </ul>
      <ul class="header__nav-list header__nav-list--secondary">
        <li class="header__nav-item">
          <a class="header__nav-link" href="<?php echo esc_url( wp_login_url() ); ?>" aria-label="Connexion">
            <svg class="header__nav-icon" viewBox="0 0 24 24" fill="none">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
              <circle cx="12" cy="7" r="4"></circle>
            </svg>
          </a>
        </li>
        <li class="header__nav-item">
          <a class="header__nav-link" href="<?php echo esc_url( home_url('/reservation/') ); ?>" aria-label="Billetterie">
            <svg class="header__nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
              stroke-linecap="round" stroke-linejoin="round">
              <path
                d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z">
              </path>
              <path d="M13 5v2"></path>
              <path d="M13 17v2"></path>
              <path d="M13 11v2"></path>
              </svg>
          </a>
        </li>
        <li class="header__nav-item">
          <a class="header__nav-link" href="#" id="search-trigger" aria-label="Recherche">
            <svg class="header__nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
              stroke-linecap="round" stroke-linejoin="round">
              <circle cx="11" cy="11" r="8"></circle>
              <path d="m21 21-4.35-4.35"></path>
            </svg>
          </a>
        </li>
        <li class="header__nav-item">
          <a class="header__nav-link-fr" href="#">FR</a>
        </li>
        <li class="header__nav-item">
          <a class="header__nav-link-fr" href="#">EN</a>
        </li>
      </ul>
    </nav>
  </div>

  <!-- ====== HERO — NEW LAYOUT ====== -->
  <div class="header__hero" style="<?php 
    if (!empty($oeuvre['image_url'])) {
        echo 'background-image: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url(\'' . esc_url($oeuvre['image_url']) . '\'); background-size: cover; background-position: center; background-repeat: no-repeat;';
    } else {
        echo 'background-image: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url(\'' . esc_url($assets . '/Img/hero-default.jpg') . '\'); background-size: cover; background-position: center; background-repeat: no-repeat;';
    }
  ?>">
    <!-- Left: title block -->
    <div class="hero__left">
      <p class="hero__eyebrow">La collection — Beaux-Arts</p>
      <h1 class="hero__title">
        <?php 
        $titre_lines = explode( ' ', $oeuvre['titre'] ?? 'Œuvre inconnue' );
        foreach ( $titre_lines as $i => $line ) {
          echo esc_html( $line );
          if ( $i < count( $titre_lines ) - 1 ) echo '<br>';
        }
        ?>
        <br>
        <em><?php echo esc_html( $oeuvre['artiste_nom'] ?? 'Artiste inconnu' ); ?></em> 
      </h1>
    </div>

    <!-- Right: description + CTA -->
    <div class="hero__right">
      <p class="hero__description">
        Peinture, sculpture, dessin — explorez cinq siècles de création artistique conservés dans nos collections
        permanentes.
      </p>
      <a href="<?php echo esc_url( home_url('/reservation/') ); ?>?event=1" class="hero__cta">
        Réserver ma place
        <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round"
          stroke-linejoin="round">
          <path d="M5 12h14M12 5l7 7-7 7" />
        </svg>
      </a>
    </div>

    <!-- Bottom strip: stats + scroll -->
    <div class="hero__strip">
      <div class="hero__stat">
        <span class="hero__stat-number">5 600</span>
        <span class="hero__stat-label">Œuvres exposées</span>
      </div>
      <div class="hero__strip-divider"></div>
      <div class="hero__stat">
        <span class="hero__stat-number">XVI<sup style="font-size:0.55em">e</sup></span>
        <span class="hero__stat-label">Période la plus ancienne</span>
      </div>
      <div class="hero__strip-divider"></div>
      <div class="hero__stat">
        <span class="hero__stat-number">Gratuit</span>
        <span class="hero__stat-label">Moins de 26 ans</span>
      </div>
      <div class="hero__scroll">
        <div class="hero__scroll-arrow">
          <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 5v14M5 12l7 7 7-7" />
          </svg>
        </div>
        Défiler
      </div>
    </div>
  </div>
</header>

<?php if ( $oeuvre ): ?>
<!-- BLOCK: fiche (Main) -->
<main class="fiche">

  <section class="main__banniere">
    <section class="main__banniere--explorer">
      <img class="main__banniere-img" src="<?php echo esc_url( $assets . '/Img/svg/croix-blanche.svg' ); ?>" alt="Croix blanche">
      <h2 class="main__banniere-titre">CETTE OEUVRE<br>PARLE</h2>
    </section>
    <section class="main__banniere-contenu">
      <h3 class="main__banniere-sous-titre">Comment regarder une œuvre d'art ?</h3>
      <p class="main__banniere-description">
        <?php echo esc_html( $oeuvre['titre'] ); ?> invite à la réflexion sur les valeurs de partage et de mesure qui caractérisent l'art de vivre français. 
      </p>
    </section>
  </section>

  <!-- Section Eléments pédagogiques -->
  <section class="pedagogical-elements-section">
    <div class="pedagogical-elements-container">
      <div class="pedagogical-elements-left">
        <button class="pedagogical-element-item" data-filter="couleur">
          <span class="pedagogical-element-text">La couleur</span>
          <img src="<?php echo esc_url( $assets . '/Img/svg/icon-arrow-filtre.svg' ); ?>" alt="">
        </button>
        <div class="pedagogical-element-separator"></div>
        <button class="pedagogical-element-item" data-filter="ligne">
          <span class="pedagogical-element-text">La ligne</span>
          <img src="<?php echo esc_url( $assets . '/Img/svg/icon-arrow-filtre.svg' ); ?>" alt="">
        </button>
        <div class="pedagogical-element-separator"></div>
        <button class="pedagogical-element-item" data-filter="forme">
          <span class="pedagogical-element-text">La forme</span>
          <img src="<?php echo esc_url( $assets . '/Img/svg/icon-arrow-filtre.svg' ); ?>" alt="">
        </button>
        <div class="pedagogical-element-separator"></div>
        <button class="pedagogical-element-item" data-filter="espace">
          <span class="pedagogical-element-text">L'espace</span>
          <img src="<?php echo esc_url( $assets . '/Img/svg/icon-arrow-filtre.svg' ); ?>" alt="">
        </button>
        <div class="pedagogical-element-separator"></div>
      </div>
      <div class="pedagogical-elements-right">
        <img src="<?php echo !empty($oeuvre['image_url']) ? esc_url($oeuvre['image_url']) : esc_url($assets . '/Img/tableaux/tableau-collomb-1.png'); ?>" alt="<?php echo esc_attr( $oeuvre['titre'] ); ?> - <?php echo esc_attr( $oeuvre['artiste_nom'] ); ?>" class="pedagogical-painting">
      </div>
    </div>
  </section>

  <!-- BLOCK: fiche__content-wrapper -->
  <article class="fiche__content-wrapper">
    <!-- BLOCK: description -->
    <div class="block-description">
      <div class="description">
        <img class="description__img" src="<?php echo !empty($oeuvre['image_url']) ? esc_url($oeuvre['image_url']) : esc_url($assets . '/Img/tableaux/tableau-collomb-1.png'); ?>"
          alt="<?php echo esc_attr( $oeuvre['titre'] ); ?> - <?php echo esc_attr( $oeuvre['artiste_nom'] ); ?>">
      </div>
      <div class="description__texte">
        <div class="artwork">
          <h1 class="artwork__title"><?php echo esc_html( $oeuvre['titre'] ); ?></h1>
          <h2 class="artwork__artist"><?php echo esc_html( $oeuvre['artiste_nom'] ); ?></h2>
          <p class="artwork__year"><?php echo esc_html( $oeuvre['date_creation'] ?? 'N/A' ); ?></p>
          <p><?php echo wp_kses_post( $oeuvre['description'] ?? 'Description de l\'œuvre à venir.' ); ?></p>
        </div>
        <section class="details">
          <h3 class="section__title">Détails de l'œuvre</h3>
          <div class="details__grid">
            <div class="details__card">
              <span class="details__label">Technique</span>
              <span class="details__value"><?php echo esc_html( $oeuvre['nom_medium'] ?? 'Inconnue' ); ?></span>
            </div>
            <div class="details__card">
              <span class="details__label">Dimensions</span>
              <span class="details__value"><?php echo esc_html( $oeuvre['dimensions'] ?? 'N/A' ); ?></span>
            </div>
            <div class="details__card">
              <span class="details__label">Genre</span>
              <span class="details__value"><?php echo esc_html( $oeuvre['nom_categorie'] ?? 'Inconnu' ); ?></span>
            </div>
            <div class="details__card">
              <span class="details__label">N° inventaire</span>
              <span class="details__value"><?php echo esc_html( $oeuvre['numero_inventaire'] ?? 'N/A' ); ?></span>
            </div>
          </div>
        </section>
        
        <?php if ( $audioguide ): ?>
        <div class="audio-player">
          <div class="audio-player__card">
            <div class="audio-player__header">
              <div class="audio-player__icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M9 18V5l12-2v13"></path>
                  <circle cx="6" cy="18" r="3"></circle>
                  <circle cx="18" cy="16" r="3"></circle>
                </svg>
              </div>
              <div class="audio-player__info">
                <span class="audio-player__label">Audioguide</span>
                <h3 class="audio-player__title"><?php echo esc_html( $audioguide['titre'] ?? 'Analyse de l\'œuvre' ); ?></h3>
              </div>
            </div>

            <div class="audio-player__controls">
              <button class="audio-player__play-button" id="playPauseBtn">
                <svg id="playIcon" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M5 3l14 9-14 9V3z"></path>
                </svg>
                <svg id="pauseIcon" width="20" height="20" viewBox="0 0 24 24" fill="currentColor" style="display: none;">
                  <path d="M6 4h4v16H6zm8 0h4v16h-4z"></path>
                </svg>
              </button>
              <div class="audio-player__progress-container">
                <span class="audio-player__time audio-player__time--current" id="currentTime">0:00</span>
                <div class="audio-player__progress-bar" id="progressBar">
                  <div class="audio-player__progress-fill" id="progressFill"></div>
                </div>
                <span class="audio-player__time" id="duration">0:00</span>
              </div>
            </div>

            <div class="audio-player__volume">
              <button class="audio-player__volume-button" id="muteBtn">
                <svg id="volumeIcon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon>
                  <path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path>
                </svg>
              </button>
              <input type="range" class="audio-player__volume-slider" id="volumeSlider" min="0" max="1" step="0.01" value="1">
              <span class="audio-player__volume-value" id="volumeValue">100%</span>
            </div>

            <audio id="artworkAudio" src="<?php echo esc_url( $audioguide['fichier_audio_url'] ?? '' ); ?>"></audio>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </article>

  <section class="qr-scan">
    <button class="qr-scan-btn" id="openScanner" style="padding: 15px 25px; font-size: 16px; min-height: 50px; min-width: 200px;">
      <img src="<?php echo esc_url( $assets . '/Img/svg/qr-code.svg' ); ?>" alt="QR Code" style="width: 24px; height: 24px; display: block; filter: invert(1) brightness(2); margin-bottom: 8px;">
      Scanner un QR code
    </button>
  </section>

  <!-- Modal -->
  <div class="qr-modal" id="qrModal">
    <div class="qr-modal__header">
      <span class="qr-modal__label">◆ Scanner</span>
      <button class="qr-modal__close" id="closeScanner">✕</button>
    </div>

    <div id="qr-reader"></div>

    <div class="qr-result" id="qrResult">
      <span class="qr-result__eyebrow">QR code détecté</span>
      <span class="qr-result__text" id="qrResultText"></span>
      <div class="qr-result__actions">
        <button class="btn-outline" id="qrCopy">Copier</button>
        <button class="btn-gold" id="qrOpen" style="display:none">Ouvrir le lien</button>
      </div>
    </div>
  </div>

  <!-- Section Ses autres œuvres -->
  <?php if ( ! empty( $oeuvres_similaires ) ): ?>
  <section class="other-works">
    <div class="other-works__header">
      <img src="<?php echo esc_url( $assets . '/Img/svg/croix-black.svg' ); ?>" alt="Icône">
      <h2 class="section__title section__title--with-icon">Ses autres œuvres</h2>
    </div>
    <div class="panels">
      <?php foreach ( $oeuvres_similaires as $similaire ): ?>
        <div class="panel">
          <img src="<?php echo !empty($similaire['image_url']) ? esc_url($similaire['image_url']) : esc_url($assets . '/Img/tableaux/tableau-default.jpg'); ?>" alt="<?php echo esc_attr($similaire['titre']); ?>">
          <div class="panel-overlay"></div>
          <div class="panel-arrow">
            <svg viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
          </div>
          <div class="panel-content">
            <h3><?php echo esc_html($similaire['titre']); ?></h3>
            <p><?php echo wp_kses_post($similaire['description'] ?? 'Description à venir.'); ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endif; ?>

  <!-- Section Artistes en lien -->
  <?php if ( ! empty( $artistes_en_lien ) ): ?>
  <section class="techniques-grid-section">
    <div class="other-works__header">
      <img src="<?php echo esc_url( $assets . '/Img/svg/croix-black.svg' ); ?>" alt="Icône">
      <h2 class="section__title section__title--with-icon">Artistes en lien</h2>
    </div>
    <div class="techniques-grid-container">
      <div class="techniques-grid">
        <?php foreach ( $artistes_en_lien as $artiste ): ?>
          <div class="technique-card">
            <div class="technique-card-image">
              <img src="<?php echo !empty($artiste['image_url']) ? esc_url($artiste['image_url']) : esc_url($assets . '/Img/tableaux/tableau-art-contemporain.png'); ?>" alt="<?php echo esc_attr($artiste['nom']); ?>">
              <div class="technique-card-overlay"></div>
            </div>
            <div class="technique-card-content">
              <h3 class="technique-card-title"><?php echo esc_html($artiste['nom']); ?></h3>
              <p class="technique-card-description">
                <?php echo wp_kses_post($artiste['biographie'] ?? 'Biographie à venir. ' . ($artiste['nb_oeuvres'] ?? 0) . ' œuvre(s) dans la collection.'); ?>
              </p>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- Section FAQ adaptée au thème des œuvres -->
  <section class="event-faq" id="event-faq">
    <div class="event-faq__container">
      <div class="event-faq__header">
        <span class="event-faq__eyebrow">Questions fréquentes</span>
        <h2 class="event-faq__title">Découvrir l'œuvre & sa visite</h2>
      </div>
      <div class="event-faq__grid">
        <div class="faq-list">
          <details class="faq-item">
            <summary class="faq-item__question">Comment réserver une visite guidée ?<span class="faq-item__icon">+</span></summary>
            <p class="faq-item__answer">Cliquez sur le bouton "Réserver" de la visite guidée qui vous intéresse, remplissez le formulaire et confirmez. Vous recevrez un numéro de réservation à présenter le jour J.</p>
          </details>
          <details class="faq-item">
            <summary class="faq-item__question">Puis-je prendre des photos de l'œuvre ?<span class="faq-item__icon">+</span></summary>
            <p class="faq-item__answer">La photographie sans flash est autorisée pour la plupart des œuvres. Veuillez respecter les autres visiteurs et ne pas utiliser de trépied. Certaines œuvres temporaires peuvent avoir des restrictions spécifiques.</p>
          </details>
          <details class="faq-item">
            <summary class="faq-item__question">L'œuvre est-elle accessible aux personnes à mobilité réduite ?<span class="faq-item__icon">+</span></summary>
            <p class="faq-item__answer">Oui, le musée est entièrement accessible PMR. Des places prioritaires sont réservées pour les personnes en situation de handicap. Mentionnez-le lors de votre réservation.</p>
          </details>
          <details class="faq-item">
            <summary class="faq-item__question">Y a-t-il des audioguides disponibles ?<span class="faq-item__icon">+</span></summary>
            <p class="faq-item__answer">Oui, des audioguides sont disponibles gratuitement à l'accueil. Vous pouvez aussi scanner le QR code de l'œuvre pour accéder à son audioguide directement sur votre smartphone.</p>
          </details>
          <details class="faq-item">
            <summary class="faq-item__question">Comment en savoir plus sur l'artiste ?<span class="faq-item__icon">+</span></summary>
            <p class="faq-item__answer">Chaque fiche œuvre inclut une biographie de l'artiste. Vous pouvez également consulter la section "Artistes en lien" pour découvrir d'autres œuvres du même artiste ou des artistes similaires.</p>
          </details>
          <details class="faq-item">
            <summary class="faq-item__question">Puis-je acheter des reproductions de l'œuvre ?<span class="faq-item__icon">+</span></summary>
            <p class="faq-item__answer">Des reproductions de haute qualité sont disponibles à la boutique du musée. Vous pouvez aussi commander en ligne via notre site web ou contacter notre service commercial.</p>
          </details>
        </div>
        <div class="faq-contact">
          <div class="faq-contact__inner">
            <div class="faq-contact__icon"><img draggable="false" role="img" class="emoji" alt="🎨" src="https://s.w.org/images/core/emoji/17.0.2/svg/1f3a8.svg"></div>
            <h3 class="faq-contact__title">Une autre question sur cette œuvre ?</h3>
            <p class="faq-contact__text">Notre équipe d'experts répond en moins de 24h du mardi au dimanche, de 10h à 18h.</p>
            <a href="<?php echo esc_url( home_url('/infos-pratiques/') ); ?>" class="faq-contact__btn">Nous contacter</a>
            <div class="faq-contact__divider">ou</div>
            <a href="<?php echo esc_url( home_url('/reservation/') ); ?>" class="faq-contact__reserve">Réserver une visite guidée →</a>
          </div>
        </div>
      </div>
    </div>
  </section>

</main>

<?php else: ?>
<!-- Œuvre non trouvée -->
<main class="fiche">
  <section class="main__banniere">
    <section class="main__banniere--explorer">
      <img class="main__banniere-img" src="<?php echo esc_url( $assets . '/Img/svg/croix-blanche.svg' ); ?>" alt="Croix blanche">
      <h2 class="main__banniere-titre">ŒUVRE<br>NON TROUVÉE</h2>
    </section>
    <section class="main__banniere-contenu">
      <h3 class="main__banniere-sous-titre">Cette œuvre n'existe pas</h3>
      <p class="main__banniere-description">
        L'œuvre que vous recherchez n'existe pas ou a été retirée de la collection.
      </p>
    </section>
  </section>
  
  <div class="fiche__content-wrapper">
    <div class="description__texte">
      <p><a href="<?php echo esc_url( home_url('/collections/') ); ?>" class="hero__cta">Retour aux collections</a></p>
    </div>
  </div>
</main>
<?php endif; ?>

<!-- Search overlay -->
<div id="search-overlay" class="search-overlay">
  <div class="search-overlay__close" id="search-close">&times;</div>
  <div class="search-overlay__content">
    <form class="search-overlay__form" role="search" method="get" action="<?php echo esc_url( home_url('/') ); ?>">
      <input type="search" name="s" class="search-overlay__input" placeholder="Rechercher..." required>
      <button type="submit" class="search-overlay__submit">Rechercher</button>
    </form>
  </div>
</div>

<!-- Scripts -->
<script src="<?php echo esc_url( get_template_directory_uri() . '/js/menu.js' ); ?>"></script>
<script src="<?php echo esc_url( get_template_directory_uri() . '/js/art-gallery.js' ); ?>"></script>
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script src="<?php echo esc_url( get_template_directory_uri() . '/js/qr-code.js' ); ?>"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  // Audio player
  const audio = document.getElementById('artworkAudio');
  if (audio) {
    const playBtn = document.getElementById('playPauseBtn');
    const playIcon = document.getElementById('playIcon');
    const pauseIcon = document.getElementById('pauseIcon');
    const currentTimeEl = document.getElementById('currentTime');
    const durationEl = document.getElementById('duration');
    const progressFill = document.getElementById('progressFill');
    const progressBar = document.getElementById('progressBar');
    const volumeSlider = document.getElementById('volumeSlider');
    const volumeValue = document.getElementById('volumeValue');

    function fmt(s) {
      const m = Math.floor(s / 60), sec = Math.floor(s % 60);
      return m + ':' + (sec < 10 ? '0' : '') + sec;
    }

    if (playBtn) {
      playBtn.addEventListener('click', () => {
        if (audio.paused) { 
          audio.play(); 
          playIcon.style.display = 'none'; 
          pauseIcon.style.display = ''; 
        } else { 
          audio.pause(); 
          playIcon.style.display = ''; 
          pauseIcon.style.display = 'none'; 
        }
      });
    }

    audio.addEventListener('loadedmetadata', () => durationEl.textContent = fmt(audio.duration));
    audio.addEventListener('timeupdate', () => {
      if (!audio.duration) return;
      progressFill.style.width = (audio.currentTime / audio.duration * 100) + '%';
      currentTimeEl.textContent = fmt(audio.currentTime);
    });

    if (progressBar) {
      progressBar.addEventListener('click', e => {
        const rect = progressBar.getBoundingClientRect();
        audio.currentTime = ((e.clientX - rect.left) / rect.width) * audio.duration;
      });
    }

    if (volumeSlider) {
      volumeSlider.addEventListener('input', () => {
        audio.volume = volumeSlider.value;
        volumeValue.textContent = Math.round(volumeSlider.value * 100) + '%';
      });
    }

    // Gestion du bouton mute
    const muteBtn = document.getElementById('muteBtn');
    const volumeIcon = document.getElementById('volumeIcon');
    
    if (muteBtn && volumeIcon) {
      muteBtn.addEventListener('click', () => {
        audio.muted = !audio.muted;
        if (audio.muted) {
          volumeIcon.innerHTML = '<polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><line x1="23" y1="9" x2="17" y2="15"></line><line x1="17" y1="9" x2="23" y2="15"></line>';
        } else {
          volumeIcon.innerHTML = '<polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path>';
        }
      });
    }
  }

  // Search overlay
  const trigger = document.getElementById('search-trigger');
  const overlay = document.getElementById('search-overlay');
  const close   = document.getElementById('search-close');
  if (trigger && overlay) {
    trigger.addEventListener('click', e => { e.preventDefault(); overlay.classList.add('is-active'); });
    close.addEventListener('click', () => overlay.classList.remove('is-active'));
    document.addEventListener('keydown', e => { if (e.key === 'Escape') overlay.classList.remove('is-active'); });
  }

  // QR Scanner
  const modal      = document.getElementById('qrModal');
  const openBtn    = document.getElementById('openScanner');
  const closeBtn   = document.getElementById('closeScanner');
  const result     = document.getElementById('qrResult');
  const resultText = document.getElementById('qrResultText');
  const copyBtn    = document.getElementById('qrCopy');
  const openLink   = document.getElementById('qrOpen');

  // Vérification que tous les éléments existent
  if (!modal || !openBtn || !closeBtn) {
    console.log('Éléments du scanner QR non trouvés');
    return;
  }

  const scanner = new Html5Qrcode('qr-reader');
  let lastData  = '';
  let running   = false;

  // ── Ouvrir : démarre la caméra directement ──
  openBtn.addEventListener('click', () => {
    modal.classList.add('open');
    result.classList.remove('visible');
    lastData = '';

    scanner.start(
      { facingMode: 'environment' },
      { fps: 10, qrbox: 220 },
      onFound,
      () => {} // erreurs silencieuses (frames non lisibles)
    )
    .then(() => { running = true; })
    .catch(() => { alert('Accès à la caméra refusé.'); });
  });

  // ── QR trouvé ──
  function onFound(data) {
    if (data === lastData) return;
    lastData = data;
    resultText.textContent = data;
    result.classList.add('visible');

    const isURL = /^https?:\/\//i.test(data);
    openLink.style.display = isURL ? '' : 'none';
    openLink.onclick = () => window.open(data, '_blank');
  }

  // ── Fermer ──
  function closeModal() {
    if (running) {
      scanner.stop()
        .then(() => { running = false; })
        .catch(() => {});
    }
    modal.classList.remove('open');
  }

  closeBtn.addEventListener('click', closeModal);
  // Clic sur le fond de la modal pour fermer
  modal.addEventListener('click', e => {
    if (e.target === modal) closeModal();
  });

  // ── Copier ──
  copyBtn.addEventListener('click', () => {
    if (!lastData) return;
    navigator.clipboard.writeText(lastData).then(() => {
      copyBtn.textContent = 'Copié ✓';
      setTimeout(() => { copyBtn.textContent = 'Copier'; }, 2000);
    });
  });

});
</script>

<!-- ═══════════════════════════════════════════════════
     FOOTER (même que la front page)
═══════════════════════════════════════════════════ -->
<footer class="footer">
  <div class="footer__contain-form">
    <div class="footer__logo-container">
      <a class="footer__logo-link" href="<?php echo esc_url( home_url('/') ); ?>">
        <img class="footer__logo-img"
             src="<?php echo esc_url( $assets . '/Img/logo/logo-mat-small.png' ); ?>"
             alt="Logo MBAA" />
        <h2 class="footer__title">Suivez-nous pour recevoir la newsletter</h2>
      </a>
      <?php
      // Formulaire newsletter — utilise wp_nonce pour la sécurité
      $newsletter_action = esc_url( admin_url('admin-post.php') );
      ?>
      <form class="footer__form" method="post" action="<?php echo $newsletter_action; ?>">
        <?php wp_nonce_field( 'mbaa_newsletter', 'mbaa_newsletter_nonce' ); ?>
        <input type="hidden" name="action" value="mbaa_newsletter_subscribe" />
        <input class="footer__input"
               type="email"
               name="email"
               placeholder="Entrez votre adresse e-mail"
               required />
        <button class="footer__button" type="submit">S'abonner</button>
      </form>
    </div>

    <section class="footer__nav">
      <ul class="footer__nav-list">
        <li class="footer__nav-item"><h4 class="footer__nav-title">Le musée</h4></li>
        <li class="footer__nav-item">
          <a class="footer__nav-link" href="<?php echo esc_url( home_url('/le-musee/') ); ?>">Présentation &amp; histoire</a>
        </li>
        <li class="footer__nav-item">
          <a class="footer__nav-link" href="<?php echo esc_url( home_url('/collections/') ); ?>">Collections</a>
        </li>
      </ul>
      <ul class="footer__nav-list">
        <li class="footer__nav-item"><h4 class="footer__nav-title">Programmation</h4></li>
        <li class="footer__nav-item">
          <a class="footer__nav-link" href="<?php echo esc_url( home_url('/evenements/') ); ?>">Événements</a>
        </li>
        <li class="footer__nav-item">
          <a class="footer__nav-link" href="<?php echo esc_url( home_url('/collections/') ); ?>">Collections</a>
        </li>
      </ul>
      <ul class="footer__nav-list">
        <li class="footer__nav-item"><h4 class="footer__nav-title">Contact</h4></li>
        <li class="footer__nav-item">
          <a class="footer__nav-link" href="<?php echo esc_url( home_url('/infos-pratiques/') ); ?>">Horaires et tarifs</a>
        </li>
        <li class="footer__nav-item">
          <a class="footer__nav-link" href="<?php echo esc_url( home_url('/infos-pratiques/') ); ?>">Nous contacter</a>
        </li>
        <li class="footer__nav-item">
          <a class="footer__nav-link" href="<?php echo esc_url( home_url('/infos-pratiques/') ); ?>">Accessibilité</a>
        </li>
      </ul>
    </section>

    <section class="footer__social">
      <h2>Nos réseaux sociaux</h2>
      <section class="footer__social-media">
        <a class="footer__social-link" href="#" aria-label="Facebook">
          <img class="footer__social-icon"
               src="<?php echo esc_url( $assets . '/Img/svg/facebook.svg' ); ?>"
               alt="Facebook" />
        </a>
        <a class="footer__social-link" href="#" aria-label="Instagram">
          <img class="footer__social-icon"
               src="<?php echo esc_url( $assets . '/Img/svg/insta.svg' ); ?>"
               alt="Instagram" />
        </a>
        <a class="footer__social-link" href="#" aria-label="LinkedIn">
          <img class="footer__social-icon"
               src="<?php echo esc_url( $assets . '/Img/svg/linkdin.svg' ); ?>"
               alt="LinkedIn" />
        </a>
        <a class="footer__social-link" href="#" aria-label="TikTok">
          <img class="footer__social-icon"
               src="<?php echo esc_url( $assets . '/Img/svg/tiktok.svg' ); ?>"
               alt="TikTok" />
        </a>
      </section>
    </section>

    <section class="footer__links">
      <a class="footer__link" href="#">Mentions légales</a>
      <a class="footer__link" href="#">Politique de confidentialité</a>
    </section>
  </div>
</footer>

<!-- Search Overlay -->
<div id="search-overlay" class="search-overlay">
  <div class="search-overlay__close" id="search-close">&times;</div>
  <div class="search-overlay__content">
    <form class="search-overlay__form" role="search" method="get"
          action="<?php echo esc_url( home_url('/') ); ?>">
      <input type="text"
             name="s"
             class="search-overlay__input"
             placeholder="Rechercher sur le site…"
             autofocus>
      <button type="submit" class="search-overlay__submit">
        <img src="<?php echo esc_url( $assets . '/Img/svg/Search.svg' ); ?>" alt="Rechercher">
      </button>
    </form>
  </div>
</div>

<?php wp_footer(); ?>

<!-- Search overlay -->
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const searchTrigger = document.getElementById('search-trigger');
    const searchOverlay = document.getElementById('search-overlay');
    const searchClose   = document.getElementById('search-close');

    if (searchTrigger && searchOverlay) {
      searchTrigger.addEventListener('click', function (e) {
        e.preventDefault();
        searchOverlay.classList.add('is-active');
        setTimeout(() => searchOverlay.querySelector('input').focus(), 300);
      });
      searchClose.addEventListener('click', () => searchOverlay.classList.remove('is-active'));
      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') searchOverlay.classList.remove('is-active');
      });
      searchOverlay.addEventListener('click', (e) => {
        if (e.target === searchOverlay) searchOverlay.classList.remove('is-active');
      });
    }
  });
</script>
