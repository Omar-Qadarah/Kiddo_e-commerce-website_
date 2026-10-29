<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/csrf.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCsrf();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'],
            $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
    header('Location: login.php');
    exit;
}
$pageTitle = 'Logout';
$activePage = 'logout';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="auth-section container"><div class="auth-card">
<h1>Log out?</h1><p>Confirm to end your session.</p>
<form method="post">
<input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
<button class="btn btn-primary" type="submit">Confirm logout</button>
</form><p><a href="../index.php">Back to store</a></p>
</div></section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
