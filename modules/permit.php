<?php
/**
 * Permit Management Module
 * CRUD Operations for Permits
 */

class Permit {
    private $conn;
    private $table_name = "permits";

    public $permit_id;
    public $business_id;
    public $permit_number;
    public $date_issued;
    public $expiry_date;
    public $status;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * CREATE - Add new permit
     */
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (business_id, permit_number, date_issued, expiry_date, status) 
                  VALUES (:business_id, :permit_number, :date_issued, :expiry_date, :status)";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize inputs
        $this->business_id = htmlspecialchars(strip_tags($this->business_id));
        $this->permit_number = htmlspecialchars(strip_tags($this->permit_number));
        $this->status = htmlspecialchars(strip_tags($this->status));
        
        // Bind parameters
        $stmt->bindParam(":business_id", $this->business_id);
        $stmt->bindParam(":permit_number", $this->permit_number);
        $stmt->bindParam(":date_issued", $this->date_issued);
        $stmt->bindParam(":expiry_date", $this->expiry_date);
        $stmt->bindParam(":status", $this->status);
        
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * READ - Get all permits with business info
     */
    public function readAll() {
        $query = "SELECT p.*, b.business_name, b.owner_name 
                  FROM " . $this->table_name . " p
                  LEFT JOIN businesses b ON p.business_id = b.business_id
                  ORDER BY p.date_issued DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    /**
     * READ - Get single permit by ID
     */
    public function readOne() {
        $query = "SELECT p.*, b.business_name, b.owner_name 
                  FROM " . $this->table_name . " p
                  LEFT JOIN businesses b ON p.business_id = b.business_id
                  WHERE p.permit_id = :permit_id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":permit_id", $this->permit_id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            $this->business_id = $row['business_id'];
            $this->permit_number = $row['permit_number'];
            $this->date_issued = $row['date_issued'];
            $this->expiry_date = $row['expiry_date'];
            $this->status = $row['status'];
            return true;
        }
        return false;
    }

    /**
     * READ - Get permits by business ID
     */
    public function readByBusiness($business_id) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE business_id = :business_id 
                  ORDER BY date_issued DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":business_id", $business_id);
        $stmt->execute();
        return $stmt;
    }

    /**
     * UPDATE - Update permit information
     */
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET business_id = :business_id,
                      permit_number = :permit_number,
                      date_issued = :date_issued,
                      expiry_date = :expiry_date,
                      status = :status
                  WHERE permit_id = :permit_id";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize inputs
        $this->business_id = htmlspecialchars(strip_tags($this->business_id));
        $this->permit_number = htmlspecialchars(strip_tags($this->permit_number));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->permit_id = htmlspecialchars(strip_tags($this->permit_id));
        
        // Bind parameters
        $stmt->bindParam(":business_id", $this->business_id);
        $stmt->bindParam(":permit_number", $this->permit_number);
        $stmt->bindParam(":date_issued", $this->date_issued);
        $stmt->bindParam(":expiry_date", $this->expiry_date);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":permit_id", $this->permit_id);
        
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * DELETE - Delete permit
     */
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE permit_id = :permit_id";
        $stmt = $this->conn->prepare($query);
        
        $this->permit_id = htmlspecialchars(strip_tags($this->permit_id));
        $stmt->bindParam(":permit_id", $this->permit_id);
        
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * COUNT - Get total active permits
     */
    public function countActive() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE status = 'Active'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    /**
     * COUNT - Get total expired permits
     */
    public function countExpired() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE status = 'Expired'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    /**
     * Get permits expiring soon (within 30 days)
     */
    public function getExpiringSoon() {
        $query = "SELECT p.*, b.business_name, b.owner_name 
                  FROM " . $this->table_name . " p
                  LEFT JOIN businesses b ON p.business_id = b.business_id
                  WHERE p.status = 'Active' 
                  AND p.expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
                  ORDER BY p.expiry_date ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Generate permit number
     */
    public function generatePermitNumber() {
        $year = date('Y');
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE YEAR(date_issued) = :year";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":year", $year);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $count = $row['total'] + 1;
        return "ILGN-" . $year . "-" . str_pad($count, 5, "0", STR_PAD_LEFT);
    }
}
?>
