<?php
// Mbatha's iPhone Plug - Global Helper Functions

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/**
 * Format numeric price to South African Rand formatting (e.g., R 10,999)
 */
function formatPrice($price) {
    return 'R ' . number_format((float)$price, 0, '.', ',');
}

/**
 * Sanitize user inputs for security (XSS prevention)
 */
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Generate CSRF Token and inject it into the session
 */
function getCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify form CSRF Token
 */
function verifyCsrfToken($token) {
    if (!isset($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Customer Authentication Helpers
 */
function isUserLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function getLoggedInUser() {
    if (!isUserLoggedIn()) return null;
    return [
        'id' => $_SESSION['user_id'],
        'name' => $_SESSION['user_name'],
        'email' => $_SESSION['user_email'],
        'phone' => $_SESSION['user_phone'] ?? '',
        'address' => $_SESSION['user_address'] ?? ''
    ];
}

function requireUserLogin() {
    if (!isUserLoggedIn()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header("Location: ../account/login.php");
        exit;
    }
}

/**
 * Admin Authentication Helpers
 */
function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        header("Location: login.php");
        exit;
    }
}

/**
 * Output Bootstrap alert if session messages are set
 */
function displayFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $msg = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'] ?? 'info';
        echo '<div class="alert alert-' . $type . ' alert-dismissible fade show border-0 rounded-4 shadow-sm" role="alert" style="background: rgba(22, 22, 26, 0.9); backdrop-filter: blur(10px); color: ' . ($type === 'success' ? '#d4af37' : '#ff4d4d') . '; border-left: 4px solid ' . ($type === 'success' ? '#d4af37' : '#ff4d4d') . ' !important;">
                <i class="fas ' . ($type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle') . ' me-2"></i> ' . $msg . '
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
    }
}

function setFlashMessage($message, $type = 'success') {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
}

/**
 * Generate high quality local images dynamically as SVGs if missing
 */
function ensureImageDirectory() {
    $dirs = [
        __DIR__ . '/../assets/images/products',
        __DIR__ . '/../assets/images/reviews',
        __DIR__ . '/../assets/images/tradeins',
    ];
    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
}

/**
 * Get product image URL (returns local image path if exists, otherwise calls SVG placeholder)
 */
function getProductImage($imageUrl, $name, $category = 'Phone') {
    $local_path = __DIR__ . '/../' . $imageUrl;
    
    if (!empty($imageUrl) && file_exists($local_path) && !is_dir($local_path)) {
        return $imageUrl;
    }
    
    $prefix = '';
    if (strpos($_SERVER['REQUEST_URI'], '/account/') !== false) {
        $prefix = '../';
    }
    
    $type = 'iphone';
    $cat = strtolower($category);
    if (strpos($cat, 'airpod') !== false) {
        $type = 'airpods';
    } else if (strpos($cat, 'watch') !== false) {
        $type = 'watch';
    } else if (strpos($cat, 'charger') !== false || strpos($cat, 'power') !== false || strpos($cat, 'plug') !== false) {
        $type = 'charger';
    }
    
    return $prefix . 'assets/images/placeholder.php?name=' . urlencode($name) . '&type=' . $type;
}
?>
