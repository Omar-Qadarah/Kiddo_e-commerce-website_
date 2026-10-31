<?php
// One order of the logged-in customer: status, toys and totals.
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../classes/Order.php';

$orderModel = new Order($pdo);
$orderId    = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$order      = $orderId ? $orderModel->getCustomerOrder($orderId, (int) $currentUserId) : null;

// Not found, or not this customer's order
if (!$order) {
    header('Location: ' . BASE_URL . '/orders/index.php');
    exit;
}

$items = $orderModel->getOrderItems($orderId);

// Thank-you message after checkout, shown once
$flash = $_SESSION['order_flash'] ?? null;
unset($_SESSION['order_flash']);

// How far the order got: placed, shipped, delivered
$steps   = Order::TRACKING_STATUSES;
$reached = array_search($order['status'], $steps);

$pageTitle  = 'Order #' . $order['id'];
$activePage = 'orders';
$pageStyles = ['cart.css'];
require_once __DIR__ . '/../includes/shop_header.php';
?>

<section class="container cart-page">
    <a class="checkout-back" href="<?= BASE_URL ?>/orders/index.php">← My orders</a>
    <h1>Order #<?= (int) $order['id'] ?></h1>
    <p class="checkout-as">Placed on <?= date('j M Y, g:i A', strtotime($order['created_at'])) ?></p>

    <?php if ($flash): ?>
        <p class="cart-flash is-success"><?= htmlspecialchars($flash) ?></p>
    <?php endif; ?>

    <?php if ($order['status'] === 'cancelled'): ?>
        <p class="order-status is-cancelled" role="status">This order was cancelled.</p>
    <?php else: ?>
        <ol class="order-steps">
            <?php foreach ($steps as $i => $step): ?>
                <li class="<?= $i <= $reached ? 'is-done' : '' ?>"><?= ucfirst($step) ?></li>
            <?php endforeach; ?>
        </ol>
    <?php endif; ?>

    <div class="cart-layout">
        <div class="checkout-card">
            <h2>Toys</h2>
            <?php foreach ($items as $item): ?>
                <p class="order-line">
                    <span><?= (int) $item['quantity'] ?> × <?= htmlspecialchars($item['product_name']) ?></span>
                    <strong><?= shopMoney((float) $item['line_total']) ?></strong>
                </p>
            <?php endforeach; ?>

            <h2>Delivery</h2>
            <p><strong><?= htmlspecialchars($_SESSION['user_name'] ?? '') ?></strong></p>
            <p><?= htmlspecialchars($order['phone']) ?></p>
            <p><?= nl2br(htmlspecialchars($order['address_text'])) ?></p>
        </div>

        <aside class="cart-summary">
            <h2>Summary</h2>
            <p><span>Subtotal</span> <strong><?= shopMoney((float) $order['subtotal']) ?></strong></p>
            <p><span>Delivery</span> <strong><?= shopMoney((float) $order['delivery_fee']) ?></strong></p>
            <p class="checkout-total"><span>Total</span> <strong><?= shopMoney((float) $order['total']) ?></strong></p>
            <p class="cart-note">💵 Cash on delivery.</p>
        </aside>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
