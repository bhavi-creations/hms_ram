<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Define the default route to point to the login page
$routes->GET('/', 'Auth::login');

// Authentication Routes
$routes->GET('/login', 'Auth::login');
$routes->POST('/auth/loginAttempt', 'Auth::loginAttempt');
$routes->GET('/logout', 'Auth::logout');

// Dashboard route (will be protected)
$routes->GET('/dashboard', 'Home::index', ['filter' => 'auth']); // Apply 'auth' filter here

// New: Top-level route for Discharged Patients List
// This matches the sidebar link base_url('discharged-patients')
$routes->GET('/discharged-patients', 'Patients::dischargedPatients', ['filter' => 'auth']);



// Universal Profile Route
$routes->get('profile', 'ProfileController::index');
$routes->get('profile/edit', 'ProfileController::edit');
$routes->post('profile/update', 'ProfileController::update');

// Patients
$routes->group('patients', ['filter' => 'auth'], function ($routes) {
    $routes->GET('/', 'Patients::index');
    $routes->GET('register', 'Patients::register');
    $routes->POST('save', 'Patients::save');
    $routes->GET('filter', 'Patients::filter');
    $routes->GET('view/(:num)', 'Patients::view/$1');
    $routes->GET('edit/(:num)', 'Patients::edit/$1');
    $routes->GET('delete/(:num)', 'Patients::delete/$1');
    $routes->GET('download-report/(:any)', 'Patients::downloadReport/$1');
    $routes->POST('deleteReportFile', 'Patients::deleteReportFile');
    $routes->POST('admitToIPD', 'Patients::admitToIPD');
    $routes->GET('getPatientsByPhone', 'Patients::getPatientsByPhone');
    // Removed the nested 'discharged' route here as it's now top-level
    // $routes->get('discharged', 'Patients::dischargedPatients');
});

// New: General Management Routes (now in its own controller)
$routes->group('general', ['filter' => 'auth'], function ($routes) {
    $routes->GET('/', 'GeneralController::index');
});


$routes->group('opd', ['filter' => 'auth'], function ($routes) {
    $routes->GET('/', 'OpdController::index');
});

// New: IPD Management Routes
$routes->group('ipd', ['filter' => 'auth'], function ($routes) {
    $routes->GET('/', 'IpdController::index');
    $routes->POST('removeFromIPD', 'IpdController::removeFromIPD');
    $routes->POST('dischargePatient', 'IpdController::dischargePatient');

    // --- Critical New Routes for Ward/Bed Assignment AJAX ---
    $routes->GET('getAvailableBedsByWard/(:num)', 'IpdController::getAvailableBedsByWard/$1');
    $routes->GET('getPatientAdmissionDetails/(:num)', 'IpdController::getPatientAdmissionDetails/$1');

    // Simplified POST route for assignWardBed
    $routes->POST('assignWardBed', 'IpdController::assignWardBed');
    // --- END Critical New Routes ---
});

// New: Casualty Management Routes
$routes->group('casualty', ['filter' => 'auth'], function ($routes) {
    $routes->GET('/', 'CasualtyController::index');
});


// Doctors Routes (Admin view of doctors list)
$routes->group('doctors', ['filter' => 'auth'], function ($routes) {
    $routes->GET('/', 'Doctors::index');
    $routes->GET('new', 'Doctors::new');
    $routes->POST('save', 'Doctors::save');
    $routes->POST('delete/(:num)', 'Doctors::delete/$1');
    $routes->GET('edit/(:num)', 'Doctors::edit/$1');
    $routes->GET('view/(:num)', 'Doctors::view/$1');
    $routes->POST('delete_document_ajax', 'Doctors::deleteDocumentAjax');
});


// Group for appointment management (Admin/General)
$routes->group('appointments', ['namespace' => 'App\Controllers', 'filter' => 'auth'], function ($routes) {
    $routes->GET('/', 'AppointmentController::index');
    $routes->GET('create', 'AppointmentController::create');
    $routes->POST('store', 'AppointmentController::store');
    $routes->GET('edit/(:num)', 'AppointmentController::edit/$1');
    $routes->POST('update/(:num)', 'AppointmentController::update/$1');
    $routes->GET('delete/(:num)', 'AppointmentController::delete/$1');
    $routes->GET('history', 'AppointmentController::history');
});

// Patient-specific appointment routes
$routes->GET('patient/appointments', 'AppointmentController::patientAppointments');

