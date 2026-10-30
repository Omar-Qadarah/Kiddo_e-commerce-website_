<?php
// Receives the "Add to Cart" form from products/details.php
// POST: product_id, quantity, csrf_token
// Then goes back to the same toy page with a message.
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

// One-time message shown at the top of the toy page.
$_SESSION['cart_flash'] = [
    'type'    => $result['added'] > 0 ? 'success' : 'error',
    'message' => $result['message'],
];

// Added from the cart suggestions: stay on the cart page
if (($_POST['return_to'] ?? '') === 'cart') {
    header('Location: ' . BASE_URL . '/cart/index.php');
    exit;
}

header('Location: ' . BASE_URL . '/products/details.php?id=' . $productId);
exit;
