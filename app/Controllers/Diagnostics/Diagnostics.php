<?php

namespace App\Controllers\Diagnostics;

use App\Controllers\BaseController;
use App\Models\Diagnostics\DiagnosticsOrderFileModel;
use App\Models\Diagnostics\DiagnosticsOrderItemModel;
use App\Models\Diagnostics\DiagnosticsOrderModel;
use App\Models\Diagnostics\DiagnosticsTestModel;
use App\Models\DoctorModel;
use App\Models\PatientModel;
use CodeIgniter\Files\File;
use CodeIgniter\I18n\Time;

class Diagnostics extends BaseController
{
    protected $helpers = ['form', 'url'];

    protected $orderModel;
    protected $orderItemModel;
    protected $orderFileModel;
    protected $testModel;
    protected $doctorModel;
    protected $patientModel;



    public function __construct()
    {
        // Load Models
        $this->orderModel     = new DiagnosticsOrderModel();
        $this->orderItemModel = new DiagnosticsOrderItemModel();
        $this->orderFileModel = new DiagnosticsOrderFileModel();
        $this->testModel      = new DiagnosticsTestModel();
        $this->doctorModel    = new DoctorModel();
        $this->patientModel   = new PatientModel();
    }



    public function newOrder()
    {
        $patientModel = new PatientModel();
        $diagnosticsTestModel = new DiagnosticsTestModel();
        $doctorModel = new DoctorModel();

        $doctors = $doctorModel->findAll();
        $doctorsLookup = [];
        foreach ($doctors as $doctor) {
            $doctorsLookup[$doctor['id']] = $doctor['first_name'] . ' ' . $doctor['last_name'];
        }

        $patients = $patientModel->findAll();
        $formattedPatients = [];
        foreach ($patients as $patient) {
            $doctorName = $doctorsLookup[$patient['referred_to_doctor_id']] ?? 'Unknown';
            $formattedPatients[] = array_merge($patient, ['doctor_name' => $doctorName]);
        }

        $orderedBy = session()->get('user_id');

        $data['title'] = 'Place New Diagnostic Order';
        $data['patients'] = $formattedPatients;
        $data['diagnosticsTests'] = $diagnosticsTestModel->findAll();
        $data['orderedBy'] = $orderedBy;

        return view('diagnostics/new_order', $data);
    }


