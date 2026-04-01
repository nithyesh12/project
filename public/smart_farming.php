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
    <title>Smart Farming Modules - Grow Your Crops India</title>
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,600;1,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        /* Structural CSS (matching dashboard logic) */
        :root {
            --sidebar-width: 250px;
            --header-height: 70px;
            --primary-dark: #064e3b; /* Deep green */
            --primary-color: #059669; /* Main emerald green */
            --primary-light: #10b981; 
            --accent-color: #f59e0b; /* Amber */
            --bg-main: #f8fafc;
        }
        body {
            background-color: var(--bg-main);
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            display: flex;
            font-family: 'Outfit', sans-serif;
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
            border: 2px solid #e2e8f0;
        }
        
        /* Dashboard Container Areas */
        .dashboard-container {
            padding: 2rem;
            flex-grow: 1;
        }

        /* --- Smart Modules Custom UI --- */
        .module-header {
            text-align: center;
            margin-bottom: 3rem;
            animation: fadeInDown 0.6s ease;
        }
        .module-header h1 {
            color: var(--primary-dark);
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        .module-header p {
            color: #64748b;
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
        }

        .module-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.04);
            margin-bottom: 3rem;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            animation: fadeInUp 0.8s ease;
        }
        .module-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        }

        .module-topbar {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary-color));
            padding: 1.5rem 2rem;
            color: white;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .module-topbar i {
            font-size: 1.8rem;
            color: var(--accent-color);
        }
        .module-topbar h2 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .module-body {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            padding: 2rem;
        }
        @media (max-width: 1024px) {
            .module-body { grid-template-columns: 1fr; }
        }

        /* Form Inputs */
        .form-group {
            margin-bottom: 1.25rem;
        }
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: #334155;
            font-weight: 600;
            font-size: 0.95rem;
        }
        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-family: 'Outfit', sans-serif;
            font-size: 1rem;
            color: #1e293b;
            background: #f8fafc;
            transition: all 0.2s;
            box-sizing: border-box;
        }
        .form-control:focus {
            outline: none;
            border-color: var(--primary-light);
            background: white;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
        }
        
        .btn-smart {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s, transform 0.1s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-smart:hover {
            background: var(--primary-dark);
        }
        .btn-smart:active {
            transform: scale(0.98);
        }
        .btn-reset {
            background: #e2e8f0;
            color: #475569;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
            margin-left: 10px;
        }
        .btn-reset:hover {
            background: #cbd5e1;
            color: #1e293b;
        }

        /* Output Areas */
        .result-container {
            background: #f0fdf4;
            border: 2px dashed var(--primary-light);
            border-radius: 12px;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            min-height: 250px;
            position: relative;
        }
        .result-placeholder i {
            font-size: 3rem;
            color: #94a3b8;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
        .result-placeholder p {
            color: #64748b;
            font-size: 1.05rem;
            margin: 0;
        }

        /* Output Cards */
        .result-data {
            display: none;
            width: 100%;
            animation: fadeIn 0.5s ease;
        }
        .result-data h3 {
            color: var(--primary-dark);
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
        }
        .data-card {
            background: white;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.02);
            border-left: 4px solid var(--primary-color);
            text-align: left;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .data-card-icon {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: #ecfdf5;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
        }
        .data-card-content h4 {
            margin: 0 0 0.2rem 0;
            font-size: 0.9rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .data-card-content p {
            margin: 0;
            font-size: 1.2rem;
            font-weight: 600;
            color: #1e293b;
        }
        .highlight-value {
            color: var(--primary-color);
            font-weight: 700;
        }
        
        /* Pest image specific */
        .pest-img-placeholder {
            width: 100%;
            height: 180px;
            background: #e2e8f0;
            border-radius: 8px;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }
        .pest-img-placeholder img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: none;
        }

        .error-message {
            color: #ef4444;
            font-size: 0.85rem;
            margin-top: 5px;
            display: none;
        }

        /* Animations */
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 992px) {
            .sidebar { transform: translateX(-100%); transition: 0.3s; }
            .main-wrapper { margin-left: 0; width: 100%; }
        }
    </style>
</head>
<body>

    <!-- Sidebar Navigation -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            <i class="fa-solid fa-leaf"></i>
            <span>GrowYourCrops</span>
        </div>
        <ul class="sidebar-menu">
            <li><a href="index.html"><i class="fa-solid fa-house-chimney"></i> Home</a></li>
            <li><a href="dashboard.php"><i class="fa-solid fa-chart-line"></i> Dashboard</a></li>
            <li><a href="crops.php"><i class="fa-solid fa-book-open"></i> Crop Encyclopedia</a></li>
            <li><a href="recommendation.php"><i class="fa-solid fa-wand-magic-sparkles"></i> Recommendations</a></li>
            <li><a href="weather.php"><i class="fa-solid fa-cloud-sun"></i> Weather Insights</a></li>
            <li><a href="smart_farming.php" class="active"><i class="fa-solid fa-microchip"></i> Smart Modules</a></li>
        </ul>
        <div style="padding: 1.5rem; border-top: 1px solid rgba(255,255,255,0.1); margin-top: auto;">
            <a href="javascript:void(0)" onclick="logout()" style="color: #fca5a5; display:flex; align-items:center; gap:10px; text-decoration:none;"><i class="fa-solid fa-arrow-right-from-bracket"></i> Sign Out</a>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="main-wrapper">
        <!-- Top Navbar -->
        <header class="top-navbar">
            <div style="display:flex; align-items:center; gap:1rem;">
                <h2 style="margin:0; font-family:'Outfit'; font-size:1.25rem; font-weight:600;"><i class="fa-solid fa-microchip" style="color:var(--text-muted);"></i> Smart Farming Toolkit</h2>
            </div>
            <div class="top-nav-right">
                <i class="fa-solid fa-bell" style="font-size:1.2rem; color:var(--text-muted); cursor:pointer;"></i>
                <div class="user-profile">
                    <img src="https://ui-avatars.com/api/?name=Farmer&background=059669&color=fff" id="nav-avatar" alt="Avatar">
                    <span id="nav-name" style="font-weight: 500;">Farmer</span>
                </div>
            </div>
        </header>

        <!-- Dynamic Content Area -->
        <div class="dashboard-container">
            
            <div class="module-header">
                <h1>Smart Agriculture Modules</h1>
                <p>Enhance your crop yield with data-driven irrigation calculations, precise fertilizer recommendations, and intelligent pest management tools.</p>
            </div>

            <!-- MODULE 1: IRRIGATION MANAGEMENT -->
            <div class="module-card">
                <div class="module-topbar" style="background: linear-gradient(135deg, #0369a1, #0ea5e9);">
                    <i class="fa-solid fa-droplet"></i>
                    <h2>Irrigation Management</h2>
                </div>
                <div class="module-body">
                    <div class="form-area">
                        <form id="irrigation-form" onsubmit="calculateIrrigation(event)">
                            <div class="form-group">
                                <label class="form-label">Crop Type</label>
                                <select class="form-control" id="irig-crop" required>
                                    <option value="" disabled selected>Select Crop</option>
                                    <option value="wheat">Wheat (High Water Needs)</option>
                                    <option value="rice">Rice/Paddy (Very High Water Needs)</option>
                                    <option value="maize">Maize (Moderate Water Needs)</option>
                                    <option value="cotton">Cotton (Moderate Water Needs)</option>
                                    <option value="millets">Millets (Low Water Needs)</option>
                                </select>
                                <div class="error-message" id="err-irig-crop">Please select a crop type.</div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Soil Type</label>
                                <select class="form-control" id="irig-soil" required>
                                    <option value="" disabled selected>Select Soil Type</option>
                                    <option value="sandy">Sandy (Low Retention)</option>
                                    <option value="loamy">Loamy (Optimal Retention)</option>
                                    <option value="clay">Clay (High Retention)</option>
                                </select>
                                <div class="error-message" id="err-irig-soil">Please select a soil type.</div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Land Size (in Hectares)</label>
                                <input type="number" step="0.1" min="0.1" class="form-control" id="irig-size" placeholder="e.g., 2.5" required>
                                <div class="error-message" id="err-irig-size">Please enter a valid land size.</div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Current Weather (Optional)</label>
                                <select class="form-control" id="irig-weather">
                                    <option value="normal" selected>Normal/Average</option>
                                    <option value="hot">Hot & Dry</option>
                                    <option value="rainy">Recent Rainfall</option>
                                </select>
                            </div>

                            <div style="margin-top: 1.5rem;">
                                <button type="submit" class="btn-smart" style="background: #0284c7;"><i class="fa-solid fa-calculator"></i> Calculate Water Output</button>
                                <button type="button" class="btn-reset" onclick="resetForm('irrigation-form', 'irig-result-placeholder', 'irig-result-data')">Reset</button>
                            </div>
                        </form>
                    </div>

                    <div class="result-container" style="background: #f0f9ff; border-color: #7dd3fc;">
                        <div id="irig-result-placeholder" class="result-placeholder">
                            <i class="fa-solid fa-faucet-drip" style="color: #7dd3fc;"></i>
                            <p>Fill the form to calculate your detailed water requirement and irrigation schedule.</p>
                        </div>
                        <div id="irig-result-data" class="result-data">
                            <h3 style="color: #0369a1;"><i class="fa-solid fa-circle-check" style="color: #0ea5e9;"></i> Irrigation Plan</h3>
                            
                            <div class="data-card" style="border-color: #0ea5e9;">
                                <div class="data-card-icon" style="background: #e0f2fe; color: #0284c7;"><i class="fa-solid fa-glass-water"></i></div>
                                <div class="data-card-content">
                                    <h4>Total Daily Requirement</h4>
                                    <p><span class="highlight-value" id="out-irig-liters" style="color: #0284c7;">0</span> Liters/Day</p>
                                </div>
                            </div>

                            <div class="data-card" style="border-color: #0ea5e9;">
                                <div class="data-card-icon" style="background: #e0f2fe; color: #0284c7;"><i class="fa-solid fa-clock-rotate-left"></i></div>
                                <div class="data-card-content">
                                    <h4>Recommended Frequency</h4>
                                    <p id="out-irig-freq">Every X days</p>
                                </div>
                            </div>

                            <div class="data-card" style="border-color: #0ea5e9;">
                                <div class="data-card-icon" style="background: #e0f2fe; color: #0284c7;"><i class="fa-solid fa-lightbulb"></i></div>
                                <div class="data-card-content">
                                    <h4>System Suggestion</h4>
                                    <p id="out-irig-method" style="font-size:1rem; font-weight:500; color:#334155;">Drip Irrigation recommended.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MODULE 2: FERTILIZER RECOMMENDATION -->
            <div class="module-card">
                <div class="module-topbar" style="background: linear-gradient(135deg, #b45309, #f59e0b);">
                    <i class="fa-solid fa-flask-vial"></i>
                    <h2>Fertilizer Recommendation</h2>
                </div>
                <div class="module-body">
                    <div class="form-area">
                        <form id="fert-form" onsubmit="calculateFertilizer(event)">
                            <div class="form-group">
                                <label class="form-label">Crop Type</label>
                                <select class="form-control" id="fert-crop" required>
                                    <option value="" disabled selected>Select Crop</option>
                                    <option value="wheat">Wheat</option>
                                    <option value="tomato">Tomato</option>
                                    <option value="potato">Potato</option>
                                    <option value="sugar">Sugarcane</option>
                                    <option value="legumes">Legumes/Pulses</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Soil Type</label>
                                <select class="form-control" id="fert-soil" required>
                                    <option value="" disabled selected>Select Soil Condition</option>
                                    <option value="deficient">Nutrient Deficient (Low Organic Matter)</option>
                                    <option value="normal">Normal / Balanced Soil</option>
                                    <option value="rich">Rich Alluvial Soil</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Current Growth Stage</label>
                                <select class="form-control" id="fert-stage" required>
                                    <option value="" disabled selected>Select Phase</option>
                                    <option value="seedling">Seedling / Early Vegetative</option>
                                    <option value="vegetative">Active Vegetative Growth</option>
                                    <option value="flowering">Flowering / Fruiting Stage</option>
                                </select>
                            </div>

                            <div style="margin-top: 1.5rem;">
                                <button type="submit" class="btn-smart" style="background: #d97706;"><i class="fa-solid fa-wand-magic-sparkles"></i> Get Recommendation</button>
                                <button type="button" class="btn-reset" onclick="resetForm('fert-form', 'fert-result-placeholder', 'fert-result-data')">Reset</button>
                            </div>
                        </form>
                    </div>

                    <div class="result-container" style="background: #fffbeb; border-color: #fcd34d;">
                        <div id="fert-result-placeholder" class="result-placeholder">
                            <i class="fa-solid fa-seedling" style="color: #fcd34d;"></i>
                            <p>Select crop parameters to get optimal NPK ratios and fertilizer suggestions.</p>
                        </div>
                        <div id="fert-result-data" class="result-data">
                            <h3 style="color: #b45309;"><i class="fa-solid fa-circle-check" style="color:#f59e0b;"></i> Fertilizer Profile</h3>
                            
                            <div class="data-card" style="border-color: #f59e0b;">
                                <div class="data-card-icon" style="background: #fef3c7; color: #d97706;"><i class="fa-solid fa-chart-pie"></i></div>
                                <div class="data-card-content">
                                    <h4>Recommended NPK Ratio</h4>
                                    <p id="out-fert-npk" class="highlight-value" style="color: #d97706;">20:20:20</p>
                                </div>
                            </div>

                            <div class="data-card" style="border-color: #f59e0b;">
                                <div class="data-card-icon" style="background: #fef3c7; color: #d97706;"><i class="fa-solid fa-box-open"></i></div>
                                <div class="data-card-content">
                                    <h4>Primary Fertilizer Source</h4>
                                    <p id="out-fert-type" style="font-size:1.1rem;">Urea + DAP</p>
                                </div>
                            </div>

                            <div class="data-card" style="border-color: #f59e0b;">
                                <div class="data-card-icon" style="background: #fef3c7; color: #d97706;"><i class="fa-solid fa-calendar-day"></i></div>
                                <div class="data-card-content">
                                    <h4>Application Timing</h4>
                                    <p id="out-fert-timing" style="font-size:1rem; font-weight:500; color:#334155;">Apply as basal dose during sowing.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MODULE 3: PEST & DISEASE MANAGEMENT -->
            <div class="module-card">
                <div class="module-topbar" style="background: linear-gradient(135deg, #9f1239, #e11d48);">
                    <i class="fa-solid fa-bug"></i>
                    <h2>Pest & Disease Diagnosis</h2>
                </div>
                <div class="module-body">
                    <div class="form-area">
                        <form id="pest-form" onsubmit="diagnosePest(event)">
                            <div class="form-group">
                                <label class="form-label">Crop Type</label>
                                <select class="form-control" id="pest-crop" required>
                                    <option value="" disabled selected>Select Crop</option>
                                    <option value="cotton">Cotton</option>
                                    <option value="rice">Rice</option>
                                    <option value="tomato">Tomato / Vegetables</option>
                                    <option value="fruits">Fruit Orchards</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Observed Symptoms</label>
                                <select class="form-control" id="pest-symptom" required>
                                    <option value="" disabled selected>What do you see?</option>
                                    <option value="yellowing">Yellowing of leaves / Wilting</option>
                                    <option value="spots">White/Brown spots on leaves</option>
                                    <option value="holes">Holes in leaves / Chewed edges</option>
                                    <option value="rot">Fruit or Stem rotting</option>
                                </select>
                            </div>

                            <div style="margin-top: 1.5rem;">
                                <button type="submit" class="btn-smart" style="background: #e11d48;"><i class="fa-solid fa-magnifying-glass"></i> Diagnose Problem</button>
                                <button type="button" class="btn-reset" onclick="resetForm('pest-form', 'pest-result-placeholder', 'pest-result-data')">Reset</button>
                            </div>
                        </form>
                    </div>

                    <div class="result-container" style="background: #fff1f2; border-color: #fda4af;">
                        <div id="pest-result-placeholder" class="result-placeholder">
                            <i class="fa-solid fa-magnifying-glass-chart" style="color: #fda4af;"></i>
                            <p>Describe the crop symptoms to identify pests or diseases and get treatment plans.</p>
                        </div>
                        <div id="pest-result-data" class="result-data">
                            
                            <div class="pest-img-placeholder">
                                <i class="fa-solid fa-bug" id="pest-icon-placeholder" style="font-size: 4rem; color: #f43f5e; opacity: 0.3;"></i>
                                <img id="pest-img" src="" alt="Pest Image" style="display:none;">
                            </div>

                            <h3 style="color: #9f1239; margin-bottom:1rem; font-size:1.3rem;"><i class="fa-solid fa-triangle-exclamation" style="color:#e11d48;"></i> <span id="out-pest-name">Possible Infection</span></h3>
                            
                            <div class="data-card" style="border-color: #e11d48; margin-bottom: 0.8rem;">
                                <div class="data-card-icon" style="background: #ffe4e6; color: #be123c;"><i class="fa-solid fa-shield-halved"></i></div>
                                <div class="data-card-content">
                                    <h4>Prevention Method</h4>
                                    <p id="out-pest-prevent" style="font-size:0.95rem; font-weight:500; color:#334155;">Crop rotation.</p>
                                </div>
                            </div>

                            <div class="data-card" style="border-color: #e11d48;">
                                <div class="data-card-icon" style="background: #ffe4e6; color: #be123c;"><i class="fa-solid fa-spray-can"></i></div>
                                <div class="data-card-content">
                                    <h4>Treatment Suggestion</h4>
                                    <p id="out-pest-treat" style="font-size:0.95rem; font-weight:500; color:#334155;">Apply Neem oil.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- JavaScript Logic for Smart Modules -->
    <script>
        // Authentication disconnect
        async function logout() {
            try {
                await fetch('api/auth.php', { method: 'POST', body: JSON.stringify({action: 'logout'}) });
                window.location.href = 'index.html';
            } catch(e) { console.error("Logout failed."); }
        }

        // Generic UI toggle for results
        function showResult(placeholderId, dataId) {
            document.getElementById(placeholderId).style.display = 'none';
            document.getElementById(dataId).style.display = 'block';
        }

        function resetForm(formId, placeholderId, dataId) {
            document.getElementById(formId).reset();
            document.getElementById(placeholderId).style.display = 'flex';
            document.getElementById(dataId).style.display = 'none';
        }

        /* ----------------------------------------------------
           1. Irrigation Calculation Logic
        ---------------------------------------------------- */
        function calculateIrrigation(e) {
            e.preventDefault();
            
            const crop = document.getElementById('irig-crop').value;
            const soil = document.getElementById('irig-soil').value;
            const size = parseFloat(document.getElementById('irig-size').value);
            const weather = document.getElementById('irig-weather').value;

            // Base requirement in liters per hectare per day
            let baseWater = 40000; 

            // Crop multiplier
            const cropFactors = { 'wheat': 1.2, 'rice': 2.5, 'maize': 1.0, 'cotton': 1.1, 'millets': 0.6 };
            baseWater *= cropFactors[crop];

            // Soil multiplier (Sandy needs more frequent water, Clay retains more)
            const soilFactors = { 'sandy': 1.3, 'loamy': 1.0, 'clay': 0.8 };
            baseWater *= soilFactors[soil];

            // Weather modifier
            if(weather === 'hot') baseWater *= 1.4;
            if(weather === 'rainy') baseWater *= 0.3;

            const totalWater = Math.round(baseWater * size);

            // Frequency and Method Logic
            let frequency = "Every 3-4 days";
            let method = "Sprinkler Irrigation recommended";
            
            if (soil === 'sandy' || weather === 'hot') frequency = "Every 1-2 days (Daily)";
            if (soil === 'clay') frequency = "Every 5-7 days";
            if (weather === 'rainy') frequency = "Halt irrigation until topsoil dries";

            if (crop === 'rice') method = "Flood Irrigation (maintaining water levels)";
            if (crop === 'cotton' || crop === 'tomato') method = "Drip Irrigation highly recommended to avoid humidity clustering.";

            // UI Update
            document.getElementById('out-irig-liters').innerText = totalWater.toLocaleString();
            document.getElementById('out-irig-freq').innerText = frequency;
            document.getElementById('out-irig-method').innerText = method;

            showResult('irig-result-placeholder', 'irig-result-data');
        }

        /* ----------------------------------------------------
           2. Fertilizer Recommendation Logic
        ---------------------------------------------------- */
        function calculateFertilizer(e) {
            e.preventDefault();

            const crop = document.getElementById('fert-crop').value;
            const soil = document.getElementById('fert-soil').value;
            const stage = document.getElementById('fert-stage').value;

            let npk = "10:10:10";
            let type = "Balanced NPK Mix";
            let timing = "General application";

            // Stage logic (N for vegetative, P for Root/Seedling, K for fruiting)
            if (stage === 'seedling') {
                npk = "10:20:10";
                type = "DAP (Diammonium Phosphate) dominant";
                timing = "Apply near root zone as basal or early top-dressing.";
            } else if (stage === 'vegetative') {
                npk = "20:10:10";
                type = "Urea / High Nitrogen Mix";
                timing = "Broadcast evenly prior to irrigation.";
            } else if (stage === 'flowering') {
                npk = "10:10:20";
                type = "MOP (Muriate of Potash) dominant";
                timing = "Apply carefully avoiding direct contact with young buds.";
            }

            // Crop specific adjustments
            if (crop === 'legumes') {
                npk = stage === 'vegetative' ? "0:20:20" : "5:20:20"; 
                type = "Low N (Nitrogen fixing) - High Phosphorus";
            } else if (crop === 'tomato' && stage === 'flowering') {
                type += " + Calcium boost (prevent blossom end rot)";
            }

            // Soil modifier
            if (soil === 'deficient') {
                type += " & Add Organic Compost/Manure";
                timing += " Increase dosage by 15%.";
            } else if (soil === 'rich') {
                timing += " Decrease chemical dosage by 20% to avoid burning.";
            }

            // UI Update
            document.getElementById('out-fert-npk').innerText = npk;
            document.getElementById('out-fert-type').innerText = type;
            document.getElementById('out-fert-timing').innerText = timing;

            showResult('fert-result-placeholder', 'fert-result-data');
        }

        /* ----------------------------------------------------
           3. Pest & Disease Logic
        ---------------------------------------------------- */
        function diagnosePest(e) {
            e.preventDefault();

            const crop = document.getElementById('pest-crop').value;
            const symptom = document.getElementById('pest-symptom').value;

            let name = "Unknown Stress";
            let prevent = "Maintain optimal spacing and field sanitation.";
            let treat = "Consult local agricultural extension for broad-spectrum analysis.";
            let imgIcon = "fa-bug";

            // Rule based mapping
            if (symptom === 'holes') {
                name = "Leaf-chewing Caterpillars / Armyworms";
                prevent = "Use light traps and pheromone traps to monitor adult moth activity. Encourage natural predators like birds.";
                treat = "Apply Bacillus thuringiensis (Bt) or Neem seed kernel extract (NSKE 5%).";
                imgIcon = "fa-worm";
            } 
            else if (symptom === 'spots') {
                if (crop === 'tomato' || crop === 'fruits') {
                    name = "Blight / Leaf Spot Disease (Fungal)";
                    prevent = "Avoid overhead irrigation. Ensure adequate air circulation via pruning.";
                    treat = "Spray copper-based fungicides or Mancozeb at early stages of infection.";
                    imgIcon = "fa-bacterium";
                } else {
                    name = "Brown Spot / Blast Disease";
                    prevent = "Use certified disease-free seeds. Practice crop rotation.";
                    treat = "Apply preventative fungicide sprays before the wet season peaks.";
                    imgIcon = "fa-disease";
                }
            }
            else if (symptom === 'yellowing') {
                if (crop === 'cotton') {
                    name = "Aphids / Whiteflies (Sucking Pests) or Nitrogen Deficiency";
                    prevent = "Install yellow sticky traps (10-15 per acre). Remove host weeds.";
                    treat = "Spray Neem oil (1500 ppm) or Imidacloprid if population exceeds economic threshold level (ETL).";
                    imgIcon = "fa-mosquito";
                } else {
                    name = "Root Rot or Nematode Infection";
                    prevent = "Improve soil drainage. Crop rotation with non-host crops like marigold.";
                    treat = "Soil drenching with Trichoderma viride or standard nematicides if severe.";
                    imgIcon = "fa-seedling";
                }
            }
            else if (symptom === 'rot') {
                name = "Fruit Rot / Anthracnose";
                prevent = "Harvest timely. Do not leave decaying fruits in the field.";
                treat = "Apply biological control agents (Pseudomonas fluorescens) early in the fruiting cycle.";
                imgIcon = "fa-apple-whole";
            }

            // UI Update
            document.getElementById('out-pest-name').innerText = name;
            document.getElementById('out-pest-prevent').innerText = prevent;
            document.getElementById('out-pest-treat').innerText = treat;
            
            // Swap icon placeholder based on pest type
            const pestIcon = document.getElementById('pest-icon-placeholder');
            pestIcon.className = `fa-solid ${imgIcon}`;

            showResult('pest-result-placeholder', 'pest-result-data');
        }

        // Fetch User Info for Header on load
        document.addEventListener('DOMContentLoaded', async () => {
            try {
                const sessionRes = await fetch('api/auth.php?action=session');
                if(!sessionRes.ok) return;
                const sessionData = await sessionRes.json();
                document.getElementById('nav-name').innerText = sessionData.user.first_name;
                document.getElementById('nav-avatar').src = `https://ui-avatars.com/api/?name=${sessionData.user.first_name}&background=059669&color=fff`;
            } catch(e) { console.error("Session data unavailable"); }
        });
    </script>
</body>
</html>
