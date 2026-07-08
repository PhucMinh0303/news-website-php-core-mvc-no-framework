<?php
// Định nghĩa hằng số BASE_PATH nếu chưa được định nghĩa
// Points to project root directory
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(dirname(__DIR__)));
}

/**
 * Hàm đọc file .env và parse thành mảng
 * @param string $filePath Đường dẫn đến file .env
 * @return array Mảng các cặp key-value từ file .env
 */
function loadEnvFile($filePath)
{
    $variables = [];

    if (!file_exists($filePath)) {
        die("File .env không tồn tại: " . $filePath);
    }

    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        // Bỏ qua comment (dòng bắt đầu bằng #)
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        // Phân tích cú pháp KEY=value
        $parts = explode('=', $line, 2);
        if (count($parts) === 2) {
            $key = trim($parts[0]);
            $value = trim($parts[1]);

            // Xóa dấu ngoặc kép nếu có
            $value = trim($value, '"\'');

            $variables[$key] = $value;
        }
    }

    return $variables;
}

// Đường dẫn đến file .env tại root của project
$envFile = BASE_PATH . '/app/config/.env';


// Load cấu hình từ file .env
$env = loadEnvFile($envFile);

// Validate required database configuration keys
$requiredKeys = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS', 'DB_CHARSET'];
foreach ($requiredKeys as $key) {
    if (!isset($env[$key])) {
        die("Missing required environment variable: " . $key . " in .env file");
    }
}

// Cấu hình kết nối database
define('DB_HOST', $env['DB_HOST']);
define('DB_NAME', $env['DB_NAME']); // Tên database trong phpMyAdmin
define('DB_USER', $env['DB_USER']);               // Username MySQL
define('DB_PASS', $env['DB_PASS']);                   // Password MySQL
define('DB_CHARSET', $env['DB_CHARSET']);

function getDatabaseConfig()
{
    // Đường dẫn đến file .env tại root của project
    $envFile = BASE_PATH . '/app/config/.env';

    
    // Load cấu hình từ file .env
    $env = loadEnvFile($envFile);
    
    // Validate required database configuration keys
    $requiredKeys = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS', 'DB_CHARSET'];
    foreach ($requiredKeys as $key) {
        if (!isset($env[$key])) {
            die("Missing required environment variable: " . $key . " in .env file");
        }
    }
    
    // Trả về mảng cấu hình
    return [
        'DB_HOST' => $env['DB_HOST'],
        'DB_NAME' => $env['DB_NAME'],
        'DB_USER' => $env['DB_USER'],
        'DB_PASS' => $env['DB_PASS'],
        'DB_CHARSET' => $env['DB_CHARSET']
    ];
}