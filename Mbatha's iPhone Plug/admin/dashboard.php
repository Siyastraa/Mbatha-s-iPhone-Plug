<?php
// Mbatha's iPhone Plug - Admin Dashboard & Analytics

require_once '../config/db.php';
require_once '../includes/functions.php';

// Gate Access
requireAdminLogin();

// -----------------------------------------------------
// Fetch Analytics Data
// -----------------------------------------------------

// 1. Total Sales Revenue
$stmt = $pdo->query("SELECT SUM(order_total) FROM orders WHERE status != 'Cancelled'");
$total_revenue = (float)$stmt->fetchColumn();

// 2. Total Orders Count
$stmt = $pdo->query("SELECT COUNT(*) FROM orders");
$orders_count = (int)$stmt->fetchColumn();

// 3. Products Stock Inventory Count
$stmt = $pdo->query("SELECT SUM(stock) FROM products");
$stock_count = (int)$stmt->fetchColumn();

// 4. Pending Trade-ins count
$stmt = $pdo->query("SELECT COUNT(*) FROM trade_ins WHERE status = 'Pending'");
$trade_pending = (int)$stmt->fetchColumn();

// 5. Recent Orders (Latest 5)
$stmt = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 5");
$recent_orders = $stmt->fetchAll();

// 6. Recent Trade-ins (Latest 5)
$stmt = $pdo->query("SELECT * FROM trade_ins ORDER BY created_at DESC LIMIT 5");
$recent_tradeins = $stmt->fetchAll();

// 7. Graph Data: Revenue by Category (Simulated groupings)
$cat_revenue = $pdo->query("SELECT category, SUM(price * stock) as val FROM products GROUP BY category LIMIT 5")->fetchAll();
$graph_labels = [];
$graph_data = [];
foreach ($cat_revenue as $cr) {
    $graph_labels[] = $cr['category'];
    $graph_data[] = (float)$cr['val'];
}

