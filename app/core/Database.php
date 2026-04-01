<?php
require_once __DIR__ . '/../config/database.php';

class Database
{
    private $host = DB_HOST;
    private $dbname = DB_NAME;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $charset = DB_CHARSET;
    private $pdo;
    private $error;
    private static $instance = null;

    public function __construct()
    {
        $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ];

        try {
            $this->pdo = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            die("Connection failed: " . $this->error);
        }
    }

    public function getConnection()
    {
        return $this->pdo;
    }


    // Thực thi câu lệnh SQL
    public function query($sql, $params = [])
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            die('Lỗi truy vấn: ' . $e->getMessage());
        }
    }

    // Lấy tất cả bản ghi
    public function fetchAll($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }

    // Lấy một bản ghi
    public function fetchOne($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }

    // Lấy ID vừa insert
    public function lastInsertId()
    {
        return $this->pdo->lastInsertId();
    }

    // Bắt đầu transaction
    public function beginTransaction()
    {
        return $this->pdo->beginTransaction();
    }

    // Commit transaction
    public function commit()
    {
        return $this->pdo->commit();
    }

    // Rollback transaction
    public function rollback()
    {
        return $this->pdo->rollBack();
    }
}