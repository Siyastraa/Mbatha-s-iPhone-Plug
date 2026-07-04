<?php
// Mbatha's iPhone Plug - Shop Catalog

require_once 'config/db.php';
require_once 'includes/functions.php';
require_once 'includes/cart_handler.php';

// Handle Wishlist Toggle
if (isset($_GET['toggle_wishlist'])) {
    $prod_id = (int)$_GET['toggle_wishlist'];
    $res = toggleWishlist($prod_id);
    setFlashMessage($res['message'], 'success');
    
    // Redirect back without the parameter
    $params = $_GET;
    unset($params['toggle_wishlist']);
    $query = http_build_query($params);
    $redirect = 'shop.php' . (!empty($query) ? '?' . $query : '');
    header("Location: " . $redirect);
    exit;
}

// -----------------------------------------------------
// SQL Query Builder based on Active Filters
// -----------------------------------------------------
$params = [];
$where_clauses = ["type = 'iphone'"]; // Default to iPhones on main shop page

// Search Filter
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search = sanitize($_GET['search']);
    $where_clauses[] = "(name LIKE ? OR model LIKE ? OR description LIKE ? OR storage LIKE ? OR color LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

// Model Filter
if (isset($_GET['model']) && !empty($_GET['model'])) {
    $model = sanitize($_GET['model']);
    $where_clauses[] = "model = ?";
    $params[] = $model;
}

// Color Filter
if (isset($_GET['color']) && !empty($_GET['color'])) {
    $color = sanitize($_GET['color']);
    $where_clauses[] = "color = ?";
    $params[] = $color;
}

// Storage Filter
if (isset($_GET['storage']) && !empty($_GET['storage'])) {
    $storage = sanitize($_GET['storage']);
    $where_clauses[] = "storage = ?";
    $params[] = $storage;
}

// Grade Filter
if (isset($_GET['grade']) && !empty($_GET['grade'])) {
    $grade = sanitize($_GET['grade']);
    $where_clauses[] = "grade = ?";
    $params[] = $grade;
}

// Battery Health Filter
if (isset($_GET['battery']) && !empty($_GET['battery'])) {
    $battery_val = (int)$_GET['battery'];
    $where_clauses[] = "battery_health >= ?";
    $params[] = $battery_val;
}

// Price Slider Filter
$max_price = 20000;
if (isset($_GET['price_range']) && !empty($_GET['price_range'])) {
    $max_price = (float)$_GET['price_range'];
    $where_clauses[] = "price <= ?";
    $params[] = $max_price;
}

// Flash Sale toggle
if (isset($_GET['flash']) && $_GET['flash'] == 1) {
    $where_clauses[] = "is_flash_sale = 1";
}

// Wishlist page toggle
if (isset($_GET['wishlist']) && $_GET['wishlist'] == 1) {
    if (empty($_SESSION['wishlist'])) {
        $where_clauses[] = "id = -1"; // Matches nothing
    } else {
        $placeholders = implode(',', array_fill(0, count($_SESSION['wishlist']), '?'));
        $where_clauses[] = "id IN ($placeholders)";
        foreach ($_SESSION['wishlist'] as $w_id) {
            $params[] = $w_id;
        }
    }
}

// Construct WHERE query
$where_sql = "";
if (!empty($where_clauses)) {
    $where_sql = " WHERE " . implode(" AND ", $where_clauses);
}

// Sorting Order
$sort_by = "created_at DESC"; // Default Newest
if (isset($_GET['sort']) && !empty($_GET['sort'])) {
    switch ($_GET['sort']) {
        case 'price_asc':
            $sort_by = "price ASC";
            break;
        case 'price_desc':
            $sort_by = "price DESC";
            break;
        case 'oldest':
            $sort_by = "created_at ASC";
            break;
    }
}

