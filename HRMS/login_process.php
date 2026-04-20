<?php
ob_start();
include("connection.php");

// 1. Make sure all expected POST values exist
if (!isset($_POST['loginRole'], $_POST['email'], $_POST['password'])) {
    die("Form data missing.");
}

$role     = $_POST['loginRole'];
$email    = $_POST['email'];
$password = $_POST['password'];

// Determine which table to query
$table = '';
$redirect = '';

if ($role == 'customer') {
    $table = 'customers';
    $redirect = 'customer_dashboard.php';
} else if ($role == 'provider') {
    $table = 'providers';
    $redirect = 'product_display.php';
} else if ($role == 'admin') {
    $table = 'admins';
    $redirect = 'admin_dashboard.php';
} else {
    die("Invalid login role.");
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Invalid email address. Please enter a valid email (e.g. user@example.com).");
}

// Use Prepared Statements to prevent SQL Injection
$stmt = mysqli_prepare($con, "SELECT id FROM $table WHERE email = ? AND password = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "ss", $email, $password);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result) {
    die("Query failed: " . mysqli_error($con));
}

if (mysqli_num_rows($result) == 0) {
    echo "Invalid $role email or password";
} else {
    $row = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    
    // Redirect with user ID
    header("Location: $redirect?id=" . $row['id']);
    exit();
}

mysqli_stmt_close($stmt);
?>
