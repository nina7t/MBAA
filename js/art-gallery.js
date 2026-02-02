// Configuration des filtres et sous-filtres
const filtersConfig = {
  toutVoir: {
    label: "Tout voir",
    subFilters: []
  },
  collections: {
    label: "Collections",
    subFilters: [
      { id: "peinture", label: "Peinture" },
      { id: "sculpture", label: "Sculpture" },
      { id: "dessin", label: "Dessin" },
      { id: "arts-decoratifs", label: "Arts décoratifs" }
    ]
  },
  epoque: {
    label: "Époque",
    subFilters: [
      { id: "renaissance", label: "Renaissance" },
      { id: "baroque", label: "Baroque" },
      { id: "classicisme", label: "Classicisme" },
      { id: "romantisme", label: "Romantisme" },
      { id: "impressionnisme", label: "Impressionnisme" },
      { id: "moderne", label: "Art Moderne" },
      { id: "contemporain", label: "Art Contemporain" }
    ]
  },
  artistes: {
    label: "Artistes",
    subFilters: [
      { id: "hokusai", label: "Hokusai" },
      { id: "turner", label: "Turner" },
      { id: "boucher", label: "Boucher" },
      { id: "collomb", label: "Collomb" },
      { id: "guenat", label: "Guenat" },
      { id: "stojka", label: "Ceija Stojka" }
    ]
  },
  salle: {
    label: "Salle",
    subFilters: [
      { id: "salle-1", label: "Salle 1 - Renaissance" },
      { id: "salle-2", label: "Salle 2 - Baroque" },
      { id: "salle-3", label: "Salle 3 - XIXe siècle" },
      { id: "salle-4", label: "Salle 4 - Impressionnisme" },
      { id: "salle-5", label: "Salle 5 - Art Moderne" },
      { id: "salle-6", label: "Salle 6 - Contemporain" }
    ]
  },
  medium: {
    label: "Medium",
    subFilters: [
      { id: "huile", label: "Huile sur toile" },
      { id: "aquarelle", label: "Aquarelle" },
      { id: "encre", label: "Encre" },
      { id: "fusain", label: "Fusain" },
      { id: "bronze", label: "Bronze" },
      { id: "marbre", label: "Marbre" }
    ]
  }
};

// Association des œuvres avec leurs tags
const artworksTags = {
  1: { collections: ["peinture"], epoque: ["moderne"], artistes: ["hokusai"], salle: ["salle-5"], medium: ["encre"] },
  2: { collections: ["peinture"], epoque: ["contemporain"], artistes: ["collomb"], salle: ["salle-6"], medium: ["huile"] },
  3: { collections: ["peinture"], epoque: ["contemporain"], artistes: ["stojka"], salle: ["salle-6"], medium: ["huile"] },
  4: { collections: ["peinture"], epoque: ["baroque"], artistes: ["boucher"], salle: ["salle-2"], medium: ["huile"] },
  5: { collections: ["peinture"], epoque: ["impressionnisme"], artistes: [], salle: ["salle-4"], medium: ["huile"] },
  6: { collections: ["peinture"], epoque: ["romantisme"], artistes: ["turner"], salle: ["salle-3"], medium: ["aquarelle"] },
  7: { collections: ["sculpture"], epoque: ["classicisme"], artistes: [], salle: ["salle-2"], medium: ["bronze"] },
  8: { collections: ["peinture"], epoque: ["classicisme"], artistes: [], salle: ["salle-2"], medium: ["huile"] },
  9: { collections: ["peinture"], epoque: ["contemporain"], artistes: [], salle: ["salle-6"], medium: ["huile"] },
  10: { collections: ["peinture"], epoque: ["romantisme"], artistes: ["guenat"], salle: ["salle-3"], medium: ["huile"] },
  11: { collections: ["sculpture"], epoque: ["classicisme"], artistes: [], salle: ["salle-2"], medium: ["marbre"] },
  12: { collections: ["peinture"], epoque: ["contemporain"], artistes: [], salle: ["salle-6"], medium: ["huile"] },
  13: { collections: ["peinture"], epoque: ["romantisme"], artistes: ["turner"], salle: ["salle-3"], medium: ["aquarelle"] },
  14: { collections: ["dessin"], epoque: ["romantisme"], artistes: ["guenat"], salle: ["salle-3"], medium: ["fusain"] },
  15: { collections: ["peinture"], epoque: ["romantisme"], artistes: [], salle: ["salle-3"], medium: ["huile"] },
  16: { collections: ["peinture"], epoque: ["contemporain"], artistes: ["collomb"], salle: ["salle-6"], medium: ["huile"] },
  17: { collections: ["sculpture"], epoque: ["classicisme"], artistes: [], salle: ["salle-2"], medium: ["bronze"] },
  18: { collections: ["peinture"], epoque: ["moderne"], artistes: [], salle: ["salle-5"], medium: ["huile"] },
  19: { collections: ["sculpture"], epoque: ["classicisme"], artistes: [], salle: ["salle-2"], medium: ["marbre"] }
};

