<?php
declare(strict_types= 1);
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

     
    public function validate($title, $percentage, $product_id, $category_id, $starts_at, $ends_at)
    {
        $errors = [];
        if ($title === '' || mb_strlen($title, 'UTF-8') > 100) {
            $errors[] = 'Title is required and must not exceed 100 characters.';
        }
        if (!is_numeric($percentage) || $percentage <= 0 || $percentage > 100) {
            $errors[] = 'Percentage must be greater than 0 and up to 100.';
        } elseif (round((float) $percentage, 2) != (float) $percentage) {
            $errors[] = 'Percentage must have at most two decimal places.';
        }

        $validDates = true;
        foreach ([$starts_at, $ends_at] as $date) {
            if ($date === '') {
                continue;
            }
            if (!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/D', $date)) {
                $errors[] = 'Please enter valid dates in YYYY-MM-DD format.';
                $validDates = false;
                continue;
            }
            $parsed = DateTime::createFromFormat('!Y-m-d', $date);
            if (!$parsed || $parsed->format('Y-m-d') !== $date || $date < '1000-01-01') {
                $errors[] = 'Please enter valid dates in YYYY-MM-DD format.';
                $validDates = false;
            }
        }
        if ($validDates && $starts_at !== '' && $ends_at !== '' && $ends_at < $starts_at) {
            $errors[] = 'End date must not be before start date.';
        }

         
        if (($product_id === '' && $category_id === '') ||
            ($product_id !== '' && $category_id !== '')) {
            $errors[] = 'Choose either a product or a category, not both.';
        }
        if ($product_id !== '') {
            $statement = $this->pdo->prepare('SELECT id FROM products WHERE id = ?');
            $statement->execute([$product_id]);
            if (!ctype_digit($product_id) || !$statement->fetch(PDO::FETCH_ASSOC)) {
                $errors[] = 'Please choose an existing product.';
            }
        }
        if ($category_id !== '') {
            $statement = $this->pdo->prepare('SELECT id FROM categories WHERE id = ?');
            $statement->execute([$category_id]);
            if (!ctype_digit($category_id) || !$statement->fetch(PDO::FETCH_ASSOC)) {
                $errors[] = 'Please choose an existing category.';
            }
        }
        return $errors;
    }
}
