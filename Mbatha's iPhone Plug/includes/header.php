<?php
// Mbatha's iPhone Plug - Global Header Template

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/cart_handler.php';

// Active page indicator helper
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mbatha's iPhone Plug | Premium Pre-Owned Apple Devices</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="Get premium quality pre-owned iPhones and Apple devices in South Africa at unbeatable prices. Certified battery health, quality checked, and nationwide delivery.">
    <meta name="keywords" content="iPhones, Apple South Africa, pre-owned iPhones, cheap iPhones Johannesburg, Mbatha iPhone Plug, buy iPhone 15, iPhone 14 Pro Max">
    <meta name="author" content="Mbatha's iPhone Plug">
    <link rel="icon" type="image/svg+xml" href="<?php echo $current_page === 'login.php' || strpos($_SERVER['REQUEST_URI'], '/account/') !== false ? '../' : ''; ?>assets/images/logo.svg">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Custom Style Path Adjustment depending on directory location -->
    <?php
        $asset_prefix = '';
        if (strpos($_SERVER['REQUEST_URI'], '/account/') !== false) {
            $asset_prefix = '../';
        }
    ?>
    <link href="<?php echo $asset_prefix; ?>assets/css/style.css" rel="stylesheet">
</head>
<body>

    <!-- Header Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom fixed-top">
        <div class="container">
            <!-- Luxury Brand Logo -->
            <a class="navbar-brand d-flex align-items-center" href="<?php echo $asset_prefix; ?>index.php">
                <svg width="40" height="40" viewBox="0 0 200 200" class="me-2" style="filter: drop-shadow(0px 2px 4px rgba(212,175,55,0.3));">
                    <!-- Gold Circle Outline -->
                    <circle cx="100" cy="100" r="90" fill="none" stroke="#d4af37" stroke-width="6"/>
                    <!-- iPhone Outline -->
                    <rect x="75" y="45" width="50" height="95" rx="10" fill="none" stroke="#ffffff" stroke-width="4"/>
                    <!-- iPhone Dynamic Island -->
                    <rect x="90" y="52" width="20" height="5" rx="2.5" fill="#ffffff"/>
                    <!-- Gold charging cable forming M -->
                    <path d="M 60 160 Q 80 120, 90 90 L 100 110 L 110 90 Q 120 120, 140 160" fill="none" stroke="#d4af37" stroke-width="5" stroke-linecap="round"/>
                    <!-- Plug Header -->
                    <rect x="93" y="80" width="14" height="10" rx="2" fill="#d4af37"/>
                    <!-- Plug Pins -->
                    <line x1="97" y1="80" x2="97" y2="74" stroke="#d4af37" stroke-width="3"/>
                    <line x1="103" y1="80" x2="103" y2="74" stroke="#d4af37" stroke-width="3"/>
                </svg>
                <div class="d-flex flex-column leading-none">
                    <span class="fs-5 fw-bold text-white tracking-wider" style="font-family: 'Outfit', sans-serif; line-height: 1.1;">MBATHA'S</span>
                    <span class="text-uppercase tracking-widest text-gradient" style="font-family: 'Outfit', sans-serif; font-size: 0.7rem; font-weight: 700; letter-spacing: 0.22em;">iPhone Plug</span>
                </div>
            </a>

            <!-- Toggle Button for Mobile -->
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Links & Action Icons -->
            <div class="collapse navbar-collapse" id="navbarText">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>" href="<?php echo $asset_prefix; ?>index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom <?php echo ($current_page == 'shop.php') ? 'active' : ''; ?>" href="<?php echo $asset_prefix; ?>shop.php">iPhones</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom <?php echo ($current_page == 'accessories.php') ? 'active' : ''; ?>" href="<?php echo $asset_prefix; ?>accessories.php">Accessories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom <?php echo ($current_page == 'tradein.php') ? 'active' : ''; ?>" href="<?php echo $asset_prefix; ?>tradein.php">Trade-In</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom <?php echo ($current_page == 'about.php') ? 'active' : ''; ?>" href="<?php echo $asset_prefix; ?>about.php">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom <?php echo ($current_page == 'faq.php') ? 'active' : ''; ?>" href="<?php echo $asset_prefix; ?>faq.php">FAQ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom <?php echo ($current_page == 'contact.php') ? 'active' : ''; ?>" href="<?php echo $asset_prefix; ?>contact.php">Contact</a>
                    </li>
                </ul>

                <!-- Utility Icons -->
                <div class="d-flex align-items-center gap-3 mt-3 mt-lg-0">
                    <!-- Search Icon Triggering Modal -->
                    <a href="#" class="text-secondary hover-gold fs-5" data-bs-toggle="modal" data-bs-target="#searchModal" title="Search Products">
                        <i class="fas fa-search"></i>
                    </a>

                    <!-- Wishlist Counter -->
                    <a href="<?php echo $asset_prefix; ?>shop.php?wishlist=1" class="text-secondary hover-gold fs-5 position-relative" title="Wishlist">
                        <i class="far fa-heart"></i>
                        <?php 
                        $wish_count = count($_SESSION['wishlist'] ?? []);
                        if ($wish_count > 0): 
                        ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.55rem; padding: 2px 5px;">
                                <?php echo $wish_count; ?>
                            </span>
                        <?php endif; ?>
                    </a>

                    <!-- Cart Counter -->
                    <a href="<?php echo $asset_prefix; ?>cart.php" class="text-secondary hover-gold fs-5 position-relative" title="Shopping Cart">
                        <i class="fas fa-shopping-bag"></i>
                        <?php 
                        $cart_count = getCartCount();
                        if ($cart_count > 0): 
                        ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill" style="font-size: 0.55rem; padding: 2px 5px; background-color: var(--accent-gold); color: var(--bg-primary);">
                                <?php echo $cart_count; ?>
                            </span>
                        <?php endif; ?>
                    </a>

                    <!-- Account Profile Dropdown -->
                    <div class="dropdown">
                        <a class="text-secondary hover-gold fs-5 dropdown-toggle no-caret" href="#" role="button" id="accountMenu" data-bs-toggle="dropdown" aria-expanded="false" title="Account">
                            <i class="far fa-user"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end border-0 rounded-3 shadow-lg" aria-labelledby="accountMenu" style="background-color: var(--bg-card); border: 1px solid var(--border-color-light) !important;">
                            <?php if (isUserLoggedIn()): ?>
                                <li><a class="dropdown-item text-white hover-gold py-2" href="<?php echo $asset_prefix; ?>account/dashboard.php"><i class="fas fa-columns me-2 text-warning"></i> Dashboard</a></li>
                                <li><hr class="dropdown-divider bg-secondary"></li>
                                <li><a class="dropdown-item text-white hover-gold py-2" href="<?php echo $asset_prefix; ?>account/logout.php"><i class="fas fa-sign-out-alt me-2 text-danger"></i> Logout</a></li>
                            <?php else: ?>
                                <li><a class="dropdown-item text-white hover-gold py-2" href="<?php echo $asset_prefix; ?>account/login.php"><i class="fas fa-sign-in-alt me-2 text-warning"></i> Login</a></li>
                                <li><a class="dropdown-item text-white hover-gold py-2" href="<?php echo $asset_prefix; ?>account/register.php"><i class="fas fa-user-plus me-2 text-warning"></i> Register</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider bg-secondary"></li>
                            <li><a class="dropdown-item text-muted hover-gold py-2" href="<?php echo $asset_prefix; ?>admin/login.php" style="font-size: 0.85rem;"><i class="fas fa-lock me-2"></i> Admin Portal</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Global Search Modal -->
    <div class="modal fade" id="searchModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 rounded-4" style="background-color: var(--bg-card); border: 1px solid var(--border-color) !important;">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form action="<?php echo $asset_prefix; ?>shop.php" method="GET">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control form-control-custom text-white border-0 py-3 ps-4" placeholder="Search for iPhones, cases, chargers..." aria-label="Search Query" autofocus>
                            <button class="btn btn-gold px-4" type="submit"><i class="fas fa-search"></i> Search</button>
                        </div>
                    </form>
                    <div class="mt-3 text-muted" style="font-size: 0.85rem;">
                        <span class="me-2">Trending:</span>
                        <a href="<?php echo $asset_prefix; ?>shop.php?search=iPhone+15" class="text-white text-decoration-none me-2 badge bg-dark py-2 px-3 border border-secondary rounded-pill hover-gold">iPhone 15</a>
                        <a href="<?php echo $asset_prefix; ?>shop.php?search=Pro+Max" class="text-white text-decoration-none me-2 badge bg-dark py-2 px-3 border border-secondary rounded-pill hover-gold">Pro Max</a>
                        <a href="<?php echo $asset_prefix; ?>shop.php?search=Charger" class="text-white text-decoration-none badge bg-dark py-2 px-3 border border-secondary rounded-pill hover-gold">20W Adapter</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Container Margins to Clear Navbar height -->
    <div style="height: 80px;"></div>
    
    <div class="container my-3">
        <?php displayFlashMessage(); ?>
    </div>
