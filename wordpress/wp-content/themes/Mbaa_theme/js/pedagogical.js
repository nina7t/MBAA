document.addEventListener('DOMContentLoaded', () => {

  const textes = {
    'nature-morte': 'La nature morte représente des objets inanimés : fruits, fleurs, ustensiles. Elle invite à méditer sur la beauté éphémère du quotidien.',
    'paysage': 'Le paysage représente la nature extérieure. Du XVIIe siècle à l\'impressionnisme, il devient un genre artistique à part entière.',
    'palette': 'La palette désigne le choix et l\'organisation des couleurs utilisées par un artiste dans une œuvre.',
    'scene': 'La scène de genre illustre la vie quotidienne ordinaire : marchés, repas, intérieurs domestiques et moments du quotidien.',
    'perspective': 'La perspective est une technique qui crée l\'illusion de profondeur et de volume sur une surface plane.',
    'clair-obscur': 'Le clair-obscur joue sur les contrastes forts entre lumière et ombre pour dramatiser et donner du relief à une scène.',
    'couleur': 'La couleur structure l\'espace, guide l\'œil et exprime les émotions. Chaque teinte porte une valeur symbolique.',
    'ligne': 'La ligne délimite les formes, oriente le regard et rythme la composition. Elle peut être douce, courbe ou tendue.',
    'forme': 'La forme définit les contours des figures et des objets. Géométrique ou organique, elle structure la lecture de l\'image.',
    'espace': 'L\'espace désigne la manière dont l\'artiste organise le vide et le plein, le premier et l\'arrière-plan dans la composition.',
  };

  document.querySelectorAll('.pedagogical-element-item').forEach(button => {
    button.addEventListener('click', () => {
      const filter = button.dataset.filter;
      const isActive = button.classList.contains('is-active');

      // Fermer tous les panels dans la même section
      const section = button.closest('.pedagogical-elements-left');
      section.querySelectorAll('.pedagogical-element-item').forEach(btn => {
        btn.classList.remove('is-active');
        const next = btn.nextElementSibling;
        if (next && next.classList.contains('pedagogical-element-panel')) {
          next.remove();
        }
      });

      // Si pas déjà ouvert, ouvrir ce panel
      if (!isActive) {
        button.classList.add('is-active');
        const panel = document.createElement('div');
        panel.classList.add('pedagogical-element-panel');
        panel.textContent = textes[filter] || 'Contenu à venir.';
        button.insertAdjacentElement('afterend', panel);
      }
    });
  });

});