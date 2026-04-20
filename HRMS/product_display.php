<?php
include 'connection.php';

$providerId = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($providerId <= 0) {
    die("Invalid provider id");
}

$pstmt = mysqli_prepare($con, "SELECT full_name, email, pPhoto FROM providers WHERE id = ?");
mysqli_stmt_bind_param($pstmt, "i", $providerId);
mysqli_stmt_execute($pstmt);
$presult = mysqli_stmt_get_result($pstmt);
$provider = mysqli_fetch_assoc($presult);
mysqli_stmt_close($pstmt);

if (!$provider) {
    die("Provider not found");
}

$providerEmail = $provider['email'];
$providerName = $provider['full_name'];

$productStmt = mysqli_prepare($con, "SELECT id, product_name, description, price FROM product WHERE provider_email = ? ORDER BY id DESC");
mysqli_stmt_bind_param($productStmt, "s", $providerEmail);
mysqli_stmt_execute($productStmt);
$productsResult = mysqli_stmt_get_result($productStmt);
$products = [];
if ($productsResult) {
    while ($row = mysqli_fetch_assoc($productsResult)) {
        $products[] = $row;
    }
}
mysqli_stmt_close($productStmt);
$hasProducts = count($products) > 0;

$requestsStmt = mysqli_prepare(
    $con,
    "SELECT id, product_name, customer_name, customer_phone, customer_location, booking_date, booking_time, price, status, paid, star
     FROM services
     WHERE provider_email = ?
     ORDER BY created_at DESC"
);
mysqli_stmt_bind_param($requestsStmt, "s", $providerEmail);
mysqli_stmt_execute($requestsStmt);
$requestsResult = mysqli_stmt_get_result($requestsStmt);
$requests = [];
if ($requestsResult) {
    while ($row = mysqli_fetch_assoc($requestsResult)) {
        $requests[] = $row;
    }
}
mysqli_stmt_close($requestsStmt);
$hasRequests = count($requests) > 0;

$totalProducts = count($products);
$totalRequests = count($requests);
$pendingRequests = 0;
$acceptedRequests = 0;
$canceledRequests = 0;
$totalRevenue = 0.0;
$totalStars = 0;
$starCount = 0;

foreach ($requests as $req) {
    $status = strtolower(trim((string)($req['status'] ?? '')));
    if ($status === 'pending') {
        $pendingRequests++;
    } elseif ($status === 'accepted') {
        $acceptedRequests++;
    } elseif ($status === 'canceled' || $status === 'cancelled') {
        $canceledRequests++;
    }

    $isPaid = isset($req['paid']) && ($req['paid'] === 'paid' || intval($req['paid']) === 1);
    if ($isPaid) {
        $totalRevenue += (float)($req['price'] ?? 0);
    }

    if (isset($req['star']) && is_numeric($req['star']) && $req['star'] > 0) {
        $totalStars += (int)$req['star'];
        $starCount++;
    }
}
$avgStar = $starCount > 0 ? round($totalStars / $starCount) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Provider Dashboard</title>
    <link rel="stylesheet" href="app_theme.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="provider.css?v=<?php echo time(); ?>">
