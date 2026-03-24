<?php
/**
 * EnPharChem Platform - Licensing Portal Migration Runner
 * Run this file to create the licensing tables.
 */

$host = 'localhost';
$dbname = 'enpharchem';
$user = 'root';
$pass = '';

$results = [];
$pdo = null;

// Try port 3311 first, fallback to 3306
foreach ([3311, 3306] as $port) {
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
        $lastError = $e->getMessage();
    }
}

if (!$pdo) {
    die("Database connection failed on both ports 3311 and 3306: " . $lastError);
}

$sqlFile = __DIR__ . '/licensing_tables.sql';
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
    }

    try {
        $pdo->exec($stmt);
        $results[] = ['table' => $tableName, 'status' => 'success', 'message' => 'Created / already exists'];
    } catch (PDOException $e) {
        $results[] = ['table' => $tableName, 'status' => 'error', 'message' => $e->getMessage()];
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <title>Licensing Migration - EnPharChem</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #141720; color: #dee2e6; padding: 2rem; }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mb-4">Licensing Portal Migration Results</h2>
        <p class="text-muted">Connected on port <?= $connectedPort ?></p>
        <table class="table table-dark table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Table</th>
                    <th>Status</th>
                    <th>Message</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $i => $r): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><code><?= htmlspecialchars($r['table']) ?></code></td>
                    <td>
                        <?php if ($r['status'] === 'success'): ?>
                            <span class="badge bg-success">Success</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Error</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($r['message']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="/enpharchem/control-panel/licensing" class="btn btn-primary mt-3">Go to Licensing Portal</a>
        <a href="/enpharchem/control-panel" class="btn btn-outline-secondary mt-3">Back to Control Panel</a>
        <a href="/enpharchem/dashboard" class="btn btn-outline-secondary mt-3">Back to Dashboard</a>
    </div>
</body>
</html>
