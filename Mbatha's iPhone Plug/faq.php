<?php
// Mbatha's iPhone Plug - FAQ & Warranty Information

require_once 'config/db.php';
require_once 'includes/functions.php';
require_once 'includes/cart_handler.php';

require_once 'includes/header.php';
?>

<div class="py-5" style="background: radial-gradient(circle at 50% 10%, rgba(212, 175, 55, 0.04) 0%, rgba(11, 11, 12, 1) 50%); border-bottom: 1px solid var(--border-color-light);">
    <div class="container text-center">
        <span class="text-warning text-uppercase tracking-widest fw-bold small">Information & Policies</span>
        <h1 class="text-white mt-1 mb-2 fw-bold" style="font-family: 'Outfit', sans-serif;">Frequently Asked Questions</h1>
        <p class="text-secondary max-width-600 mx-auto mb-0">Learn about our diagnostic standards, delivery protocols, 7-day exchange rules, and 6-month warranty guarantees.</p>
    </div>
</div>

<div class="container py-5">
    <div class="row g-5">
        <!-- Sidebar Navigation Shortcuts -->
        <div class="col-lg-4 d-none d-lg-block" data-aos="fade-right">
            <div class="glass-card sticky-top" style="top: 100px;">
                <h5 class="text-white mb-4 fw-bold"><i class="fas fa-info-circle text-warning me-2"></i> Policies Overview</h5>
                <ul class="list-unstyled mb-0">
                    <li class="mb-3"><i class="fas fa-check text-warning me-2"></i> <strong>7-Day Exchange</strong>: Instant swap if not satisfied.</li>
                    <li class="mb-3"><i class="fas fa-check text-warning me-2"></i> <strong>6-Month Warranty</strong>: Repair or swap on hardware failures.</li>
                    <li class="mb-3"><i class="fas fa-check text-warning me-2"></i> <strong>Nationwide Delivery</strong>: 1-3 days tracked.</li>
                    <li class="mb-0"><i class="fas fa-check text-warning me-2"></i> <strong>85%+ Battery</strong>: Guaranteed capacity health.</li>
                </ul>
            </div>
        </div>

        <!-- Accordions -->
        <div class="col-lg-8" data-aos="fade-left">
            <div class="accordion accordion-flush" id="faqAccordion">
                
                <!-- FAQ 1: Battery Health -->
                <div class="accordion-item bg-transparent mb-3 border border-secondary border-opacity-25 rounded-4 overflow-hidden" style="background-color: var(--bg-secondary) !important;">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button bg-transparent text-white fw-bold py-3 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne" style="box-shadow: none;">
                            <i class="fas fa-battery-three-quarters text-warning me-3"></i> What is your battery health guarantee?
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-secondary small pt-0">
                            We guarantee that all pre-owned iPhones sold by Mbatha's iPhone Plug have a minimum battery health capacity of **85% or higher** (unless explicitly marked on sale). This ensures your device operates at peak performance capacity. Every battery is diagnostics audited prior to listing.
                        </div>
                    </div>
                </div>

                <!-- FAQ 2: Delivery -->
                <div class="accordion-item bg-transparent mb-3 border border-secondary border-opacity-25 rounded-4 overflow-hidden" style="background-color: var(--bg-secondary) !important;">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button bg-transparent text-white fw-bold py-3 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo" style="box-shadow: none;">
                            <i class="fas fa-truck text-warning me-3"></i> How does nationwide delivery work?
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-secondary small pt-0">
                            We ship nationwide across South Africa via **The Courier Guy**. Delivery takes 1-3 business days (Johannesburg/Pretoria usually 24 hours, coastal areas 2-3 days). Once your order is processed and packed, you will receive a tracking link via Email or SMS. Courier costs a flat R150 or is free for orders over R12,000.
                        </div>
                    </div>
                </div>

                <!-- FAQ 3: Return Window -->
                <div class="accordion-item bg-transparent mb-3 border border-secondary border-opacity-25 rounded-4 overflow-hidden" style="background-color: var(--bg-secondary) !important;">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button bg-transparent text-white fw-bold py-3 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree" style="box-shadow: none;">
                            <i class="fas fa-undo text-warning me-3"></i> What is your 7-Day Exchange policy?
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-secondary small pt-0">
                            If you receive a device and change your mind, we offer a **7-Day hassle-free exchange window**. The device must be in the exact condition it was delivered in, with no user accounts logged in (i.e. iCloud signed out), and including all accessories. Shipping fees are responsibility of the customer.
                        </div>
                    </div>
                </div>

                <!-- FAQ 4: Warranty -->
                <div class="accordion-item bg-transparent mb-3 border border-secondary border-opacity-25 rounded-4 overflow-hidden" style="background-color: var(--bg-secondary) !important;">
                    <h2 class="accordion-header" id="headingFour">
                        <button class="accordion-button bg-transparent text-white fw-bold py-3 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour" style="box-shadow: none;">
                            <i class="fas fa-shield-alt text-warning me-3"></i> What does the 6-Month Warranty cover?
                        </h2>
                    </h2>
                    <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-secondary small pt-0">
                            Our **6-Month Warranty** covers all hardware defect issues, including malfunctions in the screen display, audio speakers, charging dock port, FaceID camera arrays, battery cells, or software operating failures. 
                            <br><br>
                            <strong>Exclusions:</strong> The warranty strictly does not cover any damage resulting from drop impacts (cracks), liquid submersion (water ingress), unauthorized motherboard repairs, or jailbreaking.
                        </div>
                    </div>
                </div>

                <!-- FAQ 5: Payment Methods -->
                <div class="accordion-item bg-transparent mb-3 border border-secondary border-opacity-25 rounded-4 overflow-hidden" style="background-color: var(--bg-secondary) !important;">
                    <h2 class="accordion-header" id="headingFive">
                        <button class="accordion-button bg-transparent text-white fw-bold py-3 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive" style="box-shadow: none;">
                            <i class="fas fa-wallet text-warning me-3"></i> What payment methods do you accept?
                        </h2>
                    </h2>
                    <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-secondary small pt-0">
                            We accept secure **Electronic Funds Transfer (EFT)** payments. Orders will only be packed and shipped once the funds reflect in our bank account. Alternatively, cash or instant EFT on collection is accepted at our Sandton showroom by pre-booking appointment.
                        </div>
                    </div>
                </div>

                <!-- FAQ 6: Trade-In Process -->
                <div class="accordion-item bg-transparent border border-secondary border-opacity-25 rounded-4 overflow-hidden" style="background-color: var(--bg-secondary) !important;">
                    <h2 class="accordion-header" id="headingSix">
                        <button class="accordion-button bg-transparent text-white fw-bold py-3 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix" style="box-shadow: none;">
                            <i class="fas fa-exchange-alt text-warning me-3"></i> How long does the Trade-In quote validation take?
                        </button>
                    </h2>
                    <div id="collapseSix" class="accordion-collapse collapse" aria-labelledby="headingSix" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-secondary small pt-0">
                            After submitting your online request, we will contact you within **4-12 hours** with a formal quotation range. Once you deliver the device to our Sandton showroom or we receive it via courier, a technician will run a physical diagnostic check (takes 15 minutes). Once verified, the funds are paid to you via immediate EFT or deducted from your upgrade purchase.
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
