<?php 
session_start();
include 'connection.php';
include 'send_otp.php';

$fullName        = trim($_POST['full_name'] ?? '');
$email           = trim($_POST['email'] ?? '');
$mobileNumber    = trim($_POST['phone'] ?? '');
$location        = trim($_POST['location'] ?? '');
$password        = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

// 🔴 EMAIL VALIDATION FIX
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Invalid email: check form submission");
}

// PASSWORD CHECK
if ($password !== $confirmPassword) {
    die("Passwords do not match.");
}

// NID Upload check
if (!isset($_FILES['nid_photo']) || $_FILES['nid_photo']['error'] !== UPLOAD_ERR_OK) {
    die("NID required.");
}

$allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
$file = $_FILES['nid_photo'];

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mime, $allowed_types)) {
    die("Invalid file type.");
}

// Create folder if not exists
$nid_dir = __DIR__ . '/media/nids/';
if (!is_dir($nid_dir)) {
    mkdir($nid_dir, 0750, true);
}

// Save file
$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$nid_name = 'cust_' . time() . '_' . bin2hex(random_bytes(6)) . '.' . strtolower($ext);
$nid_path = $nid_dir . $nid_name;

move_uploaded_file($file['tmp_name'], $nid_path);

// OTP GENERATE
$otp = rand(100000, 999999);

// SESSION STORE
$_SESSION['otp'] = $otp;
$_SESSION['type'] = 'customer';
$_SESSION['data'] = [
    'full_name' => $fullName,
    'email' => $email,
    'phone' => $mobileNumber,
    'location' => $location,
    'password' => password_hash($password, PASSWORD_DEFAULT),
    'nid_photo' => $nid_name
];

// SEND OTP
if (sendOTP($email, $otp)) {
    header("Location: verify_otp.php");
    exit;
} else {
    die("OTP send failed.");
}
?>