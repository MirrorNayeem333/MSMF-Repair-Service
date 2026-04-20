<?php
include 'connection.php';
require_once 'payment_helpers.php';
ensure_services_payment_columns($con);

$serviceId  = isset($_POST['service_id']) ? (int) $_POST['service_id'] : 0;
$customerId = isset($_POST['customer_id']) ? (int) $_POST['customer_id'] : 0;
$star       = isset($_POST['star']) ? intval($_POST['star']) : 0;
$comment    = isset($_POST['comment']) ? trim($_POST['comment']) : '';
$paymentMethod = isset($_POST['payment_method']) ? trim($_POST['payment_method']) : '';
$paymentRef = isset($_POST['payment_ref']) ? trim($_POST['payment_ref']) : '';

if ($serviceId <= 0 || $customerId <= 0) {
    die("Invalid request");
}

if (!in_array($paymentMethod, ['online', 'cod'], true)) {
    die("Please select a payment method.");
}

// Clamp rating 1-5
if ($star < 1 || $star > 5) {
    die("Rating must be between 1 and 5");
}

if ($paymentMethod === 'online' && $paymentRef === '') {
    $paymentRef = 'ONLINE-' . strtoupper(substr(uniqid(), -8));
}
if ($paymentMethod === 'cod') {
    $paymentRef = 'COD-' . strtoupper(substr(uniqid(), -8));
}

// Paid: 1 = paid online, 2 = cash on delivery selected
$paidValue = ($paymentMethod === 'online') ? 1 : 2;
$paidAtSql = ($paymentMethod === 'online') ? "NOW()" : "NULL";

$updateSql = "UPDATE services 
              SET paid = ?, star = ?, comment = ?, payment_method = ?, payment_ref = ?, paid_at = {$paidAtSql}
              WHERE id = ?";
$stmt = mysqli_prepare($con, $updateSql);
if (!$stmt) {
    die("Prepare failed: " . mysqli_error($con));
}

mysqli_stmt_bind_param($stmt, "iisssi", $paidValue, $star, $comment, $paymentMethod, $paymentRef, $serviceId);
if (!mysqli_stmt_execute($stmt)) {
    die("Update failed: " . mysqli_error($con));
}
mysqli_stmt_close($stmt);

header("Location: profile.php?id=" . $customerId);
exit;
?>
