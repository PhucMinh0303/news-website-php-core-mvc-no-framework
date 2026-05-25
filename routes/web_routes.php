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

return $router;