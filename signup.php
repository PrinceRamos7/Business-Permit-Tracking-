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
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['password'] !== $_POST['confirm_password']) {
        $error = 'Passwords do not match!';
    } else {
        $database = new Database();
        $db = $database->getConnection();
        $user = new User($db);
        
        $user->username = $_POST['username'];
        $user->password = $_POST['password'];
        $user->role = 'Staff';
        
        if ($user->usernameExists()) {
            $error = 'Username already exists. Please choose another.';
        } else {
            if ($user->create()) {
                $success = 'Account created successfully! You can now sign in.';
            } else {
                $error = 'Failed to create account. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>BPLO Ilagan - Sign Up</title>
    <link rel="stylesheet" href="src/assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="shortcut icon" href="src/assets/images/favicon.png" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #7eb5a6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .container {
            width: 100%;
            max-width: 1100px;
            min-height: 650px;
            background: white;
            border-radius: 40px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            display: grid;
            grid-template-columns: 1fr 1fr;
            position: relative;
        }

        /* Left Side - Welcome */
        .welcome-side {
            background: #7eb5a6;
            padding: 4rem 3rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .welcome-side::before {
            content: '';
            position: absolute;
            right: -150px;
            top: -100px;
            width: 400px;
            height: 800px;
            background: white;
            border-radius: 50%;
            transform: rotate(15deg);
        }

        .welcome-content {
            position: relative;
            z-index: 1;
        }

        .logo-circle {
            width: 120px;
            height: 120px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .logo-circle img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
        }

        .welcome-side h2 {
            font-size: 4rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-transform: uppercase;
            letter-spacing: 3px;
        }

        .welcome-side p {
            font-size: 1.125rem;
            opacity: 0.95;
            margin-bottom: 3rem;
            line-height: 1.6;
            max-width: 350px;
            margin-left: auto;
            margin-right: auto;
        }

        .btn-signin {
            padding: 1rem 3rem;
            background: transparent;
            color: white;
            border: 2px solid white;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-signin:hover {
            background: white;
            color: #7eb5a6;
        }

        /* Right Side - Form */
        .form-side {
            padding: 4rem 3.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            z-index: 2;
        }

        .form-content {
            max-width: 400px;
        }

        h1 {
            font-size: 3.5rem;
            font-weight: 700;
            color: #000;
            margin-bottom: 2.5rem;
        }

        .social-login {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .social-btn {
            width: 50px;
            height: 50px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .social-btn:hover {
            border-color: #7eb5a6;
            transform: translateY(-2px);
        }

        .social-btn i {
            font-size: 1.5rem;
        }

        .social-btn.google i { color: #DB4437; }
        .social-btn.linkedin i { color: #0077B5; }
        .social-btn.github i { color: #333; }
        .social-btn.facebook i { color: #1877F2; }

        .divider {
            color: #666;
            margin-bottom: 2rem;
            font-size: 0.9375rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-control {
            width: 100%;
            padding: 1rem 1.25rem;
            border: none;
            background: #f0f0f0;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            background: #e8e8e8;
        }

        .form-control::placeholder {
            color: #999;
        }

        .btn-signup {
            padding: 1rem 3rem;
            background: #000;
            color: white;
            border: none;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 1rem;
        }

        .btn-signup:hover {
            background: #333;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .alert {
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-size: 0.9375rem;
        }

        .alert-danger {
            background: #fee;
            color: #c33;
        }

        .alert-success {
            background: #efe;
            color: #3c3;
        }

        .back-link {
            position: absolute;
            top: 2rem;
            right: 2rem;
            color: #666;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            transition: color 0.3s ease;
            z-index: 10;
        }

        .back-link:hover {
            color: #7eb5a6;
        }

        /* Responsive */
        @media (max-width: 968px) {
            .container {
                grid-template-columns: 1fr;
                border-radius: 30px;
            }

            .welcome-side {
                padding: 3rem 2rem;
            }

            .welcome-side::before {
                display: none;
            }

            .welcome-side h2 {
                font-size: 3rem;
            }

            .form-side {
                padding: 3rem 2rem;
            }

            h1 {
                font-size: 2.5rem;
            }
        }

        @media (max-width: 576px) {
            body {
                padding: 1rem;
            }

            .form-side {
                padding: 2rem 1.5rem;
            }

            h1 {
                font-size: 2rem;
            }

            .welcome-side h2 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Left Side - Welcome -->
        <div class="welcome-side">
            <div class="welcome-content">
                <div class="logo-circle">
                    <img src="src/assets/images/logos/bplo.jpg" alt="BPLO Logo">
                </div>
                <h2>HELLO!</h2>
                <p>Already have an account? Sign in to access the admin dashboard.</p>
                <a href="login.php" class="btn-signin">Sign In</a>
            </div>
        </div>

        <!-- Right Side - Form -->
        <div class="form-side">
            <a href="index.php" class="back-link">
                <span>Back to Home</span>
                <i class="mdi mdi-arrow-right"></i>
            </a>

            <div class="form-content">
                <h1>Sign Up</h1>

                <div class="social-login">
                    <a href="#" class="social-btn google" title="Sign up with Google">
                        <i class="mdi mdi-google"></i>
                    </a>
                    <a href="#" class="social-btn linkedin" title="Sign up with LinkedIn">
                        <i class="mdi mdi-linkedin"></i>
                    </a>
                    <a href="#" class="social-btn github" title="Sign up with GitHub">
                        <i class="mdi mdi-github"></i>
                    </a>
                    <a href="#" class="social-btn facebook" title="Sign up with Facebook">
                        <i class="mdi mdi-facebook"></i>
                    </a>
                </div>

                <div class="divider">Or use your email for registration</div>

                <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="mdi mdi-alert-circle"></i>
                    <?php echo $error; ?>
                </div>
                <?php endif; ?>

                <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="mdi mdi-check-circle"></i>
                    <?php echo $success; ?>
                </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <input 
                            type="text" 
                            class="form-control" 
                            name="username" 
                            placeholder="Username" 
                            required
                            autofocus
                        >
                    </div>

                    <div class="form-group">
                        <input 
                            type="password" 
                            class="form-control" 
                            name="password" 
                            placeholder="Password" 
                            required
                            minlength="6"
                        >
                    </div>

                    <div class="form-group">
                        <input 
                            type="password" 
                            class="form-control" 
                            name="confirm_password" 
                            placeholder="Confirm Password" 
                            required
                            minlength="6"
                        >
                    </div>

                    <button type="submit" class="btn-signup">Sign Up</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
