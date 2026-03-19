

<?php
/**
 * Template Name: Page Événements
 * Description: Page agenda & événements du Musée des Beaux-Arts de Besançon
 */

// ── Lucide + Locomotive Scroll dans le <head> WP ─────────────────────────────
add_action('wp_head', function () {
    echo '<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>' . "\n";
    echo '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/locomotive-scroll@4.1.3/dist/locomotive-scroll.css" />' . "\n";
    echo '<script src="https://cdn.jsdelivr.net/npm/locomotive-scroll@4.1.3/dist/locomotive-scroll.umd.js"></script>' . "\n";
}, 20);

// ── Couleurs par catégorie (même palette que le plugin admin) ─────────────────
function mbaa_get_category_color(string $category): string {
    $map = [
        'exposition'    => '#e74c3c',
        'visite'        => '#3498db',
        'atelier'       => '#2ecc71',
        'conference'    => '#f39c12',
        'conférence'    => '#f39c12',
        'spectacle'     => '#9b59b6',
        'soiree'        => '#e67e22',
        'soirée'        => '#e67e22',
        'concert'       => '#e67e22',
        'atelier peinture'   => '#2ecc71',
        'atelier sculpture'  => '#27ae60',
        'ateliers adultes'   => '#2ecc71',
        'ateliers enfants'   => '#1abc9c',
        'soirées musées'     => '#e67e22',
        'tout public'        => '#3498db',
    ];
    $key = strtolower(trim($category));
    return $map[$key] ?? '#0073aa';
}

// ── Récupération depuis la table SQL du plugin MBAA ───────────────────────────
global $wpdb;

$today = date('Y-m-d');

// On cherche d'abord le nom exact de la table (le plugin peut préfixer différemment)
$table_evenements  = $wpdb->prefix . 'mbaa_evenements';
$table_types       = $wpdb->prefix . 'mbaa_types_evenements';

// Vérification que la table existe, sinon fallback sur WP_Query
$table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_evenements'") === $table_evenements;

$events = [];

