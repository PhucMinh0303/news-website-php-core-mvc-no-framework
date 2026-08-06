<?php
// Định nghĩa BASE_PATH nếu chưa được định nghĩa
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(dirname(dirname(dirname(__DIR__)))));
}

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
    // Xác định đường dẫn file .env chính xác
    $envFile = getEnvFilePath();

    // Kiểm tra file tồn tại
    if (!file_exists($envFile)) {
        // Thử tạo file .env mới nếu chưa tồn tại
        $envDir = dirname($envFile);
        if (!is_dir($envDir)) {
            if (!mkdir($envDir, 0755, true)) {
                throw new Exception('Không thể tạo thư mục: ' . $envDir);
            }
        }

        // Tạo file .env mới với nội dung mặc định
        $defaultContent = "# Database Configuration\n";
        $defaultContent .= "# Last updated: " . date('Y-m-d H:i:s') . "\n\n";
        foreach ($config as $key => $value) {
            $defaultContent .= $key . "=" . $value . "\n";
        }

        if (file_put_contents($envFile, $defaultContent) === false) {
            throw new Exception('Không thể tạo file .env mới tại: ' . $envFile);
        }

        return true;
    }

    // Đọc nội dung file .env hiện tại
    $envContent = file_get_contents($envFile);
    if ($envContent === false) {
        throw new Exception('Không thể đọc file .env');
    }

    // Backup file .env hiện tại
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

    // Xóa cache nếu có
    if (function_exists('opcache_reset')) {
        opcache_reset();
    }

    return true;
}



// Xử lý AJAX request để kiểm tra kết nối
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_action'])) {
    header('Content-Type: application/json');

    // Xử lý test connection
    if ($_POST['ajax_action'] === 'update_config') {
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

    
}

