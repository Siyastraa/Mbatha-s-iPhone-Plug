<?php
// Mbatha's iPhone Plug - Cart and Wishlist Handler

require_once __DIR__ . '/../config/db.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (!isset($_SESSION['wishlist'])) {
    $_SESSION['wishlist'] = [];
}

/**
 * Add an item to the shopping cart
 */
function addToCart($productId, $qty = 1) {
    global $pdo;
    
    // Validate product first
    $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();
    
    if (!$product) {
        return ['success' => false, 'message' => 'Product not found.'];
    }
    
    $currentQty = isset($_SESSION['cart'][$productId]) ? $_SESSION['cart'][$productId] : 0;
    $newQty = $currentQty + $qty;
    
    if ($newQty > $product['stock']) {
        $_SESSION['cart'][$productId] = $product['stock'];
        return ['success' => false, 'message' => "Only {$product['stock']} unit(s) available in stock."];
    }
    
    $_SESSION['cart'][$productId] = $newQty;
    return ['success' => true, 'message' => 'Product added to cart successfully.'];
}

/**
 * Remove an item from the cart
 */
function removeFromCart($productId) {
    if (isset($_SESSION['cart'][$productId])) {
        unset($_SESSION['cart'][$productId]);
    }
}

/**
 * Update cart item quantity
 */
function updateCartQuantity($productId, $qty) {
    global $pdo;
    $qty = max(1, (int)$qty);
    
    $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();
    
    if (!$product) {
        return ['success' => false, 'message' => 'Product not found.'];
    }
    
    if ($qty > $product['stock']) {
        $_SESSION['cart'][$productId] = $product['stock'];
        return ['success' => false, 'message' => "Requested quantity exceeds available stock ({$product['stock']})."];
    }
    
    $_SESSION['cart'][$productId] = $qty;
    return ['success' => true, 'message' => 'Cart updated.'];
}

/**
 * Clear the entire cart and applied coupons
 */
function clearCart() {
    $_SESSION['cart'] = [];
    unset($_SESSION['coupon']);
}

/**
 * Get count of items in the cart
 */
function getCartCount() {
    $count = 0;
    foreach ($_SESSION['cart'] as $qty) {
        $count += $qty;
    }
    return $count;
}

/**
 * Fetch all items in the cart with fresh database details
 */
function getCartItems() {
    global $pdo;
    $items = [];
    
    if (empty($_SESSION['cart'])) {
        return $items;
    }
    
    $placeholders = implode(',', array_fill(0, count($_SESSION['cart']), '?'));
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->execute(array_keys($_SESSION['cart']));
    $products = $stmt->fetchAll();
    
    foreach ($products as $product) {
        $qty = $_SESSION['cart'][$product['id']];
        $subtotal = $product['price'] * $qty;
        
        $items[] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'original_price' => $product['original_price'],
            'image_url' => $product['image_url'],
            'storage' => $product['storage'],
            'color' => $product['color'],
            'grade' => $product['grade'],
            'stock' => $product['stock'],
            'quantity' => $qty,
            'subtotal' => $subtotal
        ];
    }
    
    return $items;
}

/**
 * Calculate raw total of all items in cart
 */
function getCartSubtotal() {
    $items = getCartItems();
    $subtotal = 0.00;
    foreach ($items as $item) {
        $subtotal += $item['subtotal'];
    }
    return $subtotal;
}

/**
 * Apply coupon code
 */
function applyCoupon($code) {
    global $pdo;
    $code = strtoupper(trim($code));
    
    $stmt = $pdo->prepare("SELECT * FROM coupons WHERE code = ? AND active = 1 AND expiry_date >= DATE('now')");
    // For compatibility, if MySQL it checks date, SQLite uses 'now' or date()
    if (ACTIVE_DB_TYPE === 'MySQL') {
        $stmt = $pdo->prepare("SELECT * FROM coupons WHERE code = ? AND active = 1 AND expiry_date >= CURDATE()");
    }
    
    $stmt->execute([$code]);
    $coupon = $stmt->fetch();
    
    if (!$coupon) {
        return ['success' => false, 'message' => 'Invalid or expired coupon code.'];
    }
    
    $_SESSION['coupon'] = [
        'code' => $coupon['code'],
        'type' => $coupon['type'],
        'value' => (float)$coupon['value']
    ];
    
    return ['success' => true, 'message' => 'Coupon code applied successfully!'];
}

/**
 * Calculate coupon discount amount
 */
function getDiscountAmount() {
    if (!isset($_SESSION['coupon'])) {
        return 0.00;
    }
    
    $subtotal = getCartSubtotal();
    $coupon = $_SESSION['coupon'];
    
    if ($coupon['type'] === 'percentage') {
        return round(($subtotal * ($coupon['value'] / 100)), 2);
    } else {
        return min($coupon['value'], $subtotal); // Flat discount cannot exceed subtotal
    }
}

/**
 * Get delivery charge
 * Nationwide courier is flat R150 in South Africa. Free if cart is over R12,000.
 */
function getDeliveryFee() {
    $subtotal = getCartSubtotal();
    if ($subtotal == 0 || $subtotal >= 12000) {
        return 0.00;
    }
    return 150.00;
}

/**
 * Final payable total
 */
function getCartGrandTotal() {
    $subtotal = getCartSubtotal();
    $discount = getDiscountAmount();
    $delivery = getDeliveryFee();
    return max(0, $subtotal - $discount + $delivery);
}

/**
 * Toggle item in wishlist
 */
function toggleWishlist($productId) {
    if (in_array($productId, $_SESSION['wishlist'])) {
        $_SESSION['wishlist'] = array_diff($_SESSION['wishlist'], [$productId]);
        return ['success' => true, 'in_wishlist' => false, 'message' => 'Removed from wishlist.'];
    } else {
        $_SESSION['wishlist'][] = $productId;
        return ['success' => true, 'in_wishlist' => true, 'message' => 'Added to wishlist.'];
    }
}
?>
