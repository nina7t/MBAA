const filters = [
  'Tous les événements',
  'Ateliers Adultes',
  'Ateliers Enfants',
  'Soirées Musées',
  'Tout public'
];

const events = [
  { date: 3, title: 'Ateliers Peinture', type: 'Ateliers Adultes', color: 'bg-yellow-700', colorHex: '#a16207' },
  { date: 8, title: 'Ateliers Poterie', type: 'Ateliers Enfants', color: 'bg-gray-600', colorHex: '#4b5563' },
  { date: 9, title: 'Soirée Jazz au musée', type: 'Soirées Musées', color: 'bg-indigo-950', colorHex: '#1e1b4b' },
  { date: 11, title: 'Concert de Noël', type: 'Tout public', color: 'bg-gray-800', colorHex: '#1f2937' },
  { date: 14, title: 'Atelier Sculpture', type: 'Ateliers Adultes', color: 'bg-gray-700', colorHex: '#374151' },
  { date: 15, title: 'Visite Guidée', type: 'Tout public', color: 'bg-gray-700', colorHex: '#374151' }
];

const monthNames = [
  'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
  'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
];

let currentMonth = 11; // Décembre
let currentYear = 2025;
let activeFilter = 'Tous les événements';
let selectedEvent = null;

function initCalendar() {
  renderFilters();
  renderCalendar();
  renderEventsList();
  if (window.lucide) {
    window.lucide.createIcons();
  }
}

function renderFilters() {
  const filterOptions = document.getElementById('filterOptions');
  filterOptions.innerHTML = filters.map(filter => `
    <button 
      class="filter-pill ${activeFilter === filter ? 'active' : ''}" 
      onclick="setActiveFilter('${filter}')"
    >
      ${filter}
    </button>
  `).join('');
}

function setActiveFilter(filter) {
  activeFilter = filter;
  selectedEvent = null; // Reset selection when filtering
  renderFilters();
  renderCalendar();
  renderEventsList();
}

function getDaysInMonth(month, year) {
  return new Date(year, month + 1, 0).getDate();
}

function getFirstDayOfMonth(month, year) {
  return new Date(year, month, 1).getDay();
}

function selectEvent(day) {
  const filteredEvents = activeFilter === 'Tous les événements' 
    ? events 
    : events.filter(e => e.type === activeFilter);
  
  const clickedEvent = filteredEvents.find(e => e.date === day);
  if (clickedEvent) {
    if (selectedEvent && selectedEvent.date === clickedEvent.date && selectedEvent.title === clickedEvent.title) {
      selectedEvent = null; // Unselect if clicking again
    } else {
      selectedEvent = clickedEvent;
    }
    renderCalendar();
    renderEventsList();
  }
}

function renderCalendar() {
  const calendarGrid = document.getElementById('calendarGrid');
  const calendarTitle = document.getElementById('calendarTitle');
  
  calendarTitle.textContent = `${monthNames[currentMonth]} ${currentYear}`;
  
  const daysInMonth = getDaysInMonth(currentMonth, currentYear);
  const firstDay = getFirstDayOfMonth(currentMonth, currentYear);
  
  const filteredEvents = activeFilter === 'Tous les événements' 
    ? events 
    : events.filter(e => e.type === activeFilter);

  let html = '';

  // Jours vides
  for (let i = 0; i < firstDay; i++) {
    html += '<div class="calendar-day-empty"></div>';
  }

  // Jours du mois
  for (let day = 1; day <= daysInMonth; day++) {
    const dayEvents = filteredEvents.filter(e => e.date === day);
    const hasEvent = dayEvents.length > 0;
    const isSelected = selectedEvent && selectedEvent.date === day;
    const eventColorClass = hasEvent ? dayEvents[0].color : '';
    const eventColorHex = hasEvent ? dayEvents[0].colorHex : '';

    html += `
      <div class="calendar-day-cell ${hasEvent ? 'has-event ' + eventColorClass : ''} ${isSelected ? 'selected' : ''}" 
           ${hasEvent ? `style="background-color: ${eventColorHex}; cursor: pointer;"` : ''}
           ${hasEvent ? `onclick="selectEvent(${day})"` : ''}>
        <span class="day-number">${day}</span>
        ${hasEvent ? '<div class="event-indicator"></div>' : ''}
      </div>
    `;
  }

  calendarGrid.innerHTML = html;
}

function renderEventsList() {
  const eventsList = document.getElementById('eventsList');
  const previewTitle = document.querySelector('.events-preview-title');
  
  if (selectedEvent) {
    previewTitle.textContent = "Détails de l'événement";
    eventsList.innerHTML = `
      <div class="event-preview-card ${selectedEvent.color}" style="background-color: ${selectedEvent.colorHex}">
        <div class="event-date-box">
          <span class="event-month-abbr">Déc</span>
          <span class="event-day-number">${selectedEvent.date}</span>
        </div>
        <div class="event-details">
          <h3 class="event-title">${selectedEvent.title}</h3>
          <span class="event-type-tag">${selectedEvent.type}</span>
          <div style="margin-top: 1rem;">
            <a href="./reservation.html?event=${selectedEvent.date}" class="filter-pill" style="background: white; text-decoration: none; color: ${selectedEvent.colorHex}; font-weight: bold;">Réserver</a>
          </div>
        </div>
      </div>
      <button onclick="selectedEvent=null; renderEventsList(); renderCalendar();" class="filter-pill" style="margin-top: 1rem; width: 100%;">Voir tous les événements</button>
    `;
    return;
  }

  previewTitle.textContent = activeFilter === 'Tous les événements' ? "Tous les événements" : activeFilter;
  
  const filteredEvents = activeFilter === 'Tous les événements' 
    ? events 
    : events.filter(e => e.type === activeFilter);

  eventsList.innerHTML = filteredEvents.map(event => `
    <div class="event-preview-card ${event.color}" style="background-color: ${event.colorHex}; cursor: pointer;" onclick="selectEvent(${event.date})">
      <div class="event-date-box">
        <span class="event-month-abbr">Déc</span>
        <span class="event-day-number">${event.date}</span>
      </div>
      <div class="event-details">
        <h3 class="event-title">${event.title}</h3>
        <span class="event-type-tag">${event.type}</span>
      </div>
    </div>
  `).join('');
}

function previousMonth() {
  if (currentMonth === 0) {
    currentMonth = 11;
    currentYear--;
  } else {
    currentMonth--;
  }
  renderCalendar();
}

function nextMonth() {
  if (currentMonth === 11) {
    currentMonth = 0;
    currentYear++;
  } else {
    currentMonth++;
  }
  renderCalendar();
}

document.addEventListener('DOMContentLoaded', initCalendar);
