<?php
/**
 * Reset Admin Password
 */

require_once 'config/database.php';

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    
    $username = 'admin';
    $new_password = $_POST['new_password'];
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    
    // Check if admin exists
    $query = "SELECT user_id FROM users WHERE username = :username";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        // Update existing admin
        $query = "UPDATE users SET password = :password WHERE username = :username";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':username', $username);
        
        if ($stmt->execute()) {
            $message = "Admin password updated successfully!";
            $message_type = 'success';
        } else {
            $message = "Failed to update password.";
            $message_type = 'error';
        }
    } else {
        // Create new admin
        $role = 'Admin';
        $query = "INSERT INTO users (username, password, role) VALUES (:username, :password, :role)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':role', $role);
        
        if ($stmt->execute()) {
            $message = "Admin user created successfully!";
            $message_type = 'success';
        } else {
            $message = "Failed to create admin user.";
            $message_type = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Reset Admin Password - BPLO Ilagan</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            padding: 20px; 
            background: linear-gradient(135deg, #1e88e5 0%, #0d47a1 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container { 
            max-width: 500px; 
            background: white; 
            padding: 40px; 
            border-radius: 10px; 
            box-shadow: 0 10px 40px rgba(0,0,0,0.3); 
        }
        h1 { color: #1e88e5; margin-bottom: 20px; }
        .success { 
            color: green; 
            padding: 15px; 
            background: #e6ffe6; 
            border-radius: 5px; 
            margin: 15px 0;
            border-left: 4px solid green;
        }
        .error { 
            color: red; 
            padding: 15px; 
            background: #ffe6e6; 
            border-radius: 5px; 
            margin: 15px 0;
            border-left: 4px solid red;
        }
        label { 
            display: block; 
            margin: 15px 0 5px; 
            font-weight: bold;
            color: #333;
        }
        input { 
            width: 100%; 
            padding: 12px; 
            border: 2px solid #ddd; 
            border-radius: 5px; 
            font-size: 16px;
            box-sizing: border-box;
        }
        input:focus {
            border-color: #1e88e5;
            outline: none;
        }
        button { 
            background: linear-gradient(to right, #1e88e5, #1565c0);
            color: white; 
            padding: 12px 30px; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer; 
            font-size: 16px;
            width: 100%;
            margin-top: 20px;
            font-weight: bold;
        }
        button:hover { 
            background: linear-gradient(to right, #1565c0, #0d47a1);
        }
        .links {
            margin-top: 20px;
            text-align: center;
        }
        .links a { 
            color: #1e88e5; 
            text-decoration: none;
            margin: 0 10px;
        }
        .links a:hover {
            text-decoration: underline;
        }
        .info {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            border-left: 4px solid #1e88e5;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 Reset Admin Password</h1>
        
        <?php if ($message): ?>
            <div class="<?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <div class="info">
            <strong>Note:</strong> This will reset the password for the 'admin' user. If the admin user doesn't exist, it will be created.
        </div>
        
        <form method="POST">
            <label>New Password:</label>
            <input type="text" name="new_password" value="admin123" required>
            <small style="color: #666;">Default: admin123</small>
            
            <button type="submit">Reset Password</button>
        </form>
        
        <div class="links">
            <a href="login.php">← Back to Login</a>
            <a href="login_debug.php">Debug Login</a>
        </div>
    </div>
</body>
</html>
