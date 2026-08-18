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
$routes->get('admin/residents/details/(:num)', 'DashboardController::residentDetails/$1', ['filter' => 'admin']);
$routes->get('admin/categories', 'DashboardController::categories', ['filter' => 'admin']);
$routes->get('admin/notifications', 'DashboardController::notificationsAdmin', ['filter' => 'admin']);
$routes->get('admin/notifications/open/(:num)', 'DashboardController::openAdminNotification/$1', ['filter' => 'admin']);
$routes->get('admin/settings', 'DashboardController::settings', ['filter' => 'admin']);
$routes->post('admin/settings/save', 'DashboardController::saveSettings', ['filter' => 'admin']);
$routes->get('admin/account', 'DashboardController::account', ['filter' => 'admin']);
$routes->get('admin/announcements', 'DashboardController::announcements', ['filter' => 'admin']);
$routes->post('admin/announcements/create', 'DashboardController::createAnnouncement', ['filter' => 'admin']);
$routes->get('admin/map', 'DashboardController::map', ['filter' => 'admin']);
$routes->post('admin/reports/update-status', 'ReportController::updateStatus', ['filter' => 'admin']);

// RESIDENT - protected
$routes->get('resident/dashboard', 'DashboardController::resident', ['filter' => 'resident']);
$routes->get('resident/report', 'DashboardController::report', ['filter' => 'resident']);
$routes->post('resident/report', 'ReportController::create', ['filter' => 'resident']);
$routes->post('resident/report/delete/(:num)', 'ReportController::delete/$1', ['filter' => 'resident']);
$routes->get('resident/my-reports', 'DashboardController::myReports', ['filter' => 'resident']);
$routes->get('resident/report/edit/(:num)', 'ReportController::edit/$1', ['filter' => 'resident']);
$routes->post('resident/report/edit/(:num)', 'ReportController::updateResidentReport/$1', ['filter' => 'resident']);
$routes->get('resident/notifications', 'DashboardController::notifications', ['filter' => 'resident']);
$routes->get('resident/notifications/open/(:num)', 'DashboardController::openResidentNotification/$1', ['filter' => 'resident']);
$routes->get('resident/profile', 'DashboardController::profile', ['filter' => 'resident']);
$routes->post('resident/profile/update', 'DashboardController::updateProfile', ['filter' => 'resident']);
$routes->post('resident/account/delete', 'DashboardController::deleteResidentAccount', ['filter' => 'resident']);
$routes->get('resident/report-details', 'DashboardController::reportDetails', ['filter' => 'resident']);
$routes->get('resident/report-details/(:num)', 'DashboardController::reportDetails/$1', ['filter' => 'resident']);

// CRUD locations/reports
$routes->get('api/locations', 'Locations::index');
$routes->post('api/locations', 'Locations::create');
$routes->put('api/locations/(:num)', 'Locations::update/$1');
$routes->delete('api/locations/(:num)', 'Locations::delete/$1');
