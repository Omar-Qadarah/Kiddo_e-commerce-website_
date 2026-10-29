<?php

class ProductImage
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // رفع صورة المنتج
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

        $info = getimagesize($file['tmp_name']);

        if ($info === false) {
            throw new RuntimeException("Invalid image.");
        }

        $types = [
            IMAGETYPE_JPEG => 'jpg',
            IMAGETYPE_PNG => 'png',
            IMAGETYPE_WEBP => 'webp'
        ];

        if (!isset($types[$info[2]])) {
            throw new RuntimeException(
                "Only JPG, PNG and WEBP are allowed."
            );
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

    // ربط الصورة بالمنتج في جدول images
    public function saveForProduct(
        int $productId,
        string $path,
        ?string $altText = null
    ): bool {
        $stmt = $this->pdo->prepare(
            "INSERT INTO images
            (product_id, image_path, alt_text, is_primary)
            VALUES (?, ?, ?, 1)"
        );

        return $stmt->execute([
            $productId,
            $path,
            $altText
        ]);
    }

    // عرض صور منتج معين
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
}