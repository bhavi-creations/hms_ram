<?php

namespace App\Controllers\Users;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\RoleModel;

class Users extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        // Fetches users, relying on the overridden find() method in UserModel 
        // to automatically join role names, if the controller were fetching all users with role names.
        $data['users'] = $this->userModel->findAll();
        return view('users/list', $data);
    }

    public function view($id)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to(base_url('users'))->with('error', 'User not found.');
        }
        return view('users/view', ['user' => $user]);
    }

    public function edit($id)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to(base_url('users'))->with('error', 'User not found.');
        }
        return view('users/edit', ['user' => $user]);
    }

    public function save()
    {
        $userId = $this->request->getPost('id');

        // --- 1. Load Existing User Data ---
        // This is crucial for retrieving the 'username' and 'role_id' 
        // which are required by the UserModel validation but not present in the form.
        $existingUser = $this->userModel->find($userId);

        if (!$existingUser) {
            return redirect()->back()->with('error', 'User not found for update.');
        }

        // --- 2. Prepare Data for Update ---
        // We ensure all fields required by the Model's validation are present in this array.
        $updateData = [
            // CRITICAL: The 'id' must be in the data array to resolve the {id} placeholder 
            // used in the Model's is_unique rules for email and username.
            'id'           => $userId, 
            
            // Data from the form (uses post data)
            'first_name'   => $this->request->getPost('first_name'),
            'last_name'    => $this->request->getPost('last_name'),
            'phone_number' => $this->request->getPost('phone_number'),
            'address'      => $this->request->getPost('address'),
            'email'        => $this->request->getPost('email'),
            'status'       => $this->request->getPost('status'),
            
            // CRITICAL FIX: Add existing required fields not in the form
            'username'     => $existingUser['username'], 
            'role_id'      => $existingUser['role_id'],
        ];
        
        // Add password if provided (model handles hashing and 'permit_empty')
        if ($this->request->getPost('password')) {
            $updateData['password'] = $this->request->getPost('password');
        }
        
        // --- 3. Perform the Update (Relying ONLY on Model's built-in validation) ---
        if ($this->userModel->update($userId, $updateData)) {
            return redirect()->to(base_url('users'))->with('success', 'User updated successfully!');
        }
        
        // --- 4. Handle Failure ---
        $errors = $this->userModel->errors();
        
        if (!empty($errors)) {
            // Failure was due to validation (e.g., duplicate username/email).
            // Pass the model's errors back to the view.
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        // Final fallback error if the database update failed for a non-validation reason
        return redirect()->back()->withInput()->with('error', 'Failed to save user due to a system error.');
    }
    
    public function register()
    {
        $roleModel = new \App\Models\RoleModel();
        $roles = $roleModel->findAll();

        return view('users/register', ['roles' => $roles]);
    }
}
