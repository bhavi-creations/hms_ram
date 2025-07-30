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

// New: Top-level route for Discharged Patients List
// This matches the sidebar link base_url('discharged-patients')
$routes->get('/discharged-patients', 'Patients::dischargedPatients', ['filter' => 'auth']);


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
    // Removed the nested 'discharged' route here as it's now top-level
    // $routes->get('discharged', 'Patients::dischargedPatients');
});

// New: General Management Routes (now in its own controller)
$routes->group('general', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'GeneralController::index');
});


$routes->group('opd', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'OpdController::index');
});

// New: IPD Management Routes
$routes->group('ipd', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'IpdController::index');
    $routes->post('removeFromIPD', 'IpdController::removeFromIPD');
    $routes->post('dischargePatient', 'IpdController::dischargePatient');

    // --- Critical New Routes for Ward/Bed Assignment AJAX ---
    $routes->get('getAvailableBedsByWard/(:num)', 'IpdController::getAvailableBedsByWard/$1');
    $routes->get('getPatientAdmissionDetails/(:num)', 'IpdController::getPatientAdmissionDetails/$1');

    // Simplified POST route for assignWardBed
    $routes->post('assignWardBed', 'IpdController::assignWardBed');
    // --- END Critical New Routes ---
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

// Assets & Equipment Routes
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

// --- NEW HOSPITAL RESOURCES ROUTES: WARDS ---
$routes->group('wards', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Wards::index');
    $routes->get('create', 'Wards::create');
    $routes->post('store', 'Wards::store');
    $routes->get('edit/(:num)', 'Wards::edit/$1');
    $routes->post('update/(:num)', 'Wards::update/$1');
    $routes->get('delete/(:num)', 'Wards::delete/$1');
    $routes->get('getWards', 'Wards::getWards');
});

// --- NEW HOSPITAL RESOURCES ROUTES: BEDS ---
$routes->group('beds', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Beds::index');
    // Route for filtering beds by ward
    $routes->get('filter/(:num)', 'Beds::filter/$1');
    // Route for updating bed status via AJAX
    $routes->post('updateStatus/(:num)', 'Beds::updateStatus/$1');
    $routes->get('getBedDetails/(:num)', 'Beds::getBedDetails/$1');
});











// Pharmacy Routes


$routes->group('pharmacy', ['namespace' => 'App\Controllers\Pharmacy'], function ($routes) {

    // Pharmacy Dashboard
    $routes->get('/', 'Dashboard::index');
    $routes->get('dashboard', 'Dashboard::index'); // Alias for /pharmacy

    // Medicine Management (PharmacyMedicinesController)
    $routes->get('medicines', 'Medicines::index');
    $routes->get('medicines/create', 'Medicines::create');
    $routes->post('medicines/store', 'Medicines::store');
    $routes->get('medicines/edit/(:num)', 'Medicines::edit/$1');
    $routes->post('medicines/update/(:num)', 'Medicines::update/$1');
    $routes->post('medicines/delete/(:num)', 'Medicines::delete/$1'); // Using POST for delete for safety

    // Medicine Batches
    $routes->get('medicines/batches/(:num)', 'Medicines::batches/$1');
    $routes->get('medicines/add-batch/(:num)', 'Medicines::addBatch/$1');
    $routes->post('medicines/store-batch', 'Medicines::storeBatch');
    $routes->get('medicines/edit-batch/(:num)', 'Medicines::editBatch/$1');
    $routes->post('medicines/update-batch/(:num)', 'Medicines::updateBatch/$1');

    // Stock Adjustments
    $routes->match(['get', 'post'], 'medicines/adjust-stock', 'Medicines::adjustStock'); // Handles both form display and submission

    // Sales Management (PharmacySalesController)
    $routes->get('sales', 'Sales::index'); // POS Panel
    $routes->post('sales/process', 'Sales::processSale');
    $routes->get('sales/invoice/(:num)', 'Sales::invoice/$1');
    $routes->get('sales/list', 'Sales::listSales'); // Sales History/Reports view

    // Purchases Management (PharmacyPurchasesController)
    $routes->get('purchases', 'Purchases::index');
    $routes->get('purchases/create', 'Purchases::create');
    $routes->post('purchases/store', 'Purchases::store');
    $routes->get('purchases/view/(:num)', 'Purchases::view/$1');
    $routes->match(['get', 'post'], 'purchases/receive-stock/(:num)', 'Purchases::receiveStock/$1'); // Handles stock receiving

    // Supplier Management (PharmacySuppliersController)
    $routes->get('suppliers', 'Suppliers::index');
    $routes->get('suppliers/create', 'Suppliers::create');
    $routes->post('suppliers/store', 'Suppliers::store');
    $routes->get('suppliers/edit/(:num)', 'Suppliers::edit/$1');
    $routes->post('suppliers/update/(:num)', 'Suppliers::update/$1');
    $routes->post('suppliers/delete/(:num)', 'Suppliers::delete/$1');

    // Returns Management (PharmacyReturnsController)
    $routes->get('returns', 'Returns::index'); // List pending/all returns
    $routes->get('returns/create', 'Returns::create'); // Initiate a return
    $routes->post('returns/store', 'Returns::store');
    $routes->get('returns/approve/(:num)', 'Returns::approve/$1'); // Manager approval form
    $routes->post('returns/process-approval/(:num)', 'Returns::processApproval/$1');

    // Reports (PharmacyReportsController)
    $routes->get('reports', 'Reports::index');
    $routes->get('reports/sales', 'Reports::sales');
    $routes->get('reports/stock', 'Reports::stock');
    $routes->get('reports/expiry', 'Reports::expiry');
    $routes->get('reports/purchases', 'Reports::purchases');

    // Category Management (PharmacyCategoriesController - if kept separate)
    $routes->get('categories', 'Categories::index');
    $routes->get('categories/create', 'Categories::create');
    $routes->post('categories/store', 'Categories::store');
    $routes->get('categories/edit/(:num)', 'Categories::edit/$1');
    $routes->post('categories/update/(:num)', 'Categories::update/$1');
    $routes->post('categories/delete/(:num)', 'Categories::delete/$1');
});


 
$routes->group('api', function($routes){
    $routes->get('get-doctors', 'Api::getDoctors'); // Maps /api/get-doctors to Api controller's getDoctors method
});

$routes->group('pharmacy', function($routes){
    $routes->get('medicines/get-batches-by-medicine/(:num)', 'Pharmacy\Medicine::getBatchesByMedicine/$1');
    // Make sure this route is correct based on your controller structure
});