// Assets & Equipment Routes
$routes->group('assets', ['filter' => 'auth'], function ($routes) {
    $routes->GET('/', 'AssetController::index');
    $routes->GET('create', 'AssetController::create');
    $routes->POST('store', 'AssetController::store');
    $routes->GET('edit/(:num)', 'AssetController::edit/$1');
    $routes->POST('update/(:num)', 'AssetController::update/$1');
    $routes->GET('delete/(:num)', 'AssetController::delete/$1');
});
// --- END HOSPITAL RESOURCES ROUTES ---

// --- DOCTOR-SPECIFIC ROUTES ---
$routes->group('doctor', ['filter' => 'auth'], function ($routes) {
    $routes->GET('dashboard', 'Home::doctorDashboard');
    $routes->GET('appointments', 'AppointmentController::doctorAppointments');
    $routes->GET('appointments/view/(:num)', 'AppointmentController::doctorViewAppointment/$1');
    $routes->GET('appointments/edit/(:num)', 'AppointmentController::doctorEditAppointment/$1');
    $routes->POST('appointments/update/(:num)', 'AppointmentController::doctorUpdateAppointment/$1');
    $routes->GET('appointments/history', 'AppointmentController::doctorAppointmentHistory');
    $routes->GET('patients', 'Patients::doctorPatientsList');
});
// --- END DOCTOR-SPECIFIC ROUTES ---

// --- NEW HOSPITAL RESOURCES ROUTES: WARDS ---
$routes->group('wards', ['filter' => 'auth'], function ($routes) {
    $routes->GET('/', 'Wards::index');
    $routes->GET('create', 'Wards::create');
    $routes->POST('store', 'Wards::store');
    $routes->GET('edit/(:num)', 'Wards::edit/$1');
    $routes->POST('update/(:num)', 'Wards::update/$1');
    $routes->GET('delete/(:num)', 'Wards::delete/$1');
    $routes->GET('getWards', 'Wards::getWards');
});

// --- NEW HOSPITAL RESOURCES ROUTES: BEDS ---
$routes->group('beds', ['filter' => 'auth'], function ($routes) {
    $routes->GET('/', 'Beds::index');
    // Route for filtering beds by ward
    $routes->GET('filter/(:num)', 'Beds::filter/$1');
    // Route for updating bed status via AJAX
    $routes->POST('updateStatus/(:num)', 'Beds::updateStatus/$1');
    $routes->GET('getBedDetails/(:num)', 'Beds::getBedDetails/$1');
});






