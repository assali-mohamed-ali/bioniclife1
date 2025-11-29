<?php
// scripts/seed_products.php - A one-off script to seed the products table.
// WARNING: Run once. It reads sql/seed_products.sql and executes it via PDO.

require_once __DIR__ . '/../config/db.php';

$sqlFile = __DIR__ . '/../sql/seed_products.sql';
if (!file_exists($sqlFile)) {
    echo "Missing SQL file: $sqlFile\n";
    exit(1);
}

$sql = file_get_contents($sqlFile);
try {
    // In case the file contains multiple statements, we'll split on semicolons.
    $stmts = array_filter(array_map('trim', explode(';', $sql)));
    $pdo->beginTransaction();
    foreach ($stmts as $s) {
        if (strlen($s) === 0) continue;
        $pdo->exec($s);
    }
    $pdo->commit();
    echo "Products seeded successfully.\n";
} catch (Exception $e) {
    $pdo->rollBack();
    echo "Error seeding products: " . $e->getMessage() . "\n";
}

?>