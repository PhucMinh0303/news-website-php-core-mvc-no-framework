<?php
/**
 * Core Router - Maps URL requests to Controllers
 * Part of MVC Framework
 */

class Router {
    private $routes = [];
    private $currentRoute = null;
    
    public function __construct() {
        $this->registerRoutes();
    }
    
    /**
     * Register all application routes
     */
    private function registerRoutes() {
        // Home page
        $this->addRoute('', 'HomeController@index');
        $this->addRoute('/', 'HomeController@index');
        $this->addRoute('home', 'HomeController@index');
        
        // Introduction
        $this->addRoute('introduction', 'PageController@introduction');
        
        // Products/Services
        $this->addRoute('asset-management', 'PageController@assetManagement');
        $this->addRoute('portfolio-management', 'PageController@portfolioManagement');
        $this->addRoute('business-management', 'PageController@businessManagement');
        $this->addRoute('m&a-restructuring', 'PageController@maRestructuring');
        $this->addRoute('m&a-project', 'PageController@maProject');
        
        // News
        $this->addRoute('news', 'NewsController@index');
        $this->addRoute('News/News-title', 'NewsController@show');
        $this->addRoute('news/@id', 'NewsController@detail');
        
        // Recruitment
        $this->addRoute('recruitment', 'RecruitmentController@index');
        
        // Contact
        $this->addRoute('contact', 'ContactController@index');
        $this->addRoute('contact/send', 'ContactController@send');
        
        // Investor Relations pages
        $this->addRoute('investor-relations', 'PageController@investorRelations');
        $this->addRoute('financial-information', 'PageController@financialInformation');
        $this->addRoute('annual-report', 'PageController@annualReport');
        $this->addRoute('information-disclosure', 'PageController@informationDisclosure');
        $this->addRoute('shareholder-information', 'PageController@shareholderInformation');
        $this->addRoute('corporate-governance', 'PageController@corporateGovernance');
    }
    
    /**
     * Add a route
     * @param string $pattern URL pattern
     * @param string $action Controller@method
     */
    public function addRoute($pattern, $action) {
        $this->routes[$pattern] = $action;
    }
    
    /**
     * Route the current request
     * @return array [controller, action, params]
     */
    public function route() {
        $path = $this->getRequestPath();
        
        // Log for debugging
        error_log("Routing path: " . $path);
        
        // Try exact match first
        if (isset($this->routes[$path])) {
            return $this->parseAction($this->routes[$path]);
        }
        
        // Try pattern matching with parameters
        foreach ($this->routes as $pattern => $action) {
            if ($this->matchRoute($pattern, $path)) {
                return $this->parseAction($action, $path, $pattern);
            }
        }
        
        // Try to parse as controller/method if no route found
        return $this->parseDefaultRoute($path);
    }
    
    /**
     * Try to parse default controller/method from path
     */
    private function parseDefaultRoute($path) {
        $parts = explode('/', $path);
        
        if (count($parts) >= 2) {
            $controller = ucfirst($parts[0]) . 'Controller';
            $method = $parts[1];
            $params = array_slice($parts, 2);
            
            return [
                'controller' => $controller,
                'action' => $method,
                'params' => $params
            ];
        }
        
        // Default to 404
        return [
            'controller' => 'NotFoundController',
            'action' => 'index',
            'params' => []
        ];
    }
    
    /**
     * Get the request path from URL
     */
    private function getRequestPath() {
        $path = $_SERVER['REQUEST_URI'] ?? '';
        
        // Remove query string
        if (($pos = strpos($path, '?')) !== false) {
            $path = substr($path, 0, $pos);
        }
        
        // Remove base path
        $basePath = $this->getBasePath();
        if (strpos($path, $basePath) === 0) {
            $path = substr($path, strlen($basePath));
        }
        
        // Remove leading and trailing slashes
        $path = trim($path, '/');
        
        return $path;
    }
    
    /**
     * Get base path from script location
     */
    private function getBasePath() {
        $scriptPath = dirname($_SERVER['SCRIPT_NAME']);
        if ($scriptPath === '\\' || $scriptPath === '.') {
            $scriptPath = '/';
        }
        return rtrim($scriptPath, '/');
    }
    
    /**
     * Check if route pattern matches path
     */
    private function matchRoute($pattern, $path) {
        // Escape special regex chars
        $pattern = preg_quote($pattern, '#');
        
        // Replace parameter placeholders with regex
        $pattern = str_replace('@id', '([0-9]+)', $pattern);
        $pattern = str_replace('@slug', '([a-z0-9-]+)', $pattern);
        
        // Add start and end anchors
        $regex = '#^' . $pattern . '$#i';
        
        return preg_match($regex, $path);
    }
    
    /**
     * Parse action string to controller and method
     */
    private function parseAction($action, $path = '', $pattern = '') {
        list($controller, $method) = explode('@', $action);
        
        // Extract parameters from path
        $params = $this->extractParams($path, $pattern);
        
        return [
            'controller' => $controller,
            'action' => $method,
            'params' => $params
        ];
    }
    
    /**
     * Extract parameters from URL path
     */
    private function extractParams($path, $pattern) {
        $params = [];
        
        if (empty($pattern) || $pattern === $path) {
            return $params;
        }
        
        // Escape pattern for regex
        $regexPattern = preg_quote($pattern, '#');
        
        // Replace placeholders with capture groups
        $regexPattern = str_replace('@id', '([0-9]+)', $regexPattern);
        $regexPattern = str_replace('@slug', '([a-z0-9-]+)', $regexPattern);
        
        if (preg_match('#^' . $regexPattern . '$#i', $path, $matches)) {
            array_shift($matches); // Remove full match
            $params = $matches;
        }
        
        return $params;
    }
    
    /**
     * Get all registered routes
     */
    public function getRoutes() {
        return $this->routes;
    }
    
    /**
     * Redirect to a route
     */
    public static function redirect($path) {
        $baseUrl = defined('BASE_URL') ? BASE_URL : '/';
        header('Location: ' . $baseUrl . $path);
        exit;
    }
    
    /**
     * Generate URL for a route
     */
    public static function url($path, $params = []) {
        $baseUrl = defined('BASE_URL') ? BASE_URL : '/';
        $url = $baseUrl . $path;
        
        if (!empty($params)) {
            $url .= '/' . implode('/', $params);
        }
        
        return $url;
    }
}
?>