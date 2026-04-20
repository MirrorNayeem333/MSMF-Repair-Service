<?php
include("connection.php");
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) die("Invalid provider id");

$query = "SELECT * FROM providers WHERE id='$id'";
$result = mysqli_query($con, $query);
$row = mysqli_fetch_assoc($result);
if (!$row) die("Provider not found");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Provider Profile</title>
    <link rel="stylesheet" href="app_theme.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="profile.css">
</head>
<body>
<header class="main-header">
    <a href="product_display.php?id=<?= $id ?>" style="text-decoration:none; color:inherit;">
    <div class="nav-left">
        <img src="media/logo.png" alt="Multi-Service & Multi-Fixing Logo">
        <span>Multi-Service & Multi-Fixing</span>
    </div>
    </a>
    <div class="nav-title">Provider Identity</div>
</header>
<main class="page-content">
    <section class="profile-card">
        <div class="profile-top">
            <div class="profile-avatar">
                <img src="media/<?php echo htmlspecialchars($row['pPhoto'] ?? 'default.png'); ?>" alt="Provider photo">
            </div>
            <div class="profile-info">
                <h2 class="profile-name"><?php echo htmlspecialchars($row['full_name']); ?></h2>
                <p class="profile-role" style="color: #28a745; font-weight: bold;">Registered Service Provider</p>
                <ul class="profile-details">
                    <li><strong>Email: </strong><?php echo htmlspecialchars($row['email']); ?></li>
                    <li><strong>Phone: </strong><?php echo htmlspecialchars($row['mobile_number']); ?></li>
                    <li><strong>Location: </strong><?php echo htmlspecialchars($row['location']); ?></li>
                    <li><strong>Password: </strong>••••••••</li>
                    <li><strong>Joined: </strong><?php echo htmlspecialchars($row['created_at']); ?></li>
                </ul>
            </div>
        </div>
        <div style="display: flex; gap: 10px; margin-top: 15px;">
            <a class="logout-btn" href="provider_profile_edit.php?id=<?= $id ?>" style="background-color: #28a745;">Edit Settings & Photo</a>
            <a class="logout-btn" href="login.html">Logout</a>
        </div>

        <?php if (!empty($row['nid_photo'])): ?>
        <div style="margin-top: 24px; padding: 18px 20px; background: rgba(40,167,69,0.04); border: 1.5px solid rgba(40,167,69,0.15); border-radius: 16px;">
            <p style="font-size: 13px; font-weight: 700; color: #28a745; margin-bottom: 10px; display: flex; align-items: center; gap: 6px;">
                🔒 Your National ID (NID) &nbsp;<span style="font-size:11px; font-weight:500; color:#e53e3e; background:rgba(229,62,62,0.08); padding:2px 10px; border-radius:99px;">Private — Only visible to you & Admin</span>
            </p>
            <img src="media/nids/<?php echo htmlspecialchars($row['nid_photo']); ?>"
                 alt="NID Photo"
                 style="max-width:100%; max-height:220px; object-fit:contain; border-radius:12px; border:2px solid rgba(40,167,69,0.2); display:block;">
        </div>
        <?php endif; ?>
    </section>
</main>
</body>
</html>
