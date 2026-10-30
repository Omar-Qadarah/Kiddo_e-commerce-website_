<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/shop_functions.php';

$cart = $_SESSION['cart'] ?? [];
$cartItems = [];
$subtotal = 0.0;

if (!empty($cart)) {
    $ids = array_map('intval', array_keys($cart));
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    $stmt = $pdo->prepare("SELECT p.*, c.name AS category_name,
                            COALESCE(
                                (SELECT image_path FROM images 
                                 WHERE product_id = p.id 
                                 ORDER BY is_primary DESC, sort_order ASC, image_id ASC 
                                 LIMIT 1),
                                p.image
                            ) AS image_path
                            FROM products p
                            LEFT JOIN categories c ON c.id = p.category_id
                            WHERE p.id IN ($placeholders) AND p.is_active = 1");
    $stmt->execute($ids);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($products as $p) {
        $id = (int)$p['id'];
        $qty = (int)($cart[$id]['quantity'] ?? 1);
        $stock = (int)$p['stock'];

        if ($qty > $stock) {
            $qty = $stock;
            $_SESSION['cart'][$id]['quantity'] = $qty;
        }

        if ($qty <= 0) {
            unset($_SESSION['cart'][$id]);
            continue;
        }

        $finalPrice = shopFinalPrice($p);
        $total = $finalPrice * $qty;
        $subtotal += $total;

        $cartItems[] = [
            'id' => $id,
            'name' => (string)$p['name'],
            'category_name' => (string)($p['category_name'] ?? 'Toy'),
            'price' => (float)$p['price'],
            'final_price' => $finalPrice,
            'image' => shopImageUrl(isset($p['image_path']) ? (string)$p['image_path'] : null),
            'quantity' => $qty,
            'stock' => $stock,
            'total' => $total,
        ];
    }

    if (empty($_SESSION['cart'])) {
        $cart = [];
        $cartItems = [];
        $subtotal = 0.0;
    }
}

$pageTitle = "Shopping Cart - Kiddo's";
$activePage = 'cart';
$pageStyles = ['cart.css'];
require_once __DIR__ . '/../includes/shop_header.php';
?>

<section class="container cart-page" style="padding: 40px 0 80px; min-height: 75vh;">
    <h1 style="font-size: 2rem; font-weight: 800; color: #2d3436; margin-bottom: 24px;">Shopping Cart</h1>

    <?php if (empty($cartItems)): ?>
        <div style="background: #ffffff; border: 1px solid #edf0f5; border-radius: 20px; padding: 60px 20px; text-align: center; box-shadow: 0 4px 16px rgba(0,0,0,0.02);">
            <div style="font-size: 3.5rem; margin-bottom: 16px;" aria-hidden="true">🛒</div>
            <h2 style="font-size: 1.5rem; font-weight: 700; color: #2d3436; margin-bottom: 8px;">Your cart is empty</h2>
            <p style="color: #636e72; margin-bottom: 24px;">Looks like you haven't added any toys to your cart yet.</p>
            <a href="<?= BASE_URL ?>/products/index.php" class="btn btn-primary" style="padding: 12px 30px; font-weight: 700; border-radius: 12px; text-decoration: none;">Explore Toys</a>
        </div>
    <?php else: ?>
        <div style="display: grid; grid-template-columns: 1fr 340px; gap: 30px; align-items: start;">
            <div style="background: #ffffff; border: 1px solid #edf0f5; border-radius: 20px; padding: 24px; box-shadow: 0 4px 16px rgba(0,0,0,0.02);">
                <div style="display: flex; flex-direction: column; gap: 20px;">
                    <?php foreach ($cartItems as $item): ?>
                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 16px; padding-bottom: 20px; border-bottom: 1px solid #f1f2f6;">
                            <div style="display: flex; align-items: center; gap: 16px;">
                                <div style="width: 70px; height: 70px; background: #fafbfc; border: 1px solid #edf0f5; border-radius: 12px; display: flex; align-items: center; justify-content: center; padding: 6px; flex-shrink: 0;">
                                    <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" style="max-height: 100%; max-width: 100%; object-fit: contain;">
                                </div>
                                <div>
                                    <span style="font-size: 0.75rem; color: #6c5ce7; font-weight: 700;"><?= htmlspecialchars($item['category_name']) ?></span>
                                    <h3 style="font-size: 1rem; font-weight: 700; color: #2d3436; margin: 2px 0 6px 0;">
                                        <a href="<?= BASE_URL ?>/products/details.php?id=<?= $item['id'] ?>" style="text-decoration: none; color: inherit;">
                                            <?= htmlspecialchars($item['name']) ?>
                                        </a>
                                    </h3>
                                    <strong style="color: #2d3436; font-size: 0.95rem;"><?= shopMoney($item['final_price']) ?></strong>
                                </div>
                            </div>

                            <div style="display: flex; align-items: center; gap: 20px;">
                                <form action="<?= BASE_URL ?>/cart/update.php" method="post" style="display: flex; align-items: center; gap: 8px; margin: 0;">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                    <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                                    <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" max="<?= $item['stock'] ?>" style="width: 60px; height: 38px; border: 1px solid #edf0f5; border-radius: 8px; text-align: center; font-weight: 700;" onchange="this.form.submit()">
                                </form>

                                <strong style="color: #2d3436; font-size: 1.05rem; min-width: 80px; text-align: right;">
                                    <?= shopMoney($item['total']) ?>
                                </strong>

                                <form action="<?= BASE_URL ?>/cart/remove.php" method="post" style="margin: 0;">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                    <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                                    <button type="submit" style="background: transparent; border: none; color: #ff7675; cursor: pointer; padding: 6px; font-size: 1.1rem; line-height: 1;" title="Remove">
                                        &times;
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div data-assistant-box 
                     data-context="cart" 
                     data-limit="3" 
                     data-product-ids="<?= implode(',', array_column($cartItems, 'id')) ?>">
                </div>
            </div>

            <div style="background: #ffffff; border: 1px solid #edf0f5; border-radius: 20px; padding: 24px; box-shadow: 0 4px 16px rgba(0,0,0,0.02); position: sticky; top: 20px;">
                <h3 style="font-size: 1.25rem; font-weight: 800; color: #2d3436; margin-bottom: 20px;">Order Summary</h3>
                
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; color: #636e72;">
                    <span>Subtotal</span>
                    <strong style="color: #2d3436;"><?= shopMoney($subtotal) ?></strong>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; color: #636e72;">
                    <span>Shipping</span>
                    <span style="color: #00b894; font-weight: 700;">Calculated at checkout</span>
                </div>

                <div style="border-top: 1px solid #edf0f5; padding-top: 16px; margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-weight: 800; color: #2d3436; font-size: 1.1rem;">Estimated Total</span>
                    <strong style="font-size: 1.35rem; color: #2d3436;"><?= shopMoney($subtotal) ?></strong>
                </div>

                <a href="<?= BASE_URL ?>/checkout/index.php" class="btn btn-primary" style="display: block; width: 100%; text-align: center; padding: 14px; font-weight: 700; border-radius: 12px; text-decoration: none;">
                    Proceed to Checkout
                </a>
            </div>
        </div>
    <?php endif; ?>
</section>

<script>
    window.BASE_URL = '<?= BASE_URL ?>';
    window.CSRF_TOKEN = '<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>';
</script>
<script src="<?= BASE_URL ?>/assets/js/shopping_assistant.js" defer></script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>