<?php
/**
 * Class Config - Quản lý cấu hình từ file .env
 * 
 * Cách sử dụng:
 * - Lấy giá trị: Config::get('DB_HOST', 'default_value')
 * - Cập nhật: Config::update(['DB_HOST' => 'localhost:3307'])
 * - Lấy tất cả DB config: Config::getDatabaseConfig()
 */
class Config
{
    private static $config = [];
    private static $envFile = null;
    private static $loaded = false;
    private static $envDir = null;

    /**
     * Khởi tạo và load file .env
     */
    public static function init()
    {
        if (self::$loaded) {
            return;
        }

        // Xác định thư mục gốc (root của project)
        self::$envDir = self::getRootPath();
        self::$envFile = self::$envDir . '.env';

        // Tạo file .env nếu chưa tồn tại
        if (!file_exists(self::$envFile)) {
            self::createDefaultEnvFile();
        }

        // Load file .env
        self::loadEnvFile();

        // Định nghĩa các hằng số database
        self::defineDatabaseConstants();

        self::$loaded = true;
    }

    /**
     * Lấy đường dẫn thư mục gốc của project
     * @return string
     */
    private static function getRootPath()
    {
        // Giả sử Config.php nằm trong thư mục core/
        // Thư mục core nằm trong thư mục gốc
        $currentDir = dirname(__FILE__);
        
        // Nếu core nằm trong thư mục gốc
        if (basename($currentDir) === 'core') {
            return dirname($currentDir) . '/';
        }
        
        // Tìm kiếm thư mục gốc (có chứa thư mục app, core, config)
        $dir = $currentDir;
        $maxDepth = 10;
        
        for ($i = 0; $i < $maxDepth; $i++) {
            if (file_exists($dir . '/app') && file_exists($dir . '/core')) {
                return $dir . '/';
            }
            $parent = dirname($dir);
            if ($parent === $dir) {
                break;
            }
            $dir = $parent;
        }
        
        // Fallback: giả sử core nằm trong thư mục gốc
        return dirname($currentDir) . '/';
    }

    /**
     * Tạo file .env mặc định
     */
    private static function createDefaultEnvFile()
    {
        $defaultContent = "# ===========================================\n";
        $defaultContent .= "# Application Configuration\n";
        $defaultContent .= "# ===========================================\n\n";
        
        $defaultContent .= "# Database Configuration\n";
        $defaultContent .= "# -------------------------------------------\n";
        $defaultContent .= "DB_HOST=localhost:3306\n";
        $defaultContent .= "DB_NAME=quanlytintuc\n";
        $defaultContent .= "DB_USER=root\n";
        $defaultContent .= "DB_PASS=\n";
        $defaultContent .= "DB_CHARSET=utf8mb4\n\n";
        
        $defaultContent .= "# Application Settings\n";
        $defaultContent .= "# -------------------------------------------\n";
        $defaultContent .= "ENVIRONMENT=development\n";
        $defaultContent .= "DEBUG=true\n\n";
        
        $defaultContent .= "# Site Information\n";
        $defaultContent .= "# -------------------------------------------\n";
        $defaultContent .= "SITE_NAME=Capital AM\n";
        $defaultContent .= "SITE_DOMAIN=capitalam.vn\n\n";
        
        $defaultContent .= "# ===========================================\n";
        $defaultContent .= "# Generated automatically on: " . date('Y-m-d H:i:s') . "\n";
        $defaultContent .= "# ===========================================\n";
        
        file_put_contents(self::$envFile, $defaultContent);
    }

    /**
     * Load file .env
     */
    private static function loadEnvFile()
    {
        if (!file_exists(self::$envFile)) {
            return;
        }

        $lines = file(self::$envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            $line = trim($line);
            
            // Bỏ qua comment
            if (strpos($line, '#') === 0) {
                continue;
            }

            // Phân tích key=value
            $parts = explode('=', $line, 2);
            if (count($parts) === 2) {
                $key = trim($parts[0]);
                $value = trim($parts[1]);
                
                // Xóa quotes nếu có (cả single và double)
                $value = trim($value, "\"'");
                
                // Xử lý boolean values
                if (strtolower($value) === 'true') {
                    $value = true;
                } elseif (strtolower($value) === 'false') {
                    $value = false;
                } elseif (is_numeric($value)) {
                    $value = $value;
                }
                
                self::$config[$key] = $value;
                
                // Set environment variable
                putenv("$key=$value");
                $_ENV[$key] = $value;
            }
        }
    }

