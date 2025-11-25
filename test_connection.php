<?php
/**
 * Test Database Connection and User
 */

require_once 'config/database.php';

echo "<h2>BPLO Database Connection Test</h2>";

$database = new Database();
$db = $database->getConnection();

if ($db) {
    echo "<p style='color: green;'>✓ Database connection successful!</p>";
    
    // Check if users table exists
    try {
        $query = "SELECT COUNT(*) as count FROM users";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "<p style='color: green;'>✓ Users table exists with " . $result['count'] . " user(s)</p>";
        
        // Check if admin user exists
        $query = "SELECT user_id, username, role FROM users WHERE username = 'admin'";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($admin) {
            echo "<p style='color: green;'>✓ Admin user found!</p>";
            echo "<pre>";
            print_r($admin);
            echo "</pre>";
        } else {
            echo "<p style='color: red;'>✗ Admin user NOT found. Creating admin user...</p>";
            
            // Create admin user
            $username = 'admin';
            $password = password_hash('admin123', PASSWORD_DEFAULT);
            $role = 'Admin';
            
            $query = "INSERT INTO users (username, password, role) VALUES (:username, :password, :role)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':role', $role);
            
            if ($stmt->execute()) {
                echo "<p style='color: green;'>✓ Admin user created successfully!</p>";
                echo "<p><strong>Username:</strong> admin</p>";
                echo "<p><strong>Password:</strong> admin123</p>";
            } else {
                echo "<p style='color: red;'>✗ Failed to create admin user</p>";
            }
        }
        
        // List all users
        echo "<h3>All Users in Database:</h3>";
        $query = "SELECT user_id, username, role, created_at FROM users";
        $stmt = $db->prepare($query);
        $stmt->execute();
        
        echo "<table border='1' cellpadding='10'>";
        echo "<tr><th>ID</th><th>Username</th><th>Role</th><th>Created</th></tr>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . $row['user_id'] . "</td>";
            echo "<td>" . $row['username'] . "</td>";
            echo "<td>" . $row['role'] . "</td>";
            echo "<td>" . $row['created_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
    } catch (PDOException $e) {
        echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
        echo "<p>The database might not be imported yet. Please import database/bplo_schema.sql</p>";
    }
    
} else {
    echo "<p style='color: red;'>✗ Database connection failed!</p>";
    echo "<p>Please check your database credentials in config/database.php</p>";
}

echo "<hr>";
echo "<h3>Database Configuration:</h3>";
echo "<p><strong>Host:</strong> localhost</p>";
echo "<p><strong>Database:</strong> bplo_ilagan</p>";
echo "<p><strong>Username:</strong> root</p>";
echo "<p><strong>Password:</strong> (empty)</p>";

echo "<hr>";
echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li>Make sure MySQL/MariaDB is running in XAMPP</li>";
echo "<li>Open phpMyAdmin (http://localhost/phpmyadmin)</li>";
echo "<li>Import the file: database/bplo_schema.sql</li>";
echo "<li>Try logging in again with username: <strong>admin</strong> and password: <strong>admin123</strong></li>";
echo "</ol>";

echo "<p><a href='login.php' style='padding: 10px 20px; background: #1e88e5; color: white; text-decoration: none; border-radius: 5px;'>Go to Login Page</a></p>";
?>
