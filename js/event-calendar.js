// ========================================
// DONNÉES DE BASE
// ========================================

// Tableau contenant tous les types de filtres disponibles pour les événements

const filters = [
  'Tous les événements',  // Option pour afficher tous les événements sans filtre
  'Ateliers Adultes',     // Filtre pour les ateliers destinés aux adultes
  'Ateliers Enfants',     // Filtre pour les ateliers destinés aux enfants
  'Soirées Musées',       // Filtre pour les événements en soirée au musée
  'Tout public'           // Filtre pour les événements ouverts à tous
];

// Tableau contenant tous les événements du mois

const events = [
  { 
    date: 3,                        // Jour du mois (3 décembre)
    title: 'Ateliers Peinture',     // Nom de l'événement
    type: 'Ateliers Adultes',       // Catégorie de l'événement (doit correspondre à un filtre)
    color: 'bg-yellow-700',         // Classe CSS Tailwind pour la couleur
    colorHex: '#a16207'             // Code hexadécimal de la couleur
  },
  { date: 8, title: 'Ateliers Poterie', type: 'Ateliers Enfants', color: 'bg-gray-600', colorHex: '#4b5563' },
  { date: 9, title: 'Soirée Jazz au musée', type: 'Soirées Musées', color: 'bg-indigo-950', colorHex: '#1e1b4b' },
  { date: 11, title: 'Concert de Noël', type: 'Tout public', color: 'bg-gray-800', colorHex: '#1f2937' },
  { date: 14, title: 'Atelier Sculpture', type: 'Ateliers Adultes', color: 'bg-gray-700', colorHex: '#374151' },
  { date: 15, title: 'Visite Guidée', type: 'Tout public', color: 'bg-gray-700', colorHex: '#374151' }
];

// Tableau contenant les noms des mois en français
const monthNames = [
  'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
  'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
];

// ========================================
// VARIABLES D'ÉTAT
// ========================================

let currentMonth = 11;                    // Mois actuel (11 = décembre, car les mois vont de 0 à 11)
let currentYear = 2025;                   // Année actuelle
let activeFilter = 'Tous les événements'; // Filtre actuellement actif (par défaut : tous les événements)
let selectedEvent = null;                 // Événement actuellement sélectionné (null = aucun)

// ========================================
// FONCTION D'INITIALISATION
// ========================================

// Fonction appelée au chargement de la page pour initialiser le calendrier

function initCalendar() {
  renderFilters();    // Affiche les boutons de filtres
  renderCalendar();   // Affiche le calendrier du mois
  renderEventsList(); // Affiche la liste des événements
  
  // Si la bibliothèque Lucide (icônes) est disponible, créer les icônes
  if (window.lucide) {
    window.lucide.createIcons();
  }
}

// ========================================
// GESTION DES FILTRES
// ========================================

// Fonction qui génère et affiche les boutons de filtres

function renderFilters() {

  // Récupère l'élément HTML où afficher les filtres
  const filterOptions = document.getElementById('filterOptions');
  
  // Crée un bouton pour chaque filtre et les insère dans le DOM

  filterOptions.innerHTML = filters.map(filter => `
    <button class="filter-pill ${activeFilter === filter ? 'active' : ''}"  onclick="setActiveFilter('${filter}')" >
      ${filter}
    </button>
  `).join('');
  
  // map() : transforme chaque filtre en HTML de bouton
  // activeFilter === filter ? 'active' : '' : ajoute la classe 'active' au filtre sélectionné
  // join('') : combine tous les boutons en une seule chaîne de caractères
}

// Fonction appelée quand l'utilisateur clique sur un filtre
function setActiveFilter(filter) {
  activeFilter = filter;      // Met à jour le filtre actif
  selectedEvent = null;       // Réinitialise la sélection d'événement (pour revenir à la vue liste)
  renderFilters();            // Réaffiche les filtres (pour mettre à jour le bouton actif)
  renderCalendar();           // Réaffiche le calendrier avec le nouveau filtre
  renderEventsList();         // Réaffiche la liste des événements filtrés
}

// ========================================
// FONCTIONS UTILITAIRES POUR LES DATES
// ========================================

// Retourne le nombre de jours dans un mois donné
function getDaysInMonth(month, year) {
  // new Date(year, month + 1, 0) crée une date au jour 0 du mois suivant
  // ce qui correspond au dernier jour du mois actuel
  return new Date(year, month + 1, 0).getDate();
}

// Retourne le jour de la semaine du premier jour du mois (0=dimanche, 1=lundi, etc.)
function getFirstDayOfMonth(month, year) {
  return new Date(year, month, 1).getDay();
}

// ========================================
// GESTION DE LA SÉLECTION D'ÉVÉNEMENTS
// ========================================

// Fonction appelée quand l'utilisateur clique sur un jour avec événement
function selectEvent(day) {
  // Filtre les événements selon le filtre actif
  const filteredEvents = activeFilter === 'Tous les événements' 
    ? events                                      // Si "Tous", garde tous les événements
    : events.filter(e => e.type === activeFilter); // Sinon, filtre par type
  
  // Trouve l'événement correspondant au jour cliqué
  const clickedEvent = filteredEvents.find(e => e.date === day);
  
  // Si un événement existe à cette date
  if (clickedEvent) {
    // Vérifie si l'événement cliqué est déjà sélectionné
    if (selectedEvent && selectedEvent.date === clickedEvent.date && selectedEvent.title === clickedEvent.title) {
      selectedEvent = null; // Si oui, désélectionne (toggle)
    } else {
      selectedEvent = clickedEvent; // Sinon, sélectionne le nouvel événement
    }
    // Réaffiche le calendrier et la liste pour refléter la sélection
    renderCalendar();
    renderEventsList();
  }
}

