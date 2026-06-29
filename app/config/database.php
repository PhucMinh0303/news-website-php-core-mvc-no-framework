<?php
define('BASE_PATH', dirname(__DIR__));

/**
 * Load environment variables from .env file
 * 
 * @param string $path Path to .env file
 * @return array Environment variables
 */
function loadEnv($path)
{
    if (!file_exists($path)) {
        // Tạo file .env từ .env.example nếu chưa tồn tại
        $examplePath = dirname($path) . '/.env.example';
        if (file_exists($examplePath)) {
            copy($examplePath, $path);
        } else {
            // Tạo file .env mặc định
            $defaultEnv = "# Database Configuration\n";
            $defaultEnv .= "DB_HOST=localhost:3306\n";
            $defaultEnv .= "DB_NAME=quanlytintuc\n";
            $defaultEnv .= "DB_USER=root\n";
            $defaultEnv .= "DB_PASS=\n";
            $defaultEnv .= "DB_CHARSET=utf8mb4\n";
            file_put_contents($path, $defaultEnv);
        }
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $env = [];
    
    foreach ($lines as $line) {
        // Bỏ qua comment
        $line = trim($line);
        if (empty($line) || strpos($line, '#') === 0) {
            continue;
        }
        
        // Phân tích key=value
        $parts = explode('=', $line, 2);
        if (count($parts) === 2) {
            $key = trim($parts[0]);
            $value = trim($parts[1]);
            
            // Xóa dấu quotes nếu có
            $value = trim($value, '"\'');
            
            $env[$key] = $value;
        }
    }
    
    return $env;
}

// Load cấu hình từ .env
$envPath = __DIR__ . '/.env';
$env = loadEnv($envPath);

// Định nghĩa hằng số
define('DB_HOST', $env['DB_HOST'] ?? 'localhost:3306');
define('DB_NAME', $env['DB_NAME'] ?? 'quanlytintuc');
define('DB_USER', $env['DB_USER'] ?? 'root');
define('DB_PASS', $env['DB_PASS'] ?? '');
define('DB_CHARSET', $env['DB_CHARSET'] ?? 'utf8mb4');