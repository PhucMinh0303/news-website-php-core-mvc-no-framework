<?php
namespace App\Models;

use App\Core\Database;
use PDO;

abstract class BaseModel
{
    protected $table;
    protected $primaryKey = 'id';
    protected $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    // CRUD Operations
    public function all($columns = ['*'])
    {
        $columns = implode(', ', $columns);
        $sql = "SELECT {$columns} FROM {$this->table}";
        return $this->db->select($sql);
    }
    
    public function find($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id";
        return $this->db->selectOne($sql, ['id' => $id]);
    }
    
    public function findBy($column, $value)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$column} = :value";
        return $this->db->selectOne($sql, ['value' => $value]);
    }
    
    public function create(array $data)
    {
        return $this->db->insert($this->table, $data);
    }
    
    public function update($id, array $data)
    {
        $where = "{$this->primaryKey} = :id";
        return $this->db->update($this->table, $data, $where, ['id' => $id]);
    }
    
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        return $this->db->query($sql, ['id' => $id]);
    }
    
    // Query Builder Methods
    public function where($column, $operator, $value = null)
    {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }
        
        $this->whereClauses[] = [
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
            'type' => 'AND'
        ];
        
        return $this;
    }
    
    public function orWhere($column, $operator, $value = null)
    {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }
        
        $this->whereClauses[] = [
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
            'type' => 'OR'
        ];
        
        return $this;
    }
    
    public function get($columns = ['*'])
    {
        $columns = implode(', ', $columns);
        $sql = "SELECT {$columns} FROM {$this->table}";
        
        if (!empty($this->whereClauses)) {
            $sql .= " WHERE ";
            $whereParts = [];
            
            foreach ($this->whereClauses as $index => $where) {
                if ($index === 0) {
                    $whereParts[] = "{$where['column']} {$where['operator']} :where_{$index}";
                } else {
                    $whereParts[] = "{$where['type']} {$where['column']} {$where['operator']} :where_{$index}";
                }
                $params["where_{$index}"] = $where['value'];
            }
            
            $sql .= implode(' ', $whereParts);
            $result = $this->db->select($sql, $params);
        } else {
            $result = $this->db->select($sql);
        }
        
        // Reset clauses
        $this->whereClauses = [];
        
        return $result;
    }
    
    // Pagination
    public function paginate($perPage = 15, $currentPage = null)
    {
        $currentPage = $currentPage ?: ($_GET['page'] ?? 1);
        $offset = ($currentPage - 1) * $perPage;
        
        // Get total count
        $total = $this->count();
        
        // Get paginated results
        $sql = "SELECT * FROM {$this->table}";
        
        if (!empty($this->whereClauses)) {
            $sql .= " WHERE ";
            // ... build where clauses
        }
        
        $sql .= " LIMIT {$perPage} OFFSET {$offset}";
        
        $items = $this->db->select($sql);
        
        return [
            'data' => $items,
            'current_page' => (int)$currentPage,
            'per_page' => $perPage,
            'total' => $total,
            'last_page' => ceil($total / $perPage)
        ];
    }
    
    public function count()
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        
        if (!empty($this->whereClauses)) {
            $sql .= " WHERE ";
            // ... build where clauses
        }
        
        $result = $this->db->selectOne($sql);
        return (int)$result['count'];
    }
}