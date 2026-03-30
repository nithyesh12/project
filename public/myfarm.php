<?php
// Secure session settings
ini_set('session.use_only_cookies', 1);
ini_set('session.use_strict_mode', 1);
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => isset($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Strict'
]);
session_start();

// Disable caching to prevent back-button access
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Ensure unauthorized users are redirected
if (!isset($_SESSION['user_id'])) {
    header("Location: auth.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Farm Operations - Grow Your Crops India</title>
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,600;1,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        /* Inheriting Structural CSS from Dashboard Architecture */
        :root {
            --sidebar-width: 250px;
            --header-height: 70px;
        }
        body {
            background-color: var(--bg-main);
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            display: flex;
        }
        
        .sidebar {
            width: var(--sidebar-width);
            background-color: var(--primary-dark);
            color: white;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            display: flex;
            flex-direction: column;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        .sidebar-logo {
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.25rem;
            font-family: 'Playfair Display', serif;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar-logo i { color: var(--accent-color); font-size: 1.5rem; }
        .sidebar-menu {
            list-style: none;
            padding: 1.5rem 0;
            margin: 0;
            flex-grow: 1;
        }
        .sidebar-menu li a {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 1rem 1.5rem;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        .sidebar-menu li a:hover, .sidebar-menu li a.active {
            background-color: rgba(255,255,255,0.1);
            color: white;
            border-left: 4px solid var(--accent-color);
        }
        .sidebar-menu li a i { width: 20px; text-align: center; }
        
        .main-wrapper {
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .top-navbar {
            height: var(--header-height);
            background: white;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 900;
        }
        .top-nav-right {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }
        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .user-profile img {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            border: 2px solid var(--border-color);
        }
        
        .dashboard-container {
            padding: 2.5rem;
            flex-grow: 1;
        }

        /* --------------------------------------
           Farm Asset Card Grid Specific UI
        --------------------------------------- */
        .farm-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 2rem;
            margin-top: 1.5rem;
        }
        
        .farm-card {
            background: white;
            border-radius: var(--border-radius-lg);
            box-shadow: var(--box-shadow);
            border: 1px solid var(--border-color);
            transition: transform 0.3s, box-shadow 0.3s;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        
        .farm-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--box-shadow-hover);
        }
        
        .farm-card-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
        }

        /* Standard gradient for top bar */
        .header-bg-analyzed { background: linear-gradient(135deg, #e0e7ff, #c7d2fe); }
        .header-bg-planted { background: linear-gradient(135deg, #dcfce7, #bbf7d0); }
        .header-bg-harvested { background: linear-gradient(135deg, #f1f5f9, #e2e8f0); }
        
        .farm-card-body {
            padding: 1.25rem 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            flex-grow: 1;
        }
        
        .farm-stat-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.9rem;
            color: var(--text-muted);
            align-items: center;
        }
        
        .farm-card-footer {
            padding: 1rem 1.5rem;
            background: #f8fafc;
            border-top: 1px dashed var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .badge {
            padding: 0.35rem 0.75rem;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .badge-analyzed { background: white; color: #4f46e5; border: 1px solid #4f46e5; }
        .badge-planted { background: white; color: #059669; border: 1px solid #059669; }
        .badge-harvested { background: white; color: #64748b; border: 1px solid #64748b; }
        
        /* Action buttons layout */
        .btn-action {
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-advance { background: var(--primary-color); color: white; }
        .btn-advance:hover { background: var(--primary-dark); }
        
        .btn-delete { background: white; color: #ef4444; border: 1px solid #fee2e2; }
        .btn-delete:hover { background: #fef2f2; border-color: #ef4444; }

        @media (max-width: 992px) {
            .sidebar { transform: translateX(-100%); transition: 0.3s; }
            .main-wrapper { margin-left: 0; width: 100%; }
        }
    </style>
</head>
<body>

    <!-- 1. Left Sidebar Navigation -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            <i class="fa-solid fa-leaf"></i>
            <span>GrowYourCrops</span>
        </div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="fa-solid fa-house"></i> Dashboard</a></li>
            <li><a href="crops.php"><i class="fa-solid fa-book-open"></i> Crop Encyclopedia</a></li>
            <li><a href="recommendation.php"><i class="fa-solid fa-wand-magic-sparkles"></i> Recommendations</a></li>
            <li><a href="myfarm.php" class="active"><i class="fa-solid fa-tractor"></i> My Farm</a></li>
            <li><a href="weather.php" style="color: #60a5fa;"><i class="fa-solid fa-cloud-sun"></i> Weather Insights</a></li>
        </ul>
        <div style="padding: 1.5rem; border-top: 1px solid rgba(255,255,255,0.1);">
            <a href="javascript:void(0)" onclick="logout()" style="color: #fca5a5; display:flex; align-items:center; gap:10px; text-decoration:none;"><i class="fa-solid fa-arrow-right-from-bracket"></i> Sign Out</a>
        </div>
    </aside>

    <!-- 2. Main Content Wrapper -->
    <div class="main-wrapper">
        
        <!-- Top Navbar Header -->
        <header class="top-navbar">
            <div style="display:flex; align-items:center; gap:1rem;">
                <button class="btn btn-outline" style="border:none; padding:0.5rem;" id="mobile-toggle"><i class="fa-solid fa-bars"></i></button>
                <h2 style="margin:0; font-family:'Outfit'; font-size:1.25rem; font-weight:600;"><i class="fa-solid fa-tractor" style="color:var(--text-muted);"></i> Farm Operations</h2>
            </div>
            <div class="top-nav-right">
                <i class="fa-solid fa-bell" style="font-size:1.2rem; color:var(--text-muted); cursor:pointer;"></i>
                <div class="user-profile">
                    <img src="https://ui-avatars.com/api/?name=User&background=059669&color=fff" id="nav-avatar" alt="Avatar">
                    <span id="nav-name" style="font-weight: 500;">Farmer</span>
                </div>
            </div>
        </header>

        <!-- Dashboard Workspace -->
        <div class="dashboard-container">
            
            <div style="display:flex; justify-content:space-between; align-items:flex-end; border-bottom: 2px solid var(--border-color); padding-bottom: 1rem;">
                <div>
                    <h1 style="font-size: 2.2rem; margin-bottom: 0.5rem; color:var(--primary-dark);">My Planted Yields</h1>
                    <p style="color: var(--text-muted); margin:0; font-size: 1.1rem;">Manage the lifecycle of your real-world agricultural operations directly mapped to AI recommendations.</p>
                </div>
                <a href="recommendation.php" class="btn btn-primary" style="padding:0.75rem 1.5rem;"><i class="fa-solid fa-plus"></i> Audit New Plot</a>
            </div>

            <!-- Filter Pipeline (Visual only for now, could act via JS filter) -->
            <div style="margin-top:2rem; display:flex; gap:1rem; flex-wrap:wrap;">
                <span style="font-weight:600; color:var(--text-muted); padding-top:0.4rem;">Filter Status:</span>
                <button class="btn btn-outline" style="background:#e2e8f0; color:#475569; border:none;" onclick="filterOperations('All')">All Operations</button>
                <button class="btn btn-outline" style="border-color:#4f46e5; color:#4f46e5;" onclick="filterOperations('Analyzed')">Pending Planting</button>
                <button class="btn btn-outline" style="border-color:#059669; color:#059669;" onclick="filterOperations('Planted')">Harvest Expected</button>
                <button class="btn btn-outline" style="border-color:#64748b; color:#64748b;" onclick="filterOperations('Harvested')">Archived Outputs</button>
            </div>

            <div class="farm-grid" id="farm-grid">
                <p style="color:var(--text-muted); font-size:1.1rem;">Loading synchronization modules from backend database...</p>
                <!-- Dynamically Populated with Cards -->
            </div>

        </div>
    </div>

    <script>
        let GLOBAL_RECORDS = [];

        async function logout() {
            try {
                await fetch('api/auth.php', { method: 'POST', body: JSON.stringify({action: 'logout'}) });
                window.location.href = 'index.html';
            } catch(e) { console.error("Logout error"); }
        }

        async function loadProfile() {
            try {
                const sessionRes = await fetch('api/auth.php?action=session');
                if(!sessionRes.ok) { window.location.href = 'auth.html'; return; }
                const sessionData = await sessionRes.json();
                document.getElementById('nav-name').innerText = sessionData.user.first_name;
                document.getElementById('nav-avatar').src = `https://ui-avatars.com/api/?name=${sessionData.user.first_name}&background=059669&color=fff`;
            } catch(e) {}
        }

        async function fetchFarmOperations() {
            try {
                const res = await fetch('api/records.php');
                const data = await res.json();
                
                const grid = document.getElementById('farm-grid');
                
                if(data.records && data.records.length > 0) {
                    GLOBAL_RECORDS = data.records;
                    renderCards(data.records);
                } else {
                    grid.innerHTML = `
                        <div style="grid-column: 1 / -1; padding: 4rem 2rem; text-align: center; background: white; border-radius: var(--border-radius-lg); border: 1px dashed #cbd5e1; box-shadow: var(--box-shadow);">
                            <i class="fa-solid fa-wheat-awn-circle-exclamation" style="font-size: 3.5rem; color: #94a3b8; margin-bottom: 1.5rem;"></i>
                            <h2 style="margin-bottom:0.5rem; color:var(--text-main);">No Agricultural Operations Found</h2>
                            <p style="color:var(--text-muted); max-width:500px; margin: 0 auto 1.5rem;">You have not analyzed or mapped any physical farm plots. Execute an AI sequence over in Recommendations to automatically permanently trace your land parameters here.</p>
                            <a href="recommendation.php" class="btn btn-primary">Start Intelligence Analysis</a>
                        </div>`;
                }
            } catch(e) { console.error("Database disconnected"); }
        }

        function renderCards(recordsArray) {
            const grid = document.getElementById('farm-grid');
            grid.innerHTML = '';

            recordsArray.forEach(rec => {
                const dateObj = new Date(rec.created_at);
                const stringDate = dateObj.toLocaleDateString('en-US', {month:'long', day:'numeric', year:'numeric'});
                
                // Establish contextual status formats
                let currentStatus = rec.status || 'Analyzed';
                let headerClass = 'header-bg-analyzed';
                let badgeClass = 'badge-analyzed';
                let dynamicButtons = '';
                let statusIcon = 'fa-microchip';
                
                if(currentStatus === 'Planted') {
                    headerClass = 'header-bg-planted';
                    badgeClass = 'badge-planted';
                    statusIcon = 'fa-seedling';
                    dynamicButtons = `
                        <button class="btn-action btn-advance" onclick="updateOperationPhase(${rec.id}, 'Harvested')">
                            <i class="fa-solid fa-basket-shopping"></i> Mark Harvested
                        </button>
                    `;
                } else if(currentStatus === 'Harvested') {
                    headerClass = 'header-bg-harvested';
                    badgeClass = 'badge-harvested';
                    statusIcon = 'fa-box-open';
                    dynamicButtons = `
                        <span style="color:var(--text-muted); font-size:0.85rem;"><i class="fa-solid fa-check"></i> Crop Cycle Finished</span>
                    `;
                } else {
                    // Default to Analyzed/Pending
                    currentStatus = 'Analyzed';
                    dynamicButtons = `
                        <button class="btn-action btn-advance" onclick="updateOperationPhase(${rec.id}, 'Planted')">
                            <i class="fa-solid fa-seedling"></i> Mark Planted
                        </button>
                    `;
                }

                grid.innerHTML += `
                    <div class="farm-card" data-status="${currentStatus}">
                        <div class="farm-card-header ${headerClass}">
                            <div style="display:flex; align-items:center; gap:0.75rem;">
                                <i class="fa-solid fa-wheat-awn" style="font-size:1.5rem; color:rgba(0,0,0,0.6);"></i>
                                <h3 style="margin:0; font-family:'Outfit'; font-size:1.3rem; color:rgba(0,0,0,0.8); font-weight:700;">${rec.recommended_crop}</h3>
                            </div>
                            <span class="badge ${badgeClass}"><i class="fa-solid ${statusIcon}"></i> ${currentStatus}</span>
                        </div>
                        <div class="farm-card-body">
                            <div class="farm-stat-row">
                                <span><i class="fa-solid fa-vial" style="width:20px;"></i> Soil Acidity (pH)</span>
                                <span style="font-weight:600; color:var(--text-main);">${rec.soil_ph}</span>
                            </div>
                            <div class="farm-stat-row">
                                <span><i class="fa-solid fa-sun-plant-wilt" style="width:20px;"></i> Heat Resistance</span>
                                <span style="font-weight:600; color:var(--text-main);">${rec.temperature}°C</span>
                            </div>
                            <div class="farm-stat-row">
                                <span><i class="fa-solid fa-cloud-showers-water" style="width:20px;"></i> Regional Rainfall</span>
                                <span style="font-weight:600; color:var(--text-main);">${rec.rainfall} mm</span>
                            </div>
                            <div class="farm-stat-row" style="margin-top:0.5rem; padding-top:0.5rem; border-top:1px solid #f1f5f9;">
                                <span><i class="fa-solid fa-map-location-dot" style="width:20px;"></i> Topography</span>
                                <span style="font-weight:600; color:var(--primary-dark);">${rec.state || 'Local Zone'}</span>
                            </div>
                            <div class="farm-stat-row">
                                <span><i class="fa-regular fa-calendar" style="width:20px;"></i> Registered</span>
                                <span style="font-weight:500; font-size:0.8rem;">${stringDate}</span>
                            </div>
                        </div>
                        <div class="farm-card-footer">
                            <div style="display:flex; gap:0.5rem;">
                                ${dynamicButtons}
                            </div>
                            <button class="btn-action btn-delete" title="Delete Operational Entry" onclick="deleteOperation(${rec.id})">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
            });
        }

        function filterOperations(filterType) {
            if(filterType === 'All') {
                renderCards(GLOBAL_RECORDS);
            } else {
                const subset = GLOBAL_RECORDS.filter(x => {
                    const status = x.status || 'Analyzed';
                    return status === filterType;
                });
                renderCards(subset);
            }
        }

        async function updateOperationPhase(recordId, newStatus) {
            try {
                const res = await fetch('api/records.php', {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: recordId, status: newStatus })
                });
                const data = await res.json();
                if(data.status === 'success') {
                    // Instantly refresh layout dynamically
                    fetchFarmOperations();
                } else {
                    alert("Operation failed due to Server integrity validation.");
                }
            } catch(e) { console.error("Put sequence failed.", e); }
        }

        async function deleteOperation(recordId) {
            if(confirm("Are you absolutely sure you want to permanently delete this farm plot architecture?")) {
                try {
                    const res = await fetch('api/records.php', {
                        method: 'DELETE',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id: recordId })
                    });
                    const data = await res.json();
                    if(data.status === 'success') {
                        fetchFarmOperations(); // Re-render matrix
                    }
                } catch(e) { console.error("Delete sequence failed.", e); }
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            loadProfile();
            fetchFarmOperations();
        });
    </script>
</body>
</html>
