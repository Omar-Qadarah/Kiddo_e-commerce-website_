<?php

class Order
{
    // Whitelist: the only statuses allowed (same as the ENUM in the database)
    public const STATUSES = ['placed', 'shipped', 'delivered'];

    // Business rule: an order moves forward only, one step at a time
    private const NEXT_STATUS = [
        'placed'    => 'shipped',
        'shipped'   => 'delivered',
        'delivered' => null,          // final step, nothing after it
    ];

    private PDO $pdo;                 // the database connection, shared by all methods

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;            // save the connection we received from init.php
    }

    // LISTING + FILTERING: all orders, or only one status if given
    public function getAllOrders(string $status = ''): array
    {
        $sql = "SELECT o.id, o.total, o.status, o.created_at,
                       u.first_name, u.last_name
                FROM orders o
                JOIN users u ON o.user_id = u.id";
        $params = [];

        // Only filter if the status is in our whitelist
        if (in_array($status, self::STATUSES, true)) {
            $sql .= " WHERE o.status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY o.created_at DESC";     // newest first

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();                   // many rows
    }

    // VIEWING: one order with the customer name, or null if not found
    public function getOrderById(int $id): ?array
    {
        $sql = "SELECT o.*, u.first_name, u.last_name, u.email
                FROM orders o
                JOIN users u ON o.user_id = u.id
                WHERE o.id = ?";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $order = $stmt->fetch();                    // one row (or false)

        return $order ?: null;                      // turn false into null
    }

    // VIEWING ITEMS: the products inside one order
    public function getOrderItems(int $orderId): array
    {
        $sql = "SELECT product_name, unit_price, quantity, line_total
                FROM order_items
                WHERE order_id = ?";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();                   // many rows
    }

    // Helper: which status is allowed after the current one
    public function getNextStatus(string $current): ?string
    {
        return self::NEXT_STATUS[$current] ?? null;
    }

    // CHANGING STATUS: only if it is the correct next step
    public function updateStatus(int $id, string $newStatus): bool
    {
        $order = $this->getOrderById($id);

        if ($order === null) {                      // check 1: the order exists?
            return false;
        }

        if ($this->getNextStatus($order['status']) !== $newStatus) {   // check 2: correct next step?
            return false;
        }

        $stmt = $this->pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        return $stmt->execute([$newStatus, $id]);
    }
}