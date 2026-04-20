<?php
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id']);
    $full_name = mysqli_real_escape_string($con, $_POST['full_name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $phone = mysqli_real_escape_string($con, $_POST['phone']);
    $location = mysqli_real_escape_string($con, $_POST['location']);

    // Fetch existing details to get current photo
    $q = mysqli_query($con, "SELECT cPhoto FROM customers WHERE id='$id'");
    $r = mysqli_fetch_assoc($q);
    $cPhoto = $r['cPhoto'] ?? 'default.png';

    // Handle new photo upload
    if (isset($_FILES['cPhoto']) && $_FILES['cPhoto']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['cPhoto']['name'], PATHINFO_EXTENSION);
        $cPhoto = 'cst_' . time() . '_' . uniqid() . '.' . $ext;
        $uploadPath = 'media/' . $cPhoto;
        if (!move_uploaded_file($_FILES['cPhoto']['tmp_name'], $uploadPath)) {
            $cPhoto = $r['cPhoto']; // Revert to old photo if move failed
        }
    }

    $updateQuery = "UPDATE customers SET 
                    full_name='$full_name', 
                    email='$email', 
                    mobile_number='$phone', 
                    location='$location', 
                    cPhoto='$cPhoto' 
                    WHERE id='$id'";
    
    mysqli_query($con, $updateQuery);
    
    header("Location: profile.php?id=" . $id);
    exit;
} else {
    echo "Invalid Request";
}
?>
