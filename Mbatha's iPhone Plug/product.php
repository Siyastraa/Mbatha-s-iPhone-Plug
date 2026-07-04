<?php
// Mbatha's iPhone Plug - Product Details

require_once 'config/db.php';
require_once 'includes/functions.php';
require_once 'includes/cart_handler.php';

// Validate Product ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: shop.php");
    exit;
}

$product_id = (int)$_GET['id'];

// Fetch Product Details
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    header("Location: shop.php");
    exit;
}

// Handle Add To Cart / Buy Now
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $qty = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    $res = addToCart($product_id, $qty);
    
    if ($res['success']) {
        setFlashMessage($res['message'], 'success');
        if ($_POST['action'] === 'buy_now') {
            header("Location: checkout.php");
            exit;
        }
    } else {
        setFlashMessage($res['message'], 'danger');
    }
    header("Location: product.php?id=" . $product_id);
    exit;
}

// Handle Add Review Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $name = sanitize($_POST['review_name']);
    $comment = sanitize($_POST['review_comment']);
    $rating = (int)$_POST['review_rating'];
    
    if (!empty($name) && !empty($comment) && $rating >= 1 && $rating <= 5) {
        $stmt = $pdo->prepare("INSERT INTO reviews (product_id, rating, name, comment) VALUES (?, ?, ?, ?)");
        $stmt->execute([$product_id, $rating, $name, $comment]);
        setFlashMessage("Thank you! Your verified purchase review has been submitted.", "success");
    } else {
        setFlashMessage("Please complete all review fields correctly.", "danger");
    }
    header("Location: product.php?id=" . $product_id);
    exit;
}

// Fetch Reviews for this product
$stmt = $pdo->prepare("SELECT * FROM reviews WHERE product_id = ? ORDER BY created_at DESC");
$stmt->execute([$product_id]);
$product_reviews = $stmt->fetchAll();

// Fetch Related Products (same category, different ID)
$stmt = $pdo->prepare("SELECT * FROM products WHERE category = ? AND id != ? LIMIT 4");
$stmt->execute([$product['category'], $product_id]);
$related_products = $stmt->fetchAll();

// Pre-built WhatsApp order text
$whatsapp_number = "27712345678";
$whatsapp_text = "Hello Mbatha's iPhone Plug! I'd like to order the " . $product['name'] . " (" . ($product['storage'] ?? 'N/A') . " - " . ($product['color'] ?? 'N/A') . " - Grade " . $product['grade'] . ") listed for " . formatPrice($product['price']) . " on your website. Reference SKU: SKU-" . $product['id'];
$whatsapp_link = "https://wa.me/" . $whatsapp_number . "?text=" . urlencode($whatsapp_text);

require_once 'includes/header.php';
?>

<!-- Breadcrumbs -->
<div class="py-3 border-bottom border-secondary border-opacity-10 bg-secondary bg-opacity-10">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0" style="font-size: 0.85rem;">
                <li class="breadcrumb-item"><a href="index.php" class="text-warning text-decoration-none">Home</a></li>
                <li class="breadcrumb-item"><a href="shop.php" class="text-warning text-decoration-none">Shop</a></li>
                <li class="breadcrumb-item text-white active" aria-current="page"><?php echo sanitize($product['name']); ?></li>
            </ol>
        </nav>
    </div>
</div>

