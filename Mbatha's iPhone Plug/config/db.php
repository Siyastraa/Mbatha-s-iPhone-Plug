<?php
// Mbatha's iPhone Plug - Database Connection Configurator
// Dual-Mode: MySQL (for cPanel Hosting) & SQLite (for immediate local execution)

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Database Mode Configuration
// Options: 'mysql' or 'sqlite'. If set to 'auto', it tries MySQL first, and falls back to SQLite on failure.
define('DB_MODE', 'auto'); 

// MySQL Credentials (Hostinger, Afrihost, Domains.co.za)
define('MYSQL_HOST', 'localhost');
define('MYSQL_USER', ''); // Enter MySQL Username
define('MYSQL_PASS', ''); // Enter MySQL Password
define('MYSQL_NAME', 'mbathas_iphone_plug'); // Enter MySQL Database Name

// SQLite Configuration
define('SQLITE_FILE', __DIR__ . '/../database/iphones.sqlite');
define('SQL_SEED_FILE', __DIR__ . '/../database/iphones.sql');

$pdo = null;

try {
    if (DB_MODE === 'mysql' || (DB_MODE === 'auto' && !empty(MYSQL_USER))) {
        // Try connecting via MySQL
        $dsn = "mysql:host=" . MYSQL_HOST . ";dbname=" . MYSQL_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $pdo = new PDO($dsn, MYSQL_USER, MYSQL_PASS, $options);
        $db_type_active = 'MySQL';
    } else {
        // Fallback or force SQLite
        throw new Exception("MySQL not configured. Redirecting to SQLite.");
    }
} catch (Exception $e) {
    // SQLite Fallback
    try {
        $dbDir = dirname(SQLITE_FILE);
        if (!is_dir($dbDir)) {
            mkdir($dbDir, 0755, true);
        }
        
        $sqlite_new = !file_exists(SQLITE_FILE);
        
        $pdo = new PDO("sqlite:" . SQLITE_FILE);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $db_type_active = 'SQLite';
        
        // If it's a new SQLite database, seed it from our SQL file
        if ($sqlite_new && file_exists(SQL_SEED_FILE)) {
            $sql = file_get_contents(SQL_SEED_FILE);
            
            // SQLite does not support AUTO_INCREMENT or CHECK constraints in the same way, 
            // but it accepts standard SQL statements. We must clean up MySQL specific syntaxes
            // like AUTO_INCREMENT, TIMESTAMP DEFAULT CURRENT_TIMESTAMP, check constraints, etc.
            // A clean way is to split by ';' and execute statements, converting AUTO_INCREMENT to AUTOINCREMENT (if INTEGER PRIMARY KEY)
            
            // Perform basic translations for SQLite compatibility
            $sql = str_ireplace('INT AUTO_INCREMENT PRIMARY KEY', 'INTEGER PRIMARY KEY AUTOINCREMENT', $sql);
            $sql = str_ireplace('INTEGER AUTO_INCREMENT PRIMARY KEY', 'INTEGER PRIMARY KEY AUTOINCREMENT', $sql);
            $sql = str_ireplace('DECIMAL(10,2)', 'REAL', $sql);
            $sql = str_ireplace('TIMESTAMP DEFAULT CURRENT_TIMESTAMP', 'DATETIME DEFAULT CURRENT_TIMESTAMP', $sql);
            $sql = preg_replace('/CHECK\s*\(.*?\)/i', '', $sql); // Remove MySQL check constraints
            $sql = preg_replace('/FOREIGN KEY.*?\n/i', '', $sql); // Simple remove of foreign keys to avoid order issues in SQLite
            $sql = str_replace('ENGINE=InnoDB', '', $sql);
            
            // Execute statements individually
            $statements = explode(';', $sql);
            foreach ($statements as $statement) {
                $statement = trim($statement);
                if (!empty($statement)) {
                    $pdo->exec($statement);
                }
            }
        }
    } catch (PDOException $sqlite_error) {
        die("Critical Database Connection Failure: " . $sqlite_error->getMessage());
    }
}

// Global variable indicating current DB type
define('ACTIVE_DB_TYPE', $db_type_active);
?>
