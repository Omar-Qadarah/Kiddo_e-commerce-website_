<?php
// My orders: every order of the logged-in customer, newest first.
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../classes/Order.php';

$orderModel = new Order($pdo);
$orders     = $orderModel->getCustomerOrders((int) $currentUserId);

$pageTitle  = 'My orders';
$activePage = 'orders';
$pageStyles = ['cart.css'];
require_once __DIR__ . '/../includes/shop_header.php';
?>

<section class="container cart-page">
    <h1>My orders</h1>

    <?php if (empty($orders)): ?>
        <div class="cart-empty">
            <p>You have no orders yet.</p>
            <a class="btn btn-primary" href="<?= BASE_URL ?>/products/index.php">Browse toys</a>
        </div>
    <?php else: ?>
        <ul class="cart-list">
            <?php foreach ($orders as $order): ?>
                <li>
                    <a class="order-card" href="<?= BASE_URL ?>/orders/details.php?id=<?= (int) $order['id'] ?>">
                        <strong>Order #<?= (int) $order['id'] ?></strong>
                        <span><?= date('j M Y', strtotime($order['created_at'])) ?></span>
                        <span><?= (int) $order['toys_count'] ?> <?= (int) $order['toys_count'] === 1 ? 'toy' : 'toys' ?></span>
                        <span class="order-status is-<?= htmlspecialchars($order['status']) ?>"><?= ucfirst($order['status']) ?></span>
                        <strong><?= shopMoney((float) $order['total']) ?></strong>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>