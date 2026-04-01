<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather Insights - Grow Your Crops India</title>
    <!-- Google Fonts & Font Awesome -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,600;1,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/weather.css">
</head>
<body style="background-color: var(--bg-main);">
    <!-- Navigation -->
    <nav class="navbar scrolled" id="navbar" style="background:var(--bg-surface);">
        <div class="container nav-container">
            <a href="index.html" class="logo">
                <i class="fa-solid fa-leaf"></i>
                <span>GrowYourCrops<span class="highlight">India</span></span>
            </a>
            <ul class="nav-links">
                <li><a href="index.html">Home</a></li>
                <li><a href="crops.php">Crops</a></li>
                <li><a href="recommendation.php">Recommendations</a></li>
                <li><a href="weather.php" class="active" style="color:var(--primary-color);">Weather <i class="fa-solid fa-cloud-sun"></i></a></li>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li id="auth-links">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="javascript:void(0)" onclick="handleLogout()" class="btn btn-outline" style="color:var(--error-color); border-color:var(--error-color)">Sign Out</a>
                    <?php else: ?>
                        <a href="auth.html" class="btn btn-outline">Login/Register</a>
                    <?php endif; ?>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container" style="margin-top: 100px; padding: 2rem 1rem;">
        <div class="section-header" style="text-align: left; margin-bottom: 2rem;">
            <h2>Agricultural <span class="highlight">Weather Insights</span></h2>
            <p>Real-time meteorological data and 7-day accurate forecasts to optimize your farming schedule.</p>
        </div>

        <!-- Controls -->
        <div class="form-card mb-4" style="background:white; display:flex; gap:1rem; align-items:flex-end; flex-wrap:wrap; padding: 1.5rem;">
            <div class="form-group" style="flex:1; min-width:250px; margin-bottom:0;">
                <label><i class="fa-solid fa-location-dot"></i> Search Location</label>
                <div style="display:flex; gap:0.5rem;">
                    <input type="text" id="city-input" class="form-control" placeholder="Enter a place (e.g., Kasaragod, Kerala)" style="flex: 1;" value="New Delhi, Delhi">
                    <button id="btn-search" class="btn btn-primary" style="height:48px; width:60px;"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>
            </div>
            <button id="btn-refresh" class="btn btn-outline" style="height:48px;"><i class="fa-solid fa-rotate-right"></i> Refresh</button>
            <button id="btn-locate" class="btn btn-secondary" style="height:48px; background-color:#3b82f6;"><i class="fa-solid fa-location-crosshairs"></i> Auto Detect</button>
        </div>

        <!-- Alerts Banner -->
        <div id="weather-alerts" class="mb-4"></div>

        <!-- Main Weather Display -->
        <div class="weather-layout">
            <!-- Current Weather Card -->
            <div class="current-weather-card">
                <h3 style="color:white; margin-bottom:1.5rem; font-family:'Outfit'; font-weight:600;"><i class="fa-solid fa-location-arrow"></i> Current Conditions</h3>
                <div class="current-main">
                    <div class="icon-temp" style="display:flex; align-items:center; gap:1.5rem; justify-content:center;">
                        <i id="current-icon" class="fa-solid fa-spinner fa-spin weather-main-icon"></i>
                        <div class="temp-display">
                            <span id="current-temp">--</span>°C
                        </div>
                    </div>
                    <div id="current-desc" class="weather-desc">Initializing Data Engine...</div>
                </div>
                
                <div class="weather-details-grid mt-4">
                    <div class="weather-detail-item">
                        <div class="wd-icon"><i class="fa-solid fa-droplet"></i></div>
                        <div>
                            <p class="detail-label">Relative Humidity</p>
                            <p class="detail-value" id="current-humidity">--%</p>
                        </div>
                    </div>
                    <div class="weather-detail-item">
                        <div class="wd-icon"><i class="fa-solid fa-wind"></i></div>
                        <div>
                            <p class="detail-label">Wind Speed</p>
                            <p class="detail-value" id="current-wind">-- km/h</p>
                        </div>
                    </div>
                    <div class="weather-detail-item">
                        <div class="wd-icon"><i class="fa-solid fa-cloud-rain"></i></div>
                        <div>
                            <p class="detail-label">Forecast Rainfall</p>
                            <p class="detail-value" id="current-rain">-- mm</p>
                        </div>
                    </div>
                    <div class="weather-detail-item">
                        <div class="wd-icon"><i class="fa-solid fa-mountain-sun"></i></div>
                        <div>
                            <p class="detail-label">Elevation</p>
                            <p class="detail-value" id="current-elevation">-- m</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hourly Forecast Container -->
            <div class="forecast-container mb-4" style="background:white; border-left: 4px solid var(--primary-light);">
                <h3 class="mb-4" style="color:var(--primary-dark); font-family:'Playfair Display'; font-size:1.5rem;"><i class="fa-solid fa-clock" style="color:#0ea5e9;"></i> 24-Hour Forecast</h3>
                <div class="hourly-scroll-wrapper" style="overflow-x: auto; padding-bottom: 15px; scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent;">
                    <div id="hourly-row" style="display: grid; grid-template-rows: auto auto auto; grid-auto-flow: column; gap: 1rem; width: max-content;">
                        <!-- JS injected -->
                        <div style="padding: 2rem; text-align: center; color: var(--text-muted); width: 100%;"><i class="fa-solid fa-spinner fa-spin"></i> Fetching hourly data...</div>
                    </div>
                </div>
            </div>

            <!-- Deep 6-Day Forecast -->
            <div class="forecast-container" style="background:white;">
                <h3 class="mb-4" style="color:var(--primary-dark); font-family:'Playfair Display'; font-size:1.75rem; text-align: center;">Deep 6-Day Forecast & Precipitation</h3>
                <div class="forecast-row" id="forecast-row">
                    <!-- Populated natively by JS -->
                    <div style="padding:2rem; width:100%; text-align:center; color:var(--text-muted);"><i class="fa-solid fa-circle-notch fa-spin"></i> Fetching meteorological arrays...</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer style="background:#1e293b; color:white; padding:3rem 0; margin-top:5rem;">
        <div class="container text-center">
            <p style="color:#cbd5e1;">&copy; 2026 Grow Your Crops India. Built with Open-Meteo Meteorological Systems.</p>
        </div>
    </footer>

    <script src="assets/js/weather.js"></script>
</body>
</html>
