let currentCoords = "28.6139,77.2090"; // Default New Delhi

document.addEventListener('DOMContentLoaded', () => {
    const cityInput = document.getElementById('city-input');
    const searchBtn = document.getElementById('btn-search');
    const refreshBtn = document.getElementById('btn-refresh');
    const locateBtn = document.getElementById('btn-locate');
    
    // Initial fetch on page load
    fetchWeather(currentCoords);

    searchBtn.addEventListener('click', () => {
        performSearch();
    });

    cityInput.addEventListener('keypress', (e) => {
        if(e.key === 'Enter') performSearch();
    });

    async function performSearch() {
        const query = cityInput.value.trim();
        if(!query) {
            alert("Please enter a valid city or region (e.g., Kasaragod, Kerala).");
            return;
        }
        
        // Show loading state on search button
        const originalIcon = searchBtn.innerHTML;
        searchBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
        searchBtn.disabled = true;

        try {
            // Extract core city name by stripping comma-separated regions for Open-Meteo
            let searchQuery = query;
            if (searchQuery.includes(',')) {
                searchQuery = searchQuery.split(',')[0].trim();
            }

            // Geocoding API to resolve text to exact coordinates natively
            const geoUrl = `https://geocoding-api.open-meteo.com/v1/search?name=${encodeURIComponent(searchQuery)}&count=1&language=en&format=json`;
            const geoRes = await fetch(geoUrl);
            const geoData = await geoRes.json();
            
            if(geoData.results && geoData.results.length > 0) {
                const location = geoData.results[0];
                currentCoords = `${location.latitude},${location.longitude}`;
                
                // Beautifully update input accurately to resolved name
                let resolvedName = location.name;
                if(location.admin1) resolvedName += `, ${location.admin1}`;
                else if(location.country) resolvedName += `, ${location.country}`;
                cityInput.value = resolvedName;
                
                await fetchWeather(currentCoords);
            } else {
                alert(`Could not precisely locate coordinates for "${query}". Please check your spelling and try again.`);
            }
        } catch(e) {
            console.error(e);
            alert("External geocoding service currently unavailable.");
        } finally {
            searchBtn.innerHTML = originalIcon;
            searchBtn.disabled = false;
        }
    }

    refreshBtn.addEventListener('click', () => {
        const icon = refreshBtn.querySelector('i');
        icon.classList.add('fa-spin');
        fetchWeather(currentCoords).then(() => {
            setTimeout(() => icon.classList.remove('fa-spin'), 500);
        });
    });

    locateBtn.addEventListener('click', () => {
        if ("geolocation" in navigator) {
            locateBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Locating...';
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = position.coords.latitude;
                    const lon = position.coords.longitude;
                    currentCoords = `${lat},${lon}`;
                    
                    cityInput.value = "📍 Your Current Location";
                    
                    fetchWeather(currentCoords);
                    locateBtn.innerHTML = '<i class="fa-solid fa-location-crosshairs"></i> Auto Detect';
                },
                (error) => {
                    let msg = "Location error.";
                    if(error.code === 1) msg = "Location access denied by user.";
                    alert(msg + " Please use the search bar manually.");
                    locateBtn.innerHTML = '<i class="fa-solid fa-location-crosshairs"></i> Auto Detect';
                },
                { timeout: 10000 }
            );
        } else {
            alert("Geolocation is not natively supported by your browser.");
        }
    });
});

