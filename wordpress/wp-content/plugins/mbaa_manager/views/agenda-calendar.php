<?php
/**
 * Template pour la page Agenda/Calendrier du plugin MBAA
 * Identique à la page publique événements
 */

if (!defined('ABSPATH')) {
    exit;
}

// Fonctions utilitaires
function get_category_color($category) {
    $colors = array(
        'exposition' => '#e74c3c',
        'visite' => '#3498db', 
        'atelier' => '#2ecc71',
        'conference' => '#f39c12',
        'spectacle' => '#9b59b6',
        'soiree' => '#e67e22'
    );
    return isset($colors[$category]) ? $colors[$category] : '#0073aa';
}

function get_public_targets($event) {
    $targets = array();
    if ($event->public_enfant) $targets[] = 'Enfants';
    if ($event->public_ado) $targets[] = 'Ados';
    if ($event->public_adulte) $targets[] = 'Adultes';
    if ($event->public_tout_public) $targets[] = 'Tout public';
    return implode(', ', $targets);
}

// Préparer les données pour le JavaScript
$events_data = array();
foreach ($evenements as $event) {
    $events_data[] = array(
        'id' => $event->id_evenement,
        'title' => $event->titre,
        'date' => $event->date_evenement,
        'time' => $event->heure_debut,
        'endTime' => $event->heure_fin,
        'category' => $event->nom_type,
        'category_color' => $event->type_categorie ? get_category_color($event->type_categorie) : '#0073aa',
        'description' => wp_strip_all_tags(substr($event->descriptif, 0, 100)) . '...',
        'location' => $event->lieu_musee,
        'price' => $event->est_gratuit ? 'Gratuit' : ($event->prix ? $event->prix . '€' : ''),
        'image' => $event->image_url,
        'intervenant' => $event->intervenant,
        'public' => get_public_targets($event),
        'capacity' => $event->capacite_max
    );
}

// Catégories uniques pour les filtres
$categories = array_values(array_unique(array_filter(array_column($events_data, 'category'))));

// Compteur d'événements à venir
$today = date('Y-m-d');
$upcoming_count = count(array_filter($events_data, function($e) use ($today) {
    return ($e['date'] ?? '') >= $today;
}));

// URLs
$admin_url = admin_url();
$plugin_url = plugins_url('mbaa_manager');
?>

<!-- Inclure les styles du thème -->
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/style.css">

<!-- Inclure Lucide Icons -->
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>

