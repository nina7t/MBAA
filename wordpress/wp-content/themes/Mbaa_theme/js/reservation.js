// ==========================================
// DASHBOARD NAVIGATION FUNCTIONS
// ==========================================

function showDashboard() {
  hideAllSections();
  document.getElementById('reservationDashboard').style.display = 'block';
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

function hideAllSections() {
  document.getElementById('reservationDashboard').style.display = 'none';
  document.getElementById('ticketPurchaseSection').style.display = 'none';
  document.getElementById('legacyReservationSection').style.display = 'none';
  document.getElementById('summarySection').style.display = 'none';
  document.getElementById('ticketConfirmationSection').style.display = 'none';
}

// Navigation functions for each module
function goToTicketPurchase() {
  hideAllSections();
  document.getElementById('ticketPurchaseSection').style.display = 'block';
  
  // Initialize ticket system if needed
  if (!window.ticketSystemInitialized) {
    initTicketSystem();
    window.ticketSystemInitialized = true;
  }
  
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

function goToEventReservation() {
  hideAllSections();
  document.getElementById('legacyReservationSection').style.display = 'block';
  loadEventDetails();
  
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

function goToWorkshop() {
  // Redirect to specific workshop page or show workshop section
  window.location.href = './reservation.html?event=1';
}

function goToGuidedTour() {
  // Redirect to guided tour page
  window.location.href = './reservation.html?event=6';
}

function goToSpecialEvening() {
  // Redirect to special evening page
  window.location.href = './reservation.html?event=3';
}

function goToKidsWorkshop() {
  // Redirect to kids workshop page
  window.location.href = './reservation.html?event=7';
}

function goToEventDetail(eventId) {
  window.location.href = `./reservation.html?event=${eventId}`;
}

// ==========================================
// TOGGLE FUNCTIONS
// ==========================================

function showLegacySystem() {
  hideAllSections();
  document.getElementById('legacyReservationSection').style.display = 'block';
  loadEventDetails();
}

function showTicketSystem() {
  hideAllSections();
  document.getElementById('ticketPurchaseSection').style.display = 'block';
  
  // Initialize ticket system if needed
  if (!window.ticketSystemInitialized) {
    initTicketSystem();
    window.ticketSystemInitialized = true;
  }
}

// ==========================================
// SYSTÈME DE FILTRES
// ==========================================

// État des filtres actifs
const filtresActifs = {
  category: 'all',
  price: 'all',
  availability: 'all'
};

function initFiltres() {
  const pills = document.querySelectorAll('.filter-pill');
  
  pills.forEach(pill => {
    pill.addEventListener('click', () => {
      const filterType = pill.dataset.filter;
      const filterValue = pill.dataset.value;

      // Désactiver les autres pills du même groupe
      document.querySelectorAll(`[data-filter="${filterType}"]`)
        .forEach(p => p.classList.remove('active'));
      
      pill.classList.add('active');
      filtresActifs[filterType] = filterValue;

      appliquerFiltres();
    });
  });

  // Bouton reset
  const resetBtn = document.getElementById('filterReset');
  if (resetBtn) {
    resetBtn.addEventListener('click', resetFiltres);
  }
}

function appliquerFiltres() {
  const evenementsFiltres = evenements.filter(evenement => {
    // Filtre catégorie
    if (filtresActifs.category !== 'all' && evenement.categorie !== filtresActifs.category) {
      return false;
    }

    // Filtre prix
    if (filtresActifs.price === 'free' && evenement.prix > 0) return false;
    if (filtresActifs.price === 'paid' && evenement.prix === 0) return false;

    // Filtre disponibilité
    if (filtresActifs.availability === 'available' && evenement.placesDisponibles === 0) return false;
    if (filtresActifs.availability === 'last' && evenement.placesDisponibles > 10) return false;

    return true;
  });

  afficherEvenementsFilters(evenementsFiltres);
  
  // Mettre à jour le compteur
  const count = document.getElementById('filtersCount');
  if (count) {
    count.textContent = `${evenementsFiltres.length} événement${evenementsFiltres.length > 1 ? 's' : ''}`;
  }
}

function afficherEvenementsFilters(liste) {
  const grid = document.getElementById('eventsGrid');
  const carousel = document.querySelector('.events-carousel');

  // Animation de sortie
  const cards = grid ? grid.querySelectorAll('.event-card') : [];
  const slides = carousel ? carousel.querySelectorAll('.event-slide') : [];
  
  cards.forEach(card => card.classList.add('event-card--exiting'));
  slides.forEach(slide => slide.classList.add('event-card--exiting'));

  setTimeout(() => {
    // Vider et repeupler
    if (grid) {
      grid.innerHTML = '';
      if (liste.length === 0) {
        grid.innerHTML = `
          <div class="filters-empty">
            <p>Aucun événement ne correspond à vos filtres.</p>
            <button onclick="resetFiltres()" class="filter-pill active">Voir tous les événements</button>
          </div>`;
      } else {
        liste.forEach(e => {
          const eventCard = createEventCard(e);
          eventCard.classList.remove('event-card--exiting');
          grid.appendChild(eventCard);
        });
      }
    }

    if (carousel) {
      carousel.innerHTML = '';
      liste.forEach(e => {
        const eventSlide = createEventSlide(e);
        eventSlide.classList.remove('event-card--exiting');
        carousel.appendChild(eventSlide);
      });
    }
  }, 200);
}

function resetFiltres() {
  // Remettre tous les filtres à "all"
  Object.keys(filtresActifs).forEach(key => filtresActifs[key] = 'all');
  
  document.querySelectorAll('.filter-pill').forEach(pill => {
    pill.classList.toggle('active', pill.dataset.value === 'all');
  });

  appliquerFiltres();
}

function createEventSlide(evenement) {
  const slide = document.createElement('div');
  slide.className = 'event-slide';
  slide.onclick = () => goToEventDetail(evenement.id);
  
  slide.innerHTML = `
    <div class="event-image">
      <img src="${getEventImage(evenement.id)}" 
           alt="${evenement.nom} - ${evenement.description}" 
           loading="lazy"
           onerror="this.parentElement.classList.add('event-image--fallback')">
      <span class="event-badge">${getEventBadge(evenement)}</span>
    </div>
    <div class="event-info">
      <h4 class="event-name">${evenement.nom}</h4>
      <p class="event-date">${formatDate(evenement.date)}</p>
      <span class="event-price">${evenement.prix === 0 ? 'Gratuit' : evenement.prix + '€'}</span>
    </div>
  `;
  
  return slide;
}

function getEventBadge(evenement) {
  if (evenement.placesDisponibles === 0) return 'Complet';
  if (evenement.placesDisponibles <= 5) return 'Dernières places';
  if (evenement.prix === 0) return 'Populaire';
  return 'Disponible';
}

// ==========================================
// MODULE DE RÉSERVATION DE TICKETS
// ==========================================

// Structure des données événements avec gestion des places
const evenements = [
  {
    id: 1,
    nom: "Exposition Lumière",
    categorie: "exposition",
    date: "2025-03-15",
    description: "Une exposition immersive sur l'art de la lumière et ses influences à travers les époques.",
    prix: 10,
    placesDisponibles: 50,
    placesTotales: 50,
    lieu: "Galerie principale",
    horaire: "10:00 - 18:00"
  },
  {
    id: 2,
    nom: "Atelier Sculpture",
    categorie: "atelier",
    date: "2025-03-20",
    description: "Initiation à la sculpture sur argile avec un artiste professionnel.",
    prix: 15,
    placesDisponibles: 20,
    placesTotales: 20,
    lieu: "Atelier créatif",
    horaire: "14:00 - 17:00"
  },
  {
    id: 3,
    nom: "Visite Nocturne",
    categorie: "visite",
    date: "2025-03-22",
    description: "Découverte des coulisses du musée après la fermeture au public.",
    prix: 12,
    placesDisponibles: 30,
    placesTotales: 30,
    lieu: "Entrée principale",
    horaire: "20:00 - 22:00"
  },
  {
    id: 4,
    nom: "Concert Jazz",
    categorie: "concert",
    date: "2025-03-25",
    description: "Soirée jazz en plein cœur des collections permanentes.",
    prix: 0,
    placesDisponibles: 100,
    placesTotales: 100,
    lieu: "Cour d'honneur",
    horaire: "20:00 - 23:00"
  },
  {
    id: 5,
    nom: "Atelier Dessin Enfants",
    categorie: "atelier",
    date: "2025-03-28",
    description: "Atelier de dessin adapté aux enfants de 6 à 12 ans.",
    prix: 5,
    placesDisponibles: 15,
    placesTotales: 15,
    lieu: "Espace jeunesse",
    horaire: "15:00 - 16:30"
  },
  {
    id: 6,
    nom: "Conférence Art Moderne",
    categorie: "conference",
    date: "2025-04-02",
    description: "Analyse des courants artistiques modernes par un expert renommé.",
    prix: 8,
    placesDisponibles: 8,
    placesTotales: 50,
    lieu: "Auditorium",
    horaire: "18:00 - 20:00"
  }
];

// Grille tarifaire Musée MAT
const tarifsMusee = {
  moins_6: { nom: "Gratuit", prix: 0 },
  enfant: { nom: "Enfant", prix: 5, min: 6, max: 17 },
  jeune: { nom: "Jeune", prix: 8, min: 18, max: 25 },
  adulte: { nom: "Adulte", prix: 12, min: 26, max: 64 },
  senior: { nom: "Senior", prix: 8, min: 65 }
};

// État global de l'application
let reservationState = {
  formData: {
    firstName: '',
    lastName: '',
    birthDate: '',
    ticketType: '',
    eventId: null,
    quantity: 1
  },
  pricing: {
    age: null,
    tariff: null,
    unitPrice: 0,
    totalPrice: 0
  },
  currentStep: 'form' // form, summary, confirmation
};

// ==========================================
// FONCTIONS UTILITAIRES
// ==========================================

function calculerAge(dateNaissance) {
  const aujourdhui = new Date();
  const dateNaiss = new Date(dateNaissance);
  let age = aujourdhui.getFullYear() - dateNaiss.getFullYear();
  const differenceMois = aujourdhui.getMonth() - dateNaiss.getMonth();
  
  if (differenceMois < 0 || (differenceMois === 0 && aujourdhui.getDate() < dateNaiss.getDate())) {
    age--;
  }
  
  return age;
}

function getTarifParAge(age) {
  if (age < 6) return tarifsMusee.moins_6;
  if (age >= 6 && age <= 17) return tarifsMusee.enfant;
  if (age >= 18 && age <= 25) return tarifsMusee.jeune;
  if (age >= 26 && age <= 64) return tarifsMusee.adulte;
  if (age >= 65) return tarifsMusee.senior;
  
  return tarifsMusee.adulte; // Par défaut
}

function formatDate(dateStr) {
  const date = new Date(dateStr + 'T00:00:00');
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return date.toLocaleDateString('fr-FR', options);
}

function genererNumeroReservation() {
  const prefix = 'MAT';
  const timestamp = Date.now().toString(36).toUpperCase();
  const random = Math.random().toString(36).substring(2, 6).toUpperCase();
  return `${prefix}-${timestamp}-${random}`;
}

function mettreAJourPlacesDisponibles(eventId, quantity) {
  const evenement = evenements.find(e => e.id === eventId);
  if (evenement) {
    evenement.placesDisponibles = Math.max(0, evenement.placesDisponibles - quantity);
    return evenement.placesDisponibles;
  }
  return 0;
}

// ==========================================
// GESTION DU FORMULAIRE
// ==========================================

function initFormListeners() {
  // Écouteurs pour les changements dans le formulaire
  document.getElementById('birthDate').addEventListener('change', handleBirthDateChange);
  document.getElementById('ticketType').addEventListener('change', handleTicketTypeChange);
  document.getElementById('eventSelect').addEventListener('change', handleEventChange);
  document.getElementById('quantity').addEventListener('change', handleQuantityChange);
  document.getElementById('previewBtn').addEventListener('click', showSummary);
  document.getElementById('backBtn').addEventListener('click', backToForm);
  document.getElementById('confirmBtn').addEventListener('click', confirmReservation);
  document.getElementById('newReservationBtn').addEventListener('click', newReservation);
}

function handleBirthDateChange(e) {
  const birthDate = e.target.value;
  if (!birthDate) return;
  
  const age = calculerAge(birthDate);
  reservationState.formData.birthDate = birthDate;
  reservationState.pricing.age = age;
  
  updatePricing();
}

function handleTicketTypeChange(e) {
  const ticketType = e.target.value;
  reservationState.formData.ticketType = ticketType;
  
  const eventSelectionGroup = document.getElementById('eventSelectionGroup');
  const eventsListSection = document.getElementById('eventsListSection');
  
  if (ticketType === 'event') {
    eventSelectionGroup.style.display = 'block';
    eventsListSection.style.display = 'block';
    populateEventsList();
    populateEventSelect();
  } else {
    eventSelectionGroup.style.display = 'none';
    eventsListSection.style.display = 'none';
    reservationState.formData.eventId = null;
  }
  
  updatePricing();
}

function handleEventChange(e) {
  const eventId = parseInt(e.target.value);
  reservationState.formData.eventId = eventId || null;
  
  updatePricing();
}

function handleQuantityChange(e) {
  const quantity = parseInt(e.target.value);
  reservationState.formData.quantity = quantity;
  
  updatePricing();
}

function updatePricing() {
  const { birthDate, ticketType, eventId, quantity } = reservationState.formData;
  
  if (!birthDate || !ticketType) {
    hidePriceDisplay();
    return;
  }
  
  const age = reservationState.pricing.age;
  let unitPrice = 0;
  let tariff = null;
  
  if (ticketType === 'musee') {
    tariff = getTarifParAge(age);
    unitPrice = tariff.prix;
  } else if (ticketType === 'event' && eventId) {
    const evenement = evenements.find(e => e.id === eventId);
    if (evenement) {
      // Pour les événements, on peut appliquer la même grille tarifaire ou un prix fixe
      // Ici, j'applique le prix de l'événement + la réduction selon l'âge si applicable
      const tarifAge = getTarifParAge(age);
      if (tarifAge.prix === 0) {
        unitPrice = 0; // Gratuit pour les moins de 6 ans
      } else {
        unitPrice = evenement.prix; // Prix fixe de l'événement
      }
      tariff = { nom: evenement.nom, prix: unitPrice };
    }
  }
  
  const totalPrice = unitPrice * quantity;
  
  reservationState.pricing.tariff = tariff;
  reservationState.pricing.unitPrice = unitPrice;
  reservationState.pricing.totalPrice = totalPrice;
  
  displayPricing(age, tariff, quantity, totalPrice);
}

function displayPricing(age, tariff, quantity, totalPrice) {
  const priceDisplay = document.getElementById('priceDisplay');
  const detectedAge = document.getElementById('detectedAge');
  const appliedTariff = document.getElementById('appliedTariff');
  const totalPriceElement = document.getElementById('totalPrice');
  
  detectedAge.textContent = `${age} ans`;
  appliedTariff.textContent = tariff ? `${tariff.nom} - ${tariff.prix}€` : '-';
  totalPriceElement.textContent = `${totalPrice}€`;
  
  priceDisplay.style.display = 'block';
}

function hidePriceDisplay() {
  document.getElementById('priceDisplay').style.display = 'none';
}

// ==========================================
// GESTION DES ÉVÉNEMENTS
// ==========================================

function populateEventsList() {
  const eventsGrid = document.getElementById('eventsGrid');
  if (!eventsGrid) return;
  
  eventsGrid.innerHTML = evenements.map(evenement => `
    <div class="event-card ${evenement.placesDisponibles === 0 ? 'event-full' : ''}" data-event-id="${evenement.id}">
      <div class="event-header">
        <h4 class="event-title">${evenement.nom}</h4>
        <span class="event-status ${evenement.placesDisponibles === 0 ? 'unavailable' : 'available'}">
          ${evenement.placesDisponibles === 0 ? 'Complet' : `${evenement.placesDisponibles} places`}
        </span>
      </div>
      
      <div class="event-image">
        <img src="${getEventImage(evenement.id)}" 
             alt="${evenement.nom} - ${evenement.description}" 
             loading="lazy"
             onerror="this.parentElement.classList.add('event-image--fallback')">
      </div>
      
      <div class="event-body">
        <p class="event-description">${evenement.description}</p>
        <p><strong>Date:</strong> ${formatDate(evenement.date)}</p>
        <p><strong>Horaire:</strong> ${evenement.horaire}</p>
        <p><strong>Lieu:</strong> ${evenement.lieu}</p>
      </div>
      
      <div class="event-footer">
        <span class="event-price">${evenement.prix}€</span>
        ${evenement.placesDisponibles > 0 ? 
          `<button class="event-select-btn" onclick="selectEvent(${evenement.id})">Sélectionner</button>` :
          `<span class="event-full">Événement complet</span>`
        }
      </div>
    </div>
  `).join('');
}

// Helper function to get event image by ID
function getEventImage(eventId) {
  const eventImages = {
    1: './asset/Img/Evenement/evenement-visite-musee.jpg',
    2: './asset/Img/Evenement/evenement-soiree-1.jpg', 
    3: './asset/Img/Evenement/evenement-concert.jpg',
    4: './asset/Img/Evenement/evenement-lumiere-musee.jpg',
    5: './asset/Img/Evenement/evenement-visite-guidee.jpg'
  };
  return eventImages[eventId] || './asset/Img/Evenement/evenement-beaux-arts.jpg';
}

function populateEventSelect() {
  const eventSelect = document.getElementById('eventSelect');
  eventSelect.innerHTML = '<option value="">Sélectionnez un événement</option>';
  
  evenements.forEach(evenement => {
    if (evenement.placesDisponibles > 0) {
      const option = document.createElement('option');
      option.value = evenement.id;
      option.textContent = `${evenement.nom} - ${formatDate(evenement.date)} - ${evenement.prix}€`;
      eventSelect.appendChild(option);
    }
  });
}

function selectEvent(eventId) {
  document.getElementById('eventSelect').value = eventId;
  handleEventChange({ target: { value: eventId } });
  
  // Scroll to form
  document.querySelector('.ticket-form-section').scrollIntoView({ 
    behavior: 'smooth', 
    block: 'start' 
  });
}

// ==========================================
// RÉCAPITULATIF ET CONFIRMATION
// ==========================================

function showSummary() {
  if (!validateForm()) return;
  
  updateSummaryDisplay();
  document.querySelector('.ticket-purchase-section').style.display = 'none';
  document.getElementById('summarySection').style.display = 'block';
  
  reservationState.currentStep = 'summary';
  
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

function validateForm() {
  const { firstName, lastName, birthDate, ticketType, quantity } = reservationState.formData;
  
  if (!firstName || !lastName || !birthDate || !ticketType || !quantity) {
    alert('Veuillez remplir tous les champs obligatoires.');
    return false;
  }
  
  if (ticketType === 'event' && !reservationState.formData.eventId) {
    alert('Veuillez sélectionner un événement.');
    return false;
  }
  
  if (ticketType === 'event' && reservationState.formData.eventId) {
    const evenement = evenements.find(e => e.id === reservationState.formData.eventId);
    if (evenement && evenement.placesDisponibles < quantity) {
      alert(`Il ne reste que ${evenement.placesDisponibles} place(s) disponible(s) pour cet événement.`);
      return false;
    }
  }
  
  return true;
}

function updateSummaryDisplay() {
  const { firstName, lastName, ticketType, eventId, quantity } = reservationState.formData;
  const { age, tariff, totalPrice } = reservationState.pricing;
  
  document.getElementById('summaryName').textContent = `${firstName} ${lastName}`;
  document.getElementById('summaryTicketType').textContent = ticketType === 'musee' ? 'Entrée Musée MAT' : 'Événement spécifique';
  document.getElementById('summaryAge').textContent = `${age} ans`;
  document.getElementById('summaryTariff').textContent = `${tariff.nom} - ${tariff.prix}€`;
  document.getElementById('summaryQuantity').textContent = quantity;
  document.getElementById('summaryTotal').textContent = `${totalPrice}€`;
  
  const summaryEventItem = document.getElementById('summaryEventItem');
  if (ticketType === 'event' && eventId) {
    const evenement = evenements.find(e => e.id === eventId);
    document.getElementById('summaryEvent').textContent = evenement ? evenement.nom : '-';
    summaryEventItem.style.display = 'flex';
  } else {
    summaryEventItem.style.display = 'none';
  }
}

function backToForm() {
  document.getElementById('summarySection').style.display = 'none';
  document.querySelector('.ticket-purchase-section').style.display = 'block';
  reservationState.currentStep = 'form';
}

function confirmReservation() {
  const reservationNumber = genererNumeroReservation();
  const { firstName, lastName, ticketType, eventId, quantity } = reservationState.formData;
  const { totalPrice } = reservationState.pricing;
  
  // Mettre à jour les places disponibles si c'est un événement
  if (ticketType === 'event' && eventId) {
    mettreAJourPlacesDisponibles(eventId, quantity);
  }
  
  // Afficher la confirmation
  updateConfirmationDisplay(reservationNumber);
  document.getElementById('summarySection').style.display = 'none';
  document.getElementById('ticketConfirmationSection').style.display = 'block';
  
  reservationState.currentStep = 'confirmation';
  
  // Log pour le débogage
  console.log('Réservation confirmée:', {
    reservationNumber,
    formData: reservationState.formData,
    pricing: reservationState.pricing
  });
}

function updateConfirmationDisplay(reservationNumber) {
  const { firstName, lastName, ticketType, eventId } = reservationState.formData;
  const { totalPrice } = reservationState.pricing;
  
  document.getElementById('reservationNumber').textContent = reservationNumber;
  document.getElementById('confirmationName').textContent = `${firstName} ${lastName}`;
  document.getElementById('confirmationTicketType').textContent = ticketType === 'musee' ? 'Entrée Musée MAT' : 'Événement spécifique';
  document.getElementById('confirmationTotal').textContent = `${totalPrice}€`;
  
  const confirmationEventItem = document.getElementById('confirmationEventItem');
  if (ticketType === 'event' && eventId) {
    const evenement = evenements.find(e => e.id === eventId);
    document.getElementById('confirmationEvent').textContent = evenement ? evenement.nom : '-';
    confirmationEventItem.style.display = 'flex';
  } else {
    confirmationEventItem.style.display = 'none';
  }
}

function newReservation() {
  // Réinitialiser l'état
  reservationState = {
    formData: {
      firstName: '',
      lastName: '',
      birthDate: '',
      ticketType: '',
      eventId: null,
      quantity: 1
    },
    pricing: {
      age: null,
      tariff: null,
      unitPrice: 0,
      totalPrice: 0
    },
    currentStep: 'form'
  };
  
  // Réinitialiser le formulaire
  document.getElementById('ticketForm').reset();
  hidePriceDisplay();
  
  // Masquer la section de confirmation et afficher le formulaire
  document.getElementById('ticketConfirmationSection').style.display = 'none';
  document.querySelector('.ticket-purchase-section').style.display = 'block';
  
  // Réinitialiser les listes d'événements
  populateEventsList();
  populateEventSelect();
  
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ==========================================
// INITIALISATION
// ==========================================

function initTicketSystem() {
  // Vérifier si on vient d'une page d'événement spécifique
  const urlParams = new URLSearchParams(window.location.search);
  const eventId = urlParams.get('event');
  
  if (eventId) {
    const evenement = evenements.find(e => e.id === parseInt(eventId));
    if (evenement && evenement.placesDisponibles > 0) {
      reservationState.formData.ticketType = 'event';
      reservationState.formData.eventId = parseInt(eventId);
      
      // Pré-remplir le formulaire
      setTimeout(() => {
        document.getElementById('ticketType').value = 'event';
        handleTicketTypeChange({ target: { value: 'event' } });
        document.getElementById('eventSelect').value = eventId;
        handleEventChange({ target: { value: eventId } });
      }, 100);
    }
  }
  
  // Initialiser les écouteurs d'événements
  initFormListeners();
  
  // Initialiser les listes d'événements
  populateEventsList();
  
  // Initialiser les filtres
  initFiltres();
}

// ==========================================
// CODE LÉGACY (COMPATIBILITÉ)
// ==========================================

// Conserver les fonctions existantes pour la rétrocompatibilité
const eventDetails = {
  1: {
    title: 'Ateliers Interactifs',
    date: '2025-12-03',
    time: '14:30 - 15:30',
    duration: '1h',
    location: 'Salle d\'atelier - Niveau 2',
    audience: 'Tous les âges',
    capacity: 20,
    available: 12,
    price: 'Gratuit',
    description: 'Participez à nos ateliers interactifs conçus pour tous les âges et niveaux d\'expérience. Que vous soyez un artiste en herbe ou simplement curieux, nos ateliers offrent une opportunité unique d\'explorer votre créativité tout en découvrant les techniques artistiques. Venez exprimer votre imagination dans une ambiance conviviale!'
  },
  2: {
    title: 'Atelier Poterie - Porterie',
    date: '2025-12-08',
    time: '15:00 - 16:30',
    duration: '1,5h',
    location: 'Salle de poterie - Porterie',
    audience: 'Tous les âges',
    capacity: 18,
    available: 8,
    price: 'Gratuit',
    description: 'Découvrez nos ateliers spéciaux organisés à la Porterie, un espace dédié à la créativité et à l\'apprentissage. Créez vos propres formes en argile et explorez les techniques de la poterie traditionnelle. Ces ateliers offrent une expérience immersive où vous pouvez explorer diverses formes d\'art tout en vous inspirant de l\'histoire et de la culture locale.'
  },
  3: {
    title: 'Soirées Jazz au Musée',
    date: '2025-12-09',
    time: '20:00 - 22:00',
    duration: '2h',
    location: 'Auditorium principal',
    audience: 'Tout public',
    capacity: 150,
    available: 67,
    price: 'Gratuit',
    description: 'Vivez des soirées inoubliables au rythme du jazz dans le cadre unique du Musée des Beaux-Arts et d\'Archéologie de Besançon. Chaque mois, nous accueillons des musiciens de talent pour des performances live qui créent une ambiance chaleureuse et conviviale. Venez profiter de cette expérience musicale exceptionnelle!'
  },
  4: {
    title: 'Concert de Noël',
    date: '2025-12-11',
    time: '18:00 - 19:30',
    duration: '1,5h',
    location: 'Grand auditorium',
    audience: 'Tout public',
    capacity: 200,
    available: 120,
    price: 'Gratuit',
    description: 'Célébrez Noël avec nous! Un concert festif mettant en vedette des artistes locaux. Une ambiance magique et chaleureuse pour toute la famille.'
  },
  5: {
    title: 'Exposition Temporaire',
    date: '2025-12-14',
    time: '10:00 - 18:00',
    duration: '8h',
    location: 'Galerie centrale',
    audience: 'Tout public',
    capacity: 500,
    available: 250,
    price: 'Gratuit',
    description: 'Découvrez notre exposition temporaire mettant en avant des œuvres exceptionnelles. Une visite enrichissante à travers l\'histoire de l\'art et de la culture.'
  },
  6: {
    title: 'Visite Guidée',
    date: '2025-12-15',
    time: '14:00 - 15:30',
    duration: '1,5h',
    location: 'Départ de l\'accueil',
    audience: 'Adultes (18+)',
    capacity: 25,
    available: 12,
    price: 'Gratuit',
    description: 'Participez à une visite guidée expert du musée. Nos guides chevronnés vous feront découvrir les collections principales et les anecdotes historiques fascinantes.'
  },
  7: {
    title: 'Atelier Dessin',
    date: '2025-12-17',
    time: '16:00 - 17:30',
    duration: '1,5h',
    location: 'Studio de dessin - Niveau 1',
    audience: 'Enfants (6-14 ans)',
    capacity: 18,
    available: 10,
    price: 'Gratuit',
    description: 'Laissez votre imagination s\'exprimer dans cet atelier de dessin! Apprenez les techniques de base du dessin en s\'inspirant des œuvres du musée.'
  },
  8: {
    title: 'Conférences - Art et Archéologie',
    date: '2025-12-22',
    time: '19:00 - 20:30',
    duration: '1,5h',
    location: 'Salle de conférence',
    audience: 'Tout public',
    capacity: 100,
    available: 42,
    price: 'Gratuit',
    description: 'Rejoignez-nous pour des conférences captivantes animées par des experts renommés dans le domaine de l\'art et de l\'archéologie. Plongez dans des discussions enrichissantes sur des sujets variés, allant des mouvements artistiques aux découvertes archéologiques récentes. Découvrez les secrets et les histoires fascinantes de notre patrimoine culturel!'
  }
};

function getEventIdFromURL() {
  const params = new URLSearchParams(window.location.search);
  return parseInt(params.get('event')) || 1;
}

function loadEventDetails() {
  const eventId = getEventIdFromURL();
  const event = eventDetails[eventId];

  if (!event) {
    console.error('Événement non trouvé');
    return;
  }

  document.getElementById('eventTitle').textContent = event.title;
  document.getElementById('eventDate').textContent = formatDate(event.date);
  document.getElementById('eventTime').textContent = event.time;
  document.getElementById('eventDuration').textContent = event.duration;
  document.getElementById('eventAudience').textContent = event.audience;
  document.getElementById('eventLocation').textContent = event.location;
  document.getElementById('eventCapacity').textContent = `${event.capacity} personnes`;
  document.getElementById('eventAvailable').textContent = `${event.available} / ${event.capacity}`;
  document.getElementById('eventPrice').textContent = event.price;
  document.getElementById('eventDescription').textContent = event.description;

  const statusElement = document.getElementById('reservationStatus');
  if (event.available > 0) {
    statusElement.innerHTML = '<span class="status-badge status-available">✓ Places disponibles</span>';
  } else {
    statusElement.innerHTML = '<span class="status-badge status-unavailable">✗ Complet</span>';
    const submitBtn = document.querySelector('.reservation-btn-submit');
    if (submitBtn) submitBtn.disabled = true;
  }

  const participantsInput = document.getElementById('participants');
  if (participantsInput) {
    participantsInput.max = Math.min(event.available, 15);
  }
}

function handleFormSubmit(e) {
  e.preventDefault();

  const firstName = document.getElementById('firstName').value;
  const lastName = document.getElementById('lastName').value;
  const email = document.getElementById('email').value;
  const phone = document.getElementById('phone').value;
  const participants = parseInt(document.getElementById('participants').value);
  const eventId = getEventIdFromURL();
  const event = eventDetails[eventId];

  if (participants > event.available) {
    alert(`Désolé, seulement ${event.available} place(s) disponible(s)`);
    return;
  }

  document.querySelector('.reservation-section').style.display = 'none';
  document.getElementById('confirmationSection').style.display = 'block';

  document.getElementById('confirmationEmail').textContent = email;
  document.getElementById('confirmationEventTitle').textContent = event.title;
  document.getElementById('confirmationEventDate').textContent = formatDate(event.date);
  document.getElementById('confirmationParticipants').textContent = participants;

  console.log('Réservation soumise:', {
    firstName,
    lastName,
    email,
    phone,
    participants,
    event: event.title,
    eventId
  });

  window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ==========================================
// DÉMARRAGE DE L'APPLICATION
// ==========================================

document.addEventListener('DOMContentLoaded', function() {
  // Vérifier si on vient d'une page d'événement spécifique
  const urlParams = new URLSearchParams(window.location.search);
  const eventId = urlParams.get('event');
  
  if (eventId) {
    // Mode événement spécifique: afficher directement l'événement
    hideAllSections();
    document.getElementById('legacyReservationSection').style.display = 'block';
    loadEventDetails();
    const legacyForm = document.getElementById('reservationForm');
    if (legacyForm) {
      legacyForm.addEventListener('submit', handleFormSubmit);
    }
  } else {
    // Mode normal: afficher le dashboard
    showDashboard();
    const legacyForm = document.getElementById('reservationForm');
    if (legacyForm) {
      legacyForm.addEventListener('submit', handleFormSubmit);
    }
  }
  
  // Préparer le système de tickets mais ne pas l'initialiser complètement
  window.ticketSystemInitialized = false;
});

// ==========================================
// EXPORTS POUR DÉBOGAGE
// ==========================================

window.ticketSystem = {
  evenements,
  reservationState,
  calculerAge,
  getTarifParAge,
  genererNumeroReservation,
  mettreAJourPlacesDisponibles
};
