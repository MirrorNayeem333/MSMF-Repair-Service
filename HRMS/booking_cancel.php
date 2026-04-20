<?php
include 'connection.php';

$serviceId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($serviceId <= 0) {
    die("Invalid service id");
}

$stmt = mysqli_prepare($con, "UPDATE services SET status = 'canceled' WHERE id = ?");
if (!$stmt) {
    die("Prepare failed: " . mysqli_error($con));
}
mysqli_stmt_bind_param($stmt, "i", $serviceId);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

$providerId = isset($_GET['pid']) ? (int) $_GET['pid'] : 0;
$redirect = $providerId > 0 ? "product_display.php?id={$providerId}" : "javascript:history.back()";
header("Location: " . $redirect);
exit;
?>
