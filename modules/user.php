<?php
/**
 * User Management Module
 * CRUD Operations for Users
 */

class User {
    private $conn;
    private $table_name = "users";

    public $user_id;
    public $username;
    public $password;
    public $role;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * CREATE - Add new user
     */
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (username, password, role) 
                  VALUES (:username, :password, :role)";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize inputs
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->role = htmlspecialchars(strip_tags($this->role));
        
        // Hash password
        $hashed_password = password_hash($this->password, PASSWORD_DEFAULT);
        
        // Bind parameters
        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":password", $hashed_password);
        $stmt->bindParam(":role", $this->role);
        
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * READ - Get all users
     */
    public function readAll() {
        $query = "SELECT user_id, username, role, created_at FROM " . $this->table_name . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    /**
     * READ - Get single user by ID
     */
    public function readOne() {
        $query = "SELECT user_id, username, role, created_at FROM " . $this->table_name . " WHERE user_id = :user_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            $this->username = $row['username'];
            $this->role = $row['role'];
            return true;
        }
        return false;
    }

    /**
     * UPDATE - Update user information
     */
    public function update() {
        // Check if password needs to be updated
        if (!empty($this->password)) {
            $query = "UPDATE " . $this->table_name . " 
                      SET username = :username,
                          password = :password,
                          role = :role
                      WHERE user_id = :user_id";
        } else {
            $query = "UPDATE " . $this->table_name . " 
                      SET username = :username,
                          role = :role
                      WHERE user_id = :user_id";
        }
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize inputs
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->role = htmlspecialchars(strip_tags($this->role));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        
        // Bind parameters
        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":role", $this->role);
        $stmt->bindParam(":user_id", $this->user_id);
        
        if (!empty($this->password)) {
            $hashed_password = password_hash($this->password, PASSWORD_DEFAULT);
            $stmt->bindParam(":password", $hashed_password);
        }
        
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * DELETE - Delete user
     */
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $stmt->bindParam(":user_id", $this->user_id);
        
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * LOGIN - Authenticate user
     */
    public function login() {
        $query = "SELECT user_id, username, password, role FROM " . $this->table_name . " WHERE username = :username LIMIT 1";
        $stmt = $this->conn->prepare($query);
        
        $this->username = htmlspecialchars(strip_tags($this->username));
        $stmt->bindParam(":username", $this->username);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row && password_verify($this->password, $row['password'])) {
            $this->user_id = $row['user_id'];
            $this->username = $row['username'];
            $this->role = $row['role'];
            return true;
        }
        return false;
    }

    /**
     * Check if username exists
     */
    public function usernameExists() {
        $query = "SELECT user_id FROM " . $this->table_name . " WHERE username = :username LIMIT 1";
        $stmt = $this->conn->prepare($query);
        
        $this->username = htmlspecialchars(strip_tags($this->username));
        $stmt->bindParam(":username", $this->username);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            return true;
        }
        return false;
    }
}
?>
