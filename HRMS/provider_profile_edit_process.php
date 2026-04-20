<?php
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id']);
    $full_name = mysqli_real_escape_string($con, $_POST['full_name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $mobile_number = mysqli_real_escape_string($con, $_POST['mobile_number']);
    $location = mysqli_real_escape_string($con, $_POST['location']);
    $password = mysqli_real_escape_string($con, $_POST['password']);

    $q = mysqli_query($con, "SELECT pPhoto FROM providers WHERE id='$id'");
    $r = mysqli_fetch_assoc($q);
    $pPhoto = $r['pPhoto'] ?? 'default.png';

    if (isset($_FILES['pPhoto']) && $_FILES['pPhoto']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['pPhoto']['name'], PATHINFO_EXTENSION);
        $pPhoto = 'prv_' . time() . '_' . uniqid() . '.' . $ext;
        $uploadPath = 'media/' . $pPhoto;
        // Verify move is fully successful before re-assigning path string bounds
        if (!move_uploaded_file($_FILES['pPhoto']['tmp_name'], $uploadPath)) {
            $pPhoto = $r['pPhoto'];
        }
    }

    $updateQuery = "UPDATE providers SET 
                    full_name='$full_name', 
                    email='$email', 
                    mobile_number='$mobile_number',
                    location='$location',
                    password='$password', 
                    pPhoto='$pPhoto' 
                    WHERE id='$id'";
    
    mysqli_query($con, $updateQuery);
    header("Location: provider_profile.php?id=" . $id);
    exit;
} else {
    echo "Invalid Request";
}
?>
