<?php
// Mbatha's iPhone Plug - Admin Order fulfillment Manager

require_once '../config/db.php';
require_once '../includes/functions.php';

// Gate Access
requireAdminLogin();

// Handle Order Status Updates POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $new_status = sanitize($_POST['status']);
    
    $allowed_statuses = ['Pending', 'Paid', 'Packed', 'Shipped', 'Delivered', 'Cancelled'];
    if (in_array($new_status, $allowed_statuses)) {
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $order_id]);
        
        setFlashMessage("Order #1000{$order_id} status updated to: {$new_status}.", "success");
    } else {
        setFlashMessage("Invalid status option selected.", "danger");
    }
    header("Location: orders.php");
    exit;
}

// Fetch All Orders list
$stmt = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC");
$orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management | Mbatha's iPhone Plug</title>
    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="../assets/css/admin.css" rel="stylesheet">
</head>
<body class="admin-body">

    <!-- Admin Sidebar -->
    <div class="admin-sidebar">
        <div class="admin-logo">
            <svg width="30" height="30" viewBox="0 0 200 200" style="filter: drop-shadow(0px 2px 4px rgba(212,175,55,0.3));">
                <circle cx="100" cy="100" r="90" fill="none" stroke="#d4af37" stroke-width="6"/>
                <rect x="75" y="45" width="50" height="95" rx="10" fill="none" stroke="#ffffff" stroke-width="4"/>
                <path d="M 60 160 Q 80 120, 90 90 L 100 110" fill="none" stroke="#d4af37" stroke-width="5" stroke-linecap="round"/>
            </svg>
            <span>MBATHA'S PLUG</span>
        </div>
        
        <nav class="admin-nav">
            <a href="dashboard.php" class="admin-nav-link"><i class="fas fa-chart-line"></i> Dashboard</a>
            <a href="products.php" class="admin-nav-link"><i class="fas fa-mobile-alt"></i> Products CRUD</a>
            <a href="orders.php" class="admin-nav-link active"><i class="fas fa-receipt"></i> Orders List</a>
            <a href="tradeins.php" class="admin-nav-link"><i class="fas fa-exchange-alt"></i> Trade-Ins Manager</a>
            <a href="customers.php" class="admin-nav-link"><i class="fas fa-users"></i> Customers Registry</a>
        </nav>
        
        <div class="admin-logout-btn">
            <a href="logout.php" class="admin-nav-link text-danger"><i class="fas fa-sign-out-alt"></i> Log Out</a>
        </div>
    </div>

    <!-- Admin Wrapper Content -->
    <div class="admin-wrapper">
        <header class="admin-header">
            <div>
                <h1 class="text-white fw-bold mb-1" style="font-family: 'Outfit', sans-serif;">Customer Sales Orders</h1>
                <p class="text-secondary small mb-0">Manage payments, pack boxes, verify courier references, or trigger customer WhatsApp notices.</p>
            </div>
        </header>

        <!-- Display flash notices -->
        <?php displayFlashMessage(); ?>

        <!-- Orders list panel -->
        <div class="admin-panel-card">
            <?php if (!empty($orders)): ?>
                <div class="table-responsive">
                    <table class="table glass-table text-white mb-0 align-middle" style="font-size: 0.9rem;">
                        <thead>
                            <tr class="text-muted border-bottom border-secondary border-opacity-25">
                                <th>Order Ref</th>
                                <th>Customer Details</th>
                                <th>Delivery Address</th>
                                <th>Order items Details</th>
                                <th class="text-center">Total Price</th>
                                <th class="text-center" style="width: 160px;">Fulfillment Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $ord): ?>
                                <tr class="border-bottom border-secondary border-opacity-10">
                                    <!-- Order ID -->
                                    <td>
                                        <strong class="text-warning">#1000<?php echo $ord['id']; ?></strong>
                                        <small class="text-muted d-block"><?php echo date('Y-m-d H:i', strtotime($ord['created_at'])); ?></small>
                                    </td>
                                    
                                    <!-- Customer Profile -->
                                    <td>
                                        <span class="d-block text-white fw-bold"><?php echo sanitize($ord['customer_name']); ?></span>
                                        <small class="text-secondary d-block"><?php echo sanitize($ord['email']); ?></small>
                                        <small class="text-secondary d-block"><?php echo sanitize($ord['phone']); ?></small>
                                    </td>
                                    
                                    <!-- Address -->
                                    <td>
                                        <span class="small text-secondary" style="max-width: 200px; display: inline-block; word-wrap: break-word;">
                                            <?php echo sanitize($ord['address']); ?>
                                        </span>
                                    </td>
                                    
                                    <!-- Line items -->
                                    <td>
                                        <?php 
                                            $stmt_items = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
                                            $stmt_items->execute([$ord['id']]);
                                            $items = $stmt_items->fetchAll();
                                            foreach ($items as $item) {
                                                echo "<span class='d-block small text-secondary'>- " . sanitize($item['product_name']) . " <strong class='text-white'>x" . $item['quantity'] . "</strong></span>";
                                            }
                                        ?>
                                    </td>
                                    
                                    <!-- Total -->
                                    <td class="text-center text-warning fw-bold"><?php echo formatPrice($ord['order_total']); ?></td>
                                    
                                    <!-- Status Selector form -->
                                    <td class="text-center">
                                        <form action="orders.php" method="POST" id="statusForm_<?php echo $ord['id']; ?>">
                                            <input type="hidden" name="order_id" value="<?php echo $ord['id']; ?>">
                                            <input type="hidden" name="update_status" value="1">
                                            <select name="status" class="form-select form-control-custom py-1 ps-2 pe-4 text-white" style="font-size: 0.8rem; background-position: right 6px center;" onchange="this.form.submit()">
                                                <option value="Pending" <?php echo ($ord['status'] === 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                                <option value="Paid" <?php echo ($ord['status'] === 'Paid') ? 'selected' : ''; ?>>Paid</option>
                                                <option value="Packed" <?php echo ($ord['status'] === 'Packed') ? 'selected' : ''; ?>>Packed</option>
                                                <option value="Shipped" <?php echo ($ord['status'] === 'Shipped') ? 'selected' : ''; ?>>Shipped</option>
                                                <option value="Delivered" <?php echo ($ord['status'] === 'Delivered') ? 'selected' : ''; ?>>Delivered</option>
                                                <option value="Cancelled" <?php echo ($ord['status'] === 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                            </select>
                                        </form>
                                    </td>

                                    <!-- Actions (WhatsApp Customer Contact) -->
                                    <td class="text-end">
                                        <!-- Pre-constructed message for updates -->
                                        <?php 
                                            $msg_text = "Hello " . $ord['customer_name'] . "! This is Mbatha's iPhone Plug. Regarding your order #1000" . $ord['id'] . ", its status is now updated to: " . $ord['status'] . ". Thank you for your support!";
                                            if ($ord['status'] === 'Shipped') {
                                                $msg_text .= " We have shipped your parcel. POP reference is logged.";
                                            }
                                            $clean_phone = preg_replace('/[^0-9]/', '', $ord['phone']);
                                            // Convert 07... to South African prefix 277...
                                            if (substr($clean_phone, 0, 1) === '0') {
                                                $clean_phone = '27' . substr($clean_phone, 1);
                                            }
                                            $whatsapp_link = "https://wa.me/" . $clean_phone . "?text=" . urlencode($msg_text);
                                        ?>
                                        <a href="<?php echo $whatsapp_link; ?>" target="_blank" class="btn btn-outline-success btn-sm py-1 px-3" style="font-size: 0.8rem;" title="Text Customer"><i class="fab fa-whatsapp"></i> Notify</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-receipt text-warning fa-3x mb-3"></i>
                    <h5 class="text-white">No Customer Orders Logged</h5>
                    <p class="text-secondary small">As soon as users complete secure checkouts on the storefront, their details will display here.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bootstrap Bundle script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
