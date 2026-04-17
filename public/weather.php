<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather Insights - GROW YOUR CROPS</title>
    <!-- Google Fonts & Font Awesome -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,600;1,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/weather.css">
</head>
<body class="bg-clear-day">
    <!-- Navigation -->
    <nav class="navbar" id="navbar" style="background: rgba(255,255,255,0.1) !important; backdrop-filter: blur(15px); border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 0;">
        <div class="container nav-container">
            <a href="index.html" class="logo">
                <i class="fa-solid fa-leaf"></i>
                <span>Grow Your Crops</span>
            </a>
            <ul class="nav-links">
                <li><a href="index.html">Home</a></li>
                <li><a href="crops.php">Crops</a></li>
                <li><a href="recommendation.php">Recommendations</a></li>
                <li><a href="weather.php" class="active" style="color:var(--primary-color);">Weather <i class="fa-solid fa-cloud-sun"></i></a></li>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="cosmetic_uses.php">Cosmetic Uses</a></li>
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

    <div class="container" style="margin-top: 100px; padding: 3rem 1rem;">
        <div class="section-header" style="text-align: left; margin-bottom: 3rem;">
            <h2>Agricultural <span class="highlight">Weather Insights</span></h2>
            <p style="color: #475569; font-size: 1.1rem;">Real-time meteorological data and 7-day accurate forecasts to optimize your farming schedule.</p>
        </div>

        <!-- Controls -->
        <div class="form-card glass-panel" style="display:flex; gap:1.5rem; align-items:flex-end; flex-wrap:wrap; padding: 2.5rem; border:1px solid rgba(255,255,255,0.2);">
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
        <div class="msn-weather-layout">
            <!-- Hero / Current Weather Panel -->
            <div class="msn-hero-panel glass-panel">
                <div class="hero-left">
                    <div class="hero-temp-wrapper">
                        <h1 id="current-temp" class="hugetemp">--</h1><span class="deg-symbol">°C</span>
                    </div>
                    <div class="hero-desc">
                        <i id="current-icon" class="fa-solid fa-spinner fa-spin"></i>
                        <span id="current-desc">Initializing...</span>
                    </div>
                </div>
                <div class="hero-right">
                    <p class="feels-like">Feels like <span id="current-feels">--</span>°</p>
                    <p class="high-low">Day <span id="day-high">--</span>° &bull; Night <span id="day-low">--</span>°</p>
                    <p class="visibility-sum"><i class="fa-solid fa-eye"></i> Visibility: <span id="current-vis">--</span> km</p>
                </div>
            </div>

            <!-- Detailed Current Conditions Tiles Grid -->
            <div class="msn-details-grid">
                <div class="msn-tile glass-panel">
                    <div class="tile-header"><i class="fa-solid fa-droplet"></i> Humidity</div>
                    <div class="tile-body" id="current-humidity">--%</div>
                    <div class="tile-foot">Moisture Level</div>
                </div>
                <div class="msn-tile glass-panel">
                    <div class="tile-header"><i class="fa-solid fa-wind"></i> Wind</div>
                    <div class="tile-body" id="current-wind">-- km/h</div>
                    <div class="tile-foot">Surface Level Speed</div>
                </div>
                <div class="msn-tile glass-panel">
                    <div class="tile-header"><i class="fa-solid fa-sun"></i> UV Index</div>
                    <div class="tile-body" id="current-uv">--</div>
                    <div class="tile-foot" id="uv-desc">Loading...</div>
                </div>
                <div class="msn-tile glass-panel">
                    <div class="tile-header"><i class="fa-solid fa-cloud-showers-water"></i> Rain Forecast</div>
                    <div class="tile-body" id="current-rain">-- mm</div>
                    <div class="tile-foot">Next 24 Hours</div>
                </div>
                <div class="msn-tile glass-panel">
                    <div class="tile-header"><i class="fa-solid fa-stopwatch"></i> Air Pressure</div>
                    <div class="tile-body" id="current-pressure">-- hPa</div>
                    <div class="tile-foot">Surface Level</div>
                </div>
                <div class="msn-tile glass-panel">
                    <div class="tile-header"><i class="fa-solid fa-cloud-sun"></i> Sun Cycle</div>
                    <div class="tile-body sunrise-set">
                        <div class="sun-item"><i class="fa-solid fa-sun" style="color: #fbbf24;"></i> <span id="sunrise-time">--:--</span> AM</div>
                        <div class="sun-item"><i class="fa-solid fa-moon" style="color: #9ca3af;"></i> <span id="sunset-time">--:--</span> PM</div>
                    </div>
                </div>
            </div>

            <!-- Deep 6-Day Forecast List -->
            <div class="msn-forecast-panel glass-panel">
                <div class="msn-panel-header"><i class="fa-regular fa-calendar-days" style="opacity:0.8;"></i> Daily Forecast</div>
                <div class="msn-daily-list" id="forecast-row">
                    <!-- Populated natively by JS -->
                    <div style="padding:2rem; width:100%; text-align:center; opacity:0.8;"><i class="fa-solid fa-circle-notch fa-spin"></i> Fetching meteorological arrays...</div>
                </div>
            </div>
            
            <!-- MSN Style Hourly Forecast -->
            <div class="msn-forecast-panel glass-panel" style="grid-column: 1 / -1;">
                <div class="msn-panel-header"><i class="fa-solid fa-clock" style="opacity:0.8;"></i> 24-Hour Forecast</div>
                <div class="msn-hourly-scroll">
                    <div id="hourly-row" class="msn-hourly-flex">
                        <div style="padding: 2rem; width: 100%; text-align: center; opacity:0.8;"><i class="fa-solid fa-spinner fa-spin"></i> Fetching hourly data...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer style="background:#1e293b; color:white; padding:3rem 0; margin-top:5rem;">
        <div class="container text-center">
            <p style="color:#cbd5e1;">&copy; 2026 GROW YOUR CROPS. Built with Open-Meteo Meteorological Systems.</p>
        </div>
    </footer>

    <script src="assets/js/weather.js"></script>
</body>
</html>
