<?php
// routes/web_routes.php

$router = new Router();

// Admin routes
$router->addRoute('admin/recruitment', 'AdminRecruitmentController@index');
$router->addRoute('admin/recruitment/create', 'AdminRecruitmentController@create');
$router->addRoute('admin/recruitment/store', 'AdminRecruitmentController@store');
$router->addRoute('admin/recruitment/@slug', 'AdminRecruitmentController@edit');
$router->addRoute('admin/recruitment/edit/@id', 'AdminRecruitmentController@edit');
$router->addRoute('admin/recruitment/update/@id', 'AdminRecruitmentController@update');
$router->addRoute('admin/recruitment/delete/@id', 'AdminRecruitmentController@delete');
$router->addRoute('admin/recruitment/toggle-status/@id', 'AdminRecruitmentController@toggleStatus');

// User routes
$router->addRoute('recruitment', 'RecruitmentController@index');
$router->addRoute('recruitment/apply', 'RecruitmentController@apply');
$router->addRoute('recruitment/@slug', 'RecruitmentController@show');

// Contact form and admin inbox
$router->addRoute('contact', 'ContactController@index');
$router->addRoute('contact/submit', 'ContactController@send');
$router->addRoute('contact/send', 'ContactController@send');
$router->addRoute('admin/contact', 'AdminContactController@index');

// ... các route khác