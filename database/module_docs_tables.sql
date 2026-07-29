-- EnPharChem - Module Documentation Tables
-- Backs the Module Catalogue brochure and the How-To Manual.
--
-- Per-module feature bullets live in the existing (previously unused)
-- modules.features column as a JSON array. Task walkthroughs live here.

CREATE TABLE IF NOT EXISTS module_tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    module_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    objective VARCHAR(500),
    inputs VARCHAR(500),
    outputs VARCHAR(500),
    steps TEXT,                      -- JSON array of ordered step strings
    tip VARCHAR(500),                -- "how helper" hint shown beside the walkthrough
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_module_task (module_id, title),
    KEY idx_module (module_id),
    CONSTRAINT fk_module_tasks_module FOREIGN KEY (module_id)
        REFERENCES modules(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
