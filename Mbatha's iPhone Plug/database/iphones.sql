-- Mbatha's iPhone Plug Database Schema
-- Compatible with MySQL and SQLite (via mapping layer or strict syntax)

CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    type VARCHAR(20) NOT NULL DEFAULT 'iphone', -- 'iphone', 'accessory'
    category VARCHAR(50) NOT NULL DEFAULT 'Phone', -- 'Phone', 'Case', 'Charger', 'Powerbank', 'AirPods', 'Watch', 'Screen Protector'
    model VARCHAR(50),
    price DECIMAL(10,2) NOT NULL,
    original_price DECIMAL(10,2) DEFAULT NULL,
    storage VARCHAR(20), -- e.g., '128GB', '256GB'
    color VARCHAR(30), -- e.g., 'Gold', 'Space Grey', 'Midnight'
    battery_health INT, -- e.g., 88, 92, 100
    grade VARCHAR(20), -- 'New', 'Like New', 'Excellent', 'Very Good'
    warranty VARCHAR(50) DEFAULT '6-Month Warranty',
    stock INT DEFAULT 1,
    description TEXT,
    is_featured INT DEFAULT 0,
    is_flash_sale INT DEFAULT 0,
    image_url VARCHAR(255) NOT NULL,
    images_gallery TEXT, -- JSON array of extra images or comma separated paths
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    customer_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'Pending', -- 'Pending', 'Paid', 'Packed', 'Shipped', 'Delivered', 'Cancelled'
    order_total DECIMAL(10,2) NOT NULL,
    coupon_code VARCHAR(20) DEFAULT NULL,
    discount_amount DECIMAL(10,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT NULL,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    name VARCHAR(100) NOT NULL,
    comment TEXT NOT NULL,
    is_verified INT DEFAULT 1,
    photo_url VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS trade_ins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    phone_model VARCHAR(100) NOT NULL,
    storage VARCHAR(20) NOT NULL,
    battery_health INT NOT NULL,
    condition_grade VARCHAR(50) NOT NULL, -- 'Flawless', 'Good', 'Minor Scratches', 'Cracked Screen'
    photo_url VARCHAR(255) DEFAULT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'Pending', -- 'Pending', 'Quoted', 'Declined', 'Completed'
    quotation_amount DECIMAL(10,2) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS coupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) NOT NULL UNIQUE,
    type VARCHAR(20) NOT NULL, -- 'percentage', 'flat'
    value DECIMAL(10,2) NOT NULL,
    active INT DEFAULT 1,
    expiry_date DATE NOT NULL
);

-- Seed Admin User (Username: admin, Password: admin123)
-- Hash generated via password_hash('admin123', PASSWORD_BCRYPT)
INSERT INTO admins (username, password_hash, email) VALUES (
    'admin',
    '$2y$10$q.tqYwG51oK5bLgG4X5QBe9q.iF50Fw66kIewy3WqEeqPqJb8z2fC',
    'admin@mbathaphoneplug.co.za'
);

-- Seed Coupons
INSERT INTO coupons (code, type, value, active, expiry_date) VALUES 
('PLUG10', 'percentage', 10.00, 1, '2027-12-31'),
('WELCOME500', 'flat', 500.00, 1, '2027-12-31');

