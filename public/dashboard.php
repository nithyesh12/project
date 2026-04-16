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
    <title>Farmer Dashboard - Grow Your Crops India</title>
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,600;1,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        /* New Custom Dashboard Specific Structural CSS */
        :root {
            --sidebar-width: 250px;
            --header-height: 70px;
        }
        body {
            background-color: var(--bg-main);
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            display: flex; /* Override global body if needed */
        }
        
        /* Sidebar Styling */
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
        
        /* Main Content Wrapper */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        /* Top Navbar Header */
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
        
        /* Dashboard Container Areas */
        .dashboard-container {
            padding: 2rem;
            flex-grow: 1;
        }
        
        /* Quick Stats Layout */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-card-custom {
            background: white;
            border-radius: var(--border-radius-lg);
            padding: 1.5rem;
            box-shadow: var(--box-shadow);
            display: flex;
            align-items: center;
            gap: 1rem;
            border: 1px solid var(--border-color);
            transition: transform 0.3s;
        }
        .stat-card-custom:hover { transform: translateY(-3px); box-shadow: var(--box-shadow-hover); }
        .stat-icon-wrapper {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        .stat-info span { font-size: 0.85rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; }
        .stat-info h3 { margin: 0; font-size: 1.5rem; color: var(--text-main); font-family: 'Outfit', sans-serif;}
        
        /* Content Grids */
        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
        }
        
        @media (max-width: 992px) {
            .dashboard-grid { grid-template-columns: 1fr; }
            .sidebar { transform: translateX(-100%); transition: 0.3s; }
            .main-wrapper { margin-left: 0; width: 100%; }
        }
    </style>
