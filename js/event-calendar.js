// ============================================================
// event-calendar.js — Version unifiée (desktop + mobile)
// ============================================================

// ========================================
// DONNÉES
// ========================================

const events = [
  { id: 1,  date: 3,  month: 11, year: 2025, title: 'Ateliers Peinture',       type: 'Ateliers Adultes', color: 'bg-yellow-700', colorHex: '#a16207' },
  { id: 2,  date: 8,  month: 11, year: 2025, title: 'Ateliers Poterie',         type: 'Ateliers Enfants', color: 'bg-gray-600',   colorHex: '#4b5563' },
  { id: 3,  date: 9,  month: 11, year: 2025, title: 'Soirée Jazz au musée',     type: 'Soirées Musées',   color: 'bg-indigo-950', colorHex: '#1e1b4b' },
  { id: 4,  date: 11, month: 11, year: 2025, title: 'Concert de Noël',          type: 'Tout public',      color: 'bg-gray-800',   colorHex: '#1f2937' },
  { id: 5,  date: 14, month: 11, year: 2025, title: 'Atelier Sculpture',        type: 'Ateliers Adultes', color: 'bg-gray-700',   colorHex: '#374151' },
  { id: 6,  date: 15, month: 11, year: 2025, title: 'Visite Guidée',            type: 'Tout public',      color: 'bg-gray-700',   colorHex: '#374151' },
  { id: 7,  date: 10, month: 0,  year: 2026, title: 'Atelier gravure adultes',  type: 'Ateliers Adultes', color: 'bg-yellow-700', colorHex: '#a16207' },
  { id: 8,  date: 18, month: 0,  year: 2026, title: 'Conférence art moderne',   type: 'Tout public',      color: 'bg-gray-800',   colorHex: '#1f2937' },
  { id: 9,  date: 25, month: 0,  year: 2026, title: 'Soirée Jazz - Quartet',    type: 'Soirées Musées',   color: 'bg-indigo-950', colorHex: '#1e1b4b' },
];

const filters = [
  'Tous les événements',
  'Ateliers Adultes',
  'Ateliers Enfants',
  'Soirées Musées',
  'Tout public'
];

const monthNames = [
  'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
  'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
];

const DAYS_SHORT = ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'];

// ========================================
// ÉTAT GLOBAL
// ========================================

let currentMonth  = new Date().getMonth();
let currentYear   = new Date().getFullYear();
let activeFilter  = 'Tous les événements';
let selectedEvent = null;
let currentView   = 'list';
let showAllEvents = false;

// ========================================
// UTILITAIRES
// ========================================

function isMobile() {
  return window.innerWidth < 1024;
}

function getFilteredEvents() {
  return activeFilter === 'Tous les événements'
    ? events
    : events.filter(e => e.type === activeFilter);
}

function getDaysInMonth(month, year) {
  return new Date(year, month + 1, 0).getDate();
}

function getFirstDayOfMonth(month, year) {
  return new Date(year, month, 1).getDay();
}

function eventToDate(ev) {
  return new Date(ev.year, ev.month, ev.date);
}

// ========================================
// FILTRES
// ========================================

function renderFilters() {
  const container = document.getElementById('filterOptions');
  if (!container) return;

  container.innerHTML = filters.map(f => `
    <button class="filter-pill ${activeFilter === f ? 'active' : ''}"
            onclick="setActiveFilter('${f}')">${f}</button>
  `).join('');
}

function setActiveFilter(filter) {
  activeFilter  = filter;
  selectedEvent = null;
  showAllEvents = false;
  renderFilters();
  renderCalendar();
  renderSidebarList();
  renderMobileList();
}

// ========================================
// CALENDRIER
// ========================================

