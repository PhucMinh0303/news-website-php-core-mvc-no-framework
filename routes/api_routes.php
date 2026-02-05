<?php

use App\Controllers\Api\NewsApiController;

$router->get('/news', [NewsApiController::class, 'index']);
$router->get('/news/{id:\d+}', [NewsApiController::class, 'show']);
Router::get('/api/news', 'Api\NewsApiController@index');
Router::get('/api/news/{id}', 'Api\NewsApiController@show');
Router::post('/api/news', 'Api\NewsApiController@store');