</head>
<body>

    <!-- 1. Contextual Sidebar Navigation Layout -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            <i class="fa-solid fa-leaf"></i>
            <span>GrowYourCrops</span>
        </div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php" class="active"><i class="fa-solid fa-house"></i> Dashboard</a></li>
            <li><a href="cosmetic_uses.php"><i class="fa-solid fa-spa"></i> Cosmetic Uses</a></li>
            <li><a href="crops.php"><i class="fa-solid fa-book-open"></i> Crop Encyclopedia</a></li>
            <li><a href="recommendation.php"><i class="fa-solid fa-wand-magic-sparkles"></i> Recommendations</a></li>
            <li><a href="myfarm.php"><i class="fa-solid fa-tractor"></i> My Farm</a></li>
            <li><a href="smart_farming.php"><i class="fa-solid fa-microchip"></i> Smart Modules</a></li>
            <!-- Dedicated Weather Link explicit payload integration -->
            <li><a href="weather.php" style="color: #60a5fa;"><i class="fa-solid fa-cloud-sun"></i> Weather Insights</a></li>
        </ul>
        <div style="padding: 1.5rem; border-top: 1px solid rgba(255,255,255,0.1);">
            <a href="javascript:void(0)" onclick="logout()" style="color: #fca5a5; display:flex; align-items:center; gap:10px; text-decoration:none;"><i class="fa-solid fa-arrow-right-from-bracket"></i> Sign Out</a>
        </div>
    </aside>

    <!-- 2. Main Content Wrapper -->
    <div class="main-wrapper">
        
        <!-- Top Navbar Header Native Format -->
        <header class="top-navbar">
            <div style="display:flex; align-items:center; gap:1rem;">
                <h2 style="margin:0; font-family:'Outfit'; font-size:1.25rem; font-weight:600;"><i class="fa-solid fa-chart-line" style="color:var(--text-muted);"></i> Farm Overview</h2>
            </div>
            <div class="top-nav-right">
                <i class="fa-solid fa-bell" style="font-size:1.2rem; color:var(--text-muted); cursor:pointer;"></i>
                <div class="user-profile">
                    <img src="https://ui-avatars.com/api/?name=User&background=059669&color=fff" id="nav-avatar" alt="Avatar">
                    <span id="nav-name" style="font-weight: 500;">Farmer</span>
                </div>
                <button onclick="logout()" class="btn btn-outline" style="padding: 0.4rem 1rem; font-size: 0.85rem;"><i class="fa-solid fa-power-off"></i></button>
            </div>
        </header>

        <!-- Main Workspace -->
        <div class="dashboard-container">
            
            <!-- Welcome Presentation -->
            <div style="margin-bottom: 2rem;">
                <h1 style="font-size: 2rem; margin-bottom: 0.25rem;">Welcome, <span id="welcome-name" class="highlight">Farmer</span>!</h1>
                <p style="color: var(--text-muted); margin:0;" id="current-date">Fetching current timeline...</p>
            </div>

            <!-- Quick Data Statistics Native -->
            <div class="stats-grid">
                <div class="stat-card-custom">
                    <div class="stat-icon-wrapper" style="background: #dcfce7; color: #16a34a;">
                        <i class="fa-solid fa-seedling"></i>
                    </div>
                    <div class="stat-info">
                        <span>Crops Analyzed & Saved</span>
                        <h3 id="stat-crops-saved">0</h3>
                    </div>
                </div>
                <!-- Dynamic Seasonal Detection Output -->
                <div class="stat-card-custom">
                    <div class="stat-icon-wrapper" style="background: #fef9c3; color: #ca8a04;">
                        <i class="fa-solid fa-sun-plant-wilt"></i>
                    </div>
                    <div class="stat-info">
                        <span>Current Active Season</span>
                        <h3 id="current-season">Loading...</h3>
                    </div>
                </div>
                <!-- Mini Weather Overview -->
                <div class="stat-card-custom">
                    <div class="stat-icon-wrapper" style="background: #e0f2fe; color: #0284c7;">
                        <i class="fa-solid fa-temperature-half"></i>
                    </div>
                    <div class="stat-info">
                        <span>Regional Heat Preview</span>
                        <h3><span id="preview-temp">--</span>°C</h3>
                    </div>
                </div>
            </div>

            <div class="dashboard-grid">
                
                <!-- Left Hand Priority Display Matrix -->
                <div style="display: flex; flex-direction: column; gap: 2rem;">
                    
                    <!-- Formatted AI Analyzed Recommendations Grid -->
                    <div class="glass-panel" style="background: white; padding: 1.5rem; border-radius: var(--border-radius-lg); box-shadow: var(--box-shadow);">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 1.5rem;">
                            <h3 style="margin:0;"><i class="fa-solid fa-leaf" style="color:var(--primary-color);"></i> Saved Recommended Crops</h3>
                            <a href="recommendation.php" class="btn btn-outline" style="padding:0.4rem 1rem; font-size:0.85rem;">Run New Analysis</a>
                        </div>
                        <div id="recommended-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                            <!-- Backend payload securely parsed by JS below -->
                            <p style="color:var(--text-muted);">Fetching your personalized agricultural parameters...</p>
                        </div>
                    </div>

                    <!-- Chronological Recent App Actions Log -->
                    <div class="glass-panel" style="background: white; padding: 1.5rem; border-radius: var(--border-radius-lg); box-shadow: var(--box-shadow);">
                        <h3 style="margin-bottom: 1.5rem;"><i class="fa-solid fa-clock-rotate-left" style="color:var(--text-muted);"></i> Recent Farm Activity</h3>
                        <div id="activity-list" style="display:flex; flex-direction:column; gap:1rem;">
                            <!-- Activity dynamically generated directly connected to API output -->
                        </div>
                    </div>

                </div>

                <!-- Right Side Widget Layer -->
                <div style="display: flex; flex-direction: column; gap: 2rem;">
                    
                    <!-- Advanced Open-Meteo Weather Integration Native Callout -->
                    <div style="background: linear-gradient(135deg, #0284c7, #38bdf8); border-radius: var(--border-radius-lg); padding: 2.5rem 2rem; color: white; box-shadow: 0 10px 15px -3px rgba(2, 132, 199, 0.4); text-align: center; position: relative; overflow: hidden;">
                        <i class="fa-solid fa-cloud-showers-water" style="position:absolute; right:-30px; top:-30px; font-size:10rem; opacity:0.1; transform:rotate(-15deg);"></i>
                        <h3 style="color:white; font-size:1.5rem; margin-bottom:1rem; position:relative; z-index:1;">Check Weather Forecast</h3>
                        <p style="margin-bottom:2rem; opacity:0.9; position:relative; z-index:1; line-height: 1.6;">Leverage the robust new Geocoding Data Engine to secure precise 6-day multi-precipitation telemetry specifically designed for farmers.</p>
                        <a href="weather.php" class="btn" style="background: white; color: #0284c7; width:100%; font-weight:bold; position:relative; z-index:1; font-size: 1.1rem; padding: 0.8rem 1rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                            View Detailed Weather <i class="fa-solid fa-arrow-right" style="margin-left:0.5rem;"></i>
                        </a>
                    </div>
                    
                </div>
            </div>

        </div>
    </div>

    <!-- Central Authentication & Processing Script Layer -->
    <script>
        // 1. Instantly parse Current Date parameters
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        document.getElementById('current-date').innerText = new Date().toLocaleDateString('en-US', options);

        // 2. Mathematically evaluate structural Indian agricultural season
        const activeMonth = new Date().getMonth(); 
        let calcSeason = "Zaid (Summer)"; 
        if(activeMonth >= 6 && activeMonth <= 9) calcSeason = "Kharif (Monsoon)"; 
        if(activeMonth >= 10 || activeMonth <= 2) calcSeason = "Rabi (Winter)";  
        document.getElementById('current-season').innerText = calcSeason;

        // 3. Complete authentication disconnect script
        async function logout() {
            try {
                await fetch('api/auth.php', { method: 'POST', body: JSON.stringify({action: 'logout'}) });
                window.location.href = 'index.html';
            } catch(e) { console.error("Secure logout protocol failed."); }
        }

        document.addEventListener('DOMContentLoaded', async () => {
            // Confirm encrypted local session explicitly
            try {
                const sessionRes = await fetch('api/auth.php?action=session');
                // Return users directly to portal if unauthenticated
                if(!sessionRes.ok) { window.location.href = 'auth.html'; return; }
                const sessionData = await sessionRes.json();
                
                document.getElementById('welcome-name').innerText = sessionData.user.first_name;
                document.getElementById('nav-name').innerText = sessionData.user.first_name;
                document.getElementById('nav-avatar').src = `https://ui-avatars.com/api/?name=${sessionData.user.first_name}&background=059669&color=fff`;
            } catch(e) { console.error("Session fetch rejected."); }

            // Pull latest JSON parameters from securely mapped backend DB
            try {
                const res = await fetch('api/records.php');
                const data = await res.json();
                
                const cropGrid = document.getElementById('recommended-grid');
                const historyList = document.getElementById('activity-list');
                
                if(data.records && data.records.length > 0) {
                    // Update main statistics natively
                    document.getElementById('stat-crops-saved').innerText = data.records.length;
                    
                    cropGrid.innerHTML = '';
                    historyList.innerHTML = '';
                    
                    // Filter crop layout sequentially mapping directly to CSS variables
                    const gridItems = data.records.slice(0, 4);
                    gridItems.forEach((rec, idx) => {
                        let dynamicBorder = idx % 2 === 0 ? "var(--primary-light)" : "var(--secondary-color)";
                        cropGrid.innerHTML += `
                            <div style="border-left:4px solid ${dynamicBorder}; border-radius:8px; padding:1.2rem; background:#f8fafc; box-shadow:0 1px 3px rgba(0,0,0,0.05); transition:0.3s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                                <h4 style="color:var(--primary-dark); margin-bottom:0.75rem;"><i class="fa-solid fa-wheat-awn" style="color:${dynamicBorder}"></i> ${rec.recommended_crop}</h4>
                                <div style="font-size:0.85rem; color:var(--text-muted); display:flex; flex-direction:column; gap:6px;">
                                    <span style="background:white; padding:0.25rem 0.5rem; border-radius:4px; border:1px solid #e2e8f0;"><i class="fa-solid fa-vial" style="color:#64748b;"></i> Soil Type / pH: <strong>${rec.soil_ph}</strong></span>
                                    <span style="background:white; padding:0.25rem 0.5rem; border-radius:4px; border:1px solid #e2e8f0;"><i class="fa-solid fa-cloud-sun" style="color:#f59e0b;"></i> Regional Target: <strong>${rec.state || 'Local Zone'}</strong></span>
                                </div>
                            </div>
                        `;
                    });

                    // Update chronometric history tables natively
                    const historyItems = data.records.slice(0, 5);
                    historyItems.forEach(rec => {
                        const dateObj = new Date(rec.created_at);
                        const formattingScale = dateObj.toLocaleDateString('en-US', {month:'short', day:'numeric', year:'numeric'});
                        
                        historyList.innerHTML += `
                            <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #f1f5f9; padding-bottom:1rem;">
                                <div style="display:flex; align-items:center; gap:1rem;">
                                    <div style="width:40px; height:40px; border-radius:50%; background:#ecfdf5; color:#059669; display:flex; align-items:center; justify-content:center;">
                                        <i class="fa-solid fa-server"></i>
                                    </div>
                                    <div>
                                        <h5 style="margin:0; font-family:'Outfit'; font-size:1rem; color:var(--text-main);">Soil & ML Analysis Ran</h5>
                                        <p style="margin:0; font-size:0.85rem; color:var(--text-muted);"><span style="color:var(--primary-color); font-weight:600;">Engine Identified:</span> ${rec.recommended_crop}</p>
                                    </div>
                                </div>
                                <span style="font-size:0.8rem; color:#94a3b8; font-weight:500;">${formattingScale}</span>
                            </div>
                        `;
                    });

                } else {
                    cropGrid.innerHTML = '<div style="grid-column: 1 / -1; padding: 2rem; text-align: center; background: #f8fafc; border-radius: 8px; border: 1px dashed #cbd5e1;"><i class="fa-solid fa-triangle-exclamation" style="font-size: 2rem; color: #94a3b8; margin-bottom: 1rem;"></i><p style="color:var(--text-muted); margin:0;">No structural crop parameters saved yet. Run a new analysis on the recommendation page to permanently store outputs here!</p></div>';
                    historyList.innerHTML = '<p style="color:var(--text-muted);">No recent analysis logs detected.</p>';
                }
            } catch(e) { console.error("Database connection array explicitly closed or inactive."); }

            // Simulated ambient weather telemetry preview natively
            setTimeout(() => { document.getElementById('preview-temp').innerText = "27"; }, 600);
        });
    </script>
</body>
</html>
