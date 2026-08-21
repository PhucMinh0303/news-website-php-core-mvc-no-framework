<?php
// routes/routes.php

$router = new Router();

// Recruitment routes
$router->addRoute('admin/recruitment', 'AdminRecruitmentController@index');
$router->addRoute('admin/recruitment/create', 'AdminRecruitmentController@create');
$router->addRoute('admin/recruitment/store', 'AdminRecruitmentController@store');
$router->addRoute('admin/recruitment/edit/@id', 'AdminRecruitmentController@edit');
$router->addRoute('admin/recruitment/update/@id', 'AdminRecruitmentController@update');
$router->addRoute('admin/recruitment/delete/@id', 'AdminRecruitmentController@destroy');
$router->addRoute('admin/recruitment/toggle-status/@id', 'AdminRecruitmentController@toggleStatus');
// News routes
$router->addRoute('admin/news', 'AdminNewsController@index');
$router->addRoute('admin/news/create', 'AdminNewsController@create');
$router->addRoute('admin/news/store', 'AdminNewsController@store');
$router->addRoute('admin/news/edit/@id', 'AdminNewsController@edit');
$router->addRoute('admin/news/update/@id', 'AdminNewsController@update');
$router->addRoute('admin/news/delete/@id', 'AdminNewsController@destroy');
$router->addRoute('admin/news/toggle-status/@id', 'AdminNewsController@toggleStatus');

// User routes
$router->addRoute('/recruitment', 'RecruitmentController@index');
$router->addRoute('/recruitment/detail/{id}', 'RecruitmentController@detail');

// Other admin routes
$router->addRoute('admin', 'AdminController@index');

// Contact routes
$router->addRoute('admin/contact', 'AdminContactController@index');

return $router;
