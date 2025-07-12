<?php
// Database configuration
define('DB_HOST', '192.168.33.10');
define('DB_NAME', 'sns_db');
define('DB_USER', 'sns_user');
define('DB_PASS', 'sns_pass');
define('DB_CHARSET', 'utf8mb4');

// Site configuration
define('SITE_NAME', 'Simple SNS');
define('SITE_URL', 'http://localhost:8080');

// Security settings
define('SESSION_LIFETIME', 3600); // 1 hour

// Database connection function
function getDbConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        return $pdo;
    } catch (PDOException $e) {
        error_log("Database connection failed: " . $e->getMessage());
        throw new Exception("データベース接続に失敗しました。");
    }
}

// CSRF token functions
function generateCsrfToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// XSS protection
function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
?>
