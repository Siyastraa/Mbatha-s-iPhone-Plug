<?php
// Mbatha's iPhone Plug - Checkout Order Form

require_once 'config/db.php';
require_once 'includes/functions.php';
require_once 'includes/cart_handler.php';

// Redirect if cart is empty
$cart_items = getCartItems();
if (empty($cart_items)) {
    header("Location: cart.php");
    exit;
}

$subtotal = getCartSubtotal();
$discount = getDiscountAmount();
$delivery = getDeliveryFee();
$grand_total = getCartGrandTotal();

// Pre-fill user profile fields if logged in
$user = getLoggedInUser();
$pre_name = $user ? $user['name'] : '';
$pre_email = $user ? $user['email'] : '';
$pre_phone = $user ? $user['phone'] : '';
$pre_address = $user ? $user['address'] : '';

// Process Checkout Form POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    
    // Compile full address
    $street = sanitize($_POST['address_street']);
    $suburb = sanitize($_POST['address_suburb']);
    $city = sanitize($_POST['address_city']);
    $province = sanitize($_POST['address_province']);
    $postal = sanitize($_POST['address_postal']);
    $full_address = $street . ", " . $suburb . ", " . $city . ", " . $province . " - " . $postal;
    
    $payment_method = sanitize($_POST['payment_method']);
    $coupon_code = isset($_SESSION['coupon']) ? $_SESSION['coupon']['code'] : null;
    $user_id = $user ? $user['id'] : null;
    
    try {
        // Begin Transaction to guarantee database consistency
        $pdo->beginTransaction();
        
        // 1. Insert order record
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, customer_name, email, phone, address, payment_method, status, order_total, coupon_code, discount_amount) VALUES (?, ?, ?, ?, ?, ?, 'Pending', ?, ?, ?)");
        $stmt->execute([$user_id, $name, $email, $phone, $full_address, $payment_method, $grand_total, $coupon_code, $discount]);
        
        $order_id = $pdo->lastInsertId();
        
        // 2. Insert line items & decrement stock
        foreach ($cart_items as $item) {
            // Log order item
            $stmt_item = $pdo->prepare("INSERT INTO order_items (order_id, product_id, product_name, price, quantity) VALUES (?, ?, ?, ?, ?)");
            $stmt_item->execute([$order_id, $item['id'], $item['name'], $item['price'], $item['quantity']]);
            
            // Decrement inventory stock
            $stmt_stock = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
            $stmt_stock->execute([$item['quantity'], $item['id']]);
        }
        
        $pdo->commit();
        
        // Clear Cart session data
        clearCart();
        
        // Redirect to confirmation success invoice page
        header("Location: confirmation.php?order_id=" . $order_id);
        exit;
        
    } catch (Exception $e) {
        $pdo->rollBack();
        setFlashMessage("Failed to place your order due to a database exception: " . $e->getMessage(), "danger");
    }
}

require_once 'includes/header.php';
?>

<div class="py-5" style="background: radial-gradient(circle at 50% 10%, rgba(212, 175, 55, 0.04) 0%, rgba(11, 11, 12, 1) 50%); border-bottom: 1px solid var(--border-color-light);">
    <div class="container text-center">
        <h1 class="text-white mb-2 fw-bold" style="font-family: 'Outfit', sans-serif;">Checkout Order</h1>
        <p class="text-secondary max-width-600 mx-auto mb-0">Fill in your delivery address and payment specifications to finalize your upgrade purchase.</p>
    </div>
</div>

