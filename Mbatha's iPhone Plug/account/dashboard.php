<?php
// Mbatha's iPhone Plug - Customer Dashboard

require_once '../config/db.php';
require_once '../includes/functions.php';
require_once '../includes/cart_handler.php';

// Gate Access
requireUserLogin();

$user = getLoggedInUser();
$user_id = $user['id'];

// Handle Profile Updates POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = sanitize($_POST['name']);
    $phone = sanitize($_POST['phone']);
    $address = sanitize($_POST['address']);
    
    if (!empty($name)) {
        $stmt = $pdo->prepare("UPDATE users SET name = ?, phone = ?, address = ? WHERE id = ?");
        $stmt->execute([$name, $phone, $address, $user_id]);
        
        // Update session objects
        $_SESSION['user_name'] = $name;
        $_SESSION['user_phone'] = $phone;
        $_SESSION['user_address'] = $address;
        
        setFlashMessage("Profile details updated successfully.", "success");
    } else {
        setFlashMessage("Name cannot be left empty.", "danger");
    }
    header("Location: dashboard.php");
    exit;
}

// Fetch Purchase History
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();

require_once '../includes/header.php';
?>

<div class="py-5" style="background: radial-gradient(circle at 50% 10%, rgba(212, 175, 55, 0.04) 0%, rgba(11, 11, 12, 1) 50%); border-bottom: 1px solid var(--border-color-light);">
    <div class="container text-center text-md-start">
        <h1 class="text-white mb-2 fw-bold" style="font-family: 'Outfit', sans-serif;">Welcome Back, <?php echo sanitize($user['name']); ?></h1>
        <p class="text-secondary mb-0">Manage your default shipping addresses, inspect invoices, and track your pending EFT orders.</p>
    </div>
</div>

<div class="container py-5">
    <div class="row g-4">
        <!-- Sidebar Navigation -->
        <div class="col-lg-3 col-md-4" data-aos="fade-right">
            <div class="glass-card p-3">
                <nav class="nav flex-column gap-2">
                    <a href="#orders" class="dash-sidebar-link active" data-bs-toggle="pill" role="tab"><i class="fas fa-box me-2"></i> Purchase History</a>
                    <a href="#profile" class="dash-sidebar-link" data-bs-toggle="pill" role="tab"><i class="fas fa-user-edit me-2"></i> Account Details</a>
                    <a href="logout.php" class="dash-sidebar-link text-danger"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
                </nav>
            </div>
        </div>

        <!-- Dashboard Content Areas -->
        <div class="col-lg-9 col-md-8" data-aos="fade-left">
            <div class="tab-content">
                
                <!-- Tab 1: Purchase History -->
                <div class="tab-pane fade show active" id="orders" role="tabpanel">
                    <div class="glass-card">
                        <h3 class="text-white mb-4 fw-bold" style="font-family: 'Outfit', sans-serif;">Order History</h3>
                        
                        <?php if (!empty($orders)): ?>
                            <?php foreach ($orders as $order): ?>
                                <!-- Order Accordion Card layout -->
                                <div class="p-3 border border-secondary border-opacity-25 rounded-4 mb-3" style="background: rgba(255,255,255,0.01);">
                                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
                                        <div>
                                            <span class="text-warning fw-bold d-block">Order #1000<?php echo $order['id']; ?></span>
                                            <small class="text-muted"><?php echo date('Y-m-d H:i', strtotime($order['created_at'])); ?></small>
                                        </div>
                                        <div>
                                            <!-- Status coloring logic -->
                                            <?php 
                                                $status = $order['status'];
                                                $badge_class = 'bg-secondary';
                                                if ($status === 'Pending') $badge_class = 'bg-warning text-dark';
                                                elseif ($status === 'Paid') $badge_class = 'bg-primary';
                                                elseif ($status === 'Shipped') $badge_class = 'bg-info text-dark';
                                                elseif ($status === 'Delivered') $badge_class = 'bg-success';
                                                elseif ($status === 'Cancelled') $badge_class = 'bg-danger';
                                            ?>
                                            <span class="badge <?php echo $badge_class; ?> rounded-pill px-3 py-2 small fw-bold text-uppercase" style="font-size: 0.75rem;">
                                                <?php echo $status; ?>
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Fetch Line items inside card loop -->
                                    <?php 
                                        $stmt_items = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
                                        $stmt_items->execute([$order['id']]);
                                        $items = $stmt_items->fetchAll();
                                    ?>
                                    <div class="mb-3 border-top border-bottom border-secondary border-opacity-10 py-2">
                                        <?php foreach ($items as $item): ?>
                                            <div class="d-flex justify-content-between text-secondary small py-1">
                                                <span><?php echo sanitize($item['product_name']); ?> <strong class="text-white">x<?php echo $item['quantity']; ?></strong></span>
                                                <span class="text-white"><?php echo formatPrice($item['price'] * $item['quantity']); ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted small">Paid via: <?php echo sanitize($order['payment_method']); ?></span>
                                        <span class="text-white fw-bold">Total: <span class="text-warning"><?php echo formatPrice($order['order_total']); ?></span></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-box-open text-warning fa-3x mb-3"></i>
                                <p class="text-secondary mb-0">You haven't placed any orders yet.</p>
                                <a href="../shop.php" class="btn btn-gold btn-sm px-4 mt-3">Start Shopping</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Tab 2: Profile Settings Details -->
                <div class="tab-pane fade" id="profile" role="tabpanel">
                    <div class="glass-card">
                        <h3 class="text-white mb-4 fw-bold" style="font-family: 'Outfit', sans-serif;">Account Profile Details</h3>
                        
                        <form action="dashboard.php" method="POST">
                            <div class="mb-3">
                                <label class="form-label-custom">Full Name</label>
                                <input type="text" name="name" class="form-control form-control-custom" value="<?php echo sanitize($user['name']); ?>" required>
                            </div>
                            
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label-custom">Email Address (Read-only)</label>
                                    <input type="email" class="form-control form-control-custom opacity-50" value="<?php echo sanitize($user['email']); ?>" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-custom">WhatsApp Phone Number</label>
                                    <input type="tel" name="phone" class="form-control form-control-custom" placeholder="e.g. 0712345678" value="<?php echo sanitize($user['phone']); ?>">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label-custom">Default Delivery Address</label>
                                <textarea name="address" class="form-control form-control-custom" rows="3" placeholder="Street, Suburb, City, Code"><?php echo sanitize($user['address']); ?></textarea>
                            </div>

                            <button type="submit" name="update_profile" class="btn btn-gold py-2 px-4 fw-bold"><i class="fas fa-save me-2"></i> Save Changes</button>
                        </form>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
