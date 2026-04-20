<?php
include("connection.php");
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) die("Invalid admin id");

$query = "SELECT * FROM admins WHERE id='$id'";
$result = mysqli_query($con, $query);
$row = mysqli_fetch_assoc($result);
if (!$row) die("Admin not found");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Profile</title>
    <link rel="stylesheet" href="app_theme.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="profile.css">
</head>
<body>
<header class="main-header">
    <a href="admin_dashboard.php?id=<?= $id ?>" style="text-decoration:none; color:inherit;">
    <div class="nav-left">
        <img src="media/logo.png" alt="Multi-Service & Multi-Fixing Logo">
        <span>Multi-Service & Multi-Fixing</span>
    </div>
    </a>
    <div class="nav-title">Admin Profile</div>
</header>
<main class="page-content">
    <section class="profile-card">
        <div class="profile-top">
            <div class="profile-avatar">
                <img src="media/<?php echo htmlspecialchars($row['aPhoto'] ?? 'default.png'); ?>" alt="Admin photo">
            </div>
            <div class="profile-info">
                <h2 class="profile-name"><?php echo htmlspecialchars($row['full_name']); ?></h2>
                <p class="profile-role" style="color: #e67e22; font-weight: bold;">System Administrator</p>
                <ul class="profile-details">
                    <li><strong>Email: </strong><?php echo htmlspecialchars($row['email']); ?></li>
                    <li><strong>Password: </strong>••••••••</li>
                    <li><strong>Joined: </strong><?php echo htmlspecialchars($row['created_at']); ?></li>
                </ul>
            </div>
        </div>
        <div style="display: flex; gap: 10px; margin-top: 15px;">
            <a class="logout-btn" href="admin_profile_edit.php?id=<?= $id ?>" style="background-color: #4CAF50;">Edit Profile & Password</a>
            <a class="logout-btn" href="login.html">Logout</a>
        </div>
    </section>
</main>
</body>
</html>
