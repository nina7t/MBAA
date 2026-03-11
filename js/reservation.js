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

function formatDate(dateStr) {
  const date = new Date(dateStr + 'T00:00:00');
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return date.toLocaleDateString('fr-FR', options).charAt(0).toUpperCase() + 
         date.toLocaleDateString('fr-FR', options).slice(1);
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
    document.querySelector('.reservation-btn-submit').disabled = true;
  }

  const participantsInput = document.getElementById('participants');
  participantsInput.max = Math.min(event.available, 15);
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

document.addEventListener('DOMContentLoaded', function() {
  loadEventDetails();
  document.getElementById('reservationForm').addEventListener('submit', handleFormSubmit);
});
