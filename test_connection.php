<?php

require_once __DIR__ . '/includes/init.php';

try {
    $stmt = $pdo->query('SELECT DATABASE()');
    $databaseName = $stmt->fetchColumn();

    echo "Database connected successfully!";
    echo "<br>Connected database: "
        . htmlspecialchars($databaseName, ENT_QUOTES, 'UTF-8');

} catch (PDOException $e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo "A database query failed.";
}
