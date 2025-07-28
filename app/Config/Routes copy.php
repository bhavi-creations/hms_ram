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
    $routes->post('admitToIPD', 'Patients::admitToIPD');
    $routes->get('getPatientsByPhone', 'Patients::getPatientsByPhone');
});

// New: General Management Routes (now in its own controller)
$routes->group('general', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'GeneralController::index'); // Points to the new GeneralController
});


$routes->group('opd', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'OpdController::index');
});

// New: IPD Management Routes
$routes->group('ipd', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'IpdController::index');
    $routes->post('removeFromIPD', 'IpdController::removeFromIPD');
    $routes->post('dischargePatient', 'IpdController::dischargePatient');
});

// New: Casualty Management Routes
$routes->group('casualty', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'CasualtyController::index');
});


// Doctors Routes (Admin view of doctors list)
$routes->group('doctors', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Doctors::index');
    $routes->get('new', 'Doctors::new');
    $routes->post('save', 'Doctors::save');
    $routes->post('delete/(:num)', 'Doctors::delete/$1');
    $routes->get('edit/(:num)', 'Doctors::edit/$1');
    $routes->get('view/(:num)', 'Doctors::view/$1');
    $routes->post('delete_document_ajax', 'Doctors::deleteDocumentAjax');
});


// Group for appointment management (Admin/General)
$routes->group('appointments', ['namespace' => 'App\Controllers', 'filter' => 'auth'], function ($routes) {
    $routes->get('/', 'AppointmentController::index');
    $routes->get('create', 'AppointmentController::create');
    $routes->post('store', 'AppointmentController::store');
    $routes->get('edit/(:num)', 'AppointmentController::edit/$1');
    $routes->post('update/(:num)', 'AppointmentController::update/$1');
    $routes->get('delete/(:num)', 'AppointmentController::delete/$1');
    $routes->get('history', 'AppointmentController::history');
});

// Patient-specific appointment routes
$routes->get('patient/appointments', 'AppointmentController::patientAppointments');


// --- HOSPITAL RESOURCES ROUTES ---
// Existing wards group (KEEP THIS EXACTLY AS IS)
// $routes->group('wards', ['filter' => 'auth'], function ($routes) {
//     $routes->get('/', 'WardController::index');
//     $routes->get('create', 'WardController::create');
//     $routes->post('store', 'WardController::store');
//     $routes->get('edit/(:num)', 'WardController::edit/$1');
//     $routes->post('update/(:num)', 'WardController::update/$1');
//     $routes->get('delete/(:num)', 'WardController::delete/$1');
// });

 
// $routes->group('beds', ['filter' => 'auth'], function ($routes) {
//     $routes->get('/', 'BedController::index'); 
//     $routes->get('ward/(:num)', 'BedController::showByWard/$1');  
//     $routes->get('beds/renumber/(:num)', 'BedController::renumberWardBeds/$1');
   
// });
$routes->group('assets', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'AssetController::index');
    $routes->get('create', 'AssetController::create');
    $routes->post('store', 'AssetController::store');
    $routes->get('edit/(:num)', 'AssetController::edit/$1');
    $routes->post('update/(:num)', 'AssetController::update/$1');
    $routes->get('delete/(:num)', 'AssetController::delete/$1');
});
// --- END HOSPITAL RESOURCES ROUTES ---

// --- DOCTOR-SPECIFIC ROUTES ---
$routes->group('doctor', ['filter' => 'auth'], function ($routes) {
    $routes->get('dashboard', 'Home::doctorDashboard');
    $routes->get('appointments', 'AppointmentController::doctorAppointments');
    $routes->get('appointments/view/(:num)', 'AppointmentController::doctorViewAppointment/$1');
    $routes->get('appointments/edit/(:num)', 'AppointmentController::doctorEditAppointment/$1');
    $routes->post('appointments/update/(:num)', 'AppointmentController::doctorUpdateAppointment/$1');
    $routes->get('appointments/history', 'AppointmentController::doctorAppointmentHistory');
    $routes->get('patients', 'Patients::doctorPatientsList');
});
// --- END DOCTOR-SPECIFIC ROUTES ---
