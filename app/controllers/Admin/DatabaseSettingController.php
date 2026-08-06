<?php


class DatabaseSettingController extends Controller
{
    private $envFile;
    
    public function __construct()
    {
        parent::__construct();
        $this->envFile = getEnvFilePath();
    }
    
    /**
     * Hiển thị form quản lý database
     */
    public function index()
    {
        // Lấy config hiện tại
        $config = getDatabaseConfig();
        
        // Kiểm tra kết nối
        $testResult = Database::testConnection($config);
        
        $data = [
            'config' => $config,
            'isConnected' => $testResult['success'],
            'errorMessage' => $testResult['success'] ? '' : $testResult['error'],
            'mysqlVersion' => $this->getMysqlVersion($testResult)
        ];
        
        $this->view('admin/database/setting', $data);
    }
    
    /**
     * API: Test kết nối database
     */
    public function testConnection()
    {
        header('Content-Type: application/json');
        
        try {
            $config = [
                'DB_HOST' => trim($_POST['db_host']),
                'DB_NAME' => trim($_POST['db_name']),
                'DB_USER' => trim($_POST['db_user']),
                'DB_PASS' => $_POST['db_pass'],
                'DB_CHARSET' => trim($_POST['db_charset']) ?: 'utf8mb4'
            ];
            
            $result = Database::testConnection($config);
            
            if ($result['success']) {
                echo json_encode([
                    'success' => true,
                    'message' => '✅ Kết nối thành công!',
                    'mysql_version' => $this->getMysqlVersion($result)
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => '❌ Kết nối thất bại',
                    'error' => $result['error'],
                    'suggestion' => $this->getSuggestion($result['error'], $config)
                ]);
            }
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => '❌ Lỗi kiểm tra kết nối',
                'error' => $e->getMessage()
            ]);
        }
        exit;
    }
    
    /**
     * API: Cập nhật cấu hình database
     */
    public function updateConfig()
    {
        header('Content-Type: application/json');
        
        try {
            // Validate dữ liệu
            $config = $this->validateConfig($_POST);
            
            // Kiểm tra quyền ghi file
            $this->checkWritePermission();
            
            // Cập nhật file .env
            $this->updateEnvFile($config);
            
            // Kiểm tra kết nối với cấu hình mới
            $testResult = Database::testConnection($config);
            
            if (!$testResult['success']) {
                throw new \Exception($testResult['error']);
            }
            
            echo json_encode([
                'success' => true,
                'message' => '✅ Cập nhật cấu hình thành công!',
                'config' => $config,
                'mysql_version' => $this->getMysqlVersion($testResult),
                'reload' => true
            ]);
            
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => '❌ Cập nhật thất bại',
                'error' => $e->getMessage(),
                'suggestion' => $this->getSuggestion($e->getMessage(), $_POST)
            ]);
        }
        exit;
    }
    
    /**
     * Validate cấu hình database
     */
    private function validateConfig($data)
    {
        $config = [
            'DB_HOST' => trim($data['db_host']),
            'DB_NAME' => trim($data['db_name']),
            'DB_USER' => trim($data['db_user']),
            'DB_PASS' => $data['db_pass'],
            'DB_CHARSET' => trim($data['db_charset']) ?: 'utf8mb4'
        ];
        
        // Validate required fields
        if (empty($config['DB_HOST'])) {
            throw new \Exception('Host không được để trống');
        }
        if (empty($config['DB_NAME'])) {
            throw new \Exception('Database name không được để trống');
        }
        if (empty($config['DB_USER'])) {
            throw new \Exception('Username không được để trống');
        }
        
        // Validate format
        if (!preg_match('/^[a-zA-Z0-9\.\:\-]+$/', $config['DB_HOST'])) {
            throw new \Exception('Host không hợp lệ');
        }
        if (!preg_match('/^[a-zA-Z0-9\_\-]+$/', $config['DB_NAME'])) {
            throw new \Exception('Database name không hợp lệ');
        }
        
        return $config;
    }
    
    /**
     * Cập nhật file .env
     */
    private function updateEnvFile($config)
    {
        // Đọc file .env hiện tại
        if (!file_exists($this->envFile)) {
            throw new \Exception('File .env không tồn tại');
        }
        
        $content = file_get_contents($this->envFile);
        if ($content === false) {
            throw new \Exception('Không thể đọc file .env');
        }
        
        // Backup file .env
        $this->backupEnvFile();
        
        // Parse và cập nhật
        $lines = explode("\n", $content);
        $newLines = [];
        $foundKeys = [];
        
        foreach ($lines as $line) {
            $trimmedLine = trim($line);
            $isUpdated = false;
            
            if (empty($trimmedLine) || strpos($trimmedLine, '#') === 0) {
                $newLines[] = $line;
                continue;
            }
            
            foreach ($config as $key => $value) {
                if (strpos($trimmedLine, $key . '=') === 0) {
                    $escapedValue = $this->escapeEnvValue($value);
                    $newLines[] = $key . '=' . $escapedValue;
                    $foundKeys[$key] = true;
                    $isUpdated = true;
                    break;
                }
            }
            
            if (!$isUpdated) {
                $newLines[] = $line;
            }
        }
        
        // Thêm key chưa có
        foreach ($config as $key => $value) {
            if (!isset($foundKeys[$key])) {
                $escapedValue = $this->escapeEnvValue($value);
                $newLines[] = $key . '=' . $escapedValue;
            }
        }
        
        // Ghi file
        $newContent = implode("\n", $newLines);
        if (file_put_contents($this->envFile, $newContent) === false) {
            throw new \Exception('Không thể ghi file .env');
        }
        
        // Clear cache
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
    }
    
    /**
     * Escape giá trị cho .env
     */
    private function escapeEnvValue($value)
    {
        if (strpos($value, ' ') !== false || 
            strpos($value, '#') !== false || 
            strpos($value, '=') !== false || 
            strpos($value, '"') !== false) {
            return '"' . str_replace('"', '\\"', $value) . '"';
        }
        return $value;
    }
    
    /**
     * Backup file .env
     */
    private function backupEnvFile()
    {
        $backupDir = dirname($this->envFile) . '/backups';
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }
        
        $backupFile = $backupDir . '/.env.backup_' . date('Ymd_His');
        copy($this->envFile, $backupFile);
    }
    
    /**
     * Kiểm tra quyền ghi
     */
    private function checkWritePermission()
    {
        $envDir = dirname($this->envFile);
        
        if (!is_writable($envDir)) {
            throw new \Exception('Thư mục không có quyền ghi');
        }
        
        if (file_exists($this->envFile) && !is_writable($this->envFile)) {
            throw new \Exception('File .env không có quyền ghi');
        }
    }
    
    /**
     * Lấy phiên bản MySQL
     */
    private function getMysqlVersion($testResult)
    {
        if (!$testResult['success']) {
            return 'Không xác định';
        }
        
        try {
            $stmt = $testResult['connection']->query("SELECT VERSION() as version");
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $result['version'] ?? 'Không xác định';
        } catch (\Exception $e) {
            return 'Không xác định';
        }
    }
    
    /**
     * Đưa ra gợi ý dựa trên lỗi
     */
    private function getSuggestion($error, $config)
    {
        if (strpos($error, 'Unknown database') !== false) {
            return 'Database "' . ($config['DB_NAME'] ?? '') . '" chưa tồn tại. Vui lòng tạo database trước.';
        } elseif (strpos($error, 'Access denied') !== false) {
            return 'Sai username hoặc password. Kiểm tra lại thông tin đăng nhập.';
        } elseif (strpos($error, 'Connection refused') !== false) {
            return 'Không thể kết nối đến MySQL. Kiểm tra host và port.';
        } elseif (strpos($error, 'Permission denied') !== false) {
            return 'Không có quyền ghi file .env. Vui lòng cấp quyền ghi.';
        }
        return 'Kiểm tra lại cấu hình kết nối.';
    }
}