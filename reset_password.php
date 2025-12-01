<?php
/**
 * Reset Admin Password
 * This will reset the admin password to 'admin123'
 */

require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    die("❌ Database connection failed!");
}

try {
    // Reset admin password to 'admin123'
    $username = 'admin';
    $new_password = 'admin123';
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    
    $stmt = $db->prepare("UPDATE users SET password = :password WHERE username = :username");
    $stmt->bindParam(':password', $hashed_password);
    $stmt->bindParam(':username', $username);
    
    if ($stmt->execute()) {
        echo "✅ <strong>Password reset successful!</strong><br><br>";
        echo "Username: <strong>admin</strong><br>";
        echo "New Password: <strong>admin123</strong><br><br>";
        
        // Verify the new password works
        $stmt = $db->prepare("SELECT password FROM users WHERE username = 'admin'");
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (password_verify($new_password, $row['password'])) {
            echo "✅ Password verification successful!<br><br>";
            echo "<a href='login.php' style='display: inline-block; padding: 10px 20px; background: #2196f3; color: white; text-decoration: none; border-radius: 5px;'>Go to Login</a>";
        } else {
            echo "❌ Password verification failed!<br>";
        }
    } else {
        echo "❌ Failed to reset password!";
    }
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Admin Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background: #f5f5f5;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h2 {
            color: #333;
            border-bottom: 2px solid #2196f3;
            padding-bottom: 10px;
        }
        a:hover {
            background: #1976d2 !important;
        }
    </style>
</head>
<body>
    <h2>Reset Admin Password</h2>
</body>
</html>
