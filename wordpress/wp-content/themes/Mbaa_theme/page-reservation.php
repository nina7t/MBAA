<?php
/**
 * Template Name: Réservation
 * Template pour la page de réservation du MBAA
 * Remplace reservation.html
 *
 * @package Mbaa_theme
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// ── Données dynamiques depuis la BDD MBAA ──────────────────────────────────
global $wpdb;

// Événements futurs
$evenements = $wpdb->get_results(
    "SELECT * FROM {$wpdb->prefix}mbaa_evenement
     WHERE date_evenement >= CURDATE()
     ORDER BY date_evenement ASC",
    ARRAY_A
);

// Helper — URL assets du thème
$assets = get_template_directory_uri() . '/asset';

get_header();
?>

<?php wp_body_open(); ?>

  <header class="header header--evenement">
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
            <a class="header__nav-link" href="#" aria-label="Recherche">
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
        <p class="hero__eyebrow">Les ÉVÈNEMENTS — Musée des Beaux-Arts</p>
        <h1 class="hero__title">
          Les<br>
          Réservations<br>
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

  <main class="reservation-main">
    <!-- Main Reservation Dashboard -->
    <section class="reservation-dashboard" id="reservationDashboard">
      <div class="dashboard-container">
        <div class="dashboard-header">
          <h2 class="dashboard-title">Réservez votre expérience</h2>
          <p class="dashboard-subtitle">Découvrez les différents événements organisée au sein du MAT</p>
        </div>

        <div class="modules-grid">
          <!-- Module 1: Entrée Musée -->
          <div class="module-card" onclick="goToTicketPurchase()">
            <div class="module-header">
              <div class="module-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"></path>
                  <path d="M13 5v2"></path>
                  <path d="M13 17v2"></path>
                  <path d="M13 11v2"></path>
                </svg>
              </div>
              <h3 class="module-title">Entrée Musée</h3>
            </div>
            <div class="module-content">
              <p class="module-description">Accédez aux collections permanentes avec nos tarifs adaptés à votre âge</p>
              <ul class="module-features">
                <li>5 600 œuvres exposées</li>
                <li>Tarifs par âge (6-17, 18-25, 26-64, 65+)</li>
                <li>Gratuit pour les moins de 6 ans</li>
              </ul>
            </div>
            <div class="module-footer">
              <span class="module-price">À partir de 5€</span>
              <button class="module-btn">Réserver</button>
            </div>
          </div>

          <!-- Module 2: Événements & Ateliers -->
          <div class="module-card" onclick="goToEventReservation()">
            <div class="module-header">
              <div class="module-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                  <line x1="16" y1="2" x2="16" y2="6"></line>
                  <line x1="8" y1="2" x2="8" y2="6"></line>
                  <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
              </div>
              <h3 class="module-title">Événements & Ateliers</h3>
            </div>
            <div class="module-content">
              <p class="module-description">Participez à nos événements uniques, ateliers créatifs et visites thématiques</p>
              <ul class="module-features">
                <li>Expositions temporaires</li>
                <li>Ateliers interactifs</li>
                <li>Visites guidées</li>
                <li>Soirées spéciales</li>
              </ul>
            </div>
            <div class="module-footer">
              <span class="module-price">Gratuit à 15€</span>
              <button class="module-btn">Découvrir</button>
            </div>
          </div>

          <!-- Module 3: Groupes & Scolaires -->
          <div class="module-card" onclick="goToGuidedTour()">
            <div class="module-header">
              <div class="module-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                  <circle cx="9" cy="7" r="4"></circle>
                  <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                  <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
              </div>
              <h3 class="module-title">Groupes & Scolaires</h3>
            </div>
            <div class="module-content">
              <p class="module-description">Organisez votre visite en groupe avec nos formules adaptées</p>
              <ul class="module-features">
                <li>Visites guidées thématiques</li>
                <li>Ateliers pédagogiques</li>
                <li>Tarifs groupes</li>
                <li>Réservation obligatoire</li>
              </ul>
            </div>
            <div class="module-footer">
              <span class="module-price">Sur devis</span>
              <button class="module-btn">Contacter</button>
            </div>
          </div>
        </div>

        <!-- Quick Access to Popular Events -->
        <div class="popular-events">
          <h3 class="popular-title">Événements à ne pas manquer</h3>
          
          <!-- Events Filters -->
          <div class="events-filters">
            <div class="filters-header">
              <span class="filters-label">Filtrer par :</span>
              <button class="filter-reset" id="filterReset">Tout effacer</button>
            </div>

            <div class="filters-groups">
              <!-- Filtre catégorie -->
              <div class="filter-group">
                <span class="filter-group-title">Catégorie</span>
                <div class="filter-pills">
                  <button class="filter-pill active" data-filter="category" data-value="all">Tous</button>
                  <button class="filter-pill" data-filter="category" data-value="concert">Concerts</button>
                  <button class="filter-pill" data-filter="category" data-value="atelier">Ateliers</button>
                  <button class="filter-pill" data-filter="category" data-value="exposition">Expositions</button>
                  <button class="filter-pill" data-filter="category" data-value="visite">Visites guidées</button>
                  <button class="filter-pill" data-filter="category" data-value="conference">Conférences</button>
                </div>
              </div>

              <!-- Filtre prix -->
              <div class="filter-group">
                <span class="filter-group-title">Tarif</span>
                <div class="filter-pills">
                  <button class="filter-pill active" data-filter="price" data-value="all">Tous</button>
                  <button class="filter-pill" data-filter="price" data-value="free">Gratuit</button>
                  <button class="filter-pill" data-filter="price" data-value="paid">Payant</button>
                </div>
              </div>

              <!-- Filtre disponibilité -->
              <div class="filter-group">
                <span class="filter-group-title">Disponibilité</span>
                <div class="filter-pills">
                  <button class="filter-pill active" data-filter="availability" data-value="all">Tous</button>
                  <button class="filter-pill" data-filter="availability" data-value="available">Places dispo</button>
                  <button class="filter-pill" data-filter="availability" data-value="last">Dernières places</button>
                </div>
              </div>
            </div>

            <!-- Résultat du filtre -->
            <div class="filters-result">
              <span id="filtersCount"><?php echo count( $evenements ); ?> événements</span>
            </div>
          </div>
          
          <!-- Featured Exhibition -->
          <div class="featured-exhibition">
            <div class="featured-content">
              <h4 class="featured-title">Nuit rom au musée</h4>
              <p class="featured-description">
                À l'occasion de l'ouverture de l'exposition « Ceija Stojka. Garder les yeux ouverts », 
                le musée des Beaux-arts et d'Archéologie en partenariat avec les insolites de La Rodia 
                ont programmé une Nuit rom au musée, deux concerts sont prévus à partir de 20h: ZINDA + Dj CLICK...
              </p>
              <div class="featured-details">
                <span class="featured-date">Exposition ouverte</span>
                <span class="featured-price">Concerts gratuits</span>
                <button class="featured-btn" onclick="goToEventDetail(4)">En savoir plus</button>
              </div>
            </div>
          </div>
          
          <div class="events-carousel">
            <?php if ( ! empty( $evenements ) ) : ?>
              <?php foreach ( $evenements as $index => $evenement ) : ?>
                <div class="event-slide" onclick="goToEventDetail(<?php echo esc_attr( $evenement['id_evenement'] ); ?>)">
                  <div class="event-image">
                    <img src="<?php 
                      if ( ! empty( $evenement['image_url'] ) ) {
                        echo esc_url( $evenement['image_url'] );
                      } else {
                        echo esc_url( $assets . '/Img/Evenement/evenement-visite-musee.jpg' );
                      }
                    ?>" 
                         alt="<?php echo esc_attr( $evenement['titre'] ); ?>" 
                         loading="lazy"
                         onerror="this.parentElement.classList.add('event-image--fallback')">
                    <span class="event-badge"><?php echo $index === 0 ? 'Populaire' : ( $index === 1 ? 'Nouveau' : 'Exclusif' ); ?></span>
                  </div>
                  <div class="event-info">
                    <h4 class="event-name"><?php echo esc_html( $evenement['titre'] ); ?></h4>
                    <p class="event-date"><?php 
                      $date = date_create( $evenement['date_evenement'] );
                      echo date_format( $date, 'd F Y' );
                    ?></p>
                    <span class="event-price"><?php 
                      if ( $evenement['est_gratuit'] ) {
                        echo 'Gratuit';
                      } else {
                        echo esc_html( $evenement['prix'] ) . '€';
                      }
                    ?></span>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else : ?>
              <!-- Fallback statique si la BDD est vide -->
              <div class="event-slide" onclick="goToEventDetail(1)">
                <div class="event-image">
                  <img src="<?php echo esc_url( $assets . '/Img/Evenement/evenement-visite-musee.jpg' ); ?>" 
                       alt="Ateliers Interactifs - Activités créatives au musée" 
                       loading="lazy"
                       onerror="this.parentElement.classList.add('event-image--fallback')">
                  <span class="event-badge">Populaire</span>
                </div>
                <div class="event-info">
                  <h4 class="event-name">Ateliers Interactifs</h4>
                  <p class="event-date">3 Décembre 2025</p>
                  <span class="event-price">Gratuit</span>
                </div>
              </div>
              
              <div class="event-slide" onclick="goToEventDetail(2)">
                <div class="event-image">
                  <img src="<?php echo esc_url( $assets . '/Img/Evenement/evenement-soiree-1.jpg' ); ?>" 
                       alt="Soirées Jazz au Musée - Concerts jazz en nocturne" 
                       loading="lazy"
                       onerror="this.parentElement.classList.add('event-image--fallback')">
                  <span class="event-badge">Nouveau</span>
                </div>
                <div class="event-info">
                  <h4 class="event-name">Soirées Jazz au Musée</h4>
                  <p class="event-date">9 Décembre 2025</p>
                  <span class="event-price">Gratuit</span>
                </div>
              </div>
              
              <div class="event-slide" onclick="goToEventDetail(3)">
                <div class="event-image">
                  <img src="<?php echo esc_url( $assets . '/Img/Evenement/evenement-concert.jpg' ); ?>" 
                       alt="Concert de Noël - Musique festive au musée" 
                       loading="lazy"
                       onerror="this.parentElement.classList.add('event-image--fallback')">
                  <span class="event-badge">Exclusif</span>
                </div>
                <div class="event-info">
                  <h4 class="event-name">Concert de Noël</h4>
                  <p class="event-date">11 Décembre 2025</p>
                  <span class="event-price">Gratuit</span>
                </div>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </section>

    <!-- Section Ticket Purchase -->
    <section class="ticket-purchase-section" id="ticketPurchaseSection" style="display: none;">
      <div class="ticket-container">
        <div class="ticket-header">
          <button class="back-to-dashboard" onclick="showDashboard()">← Retour aux modules</button>
          <h2 class="ticket-title">Acheter un ticket</h2>
          <p class="ticket-subtitle">Réservez votre entrée ou un événement spécifique</p>
        </div>

        <div class="ticket-content">
          <!-- Purchase Form -->
          <div class="ticket-form-section">
            <form class="ticket-form" id="ticketForm">
              <?php wp_nonce_field( 'mbaa_reservation', 'mbaa_reservation_nonce' ); ?>
              <input type="hidden" name="action" value="mbaa_process_reservation" />

              <!-- Step 1: Type de ticket (prioritaire) -->
              <div class="form-group">
                <label for="ticketType">Type de billet *</label>
                <select class="form-select" id="ticketType" name="ticketType" required>
                  <option value="">Choisissez un type</option>
                  <option value="musee">Entrée Musée MAT</option>
                  <option value="event">Événement spécifique</option>
                </select>
                <span class="form-help">Sélectionnez d'abord le type de billet</span>
              </div>

              <!-- Step 2: Événement (si événement sélectionné) -->
              <div class="form-group" id="eventSelectionGroup" style="display: none;">
                <label for="eventId">Événement *</label>
                <select class="form-select" id="eventId" name="eventId">
                  <option value="">Choisissez un événement</option>
                  <?php if ( ! empty( $evenements ) ) : ?>
                    <?php foreach ( $evenements as $evenement ) : ?>
                      <option value="<?php echo esc_attr( $evenement['id_evenement'] ); ?>">
                        <?php echo esc_html( $evenement['titre'] ); ?> - 
                        <?php 
                          $date = date_create( $evenement['date_evenement'] );
                          echo date_format( $date, 'd F Y' );
                        ?>
                      </option>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </select>
                <span class="form-help">Sélectionnez l'événement souhaité</span>
              </div>

              <!-- Step 3: Informations personnelles -->
              <div class="form-row">
                <div class="form-group">
                  <label for="firstName">Prénom *</label>
                  <input type="text" class="form-input" id="firstName" name="firstName" required>
                </div>
                <div class="form-group">
                  <label for="lastName">Nom *</label>
                  <input type="text" class="form-input" id="lastName" name="lastName" required>
                </div>
              </div>

              <!-- Step 4: Date de naissance (seulement pour entrée musée) -->
              <div class="form-group" id="birthDateGroup">
                <label for="birthDate">Date de naissance *</label>
                <input type="date" class="form-input" id="birthDate" name="birthDate" required>
                <span class="form-help">Requis pour le calcul du tarif adapté</span>
              </div>

              <!-- Step 5: Quantité -->
              <div class="form-group">
                <label for="quantity">Quantité *</label>
                <select class="form-select" id="quantity" name="quantity" required>
                  <option value="1">1 billet</option>
                  <option value="2">2 billets</option>
                  <option value="3">3 billets</option>
                  <option value="4">4 billets</option>
                  <option value="5">5 billets</option>
                  <option value="6">6 billets</option>
                  <option value="7">7 billets</option>
                  <option value="8">8 billets</option>
                  <option value="9">9 billets</option>
                  <option value="10">10 billets</option>
                </select>
              </div>

              <!-- Pricing Display -->
              <div class="price-display" id="priceDisplay" style="display: none;">
                <div class="price-info">
                  <span class="price-label">Âge détecté:</span>
                  <span class="price-value" id="detectedAge">-</span>
                </div>
                <div class="price-info">
                  <span class="price-label">Tarif appliqué:</span>
                  <span class="price-value" id="appliedTariff">-</span>
                </div>
                <div class="price-total">
                  <span class="price-label">Total:</span>
                  <span class="price-amount" id="totalPrice">-</span>
                </div>
              </div>

              <button type="submit" class="ticket-btn-preview">Voir le récapitulatif</button>
            </form>
          </div>

          <!-- Events List -->
          <div class="events-list-section" id="eventsListSection" style="display: none;">
            <h3 class="events-list-title">Événements disponibles</h3>
            <div class="events-grid" id="eventsGrid">
              <!-- Events will be dynamically inserted here -->
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Summary Section -->
    <section class="summary-section" id="summarySection" style="display: none;">
      <div class="summary-container">
        <h2 class="summary-title">Récapitulatif de votre réservation</h2>
        
        <div class="summary-card">
          <div class="summary-info">
            <div class="summary-item">
              <span class="summary-label">Nom du visiteur:</span>
              <span class="summary-value" id="summaryName">-</span>
            </div>
            <div class="summary-item">
              <span class="summary-label">Type de ticket:</span>
              <span class="summary-value" id="summaryTicketType">-</span>
            </div>
            <div class="summary-item" id="summaryEventItem" style="display: none;">
              <span class="summary-label">Événement:</span>
              <span class="summary-value" id="summaryEvent">-</span>
            </div>
            <div class="summary-item">
              <span class="summary-label">Âge détecté:</span>
              <span class="summary-value" id="summaryAge">-</span>
            </div>
            <div class="summary-item">
              <span class="summary-label">Tarif appliqué:</span>
              <span class="summary-value" id="summaryTariff">-</span>
            </div>
            <div class="summary-item">
              <span class="summary-label">Quantité:</span>
              <span class="summary-value" id="summaryQuantity">-</span>
            </div>
            <div class="summary-total">
              <span class="summary-label">Total à payer:</span>
              <span class="summary-total-amount" id="summaryTotal">0€</span>
            </div>
          </div>
          
          <div class="summary-actions">
            <button type="button" class="summary-btn-back" id="backBtn">Modifier</button>
            <button type="button" class="summary-btn-confirm" id="confirmBtn">Confirmer la réservation</button>
          </div>
        </div>
      </div>
    </section>

    <!-- Confirmation Section -->
    <section class="confirmation-section" id="ticketConfirmationSection" style="display: none;">
      <div class="confirmation-container">
        <div class="confirmation-card">
          <div class="confirmation-icon">✓</div>
          <h2 class="confirmation-title">Réservation confirmée!</h2>
          <p class="confirmation-message">Votre réservation a été enregistrée avec succès.</p>
          
          <div class="confirmation-details">
            <div class="confirmation-item">
              <span class="confirmation-label">Numéro de réservation:</span>
              <span class="confirmation-value" id="reservationNumber">-</span>
            </div>
            <div class="confirmation-item">
              <span class="confirmation-label">Nom:</span>
              <span class="confirmation-value" id="confirmationName">-</span>
            </div>
            <div class="confirmation-item">
              <span class="confirmation-label">Type de ticket:</span>
              <span class="confirmation-value" id="confirmationTicketType">-</span>
            </div>
            <div class="confirmation-item" id="confirmationEventItem" style="display: none;">
              <span class="confirmation-label">Événement:</span>
              <span class="confirmation-value" id="confirmationEvent">-</span>
            </div>
            <div class="confirmation-item">
              <span class="confirmation-label">Total payé:</span>
              <span class="confirmation-value" id="confirmationTotal">-</span>
            </div>
          </div>
          
          <div class="confirmation-actions">
            <a href="<?php echo esc_url( home_url('/') ); ?>" class="confirmation-btn-home">Retour à l'accueil</a>
            <button type="button" class="confirmation-btn-new" id="newReservationBtn">Nouvelle réservation</button>
          </div>
        </div>
      </div>
    </section>

    <!-- Legacy reservation section (kept for compatibility) -->
    <section class="reservation-section" id="legacyReservationSection">
      <div class="reservation-container">
        
        <div class="reservation-header">
          <button class="back-to-dashboard" onclick="showDashboard()">← Retour aux modules</button>
          <a href="<?php echo esc_url( home_url('/evenements/') ); ?>" class="reservation-back-btn">← Retour aux événements</a>
        </div>

        <div class="reservation-content">
          
          <div class="reservation-event-details">
            <h2 class="reservation-event-title" id="eventTitle">Titre de l'événement</h2>
            
            <div class="reservation-status" id="reservationStatus">
              <span class="status-badge status-available">✓ Places disponibles</span>
            </div>

            <div class="reservation-infos-grid">
              <div class="reservation-info-item">
                <span class="reservation-info-label">DATE</span>
                <span class="reservation-info-value" id="eventDate">--/--/----</span>
              </div>

              <div class="reservation-info-item">
                <span class="reservation-info-label">HORAIRES</span>
                <span class="reservation-info-value" id="eventTime">--:-- - --:--</span>
              </div>

              <div class="reservation-info-item">
                <span class="reservation-info-label">DURÉE</span>
                <span class="reservation-info-value" id="eventDuration">-- h</span>
              </div>

              <div class="reservation-info-item">
                <span class="reservation-info-label">PUBLIC</span>
                <span class="reservation-info-value" id="eventAudience">Tous publics</span>
              </div>

              <div class="reservation-info-item">
                <span class="reservation-info-label">LIEU</span>
                <span class="reservation-info-value" id="eventLocation">Salle principale</span>
              </div>

              <div class="reservation-info-item">
                <span class="reservation-info-label">CAPACITÉ</span>
                <span class="reservation-info-value" id="eventCapacity">-- personnes</span>
              </div>

              <div class="reservation-info-item">
                <span class="reservation-info-label">PLACES RESTANTES</span>
                <span class="reservation-info-value" id="eventAvailable">-- / --</span>
              </div>

              <div class="reservation-info-item">
                <span class="reservation-info-label">TARIF</span>
                <span class="reservation-info-value" id="eventPrice">Gratuit</span>
              </div>
            </div>

            <div class="reservation-description">
              <h3>Description</h3>
              <p id="eventDescription">Description de l'événement...</p>
            </div>
          </div>

          <div class="reservation-form-section">
            <h3 class="reservation-form-title">Finaliser votre réservation</h3>
            
            <form class="reservation-form" id="reservationForm">
              <?php wp_nonce_field( 'mbaa_event_reservation', 'mbaa_event_reservation_nonce' ); ?>
              <input type="hidden" name="action" value="mbaa_process_event_reservation" />
              
              <div class="form-group">
                <label for="firstName" class="form-label">Prénom *</label>
                <input type="text" id="firstName" name="firstName" class="form-input" required>
              </div>

              <div class="form-group">
                <label for="lastName" class="form-label">Nom *</label>
                <input type="text" id="lastName" name="lastName" class="form-input" required>
              </div>

              <div class="form-group">
                <label for="email" class="form-label">Email *</label>
                <input type="email" id="email" name="email" class="form-input" required>
              </div>

              <div class="form-group">
                <label for="phone" class="form-label">Téléphone *</label>
                <input type="tel" id="phone" name="phone" class="form-input" required>
              </div>

              <div class="form-group">
                <label for="participants" class="form-label">Nombre de participants *</label>
                <input type="number" id="participants" name="participants" class="form-input" min="1" max="15" value="1" required>
              </div>

              <div class="form-group">
                <label class="form-checkbox">
                  <input type="checkbox" name="newsletter" id="newsletter">
                  <span>Je souhaite recevoir les actualités du musée</span>
                </label>
              </div>

              <div class="form-group">
                <label class="form-checkbox">
                  <input type="checkbox" name="terms" id="terms" required>
                  <span>J'accepte les conditions générales d'utilisation *</span>
                </label>
              </div>

              <button type="submit" class="reservation-btn-submit">CONFIRMER LA RÉSERVATION</button>
            </form>

            <p class="reservation-form-note">Les champs marqués * sont obligatoires</p>
          </div>
        </div>
      </div>
    </section>

    <section class="reservation-confirmation" id="confirmationSection" style="display: none;">
      <div class="confirmation-card">
        <div class="confirmation-icon">✓</div>
        <h2>Réservation confirmée!</h2>
        <p>Un email de confirmation a été envoyé à <strong id="confirmationEmail"></strong></p>
        <div class="confirmation-details">
          <p><strong>Événement:</strong> <span id="confirmationEventTitle"></span></p>
          <p><strong>Date:</strong> <span id="confirmationEventDate"></span></p>
          <p><strong>Participants:</strong> <span id="confirmationParticipants"></span></p>
        </div>
        <a href="<?php echo esc_url( home_url('/evenements/') ); ?>" class="reservation-btn-back">Retour aux événements</a>
      </div>
    </section>
  </main>

  <!-- Footer -->
  <footer class="footer">
    <div class="footer__contain-form">
    <div class="footer__logo-container">
      <a class="footer__logo-link" href="<?php echo esc_url( home_url('/') ); ?>">
        <img class="footer__logo-img" src="<?php echo esc_url( $assets . '/Img/logo/logo-mat-small.png' ); ?>" alt="Logo MBAA" />
        <h2 class="footer__title">Suivez-nous pour recevoir la newsletter</h2>
      </a>
      <?php
      // Formulaire newsletter — utilise wp_nonce pour la sécurité
      $newsletter_action = esc_url( admin_url('admin-post.php') );
      ?>
      <form class="footer__form" method="post" action="<?php echo $newsletter_action; ?>">
        <?php wp_nonce_field( 'mbaa_newsletter', 'mbaa_newsletter_nonce' ); ?>
        <input type="hidden" name="action" value="mbaa_newsletter_subscribe" />
        <input class="footer__input" type="email" name="email" placeholder="Entrez votre adresse e-mail" required />
        <button class="footer__button" type="submit">S'abonner</button>
      </form>
    </div>

    <section class="footer__nav">
      <ul class="footer__nav-list">
        <li class="footer__nav-item">
          <h4 class="footer__nav-title">Le musée</h4>
        </li>
        <li class="footer__nav-item">
          <a class="footer__nav-link" href="<?php echo esc_url( home_url('/le-musee/') ); ?>">Présentation & histoire</a>
        </li>
        <li class="footer__nav-item">
          <a class="footer__nav-link" href="<?php echo esc_url( home_url('/collections/') ); ?>">Collections</a>
        </li>
      </ul>

      <ul class="footer__nav-list">
        <li class="footer__nav-item">
          <h4 class="footer__nav-title">Programmation</h4>
        </li>
        <li class="footer__nav-item">
          <a class="footer__nav-link" href="<?php echo esc_url( home_url('/evenements/') ); ?>">Événements</a>
        </li>
        <li class="footer__nav-item">
          <a class="footer__nav-link" href="<?php echo esc_url( home_url('/collections/') ); ?>">Collections</a>
        </li>
      </ul>

      <ul class="footer__nav-list">
        <li class="footer__nav-item">
          <h4 class="footer__nav-title">Contact</h4>
        </li>
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
          <img class="footer__social-icon" src="<?php echo esc_url( $assets . '/Img/svg/facebook.svg' ); ?>" alt="Facebook" />
        </a>
        <a class="footer__social-link" href="#" aria-label="Instagram">
          <img class="footer__social-icon" src="<?php echo esc_url( $assets . '/Img/svg/insta.svg' ); ?>" alt="Instagram" />
        </a>
        <a class="footer__social-link" href="#" aria-label="LinkedIn">
          <img class="footer__social-icon" src="<?php echo esc_url( $assets . '/Img/svg/linkdin.svg' ); ?>" alt="Twitter" />
        </a>
        <a class="footer__social-link" href="#" aria-label="Tiktok">
          <img class="footer__social-icon" src="<?php echo esc_url( $assets . '/Img/svg/tiktok.svg' ); ?>" alt="YouTube" />
        </a>
      </section>
    </section>

    <section class="footer__links">
      <a class="footer__link" href="#">Mentions légales</a>
      <a class="footer__link" href="#">Politique de confidentialité</a>
    </section>
  </footer>

  <script src="<?php echo esc_url( get_template_directory_uri() . '/dist/js/reservation.js' ); ?>"></script>
  <script src="<?php echo esc_url( get_template_directory_uri() . '/dist/js/menu.js' ); ?>"></script>

<?php get_footer(); ?>
