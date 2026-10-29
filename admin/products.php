
<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../classes/Product.php';

$product = new Product($pdo);
$products = $product->getAll();

function h($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Products - Kiddo Admin</title>

<style>
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    padding: 35px;
    font-family: Arial, sans-serif;
    background: #f6f3ff;
    color: #201447;
}

.container {
    max-width: 1500px;
    margin: auto;
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    margin-bottom: 28px;
}

h1 {
    margin: 0;
    font-size: 36px;
    color: #201447;
}

.add-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 20px;
    border-radius: 25px;
    background: #7654ff;
    color: white;
    text-decoration: none;
    font-size: 15px;
    transition: 0.2s;
}

.add-btn:hover {
    background: #6040e8;
    transform: translateY(-2px);
}

.add-btn svg {
    width: 19px;
    height: 19px;
    stroke: currentColor;
    fill: none;
    stroke-width: 2;
}

.card {
    background: white;
    padding: 25px;
    border-radius: 20px;
    box-shadow: 0 5px 20px rgba(32, 20, 71, 0.05);
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    min-width: 850px;
}

thead th {
    padding: 18px 14px;
    text-align: left;
    font-size: 16px;
    font-weight: 700;
    border-bottom: 1px solid #eeeaf8;
    white-space: nowrap;
}

tbody td {
    padding: 18px 14px;
    border-bottom: 1px solid #eeeaf8;
    font-size: 16px;
    vertical-align: middle;
}

tbody tr:last-child td {
    border-bottom: none;
}

tbody tr:hover {
    background: #fcfbff;
}

.product-image {
    width: 58px;
    height: 58px;
    object-fit: cover;
    border-radius: 12px;
    background: #f6f3ff;
    display: block;
}

.no-image {
    width: 58px;
    height: 58px;
    display: flex;
    justify-content: center;
    align-items: center;
    background: #f6f3ff;
    color: #a397c9;
    border-radius: 12px;
    font-size: 12px;
}

.status {
    display: inline-block;
    padding: 7px 12px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    white-space: nowrap;
}

.status.active {
    color: #218653;
    background: #e8f8ef;
}

.status.hidden {
    color: #9a6b20;
    background: #fff4dc;
}

.actions {
    display: flex;
    align-items: center;
    gap: 10px;
}

.actions form {
    margin: 0;
}

.icon-btn {
    width: 42px;
    height: 42px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #e5ddff;
    border-radius: 12px;
    background: #f6f3ff;
    color: #7654ff;
    text-decoration: none;
    cursor: pointer;
    transition: 0.2s ease;
}

.icon-btn:hover {
    background: #7654ff;
    border-color: #7654ff;
    color: white;
    transform: translateY(-2px);
}

.icon-btn.delete {
    background: #fff1f2;
    color: #dc3545;
    border-color: #ffd5da;
}

.icon-btn.delete:hover {
    background: #dc3545;
    border-color: #dc3545;
    color: white;
}

.icon-btn svg {
    width: 19px;
    height: 19px;
    stroke: currentColor;
    fill: none;
    stroke-width: 1.8;
    stroke-linecap: round;
    stroke-linejoin: round;
}

.empty {
    text-align: center;
    padding: 35px;
    color: #756b99;
}

@media (max-width: 700px) {
    body {
        padding: 18px;
    }

    .header {
        align-items: flex-start;
        flex-direction: column;
    }

    h1 {
        font-size: 30px;
    }

    .card {
        padding: 12px;
    }
}
</style>
</head>

<body>
<div class="container">

    <div class="header">
        <h1>Products</h1>

        <a class="add-btn" href="add_product.php">
            <svg viewBox="0 0 24 24">
                <path d="M12 5v14M5 12h14"/>
            </svg>
            Add Toy
        </a>
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Ages</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
            <?php if (!empty($products)): ?>

                <?php foreach ($products as $item): ?>
                    <tr>
                        <td>
                            <?php if (!empty($item['image'])): ?>
                                <img
                                    class="product-image"
                                    src="../<?= h($item['image']) ?>"
                                    alt="<?= h($item['name']) ?>"
                                >
                            <?php else: ?>
                                <div class="no-image">No image</div>
                            <?php endif; ?>
                        </td>

                        <td><?= h($item['name']) ?></td>

                        <td><?= h($item['category_name'] ?? 'Uncategorized') ?></td>

                        <td>
                            <?= h($item['min_age'] ?? 0) ?>–<?= h($item['max_age'] ?? 0) ?>
                        </td>

                        <td>
                            <?= number_format((float)$item['price'], 2) ?> JOD
                        </td>

                        <td><?= h($item['stock']) ?></td>

                        <td>
                            <?php if (!empty($item['is_active'])): ?>
                                <span class="status active">Visible</span>
                            <?php else: ?>
                                <span class="status hidden">Hidden</span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <div class="actions">

                                <a
                                    class="icon-btn"
                                    href="edit_product.php?id=<?= (int)$item['id'] ?>"
                                    title="Edit product"
                                    aria-label="Edit product"
                                >
                                    <svg viewBox="0 0 24 24">
                                        <path d="M12 20h9"/>
                                        <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z"/>
                                    </svg>
                                </a>

                                <form
                                    method="post"
                                    action="delete_product.php"
                                    onsubmit="return confirm('Are you sure you want to delete this product?')"
                                >
                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= (int)$item['id'] ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="icon-btn delete"
                                        title="Delete product"
                                        aria-label="Delete product"
                                    >
                                        <svg viewBox="0 0 24 24">
                                            <path d="M3 6h18"/>
                                            <path d="M8 6V4h8v2"/>
                                            <path d="m19 6-1 14H6L5 6"/>
                                            <path d="M10 11v5M14 11v5"/>
                                        </svg>
                                    </button>
                                </form>

                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>

            <?php else: ?>
                <tr>
                    <td colspan="8" class="empty">
                        No products found. Click "Add Toy" to add your first product.
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>
</body>
</html>
