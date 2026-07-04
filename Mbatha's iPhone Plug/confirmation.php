<?php
// Mbatha's iPhone Plug - Order Confirmation & Invoice Receipt

require_once 'config/db.php';
require_once 'includes/functions.php';
require_once 'includes/cart_handler.php';

// Validate order parameters
if (!isset($_GET['order_id']) || empty($_GET['order_id'])) {
    header("Location: index.php");
    exit;
}

$order_id = (int)$_GET['order_id'];

// Fetch Order Details
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    header("Location: index.php");
    exit;
}

// Fetch Order Line Items
$stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
$stmt->execute([$order_id]);
$order_items = $stmt->fetchAll();

// Pre-built WhatsApp Proof of Payment text
$whatsapp_number = "27712345678";
$whatsapp_text = "Hello Mbatha's iPhone Plug! I've placed order #1000" . $order['id'] . " on your website for a total of " . formatPrice($order['order_total']) . ". I will send through the Proof of Payment (POP) shortly. Please process my shipment!";
$whatsapp_confirm_link = "https://wa.me/" . $whatsapp_number . "?text=" . urlencode($whatsapp_text);

require_once 'includes/header.php';
?>

<div class="container py-5">
    <div class="max-width-800 mx-auto text-center mb-5" data-aos="zoom-in">
        <i class="fas fa-check-circle text-success fa-4x mb-3" style="filter: drop-shadow(0 0 10px rgba(40,167,69,0.3));"></i>
        <h1 class="text-white fw-bold mb-2" style="font-family: 'Outfit', sans-serif;">Order Placed Successfully!</h1>
        <p class="text-secondary mb-0">Thank you for your purchase. Your order number is <strong class="text-warning">#1000<?php echo $order['id']; ?></strong></p>
    </div>

    <div class="row g-4">
        <!-- Left Column: EFT Banking Instructions -->
        <div class="col-lg-6" data-aos="fade-right">
            <div class="glass-card h-100 border-warning" style="border-width: 2px !important;">
                <h3 class="text-gradient fw-bold mb-4" style="font-family: 'Outfit', sans-serif;"><i class="fas fa-university me-2"></i> EFT Payment Instructions</h3>
                <p class="text-secondary small mb-4">
                    Please transfer the grand total to the bank account below. Ensure you use the exact **Order Reference** below to help us verify your payment.
                </p>
                
                <!-- Banking Table Details -->
                <table class="table glass-table text-white mb-4">
                    <tbody>
                        <tr><td class="text-muted w-40">Bank Name</td><td><strong>First National Bank (FNB)</strong></td></tr>
                        <tr><td class="text-muted">Account Holder</td><td>Mbatha's iPhone Plug</td></tr>
                        <tr><td class="text-muted">Account Number</td><td>62912345678</td></tr>
                        <tr><td class="text-muted">Account Type</td><td>Cheque / Current</td></tr>
                        <tr><td class="text-muted">Branch Code</td><td>250655</td></tr>
                        <tr><td class="text-muted">Transfer Total</td><td class="text-warning fw-bold fs-5"><?php echo formatPrice($order['order_total']); ?></td></tr>
                        <tr><td class="text-muted">Payment Reference</td><td class="text-danger fw-bold fs-6">M-PLUG-1000<?php echo $order['id']; ?></td></tr>
                    </tbody>
                </table>

                <div class="alert alert-info border-0 rounded-3 mb-4 bg-secondary bg-opacity-20 text-secondary" style="font-size: 0.85rem;">
                    <i class="fas fa-info-circle text-warning me-2"></i> Send your PDF proof of payment to <strong class="text-white">payments@mbathaphoneplug.co.za</strong> or via WhatsApp.
                </div>

                <a href="<?php echo $whatsapp_confirm_link; ?>" target="_blank" class="btn btn-success w-100 py-3 rounded-pill fw-bold text-white d-flex align-items-center justify-content-center gap-2" style="background-color: #25d366; border: none;">
                    <i class="fab fa-whatsapp fs-4"></i> WhatsApp Proof of Payment
                </a>
            </div>
        </div>

        <!-- Right Column: Invoice Details & Items Summary -->
        <div class="col-lg-6" data-aos="fade-left">
            <div class="glass-card">
                <h3 class="text-white mb-4 fw-bold" style="font-family: 'Outfit', sans-serif;">Invoice Summary</h3>
                
                <div class="mb-4">
                    <h6 class="text-muted small uppercase tracking-wider mb-2">Delivery Address:</h6>
                    <p class="text-white small mb-0"><?php echo sanitize($order['address']); ?></p>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <h6 class="text-muted small uppercase tracking-wider mb-1">Customer Phone:</h6>
                        <span class="text-white small"><?php echo sanitize($order['phone']); ?></span>
                    </div>
                    <div class="col-6">
                        <h6 class="text-muted small uppercase tracking-wider mb-1">Payment Method:</h6>
                        <span class="text-white small"><?php echo sanitize($order['payment_method']); ?></span>
                    </div>
                </div>

                <h5 class="text-white mb-3" style="font-family: 'Outfit', sans-serif;">Ordered Items</h5>
                <div class="border-top border-secondary border-opacity-10 pt-2 mb-4">
                    <?php foreach ($order_items as $item): ?>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="text-white mb-0 fw-semibold" style="font-size: 0.9rem;"><?php echo sanitize($item['product_name']); ?></h6>
                                <span class="text-muted small">Qty: <?php echo $item['quantity']; ?></span>
                            </div>
                            <span class="text-warning fw-bold small"><?php echo formatPrice($item['price'] * $item['quantity']); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Final totals invoice mapping -->
                <div class="d-flex justify-content-between border-top border-secondary border-opacity-25 pt-3">
                    <span class="text-white fw-bold">Total Paid/Pending</span>
                    <span class="text-warning fs-5 fw-bold text-gradient" style="font-family: 'Outfit', sans-serif;"><?php echo formatPrice($order['order_total']); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
