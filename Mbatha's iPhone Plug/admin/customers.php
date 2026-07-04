<?php
// Mbatha's iPhone Plug - Admin Customer Accounts List

require_once '../config/db.php';
require_once '../includes/functions.php';

// Gate Access
requireAdminLogin();

// Fetch All Registered Customers along with Order summaries (Order count & total spent)
// SQLite and MySQL compliant query mapping
$sql = "SELECT u.*, COUNT(o.id) as order_count, SUM(o.order_total) as total_spent 
        FROM users u 
        LEFT JOIN orders o ON u.id = o.user_id AND o.status != 'Cancelled'
        GROUP BY u.id 
        ORDER BY u.created_at DESC";

$stmt = $pdo->query($sql);
$customers = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Management | Mbatha's iPhone Plug</title>
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
            <a href="orders.php" class="admin-nav-link"><i class="fas fa-receipt"></i> Orders List</a>
            <a href="tradeins.php" class="admin-nav-link"><i class="fas fa-exchange-alt"></i> Trade-Ins Manager</a>
            <a href="customers.php" class="admin-nav-link active"><i class="fas fa-users"></i> Customers Registry</a>
        </nav>
        
        <div class="admin-logout-btn">
            <a href="logout.php" class="admin-nav-link text-danger"><i class="fas fa-sign-out-alt"></i> Log Out</a>
        </div>
    </div>

    <!-- Admin Wrapper Content -->
    <div class="admin-wrapper">
        <header class="admin-header">
            <div>
                <h1 class="text-white fw-bold mb-1" style="font-family: 'Outfit', sans-serif;">Customer Accounts Registry</h1>
                <p class="text-secondary small mb-0">Monitor customer profiles, purchase history indexes, and lifetime support spend metrics.</p>
            </div>
        </header>

        <!-- Display flash notices -->
        <?php displayFlashMessage(); ?>

        <!-- Customer list panel -->
        <div class="admin-panel-card">
            <?php if (!empty($customers)): ?>
                <div class="table-responsive">
                    <table class="table glass-table text-white mb-0 align-middle">
                        <thead>
                            <tr class="text-muted border-bottom border-secondary border-opacity-25">
                                <th>Customer ID</th>
                                <th>Full Name</th>
                                <th>Email Address</th>
                                <th>WhatsApp Phone</th>
                                <th class="text-center">Orders Placed</th>
                                <th class="text-center">Lifetime Spent</th>
                                <th class="text-end">Registered Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($customers as $cust): ?>
                                <tr class="border-bottom border-secondary border-opacity-10">
                                    <td><strong class="text-warning">#USER-00<?php echo $cust['id']; ?></strong></td>
                                    <td><span class="text-white fw-bold"><?php echo sanitize($cust['name']); ?></span></td>
                                    <td><span class="text-secondary small"><?php echo sanitize($cust['email']); ?></span></td>
                                    <td><span class="text-secondary small"><?php echo !empty($cust['phone']) ? sanitize($cust['phone']) : '<span class="text-muted italic">Not set</span>'; ?></span></td>
                                    <td class="text-center fw-semibold text-white"><?php echo $cust['order_count']; ?></td>
                                    <td class="text-center text-warning fw-bold">
                                        <?php echo $cust['total_spent'] > 0 ? formatPrice($cust['total_spent']) : 'R 0'; ?>
                                    </td>
                                    <td class="text-end text-muted small"><?php echo date('Y-m-d', strtotime($cust['created_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-users text-warning fa-3x mb-3"></i>
                    <h5 class="text-white">No Customer Accounts Registered</h5>
                    <p class="text-secondary small">Customer records will manifest here once accounts are registered via the storefront check-in form.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bootstrap Bundle script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
