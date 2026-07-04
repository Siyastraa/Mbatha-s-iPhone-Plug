<?php
// Mbatha's iPhone Plug - Homepage

require_once 'config/db.php';
require_once 'includes/functions.php';
require_once 'includes/cart_handler.php';

// Handle newsletter subscription
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'subscribe') {
    $email = sanitize($_POST['email']);
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        setFlashMessage("Thank you for subscribing to our newsletter! You will receive hot drops first.", "success");
    } else {
        setFlashMessage("Please enter a valid email address.", "danger");
    }
    header("Location: index.php");
    exit;
}

// Fetch Featured Products (Phones or Watches)
$stmt = $pdo->prepare("SELECT * FROM products WHERE is_featured = 1 LIMIT 4");
$stmt->execute();
$featured_products = $stmt->fetchAll();

// Fetch Flash Sales
$stmt = $pdo->prepare("SELECT * FROM products WHERE is_flash_sale = 1 LIMIT 4");
$stmt->execute();
$flash_sales = $stmt->fetchAll();

// Fetch Latest Customer Reviews
$stmt = $pdo->prepare("SELECT r.*, p.name as product_name FROM reviews r JOIN products p ON r.product_id = p.id ORDER BY r.created_at DESC LIMIT 3");
$stmt->execute();
$homepage_reviews = $stmt->fetchAll();

require_once 'includes/header.php';
?>

<!-- Hero Banner -->
<section class="hero-section d-flex align-items-center">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="hero-subtitle" data-aos="fade-up">Stay Connected</span>
                <h1 class="hero-title" data-aos="fade-up" data-aos-delay="100">
                    Premium Pre-Owned <br><span class="text-gradient">Apple Devices</span>
                </h1>
                <p class="hero-lead text-secondary fs-5 mb-4" data-aos="fade-up" data-aos-delay="200" style="max-width: 500px;">
                    Experience luxury performance without the luxury price tag. Certified original, battery-tested, and covered by a comprehensive warranty.
                </p>
                <div class="d-flex flex-wrap gap-3 hero-cta-btn" data-aos="fade-up" data-aos-delay="300">
                    <a href="shop.php" class="btn btn-gold py-3 px-4"><i class="fas fa-shopping-bag me-2"></i> Shop Price List</a>
                    <a href="tradein.php" class="btn btn-outline-gold py-3 px-4"><i class="fas fa-exchange-alt me-2"></i> Trade-In iPhone</a>
                </div>
            </div>
            
            <div class="col-lg-6 text-center hero-img-container">
                <div class="hero-circle-back"></div>
                <!-- Premium dynamic placeholder SVG mockup representing luxury golden iPhone -->
                <img src="assets/images/placeholder.php?name=iPhone+16+Pro+Max&type=iphone" class="img-fluid position-relative" style="max-height: 480px; filter: drop-shadow(0 15px 35px rgba(212,175,55,0.25)); z-index: 2;" alt="Luxury Pre-Owned iPhones">
            </div>
        </div>
    </div>
</section>

<!-- Trust Core Badges Section -->
<section class="py-4" style="background-color: var(--bg-secondary); border-top: 1px solid var(--border-color-light); border-bottom: 1px solid var(--border-color-light);">
    <div class="container">
        <div class="row text-center g-4">
            <div class="col-6 col-md-3" data-aos="fade-up">
                <div class="d-flex align-items-center justify-content-center gap-3">
                    <i class="fas fa-shield-alt text-warning fs-3"></i>
                    <div class="text-start">
                        <h6 class="mb-0 text-white fw-bold">100% Tested</h6>
                        <small class="text-secondary">40+ Point Inspection</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="100">
                <div class="d-flex align-items-center justify-content-center gap-3">
                    <i class="fas fa-battery-three-quarters text-warning fs-3"></i>
                    <div class="text-start">
                        <h6 class="mb-0 text-white fw-bold">Excellent Battery</h6>
                        <small class="text-secondary">85%+ Health Guaranteed</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="200">
                <div class="d-flex align-items-center justify-content-center gap-3">
                    <i class="fas fa-check-circle text-warning fs-3"></i>
                    <div class="text-start">
                        <h6 class="mb-0 text-white fw-bold">Quality Checked</h6>
                        <small class="text-secondary">A-Grade Shell Diagnostics</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="300">
                <div class="d-flex align-items-center justify-content-center gap-3">
                    <i class="fas fa-handshake text-warning fs-3"></i>
                    <div class="text-start">
                        <h6 class="mb-0 text-white fw-bold">Trusted Service</h6>
                        <small class="text-secondary">Thousands of Happy Clients</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Flash Sales Banner -->
