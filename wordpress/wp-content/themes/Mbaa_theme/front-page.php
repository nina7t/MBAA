<?php
/**
 * Template Name: Accueil
 * Template pour la page d'accueil du MBAA
 * Remplace index.html
 *
 * @package Mbaa_theme
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// ── Données dynamiques depuis la BDD MBAA ──────────────────────────────────
global $wpdb;

// Prochain événement mis en avant (le plus proche dans le futur)
$db_prefix   = $wpdb->prefix;
$exposition  = $wpdb->get_row(
    "SELECT * FROM {$db_prefix}mbaa_exposition
     WHERE date_fin >= CURDATE()
     ORDER BY date_debut ASC
     LIMIT 1",
    ARRAY_A
);

// Événements à venir pour le carousel (12 max)
$evenements = $wpdb->get_results(
    "SELECT e.*, t.nom_type, t.categorie as type_categorie
     FROM {$db_prefix}mbaa_evenement e
     LEFT JOIN {$db_prefix}mbaa_type_evenement t ON e.id_type_evenement = t.id_type_evenement
     WHERE e.date_evenement >= CURDATE()
     ORDER BY e.date_evenement ASC
     LIMIT 12",
    ARRAY_A
);

// Helper — URL assets du thème
$assets = get_template_directory_uri() . '/asset';

get_header();
?>

<?php wp_body_open(); ?>

<!-- ═══════════════════════════════════════════════════
     HEADER / HERO
═══════════════════════════════════════════════════ -->
<header class="header header--index">

  <!-- NAVBAR -->
  <div class="header__container">
    <a class="header__logo-link" href="<?php echo esc_url( home_url('/') ); ?>">
      <img class="header__logo-img"
           src="<?php echo esc_url( $assets . '/Img/logo/logo-mat-small.png' ); ?>"
           alt="Logo MBAA" />
    </a>

    <button class="header__menu-toggle"
            aria-label="menu"
            aria-expanded="false"
            aria-controls="headerNav">
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
          <a class="header__nav-link" href="<?php echo esc_url( home_url('/evenements/') ); ?>">Évènements</a>
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
          <a class="header__nav-link"
             href="<?php echo esc_url( wp_login_url() ); ?>"
             aria-label="Connexion">
            <svg class="header__nav-icon" viewBox="0 0 24 24" fill="none">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
              <circle cx="12" cy="7" r="4"></circle>
            </svg>
          </a>
        </li>
        <li class="header__nav-item">
          <a class="header__nav-link"
             href="<?php echo esc_url( home_url('/reservation/') ); ?>"
             aria-label="Billetterie">
            <svg class="header__nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"></path>
              <path d="M13 5v2"></path><path d="M13 17v2"></path><path d="M13 11v2"></path>
            </svg>
          </a>
        </li>
        <li class="header__nav-item">
          <a class="header__nav-link" href="#" id="search-trigger" aria-label="Recherche">
            <svg class="header__nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="11" cy="11" r="8"></circle>
              <path d="m21 21-4.35-4.35"></path>
            </svg>
          </a>
        </li>
        <li class="header__nav-item"><a class="header__nav-link-fr" href="#">FR</a></li>
        <li class="header__nav-item"><a class="header__nav-link-fr" href="#">EN</a></li>
      </ul>
    </nav>
  </div><!-- /.header__container -->

  <!-- HERO -->
  <div class="header__hero">
    <div class="hero__left">
      <h1 class="hero__title">
        Musée<br>D'art &<br>du temps<br>
        <em>Besançon</em>
      </h1>
    </div>

    <div class="hero__right">
      <a href="<?php echo esc_url( home_url('/reservation/') ); ?>" class="hero__cta">
        Réserver ma place
        <img class="hero__cta-icon"
             src="<?php echo esc_url( $assets . '/Img/svg/icon-arrow-droite.svg' ); ?>"
             alt="Flèche vers la droite">
      </a>
      <p class="hero__description">Plongez dans 5000 ans d'art</p>
    </div>

    <div class="hero__strip">
      <div class="hero__stat">
        <span class="hero__stat-number">5 600</span>
        <span class="hero__stat-label">Œuvres exposées</span>
      </div>
      <div class="hero__strip-divider"></div>
      <div class="hero__stat">
        <span class="hero__stat-number">XVI<sup style="font-size:.55em">e</sup></span>
        <span class="hero__stat-label">Période la plus ancienne</span>
      </div>
      <div class="hero__strip-divider"></div>
      <div class="hero__stat">
        <span class="hero__stat-number">Gratuit</span>
        <span class="hero__stat-label">Moins de 26 ans</span>
      </div>
      <div class="hero__scroll">
        <div class="hero__scroll-arrow">
          <img src="<?php echo esc_url( $assets . '/Img/svg/arrow_bas.svg' ); ?>" alt="Défiler">
        </div>
        Défiler
      </div>
    </div>
  </div><!-- /.header__hero -->

</header>

<!-- ═══════════════════════════════════════════════════
     MAIN
═══════════════════════════════════════════════════ -->
<main class="main">

  <!-- ── BANNIÈRE PROGRAMMATION ──────────────────── -->
  <section class="main__programmation">
    <section class="main__banniere-prog">
      <section class="main__banniere--programmation">
        <img class="main__banniere-img-fonce"
             src="<?php echo esc_url( $assets . '/Img/svg/croix-black.svg' ); ?>"
             alt="Croix noire" />
        <h2 class="main__banniere-titre-fonce">
          Programmation<br />octobre &rsaquo; avril
        </h2>
      </section>
    </section>
  </section>

  <!-- ── EXPOSITION MISE EN AVANT ────────────────── -->
  <section class="main__card">
    <div class="main__card-agencement">
      <?php if ( ! empty( $exposition['image_url'] ) ) : ?>
        <img class="main__card-img"
             src="<?php echo esc_url( $exposition['image_url'] ); ?>"
             alt="<?php echo esc_attr( $exposition['titre'] ); ?>" />
      <?php else : ?>
        <img class="main__card-img"
             src="<?php echo esc_url( $assets . '/Img/visite-groupe.jpg' ); ?>"
             alt="Groupe en visite" />
      <?php endif; ?>
    </div>

    <div class="main__card-container">
      <h2 class="main__card-title">Expositions et accrochages</h2>

      <?php if ( $exposition ) : ?>
        <h3 class="main__card-subtitle">
          <?php echo esc_html( $exposition['titre'] ); ?>
        </h3>
        <h4 class="main__card-text">
          <?php
            echo esc_html( date_i18n( 'd.m', strtotime( $exposition['date_debut'] ) ) )
               . ' - '
               . esc_html( date_i18n( 'd.m.Y', strtotime( $exposition['date_fin'] ) ) );
          ?>
        </h4>
        <p class="main__card-description">
          <?php echo wp_kses_post( $exposition['description'] ); ?>
        </p>
      <?php else : ?>
        <h3 class="main__card-subtitle">Ceija Stojka / «&nbsp;Garder les yeux ouverts&nbsp;»</h3>
        <h4 class="main__card-text">27.02 - 21.09.2026</h4>
        <p class="main__card-description">
          Née en Autriche, dans la communauté rom des Lovara, Ceija Stojka (1933-2013)
          est une artiste autodidacte, rescapée des camps de concentration d'Auschwitz,
          Ravensbrück et Bergen-Belsen…
        </p>
      <?php endif; ?>

      <a class="main__button--savoir-white"
         href="<?php echo esc_url( home_url('/exposition/') ); ?>">En savoir +</a>
    </div>
  </section>

  <!-- ── PRÉPARER VOTRE VISITE ───────────────────── -->
  <section class="main__visite">
    <section class="main__banniere">
      <section class="main__banniere--visite">
        <img class="main__banniere-img"
             src="<?php echo esc_url( $assets . '/Img/svg/croix-blanche.svg' ); ?>"
             alt="Croix blanche" />
        <h2 class="main__banniere-titre">Préparer<br />votre visite</h2>
      </section>
      <section class="main__banniere-contenu">
        <p class="main__banniere-description">
          Préparez votre découverte du musée en toute sérénité&nbsp;: horaires,
          accès, services et conseils pour une visite agréable.
        </p>
      </section>
    </section>

    <section class="main__visite-cards">

      <!-- Card Horaires -->
      <section class="main__visite-card">
        <h3 class="main__visite-card-title">+ Horaires</h3>

        <h4 class="main__visite-card-subtitle">Saison basse (2 nov – 31 mars)</h4>
        <p class="main__visite-card-small-text">Hors vacances scolaires</p>
        <div class="main__visite-card-row">
          <img class="main__visite-card-arrow"
               src="<?php echo esc_url( $assets . '/Img/svg/icon-arrow-droite.svg' ); ?>" alt />
          <p class="main__visite-card-text">Lundi, mercredi, jeudi, vendredi&nbsp;: 14h–18h</p>
        </div>

        <h4 class="main__visite-card-subtitle">Saison haute (1er avril – 31 oct)</h4>
        <p class="main__visite-card-small-text">Et pendant les vacances scolaires zone A</p>
        <div class="main__visite-card-row">
          <img class="main__visite-card-arrow"
               src="<?php echo esc_url( $assets . '/Img/svg/icon-arrow-droite.svg' ); ?>" alt />
          <p class="main__visite-card-text">Lundi, mercredi, jeudi, vendredi&nbsp;: 10h–12h30 / 14h–18h</p>
        </div>

        <h4 class="main__visite-card-subtitle">Toute l'année</h4>
        <div class="main__visite-card-row">
          <img class="main__visite-card-arrow"
               src="<?php echo esc_url( $assets . '/Img/svg/icon-arrow-droite.svg' ); ?>" alt />
          <p class="main__visite-card-text">Samedi, dimanche et jours fériés&nbsp;: 10h–18h sans interruption</p>
        </div>

        <p class="main__visite-card-text-bold">Fermé le mardi</p>
        <p class="main__visite-card-text-bold">Fermetures annuelles&nbsp;: 1er janvier, 1er mai, 1er novembre, 25 décembre</p>
        <h4 class="main__visite-card-subtitle--orange">Accueil des groupes à partir de 9h</h4>
        <div class="main__visite-button">
          <a class="main__button--visite-black"
             href="<?php echo esc_url( home_url('/infos-pratiques/') ); ?>">En savoir +</a>
        </div>
      </section>

      <!-- Card Tarifs -->
      <section class="main__visite-card">
        <h3 class="main__visite-card-title">+ Tarifs</h3>

        <h4 class="main__visite-card-subtitle">Billet couplé (MBAA + Musée du Temps + Maison Victor Hugo)</h4>
        <ul class="main__visite-card-column">
          <li class="main__visite-card-row">
            <img class="main__visite-card-arrow"
                 src="<?php echo esc_url( $assets . '/Img/svg/icon-arrow-droite.svg' ); ?>" alt />
            <span class="main__visite-card-text">Plein tarif&nbsp;:</span>
            <span class="main__visite-card-text">9&nbsp;€</span>
          </li>
          <li class="main__visite-card-row">
            <img class="main__visite-card-arrow"
                 src="<?php echo esc_url( $assets . '/Img/svg/icon-arrow-droite.svg' ); ?>" alt />
            <span class="main__visite-card-text">Tarif réduit&nbsp;:</span>
            <span class="main__visite-card-text">7&nbsp;€</span>
          </li>
          <li class="main__visite-card-row">
            <img class="main__visite-card-arrow"
                 src="<?php echo esc_url( $assets . '/Img/svg/icon-arrow-droite.svg' ); ?>" alt />
            <span class="main__visite-card-text">Jeune (18–25 ans)&nbsp;:</span>
            <span class="main__visite-card-text">4,50&nbsp;€</span>
          </li>
        </ul>

        <h4 class="main__visite-card-subtitle">Entrée gratuite</h4>
        <ul class="main__visite-card-column">
          <li class="main__visite-card-row">
            <img class="main__visite-card-arrow"
                 src="<?php echo esc_url( $assets . '/Img/svg/icon-arrow-droite.svg' ); ?>" alt />
            <p class="main__visite-card-text">
              -18 ans, étudiants, demandeurs d'emploi, bénéficiaires des minimas sociaux,
              personnes en situation de handicap et leur accompagnateur, enseignants
              (sur présentation d'un justificatif)
            </p>
          </li>
          <li class="main__visite-card-row">
            <img class="main__visite-card-arrow"
                 src="<?php echo esc_url( $assets . '/Img/svg/icon-arrow-droite.svg' ); ?>" alt />
            <p class="main__visite-card-text">Le premier dimanche du mois (hors expositions temporaires)</p>
          </li>
          <li class="main__visite-card-row">
            <img class="main__visite-card-arrow"
                 src="<?php echo esc_url( $assets . '/Img/svg/icon-arrow-droite.svg' ); ?>" alt />
            <p class="main__visite-card-text">Les membres de l'ICOM et de l'ICOMOS</p>
          </li>
        </ul>
        <div class="main__visite-button">
          <a class="main__button--visite-black"
             href="<?php echo esc_url( home_url('/infos-pratiques/') ); ?>">En savoir +</a>
        </div>
      </section>

    </section>
  </section>

  <!-- ── ÉVÉNEMENTS & CAROUSEL ───────────────────── -->
  <section class="main__evenement">
    <section class="main__banniere">
      <section class="main__banniere--evenement">
        <img class="main__banniere-img"
             src="<?php echo esc_url( $assets . '/Img/svg/croix-blanche.svg' ); ?>"
             alt="Croix blanche" />
        <h2 class="main__banniere-titre">
          ÉVÈNEMENTS &amp; ACTIVITÉS<br />
          <?php echo esc_html( strtoupper( date_i18n( 'M Y' ) ) ); ?>
        </h2>
      </section>
      <section class="main__banniere-contenu">
        <p class="main__banniere-description">
          Tout au long de la saison, le musée propose des ateliers, visites guidées,
          conférences et rencontres pour tous les âges.
        </p>
      </section>
    </section>

    <section class="carousel">
      <section class="filtre">
        <h3 class="filtre__title">Filtrer par type&nbsp;:</h3>
        <ul class="filtre__list">
          <li class="filtre__list-item filtre__list-item--active" data-filter="tous">Tous</li>
          <li class="filtre__list-item" data-filter="ateliers">
            <img class="filtre__list-img" src="<?php echo esc_url( $assets . '/Img/svg/icon-arrow-filtre.svg' ); ?>" alt="">
            Ateliers
          </li>
          <li class="filtre__list-item" data-filter="visites">
            <img class="filtre__list-img" src="<?php echo esc_url( $assets . '/Img/svg/icon-arrow-filtre.svg' ); ?>" alt="">
            Visites
          </li>
          <li class="filtre__list-item" data-filter="conferences">
            <img class="filtre__list-img" src="<?php echo esc_url( $assets . '/Img/svg/icon-arrow-filtre.svg' ); ?>" alt="">
            Conférences
          </li>
          <li class="filtre__list-item" data-filter="nouveaute">
            <img class="filtre__list-img" src="<?php echo esc_url( $assets . '/Img/svg/icon-arrow-filtre.svg' ); ?>" alt="">
            Nouveautés
          </li>
          <li class="filtre__list-item" data-filter="prix">
            <img class="filtre__list-img" src="<?php echo esc_url( $assets . '/Img/svg/icon-arrow-filtre.svg' ); ?>" alt="">
            Prix
          </li>
        </ul>
        <div class="carousel__controls">
          <button class="carousel__arrow carousel__arrow--prev" aria-label="Précédent">
            <img src="<?php echo esc_url( $assets . '/Img/svg/icon-arrow-gauche-hover.svg' ); ?>" alt="Précédent">
          </button>
          <button class="carousel__arrow carousel__arrow--next" aria-label="Suivant">
            <img src="<?php echo esc_url( $assets . '/Img/svg/icon-arrow-droite.svg' ); ?>" alt="Suivant">
          </button>
        </div>
      </section>

      <div class="carousel__list swiper">
        <div class="swiper-wrapper">
        <?php if ( ! empty( $evenements ) ) : ?>
          <?php foreach ( $evenements as $evt ) :
            // Mapping catégorie BDD → filtre HTML
            $cat_map = [
              'Atelier'    => 'ateliers',
              'Visite'     => 'visites',
              'Conférence' => 'conferences',
            ];
            $cat_slug   = $cat_map[ $evt['type_categorie'] ] ?? 'tous';
            
            // Vérifier si c'est une nouveauté (créé il y a moins de 30 jours)
            $created_date = new DateTime($evt['date_creation'] ?? $evt['date_evenement']);
            $now = new DateTime();
            $days_diff = $now->diff($created_date)->days;
            $is_nouveaute = $days_diff <= 30;
            
            $data_filter = 'tous ' . $cat_slug . ' prix'; // Le filtre "prix" s'applique à tous les événements
            if ($is_nouveaute) {
                $data_filter .= ' nouveaute';
            }
            
            $is_free     = (bool) $evt['est_gratuit'];
            $badge_class = $is_free ? 'carousel__item-badge--free' : 'carousel__item-badge--price';
            $badge_label = $is_free
              ? 'Gratuit'
              : ( ! empty( $evt['prix'] ) ? number_format( $evt['prix'], 2, ',', ' ' ) . '&nbsp;€' : 'Payant' );
            $bg_img      = ! empty( $evt['image_url'] )
              ? esc_url( $evt['image_url'] )
              : esc_url( $assets . '/Img/atelier-jeunes.jpg' );
            $date_fmt    = date_i18n( 'd.m.Y', strtotime( $evt['date_evenement'] ) );
            $heure       = substr( $evt['heure_debut'], 0, 5 ) . ' - ' . substr( $evt['heure_fin'], 0, 5 );
          ?>
          <div class="swiper-slide" data-filter="<?php echo esc_attr( $data_filter ); ?>">
            <div class="carousel__item"
                 style="background-image: url('<?php echo $bg_img; ?>');">
              <span class="carousel__item-badge <?php echo esc_attr( $badge_class ); ?>">
                <?php echo $badge_label; ?>
              </span>
              <div>
                <h3 class="carousel__item-title"><?php echo esc_html( $evt['titre'] ); ?></h3>
                <p class="carousel__item-text">
                  <?php echo esc_html( wp_trim_words( $evt['descriptif'] ?? '', 20, '…' ) ); ?>
                </p>
                <div class="carousel__item-meta">
                  <span class="carousel__item-date"><?php echo esc_html( $date_fmt ); ?></span>
                  <span class="carousel__item-time"><?php echo esc_html( $heure ); ?></span>
                </div>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        <?php else : ?>
          <!-- Fallback statique si la BDD est vide -->
          <div class="swiper-slide" data-filter="tous ateliers prix nouveaute">
            <div class="carousel__item"
                 style="background-image: url('<?php echo esc_url( $assets . '/Img/atelier-jeunes.jpg' ); ?>');">
              <span class="carousel__item-badge carousel__item-badge--free">Gratuit</span>
              <div>
                <h3 class="carousel__item-title">Atelier peinture</h3>
                <p class="carousel__item-text">
                  Cet atelier de peinture, ouvert à tous et gratuit, vous propose
                  d'explorer différentes techniques picturales.
                </p>
                <div class="carousel__item-meta">
                  <span class="carousel__item-date">10.09.2025</span>
                  <span class="carousel__item-time">14:30 – 15:30</span>
                </div>
              </div>
            </div>
          </div>
        <?php endif; ?>
        </div><!-- /.swiper-wrapper -->
      </div><!-- /.carousel__list -->
    </section><!-- /.carousel -->

    <div class="carousel__button">
      <a class="main__button--evenement-black"
         href="<?php echo esc_url( home_url('/evenements/') ); ?>">Voir tous les évènements</a>
    </div>
  </section>

  <!-- ── EXPLORER NOS COLLECTIONS ────────────────── -->
  <section class="main__banniere">
    <section class="main__banniere--evenement">
      <img class="main__banniere-img"
           src="<?php echo esc_url( $assets . '/Img/svg/croix-blanche.svg' ); ?>"
           alt="Croix blanche" />
      <h2 class="main__banniere-titre">EXPLORER<br />NOS<br />COLLECTIONS</h2>
    </section>
    <section class="main__banniere-contenu">
      <p class="main__banniere-description">
        Plongez au cœur des collections des Beaux-Arts&nbsp;: peintures, sculptures,
        dessins et œuvres graphiques racontent plusieurs siècles de création.
      </p>
    </section>
  </section>

  <div class="main__explore-grid">
    <a href="<?php echo esc_url( home_url('/collections/') ); ?>"
       class="main__explore-card main__explore-card--test">
      <img class="main__explore-card-img"
           src="<?php echo esc_url( $assets . '/Img/tableaux/tableau-hallali-cerf-chasse.jpg' ); ?>"
           alt="Beaux arts">
      <div class="main__explore-card-wrappper">
        <h3 class="main__explore-card-title">Beaux arts</h3>
        <p class="main__explore-card-text">
          Découvrez les chefs-d'œuvre qui traversent les époques.
        </p>
      </div>
    </a>
    <a href="<?php echo esc_url( home_url('/collections/') ); ?>"
       class="main__explore-card main__explore-card--child" style="grid-column: span 5;">
      <img class="main__explore-card-img"
           src="<?php echo esc_url( $assets . '/Img/tableaux/tableau-guenat-vieillard.jpg' ); ?>"
           alt="Portraits">
      <div class="main__explore-card-wrappper">
        <h3 class="main__explore-card-title">Portraits</h3>
        <p class="main__explore-card-text">Visages, regards, expressions…</p>
      </div>
    </a>
    <a href="<?php echo esc_url( home_url('/collections/') ); ?>"
       class="main__explore-card main__explore-card--fat">
      <img class="main__explore-card-img"
           src="<?php echo esc_url( $assets . '/Img/tableaux/tableau-guenat-cuisinier.jpg' ); ?>"
           alt="Tableaux">
      <h3 class="main__explore-card-title">Tableaux</h3>
      <p class="main__explore-card-text">Scènes du quotidien, portraits et paysages.</p>
    </a>
    <a href="<?php echo esc_url( home_url('/collections/') ); ?>"
       class="main__explore-card main__explore-card--wide">
      <img class="main__explore-card-img"
           src="<?php echo esc_url( $assets . '/Img/musee-mosaique.jpg' ); ?>"
           alt="Archéologie">
      <div class="main__explore-card-wrappper">
        <h3 class="main__explore-card-title">Archéologie</h3>
        <p class="main__explore-card-text">Vestiges uniques, témoins des civilisations.</p>
      </div>
    </a>
    <a href="<?php echo esc_url( home_url('/collections/') ); ?>"
       class="main__explore-card main__explore-card--tall">
      <img class="main__explore-card-img"
           src="<?php echo esc_url( $assets . '/Img/statues-groupe.jpg' ); ?>"
           alt="Sculptures">
      <div class="main__explore-card-wrappper">
        <h3 class="main__explore-card-title">Sculptures</h3>
        <p class="main__explore-card-text">Formes, matières et émotions à travers les siècles.</p>
      </div>
    </a>
  </div>
  <div class="main__explore-button">
    <a class="main__button--explore-black"
       href="<?php echo esc_url( home_url('/collections/') ); ?>">En savoir +</a>
  </div>

  <!-- ── INFOS UTILES + CARTE ─────────────────────── -->
  <section class="main__infos-utiles" id="infos-pratiques">
    <section class="main__banniere">
      <section class="main__banniere--infos">
        <img class="main_map-img"
             src="<?php echo esc_url( $assets . '/Img/svg/croix-blanche.svg' ); ?>"
             alt="Croix blanche" />
        <h2 class="main__banniere-titre">INFOS<br />UTILES</h2>
      </section>
      <section class="main__banniere-contenu">
        <p class="main__banniere-description">
          Retrouvez toutes les informations pratiques pour préparer votre visite.
        </p>
      </section>
    </section>

    <div class="main__infos-utiles-separator">
      <section class="main__map-section">
        <div class="main__infos-utiles-card-header">
          <h2 class="main__infos-utiles-card-title-or">NOUS<br />TROUVER</h2>
        </div>
        <div class="main__map-container">
          <div id="map" class="main__map"></div>
        </div>
      </section>

      <section class="main__infos-utiles-cards">
        <section class="main__infos-utiles-card">
          <h3 class="main__infos-utiles-card-title-or">+ Accès</h3>
          <section class="main__infos-utiles-card-or">
            <h4 class="main__infos-utiles-card-subtitle-white">Adresse</h4>
            <p class="main__infos-utiles-card-text-white">1 place de la Révolution, 25000 Besançon</p>
            <h4 class="main__infos-utiles-card-subtitle-white">Standard</h4>
            <p class="main__infos-utiles-card-text-white">+33 3 81 87 80 67</p>
            <h4 class="main__infos-utiles-card-subtitle-white">Email</h4>
            <p class="main__infos-utiles-card-text-white">
              <a href="mailto:contact@mbaa-besancon.fr">contact@mbaa-besancon.fr</a>
            </p>
          </section>
        </section>

        <section class="main__infos-utiles-card">
          <h4 class="main__infos-utiles-card-subtitle">Transports en commun</h4>
          <ul class="main__infos-utiles-card-list">
            <li class="main__infos-utiles-card-list-item">
              <img class="main__infos-utiles-card-arrow"
                   src="<?php echo esc_url( $assets . '/Img/svg/icon-arrow-droite.svg' ); ?>" alt />
              <p class="main__infos-utiles-card-text">Tram ligne T1 et T2&nbsp;: arrêt «&nbsp;Révolution&nbsp;»</p>
            </li>
            <li class="main__infos-utiles-card-list-item">
              <img class="main__infos-utiles-card-arrow"
                   src="<?php echo esc_url( $assets . '/Img/svg/icon-arrow-droite.svg' ); ?>" alt />
              <p class="main__infos-utiles-card-text">Bus lignes 3, 4, 5, 10, 11, 20, 21, 22, 26, 27&nbsp;: arrêt République</p>
            </li>
            <li class="main__infos-utiles-card-list-item">
              <img class="main__infos-utiles-card-arrow"
                   src="<?php echo esc_url( $assets . '/Img/svg/icon-arrow-droite.svg' ); ?>" alt />
              <p class="main__infos-utiles-card-text">Bus 3, 4, 5, 10, 11, 20, 22&nbsp;: arrêt Courbet</p>
            </li>
          </ul>
          <h4 class="main__infos-utiles-card-subtitle">Stationnement</h4>
          <div class="main__infos-utiles-card-row">
            <img class="main__infos-utiles-card-arrow"
                 src="<?php echo esc_url( $assets . '/Img/svg/icon-arrow-droite.svg' ); ?>" alt />
            <p class="main__infos-utiles-card-text">Parking payant Marché-Beaux-Arts (souterrain et surface)</p>
          </div>
          <a class="main__button--infos-or"
             href="<?php echo esc_url( home_url('/infos-pratiques/') ); ?>">En savoir +</a>
        </section>
      </section>
    </div>
  </section>

</main><!-- /.main -->

<!-- ═══════════════════════════════════════════════════
     FOOTER
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

<!-- ═══════════════════════════════════════════════════
     OVERLAYS & SCRIPTS
═══════════════════════════════════════════════════ -->

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

<!-- Script carousel unifié avec filtres -->
<script src="<?php echo esc_url( get_template_directory_uri() . '/js/carousel-swiper-filters.js' ); ?>"></script>

<!-- Leaflet carte -->
<script>
  document.addEventListener('DOMContentLoaded', function () {
    if (typeof L === 'undefined') return;

    const map = L.map('map').setView([47.240335, 6.022807], 16);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
      attribution: '&copy; OpenStreetMap contributors &copy; CARTO',
      maxZoom: 19
    }).addTo(map);

    const customIcon = L.icon({
      iconUrl: '<?php echo esc_js( $assets . "/Img/svg/musee_or_vector.svg" ); ?>',
      iconSize: [200, 100],
      iconAnchor: [20, 40],
      popupAnchor: [0, -40]
    });

    L.marker([47.240335, 6.022807], { icon: customIcon })
      .addTo(map)
      .bindPopup('<strong>Musée des Beaux-Arts</strong><br>1 place de la Révolution<br>25000 Besançon')
      .openPopup();
  });
</script>

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

<?php get_footer(); ?>