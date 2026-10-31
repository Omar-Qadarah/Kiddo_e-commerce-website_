<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../classes/Cart.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/cart/index.php');
    exit;
}

checkCsrf();
$cart = new Cart($pdo);
$cart->clear();
$_SESSION['cart_flash'] = [
    'type' => 'success',
    'message' => 'Your cart has been cleared.',
];
header('Location: ' . BASE_URL . '/cart/index.php');
exit;
