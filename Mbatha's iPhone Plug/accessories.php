<?php
// Mbatha's iPhone Plug - Accessories Catalog

require_once 'config/db.php';
require_once 'includes/functions.php';
require_once 'includes/cart_handler.php';

// Handle Wishlist Toggle
if (isset($_GET['toggle_wishlist'])) {
    $prod_id = (int)$_GET['toggle_wishlist'];
    $res = toggleWishlist($prod_id);
    setFlashMessage($res['message'], 'success');
    
    $params = $_GET;
    unset($params['toggle_wishlist']);
    $query = http_build_query($params);
    $redirect = 'accessories.php' . (!empty($query) ? '?' . $query : '');
    header("Location: " . $redirect);
    exit;
}

// -----------------------------------------------------
// SQL Query Builder based on Active Filters
// -----------------------------------------------------
$params = [];
$where_clauses = ["type = 'accessory'"];

// Category Filter
$active_cat = isset($_GET['cat']) ? sanitize($_GET['cat']) : 'all';
if ($active_cat !== 'all') {
    $where_clauses[] = "category = ?";
    $params[] = $active_cat;
}

// Search Filter
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search = sanitize($_GET['search']);
    $where_clauses[] = "(name LIKE ? OR description LIKE ? OR category LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

// Price Slider Filter
$max_price = 10000;
if (isset($_GET['price_range']) && !empty($_GET['price_range'])) {
    $max_price = (float)$_GET['price_range'];
    $where_clauses[] = "price <= ?";
    $params[] = $max_price;
}

// Construct WHERE query
$where_sql = " WHERE " . implode(" AND ", $where_clauses);

// Fetch Matching Accessories
$query_str = "SELECT * FROM products" . $where_sql . " ORDER BY created_at DESC";
$stmt = $pdo->prepare($query_str);
$stmt->execute($params);
$accessories = $stmt->fetchAll();

// Dynamic list of categories for filter tabs
$categories = ['AirPods', 'Watch', 'Case', 'Charger', 'Powerbank', 'Screen Protector'];

require_once 'includes/header.php';
?>

<div class="py-5" style="background: radial-gradient(circle at 50% 10%, rgba(212, 175, 55, 0.04) 0%, rgba(11, 11, 12, 1) 50%); border-bottom: 1px solid var(--border-color-light);">
    <div class="container">
        <h1 class="text-white mb-2 fw-bold text-center text-md-start" style="font-family: 'Outfit', sans-serif;">Apple Accessories Store</h1>
        <p class="text-secondary mb-0 text-center text-md-start">Premium cases, adapters, power banks, screen protectors, AirPods, and Apple Watches.</p>
    </div>
</div>

