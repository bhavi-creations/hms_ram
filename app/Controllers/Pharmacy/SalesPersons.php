<?php

namespace App\Controllers\Pharmacy;

use App\Controllers\BaseController;
use App\Models\Pharmacy\PharmacySalesPersonModel;

class SalesPersons extends BaseController
{
    protected $salesPersonModel;

    public function __construct()
    {
        $this->salesPersonModel = new PharmacySalesPersonModel();
    }

    public function index()
    {
        $data['sales_persons'] = $this->salesPersonModel->findAll();
        return view('pharmacy/sales_persons/index', $data);
    }

    public function create()
    {
        return view('pharmacy/sales_persons/create');
    }

    public function store()
    {
        $validationRules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'phone' => 'required|numeric|min_length[10]|max_length[15]',
            'email' => 'required|valid_email|is_unique[pharmacy_sales_persons.email]',
            'address' => 'permit_empty',
        ];

        if (!$this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $salespersonId = $this->salesPersonModel->generateSalesPersonCode();

        $salespersonData = [
            'salesperson_id' => $salespersonId,
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'phone' => $this->request->getPost('phone'),
            'address' => $this->request->getPost('address'),
            'email' => $this->request->getPost('email'),
            'status' => 1 // New salespeople are active by default
        ];

        // Insert salesperson record
        if ($this->salesPersonModel->insert($salespersonData)) {

            // Use the main UserModel
            $userModel = new \App\Models\UserModel();

            // Create the user login record based on the defined rules
            $userData = [
                'role_id' => 8, // Role ID for 'Pharmacy_Sales_Person'
                'first_name' => $salespersonData['first_name'],
                'last_name' => $salespersonData['last_name'],
                'username' => $salespersonData['salesperson_id'], // Use the generated salesperson_id as username
                'email' => $salespersonData['email'],
                'password' => $salespersonData['phone'], // Set the phone number as the password
                'phone_number' => $salespersonData['phone'],
                'status' => 'active' // Set status to 'active'
            ];

            // The UserModel's beforeInsert callback will automatically hash the password
            if ($userModel->insert($userData)) {
                // FIX: Change 'phone' to 'phone_number' to match the array key
                return redirect()->to('pharmacy/salespersons')->with('success', 'Salesperson added successfully. Username: ' . $userData['username'] . ', Password: ' . $userData['phone_number']);
            } else {
                // If user creation fails, delete the salesperson record to prevent orphaned data
                $this->salesPersonModel->delete($this->salesPersonModel->getInsertID());
                return redirect()->back()->withInput()->with('error', 'Failed to create user account for salesperson.');
            }
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to add salesperson.');
        }
    }

    public function edit($id = null)
    {
        $data['salesperson'] = $this->salesPersonModel->find($id);

        if (empty($data['salesperson'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Cannot find the salesperson item: ' . $id);
        }

        return view('pharmacy/sales_persons/edit', $data);
    }

    public function update($id = null)
    {
        $validationRules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'phone' => 'required|numeric|min_length[10]|max_length[15]',
            'email' => 'required|valid_email|is_unique[pharmacy_sales_persons.email,id,' . $id . ']',
            'address' => 'permit_empty',
            'status' => 'required|integer' // ADD STATUS VALIDATION
        ];

        if (!$this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'phone' => $this->request->getPost('phone'),
            'address' => $this->request->getPost('address'),
            'email' => $this->request->getPost('email'),
            'status' => $this->request->getPost('status') // GET STATUS FROM THE FORM
        ];

        if ($this->salesPersonModel->update($id, $data)) {
            return redirect()->to('pharmacy/salespersons')->with('success', 'Salesperson updated successfully!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update salesperson.');
        }
    }

    public function delete($id = null)
    {
        if ($this->salesPersonModel->delete($id)) {
            return redirect()->to('pharmacy/salespersons')->with('success', 'Salesperson deleted successfully!');
        } else {
            return redirect()->to('pharmacy/salespersons')->with('error', 'Failed to delete salesperson.');
        }
    }

    /**
     * Toggles the status of a salesperson (active/inactive).
     */
    public function toggleStatus($id = null)
    {
        $salesperson = $this->salesPersonModel->find($id);

        if (empty($salesperson)) {
            return redirect()->to('pharmacy/salespersons')->with('error', 'Salesperson not found.');
        }

        $newStatus = ($salesperson['status'] == 1) ? 0 : 1;

        $data = ['status' => $newStatus];

        if ($this->salesPersonModel->update($id, $data)) {
            $message = ($newStatus == 1) ? 'Salesperson activated successfully.' : 'Salesperson deactivated successfully.';
            return redirect()->to('pharmacy/salespersons')->with('success', $message);
        } else {
            return redirect()->to('pharmacy/salespersons')->with('error', 'Failed to update salesperson status.');
        }
    }

    public function profile($userId = null)
{
    $session = session();
    $loggedInUserId = $session->get('user_id');
    $loggedInUserRoleId = $session->get('role_id');
    
    // Determine the ID of the salesperson to display
    if ($userId === null) {
        // If no ID is provided, assume the logged-in user wants to see their own profile
        $userId = $loggedInUserId;
    }

    // Check permissions
    if ($loggedInUserRoleId == 8 && $userId != $loggedInUserId) {
        // A Sales Person can only view their own profile
        return redirect()->back()->with('error', 'You are not authorized to view this profile.');
    }

    // Load the models
    $userModel = new \App\Models\UserModel();
    $salesPersonModel = new \App\Models\Pharmacy\PharmacySalesPersonModel();

    // Fetch user and salesperson data
    $user = $userModel->find($userId);
    if (!$user || $user['role_id'] != 8) {
        return redirect()->back()->with('error', 'Sales person not found or invalid user.');
    }

    $salesPerson = $salesPersonModel->where('email', $user['email'])->first();

    if (!$salesPerson) {
        return redirect()->back()->with('error', 'Sales person profile not found.');
    }

    $data = [
        'title' => 'My Profile',
        'salesPerson' => $salesPerson,
        'user' => $user
    ];

    return view('pharmacy/sales_persons/profile', $data);
}
}
