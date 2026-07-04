<?php
// Mbatha's iPhone Plug - Trade-In Valuation Request

require_once 'config/db.php';
require_once 'includes/functions.php';
require_once 'includes/cart_handler.php';

// Handle Quote Submission POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_tradein'])) {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $phone_model = sanitize($_POST['phone_model']);
    $storage = sanitize($_POST['storage']);
    $battery_health = (int)$_POST['battery_health'];
    $condition_grade = sanitize($_POST['condition_grade']);
    
    // File Upload Processing
    $photo_path = null;
    ensureImageDirectory();
    
    if (isset($_FILES['device_photo']) && $_FILES['device_photo']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['device_photo']['tmp_name'];
        $file_name = $_FILES['device_photo']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        $allowed_exts = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array($file_ext, $allowed_exts)) {
            $unique_name = 'trade_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $file_ext;
            $upload_dest = __DIR__ . '/assets/images/tradeins/' . $unique_name;
            
            if (move_uploaded_file($file_tmp, $upload_dest)) {
                $photo_path = 'assets/images/tradeins/' . $unique_name;
            }
        }
    }
    
    // Calculate initial estimated quote on server side (to save to DB)
    $baseValue = 1500;
    if (strpos($phone_model, '15 Pro Max') !== false) $baseValue = 12000;
    elseif (strpos($phone_model, '15 Pro') !== false) $baseValue = 10000;
    elseif (strpos($phone_model, '15') !== false) $baseValue = 8500;
    elseif (strpos($phone_model, '14 Pro Max') !== false) $baseValue = 9000;
    elseif (strpos($phone_model, '14 Pro') !== false) $baseValue = 8000;
    elseif (strpos($phone_model, '14') !== false) $baseValue = 6800;
    elseif (strpos($phone_model, '13 Pro Max') !== false) $baseValue = 7500;
    elseif (strpos($phone_model, '13 Pro') !== false) $baseValue = 6800;
    elseif (strpos($phone_model, '13') !== false) $baseValue = 5500;
    elseif (strpos($phone_model, '12') !== false) $baseValue = 4800;
    elseif (strpos($phone_model, '11') !== false) $baseValue = 3800;
    
    if ($storage === '256GB') $baseValue += 600;
    elseif ($storage === '512GB') $baseValue += 1300;
    
    $condFactor = 1.0;
    if ($condition_grade === 'Flawless') $condFactor = 1.1;
    elseif ($condition_grade === 'Minor Scratches') $condFactor = 0.85;
    elseif ($condition_grade === 'Cracked Screen') $condFactor = 0.5;
    
    $batFactor = 1.0;
    if ($battery_health >= 90) $batFactor = 1.0;
    elseif ($battery_health >= 85) $batFactor = 0.92;
    else $batFactor = 0.75;
    
    $computed_quote = round($baseValue * $condFactor * $batFactor);
    
    // Insert into database
    $stmt = $pdo->prepare("INSERT INTO trade_ins (name, email, phone, phone_model, storage, battery_health, condition_grade, photo_url, quotation_amount) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $email, $phone, $phone_model, $storage, $battery_health, $condition_grade, $photo_path, $computed_quote]);
    
    // Construct custom WhatsApp verification follow-up link
    $whatsapp_msg = "Hello Mbatha's iPhone Plug! I've just requested a trade-in quote on your website for my " . $phone_model . " (" . $storage . ", " . $battery_health . "% battery health, Condition: " . $condition_grade . "). My estimated online quote range was " . formatPrice($computed_quote * 0.95) . " - " . formatPrice($computed_quote * 1.05) . ". Can we proceed?";
    $_SESSION['tradein_whatsapp'] = "https://wa.me/27712345678?text=" . urlencode($whatsapp_msg);
    
    setFlashMessage("Trade-in request logged successfully! Click the button below to connect with us on WhatsApp to finalize your quote.", "success");
    header("Location: tradein.php?success=1");
    exit;
}

require_once 'includes/header.php';
?>

<!-- Title Banner -->
<div class="py-5" style="background: radial-gradient(circle at 50% 10%, rgba(212, 175, 55, 0.04) 0%, rgba(11, 11, 12, 1) 50%); border-bottom: 1px solid var(--border-color-light);">
    <div class="container text-center">
        <span class="text-warning text-uppercase tracking-widest fw-bold small">Upgrade Faster</span>
        <h1 class="text-white mt-1 mb-2 fw-bold" style="font-family: 'Outfit', sans-serif;">iPhone Trade-In Portal</h1>
        <p class="text-secondary max-width-600 mx-auto mb-0">Turn your current device into immediate discount credit. Get a fast estimated valuation online, submit photos, and swap your device today.</p>
    </div>