// État actuel des filtres
let activeMainFilter = null;
let activeSubFilters = [];

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
  initializeFilters();
  setupSearch();
});

function initializeFilters() {
  const navButtons = document.querySelectorAll('.art-gallery-nav-btn');
  
  navButtons.forEach(button => {
    button.addEventListener('click', function() {
      const filterId = this.dataset.filter;
      toggleMainFilter(filterId, this);
    });
  });
}

function toggleMainFilter(filterId, buttonElement) {
  const allButtons = document.querySelectorAll('.art-gallery-nav-btn');
  
  // Si on clique sur le même filtre, on le désactive
  if (activeMainFilter === filterId) {
    activeMainFilter = null;
    activeSubFilters = [];
    buttonElement.classList.remove('active');
    removeSubFiltersMenu();
    showAllArtworks();
    return;
  }
  
  // Désactiver tous les boutons
  allButtons.forEach(btn => btn.classList.remove('active'));
  
  // Activer le nouveau filtre
  activeMainFilter = filterId;
  activeSubFilters = [];
  buttonElement.classList.add('active');
  
  // Afficher les sous-filtres si disponibles
  if (filtersConfig[filterId].subFilters.length > 0) {
    showSubFilters(filterId, buttonElement);
  } else {
    removeSubFiltersMenu();
    showAllArtworks();
  }
}

function showSubFilters(filterId, buttonElement) {
  removeSubFiltersMenu();
  
  const subFiltersContainer = document.createElement('div');
  subFiltersContainer.className = 'art-gallery-subfilters';
  subFiltersContainer.id = 'subfiltersMenu';
  
  const config = filtersConfig[filterId];
  
  config.subFilters.forEach(subFilter => {
    const subButton = document.createElement('button');
    subButton.className = 'art-gallery-subfilterbtn';
    subButton.dataset.subfilter = subFilter.id;
    subButton.dataset.mainfilter = filterId;
    subButton.textContent = subFilter.label;
    
    subButton.addEventListener('click', function() {
      toggleSubFilter(subFilter.id, filterId, this);
    });
    
    subFiltersContainer.appendChild(subButton);
  });
  
  // Insérer après le menu de navigation principal
  const navElement = document.querySelector('.art-gallery-nav');
  navElement.parentNode.insertBefore(subFiltersContainer, navElement.nextSibling);
}

function removeSubFiltersMenu() {
  const existingMenu = document.getElementById('subfiltersMenu');
  if (existingMenu) {
    existingMenu.remove();
  }
}

function toggleSubFilter(subFilterId, mainFilterId, buttonElement) {
  const index = activeSubFilters.indexOf(subFilterId);
  
  if (index > -1) {
    // Désactiver le sous-filtre
    activeSubFilters.splice(index, 1);
    buttonElement.classList.remove('active');
  } else {
    // Activer le sous-filtre
    activeSubFilters.push(subFilterId);
    buttonElement.classList.add('active');
  }
  
  filterArtworks();
}

function filterArtworks() {
  const artworkItems = document.querySelectorAll('.art-gallery-item');
  
  if (activeSubFilters.length === 0) {
    showAllArtworks();
    return;
  }
  
  artworkItems.forEach(item => {
    const artworkId = parseInt(item.dataset.id);
    const artworkData = artworksTags[artworkId];
    
    if (!artworkData) {
      item.style.display = 'none';
      return;
    }
    
    // Vérifier si l'œuvre correspond à au moins un des sous-filtres actifs
    const matches = activeSubFilters.some(subFilterId => {
      return artworkData[activeMainFilter] && 
             artworkData[activeMainFilter].includes(subFilterId);
    });
    
    item.style.display = matches ? 'block' : 'none';
  });
}

function showAllArtworks() {
  const artworkItems = document.querySelectorAll('.art-gallery-item');
  artworkItems.forEach(item => {
    item.style.display = 'block';
  });
}

function setupSearch() {
  const searchInput = document.getElementById('artGallerySearch');
  
  if (searchInput) {
    searchInput.addEventListener('input', function(e) {
      const searchTerm = e.target.value.toLowerCase();
      const artworkItems = document.querySelectorAll('.art-gallery-item');
      
      if (searchTerm === '') {
        if (activeSubFilters.length > 0) {
          filterArtworks();
        } else {
          showAllArtworks();
        }
        return;
      }
      
      artworkItems.forEach(item => {
        const artworkId = item.dataset.id;
        const imgAlt = item.querySelector('img').alt.toLowerCase();
        
        if (artworkId.includes(searchTerm) || imgAlt.includes(searchTerm)) {
          item.style.display = 'block';
        } else {
          item.style.display = 'none';
        }
      });
    });
  }
}