<?php
require_once __DIR__ . '/../core/Database.php';

class BaseModel
{
    protected $db;
    protected $conn;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->conn = $this->db->getConnection();
    }

    public function __destruct()
    {
        if ($this->db) {
            $this->db->closeConnection();
        }
    }

    // Phương thức lấy dữ liệu (SELECT)
    protected function select($sql, $params = [])
    {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Phương thức lấy 1 dòng duy nhất
    protected function selectOne($sql, $params = [])
    {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Phương thức thực thi (INSERT, UPDATE, DELETE)
    protected function execute($sql, $params = [])
    {
        $stmt = $this->conn->prepare($sql);
        $success = $stmt->execute($params);

        return [
            'success' => $success,
            'insert_id' => $success ? (int)$this->conn->lastInsertId() : 0,
            'affected_rows' => $stmt->rowCount()
        ];
    }
}
