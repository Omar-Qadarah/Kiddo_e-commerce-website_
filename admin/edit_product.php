
<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../classes/Product.php';
require_once __DIR__ . '/../classes/ProductImage.php';

$product = new Product($pdo);
$imageModel = new ProductImage($pdo);

function h($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: products.php');
    exit;
}

$item = $product->getById($id);

if (!$item) {
    header('Location: products.php');
    exit;
}

$categories = $product->getCategories();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $instructions = trim($_POST['instructions'] ?? '');
    $categoryId = (int)($_POST['category_id'] ?? 0);
    $price = $_POST['price'] ?? '';
    $stock = $_POST['stock'] ?? '';
    $gender = $_POST['gender'] ?? 'unisex';
    $minAge = $_POST['min_age'] ?? '';
    $maxAge = $_POST['max_age'] ?? '';
    $active = isset($_POST['is_active']) ? 1 : 0;

    if (
        $name === '' ||
        $description === '' ||
        $categoryId <= 0 ||
        !is_numeric($price) ||
        $price <= 0 ||
        !is_numeric($stock) ||
        $stock < 0 ||
        filter_var($minAge, FILTER_VALIDATE_INT) === false ||
        filter_var($maxAge, FILTER_VALIDATE_INT) === false ||
        $minAge < 0 ||
        $maxAge < $minAge ||
        !in_array($gender, ['boys', 'girls', 'unisex'], true)
    ) {
        $error = 'Please check all fields and enter valid values.';
    } else {
        try {
            $newImage = $imageModel->upload($_FILES['image'] ?? []);

            $data = [
                'category_id' => $categoryId,
                'name' => $name,
                'description' => $description,
                'instructions' => $instructions,
                'price' => $price,
                'stock' => $stock,
                'gender' => $gender,
                'min_age' => (int)$minAge,
                'max_age' => (int)$maxAge,
                'image' => $newImage ?? ($item['image'] ?? ''),
                'is_active' => $active
            ];

            if ($product->update($id, $data)) {
                if ($newImage !== null) {
                    $imageModel->saveForProduct($id, $newImage, $name);
                }

                header('Location: products.php');
                exit;
            }

            $error = 'Could not update the product.';
        } catch (Throwable $e) {
            $error = $e->getMessage();
        }
    }

    $item = array_merge($item, [
        'name' => $name,
        'description' => $description,
        'instructions' => $instructions,
        'category_id' => $categoryId,
        'price' => $price,
        'stock' => $stock,
        'gender' => $gender,
        'min_age' => $minAge,
        'max_age' => $maxAge,
        'is_active' => $active
    ]);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Edit Product - Kiddo</title>

<style>
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    padding: 30px;
    font-family: Arial, sans-serif;
    background: #f6f3ff;
    color: #201447;
}

.container {
    max-width: 1000px;
    margin: 0 auto;
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
    margin-bottom: 25px;
}

h1 {
    margin: 0;
    font-size: 32px;
}

.card {
    background: white;
    padding: 28px;
    border-radius: 18px;
    box-shadow: 0 5px 18px rgba(32, 20, 71, 0.06);
}

.grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.field {
    margin-bottom: 20px;
}

label {
    display: block;
    font-weight: bold;
    margin-bottom: 8px;
}

input:not([type="checkbox"]):not([type="file"]),
select,
textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid #ded6f5;
    border-radius: 10px;
    font-size: 15px;
    font-family: Arial, sans-serif;
    color: #201447;
    background: white;
}

textarea {
    min-height: 90px;
    resize: vertical;
}

input:focus,
select:focus,
textarea:focus {
    outline: 2px solid #c9baff;
    border-color: #7654ff;
}

input[type="checkbox"] {
    accent-color: #7654ff;
}

input[type="file"] {
    max-width: 100%;
}

button,
.btn {
    display: inline-block;
    padding: 11px 18px;
    border: 1px solid #7654ff;
    border-radius: 22px;
    background: white;
    color: #7654ff;
    font-size: 15px;
    text-decoration: none;
    cursor: pointer;
}