</div>

<div class="container py-5">
    <?php if (isset($_GET['success']) && isset($_SESSION['tradein_whatsapp'])): ?>
        <!-- Success CTA redirect page to WhatsApp -->
        <div class="glass-card text-center p-5 max-width-600 mx-auto border-warning" data-aos="zoom-in">
            <i class="fab fa-whatsapp text-success fa-4x mb-4"></i>
            <h2 class="text-white fw-bold">Let's Finalize on WhatsApp</h2>
            <p class="text-secondary my-3">
                Your online trade-in quotation query is successfully saved! To get your final valuation and book your trade-in, click the button below to send your details directly to Mbatha's iPhone Plug.
            </p>
            <a href="<?php echo $_SESSION['tradein_whatsapp']; ?>" target="_blank" class="btn btn-success py-3 px-5 rounded-pill fs-5 fw-bold text-white mt-3" style="background-color: #25d366; border: none;">
                <i class="fab fa-whatsapp me-2"></i> Click to Message Mbatha
            </a>
            <div class="mt-4">
                <a href="tradein.php" class="text-warning text-decoration-none small">Submit another trade-in request</a>
            </div>
        </div>
        <?php unset($_SESSION['tradein_whatsapp']); ?>
        
    <?php else: ?>
        <!-- General Trade-in Form Layout -->
        <div class="row g-5">
            <!-- Left Column: Interactive Calculator -->
            <div class="col-lg-6" data-aos="fade-right">
                <div class="glass-card h-100 p-4 d-flex flex-column justify-content-between">
                    <div>
                        <h3 class="text-gradient fw-bold mb-4" style="font-family: 'Outfit', sans-serif;">Instant Estimate Calculator</h3>
                        <p class="text-secondary small mb-4">
                            Adjust the criteria below to get a real-time price estimation of your phone's trade-in value. This range is subject to confirmation after physical inspection.
                        </p>
                        
                        <!-- Calculator Form Elements -->
                        <div id="tradeInCalculator">
                            <!-- Model Select -->
                            <div class="mb-3">
                                <label class="form-label-custom">Select Model Series</label>
                                <select id="phone_model" class="form-select form-control-custom">
                                    <option value="iPhone 15 Pro Max">iPhone 15 Pro Max</option>
                                    <option value="iPhone 15 Pro">iPhone 15 Pro</option>
                                    <option value="iPhone 15">iPhone 15</option>
                                    <option value="iPhone 14 Pro Max">iPhone 14 Pro Max</option>
                                    <option value="iPhone 14 Pro" selected>iPhone 14 Pro</option>
                                    <option value="iPhone 14">iPhone 14</option>
                                    <option value="iPhone 13 Pro Max">iPhone 13 Pro Max</option>
                                    <option value="iPhone 13 Pro">iPhone 13 Pro</option>
                                    <option value="iPhone 13">iPhone 13</option>
                                    <option value="iPhone 12 Pro Max">iPhone 12 Pro Max</option>
                                    <option value="iPhone 12">iPhone 12</option>
                                    <option value="iPhone 11 Pro Max">iPhone 11 Pro Max</option>
                                    <option value="iPhone 11">iPhone 11</option>
                                    <option value="iPhone XR">iPhone XR</option>
                                    <option value="iPhone 8 Plus">iPhone 8 Plus</option>
                                </select>
                            </div>
                            
                            <!-- Storage -->
                            <div class="mb-3">
                                <label class="form-label-custom">Storage Capacity</label>
                                <select id="storage" class="form-select form-control-custom">
                                    <option value="64GB">64 GB</option>
                                    <option value="128GB" selected>128 GB</option>
                                    <option value="256GB">256 GB</option>
                                    <option value="512GB">512 GB</option>
                                    <option value="1TB">1 TB</option>
                                </select>
                            </div>

                            <!-- Condition -->
                            <div class="mb-3">
                                <label class="form-label-custom">Physical Condition</label>
                                <select id="condition_grade" class="form-select form-control-custom">
                                    <option value="Flawless">Flawless (No marks, like new)</option>
                                    <option value="Good" selected>Good (Minor normal usage marks)</option>
                                    <option value="Minor Scratches">Minor Scratches (Slight display scuffs)</option>
                                    <option value="Cracked Screen">Cracked (Front/Back glass broken but functional)</option>
                                </select>
                            </div>

                            <!-- Battery health slider -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <label class="form-label-custom mb-0">Battery Health</label>
                                    <span class="range-slider-value" id="battery_health_val">90%</span>
                                </div>
                                <input type="range" id="battery_health" class="form-range" min="70" max="100" value="90">
                                <div class="d-flex justify-content-between text-muted" style="font-size: 0.75rem;">
                                    <span>70% (Service required)</span>
                                    <span>100% (Brand New)</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Computed output display box -->
                    <div class="p-4 rounded-4 mt-4 text-center border border-warning" style="background: rgba(212,175,55,0.06); border-width: 2px !important;">
                        <span class="text-secondary uppercase tracking-widest small fw-bold d-block mb-1">Estimated Trade-In Payout</span>
                        <h2 class="text-warning fw-bold mb-0" id="estimate_value" style="font-size: 2.2rem; font-family: 'Outfit', sans-serif;">R 6,800 - R 7,500</h2>
                        <small class="text-muted d-block mt-2" style="font-size: 0.75rem;">*Inspection results determine final payout offer. Cash out or use as trade-in credit.</small>
                    </div>
                </div>
            </div>

            <!-- Right Column: Submission Form -->
            <div class="col-lg-6" data-aos="fade-left">
                <div class="glass-card p-4">
                    <h3 class="text-white fw-bold mb-4" style="font-family: 'Outfit', sans-serif;">Submit for Valuation</h3>
                    
                    <form action="tradein.php" method="POST" enctype="multipart/form-data">
                        <!-- Transfer fields from calculator to submit -->
                        <div class="mb-3">
                            <label class="form-label-custom">Your Full Name</label>
                            <input type="text" name="name" class="form-control form-control-custom" placeholder="e.g. Sipho Mbatha" required>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label-custom">Email Address</label>
                                <input type="email" name="email" class="form-control form-control-custom" placeholder="e.g. sipho@gmail.com" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">WhatsApp Phone Number</label>
                                <input type="tel" name="phone" class="form-control form-control-custom" placeholder="e.g. 0712345678" required>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label-custom">Model to Trade</label>
                                <select name="phone_model" class="form-select form-control-custom" required>
                                    <option value="iPhone 15 Pro Max">iPhone 15 Pro Max</option>
                                    <option value="iPhone 15 Pro">iPhone 15 Pro</option>
                                    <option value="iPhone 15">iPhone 15</option>
                                    <option value="iPhone 14 Pro Max">iPhone 14 Pro Max</option>
                                    <option value="iPhone 14 Pro">iPhone 14 Pro</option>
                                    <option value="iPhone 14">iPhone 14</option>
                                    <option value="iPhone 13 Pro Max">iPhone 13 Pro Max</option>
                                    <option value="iPhone 13 Pro">iPhone 13 Pro</option>
                                    <option value="iPhone 13">iPhone 13</option>
                                    <option value="iPhone 12">iPhone 12</option>
                                    <option value="iPhone 11">iPhone 11</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">Capacity</label>
                                <select name="storage" class="form-select form-control-custom" required>
                                    <option value="64GB">64 GB</option>
                                    <option value="128GB">128 GB</option>
                                    <option value="256GB">256 GB</option>
                                    <option value="512GB">512 GB</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label-custom">Battery Health (%)</label>
                                <input type="number" name="battery_health" class="form-control form-control-custom" min="50" max="100" value="88" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">Condition</label>
                                <select name="condition_grade" class="form-select form-control-custom" required>
                                    <option value="Flawless">Flawless (99%+ Condition)</option>
                                    <option value="Good">Good (Minor normal usage scuffs)</option>
                                    <option value="Minor Scratches">Minor Scratches</option>
                                    <option value="Cracked Screen">Cracked screen or back glass</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label-custom">Upload Device Screenshot or Image (Optional)</label>
                            <input type="file" name="device_photo" class="form-control form-control-custom" accept="image/*">
                            <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Uploading a photo of the "Settings > General > About" screen and outer body helps us guarantee your offer faster.</small>
                        </div>

                        <button type="submit" name="submit_tradein" class="btn btn-gold w-100 py-3 fw-bold"><i class="fas fa-paper-plane me-2"></i> Submit Online Quotation</button>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
