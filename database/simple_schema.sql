-- ============================================
-- SIMPLE BPLO DATABASE SCHEMA
-- Basic CRUD System
-- ============================================

CREATE DATABASE IF NOT EXISTS bplo_ilagan;
USE bplo_ilagan;

-- Drop existing tables
DROP TABLE IF EXISTS renewals;
DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS permits;
DROP TABLE IF EXISTS businesses;
DROP TABLE IF EXISTS users;

-- ============================================
-- 1. USERS TABLE (Admin Only)
-- ============================================
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('Admin', 'Staff') DEFAULT 'Staff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- 2. BUSINESSES TABLE
-- ============================================
CREATE TABLE businesses (
    business_id INT AUTO_INCREMENT PRIMARY KEY,
    business_name VARCHAR(255) NOT NULL,
    owner_name VARCHAR(255) NOT NULL,
    business_address TEXT NOT NULL,
    contact_number VARCHAR(20) NOT NULL,
    business_type VARCHAR(100) NOT NULL,
    date_registered DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_business_name (business_name),
    INDEX idx_owner_name (owner_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- 3. PERMITS TABLE
-- ============================================
CREATE TABLE permits (
    permit_id INT AUTO_INCREMENT PRIMARY KEY,
    business_id INT NOT NULL,
    permit_number VARCHAR(50) UNIQUE NOT NULL,
    date_issued DATE NOT NULL,
    expiry_date DATE NOT NULL,
    status ENUM('Active', 'Expired', 'Suspended') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (business_id) REFERENCES businesses(business_id) ON DELETE CASCADE,
    INDEX idx_permit_number (permit_number),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- 4. PAYMENTS TABLE
-- ============================================
CREATE TABLE payments (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    business_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    OR_number VARCHAR(50) UNIQUE NOT NULL,
    payment_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (business_id) REFERENCES businesses(business_id) ON DELETE CASCADE,
    INDEX idx_OR_number (OR_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- 5. RENEWALS TABLE
-- ============================================
CREATE TABLE renewals (
    renewal_id INT AUTO_INCREMENT PRIMARY KEY,
    business_id INT NOT NULL,
    renewal_date DATE NOT NULL,
    new_expiry_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (business_id) REFERENCES businesses(business_id) ON DELETE CASCADE,
    INDEX idx_renewal_date (renewal_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- INSERT DEFAULT ADMIN USER
-- Username: admin
-- Password: admin123
-- ============================================
INSERT INTO users (username, password, role) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin');

-- ============================================
-- SAMPLE DATA
-- ============================================
INSERT INTO businesses (business_name, owner_name, business_address, contact_number, business_type, date_registered) VALUES
('Ilagan Sari-Sari Store', 'Juan Dela Cruz', 'Brgy. Centro, Ilagan City', '09171234567', 'Retail Store', '2024-01-15'),
('ABC Construction', 'Maria Santos', 'Brgy. San Vicente, Ilagan City', '09281234567', 'Contractor', '2024-02-20'),
('Ilagan Internet Cafe', 'Pedro Reyes', 'Brgy. Marana, Ilagan City', '09391234567', 'Internet Cafe', '2024-03-10');

INSERT INTO permits (business_id, permit_number, date_issued, expiry_date, status) VALUES
(1, 'ILGN-2024-00001', '2024-01-15', '2025-01-15', 'Active'),
(2, 'ILGN-2024-00002', '2024-02-20', '2025-02-20', 'Active'),
(3, 'ILGN-2024-00003', '2024-03-10', '2025-03-10', 'Active');

INSERT INTO payments (business_id, amount, OR_number, payment_date) VALUES
(1, 5000.00, 'OR-2024-0001', '2024-01-15'),
(2, 15000.00, 'OR-2024-0002', '2024-02-20'),
(3, 8000.00, 'OR-2024-0003', '2024-03-10');

INSERT INTO renewals (business_id, renewal_date, new_expiry_date) VALUES
(1, '2024-01-15', '2025-01-15'),
(2, '2024-02-20', '2025-02-20'),
(3, '2024-03-10', '2025-03-10');

SELECT 'Simple BPLO Database Created Successfully!' as message;
