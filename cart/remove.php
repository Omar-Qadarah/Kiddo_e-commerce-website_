<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../classes/Cart.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/cart/index.php');
    exit;
}

checkCsrf();

$productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);

if ($productId) {
    $cart = new Cart($pdo);
    $cart->removeItem($productId);

    $_SESSION['cart_flash'] = [
        'type'    => 'success',
        'message' => 'Toy removed from your cart.',
    ];
}

header('Location: ' . BASE_URL . '/cart/index.php');
exit;