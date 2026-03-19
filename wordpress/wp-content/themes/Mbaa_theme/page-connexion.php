<?php
/**
 * Template Name: Connexion
 * Template pour la page de connexion du MBAA
 * Remplace connexion.html
 *
 * @package Mbaa_theme
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Rediriger si déjà connecté
if ( is_user_logged_in() ) {
    wp_redirect( home_url('/visiteur/') );
    exit;
}

global $wpdb;
$assets = get_template_directory_uri() . '/asset';

get_header();
?>

<?php wp_body_open(); ?>

<header class="header header--connexion">
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
          <a class="header__nav-link" href="#" id="search-trigger" aria-label="Recherche">
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
      <h1 class="hero__title">
        <!-- Le titre de loeuvre -->
        <br>
        Connexion<br>
        <!-- Nom et prenom de l'artiste juste en dessous -->
        <em>Administration</em> 

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

    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <h1>MBAA</h1>
                <p>Connexion à l'espace administrateur</p>
            </div>
            
            <?php
            // Formulaire de connexion WordPress
            wp_login_form([
                'redirect'       => home_url('/visiteur/'),
                'form_id'        => 'loginForm',
                'label_username' => "Nom d'utilisateur",
                'label_password' => 'Mot de passe',
                'label_remember' => 'Se souvenir de moi',
                'label_log_in'   => 'Se connecter',
                'remember'       => true,
            ]);
            ?>
                
            <div class="login-links">
                <a href="#" class="forgot-password">Mot de passe oublié ?</a>
                <a href="<?php echo esc_url( admin_url() ); ?>" target="_blank" class="admin-link">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                    </svg>
                    Administration WordPress
                </a>
            </div>
        </div>
    </div>

    <script>
        // Gestion du menu burger
        const menuToggle = document.querySelector('.header__menu-toggle');
        const headerNav = document.querySelector('.header__nav');
        
        if (menuToggle && headerNav) {
            menuToggle.addEventListener('click', () => {
                const isExpanded = menuToggle.getAttribute('aria-expanded') === 'true';
                menuToggle.setAttribute('aria-expanded', !isExpanded);
                headerNav.setAttribute('aria-hidden', isExpanded);
            });
        }

        // Gestion du mot de passe oublié
        const forgotPasswordLink = document.querySelector('.forgot-password');
        if (forgotPasswordLink) {
            forgotPasswordLink.addEventListener('click', (e) => {
                e.preventDefault();
                alert('Fonctionnalité de récupération de mot de passe à implémenter');
            });
        }
    </script>

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
