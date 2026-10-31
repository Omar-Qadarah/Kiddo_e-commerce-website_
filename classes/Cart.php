<?php
require_once __DIR__ . '/ToyCatalog.php';
require_once __DIR__ . '/../includes/shop_functions.php';

 
 
 
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

     
    public function removeItem(int $productId): void
    {
        unset($_SESSION['cart'][$productId]);
        $this->updateCount();
    }

     
    public function updateItem(int $productId, int $quantity): array
    {
         
        if (!isset($_SESSION['cart'][$productId])) {
            return ['ok' => false, 'message' => 'This toy is not in your cart.'];
        }

         
        if ($quantity < 1) {
            $this->removeItem($productId);
            return ['ok' => true, 'message' => 'Toy removed from your cart.'];
        }

         
        $stmt = $this->pdo->prepare('SELECT stock FROM products WHERE id = ? AND is_active = 1');
        $stmt->execute([$productId]);
        $stock = $stmt->fetchColumn();

        if ($stock === false || (int) $stock < 1) {
            $this->removeItem($productId);
            return ['ok' => false, 'message' => 'Sorry, this toy is no longer available.'];
        }

        $_SESSION['cart'][$productId] = min($quantity, (int) $stock);
        $this->updateCount();

        return ['ok' => true, 'message' => 'Cart updated.'];
    }

         
    public function clear(): void
    {
        $_SESSION['cart'] = [];
        $this->updateCount();
    }

     
    public function getItems(): array
    {
        return $_SESSION['cart'];
    }

     
    public function getDetails(): array
    {
        $catalog  = new ToyCatalog($this->pdo);
        $items    = [];
        $subtotal = 0;

                
            foreach (array_reverse($this->getItems(), true) as $productId => $quantity) {
            $toy = $catalog->getToy((int) $productId);

             
            if (!$toy || (int) $toy['stock'] < 1) {
                $this->removeItem((int) $productId);
                continue;
            }

             
            $quantity = min((int) $quantity, (int) $toy['stock']);
            $_SESSION['cart'][$productId] = $quantity;

             
            $unitPrice = shopFinalPrice($toy);
            $lineTotal = round($unitPrice * $quantity, 2);

            $items[] = [
                'id'         => (int) $toy['id'],
                'name'       => $toy['name'],
                'image'      => $toy['image'],
                'price'      => (float) $toy['price'],
                'unit_price' => $unitPrice,
                'quantity'   => $quantity,
                'stock'      => (int) $toy['stock'],
                'line_total' => $lineTotal,
            ];

            $subtotal += $lineTotal;
        }

        $this->updateCount();

        return ['items' => $items, 'subtotal' => round($subtotal, 2)];
    }


     
     
    public function getSuggestions(int $limit = 4): array
    {
        $catalog = new ToyCatalog($this->pdo);
        $toys    = array_merge($catalog->getDiscountedToys(8), $catalog->getNewArrivals(8));

        $suggestions = [];
        foreach ($toys as $toy) {
            $id = (int) $toy['id'];

            if (isset($_SESSION['cart'][$id]) || isset($suggestions[$id]) || (int) $toy['stock'] < 1) {
                continue;
            }

            $suggestions[$id] = $toy;

            if (count($suggestions) === $limit) {
                break;
            }
        }

        return array_values($suggestions);
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
