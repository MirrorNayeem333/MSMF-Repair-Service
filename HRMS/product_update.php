<?php
include 'connection.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $id   = $_POST['id'];
$product_name   = $_POST['product_name'];
$product_code   = $_POST['product_code'];
$category      = $_POST['category'];
$description   = $_POST['description'];
$offer_off     = $_POST['offer_off'];
$duration      = $_POST['duration'];
$price1         = $_POST['price1'];

    mysqli_query($con, "UPDATE product SET product_name='$product_name', 
    product_code='$product_code',
    category='$category',
    description='$description',
    price ='$price1',
    duration='$duration',
    offer_off ='$offer_off' WHERE id='$id'");
    header("location: product_display.php");
    exit;
}else{
    echo "Invalid Request";
}

