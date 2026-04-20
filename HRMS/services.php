<?php
    include 'connection.php';

    // Logged-in customer id passed in querystring: services.php?id=CUSTOMER_ID
    $customerId = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $isPublicView = $customerId <= 0;

    if (!$isPublicView) {
        include "cnav.php";
    }

    // simple search term from query string
    $key = '';
    if (isset($_GET['search'])) {
        $key = trim($_GET['search']);
    }

    // build query (basic LIKE search on name/code/description)
    $safeKey = mysqli_real_escape_string($con, $key);
    $query = "
        SELECT
            p.id,
            p.product_name,
            p.price,
            p.image,
            COALESCE(AVG(s.star), NULL) AS avg_star
        FROM product p
        LEFT JOIN services s ON s.product_id = p.id AND s.star IS NOT NULL
        WHERE p.product_name LIKE '%$safeKey%'
           OR p.product_code LIKE '%$safeKey%'
           OR p.description LIKE '%$safeKey%'
        GROUP BY p.id, p.product_name, p.price";

    $run = mysqli_query($con, $query);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Services</title>
    <link rel="stylesheet" href="app_theme.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="services.css?v=<?php echo time(); ?>">
</head>

<body>


    <form class="search-container" method="get" action="services.php">
        <?php if (!$isPublicView): ?>
        <input type="hidden" name="id" value="<?php echo $customerId; ?>">
        <?php endif; ?>
        <div id="hero-search">
            <input id="search-input" name="search" type="text" placeholder="Search ..." value="<?php echo htmlspecialchars($key); ?>">
        </div>
    </form>
    <div>
        <div id="our-services">
            <h2>Our Services</h2>
            <div id="service-cards">
                <?php if($run && mysqli_num_rows($run) > 0){ ?>
                    <?php while($row = mysqli_fetch_assoc($run)){ ?>
                        <div class="card">
                            <img src="media/<?php echo htmlspecialchars($row['image'] ?? 'service-1.png'); ?>" alt="<?php echo htmlspecialchars($row['product_name']); ?>">
                            <h3><?php echo htmlspecialchars($row['product_name']); ?></h3>
                            <p>Rating: <?php echo ($row['avg_star'] !== null ? htmlspecialchars(round($row['avg_star'], 1)) : 'N/A'); ?></p>
                            <div class="card-bottom">
                                <span class="price">TK <?php echo htmlspecialchars($row['price']); ?></span>
                                <?php if ($isPublicView): ?>
                                    <a class="cta-button" href="login.html">Login to Book</a>
                                <?php else: ?>
                                    <a class="cta-button" href="productinfo.php?id=<?php echo urlencode($row['id']); ?>&cid=<?php echo $customerId; ?>">Book</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php } ?>
                <?php } else { ?>
                    <p>No services found.</p>
                <?php } ?>
            </div>
        </div>
    </div>
</body>

</html>
