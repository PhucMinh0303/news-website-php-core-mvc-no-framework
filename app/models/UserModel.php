<?php

require_once __DIR__ . '/../core/Database.php';

class UserModel
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Lấy tất cả user
    public function getUsers()
    {
        $query = "SELECT * FROM users";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy user theo id
    public function getUser($id)
    {
        $query = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Thêm user
    public function create($name, $email)
    {
        $stmt = $this->conn->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
        if ($stmt->execute([$name, $email])) {
            return $this->conn->lastInsertId();
        }
        return false;
    }
}
