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
        locateBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Locating...';

        const fallbackToIP = async () => {
             try {
                 const res = await fetch('https://get.geojs.io/v1/ip/geo.json');
                 const data = await res.json();
                 currentCoords = `${data.latitude},${data.longitude}`;
                 cityInput.value = `📍 ${data.city || 'Your Location'}`;
                 await fetchWeather(currentCoords);
             } catch (err) {
                 alert("Location access completely blocked and IP-fallback failed. Please use the search bar manually.");
             } finally {
                 locateBtn.innerHTML = '<i class="fa-solid fa-location-crosshairs"></i> Auto Detect';
             }
        };

        if ("geolocation" in navigator) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = position.coords.latitude;
                    const lon = position.coords.longitude;
                    currentCoords = `${lat},${lon}`;
                    
                    cityInput.value = "📍 Exact GPS Location";
                    
                    fetchWeather(currentCoords).then(() => {
                        locateBtn.innerHTML = '<i class="fa-solid fa-location-crosshairs"></i> Auto Detect';
                    });
                },
                (error) => {
                    // Fallback natively to IP-based inference if prompt is denied or HTTP unsecured protocol limits it
                    fallbackToIP();
                },
                { timeout: 6000 }
            );
        } else {
            fallbackToIP();
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
    
    // Core Engine Call
    const url = `https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&current=temperature_2m,relative_humidity_2m,apparent_temperature,is_day,precipitation,weather_code,cloud_cover,surface_pressure,wind_speed_10m,visibility&hourly=temperature_2m,precipitation_probability,weather_code&daily=weather_code,temperature_2m_max,temperature_2m_min,apparent_temperature_max,apparent_temperature_min,sunrise,sunset,uv_index_max,precipitation_sum&timezone=auto`;
    
    try {
        const response = await fetch(url);
        if(!response.ok) throw new Error("HTTP error " + response.status);
        const data = await response.json();
        
        // --- 1. Current Weather Implementation ---
        const current = data.current;
        const info = getWeatherInfo(current.weather_code);
        
        // Dynamic MSN Background Logic
        const isDay = current.is_day === 1;
        const code = current.weather_code;
        let bgClass = isDay ? 'bg-clear-day' : 'bg-clear-night';
        if([1,2,3].includes(code)) bgClass = isDay ? 'bg-cloudy-day' : 'bg-cloudy-night';
        if([51,53,55,61,63,65,80,81,82].includes(code)) bgClass = 'bg-rainy';
        if([95,96,99].includes(code)) bgClass = 'bg-storm';
        if([71,73,75].includes(code)) bgClass = 'bg-snow';
        
        document.body.className = bgClass;
        
        // Hero Update
        document.getElementById('current-temp').innerText = Math.round(current.temperature_2m);
        document.getElementById('current-desc').innerText = info.desc;
        const iconEl = document.getElementById('current-icon');
        iconEl.className = `fa-solid ${info.icon} weather-main-icon`;
        if(info.color) iconEl.style.color = info.color; // Give icon color on transparent bg if available
        
        document.getElementById('current-feels').innerText = Math.round(current.apparent_temperature);
        document.getElementById('day-high').innerText = Math.round(data.daily.temperature_2m_max[0]);
        document.getElementById('day-low').innerText = Math.round(data.daily.temperature_2m_min[0]);
        document.getElementById('current-vis').innerText = (current.visibility / 1000).toFixed(1);
        
        // Tiles Update
        document.getElementById('current-humidity').innerText = `${current.relative_humidity_2m}%`;
        document.getElementById('current-wind').innerText = `${current.wind_speed_10m} km/h`;
        document.getElementById('current-rain').innerText = `${data.daily.precipitation_sum[0]} mm`;
        document.getElementById('current-pressure').innerText = `${Math.round(current.surface_pressure)} hPa`;
        
        const uv = data.daily.uv_index_max[0];
        document.getElementById('current-uv').innerText = uv ? uv.toFixed(1) : '0';
        let uvDesc = 'Low';
        if(uv >= 3) uvDesc = 'Moderate';
        if(uv >= 6) uvDesc = 'High';
        if(uv >= 8) uvDesc = 'Very High';
        document.getElementById('uv-desc').innerText = uvDesc;
        
        // Sunrise / Sunset parsing
        const formatTime = (isoString) => {
            const date = new Date(isoString);
            let hours = date.getHours();
            let mins = date.getMinutes();
            const ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12 || 12;
            mins = mins < 10 ? '0'+mins : mins;
            return `${hours}:${mins}`; // AM/PM handled by hardcoded html
        };
        document.getElementById('sunrise-time').innerText = formatTime(data.daily.sunrise[0]);
        document.getElementById('sunset-time').innerText = formatTime(data.daily.sunset[0]);
        
        // --- 2. Advanced Agricultural Alert Logic ---
        const alertsPanel = document.getElementById('weather-alerts');
        alertsPanel.innerHTML = '';
        
        let rainTotal3Days = 0;
        for(let i=0; i<3; i++) rainTotal3Days += data.daily.precipitation_sum[i] || 0;
        
        if(rainTotal3Days > 50) {
            alertsPanel.innerHTML += `<div class="alert alert-error glass-panel" style="background:rgba(254,226,226,0.9); color:#991b1b; padding:1.2rem; border-left:5px solid #ef4444; margin-bottom:1rem;">
                <i class="fa-solid fa-triangle-exclamation" style="font-size:1.5rem; float:left; margin-right:1rem;"></i> 
                <div><strong>HEAVY RAIN ALERT:</strong> Expected ${rainTotal3Days.toFixed(1)}mm of cumulative rain exactly over the next 3 processing days. Secure harvested crops.</div>
            </div>`;
        }
        if(current.temperature_2m > 38) {
            alertsPanel.innerHTML += `<div class="alert alert-error glass-panel" style="background:rgba(255,247,237,0.9); color:#9a3412; padding:1.2rem; border-left:5px solid #f97316; margin-bottom:1rem;">
                <i class="fa-solid fa-temperature-arrow-up" style="font-size:1.5rem; float:left; margin-right:1rem;"></i> 
                <div><strong>HEATWAVE WARNING:</strong> Peak surface temperatures exceeding native limits. Immediately increase irrigation frequency cycles.</div>
            </div>`;
        }

        // --- 3. 24-Hour Forecast Implementation ---
        const hourlyRow = document.getElementById('hourly-row');
        hourlyRow.innerHTML = '';
        
        const currentTargetTimeStr = data.current.time; 
        const currentTargetTime = Date.parse(currentTargetTimeStr + "Z"); 
        
        let currentIndex = 0;
        let minDiff = Infinity;
        for(let i = 0; i < data.hourly.time.length; i++) {
            const hTime = Date.parse(data.hourly.time[i] + "Z");
            if(Math.abs(hTime - currentTargetTime) < minDiff) {
                minDiff = Math.abs(hTime - currentTargetTime);
                currentIndex = i;
            }
        }

        for(let i = currentIndex; i < currentIndex + 24 && i < data.hourly.time.length; i++) {
            const timeStr = data.hourly.time[i];
            let hours = parseInt(timeStr.substring(11, 13), 10);
            const ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12 || 12;
            
            let timeLabel = i === currentIndex ? "Now" : `${hours} ${ampm}`;
            const hTemp = Math.round(data.hourly.temperature_2m[i]);
            const hRain = data.hourly.precipitation_probability[i];
            const hCode = data.hourly.weather_code[i];
            const hInfo = getWeatherInfo(hCode);
            
            hourlyRow.innerHTML += `
                <div class="hourly-card">
                    <span class="hourly-time">${timeLabel}</span>
                    <i class="fa-solid ${hInfo.icon} hourly-icon" style="color:${hInfo.color}"></i>
                    <span class="hourly-temp">${hTemp}°</span>
                    <span class="hourly-rain"><i class="fa-solid fa-droplet"></i> ${hRain}%</span>
                </div>
            `;
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
            
            forecastRow.innerHTML += `
                <div class="daily-row">
                    <div class="d-day">${dayName}</div>
                    <div class="d-icon"><i class="fa-solid ${stateInfo.icon}" style="color: ${stateInfo.color}"></i></div>
                    <div class="d-temps">${maxTemp}°<span class="d-min">${minTemp}°</span></div>
                    <div class="d-rain" style="opacity: ${rainMM > 0 ? 1 : 0.4}"><i class="fa-solid fa-droplet"></i> ${rainMM}mm</div>
                </div>
            `;
        }
        
    } catch(err) {
        console.error("Open-Meteo Engine Offline:", err);
        document.getElementById('weather-alerts').innerHTML = `<div class="alert alert-error glass-panel" style="background:rgba(254,226,226,0.9); color:#991b1b; padding:1rem;">
            <i class="fa-solid fa-server"></i> Unable to connect to the global meteorological data-grid. Check your connection architecture.
        </div>`;
    }
}

// Ensure handleLogout is mapped globally if needed by PHP nav
window.handleLogout = async function() {
    await fetch('api/auth.php', { method: 'POST', body: JSON.stringify({ action: 'logout' }) });
    window.location.href = 'index.html';
};