    public function saveOrder()
    {
        $rules = [
            'patient_id' => 'required',
            'test_ids' => 'required',
            'ordered_by' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $diagnosticsOrderModel = new DiagnosticsOrderModel();
        $diagnosticsOrderItemModel = new DiagnosticsOrderItemModel();

        $patientId = $this->request->getPost('patient_id');
        $orderedBy = $this->request->getPost('ordered_by');
        $testIds = $this->request->getPost('test_ids');
        $remarks = $this->request->getPost('remarks');

        // Create the new order
        $orderData = [
            'patient_id' => $patientId,
            'ordered_by' => $orderedBy,
            'order_date' => Time::now()->toDateTimeString(),
            'status' => 'Pending',
            'remarks' => $remarks,
        ];
        $diagnosticsOrderModel->insert($orderData);
        $orderId = $diagnosticsOrderModel->getInsertID();



        // Add each selected test as an order item
        foreach ($testIds as $testId) {
            $orderItemData = [
                'diagnostics_order_id' => $orderId,
                'diagnostics_test_id' => $testId,
                'status' => 'Pending',
            ];
            $diagnosticsOrderItemModel->insert($orderItemData);
        }

        return redirect()->to(base_url('diagnostics/orders'))->with('success', 'Diagnostic order has been placed successfully.');
    }


    public function orders()
    {
        $diagnosticsOrderModel = new DiagnosticsOrderModel();

        // Query to get orders, joining with the PATIENT's REFERRED DOCTOR
        $orders = $diagnosticsOrderModel
            ->select('diagnostics_orders.*, 
                     patients.patient_id_code, 
                     patients.first_name, 
                     patients.last_name, 
                     referred_doc.first_name as doctor_first_name, 
                     referred_doc.last_name as doctor_last_name') // Aliasing for the referred doctor

            // 1. Join with Patients (REQUIRED)
            ->join('patients', 'patients.id = diagnostics_orders.patient_id', 'left')

            // 2. NEW JOIN: Join the doctors table (aliased as referred_doc) 
            //    using the patient's referred_to_doctor_id.
            //    We link patients.referred_to_doctor_id (which is a doctor ID) to doctors.id
            ->join('doctors as referred_doc', 'referred_doc.id = patients.referred_to_doctor_id', 'left')

            // REMOVED: The old join based on diagnostics_orders.ordered_by is removed.

            ->orderBy('diagnostics_orders.created_at', 'DESC')
            ->findAll();

        $formattedOrders = [];
        foreach ($orders as $order) {

            // Determine the doctor's name, defaulting to "N/A" if no doctor was referred
            $doctorFirstName = $order['doctor_first_name'];
            $doctorLastName = $order['doctor_last_name'];

            $doctorName = 'N/A'; // Default to N/A
            if (!empty($doctorFirstName) || !empty($doctorLastName)) {
                $doctorName = trim(($doctorFirstName ?? '') . ' ' . ($doctorLastName ?? ''));
            }
            // If the name is still empty (e.g., both are NULL), default to "N/A"
            if (empty($doctorName)) {
                $doctorName = 'N/A';
            }


            $formattedOrders[] = [
                'id' => $order['id'],
                'order_id_code' => $order['order_id_code'],
                'patient_id_code' => $order['patient_id_code'] ?? 'N/A',
                'patient_name' => ($order['first_name'] ?? 'Unknown') . ' ' . ($order['last_name'] ?? ''),

                // Use the calculated doctor's name here
                'doctor_name' => $doctorName,

                'order_date' => $order['order_date'],
                'status' => $order['status'],
                'remarks' => $order['remarks'],
            ];
        }

        $data['title'] = 'Diagnostic Orders';
        $data['orders'] = $formattedOrders;

        return view('diagnostics/orders', $data);
    }

    public function viewOrderDetails($orderId)
    {
        $diagnosticsOrderModel = new DiagnosticsOrderModel();

        // 1. Fetch the main order details (patient name, doctor name, etc.)
        $order = $diagnosticsOrderModel->getOrdersWithDetails($orderId);

        if (empty($order)) {
            return redirect()->to(base_url('diagnostics/orders'))->with('error', 'Order not found.');
        }

        // 2. Fetch the list of tests for this order
        $diagnosticsOrderItemModel = new DiagnosticsOrderItemModel();

        // Use the correct method name from your model
        $tests = $diagnosticsOrderItemModel->getTestsForOrder($orderId);

        $data['title'] = 'Lab Order Details';
        $data['order'] = $order;

        // FIX: Assign the fetched tests to 'orderItems' 
        // because that is what the view file expects.
        $data['orderItems'] = $tests;

        return view('diagnostics/view_order', $data);
    }

    public function editOrder($orderId)
    {
        $diagnosticsOrderModel = new DiagnosticsOrderModel();
        $patientModel = new PatientModel();
        $diagnosticsTestModel = new DiagnosticsTestModel();

        $order = $diagnosticsOrderModel->find($orderId);

        if (empty($order)) {
            return redirect()->to(base_url('diagnostics/orders'))->with('error', 'Order not found for editing.');
        }

        // --- Fetch Patient Details to display in the view (read-only) ---
        $patient = $patientModel->find($order['patient_id']);
        if ($patient) {
            // Attach the patient's name and ID code to the order array
            $order['patient_name'] = $patient['first_name'] . ' ' . $patient['last_name']; // Assumes first_name and last_name fields in PatientModel
            $order['patient_id_code'] = $patient['patient_id_code'];
        } else {
            // Fallback if patient record is missing
            $order['patient_name'] = 'Patient Not Found';
            $order['patient_id_code'] = 'N/A';
        }
        // --- End Patient Details ---

        // Fetch current selected tests for this order
        $diagnosticsOrderItemModel = new DiagnosticsOrderItemModel();
        $currentTestIds = array_column($diagnosticsOrderItemModel->where('diagnostics_order_id', $orderId)->findAll(), 'diagnostics_test_id');

        $data['title'] = 'Edit Diagnostic Order';
        $data['order'] = $order; // Now includes patient name and ID code
        // Note: You probably don't need to fetch all patients here unless you allow changing the patient
        // $data['patients'] = $patientModel->findAll(); 
        $data['diagnosticsTests'] = $diagnosticsTestModel->findAll(); // For the tests dropdown
        $data['currentTestIds'] = $currentTestIds; // Pass current tests to pre-select the dropdown

        return view('diagnostics/edit_order', $data);
    }

    public function updateOrder($orderId)
    {
        // Add validation rules here (similar to saveOrder)
        $rules = [
            'patient_id' => 'required',
            'test_ids' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $diagnosticsOrderModel = new DiagnosticsOrderModel();
        $diagnosticsOrderItemModel = new DiagnosticsOrderItemModel();

        $patientId = $this->request->getPost('patient_id');
        $testIds = $this->request->getPost('test_ids');
        $remarks = $this->request->getPost('remarks');

        // Update the main order record
        $orderData = [
            'patient_id' => $patientId,
            // 'order_id_code' should NOT be updated here
            'remarks' => $remarks,
            'updated_at' => Time::now()->toDateTimeString(),
        ];
        $diagnosticsOrderModel->update($orderId, $orderData);

        // Delete all old order items and insert the new ones
        $diagnosticsOrderItemModel->where('diagnostics_order_id', $orderId)->delete();

        foreach ($testIds as $testId) {
            $orderItemData = [
                'diagnostics_order_id' => $orderId,
                'diagnostics_test_id' => $testId,
                'status' => 'Pending', // Reset status for newly added tests
            ];
            $diagnosticsOrderItemModel->insert($orderItemData);
        }

        return redirect()->to(base_url('diagnostics/orders'))->with('success', 'Diagnostic order has been updated successfully.');
    }

    public function deleteOrder($orderId)
    {
        $diagnosticsOrderModel = new DiagnosticsOrderModel();
        $diagnosticsOrderItemModel = new DiagnosticsOrderItemModel();

        // Find the order to get the ID code for the message
        $order = $diagnosticsOrderModel->find($orderId);

        if (empty($order)) {
            return redirect()->to(base_url('diagnostics/orders'))->with('error', 'Order not found for deletion.');
        }

        $orderIdCode = $order['order_id_code'];

        // 1. Delete associated order items
        $diagnosticsOrderItemModel->where('diagnostics_order_id', $orderId)->delete();

        // 2. Delete the main order
        $diagnosticsOrderModel->delete($orderId);

        return redirect()->to(base_url('diagnostics/orders'))->with('success', 'Order ' . $orderIdCode . ' and all related items have been successfully deleted.');
    }







    // RESULTS & REPORT METHODS
    public function resultsList()
    {
        $diagnosticsOrderModel = new DiagnosticsOrderModel();

        // 1. Fetch raw orders, using the same JOIN logic as the 'orders' method (linking to referred doctor)
        $orders = $diagnosticsOrderModel
            ->select('diagnostics_orders.*,
                      patients.patient_id_code,
                      patients.first_name,
                      patients.last_name,
                      referred_doc.first_name as doctor_first_name,
                      referred_doc.last_name as doctor_last_name')
            // 1. Join with Patients
            ->join('patients', 'patients.id = diagnostics_orders.patient_id', 'left')
            // 2. Join with Doctors (aliased as referred_doc) via patient's referred_to_doctor_id
            ->join('doctors as referred_doc', 'referred_doc.id = patients.referred_to_doctor_id', 'left')
            // Filter for orders ready for results or already completed
            // TEMPORARY DEBUGGING CHANGE: Commenting out the filter to see ALL orders.
            // ->whereIn('diagnostics_orders.status', ['Sample Collected', 'Processing', 'Completed'])
            ->orderBy('diagnostics_orders.order_date', 'DESC')
            ->findAll();

        $formattedOrders = [];
        // 2. Manually format the results array (matching the orders() method)
        foreach ($orders as $order) {
            // Determine the doctor's name, defaulting to "N/A" if no doctor was referred
            $doctorFirstName = $order['doctor_first_name'];
            $doctorLastName = $order['doctor_last_name'];

            $doctorName = 'N/A'; // Default to N/A
            if (!empty($doctorFirstName) || !empty($doctorLastName)) {
                $doctorName = trim(($doctorFirstName ?? '') . ' ' . ($doctorLastName ?? ''));
            }
            if (empty($doctorName)) {
                $doctorName = 'N/A';
            }

            $formattedOrders[] = [
                'id' => $order['id'],
                'order_id_code' => $order['order_id_code'],
                'patient_id_code' => $order['patient_id_code'] ?? 'N/A',
                'patient_name' => ($order['first_name'] ?? 'Unknown') . ' ' . ($order['last_name'] ?? ''),
                'doctor_name' => $doctorName,
                'order_date' => $order['order_date'],
                'status' => $order['status'],
                // Note: The 'remarks' field is not needed/used in the 'results' list view
            ];
        }

        $data['title'] = 'Orders Ready for Results Entry & Reporting';
        $data['orders'] = $formattedOrders; // Pass the formatted data

        // This maps to the diagnostics/results.php view file
        return view('diagnostics/results', $data);
    }

    public function enterResults($orderId)
    {
        $diagnosticsOrderModel = new DiagnosticsOrderModel();
        $diagnosticsOrderItemModel = new DiagnosticsOrderItemModel();
        $diagnosticsOrderFileModel = new DiagnosticsOrderFileModel();

        $order = $diagnosticsOrderModel
            ->select('diagnostics_orders.*, 
                      patients.patient_id_code, 
                      CONCAT(patients.first_name, " ", patients.last_name) AS patient_name, 
                      patients.phone_number AS patient_phone, 
                      CONCAT(doctors.first_name, " ", doctors.last_name) AS doctor_name')
            ->join('patients', 'patients.id = diagnostics_orders.patient_id', 'left')
            ->join('doctors', 'doctors.id = diagnostics_orders.ordered_by', 'left')
            ->where('diagnostics_orders.id', $orderId)
            ->first();

        if (!$order) {
            return redirect()->to(base_url('diagnostics/results'))->with('error', 'Order not found.');
        }

        $orderItems = $diagnosticsOrderItemModel
            ->select('diagnostics_order_items.*, diagnostics_tests.test_name, diagnostics_tests.test_type')
            ->join('diagnostics_tests', 'diagnostics_tests.id = diagnostics_order_items.diagnostics_test_id')
            ->where('diagnostics_order_items.diagnostics_order_id', $orderId)
            ->findAll();

        $files = $diagnosticsOrderFileModel
            ->select('diagnostics_order_files.*')
            ->join('diagnostics_order_items', 'diagnostics_order_items.id = diagnostics_order_files.diagnostics_order_item_id')
            ->where('diagnostics_order_items.diagnostics_order_id', $orderId)
            ->findAll();

        $filesByItem = [];
        foreach ($files as $file) {
            $filesByItem[$file['diagnostics_order_item_id']][] = $file;
        }

        $data['title'] = 'Enter Test Results';
        $data['order'] = $order;
        $data['orderItems'] = $orderItems;
        $data['filesByItem'] = $filesByItem;

        // FIX: Use the global view() function instead of $this->extend()->view()
        return view('diagnostics/enter_results', $data);
    }


    public function saveResult() // RENAMED and NO $orderId parameter
    {
        log_message('debug', 'Diagnostics::saveResult method (Item-by-Item) hit.');

        $post = $this->request->getPost();
        $files = $this->request->getFiles();

        $labOrderItemId = $post['diagnostics_order_item_id'] ?? null;

        if (!$labOrderItemId || !is_numeric($labOrderItemId)) {
            return redirect()->to(base_url('diagnostics/results'))->with('error', 'Invalid test item submission. Item ID missing.');
        }
        $labOrderItemId = (int)$labOrderItemId;

        // 1. Load Models and DB connection
        $itemModel = new DiagnosticsOrderItemModel();
        $fileModel = new DiagnosticsOrderFileModel();
        $orderModel = new DiagnosticsOrderModel();

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            // 2. Extract Data and set up validation for a single item
            $resultValue = $post['result'] ?? null;
            $statusValue = $post['status'] ?? 'Pending';

            // --- DEBUG LOG: Check if the result value is captured ---
            log_message('debug', 'Result Value extracted: ' . var_export($resultValue, true));
            // --------------------------------------------------------

            $rules = [
                'status' => 'required|in_list[Pending,In Progress,Completed]',
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', $this->validator->getErrors());
            }

            // 3. Update the single item
            $updateData = [
                'result'       => $resultValue,
                'status'       => $statusValue,
                // 'result_date' is removed here because the diagnostics_order_items table does not have this column.
                // It will rely on the 'updated_at' timestamp for tracking the last change.
            ];

            // CRITICAL CHECK: ensure 'result' and 'status' are in DiagnosticsOrderItemModel's $allowedFields
            if (!$itemModel->update($labOrderItemId, $updateData)) {
                throw new \Exception("Database update failed for item #{$labOrderItemId}. Check model allowed fields or column names.");
            }

            $orderItem = $itemModel->find($labOrderItemId);
            $orderId = $orderItem['diagnostics_order_id']; // Get the main order ID

            // 4. Handle File Uploads (Uses FCPATH for public access)
            $uploadedFilesCount = 0;
            if (isset($files['result_files'])) {

                // Use ROOTPATH and explicitly include 'public/' 
                $uploadSubPath = 'uploads/patient_reports'; // Relative path from public/
                $diskPath = ROOTPATH . 'public/' . $uploadSubPath;

                // Ensure directory exists
                if (!is_dir($diskPath)) {
                    if (!mkdir($diskPath, 0777, true)) {
                        throw new \Exception("Failed to create directory: {$diskPath}. Check permissions.");
                    }
                }

                $userId = $this->session->get('id') ?? 0;

                foreach ($files['result_files'] as $file) {
                    if ($file->getError() == UPLOAD_ERR_NO_FILE) {
                        continue;
                    }
                    if ($file->isValid()) {

                        if ($file->getSize() > 5 * 1024 * 1024) { // 5MB limit
                            throw new \Exception("File {$file->getClientName()} is too large (max 5MB).");
                        }
                        $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
                        if (!in_array($file->getMimeType(), $allowedTypes)) {
                            throw new \Exception("File {$file->getClientName()} is not an allowed type (JPEG, PNG, PDF only).");
                        }

                        $fileName = $file->getRandomName();
                        $clientFileName = $file->getClientName(); // Capture the client name for file_name column 

                        // --- DEBUG LOG: Check if the client file name is captured ---
                        log_message('debug', 'Client File Name extracted: ' . var_export($clientFileName, true));
                        // -------------------------------------------------------------

                        // CRITICAL FIX: Move to the public path
                        if ($file->move($diskPath, $fileName)) {
                            // CRITICAL CHECK: ensure 'file_name' is in DiagnosticsOrderFileModel's $allowedFields
                            $fileModel->save([
                                'diagnostics_order_item_id' => $labOrderItemId,
                                'file_name'                 => $clientFileName, // Now correctly storing the original name
                                'file_path'                 => $fileName,       // Storing ONLY the randomized file name
                                'uploaded_by'               => $userId,
                            ]);
                            $uploadedFilesCount++;
                        } else {
                            throw new \Exception("File move operation failed for {$file->getClientName()}. Check permissions on {$diskPath}.");
                        }
                    } else {
                        throw new \Exception("File upload failed for {$file->getClientName()}. Error: " . $file->getErrorString());
                    }
                }
            }

            // 5. Check and Update Global Order Status
            $incompleteItems = $itemModel->where('diagnostics_order_id', $orderId)
                ->where('status !=', 'Completed')
                ->countAllResults();

            $order = $orderModel->find($orderId);

            if ($incompleteItems === 0) {
                $orderModel->update($orderId, ['status' => 'Completed']);
            } elseif ($order['status'] === 'Pending' && $statusValue !== 'Pending') {
                $orderModel->update($orderId, ['status' => 'In Progress']);
            }

            $db->transCommit();

            // 6. Success Redirection
            $message = "Result for test item #{$labOrderItemId} saved and {$uploadedFilesCount} file(s) uploaded successfully.";
            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Error saving diagnostic result (Item-by-Item): ' . $e->getMessage());

            // 7. Error Redirection
            return redirect()->back()->with('error', 'A critical error occurred: ' . $e->getMessage());
        }
    }

    public function viewFile($fileId)
    {
        $fileModel = new DiagnosticsOrderFileModel();

        $fileRecord = $fileModel->find($fileId);

        if (!$fileRecord) {
            return $this->response->setStatusCode(404)->setBody('File not found in database.');
        }

        $fileNameInDB = $fileRecord['file_path']; // This is the randomized name (e.g., 12345.pdf)
        $originalFileName = $fileRecord['file_name']; // This is the name for display (e.g., Report.pdf)

        $uploadSubPath = 'uploads/patient_reports';
        // Construct the full path using ROOTPATH and the public directory
        $fullPath = ROOTPATH . 'public/' . $uploadSubPath . '/' . $fileNameInDB;

        // Check if the file physically exists
        if (!file_exists($fullPath)) {
            log_message('error', "File not found on disk: " . $fullPath);
            return $this->response->setStatusCode(404)->setBody('File not found on disk.');
        }

        // --- MODIFICATION: Display file inline instead of forcing download ---

        // 1. Determine MIME type using Fileinfo (most reliable way)
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $fullPath);
        finfo_close($finfo);

        // 2. Set Content-Type header
        $this->response->setContentType($mimeType);

        // 3. Set Content-Disposition to 'inline' to tell the browser to display the file
        // 'inline' means display in the browser if possible (e.g., PDF, image).
        $this->response->setHeader('Content-Disposition', 'inline; filename="' . $originalFileName . '"');
        $this->response->setHeader('Content-Length', filesize($fullPath));

        // 4. Set the body to the file content
        $this->response->setBody(file_get_contents($fullPath));

        // 5. Return the response object
        return $this->response;
    }

    public function viewReport($orderId)
    {
        // 1. Fetch the Order details (including patient/doctor info)
        // Assumes getReportData correctly joins users and aliases names to doctor_first_name/doctor_last_name
        $order = $this->orderModel->getReportData($orderId);

        if (!$order) {
            // Handle case where order is not found
            session()->setFlashdata('error', 'Diagnostic order not found.');
            return redirect()->to(base_url('diagnostics/results')); // Redirect back to results list
        }

        // 2. Fetch the Order Items/Test Results
        $orderItems = $this->orderItemModel->getResultsByOrderId($orderId);

        // 3. FETCH THE FILES
        $orderFiles = $this->orderFileModel->getFilesByOrderId($orderId);

        // --- Group files by item ID and merge into order items ---
        $groupedFiles = [];

        // Group files by the item ID they belong to
        foreach ($orderFiles as $file) {
            $itemId = $file['diagnostics_order_item_id'];
            if (!isset($groupedFiles[$itemId])) {
                $groupedFiles[$itemId] = [];
            }
            $groupedFiles[$itemId][] = $file;
        }

        // Merge the grouped files back into the corresponding order items
        foreach ($orderItems as $key => $item) {
            // Assuming $item['id'] holds the primary key of the order item
            $itemId = $item['id'];
            // Attach the files array (or an empty array if none exist)
            $orderItems[$key]['files'] = $groupedFiles[$itemId] ?? [];
        }
        // ------------------------------------------------------------------

        $data = [
            'title' => 'View Diagnostic Report',
            'order' => $order,
            'orderItems' => $orderItems, // This now contains nested 'files'
            // We no longer need to pass 'orderFiles' separately
        ];

        // 4. Load the correct view file
        return view('diagnostics/view_report_page', $data);
    }

    public function deleteFile($fileId)
    {
        $diagnosticsOrderFileModel = new DiagnosticsOrderFileModel();
        $diagnosticsOrderItemModel = new DiagnosticsOrderItemModel();

        $file = $diagnosticsOrderFileModel->find($fileId);

        if ($file) {
            $itemId = $file['diagnostics_order_item_id']; // Use correct column to get item ID

            // Get parent order ID to redirect back correctly
            $item = $diagnosticsOrderItemModel->find($itemId);
            $orderId = $item['diagnostics_order_id'] ?? 0;

            // Full path to file in the public directory
            $fullPath = ROOTPATH . 'public/' . $file['file_path'];

            if (file_exists($fullPath) && unlink($fullPath)) {
                // File deleted from disk successfully
            } elseif (!file_exists($fullPath)) {
                // File not found on disk, continue to delete database record
            } else {
                return redirect()->to(base_url('diagnostics/results/enter/' . $orderId))->with('error', 'Failed to delete file from server.');
            }

            // Delete record from database
            $diagnosticsOrderFileModel->delete($fileId);

            return redirect()->to(base_url('diagnostics/results/enter/' . $orderId))->with('success', 'File deleted successfully.');
        }

        return redirect()->to(base_url('diagnostics/results'))->with('error', 'File record not found.');
    }




    // public function uploadFile($orderId)
    // {
    //     $file = $this->request->getFile('diagnostic_file');
    //     $diagnosticsOrderItemModel = new DiagnosticsOrderItemModel();

    //     if ($file && $file->isValid() && !$file->hasMoved()) {
    //         $newName = $file->getRandomName();
    //         // FIX: Corrected upload path as per user request
    //         $path = 'uploads/patient_reports/';

    //         if (!is_dir(ROOTPATH . 'public/' . $path)) {
    //             mkdir(ROOTPATH . 'public/' . $path, 0777, true);
    //         }

    //         // Move file to the public folder
    //         $file->move(ROOTPATH . 'public/' . $path, $newName);

    //         $diagnosticsOrderFileModel = new DiagnosticsOrderFileModel();

    //         // NOTE: The schema links files to order items, not orders. 
    //         // We temporarily link the file to the first item of the order 
    //         // to allow upload, but the view/form structure should ideally be updated 
    //         // to pass the specific 'item_id' the file belongs to.
    //         $firstItem = $diagnosticsOrderItemModel->where('diagnostics_order_id', $orderId)->first();
    //         $itemIdToLink = $firstItem['id'] ?? null;

    //         if (!$itemIdToLink) {
    //             return redirect()->to(base_url('diagnostics/results/enter/' . $orderId))->with('error', 'Could not find a diagnostic item to link the file to. Upload failed.');
    //         }

    //         $diagnosticsOrderFileModel->insert([
    //             'diagnostics_order_item_id' => $itemIdToLink, // Correct foreign key column
    //             'original_file_name' => $file->getClientName(),
    //             'file_name' => $newName,
    //             // Store path relative to the public directory
    //             'file_path' => $path . $newName,
    //             'file_type' => $file->getClientMimeType(),
    //             'uploaded_by' => user_id(),
    //         ]);

    //         return redirect()->to(base_url('diagnostics/results/enter/' . $orderId))->with('success', 'File uploaded successfully.');
    //     }

    //     return redirect()->to(base_url('diagnostics/results/enter/' . $orderId))->with('error', 'File upload failed. Check file size or type.');
    // }

















    // TESTS METHODS

    public function tests()
    {
        $diagnosticsTestModel = new DiagnosticsTestModel();
        $data['title'] = 'Diagnostic Tests';
        $data['tests'] = $diagnosticsTestModel->findAll();

        return view('diagnostics/tests/tests', $data);
    }

    public function createTest()
    {
        $data['title'] = 'Create New Test';
        return view('diagnostics/tests/create', $data);
    }

    public function saveTest()
    {
        $rules = [
            'test_name' => 'required|is_unique[diagnostics_tests.test_name]',
            'test_type' => 'required',
            'price' => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $diagnosticsTestModel = new DiagnosticsTestModel();
        $data = [
            'test_name' => $this->request->getPost('test_name'),
            'test_type' => $this->request->getPost('test_type'),
            'price' => $this->request->getPost('price'),
        ];
        $diagnosticsTestModel->insert($data);

        return redirect()->to(base_url('diagnostics/tests'))->with('success', 'New diagnostic test has been created successfully.');
    }

    public function editTest($id)
    {
        $diagnosticsTestModel = new DiagnosticsTestModel();
        $test = $diagnosticsTestModel->find($id);

        if (!$test) {
            return redirect()->to(base_url('diagnostics/tests'))->with('error', 'Test not found.');
        }

        $data['title'] = 'Edit Diagnostic Test';
        $data['test'] = $test;

        return view('diagnostics/tests/edit', $data);
    }

    public function updateTest($id)
    {
        $diagnosticsTestModel = new DiagnosticsTestModel();

        $test = $diagnosticsTestModel->find($id);
        if (!$test) {
            return redirect()->to(base_url('diagnostics/tests'))->with('error', 'Test not found.');
        }

        $rules = [
            'test_name' => "required|is_unique[diagnostics_tests.test_name,id,$id]",
            'test_type' => 'required',
            'price' => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'test_name' => $this->request->getPost('test_name'),
            'test_type' => $this->request->getPost('test_type'),
            'price' => $this->request->getPost('price'),
        ];
        $diagnosticsTestModel->update($id, $data);

        return redirect()->to(base_url('diagnostics/tests'))->with('success', 'Diagnostic test has been updated successfully.');
    }

    public function deleteTest($id)
    {
        $diagnosticsTestModel = new DiagnosticsTestModel();
        $test = $diagnosticsTestModel->find($id);

        if (!$test) {
            return redirect()->to(base_url('diagnostics/tests'))->with('error', 'Test not found.');
        }

        $diagnosticsTestModel->delete($id);

        return redirect()->to(base_url('diagnostics/tests'))->with('success', 'Diagnostic test has been deleted successfully.');
    }
}
