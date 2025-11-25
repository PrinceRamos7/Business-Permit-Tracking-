<?php
session_start();

require_once 'config/database.php';
require_once 'modules/user.php';

$error = '';
$debug_info = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    
    $debug_info[] = "Database connected: " . ($db ? "YES" : "NO");
    
    $user = new User($db);
    
    $input_username = $_POST['username'];
    $input_password = $_POST['password'];
    
    $debug_info[] = "Input Username: " . $input_username;
    $debug_info[] = "Input Password Length: " . strlen($input_password);
    
    // Check if user exists
    $query = "SELECT user_id, username, password, role FROM users WHERE username = :username LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":username", $input_username);
    $stmt->execute();
    
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($row) {
        $debug_info[] = "User found in database: YES";
        $debug_info[] = "User ID: " . $row['user_id'];
        $debug_info[] = "Username: " . $row['username'];
        $debug_info[] = "Role: " . $row['role'];
        $debug_info[] = "Stored Password Hash: " . substr($row['password'], 0, 20) . "...";
        
        // Test password verification
        $verify_result = password_verify($input_password, $row['password']);
        $debug_info[] = "Password Verify Result: " . ($verify_result ? "TRUE (Match!)" : "FALSE (No Match)");
        
        if ($verify_result) {
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];
            $debug_info[] = "Session created successfully!";
            $debug_info[] = "Redirecting to dashboard...";
            
            echo "<h2>Login Successful!</h2>";
            echo "<pre>" . print_r($debug_info, true) . "</pre>";
            echo "<p><a href='dashboard.php'>Go to Dashboard</a></p>";
            exit();
        } else {
            $error = 'Password does not match';
            
            // Try creating a new hash for testing
            $new_hash = password_hash($input_password, PASSWORD_DEFAULT);
            $debug_info[] = "New hash for input password: " . substr($new_hash, 0, 20) . "...";
            
            // Test if the new hash works
            $test_verify = password_verify($input_password, $new_hash);
            $debug_info[] = "Test verify with new hash: " . ($test_verify ? "TRUE" : "FALSE");
        }
    } else {
        $debug_info[] = "User found in database: NO";
        $error = 'Username not found';
        
        // List all users
        $query = "SELECT username FROM users";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $all_users = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $debug_info[] = "Available usernames: " . implode(", ", $all_users);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Login Debug - BPLO Ilagan</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .debug-info { background: #f0f0f0; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .debug-info pre { margin: 0; white-space: pre-wrap; }
        .error { color: red; padding: 10px; background: #ffe6e6; border-radius: 5px; margin: 10px 0; }
        .success { color: green; padding: 10px; background: #e6ffe6; border-radius: 5px; margin: 10px 0; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; }
        button { background: #1e88e5; color: white; padding: 12px 30px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        button:hover { background: #1565c0; }
        a { color: #1e88e5; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <h1>BPLO Login Debug Tool</h1>
        
        <?php if ($error): ?>
        <div class="error">
            <strong>Error:</strong> <?php echo $error; ?>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($debug_info)): ?>
        <div class="debug-info">
            <h3>Debug Information:</h3>
            <pre><?php echo implode("\n", $debug_info); ?></pre>
        </div>
        <?php endif; ?>
        
        <form method="POST">
            <h3>Test Login</h3>
            <label>Username:</label>
            <input type="text" name="username" value="admin" required>
            
            <label>Password:</label>
            <input type="password" name="password" value="admin123" required>
            
            <button type="submit">Test Login</button>
        </form>
        
        <hr>
        
        <h3>Quick Actions:</h3>
        <p><a href="test_connection.php">Test Database Connection</a></p>
        <p><a href="login.php">Go to Normal Login Page</a></p>
        <p><a href="reset_admin.php">Reset Admin Password</a></p>
    </div>
</body>
</html>
