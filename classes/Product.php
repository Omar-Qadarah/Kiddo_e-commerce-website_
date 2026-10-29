<?php

class Product
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // عرض جميع المنتجات مع اسم التصنيف
    public function getAll(): array
    {
        $sql = "SELECT products.*, categories.name AS category_name
                FROM products
                INNER JOIN categories
                ON products.category_id = categories.id
                ORDER BY products.id DESC";

        return $this->pdo->query($sql)->fetchAll();
    }

    // جلب منتج واحد
    public function getById(int $id): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM products WHERE id = ?"
        );

        $stmt->execute([$id]);

        return $stmt->fetch();
    }

    // جلب التصنيفات للقائمة المنسدلة
    public function getCategories(): array
    {
        return $this->pdo
            ->query("SELECT id, name FROM categories ORDER BY name")
            ->fetchAll();
    }

    // إضافة منتج
    public function create(array $data): bool
    {
        $sql = "INSERT INTO products
        (category_id, name, description, instructions,
         price, stock, gender, min_age, max_age, image, is_active)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            $data['category_id'],
            $data['name'],
            $data['description'],
            $data['instructions'],
            $data['price'],
            $data['stock'],
            $data['gender'],
            $data['min_age'],
            $data['max_age'],
            $data['image'],
            $data['is_active']
        ]);
    }

    // تعديل منتج
    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE products SET
        category_id = ?, name = ?, description = ?,
        instructions = ?, price = ?, stock = ?, gender = ?,
        min_age = ?, max_age = ?, image = ?, is_active = ?
        WHERE id = ?";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            $data['category_id'],
            $data['name'],
            $data['description'],
            $data['instructions'],
            $data['price'],
            $data['stock'],
            $data['gender'],
            $data['min_age'],
            $data['max_age'],
            $data['image'],
            $data['is_active'],
            $id
        ]);
    }

    // حذف منتج
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM products WHERE id = ?"
        );

        return $stmt->execute([$id]);
    }
}