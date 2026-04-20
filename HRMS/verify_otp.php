<?php
session_start();
include 'connection.php';

if(isset($_POST['otp'])){

    if($_POST['otp'] == $_SESSION['otp']){

        $data = $_SESSION['data'];
        $nid_name = $_SESSION['nid_name'];

        $fullName = $data['full_name'];
        $email = $data['email'];
        $mobileNumber = $data['phone'];
        $location = $data['location'];

        $password = password_hash($data['password'], PASSWORD_DEFAULT);

        if($_SESSION['type'] == 'customer'){

            $query = "INSERT INTO customers 
            (email, full_name, mobile_number, location, password, cPhoto, joined, nid_photo) 
            VALUES 
            ('$email','$fullName','$mobileNumber','$location','$password','default.png', YEAR(CURDATE()), '$nid_name')";

            mysqli_query($con, $query);
            $id = mysqli_insert_id($con);

            header("Location: customer_dashboard.php?id=".$id);
        }

        else if($_SESSION['type'] == 'provider'){

            $query = "INSERT INTO providers 
            (email, full_name, mobile_number, location, password, pPhoto, nid_photo) 
            VALUES 
            ('$email','$fullName','$mobileNumber','$location','$password','default.png','$nid_name')";

            mysqli_query($con, $query);
            $id = mysqli_insert_id($con);

            header("Location: product_display.php?id=".$id);
        }

    } else {
        echo "Wrong OTP ❌";
    }
}
?>

<form method="POST">
    <h2>Enter OTP</h2>
    <input type="text" name="otp" required>
    <button type="submit">Verify</button>
</form>