function renderCalendar() {
  const titleEl = document.getElementById('calendarTitle');
  const gridEl  = document.getElementById('calendarGrid');
  if (!titleEl || !gridEl) return;

  titleEl.textContent = `${monthNames[currentMonth]} ${currentYear}`;

  const daysInMonth = getDaysInMonth(currentMonth, currentYear);
  const firstDay    = getFirstDayOfMonth(currentMonth, currentYear);

  const filtered = getFilteredEvents().filter(
    e => e.month === currentMonth && e.year === currentYear
  );

  const byDay = {};
  filtered.forEach(ev => { byDay[ev.date] = ev; });

  let html = '';

  for (let i = 0; i < firstDay; i++) {
    html += '<div class="calendar-day-empty"></div>';
  }

  for (let day = 1; day <= daysInMonth; day++) {
    const ev         = byDay[day];
    const hasEvent   = !!ev;
    const isSelected = selectedEvent && ev && selectedEvent.id === ev.id;

    if (hasEvent) {
      html += `
        <div class="calendar-day-cell has-event ${ev.color} ${isSelected ? 'selected' : ''}"
             style="background-color:${ev.colorHex}; cursor:pointer;"
             onclick="selectEventById(${ev.id})"
             title="${ev.title}">
          <span class="day-number">${day}</span>
        </div>`;
    } else {
      html += `
        <div class="calendar-day-cell">
          <span class="day-number">${day}</span>
        </div>`;
    }
  }

  gridEl.innerHTML = html;
}

function previousMonth() {
  if (currentMonth === 0) { currentMonth = 11; currentYear--; }
  else { currentMonth--; }
  renderCalendar();
}

function nextMonth() {
  if (currentMonth === 11) { currentMonth = 0; currentYear++; }
  else { currentMonth++; }
  renderCalendar();
}

// ========================================
// SÉLECTION D'UN ÉVÉNEMENT
// ========================================

function selectEventById(id) {
  const ev = events.find(e => e.id === id);
  if (!ev) return;

  if (selectedEvent && selectedEvent.id === id) {
    selectedEvent = null;
  } else {
    selectedEvent = ev;
    currentMonth  = ev.month;
    currentYear   = ev.year;
  }

  renderCalendar();
  renderSidebarList();
}

// ========================================
// SIDEBAR (desktop)
// ========================================

function renderSidebarList() {
  const container    = document.getElementById('eventsList');
  const previewTitle = document.querySelector('.events-preview-title');
  if (!container) return;

  if (selectedEvent) {
    if (previewTitle) previewTitle.textContent = "Détails de l'événement";
    const ev = selectedEvent;
    container.innerHTML = `
      <div class="event-preview-card" style="background-color:${ev.colorHex}">
        <div class="event-date-box">
          <span class="event-month-abbr">${monthNames[ev.month].substring(0, 3)}</span>
          <span class="event-day-number">${ev.date}</span>
        </div>
        <div class="event-details">
          <h3 class="event-title">${ev.title}</h3>
          <span class="event-type-tag">${ev.type}</span>
          <div style="margin-top:1rem;">
            <a href="./reservation.html?event=${ev.id}"
               class="filter-pill"
               style="background:white;text-decoration:none;color:${ev.colorHex};font-weight:bold;">
              Réserver
            </a>
          </div>
        </div>
      </div>
      <button onclick="selectedEvent=null; renderCalendar(); renderSidebarList();"
              class="filter-pill"
              style="margin-top:1rem;width:100%;">
        ← Voir tous les événements
      </button>`;
    return;
  }

  if (previewTitle) {
    previewTitle.textContent = activeFilter === 'Tous les événements'
      ? 'Tous les événements' : activeFilter;
  }

  const filtered = getFilteredEvents().sort((a, b) => eventToDate(a) - eventToDate(b));

  container.innerHTML = filtered.map(ev => `
    <div class="event-preview-card" style="background-color:${ev.colorHex}; cursor:pointer;"
         onclick="selectEventById(${ev.id})">
      <div class="event-date-box">
        <span class="event-month-abbr">${monthNames[ev.month].substring(0, 3)}</span>
        <span class="event-day-number">${ev.date}</span>
      </div>
      <div class="event-details">
        <h3 class="event-title">${ev.title}</h3>
        <span class="event-type-tag">${ev.type}</span>
      </div>
    </div>
  `).join('');
}

// ========================================
// TOGGLE VUE MOBILE
// ========================================

function renderViewToggle() {
  const mainContent = document.querySelector('.calendar-main-content');
  if (!mainContent) return;

  let toggle = document.getElementById('viewToggle');
  if (!toggle) {
    toggle = document.createElement('div');
    toggle.id = 'viewToggle';
    toggle.className = 'view-toggle';
    mainContent.insertBefore(toggle, mainContent.firstChild);
  }

  toggle.innerHTML = `
    <button class="toggle-btn ${currentView === 'list' ? 'active' : ''}" onclick="switchView('list')">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
        <line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/>
        <line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/>
        <line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/>
      </svg>
      Liste
    </button>
    <button class="toggle-btn ${currentView === 'calendar' ? 'active' : ''}" onclick="switchView('calendar')">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
        <rect x="3" y="4" width="18" height="18" rx="2"/>
        <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
        <line x1="3" y1="10" x2="21" y2="10"/>
      </svg>
      Calendrier
    </button>
  `;
}

