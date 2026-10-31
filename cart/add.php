<?php
 
 
 
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../classes/Cart.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

checkCsrf();

$productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
$quantity  = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);

if (!$productId || $productId < 1) {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

$cart   = new Cart($pdo);
$result = $cart->add($productId, $quantity ?: 0);

if (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => $result['added'] > 0,
        'message' => $result['added'] > 0 ? 'Item added successfully' : $result['message'],
        'count' => $cart->getCount(),
    ]);
    exit;
}


 
$_SESSION['cart_flash'] = [
    'type'    => $result['added'] > 0 ? 'success' : 'error',
    'message' => $result['message'],
];

 
if (($_POST['return_to'] ?? '') === 'cart') {
    header('Location: ' . BASE_URL . '/cart/index.php');
    exit;
}

header('Location: ' . BASE_URL . '/products/details.php?id=' . $productId);
exit;
