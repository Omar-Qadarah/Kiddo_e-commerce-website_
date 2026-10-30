<?php
// Cart page: shows the toys in the cart, lets the customer
// change quantities or remove toys, then go to checkout.
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../classes/Cart.php';

$cart     = new Cart($pdo);
$details  = $cart->getDetails();
$items    = $details['items'];
$subtotal = $details['subtotal'];
$suggestions = $cart->getSuggestions();

// Show the message once, then forget it
$flash = $_SESSION['cart_flash'] ?? null;
unset($_SESSION['cart_flash']);

$pageTitle  = 'Your cart';
$activePage = 'cart';
$pageStyles = ['cart.css'];
require_once __DIR__ . '/../includes/shop_header.php';
?>

<section class="container cart-page">
    <h1>Your cart</h1>

    <?php if ($flash): ?>
        <p class="cart-flash is-<?= $flash['type'] ?>"><?= htmlspecialchars($flash['message']) ?></p>
    <?php endif; ?>

    <?php if (empty($items)): ?>
        <div class="cart-empty">
            <p>Your cart is empty.</p>
            <a class="btn btn-primary" href="<?= BASE_URL ?>/products/index.php">Browse toys</a>
        </div>
    <?php else: ?>
        <div class="cart-layout">
            <ul class="cart-list">
                <?php foreach ($items as $item): ?>
                    <li class="cart-item">
                        <img src="<?= htmlspecialchars(shopImageUrl($item['image'])) ?>" alt="<?= htmlspecialchars($item['name']) ?>">

                        <div class="cart-item-info">
                            <a href="<?= BASE_URL ?>/products/details.php?id=<?= $item['id'] ?>"><?= htmlspecialchars($item['name']) ?></a>
                            <p>
                                <?= shopMoney($item['unit_price']) ?>
                                <?php if ($item['unit_price'] < $item['price']): ?>
                                    <s><?= shopMoney($item['price']) ?></s>
                                <?php endif; ?>
                            </p>
                        </div>

                        <form class="cart-qty" action="<?= BASE_URL ?>/cart/update.php" method="post">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                            <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                            <button type="submit" name="quantity" value="<?= $item['quantity'] - 1 ?>" aria-label="Decrease quantity">−</button>
                            <span><?= $item['quantity'] ?></span>
                            <button type="submit" name="quantity" value="<?= $item['quantity'] + 1 ?>" aria-label="Increase quantity" <?= $item['quantity'] >= $item['stock'] ? 'disabled' : '' ?>>+</button>
                        </form>

                        <strong class="cart-line-total"><?= shopMoney($item['line_total']) ?></strong>

                        <form action="<?= BASE_URL ?>/cart/remove.php" method="post">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                            <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                            <button class="cart-remove" type="submit" aria-label="Remove <?= htmlspecialchars($item['name']) ?>" title="Remove">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/>
                                </svg>
                            </button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>

            <aside class="cart-summary">
                <h2>Order summary</h2>
                <p><span>Subtotal</span> <strong><?= shopMoney($subtotal) ?></strong></p>
                <p class="cart-note">Delivery fee is added at checkout. Cash on delivery.</p>
                <a class="btn btn-primary btn-block" href="<?= BASE_URL ?>/checkout/index.php">Checkout</a>
            </aside>
        </div>
    <?php endif; ?>
    
   
    <?php if ($suggestions): ?>
        <section class="cart-suggestions">
            <h2><?= empty($items) ? 'Start with these favorites' : 'More toys to grow little minds' ?></h2>
            <div class="toy-grid">
                <?php foreach ($suggestions as $toy): ?>
                    <?php
                    $cardMode     = 'shop';
                    $cardReturnTo = 'cart';
                    require __DIR__ . '/../includes/toy_card.php';
                    ?>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>