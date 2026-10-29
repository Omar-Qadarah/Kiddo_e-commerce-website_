<?php

require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/admin_auth.php';
require_once __DIR__ . '/../classes/Dashboard.php';

$dashboard = new Dashboard($pdo);
$summary = $dashboard->getSummary();
$recentOrders = $dashboard->getRecentOrders();

// Seven calendar days in the application's Asia/Amman timezone, including today.
$today = new DateTime('today');
$firstDay = (clone $today)->modify('-6 days');
$tomorrow = (clone $today)->modify('+1 day');
$dailyRows = $dashboard->getDailyRevenue(
    $firstDay->format('Y-m-d'),
    $tomorrow->format('Y-m-d')
);

// Start with zero values so dates without orders still appear.
$days = [];
for ($i = 0; $i < 7; $i++) {
    $date = (clone $firstDay)->modify('+' . $i . ' days')->format('Y-m-d');
    $days[$date] = ['orders_count' => 0, 'income' => 0];
}
foreach ($dailyRows as $row) {
    $days[$row['order_date']] = [
        'orders_count' => (int) $row['orders_count'],
        'income' => (float) $row['income']
    ];
}
$weekIncome = 0;
$weekOrders = 0;
$highestIncome = 0;
foreach ($days as $day) {
    $weekIncome += $day['income'];
    $weekOrders += $day['orders_count'];
    $highestIncome = max($highestIncome, $day['income']);
}

$quickActions = [
    ['label' => 'Add Product', 'text' => 'Put a new toy on the shelf', 'icon' => 'bi-plus-lg', 'url' => 'add_product.php'],
    ['label' => 'Manage Orders', 'text' => 'Review orders and delivery status', 'icon' => 'bi-bag', 'url' => 'orders.php'],
    ['label' => 'Manage Products', 'text' => 'Update stock, prices and details', 'icon' => 'bi-box-seam', 'url' => 'products.php'],
    ['label' => 'Add Discount', 'text' => 'Create a product or category offer', 'icon' => 'bi-percent', 'url' => 'add_discount.php'],
    ['label' => 'Moderate Reviews', 'text' => 'Manage visible customer feedback', 'icon' => 'bi-star', 'url' => 'reviews.php'],
    ['label' => 'Manage Users', 'text' => 'Find and manage customer accounts', 'icon' => 'bi-people', 'url' => 'users.php']
];
$statusColors = ['placed' => 'bg-warning', 'shipped' => 'bg-info', 'delivered' => 'bg-success'];
$pageTitle = 'Dashboard';
$activePage = 'dashboard';
require_once __DIR__ . '/../includes/admin/layout_start.php';
?>

