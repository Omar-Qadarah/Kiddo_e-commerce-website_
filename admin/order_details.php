<?php

require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/admin_auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../classes/Order.php';

$orderModel = new Order($pdo);

 
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    $_SESSION['order_error'] = 'Invalid order ID.';
    header('Location: ' . BASE_URL . '/admin/orders.php');
    exit;
}

 
$order = $orderModel->getOrderById($id);

if ($order === null) {
    $_SESSION['order_error'] = 'Order not found.';
    header('Location: ' . BASE_URL . '/admin/orders.php');
    exit;
}

require_once __DIR__ . '/../classes/AdminTable.php';
require_once __DIR__ . '/../includes/admin/table_helpers.php';
$adminTable = new AdminTable($pdo);
$pagination = $adminTable->getPage('order_items', $_GET);
$items = $pagination['rows'];
$nextStatus = $orderModel->getNextStatus($order['status']);

 
$itemsTotalStatement = $pdo->prepare('SELECT COALESCE(SUM(line_total), 0) FROM order_items WHERE order_id = ?');
$itemsTotalStatement->execute([$id]);
$itemsSum = (float) $itemsTotalStatement->fetchColumn();
$totalsMatch = abs($itemsSum - (float) $order['subtotal']) < 0.01;

$badgeClasses = [
    'placed'    => 'bg-warning text-dark',
    'shipped'   => 'bg-info text-dark',
    'delivered' => 'bg-success',
    'cancelled' => 'bg-danger',
];

$pageTitle = 'Order #' . (int) $order['id'];
$activePage = 'orders';

require_once __DIR__ . '/../includes/admin/layout_start.php';
?>

