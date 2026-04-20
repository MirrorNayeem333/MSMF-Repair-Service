<?php
include 'connection.php';

$cartId = isset($_GET['cart_id']) ? (int)$_GET['cart_id'] : 0;
$customerId = isset($_GET['cid']) ? (int)$_GET['cid'] : 0;

if ($cartId > 0 && $customerId > 0) {
    $stmt = mysqli_prepare($con, "DELETE FROM cart WHERE id = ? AND customer_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $cartId, $customerId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

header("Location: cart.php?id=$customerId");
exit;
?>
