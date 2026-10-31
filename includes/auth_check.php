<?php
 
$currentUserId = $_SESSION['user_id'] ?? null;

if (!is_scalar($currentUserId) || !ctype_digit((string) $currentUserId)) {
     
    $_SESSION['after_login'] = $_SERVER['REQUEST_URI'];
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}
$statement = $pdo->prepare('SELECT id FROM users WHERE id = ? AND is_active = 1');
$statement->execute([$currentUserId]);
if (!$statement->fetch(PDO::FETCH_ASSOC)) {
    unset($_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['user_role']);
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}