<?php if (!empty($flash_sales)): ?>
<section class="section-padding">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-5">
            <div>
                <span class="text-warning text-uppercase tracking-wider fw-bold small"><i class="fas fa-bolt me-1 text-danger"></i> Limited Time Offer</span>
                <h2 class="section-title mb-0">Flash Deals</h2>
            </div>
            <a href="shop.php?flash=1" class="text-warning text-decoration-none hover-gold fw-bold">View All <i class="fas fa-arrow-right ms-1"></i></a>
        </div>
        
        <div class="row g-4">
            <?php foreach ($flash_sales as $product): ?>
                <div class="col-lg-3 col-md-6" data-aos="fade-up">
                    <div class="product-card h-100">
                        <span class="badge-sale">Promo Deal</span>
                        <div class="product-img-wrapper">
                            <img src="<?php echo getProductImage($product['image_url'], $product['name'], $product['category']); ?>" alt="<?php echo sanitize($product['name']); ?>">
                        </div>
                        <div class="product-details">
                            <span class="text-uppercase text-secondary small fw-bold"><?php echo sanitize($product['category']); ?></span>
                            <h3 class="product-title mt-1">
                                <a href="product.php?id=<?php echo $product['id']; ?>"><?php echo sanitize($product['name']); ?></a>
                            </h3>
                            <div class="product-meta">
                                <span class="meta-tag"><?php echo sanitize($product['storage']); ?></span>
                                <span class="meta-tag"><?php echo sanitize($product['color']); ?></span>
                                <span class="meta-tag battery-badge"><i class="fas fa-battery-full me-1"></i> <?php echo $product['battery_health']; ?>% BH</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top border-secondary border-opacity-25">
                                <div>
                                    <span class="product-price"><?php echo formatPrice($product['price']); ?></span>
                                    <?php if ($product['original_price']): ?>
                                        <span class="product-original-price"><?php echo formatPrice($product['original_price']); ?></span>
                                    <?php endif; ?>
                                </div>
                                <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-gold p-2 px-3 rounded-pill" title="View details"><i class="fas fa-eye text-dark"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Featured Inventory Section -->
<section class="section-padding" style="background-color: var(--bg-secondary);">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-5">
            <div>
                <span class="text-warning text-uppercase tracking-wider fw-bold small">Our Handpicked Selection</span>
                <h2 class="section-title mb-0">Featured Devices</h2>
            </div>
            <a href="shop.php" class="text-warning text-decoration-none hover-gold fw-bold">Shop Full Catalog <i class="fas fa-arrow-right ms-1"></i></a>
        </div>
        
        <div class="row g-4">
            <?php foreach ($featured_products as $product): ?>
                <div class="col-lg-3 col-md-6" data-aos="fade-up">
                    <div class="product-card h-100">
                        <span class="badge-grade"><?php echo sanitize($product['grade']); ?></span>
                        <div class="product-img-wrapper">
                            <img src="<?php echo getProductImage($product['image_url'], $product['name'], $product['category']); ?>" alt="<?php echo sanitize($product['name']); ?>">
                        </div>
                        <div class="product-details">
                            <span class="text-uppercase text-secondary small fw-bold"><?php echo sanitize($product['category']); ?></span>
                            <h3 class="product-title mt-1">
                                <a href="product.php?id=<?php echo $product['id']; ?>"><?php echo sanitize($product['name']); ?></a>
                            </h3>
                            <div class="product-meta">
                                <span class="meta-tag"><?php echo sanitize($product['storage']); ?></span>
                                <span class="meta-tag"><?php echo sanitize($product['color']); ?></span>
                                <span class="meta-tag battery-badge"><i class="fas fa-battery-full me-1"></i> <?php echo $product['battery_health']; ?>% BH</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top border-secondary border-opacity-25">
                                <div>
                                    <span class="product-price"><?php echo formatPrice($product['price']); ?></span>
                                    <?php if ($product['original_price']): ?>
                                        <span class="product-original-price"><?php echo formatPrice($product['original_price']); ?></span>
                                    <?php endif; ?>
                                </div>
                                <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-gold p-2 px-3 rounded-pill" title="View Details"><i class="fas fa-eye text-dark"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Trade-In Promo Section -->
