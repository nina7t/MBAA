document.addEventListener('DOMContentLoaded', function() {
  const searchInput = document.getElementById('artGallerySearch');
  const navButtons = document.querySelectorAll('.art-gallery-nav-btn');
  const galleryItems = document.querySelectorAll('.art-gallery-item');
  const activeFilters = {
    toutVoir: false,
    collections: false,
    epoque: false,
    artistes: false,
    salle: false,
    medium: false
  };

  // Toggle filter function
  function toggleFilter(filter) {
    activeFilters[filter] = !activeFilters[filter];
    const button = document.querySelector(`[data-filter="${filter}"]`);
    const arrow = button.querySelector('.art-gallery-nav-arrow');
    
    if (activeFilters[filter]) {
      arrow.style.transform = 'rotate(90deg)';
      button.classList.add('active');
    } else {
      arrow.style.transform = 'rotate(0deg)';
      button.classList.remove('active');
    }
  }

  // Add click handlers to navigation buttons
  navButtons.forEach(button => {
    button.addEventListener('click', function() {
      const filter = this.dataset.filter;
      toggleFilter(filter);
    });
  });

  // Search functionality
  if (searchInput) {
    searchInput.addEventListener('input', function(e) {
      const searchTerm = e.target.value.toLowerCase();
      
      galleryItems.forEach(item => {
        const alt = item.querySelector('img').alt.toLowerCase();
        const id = item.dataset.id;
        
        if (alt.includes(searchTerm) || id.includes(searchTerm)) {
          item.style.display = 'block';
        } else {
          item.style.display = 'none';
        }
      });
    });
  }
});

