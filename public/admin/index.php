<?php
session_start();
if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Grow Your Crops India</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #10b981;
            --primary-dark: #059669;
            --bg: #f8fafc;
            --surface: #ffffff;
            --text: #334155;
            --border: #e2e8f0;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Outfit', sans-serif; color: var(--text); }
        body { background: var(--bg); display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .login-card { background: var(--surface); padding: 3rem; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); width: 100%; max-width: 400px; text-align: center; border: 1px solid var(--border); }
        .logo i { font-size: 2.5rem; color: var(--primary); margin-bottom: 1rem; }
        h2 { margin-bottom: 0.5rem; font-size: 1.5rem; }
        p { color: #64748b; margin-bottom: 2rem; font-size: 0.9rem; }
        .form-group { margin-bottom: 1.2rem; text-align: left; }
        label { display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 500; }
        input { width: 100%; padding: 0.8rem 1rem; border: 1px solid var(--border); border-radius: 6px; font-size: 0.95rem; outline: none; transition: border-color 0.2s; }
        input:focus { border-color: var(--primary); }
        .btn { width: 100%; padding: 0.8rem; background: var(--primary); color: white; border: none; border-radius: 6px; font-weight: 600; font-size: 1rem; cursor: pointer; transition: background 0.2s; }
        .btn:hover { background: var(--primary-dark); }
        #errorMsg { color: #ef4444; font-size: 0.85rem; margin-top: 1rem; display: none; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="logo"><i class="fa-solid fa-seedling"></i></div>
        <h2>Admin Management</h2>
        <p>Secure portal for authorized personnel</p>
        
        <form id="adminLoginForm">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" id="email" required placeholder="admin@growyourcrops.com">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" id="password" required placeholder="••••••••">
            </div>
            <button type="submit" class="btn">Authenticating <i class="fa-solid fa-lock"></i></button>
            <div id="errorMsg"></div>
        </form>
    </div>

    <script>
        document.getElementById('adminLoginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = e.target.querySelector('.btn');
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
            try {
                const res = await fetch('../api/admin_auth.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({
                        action: 'login',
                        email: document.getElementById('email').value,
                        password: document.getElementById('password').value
                    })
                });
                const data = await res.json();
                if(data.status === 'success') {
                    window.location.href = 'dashboard.php';
                } else {
                    document.getElementById('errorMsg').textContent = data.message;
                    document.getElementById('errorMsg').style.display = 'block';
                    btn.innerHTML = 'Authenticating <i class="fa-solid fa-lock"></i>';
                }
            } catch(e) {
                document.getElementById('errorMsg').textContent = 'Network Error';
                document.getElementById('errorMsg').style.display = 'block';
                btn.innerHTML = 'Authenticating <i class="fa-solid fa-lock"></i>';
            }
        });
    </script>
</body>
</html>
