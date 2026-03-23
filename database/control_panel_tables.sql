-- EnPharChem Platform - Control Panel Tables
-- Active Directory, CMS, Marketing, Training

-- ============================================================
-- ACTIVE DIRECTORY TABLES
-- ============================================================

CREATE TABLE IF NOT EXISTS ad_groups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    dn VARCHAR(500),
    group_type ENUM('security','distribution','organizational') DEFAULT 'security',
    member_count INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS ad_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    display_name VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    department VARCHAR(100),
    title VARCHAR(255),
    group_id INT,
    phone VARCHAR(50),
    location VARCHAR(255),
    manager VARCHAR(255),
    account_status ENUM('active','disabled','locked','expired') DEFAULT 'active',
    last_sync DATETIME,
    last_logon DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (group_id) REFERENCES ad_groups(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================================
-- CMS TABLES
-- ============================================================

CREATE TABLE IF NOT EXISTS cms_pages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    content LONGTEXT,
    category ENUM('documentation','support','product','news','legal','general') DEFAULT 'general',
    status ENUM('draft','published','archived') DEFAULT 'draft',
    author_id INT,
    meta_description TEXT,
    meta_keywords VARCHAR(500),
    featured_image VARCHAR(500),
    view_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================================
-- MARKETING MATERIALS TABLE
-- ============================================================

CREATE TABLE IF NOT EXISTS marketing_materials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    material_type ENUM('brochure','whitepaper','case_study','datasheet','presentation','video','infographic') NOT NULL,
    category VARCHAR(100),
    target_audience VARCHAR(255),
    file_url VARCHAR(500),
    thumbnail_url VARCHAR(500),
    file_size VARCHAR(50),
    status ENUM('draft','review','approved','published') DEFAULT 'draft',
    download_count INT DEFAULT 0,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================================
-- TRAINING TABLES
-- ============================================================

CREATE TABLE IF NOT EXISTS training_courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    category ENUM('process_simulation','exchanger_design','apc','mes','supply_chain','apm','grid_mgmt','general') DEFAULT 'general',
    level ENUM('beginner','intermediate','advanced','expert') DEFAULT 'beginner',
    duration_hours DECIMAL(5,1),
    instructor VARCHAR(255),
    prerequisites TEXT,
    learning_objectives TEXT,
    enrollment_count INT DEFAULT 0,
    max_enrollment INT DEFAULT 50,
    status ENUM('draft','active','archived') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS training_lessons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    content LONGTEXT,
    lesson_order INT DEFAULT 0,
    lesson_type ENUM('video','document','quiz','lab','interactive') DEFAULT 'document',
    duration_minutes INT DEFAULT 30,
    resources JSON,
    status ENUM('draft','published') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES training_courses(id) ON DELETE CASCADE
) ENGINE=InnoDB;
