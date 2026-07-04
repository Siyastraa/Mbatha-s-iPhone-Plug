# Mbatha's iPhone Plug

A luxury pre-owned Apple devices e-commerce platform built for high-end retail businesses in South Africa. Designed with a dark obsidian and metallic gold theme inspired by Apple, BMW, and Tesla aesthetics.

## Features

- **Storefront**: Dynamic product filters (models, capacity, battery health, price budget range, conditions grade), full-text search engine, dynamic product page with WhatsApp Order and secure cart checkout integration.
- **Accessories Section**: Categorized filters for AirPods, Apple Watches, cases, plugs, and screen protectors.
- **Trade-In Valuation**: An interactive quotation calculator giving estimates in real-time. Supports photo submissions and triggers WhatsApp drop-off bookings.
- **Customer Accounts**: Registered profiles, order fulfillment histories, tax invoices, and verification review submissions.
- **EFT Payment Flow**: Form checkout logs pending orders, updates product stock, issues receipts with bank transfer (EFT) routing codes, and handles proof of payment submissions.
- **Admin Panel & Analytics**: Complete inventory CRUD management, order updates, quote reviews, customer registers, and dynamic visual graphs powering sales insights.
- **Dynamic Image Engine**: Auto-checks local filesystem. If product images are missing, it dynamically generates vector SVG diagrams in real-time, preventing broken visual layouts.

---

## Technical Architecture

- **Frontend**: PHP templates generating semantic HTML5, stylized with **Bootstrap 5** and custom CSS. Icons via **Font Awesome**, interactions/animations using **GSAP** and **AOS**.
- **Backend**: **PHP 8.x** with secure session tokens, sanitized forms, and strict input filters.
- **Database**: **MySQL** (Production) & **SQLite** (Development/Local Fallback) using a dynamic connection manager.

### Dynamic Database Configuration (`config/db.php`)
The project features a **dual-database connector**:
1. If MySQL is configured, the site will use it.
2. If MySQL credentials are empty or the server connection fails, it **automatically falls back to SQLite** (`database/iphones.sqlite`).
3. If the SQLite database is missing, the connector will read `database/iphones.sql` and **auto-initialize/seed itself instantly** with sample iPhones and accessories.

---

## Getting Started (Local Development)

### Prerequisites
- Install [XAMPP](https://www.apachefriends.org/) (includes PHP and Apache).

### Installation Steps
1. Clone or copy the project folder to your local server directory:
   - For XAMPP: `C:/xampp/htdocs/Mbathas-iPhone-Plug/`
2. Start the **Apache** module from the XAMPP Control Panel.
3. Open your browser and navigate to:
   `http://localhost/Mbathas-iPhone-Plug/`
4. The database is initialized and seeded **automatically** using the SQLite fallback. No SQL imports are required!

---

## Production Deployment (Hostinger, Afrihost, Domains.co.za)

To host on a standard cPanel server:
1. Upload the files to your server's `public_html` directory.
2. Create a new MySQL Database and Database User via cPanel.
3. Import the `database/iphones.sql` file into your database using phpMyAdmin.
4. Edit the file [db.php](file:///c:/Users/siyab/OneDrive%20-%20ADvTECH%20Ltd/Desktop/Mbatha's%20iPhone%20Plug/config/db.php) and input your credentials:
   ```php
   define('MYSQL_USER', 'your_mysql_username');
   define('MYSQL_PASS', 'your_mysql_password');
   define('MYSQL_NAME', 'your_database_name');
   ```

---

## Credentials (Testing & Evaluation)

### Admin Panel
- **URL**: `http://localhost/Mbathas-iPhone-Plug/admin/`
- **Username**: `admin`
- **Password**: `admin123`

### Sample Customer Account (Or register a new one)
- **Email**: `lerato@gmail.com` (Register via page to log in)
- **Password**: `123456`