<section class="section-padding position-relative" style="background: radial-gradient(circle at 10% 50%, rgba(212, 175, 55, 0.08) 0%, rgba(11, 11, 12, 1) 80%);">
    <div class="container">
        <div class="glass-card p-5" data-aos="zoom-in">
            <div class="row align-items-center g-4">
                <div class="col-lg-7 text-center text-lg-start">
                    <span class="text-warning text-uppercase tracking-widest fw-bold small">Upgrade and Save</span>
                    <h2 class="mt-2 mb-3 text-white fw-bold fs-1">Have an older iPhone? <br>Trade it in.</h2>
                    <p class="text-secondary fs-5 mb-4">
                        Get an instant estimated quote online and offset the cost of your brand new upgrade. We accept iPhones in all conditions—cracked screens welcome!
                    </p>
                    <a href="tradein.php" class="btn btn-gold py-3 px-5 fw-bold"><i class="fas fa-exchange-alt me-2"></i> Get Valuation Instantly</a>
                </div>
                <div class="col-lg-5 text-center">
                    <img src="assets/images/placeholder.php?name=Trade-In+iPhones&type=charger" class="img-fluid" style="max-height: 250px; filter: drop-shadow(0 10px 25px rgba(212,175,55,0.25));" alt="Trade In iPhones">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us Grid Details -->
