<?php

 
 
 
class ToyCatalog
{
    private PDO $pdo;
     
     
    private const CARD_SELECT = "
        SELECT p.id, p.category_id, p.name, p.description, p.instructions,
               p.price, p.stock, p.gender, p.min_age, p.max_age, p.image,
               p.created_at,
               c.name AS category_name, c.slug AS category_slug,
               COALESCE((
                   SELECT MAX(d.percentage)
                   FROM discounts d
                   WHERE d.is_active = 1
                     AND (d.product_id = p.id OR d.category_id = p.category_id)
                     AND (d.starts_at IS NULL OR d.starts_at <= CURDATE())
                     AND (d.ends_at IS NULL OR d.ends_at >= CURDATE())
               ), 0) AS discount_pct,
               COALESCE((
                   SELECT AVG(r.rating) FROM reviews r
                   WHERE r.product_id = p.id AND r.is_visible = 1
               ), 0) AS avg_rating,
               (
                   SELECT COUNT(*) FROM reviews r
                   WHERE r.product_id = p.id AND r.is_visible = 1
               ) AS review_count
        FROM products p
        INNER JOIN categories c ON c.id = p.category_id
        WHERE p.is_active = 1";

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getShopCategories(): array
    {
        return $this->getCategoriesWithToyCount();
    }
    public function getToysByIds(array $ids): array
    {
        $ids = array_values(array_unique(array_map('intval', $ids)));
        if (!$ids) return [];
        $stmt = $this->pdo->prepare(self::CARD_SELECT . ' AND p.id IN (' . implode(',', array_fill(0,count($ids),'?')) . ')');
        $stmt->execute($ids);
        return array_column($stmt->fetchAll(), null, 'id');
    }

     

     
    public function getDiscountedToys(int $limit = 4): array
    {
        $sql = "SELECT * FROM (" . self::CARD_SELECT . ") AS toys
                WHERE discount_pct > 0
                ORDER BY discount_pct DESC, id DESC
                LIMIT " . max(1, $limit);

        return $this->pdo->query($sql)->fetchAll();
    }

     
    public function getNewArrivals(int $limit = 4): array
    {
        $sql = self::CARD_SELECT . " ORDER BY p.created_at DESC, p.id DESC LIMIT " . max(1, $limit);

        return $this->pdo->query($sql)->fetchAll();
    }

     
     
    public function getAgeGroupsWithToyCount(): array
    {
        $sql = "SELECT g.id, g.name, g.slug, g.description, g.min_age, g.max_age, g.image,
                       (SELECT COUNT(*) FROM products p
                        WHERE p.is_active = 1
                          AND p.min_age <= g.max_age
                          AND p.max_age >= g.min_age) AS toy_count
                FROM age_groups g
                ORDER BY g.sort_order ASC, g.min_age ASC";

        return $this->pdo->query($sql)->fetchAll();
    }

     

     
    public function getCategoriesWithToyCount(): array
    {
        $sql = "SELECT c.id, c.name, c.slug, c.description, c.icon, c.image,
                       COUNT(p.id) AS toy_count
                FROM categories c
                LEFT JOIN products p ON p.category_id = c.id AND p.is_active = 1
                GROUP BY c.id, c.name, c.slug, c.description, c.icon, c.image
                ORDER BY c.name ASC";

        return $this->pdo->query($sql)->fetchAll();
    }

     
     
     
    public function getCategoriesWithDiscountedToyCount(): array
    {
        $sql = "SELECT c.id, c.name, c.slug, c.description, c.icon, c.image,
                       COUNT(p.id) AS toy_count
                FROM categories c
                INNER JOIN products p ON p.category_id = c.id AND p.is_active = 1
                WHERE EXISTS (
                    SELECT 1
                    FROM discounts d
                    WHERE d.is_active = 1
                      AND (d.product_id = p.id OR d.category_id = p.category_id)
                      AND (d.starts_at IS NULL OR d.starts_at <= CURDATE())
                      AND (d.ends_at IS NULL OR d.ends_at >= CURDATE())
                      AND d.percentage > 0
                )
                GROUP BY c.id, c.name, c.slug, c.description, c.icon, c.image
                ORDER BY c.name ASC";

        return $this->pdo->query($sql)->fetchAll();
    }

     
     
     
     
     
     
