<?php

function accountUserInitials(string $name): string
{
    $parts = preg_split('/\s+/u', trim($name), -1, PREG_SPLIT_NO_EMPTY) ?: [];
    $initials = '';
    foreach (array_slice($parts, 0, 2) as $part) {
        $firstCharacter = function_exists('mb_substr') ? mb_substr($part, 0, 1, 'UTF-8') : substr($part, 0, 1);
        $initials .= function_exists('mb_strtoupper') ? mb_strtoupper($firstCharacter, 'UTF-8') : strtoupper($firstCharacter);
    }

    return $initials !== '' ? $initials : 'K';
}

function accountProfileImageUrl(int $userId, ?string $imageName = null): ?string
{
    if ($userId <= 0) {
        return null;
    }

    if ($imageName === null) {
        global $pdo;
        if (!($pdo instanceof PDO)) {
            return null;
        }
        $statement = $pdo->prepare('SELECT profile_image FROM users WHERE id = ? LIMIT 1');
        $statement->execute([$userId]);
        $imageName = (string) ($statement->fetchColumn() ?: '');
    }

    if (!preg_match('/^user_' . $userId . '_[a-f0-9]{16}\.(?:jpg|png|webp)$/', $imageName)) {
        return null;
    }
    $profileDirectory = __DIR__ . '/../uploads/profiles';
    if (!is_file($profileDirectory . DIRECTORY_SEPARATOR . $imageName)) {
        return null;
    }

    return BASE_URL . '/uploads/profiles/' . rawurlencode($imageName);
}
