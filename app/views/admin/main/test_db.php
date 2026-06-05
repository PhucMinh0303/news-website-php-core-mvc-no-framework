<?php
require_once __DIR__ . '/../../../core/Database.php';

// Bật hiển thị lỗi chi tiết
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Khởi tạo biến kết nối
$connection = null;
$isConnected = false;
$errorMessage = '';

// Hàm lấy thông tin cấu hình database một cách an toàn
function getDbConfig()
{
    $config = [
        'DB_HOST' => defined('DB_HOST') ? DB_HOST : 'localhost:3306',
        'DB_NAME' => defined('DB_NAME') ? DB_NAME : 'quanlytintuc',
        'DB_USER' => defined('DB_USER') ? DB_USER : 'root',
        'DB_PASS' => defined('DB_PASS') ? DB_PASS : '',
        'DB_CHARSET' => defined('DB_CHARSET') ? DB_CHARSET : 'utf8mb4'
    ];

    // Nếu các hằng số chưa được định nghĩa, thử load từ file cấu hình
    if (!defined('DB_HOST')) {
        $configPath = __DIR__ . '/../../../config/database.php';
        if (file_exists($configPath)) {
            include_once $configPath;
            $config['DB_HOST'] = defined('DB_HOST') ? DB_HOST : $config['DB_HOST'];
            $config['DB_NAME'] = defined('DB_NAME') ? DB_NAME : $config['DB_NAME'];
            $config['DB_USER'] = defined('DB_USER') ? DB_USER : $config['DB_USER'];
            $config['DB_PASS'] = defined('DB_PASS') ? DB_PASS : $config['DB_PASS'];
            $config['DB_CHARSET'] = defined('DB_CHARSET') ? DB_CHARSET : $config['DB_CHARSET'];
        }
    }

    return $config;
}

// Lấy cấu hình hiện tại
$dbConfig = getDbConfig();

// Định nghĩa lại các hằng số nếu cần (để hiển thị trong HTML)
if (!defined('DB_HOST')) define('DB_HOST', $dbConfig['DB_HOST']);
if (!defined('DB_NAME')) define('DB_NAME', $dbConfig['DB_NAME']);
if (!defined('DB_USER')) define('DB_USER', $dbConfig['DB_USER']);
if (!defined('DB_PASS')) define('DB_PASS', $dbConfig['DB_PASS']);
if (!defined('DB_CHARSET')) define('DB_CHARSET', $dbConfig['DB_CHARSET']);

