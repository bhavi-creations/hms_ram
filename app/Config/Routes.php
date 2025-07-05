<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Define the default route to point to the login page
$routes->get('/', 'Auth::login');

// Authentication Routes
$routes->get('/login', 'Auth::login');
$routes->post('/auth/loginAttempt', 'Auth::loginAttempt');
$routes->get('/logout', 'Auth::logout');

// Dashboard route (will be protected)
$routes->get('/dashboard', 'Home::index', ['filter' => 'auth']); // Apply 'auth' filter here




// Patients 

$routes->group('patients', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Patients::index');
    $routes->get('register', 'Patients::register');
    $routes->post('save', 'Patients::save');
    $routes->get('filter', 'Patients::filter');
    $routes->get('view/(:num)', 'Patients::view/$1');
    $routes->get('edit/(:num)', 'Patients::edit/$1');
    $routes->get('delete/(:num)', 'Patients::delete/$1');
    $routes->get('download-report/(:any)', 'Patients::downloadReport/$1');
    $routes->post('deleteReportFile', 'Patients::deleteReportFile');
});





$routes->group('opd', function ($routes) {
    $routes->get('/', 'OpdController::index');
    $routes->post('admitToIpd/(:num)', 'OpdController::admitToIpd/$1');
});

// New: IPD Management Routes
$routes->group('ipd', function ($routes) {
    $routes->get('/', 'IpdController::index');
    // Add more IPD specific routes here later (e.g., assign bed, view daily notes, discharge)
});

// New: Casualty Management Routes
$routes->group('casualty', function ($routes) {
    $routes->get('/', 'CasualtyController::index');
    $routes->post('admitToIpd/(:num)', 'CasualtyController::admitToIpd/$1');
});
