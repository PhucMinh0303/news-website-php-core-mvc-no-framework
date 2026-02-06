<?php

return [
    // Web Routes
    'GET /' => 'Web\HomeController@index',
    'GET /dashboard' => 'Web\DashboardController@index',
    'GET /products' => 'Web\ProductController@index',
    'GET /products/create' => 'Web\ProductController@create',
    
    // API Routes
    'GET /api/users' => 'Api\UserController@index',
    'GET /api/users/{id}' => 'Api\UserController@show',
    'POST /api/users' => 'Api\UserController@store',
    'PUT /api/users/{id}' => 'Api\UserController@update',
    'DELETE /api/users/{id}' => 'Api\UserController@destroy',
    
    'GET /api/products' => 'Api\ProductController@index',
    'GET /api/products/{id}' => 'Api\ProductController@show',
    'POST /api/products' => 'Api\ProductController@store',
    'PUT /api/products/{id}' => 'Api\ProductController@update',
    'DELETE /api/products/{id}' => 'Api\ProductController@destroy',
];