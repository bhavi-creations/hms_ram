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

            // Get all POST data from the AJAX request.
            // This 'admissionData' array will contain details like ward_id, bed_id, etc.,
            // that you send from your frontend form when admitting a patient.
            $admissionData = $this->request->getPost(); // Collect all data from the AJAX POST request

            // Call the method from PatientModel, passing both arguments
            $success = $this->patientModel->admitPatientToIPD((int)$patientId, $admissionData);

            if ($success) {
                // Fetch the updated patient record to show the new IPD ID
                $updatedPatient = $this->patientModel->find($patientId);
                $ipdId = $updatedPatient['ipd_id_code'] ?? 'N/A';
                return $this->respondUpdated(['success' => true, 'message' => 'Patient successfully admitted to IPD. New IPD ID: ' . $ipdId]);
            } else {
                return $this->fail('Failed to admit patient to IPD. Please check logs for details.', 500);
            }
        }
        // If not an AJAX request, redirect or show an error
        return redirect()->back()->with('error', 'Invalid request method.');
    }
}