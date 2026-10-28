<?php
declare(strict_types= 1)
class Discount
{
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAll()
    {
        $sql = "SELECT * FROM discounts ORDER BY id DESC";

        $statement = $this->pdo->query($sql);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(
        $title,
        $percentage,
        $product_id = null,
        $category_id = null,
        $starts_at = null,
        $ends_at = null
    ) {
        $sql = "INSERT INTO discounts
            (title, percentage, product_id, category_id, starts_at, ends_at)
            VALUES (?, ?, ?, ?, ?, ?)";

        $statement = $this->pdo->prepare($sql);

        return $statement->execute([
           $title,
           $percentage,
           $product_id,
           $category_id,
           $starts_at,
           $ends_at
        ]);
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM discounts WHERE id = ?";

        $statement = $this->pdo->prepare($sql);

        $statement->execute([$id]);

        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function update(
        $id,
        $title,
        $percentage,
        $product_id = null,
        $category_id = null,
        $starts_at = null,
        $ends_at = null
    ) {
        $sql = "UPDATE discounts
                SET title = ?,
                percentage = ?,
                product_id = ?,
                category_id = ?,
                starts_at = ?,
                ends_at = ?
                WHERE id = ?";

        $statement = $this->pdo->prepare($sql);

        return $statement->execute([
             $title,
             $percentage,
             $product_id,
             $category_id,
             $starts_at,
             $ends_at,
             $id
        ]);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM discounts WHERE id = ?";

        $statement = $this->pdo->prepare($sql);

        return $statement->execute([$id]);
    }

    public function setActive($id, $is_active)
    {
        $sql = "UPDATE discounts
        SET is_active = ?
        WHERE id = ?";

        $statement = $this->pdo->prepare($sql);

        return $statement->execute([$is_active, $id]);
    }
}

?>