function switchView(view) {
  currentView = view;
  renderViewToggle();
  updateViewVisibility();
}

function updateViewVisibility() {
  const calCard    = document.querySelector('.calendar-card');
  const mobileList = document.getElementById('mobileEventsList');
  if (!calCard || !mobileList) return;

  if (isMobile()) {
    if (currentView === 'list') {
      calCard.classList.add('hidden-mobile');
      mobileList.classList.remove('hidden');
    } else {
      calCard.classList.remove('hidden-mobile');
      mobileList.classList.add('hidden');
    }
  } else {
    calCard.classList.remove('hidden-mobile');
    mobileList.classList.add('hidden');
  }
}

// ========================================
// VUE LISTE MOBILE
// ========================================

function renderMobileList() {
  const mainContent = document.querySelector('.calendar-main-content');
  if (!mainContent) return;

  let listEl = document.getElementById('mobileEventsList');
  if (!listEl) {
    listEl = document.createElement('div');
    listEl.id = 'mobileEventsList';
    listEl.className = 'mobile-events-list hidden';
    mainContent.appendChild(listEl);
  }

  const now      = new Date();
  const today    = new Date(now.getFullYear(), now.getMonth(), now.getDate());
  const filtered = getFilteredEvents();

  const upcoming = filtered
    .filter(e => eventToDate(e) >= today)
    .sort((a, b) => eventToDate(a) - eventToDate(b));

  const past = filtered
    .filter(e => eventToDate(e) < today)
    .sort((a, b) => eventToDate(b) - eventToDate(a));

  const displayList = showAllEvents ? [...upcoming, ...past] : upcoming;

  const groups = {};
  displayList.forEach(ev => {
    const key = `${ev.year}-${ev.month}`;
    if (!groups[key]) {
      groups[key] = { label: `${monthNames[ev.month]} ${ev.year}`, items: [] };
    }
    groups[key].items.push(ev);
  });

  let html = '';

  if (displayList.length === 0) {
    html = `<p style="text-align:center;color:#9ca3af;padding:2rem 0;font-size:0.9rem;">Aucun événement à venir</p>`;
  } else {
    Object.values(groups).forEach(group => {
      html += `<div class="mobile-month-group"><p class="mobile-month-label">${group.label}</p>`;
      group.items.forEach(ev => {
        const weekday = DAYS_SHORT[new Date(ev.year, ev.month, ev.date).getDay()];
        html += `
          <a class="mobile-event-card" href="./reservation.html?event=${ev.id}">
            <div class="mec-date">
              <span class="mec-day">${ev.date}</span>
              <span class="mec-weekday">${weekday}</span>
            </div>
            <div class="mec-body">
              <span class="mec-title">${ev.title}</span>
              <span class="mec-tag" style="background:${ev.colorHex}">${ev.type}</span>
            </div>
            <div class="mec-arrow">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path d="M9 18l6-6-6-6"/>
              </svg>
            </div>
          </a>`;
      });
      html += `</div>`;
    });
  }

  if (!showAllEvents && past.length > 0) {
    html += `
      <button class="mobile-show-all-btn" onclick="showAllEventsToggle()">
        Voir aussi les ${past.length} événement${past.length > 1 ? 's' : ''} passé${past.length > 1 ? 's' : ''}
      </button>`;
  }

  listEl.innerHTML = html;
  updateViewVisibility();
}

function showAllEventsToggle() {
  showAllEvents = true;
  renderMobileList();
}

// ========================================
// INITIALISATION
// ========================================

function initCalendar() {
  renderFilters();
  renderCalendar();
  renderSidebarList();
  renderViewToggle();
  renderMobileList();

  if (isMobile()) {
    currentView = 'list';
    renderViewToggle();
  }
  updateViewVisibility();

  if (window.lucide) window.lucide.createIcons();

  let resizeTimer;
  window.addEventListener('resize', () => {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(() => {
      renderViewToggle();
      updateViewVisibility();
    }, 150);
  });
}

document.addEventListener('DOMContentLoaded', initCalendar);