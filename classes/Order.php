<?php

class Order
{
     
    public const STATUSES = ['placed', 'shipped', 'delivered', 'cancelled'];
    public const TRACKING_STATUSES = ['placed', 'shipped', 'delivered'];

     
    private const NEXT_STATUS = [
        'placed'    => 'shipped',
        'shipped'   => 'delivered',
        'delivered' => null,           
    ];
    
     
    public const DELIVERY_FEE = 2.00;

    private PDO $pdo;                  

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;             
    }

     
    public function getAllOrders(string $status = ''): array
    {
        $sql = "SELECT o.id, o.total, o.status, o.created_at,
                       u.first_name, u.last_name
                FROM orders o
                JOIN users u ON o.user_id = u.id";
        $params = [];

         
        if (in_array($status, self::STATUSES, true)) {
            $sql .= " WHERE o.status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY (o.status = 'delivered') DESC, o.created_at DESC";      

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();                    
    }

     
    public function getOrderById(int $id): ?array
    {
        $sql = "SELECT o.*, u.first_name, u.last_name, u.email
                FROM orders o
                JOIN users u ON o.user_id = u.id
                WHERE o.id = ?";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $order = $stmt->fetch();                     

        return $order ?: null;                       
    }

     
    public function getOrderItems(int $orderId): array
    {
        $sql = "SELECT product_name, unit_price, quantity, line_total
                FROM order_items
                WHERE order_id = ?";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();                    
    }

     
    public function getNextStatus(string $current): ?string
    {
        return self::NEXT_STATUS[$current] ?? null;
    }

     
    public function updateStatus(int $id, string $newStatus): bool
    {
        $order = $this->getOrderById($id);

        if ($order === null) {                       
            return false;
        }

        if ($this->getNextStatus($order['status']) !== $newStatus) {    
            return false;
        }

        $stmt = $this->pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        return $stmt->execute([$newStatus, $id]);
    }

     
    public function cancelOrder(int $id): bool
    {
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare('SELECT status FROM orders WHERE id = ? FOR UPDATE');
            $stmt->execute([$id]);
            $status = $stmt->fetchColumn();
            if (!in_array($status, ['placed', 'shipped'], true)) {
                $this->pdo->rollBack();
                return false;
            }

            $stockStmt = $this->pdo->prepare(
                'UPDATE products p INNER JOIN order_items oi ON oi.product_id = p.id
                 SET p.stock = p.stock + oi.quantity WHERE oi.order_id = ?'
            );
            $stockStmt->execute([$id]);
            $cancelStmt = $this->pdo->prepare("UPDATE orders SET status = 'cancelled' WHERE id = ?");
            $cancelStmt->execute([$id]);
            $this->pdo->commit();
            return true;
        } catch (Throwable $error) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            throw $error;
        }
    }
    
         
    public function getCustomerOrder(int $orderId, int $userId): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
        $stmt->execute([$orderId, $userId]);
        $order = $stmt->fetch();

        return $order ?: null;
    } 
         
    public function getCustomerOrders(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT o.id, o.total, o.status, o.created_at,
                    COALESCE(SUM(oi.quantity), 0) AS toys_count
             FROM orders o
             LEFT JOIN order_items oi ON oi.order_id = o.id
             WHERE o.user_id = ?
             GROUP BY o.id
             ORDER BY o.created_at DESC"
        );
        $stmt->execute([$userId]);

        return $stmt->fetchAll();
    }
    
     

     
    public function create(int $userId, array $cart, string $phone, string $address): int
    {
        $subtotal = $cart['subtotal'];
        $total    = round($subtotal + self::DELIVERY_FEE, 2);

        $this->pdo->beginTransaction();

        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO orders (user_id, subtotal, delivery_fee, total, phone, address_text, latitude, longitude)
                 VALUES (?, ?, ?, ?, ?, ?, 0, 0)"
            );
            $stmt->execute([$userId, $subtotal, self::DELIVERY_FEE, $total, $phone, $address]);
            $orderId = (int) $this->pdo->lastInsertId();

            $stockStmt = $this->pdo->prepare(
                "UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?"
            );
            $itemStmt = $this->pdo->prepare(
                "INSERT INTO order_items (order_id, product_id, product_name, unit_price, quantity, line_total)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );

            foreach ($cart['items'] as $item) {
                 
                $stockStmt->execute([$item['quantity'], $item['id'], $item['quantity']]);

                if ($stockStmt->rowCount() === 0) {
                    throw new Exception($item['name'] . ' is no longer available in this quantity.');
                }

                $itemStmt->execute([
                    $orderId,
                    $item['id'],
                    $item['name'],
                    $item['unit_price'],
                    $item['quantity'],
                    $item['line_total'],
                ]);
            }

            $this->pdo->commit();
            return $orderId;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
