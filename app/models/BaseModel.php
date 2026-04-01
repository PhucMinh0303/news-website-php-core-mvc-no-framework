<?php
require_once __DIR__ . '/../config/Database.php';

class BaseModel
{
    protected $db;
    protected $connection;

    public function __construct()
    {
        $this->db = new Database();
        $this->connection = $this->db->getConnection();
    }

    public function __destruct()
    {
        $this->db->closeConnection();
    }

    // Phương thức lấy dữ liệu (SELECT)
    protected function select($sql, $params = [], $types = "")
    {
        $stmt = $this->connection->prepare($sql);

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $data = [];

        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        $stmt->close();
        return $data;
    }

    // Phương thức lấy 1 dòng duy nhất
    protected function selectOne($sql, $params = [], $types = "")
    {
        $result = $this->select($sql, $params, $types);
        return $result[0] ?? null;
    }

    // Phương thức thực thi (INSERT, UPDATE, DELETE)
    protected function execute($sql, $params = [], $types = "")
    {
        $stmt = $this->connection->prepare($sql);

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $result = $stmt->execute();
        $insertId = $stmt->insert_id;
        $affectedRows = $stmt->affected_rows;

        $stmt->close();

        return [
            'success' => $result,
            'insert_id' => $insertId,
            'affected_rows' => $affectedRows
        ];
    }
}

?>