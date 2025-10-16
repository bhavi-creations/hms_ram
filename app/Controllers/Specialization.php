<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\SpecializationModel;
use CodeIgniter\HTTP\ResponseInterface;

class Specialization extends BaseController
{
    /**
     * @var SpecializationModel
     */
    protected $specializationModel;

    public function __construct()
    {
        // Load the model
        $this->specializationModel = new SpecializationModel();
    }

    // --- READ: List all specializations ---
    public function index()
    {
        $data = [
            'title' => 'Manage Hospital Specializations',
            'specializations' => $this->specializationModel->findAll(), 
            'page_title' => 'Specializations List',
            'session' => session(),
        ];

        // FIX: Changed 'specializations/index' (plural) to 'specialization/index' (singular)
        return view('specialization/index', $data);
    }

    // --- CREATE: Show the create form ---
    public function create()
    {
        $data = [
            'title' => 'Create New Specialization',
            'page_title' => 'Add New Specialization',
            'validation' => \Config\Services::validation(),
        ];

        // FIX: Changed 'specializations/create' (plural) to 'specialization/create' (singular)
        return view('specialization/create', $data);
    }

    // --- CREATE: Save a new specialization ---
    public function store()
    {
        $postData = $this->request->getPost();

        if (!$this->specializationModel->validate($postData)) {
            return redirect()->back()->withInput()->with('errors', $this->specializationModel->errors());
        }

        $this->specializationModel->save($postData);

        return redirect()->to(base_url('specializations'))
            ->with('success', 'Specialization "' . esc($postData['name']) . '" created successfully.');
    }

    // --- EDIT: Show the edit form ---
    public function edit($id = null)
    {
        if ($id === null) {
            return redirect()->to(base_url('specializations'))->with('error', 'No specialization ID provided for editing.');
        }
        
        $specialization = $this->specializationModel->find($id);

        if (!$specialization) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Cannot find specialization with ID: ' . $id);
        }

        $data = [
            'title' => 'Edit Specialization',
            'page_title' => 'Edit Specialization',
            'validation' => \Config\Services::validation(),
            'specialization' => $specialization, // This resolves the Undefined variable error
        ];

        // FIX: Changed 'specializations/edit' (plural) to 'specialization/edit' (singular)
        return view('specialization/edit', $data);
    }

    // --- UPDATE: Update an existing specialization ---
    public function update($id = null)
    {
        $postData = $this->request->getPost();

        // Dynamically replace the {id} placeholder in the 'is_unique' rule
        $rules = $this->specializationModel->getValidationRules(['except' => ['id']]);
        
        // Ensure the rule for 'name' is retrieved and updated
        if (isset($rules['name'])) {
            $rules['name'] = str_replace('{id}', $id, $rules['name']);
            $this->specializationModel->setValidationRules($rules);
        }


        // Run validation
        if (!$this->specializationModel->validate($postData)) {
            // Re-fetch the specialization if validation fails
            $specialization = $this->specializationModel->find($id);
            return redirect()->back()->withInput()->with('errors', $this->specializationModel->errors())
                ->with('specialization', $specialization); 
        }

        // Add the ID to the data array for the update method
        $postData['id'] = $id; 

        $this->specializationModel->save($postData); // save handles update if PK is present

        return redirect()->to(base_url('specializations'))
            ->with('success', 'Specialization "' . esc($postData['name']) . '" updated successfully.');
    }

    // --- DELETE: Soft delete a specialization ---
    public function delete($id = null)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Access Denied.']);
        }

        $specialization = $this->specializationModel->find($id);

        if (!$specialization) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Specialization not found.']);
        }

        try {
            $isDeleted = $this->specializationModel->delete($id);
        
            if ($isDeleted) {
                return $this->response->setJSON(['status' => 'success', 'message' => 'Specialization "' . esc($specialization['name']) . '" deleted successfully.']);
            } else {
                return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Database operation failed during deletion.']);
            }
        } catch (\Exception $e) {
            log_message('error', 'Specialization deletion failed: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'A server error occurred during deletion.']);
        }
    }
}
