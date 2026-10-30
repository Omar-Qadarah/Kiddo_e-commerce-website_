<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/shop_functions.php';

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    header('Location: ' . BASE_URL . '/cart/index.php');
    exit;
}

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

$checkoutItems = [];
$subtotal = 0.0;

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

    $checkoutItems[] = [
        'id' => $id,
        'name' => (string)$p['name'],
        'category_name' => (string)($p['category_name'] ?? 'Toy'),
        'price' => $finalPrice,
        'quantity' => $qty,
        'image' => shopImageUrl(isset($p['image_path']) ? (string)$p['image_path'] : null),
        'total' => $total,
    ];
}

if (empty($checkoutItems)) {
    header('Location: ' . BASE_URL . '/cart/index.php');
    exit;
}

$shippingFee = $subtotal >= 25.0 ? 0.0 : 3.0;
$grandTotal = $subtotal + $shippingFee;

$userId = $_SESSION['user_id'] ?? null;
$userProfile = null;
if ($userId) {
    $userStmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $userStmt->execute([(int)$userId]);
    $userProfile = $userStmt->fetch(PDO::FETCH_ASSOC);
}

$pageTitle = "Checkout - Kiddo's";
$activePage = 'cart';
$pageStyles = ['cart.css'];
require_once __DIR__ . '/../includes/shop_header.php';
?>

<section class="container checkout-page" style="padding: 40px 0 80px; min-height: 75vh;">
    <h1 style="font-size: 2rem; font-weight: 800; color: #2d3436; margin-bottom: 24px;">Checkout</h1>

    <div style="display: grid; grid-template-columns: 1fr 380px; gap: 36px; align-items: start;">
        <div style="background: #ffffff; border: 1px solid #edf0f5; border-radius: 20px; padding: 30px; box-shadow: 0 4px 16px rgba(0,0,0,0.02);">
            <h2 style="font-size: 1.3rem; font-weight: 800; color: #2d3436; margin-bottom: 20px;">Shipping & Contact Details</h2>

            <form action="<?= BASE_URL ?>/checkout/process.php" method="post" id="checkoutForm">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #2d3436; margin-bottom: 6px;">First Name</label>
                        <input type="text" name="first_name" required value="<?= htmlspecialchars($userProfile['first_name'] ?? '') ?>" style="width: 100%; height: 46px; border: 1px solid #edf0f5; border-radius: 10px; padding: 0 14px; font-size: 0.95rem;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #2d3436; margin-bottom: 6px;">Last Name</label>
                        <input type="text" name="last_name" required value="<?= htmlspecialchars($userProfile['last_name'] ?? '') ?>" style="width: 100%; height: 46px; border: 1px solid #edf0f5; border-radius: 10px; padding: 0 14px; font-size: 0.95rem;">
                    </div>
                </div>

                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #2d3436; margin-bottom: 6px;">Email Address</label>
                    <input type="email" name="email" required value="<?= htmlspecialchars($userProfile['email'] ?? '') ?>" style="width: 100%; height: 46px; border: 1px solid #edf0f5; border-radius: 10px; padding: 0 14px; font-size: 0.95rem;">
                </div>

                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #2d3436; margin-bottom: 6px;">Phone Number</label>
                    <input type="tel" name="phone" required value="<?= htmlspecialchars($userProfile['phone'] ?? '') ?>" placeholder="07XXXXXXXX" style="width: 100%; height: 46px; border: 1px solid #edf0f5; border-radius: 10px; padding: 0 14px; font-size: 0.95rem;">
                </div>

                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #2d3436; margin-bottom: 6px;">Delivery Address</label>
                    <textarea name="address" rows="3" required placeholder="City, neighborhood, street name..." style="width: 100%; border: 1px solid #edf0f5; border-radius: 10px; padding: 12px 14px; font-size: 0.95rem; resize: vertical;"><?= htmlspecialchars($userProfile['address'] ?? '') ?></textarea>
                </div>

                <div style="margin-bottom: 24px;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #2d3436; margin-bottom: 8px;">Payment Method</label>
                    <div style="display: flex; align-items: center; gap: 10px; padding: 14px; border: 1px solid #6c5ce7; border-radius: 12px; background: #faf9ff;">
                        <input type="radio" name="payment_method" value="cod" checked id="codPayment">
                        <label for="codPayment" style="font-weight: 700; color: #2d3436; cursor: pointer; margin: 0;">Cash on Delivery (COD)</label>
                    </div>
                </div>
            </form>
        </div>

        <div style="background: #ffffff; border: 1px solid #edf0f5; border-radius: 20px; padding: 24px; box-shadow: 0 4px 16px rgba(0,0,0,0.02); position: sticky; top: 20px;">
            <h3 style="font-size: 1.25rem; font-weight: 800; color: #2d3436; margin-bottom: 18px;">Order Summary</h3>

            <div style="display: flex; flex-direction: column; gap: 14px; margin-bottom: 20px; max-height: 220px; overflow-y: auto; padding-right: 6px;">
                <?php foreach ($checkoutItems as $item): ?>
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <img src="<?= htmlspecialchars($item['image']) ?>" alt="" style="width: 42px; height: 42px; object-fit: contain; background: #fafbfc; border-radius: 8px; border: 1px solid #edf0f5;">
                            <div>
                                <h4 style="font-size: 0.88rem; font-weight: 700; color: #2d3436; margin: 0; max-width: 170px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars($item['name']) ?></h4>
                                <span style="font-size: 0.75rem; color: #636e72;">Qty: <?= $item['quantity'] ?></span>
                            </div>
                        </div>
                        <strong style="color: #2d3436; font-size: 0.95rem;"><?= shopMoney($item['total']) ?></strong>
                    </div>
                <?php endforeach; ?>
            </div>

            <div style="border-top: 1px solid #edf0f5; padding-top: 14px; margin-bottom: 12px; display: flex; justify-content: space-between; color: #636e72; font-size: 0.95rem;">
                <span>Subtotal</span>
                <strong style="color: #2d3436;"><?= shopMoney($subtotal) ?></strong>
            </div>

            <div style="display: flex; justify-content: space-between; color: #636e72; font-size: 0.95rem; margin-bottom: 14px;">
                <span>Delivery Fee</span>
                <strong style="color: <?= $shippingFee === 0.0 ? '#00b894' : '#2d3436' ?>;">
                    <?= $shippingFee === 0.0 ? 'Free Delivery' : shopMoney($shippingFee) ?>
                </strong>
            </div>

            <div style="border-top: 1px solid #edf0f5; padding-top: 14px; margin-bottom: 18px; display: flex; justify-content: space-between; align-items: center;">
                <span style="font-weight: 800; color: #2d3436; font-size: 1.1rem;">Total to Pay</span>
                <strong style="font-size: 1.35rem; color: #2d3436;"><?= shopMoney($grandTotal) ?></strong>
            </div>

            <div data-assistant-box 
                 data-context="checkout" 
                 data-limit="1" 
                 data-product-ids="<?= implode(',', array_column($checkoutItems, 'id')) ?>">
            </div>

            <button type="submit" form="checkoutForm" class="btn btn-primary" style="width: 100%; padding: 14px; font-weight: 700; border-radius: 12px; font-size: 1rem; border: none; cursor: pointer;">
                Place Order (<?= shopMoney($grandTotal) ?>)
            </button>
        </div>
    </div>
</section>

<script>
    window.BASE_URL = '<?= BASE_URL ?>';
    window.CSRF_TOKEN = '<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>';
</script>
<script src="<?= BASE_URL ?>/assets/js/shopping_assistant.js" defer></script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>