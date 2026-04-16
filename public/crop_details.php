<?php
ini_set('session.use_only_cookies', 1);
session_start();
$is_logged_in = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crop Details - Grow Your Crops India</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,600;1,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .details-header {
            position: relative; height: 350px; overflow: hidden; border-radius: var(--border-radius-lg); margin-bottom: 2rem;
            box-shadow: var(--box-shadow);
        }
        .details-header img {
            width: 100%; height: 100%; object-fit: cover; filter: brightness(0.6);
        }
        .header-text {
            position: absolute; bottom: 2rem; left: 2rem; color: white;
        }
        .header-text h1 { color: white; font-size: 3.5rem; margin-bottom: 0; }
        .detail-card {
            background: var(--bg-surface); padding: 2rem; border-radius: var(--border-radius-lg);
            box-shadow: var(--box-shadow); margin-bottom: 2rem; border: 1px solid var(--border-color);
        }
        .detail-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;
        }
        .info-block h4 { color: var(--primary-color); font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; }
        .info-block p { font-size: 1.1rem; font-weight: 500; color: var(--text-main); }
        .back-btn { margin-bottom: 1rem; display: inline-flex; align-items: center; gap: 0.5rem; color: var(--text-muted); font-weight: 600; transition: color 0.2s; }
        .back-btn:hover { color: var(--primary-color); }
    </style>
</head>
<body style="padding-top: 80px; background-color: var(--bg-main);">
    
    <nav class="navbar scrolled" id="navbar">
        <div class="container nav-container">
            <a href="index.html" class="logo">
                <i class="fa-solid fa-leaf"></i>
                <span>GrowYourCrops<span class="highlight">India</span></span>
            </a>
            <ul class="nav-links">
                <li><a href="index.html">Home</a></li>
                <li><a href="crops.php" class="active">Crop Encyclopedia</a></li>
                <li><a href="recommendation.php">AI Recommendation</a></li>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="cosmetic_uses.php">Cosmetic Uses</a></li>
                
                <?php if($is_logged_in): ?>
                <li>
                    <button onclick="logout()" class="btn btn-outline" style="padding: 0.4rem 1rem; font-size: 0.85rem;">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
                    </button>
                </li>
                <?php else: ?>
                <li><a href="auth.html" class="btn btn-outline">Sign In</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <main class="container" style="padding: 2rem 1.5rem 4rem;" id="main-content">
        <a href="crops.php" class="back-btn"><i class="fa-solid fa-arrow-left"></i> Back to Encyclopedia</a>
        
        <div id="loading" style="text-align: center; padding: 4rem;">
            <i class="fa-solid fa-spinner fa-spin fa-3x" style="color: var(--primary-color);"></i>
        </div>

        <div id="cropDetailsContent" style="display: none;" class="animate-fade-in">
            <!-- Populated via JS -->
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const urlParams = new URLSearchParams(window.location.search);
            const cropId = parseInt(urlParams.get('id'));

            if (!cropId) {
                window.location.href = 'crops.php';
                return;
            }

            try {
                const response = await fetch('assets/data/crops.json');
                const crops = await response.json();
                const crop = crops.find(c => c.id === cropId);

                if (!crop) {
                    document.getElementById('main-content').innerHTML = `<h2>Crop not found.</h2><a href="crops.php" class="btn btn-primary">Go Back</a>`;
                    return;
                }

                const html = `
                    <div class="details-header">
                        <img src="${crop.image_url}" onerror="this.onerror=null; this.src='assets/images/default_crop.jpg';" loading="lazy" alt="${crop.name}" style="width: 100%; height: 100%; object-fit: cover;">
                        <div class="header-text">
                            <h1>${crop.name}</h1>
                            <p style="font-size: 1.2rem; font-style: italic; opacity: 0.9;">${crop.scientific_name}</p>
                        </div>
                    </div>

                    <div class="detail-grid">
                        <div class="detail-card">
                            <h3 style="margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;"><i class="fa-solid fa-earth-americas"></i> Environmental Requirements</h3>
                            <div class="info-block" style="margin-bottom: 1rem;">
                                <h4>Optimal Season</h4>
                                <p><span class="badge" style="background: #e0f2fe; color: #0284c7;">${crop.season}</span></p>
                            </div>
                            <div class="info-block" style="margin-bottom: 1rem;">
                                <h4>Soil Type & pH</h4>
                                <p>${crop.soil_type} • pH ${crop.ph_range}</p>
                            </div>
                            <div class="info-block" style="margin-bottom: 1rem;">
                                <h4>Climate Suitability</h4>
                                <p>${crop.temp_range} • Rainfall: ${crop.rainfall_range}</p>
                            </div>
                            <div class="info-block">
                                <h4>Water Requirement</h4>
                                <p>${crop.water_req}</p>
                            </div>
                        </div>

                        <div class="detail-card">
                            <h3 style="margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;"><i class="fa-solid fa-seedling"></i> Cultivation Lifecycle</h3>
                            <div class="info-block" style="margin-bottom: 1rem;">
                                <h4>Sowing & Harvesting</h4>
                                <p>Sow: ${crop.sowing_time} <br>Harvest: ${crop.harvest_time}</p>
                            </div>
                            <div class="info-block" style="margin-bottom: 1rem;">
                                <h4>Days to Maturity</h4>
                                <p>${crop.maturity_days}</p>
                            </div>
                            <div class="info-block" style="margin-bottom: 1rem;">
                                <h4>Cultivation Practices</h4>
                                <p style="font-size: 1rem; font-weight: 400; line-height: 1.7;">${crop.cultivation}</p>
                            </div>
                            <div class="info-block">
                                <h4>Major Growing States</h4>
                                <p style="font-size: 1rem; font-weight: 400;">${crop.suitable_states}</p>
                            </div>
                        </div>
                    </div>
                `;

                document.getElementById('loading').style.display = 'none';
                const contentDiv = document.getElementById('cropDetailsContent');
                contentDiv.innerHTML = html;
                contentDiv.style.display = 'block';

            } catch (error) {
                console.error("Error loading crop details", error);
            }
        });
        
        async function logout() {
            await fetch('api/auth.php', { method: 'POST', body: JSON.stringify({action: 'logout'}) });
            window.location.reload();
        }
    </script>
</body>
</html>
