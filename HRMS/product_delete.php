<?php

    include 'connection.php';

    $id = $_GET['id'];

    // Get the provider ID before safely deleting the product
    $fetch_pid = mysqli_query($con, "SELECT pr.id FROM providers pr JOIN product p ON pr.email = p.provider_email WHERE p.id = '$id'");
    $providerId = 0;
    if($fetch_pid && mysqli_num_rows($fetch_pid) > 0) {
        $prow = mysqli_fetch_assoc($fetch_pid);
        $providerId = $prow['id'];
    }

    $query = "DELETE FROM product WHERE id = '$id'";
    $run = mysqli_query($con, $query);

    if(!$run){
        echo 'delete operation failed!';
    } else{
        header("location: product_display.php?id=" . $providerId);
    }

?>