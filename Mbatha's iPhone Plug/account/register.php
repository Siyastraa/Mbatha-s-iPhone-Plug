<?php
// Mbatha's iPhone Plug - Customer Registration

require_once '../config/db.php';
require_once '../includes/functions.php';

// Redirect if already logged in
if (isUserLoggedIn()) {
    header("Location: dashboard.php");
    exit;
}

// Handle registration processing
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_user'])) {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $address = sanitize($_POST['address']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Server-side validation
    if (empty($name) || empty($email) || empty($password)) {
        setFlashMessage("Please complete all required fields.", "danger");
    } elseif ($password !== $confirm_password) {
        setFlashMessage("Passwords do not match.", "danger");
    } elseif (strlen($password) < 6) {
        setFlashMessage("Password must be at least 6 characters long.", "danger");
    } else {
        // Check if email already registered
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            setFlashMessage("Email address is already registered. Try logging in instead.", "danger");
        } else {
            // Hash Password securely
            $password_hash = password_hash($password, PASSWORD_BCRYPT);
            
            // Insert User
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, phone, address) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $password_hash, $phone, $address]);
            
            // Set User Session details
            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_phone'] = $phone;
            $_SESSION['user_address'] = $address;
            
            setFlashMessage("Welcome! Your account has been registered successfully.", "success");
            header("Location: dashboard.php");
            exit;
        }
    }
}

require_once '../includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6" data-aos="zoom-in">
            <div class="glass-card p-5">
                <div class="text-center mb-4">
                    <h2 class="text-white fw-bold mb-1" style="font-family: 'Outfit', sans-serif;">Create Account</h2>
                    <p class="text-secondary small">Join the club for faster order tracking and history.</p>
                </div>

                <form action="register.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label-custom">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control form-control-custom" placeholder="e.g. Sipho Mbatha" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label-custom">Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control form-control-custom" placeholder="e.g. sipho@gmail.com" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label-custom">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control form-control-custom" placeholder="Min. 6 chars" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" name="confirm_password" class="form-control form-control-custom" placeholder="Match password" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label-custom">WhatsApp Phone Number</label>
                        <input type="tel" name="phone" class="form-control form-control-custom" placeholder="e.g. 0712345678">
                    </div>

                    <div class="mb-4">
                        <label class="form-label-custom">Default Delivery Address</label>
                        <textarea name="address" class="form-control form-control-custom" rows="2" placeholder="e.g. 15 Sandton Gate Drive, Johannesburg"></textarea>
                    </div>

                    <button type="submit" name="register_user" class="btn btn-gold w-100 py-3 fw-bold"><i class="fas fa-user-plus me-2"></i> Register Account</button>
                </form>

                <div class="text-center mt-4">
                    <span class="text-secondary small">Already have an account?</span>
                    <a href="login.php" class="text-warning text-decoration-none small ms-2 hover-gold">Login here</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
