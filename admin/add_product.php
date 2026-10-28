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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Product - Kiddo</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 35px 20px;
            font-family: Arial, sans-serif;
            background: #f6f3ff;
            color: #201447;
        }

        .container {
            max-width: 760px;
            margin: 0 auto;
        }

        .top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            margin-bottom: 24px;
        }

        h1 {
            margin: 0;
            font-size: 28px;
        }

        .card {
            background: #fff;
            padding: 30px;
            border-radius: 18px;
            box-shadow: 0 5px 25px rgba(40, 20, 80, 0.07);
        }

        .field {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            font-size: 14px;
        }

        input[type="text"],
        input[type="number"],
        input[type="file"],
        textarea,
        select {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #ded8ef;
            border-radius: 10px;
            background: #fff;
            color: #201447;
            font: inherit;
        }

        textarea {
            min-height: 110px;
            resize: vertical;
        }

        input:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: #7654ff;
            box-shadow: 0 0 0 3px rgba(118, 84, 255, 0.12);
        }

        .active {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 24px;
        }

        .active label {
            margin: 0;
        }

        .active input {
            width: 17px;
            height: 17px;
            accent-color: #7654ff;
        }

        .actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .button {
            display: inline-block;
            padding: 12px 20px;
            border: none;
            border-radius: 10px;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
        }

        .primary {
            background: #7654ff;
            color: white;
        }

        .secondary {
            background: #eee9fb;
            color: #49347d;
        }

        .message {
            margin-bottom: 18px;
            padding: 12px;
            border-radius: 10px;
            background: #fff0f0;
            color: #a12828;
        }

        @media (max-width: 500px) {
            .card {
                padding: 20px;
            }

            .top {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
<div class="container">

    <div class="top">
        <h1>Add New Product</h1>
        <a class="button secondary" href="products.php">← Back to Products</a>
    </div>

    <div class="card">

        <?php if ($message !== ""): ?>
            <div class="message">
                <?= htmlspecialchars($message, ENT_QUOTES, "UTF-8") ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">

            <div class="field">
                <label for="name">Product Name *</label>
                <input type="text" id="name" name="name"
                       value="<?= htmlspecialchars($_POST["name"] ?? "", ENT_QUOTES, "UTF-8") ?>"
                       required>
            </div>

            <div class="field">
                <label for="description">Description</label>
                <textarea id="description" name="description"><?= htmlspecialchars($_POST["description"] ?? "", ENT_QUOTES, "UTF-8") ?></textarea>
            </div>

            <div class="field">
                <label for="category_id">Category *</label>
                <select id="category_id" name="category_id" required>
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= (int)$category["id"] ?>"
                            <?= (string)($_POST["category_id"] ?? "") === (string)$category["id"] ? "selected" : "" ?>>
                            <?= htmlspecialchars($category["name"], ENT_QUOTES, "UTF-8") ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="field">
                <label for="price">Price (JOD) *</label>
                <input type="number" id="price" name="price"
                       min="0" step="0.01"
                       value="<?= htmlspecialchars($_POST["price"] ?? "", ENT_QUOTES, "UTF-8") ?>"
                       required>
            </div>

            <div class="field">
                <label for="stock">Stock Quantity *</label>
                <input type="number" id="stock" name="stock"
                       min="0" step="1"
                       value="<?= htmlspecialchars($_POST["stock"] ?? "", ENT_QUOTES, "UTF-8") ?>"
                       required>
            </div>

            <div class="field">
                <label for="image">Product Image</label>
                <input type="file" id="image" name="image" accept="image/*">
            </div>

            <div class="active">
                <input type="checkbox" id="is_active" name="is_active" value="1"
                    <?= isset($_POST["is_active"]) || $_SERVER["REQUEST_METHOD"] !== "POST" ? "checked" : "" ?>>
                <label for="is_active">Active — visible to customers</label>
            </div>

            <div class="actions">
                <button class="button primary" type="submit">Add Product</button>
                <a class="button secondary" href="products.php">Cancel</a>
            </div>

        </form>
    </div>
</div>
</body>
</html>
```