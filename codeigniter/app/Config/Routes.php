<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

$routes->get('login', 'AuthController::showLogin');
$routes->post('login', 'AuthController::login');

$routes->get('register', 'AuthController::register');
$routes->post('register', 'AuthController::registerSubmit');

$routes->get('logout', 'AuthController::logout');


// ADMIN
$routes->get('admin/dashboard', 'DashboardController::admin', ['filter' => 'admin']);
$routes->get('admin/reports', 'DashboardController::reports', ['filter' => 'admin']);
$routes->get('admin/residents', 'DashboardController::residents', ['filter' => 'admin']);
$routes->get('admin/categories', 'DashboardController::categories', ['filter' => 'admin']);
$routes->get('admin/notifications', 'DashboardController::notificationsAdmin', ['filter' => 'admin']);
$routes->get('admin/settings', 'DashboardController::settings', ['filter' => 'admin']);
$routes->get('admin/account', 'DashboardController::account', ['filter' => 'admin']);
$routes->get('admin/announcements', 'DashboardController::announcements', ['filter' => 'admin']);
$routes->get('admin/map', 'DashboardController::map', ['filter' => 'admin']);

// RESIDENT - protected
$routes->get('resident/dashboard', 'DashboardController::resident', ['filter' => 'resident']);
$routes->get('resident/report', 'DashboardController::report', ['filter' => 'resident']);
$routes->post('resident/report', 'ReportController::create', ['filter' => 'resident']);
$routes->get('resident/my-reports', 'DashboardController::myReports', ['filter' => 'resident']);
$routes->get('resident/notifications', 'DashboardController::notifications', ['filter' => 'resident']);
$routes->get('resident/profile', 'DashboardController::profile', ['filter' => 'resident']);
$routes->get('resident/report-details', 'DashboardController::reportDetails', ['filter' => 'resident']);


// CRUD locations/reports
$routes->get('api/locations', 'Locations::index');
$routes->post('api/locations', 'Locations::create');
$routes->put('api/locations/(:num)', 'Locations::update/$1');
$routes->delete('api/locations/(:num)', 'Locations::delete/$1');