<!-- Product Details Section -->
<div class="container py-5">
    <div class="row g-5">
        <!-- Gallery Column -->
        <div class="col-lg-6" data-aos="fade-right">
            <div class="gallery-main">
                <!-- Fallback resolved image -->
                <img src="<?php echo getProductImage($product['image_url'], $product['name'], $product['category']); ?>" class="img-fluid" alt="<?php echo sanitize($product['name']); ?>">
            </div>
            
            <!-- Gallery Thumbnails (Simulating multiple slots using placeholders) -->
            <div class="gallery-thumbs">
                <div class="gallery-thumb active">
                    <img src="<?php echo getProductImage($product['image_url'], $product['name'], $product['category']); ?>" alt="Angle 1">
                </div>
                <div class="gallery-thumb">
                    <!-- Simulate charger representation or secondary details to create professional visuals -->
                    <img src="assets/images/placeholder.php?name=<?php echo urlencode($product['name']); ?>+Box&type=charger" alt="Angle 2">
                </div>
                <div class="gallery-thumb">
                    <img src="assets/images/placeholder.php?name=<?php echo urlencode($product['name']); ?>+Side&type=iphone" alt="Angle 3">
                </div>
            </div>
        </div>

        <!-- Specifications & Purchase Details Column -->
        <div class="col-lg-6" data-aos="fade-left">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-warning text-uppercase tracking-widest fw-bold small"><?php echo sanitize($product['category']); ?></span>
                <span class="badge-grade py-1 px-3 position-relative top-0 right-0"><?php echo sanitize($product['grade']); ?></span>
            </div>
            
            <h1 class="text-white mb-3 fw-bold" style="font-family: 'Outfit', sans-serif;"><?php echo sanitize($product['name']); ?></h1>
            
            <!-- Price Block -->
            <div class="d-flex align-items-baseline gap-3 mb-4">
                <h2 class="text-gradient fw-bold mb-0" style="font-size: 2.2rem; font-family: 'Outfit', sans-serif;">
                    <?php echo formatPrice($product['price']); ?>
                </h2>
                <?php if ($product['original_price']): ?>
                    <h5 class="text-muted text-decoration-line-through mb-0">
                        <?php echo formatPrice($product['original_price']); ?>
                    </h5>
                    <span class="badge bg-danger rounded-pill px-2 py-1 small" style="font-size: 0.75rem;">
                        Save <?php echo formatPrice($product['original_price'] - $product['price']); ?>
                    </span>
                <?php endif; ?>
            </div>

            <!-- Description -->
            <p class="text-secondary mb-4 fs-6">
                <?php echo !empty($product['description']) ? nl2br(sanitize($product['description'])) : 'No custom description provided for this device. Contact Mbatha on WhatsApp for details.'; ?>
            </p>

            <!-- Diagnostic Indicators Table -->
            <div class="glass-card mb-4 p-3">
                <div class="row text-center g-3">
                    <?php if ($product['battery_health']): ?>
                        <div class="col-4 border-end border-secondary border-opacity-25">
                            <span class="text-muted small d-block">Battery Health</span>
                            <span class="text-white fw-bold text-gradient fs-5"><i class="fas fa-battery-full me-1 text-success"></i> <?php echo $product['battery_health']; ?>%</span>
                        </div>
                    <?php endif; ?>
                    <div class="col-4 border-end border-secondary border-opacity-25">
                        <span class="text-muted small d-block">Warranty Period</span>
                        <span class="text-white fw-bold text-gradient fs-5" style="font-size: 0.95rem;"><?php echo sanitize($product['warranty']); ?></span>
                    </div>
                    <div class="col-4">
                        <span class="text-muted small d-block">Stock Level</span>
                        <span class="fw-bold <?php echo $product['stock'] > 0 ? 'text-success' : 'text-danger'; ?>" style="font-size: 0.95rem;">
                            <?php echo $product['stock'] > 0 ? ($product['stock'] == 1 ? 'Only 1 Left!' : $product['stock'] . ' In Stock') : 'Sold Out'; ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Purchase CTAs -->
            <?php if ($product['stock'] > 0): ?>
                <form action="product.php?id=<?php echo $product_id; ?>" method="POST" class="mb-4">
                    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                    
                    <div class="row g-3 align-items-center mb-4">
                        <div class="col-3 col-md-2">
                            <label class="form-label-custom mb-0">Qty:</label>
                        </div>
                        <div class="col-5 col-md-3">
                            <select name="quantity" class="form-select form-control-custom py-2">
                                <?php for ($i = 1; $i <= min(5, $product['stock']); $i++): ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex flex-column flex-sm-row gap-3">
                        <button type="submit" name="action" value="buy_now" class="btn btn-gold flex-grow-1 py-3"><i class="fas fa-credit-card me-2"></i> Buy Now</button>
                        <button type="submit" name="action" value="add_to_cart" class="btn btn-outline-gold flex-grow-1 py-3"><i class="fas fa-shopping-cart me-2"></i> Add To Cart</button>
                    </div>
                </form>
            <?php endif; ?>

            <!-- WhatsApp Direct Button -->
            <a href="<?php echo $whatsapp_link; ?>" target="_blank" class="btn btn-success w-100 py-3 mb-4 rounded-pill fw-bold text-white d-flex align-items-center justify-content-center gap-2" style="background-color: #25d366; border: none; box-shadow: 0 4px 15px rgba(37, 211, 102, 0.3);">
                <i class="fab fa-whatsapp fs-4"></i> Order Instantly on WhatsApp
            </a>

            <!-- Quick Specs Table -->
            <h5 class="text-white mb-3" style="font-family: 'Outfit', sans-serif;">Full Specifications</h5>
            <table class="table glass-table text-white mb-0">
                <tbody>
                    <?php if ($product['model']): ?>
                        <tr><td class="text-muted w-40">Model Series</td><td><?php echo sanitize($product['model']); ?></td></tr>
                    <?php endif; ?>
                    <?php if ($product['storage']): ?>
                        <tr><td class="text-muted">Storage Capacity</td><td><?php echo sanitize($product['storage']); ?></td></tr>
                    <?php endif; ?>
                    <?php if ($product['color']): ?>
                        <tr><td class="text-muted">Color Option</td><td><?php echo sanitize($product['color']); ?></td></tr>
                    <?php endif; ?>
                    <?php if ($product['grade']): ?>
                        <tr><td class="text-muted">Physical Grade</td><td><?php echo sanitize($product['grade']); ?></td></tr>
                    <?php endif; ?>
                    <?php if ($product['battery_health']): ?>
                        <tr><td class="text-muted">Battery Capacity</td><td><?php echo $product['battery_health']; ?>% Maximum Capacity</td></tr>
                    <?php endif; ?>
                    <tr><td class="text-muted">Warranty Type</td><td><?php echo sanitize($product['warranty']); ?> included</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Review Section -->
    <div class="row mt-5 pt-5 border-top border-secondary border-opacity-25 g-5">
        <!-- Review Loop -->
        <div class="col-lg-7" data-aos="fade-right">
            <h3 class="text-white mb-4" style="font-family: 'Outfit', sans-serif;">Customer Reviews</h3>
            
            <?php if (!empty($product_reviews)): ?>
                <?php foreach ($product_reviews as $rev): ?>
                    <div class="review-item">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="text-white mb-0 fw-bold"><?php echo sanitize($rev['name']); ?></h6>
                            <span class="text-muted small" style="font-size: 0.75rem;"><?php echo date('Y-m-d', strtotime($rev['created_at'])); ?></span>
                        </div>
                        <div class="stars mb-2">
                            <?php for ($i=1; $i<=5; $i++): ?>
                                <i class="<?php echo ($i <= $rev['rating']) ? 'fas' : 'far'; ?> fa-star"></i>
                            <?php endfor; ?>
                            <span class="text-warning ms-2 small" style="font-size: 0.75rem;"><i class="fas fa-check-circle"></i> Verified Buyer</span>
                        </div>
                        <p class="text-secondary mb-0 small"><?php echo sanitize($rev['comment']); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-secondary">No client reviews submitted for this phone yet. Be the first to share your purchase!</p>
            <?php endif; ?>
        </div>

        <!-- Write a Review Form -->
        <div class="col-lg-5" data-aos="fade-left">
            <div class="glass-card">
                <h4 class="text-white mb-3" style="font-family: 'Outfit', sans-serif;">Write a Review</h4>
                <form action="product.php?id=<?php echo $product_id; ?>" method="POST">
                    <div class="mb-3">
                        <label class="form-label-custom">Your Full Name</label>
                        <input type="text" name="review_name" class="form-control form-control-custom" placeholder="e.g. Lerato Khumalo" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label-custom">Rating Score</label>
                        <select name="review_rating" class="form-select form-control-custom" required>
                            <option value="5">★★★★★ - Excellent (5 Stars)</option>
                            <option value="4">★★★★☆ - Very Good (4 Stars)</option>
                            <option value="3">★★★☆☆ - Average (3 Stars)</option>
                            <option value="2">★★☆☆☆ - Disappointed (2 Stars)</option>
                            <option value="1">★☆☆☆☆ - Unacceptable (1 Star)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label-custom">Your Comments</label>
                        <textarea name="review_comment" class="form-control form-control-custom" rows="3" placeholder="Tell us about the device performance and transaction..." required></textarea>
                    </div>
                    <button type="submit" name="submit_review" class="btn btn-gold w-100 mt-2">Submit Review</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Related Products Grid -->
    <?php if (!empty($related_products)): ?>
        <div class="mt-5 pt-5 border-top border-secondary border-opacity-25" data-aos="fade-up">
            <h3 class="text-white mb-4" style="font-family: 'Outfit', sans-serif;">Related Apple Devices</h3>
            <div class="row g-4">
                <?php foreach ($related_products as $related): ?>
                    <div class="col-lg-3 col-sm-6">
                        <div class="product-card h-100">
                            <span class="badge-grade"><?php echo sanitize($related['grade']); ?></span>
                            <div class="product-img-wrapper">
                                <img src="<?php echo getProductImage($related['image_url'], $related['name'], $related['category']); ?>" alt="<?php echo sanitize($related['name']); ?>">
                            </div>
                            <div class="product-details">
                                <span class="text-uppercase text-secondary small fw-bold" style="font-size: 0.7rem;"><?php echo sanitize($related['category']); ?></span>
                                <h4 class="product-title mt-1" style="font-size: 0.95rem;">
                                    <a href="product.php?id=<?php echo $related['id']; ?>"><?php echo sanitize($related['name']); ?></a>
                                </h4>
                                <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top border-secondary border-opacity-25">
                                    <span class="product-price" style="font-size: 1.05rem;"><?php echo formatPrice($related['price']); ?></span>
                                    <a href="product.php?id=<?php echo $related['id']; ?>" class="btn btn-gold p-1 px-3 rounded-pill" style="font-size: 0.8rem;"><i class="fas fa-eye text-dark"></i> View</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
