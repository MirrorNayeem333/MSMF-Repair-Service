<?php
include 'connection.php';
require_once 'coupon_config.php';

$customerId = isset($_POST['customer_id']) ? (int)$_POST['customer_id'] : 0;
$dates = $_POST['dates'] ?? [];
$times = $_POST['times'] ?? [];
$couponCode = normalize_coupon_code($_POST['coupon_code'] ?? '');
$discountPercent = coupon_discount_percent($couponCode);

if ($customerId <= 0 || empty($dates) || empty($times)) {
    die("Invalid checkout data.");
}

// 1. Fetch customer info
$cstmt = mysqli_prepare($con, "SELECT full_name, email, location, mobile_number FROM customers WHERE id = ?");
mysqli_stmt_bind_param($cstmt, "i", $customerId);
mysqli_stmt_execute($cstmt);
$cresult = mysqli_stmt_get_result($cstmt);
$customer = mysqli_fetch_assoc($cresult);
mysqli_stmt_close($cstmt);

if (!$customer) {
    die("Customer not found.");
}

// 2. Fetch cart info joined with products
$cartQuery = "
    SELECT c.id as cart_id, p.id as product_id, p.product_name, p.provider_name, p.provider_email, p.price 
    FROM cart c 
    JOIN product p ON c.product_id = p.id 
    WHERE c.customer_id = ?
";
$stmt = mysqli_prepare($con, $cartQuery);
mysqli_stmt_bind_param($stmt, "i", $customerId);
mysqli_stmt_execute($stmt);
$cartItems = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($cartItems) == 0) {
    die("Cart is empty.");
}

// 3. Process each cart item into 'services'
$insertSql = "INSERT INTO services (product_id, product_name, provider_name, provider_email, customer_name, customer_email, customer_location, customer_phone, booking_date, booking_time, price, status, paid, created_at, booking_note)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)";
$insertStmt = mysqli_prepare($con, $insertSql);

$status = 'pending';
$paid = 0;
$note = '';

while ($item = mysqli_fetch_assoc($cartItems)) {
    $cartId = $item['cart_id'];
    $bDate = $dates[$cartId] ?? '';
    $bTime = $times[$cartId] ?? '';
    
    if (!$bDate || !$bTime) continue; // skip if somehow missing date/time
    
    $finalItemPrice = (float) $item['price'];
    if ($discountPercent > 0) {
        $finalItemPrice = round($finalItemPrice * ((100 - $discountPercent) / 100), 2);
        $note = "Coupon {$couponCode} applied ({$discountPercent}% off).";
    } else {
        $note = '';
    }

    mysqli_stmt_bind_param(
        $insertStmt,
        "isssssssssdsis",
        $item['product_id'],
        $item['product_name'],
        $item['provider_name'],
        $item['provider_email'],
        $customer['full_name'],
        $customer['email'],
        $customer['location'],
        $customer['mobile_number'],
        $bDate,
        $bTime,
        $finalItemPrice,
        $status,
        $paid,
        $note
    );
    mysqli_stmt_execute($insertStmt);
}

// 4. Clear the cart
$clearCart = mysqli_prepare($con, "DELETE FROM cart WHERE customer_id = ?");
mysqli_stmt_bind_param($clearCart, "i", $customerId);
mysqli_stmt_execute($clearCart);

// 5. Redirect to Profile (where they can see their requests)
header("Location: profile.php?id=" . $customerId);
exit;
?>
