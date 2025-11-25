<?php
/**
 * Business Management Module
 * CRUD Operations for Businesses
 */

class Business {
    private $conn;
    private $table_name = "businesses";

    public $business_id;
    public $business_name;
    public $owner_name;
    public $business_address;
    public $contact_number;
    public $business_type;
    public $date_registered;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * CREATE - Add new business
     */
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (business_name, owner_name, business_address, contact_number, business_type, date_registered) 
                  VALUES (:business_name, :owner_name, :business_address, :contact_number, :business_type, :date_registered)";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize inputs
        $this->business_name = htmlspecialchars(strip_tags($this->business_name));
        $this->owner_name = htmlspecialchars(strip_tags($this->owner_name));
        $this->business_address = htmlspecialchars(strip_tags($this->business_address));
        $this->contact_number = htmlspecialchars(strip_tags($this->contact_number));
        $this->business_type = htmlspecialchars(strip_tags($this->business_type));
        
        // Bind parameters
        $stmt->bindParam(":business_name", $this->business_name);
        $stmt->bindParam(":owner_name", $this->owner_name);
        $stmt->bindParam(":business_address", $this->business_address);
        $stmt->bindParam(":contact_number", $this->contact_number);
        $stmt->bindParam(":business_type", $this->business_type);
        $stmt->bindParam(":date_registered", $this->date_registered);
        
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * READ - Get all businesses
     */
    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY date_registered DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    /**
     * READ - Get single business by ID
     */
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE business_id = :business_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":business_id", $this->business_id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            $this->business_name = $row['business_name'];
            $this->owner_name = $row['owner_name'];
            $this->business_address = $row['business_address'];
            $this->contact_number = $row['contact_number'];
            $this->business_type = $row['business_type'];
            $this->date_registered = $row['date_registered'];
            return true;
        }
        return false;
    }

    /**
     * UPDATE - Update business information
     */
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET business_name = :business_name,
                      owner_name = :owner_name,
                      business_address = :business_address,
                      contact_number = :contact_number,
                      business_type = :business_type,
                      date_registered = :date_registered
                  WHERE business_id = :business_id";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize inputs
        $this->business_name = htmlspecialchars(strip_tags($this->business_name));
        $this->owner_name = htmlspecialchars(strip_tags($this->owner_name));
        $this->business_address = htmlspecialchars(strip_tags($this->business_address));
        $this->contact_number = htmlspecialchars(strip_tags($this->contact_number));
        $this->business_type = htmlspecialchars(strip_tags($this->business_type));
        $this->business_id = htmlspecialchars(strip_tags($this->business_id));
        
        // Bind parameters
        $stmt->bindParam(":business_name", $this->business_name);
        $stmt->bindParam(":owner_name", $this->owner_name);
        $stmt->bindParam(":business_address", $this->business_address);
        $stmt->bindParam(":contact_number", $this->contact_number);
        $stmt->bindParam(":business_type", $this->business_type);
        $stmt->bindParam(":date_registered", $this->date_registered);
        $stmt->bindParam(":business_id", $this->business_id);
        
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * DELETE - Delete business
     */
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE business_id = :business_id";
        $stmt = $this->conn->prepare($query);
        
        $this->business_id = htmlspecialchars(strip_tags($this->business_id));
        $stmt->bindParam(":business_id", $this->business_id);
        
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * SEARCH - Search businesses
     */
    public function search($keyword) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE business_name LIKE :keyword 
                  OR owner_name LIKE :keyword 
                  OR business_type LIKE :keyword 
                  ORDER BY date_registered DESC";
        
        $stmt = $this->conn->prepare($query);
        $keyword = "%{$keyword}%";
        $stmt->bindParam(":keyword", $keyword);
        $stmt->execute();
        return $stmt;
    }

    /**
     * COUNT - Get total businesses
     */
    public function count() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }
}
?>
