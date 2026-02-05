<?php
/**
 * Product Controller - Handles product/service pages
 */

class ProductController extends Controller {
    
    public function assetManagement() {
        $this->setPageTitle('Quản lý tài sản');
        $this->setData('sections', [
            'product-service-section',
            'asset-manegerment'
        ]);
        $this->render('product&service/asset-manegerment');
    }
    
    public function portfolioManagement() {
        $this->setPageTitle('Quản lý danh mục đầu tư');
        $this->setData('sections', [
            'product-service-section',
            'portfolio-management'
        ]);
        $this->render('product&service/portfolio-management');
    }
    
    public function businessManagement() {
        $this->setPageTitle('Tư vấn quản lý kinh doanh');
        $this->setData('sections', [
            'product-service-section',
            'Business-management-consulting'
        ]);
        $this->render('product&service/Business-management-consulting');
    }
    
    public function maRestructuring() {
        $this->setPageTitle('M&A và tái cấu trúc doanh nghiệp');
        $this->setData('sections', [
            'product-service-section',
            'M&A-Project-Consulting'
        ]);
        $this->render('product&service/M&A-Project-Consulting');
    }
    
    public function maProject() {
        $this->setPageTitle('Tư vấn dự án M&A');
        $this->setData('sections', [
            'product-service-section',
            'M&A-and-corporate-restructuring'
        ]);
        $this->render('product&service/M&A-and-corporate-restructuring');
    }
}

?>