<div class="wrap mbaa-wrap">
    <div class="mbaa-header">
        <div class="mbaa-header-content">
            <h1 class="mbaa-page-title">
                <span class="dashicons-calendar-alt"></span>
                Agenda Culturel
            </h1>
            <p class="mbaa-page-description">
                Vue d'ensemble de tous les événements à venir du musée
            </p>
        </div>
        <div class="mbaa-header-actions">
            <a href="<?php echo admin_url('admin.php?page=mbaa-evenements&action=add'); ?>" class="button button-primary">
                <span class="dashicons-plus"></span>
                Nouvel événement
            </a>
            <a href="<?php echo admin_url('admin.php?page=mbaa-evenements'); ?>" class="button">
                <span class="dashicons-list-view"></span>
                Gérer les événements
            </a>
        </div>
    </div>

    <!-- Hero Section (identique à la page publique) -->
    <header class="header header--evenement">
        <div class="header__container">
            <div class="header__hero header__hero--evenement">
                <div class="hero__left">
                    <p class="hero__eyebrow">AGENDA CULTUREL — Musée des Beaux-Arts</p>
                    <h1 class="hero__title">
                        Agenda<br>
                        &amp; Événements<br>
                        <em>au Musée</em>
                    </h1>
                </div>
                <div class="hero__right">
                    <p class="hero__description">
                        Ateliers créatifs, concerts jazz, expositions temporaires et soirées exclusives — 
                        vivez le musée autrement toute l'année.
                    </p>
                    <a href="#event-calendar" class="hero__cta">
                        Voir l'agenda
                        <img class="hero__cta-icon" src="<?php echo get_template_directory_uri(); ?>/asset/Img/svg/icon-arrow-droite.svg" alt="Flèche vers la droite">
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
                        <span class="hero__stat-number"><?php echo date('Y'); ?></span>
                        <span class="hero__stat-label">Année en cours</span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main>
        <!-- Filtres -->
        <section class="events-filter">
            <div class="events-filter__container">
                <div class="events-filter__categories">
                    <button class="events-filter__btn events-filter__btn--active" data-filter="all">Tous</button>
                    <?php foreach ($categories as $cat) : ?>
                        <button class="events-filter__btn" data-filter="<?php echo esc_attr($cat); ?>"
                                style="--cat-color: <?php echo esc_attr(get_category_color($cat)); ?>">
                            <span class="events-filter__btn-dot" style="background:<?php echo esc_attr(get_category_color($cat)); ?>"></span>
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

        <!-- Calendrier (identique à la page publique) -->
        <section class="event-calendar-section" id="event-calendar">
            <div class="event-calendar-container">
                <!-- Légende couleurs -->
                <?php if (!empty($categories)) : ?>
                <div class="calendar-legend" id="calendarLegend">
                    <?php foreach ($categories as $cat) : ?>
                        <div class="calendar-legend__item">
                            <span class="calendar-legend__dot" style="background-color: <?php echo esc_attr(get_category_color($cat)); ?>"></span>
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
                                <button onclick="previousMonth()" class="nav-arrow-btn">
                                    <i data-lucide="chevron-left"></i>
                                </button>
                                <h2 class="calendar-current-title" id="calendarTitle"></h2>
                                <button onclick="nextMonth()" class="nav-arrow-btn">
                                    <i data-lucide="chevron-right"></i>
                                </button>
                            </div>
                            <div class="calendar-weekdays-grid">
                                <div>Dim</div><div>Lun</div><div>Mar</div><div>Mer</div>
                                <div>Jeu</div><div>Ven</div><div>Sam</div>
                            </div>
                            <div class="calendar-days-grid" id="calendarGrid"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Grille d'événements -->
        <section class="events-grid">
            <div class="events-grid__container">
                <div class="events-grid__grid" id="eventsGrid">
                    <?php foreach ($events_data as $e) :
                        $date_obj  = !empty($e['date']) ? new DateTime($e['date']) : null;
                        $day       = $date_obj ? $date_obj->format('d') : '--';
                        $month_str = $date_obj ? strtoupper($date_obj->format('M')) : '--';
                        $img_src = !empty($e['image']) && filter_var($e['image'], FILTER_VALIDATE_URL)
                                    ? $e['image']
                                    : get_template_directory_uri() . '/asset/Img/evenements/' . ($e['image'] ?: 'event-default.jpg');
                    ?>
                    <div class="event-card" data-category="<?php echo esc_attr($e['category']); ?>"
                         style="--event-color: <?php echo esc_attr($e['category_color']); ?>">
                        <div class="event-card__image">
                            <img src="<?php echo esc_url($img_src); ?>" alt="<?php echo esc_attr($e['title']); ?>" loading="lazy" />
                            <div class="event-card__date">
                                <span class="event-card__day"><?php echo esc_html($day); ?></span>
                                <span class="event-card__month"><?php echo esc_html($month_str); ?></span>
                            </div>
                            <?php if (!empty($e['category'])) : ?>
                                <div class="event-card__category"
                                     style="background-color: <?php echo esc_attr($e['category_color']); ?>">
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
                                <a href="<?php echo admin_url('admin.php?page=mbaa-evenements&action=edit&id=' . $e['id']); ?>"
                                   class="event-card__btn event-card__btn--primary">Modifier</a>
                                <button class="event-card__btn event-card__btn--secondary"
                                        onclick="showEventDetails(<?php echo json_encode($e); ?>)">
                                    Détails
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    </main>

    <!-- Panneau latéral pour les détails -->
    <div id="eventDetailsPanel" class="event-details-panel" style="display: none;">
        <div class="panel-header">
            <h3 id="eventDetailsTitle"></h3>
            <button class="close-panel" id="closeDetails">
                <span class="dashicons-no-alt"></span>
            </button>
        </div>
        <div class="panel-content">
            <div id="eventDetailsContent"></div>
            <div class="panel-actions">
                <a href="#" id="editEventBtn" class="button button-primary">Modifier</a>
                <a href="#" id="viewEventBtn" class="button">Voir la fiche</a>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript identique à la page publique -->
<script>
// Données injectées depuis PHP
const eventsData         = <?php echo json_encode(array_values($events_data)); ?>;
const reservationBaseUrl = "<?php echo esc_js(admin_url('admin.php?page=mbaa-evenements')); ?>";

// Variables globales
let currentDate  = new Date();
let activeFilter = 'all';

// Utilitaire : formater heure
function fmtTime(t) {
  if (!t) return '';
  return t.substring(0, 5);
}

