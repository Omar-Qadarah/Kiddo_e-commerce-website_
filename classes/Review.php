<?php
class Review
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAll()
    {
        $sql = "SELECT reviews.*, products.name AS product_name,
                       users.first_name, users.last_name
                FROM reviews
                JOIN products ON products.id = reviews.product_id
                JOIN users ON users.id = reviews.user_id
                ORDER BY reviews.id DESC";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $statement = $this->pdo->prepare('SELECT * FROM reviews WHERE id = ?');
        $statement->execute([$id]);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function hide($id)
    {
        $statement = $this->pdo->prepare('UPDATE reviews SET is_visible = 0 WHERE id = ?');
        return $statement->execute([$id]);
    }

    public function restore($id)
    {
        // Do not restore a review with an invalid rating.
        $statement = $this->pdo->prepare(
            'UPDATE reviews SET is_visible = 1 WHERE id = ? AND rating BETWEEN 1 AND 5'
        );
        $statement->execute([$id]);
        return $statement->rowCount() > 0;
    }

    public function delete($id)
    {
        $statement = $this->pdo->prepare('DELETE FROM reviews WHERE id = ?');
        return $statement->execute([$id]);
    }

    public function isValidRating($rating)
    {
        return in_array((string) $rating, ['1', '2', '3', '4', '5'], true);
    }

    // Visible reviews for several toys at once, grouped by product id.
    // Used by the home page card details. Only the first name and the
    // first letter of the last name are returned.
    public function getVisibleForProducts(array $productIds, int $perProduct = 10)
    {
        $productIds = array_values(array_unique(array_map('intval', $productIds)));
        if (empty($productIds)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($productIds), '?'));
        $sql = "SELECT reviews.product_id, reviews.rating, reviews.comment, reviews.created_at,
                       users.first_name, users.last_name
                FROM reviews
                JOIN users ON users.id = reviews.user_id
                WHERE reviews.is_visible = 1
                  AND reviews.rating BETWEEN 1 AND 5
                  AND reviews.product_id IN ($placeholders)
                ORDER BY reviews.created_at DESC, reviews.id DESC";
        $statement = $this->pdo->prepare($sql);
        $statement->execute($productIds);

        $grouped = [];
        foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $id = (int) $row['product_id'];
            if (count($grouped[$id] ?? []) >= $perProduct) {
                continue;
            }
            $grouped[$id][] = [
                'name'    => trim($row['first_name'] . ' ' . mb_substr((string) $row['last_name'], 0, 1) . '.'),
                'rating'  => (int) $row['rating'],
                'comment' => (string) ($row['comment'] ?? ''),
                'date'    => date('M j, Y', strtotime($row['created_at'])),
            ];
        }
        return $grouped;
    }
}
