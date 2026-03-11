<?php
/**
 * Template Name: Histoire de l'Art
 * Template pour le tableau des périodes artistiques
 *
 * @package Mbaa_theme
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();
?>

<?php wp_body_open(); ?>

<section class="art-history-section">
  <div class="container">
    <header class="art-history__header">
      <h1 class="art-history__title">Histoire de l'Art</h1>
      <p class="art-history__subtitle">Périodes, mouvements et artistes majeurs</p>
    </header>

    <div class="art-history__table-wrapper">
      <table class="art-history__table">
        <thead>
          <tr>
            <th class="art-history__th art-history__th--period">Période</th>
            <th class="art-history__th art-history__th--movement">Mouvement / Caractéristiques</th>
            <th class="art-history__th art-history__th--artists">Artistes majeurs</th>
          </tr>
        </thead>
        <tbody>
          <tr class="art-history__row">
            <td class="art-history__td art-history__td--period">1400-1600</td>
            <td class="art-history__td art-history__td--movement">
              <strong>Renaissance</strong><br>
              Retour à l'Antiquité classique, perspective, réalisme anatomique
            </td>
            <td class="art-history__td art-history__td--artists">
              Léonard de Vinci, Michel-Ange, Raphaël, Botticelli, Titien
            </td>
          </tr>
          
          <tr class="art-history__row">
            <td class="art-history__td art-history__td--period">1600-1750</td>
            <td class="art-history__td art-history__td--movement">
              <strong>Baroque</strong><br>
              Dramatisme, mouvement intense, clair-obscur, grandeur théâtrale
            </td>
            <td class="art-history__td art-history__td--artists">
              Caravage, Rembrandt, Rubens, Vélasquez, Bernin
            </td>
          </tr>
          
          <tr class="art-history__row">
            <td class="art-history__td art-history__td--period">1750-1820</td>
            <td class="art-history__td art-history__td--movement">
              <strong>Néoclassicisme</strong><br>
              Retour aux valeurs antiques, rigueur, noblesse, vertus civiques
            </td>
            <td class="art-history__td art-history__td--artists">
              Jacques-Louis David, Ingres, Canova, Angelica Kauffman
            </td>
          </tr>
          
          <tr class="art-history__row">
            <td class="art-history__td art-history__td--period">1820-1900</td>
            <td class="art-history__td art-history__td--movement">
              <strong>Romantisme</strong><br>
              Émotion, individualisme, nature sauvage, exotisme
            </td>
            <td class="art-history__td art-history__td--artists">
              Delacroix, Géricault, Turner, Constable, Friedrich
            </td>
          </tr>
          
          <tr class="art-history__row">
            <td class="art-history__td art-history__td--period">1870-1910</td>
            <td class="art-history__td art-history__td--movement">
              <strong>Impressionnisme</strong><br>
              Capture de l'instant, lumière, couleur, sujets modernes
            </td>
            <td class="art-history__td art-history__td--artists">
              Monet, Renoir, Degas, Pissarro, Caillebotte
            </td>
          </tr>
          
          <tr class="art-history__row">
            <td class="art-history__td art-history__td--period">1900-1945</td>
            <td class="art-history__td art-history__td--movement">
              <strong>Cubisme / Modernisme</strong><br>
              Fragmentation géométrique, multiple perspectives, abstraction
            </td>
            <td class="art-history__td art-history__td--artists">
              Picasso, Braque, Léger, Gris, Mondrian
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="art-history__navigation">
      <a href="<?php echo esc_url( home_url('/') ); ?>" class="art-history__back-btn">
        ← Retour à l'accueil
      </a>
      <a href="<?php echo esc_url( home_url('/collections/') ); ?>" class="art-history__collections-btn">
        Voir les collections →
      </a>
    </div>
  </div>
</section>

<?php get_footer(); ?>
