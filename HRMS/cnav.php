<?php
include "connection.php";

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

$row = null;
if ($id > 0) {
    // 1. Fetch Customer
    $stmt = mysqli_prepare($con, "SELECT * FROM customers WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}

$cartItemCount = 0;
if ($id > 0) {
    // 2. Fetch Cart Count
    $cstmt = mysqli_prepare($con, "SELECT COUNT(*) as item_count FROM cart WHERE customer_id = ?");
    mysqli_stmt_bind_param($cstmt, "i", $id);
    mysqli_stmt_execute($cstmt);
    $cresult = mysqli_stmt_get_result($cstmt);
    $cartRow = mysqli_fetch_assoc($cresult);
    $cartItemCount = $cartRow['item_count'] ?? 0;
    mysqli_stmt_close($cstmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="app_theme.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="customer_dashboard.css?v=<?php echo time(); ?>">
    <style>
        #cart-icon:hover { transform: scale(1.1); transition: 0.2s; }
    </style>
</head>
<body>
        <div id="page">
        <div id="emergency-banner">24/7 emergency services +1 (877) 555-6666</div>

        <div id="nav">
            <div id="nav-left">
                <a href="customer_dashboard.php?id=<?php echo $id; ?>" style="text-decoration:none; color:inherit;">
                <div id="logo-wrap">
                    <img id="logo" src="media/logo.png" alt="Multi-Service & Multi-Fixing Logo">
                    <div id="logo-text">Multi-Service & Multi-Fixing</div>
                </div>
                </a>
            </div>
            <div id="nav-right" style="display: flex; align-items: center; gap: 20px;">
                <div id="service-location">
                    <div id="service-text">Service In</div>
                    <div id="service-area"><?php echo htmlspecialchars($row['location'] ?? '') ?></div>
                </div>
                
                <div id="cart-icon" style="position: relative; display: flex; align-items: center; padding: 5px;">
                    <a href="cart.php?id=<?php echo $id; ?>" style="text-decoration: none; color: #192156;">
                        <span style="font-size: 26px;">🛒</span>
                        <?php if($cartItemCount > 0): ?>
                        <span style="position: absolute; top: -2px; right: -8px; background: #E74C3C; color: white; border-radius: 50%; padding: 2px 6px; font-size: 11px; font-weight: bold; border: 2px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.2);"><?= $cartItemCount ?></span>
                        <?php endif; ?>
                    </a>
                </div>

                <div id="profile-icon">
                    <a href="profile.php?id=<?php echo $row['id']; ?>" style="display: flex; align-items: center; justify-content: center;">
                        <img src="media/<?php echo htmlspecialchars($row['cPhoto'] ?? 'default.png'); ?>" alt="Profile" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid #192156;">
                    </a>
                </div>
            </div>
        </div>

</body>
</html>
