<?php
/**
 * Template Name: Le Musée
 * Template pour la page d'histoire du musée MBAA
 * Histoire, mécènes, transformation de la halle au grain, restauration 2016
 *
 * @package Mbaa_theme
 */

if ( ! defined( 'ABSPATH' ) ) exit;

global $wpdb;
$assets = get_template_directory_uri() . '/asset';

get_header();
?>

<?php wp_body_open(); ?>

<header class="header header--musee">
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
          <a class="header__nav-link header__nav-link--active" href="<?php echo esc_url( home_url('/le-musee/') ); ?>">Le musée</a>
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
            <svg class="header__nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"></path>
              <path d="M13 5v2"></path><path d="M13 17v2"></path><path d="M13 11v2"></path>
            </svg>
          </a>
        </li>
        <li class="header__nav-item">
          <a class="header__nav-link" href="#" id="search-trigger" aria-label="Recherche">
            <svg class="header__nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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

  <!-- ====== HERO SECTION ====== -->
  <div class="header__hero">
    <div class="hero__left">
      <p class="hero__eyebrow">Histoire & Patrimoine</p>
      <h1 class="hero__title">
        Le<br>
        Musée<br>
        MBAA
      </h1>
    </div>
    <div class="hero__right">
      <p class="hero__description">
        Découvrez l'histoire fascinante du Musée des Beaux-Arts, de la transformation de la halle au grain à la restauration moderne de 2016.
      </p>
      <a href="#histoire" class="hero__cta">
        Explorer l'histoire
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M7 17L17 7M17 7H7M17 7V17"/>
        </svg>
      </a>
    </div>
  </div>
</header>