    private function filteredQuery(array $filters): array
    {
        $sql    = self::CARD_SELECT;
        $params = [];

        if (!empty($filters['q'])) {
            $words = preg_split('/\s+/u', trim($filters['q']), -1, PREG_SPLIT_NO_EMPTY);
            foreach (array_slice($words ?: [], 0, 8) as $i => $word) {
                $sql .= " AND (LOWER(p.name) LIKE LOWER(:word_name_$i) OR LOWER(c.name) LIKE LOWER(:word_category_$i) OR LOWER(p.description) LIKE LOWER(:word_description_$i))";
                $like = '%' . addcslashes($word, '%_\\') . '%';
                $params["word_name_$i"] = $like;
                $params["word_category_$i"] = $like;
                $params["word_description_$i"] = $like;
            }
        }
        if (!empty($filters['category'])) {
            $sql .= ' AND c.slug = :category';
            $params['category'] = $filters['category'];
        }

        if (!empty($filters['age'])) {
            $sql .= " AND p.min_age <= :age_max AND p.max_age >= :age_min";
            $params['age_min'] = (int) $filters['age']['min_age'];
            $params['age_max'] = (int) $filters['age']['max_age'];
        }

        if (!empty($filters['gender'])) {
            $sql .= " AND p.gender = :gender";
            $params['gender'] = $filters['gender'];
        }

        if (!empty($filters['stock'])) {
            $sql .= " AND p.stock > 0";
        }

         
         
         
         
        $sql = "SELECT * FROM (" . $sql . ") AS toys WHERE 1 = 1";
        $finalPrice = "ROUND(toys.price * (1 - toys.discount_pct / 100), 2)";
        if (!empty($filters['price'])) {
            if ($filters['price']['min'] !== null) {
                $sql .= " AND $finalPrice >= :price_min";
                $params['price_min'] = (float) $filters['price']['min'];
            }
            if ($filters['price']['max'] !== null) {
                $sql .= " AND $finalPrice < :price_max";
                $params['price_max'] = (float) $filters['price']['max'];
            }
        }
        if (!empty($filters['offers'])) $sql .= " AND toys.discount_pct > 0";
        $sorts = [
            'newest' => 'toys.created_at DESC, toys.id DESC',
            'price_asc' => "$finalPrice ASC, toys.id DESC",
            'price_desc' => "$finalPrice DESC, toys.id DESC",
            'name_asc' => 'toys.name ASC, toys.id DESC',
        ];
        $sql .= ' ORDER BY ' . ($sorts[$filters['sort'] ?? 'newest'] ?? $sorts['newest']);

        return [$sql, $params];
    }

    public function countFilteredToys(array $filters): int
    {
        [$sql, $params] = $this->filteredQuery($filters);
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM ($sql) AS matching_toys");
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function getFilteredToys(array $filters, ?int $limit = null, int $offset = 0): array
    {
        [$sql, $params] = $this->filteredQuery($filters);
        if ($limit !== null) {
            $sql .= " LIMIT :page_limit OFFSET :page_offset";
        }
        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        if ($limit !== null) {
            $stmt->bindValue(':page_limit', max(1, $limit), PDO::PARAM_INT);
            $stmt->bindValue(':page_offset', max(0, $offset), PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

     
     
    public function getImagePathsForToys(array $productIds): array
    {
        $productIds = array_values(array_unique(array_map('intval', $productIds)));
        if (empty($productIds)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($productIds), '?'));
        $stmt = $this->pdo->prepare(
            "SELECT product_id, image_path
             FROM images
             WHERE product_id IN ($placeholders)
             ORDER BY product_id, is_primary DESC, sort_order ASC, image_id ASC"
        );
        $stmt->execute($productIds);

        $paths = [];
        foreach ($stmt->fetchAll() as $row) {
            $paths[(int) $row['product_id']][] = $row['image_path'];
        }

        return $paths;
    }

     

     
    public function findToyByExactName(string $name): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT p.id FROM products p
             WHERE p.is_active = 1 AND LOWER(TRIM(p.name)) = LOWER(:name)
             ORDER BY p.id ASC LIMIT 1"
        );
        $stmt->execute(['name' => trim($name)]);

        return $stmt->fetch();
    }

     
    public function findToysByNamePart(string $text, int $limit = 4): array
    {
         
        $like = '%' . addcslashes(trim($text), '%_\\') . '%';
        $stmt = $this->pdo->prepare(
            self::CARD_SELECT . " AND p.name LIKE :text ORDER BY p.name ASC LIMIT " . max(1, $limit)
        );
        $stmt->execute(['text' => $like]);

        return $stmt->fetchAll();
    }

     

     
    public function getToy(int $id): array|false
    {
        $stmt = $this->pdo->prepare(self::CARD_SELECT . " AND p.id = :id");
        $stmt->execute(['id' => $id]);

        return $stmt->fetch();
    }

     
    public function getReviews(int $productId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT r.rating, r.comment, r.created_at, u.first_name
             FROM reviews r
             INNER JOIN users u ON u.id = r.user_id
             WHERE r.product_id = ? AND r.is_visible = 1
             ORDER BY r.created_at DESC"
        );
        $stmt->execute([$productId]);

        return $stmt->fetchAll();
    }
}