-- Seed Featured Pre-Owned iPhones (matching pricing tiers in the brochure)
INSERT INTO products (name, type, category, model, price, original_price, storage, color, battery_health, grade, warranty, stock, description, is_featured, is_flash_sale, image_url) VALUES
('iPhone 16 Pro Max', 'iphone', 'Phone', 'iPhone 16', 14500.00, 16000.00, '256GB', 'Desert Titanium', 100, 'Like New', '12-Month Warranty', 2, 'Stunning iPhone 16 Pro Max with the new Desert Titanium finish. Captures pristine performance with A18 Pro chip. Battery health is at 100% capacity.', 1, 0, 'assets/images/products/iphone16_desert.jpg'),
('iPhone 15 Pro', 'iphone', 'Phone', 'iPhone 15', 10999.00, 12500.00, '128GB', 'Natural Titanium', 94, 'Excellent', '6-Month Warranty', 3, 'iPhone 15 Pro in natural titanium. Premium Grade A condition, tested thoroughly. Features a dynamic island, 48MP main camera, and seamless USB-C integration.', 1, 0, 'assets/images/products/iphone15_natural.jpg'),
('iPhone 14 Pro Max', 'iphone', 'Phone', 'iPhone 14', 9999.00, 11200.00, '256GB', 'Deep Purple', 89, 'Excellent', '6-Month Warranty', 2, 'Luxury Deep Purple iPhone 14 Pro Max. Unmatched screen presence, outstanding 48MP Pro camera system, stable battery capacity at 89% health.', 1, 1, 'assets/images/products/iphone14_purple.jpg'),
('iPhone 13 Pro Max', 'iphone', 'Phone', 'iPhone 13', 8200.00, 9000.00, '128GB', 'Sierra Blue', 87, 'Excellent', '6-Month Warranty', 1, 'Immaculate Sierra Blue iPhone 13 Pro Max. Cinematic mode, ProMotion 120Hz display, stellar battery longevity. Great value.', 1, 0, 'assets/images/products/iphone13_sierra.jpg'),
('iPhone 12 Pro Max', 'iphone', 'Phone', 'iPhone 12', 7400.00, 7800.00, '256GB', 'Pacific Blue', 86, 'Very Good', '6-Month Warranty', 2, 'Elegant Pacific Blue design, fully checked and tested. Screen is flawless, minimal casing marks. Premium 6-month warranty coverage.', 0, 0, 'assets/images/products/iphone12_pacific.jpg'),
('iPhone 11 Pro Max', 'iphone', 'Phone', 'iPhone 11', 6200.00, 6700.00, '256GB', 'Midnight Green', 85, 'Very Good', '6-Month Warranty', 1, 'Classic Midnight Green iPhone 11 Pro Max. Triple lens system, supreme battery value, complete operating test pass.', 0, 0, 'assets/images/products/iphone11_green.jpg'),
('iPhone XR', 'iphone', 'Phone', 'iPhone XR', 4500.00, 6000.00, '128GB', 'Black', 84, 'Very Good', '6-Month Warranty', 4, 'Reliable, powerful budget option. Liquid Retina display, great battery lifespan. 100% genuine Apple components.', 0, 0, 'assets/images/products/iphonexr_black.jpg'),
('iPhone 8 Plus', 'iphone', 'Phone', 'iPhone 8', 2900.00, 3500.00, '64GB', 'Gold', 83, 'Very Good', '6-Month Warranty', 2, 'Gold design, large display with home button and Touch ID. Fully certified and functioning correctly.', 0, 0, 'assets/images/products/iphone8plus_gold.jpg'),
('iPhone 14', 'iphone', 'Phone', 'iPhone 14', 8500.00, 10000.00, '128GB', 'Starlight', 91, 'Like New', '6-Month Warranty', 2, 'Starlight white iPhone 14. Excellent condition, minimal use, super clean screen and camera lenses. Original packaging.', 0, 1, 'assets/images/products/iphone14_starlight.jpg'),
('iPhone 13', 'iphone', 'Phone', 'iPhone 13', 6800.00, 8000.00, '128GB', 'Pink', 88, 'Excellent', '6-Month Warranty', 3, 'Charming Pink iPhone 13. Dual-lens system, strong battery performance. Perfect for everyday performance and value.', 0, 0, 'assets/images/products/iphone13_pink.jpg'),
('iPhone 12', 'iphone', 'Phone', 'iPhone 12', 6200.00, 7500.00, '128GB', 'White', 85, 'Very Good', '6-Month Warranty', 2, 'Premium white finish, super light design, 5G supported. Screen fully calibrated and checked.', 0, 0, 'assets/images/products/iphone12_white.jpg'),
('iPhone 11', 'iphone', 'Phone', 'iPhone 11', 4999.00, 6200.00, '128GB', 'Red', 84, 'Very Good', '6-Month Warranty', 5, 'Special Product Red edition. Completely checked and certified original parts. Excellent entry-level Apple phone.', 0, 0, 'assets/images/products/iphone11_red.jpg');

