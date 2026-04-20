<?php
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id']);
    $full_name = mysqli_real_escape_string($con, $_POST['full_name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = mysqli_real_escape_string($con, $_POST['password']);

    $q = mysqli_query($con, "SELECT aPhoto FROM admins WHERE id='$id'");
    $r = mysqli_fetch_assoc($q);
    $aPhoto = $r['aPhoto'] ?? 'default.png';

    if (isset($_FILES['aPhoto']) && $_FILES['aPhoto']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['aPhoto']['name'], PATHINFO_EXTENSION);
        $aPhoto = 'adm_' . time() . '_' . uniqid() . '.' . $ext;
        $uploadPath = 'media/' . $aPhoto;
        if (!move_uploaded_file($_FILES['aPhoto']['tmp_name'], $uploadPath)) {
            $aPhoto = $r['aPhoto'];
        }
    }

    $updateQuery = "UPDATE admins SET 
                    full_name='$full_name', 
                    email='$email', 
                    password='$password', 
                    aPhoto='$aPhoto' 
                    WHERE id='$id'";
    
    mysqli_query($con, $updateQuery);
    header("Location: admin_profile.php?id=" . $id);
    exit;
} else {
    echo "Invalid Request";
}
?>
