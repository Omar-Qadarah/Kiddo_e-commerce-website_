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
$quantity  = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);

if ($productId && is_int($quantity)) {
    $cart   = new Cart($pdo);
    $result = $cart->updateItem($productId, $quantity);

    $_SESSION['cart_flash'] = [
        'type'    => $result['ok'] ? 'success' : 'error',
        'message' => $result['message'],
    ];
}

header('Location: ' . BASE_URL . '/cart/index.php');
exit;