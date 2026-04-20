<?php
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cid = intval($_POST['customer_id'] ?? 0);
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $feedback = mysqli_real_escape_string($con, $_POST['feedback']);

    if (!empty($name) && !empty($email) && !empty($feedback)) {
        $stmt = mysqli_prepare($con, "INSERT INTO feedback (customer_name, email, feedback) VALUES (?, ?, ?)");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sss", $name, $email, $feedback);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }
    
    // Redirect back seamlessly
    if ($cid > 0) {
        header("Location: customer_dashboard.php?id=" . $cid);
    } else {
        header("Location: welcome.html");
    }
    exit;
} else {
    echo "Invalid Request";
}
?>