<div class="dashboard-page">
    <div class="admin-page-header">
        <div>
            <h1>Dashboard</h1>
            <p>A quick overview of your store, orders and daily tasks.</p>
        </div>
        <span class="dashboard-date"><i class="bi bi-calendar3" aria-hidden="true"></i> <?= $today->format('d M Y') ?></span>
    </div>

    <div class="dashboard-summary">
        <section class="admin-card dashboard-metric dashboard-metric-income">
            <div class="dashboard-metric-top"><span>Total Income</span><i class="bi bi-cash-stack" aria-hidden="true"></i></div>
            <strong><?= number_format((float) $summary['income'], 2) ?> <small>JOD</small></strong>
            <p>Income from delivered orders</p>
        </section>
        <section class="admin-card dashboard-metric">
            <div class="dashboard-metric-top"><span>Total Orders</span><i class="bi bi-bag dashboard-icon-pink" aria-hidden="true"></i></div>
            <strong><?= number_format((int) $summary['orders']) ?></strong>
            <p>Across all order statuses</p>
        </section>
        <section class="admin-card dashboard-metric">
            <div class="dashboard-metric-top"><span>Total Customers</span><i class="bi bi-people dashboard-icon-blue" aria-hidden="true"></i></div>
            <strong><?= number_format((int) $summary['customers']) ?></strong>
            <p>Registered customer accounts</p>
        </section>
        <section class="admin-card dashboard-metric">
            <div class="dashboard-metric-top"><span>Total Categories</span><i class="bi bi-tags dashboard-icon-yellow" aria-hidden="true"></i></div>
            <strong><?= number_format((int) $summary['categories']) ?></strong>
            <p>Ways to explore your toy catalog</p>
        </section>
    </div>

    <div class="dashboard-main-grid">
        <section class="admin-card dashboard-revenue" aria-labelledby="revenue-title">
            <div class="dashboard-panel-heading">
                <div><h2 id="revenue-title">Revenue Overview</h2><p>Last 7 days · delivered orders only</p></div>
                <span class="dashboard-period"><?= $firstDay->format('d M') ?> – <?= $today->format('d M') ?></span>
            </div>
            <div class="dashboard-revenue-total"><?= number_format($weekIncome, 2) ?> <small>JOD</small></div>
            <p class="dashboard-chart-caption">Order totals grouped by order date, including delivery fees.</p>

            <div class="dashboard-chart" aria-label="Daily income in JOD. Exact figures are also listed in the daily revenue table below.">
                <?php foreach ($days as $date => $day): ?>
                    <?php $height = $highestIncome > 0 ? ($day['income'] / $highestIncome) * 100 : 0; ?>
                    <div class="dashboard-chart-column">
                        <span class="dashboard-chart-value"><?= number_format($day['income'], 2) ?></span>
                        <div class="dashboard-bar-track">
                            <div class="dashboard-bar <?= $height == 0 ? 'dashboard-bar-zero' : '' ?>"
                                 style="height: <?= round($height, 2) ?>%"></div>
                        </div>
                        <span class="dashboard-chart-day"><?= date('D', strtotime($date)) ?></span>
                        <small><?= date('d M', strtotime($date)) ?></small>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php if ($weekOrders === 0): ?>
                <p class="dashboard-empty-note">No delivered orders in this period. Income will appear here when orders are marked delivered.</p>
            <?php endif; ?>
        </section>

        <section class="admin-card dashboard-quick" aria-labelledby="quick-title">
            <div class="dashboard-panel-heading">
                <div><h2 id="quick-title">Quick Actions</h2><p>Jump straight to your everyday store tasks.</p></div>
                <span class="dashboard-quick-symbol"><i class="bi bi-lightning-charge" aria-hidden="true"></i></span>
            </div>
            <div class="dashboard-actions">
                <?php foreach ($quickActions as $index => $action): ?>
                    <a class="dashboard-action <?= $index === 0 ? 'dashboard-action-primary' : '' ?>"
                       href="<?= htmlspecialchars($action['url'], ENT_QUOTES, 'UTF-8') ?>">
                        <i class="bi <?= htmlspecialchars($action['icon'], ENT_QUOTES, 'UTF-8') ?>" aria-hidden="true"></i>
                        <strong><?= htmlspecialchars($action['label'], ENT_QUOTES, 'UTF-8') ?></strong>
                        <span><?= htmlspecialchars($action['text'], ENT_QUOTES, 'UTF-8') ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>
    </div>

    <div class="dashboard-lower-grid">
        <section class="admin-card" aria-labelledby="recent-title">
            <div class="dashboard-panel-heading">
                <div><h2 id="recent-title">Recent Orders</h2><p>Your five latest customer orders.</p></div>
                <a class="btn btn-outline-primary btn-sm" href="orders.php">View All Orders <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
            </div>
            <div class="table-responsive">
                <table class="table admin-table align-middle">
                    <thead><tr><th>Order</th><th>Customer</th><th>Date</th><th>Total</th><th>Status</th><th>Details</th></tr></thead>
                    <tbody>
                        <?php foreach ($recentOrders as $order): ?>
                            <tr>
                                <td class="fw-bold">#<?= (int) $order['id'] ?></td>
                                <td><?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= date('d M Y', strtotime($order['created_at'])) ?></td>
                                <td class="text-nowrap"><?= number_format((float) $order['total'], 2) ?> JOD</td>
                                <td><span class="badge <?= $statusColors[$order['status']] ?? 'bg-secondary' ?>"><?= htmlspecialchars(ucfirst($order['status']), ENT_QUOTES, 'UTF-8') ?></span></td>
                                <td><a class="btn btn-sm btn-light" href="order_details.php?id=<?= (int) $order['id'] ?>">View<span class="visually-hidden"> order #<?= (int) $order['id'] ?></span></a></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($recentOrders)): ?>
                            <tr><td colspan="6" class="text-center py-5 text-muted">No orders yet. New orders will appear here.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="admin-card" aria-labelledby="daily-title">
            <div class="dashboard-panel-heading"><div><h2 id="daily-title">Daily Revenue</h2><p>By order date · delivered orders only</p></div></div>
            <div class="table-responsive">
                <table class="table admin-table">
                    <thead><tr><th>Date</th><th>Delivered Orders</th><th>Income (JOD)</th></tr></thead>
                    <tbody>
                        <?php foreach ($days as $date => $day): ?>
                            <tr><td><?= date('d M', strtotime($date)) ?></td><td><?= $day['orders_count'] ?></td><td><?= number_format($day['income'], 2) ?></td></tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot><tr><th>7-day total</th><td class="fw-bold"><?= $weekOrders ?></td><td class="fw-bold"><?= number_format($weekIncome, 2) ?></td></tr></tfoot>
                </table>
            </div>
        </section>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/admin/layout_end.php'; ?>
