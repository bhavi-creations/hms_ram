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

    // Centralized route for admitting patients to IPD (within the patients group)
    $routes->post('admitToIPD', 'Patients::admitToIPD');


});

// New: General Management Routes (now in its own controller)
$routes->group('general', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'GeneralController::index'); // Points to the new GeneralController
});


$routes->group('opd', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'OpdController::index');
    // Removed: $routes->post('admitToIpd/(:num)', 'OpdController::admitToIpd/$1');
});

// New: IPD Management Routes
$routes->group('ipd', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'IpdController::index');
    // Add more IPD specific routes here later (e.g., assign bed, view daily notes, discharge)

    // <--- ADDED THESE NEW ROUTES ---
    $routes->post('removeFromIPD', 'IpdController::removeFromIPD');
    $routes->post('dischargePatient', 'IpdController::dischargePatient');
    // <--- END ADDED ROUTES ---
});

// New: Casualty Management Routes
$routes->group('casualty', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'CasualtyController::index');
    // Removed: $routes->post('admitToIpd/(:num)', 'CasualtyController::admitToIpd/$1');
});






// Doctors Routes
 
$routes->get('doctors', 'Doctors::index');
$routes->get('doctors/new', 'Doctors::new');
$routes->post('doctors/save', 'Doctors::save');
$routes->post('doctors/delete/(:num)', 'Doctors::delete/$1'); // Route for deleting entire doctor record (if that's its purpose)

$routes->get('doctors/edit/(:num)', 'Doctors::edit/$1');
$routes->get('doctors/view/(:num)', 'Doctors::view/$1');

$routes->post('doctors/delete_document_ajax', 'Doctors::deleteDocumentAjax');
// $routes->post('doctors/delete_document/(:num)', 'Doctors::deleteDocument/$1');