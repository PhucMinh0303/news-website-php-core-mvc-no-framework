<?php
// app/core/Model.php

require_once __DIR__ . '/../config/Database.php';

class Model
{
    protected $db;
    protected $connection;
    protected $table;
    protected $isPdo = false; // Thêm flag để kiểm tra loại kết nối

    public function __construct()
    {
        $database = new Database();
        $this->connection = $database->getConnection();
        $this->db = $this->connection;

        // Kiểm tra xem kết nối có phải là PDO không
        $this->isPdo = ($this->connection instanceof PDO);
    }

    // Lấy tất cả bản ghi
    public function findAll($conditions = [], $orderBy = null, $limit = null, $offset = null)
    {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];

        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $key => $value) {
                $where[] = "{$key} = :{$key}";
                $params[$key] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $where);
        }

        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }

        if ($limit) {
            $sql .= " LIMIT :limit";
            $params['limit'] = $limit;
            if ($offset) {
                $sql .= " OFFSET :offset";
                $params['offset'] = $offset;
            }
        }

        $stmt = $this->db->prepare($sql);

        foreach ($params as $key => $value) {
            if ($key === 'limit' || $key === 'offset') {
                $stmt->bindValue(":{$key}", $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue(":{$key}", $value);
            }
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Lấy bản ghi theo ID
    public function findById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";

        if ($this->isPdo) {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $stmt = $this->connection->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();
            $stmt->close();
            return $data;
        }
    }

    // Lấy bản ghi theo slug
    public function findBySlug($slug)
    {
        $sql = "SELECT * FROM {$this->table} WHERE slug = ?";

        if ($this->isPdo) {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute([$slug]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $stmt = $this->connection->prepare($sql);
            $stmt->bind_param("s", $slug);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();
            $stmt->close();
            return $data;
        }
    }

    // Thêm bản ghi
    public function create($data)
    {
        $fields = array_keys($data);
        $placeholders = array_fill(0, count($fields), '?');

        $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";

        if ($this->isPdo) {
            $stmt = $this->connection->prepare($sql);
            $result = $stmt->execute(array_values($data));
            $insertId = $this->connection->lastInsertId();
            return $result ? $insertId : false;
        } else {
            $stmt = $this->connection->prepare($sql);
            $types = $this->getParamTypes($data);
            $values = array_values($data);
            $stmt->bind_param($types, ...$values);
            $result = $stmt->execute();
            $insertId = $stmt->insert_id;
            $stmt->close();
            return $result ? $insertId : false;
        }
    }

    // Cập nhật bản ghi
    public function update($id, $data)
    {
        $fields = array_keys($data);
        $setClause = implode(' = ?, ', $fields) . ' = ?';

        $sql = "UPDATE {$this->table} SET {$setClause} WHERE id = ?";

        if ($this->isPdo) {
            $values = array_values($data);
            $values[] = $id;
            $stmt = $this->connection->prepare($sql);
            $result = $stmt->execute($values);
            $affectedRows = $stmt->rowCount();
            return $result ? $affectedRows : false;
        } else {
            $stmt = $this->connection->prepare($sql);
            $types = $this->getParamTypes($data) . 'i';
            $values = array_values($data);
            $values[] = $id;
            $stmt->bind_param($types, ...$values);
            $result = $stmt->execute();
            $affectedRows = $stmt->affected_rows;
            $stmt->close();
            return $result ? $affectedRows : false;
        }
    }

    // Xóa bản ghi
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";

        if ($this->isPdo) {
            $stmt = $this->connection->prepare($sql);
            $result = $stmt->execute([$id]);
            $affectedRows = $stmt->rowCount();
            return $result ? $affectedRows : false;
        } else {
            $stmt = $this->connection->prepare($sql);
            $stmt->bind_param("i", $id);
            $result = $stmt->execute();
            $affectedRows = $stmt->affected_rows;
            $stmt->close();
            return $result ? $affectedRows : false;
        }
    }

    // Lấy bản ghi với điều kiện
    public function where($conditions, $params = [], $limit = null)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$conditions}";

        if ($limit) {
            $sql .= " LIMIT {$limit}";
        }

        if ($this->isPdo) {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $stmt = $this->connection->prepare($sql);
            if (!empty($params)) {
                $types = $this->getParamTypes($params);
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
    }

    // Lấy một bản ghi với điều kiện
    public function whereOne($conditions, $params = [])
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$conditions} LIMIT 1";

        if ($this->isPdo) {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $stmt = $this->connection->prepare($sql);
            if (!empty($params)) {
                $types = $this->getParamTypes($params);
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();
            $stmt->close();
            return $data;
        }
    }

    // Đếm số bản ghi
    public function count($conditions = null, $params = [])
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";

        if ($conditions) {
            $sql .= " WHERE {$conditions}";
        }

        if ($this->isPdo) {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } else {
            $stmt = $this->connection->prepare($sql);
            if (!empty($params)) {
                $types = $this->getParamTypes($params);
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();
            $stmt->close();
            return $data['total'] ?? 0;
        }
    }

    // Lấy bản ghi có phân trang
    public function paginate($limit = 10, $offset = 0, $conditions = null, $params = [])
    {
        $sql = "SELECT * FROM {$this->table}";

        if ($conditions) {
            $sql .= " WHERE {$conditions}";
        }

        $sql .= " LIMIT {$limit} OFFSET {$offset}";

        if ($this->isPdo) {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $stmt = $this->connection->prepare($sql);
            if (!empty($params)) {
                $types = $this->getParamTypes($params);
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
    }

    // Thực thi câu lệnh SQL tùy chỉnh
    public function query($sql, $params = [], $types = "")
    {
        if ($this->isPdo) {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);

            // Kiểm tra nếu là SELECT
            if (strpos(strtoupper($sql), 'SELECT') === 0) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }

            // INSERT, UPDATE, DELETE
            return [
                'success' => true,
                'affected_rows' => $stmt->rowCount(),
                'insert_id' => $this->connection->lastInsertId()
            ];
        } else {
            $stmt = $this->connection->prepare($sql);

            if (!empty($params)) {
                if (empty($types)) {
                    $types = $this->getParamTypes($params);
                }
                $stmt->bind_param($types, ...$params);
            }

            $stmt->execute();

            // Kiểm tra nếu là SELECT
            if (strpos(strtoupper($sql), 'SELECT') === 0) {
                $result = $stmt->get_result();
                $data = [];
                while ($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }
                $stmt->close();
                return $data;
            }

            // INSERT, UPDATE, DELETE
            $affectedRows = $stmt->affected_rows;
            $insertId = $stmt->insert_id;
            $stmt->close();

            return [
                'success' => true,
                'affected_rows' => $affectedRows,
                'insert_id' => $insertId
            ];
        }
    }

    // Lấy kiểu dữ liệu cho bind_param (chỉ dùng cho mysqli)
    private function getParamTypes($params)
    {
        $types = '';
        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= 'i';
            } elseif (is_float($param) || is_double($param)) {
                $types .= 'd';
            } elseif (is_string($param)) {
                $types .= 's';
            } else {
                $types .= 'b';
            }
        }
        return $types;
    }

    // Escape string để tránh SQL injection
    public function escapeString($string)
    {
        if ($this->isPdo) {
            // PDO không có escape string riêng, dùng quote
            return substr($this->connection->quote($string), 1, -1);
        } else {
            return $this->connection->real_escape_string($string);
        }
    }

    // Lấy ID cuối cùng được insert
    public function lastInsertId()
    {
        if ($this->isPdo) {
            return $this->connection->lastInsertId();
        } else {
            return $this->connection->insert_id;
        }
    }

    // Bắt đầu transaction
    public function beginTransaction()
    {
        if ($this->isPdo) {
            return $this->connection->beginTransaction();
        } else {
            return $this->connection->begin_transaction();
        }
    }

    // Commit transaction
    public function commit()
    {
        if ($this->isPdo) {
            return $this->connection->commit();
        } else {
            return $this->connection->commit();
        }
    }

    // Rollback transaction
    public function rollback()
    {
        if ($this->isPdo) {
            return $this->connection->rollBack();
        } else {
            return $this->connection->rollback();
        }
    }

    // Đóng kết nối
    public function closeConnection()
    {
        if ($this->connection) {
            if ($this->isPdo) {
                // PDO: gán null để đóng kết nối
                $this->connection = null;
            } else {
                $this->connection->close();
            }
        }
    }

    public function __destruct()
    {
        $this->closeConnection();
    }
}

?>