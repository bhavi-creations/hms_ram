<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PatientModel;
use CodeIgniter\API\ResponseTrait;

class OpdController extends BaseController
{
    use ResponseTrait;

    protected $patientModel;

    public function __construct()
    {
        $this->patientModel = new PatientModel();
    }

    /**
     * Displays the list of OPD patients.
     */
    public function index()
    {
        $data['title'] = 'OPD Management';
        // Use the new method from PatientModel to get only OPD patients
        $data['patients'] = $this->patientModel->where('patient_type', 'OPD')->findAll();

        return view('opd/opd_list', $data);
    }

    /**
     * Handles the AJAX request to admit an OPD patient to IPD.
     */
    public function admitToIpd($patientId = null)
    {
        if ($this->request->isAJAX()) {
            if (empty($patientId)) {
                return $this->fail('Patient ID is required.', 400);
            }

            // Call the new method from PatientModel to update patient type to IPD
            $success = $this->patientModel->admitPatientToIPD($patientId);

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