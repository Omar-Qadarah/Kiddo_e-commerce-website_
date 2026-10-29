<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/User.php';

$database = new Database();
$db = $database->connect(); 

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        }

        body {
            background-color: #f7f6fd;
            padding: 40px;
            color: #333;
        }

        .top-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 25px;
        }

        .search-container {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-grow: 1; 
            max-width: 900px;
        }

        .search-input {
            flex-grow: 1;
            padding: 12px 20px;
            border-radius: 30px;
            border: 1px solid #e2ddf7;
            background: #fff;
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s;
        }

        .search-input:focus {
            border-color: #6c5ce7;
        }

        .btn-search {
            padding: 10px 24px;
            background-color: transparent;
            color: #6c5ce7;
            border: 1.5px solid #6c5ce7;
            border-radius: 15px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-search:hover {
            background-color: #6c5ce7;
            color: #fff;
        }

        .btn-add {
            padding: 12px 28px;
            background-color: #6c5ce7;
            color: #fff;
            border: none;
            border-radius: 30px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(108, 92, 231, 0.25);
            transition: background-color 0.2s;
            white-space: nowrap;
        }

        .btn-add:hover {
            background-color: #5844e3;
        }

        .card {
            background: #ffffff;
            border-radius: 24px;
            padding: 28px 32px;
            box-shadow: 0 4px 20px rgba(108, 92, 231, 0.05);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            font-size: 12px;
            font-weight: 700;
            color: #8c89a0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding-bottom: 20px;
            text-align: center;
        }

        td {
            padding: 12px 0;
            font-size: 14px;
            color: #4b4b60;
            border-top: 1px solid #f6f5fc;
            text-align: center;
        }

        .col-name {
            font-weight: 700;
            color: #2d3436;
        }

        .badge {
            display: inline-block;
            padding: 8px 4px;
            width: 80px;
            text-align: center;
            max-width: 100%;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
            text-transform: capitalize;
        }

        .badge-admin { background-color: #fff1e5; color: #d35400; }
        .badge-customer { background-color: #f1f0ff; color: #6c5ce7; }
        .badge-active { background-color: #eafaf1; color: #2ecc71; }

        .actions-cell {
            text-align: center;
            white-space: nowrap;
        }

        .btn-action-edit {
            border: 1px solid #6c5ce7;
            background: transparent;
            color: #6c5ce7;
            padding: 5px 16px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            margin-right: 8px;
        }

        .btn-action-edit:hover {
            background: #6c5ce7;
            color: #fff;
        }

        .btn-action-delete {
            border: 1px solid #f9d8d8;
            background: transparent;
            color: #e74c3c;
            padding: 5px 16px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-action-delete:hover {
            background: #e74c3c;
            color: #fff;
        }

        .table-footer {
            margin-top: 24px;
            font-size: 12px;
            color: #8c89a0;
        }

        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(2px);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 30px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .modal-header h3 {
            font-size: 18px;
            color: #2d3436;
        }

        .btn-close {
            background: none;
            border: none;
            font-size: 20px;
            color: #8c89a0;
            cursor: pointer;
        }

        .form-group {
            margin-bottom: 14px;
        }

        .form-group label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #636e72;
            margin-bottom: 6px;
            text-align: left;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #e2ddf7;
            border-radius: 10px;
            font-size: 14px;
            outline: none;
        }

        .modal-actions {
            margin-top: 20px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .btn-submit {
            background: #6c5ce7;
            color: #fff;
            padding: 10px 20px;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            font-weight: 600;
        }
    </style>
</head>
<body>

    <div class="top-bar">
        <form class="search-container" method="GET" action="users.php">
            <input type="text" name="search" class="search-input" placeholder="Search name, email or phone" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            <button type="submit" class="btn-search">Search</button>
        </form>
        <button class="btn-add" onclick="openModal('addModal')">+ Add user</button>
    </div>

    <div class="card">
        <table>
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
                        <td colspan="7" style="padding: 24px; color: #8c89a0;">No active users found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $user): ?>
                        <tr id="user-row-<?= $user['id'] ?>">
                            <td class="col-name"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= htmlspecialchars($user['phone'] ?? '-') ?></td>
                            <td>
                                <span class="badge <?= $user['role'] === 'admin' ? 'badge-admin' : 'badge-customer' ?>">
                                    <?= htmlspecialchars($user['role']) ?>
                                </span>
                            </td>
                            <td><?= (int)($user['orders_count'] ?? 0) ?></td>
                            <td><?= date('d M Y', strtotime($user['created_at'])) ?></td>
                            <td class="actions-cell">
                                <button class="btn-action-edit" onclick="openEditModal(
                                    <?= $user['id'] ?>, 
                                    '<?= htmlspecialchars(addslashes($user['first_name'])) ?>', 
                                    '<?= htmlspecialchars(addslashes($user['last_name'])) ?>', 
                                    '<?= htmlspecialchars(addslashes($user['phone'] ?? '')) ?>', 
                                    '<?= htmlspecialchars($user['role']) ?>'
                                )">Edit</button>
                                <button class="btn-action-delete" onclick="confirmSoftDelete(<?= $user['id'] ?>)">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="modal-overlay" id="addModal">
        <div class="modal-card">
            <div class="modal-header">
                <h3>Add New User</h3>
                <button class="btn-close" onclick="closeModal('addModal')">&times;</button>
            </div>
            <form action="users.php" method="POST">
                <input type="hidden" name="action" value="create_user">
                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" name="first_name" required>
                </div>
                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" name="last_name" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone">
                </div>
                <div class="form-group">
                    <label>Role</label>
                    <select name="role">
                        <option value="customer">Customer</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="modal-actions">
                    <button type="submit" class="btn-submit">Create User</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal-overlay" id="editModal">
        <div class="modal-card">
            <div class="modal-header">
                <h3>Edit User</h3>
                <button class="btn-close" onclick="closeModal('editModal')">&times;</button>
            </div>
            <form action="users.php" method="POST">
                <input type="hidden" name="action" value="edit_user">
                <input type="hidden" name="id" id="edit-id">
                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" name="first_name" id="edit-first-name" required>
                </div>
                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" name="last_name" id="edit-last-name" required>
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" id="edit-phone">
                </div>
                <div class="form-group">
                    <label>Role</label>
                    <select name="role" id="edit-role">
                        <option value="customer">Customer</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="modal-actions">
                    <button type="submit" class="btn-submit">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(id) {
            document.getElementById(id).style.display = 'flex';
        }

        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
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
            if (confirm("Are you sure you want to deactivate and remove this user?")) {
                const row = document.getElementById('user-row-' + userId);
                if (row) {
                    row.style.transition = 'opacity 0.3s ease';
                    row.style.opacity = '0';
                    setTimeout(() => {
                        window.location.href = 'users.php?action=delete&id=' + userId;
                    }, 300);
                }
            }
        }
    </script>
</body>
</html>