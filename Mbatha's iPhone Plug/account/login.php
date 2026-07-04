<?php
// Mbatha's iPhone Plug - Customer Login

require_once '../config/db.php';
require_once '../includes/functions.php';

// Redirect if already logged in
if (isUserLoggedIn()) {
    header("Location: dashboard.php");
    exit;
}

// Handle login POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_user'])) {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        setFlashMessage("Please enter your email and password.", "danger");
    } else {
        // Query User
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password_hash'])) {
            // Success! Populate Session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_phone'] = $user['phone'] ?? '';
            $_SESSION['user_address'] = $user['address'] ?? '';
            
            setFlashMessage("Welcome back, {$user['name']}!", "success");
            
            // Redirect back to page before login if exists, otherwise dashboard
            if (isset($_SESSION['redirect_after_login']) && !empty($_SESSION['redirect_after_login'])) {
                $redirect = $_SESSION['redirect_after_login'];
                unset($_SESSION['redirect_after_login']);
                header("Location: " . $redirect);
            } else {
                header("Location: dashboard.php");
            }
            exit;
        } else {
            setFlashMessage("Invalid email address or password combination.", "danger");
        }
    }
}

require_once '../includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5" data-aos="zoom-in">
            <div class="glass-card p-5">
                <div class="text-center mb-4">
                    <h2 class="text-white fw-bold mb-1" style="font-family: 'Outfit', sans-serif;">Customer Login</h2>
                    <p class="text-secondary small">Access your order history and invoices.</p>
                </div>

                <form action="login.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label-custom">Email Address</label>
                        <input type="email" name="email" class="form-control form-control-custom" placeholder="e.g. sipho@gmail.com" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label-custom">Password</label>
                        <input type="password" name="password" class="form-control form-control-custom" placeholder="Enter your password" required>
                    </div>

                    <button type="submit" name="login_user" class="btn btn-gold w-100 py-3 fw-bold"><i class="fas fa-sign-in-alt me-2"></i> Log In</button>
                </form>

                <div class="text-center mt-4">
                    <span class="text-secondary small">New customer?</span>
                    <a href="register.php" class="text-warning text-decoration-none small ms-2 hover-gold">Create account</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
