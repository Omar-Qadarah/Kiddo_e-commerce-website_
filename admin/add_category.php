
<?php

require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../classes/Category.php';

$categoryModel = new Category($pdo);

$errors = [];
$name = '';
$slug = '';
$description = '';
$icon = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $slug = strtolower(trim($_POST['slug'] ?? ''));
    $description = trim($_POST['description'] ?? '');
    $icon = trim($_POST['icon'] ?? '');

    if ($name === '') {
        $errors[] = 'Category name is required.';
    }

    if ($slug === '') {
        $errors[] = 'Category slug is required.';
    } elseif (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug)) {
        $errors[] = 'Slug must contain lowercase letters, numbers, and hyphens only.';
    }

    if ($categoryModel->nameExists($name)) {
        $errors[] = 'This category name already exists.';
    }

    if ($categoryModel->slugExists($slug)) {
        $errors[] = 'This category slug already exists.';
    }

    if (empty($errors)) {
        try {
            $categoryModel->create([
                'name' => $name,
                'slug' => $slug,
                'description' => $description !== '' ? $description : null,
                'icon' => $icon !== '' ? $icon : null
            ]);

            header('Location: ' . BASE_URL . '/admin/categories.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = 'Unable to save the category. Please check the details and try again.';
        }
    }
}

$pageTitle = 'Add Category';
$activePage = 'categories';

require_once __DIR__ . '/../includes/admin/layout_start.php';
?>

<div class="admin-page-header">
    <div>
        <h1>Add Category</h1>
        <p>Create a new product category.</p>
    </div>

    <a href="<?= BASE_URL ?>/admin/categories.php"
       class="btn btn-outline-secondary">
        Back to Categories
    </a>
</div>

<div class="admin-card">
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" class="admin-form">
        <div class="mb-3">
            <label for="name" class="form-label">Category Name *</label>
            <input
                type="text"
                id="name"
                name="name"
                class="form-control"
                maxlength="100"
                required
                value="<?= htmlspecialchars($name) ?>"
            >
        </div>

        <div class="mb-3">
            <label for="slug" class="form-label">Slug *</label>
            <input
                type="text"
                id="slug"
                name="slug"
                class="form-control"
                maxlength="100"
                required
                placeholder="wooden-toys"
                value="<?= htmlspecialchars($slug) ?>"
            >
            <div class="form-text">
                Example: Wooden Toys → wooden-toys
            </div>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea
                id="description"
                name="description"
                class="form-control"
                maxlength="255"
            ><?= htmlspecialchars($description) ?></textarea>
        </div>

        <div class="mb-4">
            <label for="icon" class="form-label">Icon (optional)</label>
            <input
                type="text"
                id="icon"
                name="icon"
                class="form-control"
                maxlength="100"
                placeholder="Example: bi bi-stars"
                value="<?= htmlspecialchars($icon) ?>"
            >
        </div>

        <button type="submit" class="btn btn-primary">
            Create Category
        </button>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/admin/layout_end.php'; ?>