<div class="container py-5">
    <!-- Category pills switcher -->
    <div class="d-flex flex-wrap gap-2 justify-content-center mb-5" data-aos="fade-up">
        <a href="accessories.php?cat=all" class="btn <?php echo ($active_cat === 'all') ? 'btn-gold' : 'btn-outline-gold'; ?> px-4 py-2">
            All Accessories
        </a>
        <?php foreach ($categories as $cat): ?>
            <?php 
                $active = ($active_cat === $cat) ? 'btn-gold' : 'btn-outline-gold';
                $plural = $cat;
                if ($cat === 'Watch') $plural = 'Apple Watches';
                else if ($cat === 'Case') $plural = 'Cases';
                else if ($cat === 'Charger') $plural = 'Fast Chargers';
                else if ($cat === 'Powerbank') $plural = 'Power Banks';
                else if ($cat === 'Screen Protector') $plural = 'Screen Protectors';
            ?>
            <a href="accessories.php?cat=<?php echo urlencode($cat); ?>" class="btn <?php echo $active; ?> px-4 py-2">
                <?php echo $plural; ?>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="row g-4">
        <!-- Sidebar Quick Budget Filter -->
        <div class="col-lg-3 col-md-4">
            <div class="glass-card p-4 sticky-top" style="top: 100px; z-index: 10;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="text-white mb-0 fw-bold"><i class="fas fa-filter text-warning me-2"></i> Filters</h5>
                    <a href="accessories.php" class="text-warning text-decoration-none small hover-gold">Reset</a>
                </div>
                
                <form action="accessories.php" method="GET">
                    <?php if ($active_cat !== 'all'): ?>
                        <input type="hidden" name="cat" value="<?php echo sanitize($active_cat); ?>">
                    <?php endif; ?>
                    <?php if (isset($_GET['search'])): ?>
                        <input type="hidden" name="search" value="<?php echo sanitize($_GET['search']); ?>">
                    <?php endif; ?>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <label class="form-label-custom mb-0">Max Budget</label>
                            <span class="text-warning small fw-bold" id="price_range_val"><?php echo formatPrice($max_price); ?></span>
                        </div>
                        <input type="range" name="price_range" id="price_range" class="form-range" min="50" stroke="#d4af37" max="10000" step="50" value="<?php echo $max_price; ?>" onchange="this.form.submit()">
                        <div class="d-flex justify-content-between text-muted" style="font-size: 0.75rem;">
                            <span>R 50</span>
                            <span>R 10,000</span>
                        </div>
                    </div>

                    <!-- Keywords Search -->
                    <div class="mb-4">
                        <label class="form-label-custom d-block">Search keyword</label>
                        <input type="text" name="search" class="form-control form-control-custom py-2" placeholder="e.g. silicon, plug, GPS" value="<?php echo isset($_GET['search']) ? sanitize($_GET['search']) : ''; ?>">
                    </div>

                    <button type="submit" class="btn btn-gold w-100 mt-2 py-2">Apply Filters</button>
                </form>
            </div>
        </div>

        <!-- Catalog Product Grid -->
        <div class="col-lg-9 col-md-8">
            <?php if (!empty($accessories)): ?>
                <div class="row g-4">
                    <?php foreach ($accessories as $product): ?>
                        <div class="col-lg-4 col-sm-6" data-aos="fade-up">
                            <div class="product-card h-100">
                                <span class="badge-grade"><?php echo sanitize($product['grade']); ?></span>
                                
                                <?php if ($product['is_flash_sale']): ?>
                                    <span class="badge-sale">Promo Deal</span>
                                <?php endif; ?>
                                
                                <div class="product-img-wrapper">
                                    <img src="<?php echo getProductImage($product['image_url'], $product['name'], $product['category']); ?>" alt="<?php echo sanitize($product['name']); ?>">
                                </div>
                                <div class="product-details">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-uppercase text-secondary small fw-bold" style="font-size: 0.75rem;"><?php echo sanitize($product['category']); ?></span>
                                        <?php 
                                            $wish_params = $_GET;
                                            $wish_params['toggle_wishlist'] = $product['id'];
                                            $wish_url = 'accessories.php?' . http_build_query($wish_params);
                                            $is_wished = in_array($product['id'], $_SESSION['wishlist'] ?? []);
                                        ?>
                                        <a href="<?php echo $wish_url; ?>" class="<?php echo $is_wished ? 'text-danger' : 'text-secondary hover-gold'; ?>" title="Toggle Wishlist">
                                            <i class="<?php echo $is_wished ? 'fas' : 'far'; ?> fa-heart"></i>
                                        </a>
                                    </div>
                                    <h3 class="product-title mt-1" style="font-size: 1.05rem;">
                                        <a href="product.php?id=<?php echo $product['id']; ?>"><?php echo sanitize($product['name']); ?></a>
                                    </h3>
                                    <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top border-secondary border-opacity-25">
                                        <div>
                                            <span class="product-price" style="font-size: 1.15rem;"><?php echo formatPrice($product['price']); ?></span>
                                            <?php if ($product['original_price']): ?>
                                                <span class="product-original-price" style="font-size: 0.85rem;"><?php echo formatPrice($product['original_price']); ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-gold p-2 px-3 rounded-pill" title="View details"><i class="fas fa-eye text-dark"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-search-minus text-warning fa-3x mb-3"></i>
                    <h3 class="text-white">No Accessories Found</h3>
                    <p class="text-secondary max-width-500 mx-auto">We couldn't find any accessories matching your specific criteria. Try expanding your search or selecting a different tab.</p>
                    <a href="accessories.php" class="btn btn-gold px-4 mt-2">Reset Filter</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
