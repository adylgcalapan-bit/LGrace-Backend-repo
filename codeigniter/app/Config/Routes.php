<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

$routes->get('/login', 'AuthController::login');
$routes->get('/register', 'AuthController::register');

$routes->get('/admin/dashboard', 'DashboardController::admin');
$routes->get('/admin/reports', 'DashboardController::reports');
$routes->get('/admin/residents', 'DashboardController::residents');
$routes->get('/admin/categories', 'DashboardController::categories');
$routes->get('/admin/notifications', 'DashboardController::notificationsAdmin');
$routes->get('/admin/settings', 'DashboardController::settings');
$routes->get('/admin/account', 'DashboardController::account');
$routes->get('/admin/announcements', 'DashboardController::announcements');
$routes->get('/admin/map', 'DashboardController::map');
$routes->get('/resident/dashboard', 'DashboardController::resident');
$routes->get('/resident/report', 'DashboardController::report');
$routes->get('/resident/my-reports', 'DashboardController::myReports');
$routes->get('/resident/notifications', 'DashboardController::notifications');
$routes->get('/resident/profile', 'DashboardController::profile');
$routes->get('/resident/report-details', 'DashboardController::reportDetails');

// CRUD routes para sa locations
$routes->get('api/locations', 'Locations::index');
$routes->post('api/locations', 'Locations::create');
$routes->put('api/locations/(:num)', 'Locations::update/$1');
$routes->delete('api/locations/(:num)', 'Locations::delete/$1');
