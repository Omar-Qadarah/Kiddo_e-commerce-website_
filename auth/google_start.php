<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../config/GoogleAuth.php';

try {
    $state = bin2hex(random_bytes(32));
    $nonce = bin2hex(random_bytes(32));
    $_SESSION['google_oauth_state'] = $state;
    $_SESSION['google_oauth_nonce'] = $nonce;
    header('Location: ' . GoogleAuth::authorizationUrl($state, $nonce), true, 302);
    exit;
} catch (Throwable $error) {
    error_log('Google OAuth start failed: ' . $error->getMessage());
    $_SESSION['google_auth_error'] = 'Google sign-in is not configured yet. Please use email and password.';
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}