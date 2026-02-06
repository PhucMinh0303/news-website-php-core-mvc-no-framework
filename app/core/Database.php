<?php
namespace App\Core;

use PDO;
use PDOException;
use App\Config\Database as DBConfig;

class Database
{
    private static $instance = null;
    private $connection;
    
    private function __construct()
    {
        $dsn = "mysql:host=" . DBConfig::HOST . 
               ";dbname=" . DBConfig::DB_NAME . 
               ";charset=" . DBConfig::CHARSET;
        
        try {
            $this->connection = new PDO(
                $dsn,
                DBConfig::USER,
                DBConfig::PASSWORD,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
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
        return $this->connection;
    }
    
    public function query($sql, $params = [])
    {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    public function lastInsertId()
    {
        return $this->connection->lastInsertId();
    }
}