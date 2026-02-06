<?php
namespace App\Core;

abstract class Model
{
    protected $table;
    protected $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    public function all()
    {
        $sql = "SELECT * FROM {$this->table}";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    public function find($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt->fetch();
    }
    
    public function create($data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $this->db->query($sql, array_values($data));
        
        return $this->db->lastInsertId();
    }
    
    public function update($id, $data)
    {
        $setClause = implode(' = ?, ', array_keys($data)) . ' = ?';
        $values = array_values($data);
        $values[] = $id;
        
        $sql = "UPDATE {$this->table} SET {$setClause} WHERE id = ?";
        return $this->db->query($sql, $values)->rowCount();
    }
    
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        return $this->db->query($sql, [$id])->rowCount();
    }
    
    public function where($conditions, $params = [])
    {
        $whereClause = implode(' AND ', $conditions);
        $sql = "SELECT * FROM {$this->table} WHERE {$whereClause}";
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }
}