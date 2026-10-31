<?php

class Dashboard
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getSummary()
    {
         
        $income = $this->pdo->query(
            "SELECT COALESCE(SUM(total), 0) FROM orders WHERE status = 'delivered'"
        )->fetchColumn();
        $orders = $this->pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn();
        $customers = $this->pdo->query(
            "SELECT COUNT(*) FROM users WHERE role = 'customer'"
        )->fetchColumn();
        $categories = $this->pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn();

        return [
            'income' => $income,
            'orders' => $orders,
            'customers' => $customers,
            'categories' => $categories
        ];
    }

    public function getDailyRevenue($startDate, $endDate)
    {
         
        $sql = "SELECT DATE(created_at) AS order_date, COUNT(*) AS orders_count,
                       SUM(total) AS income
                FROM orders
                WHERE status = 'delivered' AND created_at >= ? AND created_at < ?
                GROUP BY DATE(created_at)
                ORDER BY order_date";
        $statement = $this->pdo->prepare($sql);
        $statement->execute([$startDate, $endDate]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRecentOrders()
    {
        $sql = "SELECT orders.id, orders.total, orders.status, orders.created_at,
                       users.first_name, users.last_name
                FROM orders
                JOIN users ON users.id = orders.user_id
                ORDER BY orders.created_at DESC, orders.id DESC
                LIMIT 5";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}
