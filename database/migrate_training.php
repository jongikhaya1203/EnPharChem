<?php
/**
 * EnPharChem Platform - Training & Assessment Migration Runner
 * Run this file to create the training, assessment, and certificate tables.
 * Tries port 3311 first, falls back to 3306.
 */

$host = 'localhost';
$dbname = 'enpharchem';
$user = 'root';
$pass = '';
$ports = [3311, 3306];
$results = [];
$pdo = null;
$connectedPort = null;

// Try connecting on each port
foreach ($ports as $port) {
    try {
        $pdo = new PDO(
            "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4",
            $user,
            $pass,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        $connectedPort = $port;
        break;
    } catch (PDOException $e) {
        $results[] = ['table' => 'connection', 'status' => 'warning', 'message' => "Port {$port}: {$e->getMessage()}"];
    }
}

if (!$pdo) {
    die("Database connection failed on all ports (3311, 3306).");
}

$results[] = ['table' => 'connection', 'status' => 'success', 'message' => "Connected on port {$connectedPort}"];

// First, ensure the category enum supports all 15 categories
try {
    $pdo->exec("ALTER TABLE training_courses MODIFY COLUMN category ENUM('process_simulation','exchanger_design','apc','mes','supply_chain','apm','grid_mgmt','general','process_sim_energy','process_sim_chemicals','concurrent_feed','subsurface_science','energy_optimization','operations_support','dynamic_optimization','petroleum_supply_chain','industrial_data_fabric','digital_grid_mgmt') DEFAULT 'general'");
    $results[] = ['table' => 'training_courses', 'status' => 'success', 'message' => 'Category enum updated with all 15 categories'];
} catch (PDOException $e) {
    // Table might not exist yet, that's fine - it will be created below
    $results[] = ['table' => 'training_courses', 'status' => 'warning', 'message' => 'Enum update skipped: ' . $e->getMessage()];
}

// Read and execute the SQL file
$sqlFile = __DIR__ . '/training_assessment_tables.sql';
if (!file_exists($sqlFile)) {
    die("SQL file not found: {$sqlFile}");
}

$sql = file_get_contents($sqlFile);

// Split by semicolon, filter out empty/comment-only statements
$statements = array_filter(
    array_map('trim', explode(';', $sql)),
    function ($s) {
        $clean = preg_replace('/--.*$/m', '', $s);
        return trim($clean) !== '';
    }
);

foreach ($statements as $stmt) {
    $tableName = 'unknown';
    if (preg_match('/CREATE\s+TABLE\s+IF\s+NOT\s+EXISTS\s+(\w+)/i', $stmt, $m)) {
        $tableName = $m[1];
    } elseif (preg_match('/ALTER\s+TABLE\s+(\w+)/i', $stmt, $m)) {
        $tableName = $m[1];
    }

    try {
        $pdo->exec($stmt);
        $results[] = ['table' => $tableName, 'status' => 'success', 'message' => 'Created / already exists'];
    } catch (PDOException $e) {
        $results[] = ['table' => $tableName, 'status' => 'error', 'message' => $e->getMessage()];
    }
}

// Output as plain text
header('Content-Type: text/plain; charset=utf-8');
echo "=== EnPharChem Training & Assessment Migration ===\n";
echo "Connected on port: {$connectedPort}\n\n";

foreach ($results as $i => $r) {
    $status = strtoupper($r['status']);
    $icon = $r['status'] === 'success' ? '[OK]' : ($r['status'] === 'error' ? '[ERR]' : '[WARN]');
    echo sprintf("%s %-30s %s\n", $icon, $r['table'], $r['message']);
}

echo "\nMigration complete. " . count($results) . " operations processed.\n";
echo "Go to: /enpharchem/training\n";
echo "Control Panel: /enpharchem/control-panel/training\n";
