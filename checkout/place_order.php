<?php
 
 
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../classes/Cart.php';
require_once __DIR__ . '/../classes/Order.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/checkout/index.php');
    exit;
}

checkCsrf();

 
$phone   = preg_replace('/[\s-]/', '', trim((string) ($_POST['phone'] ?? '')));
$address = trim((string) ($_POST['address'] ?? ''));

$errors = [];

if (!preg_match('/^07[789][0-9]{7}$/', $phone)) {
    $errors[] = 'Please enter a valid Jordanian mobile number, like 0791234567.';
}

if (mb_strlen($address) < 5 || mb_strlen($address) > 255) {
    $errors[] = 'Please enter your full address (5 to 255 characters).';
}

$cart    = new Cart($pdo);
$details = $cart->getDetails();

if (empty($details['items'])) {
    header('Location: ' . BASE_URL . '/cart/index.php');
    exit;
}

if (!$errors) {
    try {
        $order   = new Order($pdo);
        $orderId = $order->create((int) $currentUserId, $details, $phone, $address);
    } catch (PDOException $e) {
        error_log($e->getMessage());
        $errors[] = 'We could not place your order. Please try again.';
    } catch (Exception $e) {
        $errors[] = $e->getMessage();
    }
}

 
if ($errors) {
    $_SESSION['checkout_errors'] = $errors;
    $_SESSION['checkout_old']    = ['phone' => $phone, 'address' => $address];
    header('Location: ' . BASE_URL . '/checkout/index.php');
    exit;
}

 
$cart->clear();
$_SESSION['order_flash'] = 'Thank you! Your order #' . $orderId . ' has been placed.';

header('Location: ' . BASE_URL . '/orders/details.php?id=' . $orderId);
exit;