<?php

require_once __DIR__ . '/../app/core/App.php';

use App\Core\App;

header('Content-Type: application/json; charset=utf-8');

$app = new App();
$app->runApi();
