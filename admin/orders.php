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

require_once __DIR__ . '/../classes/AdminTable.php';
require_once __DIR__ . '/../includes/admin/table_helpers.php';
$adminTable = new AdminTable($pdo);
$pagination = $adminTable->getPage('orders', $_GET);
$orders = $pagination['rows'];

// Status cards use aggregate counts, without loading every order.
$statusCounts = [];
foreach ($pdo->query('SELECT status, COUNT(*) AS total FROM orders GROUP BY status')->fetchAll(PDO::FETCH_ASSOC) as $countRow) {
    $statusCounts[$countRow['status']] = (int) $countRow['total'];
}
$totalOrders = array_sum($statusCounts);

$statusIcons = [
    'placed'    => 'bi-hourglass-split',
    'shipped'   => 'bi-truck',
    'delivered' => 'bi-check2-circle',
];

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
<div class="admin-page-header">
<div>
<h1>Orders</h1>
<p>Track customer orders and move them to the next status.</p>
</div>
</div>


<?php if (!empty($_SESSION['order_error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <?= htmlspecialchars($_SESSION['order_error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['order_error']); ?>
<?php endif; ?>



<!-- Summary cards double as the status filter -->
<div class="status-cards">
    <a href="<?= adminTableEscape(adminTableUrl($pagination, ['status' => '', 'page' => 1])) ?>"
       class="status-card <?= $status === '' ? 'active' : '' ?>">
        <i class="bi bi-bag"></i>
        <span>All</span>
        <strong><?= $totalOrders ?></strong>
    </a>
    <?php foreach (Order::STATUSES as $option): ?>
        <a href="<?= adminTableEscape(adminTableUrl($pagination, ['status' => $option, 'page' => 1])) ?>"
           class="status-card status-<?= $option ?> <?= $status === $option ? 'active' : '' ?>">
            <i class="bi <?= $statusIcons[$option] ?>"></i>
            <span><?= htmlspecialchars(ucfirst($option)) ?></span>
            <strong><?= $statusCounts[$option] ?? 0 ?></strong>
        </a>
    <?php endforeach; ?>
</div>

<?php require __DIR__ . '/../includes/admin/table_toolbar.php'; ?>
<div class="admin-card">
    <div class="table-responsive">
        <table class="table admin-table admin-table-consistent admin-table-orders">
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
                            <td>
                                <?= htmlspecialchars(date('d M Y', strtotime($order['created_at']))) ?>
                                <div class="small text-secondary">
                                      <?= htmlspecialchars(date('g:i A', strtotime($order['created_at']))) ?>
                                </div>
                            </td>
                            <td>
                                <a href="<?= BASE_URL ?>/admin/order_details.php?id=<?= (int) $order['id'] ?>"
                                   class="btn admin-action-btn admin-action-toggle" title="View order" aria-label="View order"><i class="bi bi-eye" aria-hidden="true"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div><?php require __DIR__ . '/../includes/admin/table_pagination.php'; ?>
</div>

<?php require_once __DIR__ . '/../includes/admin/layout_end.php'; ?>