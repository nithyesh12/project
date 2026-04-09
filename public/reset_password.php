<?php
require_once '../src_php/Config/database.php';

// Initialize variables
$message = '';
$status = ''; // 'success' or 'error'
$token_valid = false;
$email_for_reset = '';

// Check if token is present in GET for initial load, or POST during submission
$token = $_GET['token'] ?? $_POST['token'] ?? '';

if (empty($token)) {
    // For demonstration purposes, if no token is provided, we simulate one being valid
    // to allow the user to see the design of the page. In production, this would strictly fail.
    // We will show a warning instead.
    $status = 'error';
    $message = 'Invalid or missing reset token. Flow usually begins from the "Forgot Password" email link. (For preview purposes, add ?token=demo to the URL)';
    
    if (isset($_GET['token']) && $_GET['token'] === 'demo') {
        $token_valid = true;
        $status = '';
        $message = '';
    }
} else {
    try {
        $database = new \Config\Database();
        $db = $database->getConnection();

        // Validate Token and Check Expiry
        $query = "SELECT id, email, reset_token_expiry FROM users WHERE reset_token = :token LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
            $expiry = strtotime($user_data['reset_token_expiry']);
            
            if (time() > $expiry) {
                $status = 'error';
                $message = 'Reset token has expired. Please request a new one.';
            } else {
                $token_valid = true;
                $email_for_reset = $user_data['email'];
            }
        } elseif ($token === 'demo') {
            // Demo fallback for UI preview
            $token_valid = true;
        } else {
            $status = 'error';
            $message = 'Invalid reset token. Please request a new one.';
        }

        // 2. Handle POST Request (Form Submission)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $token_valid) {
            $new_password = $_POST['new_password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            if (empty($new_password) || empty($confirm_password)) {
                $status = 'error';
                $message = 'Please fill in both password fields.';
            } elseif ($new_password !== $confirm_password) {
                $status = 'error';
                $message = 'Passwords do not match.';
            } elseif (strlen($new_password) < 6) {
                $status = 'error';
                $message = 'Password must be at least 6 characters long.';
            } else {
                // If it's a demo token, just show success
                if ($token === 'demo') {
                    $status = 'success';
                    $message = 'Your password has been reset successfully. You can now login.';
                    $token_valid = false;
                } else {
                    // Update user password and clear token
                    $password_hash = password_hash($new_password, PASSWORD_BCRYPT);
                    $update_query = "UPDATE users SET password_hash = :hash, reset_token = NULL, reset_token_expiry = NULL WHERE reset_token = :token";
                    $update_stmt = $db->prepare($update_query);
                    $update_stmt->bindParam(':hash', $password_hash);
                    $update_stmt->bindParam(':token', $token);

                    if ($update_stmt->execute()) {
                        $status = 'success';
                        $message = 'Your password has been reset successfully. You can now login.';
                        $token_valid = false; // Prevent showing the form again on this load
                    } else {
                        $status = 'error';
                        $message = 'An error occurred while updating the password. Please try again.';
                    }
                }
            }
        }
    } catch (PDOException $e) {
        $status = 'error';
        $message = 'Database error: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Grow Your Crops India</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-green: #2E7D32;
            --primary-dark: #1B5E20;
            --primary-light: #4CAF50;
            --bg-color: #F1F8E9;
            --text-dark: #333333;
            --text-light: #666666;
            --border-color: #E0E0E0;
            --error-color: #D32F2F;
            --success-color: #388E3C;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: var(--bg-color);
            background-image: linear-gradient(135deg, rgba(241,248,233,1) 0%, rgba(200,230,201,0.5) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .reset-container {
            background: #ffffff;
            width: 100%;
            max-width: 440px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            padding: 40px;
            text-align: center;
        }

        .logo {
            font-size: 28px;
            color: var(--primary-green);
            font-weight: 700;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .logo i {
            color: var(--primary-light);
        }

        h2 {
            color: var(--text-dark);
            font-size: 24px;
            margin-bottom: 10px;
        }

        p.subtitle {
            color: var(--text-light);
            font-size: 14px;
            margin-bottom: 30px;
            line-height: 1.5;
        }

        .form-group {
            text-align: left;
            margin-bottom: 20px;
            position: relative;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: var(--text-dark);
            margin-bottom: 8px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i.icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 16px;
            transition: color 0.3s ease;
        }

        .input-wrapper i.toggle-password {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 16px;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .input-wrapper i.toggle-password:hover {
            color: var(--primary-green);
        }

        .form-control {
            width: 100%;
            padding: 12px 40px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 15px;
            color: var(--text-dark);
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-green);
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.15);
        }

        .form-control:focus + i.icon {
            color: var(--primary-green);
        }

        .btn-reset {
            width: 100%;
            background-color: var(--primary-green);
            color: #fff;
            padding: 14px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 10px;
            transition: background-color 0.3s ease, transform 0.1s ease;
        }

        .btn-reset:hover {
            background-color: var(--primary-dark);
        }

        .btn-reset:active {
            transform: scale(0.98);
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-size: 14px;
            text-align: left;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-error {
            background-color: #FFEBEE;
            color: var(--error-color);
            border: 1px solid #FFCDD2;
        }

        .alert-success {
            background-color: #E8F5E9;
            color: var(--success-color);
            border: 1px solid #C8E6C9;
        }

        .back-link {
            display: inline-block;
            margin-top: 24px;
            color: var(--primary-green);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .back-link:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        .validation-message {
            font-size: 12px;
            color: var(--error-color);
            margin-top: 6px;
            display: none;
        }

        /* Responsive Design */
        @media (max-width: 480px) {
            .reset-container {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>

<div class="reset-container">
    <div class="logo">
        <i class="fas fa-leaf"></i>
        <span>GrowCrops</span>
    </div>

    <h2>Reset Password</h2>
    
    <?php if ($status === 'success'): ?>
        <p class="subtitle">Your password has been successfully updated.</p>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <span><?php echo htmlspecialchars($message); ?></span>
        </div>
        <a href="auth.html" class="back-link"><i class="fas fa-arrow-left"></i> Back to Login</a>
    <?php else: ?>
        <p class="subtitle">Create a new, secure password for your account.</p>

        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $status; ?>">
                <i class="fas <?php echo $status === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'; ?>"></i>
                <span><?php echo htmlspecialchars($message); ?></span>
            </div>
        <?php endif; ?>

        <?php if ($token_valid): ?>
            <form id="resetForm" method="POST" action="">
                <!-- Pass the token via hidden input to maintain state -->
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock icon"></i>
                        <input type="password" id="new_password" name="new_password" class="form-control" placeholder="Enter new password" required autocomplete="new-password">
                        <i class="fas fa-eye-slash toggle-password" onclick="togglePasswordVisibility('new_password')"></i>
                    </div>
                    <div class="validation-message" id="new_password_error">Must be at least 6 characters</div>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock icon"></i>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirm new password" required autocomplete="new-password">
                        <i class="fas fa-eye-slash toggle-password" onclick="togglePasswordVisibility('confirm_password')"></i>
                    </div>
                    <div class="validation-message" id="confirm_password_error">Passwords do not match</div>
                </div>

                <button type="submit" class="btn-reset">Reset Password</button>
            </form>
        <?php endif; ?>
        
        <a href="auth.html" class="back-link"><i class="fas fa-arrow-left"></i> Back to Login</a>
    <?php endif; ?>
</div>

<script>
    // Toggle Password Visibility
    function togglePasswordVisibility(inputId) {
        const input = document.getElementById(inputId);
        const icon = input.nextElementSibling;
        
        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        } else {
            input.type = "password";
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        }
    }

    // Client-side validation
    const form = document.getElementById('resetForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            let isValid = true;

            const errorNew = document.getElementById('new_password_error');
            const errorConfirm = document.getElementById('confirm_password_error');

            errorNew.style.display = 'none';
            errorConfirm.style.display = 'none';

            // Validate length
            if (newPassword.length < 6) {
                errorNew.style.display = 'block';
                isValid = false;
            }

            // Validate match
            if (newPassword !== confirmPassword) {
                errorConfirm.style.display = 'block';
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault(); // Prevent form submission
            }
        });

        // Real-time matching validation
        const passInput = document.getElementById('new_password');
        const confirmInput = document.getElementById('confirm_password');
        const errorConfirm = document.getElementById('confirm_password_error');

        confirmInput.addEventListener('input', function() {
            if (this.value !== passInput.value && this.value.length > 0) {
                errorConfirm.style.display = 'block';
            } else {
                errorConfirm.style.display = 'none';
            }
        });
    }
</script>

</body>
</html>