</head>
<body>
<div id="page">
    <div id="emergency-banner">24/7 emergency services +1 (877) 555-6666</div>

    <header class="main-header">
        <div class="nav-left">
            <img src="media/logo.png" alt="Multi-Service & Multi-Fixing Logo">
            <span>Multi-Service & Multi-Fixing</span>
        </div>

        <a href="provider_profile.php?id=<?= $providerId ?>" class="provider-chip">
            <span><?= htmlspecialchars($providerName) ?></span>
            <img src="media/<?= htmlspecialchars($provider['pPhoto'] ?? 'default.png') ?>" alt="Profile">
        </a>
    </header>

    <main class="page-content">
        <section class="services-section">
            <div class="section-header">
                <h2>Your Services</h2>
                <div class="header-actions">
                    <a class="add-btn profile-btn" href="provider_profile.php?id=<?= $providerId ?>">My Profile</a>
                    <a class="add-btn" href="add_new.php?id=<?= $providerId ?>">Add New</a>
                    <a class="logout-btn" href="login.html">Logout</a>
                </div>
            </div>

            <div class="metrics-grid">
                <div class="metric-card">
                    <div class="metric-label">Total Services</div>
                    <div class="metric-value"><?= $totalProducts ?></div>
                </div>
                <div class="metric-card">
                    <div class="metric-label">All Requests</div>
                    <div class="metric-value"><?= $totalRequests ?></div>
                </div>
                <div class="metric-card">
                    <div class="metric-label">Pending</div>
                    <div class="metric-value"><?= $pendingRequests ?></div>
                </div>
                <div class="metric-card">
                    <div class="metric-label">Paid Revenue</div>
                    <div class="metric-value">TK <?= number_format($totalRevenue, 2) ?></div>
                </div>
            </div>

            <table class="data-table services-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($hasProducts): ?>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?= htmlspecialchars($product['product_name']) ?></td>
                                <td><?= htmlspecialchars($product['description']) ?></td>
                                <td>TK <?= number_format((float)$product['price'], 2) ?></td>
                                <td>
                                    <a class="action-btn update-link" href="update.php?id=<?= $product['id'] ?>">Update</a>
                                    <a class="action-btn delete-link" href="product_delete.php?id=<?= $product['id'] ?>">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td class="empty-state" colspan="4">No products added yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>

        <section class="stats-banner">
            <div class="rating">
                <?php
                if ($avgStar > 0) {
                    echo str_repeat("&#9733; ", $avgStar) . str_repeat("&#9734; ", 5 - $avgStar);
                } else {
                    echo "&#9734; &#9734; &#9734; &#9734; &#9734;";
                }
                ?>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?= $totalProducts ?></div>
                <div class="stat-label">Listed Services</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?= $acceptedRequests ?></div>
                <div class="stat-label">Accepted Jobs</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?= $canceledRequests ?></div>
                <div class="stat-label">Canceled Jobs</div>
            </div>
        </section>

        <section class="requests-section">
            <div class="section-header">
                <h3>Booking Requests</h3>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Service</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>C Name</th>
                        <th>Phone</th>
                        <th>Location</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($hasRequests): ?>
                        <?php foreach ($requests as $req): ?>
                            <tr>
                                <td><?= htmlspecialchars($req['product_name']) ?></td>
                                <td><?= htmlspecialchars($req['booking_date']) ?></td>
                                <td><?= htmlspecialchars($req['booking_time']) ?></td>
                                <td><?= htmlspecialchars($req['customer_name']) ?></td>
                                <td><?= htmlspecialchars($req['customer_phone']) ?></td>
                                <td><?= htmlspecialchars($req['customer_location']) ?></td>
                                <td>TK <?= number_format((float)$req['price'], 2) ?></td>
                                <td>
                                    <?php
                                    $statusText = strtolower((string)($req['status'] ?? 'pending'));
                                    $statusClass = 'status-pill pending';
                                    if ($statusText === 'accepted') {
                                        $statusClass = 'status-pill accepted';
                                    } elseif ($statusText === 'canceled' || $statusText === 'cancelled') {
                                        $statusClass = 'status-pill canceled';
                                    }
                                    ?>
                                    <span class="<?= $statusClass ?>"><?= htmlspecialchars(ucfirst($statusText)) ?></span>
                                </td>
                                <td>
                                    <?php $isPaid = isset($req['paid']) && ($req['paid'] === 'paid' || intval($req['paid']) === 1); ?>
                                    <?php if ($isPaid): ?>
                                        <span class="paid-pill">Paid</span>
                                    <?php else: ?>
                                        <a class="action-btn confirm-btn" href="booking_confirm.php?id=<?= urlencode($req['id']) ?>&pid=<?= $providerId ?>">Confirm</a>
                                        <a class="action-btn cancel-btn" href="booking_cancel.php?id=<?= urlencode($req['id']) ?>&pid=<?= $providerId ?>">Cancel</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td class="empty-state" colspan="9">No requests yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>
</div>
</body>
</html>
