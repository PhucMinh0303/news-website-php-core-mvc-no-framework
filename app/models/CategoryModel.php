<?php
// models/CategoryModel.php

require_once '../core/Model.php';

class CategoryModel extends Model
{
    protected $table = 'categories';

    public function __construct()
    {
        parent::__construct();
    }

    public function findAll()
    {
        $stmt = $this->db->prepare("SELECT * FROM categories ORDER BY name");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function findBySlug($slug)
    {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE slug = :slug");
        $stmt->execute(['slug' => $slug]);
        return $stmt->fetch();
    }

    public function getWithNewsCount()
    {
        $sql = "SELECT c.*, COUNT(n.id) as news_count 
                FROM categories c 
                LEFT JOIN news n ON c.id = n.category_id AND n.status = 'published' 
                GROUP BY c.id 
                ORDER BY c.name";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}