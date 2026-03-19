<?php
/**
 * Template Name: Espace Visiteur
 * Template pour l'espace personnel du visiteur du MBAA
 * Remplace visiteur.html
 *
 * @package Mbaa_theme
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// ── Vérification de connexion ───────────────────────────────────────
if ( ! is_user_logged_in() ) {
    // Rediriger vers la page de connexion si non connecté
    wp_redirect( wp_login_url( get_permalink() ) );
    exit;
}

// ── Données dynamiques depuis la BDD MBAA ──────────────────────────────────
global $wpdb;
$current_user = wp_get_current_user();

// Réservations de l'utilisateur connecté
$reservations = $wpdb->get_results( $wpdb->prepare(
    "SELECT r.*, e.titre AS evenement_titre, e.date_evenement, e.prix,
            CASE 
                WHEN r.type_reservation = 'musee' THEN 'Entrée Musée'
                WHEN r.type_reservation = 'event' THEN CONCAT('Événement: ', e.titre)
                ELSE r.type_reservation
            END AS type_label
     FROM {$wpdb->prefix}mbaa_reservation r
     LEFT JOIN {$wpdb->prefix}mbaa_evenement e ON r.id_evenement = e.id_evenement
     WHERE r.id_utilisateur = %d
     ORDER BY r.date_reservation DESC",
    $current_user->ID
), ARRAY_A );

// Helper — URL assets du thème
$assets = get_template_directory_uri() . '/asset';

get_header();
?>

<?php wp_body_open(); ?>

    <!-- Header MBAA avec menu burger -->
    <header class="header header--visiteur">
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
                        <a class="header__nav-link" href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>" aria-label="Déconnexion">
                            <svg class="header__nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                <polyline points="16 17 21 12 16 7"></polyline>
                                <line x1="21" y1="12" x2="9" y2="12"></line>
                            </svg>
                        </a>
                    </li>
                    <li class="header__nav-item">
                        <a class="header__nav-link" href="<?php echo esc_url( home_url('/reservation/') ); ?>" aria-label="Billetterie">
                            <svg class="header__nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"></path>
                                <path d="M13 5v2"></path>
                                <path d="M13 17v2"></path>
                                <path d="M13 11v2"></path>
                            </svg>
                        </a>
                    </li>
                    <li class="header__nav-item">
                        <a class="header__nav-link" href="#" aria-label="Recherche">
                            <svg class="header__nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"></circle>
                                <path d="m21 21-4.35-4.35"></path>
                            </svg>
                        </a>
                    </li>
                    <li class="header__nav-item">
                        <a class="header__nav-link-fr" href="#" aria-label="Changer de langue">FR</a>
                    </li>
                    <li class="header__nav-item">
                        <a class="header__nav-link-fr" href="#" aria-label="Changer de langue">EN</a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="visiteur-container">
        <!-- Sidebar -->
        <aside class="visiteur-sidebar">
            <div class="sidebar-user">
                <div class="user-avatar">👤</div>
                <div class="user-info">
                    <p class="user-name" id="userName"><?php echo esc_html( $current_user->display_name ); ?></p>
                    <p class="user-email" id="userEmail"><?php echo esc_html( $current_user->user_email ); ?></p>
                </div>
            </div>

            <nav class="sidebar-nav">
                <ul class="sidebar-menu">
                    <li><a href="#" class="sidebar-link active" data-section="tickets">🎟️ Mes tickets</a></li>
                    <li><a href="#" class="sidebar-link" data-section="events">📅 Événements visités</a></li>
                    <li><a href="#" class="sidebar-link" data-section="horaires">⏰ Horaires & Infos</a></li>
                    <li><a href="#" class="sidebar-link" data-section="newsletter">📧 Newsletter</a></li>
                </ul>
            </nav>

            <a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>" class="sidebar-logout">Déconnexion</a>
        </aside>

        <!-- Main Content -->
        <main class="visiteur-main">
            <!-- Header -->
            <header class="visiteur-header">
                <h1>Mon Espace Personnel</h1>
                <p class="welcome-text">Bienvenue au Musée des Beaux-Arts et d'Archéologie</p>
            </header>

            <!-- Tickets Section -->
            <section class="content-section" id="tickets-section">
                <div class="section-header">
                    <h2>🎟️ Mes Tickets</h2>
                </div>
                
                <div class="tickets-grid">
                    <?php if ( ! empty( $reservations ) ) : ?>
                        <?php foreach ( $reservations as $reservation ) : ?>
                            <div class="ticket-card">
                                <div class="ticket-header">
                                    <span class="ticket-type"><?php echo esc_html( $reservation['type_label'] ); ?></span>
                                    <span class="ticket-status <?php 
                                        $date_evenement = ! empty( $reservation['date_evenement'] ) ? $reservation['date_evenement'] : $reservation['date_reservation'];
                                        echo ( strtotime( $date_evenement ) >= strtotime( 'today' ) ) ? 'valid' : 'expired'; 
                                    ?>">
                                        <?php echo ( strtotime( $date_evenement ) >= strtotime( 'today' ) ) ? 'Valide' : 'Utilisé'; ?>
                                    </span>
                                </div>
                                <div class="ticket-details">
                                    <p><strong>Date :</strong> <?php 
                                        if ( ! empty( $reservation['date_evenement'] ) ) {
                                            echo date( 'd F Y', strtotime( $reservation['date_evenement'] ) );
                                        } else {
                                            echo date( 'd F Y', strtotime( $reservation['date_reservation'] ) );
                                        }
                                    ?></p>
                                    <p><strong>Heure :</strong> <?php 
                                        if ( ! empty( $reservation['heure_reservation'] ) ) {
                                            echo esc_html( $reservation['heure_reservation'] );
                                        } else {
                                            echo '14:00';
                                        }
                                    ?></p>
                                    <p><strong>Tarif :</strong> <?php 
                                        if ( ! empty( $reservation['prix_total'] ) ) {
                                            echo esc_html( $reservation['prix_total'] ) . '€';
                                        } elseif ( ! empty( $reservation['prix'] ) ) {
                                            echo esc_html( $reservation['prix'] ) . '€';
                                        } else {
                                            echo '12€';
                                        }
                                    ?></p>
                                    <p><strong>N° Ticket :</strong> #MBAA-<?php echo date( 'Y' ); ?>-<?php echo str_pad( $reservation['id_reservation'], 3, '0', STR_PAD_LEFT ); ?></p>
                                </div>
                                <button class="ticket-btn">Télécharger PDF</button>
                            </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <!-- Fallback statique si aucune réservation -->
                        <div class="ticket-card">
                            <div class="ticket-header">
                                <span class="ticket-type">Entrée Générale</span>
                                <span class="ticket-status valid">Valide</span>
                            </div>
                            <div class="ticket-details">
                                <p><strong>Date :</strong> 24 décembre 2025</p>
                                <p><strong>Heure :</strong> 14:00</p>
                                <p><strong>Tarif :</strong> 12€</p>
                                <p><strong>N° Ticket :</strong> #MBAA-2024-001</p>
                            </div>
                            <button class="ticket-btn">Télécharger PDF</button>
                        </div>

                        <div class="ticket-card">
                            <div class="ticket-header">
                                <span class="ticket-type">Visite Guidée</span>
                                <span class="ticket-status expired">Utilisé</span>
                            </div>
                            <div class="ticket-details">
                                <p><strong>Date :</strong> 10 décembre 2025</p>
                                <p><strong>Heure :</strong> 15:30</p>
                                <p><strong>Guide :</strong> Marie Leclerc</p>
                                <p><strong>N° Ticket :</strong> #MBAA-2024-002</p>
                            </div>
                            <button class="ticket-btn">Revoir le reçu</button>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="section-actions">
                    <a href="<?php echo esc_url( home_url('/reservation/') ); ?>" class="btn-primary">Réserver un nouveau ticket</a>
                </div>
            </section>

            <!-- Events Section -->
            <section class="content-section" id="events-section" style="display: none;">
                <div class="section-header">
                    <h2>📅 Événements Visités</h2>
                </div>

                <div class="events-list">
                    <?php if ( ! empty( $reservations ) ) : ?>
                        <?php foreach ( $reservations as $reservation ) : ?>
                            <?php if ( $reservation['type_reservation'] === 'event' && ! empty( $reservation['evenement_titre'] ) ) : ?>
                                <div class="event-item">
                                    <div class="event-image">
                                        <img src="<?php echo esc_url( $assets . '/Img/visite-groupe.jpg' ); ?>" alt="<?php echo esc_attr( $reservation['evenement_titre'] ); ?>">
                                    </div>
                                    <div class="event-info">
                                        <h3><?php echo esc_html( $reservation['evenement_titre'] ); ?></h3>
                                        <p class="event-date">📅 <?php 
                                            if ( ! empty( $reservation['date_evenement'] ) ) {
                                                echo date( 'd F Y', strtotime( $reservation['date_evenement'] ) );
                                            } else {
                                                echo date( 'd F Y', strtotime( $reservation['date_reservation'] ) );
                                            }
                                        ?></p>
                                        <p class="event-desc"><?php echo esc_html( $reservation['evenement_titre'] ); ?> - Une expérience mémorable au musée.</p>
                                        <div class="event-rating">
                                            <span>Votre avis : </span>
                                            <div class="stars">
                                                <span class="star filled">★</span>
                                                <span class="star filled">★</span>
                                                <span class="star filled">★</span>
                                                <span class="star filled">★</span>
                                                <span class="star">★</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <!-- Fallback statique si aucun événement -->
                    <div class="event-item">
                        <div class="event-image">
                            <img src="<?php echo esc_url( $assets . '/Img/visite-groupe.jpg' ); ?>" alt="Exposition : Les Impressionnistes">
                        </div>
                        <div class="event-info">
                            <h3>Exposition : Les Impressionnistes</h3>
                            <p class="event-date">📅 15 novembre 2025</p>
                            <p class="event-desc">Découvrez les chefs-d'œuvre de Monet, Renoir et Sisley à travers une sélection exceptionnelle de nos collections.</p>
                            <div class="event-rating">
                                <span>Votre avis : </span>
                                <div class="stars">
                                    <span class="star filled">★</span>
                                    <span class="star filled">★</span>
                                    <span class="star filled">★</span>
                                    <span class="star filled">★</span>
                                    <span class="star">★</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="event-item">
                        <div class="event-image">
                            <img src="<?php echo esc_url( $assets . '/Img/visite-groupe.jpg' ); ?>" alt="Visite guidée : Trésors de l'Égypte Antique">
                        </div>
                        <div class="event-info">
                            <h3>Visite guidée : Trésors de l'Égypte Antique</h3>
                            <p class="event-date">📅 8 novembre 2025</p>
                            <p class="event-desc">Explorez les mystères de la civilisation égyptienne avec notre expert égyptologue.</p>
                            <div class="event-rating">
                                <span>Votre avis : </span>
                                <div class="stars">
                                    <span class="star filled">★</span>
                                    <span class="star filled">★</span>
                                    <span class="star filled">★</span>
                                    <span class="star filled">★</span>
                                    <span class="star filled">★</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Hours Section -->
            <section class="content-section" id="horaires-section" style="display: none;">
                <div class="section-header">
                    <h2>⏰ Horaires & Informations</h2>
                </div>

                <div class="info-grid">
                    <div class="info-card">
                        <h3>Horaires d'ouverture</h3>
                        <ul class="hours-list">
                            <li><span>Lundi :</span> Fermé</li>
                            <li><span>Mardi - Dimanche :</span> 10:00 - 18:00</li>
                            <li><span>Jeudi :</span> 10:00 - 21:00 (nocturne)</li>
                            <li><span>Jours fériés :</span> 14:00 - 18:00</li>
                        </ul>
                    </div>

                    <div class="info-card">
                        <h3>Tarifs</h3>
                        <ul class="prices-list">
                            <li><span>Entrée générale :</span> 12€</li>
                            <li><span>Tarif réduit :</span> 7€</li>
                            <li><span>Gratuit :</span> Moins de 18 ans</li>
                            <li><span>Visite guidée :</span> +5€</li>
                        </ul>
                    </div>

                    <div class="info-card">
                        <h3>Contact</h3>
                        <ul class="contact-list">
                            <li><strong>Adresse :</strong> 1 rue de la République, 25000 Besançon</li>
                            <li><strong>Téléphone :</strong> 03 81 87 80 49</li>
                            <li><strong>Email :</strong> contact@mbaa-besancon.fr</li>
                            <li><strong>Site :</strong> www.mbaa-besancon.fr</li>
                        </ul>
                    </div>

                    <div class="info-card">
                        <h3>Services</h3>
                        <ul class="services-list">
                            <li>✓ Boutique du musée</li>
                            <li>✓ Cafétéria</li>
                            <li>✓ Accessibilité PMR</li>
                            <li>✓ Parking à proximité</li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Newsletter Section -->
            <section class="content-section" id="newsletter-section" style="display: none;">
                <div class="section-header">
                    <h2>📧 Newsletter</h2>
                </div>

                <div class="newsletter-container">
                    <div class="newsletter-card">
                        <h3>Restez informé</h3>
                        <p>Recevez nos dernières actualités, expositions et événements directement dans votre boîte mail.</p>

                        <form class="newsletter-form" id="newsletterForm" method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
                            <?php wp_nonce_field( 'mbaa_newsletter', 'mbaa_newsletter_nonce' ); ?>
                            <input type="hidden" name="action" value="mbaa_newsletter_subscribe" />
                            
                            <div class="form-group">
                                <label for="newsletter-email">Adresse email</label>
                                <input 
                                    type="email" 
                                    id="newsletter-email" 
                                    name="email"
                                    value="<?php echo esc_attr( $current_user->user_email ); ?>"
                                    placeholder="votre@email.com"
                                    required
                                >
                            </div>

                            <div class="newsletter-options">
                                <label class="checkbox-label">
                                    <input type="checkbox" name="category-expos" checked>
                                    <span>Expositions et événements</span>
                                </label>
                                <label class="checkbox-label">
                                    <input type="checkbox" name="category-news" checked>
                                    <span>Actualités du musée</span>
                                </label>
                                <label class="checkbox-label">
                                    <input type="checkbox" name="category-promos">
                                    <span>Offres spéciales et promotions</span>
                                </label>
                            </div>

                            <div class="form-agree">
                                <input type="checkbox" id="agree" name="agree" required>
                                <label for="agree">J'accepte de recevoir des communications du musée</label>
                            </div>

                            <button type="submit" class="btn-primary">S'inscrire à la newsletter</button>
                            <div class="newsletter-message" id="newsletterMessage"></div>
                        </form>
                    </div>

                    <div class="newsletter-benefits">
                        <h3>Avantages de l'inscription</h3>
                        <ul>
                            <li>🎟️ Offres exclusives sur les tickets</li>
                            <li>📢 Avant-première des expositions</li>
                            <li>🎁 Réductions partenaires</li>
                            <li>📚 Actualités et coulisses du musée</li>
                        </ul>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <script>
        // Navigation between sections
        document.querySelectorAll('.sidebar-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                const section = this.getAttribute('data-section');
                
                // Hide all sections
                document.querySelectorAll('.content-section').forEach(s => {
                    s.style.display = 'none';
                });
                
                // Show selected section
                document.getElementById(section + '-section').style.display = 'block';
                
                // Update active link
                document.querySelectorAll('.sidebar-link').forEach(l => {
                    l.classList.remove('active');
                });
                this.classList.add('active');
            });
        });

        // Newsletter form
        document.getElementById('newsletterForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = document.getElementById('newsletter-email').value;
            const message = document.getElementById('newsletterMessage');
            
            if (email) {
                // Simulate API call
                message.textContent = '✓ Inscription réussie! Vérifiez votre email.';
                message.style.color = '#4CAF50';
                
                setTimeout(() => {
                    this.reset();
                    message.textContent = '';
                }, 3000);
            }
        });

        // Star rating interaction
        document.querySelectorAll('.stars').forEach(starsContainer => {
            const stars = starsContainer.querySelectorAll('.star');
            
            stars.forEach((star, index) => {
                star.addEventListener('click', function() {
                    stars.forEach((s, i) => {
                        if (i <= index) {
                            s.classList.add('filled');
                        } else {
                            s.classList.remove('filled');
                        }
                    });
                });
            });
        });
    </script>

    <!-- Script du menu MBAA -->
    <script src="<?php echo esc_url( get_template_directory_uri() . '/dist/js/menu.js' ); ?>"></script>
    
    <!-- Script header adaptatif -->
    <script src="<?php echo esc_url( get_template_directory_uri() . '/dist/js/header-adaptive.js' ); ?>"></script>

<?php get_footer(); ?>