<section class="section-padding" style="background-color: var(--bg-secondary);">
    <div class="container">
        <div class="text-center mb-5">
            <span class="text-warning text-uppercase tracking-wider fw-bold small">Why Mbatha's iPhone Plug</span>
            <h2 class="section-title center mx-auto mt-2">Built For Peace Of Mind</h2>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4" data-aos="fade-up">
                <div class="glass-card text-center h-100 p-4">
                    <div class="mb-4 text-warning"><i class="fas fa-shipping-fast fa-3x"></i></div>
                    <h4 class="text-white mb-3">Nationwide Delivery</h4>
                    <p class="text-secondary small">
                        We ship to your doorstep anywhere in South Africa via secure, tracked courier. Delivery takes 1-3 business days. Free for orders over R12,000.
                    </p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="glass-card text-center h-100 p-4">
                    <div class="mb-4 text-warning"><i class="fas fa-certificate fa-3x"></i></div>
                    <h4 class="text-white mb-3">6-Month Warranty</h4>
                    <p class="text-secondary small">
                        Every single pre-owned device goes through a 40+ point quality diagnostic test and is covered by our worry-free 6-month repair or swap warranty.
                    </p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="glass-card text-center h-100 p-4">
                    <div class="mb-4 text-warning"><i class="fas fa-file-invoice-dollar fa-3x"></i></div>
                    <h4 class="text-white mb-3">Secure Payments</h4>
                    <p class="text-secondary small">
                        Pay safely via Electronic Funds Transfer (EFT) or cash on collection. We provide fully detailed tax invoices and receipts for every purchase.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="section-padding">
    <div class="container">
        <div class="text-center mb-5">
            <span class="text-warning text-uppercase tracking-wider fw-bold small">Verified Client Experiences</span>
            <h2 class="section-title center mx-auto mt-2">What Our Clients Say</h2>
        </div>
        
        <div class="row g-4">
            <?php if (!empty($homepage_reviews)): ?>
                <?php foreach ($homepage_reviews as $review): ?>
                    <div class="col-md-4" data-aos="fade-up">
                        <div class="glass-card h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="stars mb-3">
                                    <?php for ($i=1; $i<=5; $i++): ?>
                                        <i class="<?php echo ($i <= $review['rating']) ? 'fas' : 'far'; ?> fa-star"></i>
                                    <?php endfor; ?>
                                </div>
                                <p class="text-secondary italic mb-4" style="font-size: 0.95rem; font-style: italic;">
                                    "<?php echo sanitize($review['comment']); ?>"
                                </p>
                            </div>
                            <div class="pt-3 border-top border-secondary border-opacity-25">
                                <h6 class="text-white mb-1 fw-bold"><?php echo sanitize($review['name']); ?></h6>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-warning small" style="font-size: 0.8rem;"><i class="fas fa-check-circle me-1"></i> Verified Buyer</span>
                                    <span class="text-muted small" style="font-size: 0.75rem;"><?php echo sanitize(explode(' ', $review['product_name'])[0] ?? 'Device'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Hardcoded backup reviews if database has no items -->
                <div class="col-md-4" data-aos="fade-up">
                    <div class="glass-card h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="stars mb-3"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                            <p class="text-secondary italic mb-4" style="font-size: 0.95rem; font-style: italic;">
                                "Excellent device! The iPhone 14 Pro Max looks exactly like brand new, battery health is indeed at 100%. Mbatha is the ultimate plug!"
                            </p>
                        </div>
                        <div class="pt-3 border-top border-secondary border-opacity-25">
                            <h6 class="text-white mb-1 fw-bold">Siyabonga M.</h6>
                            <span class="text-warning small" style="font-size: 0.8rem;"><i class="fas fa-check-circle me-1"></i> Verified Buyer</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="glass-card h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="stars mb-3"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                            <p class="text-secondary italic mb-4" style="font-size: 0.95rem; font-style: italic;">
                                "Best service ever. I ordered on WhatsApp, was delivered within 24 hours to JHB. Highly recommended."
                            </p>
                        </div>
                        <div class="pt-3 border-top border-secondary border-opacity-25">
                            <h6 class="text-white mb-1 fw-bold">Lerato N.</h6>
                            <span class="text-warning small" style="font-size: 0.8rem;"><i class="fas fa-check-circle me-1"></i> Verified Buyer</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="glass-card h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="stars mb-3"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                            <p class="text-secondary italic mb-4" style="font-size: 0.95rem; font-style: italic;">
                                "Beautiful iPhone 15 Pro, battery is excellent and the titanium feel is premium. Very happy user."
                            </p>
                        </div>
                        <div class="pt-3 border-top border-secondary border-opacity-25">
                            <h6 class="text-white mb-1 fw-bold">Thabo K.</h6>
                            <span class="text-warning small" style="font-size: 0.8rem;"><i class="fas fa-check-circle me-1"></i> Verified Buyer</span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Instagram Banner CTA -->
<section class="py-5 text-center" style="background-color: var(--bg-secondary); border-top: 1px solid var(--border-color-light);">
    <div class="container">
        <h4 class="text-white mb-3" style="font-family: 'Outfit', sans-serif;"><i class="fab fa-instagram text-warning me-2"></i> Join Our Instagram Community</h4>
        <p class="text-secondary mb-4" style="font-size: 0.95rem;">Follow @mbatha_iphone_plug for live videos of devices, client reviews, and direct drops.</p>
        <a href="https://instagram.com/mbatha_iphone_plug" target="_blank" class="btn btn-outline-gold px-4"><i class="fab fa-instagram me-2"></i> Go to Instagram</a>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
