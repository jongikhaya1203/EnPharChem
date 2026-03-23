<?php
/**
 * EnPharChem Platform - Installation Script
 * Run this once to set up the database
 */

$host = 'localhost';
$port = 3311;
$user = 'root';
$pass = '';
$dbname = 'enpharchem';

echo "<!DOCTYPE html><html><head><title>EnPharChem Installation</title>";
echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>";
echo "<style>body{background:#141619;color:#e9ecef;font-family:Inter,sans-serif;padding:40px;}</style></head><body>";
echo "<div class='container' style='max-width:800px;'>";
echo "<h1 style='color:#0dcaf0;'>EnPharChem Platform Installer</h1>";
echo "<p class='text-muted'>Energy, Pharmaceutical & Chemical Engineering Software</p><hr style='border-color:#2d3238;'>";

$errors = [];
$success = [];

try {
    // Connect without database
    $pdo = new PDO("mysql:host={$host};port={$port}", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    $success[] = "Connected to MySQL server.";

    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbname}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $success[] = "Database '{$dbname}' created or already exists.";

    // Select database
    $pdo->exec("USE `{$dbname}`");

    // Read and execute schema
    $schemaFile = __DIR__ . '/database/schema.sql';
    if (file_exists($schemaFile)) {
        $schema = file_get_contents($schemaFile);

        // Remove the CREATE DATABASE and USE statements (already handled)
        $schema = preg_replace('/CREATE DATABASE.*?;\s*/i', '', $schema);
        $schema = preg_replace('/USE\s+\w+;\s*/i', '', $schema);

        // Split into individual statements
        $statements = array_filter(array_map('trim', explode(';', $schema)));

        $tableCount = 0;
        $insertCount = 0;

        foreach ($statements as $stmt) {
            if (empty($stmt)) continue;
            try {
                $pdo->exec($stmt);
                if (stripos($stmt, 'CREATE TABLE') !== false) {
                    $tableCount++;
                } elseif (stripos($stmt, 'INSERT') !== false) {
                    $insertCount++;
                }
            } catch (PDOException $e) {
                // Ignore duplicate errors for re-runs
                if ($e->getCode() != '42S01' && strpos($e->getMessage(), 'Duplicate') === false) {
                    $errors[] = "SQL Error: " . $e->getMessage();
                }
            }
        }

        $success[] = "Created {$tableCount} tables.";
        $success[] = "Executed {$insertCount} insert statements (module categories, modules, admin user).";
    } else {
        $errors[] = "Schema file not found at: {$schemaFile}";
    }

    // Verify installation
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    $success[] = "Total tables in database: " . count($tables);

    $moduleCount = $pdo->query("SELECT COUNT(*) FROM modules")->fetchColumn();
    $success[] = "Total modules installed: {$moduleCount}";

    $categoryCount = $pdo->query("SELECT COUNT(*) FROM module_categories")->fetchColumn();
    $success[] = "Total module categories: {$categoryCount}";

} catch (PDOException $e) {
    $errors[] = "Connection failed: " . $e->getMessage();
}

// Display results
if (!empty($success)) {
    echo "<div style='background:#212529;border:1px solid #2d3238;border-radius:12px;padding:20px;margin:20px 0;'>";
    echo "<h4 style='color:#198754;'><i class=''>&#10004;</i> Installation Progress</h4><ul>";
    foreach ($success as $msg) {
        echo "<li style='color:#75b798;margin:8px 0;'>{$msg}</li>";
    }
    echo "</ul></div>";
}

if (!empty($errors)) {
    echo "<div style='background:#212529;border:1px solid #dc3545;border-radius:12px;padding:20px;margin:20px 0;'>";
    echo "<h4 style='color:#dc3545;'>Errors</h4><ul>";
    foreach ($errors as $msg) {
        echo "<li style='color:#ea868f;margin:8px 0;'>{$msg}</li>";
    }
    echo "</ul></div>";
}

if (empty($errors)) {
    echo "<div style='background:#0d6efd20;border:1px solid #0d6efd;border-radius:12px;padding:24px;margin:20px 0;text-align:center;'>";
    echo "<h3 style='color:#0dcaf0;'>Installation Complete!</h3>";
    echo "<p>Default admin credentials: <strong>admin</strong> / <strong>admin123</strong></p>";
    echo "<p style='color:#ffc107;'>Please change the admin password after first login.</p>";
    echo "<a href='/enpharchem/login' style='display:inline-block;padding:12px 30px;background:#0d6efd;color:white;border-radius:8px;text-decoration:none;font-weight:600;margin-top:10px;'>Launch EnPharChem</a>";
    echo "</div>";
}

echo "</div></body></html>";
