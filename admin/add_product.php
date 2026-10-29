<?php
require_once __DIR__ . "/../includes/init.php";
require_once __DIR__ . "/../classes/Product.php";
require_once __DIR__ . "/../classes/ProductImage.php";

$product = new Product($pdo);
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $price = $_POST["price"] ?? "";
    $stock = $_POST["stock"] ?? "";
    $category_id = $_POST["category_id"] ?? "";
    $is_active = isset($_POST["is_active"]) ? 1 : 0;

    if ($name === "" || $price === "" || $stock === "" || $category_id === "") {
        $message = "Please fill in all required fields.";
    } elseif (
        !is_numeric($price) || $price < 0 ||
        filter_var($stock, FILTER_VALIDATE_INT) === false || $stock < 0
    ) {
        $message = "Please enter a valid price and stock.";
    } else {
        $data = [
            "name" => $name,
            "description" => $description,
            "instructions" => "",
            "price" => $price,
            "stock" => $stock,
            "category_id" => $category_id,
            "gender" => "",
            "min_age" => 0,
            "max_age" => 0,
            "image" => "",
            "is_active" => $is_active
        ];

        try {
            $created = $product->create($data);

            if ($created) {
                header("Location: products.php");
                exit;
            }

            $message = "Failed to add product.";
        } catch (Throwable $e) {
            $message = "Could not save the product. Please check the database fields.";
        }
    }
}

$categories = $product->getCategories();
$pageTitle = 'Add Product';
$activePage = 'products';
require_once __DIR__ . '/../includes/admin/layout_start.php';
?>

<div class="product-page">

    <div class="admin-page-header">
        <div>
<h1>Add New Product</h1>
<p>Add a new toy to your store catalog.</p>
</div>
        <a class="btn btn-light" href="products.php">← Back to Products</a>
    </div>

    <div class="admin-card">

        <?php if ($message !== ""): ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($message, ENT_QUOTES, "UTF-8") ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">

            <div class="mb-3">
                <label class="form-label" for="name">Product Name *</label>
                <input class="form-control" type="text" id="name" name="name"
                       value="<?= htmlspecialchars($_POST["name"] ?? "", ENT_QUOTES, "UTF-8") ?>"
                       required>
            </div>

            <div class="mb-3">
                <label class="form-label" for="description">Description</label>
                <textarea class="form-control" id="description" name="description"><?= htmlspecialchars($_POST["description"] ?? "", ENT_QUOTES, "UTF-8") ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label" for="category_id">Category *</label>
                <select class="form-select" id="category_id" name="category_id" required>
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= (int)$category["id"] ?>"
                            <?= (string)($_POST["category_id"] ?? "") === (string)$category["id"] ? "selected" : "" ?>>
                            <?= htmlspecialchars($category["name"], ENT_QUOTES, "UTF-8") ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label" for="price">Price (JOD) *</label>
                <input class="form-control" type="number" id="price" name="price"
                       min="0" step="0.01"
                       value="<?= htmlspecialchars($_POST["price"] ?? "", ENT_QUOTES, "UTF-8") ?>"
                       required>
            </div>

            <div class="mb-3">
                <label class="form-label" for="stock">Stock Quantity *</label>
                <input class="form-control" type="number" id="stock" name="stock"
                       min="0" step="1"
                       value="<?= htmlspecialchars($_POST["stock"] ?? "", ENT_QUOTES, "UTF-8") ?>"
                       required>
            </div>

            <div class="mb-3">
                <label class="form-label" for="image">Product Image</label>
                <input class="form-control" type="file" id="image" name="image" accept="image/*">
            </div>

            <div class="form-check mb-4">
                <input class="form-check-input me-2" type="checkbox" id="is_active" name="is_active" value="1"
                    <?= isset($_POST["is_active"]) || $_SERVER["REQUEST_METHOD"] !== "POST" ? "checked" : "" ?>>
                <label class="form-label" for="is_active">Active — visible to customers</label>
            </div>

            <div class="d-flex gap-2 flex-wrap align-items-center">
                <button class="btn btn-primary" type="submit">Add Product</button>
                <a class="btn btn-light" href="products.php">Cancel</a>
            </div>

        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/admin/layout_end.php'; ?>
