<?php

namespace App\Controllers;

use App\Controllers\BaseController; // Assuming BaseController is used
use App\Models\PatientModel;

class IpdController extends BaseController
{
    protected $patientModel;

    public function __construct()
    {
        $this->patientModel = new PatientModel();
    }

    /**
     * Displays the list of IPD patients.
     */
    public function index()
    {
        $data['title'] = 'IPD Management';
        // Get all patients where patient_type is 'IPD'
        $data['patients'] = $this->patientModel->where('patient_type', 'IPD')->findAll();

        return view('ipd/ipd_list', $data);
    }

    /**
     * Handles the AJAX request to remove a patient from IPD.
     * Reverts patient_type to their previous type and clears IPD-specific details.
     * @return \CodeIgniter\HTTP\Response
     */
    public function removeFromIPD()
    {
        // Ensure it's an AJAX POST request
        if (!$this->request->isAJAX() || !$this->request->is('post')) {
            return $this->response->setStatusCode(405)->setJSON(['success' => false, 'message' => 'Method Not Allowed']);
        }

        // Get patient ID from the POST request
        $patientId = $this->request->getPost('patient_id');

        if (empty($patientId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Patient ID is required.']);
        }

        // CodeIgniter 4 automatically handles CSRF validation for POST requests
        // when csrf_protection is enabled in app/Config/Security.php
        // Just ensure the CSRF token is sent with your AJAX request from the frontend.

        $success = $this->patientModel->revertFromIPD((int)$patientId);

        if ($success) {
            return $this->response->setJSON(['success' => true, 'message' => 'Patient successfully removed from IPD.']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to remove patient from IPD.']);
        }
    }

    /**
     * Handles the AJAX request to discharge a patient from IPD.
     * Sets patient_type to 'Discharged' and clears IPD-specific details.
     * @return \CodeIgniter\HTTP\Response
     */
    public function dischargePatient()
    {
        // Ensure it's an AJAX POST request
        if (!$this->request->isAJAX() || !$this->request->is('post')) {
            return $this->response->setStatusCode(405)->setJSON(['success' => false, 'message' => 'Method Not Allowed']);
        }

        // Get patient ID from the POST request
        $patientId = $this->request->getPost('patient_id');

        if (empty($patientId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Patient ID is required.']);
        }

        $success = $this->patientModel->markAsDischarged((int)$patientId);

        if ($success) {
            return $this->response->setJSON(['success' => true, 'message' => 'Patient successfully discharged.']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to discharge patient.']);
        }
    }
}