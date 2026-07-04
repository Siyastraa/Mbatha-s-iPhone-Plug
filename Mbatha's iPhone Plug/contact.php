<?php
// Mbatha's iPhone Plug - Contact Page

require_once 'config/db.php';
require_once 'includes/functions.php';
require_once 'includes/cart_handler.php';

// Handle Contact Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_message'])) {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $subject = sanitize($_POST['subject']);
    $message = sanitize($_POST['message']);
    
    // In a real application, you might insert into a contact_messages table or send an email.
    // For this portfolio, we'll log it as a success flash message.
    setFlashMessage("Thank you, {$name}! Your message has been logged. We will get back to you within 4-12 hours.", "success");
    header("Location: contact.php");
    exit;
}

require_once 'includes/header.php';
?>

<div class="py-5" style="background: radial-gradient(circle at 50% 10%, rgba(212, 175, 55, 0.04) 0%, rgba(11, 11, 12, 1) 50%); border-bottom: 1px solid var(--border-color-light);">
    <div class="container text-center">
        <span class="text-warning text-uppercase tracking-widest fw-bold small">Get In Touch</span>
        <h1 class="text-white mt-1 mb-2 fw-bold" style="font-family: 'Outfit', sans-serif;">Contact Our Team</h1>
        <p class="text-secondary max-width-600 mx-auto mb-0">Have questions about a device, warranty, or delivery? Reach out to us directly or visit our Sandton gate showroom.</p>
    </div>
</div>

<div class="container py-5">
    <div class="row g-5 mb-5">
        <!-- Col 1: Contact Details -->
        <div class="col-lg-5" data-aos="fade-right">
            <h3 class="text-gradient fw-bold mb-4" style="font-family: 'Outfit', sans-serif;">Connect Instantly</h3>
            <p class="text-secondary mb-4">
                We are highly responsive on WhatsApp. Tap the chat button on the bottom right of the screen or use the details below to talk to us.
            </p>
            
            <div class="d-flex align-items-start gap-3 mb-4">
                <div class="text-warning fs-4 mt-1"><i class="fab fa-whatsapp"></i></div>
                <div>
                    <h6 class="text-white fw-bold mb-1">WhatsApp & Call Hotline</h6>
                    <a href="https://wa.me/27712345678" target="_blank" class="text-secondary text-decoration-none hover-gold">+27 71 234 5678</a>
                </div>
            </div>

            <div class="d-flex align-items-start gap-3 mb-4">
                <div class="text-warning fs-4 mt-1"><i class="far fa-envelope"></i></div>
                <div>
                    <h6 class="text-white fw-bold mb-1">Email Support</h6>
                    <a href="mailto:info@mbathaphoneplug.co.za" class="text-secondary text-decoration-none hover-gold">info@mbathaphoneplug.co.za</a>
                </div>
            </div>

            <div class="d-flex align-items-start gap-3 mb-4">
                <div class="text-warning fs-4 mt-1"><i class="fas fa-map-marker-alt"></i></div>
                <div>
                    <h6 class="text-white fw-bold mb-1">Showroom Location</h6>
                    <span class="text-secondary">Sandton Gate, Glenadrienne, Johannesburg, 2196</span>
                </div>
            </div>

            <div class="d-flex align-items-start gap-3">
                <div class="text-warning fs-4 mt-1"><i class="far fa-clock"></i></div>
                <div>
                    <h6 class="text-white fw-bold mb-1">Operating Hours</h6>
                    <span class="text-secondary d-block">Monday - Friday: 09:00 - 18:00</span>
                    <span class="text-secondary d-block">Saturday: 09:00 - 16:00</span>
                    <span class="text-secondary d-block">Sunday: Closed</span>
                </div>
            </div>
        </div>

        <!-- Col 2: Contact Form -->
        <div class="col-lg-7" data-aos="fade-left">
            <div class="glass-card">
                <h3 class="text-white fw-bold mb-4" style="font-family: 'Outfit', sans-serif;">Send Us a Message</h3>
                <form action="contact.php" method="POST">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label-custom">Your Name</label>
                            <input type="text" name="name" class="form-control form-control-custom" placeholder="e.g. Lerato" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Email Address</label>
                            <input type="email" name="email" class="form-control form-control-custom" placeholder="e.g. lerato@gmail.com" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label-custom">Subject</label>
                        <input type="text" name="subject" class="form-control form-control-custom" placeholder="e.g. Battery health check question" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label-custom">Your Message</label>
                        <textarea name="message" class="form-control form-control-custom" rows="5" placeholder="How can we assist you today?" required></textarea>
                    </div>

                    <button type="submit" name="submit_message" class="btn btn-gold w-100 py-3 fw-bold"><i class="fas fa-paper-plane me-2"></i> Submit Inquiry</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Google Maps Interactive Segment -->
    <div class="row mt-5" data-aos="zoom-in">
        <div class="col-12">
            <div class="glass-card p-2" style="height: 400px; overflow: hidden; border-radius: 20px;">
                <!-- Embedded Map centering Sandton, Johannesburg -->
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m12!1m3!1d3583.5670868846706!2d28.026402476258933!3d-26.080517877150993!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1e9573887bbfba15%3A0xe54d898a3e75a6c4!2sSandton%20Gate!5e0!3m2!1sen!2sza!4v1700000000000!5m2!1sen!2sza" width="100%" height="100%" style="border:0; border-radius: 12px; filter: grayscale(1) invert(0.9) contrast(1.2);" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
