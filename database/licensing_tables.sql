-- EnPharChem Platform - Licensing Portal Tables
-- Run migrate_licensing.php to execute this migration

CREATE TABLE IF NOT EXISTS licenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    license_key VARCHAR(50) NOT NULL UNIQUE,
    user_id INT,
    license_type ENUM('trial','standard','professional','enterprise') NOT NULL DEFAULT 'trial',
    status ENUM('active','suspended','expired','revoked') DEFAULT 'active',
    issued_date DATE NOT NULL,
    expiry_date DATE,
    max_modules INT DEFAULT 5,
    max_users INT DEFAULT 1,
    issued_by INT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (issued_by) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS license_modules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    license_id INT NOT NULL,
    module_id INT NOT NULL,
    status ENUM('granted','denied','pending','revoked') DEFAULT 'pending',
    granted_date DATE,
    granted_by INT,
    denied_reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_license_module (license_id, module_id),
    FOREIGN KEY (license_id) REFERENCES licenses(id) ON DELETE CASCADE,
    FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE,
    FOREIGN KEY (granted_by) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS license_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    module_id INT NOT NULL,
    license_id INT,
    request_type ENUM('new','upgrade','renewal','additional_module') DEFAULT 'new',
    status ENUM('pending','approved','denied','cancelled') DEFAULT 'pending',
    justification TEXT,
    reviewed_by INT,
    review_date DATETIME,
    review_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE,
    FOREIGN KEY (license_id) REFERENCES licenses(id) ON DELETE SET NULL,
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS license_audit_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    license_id INT,
    action VARCHAR(100) NOT NULL,
    performed_by INT,
    details TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (license_id) REFERENCES licenses(id) ON DELETE SET NULL,
    FOREIGN KEY (performed_by) REFERENCES users(id) ON DELETE SET NULL
);
