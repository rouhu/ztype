<?php
/**
 * ZType Game Backend - Shared Configuration
 * This file contains database connection and utility functions
 */

// Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'ztype_game');
define('DB_USER', 'your_db_user');
define('DB_PASS', 'your_db_password');
define('SCREENSHOT_DIR', __DIR__ . '/../screenshots/');
define('MAX_STATS_PER_USER', 30);
define('STATS_EXCERPT_LIMIT', 100);

// Database connection
function getDB() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Database connection failed']);
            exit();
        }
    }
    
    return $pdo;
}

// Get client IP address
function getClientIP() {
    $ip = $_SERVER['REMOTE_ADDR'];
    
    // Check for proxy headers
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    }
    
    return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '0.0.0.0';
}

// Generate session ID for tracking users
function getSessionID() {
    if (isset($_COOKIE['ztype_session'])) {
        return $_COOKIE['ztype_session'];
    }
    
    $sessionId = bin2hex(random_bytes(16));
    setcookie('ztype_session', $sessionId, time() + (86400 * 365), '/');
    return $sessionId;
}
