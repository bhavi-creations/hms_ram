<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel; // Import the UserModel
use App\Models\DoctorModel; // Import the DoctorModel to fetch doctor_id

class Auth extends BaseController
{
    // Method to display the login form
    public function login()
    {
        // Load the login view
        return view('auth/login');
    }

    // Method to handle the login attempt (POST request)
    public function loginAttempt()
    {
        $session = session();
        $userModel = new UserModel();
        $doctorModel = new DoctorModel(); // Instantiate DoctorModel

        // Get credentials from the form
        $usernameOrEmail = $this->request->getPost('username_or_email');
        $password = $this->request->getPost('password');

        // Validate input
        if (empty($usernameOrEmail) || empty($password)) {
            $session->setFlashdata('error', 'Please enter both username/email and password.');
            return redirect()->to('/login');
        }

        // Try to find the user by username or email
        $user = $userModel->where('username', $usernameOrEmail)
            ->orWhere('email', $usernameOrEmail)
            ->first();

        if ($user) {
            // Check if the user's status is 'inactive'
            if ($user['status'] == 'inactive') {
                $session->setFlashdata('error', 'Your account has been deactivated. Please contact an administrator.');
                return redirect()->to('/login');
            }
            
            // User found, verify password
            if (password_verify($password, $user['password'])) {
                // Password matches, prepare session data
                $ses_data = [
                    'user_id'    => $user['id'],
                    'username'   => $user['username'],
                    'email'      => $user['email'],
                    'first_name' => $user['first_name'],
                    'last_name'  => $user['last_name'],
                    'role_id'    => $user['role_id'],
                    'isLoggedIn' => TRUE
                ];

                // --- NEW LOGIC FOR DOCTOR ID ---
                if ($user['role_id'] == 2) { // Assuming 2 is Doctor
                    $doctor = $doctorModel->where('user_id', $user['id'])->first();
                    if ($doctor) {
                        $ses_data['doctor_id'] = $doctor['id'];
                    } else {
                        log_message('error', 'User with ID ' . $user['id'] . ' has role_id 2 but no linked doctor profile.');
                        $session->setFlashdata('error', 'Doctor profile not found for this user.');
                        return redirect()->to('/login');
                    }
                }
                // --- END NEW LOGIC ---

                // Determine allowed modules by role
                $allowedModules = [];
                switch ($user['role_id']) {
                    case 1: // HMS Admin
                        $allowedModules = ['all']; // Full access
                        break;
                    case 7: // Pharmacy Manager (Corrected from 3)
                        $allowedModules = ['pharmacy']; // Pharmacy full module
                        break;
                    case 8: // Pharmacy Salesperson (Corrected from 4)
                        $allowedModules = ['pharmacy_sales', 'pharmacy_reports']; // Limited pharmacy modules
                        break;
                    // Add other roles if needed
                    default:
                        $allowedModules = [];
                        break;
                }
                $ses_data['allowed_modules'] = $allowedModules;

                // Set session
                $session->set($ses_data);

                // Update last login time
                $userModel->update($user['id'], ['last_login' => date('Y-m-d H:i:s')]);

                // Redirect based on role_id (optional)
                if ($user['role_id'] == 1) { // Admin
                    return redirect()->to('/dashboard')->with('success', 'Welcome, ' . $user['first_name'] . ' (Admin)!');
                } elseif ($user['role_id'] == 2) { // Doctor
                    return redirect()->to('/doctor/dashboard')->with('success', 'Welcome, Dr. ' . $user['last_name'] . '!');
                } elseif ($user['role_id'] == 7) { // Pharmacy Manager (Corrected)
                    return redirect()->to('/pharmacy/dashboard')->with('success', 'Welcome, ' . $user['first_name'] . ' (Pharmacy Manager)!');
                } elseif ($user['role_id'] == 8) { // Pharmacy Salesperson (Corrected)
                    return redirect()->to('/pharmacy/sales')->with('success', 'Welcome, ' . $user['first_name'] . ' (Salesperson)!');
                } else {
                    return redirect()->to('/dashboard')->with('success', 'Welcome, ' . $user['first_name'] . '!');
                }
            } else {
                // Password mismatch
                $session->setFlashdata('error', 'Invalid password.');
            }
        } else {
            // User not found
            $session->setFlashdata('error', 'Username or email not found.');
        }

        // If login fails, redirect back to login page
        return redirect()->to('/login');
    }

    // Method to handle user logout
    public function logout()
    {
        $session = session();
        $session->destroy(); // Destroy all session data
        $session->setFlashdata('success', 'You have been logged out successfully.');
        return redirect()->to('/login'); // Redirect to login page
    }
}