// Combine Query
$query_str = "SELECT * FROM products" . $where_sql . " ORDER BY " . $sort_by;
$stmt = $pdo->prepare($query_str);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Get unique values for filters sidebar (calculated from database products of type='iphone')
$models_list = $pdo->query("SELECT DISTINCT model FROM products WHERE type = 'iphone' AND model IS NOT NULL ORDER BY model DESC")->fetchAll(PDO::FETCH_COLUMN);
$colors_list = $pdo->query("SELECT DISTINCT color FROM products WHERE type = 'iphone' AND color IS NOT NULL ORDER BY color ASC")->fetchAll(PDO::FETCH_COLUMN);
$storages_list = $pdo->query("SELECT DISTINCT storage FROM products WHERE type = 'iphone' AND storage IS NOT NULL ORDER BY CAST(storage AS UNSIGNED) ASC")->fetchAll(PDO::FETCH_COLUMN);

require_once 'includes/header.php';
?>

<div class="py-5" style="background: radial-gradient(circle at 50% 10%, rgba(212, 175, 55, 0.04) 0%, rgba(11, 11, 12, 1) 50%); border-bottom: 1px solid var(--border-color-light);">
    <div class="container">
        <h1 class="text-white mb-2 fw-bold text-center text-md-start" style="font-family: 'Outfit', sans-serif;">
            <?php 
            if (isset($_GET['wishlist']) && $_GET['wishlist'] == 1) {
                echo "My Saved Wishlist";
            } elseif (isset($_GET['flash']) && $_GET['flash'] == 1) {
                echo "Flash Deals & Promotions";
            } else {
                echo "Pre-Owned iPhones Price List";
            }
            ?>
        </h1>
        <p class="text-secondary mb-0 text-center text-md-start">
            <?php 
            if (isset($_GET['wishlist']) && $_GET['wishlist'] == 1) {
                echo "All your favorite premium devices saved in one place.";
            } else {
                echo "Every single device is certified, 100% original, battery health checked, and includes a 6-month warranty.";
            }
            ?>
        </p>
    </div>
</div>

