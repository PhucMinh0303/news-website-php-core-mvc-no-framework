<?php
require_once __DIR__ . '/../config/database.php';

class Database
{
    private $host = DB_HOST;
    private $dbname = DB_NAME;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $charset = DB_CHARSET;
    private $conn;
    private $error;
    private static $instance = null;

    private function __construct()
    {
        $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ];

        try {
            $this->conn = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            die("Connection failed: " . $this->error);
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getConnection()
    {
        return $this->conn;
    }

    public function getError()
    {
        return $this->error;
    }

    public function isConnected()
    {
        return $this->conn !== null;
    }

    // Thực thi câu lệnh SQL
    public function query($sql, $params = [])
    {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            die('Lỗi truy vấn: ' . $e->getMessage());
        }
    }

    // Lấy tất cả bản ghi
    public function fetchAll($sql, $params = [])
    {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die('Lỗi truy vấn: ' . $e->getMessage());
        }
    }

    // Lấy một bản ghi
    public function fetchOne($sql, $params = [])
    {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die('Lỗi truy vấn: ' . $e->getMessage());
        }
    }

    // Lấy ID vừa insert
    public function insert($table, $data)
    {
        try {
            $fields = array_keys($data);
            $placeholders = ':' . implode(', :', $fields);

            $sql = "INSERT INTO {$table} (" . implode(', ', $fields) . ") 
                    VALUES ({$placeholders})";

            $stmt = $this->query($sql, $data);
            return $this->conn->lastInsertId();
        } catch (PDOException $e) {
            die('Lỗi insert: ' . $e->getMessage());
        }
    }

    public function update($table, $data, $where, $whereParams = [])
    {
        try {
            $fields = array_map(function ($field) {
                return "{$field} = :{$field}";
            }, array_keys($data));

            $sql = "UPDATE {$table} SET " . implode(', ', $fields) . " WHERE {$where}";
            $params = array_merge($data, $whereParams);

            $stmt = $this->query($sql, $params);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            die('Lỗi update: ' . $e->getMessage());
        }
    }

    public function delete($table, $where, $params = [])
    {
        try {
            $sql = "DELETE FROM {$table} WHERE {$where}";
            $stmt = $this->query($sql, $params);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            die('Lỗi delete: ' . $e->getMessage());
        }
    }

    // Bắt đầu transaction
    public function beginTransaction()
    {
        return $this->conn->beginTransaction();
    }

    // Commit transaction
    public function commit()
    {
        return $this->conn->commit();
    }

    // Rollback transaction
    public function rollback()
    {
        return $this->conn->rollBack();
    }

    // Test the database connection
    public static function testConnection($config)
    {
        try {
            $dsn = "mysql:host={$config['DB_HOST']};dbname={$config['DB_NAME']};charset={$config['DB_CHARSET']}";
            $conn = new PDO($dsn, $config['DB_USER'], $config['DB_PASS'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
            return ['success' => true, 'connection' => $conn];
        } catch (PDOException $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // Static method to reinitialize .env
    public static function reinitialize($config)
    {
        // Write to .env file
    }


    // Thêm vào class Database

/**
 * Update configuration in .env file
 */
public static function updateConfig($config)
{
    $envFile = BASE_PATH . '/app/config/.env';
    
    if (!file_exists($envFile)) {
        return ['success' => false, 'error' => 'File .env không tồn tại'];
    }
    
    // Đọc file .env
    $content = file_get_contents($envFile);
    $lines = explode("\n", $content);
    $newLines = [];
    $updatedKeys = [];
    
    foreach ($lines as $line) {
        $isUpdated = false;
        foreach ($config as $key => $value) {
            if (strpos($line, $key . '=') === 0) {
                // Escape giá trị nếu cần
                $escapedValue = (strpos($value, ' ') !== false || strpos($value, '#') !== false) 
                    ? '"' . $value . '"' 
                    : $value;
                $newLines[] = $key . '=' . $escapedValue;
                $updatedKeys[$key] = true;
                $isUpdated = true;
                break;
            }
        }
        if (!$isUpdated) {
            $newLines[] = $line;
        }
    }
    
    // Thêm các key chưa có
    foreach ($config as $key => $value) {
        if (!isset($updatedKeys[$key])) {
            $escapedValue = (strpos($value, ' ') !== false || strpos($value, '#') !== false) 
                ? '"' . $value . '"' 
                : $value;
            $newLines[] = $key . '=' . $escapedValue;
        }
    }
    
    // Ghi lại file
    if (file_put_contents($envFile, implode("\n", $newLines)) === false) {
        return ['success' => false, 'error' => 'Không thể ghi file .env'];
    }
    
    return ['success' => true];
}

/**
 * Get current database configuration
 */
public static function getCurrentConfig()
{
    return [
        'DB_HOST' => DB_HOST,
        'DB_NAME' => DB_NAME,
        'DB_USER' => DB_USER,
        'DB_PASS' => DB_PASS,
        'DB_CHARSET' => DB_CHARSET
    ];
}
}