$routes->group('pharmacy', ['namespace' => 'App\Controllers\Pharmacy'], function ($routes) {

    // Pharmacy Dashboard
    $routes->GET('/', 'Dashboard::index');
    $routes->GET('dashboard', 'Dashboard::index'); // Alias for /pharmacy





    // Reports (PharmacyReportsController)
    $routes->GET('reports', 'Reports::index');
    $routes->GET('reports/sales', 'Reports::sales');
    $routes->GET('reports/sales/(:any)', 'Reports::sales/$1');
    $routes->GET('reports/stock', 'Reports::stock');
    $routes->GET('reports/expiry', 'Reports::expiry');
    $routes->GET('reports/purchases', 'Reports::purchases');
    $routes->GET('reports/viewInvoice/(:any)', 'Reports::viewInvoice/$1');



    // Salespersons routes
    $routes->GET('salespersons', 'SalesPersons::index');
    $routes->GET('salespersons/create', 'SalesPersons::create');
    $routes->POST('salespersons/store', 'SalesPersons::store');
    $routes->GET('salespersons/edit/(:num)', 'SalesPersons::edit/$1');
    $routes->POST('salespersons/update/(:num)', 'SalesPersons::update/$1');
    $routes->GET('salespersons/delete/(:num)', 'SalesPersons::delete/$1');
    $routes->GET('salespersons/toggle-status/(:num)', 'SalesPersons::toggleStatus/$1');
    $routes->GET('salespersons/profile', 'SalesPersons::profile');
    $routes->GET('salespersons/profile/(:num)', 'SalesPersons::profile/$1');

    // Purchases Management (PharmacyPurchasesController)
    $routes->GET('purchases', 'Purchases::index');
    $routes->GET('purchases/view/(:num)', 'Purchases::view/$1');
    $routes->GET('purchases/bySupplier/(:num)', 'Purchases::bySupplier/$1');
    $routes->GET('purchases/viewBatch/(:num)', 'Purchases::viewBatch/$1');
    $routes->GET('purchases/byGeneric/(:num)', 'Purchases::byGeneric/$1');
    $routes->match(['GET', 'POST'], 'purchases/receive-stock/(:num)', 'Purchases::receiveStock/$1');




    // Brands routes
    $routes->GET('brands', 'Brands::index');
    $routes->GET('brands/create', 'Brands::create');
    $routes->POST('brands/store', 'Brands::store');
    $routes->GET('brands/edit/(:num)', 'Brands::edit/$1');
    $routes->POST('brands/update/(:num)', 'Brands::update/$1');
    $routes->GET('brands/delete/(:num)', 'Brands::delete/$1');

    // Generics routes
    $routes->GET('generics', 'Generics::index');
    $routes->GET('generics/create', 'Generics::create');
    $routes->POST('generics/store', 'Generics::store');
    $routes->GET('generics/edit/(:num)', 'Generics::edit/$1');
    $routes->post('generics/update/(:num)', 'Generics::update/$1');
    $routes->GET('generics/delete/(:num)', 'Generics::delete/$1');

    // Returns Management (PharmacyReturnsController)
    $routes->GET('returns', 'Returns::index'); // List pending/all returns
    $routes->GET('returns/create', 'Returns::create'); // Initiate a return
    $routes->post('returns/store', 'Returns::store');
    $routes->GET('returns/approve/(:num)', 'Returns::approve/$1'); // Manager approval form
    $routes->POST('returns/process-approval/(:num)', 'Returns::processApproval/$1');
    $routes->GET('returns/getMedicinesByInvoice/(:segment)', 'Returns::getMedicinesByInvoice/$1');
    $routes->post('returns/processApproval/(:num)', 'Returns::processApproval/$1');


    $routes->GET('payments/makePayment/(:segment)', 'Payments::makePayment/$1');
    $routes->POST('payments/processPayment', 'Payments::processPayment');



    // Sales Management (PharmacySalesController)
    $routes->GET('sales', 'Sales::index'); // POS Panel
    $routes->POST('sales/process-sale', 'Sales::processSale');
    $routes->GET('sales/invoice/(:any)', 'Sales::invoice/$1');
    $routes->GET('sales/list', 'Sales::listSales');
    $routes->get('sales/billsByPatient/(:num)', 'Sales::billsByPatient/$1');
    $routes->get('sales/printInvoice/(:any)', 'Sales::printInvoice/$1');



    // ** NEW API ROUTES FOR SALES **
    $routes->GET('sales/getMedicinesByCategory/(:num)', 'Sales::getMedicinesByCategory/$1');
    $routes->GET('sales/getBatchesByMedicine/(:num)', 'Sales::getBatchesByMedicine/$1');
    $routes->GET('sales/searchPatient/(:any)', 'Sales::searchPatient/$1');
    $routes->GET('sales/listBills/(:any)', 'Sales::listBills/$1');




    // Medicine Management (PharmacyMedicinesController)
    $routes->GET('medicines', 'Medicines::index', ['as' => 'pharmacy.medicines.index']);
    $routes->GET('medicines/create', 'Medicines::create', ['as' => 'pharmacy.medicines.create']);
    $routes->POST('medicines/store', 'Medicines::store', ['as' => 'pharmacy.medicines.store']);
    $routes->GET('medicines/edit/(:num)', 'Medicines::edit/$1', ['as' => 'pharmacy.medicines.edit']);
    $routes->PUT('medicines/update/(:num)', 'Medicines::update/$1', ['as' => 'pharmacy.medicines.update']);
    $routes->DELETE('medicines/delete/(:num)', 'Medicines::delete/$1', ['as' => 'pharmacy.medicines.delete']);

    // ** ADD THIS NEW ROUTE **
    $routes->get('medicines/getPatientDetailsAndBills/(:any)', 'Medicines::getPatientDetailsAndBills/$1');


    // Medicine Batches
    $routes->GET('medicines/batches/(:num)', 'Medicines::batches/$1');
    $routes->GET('medicines/add-batch/(:num)', 'Medicines::addBatch/$1');
    $routes->POST('medicines/store-batch', 'Medicines::storeBatch');
    $routes->GET('medicines/edit-batch/(:num)', 'Medicines::editBatch/$1');
    $routes->POST('medicines/update-batch/(:num)', 'Medicines::updateBatch/$1');
    $routes->POST('medicines/delete-batch/(:num)', 'Medicines::deleteBatch/$1');
    $routes->GET('medicines', 'Medicines::index');

    // Route for displaying the adjust stock page (GET)
    $routes->GET('medicines/adjustStock', 'Medicines::adjustStock');

    // Route for submitting the adjust stock form (POST)
    $routes->post('medicines/adjustStock', 'Medicines::adjustStock');

    // Stock Adjustments
    $routes->match(['GET', 'POST'], 'medicines/adjust-stock', 'Medicines::adjustStock'); // Handles both form display and submission
    // Stock Adjustment routes
    // $routes->GET('medicines/adjust-stock', 'Medicines::adjustStock');
    // $routes->POST('medicines/store-adjustment', 'Medicines::storeAdjustment');





    // Add other existing pharmacy routes here as needed, following the same pattern
    $routes->GET('dashboard', 'Dashboard::index');
    $routes->GET('stock', 'Stock::index');
    $routes->GET('reports', 'Reports::index');


    // Supplier Management
    $routes->GET('suppliers', 'Suppliers::index');
    $routes->GET('suppliers/create', 'Suppliers::create');
    $routes->POST('suppliers/store', 'Suppliers::store');
    $routes->GET('suppliers/edit/(:num)', 'Suppliers::edit/$1');
    $routes->POST('suppliers/update/(:num)', 'Suppliers::update/$1');
    $routes->POST('suppliers/delete/(:num)', 'Suppliers::delete/$1');

    // Manufacturer Management
    $routes->GET('manufacturers', 'Manufacturers::index');
    $routes->GET('manufacturers/create', 'Manufacturers::create');
    $routes->POST('manufacturers/store', 'Manufacturers::store');
    $routes->GET('manufacturers/edit/(:num)', 'Manufacturers::edit/$1');
    $routes->POST('manufacturers/update/(:num)', 'Manufacturers::update/$1');
    $routes->POST('manufacturers/delete/(:num)', 'Manufacturers::delete/$1');

    // Dosage Form Management
    $routes->GET('dosage_forms', 'DosageForms::index');
    $routes->GET('dosage_forms/create', 'DosageForms::create');
    $routes->POST('dosage_forms/store', 'DosageForms::store');
    $routes->GET('dosage_forms/edit/(:num)', 'DosageForms::edit/$1');
    $routes->POST('dosage_forms/update/(:num)', 'DosageForms::update/$1');
    $routes->POST('dosage_forms/delete/(:num)', 'DosageForms::delete/$1');

    // Units of Measure Management (added from previous conversation)
    $routes->GET('units_of_measure', 'UnitsOfMeasure::index');
    $routes->GET('units_of_measure/create', 'UnitsOfMeasure::create');
    $routes->POST('units_of_measure/store', 'UnitsOfMeasure::store');
    $routes->GET('units_of_measure/edit/(:num)', 'UnitsOfMeasure::edit/$1');
    $routes->PUT('units_of_measure/update/(:num)', 'UnitsOfMeasure::update/$1');
    $routes->DELETE('units_of_measure/delete/(:num)', 'UnitsOfMeasure::delete/$1');




    // Category Management
    $routes->GET('categories', 'Categories::index');
    $routes->GET('categories/create', 'Categories::create');
    $routes->POST('categories/store', 'Categories::store');
    $routes->GET('categories/edit/(:num)', 'Categories::edit/$1');
    $routes->POST('categories/update/(:num)', 'Categories::update/$1');
    $routes->POST('categories/delete/(:num)', 'Categories::delete/$1');



    // Combined API Routes for Pharmacy
    $routes->GET('medicines/get-batches-by-medicine/(:num)', 'Medicines::getBatchesByMedicine/$1');
    // $routes->GET('medicines/get-patient-details-and-bills/(:any)', 'Medicines::getPatientDetailsAndBills/$1');
    $routes->GET('medicines/get-patient-details-and-bills/(:segment)', 'Medicines::getPatientDetailsAndBills/$1');
});


// API Routes
$routes->group('api', ['namespace' => 'App\Controllers'], function ($routes) {
    $routes->GET('get-doctors', 'Api::getDoctors'); // Maps /api/get-doctors to Api controller's getDoctors method
});
