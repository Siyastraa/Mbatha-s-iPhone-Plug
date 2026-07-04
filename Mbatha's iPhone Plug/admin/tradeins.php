<?php
// Mbatha's iPhone Plug - Admin Trade-In Inquiry Manager

require_once '../config/db.php';
require_once '../includes/functions.php';

// Gate Access
requireAdminLogin();

// Process Quotation Updates POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_tradein'])) {
    $trade_id = (int)$_POST['trade_id'];
    $status = sanitize($_POST['status']);
    $quote_amount = (float)$_POST['quotation_amount'];
    
    $allowed_statuses = ['Pending', 'Quoted', 'Declined', 'Completed'];
    if (in_array($status, $allowed_statuses)) {
        $stmt = $pdo->prepare("UPDATE trade_ins SET status = ?, quotation_amount = ? WHERE id = ?");
        $stmt->execute([$status, $quote_amount, $trade_id]);
        
        setFlashMessage("Trade-In #TR-{$trade_id} updated successfully.", "success");
    } else {
        setFlashMessage("Invalid status selected.", "danger");
    }
    header("Location: tradeins.php");
    exit;
}

// Fetch All Trade-ins list
$stmt = $pdo->query("SELECT * FROM trade_ins ORDER BY created_at DESC");
$trade_ins = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trade-Ins Manager | Mbatha's iPhone Plug</title>
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
            <a href="tradeins.php" class="admin-nav-link active"><i class="fas fa-exchange-alt"></i> Trade-Ins Manager</a>
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
                <h1 class="text-white fw-bold mb-1" style="font-family: 'Outfit', sans-serif;">Trade-In Evaluations</h1>
                <p class="text-secondary small mb-0">Evaluate client phone specs, examine settings screen uploads, and issue official trade-in offers.</p>
            </div>
        </header>

        <!-- Display flash notices -->
        <?php displayFlashMessage(); ?>

        <!-- Tradeins Panel Table -->
        <div class="admin-panel-card">
            <?php if (!empty($trade_ins)): ?>
                <div class="table-responsive">
                    <table class="table glass-table text-white mb-0 align-middle" style="font-size: 0.9rem;">
                        <thead>
                            <tr class="text-muted border-bottom border-secondary border-opacity-25">
                                <th>Client Details</th>
                                <th>Device Specifications</th>
                                <th class="text-center">Settings Photo</th>
                                <th class="text-center" style="width: 140px;">Quotation (ZAR)</th>
                                <th class="text-center" style="width: 140px;">Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($trade_ins as $tr): ?>
                                <tr class="border-bottom border-secondary border-opacity-10">
                                    <!-- Client info -->
                                    <td>
                                        <span class="d-block text-white fw-bold"><?php echo sanitize($tr['name']); ?></span>
                                        <small class="text-secondary d-block"><?php echo sanitize($tr['email']); ?></small>
                                        <small class="text-secondary d-block"><?php echo sanitize($tr['phone']); ?></small>
                                    </td>
                                    
                                    <!-- Device Specs -->
                                    <td>
                                        <strong class="text-warning d-block"><?php echo sanitize($tr['phone_model']); ?></strong>
                                        <small class="text-white d-block">Storage: <?php echo sanitize($tr['storage']); ?></small>
                                        <small class="text-white d-block">Battery: <?php echo $tr['battery_health']; ?>% BH</small>
                                        <small class="text-secondary d-block">Condition: <span class="text-info"><?php echo sanitize($tr['condition_grade']); ?></span></small>
                                    </td>
                                    
                                    <!-- Photo screenshot link -->
                                    <td class="text-center">
                                        <?php if (!empty($tr['photo_url'])): ?>
                                            <a href="../<?php echo $tr['photo_url']; ?>" target="_blank" class="text-warning text-decoration-none small hover-gold">
                                                <i class="fas fa-image fa-2x"></i><br>View Photo
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted small">No photo</span>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <!-- Form for Quotation Amount & Status -->
                                    <form action="tradeins.php" method="POST">
                                        <input type="hidden" name="trade_id" value="<?php echo $tr['id']; ?>">
                                        <input type="hidden" name="update_tradein" value="1">
                                        
                                        <!-- Quotation input -->
                                        <td class="text-center">
                                            <input type="number" name="quotation_amount" class="form-control form-control-custom text-center py-1 fw-bold text-warning" min="0" value="<?php echo $tr['quotation_amount']; ?>" style="font-size: 0.85rem;">
                                        </td>
                                        
                                        <!-- Status selector -->
                                        <td class="text-center">
                                            <select name="status" class="form-select form-control-custom py-1 ps-2 pe-4 text-white" style="font-size: 0.8rem; background-position: right 6px center;">
                                                <option value="Pending" <?php echo ($tr['status'] === 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                                <option value="Quoted" <?php echo ($tr['status'] === 'Quoted') ? 'selected' : ''; ?>>Quoted</option>
                                                <option value="Declined" <?php echo ($tr['status'] === 'Declined') ? 'selected' : ''; ?>>Declined</option>
                                                <option value="Completed" <?php echo ($tr['status'] === 'Completed') ? 'selected' : ''; ?>>Completed</option>
                                            </select>
                                        </td>
                                        
                                        <!-- Actions (Save changes & Send WhatsApp validation) -->
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end gap-2">
                                                <button type="submit" class="btn btn-gold btn-sm py-1 px-2" title="Save Quote Details" style="font-size: 0.8rem;"><i class="fas fa-save"></i> Save</button>
                                                
                                                <?php 
                                                    $msg_text = "Hello " . $tr['name'] . "! This is Mbatha's iPhone Plug. We evaluated your trade-in request for the " . $tr['phone_model'] . " (" . $tr['storage'] . "). We are pleased to offer you a final valuation of R" . number_format($tr['quotation_amount'], 0, '.', ',') . ". Would you like to proceed with drop-off/courier?";
                                                    $clean_phone = preg_replace('/[^0-9]/', '', $tr['phone']);
                                                    if (substr($clean_phone, 0, 1) === '0') {
                                                        $clean_phone = '27' . substr($clean_phone, 1);
                                                    }
                                                    $whatsapp_link = "https://wa.me/" . $clean_phone . "?text=" . urlencode($msg_text);
                                                ?>
                                                <a href="<?php echo $whatsapp_link; ?>" target="_blank" class="btn btn-outline-success btn-sm py-1 px-2" style="font-size: 0.8rem;" title="WhatsApp Offer"><i class="fab fa-whatsapp"></i> Chat</a>
                                            </div>
                                        </td>
                                    </form>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-exchange-alt text-warning fa-3x mb-3"></i>
                    <h5 class="text-white">No Trade-In Requests Submitted</h5>
                    <p class="text-secondary small">As soon as customers fill out the diagnostic calculator on the storefront, queries will list here.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bootstrap Bundle script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
