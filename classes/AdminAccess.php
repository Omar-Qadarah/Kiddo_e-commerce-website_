<?php

 
final class AdminAccess
{
    public function __construct(private PDO $pdo)
    {
    }

    public function requireAdmin(): int
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $userId = $_SESSION['user_id'] ?? ($_SESSION['user']['id'] ?? null);
        if (!is_scalar($userId) || !ctype_digit((string) $userId)) {
            $this->redirectToLogin();
        }

        $statement = $this->pdo->prepare(
            'SELECT role, is_active FROM users WHERE id = ? LIMIT 1'
        );
        $statement->execute([(int) $userId]);
        $user = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$user || $user['role'] !== 'admin' || (int) $user['is_active'] !== 1) {
            $this->redirectToLogin();
        }

        return (int) $userId;
    }

    private function redirectToLogin(): never
    {
        header('Location: ' . BASE_URL . '/auth/login.php', true, 302);
        exit;
    }
}
