<?php
// Mbatha's iPhone Plug - Global Footer Template
$asset_prefix_footer = '';
if (strpos($_SERVER['REQUEST_URI'], '/account/') !== false) {
    $asset_prefix_footer = '../';
}
?>
    <!-- Footer Section -->
    <footer class="footer-custom mt-auto">
        <div class="container">
            <div class="row g-4 mb-5">
                <!-- Col 1: Brand Info -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up">
                    <div class="footer-brand d-flex align-items-center mb-3">
                        <svg width="35" height="35" viewBox="0 0 200 200" class="me-2" style="filter: drop-shadow(0px 2px 4px rgba(212,175,55,0.3));">
                            <circle cx="100" cy="100" r="90" fill="none" stroke="#d4af37" stroke-width="6"/>
                            <rect x="75" y="45" width="50" height="95" rx="10" fill="none" stroke="#ffffff" stroke-width="4"/>
                            <rect x="90" y="52" width="20" height="5" rx="2.5" fill="#ffffff"/>
                            <path d="M 60 160 Q 80 120, 90 90 L 100 110 L 110 90 Q 120 120, 140 160" fill="none" stroke="#d4af37" stroke-width="5" stroke-linecap="round"/>
                            <rect x="93" y="80" width="14" height="10" rx="2" fill="#d4af37"/>
                            <line x1="97" y1="80" x2="97" y2="74" stroke="#d4af37" stroke-width="3"/>
                            <line x1="103" y1="80" x2="103" y2="74" stroke="#d4af37" stroke-width="3"/>
                        </svg>
                        <div class="d-flex flex-column leading-none">
                            <span class="fs-6 fw-bold text-white tracking-wider" style="font-family: 'Outfit', sans-serif; line-height: 1.1;">MBATHA'S</span>
                            <span class="text-uppercase tracking-widest text-gradient" style="font-family: 'Outfit', sans-serif; font-size: 0.6rem; font-weight: 700; letter-spacing: 0.22em;">iPhone Plug</span>
                        </div>
                    </div>
                    <p class="text-secondary" style="font-size: 0.9rem;">
                        Your premier boutique for luxury pre-owned Apple devices in South Africa. We check, verify, and warranty every single device so you can upgrade smarter and save bigger.
                    </p>
                    <div class="d-flex mt-3">
                        <a href="https://wa.me/27712345678" target="_blank" class="footer-social-icon"><i class="fab fa-whatsapp"></i></a>
                        <a href="https://instagram.com/mbatha_iphone_plug" target="_blank" class="footer-social-icon"><i class="fab fa-instagram"></i></a>
                        <a href="https://facebook.com/mbathaphoneplug" target="_blank" class="footer-social-icon"><i class="fab fa-facebook-f"></i></a>
                    </div>
                </div>

                <!-- Col 2: Quick Links -->
                <div class="col-lg-2 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <h5 class="text-white mb-4" style="font-family: 'Outfit', sans-serif;">Quick Links</h5>
                    <ul class="footer-links">
                        <li><a href="<?php echo $asset_prefix_footer; ?>shop.php">Browse iPhones</a></li>
                        <li><a href="<?php echo $asset_prefix_footer; ?>accessories.php">Accessories</a></li>
                        <li><a href="<?php echo $asset_prefix_footer; ?>tradein.php">Trade-In Value</a></li>
                        <li><a href="<?php echo $asset_prefix_footer; ?>about.php">Our Brand Story</a></li>
                        <li><a href="<?php echo $asset_prefix_footer; ?>faq.php">Warranty & FAQ</a></li>
                    </ul>
                </div>

                <!-- Col 3: Business Hours & Support -->
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <h5 class="text-white mb-4" style="font-family: 'Outfit', sans-serif;">Store Hours</h5>
                    <ul class="list-unstyled text-secondary" style="font-size: 0.9rem;">
                        <li class="mb-2"><i class="far fa-clock text-warning me-2"></i> Mon - Fri: 09:00 - 18:00</li>
                        <li class="mb-2"><i class="far fa-clock text-warning me-2"></i> Saturday: 09:00 - 16:00</li>
                        <li class="mb-2"><i class="far fa-clock text-warning me-2"></i> Sunday: Closed (Pre-bookings only)</li>
                        <li class="mb-2"><i class="fas fa-map-marker-alt text-warning me-2"></i> Sandton Gate, Johannesburg, GP</li>
                        <li class="mb-2"><i class="fas fa-shipping-fast text-warning me-2"></i> Nationwide Delivery via The Courier Guy</li>
                    </ul>
                </div>

                <!-- Col 4: Newsletter -->
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <h5 class="text-white mb-4" style="font-family: 'Outfit', sans-serif;">Join The Club</h5>
                    <p class="text-secondary" style="font-size: 0.9rem;">Subscribe to get alerted first when fresh iPhone stocks and flash sales land.</p>
                    <form action="<?php echo $asset_prefix_footer; ?>index.php" method="POST" class="mt-3">
                        <input type="hidden" name="action" value="subscribe">
                        <div class="input-group">
                            <input type="email" name="email" class="form-control form-control-custom border-secondary" placeholder="Your Email Address" required style="font-size: 0.85rem;">
                            <button class="btn btn-gold px-3" type="submit"><i class="fas fa-paper-plane"></i></button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Footer Divider -->
            <hr class="bg-secondary opacity-25 my-4">

            <!-- Footer Bottom -->
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start text-secondary mb-3 mb-md-0" style="font-size: 0.85rem;">
                    &copy; <?php echo date('Y'); ?> Mbatha's iPhone Plug. All Rights Reserved. Crafted for Luxury Pre-Owned Value.
                </div>
                <div class="col-md-6 text-center text-md-end text-secondary" style="font-size: 0.85rem;">
                    <a href="<?php echo $asset_prefix_footer; ?>faq.php" class="text-secondary text-decoration-none me-3 hover-gold">Terms of Service</a>
                    <a href="<?php echo $asset_prefix_footer; ?>faq.php" class="text-secondary text-decoration-none hover-gold">Warranty Details</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Floating Actions -->
    <a href="https://wa.me/27712345678?text=Hello%20Mbatha's%20iPhone%20Plug!%20I'm%20visiting%20your%20website%20and%20would%20like%20to%20inquire%20about%20your%20latest%20device%20list." class="whatsapp-float animate__animated animate__bounceIn" target="_blank" title="Contact Us on WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>
    
    <div class="back-to-top" id="backToTop" title="Back to Top">
        <i class="fas fa-arrow-up"></i>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="<?php echo $asset_prefix_footer; ?>assets/js/main.js"></script>
</body>
</html>
