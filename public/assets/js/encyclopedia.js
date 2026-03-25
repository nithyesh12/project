document.addEventListener('DOMContentLoaded', async () => {
    const cropGrid = document.getElementById('cropGrid');
    const searchInput = document.getElementById('searchInput');
    const seasonFilter = document.getElementById('seasonFilter');
    const loadMoreBtn = document.getElementById('loadMoreBtn');
    
    if(!cropGrid) return; // Only run on list page

    let allCrops = [];
    let filteredCrops = [];
    let displayedCount = 0;
    const loadCount = 8; // load 8 items at a time

    try {
        const response = await fetch('assets/data/crops.json');
        allCrops = await response.json();
        filteredCrops = [...allCrops];
        renderCrops();
    } catch(e) {
        console.error('Error fetching crops', e);
        cropGrid.innerHTML = '<p style="color: red; padding: 2rem;">Error loading encyclopedia data.</p>';
    }

    function renderCrops() {
        const toLoad = filteredCrops.slice(displayedCount, displayedCount + loadCount);
        
        toLoad.forEach(crop => {
            const card = document.createElement('div');
            card.className = 'crop-card animate-fade-in';
            card.onclick = () => window.location.href = `crop_details.php?id=${crop.id}`;
            card.innerHTML = `
                <div style="position: relative; height: 200px; border-radius: var(--border-radius-lg) var(--border-radius-lg) 0 0; overflow: hidden; background: #e2e8f0;">
                    <img src="${crop.image}" loading="lazy" alt="${crop.name}" class="crop-image" style="width: 100%; height: 100%; object-fit: cover; transition: transform var(--transition-normal);">
                    <span class="badge" style="position: absolute; top: 1rem; right: 1rem; background: rgba(255,255,255,0.95); padding: 0.2rem 0.6rem; border-radius: 4px; font-weight: bold; font-size: 0.8rem; color: var(--primary-dark); box-shadow: 0 2px 4px rgba(0,0,0,0.1);">${crop.season}</span>
                </div>
                <div class="crop-details" style="padding: 1.5rem; border: 1px solid var(--border-color); border-top: none; border-radius: 0 0 var(--border-radius-lg) var(--border-radius-lg); background: var(--bg-surface); display: flex; flex-direction: column; height: 100%;">
                    <h3 style="color: var(--primary-dark); margin-bottom: 0.5rem;">${crop.name}</h3>
                    <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 1.5rem; flex-grow: 1;">${crop.short_desc}</p>
                    <div style="display: flex; justify-content: space-between; font-size: 0.85rem; color: #475569; font-weight: 500; border-top: 1px solid var(--border-color); padding-top: 1rem;">
                        <span><i class="fa-solid fa-droplet" style="color: var(--secondary-color);"></i> ${crop.water_req}</span>
                        <span><i class="fa-solid fa-temperature-half" style="color: var(--error-color);"></i> ${crop.temp_range.split('-')[0].trim()}</span>
                    </div>
                </div>
            `;
            
            // Add hover effect via JS or rely on CSS
            card.addEventListener('mouseenter', () => {
                const img = card.querySelector('.crop-image');
                if(img) img.style.transform = 'scale(1.05)';
                card.style.transform = 'translateY(-5px)';
                card.style.boxShadow = '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)';
            });
            card.addEventListener('mouseleave', () => {
                const img = card.querySelector('.crop-image');
                if(img) img.style.transform = 'scale(1)';
                card.style.transform = 'none';
                card.style.boxShadow = 'none';
            });
            
            card.style.transition = 'transform 0.3s ease, box-shadow 0.3s ease';
            cropGrid.appendChild(card);
        });

        displayedCount += toLoad.length;

        if (displayedCount >= filteredCrops.length) {
            if(loadMoreBtn) loadMoreBtn.style.display = 'none';
        } else {
            if(loadMoreBtn) loadMoreBtn.style.display = 'inline-flex';
        }
        
        if (filteredCrops.length === 0) {
            cropGrid.innerHTML = '<div style="grid-column: 1 / -1; text-align: center; padding: 4rem; color: var(--text-muted);"><i class="fa-solid fa-magnifying-glass fa-3x" style="margin-bottom: 1rem; color: #cbd5e1;"></i><p style="font-size: 1.2rem;">No crops found matching your filters.</p></div>';
        }
    }

    function applyFilters() {
        const term = searchInput.value.toLowerCase();
        const season = seasonFilter.value;
        
        filteredCrops = allCrops.filter(crop => {
            const matchName = crop.name.toLowerCase().includes(term) || crop.scientific_name.toLowerCase().includes(term);
            const matchSeason = season === 'All' || crop.season === season;
            return matchName && matchSeason;
        });

        cropGrid.innerHTML = '';
        displayedCount = 0;
        renderCrops();
    }

    if(searchInput) searchInput.addEventListener('input', applyFilters);
    if(seasonFilter) seasonFilter.addEventListener('change', applyFilters);
    if(loadMoreBtn) loadMoreBtn.addEventListener('click', renderCrops);
});
