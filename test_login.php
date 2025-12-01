<?php
/**
 * Login Debug Script
 * This will help us see what's happening with the login
 */

require_once 'config/database.php';
require_once 'modules/user.php';

echo "<h2>Login Debug Test</h2>";
echo "<hr>";

// Test 1: Database Connection
echo "<h3>1. Testing Database Connection...</h3>";
$database = new Database();
$db = $database->getConnection();

if ($db) {
    echo "✅ Database connected successfully!<br><br>";
} else {
    echo "❌ Database connection failed!<br><br>";
    die();
}

// Test 2: Check if users table exists
echo "<h3>2. Checking if 'users' table exists...</h3>";
try {
    $stmt = $db->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() > 0) {
        echo "✅ 'users' table exists!<br><br>";
    } else {
        echo "❌ 'users' table does NOT exist!<br>";
        echo "Please run database/simple_schema.sql first!<br><br>";
        die();
    }
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "<br><br>";
    die();
}

// Test 3: Check if admin user exists
echo "<h3>3. Checking for admin user...</h3>";
try {
    $stmt = $db->prepare("SELECT user_id, username, role FROM users WHERE username = 'admin'");
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "✅ Admin user found!<br>";
        echo "User ID: " . $admin['user_id'] . "<br>";
        echo "Username: " . $admin['username'] . "<br>";
        echo "Role: " . $admin['role'] . "<br><br>";
    } else {
        echo "❌ Admin user NOT found!<br>";
        echo "Please run <a href='create_admin.php'>create_admin.php</a> first!<br><br>";
        die();
    }
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "<br><br>";
    die();
}

// Test 4: Test password verification
echo "<h3>4. Testing password verification...</h3>";
try {
    $stmt = $db->prepare("SELECT password FROM users WHERE username = 'admin'");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $test_password = 'admin123';
    $stored_hash = $row['password'];
    
    echo "Testing password: <strong>admin123</strong><br>";
    echo "Stored hash: " . substr($stored_hash, 0, 30) . "...<br>";
    
    if (password_verify($test_password, $stored_hash)) {
        echo "✅ Password verification SUCCESSFUL!<br><br>";
    } else {
        echo "❌ Password verification FAILED!<br>";
        echo "The stored password hash doesn't match 'admin123'<br><br>";
    }
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "<br><br>";
}

// Test 5: Test User class login method
echo "<h3>5. Testing User class login() method...</h3>";
$user = new User($db);
$user->username = 'admin';
$user->password = 'admin123';

if ($user->login()) {
    echo "✅ User->login() method SUCCESSFUL!<br>";
    echo "User ID: " . $user->user_id . "<br>";
    echo "Username: " . $user->username . "<br>";
    echo "Role: " . $user->role . "<br><br>";
} else {
    echo "❌ User->login() method FAILED!<br><br>";
}

// Test 6: List all users
echo "<h3>6. All users in database:</h3>";
try {
    $stmt = $db->query("SELECT user_id, username, role, created_at FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($users) > 0) {
        echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Username</th><th>Role</th><th>Created At</th></tr>";
        foreach ($users as $u) {
            echo "<tr>";
            echo "<td>" . $u['user_id'] . "</td>";
            echo "<td>" . $u['username'] . "</td>";
            echo "<td>" . $u['role'] . "</td>";
            echo "<td>" . $u['created_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table><br>";
    } else {
        echo "No users found in database!<br><br>";
    }
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "<br><br>";
}

echo "<hr>";
echo "<h3>Summary:</h3>";
echo "If all tests passed, the login should work.<br>";
echo "Try logging in at <a href='login.php'>login.php</a><br>";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login Debug Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 30px auto;
            padding: 30px;
            background: #f5f5f5;
        }
        h2 {
            color: #333;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }
        h3 {
            color: #555;
            margin-top: 20px;
        }
        table {
            background: white;
            width: 100%;
        }
        th {
            background: #667eea;
            color: white;
        }
        a {
            color: #667eea;
            text-decoration: none;
            font-weight: bold;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
</body>
</html>
