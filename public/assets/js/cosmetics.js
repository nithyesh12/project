document.addEventListener('DOMContentLoaded', () => {
    const grid = document.getElementById('cosmeticsGrid');
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');

    let cosmeticData = [];

    // Fetch cosmetic data from backend
    fetch('api/get_cosmetics.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                cosmeticData = data.data;
                renderCards(cosmeticData);
            } else {
                grid.innerHTML = `<div class="error-message"><i class="fa-solid fa-triangle-exclamation"></i> ${data.message}</div>`;
            }
        })
        .catch(error => {
            console.error('Error fetching cosmetics:', error);
            grid.innerHTML = `<div class="error-message">Failed to load cosmetic data.</div>`;
        });

    function renderCards(data) {
        grid.innerHTML = '';
        if (data.length === 0) {
            grid.innerHTML = `<div style="grid-column: 1 / -1; text-align: center; color: var(--text-light);">No crops matched your search criteria.</div>`;
            return;
        }

        data.forEach(item => {
            let icon = 'fa-leaf';
            if (item.category === 'Skin Care') icon = 'fa-spa';
            else if (item.category === 'Hair Care') icon = 'fa-scissors'; // fallback, maybe fa-spray-can
            else if (item.category === 'Medicinal') icon = 'fa-notes-medical';

            const card = document.createElement('div');
            card.className = 'crop-card';
            
            // Generate Image src (fallback to default if image missing)
            const imgSrc = item.image_url ? item.image_url : 'assets/images/crops/default.jpg';
            
            card.innerHTML = `
                <img src="${imgSrc}" alt="${item.crop_name}" class="crop-img" onerror="this.src='assets/images/crops/default.jpg'">
                <div class="card-content">
                    <div class="card-subtitle"><i class="fa-solid ${icon} use-icon"></i>${item.category}</div>
                    <h3 class="card-title">${item.crop_name}</h3>
                    <div class="detail-item">
                        <span class="detail-label">Benefits:</span> ${item.benefits}
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Usage Method:</span> ${item.usage_method}
                    </div>
                </div>
            `;
            grid.appendChild(card);
        });
    }

    function applyFilters() {
        const searchTerm = searchInput.value.toLowerCase();
        const category = categoryFilter.value;

        const filtered = cosmeticData.filter(item => {
            const matchesSearch = item.crop_name.toLowerCase().includes(searchTerm);
            const matchesCat = (category === 'All' || item.category === category);
            return matchesSearch && matchesCat;
        });

        renderCards(filtered);
    }

    searchInput.addEventListener('input', applyFilters);
    categoryFilter.addEventListener('change', applyFilters);
});
