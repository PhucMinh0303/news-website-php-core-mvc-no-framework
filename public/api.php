<?php
require_once '../app/bootstrap.php';

use App\Core\Router;

$router = new Router();
require_once '../routes/api.php';

$router->dispatch();
