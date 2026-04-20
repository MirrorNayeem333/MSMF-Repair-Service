<?php
session_start();
include 'connection.php';

$fullName        = $_POST['full_name'] ?? '';
$email           = $_POST['email'] ?? '';
$mobileNumber    = $_POST['phone'] ?? '';
$location        = $_POST['location'] ?? '';
$password        = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

if ($password !== $confirmPassword) {
    die("Passwords do not match.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Invalid email.");
}

// NID Upload
if (!isset($_FILES['nid_photo']) || $_FILES['nid_photo']['error'] !== UPLOAD_ERR_OK) {
    die("NID required.");
}

$allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
$file = $_FILES['nid_photo'];

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mime, $allowed_types)) {
    die("Invalid file.");
}

$nid_dir = __DIR__ . '/media/nids/';
if (!is_dir($nid_dir)) {
    mkdir($nid_dir, 0750, true);
}

$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$nid_name = 'prov_' . time() . '_' . bin2hex(random_bytes(6)) . '.' . strtolower($ext);
$nid_path = $nid_dir . $nid_name;

move_uploaded_file($file['tmp_name'], $nid_path);

// OTP PART
include 'send_otp.php';

$otp = rand(100000, 999999);

$_SESSION['otp'] = $otp;
$_SESSION['type'] = 'provider';
$_SESSION['data'] = $_POST;
$_SESSION['nid_name'] = $nid_name;

if(sendOTP($email, $otp)){
    header("Location: verify_otp.php");
    exit;
} else {
    die("OTP send failed.");
}
?>