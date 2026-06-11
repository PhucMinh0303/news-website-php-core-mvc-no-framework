<?php
// routes/web.php

$router = new Router();

// Recruitment routes
$router->addRoute('admin/recruitment', 'AdminRecruitmentController@index');
$router->addRoute('admin/main/recruitment/create', 'AdminRecruitmentController@create');
$router->addRoute('admin/main/recruitment/store', 'AdminRecruitmentController@store');
$router->addRoute('admin/main/recruitment/edit/@id', 'AdminRecruitmentController@edit');
$router->addRoute('admin/main/recruitment/update/@id', 'AdminRecruitmentController@update');
$router->addRoute('admin/main/recruitment/delete/@id', 'AdminRecruitmentController@destroy');
$router->addRoute('admin/recruitment/toggle-status/@id', 'AdminRecruitmentController@toggleStatus');
// News routes
$router->addRoute('admin/news', 'AdminNewsController@index');
$router->addRoute('admin/main/news/create', 'AdminNewsController@create');
$router->addRoute('admin/main/news/store', 'AdminNewsController@store');
$router->addRoute('admin/main/news/edit/@id', 'AdminNewsController@edit');
$router->addRoute('admin/main/news/update/@id', 'AdminNewsController@update');
$router->addRoute('admin/main/news/delete/@id', 'AdminNewsController@destroy');
$router->addRoute('admin/main/news/toggle-status/@id', 'AdminNewsController@toggleStatus');


return $router;
