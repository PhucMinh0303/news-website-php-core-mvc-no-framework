<?php

/**
 * Core Router - Maps URL requests to Controllers
 * Part of MVC Framework
 */

class Router
{
    private $routes = [];
    
    public function __construct()
    {
        $this->registerRoutes();
    }

    /**
     * Register all application routes
     */
    private function registerRoutes()
    {
        // Home page
        $this->addRoute('', 'HomeController@index');
        $this->addRoute('/', 'HomeController@index');
        $this->addRoute('home', 'HomeController@index');

        // Admin panel
        $this->addRoute('admin', 'AdminController@index');
        $this->addRoute('admin/menu', 'AdminController@menu');
        $this->addRoute('admin/main', 'AdminController@main');
        $this->addRoute('admin/main/@slug', 'AdminController@main');

        // Admin news routes
        $this->addRoute('admin/news', 'AdminNewsController@index');
        $this->addRoute('admin/news/create', 'AdminNewsController@create');
        $this->addRoute('admin/news/store', 'AdminNewsController@store');
        // Admin recruitment routes
        $this->addRoute('admin/recruitment', 'AdminRecruitmentController@index');
        $this->addRoute('admin/recruitment/create', 'AdminRecruitmentController@create');
        $this->addRoute('admin/recruitment/store', 'AdminRecruitmentController@store');
        // Admin contact management
        $this->addRoute('admin/contact', 'AdminContactController@index');
        // Test database route
        $this->addRoute('admin/test-db', 'AdminController@testDb');

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
        $this->addRoute('recruitment/@slug', 'RecruitmentController@detail');

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
    public function addRoute($pattern, $action)
    {

        $this->routes[$pattern] = $action;
    }

    /**
     * Route the current request
     * @return array [controller, action, params]
     */
    public function route()
    {
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
    private function parseDefaultRoute($path)
    {
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
    private function getRequestPath()
    {
        $path = $_SERVER['REQUEST_URI'] ?? '';

        // Decode URL-encoded characters
        $path = urldecode($path);

        // Remove query string
        if (($pos = strpos($path, '?')) !== false) {
            $path = substr($path, 0, $pos);
        }

        // Remove base path if not at root
        $basePath = $this->getBasePath();
        if (!empty($basePath) && strpos($path, $basePath) === 0) {
            $path = substr($path, strlen($basePath));
        }

        // Remove leading and trailing slashes
        $path = trim($path, '/');

        error_log("Extracted path: " . $path);

        return $path;
    }

    /**
     * Get base path from script location
     */
    private function getBasePath()
    {
        $scriptPath = $_SERVER['SCRIPT_NAME'] ?? '/index.php';

        // Get directory of script
        $basePath = dirname($scriptPath);

        // Normalize to Unix-style forward slashes
        $basePath = str_replace('\\', '/', $basePath);

        // If we're at root (dirname of /index.php is /), return empty
        if ($basePath === '/') {
            return '';
        }

        return rtrim($basePath, '/');
    }

    /**
     * Check if route pattern matches path
     */
    private function matchRoute($pattern, $path)
    {
        // Convert route pattern to regex
        $regexPattern = preg_quote($pattern, '#');

        // Replace parameter placeholders with regex patterns
        $regexPattern = str_replace('\@id', '(?P<id>[0-9]+)', $regexPattern);
        $regexPattern = str_replace('\@slug', '(?P<slug>[a-z0-9-]+)', $regexPattern);

        // Add start and end anchors (case-insensitive)
        $regex = '#^' . $regexPattern . '$#i';

        return preg_match($regex, $path) > 0;
    }

    /**
     * Parse action string to controller and method
     */
    private function parseAction($action, $path = '', $pattern = '')
    {
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
    private function extractParams($path, $pattern)
    {
        $params = [];

        if (empty($pattern) || $pattern === $path) {
            return $params;
        }

        // Build regex pattern
        $regexPattern = preg_quote($pattern, '#');
        $regexPattern = str_replace('\@id', '([0-9]+)', $regexPattern);
        $regexPattern = str_replace('\@slug', '([a-z0-9-]+)', $regexPattern);

        if (preg_match('#^' . $regexPattern . '$#i', $path, $matches)) {
            // Remove full match and keys, keep only values
            array_shift($matches);
            $params = array_values($matches);
        }

        return $params;
    }

    /**
     * Get all registered routes
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Redirect to a route
     */
    public static function redirect($path)
    {
        $baseUrl = defined('BASE_URL') ? BASE_URL : '/';
        header('Location: ' . $baseUrl . $path);
        exit;
    }

    /**
     * Generate URL for a route
     */
    public static function url($path, $params = [])
    {
        $baseUrl = defined('BASE_URL') ? BASE_URL : '/';
        $url = rtrim($baseUrl, '/') . '/' . ltrim($path, '/');

        if (!empty($params)) {
            $url .= '/' . implode('/', $params);
        }

        return $url;
    }
}
