<?php
/**
 * Bootstrap - Initializes the MVC Application
 */

// Load configuration (use __DIR__ so config.php can define APP_PATH)
require_once __DIR__ . '/config/config.php';

// Autoloader for classes
spl_autoload_register(function($class) {
    // Try to load from core
    $coreFile = APP_PATH . 'core/' . $class . '.php';
    if (file_exists($coreFile)) {
        require_once $coreFile;
        return;
    }
    
    // Try to load from controllers
    $controllerFile = APP_PATH . 'controllers/' . $class . '.php';
    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        return;
    }
    
    // Try to load from models
    $modelFile = APP_PATH . 'models/' . $class . '.php';
    if (file_exists($modelFile)) {
        require_once $modelFile;
        return;
    }
    // Try to load from views
    $viewFile = APP_PATH . 'views/' . $class . '.php';
    if (file_exists($viewFile)) {
        require_once $viewFile;
        return;
    }
});

// Load all core classes
require_once APP_PATH . 'core/Router.php';
require_once APP_PATH . 'core/Controller.php';
require_once APP_PATH . 'core/Model.php';
require_once APP_PATH . 'core/View.php';

?>
