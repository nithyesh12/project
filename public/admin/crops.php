<?php require_once 'layout_header.php'; ?>

<div class="header">
    <div>
        <h1>Manage Crops</h1>
        <p style="color: var(--text-light); margin-top: 0.5rem;">Add, update, or remove crops from the database and AI engine.</p>
    </div>
    <button class="btn" onclick="openCropModal()">
        <i class="fa-solid fa-plus"></i> Add New Crop
    </button>
</div>

<div class="card table-wrapper">
    <table id="cropsTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Crop Name</th>
                <th>Ideal pH</th>
                <th>Ideal Temp (°C)</th>
                <th>Seasons</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr><td colspan="6" style="text-align: center;">Loading crops...</td></tr>
        </tbody>
    </table>
</div>

<!-- Modal for Add/Edit Crop -->
<div id="cropModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalTitle">Add New Crop</h2>
            <button class="close-modal" onclick="closeCropModal()">&times;</button>
        </div>
        <form id="cropForm">
            <input type="hidden" id="crop_id">
            
            <div class="form-group">
                <label>Crop Name *</label>
                <input type="text" id="crop_name" required placeholder="e.g. Tomato">
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label>Scientific Name</label>
                    <input type="text" id="scientific_name">
                </div>
                <div class="form-group">
                    <label>Water Requirement</label>
                    <input type="text" id="water_req" placeholder="e.g. High, Low, 500mm">
                </div>
            </div>

            <h3 style="margin: 1.5rem 0 1rem; font-size: 1.1rem; color: var(--primary-dark); border-bottom: 1px solid var(--border); padding-bottom: 0.5rem;">AI Recommendation Parameters</h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group"><label>Min pH</label><input type="number" step="0.1" id="ph_min"></div>
                <div class="form-group"><label>Max pH</label><input type="number" step="0.1" id="ph_max"></div>
                <div class="form-group"><label>Min Temp (°C)</label><input type="number" step="0.1" id="temp_min"></div>
                <div class="form-group"><label>Max Temp (°C)</label><input type="number" step="0.1" id="temp_max"></div>
                <div class="form-group"><label>Min Rainfall (mm)</label><input type="number" step="0.1" id="rain_min"></div>
                <div class="form-group"><label>Max Rainfall (mm)</label><input type="number" step="0.1" id="rain_max"></div>
                <div class="form-group"><label>Min Nitrogen (N)</label><input type="number" step="0.1" id="n_min"></div>
                <div class="form-group"><label>Max Nitrogen (N)</label><input type="number" step="0.1" id="n_max"></div>
            </div>
            
            <div class="form-group">
                <label>Seasons</label>
                <input type="text" id="seasons" placeholder="e.g. Kharif, Rabi">
                <small style="color: var(--text-light); font-size: 0.8rem;">Comma separated</small>
            </div>
            <div class="form-group">
                <label>States</label>
                <input type="text" id="states" placeholder="e.g. All OR Punjab,Haryana">
                <small style="color: var(--text-light); font-size: 0.8rem;">Comma separated</small>
            </div>
            
            <div style="margin-top: 2rem; display: flex; justify-content: flex-end; gap: 1rem;">
                <button type="button" class="btn" style="background: white; color: var(--text); border: 1px solid var(--border);" onclick="closeCropModal()">Cancel</button>
                <button type="submit" class="btn" id="saveBtn">Save Crop</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', loadCrops);

    let cropsData = [];

    async function loadCrops() {
        const tbody = document.querySelector('#cropsTable tbody');
        try {
            const res = await fetch('../api/admin_crops.php?action=list');
            const result = await res.json();
            
            if (result.status === 'success') {
                cropsData = result.data;
                tbody.innerHTML = '';
                if(cropsData.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" style="text-align: center;">No crops found.</td></tr>';
                    return;
                }
                
                cropsData.forEach(crop => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>#${crop.id}</td>
                        <td style="font-weight: 500;">${crop.crop_name}</td>
                        <td>${crop.ph_min || '?'} - ${crop.ph_max || '?'}</td>
                        <td>${crop.temp_min || '?'} - ${crop.temp_max || '?'}</td>
                        <td>${crop.seasons || 'All'}</td>
                        <td>
                            <button class="btn btn-sm" style="margin-right: 0.5rem; background: #3b82f6;" onclick="editCrop(${crop.id})">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteCrop(${crop.id})">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            }
        } catch(e) {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; color: red;">Failed to load crops.</td></tr>';
        }
    }

    function openCropModal() {
        document.getElementById('cropForm').reset();
        document.getElementById('crop_id').value = '';
        document.getElementById('modalTitle').textContent = 'Add New Crop';
        document.getElementById('saveBtn').innerHTML = 'Save Crop';
        document.getElementById('cropModal').classList.add('active');
    }

    function closeCropModal() {
        document.getElementById('cropModal').classList.remove('active');
    }

    function editCrop(id) {
        const crop = cropsData.find(c => c.id == id);
        if(!crop) return;
        
        document.getElementById('crop_id').value = crop.id;
        document.getElementById('crop_name').value = crop.crop_name;
        document.getElementById('scientific_name').value = crop.scientific_name || '';
        document.getElementById('water_req').value = crop.water_req || '';
        document.getElementById('ph_min').value = crop.ph_min || '';
        document.getElementById('ph_max').value = crop.ph_max || '';
        document.getElementById('temp_min').value = crop.temp_min || '';
        document.getElementById('temp_max').value = crop.temp_max || '';
        document.getElementById('rain_min').value = crop.rain_min || '';
        document.getElementById('rain_max').value = crop.rain_max || '';
        document.getElementById('n_min').value = crop.n_min || '';
        document.getElementById('n_max').value = crop.n_max || '';
        document.getElementById('seasons').value = crop.seasons || '';
        document.getElementById('states').value = crop.states || '';
        
        document.getElementById('modalTitle').textContent = 'Edit Crop';
        document.getElementById('saveBtn').innerHTML = 'Update Crop';
        document.getElementById('cropModal').classList.add('active');
    }

    document.getElementById('cropForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const id = document.getElementById('crop_id').value;
        const action = id ? 'update' : 'create';
        
        const payload = {
            action: action,
            id: id,
            crop_name: document.getElementById('crop_name').value,
            scientific_name: document.getElementById('scientific_name').value,
            water_req: document.getElementById('water_req').value,
            ph_min: document.getElementById('ph_min').value,
            ph_max: document.getElementById('ph_max').value,
            temp_min: document.getElementById('temp_min').value,
            temp_max: document.getElementById('temp_max').value,
            rain_min: document.getElementById('rain_min').value,
            rain_max: document.getElementById('rain_max').value,
            n_min: document.getElementById('n_min').value,
            n_max: document.getElementById('n_max').value,
            seasons: document.getElementById('seasons').value,
            states: document.getElementById('states').value
        };

        const btn = document.getElementById('saveBtn');
        const oldText = btn.innerHTML;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving...';

        try {
            const res = await fetch('../api/admin_crops.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            if(data.status === 'success') {
                closeCropModal();
                loadCrops();
            } else {
                alert(data.message);
            }
        } catch(e) {
            alert("Error saving crop.");
        }
        btn.innerHTML = oldText;
    });

    async function deleteCrop(id) {
        if(confirm(`Are you sure you want to delete crop #${id}? It will be removed from the AI recommendations.`)) {
            try {
                const res = await fetch('../api/admin_crops.php?action=delete', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({id})
                });
                const data = await res.json();
                if(data.status === 'success') {
                    loadCrops();
                } else {
                    alert(data.message);
                }
            } catch(e) {
                alert("Error deleting crop.");
            }
        }
    }
</script>

<?php require_once 'layout_footer.php'; ?>
