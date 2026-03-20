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
    public function annualReport() {
        $this->setPageTitle('Báo cáo thường niên');
        $this->setData('sections', [
            'product-service-section',
            'annual-report'
        ]);
        $this->render('investor&relations/annual-report');
    }
    public function corporateGovernance() {
        $this->setPageTitle('Quản trị doanh nghiệp');
        $this->setData('sections', [
            'product-service-section',
            'corporate-governance'
        ]);
        $this->render('investor-relations/corporate-governance');
    }
    public function financialInformation() {
        $this->setPageTitle('Thông tin tài chính');
        $this->setData('sections', [
            'product-service-section',
            'financial-information'
        ]);
        $this->render('investor-relations/financial-information');
    }
    public function informationDisclosure() {
        $this->setPageTitle('Thông tin cổ đông');
        $this->setData('sections', [
            'product-service-section',
            'information-disclosure'
        ]);
        $this->render('investor-relations/information-disclosure');
    }
    public function investorRelations() {
        $this->setPageTitle('Quan hệ nhà đầu tư');
        $this->setData('sections', [
            'product-service-section',
            'investor-relations'
        ]);
        $this->render('investor-relations/investor-relations');
    }
    public function shareholderInformation() {
        $this->setPageTitle('Thông tin cổ đông');
        $this->setData('sections', [
            'product-service-section',
            'shareholder-information'
        ]);
        $this->render('investor-relations/shareholder-information');    
    }
    
}

?>
