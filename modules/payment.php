<?php
/**
 * Payment Management Module
 * CRUD Operations for Payments
 */

class Payment {
    private $conn;
    private $table_name = "payments";

    public $payment_id;
    public $business_id;
    public $amount;
    public $OR_number;
    public $payment_date;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * CREATE - Add new payment
     */
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (business_id, amount, OR_number, payment_date) 
                  VALUES (:business_id, :amount, :OR_number, :payment_date)";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize inputs
        $this->business_id = htmlspecialchars(strip_tags($this->business_id));
        $this->amount = htmlspecialchars(strip_tags($this->amount));
        $this->OR_number = htmlspecialchars(strip_tags($this->OR_number));
        
        // Bind parameters
        $stmt->bindParam(":business_id", $this->business_id);
        $stmt->bindParam(":amount", $this->amount);
        $stmt->bindParam(":OR_number", $this->OR_number);
        $stmt->bindParam(":payment_date", $this->payment_date);
        
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * READ - Get all payments with business info
     */
    public function readAll() {
        $query = "SELECT p.*, b.business_name, b.owner_name 
                  FROM " . $this->table_name . " p
                  LEFT JOIN businesses b ON p.business_id = b.business_id
                  ORDER BY p.payment_date DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    /**
     * READ - Get single payment by ID
     */
    public function readOne() {
        $query = "SELECT p.*, b.business_name, b.owner_name 
                  FROM " . $this->table_name . " p
                  LEFT JOIN businesses b ON p.business_id = b.business_id
                  WHERE p.payment_id = :payment_id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":payment_id", $this->payment_id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            $this->business_id = $row['business_id'];
            $this->amount = $row['amount'];
            $this->OR_number = $row['OR_number'];
            $this->payment_date = $row['payment_date'];
            return true;
        }
        return false;
    }

    /**
     * READ - Get payments by business ID
     */
    public function readByBusiness($business_id) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE business_id = :business_id 
                  ORDER BY payment_date DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":business_id", $business_id);
        $stmt->execute();
        return $stmt;
    }

    /**
     * UPDATE - Update payment information
     */
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET business_id = :business_id,
                      amount = :amount,
                      OR_number = :OR_number,
                      payment_date = :payment_date
                  WHERE payment_id = :payment_id";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize inputs
        $this->business_id = htmlspecialchars(strip_tags($this->business_id));
        $this->amount = htmlspecialchars(strip_tags($this->amount));
        $this->OR_number = htmlspecialchars(strip_tags($this->OR_number));
        $this->payment_id = htmlspecialchars(strip_tags($this->payment_id));
        
        // Bind parameters
        $stmt->bindParam(":business_id", $this->business_id);
        $stmt->bindParam(":amount", $this->amount);
        $stmt->bindParam(":OR_number", $this->OR_number);
        $stmt->bindParam(":payment_date", $this->payment_date);
        $stmt->bindParam(":payment_id", $this->payment_id);
        
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * DELETE - Delete payment
     */
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE payment_id = :payment_id";
        $stmt = $this->conn->prepare($query);
        
        $this->payment_id = htmlspecialchars(strip_tags($this->payment_id));
        $stmt->bindParam(":payment_id", $this->payment_id);
        
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * Get total payments for current month
     */
    public function getTotalMonthly() {
        $query = "SELECT SUM(amount) as total FROM " . $this->table_name . " 
                  WHERE MONTH(payment_date) = MONTH(CURDATE()) 
                  AND YEAR(payment_date) = YEAR(CURDATE())";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] ? $row['total'] : 0;
    }

    /**
     * Get total payments for current year
     */
    public function getTotalYearly() {
        $query = "SELECT SUM(amount) as total FROM " . $this->table_name . " 
                  WHERE YEAR(payment_date) = YEAR(CURDATE())";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] ? $row['total'] : 0;
    }
}
?>