if ($table_exists) {
    // ── Requête SQL sur la table custom du plugin ─────────────────────────────
    $raw = $wpdb->get_results("
        SELECT
            e.id_evenement,
            e.titre,
            e.date_evenement,
            e.heure_debut,
            e.heure_fin,
            e.descriptif,
            e.prix,
            e.est_gratuit,
            e.image_url,
            e.lieu_musee,
            e.capacite_max,
            e.intervenant,
            e.public_enfant,
            e.public_ado,
            e.public_adulte,
            e.public_tout_public,
            t.nom_type,
            t.type_categorie
        FROM `$table_evenements` e
        LEFT JOIN `$table_types` t ON e.id_type = t.id_type
        ORDER BY e.date_evenement ASC
    ");

    foreach ($raw as $ev) {
        // Public cible
        $public_parts = [];
        if ($ev->public_enfant)      $public_parts[] = 'Enfants';
        if ($ev->public_ado)         $public_parts[] = 'Ados';
        if ($ev->public_adulte)      $public_parts[] = 'Adultes';
        if ($ev->public_tout_public) $public_parts[] = 'Tout public';

        $category  = $ev->nom_type ?: ($ev->type_categorie ?: '');
        $color     = mbaa_get_category_color($ev->type_categorie ?: $category);
        $price_str = $ev->est_gratuit ? 'Gratuit' : ($ev->prix ? number_format((float)$ev->prix, 2, '.', '') . '€' : '');
        $desc      = wp_strip_all_tags($ev->descriptif ?? '');
        $desc_short = mb_strlen($desc) > 120 ? mb_substr($desc, 0, 120) . '…' : $desc;

        $events[] = [
            'id'           => (int)$ev->id_evenement,
            'title'        => $ev->titre,
            'date'         => $ev->date_evenement,
            'time'         => $ev->heure_debut  ?: '',
            'endTime'      => $ev->heure_fin    ?: '',
            'category'     => $category,
            'color'        => $color,
            'price'        => $price_str,
            'description'  => $desc_short,
            'image'        => $ev->image_url   ?: 'event-default.jpg',
            'location'     => $ev->lieu_musee  ?: '',
            'intervenant'  => $ev->intervenant ?: '',
            'public'       => implode(', ', $public_parts),
            'capacity'     => $ev->capacite_max ?: '',
        ];
    }
} else {
    // ── Fallback : WP_Query sur le CPT 'evenement' ────────────────────────────
    $q = new WP_Query([
        'post_type'      => 'evenement',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'meta_key'       => 'date_evenement',
        'orderby'        => 'meta_value',
        'order'          => 'ASC',
    ]);
    if ($q->have_posts()) {
        while ($q->have_posts()) {
            $q->the_post();
            $id       = get_the_ID();
            $category = get_post_meta($id, 'categorie', true) ?: '';
            $events[] = [
                'id'          => $id,
                'title'       => get_the_title(),
                'date'        => get_post_meta($id, 'date_evenement',     true) ?: '',
                'time'        => get_post_meta($id, 'heure_debut',        true) ?: '',
                'endTime'     => get_post_meta($id, 'heure_fin',          true) ?: '',
                'category'    => $category,
                'color'       => mbaa_get_category_color($category),
                'price'       => get_post_meta($id, 'prix',               true) ?: '',
                'description' => get_post_meta($id, 'description_courte', true) ?: '',
                'image'       => get_post_meta($id, 'image_evenement',    true) ?: 'event-default.jpg',
                'location'    => '',
                'intervenant' => '',
                'public'      => '',
                'capacity'    => '',
            ];
        }
        wp_reset_postdata();
    }
}

// ── Catégories uniques pour les filtres ──────────────────────────────────────
$categories = array_values(array_unique(array_filter(array_column($events, 'category'))));

// ── Compteur d'événements à venir ────────────────────────────────────────────
$upcoming_count = count(array_filter($events, fn($e) => $e['date'] >= $today));

// ── URLs raccourcies ──────────────────────────────────────────────────────────
$url_home        = esc_url(home_url('/'));
$url_infos       = esc_url(get_permalink(get_page_by_path('infos-pratiques')));
$url_collections = esc_url(get_permalink(get_page_by_path('collections')));
$url_evenements  = esc_url(get_permalink(get_page_by_path('evenements')));
$url_musee       = esc_url(get_permalink(get_page_by_path('musee-histoire')));
$url_connexion   = esc_url(get_permalink(get_page_by_path('connexion')));
$url_reservation = esc_url(get_permalink(get_page_by_path('reservation')));
$theme_uri       = esc_url(get_template_directory_uri());

get_header();
?>

<!-- ═══════════════════════════════════════════════════
     HEADER / HERO
═══════════════════════════════════════════════════ -->
<header class="header header--evenement">

  <div class="header__container">
    <a class="header__logo-link" href="<?php echo $url_home; ?>">
      <img class="header__logo-img"
           src="<?php echo $theme_uri; ?>/asset/Img/logo/logo-mat-small.png"
           alt="Logo MBAA" />
    </a>

    <button class="header__menu-toggle" aria-label="menu" aria-expanded="false" aria-controls="headerNav">
      <span class="header__menu-bar"></span>
      <span class="header__menu-bar"></span>
      <span class="header__menu-bar"></span>
    </button>

    <nav id="headerNav" class="header__nav" aria-hidden="true">
      <ul class="header__nav-list header__nav-list--main">
        <li class="header__nav-item"><a class="header__nav-link" href="<?php echo $url_infos; ?>">Infos pratiques</a></li>
        <li class="header__nav-item"><a class="header__nav-link" href="<?php echo $url_collections; ?>">Collections</a></li>
        <li class="header__nav-item"><a class="header__nav-link" href="<?php echo $url_evenements; ?>">Événements</a></li>
        <li class="header__nav-item"><a class="header__nav-link" href="<?php echo $url_musee; ?>">Le musée</a></li>
        <li class="header__nav-item"><a class="header__nav-link" href="<?php echo $url_infos; ?>">Contact</a></li>
      </ul>
      <ul class="header__nav-list header__nav-list--secondary">
        <li class="header__nav-item">
          <a class="header__nav-link" href="<?php echo $url_connexion; ?>" aria-label="Connexion">
            <svg class="header__nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle>
            </svg>
          </a>
        </li>
        <li class="header__nav-item">
          <a class="header__nav-link" href="<?php echo $url_reservation; ?>" aria-label="Billetterie">
            <svg class="header__nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"></path>
              <path d="M13 5v2"></path><path d="M13 17v2"></path><path d="M13 11v2"></path>
            </svg>
          </a>
        </li>
        <li class="header__nav-item">
          <a class="header__nav-link" href="#" aria-label="Recherche">
            <svg class="header__nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.35-4.35"></path>
            </svg>
          </a>
        </li>
        <li class="header__nav-item"><a class="header__nav-link-fr" href="#">FR</a></li>
        <li class="header__nav-item"><a class="header__nav-link-fr" href="#">EN</a></li>
      </ul>
    </nav>
  </div>

  <div class="header__hero header__hero--evenement">
    <div class="hero__left">
      <p class="hero__eyebrow">AGENDA CULTUREL — Musée des Beaux-Arts</p>
      <h1 class="hero__title">Agenda<br>&amp; Événements<br><em>au Musée</em></h1>
    </div>
    <div class="hero__right">
      <p class="hero__description">
        Ateliers créatifs, concerts jazz, expositions temporaires et soirées exclusives —
        vivez le musée autrement toute l'année.
      </p>
      <a href="#event-calendar" class="hero__cta">
        Voir l'agenda
        <img class="hero__cta-icon" src="<?php echo $theme_uri; ?>/asset/Img/svg/icon-arrow-droite.svg" alt="Flèche vers la droite">
      </a>
    </div>
    <div class="hero__strip">
      <div class="hero__stat">
        <span class="hero__stat-number" id="heroUpcomingCount"><?php echo esc_html($upcoming_count); ?></span>
        <span class="hero__stat-label">Événements à venir</span>
      </div>
      <div class="hero__strip-divider"></div>
      <div class="hero__stat">
        <span class="hero__stat-number"><?php echo count($categories) ?: 4; ?></span>
        <span class="hero__stat-label">Catégories d'activités</span>
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

<main>

  <!-- ── ÉVÉNEMENT VEDETTE ── -->
  <section class="event-featured" id="event-featured">
    <div class="event-featured__container">

      <div class="event-featured__media">
        <img src="<?php echo $theme_uri; ?>/asset/Img/activite-performance-break.jpg"
             alt="Nuit Européenne des Musées 2026" loading="eager" class="event-featured__img" />
        <div class="event-featured__media-badge"><span>À ne pas manquer</span></div>
      </div>

      <div class="event-featured__content">
        <span class="event-featured__eyebrow">Événement du moment</span>
        <h2 class="event-featured__title">Nuit Européenne<br>des Musées</h2>
        <div class="event-featured__meta">
          <div class="event-featured__meta-item">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            <span>15 Mai 2026</span>
          </div>
          <div class="event-featured__meta-item">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <span>20h00 — 00h00</span>
          </div>
          <div class="event-featured__meta-item">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            <span>Musée MAT — Besançon</span>
          </div>
          <div class="event-featured__meta-item">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M8 8h8M8 12h8M8 16h6M17 8c0-1.1-.9-2-2-2h-3c-1.1 0-2 .9-2 2v4c0 1.1.9 2 2 2h3c1.1 0 2-.9 2-2"/></svg>
            <span class="event-featured__price-tag">Gratuit</span>
          </div>
        </div>
        <p class="event-featured__desc">
          Une nuit exceptionnelle pour découvrir le musée sous un angle inédit. Performances live,
          visites nocturnes, installations lumineuses et concerts — le musée ouvre ses portes
          jusqu'à minuit pour une expérience culturelle unique.
        </p>
        <div class="event-featured__spots">
          <div class="event-featured__spots-bar">
            <div class="event-featured__spots-fill" style="width: 68%"></div>
          </div>
          <span class="event-featured__spots-label">68% des places réservées — <strong>dépêchez-vous !</strong></span>
        </div>
        <div class="event-featured__actions">
          <a href="<?php echo $url_reservation; ?>?event=37" class="event-featured__btn-primary">
            Réserver ma place gratuite
            <img src="<?php echo $theme_uri; ?>/asset/Img/svg/icon-arrow-droite.svg" alt="" class="event-featured__btn-icon">
          </a>
          <button class="event-featured__btn-secondary"
                  onclick="selectEventById(37); document.getElementById('event-calendar').scrollIntoView({behavior:'smooth'})">
            Voir dans le calendrier
          </button>
        </div>
      </div>

    </div>
  </section>

  <!-- ── SEO indexable ── -->
  <section class="events-seo-index" aria-hidden="true">
    <h2>Agenda des événements</h2>
    <ul>
      <?php foreach ($events as $e) :
        $date_fmt = !empty($e['date']) ? date_i18n('j F Y', strtotime($e['date'])) : '';
      ?>
      <li><article>
        <h3><?php echo esc_html($e['title']); ?></h3>
        <?php if ($e['date']) : ?>
          <time datetime="<?php echo esc_attr($e['date']); ?>"><?php echo esc_html($date_fmt); ?></time>
        <?php endif; ?>
        <p><?php echo esc_html(ucfirst($e['category'])); ?> — Musée MAT Besançon</p>
        <a href="<?php echo $url_reservation; ?>?event_id=<?php echo esc_attr($e['id']); ?>">Réserver</a>
      </article></li>
      <?php endforeach; ?>
    </ul>
  </section>

  <!-- ── LÉGENDE DES COULEURS + CALENDRIER ── -->
  <section class="event-calendar-section" id="event-calendar">
    <div class="event-calendar-container">

      <!-- Légende couleurs -->
      <?php if (!empty($categories)) : ?>
      <div class="calendar-legend" id="calendarLegend">
        <?php foreach ($categories as $cat) : ?>
          <div class="calendar-legend__item">
            <span class="calendar-legend__dot" style="background-color: <?php echo esc_attr(mbaa_get_category_color($cat)); ?>"></span>
            <span class="calendar-legend__label"><?php echo esc_html(ucfirst($cat)); ?></span>
          </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <div class="event-calendar-grid">

        <aside class="calendar-sidebar">
          <div class="filter-group">
            <div class="filter-header">
              <i data-lucide="filter"></i>
              <h3>Filtres</h3>
            </div>
            <div class="filter-options" id="filterOptions"></div>
          </div>
          <div class="events-preview">
            <h3 class="events-preview-title">Tous les événements</h3>
            <div class="events-list-container" id="eventsList"></div>
          </div>
        </aside>

        <div class="calendar-main-content">
          <div class="calendar-card">
            <div class="calendar-nav-header">
              <button onclick="previousMonth()" class="nav-arrow-btn"><i data-lucide="chevron-left"></i></button>
              <h2 class="calendar-current-title" id="calendarTitle"></h2>
              <button onclick="nextMonth()" class="nav-arrow-btn"><i data-lucide="chevron-right"></i></button>
            </div>
            <div class="calendar-weekdays-grid">
              <div>Dim</div><div>Lun</div><div>Mar</div><div>Mer</div><div>Jeu</div><div>Ven</div><div>Sam</div>
            </div>
            <div class="calendar-days-grid" id="calendarGrid"></div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- ── GALERIE ÉVÉNEMENTS PASSÉS ── -->
  <section class="event-gallery" id="event-gallery">
    <div class="event-gallery__container">
      <div class="event-gallery__header">
        <div>
          <span class="event-gallery__eyebrow">Nos souvenirs</span>
          <h2 class="event-gallery__title">Les événements passés</h2>
        </div>
        <p class="event-gallery__subtitle">Revivez l'ambiance de nos dernières soirées, ateliers et concerts.</p>
      </div>
      <div class="event-gallery__grid">
        <div class="event-gallery__item event-gallery__item--large">
          <img src="<?php echo $theme_uri; ?>/asset/Img/activite-performance-break.jpg" alt="Soirée Jazz au musée" loading="lazy">
          <div class="event-gallery__overlay">
            <span class="event-gallery__overlay-tag">Soirée Jazz</span>
            <span class="event-gallery__overlay-date">Décembre 2025</span>
          </div>
        </div>
        <div class="event-gallery__item">
          <img src="<?php echo $theme_uri; ?>/asset/Img/atelier-jeunes.jpg" alt="Atelier Peinture" loading="lazy">
          <div class="event-gallery__overlay">
            <span class="event-gallery__overlay-tag">Atelier Peinture</span>
            <span class="event-gallery__overlay-date">Nov. 2025</span>
          </div>
        </div>
        <div class="event-gallery__item">
          <img src="<?php echo $theme_uri; ?>/asset/Img/atelier-scolaire.jpg" alt="Atelier Poterie" loading="lazy">
          <div class="event-gallery__overlay">
            <span class="event-gallery__overlay-tag">Atelier Poterie</span>
            <span class="event-gallery__overlay-date">Oct. 2025</span>
          </div>
        </div>
        <div class="event-gallery__item">
          <img src="<?php echo $theme_uri; ?>/asset/Img/misc-discussion.jpg" alt="Conférence" loading="lazy">
          <div class="event-gallery__overlay">
            <span class="event-gallery__overlay-tag">Conférence</span>
            <span class="event-gallery__overlay-date">Oct. 2025</span>
          </div>
        </div>
        <div class="event-gallery__item">
          <img src="<?php echo $theme_uri; ?>/asset/Img/visite-groupe.jpg" alt="Visite guidée" loading="lazy">
          <div class="event-gallery__overlay">
            <span class="event-gallery__overlay-tag">Visite guidée</span>
            <span class="event-gallery__overlay-date">Sept. 2025</span>
          </div>
        </div>
      </div>
      <div class="event-gallery__cta">
        <p>Vous aussi, vivez ces moments uniques.</p>
        <a href="<?php echo $url_reservation; ?>" class="event-gallery__cta-btn">
          Réserver un événement
          <img src="<?php echo $theme_uri; ?>/asset/Img/svg/icon-arrow-droite.svg" alt="" style="width:14px; filter:invert(1)">
        </a>
      </div>
    </div>
  </section>

  <!-- ── CATÉGORIES D'ACTIVITÉS ── -->
  <section class="event-categories" id="event-categories">
    <div class="event-categories__container">
      <h2 class="event-categories__title">Nos activités</h2>
      <div class="event-categories__grid">
        <div class="category-card" onclick="setActiveFilter('Ateliers Adultes')">
          <div class="category-card__image"><img src="<?php echo $theme_uri; ?>/asset/Img/atelier-jeunes.jpg" alt="Ateliers adultes au musée" loading="lazy"></div>
          <div class="category-card__content">
            <span class="category-card__tag">Tout au long de l'année</span>
            <h3 class="category-card__title">Ateliers Adultes</h3>
            <p class="category-card__desc">Peinture, sculpture, aquarelle — explorez votre créativité avec nos artistes.</p>
            <span class="category-card__cta">Voir les dates →</span>
          </div>
        </div>
        <div class="category-card" onclick="setActiveFilter('Ateliers Enfants')">
          <div class="category-card__image"><img src="<?php echo $theme_uri; ?>/asset/Img/atelier-scolaire.jpg" alt="Ateliers enfants et familles" loading="lazy"></div>
          <div class="category-card__content">
            <span class="category-card__tag">Familles &amp; Enfants</span>
            <h3 class="category-card__title">Ateliers Enfants</h3>
            <p class="category-card__desc">Poterie, dessin, modelage — des ateliers ludiques pour les jeunes artistes.</p>
            <span class="category-card__cta">Voir les dates →</span>
          </div>
        </div>
        <div class="category-card" onclick="setActiveFilter('Soirées Musées')">
          <div class="category-card__image"><img src="<?php echo $theme_uri; ?>/asset/Img/activite-performance-break.jpg" alt="Soirées jazz et concerts au musée" loading="lazy"></div>
          <div class="category-card__content">
            <span class="category-card__tag">Soirées exclusives</span>
            <h3 class="category-card__title">Soirées &amp; Concerts</h3>
            <p class="category-card__desc">Jazz, concerts de chambre, soirées électro — le musée vit la nuit.</p>
            <span class="category-card__cta">Voir les dates →</span>
          </div>
        </div>
        <div class="category-card" onclick="setActiveFilter('Tout public')">
          <div class="category-card__image"><img src="<?php echo $theme_uri; ?>/asset/Img/misc-discussion.jpg" alt="Conférences et visites guidées" loading="lazy"></div>
          <div class="category-card__content">
            <span class="category-card__tag">Tout public</span>
            <h3 class="category-card__title">Conférences &amp; Visites</h3>
            <p class="category-card__desc">Conférences d'experts, visites thématiques et nocturnes pour tous.</p>
            <span class="category-card__cta">Voir les dates →</span>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ── TÉMOIGNAGES ── -->
  <section class="event-testimonials" id="event-testimonials">
    <div class="event-testimonials__container">
      <div class="event-testimonials__header">
        <span class="event-testimonials__eyebrow">Ce qu'ils en disent</span>
        <h2 class="event-testimonials__title">Avis de nos visiteurs</h2>
        <div class="event-testimonials__rating-global">
          <div class="rating-stars">★★★★★</div>
          <span>4.8 / 5 — basé sur 124 avis</span>
        </div>
      </div>
      <div class="event-testimonials__grid">
        <div class="testimonial-card">
          <div class="testimonial-card__stars">★★★★★</div>
          <blockquote class="testimonial-card__quote">"La soirée Jazz au musée était une expérience magique. L'ambiance dans les galeries avec la musique live, c'est quelque chose qu'on n'oublie pas."</blockquote>
          <div class="testimonial-card__author">
            <div class="testimonial-card__avatar">ML</div>
            <div>
              <span class="testimonial-card__name">Marie-Laure D.</span>
              <span class="testimonial-card__event">Soirée Jazz — Décembre 2025</span>
            </div>
          </div>
        </div>
        <div class="testimonial-card testimonial-card--featured">
          <div class="testimonial-card__stars">★★★★★</div>
          <blockquote class="testimonial-card__quote">"Mon fils de 8 ans a adoré l'atelier poterie. Les animateurs sont patients, pédagogues et vraiment passionnés. On reviendra sans hésiter !"</blockquote>
          <div class="testimonial-card__author">
            <div class="testimonial-card__avatar">SC</div>
            <div>
              <span class="testimonial-card__name">Sophie C.</span>
              <span class="testimonial-card__event">Atelier Poterie Enfants — Nov. 2025</span>
            </div>
          </div>
          <div class="testimonial-card__badge">Coup de cœur</div>
        </div>
        <div class="testimonial-card">
          <div class="testimonial-card__stars">★★★★☆</div>
          <blockquote class="testimonial-card__quote">"La conférence sur l'art moderne était passionnante. L'intervenant a su rendre le sujet accessible à tous. Je recommande vivement."</blockquote>
          <div class="testimonial-card__author">
            <div class="testimonial-card__avatar">PT</div>
            <div>
              <span class="testimonial-card__name">Pierre T.</span>
              <span class="testimonial-card__event">Conférence Art Moderne — Jan. 2026</span>
            </div>
          </div>
        </div>
      </div>
      <div class="event-testimonials__cta">
        <p>Rejoignez les <strong>+2 400 visiteurs</strong> qui ont vécu une expérience unique au musée.</p>
        <a href="<?php echo $url_reservation; ?>" class="event-testimonials__cta-btn">
          Je réserve mon expérience
          <img src="<?php echo $theme_uri; ?>/asset/Img/svg/icon-arrow-droite.svg" alt="" style="width:14px; filter:invert(1)">
        </a>
      </div>
    </div>
  </section>

  <!-- ── FAQ ── -->
  <section class="event-faq" id="event-faq">
    <div class="event-faq__container">
      <div class="event-faq__header">
        <span class="event-faq__eyebrow">Questions fréquentes</span>
        <h2 class="event-faq__title">Tout ce que vous devez savoir</h2>
      </div>
      <div class="event-faq__grid">
        <div class="faq-list">
          <details class="faq-item">
            <summary class="faq-item__question">Comment réserver un événement ?<span class="faq-item__icon">+</span></summary>
            <p class="faq-item__answer">Cliquez sur le bouton "Réserver" de l'événement qui vous intéresse, remplissez le formulaire et confirmez. Vous recevrez un numéro de réservation à présenter le jour J.</p>
          </details>
          <details class="faq-item">
            <summary class="faq-item__question">Puis-je annuler ou modifier ma réservation ?<span class="faq-item__icon">+</span></summary>
            <p class="faq-item__answer">Les annulations sont acceptées jusqu'à 48h avant l'événement. Passé ce délai, les places ne peuvent être ni remboursées ni échangées. Contactez-nous à contact@mbaa-besancon.fr.</p>
          </details>
          <details class="faq-item">
            <summary class="faq-item__question">Les événements sont-ils accessibles aux personnes à mobilité réduite ?<span class="faq-item__icon">+</span></summary>
            <p class="faq-item__answer">Oui, le musée est entièrement accessible PMR. Des places prioritaires sont réservées pour les personnes en situation de handicap. Mentionnez-le lors de votre réservation.</p>
          </details>
          <details class="faq-item">
            <summary class="faq-item__question">Y a-t-il des tarifs réduits pour les enfants et les étudiants ?<span class="faq-item__icon">+</span></summary>
            <p class="faq-item__answer">L'entrée au musée est gratuite pour les moins de 26 ans. Pour les événements payants, des tarifs adaptés sont appliqués automatiquement selon votre âge lors de la réservation.</p>
          </details>
          <details class="faq-item">
            <summary class="faq-item__question">Que se passe-t-il si un événement est complet ?<span class="faq-item__icon">+</span></summary>
            <p class="faq-item__answer">Vous pouvez vous inscrire sur liste d'attente via le formulaire de réservation. En cas de désistement, vous serez contacté par email dans les 24h précédant l'événement.</p>
          </details>
          <details class="faq-item">
            <summary class="faq-item__question">Faut-il imprimer son billet ?<span class="faq-item__icon">+</span></summary>
            <p class="faq-item__answer">Non, votre numéro de réservation suffit. Présentez-le à l'accueil sur votre téléphone — nos équipes retrouveront votre réservation en quelques secondes.</p>
          </details>
        </div>
        <div class="faq-contact">
          <div class="faq-contact__inner">
            <div class="faq-contact__icon">💬</div>
            <h3 class="faq-contact__title">Une autre question ?</h3>
            <p class="faq-contact__text">Notre équipe répond en moins de 24h du mardi au dimanche, de 10h à 18h.</p>
            <a href="<?php echo $url_infos; ?>" class="faq-contact__btn">Nous contacter</a>
            <div class="faq-contact__divider">ou</div>
            <a href="<?php echo $url_reservation; ?>" class="faq-contact__reserve">Réserver directement →</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ── FILTRE ÉVÉNEMENTS ── -->
  <section class="events-filter">
    <div class="events-filter__container">
      <div class="events-filter__categories">
        <button class="events-filter__btn events-filter__btn--active" data-filter="all">Tous</button>
        <?php foreach ($categories as $cat) : ?>
          <button class="events-filter__btn" data-filter="<?php echo esc_attr($cat); ?>"
                  style="--cat-color: <?php echo esc_attr(mbaa_get_category_color($cat)); ?>">
            <span class="events-filter__btn-dot" style="background:<?php echo esc_attr(mbaa_get_category_color($cat)); ?>"></span>
            <?php echo esc_html(ucfirst($cat)); ?>
          </button>
        <?php endforeach; ?>
      </div>
      <div class="events-filter__search">
        <input type="text" class="events-filter__search-input" placeholder="Rechercher un événement..." />
        <svg class="events-filter__search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
        </svg>
      </div>
    </div>
  </section>

  <!-- ── GRILLE ÉVÉNEMENTS ── -->
  <section class="events-grid">
    <div class="events-grid__container">
      <div class="events-grid__grid">

        <?php foreach ($events as $e) :
          $date_obj  = !empty($e['date']) ? new DateTime($e['date']) : null;
          $day       = $date_obj ? $date_obj->format('d') : '--';
          $month_str = $date_obj ? strtoupper($date_obj->format('M')) : '--';
          // Image : URL absolue si fournie par le plugin, sinon chemin local
          $img_src = !empty($e['image']) && filter_var($e['image'], FILTER_VALIDATE_URL)
                        ? $e['image']
                        : $theme_uri . '/asset/Img/evenements/' . ($e['image'] ?: 'event-default.jpg');
        ?>
        <div class="event-card" data-category="<?php echo esc_attr($e['category']); ?>"
             style="--event-color: <?php echo esc_attr($e['color']); ?>">
          <div class="event-card__image">
            <img src="<?php echo esc_url($img_src); ?>" alt="<?php echo esc_attr($e['title']); ?>" loading="lazy" />
            <div class="event-card__date">
              <span class="event-card__day"><?php echo esc_html($day); ?></span>
              <span class="event-card__month"><?php echo esc_html($month_str); ?></span>
            </div>
            <?php if (!empty($e['category'])) : ?>
              <div class="event-card__category"
                   style="background-color: <?php echo esc_attr($e['color']); ?>">
                <?php echo esc_html(ucfirst($e['category'])); ?>
              </div>
            <?php endif; ?>
          </div>
          <div class="event-card__content">
            <h3 class="event-card__title"><?php echo esc_html($e['title']); ?></h3>
            <div class="event-card__meta">
              <?php if (!empty($e['time'])) : ?>
                <span class="event-card__time">
                  <?php echo esc_html($e['time']); ?>
                  <?php if (!empty($e['endTime'])) echo ' — ' . esc_html($e['endTime']); ?>
                </span>
              <?php endif; ?>
              <?php if (!empty($e['price'])) : ?>
                <span class="event-card__price"><?php echo esc_html($e['price']); ?></span>
              <?php endif; ?>
            </div>
            <?php if (!empty($e['description'])) : ?>
              <p class="event-card__desc"><?php echo esc_html($e['description']); ?></p>
            <?php endif; ?>
            <div class="event-card__actions">
              <a href="<?php echo $url_reservation; ?>?event_id=<?php echo esc_attr($e['id']); ?>"
                 class="event-card__btn event-card__btn--primary">Réserver</a>
              <button class="event-card__btn event-card__btn--secondary"
                      data-modal="event-<?php echo esc_attr($e['id']); ?>">En savoir plus</button>
            </div>
          </div>
        </div>
        <?php endforeach; ?>

      </div>
    </div>
  </section>

  <!-- ── NEWSLETTER ── -->
  <section class="events-newsletter">
    <div class="events-newsletter__container">
      <div class="events-newsletter__content">
        <h2 class="events-newsletter__title">Ne manquez plus aucun événement</h2>
        <p class="events-newsletter__text">
          Inscrivez-vous à notre newsletter pour être informé de tous nos événements culturels,
          ateliers pédagogiques et manifestations spéciales.
        </p>
        <form class="events-newsletter__form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
          <?php wp_nonce_field('mbaa_newsletter', 'mbaa_newsletter_nonce'); ?>
          <input type="hidden" name="action" value="mbaa_newsletter_subscribe" />
          <input type="email" name="email" placeholder="Votre adresse e-mail" required />
          <button type="submit">S'inscrire</button>
        </form>
      </div>
    </div>
  </section>

</main>

<!-- ═══════════════════════════════════════════════════
     FOOTER
═══════════════════════════════════════════════════ -->
<footer class="footer">
  <div class="footer__contain-form">
    <div class="footer__logo-container">
      <a class="footer__logo-link" href="<?php echo $url_home; ?>">
        <img class="footer__logo-img" src="<?php echo $theme_uri; ?>/asset/Img/logo/logo-mat-small.png" alt="Logo MBAA" />
        <h2 class="footer__title">Suivez-nous pour recevoir la newsletter</h2>
      </a>
      <form class="footer__form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <?php wp_nonce_field('mbaa_newsletter', 'mbaa_newsletter_nonce'); ?>
        <input type="hidden" name="action" value="mbaa_newsletter_subscribe" />
        <input class="footer__input" type="email" name="email" placeholder="Entrez votre adresse e-mail" required />
        <button class="footer__button" type="submit">S'abonner</button>
      </form>
    </div>
    <section class="footer__nav">
      <ul class="footer__nav-list">
        <li class="footer__nav-item"><h4 class="footer__nav-title">Le musée</h4></li>
        <li class="footer__nav-item"><a class="footer__nav-link" href="<?php echo $url_musee; ?>">Présentation &amp; histoire</a></li>
        <li class="footer__nav-item"><a class="footer__nav-link" href="<?php echo $url_collections; ?>">Collections</a></li>
      </ul>
      <ul class="footer__nav-list">
        <li class="footer__nav-item"><h4 class="footer__nav-title">Programmation</h4></li>
        <li class="footer__nav-item"><a class="footer__nav-link" href="<?php echo $url_evenements; ?>">Événements</a></li>
        <li class="footer__nav-item"><a class="footer__nav-link" href="<?php echo $url_collections; ?>">Collections</a></li>
      </ul>
      <ul class="footer__nav-list">
        <li class="footer__nav-item"><h4 class="footer__nav-title">Contact</h4></li>
        <li class="footer__nav-item"><a class="footer__nav-link" href="#">Horaires et tarifs</a></li>
        <li class="footer__nav-item"><a class="footer__nav-link" href="#">Nous contacter</a></li>
        <li class="footer__nav-item"><a class="footer__nav-link" href="#">Accessibilité</a></li>
      </ul>
    </section>
    <section class="footer__social">
      <h2>Nos réseaux sociaux</h2>
      <section class="footer__social-media">
        <a class="footer__social-link" href="#" aria-label="Facebook"><img class="footer__social-icon" src="<?php echo $theme_uri; ?>/asset/Img/svg/facebook.svg" alt="Facebook" /></a>
        <a class="footer__social-link" href="#" aria-label="Instagram"><img class="footer__social-icon" src="<?php echo $theme_uri; ?>/asset/Img/svg/insta.svg" alt="Instagram" /></a>
        <a class="footer__social-link" href="#" aria-label="LinkedIn"><img class="footer__social-icon" src="<?php echo $theme_uri; ?>/asset/Img/svg/linkdin.svg" alt="LinkedIn" /></a>
        <a class="footer__social-link" href="#" aria-label="TikTok"><img class="footer__social-icon" src="<?php echo $theme_uri; ?>/asset/Img/svg/tiktok.svg" alt="TikTok" /></a>
      </section>
    </section>
    <section class="footer__links">
      <a class="footer__link" href="#">Mentions légales</a>
      <a class="footer__link" href="#">Politique de confidentialité</a>
    </section>
  </div>
</footer>

<!-- ═══════════════════════════════════════════════════
     SCRIPTS
═══════════════════════════════════════════════════ -->
<script>
// ── Données injectées depuis PHP ──────────────────────────────────────────────
const eventsData         = <?php echo wp_json_encode(array_values($events)); ?>;
const reservationBaseUrl = "<?php echo esc_js($url_reservation); ?>";

// ── Variables globales ────────────────────────────────────────────────────────
let currentDate  = new Date();
let activeFilter = 'all';

// ── Utilitaire : formater heure HH:MM:SS → HH:MM ─────────────────────────────
function fmtTime(t) {
  if (!t) return '';
  return t.substring(0, 5);
}

// ── Calendrier ───────────────────────────────────────────────────────────────
function previousMonth() { currentDate.setMonth(currentDate.getMonth() - 1); renderCalendar(); }
function nextMonth()     { currentDate.setMonth(currentDate.getMonth() + 1); renderCalendar(); }

function renderCalendar() {
  const year     = currentDate.getFullYear();
  const month    = currentDate.getMonth();
  const firstDay = new Date(year, month, 1).getDay();
  const lastDate = new Date(year, month + 1, 0).getDate();
  const months   = ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'];

  document.getElementById('calendarTitle').textContent = `${months[month]} ${year}`;
  const grid = document.getElementById('calendarGrid');
  grid.innerHTML = '';

  for (let i = 0; i < firstDay; i++) {
    const e = document.createElement('div');
    e.className = 'calendar-day calendar-day--empty';
    grid.appendChild(e);
  }

  for (let day = 1; day <= lastDate; day++) {
    const el       = document.createElement('div');
    el.className   = 'calendar-day';

    // Vérifier si c'est aujourd'hui
    const now = new Date();
    if (day === now.getDate() && month === now.getMonth() && year === now.getFullYear()) {
      el.classList.add('calendar-day--today');
    }

    const dayEvents = eventsData.filter(e => {
      const d = new Date(e.date + 'T00:00:00'); // forcer local, pas UTC
      return d.getDate() === day && d.getMonth() === month && d.getFullYear() === year
          && (activeFilter === 'all' || e.category === activeFilter);
    });

    if (dayEvents.length > 0) {
      el.classList.add('calendar-day--has-events');

      // Numéro du jour
      const num = document.createElement('span');
      num.className   = 'calendar-day__number';
      num.textContent = day;
      el.appendChild(num);

      // Points colorés (max 3 + "+N")
      const dotsWrap = document.createElement('div');
      dotsWrap.className = 'calendar-day__dots';

      dayEvents.slice(0, 3).forEach(ev => {
        const dot = document.createElement('span');
        dot.className   = 'calendar-day__dot';
        dot.title       = ev.title;
        dot.style.backgroundColor = ev.color || '#0073aa';
        dotsWrap.appendChild(dot);
      });

      if (dayEvents.length > 3) {
        const more = document.createElement('span');
        more.className   = 'calendar-day__dot calendar-day__dot--more';
        more.title       = `+${dayEvents.length - 3} événement(s)`;
        more.textContent = `+${dayEvents.length - 3}`;
        dotsWrap.appendChild(more);
      }

      el.appendChild(dotsWrap);

      // Tooltip au clic : liste rapide
      el.addEventListener('click', () => showCalendarDayPopup(dayEvents, day, month, year));
    } else {
      el.textContent = day;
    }

    grid.appendChild(el);
  }
}

// ── Popup jour (liste rapide des événements du jour) ─────────────────────────
function showCalendarDayPopup(events, day, month, year) {
  // Retirer popup précédent
  const old = document.getElementById('calendarDayPopup');
  if (old) old.remove();

  const months = ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'];

  const popup = document.createElement('div');
  popup.id        = 'calendarDayPopup';
  popup.className = 'calendar-day-popup';
  popup.innerHTML = `
    <div class="calendar-day-popup__header">
      <strong>${day} ${months[month]} ${year}</strong>
      <button onclick="document.getElementById('calendarDayPopup').remove()" class="calendar-day-popup__close">✕</button>
    </div>
    <ul class="calendar-day-popup__list">
      ${events.map(ev => `
        <li class="calendar-day-popup__item">
          <span class="calendar-day-popup__dot" style="background:${ev.color}"></span>
          <div>
            <strong>${ev.title}</strong>
            ${ev.time ? `<span>${fmtTime(ev.time)}${ev.endTime ? ' — ' + fmtTime(ev.endTime) : ''}</span>` : ''}
            ${ev.location ? `<span>${ev.location}</span>` : ''}
            ${ev.price ? `<span class="calendar-day-popup__price">${ev.price}</span>` : ''}
          </div>
          <a href="${reservationBaseUrl}?event_id=${ev.id}" class="calendar-day-popup__btn">Réserver</a>
        </li>
      `).join('')}
    </ul>
  `;

  document.querySelector('.calendar-main-content').appendChild(popup);

  // Fermeture en cliquant en dehors
  setTimeout(() => {
    document.addEventListener('click', function handler(e) {
      if (!popup.contains(e.target)) {
        popup.remove();
        document.removeEventListener('click', handler);
      }
    });
  }, 100);
}

// ── Liste latérale ────────────────────────────────────────────────────────────
function renderEventsList() {
  const list     = document.getElementById('eventsList');
  const filtered = eventsData.filter(e => activeFilter === 'all' || e.category === activeFilter);
  list.innerHTML = '';

  if (filtered.length === 0) {
    list.innerHTML = '<p class="events-list__empty">Aucun événement dans cette catégorie.</p>';
    return;
  }

  filtered.forEach(e => {
    const d    = new Date(e.date + 'T00:00:00');
    const item = document.createElement('div');
    item.className = 'events-list__item';
    item.innerHTML = `
      <div class="events-list__item-date" style="border-left-color: ${e.color}">
        <span class="events-list__item-day">${d.getDate()}</span>
        <span class="events-list__item-month">${d.toLocaleDateString('fr-FR',{month:'short'}).toUpperCase()}</span>
      </div>
      <div class="events-list__item-content">
        <h4 class="events-list__item-title">${e.title}</h4>
        <p class="events-list__item-time">${fmtTime(e.time)}${e.endTime ? ' — '+fmtTime(e.endTime) : ''}</p>
        <span class="events-list__item-category" style="background:${e.color}">${e.category}</span>
      </div>
      <a href="${reservationBaseUrl}?event_id=${e.id}" class="events-list__item-btn">Réserver</a>`;
    list.appendChild(item);
  });
}

// ── Filtres sidebar ───────────────────────────────────────────────────────────
function renderFilterOptions() {
  const container = document.getElementById('filterOptions');
  if (!container) return;

  const cats = ['all', ...new Set(eventsData.map(e => e.category).filter(Boolean))];
  container.innerHTML = '';

  cats.forEach(cat => {
    const color = cat === 'all' ? '#666' : (eventsData.find(e => e.category === cat)?.color || '#0073aa');
    const btn   = document.createElement('button');
    btn.className   = 'filter-option-btn' + (cat === 'all' ? ' active' : '');
    btn.dataset.cat = cat;
    btn.innerHTML   = cat === 'all'
      ? 'Tous les événements'
      : `<span class="filter-option-btn__dot" style="background:${color}"></span>${cat.charAt(0).toUpperCase() + cat.slice(1)}`;
    btn.addEventListener('click', () => setActiveFilter(cat));
    container.appendChild(btn);
  });
}

// ── Synchronisation des filtres ───────────────────────────────────────────────
function setActiveFilter(category) {
  activeFilter = category;

  document.querySelectorAll('.filter-option-btn').forEach(b =>
    b.classList.toggle('active', b.dataset.cat === category));
  document.querySelectorAll('.events-filter__btn').forEach(b =>
    b.classList.toggle('events-filter__btn--active', b.dataset.filter === category));

  document.querySelectorAll('.event-card').forEach(card => {
    card.style.display = (category === 'all' || card.dataset.category === category) ? '' : 'none';
  });

  renderCalendar();
  renderEventsList();
}

function selectEventById(id) { console.log('Event selected:', id); }

// ── Recherche ─────────────────────────────────────────────────────────────────
function initSearch() {
  const input = document.querySelector('.events-filter__search-input');
  if (!input) return;
  input.addEventListener('input', function () {
    const q = this.value.toLowerCase().trim();
    document.querySelectorAll('.event-card').forEach(card => {
      const title = card.querySelector('.event-card__title')?.textContent.toLowerCase() ?? '';
      card.style.display = (!q || title.includes(q)) ? '' : 'none';
    });
  });
}

// ── Init ──────────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
  if (window.lucide) window.lucide.createIcons();

  renderCalendar();
  renderEventsList();
  renderFilterOptions();
  initSearch();

  document.querySelectorAll('.events-filter__btn').forEach(btn => {
    btn.addEventListener('click', function () { setActiveFilter(this.dataset.filter); });
  });

  if (window.LocomotiveScroll) {
    new LocomotiveScroll({
      el: document.querySelector('[data-scroll-container]'),
      smooth: true,
      smartphone: { smooth: false },
      tablet:     { smooth: false },
    });
  }
});
</script>

<?php get_footer(); ?>