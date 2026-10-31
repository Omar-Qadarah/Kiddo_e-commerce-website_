<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/admin_auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../classes/AgeGroup.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/admin/age_groups.php');
    exit;
}
checkCsrf();
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
if (!$id) {
    $_SESSION['age_group_error'] = 'Invalid age group ID.';
} else {
    try {
         
        $deleted = (new AgeGroup($pdo))->delete($id);
        $_SESSION[$deleted ? 'age_group_success' : 'age_group_error'] = $deleted ? 'Age group deleted successfully.' : 'Age group no longer exists.';
    } catch (PDOException $error) {
        error_log($error->getMessage());
        $_SESSION['age_group_error'] = $error->getCode() === '23000'
            ? 'This age group is still referenced and cannot be deleted.'
            : 'Unable to delete this age group. Please try again.';
    }
}
header('Location: ' . BASE_URL . '/admin/age_groups.php');
exit;
