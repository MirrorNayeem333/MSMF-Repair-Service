<?php
require 'connection.php';

// Read provider id from GET first (per flow), fallback to POST hidden field
$providerId = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($providerId <= 0 && isset($_POST['id'])) {
    $providerId = intval($_POST['id']);
}

if ($providerId <= 0) {
    die("Invalid provider ID");
}

// Fetch provider name and email
$providerStmt = mysqli_prepare($con, "SELECT full_name, email FROM providers WHERE id = ?");
mysqli_stmt_bind_param($providerStmt, "i", $providerId);
mysqli_stmt_execute($providerStmt);
$providerResult = mysqli_stmt_get_result($providerStmt);
$providerRow = mysqli_fetch_assoc($providerResult);

if (!$providerRow) {
    die("No provider found with the given id");
}

$providerName  = mysqli_real_escape_string($con, $providerRow['full_name']);
$providerEmail = mysqli_real_escape_string($con, $providerRow['email']);

// Collect and escape product data from form
$product_name = mysqli_real_escape_string($con, $_POST['product_name'] ?? '');
$product_code = mysqli_real_escape_string($con, $_POST['product_code'] ?? '');
$category     = mysqli_real_escape_string($con, $_POST['category'] ?? '');
$description  = mysqli_real_escape_string($con, $_POST['description'] ?? '');
$duration     = mysqli_real_escape_string($con, $_POST['duration'] ?? '');
$offer_off    = isset($_POST['offer_off']) ? intval($_POST['offer_off']) : 0;
$price1       = isset($_POST['price1']) ? floatval($_POST['price1']) : 0;

$imageName = 'service-1.png';
if (isset($_FILES['service_image']) && $_FILES['service_image']['error'] === UPLOAD_ERR_OK) {
    $ext = pathinfo($_FILES['service_image']['name'], PATHINFO_EXTENSION);
    $imageName = 'srv_' . time() . '_' . uniqid() . '.' . $ext;
    $uploadPath = 'media/' . $imageName;
    if (!move_uploaded_file($_FILES['service_image']['tmp_name'], $uploadPath)) {
        $imageName = 'service-1.png';
    }
}

// Insert product with provider name and email included
$insertSql = "INSERT INTO product (product_name, product_code, category, description, duration, offer_off, price, provider_name, provider_email, image)
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$insertStmt = mysqli_prepare($con, $insertSql);

if (!$insertStmt) {
    die("Prepare failed: " . mysqli_error($con));
}

mysqli_stmt_bind_param(
    $insertStmt,
    "sssssidsss",
    $product_name,
    $product_code,
    $category,
    $description,
    $duration,
    $offer_off,
    $price1,
    $providerName,
    $providerEmail,
    $imageName
);

if (!mysqli_stmt_execute($insertStmt)) {
    die("Error inserting product: " . mysqli_error($con));
}

header("Location: product_display.php?id=" . $providerId);
exit();
?>
