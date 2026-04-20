<?php
include 'connection.php';

$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$customer_id = isset($_POST['customer_id']) ? (int)$_POST['customer_id'] : 0;

if ($product_id <= 0 || $customer_id <= 0) {
    echo "<script>alert('Invalid Request'); window.history.back();</script>";
    exit;
}

// Check if item already in cart
$check = mysqli_prepare($con, "SELECT id FROM cart WHERE customer_id = ? AND product_id = ?");
mysqli_stmt_bind_param($check, "ii", $customer_id, $product_id);
mysqli_stmt_execute($check);
$res = mysqli_stmt_get_result($check);

if (mysqli_num_rows($res) > 0) {
    echo "<script>alert('Service is already in your cart!'); window.location.href='productinfo.php?id=$product_id&cid=$customer_id';</script>";
} else {
    // Add to cart
    $stmt = mysqli_prepare($con, "INSERT INTO cart (customer_id, product_id) VALUES (?, ?)");
    mysqli_stmt_bind_param($stmt, "ii", $customer_id, $product_id);
    if (mysqli_stmt_execute($stmt)) {
        header("Location: cart.php?id=$customer_id"); // Redirect to cart instead of staying
    } else {
        echo "<script>alert('Failed to add to cart'); window.history.back();</script>";
    }
    mysqli_stmt_close($stmt);
}
mysqli_stmt_close($check);
?>
