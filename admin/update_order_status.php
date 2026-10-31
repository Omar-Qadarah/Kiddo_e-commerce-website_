<?php

require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/admin_auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../classes/Order.php';

// This file only accepts form submissions
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/admin/orders.php');
    exit;
}

checkCsrf();   // make sure the form came from our own page

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$newStatus = $_POST['status'] ?? '';

if (!$id || $id < 1) {
    $_SESSION['order_error'] = 'Invalid order ID.';
    header('Location: ' . BASE_URL . '/admin/orders.php');
    exit;
}

$orderModel = new Order($pdo);

if (($_POST['action'] ?? '') === 'cancel') {
    try {
        if ($orderModel->cancelOrder($id)) {
            $_SESSION['order_success'] = 'Order cancelled and returned to stock.';
        } else {
            $_SESSION['order_error'] = 'Only placed or shipped orders can be cancelled.';
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
        $_SESSION['order_error'] = 'Unable to cancel the order. Please try again.';
    }
} elseif (!is_string($newStatus) || !in_array($newStatus, Order::STATUSES, true)) {
    $_SESSION['order_error'] = 'Invalid status.';
} else {
    try {
        if ($orderModel->updateStatus($id, $newStatus)) {
            $_SESSION['order_success'] = 'Order marked as ' . $newStatus . '.';
        } else {
            $_SESSION['order_error'] = 'This status change is not allowed.';
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
        $_SESSION['order_error'] = 'Unable to update the order. Please try again.';
    }
}

// Post / Redirect / Get: refreshing the page will not resubmit the form
if (($_POST['action'] ?? '') === 'cancel' && ($_POST['return_to'] ?? '') === 'orders') {
    header('Location: ' . BASE_URL . '/admin/orders.php');
} else {
    header('Location: ' . BASE_URL . '/admin/order_details.php?id=' . $id);
}
exit;
