<?php
/**
 * Application Configuration
 */

// Define application paths
define('ROOT_PATH', dirname(dirname(dirname(__FILE__))) . '/');
define('APP_PATH', ROOT_PATH . 'app/');
define('VIEWS_PATH', APP_PATH . 'views/');
define('MODELS_PATH', APP_PATH . 'models/');
define('CONTROLLERS_PATH', APP_PATH . 'controllers/');
define('ROUTER_PATH', APP_PATH . 'router/');

// Define base URL
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$script = dirname($_SERVER['SCRIPT_NAME']);
if ($script === '\\') {
    $script = '/';
}
define('BASE_URL', $protocol . '://' . $host . $script . '/');

// Site information
define('SITE_NAME', 'Capital AM');
define('SITE_TITLE', 'Capital AM - Asset Management & Financial Services');
define('SITE_DOMAIN', 'capitalam.vn');

// Environment
define('ENVIRONMENT', 'development'); // development, production
define('DEBUG', ENVIRONMENT === 'development');

// Error reporting
if (DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
}

// Set timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Session configuration
ini_set('session.name', 'CAPITALAMSESSID');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

?>
