<?php

class AgeGroup
{
    private PDO $conn;

    public function __construct(PDO $pdo)
    {
        $this->conn = $pdo;
    }

    public function getAll(): array
    {
        $sql = "SELECT
                    id,
                    name,
                    slug,
                    description,
                    min_age,
                    max_age,
                    image,
                    sort_order
                FROM age_groups
                ORDER BY sort_order ASC, id DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }


    public function getById(int $id): array|false
    {
        $sql = "SELECT
                    id,
                    name,
                    slug,
                    description,
                    min_age,
                    max_age,
                    image,
                    sort_order
                FROM age_groups
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch();
    }


    public function slugExists(
        string $slug,
        ?int $excludeId = null
    ): bool {
        $sql = "SELECT id
                FROM age_groups
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
        $sql = "INSERT INTO age_groups (
                    name,
                    slug,
                    description,
                    min_age,
                    max_age,
                    image,
                    sort_order
                ) VALUES (
                    :name,
                    :slug,
                    :description,
                    :min_age,
                    :max_age,
                    :image,
                    :sort_order
                )";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'description' => $data['description'],
            'min_age' => $data['min_age'],
            'max_age' => $data['max_age'],
            'image' => $data['image'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0
        ]);
    }


    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE age_groups
                SET
                    name = :name,
                    slug = :slug,
                    description = :description,
                    min_age = :min_age,
                    max_age = :max_age,
                    image = :image,
                    sort_order = :sort_order
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'slug' => $data['slug'],
            'description' => $data['description'],
            'min_age' => $data['min_age'],
            'max_age' => $data['max_age'],
            'image' => $data['image'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0
        ]);
    }


    public function delete(int $id): bool
    {
        $sql = "DELETE FROM age_groups
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            'id' => $id
        ]);

        return $stmt->rowCount() > 0;
    }

    // Age groups for the home page "Shop by age" cards, with how many
    // visible toys fit each group (the toy's age range overlaps the group's).
    public function getAllWithToyCount(): array
    {
        $sql = "SELECT
                    g.id,
                    g.name,
                    g.slug,
                    g.description,
                    g.min_age,
                    g.max_age,
                    g.image,
                    g.sort_order,
                    COUNT(p.id) AS toy_count
                FROM age_groups g
                LEFT JOIN products p
                    ON p.is_active = 1
                   AND p.min_age <= g.max_age
                   AND p.max_age >= g.min_age
                GROUP BY g.id, g.name, g.slug, g.description, g.min_age,
                         g.max_age, g.image, g.sort_order
                ORDER BY g.sort_order ASC, g.min_age ASC, g.id ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
