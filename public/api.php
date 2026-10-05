<?php
require_once '../app/bootstrap.php';

$router = new Router();
require_once '../routes/api.php';

$router->route();