-- Seed Premium Apple Accessories (matching category requirements)
INSERT INTO products (name, type, category, model, price, original_price, storage, color, battery_health, grade, warranty, stock, description, is_featured, is_flash_sale, image_url) VALUES
('AirPods Pro (2nd Generation)', 'accessory', 'AirPods', NULL, 3499.00, 3999.00, NULL, 'White', NULL, 'New', '12-Month Warranty', 10, 'Premium high-fidelity sound, Active Noise Cancellation, and Adaptive Audio. Sealed original box.', 1, 0, 'assets/images/products/airpods_pro.jpg'),
('Apple Watch Series 9 GPS', 'accessory', 'Watch', 'Watch 9', 7999.00, 8500.00, NULL, 'Midnight Aluminium', NULL, 'Like New', '6-Month Warranty', 3, 'Stunning Series 9 with Midnight sports band. Battery capacity at 100%, includes dynamic gesture control.', 1, 0, 'assets/images/products/watch9_midnight.jpg'),
('iPhone 15 Pro MagSafe Silicon Case', 'accessory', 'Case', 'iPhone 15', 350.00, 500.00, NULL, 'Midnight Black', NULL, 'New', 'No Warranty', 20, 'Premium silicon cases with tactile clicks and integrated MagSafe alignment. Protects against bumps and scratches.', 0, 0, 'assets/images/products/case_magsafe.jpg'),
('20W USB-C Power Adapter Plug', 'accessory', 'Charger', NULL, 290.00, 450.00, NULL, 'White', NULL, 'New', '6-Month Warranty', 50, 'Original fast charging wall plug adapter. Fully compatible with all Type-C charging cords.', 0, 0, 'assets/images/products/charger_plug.jpg'),
('MagSafe Power Bank (10,000mAh)', 'accessory', 'Powerbank', NULL, 599.00, 850.00, NULL, 'White', NULL, 'New', '6-Month Warranty', 15, 'Magnetic snap-on power bank with fast charging delivery. Compact and clean design for luxury transport.', 0, 1, 'assets/images/products/powerbank.jpg'),
('9H Tempered Glass Screen Protector', 'accessory', 'Screen Protector', 'iPhone 15', 99.00, 200.00, NULL, 'Clear', NULL, 'New', 'No Warranty', 100, 'Ultimate defensive glass protection. Oleophobic coating, easy installation tools included.', 0, 0, 'assets/images/products/screen_protector.jpg');

-- Seed Product Reviews
INSERT INTO reviews (product_id, rating, name, comment, is_verified) VALUES
(1, 5, 'Siyabonga M.', 'Amazing device! iPhone 16 Pro Max looks exactly like brand new, battery health is indeed at 100%. Mbatha is the ultimate plug!', 1),
(1, 5, 'Lerato N.', 'Best service ever, ordered on WhatsApp, was delivered within 24 hours to Johannesburg. Highly recommended.', 1),
(2, 5, 'Thabo K.', 'Beautiful iPhone 15 Pro, battery is excellent and the titanium feel is premium. Very happy user.', 1),
(3, 4, 'Naledi M.', 'Deep purple 14 Pro Max is beautiful, minor hairline scratch on the side frame as described, but otherwise perfect.', 1),
(4, 5, 'Jabu S.', 'Fast shipping, original phone components, verified battery health at 87%. Will buy again!', 1);
