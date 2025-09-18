<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\RoleModel; // Add this line to include the RoleModel
use App\Models\Pharmacy\PharmacySalesPersonModel;
use App\Models\DoctorModel; // Assuming you have this model for doctors

class ProfileController extends BaseController
{
    public function index()
    {
        $session = session();
        $userId = $session->get('user_id');
        $userRoleId = $session->get('role_id');

        if (!$userId) {
            return redirect()->to('/login');
        }

        $userModel = new UserModel();
        $roleModel = new RoleModel(); // Instantiate the RoleModel

        $userData = $userModel->find($userId);

        if (!$userData) {
            return redirect()->to('/login')->with('error', 'User not found.');
        }

        $profileData = [];
        $profileData['main'] = $userData; // Main user table data

        // Fetch additional data based on the user's role
        if ($userRoleId == 8) { // Pharmacy Sales Person
            $salesPersonModel = new PharmacySalesPersonModel();
            $profileData['specific'] = $salesPersonModel->where('email', $userData['email'])->first();
        } elseif ($userRoleId == 2) { // Doctor
            // Assuming you have a 'user_id' column in your doctors table
            $doctorModel = new DoctorModel();
            $profileData['specific'] = $doctorModel->where('user_id', $userId)->first();
        }

        // Fetch the role name from the roles table
        $role = $roleModel->find($userRoleId);
        $roleName = $role['name'] ?? 'Unknown Role';

        $data = [
            'title' => 'My Profile',
            'profileData' => $profileData,
            'roleName' => $roleName, // Pass the role name to the view
            'role_id' => $userRoleId
        ];

        return view('profile/index', $data);
    }

    // Method to show the profile edit form
    public function edit()
    {
        $session = session();
        $userId = $session->get('user_id');
        $userRoleId = $session->get('role_id');

        if (!$userId) {
            return redirect()->to('/login');
        }

        $userModel = new UserModel();
        $roleModel = new RoleModel();
        $userData = $userModel->find($userId);

        if (!$userData) {
            return redirect()->to('/profile')->with('error', 'User not found.');
        }

        $profileData = [];
        $profileData['main'] = $userData; // Main user table data

        // Fetch additional data based on the user's role
        if ($userRoleId == 8) { // Pharmacy Sales Person
            $salesPersonModel = new PharmacySalesPersonModel();
            $profileData['specific'] = $salesPersonModel->where('email', $userData['email'])->first();
        }
        // Add more conditions for other roles if they have a separate profile table

        $role = $roleModel->find($userRoleId);
        $roleName = $role['name'] ?? 'Unknown Role';

        $data = [
            'title' => 'Edit Profile',
            'profileData' => $profileData,
            'roleName' => $roleName,
            'role_id' => $userRoleId
        ];

        return view('profile/edit', $data);
    }

 
    public function update()
    {
        $session = session();
        $userId = $session->get('user_id');
        $userRoleId = $session->get('role_id');

        if (!$userId) {
            return redirect()->to('/login');
        }

        // Define validation rules
        $rules = [
            'first_name' => 'required|min_length[2]|max_length[100]',
            'last_name' => 'required|min_length[2]|max_length[100]',
            'phone_number' => 'required|numeric|min_length[10]|max_length[15]',
            'address' => 'permit_empty',
            'email' => "required|valid_email|is_unique[users.email,id,{$userId}]"
        ];

        // Password is optional and only validated if provided
        if ($this->request->getPost('password')) {
            $rules['password'] = 'required|min_length[8]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userModel = new UserModel();
        $userData = $userModel->find($userId);

        if (!$userData) {
            return redirect()->to('/profile')->with('error', 'User not found.');
        }

        // Prepare data for the users table
        $updateData = [
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'phone_number' => $this->request->getPost('phone_number'),
            'address' => $this->request->getPost('address'),
            'email' => $this->request->getPost('email'),
        ];

        // Pass the password to the model's callback, which will hash it automatically
        if ($this->request->getPost('password')) {
            $updateData['password'] = $this->request->getPost('password');
        }

        // Update the users table
        if (!$userModel->update($userId, $updateData)) {
            return redirect()->back()->withInput()->with('error', 'Failed to update user profile.');
        }

        // Update role-specific table if necessary
        if ($userRoleId == 8) { // Pharmacy Sales Person
            $salesPersonModel = new PharmacySalesPersonModel();
            $salesPerson = $salesPersonModel->where('email', $userData['email'])->first();
            if ($salesPerson) {
                $salesPersonModel->update($salesPerson['id'], [
                    'first_name' => $this->request->getPost('first_name'),
                    'last_name' => $this->request->getPost('last_name'),
                    'phone' => $this->request->getPost('phone_number'),
                    'email' => $this->request->getPost('email'),
                    'address' => $this->request->getPost('address'),
                ]);
            }
        }
        // Add more conditions for other roles (e.g., Doctors, etc.)

        return redirect()->to('/profile')->with('success', 'Profile updated successfully!');
    }
}
