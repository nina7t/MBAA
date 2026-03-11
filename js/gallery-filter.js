document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const masonryItems = document.querySelectorAll('.masonry-item');
    const activeFilters = {
        epoque: [],
        artiste: [],
        medium: []
    };

    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filterType = this.dataset.filterType;
            const filterOptions = document.querySelector(`.filter-options--${filterType}`);
            const arrow = this.querySelector('.filter-arrow');

            filterOptions.classList.toggle('hidden');
            arrow.classList.toggle('rotated');
            this.classList.toggle('active');
        });
    });

    const checkboxes = document.querySelectorAll('input[type="checkbox"]');

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const filterType = this.closest('.filter-group').querySelector('.filter-btn').dataset.filterType;
            const value = this.value;

            if (this.checked) {
                if (!activeFilters[filterType].includes(value)) {
                    activeFilters[filterType].push(value);
                }
            } else {
                activeFilters[filterType] = activeFilters[filterType].filter(v => v !== value);
            }

            filterGallery();
        });
    });

    function filterGallery() {
        let visibleCount = 0;

        masonryItems.forEach(item => {
            let shouldShow = true;

            for (const filterType in activeFilters) {
                if (activeFilters[filterType].length > 0) {
                    const itemValue = item.dataset[filterType];
                    if (!activeFilters[filterType].includes(itemValue)) {
                        shouldShow = false;
                        break;
                    }
                }
            }

            if (shouldShow) {
                item.classList.remove('hidden');
                visibleCount++;
            } else {
                item.classList.add('hidden');
            }
        });

        const grid = document.getElementById('masonryGrid');
        let noResultsMessage = grid.querySelector('.no-results');

        if (visibleCount === 0 && !noResultsMessage) {
            noResultsMessage = document.createElement('div');
            noResultsMessage.className = 'no-results';
            noResultsMessage.textContent = 'Aucune œuvre ne correspond à vos critères de filtrage.';
            grid.appendChild(noResultsMessage);
        } else if (visibleCount > 0 && noResultsMessage) {
            noResultsMessage.remove();
        }
    }
});
