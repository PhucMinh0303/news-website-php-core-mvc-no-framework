<?php
/**
 * Front Controller - MVC Application Entry Point
 * All requests are routed through this file
 */

// Load application bootstrap Vdt@#2023
require_once 'app/bootstrap.php';

require_once 'app/config/config.php';
// Models
require_once 'app/Core/Database.php';
require_once 'app/Models/User_model.php';
// Core
require_once 'app/core/App.php';
require_once 'app/core/Controller.php';
require_once 'app/core/Router.php';
require_once 'app/core/View.php';

// Controllers
require_once 'app/controllers/HomeController.php';
require_once 'app/controllers/PageController.php';
require_once 'app/controllers/NewsController.php';
require_once 'app/controllers/ContactController.php';
require_once 'app/controllers/NotFoundController.php';
require_once 'app/controllers/UserController.php';

// Create router instance
$router = new Router();


// Get the route for the current request
$route = $router->route();

// Extract route information
$controllerName = $route['controller'] ?? 'NotFoundController';
$actionName = $route['action'] ?? 'index';
$params = $route['params'] ?? [];

try {
    // Check if controller exists
    if (!class_exists($controllerName)) {
        throw new Exception("Controller not found: {$controllerName}");
    }
    
    // Instantiate controller
    $controller = new $controllerName();
    
    // Check if action method exists
    if (!method_exists($controller, $actionName)) {
        throw new Exception("Action not found: {$controllerName}@{$actionName}");
    }
    
    // Call the action with parameters
    call_user_func_array([$controller, $actionName], $params);
    
    // Output the view
    $controller->output();
    
} catch (Exception $e) {
    // Error handling
    if (DEBUG) {
        echo '<pre>';
        echo 'Error: ' . $e->getMessage() . "\n";
        echo 'File: ' . $e->getFile() . "\n";
        echo 'Line: ' . $e->getLine();
        echo '</pre>';
    } else {
        // Show user-friendly error
        http_response_code(500);
        echo 'An error occurred. Please try again later.';
    }
}
if (!class_exists($controllerName)) {
    $controllerName = 'NotFoundController';
    $actionName = 'index';
}

$controller = new $controllerName();

if (!method_exists($controller, $actionName)) {
    $actionName = 'index';
}

call_user_func_array(
    [$controller, $actionName],
    $params
);

?>
