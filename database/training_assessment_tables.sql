-- EnPharChem Platform - Training, Assessment & Certificate Tables
-- Run via migrate_training.php

-- ============================================================
-- PREREQUISITE TABLES (from control_panel_tables.sql)
-- ============================================================

CREATE TABLE IF NOT EXISTS training_courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    category ENUM('process_simulation','exchanger_design','apc','mes','supply_chain','apm','grid_mgmt','general','process_sim_energy','process_sim_chemicals','concurrent_feed','subsurface_science','energy_optimization','operations_support','dynamic_optimization','petroleum_supply_chain','industrial_data_fabric','digital_grid_mgmt') DEFAULT 'general',
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

-- ============================================================
-- ASSESSMENT QUESTIONS
-- ============================================================

CREATE TABLE IF NOT EXISTS assessment_questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    question TEXT NOT NULL,
    question_type ENUM('multiple_choice','true_false','fill_blank') DEFAULT 'multiple_choice',
    option_a VARCHAR(500),
    option_b VARCHAR(500),
    option_c VARCHAR(500),
    option_d VARCHAR(500),
    correct_answer CHAR(1) NOT NULL,
    explanation TEXT,
    difficulty ENUM('easy','medium','hard') DEFAULT 'medium',
    points INT DEFAULT 10,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES training_courses(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- ASSESSMENT ATTEMPTS
-- ============================================================

CREATE TABLE IF NOT EXISTS assessment_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    score DECIMAL(5,2),
    total_points INT,
    earned_points INT,
    total_questions INT,
    correct_answers INT,
    time_taken_minutes INT,
    passed TINYINT(1) DEFAULT 0,
    answers JSON,
    started_at DATETIME,
    completed_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES training_courses(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- CERTIFICATES
-- ============================================================

CREATE TABLE IF NOT EXISTS certificates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    attempt_id INT NOT NULL,
    certificate_number VARCHAR(50) NOT NULL UNIQUE,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    course_title VARCHAR(255) NOT NULL,
    course_level VARCHAR(50),
    score DECIMAL(5,2),
    issue_date DATE NOT NULL,
    expiry_date DATE,
    status ENUM('active','expired','revoked') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES training_courses(id) ON DELETE CASCADE,
    FOREIGN KEY (attempt_id) REFERENCES assessment_attempts(id) ON DELETE CASCADE
) ENGINE=InnoDB;
