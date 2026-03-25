<?php
// app/core/Model.php
require_once __DIR__ . '/../config/database.php';

class Model
{
    protected $table;
    protected $primaryKey = 'id';
    protected $db;

    public function __construct()
    {
        $database = Database::getInstance();
        $this->db = $database->getConnection();
    }

    /**
     * Execute query and return results
     */
    protected function query($sql, $params = [])
    {
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Query error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Execute query without returning results
     */
    protected function execute($sql, $params = [])
    {
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Execute error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get single record by ID
     */
    public function find($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?";
        $result = $this->query($sql, [$id]);
        return $result ? $result[0] : null;
    }

    /**
     * Get all records
     */
    public function all()
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY created_at DESC";
        return $this->query($sql);
    }

    /**
     * Create new record
     */
    public function create($data)
    {
        $fields = array_keys($data);
        $placeholders = array_fill(0, count($fields), '?');

        $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";

        $values = array_values($data);

        if ($this->execute($sql, $values)) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    /**
     * Update record
     */
    public function update($id, $data)
    {
        $fields = [];
        $values = [];

        foreach ($data as $field => $value) {
            $fields[] = "{$field} = ?";
            $values[] = $value;
        }

        $values[] = $id;
        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " 
                WHERE {$this->primaryKey} = ?";

        return $this->execute($sql, $values);
    }

    /**
     * Delete record
     */
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
        return $this->execute($sql, [$id]);
    }
}

?>