<?php
ini_set('session.use_only_cookies', 1);
session_start();
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
    <title>AI Recommendation Engine - Grow Your Crops India</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,600;1,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body style="padding-top: 80px; background: url('https://images.unsplash.com/photo-1464226184884-fa280b87c399?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80') center/cover fixed; position: relative;">
    <div style="position: absolute; inset: 0; background: rgba(248, 250, 252, 0.85); z-index: -1;"></div>
    
    <!-- Navigation -->
    <nav class="navbar scrolled" id="navbar">
        <div class="container nav-container">
            <a href="index.html" class="logo">
                <i class="fa-solid fa-leaf"></i>
                <span>GrowYourCrops<span class="highlight">India</span></span>
            </a>
            <ul class="nav-links">
                <li><a href="index.html">Home</a></li>
                <li><a href="crops.php">Crop Encyclopedia</a></li>
                <li><a href="recommendation.php" class="active">AI Recommendation</a></li>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li>
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div style="display: flex; align-items: center; gap: 0.5rem; background: var(--border-color); padding: 0.5rem 1rem; border-radius: 9999px;">
                            <img src="https://ui-avatars.com/api/?name=User&background=059669&color=fff" id="nav-avatar" alt="Profile" style="width: 24px; height: 24px; border-radius: 50%;">
                            <span id="nav-name" style="font-weight: 500; font-size: 0.9rem;">Farmer</span>
                        </div>
                        <button onclick="logout()" class="btn btn-outline" style="padding: 0.4rem 1rem; font-size: 0.85rem;"><i class="fa-solid fa-arrow-right-from-bracket"></i> Logout</button>
                    </div>
                </li>
            </ul>
        </div>
    </nav>

    <main class="container" style="padding: 4rem 1.5rem;">
        <div class="section-header">
            <span class="badge" style="margin-bottom: 1rem;"><i class="fa-solid fa-microchip"></i> Python ML Engine</span>
            <h2>Precision <span class="highlight">Crop Recommender</span></h2>
            <p>Input your farm's critical parameters to let our advanced data engine calculate your highest-yielding possibilities.</p>
        </div>

        <div class="recommendation-grid">
            <!-- Form Section -->
            <div class="form-section glass-panel animate-fade-in delay-1">
                <h3 style="margin-bottom: 2rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
                    <i class="fa-solid fa-flask"></i> Land Parameters
                </h3>

                <form id="recommendation-form">
                    <div class="form-group">
                        <label for="state"><i class="fa-solid fa-map-location-dot"></i> Select State / Region</label>
                        <select id="state" name="state" required>
                            <option value="" disabled selected>Choose your state...</option>
                            <option value="Punjab">Punjab</option>
                            <option value="Uttar Pradesh">Uttar Pradesh</option>
                            <option value="Maharashtra">Maharashtra</option>
                            <option value="Karnataka">Karnataka</option>
                            <option value="Gujarat">Gujarat</option>
                            <option value="West Bengal">West Bengal</option>
                            <option value="Madhya Pradesh">Madhya Pradesh</option>
                            <option value="Assam">Assam</option>
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="soil_ph"><i class="fa-solid fa-vial"></i> Soil pH Level</label>
                            <input type="number" id="soil_ph" name="soil_ph" step="0.1" min="0" max="14" placeholder="e.g. 6.5" required>
                        </div>
                        <div class="form-group">
                            <label for="temperature"><i class="fa-solid fa-temperature-half"></i> Avg. Temp (°C)</label>
                            <input type="number" id="temperature" name="temperature" step="0.1" placeholder="e.g. 25.5" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="rainfall"><i class="fa-solid fa-cloud-rain"></i> Annual Rainfall (mm)</label>
                            <input type="number" id="rainfall" name="rainfall" step="1" placeholder="e.g. 150" required>
                        </div>
                        <div class="form-group">
                            <label for="humidity"><i class="fa-solid fa-droplet"></i> Avg. Humidity (%)</label>
                            <input type="number" id="humidity" name="humidity" step="1" min="0" max="100" placeholder="e.g. 60" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="nitrogen"><i class="fa-solid fa-n"></i> Soil Nitrogen (N) Ratio</label>
                        <input type="number" id="nitrogen" name="nitrogen" step="1" placeholder="e.g. 90" required>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block btn-large mt-4" id="submit-btn" style="margin-top: 1rem;">
                        <span>Analyze Land Suitability</span>
                        <i class="fa-solid fa-wand-magic-sparkles"></i>
                    </button>
                </form>
            </div>

            <!-- Results Section -->
            <div class="results-area glass-panel animate-fade-in delay-2" id="results-area">
                <h3 style="margin-bottom: 2rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; color: var(--primary-dark);">
                    <i class="fa-solid fa-chart-pie"></i> Analysis Results
                </h3>

                <div id="results-placeholder" class="empty-state">
                    <i class="fa-solid fa-satellite-dish"></i>
                    <h4 style="color: var(--text-muted); margin-bottom: 0.5rem;">Awaiting Data</h4>
                    <p style="font-size: 0.95rem; max-width: 300px;">Enter your soil and climate data on the left to activate the AI engine.</p>
                </div>

                <!-- Hidden Spinner -->
                <div id="loading" class="empty-state" style="display: none;">
                    <i class="fa-solid fa-circle-notch fa-spin" style="color: var(--primary-color);"></i>
                    <h4 style="color: var(--text-main);">Running Algorithms...</h4>
                    <p style="font-size: 0.95rem;">Cross-referencing historical yield data.</p>
                </div>

                <!-- Hidden Results -->
                <div id="results-content" style="display: none;">
                    <div class="alert" style="background: #ecfdf5; border: 1px solid #10b981; color: #065f46; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 1rem;">
                        <i class="fa-solid fa-circle-check" style="font-size: 1.5rem;"></i>
                        <div>
                            <strong>High Confidence Match Found</strong>
                            <div style="font-size: 0.85rem;">Based on your inputs, 3 optimum crops are recommended.</div>
                        </div>
                    </div>
                    
                    <div id="dynamic-results-container" style="display: flex; flex-direction: column; gap: 1rem;">
                    </div>

    <script>
        document.getElementById('recommendation-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const btn = document.getElementById('submit-btn');
            const placeholder = document.getElementById('results-placeholder');
            const loading = document.getElementById('loading');
            const content = document.getElementById('results-content');
            const resultsContainer = document.getElementById('dynamic-results-container');

            // UI State Change
            placeholder.style.display = 'none';
            content.style.display = 'none';
            loading.style.display = 'flex';
            btn.innerHTML = '<span>Processing...</span><i class="fa-solid fa-spinner fa-spin"></i>';
            btn.disabled = true;

            const payload = {
                state: document.getElementById('state').value,
                soil_ph: parseFloat(document.getElementById('soil_ph').value),
                temperature: parseFloat(document.getElementById('temperature').value),
                rainfall: parseFloat(document.getElementById('rainfall').value),
                humidity: parseFloat(document.getElementById('humidity').value),
                nitrogen: parseFloat(document.getElementById('nitrogen').value)
            };

            try {
                const response = await fetch('api/recommendation.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const res = await response.json();
                
                loading.style.display = 'none';
                content.style.display = 'block';
                content.classList.add('animate-fade-in');

                if(response.ok) {
                    // Populate results
                    resultsContainer.innerHTML = '';
                    res.data.forEach((item, index) => {
                        let colorClass = index === 0 ? 'var(--primary-color)' : (index === 1 ? 'var(--secondary-color)' : '#64748b');
                        
                        resultsContainer.innerHTML += `
                        <div style="border: 1px solid var(--border-color); border-radius: 0.5rem; padding: 1rem; background: var(--bg-surface); position: relative; overflow: hidden; margin-bottom: 1rem;">
                            ${index === 0 ? '<div style="position: absolute; right: -20px; top: 10px; background: var(--primary-color); color: white; padding: 0.25rem 2rem; transform: rotate(45deg); font-size: 0.75rem; font-weight: bold;">Top Pick</div>' : ''}
                            <h4 style="color: ${colorClass}; margin-bottom: 0.5rem;"><i class="fa-solid fa-seedling"></i> ${item.crop}</h4>
                            <p style="font-size: 0.9rem; color: var(--text-muted);">${item.match_score}% Match based on your precise land parameters.</p>
                            <div style="margin-top: 1rem; background: #e2e8f0; height: 8px; border-radius: 4px; overflow: hidden;">
                                <div style="width: ${item.match_score}%; background: ${colorClass}; height: 100%;"></div>
                            </div>
                        </div>`;
                    });

                    // Automatically save Top Pick to DB if logged in
                    const recordPayload = { ...payload, recommended_crop: res.data[0].crop };
                    await fetch('api/records.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(recordPayload)
                    });

                } else {
                    resultsContainer.innerHTML = `<div style="color: #991b1b;"><i class="fa-solid fa-triangle-exclamation"></i> ${res.message}</div>`;
                }

            } catch(e) {
                loading.style.display = 'none';
                content.style.display = 'block';
                resultsContainer.innerHTML = `<div style="color: #991b1b;"><i class="fa-solid fa-triangle-exclamation"></i> System error. Ensure PHP API is running.</div>`;
            }

            btn.innerHTML = '<span>Analyze Land Suitability</span><i class="fa-solid fa-wand-magic-sparkles"></i>';
            btn.disabled = false;
        });
        // Logout function
        async function logout() {
            try {
                await fetch('api/auth.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({action: 'logout'})
                });
                window.location.href = 'index.html';
            } catch(e) {
                console.error("Logout failed");
            }
        }

        document.addEventListener('DOMContentLoaded', async () => {
            // Check session
            try {
                const sessionRes = await fetch('api/auth.php?action=session');
                const sessionData = await sessionRes.json();
                if(sessionRes.ok) {
                    if (document.getElementById('nav-name')) {
                        document.getElementById('nav-name').innerText = sessionData.user.first_name;
                    }
                    if (document.getElementById('nav-avatar')) {
                        document.getElementById('nav-avatar').src = `https://ui-avatars.com/api/?name=${sessionData.user.first_name}&background=059669&color=fff`;
                    }
                }
            } catch(e) {
                console.error("Session check failed");
            }
        });
    </script>
</body>
</html>
