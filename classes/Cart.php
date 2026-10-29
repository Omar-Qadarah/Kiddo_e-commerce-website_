<?php

// Shopping cart kept in the session (no table needed until checkout).
// $_SESSION['cart']       = [product_id => quantity, ...]
// $_SESSION['cart_count'] = total number of items (read by includes/navbar.php)
class Cart
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;

        if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    // Adds a toy to the cart. Never goes above the stock.
    // Returns how many were really added (0 = nothing added) and a message.
    public function add(int $productId, int $quantity): array
    {
        if ($quantity < 1) {
            return ['added' => 0, 'message' => 'Please choose a quantity of at least 1.'];
        }

        $stmt = $this->pdo->prepare(
            'SELECT id, name, stock FROM products WHERE id = ? AND is_active = 1'
        );
        $stmt->execute([$productId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            return ['added' => 0, 'message' => 'This toy is not available.'];
        }

        $inCart = $this->getQuantity($productId);
        $canAdd = (int) $product['stock'] - $inCart;

        if ($canAdd <= 0) {
            return ['added' => 0, 'message' => 'You already have all available pieces of this toy in your cart.'];
        }

        $added = min($quantity, $canAdd);
        $_SESSION['cart'][$productId] = $inCart + $added;
        $this->updateCount();

        $message = $added . ' × ' . $product['name'] . ' added to your cart.';
        if ($added < $quantity) {
            $message .= ' Only ' . $added . ' more left in stock.';
        }

        return ['added' => $added, 'message' => $message];
    }

    // [product_id => quantity]
    public function getItems(): array
    {
        return $_SESSION['cart'];
    }

    public function getQuantity(int $productId): int
    {
        return (int) ($_SESSION['cart'][$productId] ?? 0);
    }

    public function getCount(): int
    {
        return array_sum($_SESSION['cart']);
    }

    private function updateCount(): void
    {
        $_SESSION['cart_count'] = $this->getCount();
    }
}
