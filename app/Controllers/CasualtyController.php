<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PatientModel;
use CodeIgniter\API\ResponseTrait;

class CasualtyController extends BaseController
{
    use ResponseTrait;

    protected $patientModel;

    public function __construct()
    {
        $this->patientModel = new PatientModel();
    }

    /**
     * Displays the list of Casualty/ER patients.
     */
    public function index()
    {
        $data['title'] = 'Casualty / ER Management';
        // Use the new method from PatientModel to get only Casualty patients
        $data['patients'] = $this->patientModel->where('patient_type', 'Casualty')->findAll();

        return view('casualty/casualty_list', $data);
    }

    /**
     * Handles the AJAX request to admit a Casualty patient to IPD.
     */
    public function admitToIpd($patientId = null)
    {
        if ($this->request->isAJAX()) {
            if (empty($patientId)) {
                return $this->fail('Patient ID is required.', 400);
            }

            $success = $this->patientModel->admitPatientToIPD($patientId);

            if ($success) {
                $updatedPatient = $this->patientModel->find($patientId);
                $ipdId = $updatedPatient['ipd_id_code'] ?? 'N/A';
                return $this->respondUpdated(['success' => true, 'message' => 'Patient successfully admitted to IPD. New IPD ID: ' . $ipdId]);
            } else {
                return $this->fail('Failed to admit patient to IPD. Please check logs for details.', 500);
            }
        }
        return redirect()->back()->with('error', 'Invalid request method.');
    }
}