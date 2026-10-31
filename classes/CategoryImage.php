<?php

final class CategoryImage
{
    public static function upload(?array $file): ?string
    {
        if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Image upload failed. Please try again.');
        }
        if ((int) ($file['size'] ?? 0) > 3 * 1024 * 1024) {
            throw new RuntimeException('Category images must be 3 MB or smaller.');
        }

        $temporaryFile = (string) ($file['tmp_name'] ?? '');
        $imageInfo = $temporaryFile !== '' ? @getimagesize($temporaryFile) : false;
        $fileInfo = class_exists('finfo') ? new finfo(FILEINFO_MIME_TYPE) : null;
        $mimeType = $fileInfo && $temporaryFile !== '' ? $fileInfo->file($temporaryFile) : (string) ($imageInfo['mime'] ?? '');
        $allowedTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

        if (!$imageInfo || !isset($allowedTypes[$mimeType]) || ($imageInfo['mime'] ?? '') !== $mimeType) {
            throw new RuntimeException('Use a valid JPG, PNG, or WebP image.');
        }
        if ((int) $imageInfo[0] > 4000 || (int) $imageInfo[1] > 4000) {
            throw new RuntimeException('Category image dimensions must be 4000 × 4000 pixels or smaller.');
        }

        $directory = __DIR__ . '/../uploads/categories';
        if (!is_dir($directory) && !@mkdir($directory, 0755, true) && !is_dir($directory)) {
            throw new RuntimeException('Unable to create the category image folder.');
        }

        $filename = bin2hex(random_bytes(16)) . '.' . $allowedTypes[$mimeType];
        if (!move_uploaded_file($temporaryFile, $directory . '/' . $filename)) {
            throw new RuntimeException('Unable to save the uploaded category image.');
        }

        return 'uploads/categories/' . $filename;
    }

    public static function delete(?string $path): void
    {
        $filename = basename((string) $path);
        if ($filename === '' || $filename === '.' || $filename === '..') {
            return;
        }

        $file = __DIR__ . '/../uploads/categories/' . $filename;
        if (is_file($file)) {
            @unlink($file);
        }
    }

    public static function url(?string $path): string
    {
        $filename = basename((string) $path);
        if ($filename === '' || $filename === '.' || $filename === '..') {
            return '';
        }

        return BASE_URL . '/uploads/categories/' . rawurlencode($filename);
    }
}
