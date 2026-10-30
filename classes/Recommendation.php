<?php
class Recommendation
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getRecommendations(?int $userId, array $currentProductIds = [], int $limit = 4): array
    {
        $currentProductIds = array_values(array_unique(array_filter(array_map('intval', $currentProductIds))));
        $recommendations = [];

        if (!empty($currentProductIds)) {
            $recommendations = $this->getFrequentlyBoughtTogether($currentProductIds, $limit);
        }

        if (count($recommendations) < $limit && $userId !== null && $userId > 0) {
            $needed = $limit - count($recommendations);
            $excludeIds = array_merge($currentProductIds, array_column($recommendations, 'id'));
            $fromHistory = $this->getByUserHistory($userId, $excludeIds, $needed);
            $recommendations = array_merge($recommendations, $fromHistory);
        }

        if (count($recommendations) < $limit && !empty($currentProductIds)) {
            $needed = $limit - count($recommendations);
            $excludeIds = array_merge($currentProductIds, array_column($recommendations, 'id'));
            $fromCategoryAge = $this->getByCategoryAndAge($currentProductIds, $excludeIds, $needed);
            $recommendations = array_merge($recommendations, $fromCategoryAge);
        }

        if (count($recommendations) < $limit) {
            $needed = $limit - count($recommendations);
            $excludeIds = array_merge($currentProductIds, array_column($recommendations, 'id'));
            $fallbacks = $this->getBestSellersFallback($excludeIds, $needed);
            $recommendations = array_merge($recommendations, $fallbacks);
        }

        return array_slice($recommendations, 0, $limit);
    }

    private function getFrequentlyBoughtTogether(array $productIds, int $limit): array
    {
        if (empty($productIds) || $limit <= 0) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($productIds), '?'));

        $sql = "SELECT p.*, c.name AS category_name, COUNT(oi2.id) AS pair_count
                FROM order_items oi1
                JOIN orders o ON oi1.order_id = o.id
                JOIN order_items oi2 ON o.id = oi2.order_id
                JOIN products p ON oi2.product_id = p.id
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE oi1.product_id IN ($placeholders)
                  AND oi2.product_id NOT IN ($placeholders)
                  AND o.status = 'completed'
                  AND p.stock > 0
                GROUP BY p.id
                ORDER BY pair_count DESC, p.id DESC
                LIMIT " . (int)$limit;

        $stmt = $this->pdo->prepare($sql);
        $params = array_merge($productIds, $productIds);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getSearchColumnName(): ?string
    {
        try {
            $stmt = $this->pdo->query("SHOW COLUMNS FROM search_history");
            $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
            $candidates = ['query', 'search_query', 'keyword', 'search_term', 'term'];
            foreach ($candidates as $cand) {
                if (in_array($cand, $columns, true)) {
                    return $cand;
                }
            }
        } catch (Throwable $e) {
            return null;
        }
        return null;
    }

    private function getByUserHistory(int $userId, array $excludeIds, int $limit): array
    {
        if ($limit <= 0) {
            return [];
        }

        $excludePlaceholders = !empty($excludeIds) ? implode(',', array_fill(0, count($excludeIds), '?')) : '0';
        $searchCol = $this->getSearchColumnName();

        if ($searchCol !== null) {
            try {
                $searchStmt = $this->pdo->prepare("SELECT `{$searchCol}` FROM search_history WHERE user_id = ? ORDER BY id DESC LIMIT 5");
                $searchStmt->execute([$userId]);
                $queries = $searchStmt->fetchAll(PDO::FETCH_COLUMN);

                if (!empty($queries)) {
                    $conditions = [];
                    $params = [];
                    foreach ($queries as $q) {
                        $trimmed = trim((string)$q);
                        if ($trimmed !== '') {
                            $conditions[] = "(p.name LIKE ? OR p.description LIKE ?)";
                            $params[] = '%' . $trimmed . '%';
                            $params[] = '%' . $trimmed . '%';
                        }
                    }

                    if (!empty($conditions)) {
                        $sql = "SELECT p.*, c.name AS category_name
                                FROM products p
                                LEFT JOIN categories c ON p.category_id = c.id
                                WHERE (" . implode(' OR ', $conditions) . ")
                                  AND p.id NOT IN ($excludePlaceholders)
                                  AND p.stock > 0
                                ORDER BY p.id DESC
                                LIMIT " . (int)$limit;

                        $stmt = $this->pdo->prepare($sql);
                        $stmt->execute(array_merge($params, $excludeIds));
                        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        if (!empty($items)) {
                            return $items;
                        }
                    }
                }
            } catch (Throwable $e) {
            }
        }

        try {
            $sql = "SELECT DISTINCT p.category_id
                    FROM orders o
                    JOIN order_items oi ON o.id = oi.order_id
                    JOIN products p ON oi.product_id = p.id
                    WHERE o.user_id = ?
                    ORDER BY o.id DESC
                    LIMIT 3";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$userId]);
            $cats = array_values(array_unique(array_filter($stmt->fetchAll(PDO::FETCH_COLUMN))));

            if (!empty($cats)) {
                $cPl = implode(',', array_fill(0, count($cats), '?'));
                $sql = "SELECT p.*, c.name AS category_name
                        FROM products p
                        LEFT JOIN categories c ON p.category_id = c.id
                        WHERE p.category_id IN ($cPl)
                          AND p.id NOT IN ($excludePlaceholders)
                          AND p.stock > 0
                        ORDER BY p.id DESC
                        LIMIT " . (int)$limit;

                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array_merge($cats, $excludeIds));
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (Throwable $e) {
        }

        return [];
    }

    private function getByCategoryAndAge(array $sourceProductIds, array $excludeIds, int $limit): array
    {
        if (empty($sourceProductIds) || $limit <= 0) {
            return [];
        }

        $sourcePl = implode(',', array_fill(0, count($sourceProductIds), '?'));
        $infoStmt = $this->pdo->prepare("SELECT category_id, min_age, max_age FROM products WHERE id IN ($sourcePl)");
        $infoStmt->execute($sourceProductIds);
        $rows = $infoStmt->fetchAll(PDO::FETCH_ASSOC);

        $cats = array_values(array_unique(array_filter(array_column($rows, 'category_id'))));
        $minAge = min(array_column($rows, 'min_age') ?: [0]);
        $maxAge = max(array_column($rows, 'max_age') ?: [99]);

        $excludePl = !empty($excludeIds) ? implode(',', array_fill(0, count($excludeIds), '?')) : '0';

        $conditions = [];
        $params = [];

        if (!empty($cats)) {
            $conditions[] = "p.category_id IN (" . implode(',', array_fill(0, count($cats), '?')) . ")";
            $params = array_merge($params, $cats);
        }

        $conditions[] = "(p.min_age <= ? AND p.max_age >= ?)";
        $params[] = $maxAge;
        $params[] = $minAge;

        $sql = "SELECT p.*, c.name AS category_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE (" . implode(' OR ', $conditions) . ")
                  AND p.id NOT IN ($excludePl)
                  AND p.stock > 0
                ORDER BY p.price DESC
                LIMIT " . (int)$limit;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array_merge($params, $excludeIds));
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getBestSellersFallback(array $excludeIds, int $limit): array
    {
        if ($limit <= 0) {
            return [];
        }

        $excludePl = !empty($excludeIds) ? implode(',', array_fill(0, count($excludeIds), '?')) : '0';

        $sql = "SELECT p.*, c.name AS category_name, COALESCE(SUM(oi.quantity), 0) AS total_sales
                FROM products p
                LEFT JOIN order_items oi ON p.id = oi.product_id
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.id NOT IN ($excludePl)
                  AND p.stock > 0
                GROUP BY p.id
                ORDER BY total_sales DESC, p.id DESC
                LIMIT " . (int)$limit;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($excludeIds);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}