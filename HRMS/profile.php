<?php
include("connection.php");
require_once 'payment_helpers.php';
ensure_services_payment_columns($con);

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    die("Invalid customer id");
}

// Customer info
$query = "SELECT * FROM customers WHERE id='$id'";
$result = mysqli_query($con, $query);
$row = mysqli_fetch_assoc($result);
if (!$row) {
    die("Customer not found");
}

// Services booked by this customer (match by customer email)
$customerEmail = mysqli_real_escape_string($con, $row['email']);
$servicesSql = "
    SELECT id, product_name, provider_name, booking_date, booking_time,
           customer_name, customer_phone, customer_location, price, status, paid,
           payment_method, payment_ref, paid_at
    FROM services
    WHERE customer_email = '$customerEmail'
    ORDER BY created_at DESC
";
$servicesResult = mysqli_query($con, $servicesSql);
$hasServices = $servicesResult && mysqli_num_rows($servicesResult) > 0;
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Multi-Service & Multi-Fixing - User Profile</title>
    <link rel="stylesheet" href="app_theme.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="profile.css?v=<?php echo time(); ?>">
</head>
<body>

<!-- TOP NAVBAR -->
<header class="main-header">
    <a href="customer_dashboard.php?id=<?= $id ?>" style="text-decoration:none; color:inherit;">
    <div class="nav-left">
        <img src="media/logo.png" alt="Multi-Service & Multi-Fixing Logo">
        <span>Multi-Service & Multi-Fixing</span>
    </div>
    </a>
    <div class="nav-title">My Profile</div>
</header>

<main class="page-content">

    <!-- PROFILE CARD -->
    <section class="profile-card">
        <div class="profile-top">
            <div class="profile-avatar">
                <img src="media/<?php echo htmlspecialchars($row['cPhoto'] ?? 'default.png'); ?>" alt="User photo">
            </div>
                    <th>Location</th>
                    <th>Phone</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Payment</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($hasServices): ?>
                    <?php while ($svc = mysqli_fetch_assoc($servicesResult)): ?>
                        <?php
                            $isAccepted = ($svc['status'] === 'accepted');
                            $isCanceled = ($svc['status'] === 'canceled');
                            $isPaidOnline = ($svc['paid'] === 'paid' || intval($svc['paid']) === 1);
                            $isCodSelected = (intval($svc['paid']) === 2);
                            $canPay     = $isAccepted && !$isPaidOnline && !$isCodSelected && !$isCanceled;
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($svc['product_name']); ?></td>
                            <td><?= htmlspecialchars($svc['provider_name']); ?></td>
                            <td><?= htmlspecialchars($svc['booking_date']); ?></td>
                            <td><?= htmlspecialchars($svc['booking_time']); ?></td>
                            <td><?= htmlspecialchars($svc['customer_location']); ?></td>
                            <td><?= htmlspecialchars($svc['customer_phone']); ?></td>
                            <td><?= htmlspecialchars($svc['price']); ?></td>
                            <td><?= htmlspecialchars($svc['status']); ?></td>
                            <td>
                                <?php if ($isPaidOnline): ?>
                                    <span class="paid-pill">Paid (<?= htmlspecialchars(payment_method_label($svc['payment_method'] ?: 'online')); ?>)</span>
                                    <div style="margin-top:8px;">
                                        <a href="receipt.php?sid=<?= (int)$svc['id']; ?>&cid=<?= (int)$id; ?>" class="logout-btn" style="padding:6px 12px; font-size:12px; text-decoration:none;">Receipt</a>
                                    </div>
                                <?php elseif ($isCodSelected): ?>
                                    <span style="font-weight:700; color:#b7791f;">COD Selected</span>
                                    <div style="font-size:12px; color:#64748b; margin-top:5px;">Pay when service is delivered.</div>
                                    <div style="margin-top:8px;">
                                        <a href="receipt.php?sid=<?= (int)$svc['id']; ?>&cid=<?= (int)$id; ?>" class="logout-btn" style="padding:6px 12px; font-size:12px; text-decoration:none;">Receipt</a>
                                    </div>
                                <?php else: ?>
                                    <form action="payment.php" method="post" style="margin:0; display:flex; flex-direction:column; gap:8px; width:160px;">
                                        <input type="hidden" name="service_id" value="<?= $svc['id']; ?>">
                                        <input type="hidden" name="customer_id" value="<?= $id; ?>">
                                        
                                        <select name="payment_method" style="width:100%; padding:4px 8px; border:1px solid rgba(30,58,110,0.15); border-radius:6px; font-size:12px;" required>
                                            <option value="online">Online Payment</option>
                                            <option value="cod">Cash on Delivery</option>
                                        </select>

                                        <input type="text" name="payment_ref" placeholder="Txn / Ref (optional)" style="width:100%; padding:4px 8px; border:1px solid rgba(30,58,110,0.15); border-radius:6px; font-size:12px;">

                                        <div style="display:flex; gap:4px; align-items:center;">
                                            <span style="font-size:11px; font-weight:600; color:var(--navy);">Rating:</span>
                                            <input type="number" name="star" min="1" max="5" step="1" placeholder="1-5" style="width:100%; padding:4px 8px; border:1px solid rgba(30,58,110,0.15); border-radius:6px; font-size:12px;" required>
                                        </div>

                                        <input type="text" name="comment" placeholder="Feedback..." style="width:100%; padding:4px 8px; border:1px solid rgba(30,58,110,0.15); border-radius:6px; font-size:12px;" required>
                                        
                                        <button
                                            type="submit"
                                            name="pay"
                                            <?= $canPay ? '' : 'disabled'; ?>
                                            style="width:100%; background: <?= $canPay ? 'var(--navy)' : '#cbd5e1'; ?>; color: #fff; padding: 6px; border: none; border-radius: 8px; font-size:12px; font-weight:600; cursor: <?= $canPay ? 'pointer' : 'not-allowed'; ?>; transition:0.2s;"
                                        >
                                            <?= $isAccepted ? 'Pay Now' : 'Pending' ?>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9">No requests yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>

</main>

</body>
</html>