// Map variables for Chart.js
$chart_labels_json = json_encode($graph_labels);
$chart_data_json = json_encode($graph_data);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Mbatha's iPhone Plug</title>
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
                <rect x="90" y="52" width="20" height="5" rx="2.5" fill="#ffffff"/>
                <path d="M 60 160 Q 80 120, 90 90 L 100 110 L 110 90 Q 120 120, 140 160" fill="none" stroke="#d4af37" stroke-width="5" stroke-linecap="round"/>
            </svg>
            <span>MBATHA'S PLUG</span>
        </div>
        
        <nav class="admin-nav">
            <a href="dashboard.php" class="admin-nav-link active"><i class="fas fa-chart-line"></i> Dashboard</a>
            <a href="products.php" class="admin-nav-link"><i class="fas fa-mobile-alt"></i> Products CRUD</a>
            <a href="orders.php" class="admin-nav-link"><i class="fas fa-receipt"></i> Orders List</a>
            <a href="tradeins.php" class="admin-nav-link"><i class="fas fa-exchange-alt"></i> Trade-Ins Manager</a>
            <a href="customers.php" class="admin-nav-link"><i class="fas fa-users"></i> Customers Registry</a>
        </nav>
        
        <div class="admin-logout-btn">
            <a href="logout.php" class="admin-nav-link text-danger hover-gold"><i class="fas fa-sign-out-alt"></i> Log Out</a>
        </div>
    </div>

    <!-- Admin Wrapper Content -->
    <div class="admin-wrapper">
        <!-- Top bar header -->
        <header class="admin-header">
            <div>
                <h1 class="text-white fw-bold mb-1" style="font-family: 'Outfit', sans-serif;">Dashboard Analytics</h1>
                <p class="text-secondary small mb-0">Overview of inventory pricing, business orders, and customer activity.</p>
            </div>
            <div class="text-secondary d-none d-md-block">
                Logged in as: <strong class="text-white"><?php echo $_SESSION['admin_username']; ?></strong>
            </div>
        </header>

        <!-- Stats Counters Cards row -->
        <div class="row g-4 mb-5">
            <!-- Revenue -->
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-info">
                        <h5>Total Revenue</h5>
                        <h2><?php echo formatPrice($total_revenue); ?></h2>
                    </div>
                    <div class="stat-icon"><i class="fas fa-wallet text-dark"></i></div>
                </div>
            </div>
            <!-- Orders -->
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-info">
                        <h5>Sales Orders</h5>
                        <h2><?php echo $orders_count; ?></h2>
                    </div>
                    <div class="stat-icon"><i class="fas fa-receipt text-dark"></i></div>
                </div>
            </div>
            <!-- Stock -->
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-info">
                        <h5>Stock Count</h5>
                        <h2><?php echo $stock_count; ?> Devices</h2>
                    </div>
                    <div class="stat-icon"><i class="fas fa-boxes text-dark"></i></div>
                </div>
            </div>
            <!-- Trade-ins -->
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-info">
                        <h5>Pending Trades</h5>
                        <h2><?php echo $trade_pending; ?></h2>
                    </div>
                    <div class="stat-icon"><i class="fas fa-exchange-alt text-dark"></i></div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <!-- Analytics Graph Column -->
            <div class="col-lg-8">
                <div class="admin-panel-card">
                    <h5 class="text-white fw-bold mb-4" style="font-family: 'Outfit', sans-serif;">Inventory Valuation by Category</h5>
                    <div class="chart-container">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Database connection type fallback indicator -->
            <div class="col-lg-4">
                <div class="admin-panel-card h-100 d-flex flex-column justify-content-between">
                    <div>
                        <h5 class="text-white fw-bold mb-3" style="font-family: 'Outfit', sans-serif;">System Status</h5>
                        <p class="text-secondary small">Database Mode: <strong class="text-warning"><?php echo ACTIVE_DB_TYPE; ?></strong></p>
                        <p class="text-secondary small">Session Status: <strong class="text-success">Active</strong></p>
                    </div>
                    <div class="p-3 rounded-4 text-center mt-3" style="background: rgba(212,175,55,0.05); border: 1px solid var(--border-color);">
                        <span class="text-secondary small d-block">Quick Actions</span>
                        <a href="products.php" class="btn btn-gold btn-sm w-100 mt-2 py-2">Add New Product</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity Logs Tables -->
        <div class="row g-4">
            <!-- Recent Orders -->
            <div class="col-lg-6">
                <div class="admin-panel-card h-100">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="text-white fw-bold mb-0" style="font-family: 'Outfit', sans-serif;">Recent Sales Orders</h5>
                        <a href="orders.php" class="text-warning text-decoration-none small hover-gold">View All</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table glass-table text-white mb-0 align-middle" style="font-size: 0.85rem;">
                            <thead>
                                <tr class="text-muted border-bottom border-secondary border-opacity-25">
                                    <th>Order</th>
                                    <th>Customer</th>
                                    <th>Status</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_orders as $ord): ?>
                                    <tr class="border-bottom border-secondary border-opacity-10">
                                        <td>#1000<?php echo $ord['id']; ?></td>
                                        <td><?php echo sanitize($ord['customer_name']); ?></td>
                                        <td>
                                            <span class="badge-status badge-<?php echo strtolower($ord['status']); ?>">
                                                <?php echo $ord['status']; ?>
                                            </span>
                                        </td>
                                        <td class="text-end text-warning fw-bold"><?php echo formatPrice($ord['order_total']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Recent Trade Ins -->
            <div class="col-lg-6">
                <div class="admin-panel-card h-100">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="text-white fw-bold mb-0" style="font-family: 'Outfit', sans-serif;">Recent Trade-In Queries</h5>
                        <a href="tradeins.php" class="text-warning text-decoration-none small hover-gold">View All</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table glass-table text-white mb-0 align-middle" style="font-size: 0.85rem;">
                            <thead>
                                <tr class="text-muted border-bottom border-secondary border-opacity-25">
                                    <th>Customer</th>
                                    <th>Model</th>
                                    <th>Status</th>
                                    <th class="text-end">Estimate</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_tradeins as $tr): ?>
                                    <tr class="border-bottom border-secondary border-opacity-10">
                                        <td><?php echo sanitize($tr['name']); ?></td>
                                        <td><?php echo sanitize($tr['phone_model']); ?></td>
                                        <td>
                                            <span class="badge-status badge-<?php echo strtolower($tr['status'] === 'Completed' ? 'delivered' : ($tr['status'] === 'Declined' ? 'cancelled' : 'pending')); ?>">
                                                <?php echo $tr['status']; ?>
                                            </span>
                                        </td>
                                        <td class="text-end text-warning fw-bold"><?php echo formatPrice($tr['quotation_amount']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ChartJS and Bootstrap scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Render Chart.js dynamic dashboard graph
        const ctx = document.getElementById('salesChart').getContext('2d');
        const labels = <?php echo $chart_labels_json; ?>;
        const data = <?php echo $chart_data_json; ?>;
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Valuation (ZAR)',
                    data: data,
                    backgroundColor: 'rgba(212, 175, 55, 0.45)',
                    borderColor: 'rgba(212, 175, 55, 1)',
                    borderWidth: 2,
                    borderRadius: 8,
                    hoverBackgroundColor: 'rgba(212, 175, 55, 0.8)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(255, 255, 255, 0.05)'
                        },
                        ticks: {
                            color: '#a1a1aa'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#a1a1aa'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    </script>
</body>
</html>
