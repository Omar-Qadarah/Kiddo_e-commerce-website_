<?php

require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../classes/AgeGroup.php';

$ageGroupModel = new AgeGroup($pdo);
$ageGroups = $ageGroupModel->getAll();

$pageTitle = 'Age Groups';
$activePage = 'age_groups';

require_once __DIR__ . '/../includes/admin/layout_start.php';
?>

<!-- Success message -->
<?php if (isset($_SESSION['age_group_success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_SESSION['age_group_success']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['age_group_success']); ?>
<?php endif; ?>

<!-- Error message -->
<?php if (isset($_SESSION['age_group_error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_SESSION['age_group_error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['age_group_error']); ?>
<?php endif; ?>


<!-- Page Header -->
<div class="admin-page-header">
    <div>
        <h2>Age Groups</h2>
        <p>Manage the age groups available in your store.</p>
    </div>

    <a href="<?= BASE_URL ?>/admin/add_age_group.php" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i>
        Add Age Group
    </a>
</div>


<!-- Age Groups Table -->
<div class="admin-card">
    <div class="table-responsive">
        <table class="table admin-table">

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Age Range</th>
                    <th>Description</th>
                    <th>Order</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php if (empty($ageGroups)): ?>

                    <tr>
                        <td colspan="8" class="text-center py-4">
                            No age groups found.
                        </td>
                    </tr>

                <?php else: ?>

                    <?php foreach ($ageGroups as $ageGroup): ?>

                        <tr>
                            <td><?= (int) $ageGroup['id'] ?></td>

                            <td>
                                <?php if (!empty($ageGroup['image'])): ?>
                                    <img src="<?= BASE_URL ?>/<?= htmlspecialchars($ageGroup['image']) ?>"
                                        alt="<?= htmlspecialchars($ageGroup['name']) ?>" width="55" height="55"
                                        style="object-fit: cover; border-radius: 8px;">
                                <?php else: ?>
                                    <span class="text-secondary">No image</span>
                                <?php endif; ?>
                            </td>

                            <td><?= htmlspecialchars($ageGroup['name']) ?></td>

                            <td><?= htmlspecialchars($ageGroup['slug']) ?></td>

                            <td>
                                <?= (int) $ageGroup['min_age'] ?>
                                –
                                <?= (int) $ageGroup['max_age'] ?>
                                years
                            </td>

                            <td><?= htmlspecialchars($ageGroup['description']) ?></td>

                            <td><?= (int) $ageGroup['sort_order'] ?></td>

                            <td>
                                <div class="d-flex gap-2">

                                    <!-- Open Edit Modal -->
                                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                        data-bs-target="#editAgeGroupModal<?= (int) $ageGroup['id'] ?>">
                                        <i class="bi bi-pencil"></i>
                                        
                                    </button>

                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#deleteAgeGroupModal<?= (int) $ageGroup['id'] ?>">
                                        <i class="bi bi-trash"></i>
                                        
                                    </button>

                                </div>
                            </td>
                        </tr>

                    <?php endforeach; ?>

                <?php endif; ?>
            </tbody>

        </table>
    </div>
</div>


<!-- ================================================== -->
<!-- EDIT MODALS: Keep these OUTSIDE the table -->
<!-- ================================================== -->

<?php foreach ($ageGroups as $ageGroup): ?>

    <div class="modal fade" id="editAgeGroupModal<?= (int) $ageGroup['id'] ?>" tabindex="-1"
        aria-labelledby="editAgeGroupLabel<?= (int) $ageGroup['id'] ?>" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">

                <form action="<?= BASE_URL ?>/admin/edit_age_group.php?id=<?= (int) $ageGroup['id'] ?>" method="POST"
                    enctype="multipart/form-data">

                    <div class="modal-header">
                        <h5 class="modal-title" id="editAgeGroupLabel<?= (int) $ageGroup['id'] ?>">
                            Edit Age Group: <?= htmlspecialchars($ageGroup['name']) ?>
                        </h5>

                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">

                        <div class="mb-3">
                            <label for="name<?= (int) $ageGroup['id'] ?>" class="form-label">
                                Name
                            </label>

                            <input type="text" id="name<?= (int) $ageGroup['id'] ?>" name="name" class="form-control"
                                maxlength="100" required value="<?= htmlspecialchars($ageGroup['name']) ?>">
                        </div>

                        <div class="mb-3">
                            <label for="slug<?= (int) $ageGroup['id'] ?>" class="form-label">
                                Slug
                            </label>

                            <input type="text" id="slug<?= (int) $ageGroup['id'] ?>" name="slug" class="form-control"
                                pattern="[a-z0-9]+(-[a-z0-9]+)*" required
                                value="<?= htmlspecialchars($ageGroup['slug']) ?>">

                            <div class="form-text">
                                Use lowercase letters, numbers, and hyphens.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description<?= (int) $ageGroup['id'] ?>" class="form-label">
                                Description
                            </label>

                            <textarea id="description<?= (int) $ageGroup['id'] ?>" name="description" class="form-control"
                                rows="3" maxlength="255"
                                required><?= htmlspecialchars($ageGroup['description']) ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="min_age<?= (int) $ageGroup['id'] ?>" class="form-label">
                                    Minimum Age
                                </label>

                                <input type="number" id="min_age<?= (int) $ageGroup['id'] ?>" name="min_age"
                                    class="form-control" min="0" required value="<?= (int) $ageGroup['min_age'] ?>">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="max_age<?= (int) $ageGroup['id'] ?>" class="form-label">
                                    Maximum Age
                                </label>

                                <input type="number" id="max_age<?= (int) $ageGroup['id'] ?>" name="max_age"
                                    class="form-control" min="0" required value="<?= (int) $ageGroup['max_age'] ?>">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="sort_order<?= (int) $ageGroup['id'] ?>" class="form-label">
                                    Sort Order
                                </label>

                                <input type="number" id="sort_order<?= (int) $ageGroup['id'] ?>" name="sort_order"
                                    class="form-control" min="0" required value="<?= (int) $ageGroup['sort_order'] ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Current Image</label>
                            <div>
                                <?php if (!empty($ageGroup['image'])): ?>

                                    <img src="<?= BASE_URL ?>/<?= htmlspecialchars($ageGroup['image']) ?>"
                                        alt="<?= htmlspecialchars($ageGroup['name']) ?>" width="100" height="100"
                                        style="object-fit: cover; border-radius: 8px;">

                                <?php else: ?>

                                    <p class="text-secondary">No image uploaded.</p>

                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="image<?= (int) $ageGroup['id'] ?>" class="form-label">
                                Replace Image (Optional)
                            </label>

                            <input type="file" id="image<?= (int) $ageGroup['id'] ?>" name="image" class="form-control"
                                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">

                            <div class="form-text">
                                Leave empty to keep the current image. Maximum size: 2 MB.
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Cancel
                        </button>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i>
                            Save Changes
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>


<?php endforeach; ?>

<?php foreach ($ageGroups as $ageGroup): ?>

    <div class="modal fade" id="deleteAgeGroupModal<?= (int) $ageGroup['id'] ?>" tabindex="-1"
        aria-labelledby="deleteAgeGroupLabel<?= (int) $ageGroup['id'] ?>" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="deleteAgeGroupLabel<?= (int) $ageGroup['id'] ?>">
                        Confirm Deletion
                    </h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    Are you sure you want to delete the age group
                    <strong><?= htmlspecialchars($ageGroup['name']) ?></strong>?

                    <p class="text-danger mt-2 mb-0">
                        This action cannot be undone.
                    </p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <form action="<?= BASE_URL ?>/admin/delete_age_group.php" method="POST">
                        <input type="hidden" name="id" value="<?= (int) $ageGroup['id'] ?>">

                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash"></i>
                            Yes, Delete
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>

<?php endforeach; ?>


<?php require_once __DIR__ . '/../includes/admin/layout_end.php'; ?>