<?php
// Only these existing admin destinations can be counted or displayed.
function getAdminQuickActions()
{
    return [
        'orders.php' => ['label' => 'Manage Orders', 'text' => 'Review orders and delivery status', 'icon' => 'bi-bag', 'url' => 'orders.php'],
        'products.php' => ['label' => 'Manage Products', 'text' => 'Update stock, prices and details', 'icon' => 'bi-box-seam', 'url' => 'products.php'],
        'add_product.php' => ['label' => 'Add Product', 'text' => 'Put a new toy on the shelf', 'icon' => 'bi-plus-lg', 'url' => 'add_product.php'],
        'discounts.php' => ['label' => 'Manage Discounts', 'text' => 'Review and manage store offers', 'icon' => 'bi-percent', 'url' => 'discounts.php'],
        'add_discount.php' => ['label' => 'Add Discount', 'text' => 'Create a product or category offer', 'icon' => 'bi-percent', 'url' => 'add_discount.php'],
        'reviews.php' => ['label' => 'Moderate Reviews', 'text' => 'Manage visible customer feedback', 'icon' => 'bi-star', 'url' => 'reviews.php'],
        'users.php' => ['label' => 'Manage Users', 'text' => 'Find and manage customer accounts', 'icon' => 'bi-people', 'url' => 'users.php'],
        'categories.php' => ['label' => 'Manage Categories', 'text' => 'Organize your toy categories', 'icon' => 'bi-tags', 'url' => 'categories.php'],
        'age_groups.php' => ['label' => 'Manage Age Groups', 'text' => 'Organize toys by age', 'icon' => 'bi-people', 'url' => 'age_groups.php']
    ];
}

function recordAdminPageVisit($pdo)
{
    $userId = $_SESSION['user_id'] ?? ($_SESSION['user']['id'] ?? null);
    $page = basename($_SERVER['SCRIPT_NAME'] ?? '');
    $actions = getAdminQuickActions();
    if (!$userId || ($_SERVER['REQUEST_METHOD'] ?? '') !== 'GET' || !isset($actions[$page])) {
        return;
    }
    // Check the database role, including pages that have no separate admin guard.
    $statement = $pdo->prepare("SELECT id FROM users WHERE id = ? AND role = 'admin' AND is_active = 1");
    $statement->execute([$userId]);
    if (!$statement->fetchColumn()) {
        return;
    }
    $statement = $pdo->prepare('INSERT INTO admin_action_usage (user_id, page, visits) VALUES (?, ?, 1) ON DUPLICATE KEY UPDATE visits = visits + 1');
    $statement->execute([$userId, $page]);
}

function getMostUsedAdminActions($pdo, $userId)
{
    $actions = getAdminQuickActions();
    $counts = array_fill_keys(array_keys($actions), 0);
    $statement = $pdo->prepare('SELECT page, visits FROM admin_action_usage WHERE user_id = ?');
    $statement->execute([$userId]);
    foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
        if (isset($counts[$row['page']])) {
            $counts[$row['page']] = (int) $row['visits'];
        }
    }
    // Equal counts retain the default order (PHP 8 stable sorting).
    arsort($counts);
    $topActions = [];
    foreach (array_slice(array_keys($counts), 0, 3) as $page) {
        $topActions[] = $actions[$page];
    }
    return $topActions;
}
