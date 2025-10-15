<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\DepartmentModel;

class Department extends BaseController
{
    /**
     * @var DepartmentModel
     */
    protected $departmentModel;

    public function __construct()
    {
        // Load the model in the constructor
        $this->departmentModel = new DepartmentModel();
    }

    // --- READ: List all departments ---
    public function index()
    {
        $data = [
            'title' => 'Manage Hospital Departments',
            'departments' => $this->departmentModel->findAll(),
            'page_title' => 'Departments List',
        ];

        // CORRECT WAY: Only return the final view, the view file itself 
        // knows to load the main layout via 'extend'.
        return view('department/index', $data);
    }

    // Example for create()
    public function create()
    {
        $data = [
            'title' => 'Create New Department',
            'page_title' => 'Add New Department',
            'validation' => \Config\Services::validation(),
        ];

        // CORRECT WAY: Only return the final view.
        return view('department/create', $data);
    }


    // --- CREATE: Save a new department ---
    public function store()
    {
        // 1. Get POST data
        $postData = $this->request->getPost();

        // 2. Validate data using the model's rules
        if (!$this->departmentModel->validate($postData)) {
            // Validation failed, redirect back with errors and input data
            return redirect()->back()->withInput()->with('errors', $this->departmentModel->errors());
        }

        // 3. Save to database
        $this->departmentModel->save($postData);

        // 4. Redirect with a success message
        return redirect()->to(base_url('departments'))->with('success', 'Department "' . esc($postData['name']) . '" created successfully.');
    }


    // ... apply this single return view() structure to edit() as well ...
   
    public function edit($id = null)
    {
        // 1. Check if ID is provided
        if ($id === null) {
            // Handle case where no ID is provided (e.g., redirect or show 404)
            return redirect()->to(base_url('departments'))->with('error', 'No department ID provided for editing.');
        }
        
        // 2. Fetch the department record from the database
        $department = $this->departmentModel->find($id);

        // 3. Handle case where department is not found (404)
        if (!$department) {
            // Optional: Show a 404 page or redirect with error
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Cannot find department with ID: ' . $id);
        }

        // 4. Prepare data array to pass to the view
        $data = [
            'title' => 'Edit Department',
            'page_title' => 'Edit Department',
            'validation' => \Config\Services::validation(),
            'department' => $department, // <<-- THIS IS THE CRUCIAL LINE
        ];

        // 5. Pass data to the view
        return view('department/edit', $data);
    }
    // --- UPDATE: Update an existing department ---
    public function update($id = null)
    {
        // 1. Get POST data
        $postData = $this->request->getPost();

        // 2. Attempt to validate the data, passing the ID for the is_unique check
        // CI4 validation rule `is_unique[table.field,id,{id}]` needs the {id} placeholder to work
        if (!$this->departmentModel->validate($postData)) {
            // Validation failed, redirect back with errors and input data
            return redirect()->back()->withInput()->with('errors', $this->departmentModel->errors());
        }

        // 3. Update the database record
        // The save method handles both insert and update based on primary key presence
        $this->departmentModel->update($id, $postData);

        // 4. Redirect with a success message
        return redirect()->to(base_url('departments'))->with('success', 'Department "' . esc($postData['name']) . '" updated successfully.');
    }

    // --- DELETE: Soft delete a department ---
   public function delete($id = null)
{
    // 1. Ensure it's an AJAX request (Best Practice)
    if (!$this->request->isAJAX()) {
        return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Access Denied.']);
    }

    // 2. Find the department and handle 404
    $department = $this->departmentModel->find($id);

    if (!$department) {
        return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Department not found.']);
    }

    // 3. Execute soft delete and handle success/failure
    try {
        $isDeleted = $this->departmentModel->delete($id);
    
        if ($isDeleted) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Department "' . esc($department['name']) . '" deleted successfully.']);
        } else {
            // This happens if the delete fails, but no exception is thrown
            return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Database operation failed during deletion.']);
        }
    } catch (\Exception $e) {
        // Log the error and return a generic server error
        log_message('error', 'Department deletion failed: ' . $e->getMessage());
        return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'A server error occurred during deletion.']);
    }
}
}