// WMO Standard Weather Code Interface (Open-Meteo format)
function getWeatherInfo(code) {
    const codes = {
        0: { desc: 'Clear Sky', icon: 'fa-sun', color: '#facc15' },
        1: { desc: 'Mainly Clear', icon: 'fa-cloud-sun', color: '#fcd34d' },
        2: { desc: 'Partly Cloudy', icon: 'fa-cloud-sun', color: '#94a3b8' },
        3: { desc: 'Overcast', icon: 'fa-cloud', color: '#64748b' },
        45: { desc: 'Foggy / Haze', icon: 'fa-smog', color: '#94a3b8' },
        48: { desc: 'Depositing Rime Fog', icon: 'fa-smog', color: '#94a3b8' },
        51: { desc: 'Light Drizzle', icon: 'fa-cloud-rain', color: '#60a5fa' },
        53: { desc: 'Moderate Drizzle', icon: 'fa-cloud-rain', color: '#3b82f6' },
        55: { desc: 'Dense Drizzle', icon: 'fa-cloud-showers-heavy', color: '#2563eb' },
        61: { desc: 'Slight Rain', icon: 'fa-cloud-rain', color: '#60a5fa' },
        63: { desc: 'Moderate Rain', icon: 'fa-cloud-rain', color: '#2563eb' },
        65: { desc: 'Heavy Rain', icon: 'fa-cloud-showers-heavy', color: '#1d4ed8' },
        71: { desc: 'Slight Snow', icon: 'fa-snowflake', color: '#bae6fd' },
        73: { desc: 'Moderate Snow', icon: 'fa-snowflake', color: '#7dd3fc' },
        75: { desc: 'Heavy Snow', icon: 'fa-snowflake', color: '#38bdf8' },
        80: { desc: 'Slight Rain Showers', icon: 'fa-cloud-rain', color: '#3b82f6' },
        81: { desc: 'Moderate Rain Showers', icon: 'fa-cloud-showers-heavy', color: '#2563eb' },
        82: { desc: 'Violent Rain Showers', icon: 'fa-cloud-showers-heavy', color: '#1e40af' },
        95: { desc: 'Thunderstorm', icon: 'fa-bolt', color: '#eab308' },
        96: { desc: 'Thunderstorm & Hail', icon: 'fa-cloud-bolt', color: '#9333ea' },
        99: { desc: 'Heavy Thunderstorm', icon: 'fa-cloud-bolt', color: '#7e22ce' }
    };
    return codes[code] || { desc: 'Unknown Conditions', icon: 'fa-cloud', color: '#64748b' };
}

