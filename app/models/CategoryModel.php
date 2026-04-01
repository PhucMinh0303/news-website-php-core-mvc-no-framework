<?php
require_once __DIR__ . '/BaseModel.php';

class CategoryModel extends BaseModel
{

    // Lấy tất cả danh mục
    public function getAllCategories()
    {
        $sql = "SELECT * FROM categories ORDER BY name ASC";
        return $this->select($sql);
    }

    // Lấy danh mục theo slug
    public function getCategoryBySlug($slug)
    {
        $sql = "SELECT * FROM categories WHERE slug = ?";
        return $this->selectOne($sql, [$slug], "s");
    }

    // Lấy danh mục theo ID
    public function getCategoryById($id)
    {
        $sql = "SELECT * FROM categories WHERE id = ?";
        return $this->selectOne($sql, [$id], "i");
    }

    // Lấy danh mục có tin tức
    public function getCategoriesWithNews()
    {
        $sql = "SELECT DISTINCT c.*, COUNT(n.id) as news_count
                FROM categories c
                LEFT JOIN news n ON c.id = n.category_id AND n.status = 'published'
                GROUP BY c.id
                HAVING news_count > 0
                ORDER BY c.name ASC";

        return $this->select($sql);
    }
}

?>