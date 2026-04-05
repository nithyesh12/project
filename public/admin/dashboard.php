<?php require_once 'layout_header.php'; ?>

<div class="header">
    <div>
        <h1>Dashboard Overview</h1>
        <p style="color: var(--text-light); margin-top: 0.5rem;">System monitoring and platform statistics</p>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <div class="card" style="display: flex; align-items: center; gap: 1.5rem;">
        <div style="background: rgba(16, 185, 129, 0.1); padding: 1rem; border-radius: 50%; color: var(--primary);">
            <i class="fa-solid fa-users fa-2x"></i>
        </div>
        <div>
            <h3 style="font-size: 2rem; color: #1e293b;" id="totalUsers">-</h3>
            <p style="color: var(--text-light); font-weight: 500;">Total Registered Users</p>
        </div>
    </div>
    
    <div class="card" style="display: flex; align-items: center; gap: 1.5rem;">
        <div style="background: rgba(59, 130, 246, 0.1); padding: 1rem; border-radius: 50%; color: #3b82f6;">
            <i class="fa-solid fa-wheat-awn fa-2x"></i>
        </div>
        <div>
            <h3 style="font-size: 2rem; color: #1e293b;" id="totalCrops">-</h3>
            <p style="color: var(--text-light); font-weight: 500;">Total Documented Crops</p>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', async () => {
        try {
            const res = await fetch('../api/admin_users.php?action=stats');
            const data = await res.json();
            if(data.status === 'success') {
                document.getElementById('totalUsers').textContent = data.users;
                document.getElementById('totalCrops').textContent = data.crops;
            }
        } catch(e) {
            console.error("Failed to load stats");
        }
    });
</script>

<?php require_once 'layout_footer.php'; ?>
