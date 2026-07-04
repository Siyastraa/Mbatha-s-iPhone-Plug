<?php
// Mbatha's iPhone Plug - Admin Authentication Login

require_once '../config/db.php';
require_once '../includes/functions.php';

// Redirect if already logged in
if (isAdminLoggedIn()) {
    header("Location: dashboard.php");
    exit;
}

// Handle login submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_admin'])) {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error = "Please fill in all credentials.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();
        
        if ($admin && password_verify($password, $admin['password_hash'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid administrator credentials.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Mbatha's iPhone Plug</title>
    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="../assets/css/admin.css" rel="stylesheet">
</head>
<body class="admin-body d-flex align-items-center justify-content-center" style="min-height: 100vh; background-color: var(--bg-admin-main);">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="admin-panel-card p-5 shadow-lg border border-secondary border-opacity-25" style="background-color: var(--bg-admin-card);">
                    <div class="text-center mb-4">
                        <svg width="45" height="45" viewBox="0 0 200 200" class="mb-3" style="filter: drop-shadow(0px 2px 4px rgba(212,175,55,0.3));">
                            <circle cx="100" cy="100" r="90" fill="none" stroke="#d4af37" stroke-width="6"/>
                            <rect x="75" y="45" width="50" height="95" rx="10" fill="none" stroke="#ffffff" stroke-width="4"/>
                            <rect x="90" y="52" width="20" height="5" rx="2.5" fill="#ffffff"/>
                            <path d="M 60 160 Q 80 120, 90 90 L 100 110 L 110 90 Q 120 120, 140 160" fill="none" stroke="#d4af37" stroke-width="5" stroke-linecap="round"/>
                            <rect x="93" y="80" width="14" height="10" rx="2" fill="#d4af37"/>
                            <line x1="97" y1="80" x2="97" y2="74" stroke="#d4af37" stroke-width="3"/>
                            <line x1="103" y1="80" x2="103" y2="74" stroke="#d4af37" stroke-width="3"/>
                        </svg>
                        <h4 class="text-white fw-bold mb-1" style="font-family: 'Outfit', sans-serif;">MBATHA'S PLUG</h4>
                        <span class="text-warning text-uppercase small tracking-widest fw-bold">Admin Portal</span>
                    </div>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger border-0 small rounded-3 py-2 text-center mb-3" style="background-color: rgba(220,53,69,0.1); color: #dc3545;">
                            <i class="fas fa-exclamation-circle me-1"></i> <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <form action="login.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label-custom">Admin Username</label>
                            <input type="text" name="username" class="form-control form-control-custom" placeholder="e.g. admin" required autofocus>
                        </div>

                        <div class="mb-4">
                            <label class="form-label-custom">Secure Password</label>
                            <input type="password" name="password" class="form-control form-control-custom" placeholder="••••••••" required>
                        </div>

                        <button type="submit" name="login_admin" class="btn btn-gold w-100 py-3 fw-bold"><i class="fas fa-lock me-2"></i> Authenticate Login</button>
                    </form>
                    
                    <div class="text-center mt-4">
                        <a href="../index.php" class="text-secondary text-decoration-none small hover-gold"><i class="fas fa-arrow-left me-1"></i> Return to Storefront</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
