<?php
// Mbatha's iPhone Plug - About Page

require_once 'config/db.php';
require_once 'includes/functions.php';
require_once 'includes/cart_handler.php';

require_once 'includes/header.php';
?>

<div class="py-5" style="background: radial-gradient(circle at 50% 10%, rgba(212, 175, 55, 0.04) 0%, rgba(11, 11, 12, 1) 50%); border-bottom: 1px solid var(--border-color-light);">
    <div class="container text-center">
        <span class="text-warning text-uppercase tracking-widest fw-bold small">Who We Are</span>
        <h1 class="text-white mt-1 mb-2 fw-bold" style="font-family: 'Outfit', sans-serif;">Our Brand Story</h1>
        <p class="text-secondary max-width-600 mx-auto mb-0">Democratizing luxury tech. Bringing trust, quality diagnostics, and fair pricing back to South Africa's pre-owned iPhone marketplace.</p>
    </div>
</div>

<div class="container py-5">
    <!-- Brand Narrative Row -->
    <div class="row align-items-center g-5 mb-5">
        <div class="col-lg-6" data-aos="fade-right">
            <h2 class="text-gradient fw-bold mb-4" style="font-family: 'Outfit', sans-serif;">The iPhone Plug Difference</h2>
            <p class="text-secondary">
                Mbatha's iPhone Plug was founded with a single mission: to provide the premium, high-status experience of owning a flagship Apple device without the crippling price tag of a brand-new upgrade.
            </p>
            <p class="text-secondary">
                We noticed a massive gap in the South African pre-owned marketplace. Buyers were faced with a choice between shady, high-risk classified sales or overpriced refurbishing stores that lack personal care. We chose to build a transparent, personalized business where every phone has documented battery health, real original parts, and a real exchange warranty.
            </p>
            <p class="text-secondary">
                From Sandton to Cape Town, we deliver nationwide. We inspect every screen, speaker, microphone, face recognition scanner, and charging port before it reaches your hands. That is our promise. That is the Plug.
            </p>
        </div>
        <div class="col-lg-6 text-center" data-aos="fade-left">
            <img src="assets/images/placeholder.php?name=Premium+Inspected+iPhones&type=iphone" class="img-fluid rounded-4 shadow-lg border border-secondary border-opacity-25" style="max-height: 380px; filter: drop-shadow(0 10px 30px rgba(212,175,55,0.15));" alt="Inspected iPhones">
        </div>
    </div>

    <!-- Dynamic Features/Stats Grid -->
    <div class="row g-4 mb-5 text-center">
        <div class="col-6 col-md-3" data-aos="zoom-in">
            <div class="glass-card p-4 h-100">
                <h2 class="text-warning fw-bold mb-2">4,500+</h2>
                <span class="text-white small">Happy South Africans</span>
            </div>
        </div>
        <div class="col-6 col-md-3" data-aos="zoom-in" data-aos-delay="100">
            <div class="glass-card p-4 h-100">
                <h2 class="text-warning fw-bold mb-2">40+</h2>
                <span class="text-white small">Diagnostic Checkpoints</span>
            </div>
        </div>
        <div class="col-6 col-md-3" data-aos="zoom-in" data-aos-delay="200">
            <div class="glass-card p-4 h-100">
                <h2 class="text-warning fw-bold mb-2">100%</h2>
                <span class="text-white small">Genuine Original Parts</span>
            </div>
        </div>
        <div class="col-6 col-md-3" data-aos="zoom-in" data-aos-delay="300">
            <div class="glass-card p-4 h-100">
                <h2 class="text-warning fw-bold mb-2">6 Months</h2>
                <span class="text-white small">Full Swap Warranty</span>
            </div>
        </div>
    </div>

    <!-- Diagnostic checklist checklist -->
    <div class="glass-card p-5" data-aos="fade-up">
        <h3 class="text-white text-center fw-bold mb-5" style="font-family: 'Outfit', sans-serif;">Our 40-Point Diagnostic Inspection</h3>
        <div class="row g-4">
            <div class="col-md-4">
                <h5 class="text-warning mb-3 fw-bold"><i class="fas fa-battery-full me-2"></i> Battery Health</h5>
                <p class="text-secondary small">We never sell devices with degraded batteries. Every phone lists verified battery health, guaranteeing a minimum of 85%+ maximum operating capacity.</p>
            </div>
            <div class="col-md-4">
                <h5 class="text-warning mb-3 fw-bold"><i class="fas fa-microchip me-2"></i> Logic board & FaceID</h5>
                <p class="text-secondary small">Sensors, Face recognition, cameras, Bluetooth/Wi-Fi chips, and speakers are fully audited via professional hardware tests. No system errors.</p>
            </div>
            <div class="col-md-4">
                <h5 class="text-warning mb-3 fw-bold"><i class="fas fa-mobile-alt me-2"></i> Cosmetic Grading</h5>
                <p class="text-secondary small">We are transparent about aesthetic conditions. Every device is graded accurately (New, Like New, Excellent, Very Good) so there are no surprises.</p>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
