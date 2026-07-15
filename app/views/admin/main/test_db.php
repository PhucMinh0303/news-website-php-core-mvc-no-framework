<?php
require_once __DIR__ . '/../../../core/Database.php';
require_once __DIR__ . '/../../../config/database.php';

// Bật hiển thị lỗi chi tiết
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Khởi tạo biến kết nối
$connection = null;
$isConnected = false;
$errorMessage = '';

// Lấy cấu hình database hiện tại từ file .env thông qua Database class
$dbConfig = getDatabaseConfig();

// Định nghĩa lại các hằng số nếu cần (để hiển thị trong HTML)
if (!defined('DB_HOST')) define('DB_HOST', $dbConfig['DB_HOST']);
if (!defined('DB_NAME')) define('DB_NAME', $dbConfig['DB_NAME']);
if (!defined('DB_USER')) define('DB_USER', $dbConfig['DB_USER']);
if (!defined('DB_PASS')) define('DB_PASS', $dbConfig['DB_PASS']);
if (!defined('DB_CHARSET')) define('DB_CHARSET', $dbConfig['DB_CHARSET']);

// Kiểm tra kết nối với cấu hình hiện tại sử dụng Database class
$testResult = Database::testConnection($dbConfig);

if ($testResult['success']) {
    $isConnected = true;
    $connection = $testResult['connection'];
    $errorMessage = '';
} else {
    $isConnected = false;
    $errorMessage = $testResult['error'];
}

/**
 * Hàm cập nhật file .env
 */
