<?php
/**
 * Product Model - Handles product data
 */

class Product extends Model {
    
    protected static $data = [
        [
            'id' => 1,
            'slug' => 'asset-management',
            'title' => 'Quản lý tài sản',
            'description' => 'Chúng tôi cung cấp các giải pháp quản lý tài sản toàn diện',
        ],
        [
            'id' => 2,
            'slug' => 'portfolio-management',
            'title' => 'Quản lý danh mục đầu tư',
            'description' => 'Capital AM thiết kế và vận hành các danh mục đầu tư',
        ],
        [
            'id' => 3,
            'slug' => 'business-management',
            'title' => 'Tư vấn quản lý kinh doanh',
            'description' => 'Dịch vụ tư vấn quản lý kinh doanh toàn diện',
        ],
        [
            'id' => 4,
            'slug' => 'm&a-restructuring',
            'title' => 'M&A và tái cấu trúc doanh nghiệp',
            'description' => 'Dịch vụ M&A và tái cấu trúc doanh nghiệp',
        ],
        [
            'id' => 5,
            'slug' => 'm&a-project',
            'title' => 'Tư vấn dự án M&A',
            'description' => 'Tư vấn chuyên sâu về dự án M&A',
        ],
    ];
    
    /**
     * Get product by slug
     */
    public static function getBySlug($slug) {
        return static::getBy('slug', $slug);
    }
}

?>
