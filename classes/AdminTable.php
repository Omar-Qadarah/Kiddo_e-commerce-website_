<?php
class AdminTable
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getPage($type, $request)
    {
        $search = isset($request['search']) && is_string($request['search']) ? trim($request['search']) : '';
        $search = substr($search, 0, 120);
        $size = isset($request['per_page']) && is_scalar($request['per_page']) ? (string) $request['per_page'] : '5';
        $size = in_array($size, ['5', '10', '20', '50'], true) ? (int) $size : 5;
        $requestedPage = isset($request['page']) && is_string($request['page']) && ctype_digit($request['page']) ? (int) $request['page'] : 1;
        $where = [];
        $params = [];
        $filters = [];
        $query = ['search' => $search, 'per_page' => $size];

        switch ($type) {
            case 'products':
                $select = 'p.*, c.name AS category_name';
                $from = 'products p INNER JOIN categories c ON c.id = p.category_id';
                $order = 'p.id DESC';
                $searchColumns = ['p.name'];
                $category = isset($request['category_id']) && is_string($request['category_id']) && ctype_digit($request['category_id']) ? $request['category_id'] : '';
                $stock = isset($request['stock']) && is_string($request['stock']) && in_array($request['stock'], ['in_stock', 'out_of_stock'], true) ? $request['stock'] : '';
                if ($category !== '') { $where[] = 'p.category_id = ?'; $params[] = $category; }
                if ($stock !== '') { $where[] = $stock === 'in_stock' ? 'p.stock > 0' : 'p.stock = 0'; }
                $options = ['' => 'All categories'];
                foreach ($this->pdo->query('SELECT id, name FROM categories ORDER BY name')->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    $options[$row['id']] = $row['name'];
                }
                $filters[] = ['name' => 'category_id', 'label' => 'Category', 'value' => $category, 'options' => $options];
                $filters[] = ['name' => 'stock', 'label' => 'Stock', 'value' => $stock, 'options' => ['' => 'All stock', 'in_stock' => 'In stock', 'out_of_stock' => 'Out of stock']];
                $query += ['category_id' => $category, 'stock' => $stock];
                break;
            case 'orders':
                $select = 'o.id, o.total, o.status, o.created_at, u.first_name, u.last_name';
                $from = 'orders o JOIN users u ON o.user_id = u.id';
                $order = 'o.created_at DESC, o.id DESC';
                $searchColumns = ['CAST(o.id AS CHAR)', "CONCAT(u.first_name, ' ', u.last_name)"];
                $status = isset($request['status']) && is_string($request['status']) && in_array($request['status'], ['placed', 'shipped', 'delivered', 'cancelled'], true) ? $request['status'] : '';
                if ($status !== '') { $where[] = 'o.status = ?'; $params[] = $status; }
                $query['status'] = $status;
                break;
            case 'users':
                $select = 'u.id, u.first_name, u.last_name, u.email, u.phone, u.role, u.is_active, u.created_at, (SELECT COUNT(*) FROM orders o WHERE o.user_id = u.id) AS orders_count';
                $from = 'users u';
                $order = 'u.created_at DESC, u.id DESC';
                $searchColumns = ["CONCAT(u.first_name, ' ', u.last_name)", 'u.email', 'u.phone'];
                $where[] = 'u.is_active = 1';
                break;
            case 'categories':
                $select = 'c.*'; $from = 'categories c'; $order = 'c.id DESC'; $searchColumns = ['c.name'];
                break;
            case 'age_groups':
                $select = 'g.*'; $from = 'age_groups g'; $order = 'g.sort_order ASC, g.id DESC'; $searchColumns = ['g.name'];
                break;
            case 'discounts':
                $select = 'd.*, p.name AS product_name, c.name AS category_name';
                $from = 'discounts d LEFT JOIN products p ON p.id = d.product_id LEFT JOIN categories c ON c.id = d.category_id';
                $order = 'd.id DESC'; $searchColumns = ['d.title', 'p.name', 'c.name'];
                break;
            case 'reviews':
                $select = 'r.*, p.name AS product_name, u.first_name, u.last_name';
                $from = 'reviews r JOIN products p ON p.id = r.product_id JOIN users u ON u.id = r.user_id';
                $order = 'r.id DESC'; $searchColumns = ['p.name', "CONCAT(u.first_name, ' ', u.last_name)", 'r.comment'];
                break;
            case 'order_items':
                $select = 'i.product_name, i.unit_price, i.quantity, i.line_total';
                $from = 'order_items i'; $order = 'i.id ASC'; $searchColumns = ['i.product_name'];
                $where[] = 'i.order_id = ?'; $params[] = (int) ($request['id'] ?? 0);
                $query['id'] = (int) ($request['id'] ?? 0);
                break;
            default:
                throw new InvalidArgumentException('Unknown admin table.');
        }
        if ($search !== '') {
            $parts = [];
            foreach ($searchColumns as $column) {
                $parts[] = $column . ' LIKE ?';
                $params[] = '%' . $search . '%';
            }
            $where[] = '(' . implode(' OR ', $parts) . ')';
        }
        $condition = $where ? ' WHERE ' . implode(' AND ', $where) : '';
        $statement = $this->pdo->prepare('SELECT COUNT(*) FROM ' . $from . $condition);
        $statement->execute($params);
        $total = (int) $statement->fetchColumn();
        $pages = max(1, (int) ceil($total / $size));
        $page = min(max(1, $requestedPage), $pages);
        $offset = ($page - 1) * $size;
        $statement = $this->pdo->prepare('SELECT ' . $select . ' FROM ' . $from . $condition . ' ORDER BY ' . $order . ' LIMIT ? OFFSET ?');
        foreach ($params as $index => $value) { $statement->bindValue($index + 1, $value); }
        $statement->bindValue(count($params) + 1, $size, PDO::PARAM_INT);
        $statement->bindValue(count($params) + 2, $offset, PDO::PARAM_INT);
        $statement->execute();
        return ['rows' => $statement->fetchAll(PDO::FETCH_ASSOC), 'total' => $total, 'page' => $page, 'pages' => $pages,
            'size' => $size, 'start' => $total ? $offset + 1 : 0, 'end' => min($offset + $size, $total),
            'search' => $search, 'query' => $query, 'filters' => $filters, 'type' => $type];
    }
}