?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kiểm tra kết nối Database</title>
    <!-- Thêm jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

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
            gap: 5px;
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


                <script>
                    // Đảm bảo jQuery đã được tải
                    $(document).ready(function() {
                        // ===== CẤU HÌNH =====
                        var CONFIG = {
                            debug: true, // Bật debug để xem log
                            timeout: 30000, // Timeout 30 giây
                            reloadDelay: 2000 // Delay reload sau khi cập nhật thành công
                        };

                        // ===== CACHE SELECTORS =====
                        var $updateBtn = $('#update-config-btn');
                        var $form = $('#config-form');
                        var $notificationDiv = $('#update-notification');

                        // ===== UTILITY FUNCTIONS =====
                        function log(message, data) {
                            if (CONFIG.debug) {
                                console.log('[Update Config]', message, data || '');
                            }
                        }

                        function showNotification(message, type) {
                            log('Showing notification:', {
                                message,
                                type
                            });
                            type = type || 'success';
                            var className = type === 'success' ? 'update-success' :
                                type === 'error' ? 'update-error' : 'update-info';
                            $notificationDiv.html('<div class="' + className + '">' + message + '</div>');

                            // Tự động ẩn sau 5 giây
                            setTimeout(function() {
                                $notificationDiv.html('');
                            }, 5000);
                        }

                        function showToast(message, type) {
                            log('Showing toast:', {
                                message,
                                type
                            });
                            type = type || 'success';
                            var $toast = $('<div class="toast ' + type + '">' + message + '</div>');
                            $('body').append($toast);

                            setTimeout(function() {
                                $toast.fadeOut(300, function() {
                                    $(this).remove();
                                });
                            }, 5000);
                        }

                        function updateDisplay(config) {
                            log('Updating display with config:', config);

                            if (config.DB_HOST) $('#display-host').text(config.DB_HOST);
                            if (config.DB_NAME) $('#display-database').text(config.DB_NAME);
                            if (config.DB_USER) $('#display-username').text(config.DB_USER);
                            if (config.DB_CHARSET) $('#display-charset').text(config.DB_CHARSET);

                            if (config.DB_PASS !== undefined) {
                                $('#display-password').text(config.DB_PASS === '' ? '(rỗng)' : '•'.repeat(config.DB_PASS.length));
                            }

                            // Cập nhật input fields
                            if (config.DB_HOST !== undefined) $('#db_host').val(config.DB_HOST);
                            if (config.DB_NAME !== undefined) $('#db_name').val(config.DB_NAME);
                            if (config.DB_USER !== undefined) $('#db_user').val(config.DB_USER);
                            if (config.DB_PASS !== undefined) $('#db_pass').val(config.DB_PASS);
                            if (config.DB_CHARSET !== undefined) $('#db_charset').val(config.DB_CHARSET);
                        }

                        function updateConnectionStatus(success, message, config) {
                            log('Updating connection status:', {
                                success,
                                message,
                                config
                            });

                            var $headerStatus = $('#header-status');
                            var $statusTitle = $('#status-title');
                            var $statusDescription = $('#status-description');
                            var $statusElement = $('#connection-status');
                            var $errorDetail = $('#error-detail');
                            var $errorMessage = $('#error-message');

                            if ($headerStatus.length) {
                                $headerStatus.removeClass('success error').addClass(success ? 'success' : 'error');
                                if ($statusTitle.length) {
                                    $statusTitle.text(success ? '✅ KẾT NỐI THÀNH CÔNG' : '❌ KẾT NỐI THẤT BẠI');
                                }
                                if ($statusDescription.length) {
                                    $statusDescription.text(success ? 'Database đã được kết nối thành công!' : (message || 'Không thể kết nối đến database'));
                                }
                            }

                            if ($statusElement.length) {
                                if (success) {
                                    $statusElement.text('● Đã kết nối').css('color', '#27ae60');
                                } else {
                                    $statusElement.text('● Mất kết nối').css('color', '#e74c3c');
                                }
                            }

                            if ($errorDetail.length) {
                                if (success) {
                                    $errorDetail.hide();
                                } else {
                                    $errorDetail.show();
                                    if ($errorMessage.length) {
                                        $errorMessage.text(message || 'Không thể xác định lỗi');
                                    }
                                }
                            }

                            if (config) {
                                updateDisplay(config);
                            }
                        }

                        // ===== VALIDATION =====
                        function validateForm() {
                            log('Validating form...');

                            var $host = $('#db_host');
                            var $dbname = $('#db_name');
                            var $username = $('#db_user');

                            var host = $host.val().trim();
                            var dbname = $dbname.val().trim();
                            var username = $username.val().trim();

                            var isValid = true;
                            var errors = [];

                            // Reset error states
                            $('.form-group input').removeClass('error');

                            if (!host) {
                                $host.addClass('error');
                                errors.push('Host không được để trống');
                                isValid = false;
                            }

                            if (!dbname) {
                                $dbname.addClass('error');
                                errors.push('Database name không được để trống');
                                isValid = false;
                            }

                            if (!username) {
                                $username.addClass('error');
                                errors.push('Username không được để trống');
                                isValid = false;
                            }

                            // Kiểm tra format host
                            if (host && !/^[a-zA-Z0-9\.\:\-]+$/.test(host)) {
                                $host.addClass('error');
                                errors.push('Host không hợp lệ');
                                isValid = false;
                            }

                            // Kiểm tra format database name
                            if (dbname && !/^[a-zA-Z0-9\_\-]+$/.test(dbname)) {
                                $dbname.addClass('error');
                                errors.push('Database name không hợp lệ');
                                isValid = false;
                            }

                            if (!isValid) {
                                showNotification('❌ ' + errors.join('<br>'), 'error');
                            }

                            log('Validation result:', {
                                isValid,
                                errors
                            });
                            return isValid;
                        }

                        // ===== MAIN UPDATE FUNCTION =====
                        function updateConfig() {
                            log('=== BẮT ĐẦU CẬP NHẬT CẤU HÌNH ===');

                            // Validate form
                            if (!validateForm()) {
                                log('Validation failed, stopping update');
                                return;
                            }

                            // Disable button và hiển thị loading
                            $updateBtn.prop('disabled', true);
                            var originalText = $updateBtn.html();
                            $updateBtn.html('<span class="loading-spinner"></span> Đang cập nhật...');

                            // Clear previous notifications
                            $notificationDiv.html('');

                            // Lấy dữ liệu từ form
                            var formData = new FormData($form[0]);
                            formData.append('ajax_action', 'update_config');

                            // Log dữ liệu gửi đi
                            log('Form data:');
                            for (var pair of formData.entries()) {
                                if (pair[0] !== 'db_pass') {
                                    log(pair[0] + ': ' + pair[1]);
                                } else {
                                    log(pair[0] + ': ********');
                                }
                            }

                            // Gửi AJAX request
                            $.ajax({
                                url: window.location.href,
                                type: 'POST',
                                data: formData,
                                processData: false,
                                contentType: false,
                                dataType: 'json',
                                timeout: CONFIG.timeout,

                                success: function(data) {
                                    log('Server response:', data);

                                    if (data.success) {
                                        // Cập nhật hiển thị
                                        if (data.config) {
                                            updateDisplay(data.config);
                                        }

                                        // Cập nhật trạng thái kết nối
                                        updateConnectionStatus(true, data.message, data.config);

                                        // Cập nhật thông tin hệ thống
                                        if (data.mysql_version) {
                                            $('#mysql-version').text(data.mysql_version);
                                        }
                                        if (data.current_time) {
                                            $('#current-time').text(data.current_time);
                                        }

                                        // Hiển thị thông báo thành công
                                        var successMsg = data.message || '✅ Cập nhật cấu hình thành công!';
                                        showNotification(successMsg, 'success');
                                        showToast('✅ Cập nhật thành công!', 'success');

                                        log('Update successful, reloading page in ' + CONFIG.reloadDelay + 'ms');

                                        // Reload trang để áp dụng config mới
                                        setTimeout(function() {
                                            location.reload();
                                        }, CONFIG.reloadDelay);

                                    } else {
                                        // Xử lý lỗi từ server
                                        var errorMsg = data.message || 'Cập nhật thất bại';
                                        if (data.error) {
                                            errorMsg += ': ' + data.error;
                                        }
                                        if (data.suggestion) {
                                            errorMsg += '<br><br>💡 ' + data.suggestion;
                                        }

                                        updateConnectionStatus(false, data.error || data.message);
                                        showNotification('❌ ' + errorMsg, 'error');
                                        showToast('❌ Cập nhật thất bại!', 'error');

                                        log('Update failed:', data);
                                    }
                                },

                                error: function(xhr, status, error) {
                                    log('AJAX Error:', {
                                        status,
                                        error,
                                        responseText: xhr.responseText
                                    });

                                    var errorMsg = 'Lỗi hệ thống: ' + error;

                                    // Thử parse response nếu có
                                    if (xhr.responseText) {
                                        try {
                                            var response = JSON.parse(xhr.responseText);
                                            if (response.message) {
                                                errorMsg = response.message;
                                            }
                                            if (response.error) {
                                                errorMsg += ': ' + response.error;
                                            }
                                            if (response.suggestion) {
                                                errorMsg += '<br><br>💡 ' + response.suggestion;
                                            }
                                        } catch (e) {
                                            // Nếu không parse được JSON, hiển thị raw response (giới hạn 200 ký tự)
                                            errorMsg = 'Lỗi server: ' + xhr.responseText.substring(0, 200);
                                            if (xhr.responseText.length > 200) {
                                                errorMsg += '...';
                                            }
                                        }
                                    }

                                    showNotification('❌ ' + errorMsg, 'error');
                                    showToast('❌ Lỗi hệ thống!', 'error');
                                },

                                complete: function() {
                                    // Enable button
                                    $updateBtn.prop('disabled', false);
                                    $updateBtn.html(originalText);
                                    log('=== KẾT THÚC CẬP NHẬT ===');
                                }
                            });
                        }

                        // ===== EVENT LISTENERS =====
                        // Click event cho nút update
                        $updateBtn.on('click', function(e) {
                            log('Update button clicked');
                            e.preventDefault();
                            updateConfig();
                        });

                        // Enter key support
                        $form.on('keypress', function(e) {
                            if (e.key === 'Enter' || e.which === 13) {
                                var target = $(e.target);
                                // Không submit nếu đang ở input password
                                if (target.is('input') && !target.is('#db_pass')) {
                                    e.preventDefault();
                                    log('Enter key pressed, triggering update');
                                    updateConfig();
                                }
                            }
                        });

                        // Clear error class on focus
                        $('.form-group input').on('focus', function() {
                            $(this).removeClass('error');
                        });

                        // ===== LOGGING =====
                        log('Form cập nhật cấu hình đã sẵn sàng!');
                        log('Current config values:', {
                            DB_HOST: $('#db_host').val(),
                            DB_NAME: $('#db_name').val(),
                            DB_USER: $('#db_user').val(),
                            DB_CHARSET: $('#db_charset').val()
                        });
                    });
                </script>
</body>

</html>