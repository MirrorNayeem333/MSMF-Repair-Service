<?php
include "connection.php";
require_once "coupon_config.php";

$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;
if ($id <= 0) {
    die("Invalid customer id");
}

$stmt = mysqli_prepare($con, "SELECT * FROM customers WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$row) {
    die("Customer not found");
}

// Fetch latest 3 products with average rating
$servicesSql = "
    SELECT
        p.id,
        p.product_name,
        p.price,
        p.image,
        COALESCE(AVG(s.star), NULL) AS avg_star
    FROM product p
    LEFT JOIN services s ON s.product_id = p.id AND s.star IS NOT NULL
    GROUP BY p.id, p.product_name, p.price
    ORDER BY p.id DESC
    LIMIT 3";
$servicesResult = mysqli_query($con, $servicesSql);
$promoCode = "FIX10";
$promoPercent = coupon_discount_percent($promoCode);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard</title>
    <link rel="stylesheet" href="app_theme.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="customer_dashboard.css?v=<?php echo time(); ?>">
</head>
<body>
<?php include "cnav.php"; ?>

    <div id="hero">
        <div class="hero-video-bg">
            <video autoplay muted loop playsinline id="hero-bg-video">
                <source src="https://assets.mixkit.co/videos/preview/mixkit-abstract-motion-of-vibrant-colors-32537-large.mp4" type="video/mp4">
            </video>
        </div>
        <div id="hero-left">
            <div id="hero-title">
                <div>Expert & Hassle-Free</div>
                <div>Solutions for Your</div>
                <div class="typing-wrapper">
                    <span id="typing-text" class="typing-demo"></span>
                </div>
            </div>
            <div id="hero-subtitle">
                Browse multiple fixing services, add them to your cart, and schedule them all with a single click. Find trusted pros for all your repair needs!
            </div>
            <a id="hero-search" href="services.php?id=<?php echo $row['id']; ?>">
                <span id="search-input">Search ...</span>
            </a>
        </div>
        <div id="hero-right">
            <img id="hero-image" src="media/hero.png" alt="Technician">
        </div>
    </div>

    <!-- Promo Banner -->
    <div id="promo-banner">
        <div class="promo-content">
            <span class="promo-badge">NEW</span>
            <span class="promo-text">Get <strong><?php echo (int) $promoPercent; ?>% off</strong> your first repair! Use code: <span class="promo-code"><?php echo htmlspecialchars($promoCode); ?></span></span>
        </div>
        <a href="cart.php?id=<?php echo $row['id']; ?>&coupon=<?php echo urlencode($promoCode); ?>" class="promo-btn">Use Coupon</a>
    </div>

    <!-- How It Works -->
    <div id="how-it-works">
        <h2>How It Works</h2>
        <div class="steps-container">
            <div class="step">
                <div class="step-icon">
                    <img src="media/services.png" alt="Choose Service">
                </div>
                <h3>1. Choose Service</h3>
                <p>Browse or search for the exact repair or service you need.</p>
            </div>
            <div class="step">
                <div class="step-icon">
                    <img src="media/contact.png" alt="Book a Pro">
                </div>
                <h3>2. Book a Pro</h3>
                <p>Select a time and book one of our verified professionals.</p>
            </div>
            <div class="step">
                <div class="step-icon">
                    <img src="media/repair.png" alt="Problem Fixed">
                </div>
                <h3>3. Problem Fixed</h3>
                <p>Relax while we handle the rest, with guaranteed satisfaction.</p>
            </div>
        </div>
    </div>

    <div id="our-services">
        <h2>Our Services</h2>
        <div id="service-cards">
            <?php if ($servicesResult && mysqli_num_rows($servicesResult) > 0): ?>
                <?php while ($svc = mysqli_fetch_assoc($servicesResult)): ?>
                    <div class="card">
                        <img src="media/<?php echo htmlspecialchars($svc['image'] ?? 'service-1.png'); ?>" alt="<?php echo htmlspecialchars($svc['product_name']); ?>">
                        <h3><?php echo htmlspecialchars($svc['product_name']); ?></h3>
                        <p>Rating: <?php echo ($svc['avg_star'] !== null ? htmlspecialchars(round($svc['avg_star'], 1)) : 'N/A'); ?></p>
                        <div class="card-bottom">
                            <span class="price">TK <?php echo htmlspecialchars($svc['price']); ?></span>
                            <a class="cta-button" href="productinfo.php?id=<?php echo urlencode($svc['id']); ?>&cid=<?php echo $row['id']; ?>">Book</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No services available right now.</p>
            <?php endif; ?>
        </div>

        <div id="explore-more">
            <a class="cta-button" href="services.php?id=<?php echo $row['id']; ?>">Explore More</a>
        </div>
    </div>

    <!-- Why Choose Us -->
    <div id="why-choose-us">
        <h2>Why Choose Us</h2>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <img src="media/home.png" alt="Verified Professionals">
                </div>
                <h4>Verified Professionals</h4>
                <p>Every worker is background checked and highly skilled.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <span style="font-size: 32px; font-weight: bold; color: #1e3a6e;">$</span>
                </div>
                <h4>Upfront Pricing</h4>
                <p>No hidden fees. You know the cost before we start.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <img src="media/logo.png" alt="Guaranteed Work" style="width: 40px;">
                </div>
                <h4>Guaranteed Work</h4>
                <p>We stand by our work with a 30-day satisfaction guarantee.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <img src="media/contact.png" alt="24/7 Support">
                </div>
                <h4>24/7 Support</h4>
                <p>Our team is always here for emergency requests.</p>
            </div>
        </div>
    </div>

    <!-- Testimonials -->
    <div id="testimonials">
        <h2>What Our Customers Say</h2>
        <div class="testimonials-container">
            <div class="testimonial-card">
                <div class="stars">★★★★★</div>
                <p class="review-text">"The technician arrived on time and fixed my AC in just an hour. Incredible service!"</p>
                <div class="reviewer">
                    <div class="reviewer-avatar">S</div>
                    <div>
                        <div class="reviewer-name">Sarah J.</div>
                        <div class="reviewer-date">2 days ago</div>
                    </div>
                </div>
            </div>
            <div class="testimonial-card">
                <div class="stars">★★★★★</div>
                <p class="review-text">"Very transparent pricing and professional behavior. Highly recommended."</p>
                <div class="reviewer">
                    <div class="reviewer-avatar">M</div>
                    <div>
                        <div class="reviewer-name">Michael T.</div>
                        <div class="reviewer-date">1 week ago</div>
                    </div>
                </div>
            </div>
            <div class="testimonial-card">
                <div class="stars">★★★★★</div>
                <p class="review-text">"I couldn't find a good plumber until I used this platform. Saved me so much hassle!"</p>
                <div class="reviewer">
                    <div class="reviewer-avatar">D</div>
                    <div>
                        <div class="reviewer-name">David L.</div>
                        <div class="reviewer-date">3 weeks ago</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="contact-section">
        <div id="contact-left">
            <h2>Contact Us</h2>
            <form id="contact-form" method="post" action="feedback_submit.php">
                <input type="hidden" name="customer_id" value="<?php echo $id; ?>">
                <label for="name">Name</label><br>
                <input type="text" id="name" name="name" placeholder="Enter Name"><br><br>

                <label for="email">Email</label><br>
                <input type="email" id="email" name="email" placeholder="Enter Email"><br><br>

                <label for="feedback">Write your feedback/suggestions</label><br>
                <textarea id="feedback" name="feedback" rows="5" placeholder="Enter your feedback..."></textarea><br><br>

                <button type="submit" class="cta-button">Send</button>
            </form>
        </div>

        <div id="contact-right">
            <img src="media/contact.png" alt="Contact Us">
        </div>
    </div>

    <div id="footer">
        <div id="footer-links">
            <h4>Important Links</h4>
            <ul>
                <li><a href="customer_dashboard.php?id=<?php echo $row['id']; ?>">Home</a></li>
                <li><a href="services.php?id=<?php echo $row['id']; ?>">Services</a></li>
                <li><a href="contact.html">Contact Us</a></li>
            </ul>
        </div>

        <div id="footer-address">
            <h4>Address</h4>
            <p>123, Multi-Service & Multi-Fixing Center, Dhaka, Bangladesh</p>
            <p>Hotline: 996644</p>
        </div>

        <div id="footer-social">
            <h4>Follow Us</h4>
            <ul>
                <li><a href="#">Facebook</a></li>
                <li><a href="#">Instagram</a></li>
                <li><a href="#">LinkedIn</a></li>
            </ul>
        </div>
    </div>

    <div id="footer-bottom">
        <p>All Rights Reserved. Developed by Nayeem, Mahbuba, Tasfi, Parvej.</p>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const typingEl = document.getElementById('typing-text');
            if (!typingEl) return;

            const services = [
                'Home Appliances',
                'Electrical Work',
                'Plumbing Needs',
                'Refrigerator Repair'
            ];

            let index = 0;
            const typingSpeed = 90;
            const eraseSpeed = 55;
            const holdTime = 1200;

            function cycleText() {
                const text = services[index];
                typingEl.textContent = text;
                typingEl.style.width = 'auto';
                typingEl.style.setProperty('--typing-width', `${typingEl.scrollWidth}px`);
                typingEl.style.removeProperty('width');
                typingEl.style.setProperty('--typing-chars', text.length);
                typingEl.classList.remove('erasing');
                void typingEl.offsetWidth;
                typingEl.classList.add('typing');

                const typeDuration = text.length * typingSpeed;

                setTimeout(() => {
                    typingEl.classList.remove('typing');
                    typingEl.classList.add('erasing');

                    const eraseDuration = text.length * eraseSpeed;
                    setTimeout(() => {
                        typingEl.classList.remove('erasing');
                        index = (index + 1) % services.length;
                        cycleText();
                    }, eraseDuration);
                }, typeDuration + holdTime);
            }

            cycleText();
        });
    </script>
</body>
<script src="theme.js"></script>
</html>


