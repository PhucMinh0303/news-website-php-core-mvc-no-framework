<?php
require_once __DIR__ . '/../config/database.php';

class Database
{
    /**
     * @var string Database host
     */
    private $host;
    
    /**
     * @var string Database name
     */
    private $dbname;
    
    /**
     * @var string Database username
     */
    private $user;
    
    /**
     * @var string Database password
     */
    private $pass;
    
    /**
     * @var string Database charset
     */
    private $charset;
    
    /**
     * @var PDO PDO instance
     */
    private $conn;
    
    /**
     * @var string Error message
     */
    private $error;
    
    /**
     * @var Database Singleton instance
     */
    private static $instance = null;

    /**
     * Constructor - Initialize database connection
     */
    private function __construct()
    {
        $this->host = DB_HOST;
        $this->dbname = DB_NAME;
        $this->user = DB_USER;
        $this->pass = DB_PASS;
        $this->charset = DB_CHARSET;
        
        $this->connect();
    }

    /**
     * Get singleton instance
     * 
     * @return Database
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Establish PDO connection
     * 
     * @throws PDOException
     */
    private function connect()
    {
        // Parse host and port
        $hostParts = explode(':', $this->host);
        $host = $hostParts[0];
        $port = isset($hostParts[1]) ? (int)$hostParts[1] : 3306;

        // Build DSN
        $dsn = "mysql:host={$host};port={$port};dbname={$this->dbname};charset={$this->charset}";
        
        // PDO options
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_PERSISTENT => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$this->charset}",
            PDO::ATTR_STRINGIFY_FETCHES => false,
        ];

        try {
            $this->conn = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            throw new PDOException("Connection failed: " . $this->error);
        }
    }

    /**
     * Get PDO connection
     * 
     * @return PDO
     */
    public function getConnection()
    {
        return $this->conn;
    }

    /**
     * Prepare and execute a query with parameters
     * 
     * @param string $sql SQL query with placeholders
     * @param array $params Parameters to bind
     * @return PDOStatement
     * @throws PDOException
     */
    public function query($sql, $params = [])
    {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            throw new PDOException('Query error: ' . $e->getMessage() . ' SQL: ' . $sql);
        }
    }

    /**
     * Fetch all rows
     * 
     * @param string $sql SQL query with placeholders
     * @param array $params Parameters to bind
     * @return array
     * @throws PDOException
     */
    public function fetchAll($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Fetch single row
     * 
     * @param string $sql SQL query with placeholders
     * @param array $params Parameters to bind
     * @return array|false
     * @throws PDOException
     */
    public function fetchOne($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Fetch single column value
     * 
     * @param string $sql SQL query with placeholders
     * @param array $params Parameters to bind
     * @param int $column Column index
     * @return mixed
     * @throws PDOException
     */
    public function fetchColumn($sql, $params = [], $column = 0)
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchColumn($column);
    }

    /**
     * Insert data into table
     * 
     * @param string $table Table name
     * @param array $data Associative array of column => value
     * @return int Last insert ID
     * @throws PDOException
     */
    public function insert($table, $data)
    {
        $fields = array_keys($data);
        $placeholders = ':' . implode(', :', $fields);

        $sql = "INSERT INTO {$table} (" . implode(', ', $fields) . ") 
                VALUES ({$placeholders})";

        $this->query($sql, $data);
        return (int)$this->conn->lastInsertId();
    }

    /**
     * Update data in table
     * 
     * @param string $table Table name
     * @param array $data Associative array of column => value
     * @param string $where WHERE clause with placeholders
     * @param array $whereParams Parameters for WHERE clause
     * @return int Number of affected rows
     * @throws PDOException
     */
    public function update($table, $data, $where, $whereParams = [])
    {
        $fields = array_map(function ($field) {
            return "{$field} = :{$field}";
        }, array_keys($data));

        $sql = "UPDATE {$table} SET " . implode(', ', $fields) . " WHERE {$where}";
        $params = array_merge($data, $whereParams);

        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }

    /**
     * Delete records from table
     * 
     * @param string $table Table name
     * @param string $where WHERE clause with placeholders
     * @param array $params Parameters for WHERE clause
     * @return int Number of affected rows
     * @throws PDOException
     */
    public function delete($table, $where, $params = [])
    {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }

    /**
     * Begin transaction
     * 
     * @return bool
     */
    public function beginTransaction()
    {
        return $this->conn->beginTransaction();
    }

    /**
     * Commit transaction
     * 
     * @return bool
     */
    public function commit()
    {
        return $this->conn->commit();
    }

    /**
     * Rollback transaction
     * 
     * @return bool
     */
    public function rollback()
    {
        return $this->conn->rollBack();
    }

    /**
     * Check if inside transaction
     * 
     * @return bool
     */
    public function inTransaction()
    {
        return $this->conn->inTransaction();
    }

    /**
     * Get last insert ID
     * 
     * @param string $name Name of the sequence object
     * @return int|string
     */
    public function lastInsertId($name = null)
    {
        return $this->conn->lastInsertId($name);
    }

    /**
     * Quote a string for use in SQL
     * 
     * @param string $string String to quote
     * @return string
     */
    public function quote($string)
    {
        return $this->conn->quote($string);
    }

    /**
     * Get error info
     * 
     * @return array
     */
    public function errorInfo()
    {
        return $this->conn->errorInfo();
    }

    /**
     * Get error code
     * 
     * @return string|null
     */
    public function errorCode()
    {
        return $this->conn->errorCode();
    }

    /**
     * Get PDO attribute
     * 
     * @param int $attribute Attribute constant
     * @return mixed
     */
    public function getAttribute($attribute)
    {
        return $this->conn->getAttribute($attribute);
    }

    /**
     * Set PDO attribute
     * 
     * @param int $attribute Attribute constant
     * @param mixed $value Value
     * @return bool
     */
    public function setAttribute($attribute, $value)
    {
        return $this->conn->setAttribute($attribute, $value);
    }

    /**
     * Prevent cloning
     */
    private function __clone() {}

    /**
     * Prevent unserialization
     */
    public function __wakeup() {}
}