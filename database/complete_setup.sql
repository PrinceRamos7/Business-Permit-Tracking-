-- ============================================
-- COMPLETE BPLO DATABASE SETUP
-- For CPDO Database
-- Copy and paste this ENTIRE file into phpMyAdmin SQL tab
-- ============================================

-- Make sure we're using the correct database
USE cpdo;

-- ============================================
-- DROP ALL TABLES (Clean slate)
-- ============================================
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS application_documents;
DROP TABLE IF EXISTS applications;
DROP TABLE IF EXISTS applicants;
DROP TABLE IF EXISTS renewals;
DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS permits;
DROP TABLE IF EXISTS businesses;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

-- ============================================
-- 1. USERS TABLE (Admin Accounts)
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
    INDEX idx_owner_name (owner_name),
    INDEX idx_date_registered (date_registered)
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
    INDEX idx_status (status),
    INDEX idx_expiry_date (expiry_date)
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
    INDEX idx_OR_number (OR_number),
    INDEX idx_payment_date (payment_date)
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
-- 6. APPLICANTS TABLE (Applicant Portal)
-- ============================================
CREATE TABLE applicants (
    applicant_id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    contact_number VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    status ENUM('Active', 'Inactive') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- 7. APPLICATIONS TABLE
-- ============================================
CREATE TABLE applications (
    application_id INT AUTO_INCREMENT PRIMARY KEY,
    applicant_id INT NOT NULL,
    business_name VARCHAR(255) NOT NULL,
    business_address TEXT NOT NULL,
    business_type VARCHAR(100) NOT NULL,
    contact_number VARCHAR(20) NOT NULL,
    owner_name VARCHAR(255) NOT NULL,
    application_type ENUM('New', 'Renewal') DEFAULT 'New',
    status ENUM('Pending', 'Under Review', 'Approved', 'Rejected') DEFAULT 'Pending',
    remarks TEXT,
    amount DECIMAL(10, 2),
    payment_status ENUM('Unpaid', 'Paid', 'Verified') DEFAULT 'Unpaid',
    payment_proof VARCHAR(255),
    documents_verified ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
    submitted_date DATE NOT NULL,
    reviewed_date DATE,
    approved_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (applicant_id) REFERENCES applicants(applicant_id) ON DELETE CASCADE,
    INDEX idx_status (status),
    INDEX idx_applicant_id (applicant_id),
    INDEX idx_submitted_date (submitted_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- 8. APPLICATION DOCUMENTS TABLE
-- ============================================
CREATE TABLE application_documents (
    document_id INT AUTO_INCREMENT PRIMARY KEY,
    application_id INT NOT NULL,
    document_name VARCHAR(255) NOT NULL,
    document_type VARCHAR(100) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
    remarks TEXT,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (application_id) REFERENCES applications(application_id) ON DELETE CASCADE,
    INDEX idx_application_id (application_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- INSERT DEFAULT DATA
-- ============================================

-- Default Admin User (username: admin, password: admin123)
INSERT INTO users (username, password, role) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin');

-- Sample Businesses
INSERT INTO businesses (business_name, owner_name, business_address, contact_number, business_type, date_registered) VALUES
('Ilagan Sari-Sari Store', 'Juan Dela Cruz', 'Brgy. Centro, Ilagan City', '09171234567', 'Retail Store', '2024-01-15'),
('ABC Construction Services', 'Maria Santos', 'Brgy. San Vicente, Ilagan City', '09281234567', 'Contractor', '2024-02-20'),
('Ilagan Internet Cafe', 'Pedro Reyes', 'Brgy. Marana, Ilagan City', '09391234567', 'Internet Cafe', '2024-03-10');

-- Sample Permits
INSERT INTO permits (business_id, permit_number, date_issued, expiry_date, status) VALUES
(1, 'ILGN-2024-00001', '2024-01-15', '2025-01-15', 'Active'),
(2, 'ILGN-2024-00002', '2024-02-20', '2025-02-20', 'Active'),
(3, 'ILGN-2024-00003', '2024-03-10', '2025-03-10', 'Active');

-- Sample Payments
INSERT INTO payments (business_id, amount, OR_number, payment_date) VALUES
(1, 5000.00, 'OR-2024-0001', '2024-01-15'),
(2, 15000.00, 'OR-2024-0002', '2024-02-20'),
(3, 8000.00, 'OR-2024-0003', '2024-03-10');

-- Sample Renewals
INSERT INTO renewals (business_id, renewal_date, new_expiry_date) VALUES
(1, '2024-01-15', '2025-01-15'),
(2, '2024-02-20', '2025-02-20'),
(3, '2024-03-10', '2025-03-10');

-- Test Applicant (email: applicant@test.com, password: applicant123)
INSERT INTO applicants (email, password, first_name, last_name, contact_number, address) VALUES 
('applicant@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John', 'Doe', '09171234567', 'Brgy. Centro, Ilagan City');

-- Sample Applications
INSERT INTO applications (applicant_id, business_name, business_address, business_type, contact_number, owner_name, application_type, status, submitted_date, amount) VALUES
(1, 'Sample Restaurant', 'Brgy. Centro, Ilagan City', 'Food Service', '09171234567', 'John Doe', 'New', 'Pending', '2024-11-20', 10000.00),
(1, 'Sample Retail Store', 'Brgy. San Vicente, Ilagan City', 'Retail', '09171234567', 'John Doe', 'New', 'Under Review', '2024-11-15', 5000.00);

-- ============================================
-- VERIFICATION
-- ============================================
SELECT '✅ Database setup complete!' as Status;
SELECT 'All tables created successfully' as Message;

-- Show table counts
SELECT 
    (SELECT COUNT(*) FROM users) as users_count,
    (SELECT COUNT(*) FROM businesses) as businesses_count,
    (SELECT COUNT(*) FROM permits) as permits_count,
    (SELECT COUNT(*) FROM payments) as payments_count,
    (SELECT COUNT(*) FROM renewals) as renewals_count,
    (SELECT COUNT(*) FROM applicants) as applicants_count,
    (SELECT COUNT(*) FROM applications) as applications_count;

-- Show all tables
SHOW TABLES;