<main class="musee">
  
  <!-- ====== SECTION HISTOIRE ====== -->
  <section id="histoire" class="musee__section">
    <div class="container">
      <div class="section__header">
        <h2 class="section__title">Une Histoire Millénaire</h2>
        <p class="section__subtitle">De la halle médiévale au musée contemporain</p>
      </div>
      
      <div class="histoire__timeline">
        <div class="timeline__item">
          <div class="timeline__date">XIIIe siècle</div>
          <div class="timeline__content">
            <h3>La Halle au Grain Originelle</h3>
            <p>Construite au cœur de la cité médiévale, la halle servait de lieu de commerce et de rassemblement pour les habitants. Son architecture en bois et pierre témoignait de l'importance économique de la région.</p>
          </div>
        </div>
        
        <div class="timeline__item">
          <div class="timeline__date">XVIIIe siècle</div>
          <div class="timeline__content">
            <h3>Transformation Architecturale</h3>
            <p>La halle est restaurée et transformée, adoptant des éléments classiques tout en préservant son caractère médiéval. Elle devient un symbole du patrimoine architectural local.</p>
          </div>
        </div>
        
        <div class="timeline__item">
          <div class="timeline__date">1982</div>
          <div class="timeline__content">
            <h3>Naissance du Musée</h3>
            <p>La décision est prise de transformer la halle historique en musée des beaux-arts, marquant le début d'une nouvelle ère culturelle pour la région.</p>
          </div>
        </div>
        
        <div class="timeline__item">
          <div class="timeline__date">2016</div>
          <div class="timeline__content">
            <h3>Restauration Moderne</h3>
            <p>Une restauration complète est entreprise, alliant respect du patrimoine historique et technologies modernes pour créer un espace muséal contemporain.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ====== SECTION MÉCÈNES ====== -->
  <section id="mecenes" class="musee__section musee__section--alt">
    <div class="container">
      <div class="section__header">
        <h2 class="section__title">Nos Mécènes</h2>
        <p class="section__subtitle">Les bienfaiteurs qui ont fait vivre ce projet</p>
      </div>
      
      <div class="mecenes__grid">
        <div class="mecene__card">
          <div class="mecene__image">
            <img src="<?php echo esc_url( $assets . '/Img/placeholder/mecene1.jpg' ); ?>" alt="Portrait du mécène">
          </div>
          <div class="mecene__content">
            <h3>Famille de Clermont-Tonnerre</h3>
            <p class="mecene__role">Fondateurs principaux</p>
            <p class="mecene__description">Cette illustre famille de la noblesse locale a initié le projet muséal et offert les premières œuvres constituant le noyau de la collection.</p>
          </div>
        </div>
        
        <div class="mecene__card">
          <div class="mecene__image">
            <img src="<?php echo esc_url( $assets . '/Img/placeholder/mecene2.jpg' ); ?>" alt="Portrait du mécène">
          </div>
          <div class="mecene__content">
            <h3>Marie-Louise Dubois</h3>
            <p class="mecene__role">Collectionneuse passionnée</p>
            <p class="mecene__description">Leg exceptionnel de plus de 200 œuvres d'art moderne et contemporain, enrichissant considérablement la diversité de nos collections.</p>
          </div>
        </div>
        
        <div class="mecene__card">
          <div class="mecene__image">
            <img src="<?php echo esc_url( $assets . '/Img/placeholder/mecene3.jpg' ); ?>" alt="Portrait du mécène">
          </div>
          <div class="mecene__content">
            <h3>Fondation Régionale pour les Arts</h3>
            <p class="mecene__role">Partenaire institutionnel</p>
            <p class="mecene__description">Soutien continu depuis 2010 pour la restauration, l'acquisition d'œuvres et le développement des programmes éducatifs.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ====== SECTION ARCHITECTURE ====== -->
  <section id="architecture" class="musee__section">
    <div class="container">
      <div class="section__header">
        <h2 class="section__title">Architecture & Patrimoine</h2>
        <p class="section__subtitle">L'harmonie entre tradition et modernité</p>
      </div>
      
      <div class="architecture__showcase">
        <div class="showcase__text">
          <h3>La Transformation de la Halle</h3>
          <p>Le projet de transformation de la halle au grain en musée représente un défi architectural majeur : préserver l'âme historique du bâtiment tout en créant un espace muséal fonctionnel et moderne.</p>
          
          <div class="features__list">
            <div class="feature__item">
              <div class="feature__icon">🏛️</div>
              <div class="feature__content">
                <h4>Patrimoine Préservé</h4>
                <p>Les poutres d'origine, les murs en pierre et les voûtes ont été soigneusement restaurés pour conserver le caractère historique.</p>
              </div>
            </div>
            
            <div class="feature__item">
              <div class="feature__icon">💡</div>
              <div class="feature__content">
                <h4>Lumière Naturelle</h4>
                <p>Des ouvertures stratégiques ont été créées pour inonder les espaces d'une lumière optimale, mettant en valeur les œuvres.</p>
              </div>
            </div>
            
            <div class="feature__item">
              <div class="feature__icon">🔧</div>
              <div class="feature__content">
                <h4>Technologie Moderne</h4>
                <p>Systèmes de climatisation, sécurité et muséographie intégrés discrètement dans l'architecture historique.</p>
              </div>
            </div>
          </div>
        </div>
        
        <div class="showcase__visual">
          <div class="visual__gallery">
            <div class="gallery__main">
              <img src="<?php echo esc_url( $assets . '/Img/placeholder/halle-exterieur.jpg' ); ?>" alt="Vue extérieure de la halle">
            </div>
            <div class="gallery__thumbs">
              <img src="<?php echo esc_url( $assets . '/Img/placeholder/halle-interieur.jpg' ); ?>" alt="Vue intérieure">
              <img src="<?php echo esc_url( $assets . '/Img/placeholder/architecture-detail.jpg' ); ?>" alt="Détail architectural">
              <img src="<?php echo esc_url( $assets . '/Img/placeholder/restauration-2016.jpg' ); ?>" alt="Restauration 2016">
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ====== SECTION RESTAURATION 2016 ====== -->
  <section id="restauration" class="musee__section musee__section--alt">
    <div class="container">
      <div class="section__header">
        <h2 class="section__title">Restauration 2016</h2>
        <p class="section__subtitle">Une renaissance pour le musée</p>
      </div>
      
      <div class="restauration__story">
        <div class="story__left">
          <div class="story__image">
            <img src="<?php echo esc_url( $assets . '/Img/placeholder/restauration-avant.jpg' ); ?>" alt="Avant la restauration">
          </div>
          <div class="story__caption">État avant restauration - 2015</div>
        </div>
        
        <div class="story__center">
          <div class="story__arrow">
            <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
          </div>
          <div class="story__stats">
            <div class="stat__item">
              <div class="stat__number">18</div>
              <div class="stat__label">mois de travaux</div>
            </div>
            <div class="stat__item">
              <div class="stat__number">2.3M</div>
              <div class="stat__label">euros investis</div>
            </div>
            <div class="stat__item">
              <div class="stat__number">150</div>
              <div class="stat__label">artisans impliqués</div>
            </div>
          </div>
        </div>
        
        <div class="story__right">
          <div class="story__image">
            <img src="<?php echo esc_url( $assets . '/Img/placeholder/restauration-apres.jpg' ); ?>" alt="Après la restauration">
          </div>
          <div class="story__caption">État après restauration - 2017</div>
        </div>
      </div>
      
      <div class="restauration__details">
        <div class="details__grid">
          <div class="detail__card">
            <h3>Travaux Structuraux</h3>
            <ul>
              <li>Renforcement des fondations historiques</li>
              <li>Restauration complète des charpentes d'origine</li>
              <li>Consolidation des murs en pierre</li>
              <li>Création de nouvelles ouvertures lumineuses</li>
            </ul>
          </div>
          
          <div class="detail__card">
            <h3>Équipements Modernes</h3>
            <ul>
              <li>Système de climatisation et contrôle hygrométrique</li>
              <li>Éclairage muséographique de pointe</li>
              <li>Système de sécurité et surveillance</li>
              <li>Accessibilité PMR complète</li>
            </ul>
          </div>
          
          <div class="detail__card">
            <h3>Aménagements Intérieurs</h3>
            <ul>
              <li>Création de 5 salles d'exposition</li>
              <li>Espace pédagogique et ateliers</li>
              <li>Boutique-musée et café</li>
              <li>Réserves et bureaux administratifs</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ====== SECTION AVENIR ====== -->
  <section id="avenir" class="musee__section">
    <div class="container">
      <div class="section__header">
        <h2 class="section__title">Vers l'Avenir</h2>
        <p class="section__subtitle">Continuer l'aventure culturelle</p>
      </div>
      
      <div class="avenir__content">
        <div class="avenir__text">
          <p>Aujourd'hui, le Musée MBAA continue d'évoluer, porté par une équipe passionnée et le soutien de ses visiteurs. Nos projets futurs incluent le développement numérique, l'enrichissement des collections et l'ouverture internationale.</p>
          
          <div class="avenir__cta">
            <a href="<?php echo esc_url( home_url('/evenements/') ); ?>" class="btn btn--primary">
              Découvrir nos événements
            </a>
            <a href="<?php echo esc_url( home_url('/reservation/') ); ?>" class="btn btn--outline">
              Réserver votre visite
            </a>
          </div>
        </div>
        
        <div class="avenir__image">
          <img src="<?php echo esc_url( $assets . '/Img/placeholder/musee-avenir.jpg' ); ?>" alt="Projet d'avenir du musée">
        </div>
      </div>
    </div>
  </section>

</main>

<?php get_footer(); ?>
