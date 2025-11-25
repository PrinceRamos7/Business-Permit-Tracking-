<?php
session_start();

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

require_once 'config/database.php';
require_once 'modules/user.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    $user = new User($db);
    
    $user->username = $_POST['username'];
    $user->password = $_POST['password'];
    
    if ($user->login()) {
        $_SESSION['user_id'] = $user->user_id;
        $_SESSION['username'] = $user->username;
        $_SESSION['role'] = $user->role;
        header("Location: dashboard.php");
        exit();
    } else {
        $error = 'Invalid username or password';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>BPLO Ilagan - Login</title>
    <link rel="stylesheet" href="src/assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="src/assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="src/assets/css/style.css">
    <link rel="stylesheet" href="src/assets/css/custom-blue.css">
    <link rel="shortcut icon" href="src/assets/images/favicon.png" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 50%, #1e88e5 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            margin: 0;
            padding: 0;
        }
        
        /* Animated Background */
        body::before {
            content: '';
            position: absolute;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(33, 150, 243, 0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }
        
        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        .container-scroller {
            background: transparent;
            position: relative;
            z-index: 1;
            width: 100%;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .page-body-wrapper {
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        
        .content-wrapper {
            background: transparent !important;
            padding: 2rem !important;
        }
        
        .auth .auth-form-light {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 3rem 2.5rem !important;
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
        }
        
        .brand-logo {
            margin-bottom: 2rem;
        }
        
        .brand-logo img {
            width: 100px;
            height: 100px;
            animation: pulse 2s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        .brand-logo h2 {
            font-weight: 700;
            margin-top: 1rem;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, #1e88e5, #1565c0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .brand-logo p {
            color: #666;
            font-size: 0.9rem;
        }
        
        h4 {
            color: #333;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        h6 {
            color: #666;
            margin-bottom: 2rem;
        }
        
        .form-control {
            background: #f8f9fa;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 0.875rem 1rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            background: white;
            border-color: #2196f3;
            box-shadow: 0 0 0 0.2rem rgba(33, 150, 243, 0.15);
        }
        
        .form-control::placeholder {
            color: #999;
        }
        
        .btn-gradient-primary {
            background: linear-gradient(135deg, #2196f3 0%, #1976d2 100%);
            border: none;
            border-radius: 10px;
            padding: 0.875rem;
            font-weight: 600;
            font-size: 1rem;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(33, 150, 243, 0.3);
        }
        
        .btn-gradient-primary:hover {
            background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(33, 150, 243, 0.4);
        }
        
        .btn-gradient-primary:active {
            transform: translateY(0);
        }
        
        .form-check-label {
            color: #666;
            font-size: 0.9rem;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%);
            color: #c62828;
        }
        
        .text-center.mt-4 {
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 10px;
            margin-top: 1.5rem !important;
        }
        
        .text-center.mt-4 strong {
            color: #2196f3;
        }
        
        /* Input Icons */
        .form-group {
            position: relative;
        }
        
        .form-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
        }
        
        .form-group input {
            padding-left: 2.5rem;
        }
        
        /* Loading Animation */
        .btn-gradient-primary.loading {
            pointer-events: none;
            opacity: 0.7;
        }
        
        .btn-gradient-primary.loading::after {
            content: '';
            position: absolute;
            width: 16px;
            height: 16px;
            top: 50%;
            left: 50%;
            margin-left: -8px;
            margin-top: -8px;
            border: 2px solid #ffffff;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 0.6s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Fix Row and Column */
        .row {
            margin: 0 !important;
        }
        
        .col-lg-5,
        .col-md-7,
        .col-sm-9 {
            padding: 0 15px;
        }
        
        /* Ensure Full Width Container */
        .full-page-wrapper {
            width: 100%;
            min-height: 100vh;
        }
        
        .auth {
            width: 100%;
        }
        
        /* Responsive */
        @media (max-width: 576px) {
            .auth .auth-form-light {
                padding: 2rem 1.5rem !important;
            }
            
            .brand-logo img {
                width: 80px;
                height: 80px;
            }
            
            .content-wrapper {
                padding: 1rem !important;
            }
        }
    </style>
</head>
<body>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center justify-content-center auth" style="min-height: 100vh; padding: 2rem;">
                <div class="row w-100 justify-content-center">
                    <div class="col-lg-5 col-md-7 col-sm-9">
                        <div class="auth-form-light text-left p-5">
                            <div class="brand-logo text-center">
                                <img src="src/assets/images/logos/bplo.jpg" alt="BPLO Logo" class="logo-img" style="width: 100px; height: 100px; margin-bottom: 15px;">
                                <h2 class="text-primary">BPLO ILAGAN</h2>
                                <p class="text-muted">Business Permit & Licensing Office</p>
                            </div>
                            <h4>Hello! let's get started</h4>
                            <h6 class="font-weight-light">Sign in to continue.</h6>
                            
                            <?php if ($error): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo $error; ?>
                            </div>
                            <?php endif; ?>
                            
                            <form class="pt-3" method="POST" id="loginForm">
                                <div class="form-group mb-3">
                                    <input type="text" class="form-control form-control-lg" name="username" id="username" placeholder="Enter your username" required autocomplete="username">
                                </div>
                                <div class="form-group mb-4">
                                    <input type="password" class="form-control form-control-lg" name="password" id="password" placeholder="Enter your password" required autocomplete="current-password">
                                </div>
                                <div class="mb-3 d-flex justify-content-between align-items-center">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="rememberMe">
                                        <label class="form-check-label" for="rememberMe">
                                            Keep me signed in
                                        </label>
                                    </div>
                                </div>
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-gradient-primary btn-lg" id="loginBtn">
                                        SIGN IN
                                    </button>
                                </div>
                            </form>
                            <div class="text-center mt-4 font-weight-light">
                                Default Login: <strong>admin</strong> / <strong>admin123</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="src/assets/vendors/js/vendor.bundle.base.js"></script>
    <script src="src/assets/js/off-canvas.js"></script>
    <script src="src/assets/js/misc.js"></script>
    <script>
        // Enhanced Login Form
        document.addEventListener('DOMContentLoaded', function() {
            const loginForm = document.getElementById('loginForm');
            const loginBtn = document.getElementById('loginBtn');
            const usernameInput = document.getElementById('username');
            const passwordInput = document.getElementById('password');
            
            // Focus on username field
            usernameInput.focus();
            
            // Form submission with loading state
            loginForm.addEventListener('submit', function(e) {
                // Add loading state
                loginBtn.classList.add('loading');
                loginBtn.disabled = true;
                loginBtn.innerHTML = '<span style="opacity: 0;">SIGNING IN...</span>';
            });
            
            // Input animations
            const inputs = document.querySelectorAll('.form-control');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'translateY(-2px)';
                    this.parentElement.style.transition = 'transform 0.3s ease';
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'translateY(0)';
                });
            });
            
            // Enter key navigation
            usernameInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    passwordInput.focus();
                }
            });
        });
    </script>
</body>
</html>
