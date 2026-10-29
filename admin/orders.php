<?php

require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/admin_auth.php';
require_once __DIR__ . '/../classes/Order.php';

$orderModel = new Order($pdo);

// Filter from the URL: orders.php?status=placed (unknown value = show all)
$status = $_GET['status'] ?? '';
if (!is_string($status) || !in_array($status, Order::STATUSES, true)) {
    $status = '';
}

$orders = $orderModel->getAllOrders($status);

// Bootstrap badge colour for each status
$badgeClasses = [
    'placed'    => 'bg-warning text-dark',
    'shipped'   => 'bg-info text-dark',
    'delivered' => 'bg-success',
];

$pageTitle = 'Orders';
$activePage = 'orders';

require_once __DIR__ . '/../includes/admin/layout_start.php';
?>

<?php if (!empty($_SESSION['order_error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <?= htmlspecialchars($_SESSION['order_error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['order_error']); ?>
<?php endif; ?>

<div class="admin-page-header">
    <div>
        <h2>Orders</h2>
        <p>Track customer orders and update their status.</p>
    </div>
</div>

<div class="d-flex flex-wrap gap-2 mb-3">
    <a href="<?= BASE_URL ?>/admin/orders.php"
       class="btn btn-sm <?= $status === '' ? 'btn-primary' : 'btn-outline-primary' ?>">
        All
    </a>
    <?php foreach (Order::STATUSES as $option): ?>
        <a href="<?= BASE_URL ?>/admin/orders.php?status=<?= urlencode($option) ?>"
           class="btn btn-sm <?= $status === $option ? 'btn-primary' : 'btn-outline-primary' ?>">
            <?= htmlspecialchars(ucfirst($option)) ?>
        </a>
    <?php endforeach; ?>
</div>

<div class="admin-card">
    <div class="table-responsive">
        <table class="table admin-table">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4">No orders found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>#<?= (int) $order['id'] ?></td>
                            <td><?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?></td>
                            <td><?= number_format((float) $order['total'], 2) ?> JD</td>
                            <td>
                                <span class="badge <?= $badgeClasses[$order['status']] ?? 'bg-secondary' ?>">
                                    <?= htmlspecialchars(ucfirst($order['status'])) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars(date('Y-m-d H:i', strtotime($order['created_at']))) ?></td>
                            <td>
                                <a href="<?= BASE_URL ?>/admin/order_details.php?id=<?= (int) $order['id'] ?>"
                                   class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye"></i>
                                    View
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/admin/layout_end.php'; ?>