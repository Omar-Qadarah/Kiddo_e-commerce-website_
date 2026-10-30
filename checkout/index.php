<?php
// Checkout page: phone, address and the order summary. Login is required.
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../classes/Cart.php';
require_once __DIR__ . '/../classes/Order.php';

$cart    = new Cart($pdo);
$details = $cart->getDetails();

// Nothing to buy, back to the cart
if (empty($details['items'])) {
    header('Location: ' . BASE_URL . '/cart/index.php');
    exit;
}

$total = round($details['subtotal'] + Order::DELIVERY_FEE, 2);

// Errors and typed values sent back by place_order.php
$errors = $_SESSION['checkout_errors'] ?? [];
$old    = $_SESSION['checkout_old'] ?? [];
unset($_SESSION['checkout_errors'], $_SESSION['checkout_old']);

// The customer, to show who is ordering and fill the phone
$stmt = $pdo->prepare('SELECT first_name, email, phone FROM users WHERE id = ?');
$stmt->execute([$currentUserId]);
$customer = $stmt->fetch();

if (!isset($old['phone'])) {
    $old['phone'] = (string) $customer['phone'];
}

$pageTitle  = 'Checkout';
$activePage = 'cart';
$pageStyles = ['cart.css'];
require_once __DIR__ . '/../includes/shop_header.php';
?>

<section class="container cart-page">
    <a class="checkout-back" href="<?= BASE_URL ?>/cart/index.php">← Back to cart</a>
    <h1>Checkout</h1>

    <?php if ($errors): ?>
        <ul class="cart-flash is-error">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form class="cart-layout" action="<?= BASE_URL ?>/checkout/place_order.php" method="post">
        <div class="checkout-card">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

            <h2>Delivery details</h2>
            <p class="checkout-as">
                Ordering as <strong><?= htmlspecialchars($customer['first_name']) ?></strong>
                · <?= htmlspecialchars($customer['email']) ?>
            </p>

            <label for="phone">Phone number</label>
            <input id="phone" name="phone" type="tel" inputmode="tel" autocomplete="tel"
                   placeholder="07XXXXXXXX" maxlength="20" required
                   value="<?= htmlspecialchars($old['phone'] ?? '') ?>">
            <p class="field-hint">We'll call this number to arrange the delivery.</p>

            <label for="address">Delivery address</label>
            <textarea id="address" name="address" rows="3" maxlength="255" required
                      autocomplete="street-address"
                      placeholder="City, area, street, building"><?= htmlspecialchars($old['address'] ?? '') ?></textarea>
            <p class="field-hint">Add a landmark to help the driver, e.g. near Safeway.</p>

            <p class="cart-note">💵 Cash on delivery. Pay when your toys arrive.</p>
        </div>

        <aside class="cart-summary">
            <h2>Your order</h2>

            <?php foreach ($details['items'] as $item): ?>
                <p><span><?= $item['quantity'] ?> × <?= htmlspecialchars($item['name']) ?></span> <span><?= shopMoney($item['line_total']) ?></span></p>
            <?php endforeach; ?>

            <hr>
            <p><span>Subtotal</span> <strong><?= shopMoney($details['subtotal']) ?></strong></p>
            <p><span>Delivery</span> <strong><?= shopMoney(Order::DELIVERY_FEE) ?></strong></p>
            <p class="checkout-total"><span>Total</span> <strong><?= shopMoney($total) ?></strong></p>

            <button class="btn btn-primary btn-block" type="submit">Place order · <?= shopMoney($total) ?></button>
        </aside>
    </form>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>