// Hàm kiểm tra kết nối database với cấu hình cụ thể
function testDatabaseConnection($host, $dbname, $user, $pass, $charset)
{
    try {
        $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
        $pdo = new PDO($dsn, $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return ['success' => true, 'connection' => $pdo];
    } catch (PDOException $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

// Kiểm tra kết nối với cấu hình hiện tại
$testResult = testDatabaseConnection(
    $dbConfig['DB_HOST'],
    $dbConfig['DB_NAME'],
    $dbConfig['DB_USER'],
    $dbConfig['DB_PASS'],
    $dbConfig['DB_CHARSET']
);

if ($testResult['success']) {
    $isConnected = true;
    $connection = $testResult['connection'];
    $errorMessage = '';
} else {
    $isConnected = false;
    $errorMessage = $testResult['error'];
}

// Xử lý khi có dữ liệu POST gửi lên (cập nhật cấu hình)
$configUpdated = false;
$updateError = '';
$updateSuccess = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_config'])) {
    $newHost = $_POST['db_host'] ?? 'localhost:3306';
    $newName = $_POST['db_name'] ?? 'quanlytintuc';
    $newUser = $_POST['db_user'] ?? 'root';
    $newPass = $_POST['db_pass'] ?? '';
    $newCharset = $_POST['db_charset'] ?? 'utf8mb4';

    // Thử kết nối với cấu hình mới
    $testNewConfig = testDatabaseConnection($newHost, $newName, $newUser, $newPass, $newCharset);

    if ($testNewConfig['success']) {
        // Ghi cấu hình mới vào file
        $configPath = __DIR__ . '/../../../config/database.php';
        $configContent = "<?php\ndefine('BASE_PATH', dirname(__DIR__));\n\n\n// Thông tin cấu hình kết nối database cho XAMPP\ndefine('DB_HOST', '$newHost'); // Địa chỉ máy chủ MySQL (thường là localhost) và cổng (3306 hoặc 3307)\ndefine('DB_NAME', '$newName'); // Tên database trong phpMyAdmin\ndefine('DB_USER', '$newUser');               // Username MySQL\ndefine('DB_PASS', '$newPass');                   // Password MySQL\ndefine('DB_CHARSET', '$newCharset');";

        if (file_put_contents($configPath, $configContent)) {
            $updateSuccess = "Đã cập nhật cấu hình thành công!";
            // Cập nhật cấu hình hiện tại
            $dbConfig = getDbConfig();
            // Định nghĩa lại các hằng số
            if (!defined('DB_HOST')) define('DB_HOST', $dbConfig['DB_HOST']);
            if (!defined('DB_NAME')) define('DB_NAME', $dbConfig['DB_NAME']);
            if (!defined('DB_USER')) define('DB_USER', $dbConfig['DB_USER']);
            if (!defined('DB_PASS')) define('DB_PASS', $dbConfig['DB_PASS']);
            if (!defined('DB_CHARSET')) define('DB_CHARSET', $dbConfig['DB_CHARSET']);

            // Kiểm tra lại kết nối
            $testResult = testDatabaseConnection(
                $dbConfig['DB_HOST'],
                $dbConfig['DB_NAME'],
                $dbConfig['DB_USER'],
                $dbConfig['DB_PASS'],
                $dbConfig['DB_CHARSET']
            );

            if ($testResult['success']) {
                $isConnected = true;
                $connection = $testResult['connection'];
                $errorMessage = '';
            } else {
                $isConnected = false;
                $errorMessage = $testResult['error'];
            }
        } else {
            $updateError = "Không thể ghi file cấu hình. Vui lòng kiểm tra quyền thư mục.";
        }
    } else {
        $updateError = "Kết nối thất bại với cấu hình mới: " . $testNewConfig['error'];
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
        }

        .header.success {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .header.error {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .testdb-content {
            padding: 30px;
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
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }

        .btn-update {
            background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%);
            margin-top: 10px;
        }

        .update-success {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            border-left: 4px solid #28a745;
        }

        .update-error {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            border-left: 4px solid #dc3545;
        }
    </style>
</head>

<body>
    <div class="testdb-container">
        <?php if ($isConnected): ?>
            <!-- TRƯỜNG HỢP 1: KẾT NỐI THÀNH CÔNG -->
            <div class="header success">
                <div class="icon">✅</div>
                <h2>KẾT NỐI THÀNH CÔNG</h2>
                <p>Database đã được kết nối thành công!</p>
            </div>
            <div class="testdb-content">
                <div class="info-box">
                    <div class="info-item">
                        <span class="label">Trạng thái:</span>
                        <span class="value" style="color: #27ae60; font-weight: bold;">● Đã kết nối</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Host:</span>
                        <span class="value"><?php echo DB_HOST; ?></span>
                    </div>
                    <div class="info-item">
                        <span class="label">Database:</span>
                        <span class="value"><?php echo DB_NAME; ?></span>
                    </div>
                    <div class="info-item">
                        <span class="label">Username:</span>
                        <span class="value"><?php echo DB_USER; ?></span>
                    </div>
                </div>

                <?php
                // Chuyển xử lý cho Database.php - gọi phương thức kiểm tra từ Database class
                try {
                    $testQuery = $connection->query("SELECT VERSION() as version, NOW() as current_time");
                    $result = $testQuery->fetch(PDO::FETCH_ASSOC);
                ?>
                    <div class="info-box" style="border-left-color: #27ae60;">
                        <h3 style="margin-top: 0;">📊 Thông tin hệ thống:</h3>
                        <div class="info-item">
                            <span class="label">MySQL Version:</span>
                            <span class="value"><?php echo $result['version']; ?></span>
                        </div>
                        <div class="info-item">
                            <span class="label">Thời gian:</span>
                            <span class="value"><?php echo $result['current_time']; ?></span>
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

                <button onclick="window.location.href='index.php'">Tiếp tục →</button>
            </div>

        <?php else: ?>
            <!-- TRƯỜNG HỢP 2: KẾT NỐI THẤT BẠI - HIỂN THỊ THÔNG TIN TỪ DATABASE.PHP -->
            <div class="header error">
                <div class="icon">❌</div>
                <h2>KẾT NỐI THẤT BẠI</h2>
                <p>Không thể kết nối đến database</p>
            </div>
            <div class="testdb-content">
                <!-- Hiển thị thông báo cập nhật -->
                <?php if ($updateSuccess): ?>
                    <div class="update-success">✅ <?php echo htmlspecialchars($updateSuccess); ?></div>
                <?php endif; ?>

                <?php if ($updateError): ?>
                    <div class="update-error">❌ <?php echo htmlspecialchars($updateError); ?></div>
                <?php endif; ?>

                <!-- Hiển thị đầy đủ thông tin từ config/database.php -->
                <div class="info-box">
                    <h3 style="margin-top: 0; color: #d63031;">📋 Thông tin cấu hình (từ database.php):</h3>
                    <div class="info-item">
                        <span class="label">Host:</span>
                        <span class="value"><?php echo DB_HOST; ?></span>
                    </div>
                    <div class="info-item">
                        <span class="label">Database:</span>
                        <span class="value"><?php echo DB_NAME; ?></span>
                    </div>
                    <div class="info-item">
                        <span class="label">Username:</span>
                        <span class="value"><?php echo DB_USER; ?></span>
                    </div>
                    <div class="info-item">
                        <span class="label">Password:</span>
                        <span class="value"><?php echo DB_PASS === '' ? '(rỗng)' : str_repeat('•', strlen(DB_PASS)); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="label">Charset:</span>
                        <span class="value"><?php echo DB_CHARSET; ?></span>
                    </div>
                </div>



                <!-- Hiển thị chi tiết lỗi -->
                <div class="error-detail">
                    <strong>🔍 Chi tiết lỗi:</strong>
                    <div class="error-message">
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

                <button onclick="location.reload()">Thử lại 🔄</button>
            </div>

        <?php endif; ?>
        <div class="testdb-content">
            <!-- Form cập nhật cấu hình -->
            <div class="config-form">
                <h3>🔧 Cập nhật cấu hình kết nối</h3>
                <form method="POST" action="">
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
                    <button type="submit" name="update_config" class="btn-update">Cập nhật cấu hình</button>
                </form>
            </div>
        </div>

    </div>
</body>

</html>