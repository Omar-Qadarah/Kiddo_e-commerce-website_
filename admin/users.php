<?php

require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../classes/User.php';

$db = $pdo; 

$userObj = new User($db);

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $userObj->deleteUser((int)$_GET['id']);
    header("Location: users.php");
    exit;
}

$errorMsg = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_user') {
    $first_name = trim($_POST['first_name']);
    $last_name  = trim($_POST['last_name']);
    $email      = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password   = $_POST['password'];
    $phone      = !empty($_POST['phone']) ? trim($_POST['phone']) : null;
    $role       = $_POST['role'] ?? 'customer';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMsg = "Invalid email format";
    } else {
        $result = $userObj->createUser($first_name, $last_name, $email, $password, $phone, null);
        if ($result === true) {
            header("Location: users.php");
            exit;
        } else {
            $errorMsg = ($result === "Email already exists") ? $result : "Failed to create user";
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_user') {
    $id         = (int)$_POST['id'];
    $first_name = trim($_POST['first_name']);
    $last_name  = trim($_POST['last_name']);
    $phone      = !empty($_POST['phone']) ? trim($_POST['phone']) : null;
    
    $userObj->updateUser($id, $first_name, $last_name, $phone);
    header("Location: users.php");
    exit;
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$users = $userObj->getUsersForAdmin($search);
$pageTitle = 'Users';
$activePage = 'users';
require_once __DIR__ . '/../includes/admin/layout_start.php';
?>
<div class="admin-page-header">
<div>
<h1>Users</h1>
<p>Manage customer accounts and view their order activity.</p>
</div>
</div>


<style>
/* Keep the existing Users modal functions, scoped to this page only. */
.users-page .modal-overlay {
    display: none; position: fixed; inset: 0; z-index: 1100;
    padding: 20px; background: rgba(30, 24, 64, .4);
    justify-content: center; align-items: center;
}
.users-page .modal-card {
    width: 100%; max-width: 480px; max-height: 90vh; overflow-y: auto;
    background: var(--admin-surface); padding: 24px;
    border-radius: var(--admin-radius); box-shadow: var(--admin-shadow);
}
.users-page .modal-header { margin-bottom: 20px; display : flex; justify-content: space-between; }
</style>
<div class="users-page">
<?php if ($errorMsg !== null): ?>
<div class="alert alert-danger" role="alert"><?= htmlspecialchars($errorMsg, ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>


    <div class="admin-page-header admin-table-toolbar">
        <form class="d-flex gap-2 flex-wrap flex-grow-1" method="GET" action="users.php">
            <input type="text" name="search" class="form-control w-auto flex-grow-1" placeholder="Search name, email or phone" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            <button type="submit" class="btn btn-outline-primary">Search</button>
        </form>
        <button class="btn btn-primary" onclick="openModal('addModal')">+ Add user</button>
    </div>

    <div class="admin-card">
        <div class="table-responsive"><table class="table admin-table align-middle admin-table-consistent admin-table-users">
            <thead>
                <tr>
                    <th>NAME</th>
                    <th>EMAIL</th>
                    <th>PHONE</th>
                    <th>ROLE</th>
                    <th>ORDERS</th>
                    <th>JOINED</th>
                    <th>ACTIONS</th>
                </tr>
            </thead>
            <tbody id="usersTableBody">
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="7" style="padding: 24px; color: #8c89a0;text-align:center;">No active users found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $user): ?>
                        <tr id="user-row-<?= $user['id'] ?>">
                            <td class="fw-bold"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= htmlspecialchars($user['phone'] ?? '-') ?></td>
                            <td>
                                <span class="badge <?= $user['role'] === 'admin' ? 'bg-warning' : 'bg-secondary' ?>">
                                    <?= htmlspecialchars($user['role']) ?>
                                </span>
                            </td>
                            <td><?= (int)($user['orders_count'] ?? 0) ?></td>
                            <td><?= date('d M Y', strtotime($user['created_at'])) ?></td>
                            <td class="text-nowrap"><div class="admin-actions">
                                <button class="btn admin-action-btn admin-action-edit" onclick="openEditModal(
                                    <?= $user['id'] ?>, 
                                    '<?= htmlspecialchars(addslashes($user['first_name'])) ?>', 
                                    '<?= htmlspecialchars(addslashes($user['last_name'])) ?>', 
                                    '<?= htmlspecialchars(addslashes($user['phone'] ?? '')) ?>', 
                                    '<?= htmlspecialchars($user['role']) ?>'
                                )" title="Edit user" aria-label="Edit user"><i class="bi bi-pencil" aria-hidden="true"></i></button>
                                <button class="btn admin-action-btn admin-action-delete" onclick="confirmSoftDelete(<?= $user['id'] ?>)" title="Delete user" aria-label="Delete user"><i class="bi bi-trash" aria-hidden="true"></i></button></div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table></div>
    </div>

    <div class="modal-overlay" id="addModal">
        <div class="modal-card">
            <div class="modal-header">
                <h3>Add New User</h3>
                <button class="btn btn-light" onclick="closeModal('addModal')">&times;</button>
            </div>
            <form action="users.php" method="POST">
                <input type="hidden" name="action" value="create_user">
                <div class="mb-3">
                    <label class="form-label">First Name</label>
                    <input class="form-control" type="text" name="first_name" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Last Name</label>
                    <input class="form-control" type="text" name="last_name" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input class="form-control" type="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input class="form-control" type="password" name="password" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Phone</label>
                    <input class="form-control" type="text" name="phone">
                </div>
                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select class="form-select" name="role">
                        <option value="customer">Customer</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">Create User</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal-overlay" id="editModal">
        <div class="modal-card">
            <div class="modal-header">
                <h3>Edit User</h3>
                <button class="btn btn-light" onclick="closeModal('editModal')">&times;</button>
            </div>
            <form action="users.php" method="POST">
                <input type="hidden" name="action" value="edit_user">
                <input type="hidden" name="id" id="edit-id">
                <div class="mb-3">
                    <label class="form-label">First Name</label>
                    <input class="form-control" type="text" name="first_name" id="edit-first-name" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Last Name</label>
                    <input class="form-control" type="text" name="last_name" id="edit-last-name" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Phone</label>
                    <input class="form-control" type="text" name="phone" id="edit-phone">
                </div>
                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select class="form-select" name="role" id="edit-role">
                        <option value="customer">Customer</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(id) {
            if (id === 'addModal') {
                document.getElementById(id).style.display = 'flex';
            } else {
                bootstrap.Modal.getOrCreateInstance(document.getElementById(id)).show();
            }
        }

        function closeModal(id) {
            if (id === 'addModal') {
                document.getElementById(id).style.display = 'none';
            } else {
                bootstrap.Modal.getOrCreateInstance(document.getElementById(id)).hide();
            }
        }

        function openEditModal(id, firstName, lastName, phone, role) {
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-first-name').value = firstName;
            document.getElementById('edit-last-name').value = lastName;
            document.getElementById('edit-phone').value = phone;
            document.getElementById('edit-role').value = role;
            openModal('editModal');
        }

        function confirmSoftDelete(userId) {
            window.confirmAdminDelete("Are you sure you want to deactivate and remove this user?", () => {
                const row = document.getElementById('user-row-' + userId);
                if (row) {
                    row.style.transition = 'opacity 0.3s ease';
                    row.style.opacity = '0';
                    setTimeout(() => {
                        window.location.href = 'users.php?action=delete&id=' + userId;
                    }, 300);
                }
            }, 'Delete user?');
        }
    </script>

</div>
<?php require_once __DIR__ . "/../includes/admin/layout_end.php"; ?>
