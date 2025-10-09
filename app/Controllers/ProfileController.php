<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\RoleModel;
use App\Models\Pharmacy\PharmacySalesPersonModel;
use App\Models\DoctorModel;
use App\Models\PatientModel; // Assuming you have a PatientModel

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
        $roleModel = new RoleModel();

        $userData = $userModel->find($userId);

        if (!$userData) {
            return redirect()->to('/login')->with('error', 'User not found.');
        }

        $specificData = [];
        $imageFilename = null;
        $relativeUploadPath = null;

        // Fetch additional data based on the user's role
        if ($userRoleId == 2) { // Doctor
            $doctorModel = new DoctorModel();
            $specificData = $doctorModel->where('user_id', $userId)->first();
            $relativeUploadPath = 'uploads/doctors/';
        } elseif ($userRoleId == 8) { // Pharmacy Sales Person
            $salesPersonModel = new PharmacySalesPersonModel();
            $specificData = $salesPersonModel->where('email', $userData['email'])->first();
            $relativeUploadPath = 'uploads/pharmacy_sales_persons/';
        }

        // --- CRITICAL DEBUGGING SECTION: DUMP RAW DATA ---
        echo "<h1>CRITICAL DEBUG: Specific User Data Array (STOP)</h1>";
        echo "<p>Look for the key/column name that holds the profile picture filename (e.g., 'doc_photo', 'avatar').</p>";

        // Use a <pre> tag to format the array output cleanly
        echo "<pre>";
        print_r($specificData);
        echo "</pre>";

        die; // Execution stops here to show the raw data
        // --- END DEBUGGING SECTION ---

        // The rest of the logic below this point will not run until 'die' is removed

        $profile = array_merge($userData, $specificData ?? []);

        // --- UNIVERSAL PROFILE PICTURE LOGIC ---
        // 1. Check for the image filename in the specific data first, using common keys
        if (!empty($specificData)) {
            // Check for profile_picture, image, photo, or profile_img column names
            $imageFilename = $specificData['profile_picture'] ?? $specificData['image'] ?? $specificData['photo'] ?? $specificData['profile_img'] ?? null;
        }
        // 2. Fallback check on the main user data if not found in specific data
        if (empty($imageFilename)) {
            $imageFilename = $userData['profile_picture'] ?? $userData['image'] ?? $userData['photo'] ?? $userData['profile_img'] ?? null;
        }

        $defaultImage = 'dist/img/default-avatar.png';

        if (empty($relativeUploadPath)) {
            $relativeUploadPath = 'uploads/users/';
        }

        if (!empty($imageFilename)) {
            $fullPathSegment = 'public/' . $relativeUploadPath . urlencode($imageFilename);
            $profileImageUrl = base_url($fullPathSegment);
        } else {
            $profileImageUrl = base_url($defaultImage);
        }
        // --- END UNIVERSAL PROFILE PICTURE LOGIC ---

        $role = $roleModel->find($userRoleId);
        $roleName = $role['name'] ?? 'Unknown Role';

        $data = [
            'title' => 'My Profile',
            'profile' => $profile,
            'profileImageUrl' => $profileImageUrl,
            'roleName' => $roleName,
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

        $specificData = [];
        // Fetch additional data based on the user's role
        if ($userRoleId == 8) { // Pharmacy Sales Person
            $salesPersonModel = new PharmacySalesPersonModel();
            $specificData = $salesPersonModel->where('email', $userData['email'])->first();
        } elseif ($userRoleId == 2) { // Doctor
            $doctorModel = new DoctorModel();
            $specificData = $doctorModel->where('user_id', $userId)->first();
        }
        // Add more conditions for other roles if they have a separate profile table

        $profile = array_merge($userData, $specificData ?? []);

        $role = $roleModel->find($userRoleId);
        $roleName = $role['name'] ?? 'Unknown Role';

        $data = [
            'title' => 'Edit Profile',
            'profile' => $profile,
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
            'email' => "required|valid_email|is_unique[users.email,id,{$userId}]",
            'profile_picture' => 'permit_empty|uploaded[profile_picture]|max_size[profile_picture,1024]|ext_in[profile_picture,jpg,jpeg,png]',
            'signature_image' => 'permit_empty|uploaded[signature_image]|max_size[signature_image,512]|ext_in[signature_image,png]',
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

        // Determine the upload folder and model based on role
        $uploadFolder = 'users'; // Default folder
        $specificModel = null;
        $specificDataField = 'user_id';

        if ($userRoleId == 2) { // Doctor
            $specificModel = new DoctorModel();
            $uploadFolder = 'doctors';
            $specificDataField = 'user_id';
        } elseif ($userRoleId == 8) { // Pharmacy Sales Person
            $specificModel = new PharmacySalesPersonModel();
            $uploadFolder = 'pharmacy_sales_persons';
            $specificDataField = 'email'; // Sales person is linked by email
        }
        // Add more role conditions here...


        // Handle file uploads (Profile Picture)
        $profilePicFile = $this->request->getFile('profile_picture');
        $profilePicFilename = $userData['profile_picture'] ?? null;

        if ($profilePicFile && $profilePicFile->isValid() && !$profilePicFile->hasMoved()) {
            // Delete old file if it exists and is not the default (omitted for safety in this demo)

            $profilePicFilename = $profilePicFile->getRandomName();
            // Move file to the correct role-specific folder within public/uploads/
            $profilePicFile->move(ROOTPATH . 'public/uploads/' . $uploadFolder, $profilePicFilename);
        }

        // Handle file uploads (Signature)
        $signatureFile = $this->request->getFile('signature_image');
        $signatureFilename = null;
        if ($specificModel) {
            $specificRecord = $specificModel->where($specificDataField, $specificDataField == 'user_id' ? $userId : $userData['email'])->first();
            $signatureFilename = $specificRecord['signature_image'] ?? null;
        }


        if ($signatureFile && $signatureFile->isValid() && !$signatureFile->hasMoved()) {
            // (You'd need logic here to safely delete the old file)
            $signatureFilename = $signatureFile->getRandomName();
            $signatureFile->move(ROOTPATH . 'public/uploads/' . $uploadFolder, $signatureFilename);
        }

        // Prepare data for the users table
        $updateData = [
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'phone_number' => $this->request->getPost('phone_number'),
            'address' => $this->request->getPost('address'),
            'email' => $this->request->getPost('email'),
        ];

        // Only update profile_picture in users table if it's the main storage
        // If the role has a specific table, we typically store the picture there.
        if (!$specificModel && $profilePicFilename) { // This means it's a generic user
            $updateData['profile_picture'] = $profilePicFilename;
        }

        // Pass the password to the model's callback, which will hash it automatically
        if ($this->request->getPost('password')) {
            $updateData['password'] = $this->request->getPost('password');
        }

        // Update the users table
        if (!$userModel->update($userId, $updateData)) {
            return redirect()->back()->withInput()->with('error', 'Failed to update user profile (main).');
        }


        // Update role-specific table if necessary (where specific models exist)
        if ($specificModel) {
            $specificRecord = $specificModel->where($specificDataField, $specificDataField == 'user_id' ? $userId : $userData['email'])->first();

            $specificUpdateData = [
                'first_name' => $this->request->getPost('first_name'),
                'last_name' => $this->request->getPost('last_name'),
                // Check if the specific table uses 'phone' or 'phone_number'
                'phone' => $this->request->getPost('phone_number'),
                'address' => $this->request->getPost('address'),
                'email' => $this->request->getPost('email'),
                // Specific fields for doctors/salespersons that might be in the edit form
                'date_of_birth' => $this->request->getPost('date_of_birth'),
                'gender' => $this->request->getPost('gender'),
                'qualification' => $this->request->getPost('qualification'),
                'bio' => $this->request->getPost('bio'),
            ];

            // Only update the profile picture/signature in the specific table
            if ($profilePicFilename) {
                // Note: We use 'profile_picture' here, ensure the Doctor/SalesPerson table uses this key
                $specificUpdateData['profile_picture'] = $profilePicFilename;
            }
            if ($signatureFilename) {
                $specificUpdateData['signature_image'] = $signatureFilename;
            }


            if ($specificRecord) {
                // Update existing record
                $specificModel->update($specificRecord['id'], $specificUpdateData);
            } else {
                // Insert new record if it doesn't exist (can happen if the user record was created first)
                $specificUpdateData[$specificDataField] = $specificDataField == 'user_id' ? $userId : $userData['email'];
                $specificModel->insert($specificUpdateData);
            }
        }

        return redirect()->to('/profile')->with('success', 'Profile updated successfully!');
    }
}
