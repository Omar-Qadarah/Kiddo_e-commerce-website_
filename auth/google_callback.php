<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../config/GoogleAuth.php';
require_once __DIR__ . '/../classes/User.php';

function googleAuthFail(string $message): void
{
    unset($_SESSION['google_oauth_state'], $_SESSION['google_oauth_nonce']);
    $_SESSION['google_auth_error'] = $message;
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

$expectedState = $_SESSION['google_oauth_state'] ?? '';
$expectedNonce = $_SESSION['google_oauth_nonce'] ?? '';
$state = $_GET['state'] ?? '';
$code = $_GET['code'] ?? '';
if (!is_string($expectedState) || $expectedState === '' || !is_string($state) ||
    !hash_equals($expectedState, $state) || !is_string($expectedNonce) || $expectedNonce === '') {
    googleAuthFail('Google sign-in could not be verified. Please try again.');
}
if (isset($_GET['error']) || !is_string($code) || $code === '') {
    googleAuthFail('Google sign-in was cancelled or could not be completed.');
}

try {
    $claims = GoogleAuth::verifyAuthorizationCode($code, $expectedNonce);
    $googleId = (string) $claims['sub'];
    $email = strtolower(trim((string) $claims['email']));
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 120 || strlen($googleId) > 64) {
        throw new RuntimeException('Google returned invalid account details.');
    }

    $users = new User($pdo);
    $user = $users->getUserByGoogleId($googleId);
    if (!$user) {
        $user = $users->getUserByEmail($email);
        if ($user) {
            if (!$user['is_active']) {
                googleAuthFail('This account is inactive. Please contact support.');
            }
            if (!empty($user['google_id']) && !hash_equals((string) $user['google_id'], $googleId)) {
                googleAuthFail('This email is linked to another Google account. Sign in with your password first.');
            }
            if (empty($user['google_id']) && !$users->linkGoogleAccount((int) $user['id'], $googleId)) {
                 
                $user = $users->getUserByGoogleId($googleId);
                if (!$user) {
                    throw new RuntimeException('Could not safely link the Google account.');
                }
            } else {
                $user = $users->getUserByIdForLogin((int) $user['id']);
            }
        } else {
            $fullName = trim((string) ($claims['name'] ?? 'Google customer'));
            $firstName = trim((string) ($claims['given_name'] ?? ''));
            $lastName = trim((string) ($claims['family_name'] ?? ''));
            if ($firstName === '') {
                $parts = preg_split('/\s+/', $fullName, 2);
                $firstName = $parts[0] ?? 'Google';
                $lastName = $parts[1] ?? 'Customer';
            } elseif ($lastName === '') {
                $lastName = 'Customer';
            }
            try {
                $user = $users->createGoogleUser($firstName, $lastName, $email, $googleId);
            } catch (PDOException $insertError) {
                if ($insertError->getCode() !== '23000') {
                    throw $insertError;
                }
                 
                $user = $users->getUserByGoogleId($googleId) ?: $users->getUserByEmail($email);
                if ($user && empty($user['google_id']) && $user['is_active']) {
                    $users->linkGoogleAccount((int) $user['id'], $googleId);
                    $user = $users->getUserByGoogleId($googleId);
                } elseif ($user && !empty($user['google_id']) && !hash_equals((string) $user['google_id'], $googleId)) {
                    googleAuthFail('This email is linked to another Google account. Sign in with your password first.');
                }
            }
        }
    }

    if (!$user || !$user['is_active'] || !in_array($user['role'], ['customer', 'admin'], true)) {
        googleAuthFail('This account cannot sign in. Please contact support.');
    }

    unset($_SESSION['google_oauth_state'], $_SESSION['google_oauth_nonce']);
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = trim($user['first_name'] . ' ' . $user['last_name']);
    $_SESSION['user_role'] = $user['role'];
    unset($_SESSION['csrf_token']);

    if ($user['role'] === 'admin') {
        header('Location: ' . BASE_URL . '/admin/index.php');
    } elseif (!empty($_SESSION['after_login']) && is_string($_SESSION['after_login']) &&
        str_starts_with($_SESSION['after_login'], '/') && !str_starts_with($_SESSION['after_login'], '//')) {
        $target = $_SESSION['after_login'];
        unset($_SESSION['after_login']);
        header('Location: ' . $target);
    } else {
        header('Location: ' . BASE_URL . '/index.php');
    }
    exit;
} catch (Throwable $error) {
    error_log('Google OAuth callback failed: ' . $error->getMessage());
    googleAuthFail('Google sign-in could not be completed. Please try again.');
}