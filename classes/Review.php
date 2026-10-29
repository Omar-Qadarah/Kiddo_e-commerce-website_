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
}
