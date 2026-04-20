<?php
include 'connection.php';
require_once 'coupon_config.php';

$customerId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($customerId <= 0) {
    die("Invalid customer ID");
}

// Fetch customer
$cstmt = mysqli_prepare($con, "SELECT * FROM customers WHERE id = ?");
mysqli_stmt_bind_param($cstmt, "i", $customerId);
mysqli_stmt_execute($cstmt);
$cresult = mysqli_stmt_get_result($cstmt);
$customer = mysqli_fetch_assoc($cresult);
mysqli_stmt_close($cstmt);

if (!$customer) {
    die("Customer not found.");
}

// Fetch cart items
$cartQuery = "
    SELECT c.id as cart_id, p.id as product_id, p.product_name, p.provider_name, p.price, p.image 
    FROM cart c 
    JOIN product p ON c.product_id = p.id 
    WHERE c.customer_id = ?
";
$stmt = mysqli_prepare($con, $cartQuery);
mysqli_stmt_bind_param($stmt, "i", $customerId);
mysqli_stmt_execute($stmt);
$cartItems = mysqli_stmt_get_result($stmt);

$totalPrice = 0;
$items = [];
while ($row = mysqli_fetch_assoc($cartItems)) {
    $items[] = $row;
    $totalPrice += (float) $row['price'];
}
mysqli_stmt_close($stmt);

