<?php

namespace App\Controllers\Laboratory;

use App\Controllers\BaseController;
use App\Models\Laboratory\LabOrderModel;
use App\Models\Laboratory\LabOrderItemModel;
use App\Models\Laboratory\LabTestModel;
use App\Models\Laboratory\LabTestTypeModel;
use App\Models\Laboratory\LabOrderFileModel;
use App\Models\PatientModel;
use App\Models\DoctorModel;
use App\Models\UserModel;

class Laboratory extends BaseController
{
    protected $labOrderModel;
    protected $labOrderItemModel;
    protected $labTestModel;
    protected $labTestTypeModel;
    protected $patientModel;
    protected $userModel;
    protected $doctorModel;
    protected $labOrderFileModel;

    public function __construct()
    {
        $this->labOrderModel = new LabOrderModel();
        $this->labOrderItemModel = new LabOrderItemModel();
        $this->labTestModel = new LabTestModel();
        $this->labTestTypeModel = new LabTestTypeModel();
        $this->doctorModel    = new DoctorModel();
        $this->patientModel = new PatientModel();
        $this->userModel = new UserModel();
        $this->labOrderFileModel = new LabOrderFileModel();
    }







    public function types()
    {
        $data['types'] = $this->labTestTypeModel->findAll();
        return view('laboratory/types', $data);
    }

    public function createType()
    {
        return view('laboratory/create_type');
    }

    public function saveType()
    {
        $post = $this->request->getPost();

        $data = [
            'name' => $post['name'],
            'description' => $post['description']
        ];

        if ($this->labTestTypeModel->insert($data)) {
            return redirect()->to(base_url('laboratory/types'))->with('success', 'Test type added.');
        }

        return redirect()->back()->with('error', 'Failed to add test type.')->withInput();
    }

    public function editType($id)
    {
        $type = $this->labTestTypeModel->find($id);
        if (!$type) {
            return redirect()->to(base_url('laboratory/types'))->with('error', 'Test type not found.');
        }

        return view('laboratory/edit_type', ['type' => $type]);
    }

    public function updateType($id)
    {
        $post = $this->request->getPost();

        $data = [
            'name' => $post['name'],
            'description' => $post['description']
        ];

        if ($this->labTestTypeModel->update($id, $data)) {
            return redirect()->to(base_url('laboratory/types'))->with('success', 'Test type updated.');
        }

        return redirect()->back()->with('error', 'Failed to update.')->withInput();
    }

    public function deleteType($id)
    {
        if ($this->labTestTypeModel->delete($id)) {
            return redirect()->to(base_url('laboratory/types'))->with('success', 'Test type deleted.');
        }
        return redirect()->to(base_url('laboratory/types'))->with('error', 'Failed to delete.');
    }









    public function newOrder()
    {
        $data['patients'] = $this->patientModel->findAll();
        $data['labTests'] = $this->labTestModel->findAll();

        return view('laboratory/new_order', $data);
    }

    public function saveOrder()
    {
        $post = $this->request->getPost();

        $orderData = [
            'patient_id' => $post['patient_id'],
            'ordered_by' => session('user_id'),
            'order_date' => date('Y-m-d H:i:s'),
            'status' => 'Pending',
            'remarks' => $post['remarks'] ?? null,
        ];

        $orderId = $this->labOrderModel->insert($orderData);

        if (!$orderId) {
            return redirect()->back()->with('error', 'Failed to create order');
        }

        $testIds = $post['test_ids'] ?? [];

        foreach ($testIds as $testId) {
            $this->labOrderItemModel->insert([
                'lab_order_id' => $orderId,
                'lab_test_id' => $testId,
                'status' => 'Not Started',
            ]);
        }

        return redirect()->to(base_url('laboratory/orders'))->with('success', 'Order placed successfully');
    }

