<?php

class Product
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

     
    public function getAll(): array
    {
        $sql = "SELECT products.*, categories.name AS category_name
                FROM products
                INNER JOIN categories
                ON products.category_id = categories.id
                ORDER BY products.id DESC";

        return $this->pdo->query($sql)->fetchAll();
    }

     
    public function getById(int $id): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM products WHERE id = ?"
        );

        $stmt->execute([$id]);

        return $stmt->fetch();
    }

     
    public function getCategories(): array
    {
        return $this->pdo
            ->query("SELECT id, name FROM categories ORDER BY name")
            ->fetchAll();
    }

     
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

     
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM products WHERE id = ?"
        );

        return $stmt->execute([$id]);
    }

     

     
    public function getDiscountedToys(int $limit = 4): array
    {
        return $this->getToyCards('d.best_pct IS NOT NULL', 'd.best_pct DESC, p.id DESC', $limit);
    }

     
    public function getNewArrivals(int $limit = 4): array
    {
        return $this->getToyCards('1 = 1', 'p.created_at DESC, p.id DESC', $limit);
    }

     
     
    public function getToyDetails(int $id): array|false
    {
        $rows = $this->getToyCards('p.id = :id', 'p.id', 1, ['id' => $id]);
        return $rows[0] ?? false;
    }

     
     
     
     
     
    private function getToyCards(string $where, string $orderBy, int $limit, array $params = []): array
    {
        $sql = "SELECT p.id, p.name, p.description, p.instructions, p.price, p.stock,
                       p.gender, p.min_age, p.max_age, p.created_at,
                       c.name AS category_name,
                       COALESCE(NULLIF(p.image, ''), (
                           SELECT i.image_path FROM images i
                           WHERE i.product_id = p.id
                           ORDER BY i.is_primary DESC, i.sort_order ASC
                           LIMIT 1
                       )) AS image,
                       COALESCE(r.avg_rating, 0) AS avg_rating,
                       COALESCE(r.review_count, 0) AS review_count,
                       COALESCE(d.best_pct, 0) AS discount_pct
                FROM products p
                INNER JOIN categories c ON c.id = p.category_id
                LEFT JOIN (
                    SELECT product_id, AVG(rating) AS avg_rating, COUNT(*) AS review_count
                    FROM reviews
                    WHERE is_visible = 1
                    GROUP BY product_id
                ) r ON r.product_id = p.id
                LEFT JOIN (
                    SELECT p2.id AS product_id, MAX(ds.percentage) AS best_pct
                    FROM products p2
                    INNER JOIN discounts ds
                        ON ds.product_id = p2.id OR ds.category_id = p2.category_id
                    WHERE ds.is_active = 1
                      AND (ds.starts_at IS NULL OR ds.starts_at <= :today_start)
                      AND (ds.ends_at IS NULL OR ds.ends_at >= :today_end)
                    GROUP BY p2.id
                ) d ON d.product_id = p.id
                WHERE p.is_active = 1 AND {$where}
                ORDER BY {$orderBy}
                LIMIT :limit";

         
        $today = date('Y-m-d');

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':today_start', $today);
        $stmt->bindValue(':today_end', $today);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        foreach ($params as $name => $value) {
            $stmt->bindValue(':' . $name, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->execute();

        return $stmt->fetchAll();
    }

     
     
     
    public function getFilteredToys(array $filters): array
    {
        $where  = ['1 = 1'];
        $params = [];

        if (!empty($filters['category'])) {
            $where[] = 'c.slug = :category';
            $params['category'] = $filters['category'];
        }

        if (!empty($filters['age'])) {
             
            $where[] = 'EXISTS (SELECT 1 FROM age_groups g
                                WHERE g.slug = :age
                                  AND p.min_age <= g.max_age
                                  AND p.max_age >= g.min_age)';
            $params['age'] = $filters['age'];
        }

        if (!empty($filters['gender'])) {
            $where[] = $filters['gender'] === 'unisex'
                ? "p.gender = 'unisex'"
                : "p.gender IN (:gender, 'unisex')";
            if ($filters['gender'] !== 'unisex') {
                $params['gender'] = $filters['gender'];
            }
        }

        return $this->getToyCards(implode(' AND ', $where), 'p.created_at DESC, p.id DESC', 500, $params);
    }
}