// Navigation du calendrier
function previousMonth() { currentDate.setMonth(currentDate.getMonth() - 1); renderCalendar(); }
function nextMonth()     { currentDate.setMonth(currentDate.getMonth() + 1); renderCalendar(); }

// Rendu du calendrier
function renderCalendar() {
  const year     = currentDate.getFullYear();
  const month    = currentDate.getMonth();
  const firstDay = new Date(year, month, 1).getDay();
  const lastDate = new Date(year, month + 1, 0).getDate();
  
  const months   = ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'];
  
  document.getElementById('calendarTitle').textContent = `${months[month]} ${year}`;
  const grid = document.getElementById('calendarGrid');
  grid.innerHTML = '';

  // Jours vides avant le 1er
  for (let i = 0; i < firstDay; i++) {
    const e = document.createElement('div');
    e.className = 'calendar-day calendar-day--empty';
    grid.appendChild(e);
  }

  // Jours du mois
  for (let day = 1; day <= lastDate; day++) {
    const el       = document.createElement('div');
    el.className   = 'calendar-day';
    
    // Vérifier si c'est aujourd'hui
    const now = new Date();
    if (day === now.getDate() && month === now.getMonth() && year === now.getFullYear()) {
      el.classList.add('calendar-day--today');
    }

    const dayEvents = eventsData.filter(e => {
      const d = new Date(e.date + 'T00:00:00');
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

      // Points colorés
      const dotsWrap = document.createElement('div');
      dotsWrap.className = 'calendar-day__dots';

      dayEvents.slice(0, 3).forEach(ev => {
        const dot = document.createElement('span');
        dot.className   = 'calendar-day__dot';
        dot.title       = ev.title;
        dot.style.backgroundColor = ev.category_color || '#0073aa';
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
      el.addEventListener('click', () => showCalendarDayPopup(dayEvents, day, month, year));
    } else {
      el.textContent = day;
    }
    
    grid.appendChild(el);
  }
}

// Popup jour
function showCalendarDayPopup(events, day, month, year) {
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
          <span class="calendar-day-popup__dot" style="background:${ev.category_color}"></span>
          <div>
            <strong>${ev.title}</strong>
            ${ev.time ? `<span>${fmtTime(ev.time)}${ev.endTime ? ' — ' + fmtTime(ev.endTime) : ''}</span>` : ''}
            ${ev.location ? `<span>${ev.location}</span>` : ''}
            ${ev.price ? `<span class="calendar-day-popup__price">${ev.price}</span>` : ''}
          </div>
          <a href="${reservationBaseUrl}&action=edit&id=${ev.id}" class="calendar-day-popup__btn">Modifier</a>
        </li>
      `).join('')}
    </ul>
  `;

  document.querySelector('.calendar-main-content').appendChild(popup);

  setTimeout(() => {
    document.addEventListener('click', function handler(e) {
      if (!popup.contains(e.target)) {
        popup.remove();
        document.removeEventListener('click', handler);
      }
    });
  }, 100);
}

// Liste latérale
function renderEventsList() {
  const list     = document.getElementById('eventsList');
  const filtered = eventsData.filter(e => activeFilter === 'all' || e.category === activeFilter);
  list.innerHTML = '';

  if (filtered.length === 0) {
    list.innerHTML = '<p class="events-list__empty">Aucun événement dans cette catégorie.</p>';
    return;
  }

  filtered.sort((a, b) => new Date(a.date) - new Date(b.date));

  filtered.forEach(e => {
    const d    = new Date(e.date + 'T00:00:00');
    const item = document.createElement('div');
    item.className = 'events-list__item';
    item.innerHTML = `
      <div class="events-list__item-date" style="border-left-color: ${e.category_color}">
        <span class="events-list__item-day">${d.getDate()}</span>
        <span class="events-list__item-month">${d.toLocaleDateString('fr-FR',{month:'short'}).toUpperCase()}</span>
      </div>
      <div class="events-list__item-content">
        <h4 class="events-list__item-title">${e.title}</h4>
        <p class="events-list__item-time">${fmtTime(e.time)}${e.endTime ? ' — '+fmtTime(e.endTime) : ''}</p>
        <span class="events-list__item-category" style="background:${e.category_color}">${e.category}</span>
      </div>
      <a href="${reservationBaseUrl}&action=edit&id=${e.id}" class="events-list__item-btn">Modifier</a>`;
    list.appendChild(item);
  });
}

// Filtres sidebar
function renderFilterOptions() {
  const container = document.getElementById('filterOptions');
  if (!container) return;
  
  const cats = ['all', ...new Set(eventsData.map(e => e.category).filter(Boolean))];
  container.innerHTML = '';

  cats.forEach(cat => {
    const color = cat === 'all' ? '#666' : (eventsData.find(e => e.category === cat)?.category_color || '#0073aa');
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

// Synchronisation des filtres
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

// Recherche
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

// Détails événement
function showEventDetails(event) {
  document.getElementById('eventDetailsTitle').textContent = event.title;
  
  const content = `
    <div class="event-detail">
      <p><strong>Date:</strong> ${new Date(event.date).toLocaleDateString('fr-FR')}</p>
      <p><strong>Heure:</strong> ${event.time || 'Non défini'} ${event.endTime ? '- ' + event.endTime : ''}</p>
      <p><strong>Type:</strong> <span style="color: ${event.category_color}">${event.category}</span></p>
      <p><strong>Lieu:</strong> ${event.location || 'Non défini'}</p>
      <p><strong>Prix:</strong> ${event.price}</p>
      ${event.public ? `<p><strong>Public:</strong> ${event.public}</p>` : ''}
      ${event.intervenant ? `<p><strong>Intervenant:</strong> ${event.intervenant}</p>` : ''}
      ${event.capacity ? `<p><strong>Capacité:</strong> ${event.capacity} personnes</p>` : ''}
      <p><strong>Description:</strong> ${event.description}</p>
    </div>
  `;
  
  document.getElementById('eventDetailsContent').innerHTML = content;
  
  document.getElementById('editEventBtn').href = `${reservationBaseUrl}&action=edit&id=${event.id}`;
  document.getElementById('viewEventBtn').href = `${reservationBaseUrl}&action=edit&id=${event.id}`;
  
  document.getElementById('eventDetailsPanel').style.display = 'block';
}

// Fermeture du panneau
document.getElementById('closeDetails').addEventListener('click', () => {
  document.getElementById('eventDetailsPanel').style.display = 'none';
});

// Initialisation
document.addEventListener('DOMContentLoaded', function () {
  if (window.lucide) window.lucide.createIcons();
  
  renderCalendar();
  renderEventsList();
  renderFilterOptions();
  initSearch();

  document.querySelectorAll('.events-filter__btn').forEach(btn => {
    btn.addEventListener('click', function () { setActiveFilter(this.dataset.filter); });
  });
});
</script>

<!-- Styles additionnels pour l'admin -->
<style>
.mbaa-wrap {
    max-width: 100%;
    margin: 0;
    padding: 0;
    background: #f9fafb;
}

.mbaa-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0;
    padding: 20px;
    background: white;
    border-bottom: 1px solid #ddd;
}

.mbaa-page-title {
    font-size: 28px;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.mbaa-page-description {
    color: #666;
    margin: 5px 0 0 0;
    font-size: 14px;
}

.mbaa-header-actions {
    display: flex;
    gap: 10px;
}

/* Adapter les styles du thème pour l'admin */
.header {
    background: white !important;
    margin: 0 !important;
}

.header__container {
    max-width: 100% !important;
    padding: 0 !important;
}

.event-details-panel {
    position: fixed;
    right: -400px;
    top: 0;
    width: 400px;
    height: 100vh;
    background: white;
    box-shadow: -2px 0 10px rgba(0,0,0,0.1);
    z-index: 100000;
    transition: right 0.3s ease;
}

.event-details-panel.show {
    right: 0;
}

.panel-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid #ddd;
}

.panel-content {
    padding: 20px;
    max-height: calc(100vh - 200px);
    overflow-y: auto;
}

.panel-actions {
    padding: 20px;
    border-top: 1px solid #ddd;
    display: flex;
    gap: 10px;
}

.calendar-day-popup {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    border-radius: 8px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    z-index: 100001;
    max-width: 350px;
    max-height: 400px;
    overflow-y: auto;
}

.calendar-day-popup__header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px;
    border-bottom: 1px solid #eee;
}

.calendar-day-popup__close {
    background: none;
    border: none;
    font-size: 18px;
    cursor: pointer;
}

.calendar-day-popup__list {
    list-style: none;
    margin: 0;
    padding: 0;
}

.calendar-day-popup__item {
    padding: 12px 15px;
    border-bottom: 1px solid #eee;
    display: flex;
    align-items: center;
    gap: 10px;
}

.calendar-day-popup__dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
}

.calendar-day-popup__btn {
    margin-left: auto;
    padding: 4px 8px;
    background: #0073aa;
    color: white;
    text-decoration: none;
    border-radius: 4px;
    font-size: 12px;
}
</style>
