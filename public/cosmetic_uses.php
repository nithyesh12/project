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
    <title>Cosmetic Uses of Crops - GROW YOUR CROPS</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,600;1,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .filter-container {
            display: flex; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap;
            background: var(--bg-surface); padding: 1.5rem; border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }
        .filter-container input, .filter-container select {
            flex: 1; min-width: 200px;
        }
        .crop-grid {
            display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 2rem;
        }
        .crop-card {
            background: var(--bg-surface); 
            border-radius: var(--border-radius); 
            overflow: hidden; 
            box-shadow: var(--box-shadow);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
        }
        .crop-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--hover-shadow);
        }
        .crop-img {
            width: 100%; height: 200px; object-fit: cover;
        }
        .card-content {
            padding: 1.5rem;
        }
        .card-subtitle {
            color: var(--primary-color);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        .card-title {
            margin-bottom: 1rem;
            font-family: 'Playfair Display', serif;
        }
        .use-icon {
            color: var(--secondary-color);
            margin-right: 0.5rem;
        }
        .detail-item {
            margin-bottom: 0.8rem;
            font-size: 0.95rem;
            line-height: 1.5;
        }
        .detail-label {
            font-weight: 600;
            color: var(--text-dark);
        }
        .hidden { display: none !important; }
        .error-message {
            grid-column: 1 / -1;
            text-align: center;
            padding: 2rem;
            background: #fff3cd;
            color: #856404;
            border-radius: var(--border-radius);
        }
    </style>
</head>
<body style="padding-top: 80px; background-color: var(--bg-main);">
    
    <!-- Navigation -->
    <nav class="navbar scrolled" id="navbar">
        <div class="container nav-container">
            <a href="index.html" class="logo">
                <i class="fa-solid fa-leaf"></i>
                <span>Grow Your Crops</span>
            </a>
            <ul class="nav-links">
                <li><a href="index.html">Home</a></li>
                <li><a href="crops.php">Crop Encyclopedia</a></li>
                <li><a href="recommendation.php">AI Recommendation</a></li>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="cosmetic_uses.php" class="active">Cosmetic Uses</a></li>
                
                <?php if($is_logged_in): ?>
                <li>
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <button onclick="logout()" class="btn btn-outline" style="padding: 0.4rem 1rem; font-size: 0.85rem;">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
                        </button>
                    </div>
                </li>
                <?php else: ?>
                <li><a href="auth.html" class="btn btn-outline">Sign In</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <main class="container" style="padding: 3rem 1.5rem;">
        <div class="section-header">
            <span class="badge" style="margin-bottom: 1rem;"><i class="fa-solid fa-spa"></i> Natural Beauty</span>
            <h2>Cosmetic <span class="highlight">Uses of Crops</span></h2>
            <p>Discover the incredible skincare, hair care, and medicinal benefits hidden in everyday agricultural crops.</p>
        </div>

        <!-- Filters -->
        <div class="filter-container animate-fade-in">
            <input type="text" id="searchInput" placeholder="Search by crop name (e.g. Turmeric, Aloe Vera)...">
            <select id="categoryFilter">
                <option value="All">All Categories</option>
                <option value="Skin Care">Skin Care</option>
                <option value="Hair Care">Hair Care</option>
                <option value="Medicinal">Medicinal</option>
            </select>
        </div>

        <!-- Grid -->
        <div id="cosmeticsGrid" class="crop-grid animate-fade-in delay-1">
            <!-- Data injected via JS -->
            <div style="grid-column: 1 / -1; text-align: center;"><i class="fa-solid fa-spinner fa-spin"></i> Loading cosmetic data...</div>
        </div>

    </main>

    <footer>
        <div class="container text-center">
            <p>&copy; 2026 GROW YOUR CROPS. Architecture powered by PHP & Python.</p>
        </div>
    </footer>

    <script src="assets/js/cosmetics.js"></script>
    <script>
        async function logout() {
            try {
                await fetch('api/auth.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({action: 'logout'})
                });
                window.location.reload();
            } catch(e) {
                console.error("Logout failed");
            }
        }
    </script>
</body>
</html>