// ========================================
// AFFICHAGE DU CALENDRIER
// ========================================

// Fonction principale qui génère l'affichage du calendrier
function renderCalendar() {
  // Récupère les éléments HTML où afficher le calendrier
  const calendarGrid = document.getElementById('calendarGrid');
  const calendarTitle = document.getElementById('calendarTitle');
  
  // Affiche le titre avec le mois et l'année actuels
  calendarTitle.textContent = `${monthNames[currentMonth]} ${currentYear}`;
  
  // Calcule le nombre de jours dans le mois actuel
  const daysInMonth = getDaysInMonth(currentMonth, currentYear);
  // Calcule quel jour de la semaine commence le mois (0-6)
  const firstDay = getFirstDayOfMonth(currentMonth, currentYear);
  
  // Filtre les événements selon le filtre actif
  const filteredEvents = activeFilter === 'Tous les événements' 
    ? events 
    : events.filter(e => e.type === activeFilter);

  let html = ''; // Variable pour construire le HTML du calendrier

  // ========================================
  // JOURS VIDES AVANT LE 1ER DU MOIS
  // ========================================
  
  // Ajoute des cellules vides pour les jours avant le début du mois
  // Par exemple, si le mois commence un mercredi, ajoute 3 cellules vides
  for (let i = 0; i < firstDay; i++) {
    html += '<div class="calendar-day-empty"></div>';
  }

  // ========================================
  // JOURS DU MOIS
  // ========================================
  
  // Boucle sur chaque jour du mois
  for (let day = 1; day <= daysInMonth; day++) {
    // Trouve tous les événements pour ce jour
    const dayEvents = filteredEvents.filter(e => e.date === day);
    // Vérifie si ce jour a au moins un événement
    const hasEvent = dayEvents.length > 0;
    // Vérifie si cet événement est actuellement sélectionné
    const isSelected = selectedEvent && selectedEvent.date === day;
    // Récupère la classe de couleur du premier événement (s'il existe)
    const eventColorClass = hasEvent ? dayEvents[0].color : '';
    // Récupère le code hexadécimal de la couleur
    const eventColorHex = hasEvent ? dayEvents[0].colorHex : '';

    // Construit le HTML pour cette cellule de jour
    html += `
      <div class="calendar-day-cell ${hasEvent ? 'has-event ' + eventColorClass : ''} ${isSelected ? 'selected' : ''}" 
           ${hasEvent ? `style="background-color: ${eventColorHex}; cursor: pointer;"` : ''}
           ${hasEvent ? `onclick="selectEvent(${day})"` : ''}>
        <span class="day-number">${day}</span>
        ${hasEvent ? '<div class="event-indicator"></div>' : ''}
      </div>
    `;
    // Explications :
    // - Ajoute la classe 'has-event' si le jour a un événement
    // - Ajoute la classe 'selected' si l'événement est sélectionné
    // - Ajoute un style inline avec la couleur de l'événement
    // - Ajoute un onclick seulement si le jour a un événement
    // - Affiche le numéro du jour
    // - Ajoute un indicateur visuel si le jour a un événement
  }

  // Insère tout le HTML généré dans la grille du calendrier
  calendarGrid.innerHTML = html;
}

// ========================================
// AFFICHAGE DE LA LISTE DES ÉVÉNEMENTS
// ========================================

// Fonction qui affiche soit un événement sélectionné, soit la liste de tous les événements
function renderEventsList() {
  // Récupère les éléments HTML
  const eventsList = document.getElementById('eventsList');
  const previewTitle = document.querySelector('.events-preview-title');
  
  // ========================================
  // CAS 1 : UN ÉVÉNEMENT EST SÉLECTIONNÉ
  // ========================================
  
  // Si un événement est sélectionné, affiche ses détails
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
    // Ce bouton réinitialise la sélection et réaffiche la liste complète
    return; // Sort de la fonction (ne continue pas)
  }

  // ========================================
  // CAS 2 : AUCUN ÉVÉNEMENT SÉLECTIONNÉ
  // ========================================
  
  // Met à jour le titre selon le filtre actif
  previewTitle.textContent = activeFilter === 'Tous les événements' ? "Tous les événements" : activeFilter;
  
  // Filtre les événements selon le filtre actif
  const filteredEvents = activeFilter === 'Tous les événements' 
    ? events 
    : events.filter(e => e.type === activeFilter);

  // Génère une carte pour chaque événement filtré
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
  // map() transforme chaque événement en HTML de carte
  // join('') combine toutes les cartes en une seule chaîne
}

// ========================================
// NAVIGATION ENTRE LES MOIS
// ========================================

// Fonction pour aller au mois précédent
function previousMonth() {
  if (currentMonth === 0) {    // Si on est en janvier (mois 0)
    currentMonth = 11;          // Passe à décembre (mois 11)
    currentYear--;              // Diminue l'année de 1
  } else {
    currentMonth--;             // Sinon, diminue simplement le mois de 1
  }
  renderCalendar();             // Réaffiche le calendrier avec le nouveau mois
}

// Fonction pour aller au mois suivant
function nextMonth() {
  if (currentMonth === 11) {    // Si on est en décembre (mois 11)
    currentMonth = 0;            // Passe à janvier (mois 0)
    currentYear++;               // Augmente l'année de 1
  } else {
    currentMonth++;              // Sinon, augmente simplement le mois de 1
  }
  renderCalendar();              // Réaffiche le calendrier avec le nouveau mois
}

// ========================================
// INITIALISATION AU CHARGEMENT DE LA PAGE
// ========================================

// Écoute l'événement de chargement complet du DOM
// Quand la page est complètement chargée, lance initCalendar()
document.addEventListener('DOMContentLoaded', initCalendar);