<?php
namespace App\Controllers\Staff;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Profile extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        // Initialize the UserModel to fetch user data
        $this->userModel = new UserModel();
        // Ensure request object is available for POST methods
        $this->helpers = ['form', 'url'];
    }

    // --- Core Profile View ---

    /**
     * Display the current logged-in staff user's profile information.
     */
    public function index()
    {
        // --- AUTHENTICATION & SESSION CHECK ---
        $roleId = session()->get('role_id');
        $userId = session()->get('id');

        // Robust check: If 'id' is null, check if 'user_id' was used during login
        if ($userId === null) {
            $userId = session()->get('user_id');
        }

        if ($userId === null || $roleId === null) {
            // If the user ID or role ID is missing, redirect to login
            return redirect()->to('login')->with('error', 'Authentication required to view your profile.');
        }
        
        // 1. Fetch user data with role name
        $userData = $this->userModel
            ->select('users.*, roles.name as role_name')
            ->join('roles', 'roles.id = users.role_id')
            ->where('users.id', $userId)
            ->first();

        if (!$userData) {
            return redirect()->to('dashboard')->with('error', 'User data not found for the current session.');
        }
        
        $data = [
            'user' => $userData,
            'title' => 'My Profile',
        ];

        // Load the profile view
        return view('staff/profile', $data); 
    }

    // --- Update Profile (BIO) Logic ---

    /**
     * Handles the form submission to update the user's personal details.
     */
    public function updateProfile()
    {
        $userId = session()->get('id') ?? session()->get('user_id');
        if ($userId === null) {
            return redirect()->to('login')->with('error', 'Authentication required.');
        }

        // Get the current user's email to exclude them from the unique email validation check
        $currentUser = $this->userModel->select('email')->find($userId);
        $currentEmail = $currentUser['email'] ?? null;

        $rules = [
            'first_name' => 'required|max_length[100]',
            'last_name' => 'required|max_length[100]',
            // IMPORTANT: This validation uses the $userId variable to exclude the current record
            'email' => "required|valid_email|is_unique[users.email,id,{$userId}]", 
            'phone_number' => 'permit_empty|max_length[20]',
            'address' => 'permit_empty|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors())
                ->with('active_tab', 'details'); // Stay on the details tab
        }

        $data = [
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'email' => $this->request->getPost('email'),
            'phone_number' => $this->request->getPost('phone_number'),
            'address' => $this->request->getPost('address'),
        ];

        // --- THE FIX: Skip model validation since we just passed controller validation ---
        // This ensures the update relies on the successful controller check (which handles the uniqueness exclusion).
        $this->userModel->skipValidation(true);

        if ($this->userModel->update($userId, $data)) {
            // After a successful update, turn validation back on for subsequent model calls
            $this->userModel->skipValidation(false);
            return redirect()->to('profile')->with('success', 'Profile details updated successfully.');
        } else {
            // Restore validation state
            $this->userModel->skipValidation(false);

            // Enhanced Error Reporting (from previous turn)
            $modelErrors = $this->userModel->errors();
            
            if (!empty($modelErrors)) {
                $errorMessages = implode(' | ', array_values($modelErrors));
                log_message('error', 'Profile Update Model Error: ' . $errorMessages);
                return redirect()->back()->with('error', 'Failed to update profile details (Model Error): ' . $errorMessages);
            }
            
            $dbError = $this->userModel->db()->error();
            $errorMessage = 'Failed to update profile details.';

            if ($dbError['code'] !== 0) {
                $errorMessage .= ' (Database Error: ' . $dbError['message'] . ')';
            } else {
                $errorMessage .= ' (Possible silent failure. Check $allowedFields in UserModel.)';
            }

            log_message('error', 'Profile Update Failure: ' . $errorMessage);
            return redirect()->back()->with('error', $errorMessage);
        }
    }

    // --- Change Password Logic ---

    /**
     * Handles the form submission to change the user's password.
     */
    public function changePassword()
    {
        $userId = session()->get('id') ?? session()->get('user_id');
        if ($userId === null) {
            return redirect()->to('login')->with('error', 'Authentication required.');
        }

        $rules = [
            'current_password' => 'required',
            'new_password' => 'required|min_length[8]',
            'confirm_password' => 'required|matches[new_password]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors())
                ->with('active_tab', 'security'); // Stay on the security tab
        }

        // 1. Verify current password
        $user = $this->userModel->find($userId);
        $currentPasswordPost = $this->request->getPost('current_password');

        if (!password_verify($currentPasswordPost, $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'The current password you entered is incorrect.')
                ->with('active_tab', 'security');
        }

        // 2. Get the new password (plain text)
        $newPassword = $this->request->getPost('new_password');
        
        // Ensure model validation is skipped here too, as password hashing is handled by beforeUpdate callback
        $this->userModel->skipValidation(true);
        
        if ($this->userModel->update($userId, ['password' => $newPassword])) {
            $this->userModel->skipValidation(false);
            return redirect()->to('profile')->with('success', 'Password changed successfully.');
        } else {
            $this->userModel->skipValidation(false);
            return redirect()->back()->with('error', 'Failed to change password.');
        }
    }


    
}
