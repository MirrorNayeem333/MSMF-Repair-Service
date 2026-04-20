<?php
include 'connection.php';

// Expect data from POST
$productId   = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;
$customerId  = isset($_POST['customer_id']) ? (int) $_POST['customer_id'] : 0;
$bookingDate = $_POST['booking_date'] ?? '';
$bookingTime = $_POST['booking_time'] ?? '';
$bookingNote = $_POST['booking_note'] ?? '';

if ($productId <= 0 || $customerId <= 0 || !$bookingDate || !$bookingTime) {
    die("Missing required booking fields.");
}

// Pull product info from DB
$pstmt = mysqli_prepare($con, "SELECT product_name, provider_name, provider_email, price FROM product WHERE id = ?");
if (!$pstmt) {
    die("Product lookup prepare failed: " . mysqli_error($con));
}
mysqli_stmt_bind_param($pstmt, "i", $productId);
mysqli_stmt_execute($pstmt);
$presult = mysqli_stmt_get_result($pstmt);
$product = mysqli_fetch_assoc($presult);
mysqli_stmt_close($pstmt);

if (!$product) {
    die("Product not found.");
}

// Pull customer info from DB
$cstmt = mysqli_prepare($con, "SELECT full_name, email, location,mobile_number FROM customers WHERE id = ?");
if (!$cstmt) {
    die("Customer lookup prepare failed: " . mysqli_error($con));
}
mysqli_stmt_bind_param($cstmt, "i", $customerId);
mysqli_stmt_execute($cstmt);
$cresult = mysqli_stmt_get_result($cstmt);
$customer = mysqli_fetch_assoc($cresult);
mysqli_stmt_close($cstmt);

if (!$customer) {
    die("Customer not found.");
}

$status = 'pending';
$paid   = 0;

$stmt = mysqli_prepare(
    $con,
    "INSERT INTO services (product_id, product_name, provider_name, provider_email, customer_name, customer_email, customer_location, customer_phone, booking_date, booking_time, price, status, paid, created_at, booking_note)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)"
);

if (!$stmt) {
    die("Prepare failed: " . mysqli_error($con));
}

mysqli_stmt_bind_param(
    $stmt,
    "isssssssssdsis",
    $productId,
    $product['product_name'],
    $product['provider_name'],
    $product['provider_email'],
    $customer['full_name'],
    $customer['email'],
    $customer['location'],
    $customer['mobile_number'],
    $bookingDate,
    $bookingTime,
    $product['price'],
    $status,
    $paid,
    $bookingNote
);

if (!mysqli_stmt_execute($stmt)) {
    die("Insert failed: " . mysqli_error($con));
}

mysqli_stmt_close($stmt);

header("Location: profile.php?id=" . $customerId);
exit;
?>
