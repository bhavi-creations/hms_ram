<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AppointmentModel; // Import this model
use App\Models\PatientModel;     // Import this model

class Home extends BaseController
{
    /**
     * This is the main dashboard page, typically for admins or general users.
     */
    public function index()
    {
        // The AuthFilter already handles the isLoggedIn check for this route.
        // This method will display the admin/general dashboard.
        $data['title'] = 'Admin Dashboard';
        // You can fetch overall statistics here for the admin dashboard
        // Example:
        // $appointmentModel = new AppointmentModel();
        // $data['totalAppointments'] = $appointmentModel->countAllResults();

        return view('dashboard/index', $data); // Assuming you have an admin dashboard view
    }

    /**
     * Doctor's specific dashboard.
     * Displays appointments relevant to the logged-in doctor.
     */
    public function doctorDashboard()
    {
        $session = session();
        $doctor_id = $session->get('doctor_id'); // Get the logged-in doctor's ID from session
        $role_id = $session->get('role_id');

        // Security Check: Ensure it's a doctor accessing this page
        if ($role_id != 2 || !$doctor_id) { // Assuming role_id 2 is for Doctors
            session()->setFlashdata('error', 'Access denied. You do not have permission to view the doctor dashboard.');
            return redirect()->to('/dashboard'); // Redirect to general dashboard or login
        }

        $appointmentModel = new AppointmentModel();

        // Fetch appointments specifically for this doctor
        $data['appointments'] = $appointmentModel
            ->select('appointments.*, patients.first_name as patient_first_name, patients.last_name as patient_last_name')
            ->join('patients', 'appointments.patient_id = patients.id')
            ->where('appointments.doctor_id', $doctor_id) // Filter by logged-in doctor's ID
            ->orderBy('appointment_date', 'DESC')
            ->orderBy('appointment_time', 'ASC')
            ->findAll();

        $data['title'] = 'Doctor Dashboard';
        $data['doctor_name'] = $session->get('first_name') . ' ' . $session->get('last_name');

        // --- IMPORTANT: CORRECTED VIEW PATH HERE ---
        return view('doctors/dashboard', $data); // Corrected path from 'doctor/dashboard' to 'doctors/dashboard'
    }

    // You can add other public methods for other roles or general pages here
}
