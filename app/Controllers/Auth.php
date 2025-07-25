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
            // User found, verify password
            if (password_verify($password, $user['password'])) {
                // Password matches, prepare session data
                $ses_data = [
                    'user_id'     => $user['id'],
                    'username'    => $user['username'],
                    'email'       => $user['email'],
                    'first_name'  => $user['first_name'],
                    'last_name'   => $user['last_name'],
                    'role_id'     => $user['role_id'],
                    'isLoggedIn'  => TRUE
                ];

                // --- NEW LOGIC FOR DOCTOR ID ---
                // Assuming role_id 2 is for Doctors (you might need to adjust this based on your roles table)
                if ($user['role_id'] == 2) { // Check if the logged-in user is a doctor
                    $doctor = $doctorModel->where('user_id', $user['id'])->first();
                    if ($doctor) {
                        $ses_data['doctor_id'] = $doctor['id']; // Store the doctor's ID in the session
                    } else {
                        // Log an error or handle case where a user with role_id=2 has no linked doctor profile
                        log_message('error', 'User with ID ' . $user['id'] . ' has role_id 2 but no linked doctor profile.');
                        $session->setFlashdata('error', 'Doctor profile not found for this user.');
                        return redirect()->to('/login');
                    }
                }
                // --- END NEW LOGIC ---

                $session->set($ses_data);

                // Update last login time
                $userModel->update($user['id'], ['last_login' => date('Y-m-d H:i:s')]);

                // Redirect based on role (optional, but good for specific dashboards)
                if ($user['role_id'] == 1) { // Assuming role_id 1 is for Admin
                    return redirect()->to('/dashboard')->with('success', 'Welcome, ' . $user['first_name'] . ' (Admin)!');
                } elseif ($user['role_id'] == 2) { // Assuming role_id 2 is for Doctor
                    return redirect()->to('/doctor/dashboard')->with('success', 'Welcome, Dr. ' . $user['last_name'] . '!');
                } else {
                    // Default redirect for other roles or if role_id is not specifically handled
                    return redirect()->to('/dashboard')->with('success', 'Welcome, ' . $user['first_name'] . '!');
                }

            } else {
                // Password does not match
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