<div class="container py-5">
    <div class="row g-4">
        <!-- Sidebar Filter controls -->
        <div class="col-lg-3 col-md-4">
            <div class="glass-card p-4 sticky-top" style="top: 100px; z-index: 10;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="text-white mb-0 fw-bold"><i class="fas fa-filter text-warning me-2"></i> Filters</h5>
                    <a href="shop.php" class="text-warning text-decoration-none small hover-gold">Clear All</a>
                </div>
                
                <form action="shop.php" method="GET" id="shopFiltersForm">
                    <!-- Preserve search and sorting parameters -->
                    <?php if (isset($_GET['search'])): ?>
                        <input type="hidden" name="search" value="<?php echo sanitize($_GET['search']); ?>">
                    <?php endif; ?>
                    <?php if (isset($_GET['sort'])): ?>
                        <input type="hidden" name="sort" value="<?php echo sanitize($_GET['sort']); ?>">
                    <?php endif; ?>
                    <?php if (isset($_GET['wishlist'])): ?>
                        <input type="hidden" name="wishlist" value="1">
                    <?php endif; ?>
                    
                    <!-- Model Filter -->
                    <div class="mb-4">
                        <label class="form-label-custom d-block">Model Series</label>
                        <select name="model" class="form-select form-control-custom" onchange="this.form.submit()">
                            <option value="">All Models</option>
                            <?php foreach ($models_list as $m): ?>
                                <option value="<?php echo sanitize($m); ?>" <?php echo (isset($_GET['model']) && $_GET['model'] == $m) ? 'selected' : ''; ?>>
                                    <?php echo sanitize($m); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Storage Filter -->
                    <div class="mb-4">
                        <label class="form-label-custom d-block">Storage Capacity</label>
                        <select name="storage" class="form-select form-control-custom" onchange="this.form.submit()">
                            <option value="">All Capacities</option>
                            <?php foreach ($storages_list as $s): ?>
                                <option value="<?php echo sanitize($s); ?>" <?php echo (isset($_GET['storage']) && $_GET['storage'] == $s) ? 'selected' : ''; ?>>
                                    <?php echo sanitize($s); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Color Filter -->
                    <div class="mb-4">
                        <label class="form-label-custom d-block">Device Color</label>
                        <select name="color" class="form-select form-control-custom" onchange="this.form.submit()">
                            <option value="">All Colors</option>
                            <?php foreach ($colors_list as $c): ?>
                                <option value="<?php echo sanitize($c); ?>" <?php echo (isset($_GET['color']) && $_GET['color'] == $c) ? 'selected' : ''; ?>>
                                    <?php echo sanitize($c); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Grade Filter -->
                    <div class="mb-4">
                        <label class="form-label-custom d-block">Condition Grade</label>
                        <select name="grade" class="form-select form-control-custom" onchange="this.form.submit()">
                            <option value="">All Grades</option>
                            <option value="New" <?php echo (isset($_GET['grade']) && $_GET['grade'] == 'New') ? 'selected' : ''; ?>>New / Sealed</option>
                            <option value="Like New" <?php echo (isset($_GET['grade']) && $_GET['grade'] == 'Like New') ? 'selected' : ''; ?>>Like New (99%)</option>
                            <option value="Excellent" <?php echo (isset($_GET['grade']) && $_GET['grade'] == 'Excellent') ? 'selected' : ''; ?>>Excellent (Grade A)</option>
                            <option value="Very Good" <?php echo (isset($_GET['grade']) && $_GET['grade'] == 'Very Good') ? 'selected' : ''; ?>>Very Good (Grade B)</option>
                        </select>
                    </div>

                    <!-- Battery Health Filter -->
                    <div class="mb-4">
                        <label class="form-label-custom d-block">Minimum Battery Health</label>
                        <select name="battery" class="form-select form-control-custom" onchange="this.form.submit()">
                            <option value="">Any Battery %</option>
                            <option value="95" <?php echo (isset($_GET['battery']) && $_GET['battery'] == '95') ? 'selected' : ''; ?>>95%+ Brand New Feel</option>
                            <option value="90" <?php echo (isset($_GET['battery']) && $_GET['battery'] == '90') ? 'selected' : ''; ?>>90%+ Peak Performance</option>
                            <option value="85" <?php echo (isset($_GET['battery']) && $_GET['battery'] == '85') ? 'selected' : ''; ?>>85%+ Original Health</option>
                        </select>
                    </div>

                    <!-- Price Filter -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <label class="form-label-custom mb-0">Max Budget</label>
                            <span class="text-warning small fw-bold" id="price_range_val"><?php echo formatPrice($max_price); ?></span>
                        </div>
                        <input type="range" name="price_range" id="price_range" class="form-range" min="1500" max="20000" step="500" value="<?php echo $max_price; ?>" onchange="this.form.submit()">
                        <div class="d-flex justify-content-between text-muted" style="font-size: 0.75rem;">
                            <span>R 1,500</span>
                            <span>R 20,000</span>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-gold w-100 mt-2 py-2">Apply Filters</button>
                </form>
            </div>
        </div>

        <!-- Catalog Product Grid -->
        <div class="col-lg-9 col-md-8">
            <!-- Controls bar (Search display + sorting dropdown) -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3 bg-secondary bg-opacity-20 p-3 rounded-4 border border-secondary border-opacity-25" style="backdrop-filter: blur(10px);">
                <div class="text-secondary small">
                    Showing <span class="text-white fw-bold"><?php echo count($products); ?></span> dynamic matching products
                </div>
                
                <div class="d-flex align-items-center gap-2">
                    <label class="text-secondary small text-nowrap mb-0">Sort By:</label>
                    <select class="form-select form-control-custom py-1 px-3" style="width: auto; font-size: 0.85rem;" onchange="location = this.value;">
                        <?php 
                            // Rebuild URL queries preserving filtering parameter
                            $url_base = 'shop.php?';
                            $q_arr = $_GET;
                            
                            $q_arr['sort'] = 'newest';
                            $url_new = $url_base . http_build_query($q_arr);
                            
                            $q_arr['sort'] = 'price_asc';
                            $url_p_asc = $url_base . http_build_query($q_arr);
                            
                            $q_arr['sort'] = 'price_desc';
                            $url_p_desc = $url_base . http_build_query($q_arr);
                            
                            $q_arr['sort'] = 'oldest';
                            $url_old = $url_base . http_build_query($q_arr);
                        ?>
                        <option value="<?php echo $url_new; ?>" <?php echo (!isset($_GET['sort']) || $_GET['sort'] == 'newest') ? 'selected' : ''; ?>>Newest Arrivals</option>
                        <option value="<?php echo $url_p_asc; ?>" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'price_asc') ? 'selected' : ''; ?>>Price: Low to High</option>
                        <option value="<?php echo $url_p_desc; ?>" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'price_desc') ? 'selected' : ''; ?>>Price: High to Low</option>
                        <option value="<?php echo $url_old; ?>" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'oldest') ? 'selected' : ''; ?>>Oldest Catalog</option>
                    </select>
                </div>
            </div>

            <!-- Shop Grid -->
            <?php if (!empty($products)): ?>
                <div class="row g-4">
                    <?php foreach ($products as $product): ?>
                        <div class="col-lg-4 col-sm-6" data-aos="fade-up">
                            <div class="product-card h-100">
                                <span class="badge-grade"><?php echo sanitize($product['grade']); ?></span>
                                
                                <?php if ($product['is_flash_sale']): ?>
                                    <span class="badge-sale" style="top: 45px;"><i class="fas fa-bolt text-warning"></i> SALE</span>
                                <?php endif; ?>
                                
                                <div class="product-img-wrapper">
                                    <img src="<?php echo getProductImage($product['image_url'], $product['name'], $product['category']); ?>" alt="<?php echo sanitize($product['name']); ?>">
                                </div>
                                <div class="product-details">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-uppercase text-secondary small fw-bold" style="font-size: 0.75rem;"><?php echo sanitize($product['category']); ?></span>
                                        <!-- Wishlist Heart Trigger -->
                                        <?php 
                                            $wish_params = $_GET;
                                            $wish_params['toggle_wishlist'] = $product['id'];
                                            $wish_url = 'shop.php?' . http_build_query($wish_params);
                                            $is_wished = in_array($product['id'], $_SESSION['wishlist'] ?? []);
                                        ?>
                                        <a href="<?php echo $wish_url; ?>" class="<?php echo $is_wished ? 'text-danger' : 'text-secondary hover-gold'; ?>" title="Toggle Wishlist">
                                            <i class="<?php echo $is_wished ? 'fas' : 'far'; ?> fa-heart"></i>
                                        </a>
                                    </div>
                                    <h3 class="product-title mt-1" style="font-size: 1.05rem;">
                                        <a href="product.php?id=<?php echo $product['id']; ?>"><?php echo sanitize($product['name']); ?></a>
                                    </h3>
                                    <div class="product-meta">
                                        <span class="meta-tag"><?php echo sanitize($product['storage']); ?></span>
                                        <span class="meta-tag"><?php echo sanitize($product['color']); ?></span>
                                        <span class="meta-tag battery-badge"><i class="fas fa-battery-full me-1"></i> <?php echo $product['battery_health']; ?>% BH</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top border-secondary border-opacity-25">
                                        <div>
                                            <span class="product-price" style="font-size: 1.15rem;"><?php echo formatPrice($product['price']); ?></span>
                                            <?php if ($product['original_price']): ?>
                                                <span class="product-original-price" style="font-size: 0.85rem;"><?php echo formatPrice($product['original_price']); ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-gold p-2 px-3 rounded-pill" title="View Details"><i class="fas fa-eye text-dark"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-search-minus text-warning fa-3x mb-3"></i>
                    <h3 class="text-white">No iPhones Found</h3>
                    <p class="text-secondary max-width-500 mx-auto">We couldn't find any products matching your specific filters. Try expanding your search criteria or resetting filters.</p>
                    <a href="shop.php" class="btn btn-gold px-4 mt-2">Reset Filters</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
