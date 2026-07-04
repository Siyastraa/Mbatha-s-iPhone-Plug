<?php
// Mbatha's iPhone Plug - Cart Overview

require_once 'config/db.php';
require_once 'includes/functions.php';
require_once 'includes/cart_handler.php';

// Handle Cart updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Apply Coupon code
    if (isset($_POST['apply_coupon'])) {
        $code = sanitize($_POST['coupon_code']);
        $res = applyCoupon($code);
        if ($res['success']) {
            setFlashMessage($res['message'], 'success');
        } else {
            setFlashMessage($res['message'], 'danger');
        }
        header("Location: cart.php");
        exit;
    }
    
    // 2. Remove Coupon
    if (isset($_POST['remove_coupon'])) {
        unset($_SESSION['coupon']);
        setFlashMessage("Coupon code removed.", "info");
        header("Location: cart.php");
        exit;
    }
    
    // 3. Update quantities
    if (isset($_POST['update_cart'])) {
        foreach ($_POST['qty'] as $prod_id => $qty) {
            updateCartQuantity((int)$prod_id, (int)$qty);
        }
        setFlashMessage("Cart quantities updated successfully.", "success");
        header("Location: cart.php");
        exit;
    }
}

// Handle GET-based removal
if (isset($_GET['remove'])) {
    $prod_id = (int)$_GET['remove'];
    removeFromCart($prod_id);
    setFlashMessage("Item removed from your cart.", "info");
    header("Location: cart.php");
    exit;
}

$cart_items = getCartItems();
$subtotal = getCartSubtotal();
$discount = getDiscountAmount();
$delivery = getDeliveryFee();
$grand_total = getCartGrandTotal();

// Generate WhatsApp order message for entire cart
$whatsapp_number = "27712345678";
$cart_desc_parts = [];
foreach ($cart_items as $item) {
    $cart_desc_parts[] = "- " . $item['quantity'] . "x " . $item['name'] . " (" . ($item['storage'] ?? 'N/A') . " - " . ($item['color'] ?? 'N/A') . ") @ " . formatPrice($item['price']) . " each";
}
$cart_desc_str = implode("\n", $cart_desc_parts);

$whatsapp_text = "Hello Mbatha's iPhone Plug! I'd like to place an order for the following items:\n\n" . $cart_desc_str . "\n\nSubtotal: " . formatPrice($subtotal);
if ($discount > 0) {
    $whatsapp_text .= "\nCoupon (" . $_SESSION['coupon']['code'] . "): -" . formatPrice($discount);
}
$whatsapp_text .= "\nShipping: " . ($delivery == 0 ? 'FREE' : formatPrice($delivery));
$whatsapp_text .= "\nGrand Total: " . formatPrice($grand_total);
$whatsapp_text .= "\n\nPlease let me know how to proceed with payment and delivery details!";

$whatsapp_cart_link = "https://wa.me/" . $whatsapp_number . "?text=" . urlencode($whatsapp_text);

require_once 'includes/header.php';
?>

<div class="py-5" style="background: radial-gradient(circle at 50% 10%, rgba(212, 175, 55, 0.04) 0%, rgba(11, 11, 12, 1) 50%); border-bottom: 1px solid var(--border-color-light);">
    <div class="container text-center">
        <h1 class="text-white mb-2 fw-bold" style="font-family: 'Outfit', sans-serif;">Your Shopping Cart</h1>
        <p class="text-secondary max-width-600 mx-auto mb-0">Review your selected premium devices and calculate delivery coupons before checking out.</p>
    </div>
</div>