    public function orders()
    {
        $builder = $this->labOrderModel->builder();
        $builder->select('lab_orders.*, 
                           patients.first_name as patient_first_name, 
                           patients.last_name as patient_last_name, 
                           patients.phone_number as phone_number, 
                           patients.patient_id_code as patient_id_code,
                           doctors.first_name as doctor_first_name, 
                           doctors.last_name as doctor_last_name,
                           users.first_name as user_first_name, 
                           users.last_name as user_last_name');
        $builder->join('patients', 'patients.id = lab_orders.patient_id', 'left');
        $builder->join('doctors', 'doctors.id = patients.referred_to_doctor_id', 'left');
        $builder->join('users', 'users.id = lab_orders.ordered_by', 'left');
        $orders = $builder->get()->getResultArray();

        foreach ($orders as &$order) {
            $order['patient_id'] = $order['patient_id_code'];
            $order['patient_name'] = trim($order['patient_first_name'] . ' ' . $order['patient_last_name']);
            $order['phone_number'] = $order['phone_number'];
            $order['doctor_name'] = trim($order['doctor_first_name'] . ' ' . $order['doctor_last_name']);
            $order['ordered_by_name'] = trim($order['user_first_name'] . ' ' . $order['user_last_name']);
            $firstItem = $this->labOrderItemModel->where('lab_order_id', $order['id'])->first();
            $order['first_order_item_id'] = $firstItem ? $firstItem['id'] : 0;
        }
        unset($order);

        return view('laboratory/orders', ['orders' => $orders]);
    }

    public function view_order_page($orderId)
    {
        $order = $this->labOrderModel->builder()
            ->select('lab_orders.*, 
                    patients.patient_id_code,
                    patients.phone_number,
                    lab_orders.order_id_code,
                    CONCAT(patients.first_name, " ", patients.last_name) AS patient_name, 
                    CONCAT(doctors.first_name, " ", doctors.last_name) AS doctor_name,
                    CONCAT(users.first_name, " ", users.last_name) AS ordered_by_name')
            ->join('patients', 'patients.id = lab_orders.patient_id', 'left')
            ->join('doctors', 'doctors.id = patients.referred_to_doctor_id', 'left')
            ->join('users', 'users.id = lab_orders.ordered_by', 'left')
            ->where('lab_orders.id', $orderId)
            ->get()
            ->getRowArray();

        if (!$order) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $testItems = $this->labOrderItemModel->builder()
            ->select('lab_order_items.*, lab_tests.name AS test_name, lab_tests.price')
            ->join('lab_tests', 'lab_tests.id = lab_order_items.lab_test_id')
            ->where('lab_order_items.lab_order_id', $orderId)
            ->get()
            ->getResultArray();

        $data = [
            'order' => $order,
            'testItems' => $testItems,
            'title' => 'Lab Order Details'
        ];
        return view('laboratory/view_order', $data);
    }

    public function edit_report_page($orderId)
    {
        // Get the main order details including patient name
        $order = $this->labOrderModel->builder()
            ->select('lab_orders.*, CONCAT(patients.first_name, " ", patients.last_name) AS patient_name, patients.patient_id_code')
            ->join('patients', 'patients.id = lab_orders.patient_id')
            ->where('lab_orders.id', $orderId)
            ->get()
            ->getRowArray();

        // Fetch all available lab tests
        $labTests = $this->labTestModel->findAll();

        // Fetch the tests currently in the order
        $currentTests = $this->labOrderItemModel
            ->where('lab_order_id', $orderId)
            ->findAll();

        if (!$order) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [
            'order' => $order,
            'labTests' => $labTests,
            'currentTests' => array_column($currentTests, 'lab_test_id'),
            'title' => 'Edit Lab Report'
        ];

        return view('laboratory/edit_report_page', $data);
    }

    public function update_order($orderId)
    {
        // Validation logic here
        if (!$this->validate([
            'lab_test_ids' => 'required',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $labTestIds = $this->request->getPost('lab_test_ids');
        $remarks = $this->request->getPost('remarks');

        // Delete all old order items for this order
        $this->labOrderItemModel->where('lab_order_id', $orderId)->delete();

        // Insert new order items
        if ($labTestIds) {
            foreach ($labTestIds as $testId) {
                $testItemData = [
                    'lab_order_id' => $orderId,
                    'lab_test_id' => $testId,
                    'status' => 'Pending',
                ];
                $this->labOrderItemModel->insert($testItemData);
            }
        }

        // Update the main lab order remarks
        $this->labOrderModel->update($orderId, ['remarks' => $remarks]);

        return redirect()->to(base_url('laboratory/orders'))->with('success', 'Lab order updated successfully.');
    }
    public function deleteOrder($orderId)
    {
        // First, check if the order exists
        $order = $this->labOrderModel->find($orderId);
        if (!$order) {
            // Redirect with an error message if the order is not found
            return redirect()->back()->with('error', 'Order not found.');
        }

        // To ensure data integrity, we should also delete the associated order items
        // before deleting the main order record.
        $this->labOrderItemModel->where('lab_order_id', $orderId)->delete();

        // Now, delete the main order record
        if ($this->labOrderModel->delete($orderId)) {
            // Redirect to the orders list with a success message
            return redirect()->to(base_url('laboratory/orders'))->with('success', 'Order and associated items deleted successfully.');
        } else {
            // Redirect with an error message if the deletion fails
            return redirect()->back()->with('error', 'Failed to delete order.');
        }
    }








    public function results()
    {
        $builder = $this->labOrderModel->builder();
        $builder->select('lab_orders.*, lab_orders.id AS order_id, 
                           IFNULL(patients.patient_id_code, "N/A") AS patient_id,
                           IFNULL(CONCAT_WS(" ", patients.first_name, patients.last_name), "N/A") AS patient_name, 
                           IFNULL(patients.phone_number, "N/A") AS phone_number, 
                           IFNULL(CONCAT_WS(" ", doctors.first_name, doctors.last_name), "N/A") AS doctor_name');
        $builder->join('patients', 'patients.id = lab_orders.patient_id', 'left');
        $builder->join('doctors', 'doctors.id = patients.referred_to_doctor_id', 'left');
        $orders = $builder->get()->getResultArray();

        return view('laboratory/results', ['orders' => $orders]);
    }


    public function enterResult($orderId)
    {
        $order = $this->labOrderModel->find($orderId);
        if (!$order) {
            return redirect()->back()->with('error', 'Order not found.');
        }

        $data['order'] = $order;
        $data['orderItems'] = $this->labOrderItemModel
            ->select('lab_order_items.*, lab_tests.name as test_name') // Select test name
            ->join('lab_tests', 'lab_tests.id = lab_order_items.lab_test_id') // Join with lab_tests table
            ->where('lab_order_id', $orderId)
            ->findAll();

        foreach ($data['orderItems'] as &$item) {
            $item['files'] = $this->labOrderFileModel->getFilesByOrderItem($item['id']);
        }

        return view('laboratory/enter_result', $data);
    }


    public function viewReport($orderId)
    {
        // Fetch the Lab Order details. We explicitly join 'patients' and 'doctors'.
        $order = $this->labOrderModel
            ->select('
            lab_orders.*, 
            patients.first_name as patient_first_name, 
            patients.last_name as patient_last_name, 
            patients.patient_id_code as patient_id_code, 
            patients.phone_number as phone_number, 
            doctors.first_name as doctor_first_name, 
            doctors.last_name as doctor_last_name
        ')
            // Join to get patient details
            ->join('patients', 'patients.id = lab_orders.patient_id')

            // FIX: Replaced leftJoin() with the standard join() method, 
            // passing 'left' as the third parameter for a LEFT JOIN.
            // This ensures we get the Doctor's name based on the patient's referred_to_doctor_id.
            ->join('doctors', 'doctors.id = patients.referred_to_doctor_id', 'left')

            ->where('lab_orders.id', $orderId)
            ->first();

        if (!$order) {
            return redirect()->back()->with('error', 'Order not found.');
        }

        // Fetch the items for this lab order
        $orderItems = $this->labOrderItemModel
            ->select('lab_order_items.*, lab_tests.name as test_name, lab_test_types.name as test_type_name')
            ->join('lab_tests', 'lab_tests.id = lab_order_items.lab_test_id')
            ->join('lab_test_types', 'lab_test_types.id = lab_tests.test_type_id')
            ->where('lab_order_items.lab_order_id', $orderId)
            ->findAll();

        // Group and attach files to each order item
        foreach ($orderItems as &$item) {
            // Assuming labOrderFileModel->getFilesByOrderItem fetches files based on item ID
            $item['files'] = $this->labOrderFileModel->getFilesByOrderItem($item['id']);
        }

        $data = [
            'order' => $order,
            'orderItems' => $orderItems,
            'title' => 'Lab Report Details'
        ];

        return view('laboratory/view_report_page', $data);
    }
    




    public function saveResult()
    {
        $post = $this->request->getPost();
        $files = $this->request->getFiles();

        $labOrderItemId = $post['lab_order_item_id'];
        $resultText = $post['result'];
        $status = $post['status'];

        // Update the lab_order_item with the new result and status
        $this->labOrderItemModel->update($labOrderItemId, [
            'result' => $resultText,
            'status' => $status,
            'result_date' => date('Y-m-d H:i:s')
        ]);

        $orderItem = $this->labOrderItemModel->find($labOrderItemId);
        $orderId = $orderItem['lab_order_id'];

        $uploadedFilesCount = 0;
        // Handle multiple file uploads
        if (!empty($files['result_files'])) {
            $uploadPath = ROOTPATH . 'public/uploads/laboratory/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            foreach ($files['result_files'] as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $newName = $file->getRandomName();
                    $file->move($uploadPath, $newName);

                    $fileData = [
                        'lab_order_item_id' => $labOrderItemId,
                        'file_name' => $file->getClientName(),
                        'file_path' => $newName // Save only the filename
                    ];

                    $this->labOrderFileModel->insert($fileData);
                    $uploadedFilesCount++;
                }
            }
        }

        // Check if all items for the order are completed
        $totalItems = $this->labOrderItemModel->where('lab_order_id', $orderId)->countAllResults();
        $completedItems = $this->labOrderItemModel->where('lab_order_id', $orderId)->where('status', 'Completed')->countAllResults();

        if ($totalItems > 0 && $totalItems === $completedItems) {
            // If all items are completed, update the main lab_order status
            $this->labOrderModel->update($orderId, ['status' => 'Completed']);
        }

        return redirect()->back()->with('success', 'Result saved and ' . $uploadedFilesCount . ' file(s) uploaded successfully.');
    }



    public function deleteFile($fileId)
    {
        // Find the file record in the database
        $file = $this->labOrderFileModel->find($fileId);

        if (!$file) {
            return redirect()->back()->with('error', 'File not found.');
        }

        // Get the full server path to the file
        $filePath = ROOTPATH . 'public/uploads/laboratory/' . $file['file_path'];

        // Check if the file exists on the server and delete it
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Delete the record from the database
        $this->labOrderFileModel->delete($fileId);

        return redirect()->back()->with('success', 'File deleted successfully.');
    }



    public function reports()
    {

        $builder = $this->labOrderModel->builder();
        $builder->select('lab_orders.*, lab_orders.id AS order_id, 
                           IFNULL(patients.patient_id_code, "N/A") AS patient_id,
                           IFNULL(CONCAT_WS(" ", patients.first_name, patients.last_name), "N/A") AS patient_name, 
                           IFNULL(patients.phone_number, "N/A") AS phone_number, 
                           IFNULL(CONCAT_WS(" ", doctors.first_name, doctors.last_name), "N/A") AS doctor_name');
        $builder->join('patients', 'patients.id = lab_orders.patient_id', 'left');
        $builder->join('doctors', 'doctors.id = patients.referred_to_doctor_id', 'left');
        $labOrders = $builder->get()->getResultArray();

        $data['labOrders'] = $labOrders;

        return view('laboratory/reports', $data);
    }























    // public function view_report($orderId)
    // {
    //     $order = $this->labOrderModel->find($orderId);

    //     if (!$order) {
    //         return $this->response->setJSON(['error' => 'Report not found.']);
    //     }

    //     $files = $this->labOrderFileModel->getFilesByOrderItem($orderId);

    //     return $this->response->setJSON([
    //         'order' => $order,
    //         'files' => $files,
    //     ]);
    // }


    // public function edit_report($orderId)
    // {
    //     $order = $this->labOrderModel->find($orderId);

    //     if (!$order) {
    //         return $this->response->setJSON(['error' => 'Report not found.']);
    //     }

    //     $files = $this->labOrderFileModel->getFilesByOrderItem($orderId);

    //     return $this->response->setJSON([
    //         'order' => $order,
    //         'files' => $files,
    //     ]);
    // }














    // public function view_report_page($orderId)
    // {

    //     $order = $this->labOrderModel->builder()
    //         ->select('lab_orders.*, patients.patient_name, patients.phone_number, patients.patient_id_code, users.name as doctor_name')
    //         ->join('patients', 'patients.id = lab_orders.patient_id')
    //         ->join('users', 'users.id = lab_orders.ordered_by')
    //         ->where('lab_orders.id', $orderId)
    //         ->get()
    //         ->getRowArray();

    //     if (!$order) {
    //         throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    //     }

    //     // Get test items for this order
    //     $testItems = $this->labOrderItemModel->builder()
    //         ->select('lab_order_items.*, lab_tests.test_name')
    //         ->join('lab_tests', 'lab_tests.id = lab_order_items.test_id')
    //         ->where('lab_order_items.order_id', $orderId)
    //         ->get()
    //         ->getResultArray();

    //     // Get files for this order
    //     $files = $this->labOrderFileModel->getFilesByOrderItem($orderId);

    //     $data = [
    //         'order' => $order,
    //         'testItems' => $testItems,
    //         'files' => $files,
    //         'title' => 'View Lab Report'
    //     ];

    //     return view('laboratory/view_report_page', $data);
    // }



    // public function update_report($orderId)
    // {
    //     $remarks = $this->request->getPost('remarks');

    //     $this->labOrderModel->update($orderId, ['remarks' => $remarks]);

    //     return redirect()->to(base_url('laboratory/reports'))->with('success', 'Report updated successfully.');
    // }


    // public function delete_report($orderId)
    // {

    //     $files = $this->labOrderFileModel->getFilesByOrderItem($orderId);
    //     foreach ($files as $file) {
    //         $filePath = ROOTPATH . 'public/uploads/laboratory/' . $file['file_path'];
    //         if (is_file($filePath)) {
    //             unlink($filePath);
    //         }
    //     }
    //     $this->labOrderFileModel->where('lab_order_item_id', $orderId)->delete();


    //     $this->labOrderModel->delete($orderId);

    //     return redirect()->to(base_url('laboratory/reports'))->with('success', 'Report and associated files deleted successfully.');
    // }
}
