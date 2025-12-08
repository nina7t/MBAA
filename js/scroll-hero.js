// Effet parallax 2D (horizontal + vertical) sur les images du hero
document.addEventListener('DOMContentLoaded', function() {
    
  // ÉTAPE 1 : On récupère toutes les images qui ont des attributs data-speed-x et data-speed-y
  const parallaxElements = document.querySelectorAll('[data-speed-x]');
  
  // ÉTAPE 2 : Fonction qui calcule et applique le mouvement parallax
  function parallaxScroll() {
      
      // On récupère la distance scrollée verticalement (vers le bas)
      const scrolled = window.pageYOffset;
      
      // On récupère la position horizontale de la fenêtre
      // (utile si on veut aussi réagir au scroll horizontal, mais on va utiliser la souris à la place)
      
      // Pour chaque image, on calcule son déplacement
      parallaxElements.forEach(element => {
          
          // On récupère la vitesse horizontale (X) de cette image
          const speedX = parseFloat(element.dataset.speedX);
          // speedX peut être positif (va vers la droite) ou négatif (va vers la gauche)
          
          // On récupère la vitesse verticale (Y) de cette image
          const speedY = parseFloat(element.dataset.speedY);
          // speedY contrôle le mouvement vertical
          
          // CALCUL DE LA POSITION HORIZONTALE (X)
          // On multiplie le scroll vertical par la vitesse X
          // Cela donne un mouvement diagonal intéressant
          const xPos = scrolled * speedX;
          
          // CALCUL DE LA POSITION VERTICALE (Y)
          // On multiplie le scroll par la vitesse Y
          // Le signe "-" fait bouger vers le HAUT quand on scrolle vers le BAS
          const yPos = -(scrolled * speedY);
          
          // On applique les deux transformations en même temps
          // translate(X, Y) = déplace l'élément de X pixels horizontalement et Y pixels verticalement
          element.style.transform = `translate(${xPos}px, ${yPos}px)`;
      });
  }
  
  // ÉTAPE 3 : Optimisation des performances avec requestAnimationFrame
  let ticking = false;
  
  // On écoute l'événement de scroll
  window.addEventListener('scroll', function() {
      
      // Si on n'est pas déjà en train de traiter un scroll
      if (!ticking) {
          
          // requestAnimationFrame va appeler notre fonction au moment optimal
          // Cela garantit 60 images par seconde (60fps) pour une animation fluide
          window.requestAnimationFrame(function() {
              
              // On exécute la fonction qui déplace les images
              parallaxScroll();
              
              // On indique qu'on a fini le traitement
              ticking = false;
          });
          
          // On lève le "drapeau" pour dire qu'on est occupé
          ticking = true;
      }
  });
  
  // ÉTAPE 4 : On appelle la fonction une première fois au chargement
  // pour positionner correctement les images dès le début
  parallaxScroll();
});
