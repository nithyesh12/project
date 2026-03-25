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
    <title>Crop Encyclopedia - Grow Your Crops India</title>
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
            display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 2rem;
        }
        .crop-card {
            cursor: pointer; position: relative;
        }
        .view-more-container { text-align: center; margin: 3rem 0; }
        .hidden { display: none !important; }
    </style>
</head>
<body style="padding-top: 80px; background-color: var(--bg-main);">
    
    <!-- Navigation -->
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
            <span class="badge" style="margin-bottom: 1rem;"><i class="fa-solid fa-book-open"></i> Huge Library</span>
            <h2>Crop <span class="highlight">Encyclopedia</span></h2>
            <p>Explore detailed cultivation practices, soil requirements, and ideal climates for over 50 native Indian crops.</p>
        </div>

        <!-- Filters -->
        <div class="filter-container animate-fade-in">
            <input type="text" id="searchInput" placeholder="Search crop by name (e.g. Rice, Mango)...">
            <select id="seasonFilter">
                <option value="All">All Seasons</option>
                <option value="Kharif">Kharif (Monsoon)</option>
                <option value="Rabi">Rabi (Winter)</option>
                <option value="Zaid">Zaid (Summer)</option>
                <option value="Perennial">Perennial</option>
            </select>
        </div>

        <!-- Grid -->
        <div id="cropGrid" class="crop-grid animate-fade-in delay-1">
            <!-- Crops injected via JS -->
        </div>

        <div class="view-more-container animate-fade-in delay-2">
            <button id="loadMoreBtn" class="btn btn-primary btn-large">
                <i class="fa-solid fa-angles-down"></i> View More Crops
            </button>
        </div>
    </main>

    <footer>
        <div class="container text-center">
            <p>&copy; 2026 Grow Your Crops India. Architecture powered by PHP & Python.</p>
        </div>
    </footer>

    <script src="assets/js/encyclopedia.js"></script>
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
