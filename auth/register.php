<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../config/GoogleAuth.php';
$userModel = new User($pdo);
$errors = [];

$first_name = '';
$last_name = '';
$email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCsrf();
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
     
    if (!is_string($first_name)) {
        $errors[] = 'Invalid first_name value.';
        $first_name = '';
    }
    if (!is_string($last_name)) {
        $errors[] = 'Invalid last_name value.';
        $last_name = '';
    }
    if (!is_string($email)) {
        $errors[] = 'Invalid email value.';
        $email = '';
    }
    if (!is_string($password)) {
        $errors[] = 'Invalid password value.';
        $password = '';
    }
    if (!is_string($confirm_password)) {
        $errors[] = 'Invalid confirm_password value.';
        $confirm_password = '';
    }
    $first_name = trim($first_name);
    $last_name = trim($last_name);
    $email = strtolower(trim($email));
    if ($first_name === '' || mb_strlen($first_name, 'UTF-8') > 50) {
        $errors[] = 'First name is required (maximum 50 characters).';
    }
    if ($last_name === '' || mb_strlen($last_name, 'UTF-8') > 50) {
        $errors[] = 'Last name is required (maximum 50 characters).';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 120) {
        $errors[] = 'Please enter a valid email (maximum 120 characters).';
    }
    if (strlen($password) < 8 || strlen($password) > 72 || strpos($password, "\0") !== false) {
        $errors[] = 'Password must be between 8 and 72 bytes and contain no null characters.';
    }
    if ($password !== $confirm_password) {
        $errors[] = 'Passwords do not match.';
    }
    if (empty($errors)) {
        try {
             
            $result = $userModel->createUser($first_name, $last_name, $email, $password);
            if ($result === true) {
                $newUser = $userModel->getUserByEmail($email);
                if ($newUser && !empty($newUser['is_active']) && $newUser['role'] === 'customer') {
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = $newUser['id'];
                    $_SESSION['user_name'] = $newUser['first_name'] . ' ' . $newUser['last_name'];
                    $_SESSION['user_role'] = $newUser['role'];
                    unset($_SESSION['csrf_token'], $_SESSION['register_success']);
                    header('Location: ../index.php');
                    exit;
                }
                $errors[] = 'Your account was created, but we could not sign you in. Please log in.';
            }            $errors[] = $result === 'Email already exists'
                ? 'This email is already registered.' : 'Unable to create the account. Please try again.';
        } catch (PDOException $error) {
            error_log($error->getMessage());
            $errors[] = 'Unable to create the account. Please try again.';
        }
    }
}
$pageTitle = 'Register';
$activePage = 'register';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="auth-section container"><div class="auth-card">
<h1>Create an account</h1><p>Join Kiddo's and find your next favourite toy.</p>
<?php if (!empty($errors)): ?>
<ul class="auth-errors" role="alert">
<?php foreach ($errors as $error): ?>
    <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
<?php endforeach; ?>
</ul>
<?php endif; ?>
<form method="post">
<input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
<label for="first_name">First name</label>
<input id="first_name" name="first_name" type="text" maxlength="50" autocomplete="given-name" required value="<?= htmlspecialchars($first_name, ENT_QUOTES, 'UTF-8') ?>">
<label for="last_name">Last name</label>
<input id="last_name" name="last_name" type="text" maxlength="50" autocomplete="family-name" required value="<?= htmlspecialchars($last_name, ENT_QUOTES, 'UTF-8') ?>">
<label for="email">Email</label>
<input id="email" name="email" type="email" maxlength="120" autocomplete="email" required value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>">
<label for="password">Password</label>
<input id="password" name="password" type="password" maxlength="72" autocomplete="new-password" required>
<label for="confirm_password">Confirm password</label>
<input id="confirm_password" name="confirm_password" type="password" maxlength="72" autocomplete="new-password" required>
<p class="auth-hint">Use a password of at least 8 characters.</p>
<button class="btn btn-primary" type="submit">Register</button></form><?php if (GoogleAuth::isConfigured()): ?><p class="auth-google-separator">or create an account with</p><a class="btn btn-outline google-auth-button" href="<?= BASE_URL ?>/auth/google_start.php">Continue with Google</a><?php endif; ?>
<p>Already registered? <a href="login.php">Log in</a></p>
</div></section>
<?php require_once __DIR__ . '/../includes/auth_end.php'; ?>