async function fetchWeather(coordsStr) {
    const [lat, lon] = coordsStr.split(',');
    
    // Core Engine Call - NO API KEY REQUIRED - 100% Free
    const url = `https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&current=temperature_2m,relative_humidity_2m,weather_code,wind_speed_10m&hourly=temperature_2m,precipitation_probability,weather_code&daily=weather_code,temperature_2m_max,temperature_2m_min,precipitation_sum&timezone=auto`;
    
    try {
        const response = await fetch(url);
        if(!response.ok) throw new Error("HTTP error " + response.status);
        const data = await response.json();
        
        // --- 1. Current Weather Implementation ---
        const current = data.current;
        const info = getWeatherInfo(current.weather_code);
        
        document.getElementById('current-temp').innerText = Math.round(current.temperature_2m);
        document.getElementById('current-desc').innerText = info.desc;
        
        const iconEl = document.getElementById('current-icon');
        iconEl.className = `fa-solid ${info.icon} weather-main-icon`;
        
        document.getElementById('current-humidity').innerText = `${current.relative_humidity_2m}%`;
        document.getElementById('current-wind').innerText = `${current.wind_speed_10m} km/h`;
        document.getElementById('current-rain').innerText = `${data.daily.precipitation_sum[0]} mm`;
        document.getElementById('current-elevation').innerText = `${data.elevation} m`;
        
        // --- 2. Advanced Agricultural Alert Logic ---
        const alertsPanel = document.getElementById('weather-alerts');
        alertsPanel.innerHTML = '';
        
        let rainTotal3Days = 0;
        for(let i=0; i<3; i++) rainTotal3Days += data.daily.precipitation_sum[i] || 0;
        
        if(rainTotal3Days > 50) {
            alertsPanel.innerHTML += `<div class="alert alert-error" style="background:#fee2e2; color:#991b1b; padding:1.2rem; border-left:5px solid #ef4444; margin-bottom:1rem; border-radius:8px;">
                <i class="fa-solid fa-triangle-exclamation" style="font-size:1.5rem; float:left; margin-right:1rem;"></i> 
                <div><strong>HEAVY RAIN ALERT:</strong> Expected ${rainTotal3Days.toFixed(1)}mm of cumulative rain exactly over the next 3 processing days. Secure harvested crops and assure drainage canals are fully clear to aggressively prevent waterlogging damages.</div>
            </div>`;
        }
        if(current.temperature_2m > 38) {
            alertsPanel.innerHTML += `<div class="alert alert-error" style="background:#fff7ed; color:#9a3412; padding:1.2rem; border-left:5px solid #f97316; margin-bottom:1rem; border-radius:8px;">
                <i class="fa-solid fa-temperature-arrow-up" style="font-size:1.5rem; float:left; margin-right:1rem;"></i> 
                <div><strong>HEATWAVE WARNING:</strong> Peak surface temperatures exceeding native limits. Immediately increase irrigation frequency cycles to prevent intense moisture stress on vulnerable vegetations.</div>
            </div>`;
        }
        if(rainTotal3Days < 2 && current.temperature_2m > 32) {
            alertsPanel.innerHTML += `<div class="alert alert-warning" style="background:#fefce8; color:#c2410c; padding:1.2rem; border-left:5px solid #f59e0b; margin-bottom:1rem; border-radius:8px;">
                <i class="fa-solid fa-sun-plant-wilt" style="font-size:1.5rem; float:left; margin-right:1rem;"></i> 
                <div><strong>IMPENDING DRY SPELL:</strong> No valid measurable precipitation indicated while temperatures remain actively elevated. Closely monitor base soil moisture parameters actively.</div>
            </div>`;
        }
        if(current.wind_speed_10m > 35) {
            alertsPanel.innerHTML += `<div class="alert alert-warning" style="background:#f1f5f9; color:#334155; padding:1.2rem; border-left:5px solid #64748b; margin-bottom:1rem; border-radius:8px;">
                <i class="fa-solid fa-wind" style="font-size:1.5rem; float:left; margin-right:1rem;"></i> 
                <div><strong>GALE WARNING:</strong> High speed ground winds detected natively. Protect tall standing structural crops (like Banana or Sugarcane) against heavy lodging.</div>
            </div>`;
        }

        // Default Clear message if architecture is completely stable
        if(alertsPanel.innerHTML === '') {
            alertsPanel.innerHTML = `<div class="alert alert-success" style="background:#ecfdf5; color:#065f46; padding:1rem; border-radius:8px; border:1px solid #a7f3d0;">
                <i class="fa-solid fa-circle-check"></i> Weather conditions are fundamentally optimal for generic farming frameworks. No active alerts.
            </div>`;
        }

        // --- 3. 24-Hour Forecast Implementation ---
        const hourlyRow = document.getElementById('hourly-row');
        hourlyRow.innerHTML = '';
        
        // Find current hour index dynamically based on local time matching the API's timezone
        const nowTime = new Date().getTime();
        let currentIndex = 0;
        let minDiff = Infinity;
        for(let i = 0; i < data.hourly.time.length; i++) {
            const hTime = new Date(data.hourly.time[i]).getTime();
            if(Math.abs(hTime - nowTime) < minDiff) {
                minDiff = Math.abs(hTime - nowTime);
                currentIndex = i;
            }
        }

        // Render exactly next 24 hours into the horizontally scrolling container
        for(let i = currentIndex; i < currentIndex + 24 && i < data.hourly.time.length; i++) {
            const hTimeObj = new Date(data.hourly.time[i]);
            let hours = hTimeObj.getHours();
            const ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12 || 12; // Formatter: 12hr clock
            
            let timeLabel = i === currentIndex ? "Now" : `${hours} ${ampm}`;
            const hTemp = Math.round(data.hourly.temperature_2m[i]);
            const hRain = data.hourly.precipitation_probability[i];
            const hCode = data.hourly.weather_code[i];
            const hInfo = getWeatherInfo(hCode);
            
            const isCurrent = (i === currentIndex);
            
            const cardHtml = `
                <div style="min-width:105px; padding:1.2rem 1rem; border-radius:12px; display:flex; flex-direction:column; align-items:center; justify-content:space-between; text-align:center; background:${isCurrent ? 'var(--primary-color)' : '#f8fafc'}; color:${isCurrent ? 'white' : 'var(--text-main)'}; border:1px solid ${isCurrent ? 'var(--primary-dark)' : '#e2e8f0'}; box-shadow:${isCurrent ? '0 4px 10px rgba(5,150,105,0.2)' : 'none'}; transition:transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                    <span style="font-size:0.9rem; font-weight:600; color:${isCurrent ? 'rgba(255,255,255,0.9)' : 'var(--primary-dark)'}; margin-bottom:0.5rem;">${timeLabel}</span>
                    <i class="fa-solid ${hInfo.icon}" style="font-size:2rem; color:${isCurrent ? '#fff' : hInfo.color}; margin-bottom:0.5rem; filter:${isCurrent ? 'drop-shadow(0 2px 4px rgba(0,0,0,0.1))' : 'none'}"></i>
                    <span style="font-size:1.3rem; font-weight:700; margin-bottom:0.25rem;">${hTemp}°C</span>
                    <span style="font-size:0.85rem; font-weight:500; color:${isCurrent ? 'rgba(255,255,255,0.8)' : '#3b82f6'};"><i class="fa-solid fa-droplet"></i> ${hRain}%</span>
                </div>
            `;
            hourlyRow.innerHTML += cardHtml;
        }

        // --- 4. 6-Day Predictive Forecast Implementation ---
        const daily = data.daily;
        const forecastRow = document.getElementById('forecast-row');
        forecastRow.innerHTML = '';
        
        const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        
        for (let i = 1; i <= 6; i++) {
            const dateObj = new Date(daily.time[i]);
            let dayName;
            if(i === 1) dayName = 'Tomorrow';
            else dayName = days[dateObj.getDay()];
            
            const stateInfo = getWeatherInfo(daily.weather_code[i]);
            const maxTemp = Math.round(daily.temperature_2m_max[i]);
            const minTemp = Math.round(daily.temperature_2m_min[i]);
            const rainMM = daily.precipitation_sum[i];
            
            const card = document.createElement('div');
            card.className = 'forecast-card';
            card.innerHTML = `
                <div class="forecast-day">${dayName}</div>
                <i class="fa-solid ${stateInfo.icon} forecast-icon" style="color: ${stateInfo.color}"></i>
                <div class="forecast-temp">${maxTemp}°<span style="font-size:0.9rem; color:#94a3b8; font-weight:normal; margin-left:4px;">${minTemp}°</span></div>
                <div class="forecast-rain" style="opacity: ${rainMM > 0 ? 1 : 0.4}"><i class="fa-solid fa-droplet"></i> ${rainMM}mm</div>
            `;
            forecastRow.appendChild(card);
        }
        
    } catch(err) {
        console.error("Open-Meteo Engine Offline:", err);
        document.getElementById('weather-alerts').innerHTML = `<div class="alert alert-error" style="background:#fef2f2; color:#991b1b; padding:1rem;">
            <i class="fa-solid fa-server"></i> Unable to connect to the global meteorological data-grid. Check your specific connection architecture.
        </div>`;
    }
}

// Ensure handleLogout is mapped globally if needed by PHP nav
window.handleLogout = async function() {
    await fetch('api/auth.php', { method: 'POST', body: JSON.stringify({ action: 'logout' }) });
    window.location.href = 'index.html';
};
