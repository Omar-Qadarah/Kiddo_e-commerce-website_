<?php

// Read-only queries for the storefront pages (index.php, products/index.php,
// products/details.php). It only SELECTs, so it never changes data the admin
// pages manage. Product, ProductImage, Category and AgeGroup stay untouched.
class ToyCatalog
{
    private PDO $pdo;

    // Columns every toy card needs: the toy, its category, the best active
    // discount (product or category discount) and the visible reviews summary.
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

    // ---------- Landing page ----------

    // Toys that have an active discount, biggest discount first.
    public function getDiscountedToys(int $limit = 4): array
    {
        $sql = "SELECT * FROM (" . self::CARD_SELECT . ") AS toys
                WHERE discount_pct > 0
                ORDER BY discount_pct DESC, id DESC
                LIMIT " . max(1, $limit);

        return $this->pdo->query($sql)->fetchAll();
    }

    // Newest toys first.
    public function getNewArrivals(int $limit = 4): array
    {
        $sql = self::CARD_SELECT . " ORDER BY p.created_at DESC, p.id DESC LIMIT " . max(1, $limit);

        return $this->pdo->query($sql)->fetchAll();
    }

    // Age groups with how many active toys fit each one.
    // A toy fits a group when the two age ranges overlap.
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

    // ---------- Toys page ----------

    // Categories with the number of active toys in each one (sidebar).
    public function getCategoriesWithToyCount(): array
    {
        $sql = "SELECT c.id, c.name, c.slug, c.description, c.icon,
                       COUNT(p.id) AS toy_count
                FROM categories c
                LEFT JOIN products p ON p.category_id = c.id AND p.is_active = 1
                GROUP BY c.id, c.name, c.slug, c.description, c.icon
                ORDER BY c.name ASC";

        return $this->pdo->query($sql)->fetchAll();
    }

    // Toys that match the chosen filters. Every filter is optional:
    //   category => category slug
    //   age      => ['min_age' => .., 'max_age' => ..] of the chosen age group
    //   gender   => girls | boys | unisex
    //   price    => ['min' => .. or null, 'max' => .. or null] in JOD,
    //               checked against the price after discount (the price on the card)
    public function getFilteredToys(array $filters): array
    {
        $sql    = self::CARD_SELECT;
        $params = [];

        if (!empty($filters['category'])) {
            $sql .= " AND c.slug = :category";
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

        // Price: the discount is calculated inside CARD_SELECT, so the query is
        // wrapped (like getDiscountedToys) to filter on the final price.
        // min is included, max is not: 10-25 means 10.00 up to 24.99.
        if (!empty($filters['price'])) {
            $finalPrice = "ROUND(toys.price * (1 - toys.discount_pct / 100), 2)";
            $sql = "SELECT * FROM (" . $sql . ") AS toys WHERE 1 = 1";

            if ($filters['price']['min'] !== null) {
                $sql .= " AND $finalPrice >= :price_min";
                $params['price_min'] = (float) $filters['price']['min'];
            }

            if ($filters['price']['max'] !== null) {
                $sql .= " AND $finalPrice < :price_max";
                $params['price_max'] = (float) $filters['price']['max'];
            }

            $sql .= " ORDER BY toys.created_at DESC, toys.id DESC";
        } else {
            $sql .= " ORDER BY p.created_at DESC, p.id DESC";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    // Extra image paths (images table) for many toys at once:
    // [product_id => ['uploads/products/a.png', ...]]
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

    // ---------- Navbar search (products/find.php) ----------

    // The active toy whose name is exactly the typed text (case-insensitive).
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

    // Active toys whose name contains the typed text ("did you mean" list).
    public function findToysByNamePart(string $text, int $limit = 4): array
    {
        // % and _ typed by the user are searched as normal characters
        $like = '%' . addcslashes(trim($text), '%_\\') . '%';
        $stmt = $this->pdo->prepare(
            self::CARD_SELECT . " AND p.name LIKE :text ORDER BY p.name ASC LIMIT " . max(1, $limit)
        );
        $stmt->execute(['text' => $like]);

        return $stmt->fetchAll();
    }

    // ---------- Product details page ----------

    // One active toy with category, discount and rating summary.
    public function getToy(int $id): array|false
    {
        $stmt = $this->pdo->prepare(self::CARD_SELECT . " AND p.id = :id");
        $stmt->execute(['id' => $id]);

        return $stmt->fetch();
    }

    // Visible reviews of one toy, newest first, with the reviewer's first name.
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
