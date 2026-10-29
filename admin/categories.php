
<?php

require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../classes/Category.php';

$categoryModel = new Category($pdo);
$categories = $categoryModel->getAll();

$pageTitle = 'Categories';
$activePage = 'categories';

require_once __DIR__ . '/../includes/admin/layout_start.php';
?>
<?php if (!empty($_SESSION['category_success'])): ?>

    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        <?= htmlspecialchars($_SESSION['category_success']) ?>

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"
            aria-label="Close"
        ></button>
    </div>

    <?php unset($_SESSION['category_success']); ?>

<?php endif; ?>


<?php if (!empty($_SESSION['category_error'])): ?>

    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <?= htmlspecialchars($_SESSION['category_error']) ?>

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"
            aria-label="Close"
        ></button>
    </div>

    <?php unset($_SESSION['category_error']); ?>

<?php endif; ?>
<div class="admin-page-header">
    <div>
        <h1>Categories</h1>
        <p>Manage your store categories.</p>
    </div>

    <a
        href="<?= BASE_URL ?>/admin/add_category.php"
        class="btn btn-primary"
    >
        <i class="bi bi-plus-lg"></i>
        Add Category
    </a>
</div>

<div class="admin-card">

    <div class="table-responsive">
        <table class="table admin-table admin-table-consistent admin-table-categories">

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Description</th>
                    <th>Icon</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>

                <?php if (empty($categories)): ?>

                    <tr>
                        <td colspan="6" class="text-center py-4">
                            No categories found.
                        </td>
                    </tr>

                <?php else: ?>

                    <?php foreach ($categories as $category): ?>

                        <tr>
                            <td>
                                <?= (int) $category['id'] ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($category['name']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($category['slug']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $category['description'] ?? ''
                                ) ?>
                            </td>

                            <td>
                                <?php if (!empty($category['icon'])): ?>
                                    <i class="<?= htmlspecialchars($category['icon']) ?>"></i>
                                    <?= htmlspecialchars($category['icon']) ?>
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>

                            <td>
                                <div class="admin-actions">

                                    <!-- Edit button opens the modal -->
                                    <button
                                        type="button"
                                        class="btn admin-action-btn admin-action-edit"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editCategoryModal<?= (int) $category['id'] ?>"
                                     title="Edit category" aria-label="Edit category"><i class="bi bi-pencil" aria-hidden="true"></i></button>

                                    <button
                                        type="button"
                                        class="btn admin-action-btn admin-action-delete"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteCategoryModal<?= (int) $category['id'] ?>"
                                     title="Delete category" aria-label="Delete category"><i class="bi bi-trash" aria-hidden="true"></i></button>

                                </div>
                            </td>
                        </tr>

                    <?php endforeach; ?>

                <?php endif; ?>

            </tbody>

        </table>
    </div>


    <?php foreach ($categories as $category): ?>

        <div
            class="modal fade"
            id="editCategoryModal<?= (int) $category['id'] ?>"
            tabindex="-1"
            aria-labelledby="editCategoryLabel<?= (int) $category['id'] ?>"
            aria-hidden="true"
        >
            <div class="modal-dialog modal-dialog-centered">

                <div class="modal-content border-0 rounded-4 shadow">

                    <form
                        action="<?= BASE_URL ?>/admin/update_category.php"
                        method="POST"
                    >

                        <div class="modal-header border-0 pb-0">

                            <div>
                                <h5
                                    class="modal-title fw-bold"
                                    id="editCategoryLabel<?= (int) $category['id'] ?>"
                                >
                                    Edit Category
                                </h5>

                                <p class="text-secondary small mb-0 mt-1">
                                    Update the category information.
                                </p>
                            </div>

                            <button
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                aria-label="Close"
                            ></button>

                        </div>

                        <div class="modal-body pt-4">

                            <input
                                type="hidden"
                                name="id"
                                value="<?= (int) $category['id'] ?>"
                            >

                            <div class="mb-3">
                                <label
                                    for="edit-name-<?= (int) $category['id'] ?>"
                                    class="form-label"
                                >
                                    Category Name
                                </label>

                                <input
                                    type="text"
                                    id="edit-name-<?= (int) $category['id'] ?>"
                                    name="name"
                                    class="form-control"
                                    maxlength="100"
                                    required
                                    value="<?= htmlspecialchars($category['name']) ?>"
                                >
                            </div>

                            <div class="mb-3">
                                <label
                                    for="edit-slug-<?= (int) $category['id'] ?>"
                                    class="form-label"
                                >
                                    Slug
                                </label>

                                <input
                                    type="text"
                                    id="edit-slug-<?= (int) $category['id'] ?>"
                                    name="slug"
                                    class="form-control"
                                    maxlength="100"
                                    required
                                    pattern="[a-z0-9]+(-[a-z0-9]+)*"
                                    value="<?= htmlspecialchars($category['slug']) ?>"
                                >

                                <div class="form-text">
                                    Use lowercase letters, numbers, and single hyphens.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label
                                    for="edit-description-<?= (int) $category['id'] ?>"
                                    class="form-label"
                                >
                                    Description
                                </label>

                                <textarea
                                    id="edit-description-<?= (int) $category['id'] ?>"
                                    name="description"
                                    class="form-control"
                                    maxlength="255"
                                    rows="3"
                                ><?= htmlspecialchars($category['description'] ?? '') ?></textarea>
                            </div>

                            <div>
                                <label
                                    for="edit-icon-<?= (int) $category['id'] ?>"
                                    class="form-label"
                                >
                                    Icon (optional)
                                </label>

                                <input
                                    type="text"
                                    id="edit-icon-<?= (int) $category['id'] ?>"
                                    name="icon"
                                    class="form-control"
                                    maxlength="100"
                                    value="<?= htmlspecialchars($category['icon'] ?? '') ?>"
                                    placeholder="bi bi-stars"
                                >
                            </div>

                        </div>

                        <div class="modal-footer border-0 pt-0">

                            <button
                                type="button"
                                class="btn btn-light"
                                data-bs-dismiss="modal"
                            >
                                Cancel
                            </button>

                            <button
                                type="submit"
                                class="btn btn-primary"
                            >
                                <i class="bi bi-check2"></i>
                                Save Changes
                            </button>

                        </div>

                    </form>

                </div>

            </div>
        </div>

    <?php endforeach; ?>
    <?php foreach ($categories as $category): ?>

        <div
            class="modal fade"
            id="deleteCategoryModal<?= (int) $category['id'] ?>"
            tabindex="-1"
            aria-labelledby="deleteCategoryLabel<?= (int) $category['id'] ?>"
            aria-hidden="true"
        >
            <div class="modal-dialog modal-dialog-centered modal-sm">

                <div class="modal-content border-0 rounded-4 shadow">

                    <form
                        action="<?= BASE_URL ?>/admin/delete_category.php"
                        method="POST"
                    >

                        <div class="modal-body text-center p-4">

                            <div class="mb-3 text-danger">
                                <i class="bi bi-exclamation-triangle display-4"></i>
                            </div>

                            <h5
                                class="fw-bold"
                                id="deleteCategoryLabel<?= (int) $category['id'] ?>"
                            >
                                Delete Category?
                            </h5>

                            <p class="text-secondary mb-4">
                                Are you sure you want to delete
                                <strong><?= htmlspecialchars($category['name']) ?></strong>?
                                This action cannot be undone.
                            </p>

                            <input
                                type="hidden"
                                name="id"
                                value="<?= (int) $category['id'] ?>"
                            >

                            <div class="d-flex justify-content-center gap-2">

                                <button
                                    type="button"
                                    class="btn btn-light"
                                    data-bs-dismiss="modal"
                                >
                                    Cancel
                                </button>

                                <button
                                    type="submit"
                                    class="btn btn-danger"
                                >
                                    <i class="bi bi-trash"></i>
                                    Yes, Delete
                                </button>

                            </div>

                        </div>

                    </form>

                </div>

            </div>
        </div>

    <?php endforeach; ?>        
</div>

<?php require_once __DIR__ . '/../includes/admin/layout_end.php'; ?>
