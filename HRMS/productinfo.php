<?php
include 'connection.php';

$product     = null;
$customer    = null;
$productId   = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$customerId  = isset($_GET['cid']) ? (int) $_GET['cid'] : 0;
$isPublicView = $customerId <= 0;
$reviewsResult = null;
$hasReviews = false;
$avgRating = null;

if ($productId > 0) {
    $stmt = mysqli_prepare(
        $con,
        "SELECT id, product_name, description, category, duration, price, rating, provider_email, provider_name, image FROM product WHERE id = ?"
    );
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $productId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $product = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
    }
}

if ($customerId > 0) {
    $cstmt = mysqli_prepare(
        $con,
        "SELECT id, full_name, email, location, mobile_number FROM customers WHERE id = ?"
    );
    if ($cstmt) {
        mysqli_stmt_bind_param($cstmt, "i", $customerId);
        mysqli_stmt_execute($cstmt);
        $cresult = mysqli_stmt_get_result($cstmt);
        $customer = mysqli_fetch_assoc($cresult);
        mysqli_stmt_close($cstmt);
    }
}

if ($productId > 0) {
    $rvStmt = mysqli_prepare(
        $con,
        "SELECT customer_name, star, comment, created_at
         FROM services
         WHERE product_id = ? AND comment <> '' AND star IS NOT NULL
         ORDER BY created_at DESC"
    );
    if ($rvStmt) {
        mysqli_stmt_bind_param($rvStmt, "i", $productId);
        mysqli_stmt_execute($rvStmt);
        $reviewsResult = mysqli_stmt_get_result($rvStmt);
        $hasReviews = $reviewsResult && mysqli_num_rows($reviewsResult) > 0;
        mysqli_stmt_close($rvStmt);
    }

    $avgStmt = mysqli_prepare(
        $con,
        "SELECT AVG(star) AS avg_star, COUNT(*) AS cnt FROM services WHERE product_id = ? AND star IS NOT NULL"
    );
    if ($avgStmt) {
        mysqli_stmt_bind_param($avgStmt, "i", $productId);
        mysqli_stmt_execute($avgStmt);
        $avgResult = mysqli_stmt_get_result($avgStmt);
        $avgRow = mysqli_fetch_assoc($avgResult);
        if ($avgRow && $avgRow['cnt'] > 0) {
            $avgRating = round(floatval($avgRow['avg_star']), 1);
        }
        mysqli_stmt_close($avgStmt);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Multi-Service & Multi-Fixing - Service Info</title>
    <link rel="stylesheet" href="app_theme.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="productinfo.css?v=<?php echo time(); ?>">
</head>
<body>

<div class="main-header">
    <a href="<?php echo $isPublicView ? 'welcome.html' : ('customer_dashboard.php?id=' . (int)$customerId); ?>" style="text-decoration:none; color:inherit;">
    <div class="nav-left">
        <img src="media/logo.png" alt="Multi-Service & Multi-Fixing Logo">
        <span>Multi-Service & Multi-Fixing</span>
    </div>
    </a>
    <div class="nav-title">Service Details</div>
</div>

<main class="page-content">
    <?php if (!$product) { ?>
        <p style="padding:16px;">Service not found. <a href="services.php">Back to services</a></p>
    <?php } else { ?>
    <div class="service-section">
        <div class="service-image">
            <img src="media/<?php echo htmlspecialchars($product['image'] ?? 'service-1.png'); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
        </div>

        <div class="service-info">
            <h1><?php echo htmlspecialchars($product['product_name']); ?></h1>
            <p class="service-desc">
                <?php echo nl2br(htmlspecialchars($product['description'])); ?>
            </p>

            <ul class="service-meta">
                <li><strong>Category:</strong> <?php echo htmlspecialchars($product['category']); ?></li>
                <li><strong>Duration:</strong> <?php echo htmlspecialchars($product['duration']); ?></li>
                <li><strong>Price:</strong> TK <?php echo htmlspecialchars($product['price']); ?></li>
                <li><strong>Rating:</strong> <?php echo $avgRating !== null ? htmlspecialchars($avgRating) : 'N/A'; ?> &#9733;</li>
            </ul>
        </div>
    </div>

    <div id="provider-info">
        <div class="img-info">
            <img src="media/profile.jpeg" alt="Service Provider">
        </div>

        <div class="provider-details">
            <h3>Service Provider Information</h3>
            <ul>
                <li><strong>Name:</strong> <?php echo htmlspecialchars($product['provider_name'] ?? 'N/A'); ?></li>
                <li><strong>Email:</strong> <?php echo htmlspecialchars($product['provider_email'] ?? 'N/A'); ?></li>
            </ul>
        </div>
    </div>

    <section class="bottom-two-cards">
        <div class="card booking-card">
            <?php if ($isPublicView): ?>
                <h3>Login Required for Booking</h3>
                <p style="margin-bottom: 20px; color: #555;">You are viewing public pricing only. Please log in to continue booking.</p>
                <a href="login.html" class="primary-btn" style="display:block; text-align:center; text-decoration:none; width: 100%; font-size: 1.05em; padding: 12px;">Login to Book</a>
            <?php else: ?>
                <h3>Add to Service Cart</h3>
                <p style="margin-bottom: 20px; color: #555;">Add this service to your cart and book it along with other fixing services!</p>
                <form action="add_to_cart.php" method="post">
                    <input type="hidden" name="product_id" value="<?php echo (int)$product['id']; ?>">
                    <input type="hidden" name="customer_id" value="<?php echo (int)$customerId; ?>">
                    <button type="submit" class="primary-btn" style="background-color: #28a745; width: 100%; font-size: 1.1em; padding: 12px;">Add to Cart</button>
                </form>
            <?php endif; ?>
        </div>

        <div class="card comments-card">
            <h3>Customer Reviews</h3>

            <div class="reviews-list">
                <?php if ($hasReviews): ?>
                    <?php while ($rv = mysqli_fetch_assoc($reviewsResult)): ?>
                        <div class="review-item">
                            <div class="review-top">
                                <span class="review-name"><?= htmlspecialchars($rv['customer_name']); ?></span>
                                <span class="review-rating">&#9733; <?= htmlspecialchars($rv['star']); ?></span>
                            </div>
                            <p class="review-text">
                                <?= nl2br(htmlspecialchars($rv['comment'])); ?>
                            </p>
                            <span class="review-date"><?= htmlspecialchars($rv['created_at']); ?></span>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="reviews-footer">No reviews yet.</p>
                <?php endif; ?>
            </div>

            <?php if ($hasReviews): ?>
                <p class="reviews-footer">Showing latest reviews.</p>
            <?php endif; ?>
        </div>
    </section>

    <?php } ?>
</main>

</body>
</html>