<?php if (!empty($_SESSION['order_success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        <?= htmlspecialchars($_SESSION['order_success']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['order_success']); ?>
<?php endif; ?>

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
        <h1>Order #<?= (int) $order['id'] ?></h1>
        <p>Placed on <?= htmlspecialchars(date('d M Y, g:i A', strtotime($order['created_at']))) ?></p>
    </div>
    <a href="<?= BASE_URL ?>/admin/orders.php" class="btn btn-light">
        <i class="bi bi-arrow-left"></i>
        Back to Orders
    </a>
</div>

<div class="row g-4">

     
    <div class="col-lg-8">
        <div class="admin-card">
            <h5 class="fw-bold mb-3">Items</h5>
            <?php require __DIR__ . '/../includes/admin/table_toolbar.php'; ?>

            <div class="table-responsive">
                <table class="table admin-table admin-table-consistent admin-table-order_details">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Unit Price</th>
                            <th>Qty</th>
                            <th class="text-end">Line Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($items)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-4">This order has no items.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['product_name']) ?></td>
                                    <td><?= number_format((float) $item['unit_price'], 2) ?> JD</td>
                                    <td><?= (int) $item['quantity'] ?></td>
                                    <td class="text-end"><?= number_format((float) $item['line_total'], 2) ?> JD</td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div><?php require __DIR__ . '/../includes/admin/table_pagination.php'; ?>

            <?php if (!$totalsMatch): ?>
                <div class="alert alert-warning mt-3">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    Items add up to <?= number_format($itemsSum, 2) ?> JD,
                    but the saved subtotal is <?= number_format((float) $order['subtotal'], 2) ?> JD.
                </div>
            <?php endif; ?>

            <dl class="row mb-0 mt-3">
                <dt class="col-6 fw-normal">Subtotal</dt>
                <dd class="col-6 text-end"><?= number_format((float) $order['subtotal'], 2) ?> JD</dd>

                <?php if ((float) $order['birthday_discount'] > 0): ?>
                    <dt class="col-6 fw-normal">Birthday discount</dt>
                    <dd class="col-6 text-end text-success">− <?= number_format((float) $order['birthday_discount'], 2) ?> JD</dd>
                <?php endif; ?>

                <dt class="col-6 fw-normal">Delivery fee</dt>
                <dd class="col-6 text-end"><?= number_format((float) $order['delivery_fee'], 2) ?> JD</dd>

                <dt class="col-6 fs-5">Total</dt>
                <dd class="col-6 text-end fs-5 fw-bold"><?= number_format((float) $order['total'], 2) ?> JD</dd>
            </dl>
        </div>
    </div>

     
    <div class="col-lg-4">

        <div class="admin-card mb-4">
            <h5 class="fw-bold mb-3">Status</h5>

            <p>
                <span class="badge <?= $badgeClasses[$order['status']] ?? 'bg-secondary' ?>">
                    <?= htmlspecialchars(ucfirst($order['status'])) ?>
                </span>
            </p>

            <?php if ($nextStatus !== null): ?>
                <form action="<?= BASE_URL ?>/admin/update_order_status.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                    <input type="hidden" name="id" value="<?= (int) $order['id'] ?>">
                    <input type="hidden" name="status" value="<?= htmlspecialchars($nextStatus) ?>">

                    <button type="submit" class="btn btn-primary w-100"
                            onclick="return confirm('Mark this order as <?= htmlspecialchars($nextStatus) ?>?');">
                        <i class="bi bi-arrow-right-circle"></i>
                        Mark as <?= htmlspecialchars(ucfirst($nextStatus)) ?>
                    </button>
                </form>
            <?php else: ?>
                <?php if ($order['status'] === 'cancelled'): ?>
                    <p class="text-danger mb-0"><i class="bi bi-x-circle-fill"></i> This order was cancelled.</p>
                <?php else: ?>
                    <p class="text-success mb-0"><i class="bi bi-check-circle-fill"></i> This order is complete.</p>
                <?php endif; ?>
            <?php endif; ?>
            <?php if (in_array($order['status'], ['placed', 'shipped'], true)): ?>
                <?php $cancelModalId = 'cancelOrderModal' . (int) $order['id']; ?>
                <button type="button" class="btn btn-outline-danger w-100 mt-3" data-bs-toggle="modal" data-bs-target="#<?= $cancelModalId ?>">
                    <i class="bi bi-x-circle"></i> Cancel order
                </button>
                <div class="modal fade" id="<?= $cancelModalId ?>" tabindex="-1" aria-labelledby="<?= $cancelModalId ?>Label" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-sm">
                        <div class="modal-content border-0 rounded-4 shadow">
                            <form action="<?= BASE_URL ?>/admin/update_order_status.php" method="POST">
                                <div class="modal-body text-center p-4">
                                    <div class="mb-3 text-danger"><i class="bi bi-exclamation-triangle display-4"></i></div>
                                    <h5 class="fw-bold" id="<?= $cancelModalId ?>Label">Cancel Order?</h5>
                                    <p class="text-secondary mb-4">Cancel this order and return its items to stock?</p>
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                    <input type="hidden" name="id" value="<?= (int) $order['id'] ?>">
                                    <input type="hidden" name="action" value="cancel">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Keep Order</button>
                                        <button type="submit" class="btn btn-danger"><i class="bi bi-x-circle"></i> Yes, Cancel</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="admin-card">
            <h5 class="fw-bold mb-3">Customer &amp; Delivery</h5>

            <p class="mb-1 fw-semibold"><?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?></p>
            <p class="mb-3 text-secondary small"><?= htmlspecialchars($order['email']) ?></p>

            <p class="mb-2"><i class="bi bi-telephone me-2"></i><?= htmlspecialchars($order['phone']) ?></p>
            <p class="mb-2"><i class="bi bi-geo-alt me-2"></i><?= htmlspecialchars($order['address_text']) ?></p>

            <?php if ((float) $order['latitude'] != 0 && (float) $order['longitude'] != 0): ?>
                <a href="https://www.google.com/maps?q=<?= urlencode($order['latitude'] . ',' . $order['longitude']) ?>"
                   target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary mb-2">
                    <i class="bi bi-map"></i>
                    Open in Google Maps
                </a>
            <?php endif; ?>

            <?php if (!empty($order['child_birth_date'])): ?>
                <p class="mb-0 small text-secondary">
                    <i class="bi bi-gift me-2"></i>
                    Child's birthday: <?= htmlspecialchars($order['child_birth_date']) ?>
                </p>
            <?php endif; ?>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/../includes/admin/layout_end.php'; ?>
