<?php
/**
 * Page Controller - Handles static pages
 */

class PageController extends Controller {
    
    public function introduction() {
        $this->setPageTitle('Giới thiệu');
        $this->setData('page', 'introduction');
        $this->render('Introduction/Introduction');
        
    }
    
    public function assetManagement() {
        $this->setPageTitle('Quản lý tài sản');
        $this->setData('pages', [
            'product-service-section',
            'asset-manegerment'
        ]);
        $this->render('product&service/asset-manegerment');
    }
    
    public function portfolioManagement() {
        $this->setPageTitle('Quản lý danh mục đầu tư');
        $this->setData('pages', [
            'product-service-section',
            'portfolio-management'
        ]);
        $this->render('product&service/portfolio-management');
    }
    
    public function businessManagement() {
        $this->setPageTitle('Tư vấn quản lý kinh doanh');
        $this->setData('pages', [
            'product-service-section',
            'Business-management-consulting'
        ]);
        $this->render('product&service/Business-management-consulting');
    }
    
    public function maRestructuring() {
        $this->setPageTitle('M&A và tái cấu trúc doanh nghiệp');
        $this->setData('pages', [
            'product-service-section',
            'M&A-Project-Consulting'
        ]);
        $this->render('product&service/M&A-Project-Consulting');
    }
    
    public function maProject() {
        $this->setPageTitle('Tư vấn dự án M&A');
        $this->setData('pages', [
            'product-service-section',
            'M&A-and-corporate-restructuring'
        ]);
        $this->render('product&service/M&A-and-corporate-restructuring');
    }
    //
    public function investorRelations() {
        $this->setPageTitle('Quan hệ nhà đầu tư');
        $this->setData('page', 'investor-relations');
        $this->render('pages/investor-relations');
    }
    
    public function financialInformation() {
        $this->setPageTitle('Thông tin tài chính');
        $this->setData('page', 'financial-information');
        $this->render('pages/financial-information');
    }
    
    public function annualReport() {
        $this->setPageTitle('Báo cáo thường niên');
        $this->setData('page', 'annual-report');
        $this->render('pages/annual-report');
    }
    
    public function informationDisclosure() {
        $this->setPageTitle('Công bố thông tin');
        $this->setData('page', 'information-disclosure');
        $this->render('pages/information-disclosure');
    }
    
    public function shareholderInformation() {
        $this->setPageTitle('Thông tin cổ đông');
        $this->setData('page', 'shareholder-information');
        $this->render('pages/shareholder-information');
    }
    
    public function corporateGovernance() {
        $this->setPageTitle('Quản trị doanh nghiệp');
        $this->setData('page', 'corporate-governance');
        $this->render('pages/corporate-governance');
    }
}

?>