<div class="container py-5">
    <?php if (!empty($cart_items)): ?>
        <form action="cart.php" method="POST">
            <div class="row g-5">
                <!-- Cart Items Table Grid -->
                <div class="col-lg-8" data-aos="fade-right">
                    <div class="glass-card p-0 overflow-hidden">
                        <div class="table-responsive">
                            <table class="table glass-table text-white mb-0 align-middle">
                                <thead>
                                    <tr class="border-bottom border-secondary border-opacity-25" style="background: rgba(255,255,255,0.02);">
                                        <th class="ps-4">Product Details</th>
                                        <th class="text-center">Price</th>
                                        <th class="text-center" style="width: 130px;">Quantity</th>
                                        <th class="text-end">Subtotal</th>
                                        <th class="text-center pe-4" style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cart_items as $item): ?>
                                        <tr class="border-bottom border-secondary border-opacity-10">
                                            <!-- Product description and visual -->
                                            <td class="ps-4 py-3">
                                                <div class="d-flex align-items-center gap-3">
                                                    <img src="<?php echo getProductImage($item['image_url'], $item['name'], 'Phone'); ?>" alt="<?php echo sanitize($item['name']); ?>" style="width: 60px; height: 60px; object-fit: contain; background: rgba(255,255,255,0.05); border-radius: 8px; padding: 5px;">
                                                    <div>
                                                        <h6 class="text-white fw-bold mb-1" style="font-size: 0.95rem;">
                                                            <a href="product.php?id=<?php echo $item['id']; ?>" class="text-white text-decoration-none hover-gold"><?php echo sanitize($item['name']); ?></a>
                                                        </h6>
                                                        <span class="text-muted small d-block" style="font-size: 0.75rem;">
                                                            <?php echo sanitize($item['storage']); ?> | <?php echo sanitize($item['color']); ?> | <?php echo sanitize($item['grade']); ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </td>
                                            
                                            <!-- Price -->
                                            <td class="text-center text-warning fw-semibold">
                                                <?php echo formatPrice($item['price']); ?>
                                            </td>
                                            
                                            <!-- Quantity selector input -->
                                            <td class="text-center">
                                                <input type="number" name="qty[<?php echo $item['id']; ?>]" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['stock']; ?>" class="form-control form-control-custom text-center py-1 px-2" style="font-size: 0.9rem;">
                                            </td>
                                            
                                            <!-- Subtotal -->
                                            <td class="text-end text-white fw-bold">
                                                <?php echo formatPrice($item['subtotal']); ?>
                                            </td>
                                            
                                            <!-- Remove trigger -->
                                            <td class="text-center pe-4">
                                                <a href="cart.php?remove=<?php echo $item['id']; ?>" class="text-danger hover-gold" title="Remove Item"><i class="fas fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Update Cart trigger bar -->
                        <div class="p-3 border-top border-secondary border-opacity-25 d-flex justify-content-between align-items-center bg-secondary bg-opacity-10">
                            <a href="shop.php" class="btn btn-outline-gold py-2 px-4" style="font-size: 0.85rem;"><i class="fas fa-arrow-left me-2"></i> Keep Shopping</a>
                            <button type="submit" name="update_cart" class="btn btn-gold py-2 px-4" style="font-size: 0.85rem;"><i class="fas fa-sync-alt me-2"></i> Update Cart</button>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Summary Card -->
                <div class="col-lg-4" data-aos="fade-left">
                    <!-- Order Totals summary block -->
                    <div class="glass-card mb-4">
                        <h4 class="text-white mb-4 fw-bold" style="font-family: 'Outfit', sans-serif;">Order Summary</h4>
                        
                        <div class="d-flex justify-content-between mb-3 border-bottom border-secondary border-opacity-10 pb-2">
                            <span class="text-secondary">Cart Subtotal</span>
                            <span class="text-white fw-bold"><?php echo formatPrice($subtotal); ?></span>
                        </div>
                        
                        <!-- Coupon Discount Display -->
                        <?php if ($discount > 0): ?>
                            <div class="d-flex justify-content-between mb-3 border-bottom border-secondary border-opacity-10 pb-2">
                                <span class="text-secondary">Discount (Code: <span class="text-warning"><?php echo $_SESSION['coupon']['code']; ?></span>)</span>
                                <span class="text-success fw-bold">-<?php echo formatPrice($discount); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <div class="d-flex justify-content-between mb-3 border-bottom border-secondary border-opacity-10 pb-2">
                            <span class="text-secondary">Nationwide Shipping</span>
                            <span class="text-white fw-bold">
                                <?php echo $delivery == 0 ? '<span class="text-success">FREE</span>' : formatPrice($delivery); ?>
                            </span>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mb-4 pt-2">
                            <span class="text-white fs-5 fw-bold">Grand Total</span>
                            <span class="text-warning fs-4 fw-bold text-gradient" style="font-family: 'Outfit', sans-serif;"><?php echo formatPrice($grand_total); ?></span>
                        </div>

                        <!-- Checkouts CTAs -->
                        <a href="checkout.php" class="btn btn-gold w-100 py-3 mb-3 fw-bold"><i class="fas fa-credit-card me-2"></i> Secure Checkout</a>
                        
                        <a href="<?php echo $whatsapp_cart_link; ?>" target="_blank" class="btn btn-success w-100 py-3 rounded-pill fw-bold text-white d-flex align-items-center justify-content-center gap-2" style="background-color: #25d366; border: none; box-shadow: 0 4px 15px rgba(37, 211, 102, 0.25);">
                            <i class="fab fa-whatsapp fs-4"></i> Order Cart via WhatsApp
                        </a>
                    </div>

                    <!-- Promo Coupon input form -->
                    <div class="glass-card">
                        <h5 class="text-white mb-3 fw-bold" style="font-family: 'Outfit', sans-serif;">Have a Promo Coupon?</h5>
                        <?php if (isset($_SESSION['coupon'])): ?>
                            <div class="d-flex justify-content-between align-items-center bg-secondary bg-opacity-20 p-2 rounded-3 border border-secondary border-opacity-25">
                                <span class="text-white small">Applied: <span class="text-warning fw-bold"><?php echo $_SESSION['coupon']['code']; ?></span></span>
                                <button type="submit" name="remove_coupon" class="btn btn-danger btn-sm px-2 py-1"><i class="fas fa-times"></i> Remove</button>
                            </div>
                        <?php else: ?>
                            <div class="input-group">
                                <input type="text" name="coupon_code" class="form-control form-control-custom py-2" placeholder="Coupon Code (e.g. PLUG10)" style="font-size: 0.85rem;">
                                <button type="submit" name="apply_coupon" class="btn btn-gold px-3">Apply</button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </form>
    <?php else: ?>
        <div class="text-center py-5" data-aos="zoom-in">
            <i class="fas fa-shopping-bag text-warning fa-4x mb-4"></i>
            <h2 class="text-white fw-bold">Your Cart is Empty</h2>
            <p class="text-secondary max-width-500 mx-auto my-3">You haven't added any premium pre-owned iPhones or Apple accessories to your cart yet.</p>
            <a href="shop.php" class="btn btn-gold py-3 px-5 mt-3"><i class="fas fa-arrow-left me-2"></i> Browse iPhones List</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
