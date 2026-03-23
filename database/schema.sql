-- EnPharChem Platform Database Schema
-- Benchmarked against AspenTech EPC Software
-- MySQL Database

CREATE DATABASE IF NOT EXISTS enpharchem CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE enpharchem;

-- ============================================================
-- CORE TABLES
-- ============================================================

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    role ENUM('superuser','admin','engineer','operator','viewer') DEFAULT 'engineer',
    department VARCHAR(100),
    company VARCHAR(255),
    license_type ENUM('trial','standard','professional','enterprise') DEFAULT 'trial',
    is_active TINYINT(1) DEFAULT 1,
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    category ENUM('energy','chemicals','pharma','subsurface','grid','general') DEFAULT 'general',
    status ENUM('draft','active','completed','archived') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS module_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    icon VARCHAR(50),
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS modules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    features TEXT,
    version VARCHAR(20) DEFAULT '1.0.0',
    icon VARCHAR(50),
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    license_required ENUM('standard','professional','enterprise') DEFAULT 'standard',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES module_categories(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- PROCESS SIMULATION TABLES
-- ============================================================

CREATE TABLE IF NOT EXISTS simulations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    module_id INT NOT NULL,
    user_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    simulation_type VARCHAR(100),
    status ENUM('draft','running','completed','failed','paused') DEFAULT 'draft',
    input_data JSON,
    output_data JSON,
    parameters JSON,
    convergence_status VARCHAR(50),
    iterations INT DEFAULT 0,
    execution_time FLOAT,
    started_at DATETIME,
    completed_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (module_id) REFERENCES modules(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS process_flowsheets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    simulation_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    flowsheet_data JSON,
    svg_data LONGTEXT,
    version INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (simulation_id) REFERENCES simulations(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS streams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    flowsheet_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    stream_type ENUM('material','energy','information') DEFAULT 'material',
    phase ENUM('vapor','liquid','mixed','solid') DEFAULT 'liquid',
    temperature DOUBLE,
    pressure DOUBLE,
    flow_rate DOUBLE,
    composition JSON,
    properties JSON,
    source_unit_id INT,
    destination_unit_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (flowsheet_id) REFERENCES process_flowsheets(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS unit_operations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    flowsheet_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    unit_type VARCHAR(100) NOT NULL,
    model_type VARCHAR(100),
    parameters JSON,
    results JSON,
    position_x FLOAT DEFAULT 0,
    position_y FLOAT DEFAULT 0,
    status ENUM('configured','converged','error','warning') DEFAULT 'configured',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (flowsheet_id) REFERENCES process_flowsheets(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- CHEMICAL PROPERTIES & THERMODYNAMICS
-- ============================================================

CREATE TABLE IF NOT EXISTS chemical_components (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cas_number VARCHAR(20),
    name VARCHAR(255) NOT NULL,
    formula VARCHAR(100),
    molecular_weight DOUBLE,
    boiling_point DOUBLE,
    melting_point DOUBLE,
    critical_temperature DOUBLE,
    critical_pressure DOUBLE,
    critical_volume DOUBLE,
    acentric_factor DOUBLE,
    properties JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS thermodynamic_models (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    model_type VARCHAR(100) NOT NULL,
    description TEXT,
    equation_of_state VARCHAR(100),
    parameters JSON,
    applicable_phases VARCHAR(100),
    temperature_range JSON,
    pressure_range JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS binary_interaction_params (
    id INT AUTO_INCREMENT PRIMARY KEY,
    model_id INT NOT NULL,
    component1_id INT NOT NULL,
    component2_id INT NOT NULL,
    parameters JSON,
    temperature_dependent TINYINT(1) DEFAULT 0,
    source VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (model_id) REFERENCES thermodynamic_models(id),
    FOREIGN KEY (component1_id) REFERENCES chemical_components(id),
    FOREIGN KEY (component2_id) REFERENCES chemical_components(id)
) ENGINE=InnoDB;

-- ============================================================
-- EXCHANGER DESIGN & RATING
-- ============================================================

CREATE TABLE IF NOT EXISTS heat_exchangers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    simulation_id INT NOT NULL,
    exchanger_type ENUM('shell_tube','air_cooled','plate','plate_fin','fired_heater','coil_wound') NOT NULL,
    name VARCHAR(255) NOT NULL,
    design_data JSON,
    hot_side JSON,
    cold_side JSON,
    geometry JSON,
    materials JSON,
    thermal_results JSON,
    mechanical_results JSON,
    pressure_drop JSON,
    rating_results JSON,
    status ENUM('draft','designed','rated','verified') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (simulation_id) REFERENCES simulations(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- COST ESTIMATION (Concurrent FEED)
-- ============================================================

CREATE TABLE IF NOT EXISTS cost_estimates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    estimate_type ENUM('capital','operating','in_plant','total') DEFAULT 'capital',
    methodology VARCHAR(100),
    base_year INT,
    currency VARCHAR(10) DEFAULT 'USD',
    location_factor DOUBLE DEFAULT 1.0,
    equipment_costs JSON,
    installation_costs JSON,
    indirect_costs JSON,
    contingency_pct DOUBLE DEFAULT 15.0,
    total_cost DOUBLE,
    cost_breakdown JSON,
    status ENUM('draft','preliminary','detailed','final') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS equipment_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cost_estimate_id INT NOT NULL,
    equipment_type VARCHAR(100) NOT NULL,
    name VARCHAR(255) NOT NULL,
    specifications JSON,
    material VARCHAR(100),
    size_parameter VARCHAR(100),
    size_value DOUBLE,
    base_cost DOUBLE,
    installation_factor DOUBLE DEFAULT 1.0,
    total_cost DOUBLE,
    vendor VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cost_estimate_id) REFERENCES cost_estimates(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- SUBSURFACE SCIENCE & ENGINEERING
-- ============================================================

CREATE TABLE IF NOT EXISTS reservoir_models (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    model_type ENUM('seismic','geological','reservoir','well') NOT NULL,
    grid_data JSON,
    properties JSON,
    well_data JSON,
    simulation_params JSON,
    results JSON,
    status ENUM('draft','processing','completed','validated') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS seismic_surveys (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    survey_type ENUM('2D','3D','4D') DEFAULT '3D',
    acquisition_params JSON,
    processing_params JSON,
    interpretation_data JSON,
    status VARCHAR(50) DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- ADVANCED PROCESS CONTROL
-- ============================================================

CREATE TABLE IF NOT EXISTS apc_controllers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    controller_type ENUM('dmc3','nonlinear','inferential','transition') NOT NULL,
    configuration JSON,
    manipulated_vars JSON,
    controlled_vars JSON,
    disturbance_vars JSON,
    model_data JSON,
    tuning_params JSON,
    performance_metrics JSON,
    status ENUM('configured','testing','active','inactive') DEFAULT 'configured',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS apc_models (
    id INT AUTO_INCREMENT PRIMARY KEY,
    controller_id INT NOT NULL,
    model_type VARCHAR(100),
    input_variable VARCHAR(255),
    output_variable VARCHAR(255),
    gain DOUBLE,
    time_constant DOUBLE,
    dead_time DOUBLE,
    model_order INT DEFAULT 1,
    model_data JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (controller_id) REFERENCES apc_controllers(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- MANUFACTURING EXECUTION SYSTEMS
-- ============================================================

CREATE TABLE IF NOT EXISTS plant_data_tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    tag_name VARCHAR(255) NOT NULL,
    description TEXT,
    data_type ENUM('analog','digital','string') DEFAULT 'analog',
    engineering_unit VARCHAR(50),
    low_limit DOUBLE,
    high_limit DOUBLE,
    source_system VARCHAR(100),
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS plant_data_history (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    tag_id INT NOT NULL,
    timestamp DATETIME NOT NULL,
    value DOUBLE,
    quality ENUM('good','bad','uncertain') DEFAULT 'good',
    INDEX idx_tag_time (tag_id, timestamp),
    FOREIGN KEY (tag_id) REFERENCES plant_data_tags(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS production_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    record_type VARCHAR(100),
    batch_id VARCHAR(100),
    product_name VARCHAR(255),
    quantity DOUBLE,
    unit VARCHAR(50),
    quality_data JSON,
    start_time DATETIME,
    end_time DATETIME,
    status ENUM('planned','in_progress','completed','rejected') DEFAULT 'planned',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- PETROLEUM SUPPLY CHAIN
-- ============================================================

CREATE TABLE IF NOT EXISTS supply_chain_plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    plan_type ENUM('production','blending','scheduling','distribution') NOT NULL,
    planning_horizon INT DEFAULT 30,
    time_unit ENUM('hours','days','weeks','months') DEFAULT 'days',
    objective_function TEXT,
    constraints JSON,
    variables JSON,
    solution JSON,
    optimal_value DOUBLE,
    status ENUM('draft','solving','optimal','infeasible','suboptimal') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS crude_assays (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    crude_name VARCHAR(255) NOT NULL,
    source VARCHAR(255),
    api_gravity DOUBLE,
    sulfur_content DOUBLE,
    tbp_curve JSON,
    properties JSON,
    distillation_data JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS blend_recipes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    components JSON,
    quality_specs JSON,
    optimization_results JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- ASSET PERFORMANCE MANAGEMENT
-- ============================================================

CREATE TABLE IF NOT EXISTS assets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    asset_type VARCHAR(100),
    location VARCHAR(255),
    manufacturer VARCHAR(255),
    model VARCHAR(255),
    serial_number VARCHAR(100),
    install_date DATE,
    specifications JSON,
    maintenance_schedule JSON,
    status ENUM('operational','degraded','failed','maintenance','decommissioned') DEFAULT 'operational',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS asset_health_scores (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    asset_id INT NOT NULL,
    health_score DOUBLE,
    risk_score DOUBLE,
    predicted_failure_date DATE,
    anomaly_detected TINYINT(1) DEFAULT 0,
    sensor_data JSON,
    analysis_results JSON,
    timestamp DATETIME NOT NULL,
    INDEX idx_asset_time (asset_id, timestamp),
    FOREIGN KEY (asset_id) REFERENCES assets(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS maintenance_events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    asset_id INT NOT NULL,
    event_type ENUM('preventive','corrective','predictive','condition_based') NOT NULL,
    description TEXT,
    priority ENUM('low','medium','high','critical') DEFAULT 'medium',
    scheduled_date DATE,
    completed_date DATE,
    cost DOUBLE,
    downtime_hours DOUBLE,
    status ENUM('planned','in_progress','completed','cancelled') DEFAULT 'planned',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (asset_id) REFERENCES assets(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- ENERGY & GRID MANAGEMENT
-- ============================================================

CREATE TABLE IF NOT EXISTS energy_networks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    network_type ENUM('electrical','steam','fuel_gas','cooling_water','microgrid') NOT NULL,
    topology JSON,
    nodes JSON,
    connections JSON,
    optimization_results JSON,
    status VARCHAR(50) DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS grid_assets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    network_id INT NOT NULL,
    asset_type ENUM('generator','transformer','line','load','storage','der','substation') NOT NULL,
    name VARCHAR(255) NOT NULL,
    capacity DOUBLE,
    rating DOUBLE,
    location_data JSON,
    electrical_params JSON,
    status ENUM('online','offline','fault','maintenance') DEFAULT 'online',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (network_id) REFERENCES energy_networks(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS scada_points (
    id INT AUTO_INCREMENT PRIMARY KEY,
    network_id INT NOT NULL,
    point_name VARCHAR(255) NOT NULL,
    point_type ENUM('analog_input','analog_output','digital_input','digital_output','calculated') NOT NULL,
    value DOUBLE,
    quality VARCHAR(20) DEFAULT 'good',
    alarm_status VARCHAR(50),
    last_updated DATETIME,
    FOREIGN KEY (network_id) REFERENCES energy_networks(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- DYNAMIC OPTIMIZATION
-- ============================================================

CREATE TABLE IF NOT EXISTS optimization_problems (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    problem_type ENUM('linear','nonlinear','mixed_integer','dynamic','stochastic') NOT NULL,
    objective TEXT,
    objective_sense ENUM('minimize','maximize') DEFAULT 'minimize',
    variables JSON,
    constraints JSON,
    bounds JSON,
    solution JSON,
    solver_log TEXT,
    optimal_value DOUBLE,
    solve_time FLOAT,
    status ENUM('formulated','solving','optimal','infeasible','unbounded') DEFAULT 'formulated',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- SUPPLY CHAIN MANAGEMENT
-- ============================================================

CREATE TABLE IF NOT EXISTS demand_forecasts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    product VARCHAR(255) NOT NULL,
    forecast_period DATE NOT NULL,
    quantity DOUBLE,
    confidence_low DOUBLE,
    confidence_high DOUBLE,
    forecast_method VARCHAR(100),
    actual_quantity DOUBLE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS schedules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    schedule_type ENUM('production','shipping','maintenance','blending') NOT NULL,
    start_date DATETIME,
    end_date DATETIME,
    schedule_data JSON,
    resource_allocation JSON,
    optimization_score DOUBLE,
    status ENUM('draft','published','active','completed') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- INDUSTRIAL DATA FABRIC
-- ============================================================

CREATE TABLE IF NOT EXISTS data_sources (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    source_type ENUM('opc_ua','opc_da','modbus','mqtt','rest_api','database','file') NOT NULL,
    connection_string TEXT,
    configuration JSON,
    polling_interval INT DEFAULT 1000,
    is_connected TINYINT(1) DEFAULT 0,
    last_connected DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS data_transformations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    source_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    transform_type VARCHAR(100),
    input_tags JSON,
    output_tags JSON,
    transform_logic TEXT,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (source_id) REFERENCES data_sources(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- AUDIT & LOGGING
-- ============================================================

CREATE TABLE IF NOT EXISTS audit_log (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(100),
    entity_id INT,
    details JSON,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_action (user_id, action),
    INDEX idx_entity (entity_type, entity_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT,
    notification_type ENUM('info','warning','error','success') DEFAULT 'info',
    is_read TINYINT(1) DEFAULT 0,
    link VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- SEED DATA: Module Categories
-- ============================================================

INSERT INTO module_categories (name, slug, description, icon, sort_order) VALUES
('Process Simulation for Energy', 'process-sim-energy', 'Comprehensive process simulation tools for energy industry including oil & gas, refining, and upstream operations', 'fa-bolt', 1),
('Process Simulation for Chemicals', 'process-sim-chemicals', 'Advanced chemical process simulation including polymers, batch, distillation, and solids modeling', 'fa-flask', 2),
('Exchanger Design & Rating', 'exchanger-design', 'Complete heat exchanger design, rating, and mechanical analysis suite', 'fa-exchange-alt', 3),
('Concurrent FEED', 'concurrent-feed', 'Front-end engineering design with integrated cost estimation and 3D layout', 'fa-drafting-compass', 4),
('Subsurface Science & Engineering', 'subsurface-science', 'Seismic interpretation, geological modeling, reservoir simulation, and well management', 'fa-mountain', 5),
('Energy & Utilities Optimization', 'energy-optimization', 'Energy analysis, sustainability planning, and utilities optimization', 'fa-leaf', 6),
('Operations Support', 'operations-support', 'Online process monitoring and simulation workbook tools', 'fa-desktop', 7),
('Advanced Process Control', 'advanced-process-control', 'Dynamic matrix control, inferential qualities, and transition management', 'fa-sliders-h', 8),
('Dynamic Optimization', 'dynamic-optimization', 'Global dynamic optimization for real-time process optimization', 'fa-chart-line', 9),
('Manufacturing Execution Systems', 'mes', 'Plant historian, production management, reconciliation, and tank operations', 'fa-industry', 10),
('Petroleum Supply Chain', 'petroleum-supply-chain', 'Planning, scheduling, blending optimization, and supply chain management for petroleum', 'fa-oil-can', 11),
('Supply Chain Management', 'supply-chain-mgmt', 'Demand management, supply planning, plant scheduling, and insights', 'fa-truck', 12),
('Asset Performance Management', 'apm', 'Predictive maintenance, process monitoring, and multivariate analytics', 'fa-heartbeat', 13),
('Industrial Data Fabric', 'industrial-data-fabric', 'Unified industrial data management and integration platform', 'fa-database', 14),
('Digital Grid Management', 'digital-grid-mgmt', 'SCADA, energy management, distribution management, microgrid, and DER management', 'fa-plug', 15);

-- ============================================================
-- SEED DATA: Modules
-- ============================================================

-- Process Simulation for Energy
INSERT INTO modules (category_id, name, slug, description, icon, sort_order) VALUES
(1, 'EnPharChem HYSYS', 'enpharchem-hysys', 'Comprehensive process simulator for oil & gas, refining, and energy operations with rigorous thermodynamics', 'fa-project-diagram', 1),
(1, 'Acid Gas Cleaning', 'acid-gas-cleaning', 'Simulation of acid gas removal processes including amine treating and sulfur recovery', 'fa-wind', 2),
(1, 'EnPharChem HYSYS Crude', 'hysys-crude', 'Crude oil characterization and petroleum assay management for refinery modeling', 'fa-tint', 3),
(1, 'BLOWDOWN Technology', 'blowdown-technology', 'Depressurization and blowdown analysis for pressure vessels and piping systems', 'fa-compress-arrows-alt', 4),
(1, 'Relief Sizing', 'relief-sizing-energy', 'Pressure relief device sizing and analysis for process safety compliance', 'fa-shield-alt', 5),
(1, 'Sulsim Sulfur Recovery', 'sulsim-sulfur-recovery', 'Simulation of Claus and tail gas treatment processes for sulfur recovery', 'fa-atom', 6),
(1, 'Activated Economics', 'activated-economics-energy', 'Integrated economic analysis with process simulation for energy projects', 'fa-dollar-sign', 7),
(1, 'Activated Energy Analysis', 'activated-energy-analysis-energy', 'Pinch analysis and energy integration for heat recovery optimization', 'fa-fire', 8),
(1, 'Activated Exchanger Design & Rating', 'activated-edr-energy', 'Integrated exchanger design within process simulation environment', 'fa-cogs', 9),
(1, 'EnPharChem HYSYS Petroleum Refining', 'hysys-petroleum-refining', 'Detailed refinery unit modeling including FCC, reformer, and hydroprocessing', 'fa-gas-pump', 10),
(1, 'Refinery Reactor Models', 'refinery-reactor-models', 'Kinetic reactor models for refinery conversion processes', 'fa-vial', 11),
(1, 'EnPharChem HYSYS Upstream', 'hysys-upstream', 'Upstream oil and gas production facility modeling and optimization', 'fa-oil-can', 12),
(1, 'EnPharChem HYSYS Dynamics', 'hysys-dynamics', 'Dynamic simulation for process control design, safety studies, and operability analysis', 'fa-wave-square', 13),
(1, 'Activated Dynamics', 'activated-dynamics', 'Enhanced dynamic simulation with advanced control and safety features', 'fa-sync-alt', 14),
(1, 'EnPharChem Hybrid Models', 'hybrid-models-energy', 'AI/ML-enhanced process models combining first-principles with data-driven approaches', 'fa-brain', 15),
(1, 'EnPharChem Multi Case', 'multi-case-energy', 'Parametric studies and case management for systematic process analysis', 'fa-th', 16),
(1, 'EnPharChem Operator Training', 'operator-training', 'High-fidelity operator training simulator for energy operations', 'fa-user-graduate', 17),
(1, 'EnPharChem Flare System Analyzer', 'flare-system-analyzer-energy', 'Flare system modeling, radiation analysis, and environmental compliance', 'fa-fire-alt', 18);

-- Process Simulation for Chemicals
INSERT INTO modules (category_id, name, slug, description, icon, sort_order) VALUES
(2, 'EnPharChem Adsorption', 'enpharchem-adsorption', 'Adsorption process simulation including PSA, TSA, and VSA systems', 'fa-filter', 1),
(2, 'EnPharChem Chromatography', 'enpharchem-chromatography', 'Chromatographic separation process modeling and optimization', 'fa-columns', 2),
(2, 'EnPharChem Custom Modeler', 'enpharchem-custom-modeler', 'Custom equation-oriented modeling environment for unique process equipment', 'fa-code', 3),
(2, 'EnPharChem Hybrid Models', 'hybrid-models-chemicals', 'AI/ML-enhanced process models for chemical processes', 'fa-brain', 4),
(2, 'EnPharChem Multi Case', 'multi-case-chemicals', 'Parametric studies and sensitivity analysis for chemical process optimization', 'fa-th', 5),
(2, 'EnPharChem Plus', 'enpharchem-plus', 'Industry-leading chemical process simulator with comprehensive thermodynamics and unit operations', 'fa-plus-circle', 6),
(2, 'EnPharChem Polymers', 'enpharchem-polymers', 'Polymer process simulation including polymerization kinetics and polymer characterization', 'fa-link', 7),
(2, 'EnPharChem Properties', 'enpharchem-properties', 'Physical property estimation, data regression, and thermodynamic model selection', 'fa-table', 8),
(2, 'Batch Modeling in EnPharChem Plus', 'batch-modeling', 'Batch and semi-batch process simulation with recipe management', 'fa-clock', 9),
(2, 'Distillation Modeling in EnPharChem Plus', 'distillation-modeling', 'Advanced distillation column design including packed and tray columns', 'fa-sort-amount-down', 10),
(2, 'Relief Sizing', 'relief-sizing-chemicals', 'Pressure relief device sizing for chemical process safety', 'fa-shield-alt', 11),
(2, 'Solids Modeling', 'solids-modeling', 'Solids handling and processing simulation including drying, crushing, and conveying', 'fa-cubes', 12),
(2, 'Activated Economics', 'activated-economics-chemicals', 'Integrated economic evaluation for chemical process design', 'fa-dollar-sign', 13),
(2, 'Activated Energy Analysis', 'activated-energy-analysis-chemicals', 'Heat integration and energy optimization for chemical processes', 'fa-fire', 14),
(2, 'Activated Exchanger Design & Rating', 'activated-edr-chemicals', 'Integrated heat exchanger design within chemical process simulation', 'fa-cogs', 15),
(2, 'EnPharChem Plus Dynamics', 'enpharchem-plus-dynamics', 'Dynamic simulation of chemical processes for control and safety analysis', 'fa-wave-square', 16),
(2, 'EnPharChem Process Manuals', 'process-manuals', 'Automated process documentation and engineering reports generation', 'fa-book', 17),
(2, 'EnPharChem Flare System Analyzer', 'flare-system-analyzer-chemicals', 'Flare network analysis for chemical plant safety and environmental compliance', 'fa-fire-alt', 18);

-- Exchanger Design & Rating
INSERT INTO modules (category_id, name, slug, description, icon, sort_order) VALUES
(3, 'EnPharChem Air Cooled Exchanger', 'air-cooled-exchanger', 'Design and rating of air-cooled heat exchangers with fan optimization', 'fa-fan', 1),
(3, 'EnPharChem Fired Heater', 'fired-heater', 'Fired heater design, rating, and efficiency analysis', 'fa-fire', 2),
(3, 'EnPharChem Plate Exchanger', 'plate-exchanger', 'Plate heat exchanger thermal and hydraulic design', 'fa-layer-group', 3),
(3, 'EnPharChem Plate Fin Exchanger', 'plate-fin-exchanger', 'Compact plate-fin exchanger design for cryogenic and LNG applications', 'fa-grip-lines', 4),
(3, 'EnPharChem Shell & Tube Exchanger', 'shell-tube-exchanger', 'TEMA-standard shell and tube heat exchanger design and rating', 'fa-arrows-alt-h', 5),
(3, 'EnPharChem Shell & Tube Mechanical', 'shell-tube-mechanical', 'Mechanical design and ASME code compliance for shell and tube exchangers', 'fa-wrench', 6),
(3, 'EnPharChem Coil Wound Exchanger', 'coil-wound-exchanger', 'Coil wound heat exchanger design for LNG and cryogenic services', 'fa-circle-notch', 7);

-- Concurrent FEED
INSERT INTO modules (category_id, name, slug, description, icon, sort_order) VALUES
(4, 'EnPharChem Fidelis', 'enpharchem-fidelis', 'Integrated FEED solution linking process simulation with cost estimation', 'fa-link', 1),
(4, 'EnPharChem Capital Cost Estimator', 'capital-cost-estimator', 'Detailed capital cost estimation with equipment-level breakdown', 'fa-calculator', 2),
(4, 'EnPharChem In-Plant Cost Estimator', 'in-plant-cost-estimator', 'Cost estimation for plant modifications and revamps', 'fa-hard-hat', 3),
(4, 'EnPharChem Process Economic Analyzer', 'process-economic-analyzer', 'Comprehensive economic analysis including NPV, IRR, and payback period', 'fa-chart-pie', 4),
(4, 'EnPharChem OptiPlant 3D Layout', 'optiplant-3d-layout', '3D plant layout design and optimization with clash detection', 'fa-cube', 5),
(4, 'EnPharChem OptiRouter', 'optirouter', 'Automated pipe routing and optimization within 3D plant layout', 'fa-route', 6),
(4, 'EnPharChem Basic Engineering', 'basic-engineering', 'Basic engineering deliverables including PFDs, P&IDs, and datasheets', 'fa-file-alt', 7);

-- Subsurface Science & Engineering
INSERT INTO modules (category_id, name, slug, description, icon, sort_order) VALUES
(5, 'EnPharChem Subsurface Intelligence (ESI)', 'subsurface-intelligence', 'Integrated subsurface data management and interpretation platform', 'fa-globe-americas', 1),
(5, 'EnPharChem Echos', 'enpharchem-echos', 'Seismic data processing and imaging for exploration', 'fa-broadcast-tower', 2),
(5, 'EnPharChem EarthStudy 360', 'earthstudy-360', 'Full-azimuth seismic imaging and velocity model building', 'fa-globe', 3),
(5, 'EnPharChem GeoDepth', 'enpharchem-geodepth', 'Depth imaging and velocity model building for complex geology', 'fa-mountain', 4),
(5, 'EnPharChem SeisEarth', 'enpharchem-seisearth', 'Seismic interpretation and horizon/fault picking', 'fa-layer-group', 5),
(5, 'EnPharChem Geolog', 'enpharchem-geolog', 'Well log analysis and petrophysical interpretation', 'fa-chart-bar', 6),
(5, 'EnPharChem RMS', 'enpharchem-rms', 'Reservoir modeling and simulation with geological framework', 'fa-cubes', 7),
(5, 'EnPharChem SKUA', 'enpharchem-skua', 'Structural and stratigraphic geological modeling', 'fa-draw-polygon', 8),
(5, 'EnPharChem OpsLink', 'enpharchem-opslink', 'Real-time operations data integration for subsurface workflows', 'fa-link', 9),
(5, 'EnPharChem Tempest', 'enpharchem-tempest', 'Reservoir simulation and history matching', 'fa-water', 10),
(5, 'EnPharChem Epos', 'enpharchem-epos', 'Exploration portfolio optimization and risk analysis', 'fa-search-dollar', 11);

-- Energy & Utilities Optimization
INSERT INTO modules (category_id, name, slug, description, icon, sort_order) VALUES
(6, 'EnPharChem Energy Analyzer', 'energy-analyzer', 'Site-wide energy analysis with pinch technology and utility optimization', 'fa-chart-area', 1),
(6, 'EnPharChem Strategic Planning for Sustainability Pathways', 'sustainability-pathways', 'Strategic sustainability planning including carbon footprint and decarbonization pathways', 'fa-seedling', 2),
(6, 'EnPharChem Utilities Planner', 'utilities-planner', 'Utility system modeling, optimization, and scheduling', 'fa-plug', 3);

-- Operations Support
INSERT INTO modules (category_id, name, slug, description, icon, sort_order) VALUES
(7, 'EnPharChem OnLine', 'enpharchem-online', 'Online process simulation connected to plant data for real-time monitoring', 'fa-wifi', 1),
(7, 'EnPharChem Simulation Workbook', 'simulation-workbook', 'Spreadsheet-based interface for process simulation with Excel integration', 'fa-file-excel', 2);

-- Advanced Process Control
INSERT INTO modules (category_id, name, slug, description, icon, sort_order) VALUES
(8, 'EnPharChem DMC3', 'enpharchem-dmc3', 'Dynamic Matrix Control - advanced multivariable predictive controller', 'fa-th-large', 1),
(8, 'EnPharChem Virtual Advisor (EVA) for DMC3', 'eva-dmc3', 'AI-powered advisory system for APC controller maintenance and performance', 'fa-robot', 2),
(8, 'EnPharChem DMC3 Builder', 'dmc3-builder', 'Controller identification, design, and commissioning toolkit', 'fa-tools', 3),
(8, 'EnPharChem Inferential Qualities', 'inferential-qualities', 'Soft sensor development for real-time quality estimation', 'fa-microscope', 4),
(8, 'EnPharChem Nonlinear Controller', 'nonlinear-controller', 'Nonlinear model predictive control for highly nonlinear processes', 'fa-bezier-curve', 5),
(8, 'EnPharChem Transition Management', 'transition-management', 'Automated grade transition and product changeover optimization', 'fa-exchange-alt', 6),
(8, 'EnPharChem Watch Performance Monitor', 'watch-performance-monitor', 'Real-time APC performance monitoring, diagnostics, and KPI tracking', 'fa-tachometer-alt', 7);

-- Dynamic Optimization
INSERT INTO modules (category_id, name, slug, description, icon, sort_order) VALUES
(9, 'EnPharChem GDOT', 'enpharchem-gdot', 'Global Dynamic Optimization Technology for real-time optimization of entire process units', 'fa-bullseye', 1);

-- Manufacturing Execution Systems
INSERT INTO modules (category_id, name, slug, description, icon, sort_order) VALUES
(10, 'EnPharChem InfoPlus.21', 'infoplus-21', 'Enterprise process historian for real-time and historical data management', 'fa-database', 1),
(10, 'EnPharChem Production Record Manager', 'production-record-manager', 'Electronic batch record management and production tracking', 'fa-clipboard-list', 2),
(10, 'enPharChemONE Process Explorer', 'process-explorer', 'Web-based process data visualization and trending application', 'fa-chart-line', 3),
(10, 'EnPharChem Production Execution Manager', 'production-execution-manager', 'Manufacturing operations management and workflow automation', 'fa-tasks', 4),
(10, 'EnPharChem Unified Reconciliation and Accounting', 'unified-reconciliation', 'Material balance reconciliation and production accounting', 'fa-balance-scale', 5),
(10, 'EnPharChem Unified Movements', 'unified-movements', 'Tank farm and material movement tracking and management', 'fa-arrows-alt', 6),
(10, 'EnPharChem Operations Reconciliation and Accounting', 'operations-reconciliation', 'Operations-level material balance and yield accounting', 'fa-calculator', 7),
(10, 'EnPharChem Tank and Operations Manager', 'tank-operations-manager', 'Tank inventory management, gauging, and movement scheduling', 'fa-warehouse', 8);

-- Petroleum Supply Chain
INSERT INTO modules (category_id, name, slug, description, icon, sort_order) VALUES
(11, 'EnPharChem Unified PIMS', 'unified-pims', 'Production planning and optimization with LP/NLP for refineries and petrochemicals', 'fa-sitemap', 1),
(11, 'EnPharChem Unified Scheduling', 'unified-scheduling', 'Integrated production scheduling and crude scheduling', 'fa-calendar-alt', 2),
(11, 'EnPharChem Unified Multisite', 'unified-multisite', 'Multi-site supply chain optimization across multiple refineries', 'fa-network-wired', 3),
(11, 'EnPharChem Virtual Advisor (EVA) for Unified PIMS', 'eva-unified-pims', 'AI-powered planning model advisory and validation system', 'fa-robot', 4),
(11, 'EnPharChem Verify for Planning', 'verify-planning', 'Planning model validation and QA/QC framework', 'fa-check-double', 5),
(11, 'EnPharChem PIMS-AO', 'pims-ao', 'Advanced optimization with successive LP for refinery planning', 'fa-chart-line', 6),
(11, 'EnPharChem Assay Management', 'assay-management', 'Crude oil assay library management and characterization', 'fa-vials', 7),
(11, 'EnPharChem Petroleum Scheduler', 'petroleum-scheduler', 'Detailed operations scheduling for petroleum operations', 'fa-clock', 8),
(11, 'EnPharChem Refinery Multi-Blend Optimizer', 'refinery-multi-blend', 'Multi-period blending optimization for refinery products', 'fa-blender', 9),
(11, 'EnPharChem Petroleum Supply Chain Planner', 'petroleum-sc-planner', 'End-to-end petroleum supply chain planning from crude to product', 'fa-project-diagram', 10),
(11, 'EnPharChem Collaborative Demand Manager', 'collaborative-demand-petroleum', 'Collaborative demand forecasting for petroleum products', 'fa-users', 11);

-- Supply Chain Management
INSERT INTO modules (category_id, name, slug, description, icon, sort_order) VALUES
(12, 'enPharChemONE Supply Chain Management', 'enpharchem-scm', 'Integrated supply chain management platform for chemicals and specialty products', 'fa-boxes', 1),
(12, 'EnPharChem Scheduler Explorer', 'scheduler-explorer', 'Visual scheduling and Gantt-based production planning', 'fa-stream', 2),
(12, 'EnPharChem Collaborative Demand Manager', 'collaborative-demand-scm', 'Demand planning with collaboration and statistical forecasting', 'fa-users', 3),
(12, 'EnPharChem Supply Chain Planner', 'supply-chain-planner', 'Multi-echelon supply chain planning and inventory optimization', 'fa-route', 4),
(12, 'EnPharChem Plant Scheduler Family', 'plant-scheduler', 'Detailed plant scheduling with resource and constraint management', 'fa-calendar-check', 5),
(12, 'EnPharChem Supply Chain Management Insights', 'scm-insights', 'Analytics and dashboards for supply chain performance monitoring', 'fa-chart-pie', 6);

-- Asset Performance Management
INSERT INTO modules (category_id, name, slug, description, icon, sort_order) VALUES
(13, 'EnPharChem Mtell', 'enpharchem-mtell', 'Machine learning-based predictive maintenance and failure pattern recognition', 'fa-brain', 1),
(13, 'EnPharChem ProMV', 'enpharchem-promv', 'Multivariate statistical process monitoring and batch analytics', 'fa-chart-bar', 2),
(13, 'EnPharChem Process Pulse', 'process-pulse', 'Real-time process performance monitoring with automated alerting', 'fa-heartbeat', 3),
(13, 'EnPharChem Unscrambler', 'enpharchem-unscrambler', 'Multivariate data analysis, chemometrics, and design of experiments', 'fa-random', 4);

-- Industrial Data Fabric
INSERT INTO modules (category_id, name, slug, description, icon, sort_order) VALUES
(14, 'EnPharChem Inmation', 'enpharchem-inmation', 'Universal industrial data orchestration and contextualization platform', 'fa-project-diagram', 1);

-- Digital Grid Management
INSERT INTO modules (category_id, name, slug, description, icon, sort_order) VALUES
(15, 'EnPharChem Microgrid Management System', 'microgrid-mgmt', 'Intelligent microgrid control, optimization, and energy management', 'fa-solar-panel', 1),
(15, 'EnPharChem OSI Monarch SCADA', 'osi-monarch-scada', 'Enterprise SCADA platform for grid monitoring and control', 'fa-desktop', 2),
(15, 'EnPharChem OSI Generation Management System', 'osi-generation-mgmt', 'Generation dispatch, scheduling, and optimization', 'fa-bolt', 3),
(15, 'EnPharChem OSI Energy Management System', 'osi-energy-mgmt', 'Transmission network energy management and state estimation', 'fa-plug', 4),
(15, 'EnPharChem OSI Advanced Distribution Management System', 'osi-adms', 'Advanced distribution grid management with FLISR and VVO', 'fa-network-wired', 5),
(15, 'EnPharChem OSI Distributed Energy Resource Management System', 'osi-derms', 'DER aggregation, forecasting, and grid services optimization', 'fa-solar-panel', 6),
(15, 'EnPharChem OSI CHRONUS Historian', 'osi-chronus-historian', 'High-performance grid data historian for operational analytics', 'fa-history', 7),
(15, 'EnPharChem OSI Continua Pipeline Management', 'osi-pipeline-mgmt', 'Pipeline SCADA, leak detection, and batch tracking', 'fa-grip-lines-vertical', 8),
(15, 'EnPharChem Cimphony Network Model Management', 'cimphony-network-model', 'CIM-based network model management for utility operations', 'fa-sitemap', 9),
(15, 'EnPharChem Grid Apps', 'grid-apps', 'Grid analytics applications including DLR, hosting capacity, and load forecasting', 'fa-th', 10),
(15, 'EnPharChem Grid Reporter', 'grid-reporter', 'Regulatory and operational reporting for grid operations', 'fa-file-alt', 11),
(15, 'EnPharChem DER Connect', 'der-connect', 'DER interconnection management and communication gateway', 'fa-plug', 12),
(15, 'EnPharChem Network Maps', 'network-maps', 'Geospatial visualization of grid assets and network topology', 'fa-map-marked-alt', 13),
(15, 'EnPharChem Resilience Portal', 'resilience-portal', 'Storm preparedness, outage management, and grid resilience analytics', 'fa-shield-alt', 14);

-- Insert default admin user (password: password - should be changed after first login)
-- Hash below is bcrypt of "password". Use reset_admin.php to change to admin123.
INSERT INTO users (username, email, password_hash, first_name, last_name, role, license_type)
VALUES ('admin', 'admin@enpharchem.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System', 'Administrator', 'admin', 'enterprise');
