<?php
try {
    $pdo = new PDO("mysql:host=127.0.0.1", "root", "");
    $pdo->exec("CREATE DATABASE IF NOT EXISTS gondowangi_wms_testing");
    echo "Database 'gondowangi_wms_testing' is ready.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
