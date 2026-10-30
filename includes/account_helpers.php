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

function accountProfileImageUrl(int $userId): ?string
{
    if ($userId <= 0) {
        return null;
    }

    $profileDirectory = __DIR__ . '/../uploads/profiles';
    $images = glob($profileDirectory . '/user_' . $userId . '_*.{jpg,jpeg,png,webp}', GLOB_BRACE) ?: [];
    if (!$images) {
        return null;
    }

    usort($images, static fn(string $left, string $right): int => filemtime($right) <=> filemtime($left));
    $image = $images[0];
    if (!is_file($image)) {
        return null;
    }

    return BASE_URL . '/uploads/profiles/' . rawurlencode(basename($image));
}
