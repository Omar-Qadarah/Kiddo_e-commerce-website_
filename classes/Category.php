<?php

class Category
{
    private PDO $conn;

    public function __construct(PDO $pdo)
    {
        $this->conn = $pdo;
    }


    public function getAll(): array
    {
        $sql = "SELECT id, name, slug, description, icon, created_at
                FROM categories
                ORDER BY id DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    
    public function getById(int $id): array|false
    {
        $sql = "SELECT id, name, slug, description, icon, created_at
                FROM categories
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch();
    }

    public function nameExists(string $name, ?int $excludeId = null): bool
    {
        $sql = "SELECT id
                FROM categories
                WHERE name = :name";

        $params = ['name' => $name];

        if ($excludeId !== null) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetch() !== false;
    }

    public function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $sql = "SELECT id
                FROM categories
                WHERE slug = :slug";

        $params = ['slug' => $slug];

        if ($excludeId !== null) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetch() !== false;
    }
     public function create(array $data): bool
    {
        $sql = "INSERT INTO categories
                (name, slug, description, icon)
                VALUES
                (:name, :slug, :description, :icon)";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            'name'        => $data['name'],
            'slug'        => $data['slug'],
            'description' => $data['description'] ?? null,
            'icon'        => $data['icon'] ?? null
        ]);
    }


    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE categories
                SET name = :name,
                    slug = :slug,
                    description = :description,
                    icon = :icon
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            'id'          => $id,
            'name'        => $data['name'],
            'slug'        => $data['slug'],
            'description' => $data['description'] ?? null,
            'icon'        => $data['icon'] ?? null
        ]);
    }


    public function delete(int $id): bool
    {
        $checkSql = "SELECT id
                     FROM products
                     WHERE category_id = :category_id
                     LIMIT 1";

        $checkStmt = $this->conn->prepare($checkSql);
        $checkStmt->execute(['category_id' => $id]);

        if ($checkStmt->fetch() !== false) {
            return false;
        }

        $deleteSql = "DELETE FROM categories
                      WHERE id = :id";

        $deleteStmt = $this->conn->prepare($deleteSql);
        $deleteStmt->execute(['id' => $id]);

        return $deleteStmt->rowCount() > 0;
    }

    // Categories for the Toys page sidebar, with how many visible toys each has.
    public function getAllWithToyCount(): array
    {
        $sql = "SELECT c.id, c.name, c.slug, c.description, c.icon,
                       COUNT(p.id) AS toy_count
                FROM categories c
                LEFT JOIN products p
                    ON p.category_id = c.id AND p.is_active = 1
                GROUP BY c.id, c.name, c.slug, c.description, c.icon
                ORDER BY c.name ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}