$couponInput = $_GET['coupon'] ?? '';
$couponCode = normalize_coupon_code($couponInput);
$discountPercent = coupon_discount_percent($couponCode);
$discountAmount = round(($totalPrice * $discountPercent) / 100, 2);
$finalPrice = max($totalPrice - $discountAmount, 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Service Cart - Multi-Service & Multi-Fixing</title>
    <link rel="stylesheet" href="app_theme.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="customer_dashboard.css">
    <style>
        body { font-family: var(--app-font); color: var(--app-ink); margin: 0;}
        @keyframes flowBg { 0% { background-position: 0% 50%; } 100% { background-position: 100% 50%; } }
        
        .cart-container { padding: 40px; max-width: 1000px; margin: 60px auto; border-radius: 32px; background: rgba(255, 255, 255, 0.55); backdrop-filter: saturate(180%) blur(40px); border: 1px solid rgba(255, 255, 255, 0.8); box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08); }
        .cart-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 1px solid rgba(0,0,0,0.05); padding-bottom: 15px;}
        .cart-header h2 { color: #1d1d1f; margin:0; font-weight: 700; letter-spacing: -1px;}
        .cart-item { display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(0,0,0,0.05); padding: 20px 0; }
        .cart-item:last-child { border-bottom: none; }
        .cart-item img { width: 80px; height: 80px; border-radius: 16px; object-fit: cover; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .item-info { flex: 1; margin-left: 20px; }
        .item-info h4 { margin: 0 0 5px; color: #1d1d1f; font-size: 18px; font-weight: 600; letter-spacing: -0.5px;}
        .item-info p { margin: 0; color: #555; font-size: 0.9em; }
        .item-price { font-weight: 700; color: #1d1d1f; font-size: 18px; width: 100px; text-align: right;}
        .item-schedule { background: rgba(255,255,255,0.7); padding: 15px; border-radius: 16px; margin-left: 20px; border: 1px solid rgba(0,0,0,0.05);}
        .item-schedule label { font-size: 0.85em; font-weight: 600; color: #1d1d1f; display: block; margin-bottom: 6px;}
        .item-schedule input { padding: 8px 12px; border: 1px solid rgba(0,0,0,0.1); border-radius: 10px; margin-bottom: 8px; width: 140px; background: rgba(255,255,255,0.9); color: #1d1d1f; outline:none; transition:0.3s;}
        .item-schedule input:focus { border-color: #007aff; box-shadow: 0 0 0 3px rgba(0, 122, 255, 0.2); background: #ffffff;}
        .remove-btn { color: #ff3b30; text-decoration: none; font-size: 0.9em; font-weight: 600; margin-top: 10px; display: inline-block; transition:0.3s;}
        .remove-btn:hover { color: #d32f2f; }
        .cart-total { display: flex; justify-content: flex-end; align-items: center; margin-top: 30px; font-size: 1.4em; font-weight: 700; color: #1d1d1f;}
        .checkout-btn { display: inline-block; color: #fff; padding: 14px 30px; border-radius: 99px; text-decoration: none; margin-left: 20px; border: none; cursor: pointer; font-size: 16px; font-weight: 600; background: #000; transition: 0.3s;}
        .checkout-btn:hover { background: #333; transform: scale(0.98); }
        .coupon-row { display: flex; justify-content: space-between; align-items: center; gap: 18px; margin-top: 22px; padding: 14px; border-radius: 16px; background: rgba(255,255,255,0.6); border: 1px solid rgba(0,0,0,0.05);}
        .coupon-form { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }
        .coupon-form input { padding: 10px 12px; border-radius: 10px; border: 1px solid rgba(0,0,0,0.15); min-width: 180px; text-transform: uppercase; }
        .coupon-form button { border: none; border-radius: 10px; padding: 10px 16px; font-weight: 600; background: #1e3a6e; color: #fff; cursor: pointer; }
        .coupon-help { font-size: 13px; color: #444; }
        .coupon-ok { color: #0f9d58; font-weight: 600; font-size: 13px; }
        .coupon-bad { color: #d93025; font-weight: 600; font-size: 13px; }
        .total-breakdown { margin-top: 14px; max-width: 360px; margin-left: auto; }
        .total-line { display: flex; justify-content: space-between; margin: 8px 0; font-size: 18px; }
        .total-line.discount { color: #0f9d58; }
        .total-line.grand { font-size: 32px; font-weight: 800; letter-spacing: -0.5px; border-top: 1px solid rgba(0,0,0,0.08); padding-top: 10px; margin-top: 12px; }
    </style>
</head>
<body>

<?php include "cnav.php"; ?>

<div class="cart-container">
    <div class="cart-header">
        <h2>Your Service Cart 🛠️</h2>
        <a href="services.php?id=<?= $customerId ?>" style="color: #192156; text-decoration: none; font-weight: 600;">+ Add More Services</a>
    </div>

    <?php if (count($items) > 0): ?>
        <div class="coupon-row">
            <form class="coupon-form" method="get" action="cart.php">
                <input type="hidden" name="id" value="<?= $customerId ?>">
                <input type="text" name="coupon" placeholder="Enter coupon code" value="<?= htmlspecialchars($couponCode) ?>">
                <button type="submit">Apply Coupon</button>
            </form>
            <div>
                <div class="coupon-help">Available code: <strong>FIX10</strong> (10% off)</div>
                <?php if ($couponCode !== '' && $discountPercent > 0): ?>
                    <div class="coupon-ok">Coupon <?= htmlspecialchars($couponCode) ?> applied (<?= rtrim(rtrim((string) $discountPercent, '0'), '.') ?>% off).</div>
                <?php elseif ($couponCode !== ''): ?>
                    <div class="coupon-bad">Invalid coupon code.</div>
                <?php endif; ?>
            </div>
        </div>

        <form action="checkout.php" method="post">
            <input type="hidden" name="customer_id" value="<?= $customerId ?>">
            <input type="hidden" name="coupon_code" value="<?= htmlspecialchars($discountPercent > 0 ? $couponCode : '') ?>">
            
            <?php foreach ($items as $item): ?>
                <div class="cart-item">
                    <img src="media/<?= htmlspecialchars($item['image'] ?? 'service-1.png') ?>" alt="Service">
                    <div class="item-info">
                        <h4><?= htmlspecialchars($item['product_name']) ?></h4>
                        <p>Provider: <?= htmlspecialchars($item['provider_name']) ?></p>
                        <a href="remove_from_cart.php?cart_id=<?= $item['cart_id'] ?>&cid=<?= $customerId ?>" class="remove-btn">Remove</a>
                    </div>
                    
                    <div class="item-schedule">
                        <label>Date for this service</label>
                        <input type="date" name="dates[<?= $item['cart_id'] ?>]" required>
                        <label>Time for this service</label>
                        <input type="time" name="times[<?= $item['cart_id'] ?>]" required>
                    </div>

                    <div class="item-price">TK <?= htmlspecialchars($item['price']) ?></div>
                </div>
            <?php endforeach; ?>

            <div class="cart-total">
                <div class="total-breakdown">
                    <div class="total-line">
                        <span>Subtotal</span>
                        <span>TK <?= number_format($totalPrice, 2) ?></span>
                    </div>
                    <div class="total-line discount">
                        <span>Discount</span>
                        <span>- TK <?= number_format($discountAmount, 2) ?></span>
                    </div>
                    <div class="total-line grand">
                        <span>Total</span>
                        <span>TK <?= number_format($finalPrice, 2) ?></span>
                    </div>
                </div>
                <button type="submit" class="checkout-btn">Confirm & Schedule All</button>
            </div>
        </form>
    <?php else: ?>
        <div style="text-align: center; padding: 40px; color: #777;">
            <p>Your cart is empty.</p>
            <a href="services.php?id=<?= $customerId ?>" class="checkout-btn">Browse Services</a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
