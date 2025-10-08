<?php

namespace App\Controllers\Patient_portal;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\PatientModel;

class PatientAuth extends BaseController
{
    public function login()
    {
        // If already logged in, redirect to dashboard
        if (session()->get('isLoggedIn') && session()->get('role_id') == 10) {
            return redirect()->to('/patient-portal/dashboard');
        }
        
        // This should probably render a login view, but for now we assume a dedicated login view
        return view('patient_portal/login_page'); 
    }

    public function loginAttempt()
    {
        $session = session();
        $userModel = new UserModel();
        $patientModel = new PatientModel();

        $usernameOrEmail = $this->request->getPost('username_or_email');
        $password = trim($this->request->getPost('password'));

        if (empty($usernameOrEmail) || empty($password)) {
            $session->setFlashdata('error', 'Please enter both username/email and password.');
            return redirect()->to('/patient-portal/login');
        }

        log_message('debug', 'Login attempt for username/email: ' . $usernameOrEmail);

        $user = $userModel->where('username', $usernameOrEmail)
                          ->orWhere('email', $usernameOrEmail)
                          ->first();

        if (!$user) {
            $session->setFlashdata('error', 'Invalid credentials or unauthorized access.');
            return redirect()->to('/patient-portal/login');
        }

        if (!password_verify($password, $user['password'])) {
             $session->setFlashdata('error', 'Invalid credentials or unauthorized access.');
            return redirect()->to('/patient-portal/login');
        }

        log_message('debug', 'User found, verifying password for: ' . $user['username']);

        if ($user['role_id'] != 10) {
            $session->setFlashdata('error', 'Invalid credentials or unauthorized access.');
            return redirect()->to('/patient-portal/login');
        }

        // Lookup patient by patient_id_code stored in username
        $patient = $patientModel->where('patient_id_code', $user['username'])->first();

        if (!$patient) {
            $session->setFlashdata('error', 'No patient record found for this user. Contact support.');
            log_message('error', 'loginAttempt - No patient record found for user: ' . $user['username']); 
            return redirect()->to('/patient-portal/login');
        }
        
        log_message('debug', 'Password matched. Preparing session for: ' . $user['username']);

        // --- Reverting to standard session set, relying on the OS/CI FileHandler to work correctly ---
        
        $patientSessionData = [
            'user_id'          => $user['id'],
            'username'         => $user['username'],
            'email'            => $user['email'],
            'first_name'       => $user['first_name'],
            'last_name'        => $user['last_name'],
            'role_id'          => $user['role_id'],
            'management_level' => 'Team Member', 
            'isLoggedIn'       => true,
            'allowed_modules'  => ['patient_portal'],
            'patient_id'       => $patient['id'], // CRITICAL missing piece
            'patient_db_id'    => $patient['id'] // Setting a duplicate key just in case
        ];

        // Use the standard array set method.
        $session->set($patientSessionData);
        
        // Log the exact data that was JUST set into the session
        log_message('debug', 'loginAttempt - Session data set for user id ' . $user['id'] . ' with patient_id ' . $patient['id'] . '.');
        log_message('debug', 'loginAttempt - Session Content IMMEDIATELY AFTER SET: ' . json_encode($session->get()));


        return redirect()->to('/patient-portal/dashboard');
    }

    public function logout()
    {
        $session = session();
        $session->destroy();
        return redirect()->to('/patient-portal/login');
    }
}