<div class="container py-5">
    <div class="row g-5">
        <!-- Billing & Shipping Details Form -->
        <div class="col-lg-7" data-aos="fade-right">
            <div class="glass-card">
                <h3 class="text-white mb-4 fw-bold" style="font-family: 'Outfit', sans-serif;">Delivery Details</h3>
                
                <form action="checkout.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label-custom">Full Customer Name</label>
                        <input type="text" name="name" class="form-control form-control-custom" placeholder="e.g. Lerato Khumalo" value="<?php echo sanitize($pre_name); ?>" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label-custom">Email Address</label>
                            <input type="email" name="email" class="form-control form-control-custom" placeholder="e.g. lerato@gmail.com" value="<?php echo sanitize($pre_email); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">WhatsApp Phone Number</label>
                            <input type="tel" name="phone" class="form-control form-control-custom" placeholder="e.g. 0712345678" value="<?php echo sanitize($pre_phone); ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label-custom">Street Address</label>
                        <input type="text" name="address_street" class="form-control form-control-custom" placeholder="e.g. 15 Sandton Gate Drive, Apt 4" value="<?php echo sanitize($pre_address); ?>" required>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label-custom">Suburb</label>
                            <input type="text" name="address_suburb" class="form-control form-control-custom" placeholder="e.g. Glenadrienne" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">City</label>
                            <input type="text" name="address_city" class="form-control form-control-custom" placeholder="e.g. Sandton" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label-custom">Province</label>
                            <select name="address_province" class="form-select form-control-custom" required>
                                <option value="Gauteng">Gauteng</option>
                                <option value="Western Cape">Western Cape</option>
                                <option value="KwaZulu-Natal">KwaZulu-Natal</option>
                                <option value="Eastern Cape">Eastern Cape</option>
                                <option value="Free State">Free State</option>
                                <option value="Mpumalanga">Mpumalanga</option>
                                <option value="Limpopo">Limpopo</option>
                                <option value="North West">North West</option>
                                <option value="Northern Cape">Northern Cape</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Postal Code</label>
                            <input type="text" name="address_postal" class="form-control form-control-custom" placeholder="e.g. 2196" required>
                        </div>
                    </div>

                    <!-- Payment Option (EFT Bank Transfer Instruction block) -->
                    <h3 class="text-white mb-3 fw-bold" style="font-family: 'Outfit', sans-serif;">Payment Options</h3>
                    <div class="p-3 border border-warning rounded-4 mb-4" style="background: rgba(212,175,55,0.04);">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="payment_eft" value="EFT Bank Transfer" checked>
                            <label class="form-check-label text-white fw-bold" for="payment_eft">
                                <i class="fas fa-university text-warning me-2"></i> EFT / Bank Transfer
                            </label>
                        </div>
                        <p class="text-secondary small mt-2 mb-0">
                            Place your order, then transfer funds directly to our bank account using your order ID as payment reference. Orders ship once payment reflects in our bank account.
                        </p>
                    </div>

                    <button type="submit" name="place_order" class="btn btn-gold w-100 py-3 fw-bold fs-5"><i class="fas fa-lock me-2"></i> Place EFT Order</button>
                </form>
            </div>
        </div>

        <!-- Order Summary Card Sidebar -->
        <div class="col-lg-5" data-aos="fade-left">
            <div class="glass-card sticky-top" style="top: 100px;">
                <h4 class="text-white mb-4 fw-bold" style="font-family: 'Outfit', sans-serif;">Order Summary</h4>
                
                <!-- Order listing grid loop -->
                <div class="mb-4">
                    <?php foreach ($cart_items as $item): ?>
                        <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom border-secondary border-opacity-10">
                            <div>
                                <h6 class="text-white mb-0 fw-semibold" style="font-size: 0.9rem;"><?php echo sanitize($item['name']); ?></h6>
                                <span class="text-muted small"><?php echo sanitize($item['storage']); ?> | Qty: <?php echo $item['quantity']; ?></span>
                            </div>
                            <span class="text-warning small fw-bold"><?php echo formatPrice($item['subtotal']); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="d-flex justify-content-between mb-2">
                    <span class="text-secondary small">Subtotal</span>
                    <span class="text-white small fw-bold"><?php echo formatPrice($subtotal); ?></span>
                </div>
                
                <?php if ($discount > 0): ?>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-secondary small">Coupon Discount</span>
                        <span class="text-success small fw-bold">-<?php echo formatPrice($discount); ?></span>
                    </div>
                <?php endif; ?>

                <div class="d-flex justify-content-between mb-3 pb-2 border-bottom border-secondary border-opacity-25">
                    <span class="text-secondary small">Courier Shipping</span>
                    <span class="text-white small fw-bold">
                        <?php echo $delivery == 0 ? '<span class="text-success">FREE</span>' : formatPrice($delivery); ?>
                    </span>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-0">
                    <span class="text-white fw-bold">Grand Total</span>
                    <span class="text-warning fs-4 fw-bold text-gradient" style="font-family: 'Outfit', sans-serif;"><?php echo formatPrice($grand_total); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
