<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');


// CRUD routes para sa locations
$routes->get('api/locations', 'Locations::index');
$routes->post('api/locations', 'Locations::create');
$routes->put('api/locations/(:num)', 'Locations::update/$1');
$routes->delete('api/locations/(:num)', 'Locations::delete/$1');
