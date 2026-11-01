<?php

class ProductImage
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

     
    public function upload(array $file): ?string
    {
        if (
            !isset($file['error']) ||
            $file['error'] === UPLOAD_ERR_NO_FILE
        ) {
            return null;
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException("Image upload failed.");
        }

        if ($file['size'] > 3 * 1024 * 1024) {
            throw new RuntimeException("Image must be under 3 MB.");
        }

        $temporaryFile = (string) ($file['tmp_name'] ?? '');
        if (!is_uploaded_file($temporaryFile)) {
            throw new RuntimeException('Invalid uploaded file.');
        }
        $info = @getimagesize($temporaryFile);
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($temporaryFile);

        if ($info === false) {
            throw new RuntimeException("Invalid image.");
        }

        $types = [
            IMAGETYPE_JPEG => 'jpg',
            IMAGETYPE_PNG => 'png',
            IMAGETYPE_WEBP => 'webp'
        ];

        if (!isset($types[$info[2]]) || ($info['mime'] ?? '') !== $mime) {
            throw new RuntimeException(
                "Only JPG, PNG and WEBP are allowed."
            );
        }

        if ($info[0] > 4000 || $info[1] > 4000) {
            throw new RuntimeException('Image dimensions must be 4000 x 4000 pixels or smaller.');
        }

        $folder = dirname(__DIR__) . '/uploads/products/';

        if (!is_dir($folder)) {
            mkdir($folder, 0775, true);
        }

        $fileName = uniqid('product_', true)
                  . '.' . $types[$info[2]];

        if (!move_uploaded_file(
            $file['tmp_name'],
            $folder . $fileName
        )) {
            throw new RuntimeException("Could not save image.");
        }

        return 'uploads/products/' . $fileName;
    }

     
    public function uploadMany(array $files): array
    {
        if (!isset($files['name']) || !is_array($files['name'])) {
            return [];
        }

        $uploaded = [];
        try {
            foreach (array_keys($files['name']) as $index) {
                $error = (int) ($files['error'][$index] ?? UPLOAD_ERR_NO_FILE);
                if ($error === UPLOAD_ERR_NO_FILE) {
                    continue;
                }
                $path = $this->upload([
                    'name' => $files['name'][$index] ?? '',
                    'type' => $files['type'][$index] ?? '',
                    'tmp_name' => $files['tmp_name'][$index] ?? '',
                    'error' => $error,
                    'size' => (int) ($files['size'][$index] ?? 0),
                ]);
                if ($path !== null) {
                    $uploaded[] = $path;
                }
            }
        } catch (Throwable $error) {
            $this->deleteUploaded($uploaded);
            throw $error;
        }

        return $uploaded;
    }

    public function deleteUploaded(array $paths): void
    {
        $folder = dirname(__DIR__) . '/uploads/products/';
        foreach ($paths as $path) {
            if (!is_string($path) || !str_starts_with($path, 'uploads/products/')) {
                continue;
            }
            $file = $folder . basename($path);
            if (is_file($file)) {
                @unlink($file);
            }
        }
    }

     
    public function saveForProduct(
        int $productId,
        string $path,
        ?string $altText = null
    ): bool {
        $existingCountStmt = $this->pdo->prepare('SELECT COUNT(*) FROM images WHERE product_id = ?');
        $existingCountStmt->execute([$productId]);
        $existingCount = (int) $existingCountStmt->fetchColumn();

        $productImageStmt = $this->pdo->prepare('SELECT image FROM products WHERE id = ?');
        $productImageStmt->execute([$productId]);
        $hasLegacyPrimary = trim((string) $productImageStmt->fetchColumn()) !== '';
        $isPrimary = $existingCount === 0 && !$hasLegacyPrimary ? 1 : 0;
        $sortOrder = $existingCount + ($hasLegacyPrimary ? 1 : 0);

        $stmt = $this->pdo->prepare(
            "INSERT INTO images
            (product_id, image_path, alt_text, is_primary, sort_order)
            VALUES (?, ?, ?, ?, ?)"
        );

        return $stmt->execute([
            $productId,
            $path,
            $altText,
            $isPrimary,
            $sortOrder,
        ]);
    }

     
     
    public function deleteProductImage(int $productId, ?int $imageId = null, bool $deleteMain = false): bool
    {
        $deletedPath = '';
        $this->pdo->beginTransaction();
        try {
            if ($deleteMain) {
                $mainStmt = $this->pdo->prepare('SELECT image FROM products WHERE id = ? FOR UPDATE');
                $mainStmt->execute([$productId]);
                $deletedPath = trim((string) $mainStmt->fetchColumn());
                if ($deletedPath === '') {
                    $this->pdo->rollBack();
                    return false;
                }

                $nextStmt = $this->pdo->prepare(
                    'SELECT image_id, image_path FROM images
                     WHERE product_id = ? AND image_path <> ?
                     ORDER BY is_primary DESC, sort_order ASC, image_id ASC
                     LIMIT 1 FOR UPDATE'
                );
                $nextStmt->execute([$productId, $deletedPath]);
                $nextImage = $nextStmt->fetch(PDO::FETCH_ASSOC) ?: null;

                $updateMain = $this->pdo->prepare('UPDATE products SET image = ? WHERE id = ?');
                $updateMain->execute([$nextImage['image_path'] ?? '', $productId]);
                $deleteRows = $this->pdo->prepare('DELETE FROM images WHERE product_id = ? AND image_path = ?');
                $deleteRows->execute([$productId, $deletedPath]);
                $clearPrimary = $this->pdo->prepare('UPDATE images SET is_primary = 0 WHERE product_id = ?');
                $clearPrimary->execute([$productId]);
                if ($nextImage) {
                    $setPrimary = $this->pdo->prepare('UPDATE images SET is_primary = 1, sort_order = 0 WHERE image_id = ? AND product_id = ?');
                    $setPrimary->execute([(int) $nextImage['image_id'], $productId]);
                }
            } else {
                if ($imageId === null || $imageId < 1) {
                    $this->pdo->rollBack();
                    return false;
                }
                $imageStmt = $this->pdo->prepare('SELECT image_path, is_primary FROM images WHERE image_id = ? AND product_id = ? FOR UPDATE');
                $imageStmt->execute([$imageId, $productId]);
                $image = $imageStmt->fetch(PDO::FETCH_ASSOC);
                if (!$image) {
                    $this->pdo->rollBack();
                    return false;
                }
                $deletedPath = (string) $image['image_path'];
                $deleteStmt = $this->pdo->prepare('DELETE FROM images WHERE image_id = ? AND product_id = ?');
                $deleteStmt->execute([$imageId, $productId]);

                if ((int) $image['is_primary'] === 1) {
                    $mainStmt = $this->pdo->prepare('SELECT image FROM products WHERE id = ? FOR UPDATE');
                    $mainStmt->execute([$productId]);
                    if (trim((string) $mainStmt->fetchColumn()) === '') {
                        $nextStmt = $this->pdo->prepare('SELECT image_id FROM images WHERE product_id = ? ORDER BY sort_order ASC, image_id ASC LIMIT 1 FOR UPDATE');
                        $nextStmt->execute([$productId]);
                        $nextId = $nextStmt->fetchColumn();
                        if ($nextId) {
                            $clearPrimary = $this->pdo->prepare('UPDATE images SET is_primary = 0 WHERE product_id = ?');
                            $clearPrimary->execute([$productId]);
                            $setPrimary = $this->pdo->prepare('UPDATE images SET is_primary = 1 WHERE image_id = ? AND product_id = ?');
                            $setPrimary->execute([(int) $nextId, $productId]);
                        }
                    }
                }
            }

            $this->pdo->commit();
        } catch (Throwable $error) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $error;
        }

        if ($deletedPath !== '') {
            $imageRefs = $this->pdo->prepare('SELECT (SELECT COUNT(*) FROM products WHERE image = ?) + (SELECT COUNT(*) FROM images WHERE image_path = ?)');
            $imageRefs->execute([$deletedPath, $deletedPath]);
            if ((int) $imageRefs->fetchColumn() === 0) {
                $this->deleteUploaded([$deletedPath]);
            }
        }

        return true;
    }

     
    public function getByProductId(int $productId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM images
             WHERE product_id = ?
             ORDER BY is_primary DESC, sort_order ASC"
        );

        $stmt->execute([$productId]);

        return $stmt->fetchAll();
    }

     
     
    public function getPathsForProducts(array $productIds): array
    {
        $productIds = array_values(array_unique(array_map('intval', $productIds)));
        if (empty($productIds)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($productIds), '?'));
        $stmt = $this->pdo->prepare(
            "SELECT product_id, image_path FROM images
             WHERE product_id IN ($placeholders)
             ORDER BY product_id, is_primary DESC, sort_order ASC, image_id ASC"
        );
        $stmt->execute($productIds);

        $grouped = [];
        foreach ($stmt->fetchAll() as $row) {
            $grouped[(int) $row['product_id']][] = $row['image_path'];
        }
        return $grouped;
    }
}
