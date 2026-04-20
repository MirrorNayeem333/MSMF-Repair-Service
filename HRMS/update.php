<?php
include 'connection.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){

$id             = $_POST['id'];
$product_name   = $_POST['product_name'];
$product_code   = $_POST['product_code'];
$category      = $_POST['category'];
$description   = $_POST['description'];
$offer_off     = $_POST['offer_off'];
$duration      = $_POST['duration'];
$price1        = $_POST['price1'];



$fetch_pid = mysqli_query($con, "SELECT pr.id FROM providers pr JOIN product p ON pr.email = p.provider_email WHERE p.id = '$id'");
$providerId = 0;
if($fetch_pid && mysqli_num_rows($fetch_pid) > 0) {
    $prow = mysqli_fetch_assoc($fetch_pid);
    $providerId = $prow['id'];
}

mysqli_query($con, "UPDATE product SET product_name='$product_name', 
    product_code='$product_code',
    category='$category',
    description='$description',
    price ='$price1',
    duration='$duration',
    offer_off ='$offer_off' WHERE id='$id'");
    header("location: product_display.php?id=" . $providerId);
    exit;
}


$id  = $_GET['id'];
$row = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM product WHERE id='$id'"));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="app_theme.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="update.css">
</head>
<body>
    

<form method="post">

    <label for="product_name">Product Name</label>
                    <input type="text" id="product_name" name="product_name" value="<?= $row['product_name'] ?>">
    
    <label for="product_code">Product Code</label>
                    <input type="text" id="product_code" name="product_code" value="<?= $row['product_code'] ?>">
<label for="category">Category</label>
                    <input type="text" id="category" name="category" value="<?= $row['category'] ?>">

<label for="description">Description</label>
                    <input id="description" name="description" rows="3"
                        value="<?= $row['description'] ?>"></input>

<label for="duration">Duration</label>
                    <input type="text" id="duration" name="duration" value="<?= $row['duration'] ?>">

     <label for="price1">Price</label>
                    <input type="text" id="price1" name="price1" value="<?= $row['price'] ?>">
                    
           <label for="offer_off">Offer Off (%)</label>
                    <input type="number" id="offer_off" name="offer_off" value="<?= $row['offer_off'] ?>">
                    
                    

    <input type="submit" value="Update">
</form>
</body>
</html>
