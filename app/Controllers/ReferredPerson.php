<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ReferredPersonModel;
use CodeIgniter\HTTP\ResponseInterface;

class ReferredPerson extends BaseController
{
    /**
     * @var ReferredPersonModel The model instance for referred persons.
     */
    protected $referredPersonModel;

    /**
     * Constructor: Initializes the model and helpers.
     */
    public function __construct()
    {
        // Load the model
        $this->referredPersonModel = new ReferredPersonModel();
        // Ensure form helper is loaded for validation
        helper(['form', 'url']);
    }

    // === R - Read (List All) ===
    public function index()
    {
        // Fetch all referred persons data
        $data = [
            'persons' => $this->referredPersonModel->findAll(),
            'title' => 'Referred Persons Management', // Updated title for layout
        ];

        // Load the index view
        return view('referral/index', $data);
    }



    public function create()
    {
        // Show blank form or errors
        return view('referral/create', [
            'validation' => \Config\Services::validation(),
        ]);
    }

    public function store()
    {
        $data = [
            'name'         => $this->request->getPost('name'),
            'type'         => $this->request->getPost('type'),
            'contact_info' => $this->request->getPost('contact_info'),
        ];

        // Built-in validation from the model
        if (!$this->referredPersonModel->insert($data)) {
            // Return to form with errors
            return view('referral/create', [
                'validation' => $this->referredPersonModel->validation,
            ]);
        }

        // Success - Set flash and redirect to list
        session()->setFlashdata('success', 'Referred person added successfully!');
        return redirect()->to('referred-persons');
    }


    // Show edit form with existing data
    public function edit($id)
    {
        $person = $this->referredPersonModel->find($id);

        if (!$person) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Referred person with ID $id not found");
        }

        return view('referral/edit', [
            'person' => $person,
            'validation' => \Config\Services::validation(),
        ]);
    }


 

    // Process update submission
    public function update($id)
    {
        $person = $this->referredPersonModel->find($id);

        if (!$person) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Referred person with ID $id not found");
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'type' => $this->request->getPost('type'),
            'contact_info' => $this->request->getPost('contact_info'),
        ];

        if (!$this->referredPersonModel->update($id, $data)) {
            // On validation error, reload form with errors and old input
            return view('referral/edit', [
                'person' => array_merge($person, $data), // merge old values for filling form
                'validation' => $this->referredPersonModel->validation,
            ]);
        }

        session()->setFlashdata('success', 'Referred person updated successfully!');
        return redirect()->to('referred-persons');
    }

    // Delete a referred person record
    public function delete($id)
    {
        $person = $this->referredPersonModel->find($id);

        if (!$person) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Referred person with ID $id not found");
        }

        $this->referredPersonModel->delete($id);

        session()->setFlashdata('success', 'Referred person deleted successfully!');
        return redirect()->to('referred-persons');
    }
}
