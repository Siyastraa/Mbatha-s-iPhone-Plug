// Mbatha's iPhone Plug - Front-end Interactions & Animations

document.addEventListener('DOMContentLoaded', function() {
    
    // 1. Initialize AOS (Animate on Scroll)
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 800,
            easing: 'ease-out-cubic',
            once: true,
            offset: 50
        });
    }

    // 2. GSAP Animations for Hero Section
    if (typeof gsap !== 'undefined') {
        const tl = gsap.timeline({ defaults: { ease: "power4.out" } });
        
        // Animating hero texts and button sequentially
        if (document.querySelector('.hero-subtitle')) {
            tl.fromTo('.hero-subtitle', { opacity: 0, y: 30 }, { opacity: 1, y: 0, duration: 1, delay: 0.2 })
              .fromTo('.hero-title', { opacity: 0, y: 40 }, { opacity: 1, y: 0, duration: 1.2 }, "-=0.8")
              .fromTo('.hero-lead', { opacity: 0, y: 20 }, { opacity: 1, y: 0, duration: 1 }, "-=0.9")
              .fromTo('.hero-cta-btn', { opacity: 0, scale: 0.9 }, { opacity: 1, scale: 1, duration: 0.8 }, "-=0.8")
              .fromTo('.hero-img-container', { opacity: 0, x: 50 }, { opacity: 1, x: 0, duration: 1.5 }, "-=1.2");
        }
    }

    // 3. Navbar Sticky Effect on Scroll
    const navbar = document.querySelector('.navbar-custom');
    if (navbar) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    }

    // 4. Back To Top Button Behavior
    const backToTop = document.getElementById('backToTop');
    if (backToTop) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 400) {
                backToTop.classList.add('show');
            } else {
                backToTop.classList.remove('show');
            }
        });
        backToTop.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // 5. Product Gallery Image Switcher (Details Page)
    const thumbnails = document.querySelectorAll('.gallery-thumb');
    const mainImg = document.querySelector('.gallery-main img');
    if (thumbnails.length > 0 && mainImg) {
        thumbnails.forEach(thumb => {
            thumb.addEventListener('click', function() {
                // Remove active class from all
                thumbnails.forEach(t => t.classList.remove('active'));
                // Add to clicked
                this.classList.add('active');
                // Get image path from thumb image source
                const newSrc = this.querySelector('img').getAttribute('src');
                // Fade effect using CSS transition or manual reset
                mainImg.style.opacity = 0;
                setTimeout(() => {
                    mainImg.setAttribute('src', newSrc);
                    mainImg.style.opacity = 1;
                }, 150);
            });
        });
    }

    // 6. Dynamic Trade-In Valuation Calculator (Interactive Estimate)
    const tradeForm = document.getElementById('tradeInCalculator');
    if (tradeForm) {
        const phoneModel = document.getElementById('phone_model');
        const storageVal = document.getElementById('storage');
        const batteryVal = document.getElementById('battery_health');
        const conditionVal = document.getElementById('condition_grade');
        const estimateText = document.getElementById('estimate_value');
        const batteryDisplay = document.getElementById('battery_health_val');

        // Initial battery text trigger
        if (batteryVal && batteryDisplay) {
            batteryDisplay.textContent = batteryVal.value + '%';
            batteryVal.addEventListener('input', function() {
                batteryDisplay.textContent = this.value + '%';
                calculateTradeEstimate();
            });
        }

        // Add event listeners for other filters to update quotation estimation instantly
        [phoneModel, storageVal, conditionVal].forEach(elem => {
            if (elem) elem.addEventListener('change', calculateTradeEstimate);
        });

        function calculateTradeEstimate() {
            let baseValue = 0;
            const model = phoneModel.value;
            const storage = storageVal.value;
            const condition = conditionVal.value;
            const battery = parseInt(batteryVal.value);

            // 1. Base pricing estimate by Model
            if (model.includes('15 Pro Max')) baseValue = 12000;
            else if (model.includes('15 Pro')) baseValue = 10000;
            else if (model.includes('15')) baseValue = 8500;
            else if (model.includes('14 Pro Max')) baseValue = 9000;
            else if (model.includes('14 Pro')) baseValue = 8000;
            else if (model.includes('14')) baseValue = 6800;
            else if (model.includes('13 Pro Max')) baseValue = 7500;
            else if (model.includes('13 Pro')) baseValue = 6800;
            else if (model.includes('13')) baseValue = 5500;
            else if (model.includes('12 Pro Max')) baseValue = 6200;
            else if (model.includes('12 Pro')) baseValue = 5500;
            else if (model.includes('12')) baseValue = 4800;
            else if (model.includes('11 Pro Max')) baseValue = 5000;
            else if (model.includes('11')) baseValue = 3800;
            else if (model.includes('XR')) baseValue = 2800;
            else if (model.includes('8 Plus')) baseValue = 2000;
            else baseValue = 1500; // Base old iPhone

            // 2. Storage multiplier adjustment
            if (storage === '256GB') baseValue += 600;
            else if (storage === '512GB') baseValue += 1300;
            else if (storage === '1TB') baseValue += 2200;

            // 3. Condition factor adjustment
            let conditionFactor = 1.0;
            if (condition === 'Flawless') conditionFactor = 1.1;
            else if (condition === 'Good') conditionFactor = 1.0;
            else if (condition === 'Minor Scratches') conditionFactor = 0.85;
            else if (condition === 'Cracked Screen') conditionFactor = 0.5;

            // 4. Battery health factor adjustment
            let batteryFactor = 1.0;
            if (battery >= 95) batteryFactor = 1.05;
            else if (battery >= 90) batteryFactor = 1.0;
            else if (battery >= 85) batteryFactor = 0.92;
            else if (battery >= 80) batteryFactor = 0.85;
            else batteryFactor = 0.7; // Needs replacement

            // Computation
            const finalEstimate = Math.round(baseValue * conditionFactor * batteryFactor);
            
            // Format currency
            const formatter = new Intl.NumberFormat('en-ZA', {
                style: 'currency',
                currency: 'ZAR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });

            if (estimateText) {
                // Return range
                const lowRange = Math.round(finalEstimate * 0.95);
                const highRange = Math.round(finalEstimate * 1.05);
                estimateText.textContent = formatter.format(lowRange) + " - " + formatter.format(highRange);
            }
        }
        
        // Run once on load
        calculateTradeEstimate();
    }

    // 7. Client Side Shop Page Filtering (Optional Helper if AJAX is slow, but we'll use server-side forms)
    // Here we can support simple instant price slider outputs
    const priceSlider = document.getElementById('price_range');
    const priceDisplay = document.getElementById('price_range_val');
    if (priceSlider && priceDisplay) {
        priceSlider.addEventListener('input', function() {
            priceDisplay.textContent = 'R ' + parseInt(this.value).toLocaleString('en-ZA');
        });
    }
});
