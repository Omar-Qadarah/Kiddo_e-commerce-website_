<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../config/GoogleAuth.php';
$userModel = new User($pdo);
$errors = [];

$email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCsrf();
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    if (!is_string($email) || !is_string($password)) {
        $errors[] = 'Invalid form values.';
        $email = '';
        $password = '';
    }
    $email = strtolower(trim($email));
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 120 || $password === '') {
        $errors[] = 'Please enter your email and password.';
    }
    if (empty($errors)) {
        try {
            $user = $userModel->getUserByEmail($email);
            if (!$user || !$user['is_active'] || empty($user['password_hash']) ||
                !password_verify($password, $user['password_hash'])) {
                $errors[] = 'Invalid email or password, or the account is inactive.';
            } else {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                $_SESSION['user_role'] = $user['role'];
                unset($_SESSION['csrf_token']);
                if ($user['role'] === 'admin') {
                    header('Location: ../admin/index.php');
                } elseif (!empty($_SESSION['after_login'])) {
                    // Back to the page the customer wanted (e.g. checkout)
                    $target = $_SESSION['after_login'];
                    unset($_SESSION['after_login']);
                    header('Location: ' . $target);
                } else {
                    header('Location: ../index.php');
                }
                exit;
            }
        } catch (PDOException $error) {
            error_log($error->getMessage());
            $errors[] = 'Unable to log in. Please try again.';
        }
    }
}
$pageTitle = 'Login';
$activePage = 'login';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="auth-section container"><div class="auth-card">
<h1>Welcome back</h1><p>Log in to your Kiddo's account.</p>
<?php if (!empty($_SESSION['register_success'])): ?>
<p class="auth-success"><?= htmlspecialchars($_SESSION['register_success'], ENT_QUOTES, 'UTF-8') ?></p>
<?php unset($_SESSION['register_success']); endif; ?>
<?php if (!empty($errors)): ?>
<ul class="auth-errors" role="alert">
<?php foreach ($errors as $error): ?>
    <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
<?php endforeach; ?>
</ul>
<?php endif; ?>
<?php if (!empty($_SESSION['google_auth_error'])): ?><p class="auth-errors" role="alert"><?= htmlspecialchars($_SESSION['google_auth_error'], ENT_QUOTES, 'UTF-8') ?></p><?php unset($_SESSION['google_auth_error']); endif; ?><form method="post">
<input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
<label for="email">Email</label>
<input id="email" name="email" type="email" maxlength="120" autocomplete="username" required value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>">
<label for="password">Password</label>
<input id="password" name="password" type="password" autocomplete="current-password" required>
<button class="btn btn-primary" type="submit">Login</button>
</form><?php if (GoogleAuth::isConfigured()): ?><p class="auth-google-separator">or</p><a class="btn btn-outline google-auth-button" href="<?= BASE_URL ?>/auth/google_start.php">Continue with Google</a><?php endif; ?><p>New here? <a href="register.php">Create an account</a></p>
</div></section>
<?php require_once __DIR__ . '/../includes/auth_end.php'; ?>
