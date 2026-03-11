<?php
/**
 * Template Name: Collections
 * Template pour la page des collections du MBAA
 * Remplace collections.html
 *
 * @package Mbaa_theme
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// ── Données dynamiques depuis la BDD MBAA ──────────────────────────────────
global $wpdb;

// Vérifier si on demande une fiche œuvre spécifique
$oeuvre_id = isset( $_GET['oeuvre_id'] ) ? intval( $_GET['oeuvre_id'] ) : 0;

if ( $oeuvre_id > 0 ) {
    // Rediriger vers le template single-oeuvre
    include get_template_directory() . '/single-oeuvre.php';
    return;
}

// Œuvres visibles en galerie
$oeuvres = $wpdb->get_results(
    "SELECT o.*, a.nom AS artiste_nom, m.nom_medium,
            e.nom_epoque, c.nom_categorie
     FROM {$wpdb->prefix}mbaa_oeuvre o
     LEFT JOIN {$wpdb->prefix}mbaa_artiste a   ON o.id_artiste  = a.id_artiste
     LEFT JOIN {$wpdb->prefix}mbaa_medium m    ON o.id_medium   = m.id_medium
     LEFT JOIN {$wpdb->prefix}mbaa_epoque e    ON o.id_epoque   = e.id_epoque
     LEFT JOIN {$wpdb->prefix}mbaa_categorie c ON o.id_categorie = c.id_categorie
     WHERE o.visible_galerie = 1
     ORDER BY o.titre ASC",
    ARRAY_A
);

// Helper — URL assets du thème
$assets = get_template_directory_uri() . '/asset';

get_header();
?>

<?php wp_body_open(); ?>

<header class="header header--collections">
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
  <div class="header__hero">

    <!-- Left: title block -->
    <div class="hero__left">
      <p class="hero__eyebrow">La collection — Beaux-Arts</p>
      <h1 class="hero__title">
        La<br>
        collection<br>
        <em>Beaux Arts</em>
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
        <img class="hero__cta-icon" src="<?php echo esc_url( $assets . '/Img/svg/icon-arrow-droite.svg' ); ?>" alt="Flèche vers la droite">
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

    <section class="gallery-section">
      <div class="gallery-container">

        <!-- FILTERS — même style que le carousel de la page d'accueil -->
<div class="gallery-filters">

  <div class="filtre gallery-filtre">
    <h3 class="filtre__title">Filtrer par&nbsp;:</h3>
    <ul class="filtre__list">
      <li class="filtre__list-item filtre__list-item--active" data-filter="toutVoir">Tout voir</li>
      <li class="filtre__list-item" data-filter="collections">
        <img class="filtre__list-img" src="<?php echo esc_url( $assets . '/Img/svg/icon-arrow-filtre.svg' ); ?>" alt="">Collections
      </li>
      <li class="filtre__list-item" data-filter="epoque">
        <img class="filtre__list-img" src="<?php echo esc_url( $assets . '/Img/svg/icon-arrow-filtre.svg' ); ?>" alt="">Époques
      </li>
      <li class="filtre__list-item" data-filter="artistes">
        <img class="filtre__list-img" src="<?php echo esc_url( $assets . '/Img/svg/icon-arrow-filtre.svg' ); ?>" alt="">Artistes
      </li>
      <li class="filtre__list-item" data-filter="medium">
        <img class="filtre__list-img" src="<?php echo esc_url( $assets . '/Img/svg/icon-arrow-filtre.svg' ); ?>" alt="">Medium
      </li>
    </ul>
  </div>

  <div class="gallery-search">
    <div class="gallery-search__wrapper">
      <svg class="gallery-search__icon" viewBox="0 0 24 24" fill="none"
           stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="11" cy="11" r="8" />
        <path d="m21 21-4.35-4.35" />
      </svg>
      <input type="text" id="gallerySearch" class="gallery-search__input" placeholder="Rechercher une œuvre, un artiste, une technique...">
      <button class="gallery-search__clear" id="searchClear" style="display: none;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18"></line>
          <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
      </button>
    </div>
    <div class="gallery-search__results" id="searchResults" style="display: none;">
      <div class="search-results__header">
        <span class="search-results__count" id="resultsCount">0 résultats</span>
        <span class="search-results__label">trouvés</span>
      </div>
      <div class="search-results__list" id="resultsList">
        <!-- Results will be populated by JS -->
      </div>
    </div>
  </div>

</div>

        <!-- SUBFILTERS DRAWER -->
        <div class="subfilters-drawer" id="subfiltersDrawer">
          <span class="subfilters-label" id="subfiltersLabel">Filtrer</span>
          <!-- populated by JS -->
        </div>

        <!-- GRID HEADER -->
        <div class="gallery-grid-header">
          <span class="gallery-grid-header__tag" id="activeFilterLabel">Toutes les œuvres</span>
          <span class="gallery-grid-header__rule"></span>
        </div>

        <!-- MASONRY GRID -->
        <div class="gallery-grid" id="artGalleryGrid">

          <?php if ( ! empty( $oeuvres ) ) : ?>
            <?php foreach ( $oeuvres as $i => $oeuvre ) :
              $index = $i + 1;
              $image_url = ! empty( $oeuvre['image_url'] ) ? esc_url( $oeuvre['image_url'] ) : esc_url( $assets . '/Img/tableaux/tableau-default.jpg' );
              $artiste_nom = ! empty( $oeuvre['artiste_nom'] ) ? esc_html( $oeuvre['artiste_nom'] ) : 'Anonyme';
              $medium_nom = ! empty( $oeuvre['nom_medium'] ) ? esc_html( $oeuvre['nom_medium'] ) : 'Inconnu';
              $epoque_nom = strtolower( ! empty( $oeuvre['nom_epoque'] ) ? esc_html( $oeuvre['nom_epoque'] ) : 'inconnu' );
              $categorie_slug = strtolower( ! empty( $oeuvre['nom_categorie'] ) ? esc_html( $oeuvre['nom_categorie'] ) : 'inconnu' );
              $artiste_slug = sanitize_title( $artiste_nom );
              $medium_slug = sanitize_title( $medium_nom );
            ?>
            <div class="gallery-item" data-id="<?php echo esc_attr( $oeuvre['id_oeuvre'] ); ?>" 
                 data-collections="<?php echo esc_attr( $categorie_slug ); ?>" 
                 data-epoque="<?php echo esc_attr( $epoque_nom ); ?>" 
                 data-artistes="<?php echo esc_attr( $artiste_slug ); ?>"
                 data-medium="<?php echo esc_attr( $medium_slug ); ?>" 
                 data-title="<?php echo esc_attr( $oeuvre['titre'] ); ?>" 
                 data-artist="<?php echo esc_attr( $artiste_nom ); ?>">
              <a href="<?php echo esc_url( home_url('/collections/?oeuvre_id=' . $oeuvre['id_oeuvre']) ); ?>">
                <img src="<?php echo $image_url; ?>" alt="<?php echo esc_attr( $oeuvre['titre'] ); ?>">
              </a>
              <div class="gallery-item__overlay">
                <p class="gallery-item__title"><?php echo esc_html( $oeuvre['titre'] ); ?></p>
                <p class="gallery-item__meta"><?php echo $artiste_nom; ?> · <?php echo $medium_nom; ?></p>
              </div>
              <span class="gallery-item__index">No.<?php echo str_pad( $index, 2, '0', STR_PAD_LEFT ); ?></span>
            </div>
            <?php endforeach; ?>
          <?php else : ?>
            <!-- Fallback statique si la BDD est vide -->
            <div class="gallery-item" data-id="1" data-collections="peinture" data-epoque="moderne" data-artistes="hokusai"
              data-medium="estampe" data-title="La Grande Vague de Kanagawa" data-artist="Katsushika Hokusai">
              <a href="<?php echo esc_url( home_url('/collections/?oeuvre_id=1') ); ?>">
                <img src="<?php echo esc_url( $assets . '/Img/tableaux/tableau-hokusai-vague.jpg' ); ?>" alt="La Grande Vague de Kanagawa">
              </a>
              <div class="gallery-item__overlay">
                <p class="gallery-item__title">La Grande Vague de Kanagawa</p>
                <p class="gallery-item__meta">Hokusai · Estampe</p>
              </div>
              <span class="gallery-item__index">No.01</span>
            </div>

            <div class="gallery-item" data-id="2" data-collections="peinture" data-epoque="contemporain"
              data-artistes="collomb" data-medium="huile" data-title="Le Repas des Amis" data-artist="Paul Collomb">
              <a href="<?php echo esc_url( home_url('/collections/?oeuvre_id=2') ); ?>">
                <img src="<?php echo esc_url( $assets . '/Img/tableaux/tableau-collomb-1.png' ); ?>" alt="Le Repas des Amis">
              </a>
              <div class="gallery-item__overlay">
                <p class="gallery-item__title">Le Repas des Amis</p>
                <p class="gallery-item__meta">Paul Collomb · Huile sur toile</p>
              </div>
              <span class="gallery-item__index">No.02</span>
            </div>

            <div class="gallery-item" data-id="3" data-collections="peinture" data-epoque="contemporain"
              data-artistes="stojka" data-medium="aquarelle" data-title="Sans titre" data-artist="Ceija Stojka">
              <a href="<?php echo esc_url( home_url('/collections/?oeuvre_id=3') ); ?>">
                <img src="<?php echo esc_url( $assets . '/Img/tableaux/tableau-ceija-stojka-sans-titre.jpg' ); ?>" alt="Ceija Stojka - Sans titre">
              </a>
              <div class="gallery-item__overlay">
                <p class="gallery-item__title">Sans titre</p>
                <p class="gallery-item__meta">Ceija Stojka · Aquarelle</p>
              </div>
              <span class="gallery-item__index">No.03</span>
            </div>

            <div class="gallery-item" data-id="4" data-collections="peinture" data-epoque="classique"
              data-artistes="boucher" data-medium="huile" data-title="Chinoiserie" data-artist="François Boucher">
              <a href="<?php echo esc_url( home_url('/collections/?oeuvre_id=4') ); ?>">
                <img src="<?php echo esc_url( $assets . '/Img/tableaux/tableau-boucher-chinoiserie.jpg' ); ?>" alt="Chinoiserie - Boucher">
              </a>
              <div class="gallery-item__overlay">
                <p class="gallery-item__title">Chinoiserie</p>
                <p class="gallery-item__meta">François Boucher · Huile sur toile</p>
              </div>
              <span class="gallery-item__index">No.04</span>
            </div>

            <div class="gallery-item" data-id="5" data-collections="peinture" data-epoque="moderne" data-artistes="signac"
              data-medium="huile" data-title="Paysage pointilliste" data-artist="Paul Signac">
              <a href="<?php echo esc_url( home_url('/collections/?oeuvre_id=5') ); ?>">
                <img src="<?php echo esc_url( $assets . '/Img/tableaux/tableau-pointillisme.jpg' ); ?>" alt="Paysage pointilliste">
              </a>
              <div class="gallery-item__overlay">
                <p class="gallery-item__title">Paysage pointilliste</p>
                <p class="gallery-item__meta">Paul Signac · Huile</p>
              </div>
              <span class="gallery-item__index">No.05</span>
            </div>

            <div class="gallery-item" data-id="6" data-collections="peinture" data-epoque="moderne" data-artistes="turner"
              data-medium="aquarelle" data-title="Océan" data-artist="J.M.W. Turner">
              <a href="<?php echo esc_url( home_url('/collections/?oeuvre_id=6') ); ?>">
                <img src="<?php echo esc_url( $assets . '/Img/tableaux/tableau-turner-ocean.jpg' ); ?>" alt="Océan - Turner">
              </a>
              <div class="gallery-item__overlay">
                <p class="gallery-item__title">Océan</p>
                <p class="gallery-item__meta">J.M.W. Turner · Aquarelle</p>
              </div>
              <span class="gallery-item__index">No.06</span>
            </div>

            <div class="gallery-item" data-id="7" data-collections="sculpture" data-epoque="antique" data-artistes=""
              data-medium="marbre" data-title="Statue antique" data-artist="Anonyme">
              <a href="<?php echo esc_url( home_url('/collections/?oeuvre_id=7') ); ?>">
                <img src="<?php echo esc_url( $assets . '/Img/statue-1.png' ); ?>" alt="Statue antique">
              </a>
              <div class="gallery-item__overlay">
                <p class="gallery-item__title">Statue antique</p>
                <p class="gallery-item__meta">Anonyme · Marbre</p>
              </div>
              <span class="gallery-item__index">No.07</span>
            </div>

            <div class="gallery-item" data-id="8" data-collections="peinture" data-epoque="classique" data-artistes=""
              data-medium="huile" data-title="Scène classiciste" data-artist="École française">
              <a href="<?php echo esc_url( home_url('/collections/?oeuvre_id=8') ); ?>">
                <img src="<?php echo esc_url( $assets . '/Img/tableaux/tableau-classicisme.jpg' ); ?>" alt="Scène classiciste">
              </a>
              <div class="gallery-item__overlay">
                <p class="gallery-item__title">Scène classiciste</p>
                <p class="gallery-item__meta">École française · Huile</p>
              </div>
              <span class="gallery-item__index">No.08</span>
            </div>

            <div class="gallery-item" data-id="9" data-collections="peinture" data-epoque="contemporain" data-artistes=""
              data-medium="acrylique" data-title="Art contemporain" data-artist="Artiste inconnu">
              <a href="<?php echo esc_url( home_url('/collections/?oeuvre_id=9') ); ?>">
                <img src="<?php echo esc_url( $assets . '/Img/tableaux/tableau-art-contemporain.png' ); ?>" alt="Art contemporain">
              </a>
              <div class="gallery-item__overlay">
                <p class="gallery-item__title">Art contemporain</p>
                <p class="gallery-item__meta">Artiste inconnu · Acrylique</p>
              </div>
              <span class="gallery-item__index">No.09</span>
            </div>

            <div class="gallery-item" data-id="10" data-collections="peinture" data-epoque="moderne" data-artistes="guenat"
              data-medium="huile" data-title="Paysage" data-artist="Guénat">
              <a href="<?php echo esc_url( home_url('/collections/?oeuvre_id=10') ); ?>">
                <img src="<?php echo esc_url( $assets . '/Img/tableaux/tableau-guenat-paysage.png' ); ?>" alt="Paysage - Guénat">
              </a>
              <div class="gallery-item__overlay">
                <p class="gallery-item__title">Paysage</p>
                <p class="gallery-item__meta">Guénat · Huile sur toile</p>
              </div>
              <span class="gallery-item__index">No.10</span>
            </div>

            <div class="gallery-item" data-id="11" data-collections="sculpture" data-epoque="moderne" data-artistes=""
              data-medium="bronze" data-title="Statue moderne" data-artist="Anonyme">
              <a href="<?php echo esc_url( home_url('/collections/?oeuvre_id=11') ); ?>">
                <img src="<?php echo esc_url( $assets . '/Img/statue-2.png' ); ?>" alt="Statue moderne">
              </a>
              <div class="gallery-item__overlay">
                <p class="gallery-item__title">Statue moderne</p>
                <p class="gallery-item__meta">Anonyme · Bronze</p>
              </div>
              <span class="gallery-item__index">No.11</span>
            </div>

            <div class="gallery-item" data-id="12" data-collections="peinture" data-epoque="contemporain" data-artistes=""
              data-medium="acrylique" data-title="Océan contemporain" data-artist="Artiste inconnu">
              <a href="<?php echo esc_url( home_url('/collections/?oeuvre_id=12') ); ?>">
                <img src="<?php echo esc_url( $assets . '/Img/tableaux/tableau-ocean-contemporain.png' ); ?>" alt="Océan contemporain">
              </a>
              <div class="gallery-item__overlay">
                <p class="gallery-item__title">Océan contemporain</p>
                <p class="gallery-item__meta">Artiste inconnu · Acrylique</p>
              </div>
              <span class="gallery-item__index">No.12</span>
            </div>

            <div class="gallery-item" data-id="13" data-collections="peinture" data-epoque="moderne" data-artistes="turner"
              data-medium="aquarelle" data-title="Vue de ville" data-artist="J.M.W. Turner">
              <a href="<?php echo esc_url( home_url('/collections/?oeuvre_id=13') ); ?>">
                <img src="<?php echo esc_url( $assets . '/Img/tableaux/tableau-turner.webp' ); ?>" alt="Turner - Vue de ville">
              </a>
              <div class="gallery-item__overlay">
                <p class="gallery-item__title">Vue de ville</p>
                <p class="gallery-item__meta">J.M.W. Turner · Aquarelle</p>
              </div>
              <span class="gallery-item__index">No.13</span>
            </div>

            <div class="gallery-item" data-id="14" data-collections="peinture" data-epoque="moderne" data-artistes="guenat"
              data-medium="huile" data-title="Portrait de vieillard" data-artist="Guénat">
              <a href="<?php echo esc_url( home_url('/collections/?oeuvre_id=14') ); ?>">
                <img src="<?php echo esc_url( $assets . '/Img/tableaux/tableau-guenat-vieillard.jpg' ); ?>" alt="Portrait de vieillard - Guénat">
              </a>
              <div class="gallery-item__overlay">
                <p class="gallery-item__title">Portrait de vieillard</p>
                <p class="gallery-item__meta">Guénat · Huile sur toile</p>
              </div>
              <span class="gallery-item__index">No.14</span>
            </div>

            <div class="gallery-item" data-id="15" data-collections="peinture" data-epoque="moderne" data-artistes="courbet"
              data-medium="huile" data-title="La Ferme d'Ornans" data-artist="Gustave Courbet">
              <a href="<?php echo esc_url( home_url('/collections/?oeuvre_id=15') ); ?>">
                <img src="<?php echo esc_url( $assets . '/Img/tableaux/tableau-ornans-ferme.webp' ); ?>" alt="La Ferme d'Ornans">
              </a>
              <div class="gallery-item__overlay">
                <p class="gallery-item__title">La Ferme d'Ornans</p>
                <p class="gallery-item__meta">Gustave Courbet · Huile sur toile</p>
              </div>
              <span class="gallery-item__index">No.15</span>
            </div>

            <div class="gallery-item" data-id="16" data-collections="peinture" data-epoque="contemporain"
              data-artistes="collomb" data-medium="huile" data-title="Composition" data-artist="Paul Collomb">
              <a href="<?php echo esc_url( home_url('/collections/?oeuvre_id=16') ); ?>">
                <img src="<?php echo esc_url( $assets . '/Img/tableaux/tableau-collomb-2.webp' ); ?>" alt="Composition - Paul Collomb">
              </a>
              <div class="gallery-item__overlay">
                <p class="gallery-item__title">Composition</p>
                <p class="gallery-item__meta">Paul Collomb · Huile</p>
              </div>
              <span class="gallery-item__index">No.16</span>
            </div>

            <div class="gallery-item" data-id="17" data-collections="sculpture" data-epoque="classique" data-artistes=""
              data-medium="marbre" data-title="Groupe de statues" data-artist="École italienne">
              <a href="<?php echo esc_url( home_url('/collections/?oeuvre_id=17') ); ?>">
                <img src="<?php echo esc_url( $assets . '/Img/statues-groupe.jpg' ); ?>" alt="Groupe de statues">
              </a>
              <div class="gallery-item__overlay">
                <p class="gallery-item__title">Groupe de statues</p>
                <p class="gallery-item__meta">École italienne · Marbre</p>
              </div>
              <span class="gallery-item__index">No.17</span>
            </div>

            <div class="gallery-item" data-id="18" data-collections="peinture" data-epoque="moderne" data-artistes=""
              data-medium="huile" data-title="Nu et nature morte" data-artist="Anonyme">
              <a href="<?php echo esc_url( home_url('/collections/?oeuvre_id=18') ); ?>">
                <img src="<?php echo esc_url( $assets . '/Img/tableaux/tableau-nu-nature-morte.png' ); ?>" alt="Nu et nature morte">
              </a>
              <div class="gallery-item__overlay">
                <p class="gallery-item__title">Nu et nature morte</p>
                <p class="gallery-item__meta">Anonyme · Huile</p>
              </div>
              <span class="gallery-item__index">No.18</span>
            </div>
          <?php endif; ?>

        </div><!-- /grid -->

        <div class="gallery-empty" id="galleryEmpty">
          <p>Aucune œuvre trouvée pour cette recherche.</p>
        </div>

      </div>
    </section>

    

    <section class="main__banniere">
      <section class="main__banniere--explorer">
        <img class="main__banniere-img" src="<?php echo esc_url( $assets . '/Img/svg/croix-blanche.svg' ); ?>" alt="Croix blanche">
        <h2 class="main__banniere-titre">ESPACE<br>PÉDAGOGIQUE</h2>
      </section>
      <section class="main__banniere-contenu">
        <h3 class="main__banniere-sous-titre">Comprendre une œuvre d'art</h3>
        <p class="main__banniere-description">Comment regarder une œuvre d'art ? Découvrir une œuvre, c'est apprendre à
          observer, ressentir et comprendre ce que l'artiste a voulu exprimer.</p>
      </section>
    </section>

    <!-- ── MOUVEMENTS ARTISTIQUES ─────────────────────────────────────── -->
    <section class="main__banniere">
      <section class="main__banniere--explorer">
        <img class="main__banniere-img" src="<?php echo esc_url( $assets . '/Img/svg/croix-blanche.svg' ); ?>" alt="Croix blanche">
        <h2 class="main__banniere-titre">MOUVEMENTS<br>artistiques</h2>
      </section>
      <section class="main__banniere-contenu">
        <h3 class="main__banniere-sous-titre">Les grands courants artistiques</h3>
        <p class="main__banniere-description">Un voyage à travers le temps et les styles.</p>
      </section>
    </section>

    <!-- ── TABLEAU DES MOUVEMENTS ARTISTIQUES ─────────────────────────────────────── -->
    <section class="art-movements-table-section">
      <div class="art-movements-table">
        <div class="table-header">
          <div class="header-cell">Période</div>
          <div class="header-cell">Mouvement</div>
          <div class="header-cell">Caractéristiques</div>
          <div class="header-cell">Artistes majeurs</div>
        </div>
        <?php
        // Essayer de récupérer les données depuis la BDD d'abord
        global $wpdb;
        $mouvements_db = $wpdb->get_results(
            "SELECT * FROM {$wpdb->prefix}mbaa_mouvement_artistique ORDER BY ordre ASC, id_mouvement ASC",
            ARRAY_A
        );
        
        // Si la BDD a des données, les utiliser
        if (!empty($mouvements_db)) {
            foreach ($mouvements_db as $mouvement) {
                $periode = '';
                if (!empty($mouvement['periode_debut'])) {
                    $periode .= $mouvement['periode_debut'];
                }
                if (!empty($mouvement['periode_fin'])) {
                    $periode .= '-' . $mouvement['periode_fin'];
                }
                if (empty($periode)) {
                    $periode = 'N/A';
                }
        ?>
        <div class="table-row">
          <div class="cell period"><?php echo esc_html($periode); ?></div>
          <div class="cell movement"><?php echo esc_html($mouvement['nom_mouvement']); ?></div>
          <div class="cell characteristics"><?php echo esc_html($mouvement['description'] ?? 'Caractéristiques à définir'); ?></div>
          <div class="cell artists">
            <?php 
            $artistes = !empty($mouvement['artistes_majeurs']) ? explode(',', $mouvement['artistes_majeurs']) : ['Artistes à définir'];
            foreach ($artistes as $artiste): ?>
              <span class="artist-badge"><?php echo esc_html(trim($artiste)); ?></span>
            <?php endforeach; ?>
          </div>
        </div>
        <?php 
            }
        } else {
            // Fallback : données statiques éducatives (contenu encyclopédique)
            $mouvements_table = [
                ['1400-1600','Renaissance','Retour à l\'Antiquité classique, perspective, réalisme anatomique.',['Léonard de Vinci','Michel-Ange','Raphaël','Botticelli','Titien']],
                ['1600-1750','Baroque','Dramatisme, mouvement intense, clair-obscur, grandeur théâtrale.',['Caravage','Rembrandt','Rubens','Vélasquez','Bernini']],
                ['1750-1820','Néoclassicisme','Retour aux valeurs antiques, rigueur, noblesse, vertus civiques.',['Jacques-Louis David','Ingres','Canova','Gros']],
                ['1800-1850','Romantisme','Primauté de l\'émotion, nature sauvage, passions humaines.',['Delacroix','Géricault','Turner','Friedrich','Goya']],
                ['1850-1900','Réalisme','Représentation fidèle du quotidien, rejet de l\'idéalisation.',['Courbet','Millet','Daumier','Manet']],
                ['1870-1900','Impressionnisme','Effets de lumière, touches rapides, couleurs vives, plein air.',['Monet','Renoir','Degas','Pissarro','Sisley']],
                ['1885-1910','Post-Impressionnisme','Structures, émotions intenses, exploration personnelle.',['Van Gogh','Cézanne','Gauguin','Seurat','Toulouse-Lautrec']],
                ['1907-1920','Cubisme','Déconstruction géométrique, multiples points de vue.',['Picasso','Braque','Léger','Juan Gris']],
                ['1924-1950','Surréalisme','Inconscient, rêve, automatisme psychique, Freud.',['Dalí','Magritte','Miró','Ernst','Tanguy']],
                ['1940-1960','Expressionnisme abstrait','Abstraction gestuelle, grands formats, action painting.',['Pollock','Rothko','De Kooning','Newman']],
                ['1950-1970','Pop Art','Culture populaire, publicité, critique de la consommation.',['Warhol','Lichtenstein','Hockney','Oldenburg']],
                ['1980-2000','Art Contemporain','Pluralité des pratiques, installation, performance, vidéo.',['Jeff Koons','Damien Hirst','Ai Weiwei','Banksy','Yayoi Kusama']],
            ];
            foreach ( $mouvements_table as $row ) : ?>
        <div class="table-row">
          <div class="cell period"><?php echo esc_html( $row[0] ); ?></div>
          <div class="cell movement"><?php echo esc_html( $row[1] ); ?></div>
          <div class="cell characteristics"><?php echo esc_html( $row[2] ); ?></div>
          <div class="cell artists">
            <?php foreach ( $row[3] as $artiste ) : ?>
              <span class="artist-badge"><?php echo esc_html( $artiste ); ?></span>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endforeach; 
        } ?>
      </div>
    </section>

    <!-- ── SAVOIR DISTINGUER ─────────────────────────────────────────── -->
    <section class="main__banniere">
      <section class="main__banniere--explorer">
        <img class="main__banniere-img" src="<?php echo esc_url( $assets . '/Img/svg/croix-blanche.svg' ); ?>" alt="Croix blanche">
        <h2 class="main__banniere-titre">SAVOIR<br>DISTINGUER</h2>
        <p class="main__banniere-description">Les différentes techniques artistiques.</p>
      </section>
    </section>

    <!-- ── GRILLE DES TECHNIQUES ARTISTIQUES ─────────────────────────────────────────── -->
    <section class="techniques-grid-section">
      <div class="techniques-grid-container">
        <div class="techniques-grid">
          <?php
          // Essayer de récupérer les techniques depuis la BDD d'abord
          global $wpdb;
          $techniques_db = $wpdb->get_results(
              "SELECT * FROM {$wpdb->prefix}mbaa_technique ORDER BY ordre ASC, id_technique ASC",
              ARRAY_A
          );
          
          // Si la BDD a des données, les utiliser
          if (!empty($techniques_db)) {
              foreach ($techniques_db as $technique) {
                  $image_url = !empty($technique['image_url']) ? $technique['image_url'] : $assets . '/Img/techniques/default.jpg';
          ?>
          <div class="technique-card">
            <div class="technique-card-image">
              <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($technique['nom_technique']); ?>">
              <div class="technique-card-overlay"></div>
            </div>
            <div class="technique-card-content">
              <h3 class="technique-card-title"><?php echo esc_html($technique['nom_technique']); ?></h3>
              <p class="technique-card-description"><?php echo esc_html($technique['description'] ?? 'Description à définir'); ?></p>
            </div>
          </div>
          <?php 
              }
          } else {
              // Fallback : données statiques
              $techniques = [
                  ['tableau-classicisme.jpg',     'Peinture à l\'huile', 'Riche en couleurs et en relief, elle permet des effets de lumière subtils.'],
                  ['tableau-turner-ocean.jpg',    'Aquarelle',           'Légère et transparente, elle joue avec l\'eau et le papier.'],
                  ['statues-groupe.jpg',          'Sculpture',           'Travail de la matière : pierre, bois, métal, plâtre ou argile.'],
                  ['tableau-craie-grasse.jpg',   'Craie grasse',        'Bâtonnet de pigment mélangé à de la cire, idéal pour le dessin expressif.'],
                  ['tableau-fusain.jpg',         'Fusain',              'Bâtonnet de bois carbonisé pour le dessin et les esquisses.'],
                  ['tableau-encre.jpg',          'Encre',               'Technique de dessin utilisant de l\'encre noire ou colorée.'],
              ];
              foreach ( $techniques as $t ) : ?>
          <div class="technique-card">
            <div class="technique-card-image">
              <img src="<?php echo esc_url( $assets . '/Img/' . ( strpos($t[0], '/') === false ? 'tableaux/' . $t[0] : $t[0] ) ); ?>" alt="<?php echo esc_attr( $t[1] ); ?>">
              <div class="technique-card-overlay"></div>
            </div>
            <div class="technique-card-content">
              <h3 class="technique-card-title"><?php echo esc_html( $t[1] ); ?></h3>
              <p class="technique-card-description"><?php echo esc_html( $t[2] ); ?></p>
            </div>
          </div>
          <?php endforeach; 
          } ?>
        </div>
      </div>
      <a href="#" class="btn-collection">En savoir plus</a>
    </section>

    <!-- ── COMPOSITION & INTENTION ────────────────────────────────────── -->
    <section class="pedagogical-elements-compo">
      <img class="pedagogical-elements-compo"
           src="<?php echo esc_url( $assets . '/Img/tableaux/tableau-mondrian.jpg' ); ?>"
           alt="Composition Mondrian">
      <div class="pedagogical-elements-compo-text">
        <h2 class="pedagogical-elements-compo-title">La composition et l'équilibre</h2>
        <p class="pedagogical-elements-compo-text">Chaque élément est placé avec soin pour attirer notre regard vers ce que l'artiste veut montrer.</p>
        <h2 class="pedagogical-elements-compo-title">L'intention de l'artiste</h2>
        <p class="pedagogical-elements-compo-text">Une œuvre peut raconter une histoire, dénoncer, célébrer ou simplement faire rêver.</p>
        <h2 class="pedagogical-elements-compo-title">Le contexte historique et culturel</h2>
        <p class="pedagogical-elements-compo-text">L'époque et la société influencent l'artiste. Comprendre quand et pourquoi une œuvre a été créée aide à mieux la lire.</p>
      </div>
    </section>

    <!-- ── ESPACE PÉDAGOGIQUE : ÉLÉMENTS VISUELS ──────────────────────────────────────────── -->
    <section class="pedagogical-elements-section">
      <div class="pedagogical-elements-container">
        <div class="pedagogical-elements-left">
          <?php
          $elements_1 = [ 'couleur' => 'La couleur', 'ligne' => 'La ligne', 'forme' => 'La forme', 'espace' => "L'espace" ];
          foreach ( $elements_1 as $key => $label ) : ?>
            <button class="pedagogical-element-item" data-filter="<?php echo esc_attr( $key ); ?>">
              <span class="pedagogical-element-text"><?php echo esc_html( $label ); ?></span>
              <img src="<?php echo esc_url( $assets . '/Img/svg/icon-arrow-filtre.svg' ); ?>" alt="">
            </button>
            <div class="pedagogical-element-separator"></div>
          <?php endforeach; ?>
        </div>
        <div class="pedagogical-elements-right">
          <img src="<?php echo esc_url( $assets . '/Img/tableaux/tableau-signac-venise.jpg' ); ?>" alt="Peinture de Venise" class="pedagogical-painting">
        </div>
      </div>
    </section>

    <section class="pedagogical-elements-section">
      <div class="pedagogical-elements-container">
        <div class="pedagogical-elements-left">
          <?php
          $elements_2 = [ 'nature-morte' => 'Nature morte', 'paysage' => 'Paysage', 'palette' => 'Palette', 'scene-genre' => 'Scène de genre', 'perspective' => 'Perspective', 'clair-obscur' => 'Clair-obscur' ];
          foreach ( $elements_2 as $key => $label ) : ?>
            <button class="pedagogical-element-item" data-filter="<?php echo esc_attr( $key ); ?>">
              <span class="pedagogical-element-text"><?php echo esc_html( $label ); ?></span>
              <img src="<?php echo esc_url( $assets . '/Img/svg/icon-arrow-filtre.svg' ); ?>" alt="">
            </button>
            <div class="pedagogical-element-separator"></div>
          <?php endforeach; ?>
        </div>
        <div class="pedagogical-elements-right">
          <img src="<?php echo esc_url( $assets . '/Img/tableaux/tableau-nature-morte.jpg' ); ?>" alt="Nature morte" class="pedagogical-painting">
        </div>
      </div>
    </section>

    <!-- ── FICHIER PÉDAGOGIQUE ─────────────────────────────────────────── -->
    <div class="pedagogical-file">
      <h2 class="pedagogical-file-title">Fichier pédagogique</h2>
      <p class="pedagogical-file-text">Téléchargez le fichier pédagogique complet au format PDF pour une exploration approfondie des collections.</p>
      <a class="pedagogical-file-link" href="#">
        Télécharger la brochure
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
          <path d="M12 16L8 12H11V4H13V12H16L12 16ZM20 18V20H4V18H2V20C2 21.1 2.9 22 4 22H20C21.1 22 22 21.1 22 20V18H20Z" fill="currentColor"/>
        </svg>
      </a>
    </div>

<?php get_footer(); ?>

<!-- JavaScript pour la recherche améliorée -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('gallerySearch');
    const clearButton = document.getElementById('searchClear');
    const resultsContainer = document.getElementById('searchResults');
    const resultsCount = document.getElementById('resultsCount');
    const resultsList = document.getElementById('resultsList');
    const galleryItems = document.querySelectorAll('.gallery-item');
    
    let searchTimeout;

    // Fonction de recherche
    function performSearch(searchTerm) {
        clearTimeout(searchTimeout);
        
        searchTimeout = setTimeout(() => {
            const term = searchTerm.toLowerCase().trim();
            
            if (term.length === 0) {
                clearSearch();
                return;
            }

            // Afficher le bouton clear
            clearButton.style.display = 'flex';
            
            // Filtrer les items
            const filteredItems = Array.from(galleryItems).filter(item => {
                const title = item.querySelector('.gallery-item__title')?.textContent.toLowerCase() || '';
                const artist = item.querySelector('.gallery-item__artist')?.textContent.toLowerCase() || '';
                const technique = item.querySelector('.gallery-item__technique')?.textContent.toLowerCase() || '';
                
                return title.includes(term) || artist.includes(term) || technique.includes(term);
            });

            // Afficher les résultats
            displayResults(filteredItems, term);
            
            // Mettre à jour la galerie
            updateGalleryDisplay(filteredItems, term);
        }, 300); // Debounce de 300ms
    }

    // Afficher les résultats de recherche
    function displayResults(items, searchTerm) {
        resultsContainer.style.display = 'block';
        resultsCount.textContent = items.length;
        
        resultsList.innerHTML = '';
        
        if (items.length === 0) {
            resultsList.innerHTML = `
                <div class="search-results__empty">
                    <div class="search-results__empty-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"/>
                            <path d="m21 21-4.35-4.35"/>
                        </svg>
                    </div>
                    <p>Aucune œuvre trouvée pour "${searchTerm}"</p>
                    <p>Essayez avec d'autres mots-clés</p>
                </div>
            `;
        } else {
            // Limiter à 5 résultats pour l'affichage
            items.slice(0, 5).forEach(item => {
                const title = item.querySelector('.gallery-item__title')?.textContent || '';
                const artist = item.querySelector('.gallery-item__artist')?.textContent || '';
                const image = item.querySelector('.gallery-item__img')?.src || '';
                const itemLink = item.querySelector('a')?.href || '#';
                
                const resultItem = document.createElement('div');
                resultItem.className = 'search-results__item';
                resultItem.innerHTML = `
                    <div class="search-results__item-image">
                        <img src="${image}" alt="${title}" loading="lazy">
                    </div>
                    <div class="search-results__item-content">
                        <h4 class="search-results__item-title">${title}</h4>
                        <p class="search-results__item-artist">${artist}</p>
                    </div>
                `;
                
                resultItem.addEventListener('click', () => {
                    window.location.href = itemLink;
                });
                
                resultsList.appendChild(resultItem);
            });
            
            if (items.length > 5) {
                const showMore = document.createElement('div');
                showMore.className = 'search-results__show-more';
                showMore.innerHTML = `<span>Voir ${items.length - 5} résultats supplémentaires</span>`;
                showMore.addEventListener('click', () => {
                    resultsContainer.style.display = 'none';
                    // Scroll vers la galerie
                    document.querySelector('.gallery-grid').scrollIntoView({ behavior: 'smooth' });
                });
                resultsList.appendChild(showMore);
            }
        }
    }

    // Mettre à jour l'affichage de la galerie
    function updateGalleryDisplay(filteredItems, searchTerm) {
        galleryItems.forEach(item => {
            const title = item.querySelector('.gallery-item__title')?.textContent.toLowerCase() || '';
            const artist = item.querySelector('.gallery-item__artist')?.textContent.toLowerCase() || '';
            const technique = item.querySelector('.gallery-item__technique')?.textContent.toLowerCase() || '';
            
            const matches = title.includes(searchTerm) || artist.includes(searchTerm) || technique.includes(searchTerm);
            
            if (matches) {
                item.style.display = 'block';
                item.classList.add('search-match');
                // Surligner le terme de recherche
                highlightSearchTerm(item, searchTerm);
            } else {
                item.style.display = 'none';
                item.classList.remove('search-match');
            }
        });
    }

    // Surligner le terme de recherche
    function highlightSearchTerm(item, term) {
        const titleElement = item.querySelector('.gallery-item__title');
        const artistElement = item.querySelector('.gallery-item__artist');
        
        if (titleElement) {
            const originalText = titleElement.textContent;
            const regex = new RegExp(`(${term})`, 'gi');
            titleElement.innerHTML = originalText.replace(regex, '<mark>$1</mark>');
        }
        
        if (artistElement) {
            const originalText = artistElement.textContent;
            const regex = new RegExp(`(${term})`, 'gi');
            artistElement.innerHTML = originalText.replace(regex, '<mark>$1</mark>');
        }
    }

    // Vider la recherche
    function clearSearch() {
        searchInput.value = '';
        clearButton.style.display = 'none';
        resultsContainer.style.display = 'none';
        
        galleryItems.forEach(item => {
            item.style.display = 'block';
            item.classList.remove('search-match');
            
            // Retirer les surlignages
            const titleElement = item.querySelector('.gallery-item__title');
            const artistElement = item.querySelector('.gallery-item__artist');
            
            if (titleElement) titleElement.innerHTML = titleElement.textContent;
            if (artistElement) artistElement.innerHTML = artistElement.textContent;
        });
    }

    // Événements
    searchInput.addEventListener('input', (e) => {
        performSearch(e.target.value);
    });

    clearButton.addEventListener('click', clearSearch);

    // Fermer les résultats en cliquant ailleurs
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.gallery-search')) {
            resultsContainer.style.display = 'none';
        }
    });

    // Focus sur la recherche avec Ctrl+K
    document.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            searchInput.focus();
        }
    });
});
</script>

<!-- Styles pour la recherche améliorée -->
<style>
.gallery-search__wrapper {
    position: relative;
    display: flex;
    align-items: center;
    background: #f8f9fa;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    padding: 12px 16px;
    transition: all 0.3s ease;
}

.gallery-search__wrapper:focus-within {
    border-color: #c9a227;
    background: white;
    box-shadow: 0 0 0 4px rgba(201, 162, 39, 0.1);
}

.gallery-search__icon {
    color: #6c757d;
    width: 20px;
    height: 20px;
    margin-right: 12px;
    transition: color 0.3s ease;
}

.gallery-search__wrapper:focus-within .gallery-search__icon {
    color: #c9a227;
}

.gallery-search__input {
    flex: 1;
    border: none;
    background: transparent;
    outline: none;
    font-size: 16px;
    color: #333;
}

.gallery-search__input::placeholder {
    color: #6c757d;
}

.gallery-search__clear {
    display: none;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
    border: none;
    background: #e9ecef;
    border-radius: 50%;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-left: 8px;
}

.gallery-search__clear:hover {
    background: #dc3545;
    color: white;
}

.gallery-search__clear svg {
    width: 16px;
    height: 16px;
}

.gallery-search__results {
    position: absolute;
    top: calc(100% + 8px);
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    max-height: 400px;
    overflow-y: auto;
}

.search-results__header {
    padding: 12px 16px;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    align-items: center;
    gap: 4px;
}

.search-results__count {
    font-weight: 600;
    color: #c9a227;
}

.search-results__label {
    color: #6c757d;
}

.search-results__list {
    max-height: 300px;
    overflow-y: auto;
}

.search-results__item {
    display: flex;
    align-items: center;
    padding: 12px 16px;
    cursor: pointer;
    transition: background 0.2s ease;
    border-bottom: 1px solid #f8f9fa;
}

.search-results__item:hover {
    background: #f8f9fa;
}

.search-results__item:last-child {
    border-bottom: none;
}

.search-results__item-image {
    width: 48px;
    height: 48px;
    border-radius: 8px;
    overflow: hidden;
    margin-right: 12px;
    flex-shrink: 0;
}

.search-results__item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.search-results__item-content {
    flex: 1;
    min-width: 0;
}

.search-results__item-title {
    font-weight: 600;
    color: #333;
    margin: 0 0 4px 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.search-results__item-artist {
    color: #6c757d;
    margin: 0;
    font-size: 14px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.search-results__empty {
    padding: 32px 16px;
    text-align: center;
    color: #6c757d;
}

.search-results__empty-icon {
    width: 48px;
    height: 48px;
    margin: 0 auto 16px;
    opacity: 0.5;
}

.search-results__empty p {
    margin: 4px 0;
}

.search-results__empty p:first-child {
    font-weight: 600;
    color: #333;
}

.search-results__show-more {
    padding: 12px 16px;
    text-align: center;
    cursor: pointer;
    color: #c9a227;
    font-weight: 500;
    border-top: 1px solid #e9ecef;
    transition: background 0.2s ease;
}

.search-results__show-more:hover {
    background: #f8f9fa;
}

mark {
    background: #fff3cd;
    color: #856404;
    padding: 1px 2px;
    border-radius: 2px;
}

.gallery-item.search-match {
    animation: searchHighlight 0.3s ease;
}

@keyframes searchHighlight {
    0% { transform: scale(1); }
    50% { transform: scale(1.02); }
    100% { transform: scale(1); }
}

@media (max-width: 768px) {
    .gallery-search__results {
        max-height: 300px;
    }
    
    .search-results__item-image {
        width: 40px;
        height: 40px;
    }
}
</style>
