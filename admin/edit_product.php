<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/csrf.php';
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
$existingImages = $imageModel->getByProductId($id);
$mainImagePath = trim((string) ($item['image'] ?? ''));
$galleryImages = [];
if ($mainImagePath !== '') {
    $galleryImages[] = ['path' => $mainImagePath, 'id' => null, 'is_main' => true];
}
foreach ($existingImages as $existingImage) {
    $imagePath = (string) $existingImage['image_path'];
    if ($imagePath !== $mainImagePath) {
        $galleryImages[] = ['path' => $imagePath, 'id' => (int) $existingImage['image_id'], 'is_main' => false];
    }
}
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCsrf();
    if (isset($_POST['delete_main_image']) || isset($_POST['delete_image_id'])) {
        try {
            $deleted = false;
            if (($_POST['delete_main_image'] ?? '') === '1') {
                $deleted = $imageModel->deleteProductImage($id, null, true);
            } else {
                $imageId = filter_var($_POST['delete_image_id'] ?? null, FILTER_VALIDATE_INT);
                $deleted = $imageId !== false && $imageId > 0 && $imageModel->deleteProductImage($id, (int) $imageId);
            }
            if ($deleted) {
                $success = 'Image deleted successfully.';
            } else {
                $error = 'That image could not be found for this product.';
            }
        } catch (Throwable $e) {
            error_log('Product image delete failed: ' . $e->getMessage());
            $error = 'Could not delete this image. Please try again.';
        }

        $item = $product->getById($id);
        $existingImages = $imageModel->getByProductId($id);
        $mainImagePath = trim((string) ($item['image'] ?? ''));
        $galleryImages = [];
        if ($mainImagePath !== '') {
            $galleryImages[] = ['path' => $mainImagePath, 'id' => null, 'is_main' => true];
        }
        foreach ($existingImages as $existingImage) {
            $imagePath = (string) $existingImage['image_path'];
            if ($imagePath !== $mainImagePath) {
                $galleryImages[] = ['path' => $imagePath, 'id' => (int) $existingImage['image_id'], 'is_main' => false];
            }
        }
    } else {
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
        $uploadedImages = [];
        try {
            $uploadedImages = $imageModel->uploadMany($_FILES['images'] ?? []);
            $hasExistingImage = trim((string) ($item['image'] ?? '')) !== '' || !empty($existingImages);
            $mainImage = trim((string) ($item['image'] ?? '')) !== ''
                ? $item['image']
                : (!$hasExistingImage ? ($uploadedImages[0] ?? '') : '');

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
                'image' => $mainImage,
                'is_active' => $active
            ];

            $imagesToSave = $hasExistingImage ? $uploadedImages : array_slice($uploadedImages, 1);
            $pdo->beginTransaction();
            if (!$product->update($id, $data)) {
                throw new RuntimeException('Could not update the product.');
            }
            foreach ($imagesToSave as $path) {
                if (!$imageModel->saveForProduct($id, $path, $name)) {
                    throw new RuntimeException('Could not save all product images.');
                }
            }
            $pdo->commit();
            header('Location: products.php');
            exit;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $imageModel->deleteUploaded($uploadedImages);
            $error = 'Could not save the product or its images. Please check the fields and try again.';
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
}
$pageTitle = 'Edit Product';
$activePage = 'products';
require_once __DIR__ . '/../includes/admin/layout_start.php';
?>

<div class="product-page">

    <div class="admin-page-header">
        <div>
<h1>Edit Product</h1>
<p>Update the product details, pricing and availability.</p>
</div>
        <a class="btn btn-light" href="products.php">← Back to Products</a>
    </div>

    <div class="admin-card">

        <?php if ($error !== ''): ?>
            <div class="alert alert-danger" role="alert"><?= h($error) ?></div>
        <?php endif; ?>
        <?php if ($success !== ''): ?>
            <div class="alert alert-success" role="status"><?= h($success) ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= h($_SESSION['csrf_token'] ?? '') ?>">

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label" for="name">Product Name</label>
                        <input class="form-control" type="text" id="name" name="name"
                               value="<?= h($item['name'] ?? '') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="description">Description</label>
                        <textarea class="form-control" id="description" name="description"
                                  required><?= h($item['description'] ?? '') ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="instructions">Instructions</label>
                        <textarea class="form-control" id="instructions" name="instructions"><?= h($item['instructions'] ?? '') ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="images">Add Product Images</label>
                        <input class="form-control" type="file" id="images" name="images[]"
                               accept="image/jpeg,image/png,image/webp" multiple>
                        <p class="form-text">Select one or more JPG, PNG, or WebP images. Maximum 3 MB each. Existing images are kept.</p>
                        <div class="d-flex justify-content-start gap-2 flex-wrap align-items-center product-upload-actions mt-3">
                            <a class="btn btn-light" href="products.php">Cancel</a>
                            <button class="btn btn-primary" type="submit">Update Product</button>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label" for="category_id">Category</label>
                        <select class="form-select" id="category_id" name="category_id" required>
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= (int)$category['id'] ?>"
                                    <?= (string)($item['category_id'] ?? '') === (string)$category['id'] ? 'selected' : '' ?>>
                                    <?= h($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="price">Price (JOD)</label>
                        <input class="form-control" type="number" id="price" name="price"
                               min="0.01" step="0.01"
                               value="<?= h($item['price'] ?? '') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="stock">Stock</label>
                        <input class="form-control" type="number" id="stock" name="stock"
                               min="0" step="1"
                               value="<?= h($item['stock'] ?? 0) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="gender">Suitable For</label>
                        <select class="form-select" id="gender" name="gender">
                            <option value="unisex" <?= ($item['gender'] ?? '') === 'unisex' ? 'selected' : '' ?>>Unisex</option>
                            <option value="boys" <?= ($item['gender'] ?? '') === 'boys' ? 'selected' : '' ?>>Boys</option>
                            <option value="girls" <?= ($item['gender'] ?? '') === 'girls' ? 'selected' : '' ?>>Girls</option>
                        </select>
                    </div>

                    <div class="row g-3">
                        <div class="mb-3">
                            <label class="form-label" for="min_age">Minimum Age</label>
                            <input class="form-control" type="number" id="min_age" name="min_age"
                                   min="0" max="18"
                                   value="<?= h($item['min_age'] ?? 0) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="max_age">Maximum Age</label>
                            <input class="form-control" type="number" id="max_age" name="max_age"
                                   min="0" max="18"
                                   value="<?= h($item['max_age'] ?? 15) ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            <input class="form-check-input me-2" type="checkbox" name="is_active" value="1"
                                <?= !empty($item['is_active']) ? 'checked' : '' ?>>
                            Active
                        </label>
                    </div>
                </div>
            </div>

            <?php if (!empty($galleryImages)): ?>
                <div class="d-flex flex-wrap gap-2 mt-3" aria-label="Current product images">
                    <?php foreach ($galleryImages as $galleryImage): ?>
                        <div class="product-image-thumb">
                            <img src="<?= h(BASE_URL . '/' . ltrim($galleryImage['path'], '/')) ?>" alt="Current product image" width="88" height="88">
                            <?php if ($galleryImage['is_main']): ?>
                                <button type="submit" name="delete_main_image" value="1" class="product-image-remove" formnovalidate aria-label="Delete main product image" title="Delete image">×</button>
                            <?php else: ?>
                                <button type="submit" name="delete_image_id" value="<?= (int) $galleryImage['id'] ?>" class="product-image-remove" formnovalidate aria-label="Delete product image" title="Delete image">×</button>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/admin/layout_end.php'; ?>
