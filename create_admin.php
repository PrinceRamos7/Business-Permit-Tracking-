<?php
/**
 * Quick Admin User Creator
 * Run this once to create the admin user
 */

require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    die("Database connection failed!");
}

try {
    // Check if users table exists
    $stmt = $db->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() == 0) {
        die("Error: 'users' table doesn't exist. Please run database/simple_schema.sql first!");
    }
    
    // Check if admin already exists
    $stmt = $db->prepare("SELECT * FROM users WHERE username = 'admin'");
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        echo "✅ Admin user already exists!<br>";
        echo "Username: <strong>admin</strong><br>";
        echo "Password: <strong>admin123</strong><br><br>";
        echo "<a href='login.php'>Go to Login</a>";
    } else {
        // Create admin user
        $username = 'admin';
        $password = password_hash('admin123', PASSWORD_DEFAULT);
        $role = 'Admin';
        
        $stmt = $db->prepare("INSERT INTO users (username, password, role) VALUES (:username, :password, :role)");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':role', $role);
        
        if ($stmt->execute()) {
            echo "✅ Admin user created successfully!<br><br>";
            echo "Username: <strong>admin</strong><br>";
            echo "Password: <strong>admin123</strong><br><br>";
            echo "<a href='login.php'>Go to Login</a>";
        } else {
            echo "❌ Failed to create admin user!";
        }
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Admin User</title>
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
        a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        a:hover {
            background: #5568d3;
        }
    </style>
</head>
<body>
</body>
</html>
