<?php
/**
 * Renewal Management Module
 * CRUD Operations for Renewals
 */

class Renewal {
    private $conn;
    private $table_name = "renewals";

    public $renewal_id;
    public $business_id;
    public $renewal_date;
    public $new_expiry_date;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * CREATE - Add new renewal
     */
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (business_id, renewal_date, new_expiry_date) 
                  VALUES (:business_id, :renewal_date, :new_expiry_date)";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize inputs
        $this->business_id = htmlspecialchars(strip_tags($this->business_id));
        
        // Bind parameters
        $stmt->bindParam(":business_id", $this->business_id);
        $stmt->bindParam(":renewal_date", $this->renewal_date);
        $stmt->bindParam(":new_expiry_date", $this->new_expiry_date);
        
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * READ - Get all renewals with business info
     */
    public function readAll() {
        $query = "SELECT r.*, b.business_name, b.owner_name 
                  FROM " . $this->table_name . " r
                  LEFT JOIN businesses b ON r.business_id = b.business_id
                  ORDER BY r.renewal_date DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    /**
     * READ - Get single renewal by ID
     */
    public function readOne() {
        $query = "SELECT r.*, b.business_name, b.owner_name 
                  FROM " . $this->table_name . " r
                  LEFT JOIN businesses b ON r.business_id = b.business_id
                  WHERE r.renewal_id = :renewal_id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":renewal_id", $this->renewal_id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            $this->business_id = $row['business_id'];
            $this->renewal_date = $row['renewal_date'];
            $this->new_expiry_date = $row['new_expiry_date'];
            return true;
        }
        return false;
    }

    /**
     * READ - Get renewals by business ID
     */
    public function readByBusiness($business_id) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE business_id = :business_id 
                  ORDER BY renewal_date DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":business_id", $business_id);
        $stmt->execute();
        return $stmt;
    }

    /**
     * UPDATE - Update renewal information
     */
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET business_id = :business_id,
                      renewal_date = :renewal_date,
                      new_expiry_date = :new_expiry_date
                  WHERE renewal_id = :renewal_id";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize inputs
        $this->business_id = htmlspecialchars(strip_tags($this->business_id));
        $this->renewal_id = htmlspecialchars(strip_tags($this->renewal_id));
        
        // Bind parameters
        $stmt->bindParam(":business_id", $this->business_id);
        $stmt->bindParam(":renewal_date", $this->renewal_date);
        $stmt->bindParam(":new_expiry_date", $this->new_expiry_date);
        $stmt->bindParam(":renewal_id", $this->renewal_id);
        
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * DELETE - Delete renewal
     */
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE renewal_id = :renewal_id";
        $stmt = $this->conn->prepare($query);
        
        $this->renewal_id = htmlspecialchars(strip_tags($this->renewal_id));
        $stmt->bindParam(":renewal_id", $this->renewal_id);
        
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>
