<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Grow Your Crops India</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #10b981; --primary-dark: #059669;
            --bg: #f1f5f9; --surface: #ffffff;
            --text: #334155; --text-light: #64748b;
            --border: #e2e8f0; --sidebar-width: 260px;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Outfit', sans-serif; }
        body { background: var(--bg); color: var(--text); display: flex; min-height: 100vh; overflow-x: hidden; }
        
        .sidebar { width: var(--sidebar-width); background: #0f172a; color: white; position: fixed; height: 100vh; left: 0; top: 0; padding: 1.5rem; display: flex; flex-direction: column; }
        .sidebar-header { display: flex; align-items: center; gap: 0.8rem; font-size: 1.2rem; font-weight: 700; margin-bottom: 3rem; color: white; text-decoration: none; }
        .sidebar-header i { color: var(--primary); }
        .nav-links { display: flex; flex-direction: column; gap: 0.5rem; flex-grow: 1; }
        .nav-links a { color: #cbd5e1; text-decoration: none; padding: 0.8rem 1rem; border-radius: 8px; display: flex; align-items: center; gap: 1rem; transition: 0.2s; font-weight: 500; }
        .nav-links a:hover, .nav-links a.active { background: #1e293b; color: white; }
        .nav-links a.active i { color: var(--primary); }
        .logout-btn { color: #f87171; text-decoration: none; padding: 0.8rem 1rem; display: flex; align-items: center; gap: 1rem; transition: 0.2s; font-weight: 500; cursor: pointer; border-radius: 8px; border: none; background: transparent; width: 100%; text-align: left;}
        .logout-btn:hover { background: rgba(248, 113, 113, 0.1); }
        
        .main-content { margin-left: var(--sidebar-width); flex: 1; padding: 2rem 3rem; width: calc(100% - var(--sidebar-width)); }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2.5rem; }
        .header h1 { font-size: 1.8rem; color: #1e293b; }
        
        /* Shared Dashboard Components */
        .card { background: var(--surface); padding: 1.5rem; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid var(--border); }
        .table-wrapper { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { padding: 1rem; text-align: left; border-bottom: 1px solid var(--border); }
        th { background: #f8fafc; font-weight: 600; color: #475569; }
        .btn { padding: 0.5rem 1rem; background: var(--primary); color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 500;}
        .btn-sm { padding: 0.3rem 0.6rem; font-size: 0.85rem; }
        .btn-danger { background: #ef4444; }
        .btn-danger:hover { background: #dc2626; }
        
        /* Forms */
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 0.4rem; font-size: 0.9rem; font-weight: 500; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 0.7rem; border: 1px solid var(--border); border-radius: 6px; font-family: inherit; }
        
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; }
        .modal.active { display: flex; }
        .modal-content { background: white; padding: 2rem; border-radius: 12px; width: 90%; max-width: 600px; max-height: 90vh; overflow-y: auto; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
        .close-modal { cursor: pointer; font-size: 1.5rem; color: var(--text-light); border: none; background: none; }
    </style>
</head>
<body>
    <aside class="sidebar">
        <a href="dashboard.php" class="sidebar-header">
            <i class="fa-solid fa-seedling"></i> GrowYourCrops Admin
        </a>
        <nav class="nav-links">
            <a href="dashboard.php" class="<?= $current_page == 'dashboard.php' ? 'active' : '' ?>">
                <i class="fa-solid fa-gauge"></i> Overview
            </a>
            <a href="users.php" class="<?= $current_page == 'users.php' ? 'active' : '' ?>">
                <i class="fa-solid fa-users"></i> Manage Users
            </a>
            <a href="crops.php" class="<?= $current_page == 'crops.php' ? 'active' : '' ?>">
                <i class="fa-solid fa-leaf"></i> Manage Crops
            </a>
        </nav>
        <button class="logout-btn" onclick="logout()">
            <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
        </button>
    </aside>

    <script>
        async function logout() {
            if(confirm('Are you sure you want to logout?')) {
                await fetch('../api/admin_auth.php?action=logout');
                window.location.href = 'index.php';
            }
        }
    </script>
    
    <main class="main-content">
