<?php
include 'connection.php';
require_once 'payment_helpers.php';
ensure_services_payment_columns($con);

$serviceId = isset($_GET['sid']) ? (int) $_GET['sid'] : 0;
$customerId = isset($_GET['cid']) ? (int) $_GET['cid'] : 0;

if ($serviceId <= 0 || $customerId <= 0) {
    die('Invalid receipt request.');
}

$cstmt = mysqli_prepare($con, "SELECT full_name, email FROM customers WHERE id = ?");
mysqli_stmt_bind_param($cstmt, "i", $customerId);
mysqli_stmt_execute($cstmt);
$cresult = mysqli_stmt_get_result($cstmt);
$customer = mysqli_fetch_assoc($cresult);
mysqli_stmt_close($cstmt);

if (!$customer) {
    die('Customer not found.');
}

$sstmt = mysqli_prepare(
    $con,
    "SELECT id, product_name, provider_name, provider_email, booking_date, booking_time, price, status, paid, payment_method, payment_ref, paid_at
     FROM services
     WHERE id = ? AND customer_email = ?
     LIMIT 1"
);
mysqli_stmt_bind_param($sstmt, "is", $serviceId, $customer['email']);
mysqli_stmt_execute($sstmt);
$sresult = mysqli_stmt_get_result($sstmt);
$service = mysqli_fetch_assoc($sresult);
mysqli_stmt_close($sstmt);

if (!$service) {
    die('Receipt not available for this service.');
}

$paidValue = (int)($service['paid'] ?? 0);
$paymentStatus = 'Pending';
if ($paidValue === 1 || $service['paid'] === 'paid') {
    $paymentStatus = 'Paid';
} elseif ($paidValue === 2) {
    $paymentStatus = 'Cash on Delivery';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt</title>
    <link rel="stylesheet" href="app_theme.css?v=<?php echo time(); ?>">
    <style>
        .receipt-wrap { max-width: 760px; margin: 40px auto; padding: 30px; background: var(--app-glass); border: 1px solid var(--app-glass-border); border-radius: 22px; box-shadow: 0 16px 40px rgba(18,34,61,0.08); }
        .receipt-head { display:flex; justify-content:space-between; gap:14px; align-items:flex-start; margin-bottom:20px; border-bottom:1px solid rgba(36,75,138,0.15); padding-bottom:16px; }
        .brand { font-size: 26px; font-weight: 800; color: var(--app-blue); line-height: 1.2; }
        .receipt-id { font-size: 14px; color: #4b607f; }
        .meta-grid { display:grid; grid-template-columns: 1fr 1fr; gap:14px; margin: 16px 0 22px; }
        .meta-card { background: rgba(255,255,255,0.75); border: 1px solid rgba(36,75,138,0.14); border-radius: 12px; padding: 12px; }
        .meta-label { font-size: 12px; color: #5c7090; font-weight: 700; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 0.4px; }
        .meta-value { font-size: 16px; font-weight: 700; color: var(--app-ink); }
        .total-row { margin-top: 8px; padding: 14px; border-radius: 12px; background: rgba(36,75,138,0.1); display:flex; justify-content:space-between; align-items:center; font-size: 22px; font-weight: 800; color: var(--app-blue); }
        .actions { margin-top: 20px; display:flex; gap:10px; justify-content:flex-end; }
        .btn { text-decoration:none; border:none; border-radius:999px; padding: 10px 16px; font-weight:700; cursor:pointer; font-family: var(--app-font); }
        .btn-back { background: #e2e8f0; color: #1e293b; }
        .btn-print { background: var(--app-blue); color:#fff; }
        @media print { .actions { display:none; } body { background: #fff; } .receipt-wrap { box-shadow:none; border-color:#ddd; } }
    </style>
</head>
<body>
    <div class="receipt-wrap">
        <div class="receipt-head">
            <div>
                <div class="brand">Multi-Service & Multi-Fixing</div>
                <div class="receipt-id">Payment Receipt</div>
            </div>
            <div class="receipt-id">Receipt ID: #RCPT-<?php echo (int)$service['id']; ?></div>
        </div>

        <div class="meta-grid">
            <div class="meta-card">
                <div class="meta-label">Customer</div>
                <div class="meta-value"><?php echo htmlspecialchars($customer['full_name']); ?></div>
            </div>
            <div class="meta-card">
                <div class="meta-label">Service</div>
                <div class="meta-value"><?php echo htmlspecialchars($service['product_name']); ?></div>
            </div>
            <div class="meta-card">
                <div class="meta-label">Provider</div>
                <div class="meta-value"><?php echo htmlspecialchars($service['provider_name']); ?></div>
            </div>
            <div class="meta-card">
                <div class="meta-label">Provider Email</div>
                <div class="meta-value"><?php echo htmlspecialchars($service['provider_email']); ?></div>
            </div>
            <div class="meta-card">
                <div class="meta-label">Booking Date & Time</div>
                <div class="meta-value"><?php echo htmlspecialchars($service['booking_date']); ?> <?php echo htmlspecialchars($service['booking_time']); ?></div>
            </div>
            <div class="meta-card">
                <div class="meta-label">Payment Status</div>
                <div class="meta-value"><?php echo htmlspecialchars($paymentStatus); ?></div>
            </div>
            <div class="meta-card">
                <div class="meta-label">Payment Method</div>
                <div class="meta-value"><?php echo htmlspecialchars(payment_method_label($service['payment_method'])); ?></div>
            </div>
            <div class="meta-card">
                <div class="meta-label">Transaction / Ref</div>
                <div class="meta-value"><?php echo htmlspecialchars($service['payment_ref'] ?: 'N/A'); ?></div>
            </div>
        </div>

        <div class="total-row">
            <span>Total Amount</span>
            <span>TK <?php echo number_format((float)$service['price'], 2); ?></span>
        </div>

        <div class="receipt-id" style="margin-top: 10px;">
            Paid At: <?php echo htmlspecialchars($service['paid_at'] ?: 'Not yet paid online'); ?>
        </div>

        <div class="actions">
            <a class="btn btn-back" href="profile.php?id=<?php echo $customerId; ?>">Back</a>
            <button class="btn btn-print" onclick="window.print()">Print Receipt</button>
        </div>
    </div>
</body>
</html>
