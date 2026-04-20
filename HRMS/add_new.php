<?php
include 'connection.php';

$providerId = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($providerId <= 0) {
    die("Invalid provider id");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Service</title>
    <link rel="stylesheet" href="app_theme.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="update.css">
</head>
<body>
    <form class="add-form" method="post" action="product_insert.php?id=<?= $providerId ?>" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $providerId ?>">

        <label for="service_image">Service Image</label>
        <input type="file" id="service_image" name="service_image" accept="image/*">

        <label for="product_name">Product Name</label>
        <input type="text" id="product_name" name="product_name" placeholder="Enter product name" required>

        <label for="product_code">Product Code</label>
        <input type="text" id="product_code" name="product_code" placeholder="Enter product code">

        <label for="category">Category</label>
        <input type="text" id="category" name="category" placeholder="Enter category">

        <label for="description">Description</label>
        <input id="description" name="description" placeholder="Add a short description">

        <label for="duration">Duration</label>
        <input type="text" id="duration" name="duration" placeholder="e.g., 2 hours">

        <label for="price1">Price</label>
        <input type="text" id="price1" name="price1" placeholder="Enter price">

        <label for="offer_off">Offer Off (%)</label>
        <input type="number" id="offer_off" name="offer_off" placeholder="e.g., 10">

        <input type="submit" value="Submit">
    </form>
</body>
</html>
