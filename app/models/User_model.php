<?php
namespace App\Models;

use App\Core\Model;

class User extends Model
{
    protected $table = 'users';
    
    public function getActiveUsers()
    {
        $sql = "SELECT * FROM {$this->table} WHERE status = 'active'";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    public function findByEmail($email)
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = ?";
        $stmt = $this->db->query($sql, [$email]);
        return $stmt->fetch();
    }
}