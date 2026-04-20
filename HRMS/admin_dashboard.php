<?php
include 'connection.php';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) die("Invalid admin id");

$stmt = mysqli_prepare($con, "SELECT full_name FROM admins WHERE id=?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$r = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
if(!$r) die("Admin not found.");

// Execute all 5 major tracking scope queries globally
$q_customers = mysqli_query($con, "SELECT id, full_name, email, mobile_number, created_at FROM customers ORDER BY created_at DESC");
$q_providers = mysqli_query($con, "SELECT id, full_name, email, mobile_number, location, created_at FROM providers ORDER BY created_at DESC");
$q_products = mysqli_query($con, "SELECT id, product_name, price, provider_email FROM product ORDER BY id DESC");
$q_bookings = mysqli_query($con, "SELECT id, product_name, provider_email, customer_name, customer_phone, booking_date, booking_time, price, status, paid FROM services ORDER BY created_at DESC");
$q_feedback = mysqli_query($con, "SELECT customer_name, email, feedback, created_at FROM feedback ORDER BY created_at DESC");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Master Admin Dashboard</title>
    <link rel="stylesheet" href="app_theme.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="provider.css?v=<?php echo time(); ?>">
    <style>
        .page { padding: 40px; margin: 0 auto; max-width: 1200px; }
        #emergency-banner { position: relative; margin-bottom: 0px; }
        .feedback-message { white-space: pre-wrap; max-width: 300px; line-height: 1.5; }
        .nav-links { background: rgba(255,255,255,0.4); backdrop-filter: saturate(180%) blur(30px); padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.6); display: flex; gap: 20px; justify-content: center; position: sticky; top: 0; z-index: 1000; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .nav-links a { text-decoration: none; font-weight: 600; color: #1d1d1f; padding: 8px 20px; border-radius: 99px; background: rgba(255,255,255,0.6); border: 1px solid rgba(255,255,255,0.8); transition: 0.3s;}
        .nav-links a:hover { background: #fff; transform: scale(1.05); }
        .dashboard-section { margin-top: 40px; margin-bottom: 60px; scroll-margin-top: 100px; }
        .section-title { margin-bottom: 20px; color: #1d1d1f; font-weight: 700; font-size: 24px; letter-spacing: -0.5px; }
    </style>
</head>
<body>
    <div id="emergency-banner" style="background: rgba(255,255,255,0.4); backdrop-filter: saturate(180%) blur(20px); text-align: center; padding: 10px; font-weight: 600; font-size: 13px; border-bottom: 1px solid rgba(255,255,255,0.5);">Secure Admin Master Portal</div>
    
    <div class="nav-links">
        <a href="#customers">Customers Dashboard</a>
        <a href="#providers">Providers Dashboard</a>
        <a href="#catalog">Services Catalog</a>
        <a href="#bookings">Booking Monitor</a>
        <a href="#feedback">Feedback Logs</a>
    </div>

<div class="page">
    <div class="section-header">
        <h2>Multi-Service & Multi-Fixing Admin</h2>
        <div class="header-actions">
            <a class="add-btn" href="admin_profile.php?id=<?= $id ?>" style="background: #e67e22;">My Profile</a>
            <a class="logout-btn" href="login.html">Logout</a>
        </div>
    </div>

    <!-- CUSTOMERS -->
    <section id="customers" class="dashboard-section services-section">
        <h3 class="section-title">Globally Registered Customers</h3>
        <table class="data-table services-table">
            <thead>
                <tr>
                    <th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Joined At</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($q_customers)): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td style="font-weight: bold;"><?= htmlspecialchars($row['full_name']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['mobile_number']) ?></td>
                    <td><small><?= htmlspecialchars($row['created_at']) ?></small></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>

    <!-- PROVIDERS -->
    <section id="providers" class="dashboard-section services-section">
        <h3 class="section-title">Globally Registered Providers</h3>
        <table class="data-table services-table">
            <thead>
                <tr>
                    <th>ID</th><th>Provider Name</th><th>Email</th><th>Phone</th><th>Location</th><th>Joined At</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($q_providers)): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td style="font-weight: bold; color: #28a745;"><?= htmlspecialchars($row['full_name']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['mobile_number']) ?></td>
                    <td><?= htmlspecialchars($row['location']) ?></td>
                    <td><small><?= htmlspecialchars($row['created_at']) ?></small></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>

    <!-- CATALOG -->
    <section id="catalog" class="dashboard-section services-section">
        <h3 class="section-title">Created Services Catalog</h3>
        <table class="data-table services-table">
            <thead>
                <tr>
                    <th>Service ID</th><th>Service Name</th><th>Price</th><th>Provider Email</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($q_products)): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td style="font-weight: bold;"><?= htmlspecialchars($row['product_name']) ?></td>
                    <td><?= htmlspecialchars($row['price']) ?> TK</td>
                    <td><?= htmlspecialchars($row['provider_email']) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>

    <!-- BOOKINGS -->
    <section id="bookings" class="dashboard-section services-section">
        <h3 class="section-title">Customer Bookings & Confirmed Orders</h3>
        <table class="data-table services-table">
            <thead>
                <tr>
                    <th>ID</th><th>Customer Scope</th><th>Service Target</th><th>Provider Target</th><th>DateTime</th><th>Price</th><th>Acceptance Status</th><th>Financial Tx</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($q_bookings)): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td style="font-weight: bold;"><?= htmlspecialchars($row['customer_name']) ?> <br><small><?= htmlspecialchars($row['customer_phone']) ?></small></td>
                    <td><?= htmlspecialchars($row['product_name']) ?></td>
                    <td><?= htmlspecialchars($row['provider_email']) ?></td>
                    <td><?= htmlspecialchars($row['booking_date']) ?> <br> <?= htmlspecialchars($row['booking_time']) ?></td>
                    <td><?= htmlspecialchars($row['price']) ?> TK</td>
                    <td style="color: <?php echo ($row['status'] == 'accepted') ? '#28a745' : '#e67e22'; ?>; font-weight: bold;"><?= htmlspecialchars($row['status']) ?></td>
                    <td style="font-weight: bold;"><?= ($row['paid'] === 'paid' || intval($row['paid']) === 1) ? '<span style="color:#28a745;">Paid</span>' : '<span style="color:#dc3545;">Unpaid</span>' ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>

    <!-- FEEDBACK -->
    <section id="feedback" class="dashboard-section services-section">
        <h3 class="section-title">Global Customer Feedback Data</h3>
        <table class="data-table services-table">
            <thead>
                <tr>
                    <th>Customer Name</th>
                    <th>Email</th>
                    <th>Feedback Details</th>
                    <th>Submitted At</th>
                </tr>
            </thead>
            <tbody>
                <?php if(mysqli_num_rows($q_feedback) > 0): ?>
                    <?php while($f = mysqli_fetch_assoc($q_feedback)): ?>
                    <tr>
                        <td style="font-weight: bold; color: #192156;"><?= htmlspecialchars($f['customer_name']); ?></td>
                        <td><?= htmlspecialchars($f['email']); ?></td>
                        <td class="feedback-message">"<?= htmlspecialchars($f['feedback']); ?>"</td>
                        <td><small><?= htmlspecialchars($f['created_at']); ?></small></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="4" style="text-align: center; padding: 30px;">No feedback received yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>
</div>
</body>
</html>
