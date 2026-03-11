document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('.bibliotheque__filter-btn');
    const gridItems = document.querySelectorAll('.bibliotheque__grid-item');
    const searchInput = document.getElementById('bibliothequeSearch');
    let activeFilter = 'all';

    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            activeFilter = this.dataset.filter;
            filterGrid();
        });
    });

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            filterGrid();
        });
    }

    function filterGrid() {
        const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
        let visibleCount = 0;

        gridItems.forEach(item => {
            const category = item.dataset.category;
            const title = item.querySelector('h3')?.textContent.toLowerCase() || '';
            const description = item.querySelector('p')?.textContent.toLowerCase() || '';

            const matchesCategory = activeFilter === 'all' || category === activeFilter;
            const matchesSearch = searchTerm === '' || title.includes(searchTerm) || description.includes(searchTerm);

            if (matchesCategory && matchesSearch) {
                item.classList.remove('hidden');
                visibleCount++;
            } else {
                item.classList.add('hidden');
            }
        });

        const grid = document.getElementById('bibliothequeGrid');
        let noResultsMessage = grid.querySelector('.bibliotheque__no-results');

        if (visibleCount === 0 && !noResultsMessage) {
            noResultsMessage = document.createElement('div');
            noResultsMessage.className = 'bibliotheque__no-results';
            noResultsMessage.textContent = 'Aucune œuvre ne correspond à vos critères.';
            grid.appendChild(noResultsMessage);
        } else if (visibleCount > 0 && noResultsMessage) {
            noResultsMessage.remove();
        }
    }
});
