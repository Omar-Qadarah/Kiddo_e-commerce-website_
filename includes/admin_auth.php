<?php
// init.php must be loaded first. Check the current role in the database.
$adminUserId = $_SESSION['user_id'] ?? ($_SESSION['user']['id'] ?? null);
if (!is_scalar($adminUserId) || !ctype_digit((string) $adminUserId)) {
    http_response_code(403);
    exit('Please log in with an administrator account.');
}
$statement = $pdo->prepare('SELECT role, is_active FROM users WHERE id = ?');
$statement->execute([$adminUserId]);
$adminUser = $statement->fetch(PDO::FETCH_ASSOC);
if (!$adminUser || $adminUser['role'] !== 'admin' || !$adminUser['is_active']) {
    http_response_code(403);
    exit('Administrator access required.');
}