    /**
     * Định nghĩa các hằng số database
     */
    private static function defineDatabaseConstants()
    {
        $dbConfig = self::getDatabaseConfig();
        
        if (!defined('DB_HOST')) {
            define('DB_HOST', $dbConfig['DB_HOST']);
        }
        if (!defined('DB_NAME')) {
            define('DB_NAME', $dbConfig['DB_NAME']);
        }
        if (!defined('DB_USER')) {
            define('DB_USER', $dbConfig['DB_USER']);
        }
        if (!defined('DB_PASS')) {
            define('DB_PASS', $dbConfig['DB_PASS']);
        }
        if (!defined('DB_CHARSET')) {
            define('DB_CHARSET', $dbConfig['DB_CHARSET']);
        }
    }

    /**
     * Lấy giá trị cấu hình
     * @param string $key Tên key cần lấy
     * @param mixed $default Giá trị mặc định
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        if (!self::$loaded) {
            self::init();
        }
        
        return self::$config[$key] ?? $default;
    }

    /**
     * Cập nhật giá trị cấu hình trong file .env
     * @param array $config Mảng cấu hình cần cập nhật
     * @return bool
     */
    public static function update($config)
    {
        if (!self::$loaded) {
            self::init();
        }

        if (!self::isWritable()) {
            return false;
        }

        // Cập nhật mảng config
        foreach ($config as $key => $value) {
            self::$config[$key] = $value;
            putenv("$key=$value");
            $_ENV[$key] = $value;
        }

        // Đọc lại toàn bộ file .env hiện tại
        if (!file_exists(self::$envFile)) {
            self::createDefaultEnvFile();
        }
        
        $lines = file(self::$envFile, FILE_IGNORE_NEW_LINES);
        $updatedLines = [];
        $keysUpdated = [];
        
        // Duyệt và cập nhật các dòng hiện có
        foreach ($lines as $line) {
            $originalLine = $line;
            $line = trim($line);
            
            // Bỏ qua dòng trống
            if (empty($line)) {
                $updatedLines[] = $originalLine;
                continue;
            }
            
            // Giữ nguyên comment
            if (strpos($line, '#') === 0) {
                $updatedLines[] = $originalLine;
                continue;
            }
            
            // Phân tích key=value
            $parts = explode('=', $line, 2);
            if (count($parts) === 2) {
                $key = trim($parts[0]);
                
                // Nếu key cần cập nhật
                if (isset($config[$key])) {
                    $value = $config[$key];
                    // Xử lý boolean
                    if (is_bool($value)) {
                        $value = $value ? 'true' : 'false';
                    }
                    $updatedLines[] = "$key=$value";
                    $keysUpdated[$key] = true;
                } else {
                    $updatedLines[] = $originalLine;
                }
            } else {
                $updatedLines[] = $originalLine;
            }
        }
        
        // Thêm các key mới chưa có trong file
        foreach ($config as $key => $value) {
            if (!isset($keysUpdated[$key])) {
                if (is_bool($value)) {
                    $value = $value ? 'true' : 'false';
                }
                $updatedLines[] = "$key=$value";
            }
        }
        
        // Ghi lại file
        $content = implode("\n", $updatedLines);
        $result = file_put_contents(self::$envFile, $content);
        
        if ($result !== false) {
            // Cập nhật lại hằng số database
            self::defineDatabaseConstants();
            return true;
        }
        
        return false;
    }

    /**
     * Lấy tất cả cấu hình database
     * @return array
     */
    public static function getDatabaseConfig()
    {
        if (!self::$loaded) {
            self::init();
        }
        
        return [
            'DB_HOST' => self::get('DB_HOST', 'localhost:3306'),
            'DB_NAME' => self::get('DB_NAME', 'quanlytintuc'),
            'DB_USER' => self::get('DB_USER', 'root'),
            'DB_PASS' => self::get('DB_PASS', ''),
            'DB_CHARSET' => self::get('DB_CHARSET', 'utf8mb4')
        ];
    }

    /**
     * Kiểm tra file .env có writable không
     * @return bool
     */
    public static function isWritable()
    {
        if (!self::$envFile) {
            self::init();
        }
        
        // Kiểm tra thư mục chứa file
        $dir = dirname(self::$envFile);
        if (!is_writable($dir)) {
            return false;
        }
        
        // Kiểm tra file (nếu tồn tại)
        if (file_exists(self::$envFile)) {
            return is_writable(self::$envFile);
        }
        
        return true;
    }

    /**
     * Lấy đường dẫn file .env
     * @return string|null
     */
    public static function getEnvFilePath()
    {
        if (!self::$loaded) {
            self::init();
        }
        return self::$envFile;
    }

    /**
     * Tải lại cấu hình từ file .env
     */
    public static function reload()
    {
        self::$loaded = false;
        self::$config = [];
        self::init();
    }
}

// Tự động khởi tạo Config khi file được include
Config::init();
?>