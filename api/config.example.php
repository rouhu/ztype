<?php
/**
 * ZType Game Backend - Configuration Example
 * 
 * Copy this file to config.php and update with your settings
 * DO NOT commit config.php to version control
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'ztype_game');
define('DB_USER', 'your_db_user');
define('DB_PASS', 'your_db_password');
define('DB_PORT', 3306);
define('DB_CHARSET', 'utf8mb4');

// File Storage
define('SCREENSHOT_DIR', __DIR__ . '/../screenshots/');
define('MAX_SCREENSHOT_SIZE', 5 * 1024 * 1024); // 5MB
define('SCREENSHOT_RETENTION', 100); // Keep last 100 screenshots

// Session Configuration
define('SESSION_COOKIE_NAME', 'ztype_session');
define('SESSION_LIFETIME', 86400 * 365); // 1 year
define('SESSION_COOKIE_PATH', '/');
define('SESSION_COOKIE_DOMAIN', ''); // Leave empty for current domain
define('SESSION_COOKIE_SECURE', false); // Set to true for HTTPS only
define('SESSION_COOKIE_HTTPONLY', true);

// Statistics Configuration
define('MAX_STATS_PER_USER', 30);
define('STATS_EXCERPT_LIMIT', 100);

// Rate Limiting (optional - implement in index.php)
define('RATE_LIMIT_ENABLED', false);
define('RATE_LIMIT_MAX_REQUESTS', 10);
define('RATE_LIMIT_TIME_WINDOW', 60); // seconds

// Security
define('ENABLE_IP_LOGGING', true);
define('ALLOWED_ORIGINS', '*'); // Comma-separated list or '*' for all
define('FORCE_HTTPS', false); // Set to true in production

// Validation Limits
define('MAX_SCORE', 999999);
define('MAX_WAVE', 999);
define('MAX_STREAK', 9999);
define('MAX_ACCURACY', 100);

// Error Reporting
define('DEBUG_MODE', false); // Set to false in production
define('LOG_ERRORS', true);
define('ERROR_LOG_PATH', '/var/log/ztype_errors.log');

// API Configuration
define('API_VERSION', '1.0');
define('API_TIMEOUT', 30); // seconds

// Leaderboard Configuration
define('LEADERBOARD_SIZE', 100);
define('LEADERBOARD_CACHE_TIME', 300); // 5 minutes

// Maintenance Mode
define('MAINTENANCE_MODE', false);
define('MAINTENANCE_MESSAGE', 'The game backend is currently under maintenance. Please try again later.');

// Feature Flags
define('FEATURE_LEADERBOARD', true);
define('FEATURE_SCREENSHOTS', true);
define('FEATURE_USER_PROFILES', false);

// Database Connection Options
$dbOptions = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::ATTR_PERSISTENT => false,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
];