button.primary {
    background: #7654ff;
    color: white;
}

.actions {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    margin-top: 10px;
}

.error {
    background: #ffe9ed;
    color: #9e263b;
    padding: 12px;
    border-radius: 10px;
    margin-bottom: 20px;
}

.small {
    color: #756b99;
    font-size: 13px;
}

@media (max-width: 650px) {
    body {
        padding: 15px;
    }

    .grid {
        grid-template-columns: 1fr;
        gap: 0;
    }

    .header {
        align-items: flex-start;
        flex-direction: column;
    }

    .card {
        padding: 18px;
    }
}
</style>
</head>

<body>
<div class="container">

    <div class="header">
        <h1>Edit Product</h1>
        <a class="btn" href="products.php">← Back to Products</a>
    </div>

    <div class="card">

        <?php if ($error !== ''): ?>
            <div class="error"><?= h($error) ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">

            <div class="grid">
                <div>
                    <div class="field">
                        <label for="name">Product Name</label>
                        <input type="text" id="name" name="name"
                               value="<?= h($item['name'] ?? '') ?>" required>
                    </div>

                    <div class="field">
                        <label for="description">Description</label>
                        <textarea id="description" name="description"
                                  required><?= h($item['description'] ?? '') ?></textarea>
                    </div>

                    <div class="field">
                        <label for="instructions">Instructions</label>
                        <textarea id="instructions" name="instructions"><?= h($item['instructions'] ?? '') ?></textarea>
                    </div>

                    <div class="field">
                        <label for="image">Replace Product Image</label>
                        <input type="file" id="image" name="image"
                               accept="image/jpeg,image/png,image/webp">
                        <p class="small">JPG, PNG or WEBP. Maximum 3 MB.</p>

                        <?php if (!empty($item['image'])): ?>
                            <p class="small">Current image: <?= h($item['image']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div>
                    <div class="field">
                        <label for="category_id">Category</label>
                        <select id="category_id" name="category_id" required>
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= (int)$category['id'] ?>"
                                    <?= (string)($item['category_id'] ?? '') === (string)$category['id'] ? 'selected' : '' ?>>
                                    <?= h($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="field">
                        <label for="price">Price (JOD)</label>
                        <input type="number" id="price" name="price"
                               min="0.01" step="0.01"
                               value="<?= h($item['price'] ?? '') ?>" required>
                    </div>

                    <div class="field">
                        <label for="stock">Stock</label>
                        <input type="number" id="stock" name="stock"
                               min="0" step="1"
                               value="<?= h($item['stock'] ?? 0) ?>" required>
                    </div>

                    <div class="field">
                        <label for="gender">Suitable For</label>
                        <select id="gender" name="gender">
                            <option value="unisex" <?= ($item['gender'] ?? '') === 'unisex' ? 'selected' : '' ?>>Unisex</option>
                            <option value="boys" <?= ($item['gender'] ?? '') === 'boys' ? 'selected' : '' ?>>Boys</option>
                            <option value="girls" <?= ($item['gender'] ?? '') === 'girls' ? 'selected' : '' ?>>Girls</option>
                        </select>
                    </div>

                    <div class="grid">
                        <div class="field">
                            <label for="min_age">Minimum Age</label>
                            <input type="number" id="min_age" name="min_age"
                                   min="0" max="18"
                                   value="<?= h($item['min_age'] ?? 0) ?>" required>
                        </div>

                        <div class="field">
                            <label for="max_age">Maximum Age</label>
                            <input type="number" id="max_age" name="max_age"
                                   min="0" max="18"
                                   value="<?= h($item['max_age'] ?? 15) ?>" required>
                        </div>
                    </div>

                    <div class="field">
                        <label>
                            <input type="checkbox" name="is_active" value="1"
                                <?= !empty($item['is_active']) ? 'checked' : '' ?>>
                            Active
                        </label>
                    </div>
                </div>
            </div>

            <div class="actions">
                <a class="btn" href="products.php">Cancel</a>
                <button class="primary" type="submit">Update Product</button>
            </div>

        </form>
    </div>
</div>
</body>
</html>