function updateEnvFile($config)
{
    $envFile = function_exists('getEnvFilePath') ? getEnvFilePath() : (BASE_PATH . '/app/config/.env');

    if (!file_exists($envFile)) {
        throw new Exception('File .env không tồn tại tại: ' . $envFile);
    }

    // Đọc nội dung file .env hiện tại
    $envContent = file_get_contents($envFile);
    if ($envContent === false) {
        throw new Exception('Không thể đọc file .env');
    }

    // Backup file .env
    $backupDir = dirname($envFile) . '/backups';
    if (!is_dir($backupDir)) {
        mkdir($backupDir, 0755, true);
    }
    $backupFile = $backupDir . '/.env.backup_' . date('Ymd_His');
    if (!copy($envFile, $backupFile)) {
        error_log("Không thể tạo backup .env: " . $backupFile);
    }

    // Parse và cập nhật file .env
    $lines = explode("\n", $envContent);
    $newLines = [];
    $foundKeys = [];

    // Duyệt qua từng dòng để cập nhật
    foreach ($lines as $line) {
        $trimmedLine = trim($line);
        $isUpdated = false;

        // Bỏ qua dòng trống và comment
        if (empty($trimmedLine) || strpos($trimmedLine, '#') === 0) {
            $newLines[] = $line;
            continue;
        }

        // Kiểm tra từng key cần cập nhật
        foreach ($config as $key => $value) {
            if (strpos($trimmedLine, $key . '=') === 0) {
                // Escape giá trị nếu cần
                $escapedValue = $value;
                if (
                    strpos($value, ' ') !== false ||
                    strpos($value, '#') !== false ||
                    strpos($value, '=') !== false ||
                    strpos($value, '"') !== false
                ) {
                    $escapedValue = '"' . str_replace('"', '\\"', $value) . '"';
                }

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

    // Thêm các key chưa có trong file .env
    foreach ($config as $key => $value) {
        if (!isset($foundKeys[$key])) {
            $escapedValue = $value;
            if (
                strpos($value, ' ') !== false ||
                strpos($value, '#') !== false ||
                strpos($value, '=') !== false ||
                strpos($value, '"') !== false
            ) {
                $escapedValue = '"' . str_replace('"', '\\"', $value) . '"';
            }
            $newLines[] = $key . '=' . $escapedValue;
        }
    }

    // Ghi lại file .env
    $newContent = implode("\n", $newLines);
    if (file_put_contents($envFile, $newContent) === false) {
        // Khôi phục từ backup nếu ghi thất bại
        if (file_exists($backupFile)) {
            copy($backupFile, $envFile);
        }
        throw new Exception('Không thể ghi file .env. Vui lòng kiểm tra quyền ghi.');
    }

    return true;
}

// Xử lý AJAX request để kiểm tra kết nối
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_action'])) {
    header('Content-Type: application/json');

    // Xử lý test connection
    if ($_POST['ajax_action'] === 'test_connection') {
        try {
            $config = [
                'DB_HOST' => trim($_POST['db_host']),
                'DB_NAME' => trim($_POST['db_name']),
                'DB_USER' => trim($_POST['db_user']),
                'DB_PASS' => $_POST['db_pass'],
                'DB_CHARSET' => trim($_POST['db_charset'])
            ];

            // Kiểm tra kết nối
            $testResult = Database::testConnection($config);

            if ($testResult['success']) {
                // Lấy thông tin MySQL version
                $mysqlVersion = 'Không xác định';
                $currentTime = date('Y-m-d H:i:s');
                try {
                    $stmt = $testResult['connection']->query("SELECT VERSION() as version, NOW() as current_time");
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    $mysqlVersion = $result['version'] ?? 'Không xác định';
                    $currentTime = $result['current_time'] ?? date('Y-m-d H:i:s');
                } catch (Exception $e) {
                    // Bỏ qua lỗi lấy thông tin
                }

                echo json_encode([
                    'success' => true,
                    'message' => '✅ Kết nối thành công!',
                    'mysql_version' => $mysqlVersion,
                    'current_time' => $currentTime
                ]);
            } else {
                $errorMsg = $testResult['error'];
                $suggestion = '';

                // Đưa ra gợi ý dựa trên lỗi
                if (strpos($errorMsg, 'Unknown database') !== false) {
                    $suggestion = 'Database "' . $config['DB_NAME'] . '" chưa tồn tại.';
                } elseif (strpos($errorMsg, 'Access denied') !== false) {
                    $suggestion = 'Sai username hoặc password.';
                } elseif (strpos($errorMsg, 'Connection refused') !== false || strpos($errorMsg, 'SQLSTATE[HY000] [2002]') !== false) {
                    $suggestion = 'Không thể kết nối đến MySQL. Kiểm tra host và port.';
                }

                echo json_encode([
                    'success' => false,
                    'message' => '❌ Kết nối thất bại',
                    'error' => $errorMsg,
                    'suggestion' => $suggestion
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => '❌ Lỗi kiểm tra kết nối',
                'error' => $e->getMessage()
            ]);
        }
        exit;
    }

    // Xử lý cập nhật cấu hình
    if ($_POST['ajax_action'] === 'update_config') {
        try {
            // Lấy dữ liệu từ form
            $config = [
                'DB_HOST' => trim($_POST['db_host']),
                'DB_NAME' => trim($_POST['db_name']),
                'DB_USER' => trim($_POST['db_user']),
                'DB_PASS' => $_POST['db_pass'],
                'DB_CHARSET' => trim($_POST['db_charset'])
            ];

            // Validate dữ liệu
            if (empty($config['DB_HOST'])) {
                throw new Exception('Host không được để trống');
            }
            if (empty($config['DB_NAME'])) {
                throw new Exception('Database name không được để trống');
            }
            if (empty($config['DB_USER'])) {
                throw new Exception('Username không được để trống');
            }
            if (empty($config['DB_CHARSET'])) {
                $config['DB_CHARSET'] = 'utf8mb4';
            }

            // Validate format
            if (!preg_match('/^[a-zA-Z0-9\.\:\-]+$/', $config['DB_HOST'])) {
                throw new Exception('Host không hợp lệ. Chỉ chấp nhận chữ, số, dấu chấm, dấu hai chấm và dấu gạch ngang');
            }
            if (!preg_match('/^[a-zA-Z0-9\_\-]+$/', $config['DB_NAME'])) {
                throw new Exception('Database name không hợp lệ. Chỉ chấp nhận chữ, số, dấu gạch dưới và dấu gạch ngang');
            }

            // Kiểm tra kết nối với cấu hình mới
            $testResult = Database::testConnection($config);

            if (!$testResult['success']) {
                throw new Exception($testResult['error']);
            }

            // Cập nhật file .env
            updateEnvFile($config);

            // Lấy thông tin MySQL
            $mysqlVersion = 'Không xác định';
            $currentTime = date('Y-m-d H:i:s');
            try {
                $stmt = $testResult['connection']->query("SELECT VERSION() as version, NOW() as current_time");
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $mysqlVersion = $result['version'] ?? 'Không xác định';
                $currentTime = $result['current_time'] ?? date('Y-m-d H:i:s');
            } catch (Exception $e) {
                // Bỏ qua lỗi
            }

            // Trả về response thành công
            echo json_encode([
                'success' => true,
                'message' => '✅ Cập nhật cấu hình thành công!',
                'config' => $config,
                'mysql_version' => $mysqlVersion,
                'current_time' => $currentTime,
                'reload' => true
            ]);
        } catch (Exception $e) {
            // Trả về response lỗi
            $errorMsg = $e->getMessage();
            $suggestion = '';

            // Đưa ra gợi ý dựa trên lỗi
            if (strpos($errorMsg, 'Unknown database') !== false) {
                $suggestion = 'Database "' . ($config['DB_NAME'] ?? '') . '" chưa tồn tại. Vui lòng tạo database trước.';
            } elseif (strpos($errorMsg, 'Access denied') !== false) {
                $suggestion = 'Sai username hoặc password. Kiểm tra lại thông tin đăng nhập.';
            } elseif (strpos($errorMsg, 'Connection refused') !== false || strpos($errorMsg, 'SQLSTATE[HY000] [2002]') !== false) {
                $suggestion = 'Không thể kết nối đến MySQL. Kiểm tra host và port.';
            } elseif (strpos($errorMsg, 'Permission denied') !== false || strpos($errorMsg, 'failed to open stream') !== false) {
                $suggestion = 'Không có quyền ghi file .env. Vui lòng cấp quyền ghi cho file .env';
            }

            echo json_encode([
                'success' => false,
                'message' => '❌ Cập nhật thất bại',
                'error' => $errorMsg,
                'suggestion' => $suggestion
            ]);
        }
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kiểm tra kết nối Database</title>
    <style>
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header {
            padding: 30px;
            text-align: center;
            color: white;
            transition: all 0.3s ease;
        }


        .header.success {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .header.error {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .testdb {
            display: grid;
            grid-template-columns: 2fr 2fr;
            gap: 10px;
        }

        .testdb-content {
            padding: 20px 20px 0 20px;
        }

        .icon {
            font-size: 60px;
            margin-bottom: 10px;
        }

        h2 {
            margin: 0;
            font-size: 28px;
        }

        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .info-item {
            padding: 8px 0;
            border-bottom: 1px solid #e0e0e0;
            font-family: monospace;
            font-size: 14px;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .label {
            font-weight: bold;
            color: #495057;
            display: inline-block;
            width: 100px;
        }

        .value {
            color: #212529;
        }

        .error-detail {
            background: #fff5f5;
            border-left: 4px solid #f5576c;
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .error-message {
            color: #d63031;
            font-weight: bold;
            margin-top: 10px;
            padding: 10px;
            background: white;
            border-radius: 5px;
            font-family: monospace;
        }

        button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            transition: transform 0.2s;
        }

        button:hover {
            transform: translateY(-2px);
        }

        button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .config-form {
            background: #f8f9fa;
            border-left: 4px solid #ff9800;
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
        }

        .config-form h3 {
            margin-top: 0;
            color: #ff9800;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #495057;
        }

        .form-group input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: monospace;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }

        .form-group input.error {
            border-color: #dc3545;
        }

        .form-group input.success {
            border-color: #28a745;
        }

        .btn-update {
            background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%);
            margin-top: 10px;
        }

        .btn-test {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            margin-top: 10px;
        }

        .update-success {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            border-left: 4px solid #28a745;
            animation: slideIn 0.5s ease;
        }

        .update-error {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            border-left: 4px solid #dc3545;
            animation: slideIn 0.5s ease;
        }

        .update-info {
            background: #d1ecf1;
            color: #0c5460;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            border-left: 4px solid #17a2b8;
            animation: slideIn 0.5s ease;
        }

        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            vertical-align: middle;
            margin-right: 10px;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            border-radius: 8px;
            color: white;
            font-weight: bold;
            z-index: 1000;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            animation: slideIn 0.5s ease;
            max-width: 400px;
        }

        .toast.success {
            background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
        }

        .toast.error {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
        }

        .toast.info {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        }

        .button-group {
            display: flex;
            gap: 10px;
        }

        .button-group button {
            flex: 1;
        }

        @media (max-width: 768px) {
            .button-group {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    <div class="testdb-container">
        <?php if ($isConnected): ?>
            <!-- TRƯỜNG HỢP 1: KẾT NỐI THÀNH CÔNG -->
            <div class="header success" id="header-status">
                <div class="icon">✅</div>
                <h2 id="status-title">KẾT NỐI THÀNH CÔNG</h2>
                <p id="status-description">Database đã được kết nối thành công!</p>
            </div>
            <div class="testdb">
                <div class="testdb-content">
                    <div class="info-box" id="connection-info">
                        <div class="info-item">
                            <span class="label">Trạng thái:</span>
                            <span class="value" style="color: #27ae60; font-weight: bold;" id="connection-status">● Đã kết nối</span>
                        </div>
                        <div class="info-item">
                            <span class="label">Host:</span>
                            <span class="value" id="display-host"><?php echo DB_HOST; ?></span>
                        </div>
                        <div class="info-item">
                            <span class="label">Database:</span>
                            <span class="value" id="display-database"><?php echo DB_NAME; ?></span>
                        </div>
                        <div class="info-item">
                            <span class="label">Username:</span>
                            <span class="value" id="display-username"><?php echo DB_USER; ?></span>
                        </div>
                    </div>

                    <?php
                    // Chuyển xử lý cho Database.php - gọi phương thức kiểm tra từ Database class
                    try {
                        $testQuery = $connection->query("SELECT VERSION() as version, NOW() as current_time");
                        $result = $testQuery->fetch(PDO::FETCH_ASSOC);
                    ?>
                        <div class="info-box" style="border-left-color: #27ae60;" id="system-info">
                            <h3 style="margin-top: 0;">📊 Thông tin hệ thống:</h3>
                            <div class="info-item">
                                <span class="label">MySQL Version:</span>
                                <span class="value" id="mysql-version"><?php echo $result['version']; ?></span>
                            </div>
                            <div class="info-item">
                                <span class="label">Thời gian:</span>
                                <span class="value" id="current-time"><?php echo $result['current_time']; ?></span>
                            </div>
                        </div>
                    <?php
                    } catch (Exception $e) {
                        // Xử lý lỗi truy vấn
                        echo "<div class='error-detail'>";
                        echo "<strong>⚠️ Lỗi truy vấn:</strong> " . $e->getMessage();
                        echo "</div>";
                    }
                    ?>


                </div>

            <?php else: ?>
                <!-- TRƯỜNG HỢP 2: KẾT NỐI THẤT BẠI - HIỂN THỊ THÔNG TIN TỪ DATABASE.PHP -->
                <div class="header error" id="header-status">
                    <div class="icon">❌</div>
                    <h2 id="status-title">KẾT NỐI THẤT BẠI</h2>
                    <p id="status-description">Không thể kết nối đến database</p>
                </div>
                <div class="testdb">
                    <div class="testdb-content">
                        <!-- Hiển thị thông báo cập nhật -->
                        <div id="notification-area">
                            <?php if (isset($updateSuccess) && $updateSuccess): ?>
                                <div class="update-success" id="update-success">✅ <?php echo htmlspecialchars($updateSuccess); ?></div>
                            <?php endif; ?>

                            <?php if (isset($updateError) && $updateError): ?>
                                <div class="update-error" id="update-error">❌ <?php echo htmlspecialchars($updateError); ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Hiển thị đầy đủ thông tin từ config/database.php -->
                        <div class="info-box" id="config-info">
                            <h3 style="margin-top: 0; color: #d63031;">📋 Thông tin cấu hình (từ database.php):</h3>
                            <div class="info-item">
                                <span class="label">Host:</span>
                                <span class="value" id="display-host"><?php echo DB_HOST; ?></span>
                            </div>
                            <div class="info-item">
                                <span class="label">Database:</span>
                                <span class="value" id="display-database"><?php echo DB_NAME; ?></span>
                            </div>
                            <div class="info-item">
                                <span class="label">Username:</span>
                                <span class="value" id="display-username"><?php echo DB_USER; ?></span>
                            </div>
                            <div class="info-item">
                                <span class="label">Password:</span>
                                <span class="value" id="display-password"><?php echo DB_PASS === '' ? '(rỗng)' : str_repeat('•', strlen(DB_PASS)); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="label">Charset:</span>
                                <span class="value" id="display-charset"><?php echo DB_CHARSET; ?></span>
                            </div>
                        </div>

                        <!-- Hiển thị chi tiết lỗi -->
                        <div class="error-detail" id="error-detail">
                            <strong>🔍 Chi tiết lỗi:</strong>
                            <div class="error-message" id="error-message">
                                <?php echo isset($errorMessage) ? htmlspecialchars($errorMessage) : 'Không thể xác định lỗi'; ?>
                            </div>

                            <?php
                            // Phân tích và đưa ra gợi ý dựa trên lỗi
                            if (isset($errorMessage) && !empty($errorMessage)) {
                                echo '<br><strong>💡 Gợi ý khắc phục:</strong><br>';

                                if (strpos($errorMessage, 'Unknown database') !== false) {
                                    echo '• Database "' . DB_NAME . '" chưa tồn tại. Hãy tạo database trong phpMyAdmin.<br>';
                                    echo '• Câu lệnh tạo database: <code>CREATE DATABASE ' . DB_NAME . ';</code><br>';
                                } elseif (strpos($errorMessage, 'Access denied') !== false) {
                                    echo '• Sai tên đăng nhập hoặc mật khẩu. Kiểm tra lại DB_USER và DB_PASS.<br>';
                                    echo '• Mặc định XAMPP: user = "root", pass = "" (rỗng)<br>';
                                } elseif (strpos($errorMessage, 'Connection refused') !== false) {
                                    echo '• MySQL server chưa được khởi động. Hãy bật MySQL trong XAMPP Control Panel.<br>';
                                    echo '• Kiểm tra port: ' . DB_HOST . '<br>';
                                } elseif (strpos($errorMessage, 'No such file or directory') !== false) {
                                    echo '• Không tìm thấy socket. Thử thay localhost thành 127.0.0.1<br>';
                                } elseif (strpos($errorMessage, 'SQLSTATE[HY000] [2002]') !== false) {
                                    echo '• Không thể kết nối đến MySQL. Kiểm tra:<br>';
                                    echo '  - MySQL đã được khởi động trong XAMPP Control Panel chưa?<br>';
                                    echo '  - Port có đúng không? (mặc định: 3306 hoặc 3307)<br>';
                                    echo '  - Thử thay địa chỉ host thành "127.0.0.1" thay vì "localhost"<br>';
                                } else {
                                    echo '• Kiểm tra lại file cấu hình config/database.php<br>';
                                    echo '• Đảm bảo MySQL đang chạy đúng cách<br>';
                                    echo '• Sử dụng form bên trên để cập nhật cấu hình phù hợp<br>';
                                }

                                echo '<br><strong>📝 Ví dụ cấu hình mặc định cho XAMPP:</strong><br>';
                                echo '• Host: localhost:3306 hoặc 127.0.0.1:3306<br>';
                                echo '• Username: root (hoặc tùy chỉnh trên phpMyAdmin)<br>';
                                echo '• Password: (để trống hoặc tùy chỉnh)<br>';
                                echo '• Database: Tên database bạn đã tạo trong phpMyAdmin<br>';
                            }
                            ?>
                        </div>


                    </div>

                <?php endif; ?>
                <div class="testdb-content">
                    <!-- Form cập nhật cấu hình -->
                    <div class="config-form">
                        <h3>🔧 Cập nhật cấu hình kết nối</h3>
                        <div id="update-notification"></div>
                        <form id="config-form" method="POST" action="">
                            <div class="form-group">
                                <label for="db_host">Host:</label>
                                <input type="text" id="db_host" name="db_host" value="<?php echo htmlspecialchars(DB_HOST); ?>" placeholder="Nhập tên host (Ví dụ: localhost:3306)">
                            </div>
                            <div class="form-group">
                                <label for="db_name">Database:</label>
                                <input type="text" id="db_name" name="db_name" value="<?php echo htmlspecialchars(DB_NAME); ?>" placeholder="Nhập tên database">
                            </div>
                            <div class="form-group">
                                <label for="db_user">Username:</label>
                                <input type="text" id="db_user" name="db_user" value="<?php echo htmlspecialchars(DB_USER); ?>" placeholder="Nhập user (Ví dụ: 'root' hoặc tên user bạn đã tạo)">
                            </div>
                            <div class="form-group">
                                <label for="db_pass">Password:</label>
                                <input type="password" id="db_pass" name="db_pass" value="<?php echo htmlspecialchars(DB_PASS); ?>" placeholder="Nhập mật khẩu (để trống nếu không có mật khẩu)">
                            </div>
                            <div class="form-group">
                                <label for="db_charset">Charset:</label>
                                <input type="text" id="db_charset" name="db_charset" value="<?php echo htmlspecialchars(DB_CHARSET); ?>" placeholder="utf8mb4">
                            </div>
                            <div class="button-group">
                                <button type="button" id="update-config-btn" class="btn-update">💾 Cập nhật cấu hình</button>
                            </div>
                        </form>
                    </div>
                </div>
                </div>

            </div>


    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const updateBtn = document.getElementById('update-config-btn');
            const form = document.getElementById('config-form');
            const notificationDiv = document.getElementById('update-notification');
            const headerStatus = document.getElementById('header-status');
            const statusTitle = document.getElementById('status-title');
            const statusDescription = document.getElementById('status-description');
            const errorDetail = document.getElementById('error-detail');
            const errorMessage = document.getElementById('error-message');

            // Hàm hiển thị thông báo
            function showNotification(message, type = 'success') {
                const className = type === 'success' ? 'update-success' :
                    type === 'error' ? 'update-error' : 'update-info';
                notificationDiv.innerHTML = `<div class="${className}">${message}</div>`;

                // Tự động ẩn sau 5 giây
                setTimeout(() => {
                    notificationDiv.innerHTML = '';
                }, 5000);
            }

            // Hàm hiển thị toast
            function showToast(message, type = 'success') {
                const toast = document.createElement('div');
                toast.className = `toast ${type}`;
                toast.textContent = message;
                document.body.appendChild(toast);

                setTimeout(() => {
                    toast.remove();
                }, 5000);
            }

            // Hàm cập nhật thông tin hiển thị
            function updateDisplay(config) {
                const elements = {
                    'display-host': config.DB_HOST,
                    'display-database': config.DB_NAME,
                    'display-username': config.DB_USER,
                    'display-charset': config.DB_CHARSET
                };

                Object.keys(elements).forEach(id => {
                    const element = document.getElementById(id);
                    if (element) {
                        element.textContent = elements[id];
                    }
                });

                // Cập nhật password
                const passwordElement = document.getElementById('display-password');
                if (passwordElement) {
                    passwordElement.textContent = config.DB_PASS === '' ? '(rỗng)' : '•'.repeat(config.DB_PASS.length);
                }

                // Cập nhật input fields
                document.getElementById('db_host').value = config.DB_HOST;
                document.getElementById('db_name').value = config.DB_NAME;
                document.getElementById('db_user').value = config.DB_USER;
                document.getElementById('db_pass').value = config.DB_PASS;
                document.getElementById('db_charset').value = config.DB_CHARSET;
            }

            // Hàm cập nhật trạng thái kết nối
            function updateConnectionStatus(success, message, config = null) {
                // Cập nhật header
                if (headerStatus) {
                    headerStatus.className = `header ${success ? 'success' : 'error'}`;
                    if (statusTitle) {
                        statusTitle.textContent = success ? '✅ KẾT NỐI THÀNH CÔNG' : '❌ KẾT NỐI THẤT BẠI';
                    }
                    if (statusDescription) {
                        statusDescription.textContent = success ? 'Database đã được kết nối thành công!' : (message || 'Không thể kết nối đến database');
                    }
                }

                // Cập nhật trạng thái kết nối
                const statusElement = document.getElementById('connection-status');
                if (statusElement) {
                    if (success) {
                        statusElement.textContent = '● Đã kết nối';
                        statusElement.style.color = '#27ae60';
                    } else {
                        statusElement.textContent = '● Mất kết nối';
                        statusElement.style.color = '#e74c3c';
                    }
                }

                // Cập nhật error detail
                if (errorDetail) {
                    if (success) {
                        errorDetail.style.display = 'none';
                    } else {
                        errorDetail.style.display = 'block';
                        if (errorMessage) {
                            errorMessage.textContent = message || 'Không thể xác định lỗi';
                        }
                    }
                }

                if (config) {
                    updateDisplay(config);
                }
            }

            // Hàm validate form trước khi submit
            function validateForm() {
                const host = document.getElementById('db_host').value.trim();
                const dbname = document.getElementById('db_name').value.trim();
                const username = document.getElementById('db_user').value.trim();

                let isValid = true;
                let errors = [];

                // Reset error states
                document.querySelectorAll('.form-group input').forEach(input => {
                    input.classList.remove('error');
                });

                if (!host) {
                    document.getElementById('db_host').classList.add('error');
                    errors.push('Host không được để trống');
                    isValid = false;
                }

                if (!dbname) {
                    document.getElementById('db_name').classList.add('error');
                    errors.push('Database name không được để trống');
                    isValid = false;
                }

                if (!username) {
                    document.getElementById('db_user').classList.add('error');
                    errors.push('Username không được để trống');
                    isValid = false;
                }

                // Kiểm tra format host
                if (host && !/^[a-zA-Z0-9\.\:\-]+$/.test(host)) {
                    document.getElementById('db_host').classList.add('error');
                    errors.push('Host không hợp lệ');
                    isValid = false;
                }

                // Kiểm tra format database name
                if (dbname && !/^[a-zA-Z0-9\_\-]+$/.test(dbname)) {
                    document.getElementById('db_name').classList.add('error');
                    errors.push('Database name không hợp lệ');
                    isValid = false;
                }

                if (!isValid) {
                    showNotification('❌ ' + errors.join('<br>'), 'error');
                }

                return isValid;
            }

            // Hàm cập nhật cấu hình
            async function updateConfig() {
                if (!validateForm()) {
                    return;
                }
                // Validate dữ liệu trước khi gửi
                const host = document.getElementById('db_host').value.trim();
                const dbname = document.getElementById('db_name').value.trim();
                const username = document.getElementById('db_user').value.trim();

                if (!host) {
                    showNotification('❌ Vui lòng nhập Host (ví dụ: localhost:3306)', 'error');
                    document.getElementById('db_host').classList.add('error');
                    return;
                }
                if (!dbname) {
                    showNotification('❌ Vui lòng nhập tên Database', 'error');
                    document.getElementById('db_name').classList.add('error');
                    return;
                }
                if (!username) {
                    showNotification('❌ Vui lòng nhập Username', 'error');
                    document.getElementById('db_user').classList.add('error');
                    return;
                }

                // Xóa class error nếu có
                document.querySelectorAll('.form-group input').forEach(input => {
                    input.classList.remove('error');
                });

                // Disable button và hiển thị loading
                updateBtn.disabled = true;
                const originalText = updateBtn.innerHTML;
                updateBtn.innerHTML = '<span class="loading-spinner"></span> Đang cập nhật...';

                // Clear previous notifications
                notificationDiv.innerHTML = '';
                // 1. Lấy dữ liệu từ form
                const formData = new FormData(form);
                formData.append('ajax_action', 'update_config');
                // 2. Gửi AJAX request đến server
                try {
                    const response = await fetch(window.location.href, {
                        method: 'POST',
                        body: formData
                    });
                    const data = await response.json();

                    if (data.success) {
                        // Cập nhật thông tin hiển thị
                        updateDisplay(data.config);
                        updateConnectionStatus(true, '', data.config);

                        // Cập nhật thông tin hệ thống
                        if (data.mysql_version) {
                            const versionElement = document.getElementById('mysql-version');
                            if (versionElement) versionElement.textContent = data.mysql_version;
                        }
                        if (data.current_time) {
                            const timeElement = document.getElementById('current-time');
                            if (timeElement) timeElement.textContent = data.current_time;
                        }

                        showNotification('✅ ' + data.message, 'success');
                        showToast('✅ Cập nhật thành công!', 'success');

                        // Nếu có yêu cầu reload
                        if (data.reload) {
                            setTimeout(() => {
                                location.reload();
                            }, 2000);
                        }
                    } else {
                        // Hiển thị lỗi
                        let errorMsg = data.message;
                        if (data.error) {
                            errorMsg += ': ' + data.error;
                        }
                        if (data.suggestion) {
                            errorMsg += '<br><br>💡 ' + data.suggestion;
                        }

                        updateConnectionStatus(false, data.error || data.message);
                        showNotification(errorMsg, 'error');
                        showToast('❌ Cập nhật thất bại!', 'error');
                    }
                } catch (error) {
                    showNotification('❌ Lỗi hệ thống: ' + error.message, 'error');
                    showToast('❌ Lỗi hệ thống!', 'error');
                } finally {
                    // Enable button
                    updateBtn.disabled = false;
                    updateBtn.innerHTML = originalText;
                }
            }

            // Event listeners
            updateBtn.addEventListener('click', updateConfig);

            // Enter key support
            form.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    updateConfig();
                }
            });

            // Clear error class on focus
            document.querySelectorAll('.form-group input').forEach(input => {
                input.addEventListener('focus', function() {
                    this.classList.remove('error');
                });
            });
        });
        // Thêm vào phần JavaScript
    </script>
</body>

</html>