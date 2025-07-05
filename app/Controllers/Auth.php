<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel; // Import the UserModel

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
                // Password matches, create session
                $ses_data = [
                    'user_id'       => $user['id'],
                    'username'      => $user['username'],
                    'email'         => $user['email'],
                    'first_name'    => $user['first_name'],
                    'last_name'     => $user['last_name'],
                    'role_id'       => $user['role_id'],
                    'isLoggedIn'    => TRUE
                ];
                $session->set($ses_data);

                // Update last login time
                $userModel->update($user['id'], ['last_login' => date('Y-m-d H:i:s')]);

                // Redirect to dashboard
                $session->setFlashdata('success', 'Welcome, ' . $user['first_name'] . '!');
                return redirect()->to('/dashboard'); // Redirect to your dashboard route
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
