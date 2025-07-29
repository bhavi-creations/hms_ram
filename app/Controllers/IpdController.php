<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PatientModel;
use App\Models\PatientAdmissionModel;
use App\Models\WardModel;
use App\Models\BedModel;

class IpdController extends BaseController
{
    protected $patientModel;
    protected $patientAdmissionModel;
    protected $wardModel;
    protected $bedModel;
    protected $db; // Re-declared the db property explicitly

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = ['form', 'url']; // Declared helpers here for automatic loading

    /**
     * Initialization method for the controller.
     * This method is called after the constructor and before any action method.
     */
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger); // Call parent's initController

        // Initialize models here
        $this->patientModel = new PatientModel();
        $this->patientAdmissionModel = new PatientAdmissionModel();
        $this->wardModel = new WardModel();
        $this->bedModel = new BedModel();

        // Explicitly connect to the database here.
        // This ensures $this->db is available even if BaseController doesn't provide it automatically.
        $this->db = \Config\Database::connect();
    }

    /**
     * Displays the list of IPD patients, including their ward and bed assignments.
     */
    public function index()
    {
        $data['title'] = 'IPD Management';

        // Fetch all patients who are currently marked as 'IPD'
        $ipdPatients = $this->patientModel->where('patient_type', 'IPD')->findAll();

        $patientsWithAdmissionDetails = [];
        foreach ($ipdPatients as $patient) {
            // For each IPD patient, try to find their active admission details
            $admission = $this->patientAdmissionModel->getActiveAdmissionForPatient((int)$patient['id']);

            // Merge patient data with admission data (if exists) and related ward/bed names
            $patientData = $patient;
            $patientData['admission_id'] = null; // Default to null
            $patientData['ward_id'] = null;
            $patientData['bed_id'] = null;
            $patientData['ward_name'] = 'Unassigned';
            $patientData['bed_number'] = 'Unassigned';
            $patientData['admission_notes'] = ''; // Renamed to avoid conflict with patient notes
            $patientData['admission_status'] = 'Waiting Assignment'; // Default status for IPD patient without active admission

            if ($admission) {
                $patientData['admission_id'] = $admission['id'];
                $patientData['ward_id'] = $admission['ward_id'];
                $patientData['bed_id'] = $admission['bed_id'];
                $patientData['admission_notes'] = $admission['notes'];
                $patientData['admission_status'] = $admission['admission_status']; // Use actual admission status

                // Fetch ward name
                if ($admission['ward_id']) {
                    $ward = $this->wardModel->find($admission['ward_id']);
                    $patientData['ward_name'] = $ward['name'] ?? 'N/A';
                }

                // Fetch bed number
                if ($admission['bed_id']) {
                    $bed = $this->bedModel->find($admission['bed_id']);
                    $patientData['bed_number'] = $bed['bed_number'] ?? 'N/A';
                }
            }
            $patientsWithAdmissionDetails[] = $patientData;
        }

        $data['patients'] = $patientsWithAdmissionDetails;

        return view('ipd/ipd_list', $data);
    }

    /**
     * Handles the AJAX request to remove a patient from IPD.
     * Reverts patient_type to their previous type and clears IPD-specific details.
     * This method now also updates the bed status and admission record.
     *
     * @return \CodeIgniter\HTTP\Response
     */
    public function removeFromIPD()
    {
        if (!$this->request->is('post')) {
            return $this->response->setStatusCode(405)->setJSON(['success' => false, 'message' => 'Method Not Allowed']);
        }

        $patientId = (int)$this->request->getPost('patient_id'); // Cast to int for safety

        if (empty($patientId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Patient ID is required.']);
        }

        $this->db->transBegin(); // Start transaction

        try {
            // 1. Find the active admission for this patient
            $admission = $this->patientAdmissionModel
                               ->where('patient_id', $patientId)
                               ->whereIn('admission_status', ['Admitted', 'Waiting Assignment'])
                               ->first();

            if (!$admission) {
                $this->db->transRollback();
                return $this->response->setJSON(['success' => false, 'message' => 'Active IPD admission not found for this patient.']);
            }

            // 2. If a bed was assigned, update its status to 'Available'
            if (!empty($admission['bed_id'])) {
                $this->bedModel->update($admission['bed_id'], ['status' => 'Available']);
            }

            // 3. Update the admission record itself
            $admissionUpdateData = [
                'admission_status' => 'Discharged', // Or 'Canceled' if you prefer for removal
                'discharge_date' => date('Y-m-d H:i:s'),
                'ward_id' => null, // Clear ward
                'bed_id' => null   // Clear bed
            ];
            $this->patientAdmissionModel->update($admission['id'], $admissionUpdateData);

            // 4. Update the patient's type in the PatientModel
            $success = $this->patientModel->revertFromIPD($patientId); // Pass int patientId

            if ($this->db->transStatus() === false || !$success) {
                $this->db->transRollback();
                return $this->response->setJSON(['success' => false, 'message' => 'Failed to remove patient from IPD.']);
            } else {
                $this->db->transCommit();
                return $this->response->setJSON(['success' => true, 'message' => 'Patient successfully removed from IPD and bed released.']);
            }

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Exception during removeFromIPD for patient ID ' . $patientId . ': ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'An error occurred during removal.']);
        }
    }

    /**
     * Handles the AJAX request to discharge a patient from IPD.
     * Sets patient_type to 'Discharged' and clears IPD-specific details.
     * This method now also updates the bed status and admission record.
     *
     * @return \CodeIgniter\HTTP\Response
     */
    public function dischargePatient()
    {
        if (!$this->request->is('post')) {
            return $this->response->setStatusCode(405)->setJSON(['success' => false, 'message' => 'Method Not Allowed']);
        }

        $patientId = (int)$this->request->getPost('patient_id'); // Cast to int for safety

        if (empty($patientId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Patient ID is required.']);
        }

        $this->db->transBegin(); // Start transaction

        try {
            // 1. Find the active admission for this patient
            $admission = $this->patientAdmissionModel
                               ->where('patient_id', $patientId)
                               ->whereIn('admission_status', ['Admitted', 'Waiting Assignment'])
                               ->first();

            if (!$admission) {
                $this->db->transRollback();
                return $this->response->setJSON(['success' => false, 'message' => 'Active IPD admission not found for this patient.']);
            }

            // --- DEBUGGING START ---
            $patientBeforeDischarge = $this->patientModel->find($patientId);
            log_message('debug', 'IpdController::dischargePatient - Patient data BEFORE patientModel->markAsDischarged: ' . json_encode($patientBeforeDischarge));
            // --- DEBUGGING END ---

            // 2. If a bed was assigned, update its status to 'Available'
            if (!empty($admission['bed_id'])) {
                $this->bedModel->update($admission['bed_id'], ['status' => 'Available']);
            }

            // 3. Update the admission record itself
            $admissionUpdateData = [
                'admission_status' => 'Discharged',
                'discharge_date' => date('Y-m-d H:i:s'),
                'ward_id' => null, // Clear ward
                'bed_id' => null   // Clear bed
            ];
            $this->patientAdmissionModel->update($admission['id'], $admissionUpdateData);

            // 4. Update the patient's type in the PatientModel
            $success = $this->patientModel->markAsDischarged($patientId); // Pass int patientId

            // --- DEBUGGING START ---
            $patientAfterDischarge = $this->patientModel->find($patientId);
            log_message('debug', 'IpdController::dischargePatient - Patient data AFTER patientModel->markAsDischarged: ' . json_encode($patientAfterDischarge));
            // --- DEBUGGING END ---


            if ($this->db->transStatus() === false || !$success) {
                $this->db->transRollback();
                return $this->response->setJSON(['success' => false, 'message' => 'Failed to discharge patient.']);
            } else {
                $this->db->transCommit();
                return $this->response->setJSON(['success' => true, 'message' => 'Patient successfully discharged and bed released.']);
            }

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Exception during dischargePatient for patient ID ' . $patientId . ': ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'An error occurred during discharge.']);
        }
    }

    /**
     * Fetches available beds for a given ward via AJAX.
     * Used for populating the bed dropdown in the assignment modal.
     *
     * @param int $wardId
     * @return \CodeIgniter\HTTP\Response
     */
    public function getAvailableBedsByWard($wardId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }

        $beds = $this->bedModel->where('ward_id', $wardId)
                               ->where('status', 'Available')
                               ->findAll();

        return $this->response->setJSON($beds);
    }

    /**
     * Handles the assignment or update of ward/bed for an IPD patient.
     * This will create a new patient_admission record or update an existing one.
     * Also updates the bed status.
     *
     * @return \CodeIgniter\HTTP\Response
     */
    public function assignWardBed()
    {
        if (!$this->request->is('post')) {
            return $this->response->setStatusCode(405)->setJSON(['success' => false, 'message' => 'Method Not Allowed']);
        }

        $patientId = (int)$this->request->getPost('patient_id'); // Cast to int for safety
        $wardId = $this->request->getPost('ward_id');
        $bedId = $this->request->getPost('bed_id');
        $notes = $this->request->getPost('notes');
        $patientAdmissionId = $this->request->getPost('admission_id'); // Get admission_id from POST data

        if (empty($patientId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Patient ID is required.']);
        }

        // Validate ward and bed IDs if provided
        if (!empty($wardId) && !$this->wardModel->find($wardId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid Ward selected.']);
        }
        if (!empty($bedId) && !$this->bedModel->find($bedId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid Bed selected.']);
        }

        // Check if the selected bed is available, unless it's the bed already assigned to this patient
        if (!empty($bedId)) {
            $isBedOccupied = $this->patientAdmissionModel->isBedOccupied((int)$bedId, (int)$patientAdmissionId);
            if ($isBedOccupied) {
                return $this->response->setJSON(['success' => false, 'message' => 'Selected bed is already occupied.']);
            }
        }

        $data = [
            'patient_id'       => $patientId, // Use int patientId
            'ward_id'          => !empty($wardId) ? (int)$wardId : null,
            'bed_id'           => !empty($bedId) ? (int)$bedId : null,
            'notes'            => $notes,
            'admission_status' => 'Admitted', // Default to Admitted upon assignment
        ];

        $currentAdmission = null;
        if ($patientAdmissionId) {
            $currentAdmission = $this->patientAdmissionModel->find($patientAdmissionId);
        } else {
            // If no admission ID, try to find an existing 'Waiting Assignment' admission for this patient
            $currentAdmission = $this->patientAdmissionModel->where('patient_id', $patientId)
                                                             ->where('admission_status', 'Waiting Assignment')
                                                             ->first();
        }

        $oldBedId = $currentAdmission['bed_id'] ?? null;
        $success = false;
        $message = '';

        if ($currentAdmission) {
            // Update existing admission
            $success = $this->patientAdmissionModel->update($currentAdmission['id'], $data);
            $message = 'Ward and Bed assignment updated successfully.';
            $patientAdmissionId = $currentAdmission['id']; // Ensure ID is set for bed status update
        } else {
            // Create new admission
            $data['admission_date'] = date('Y-m-d H:i:s'); // Set admission date for new records
            $patientAdmissionId = $this->patientAdmissionModel->insert($data);
            $success = (bool)$patientAdmissionId;
            $message = 'Ward and Bed assigned successfully.';

            // Also update the patient's type to IPD if not already
            $patient = $this->patientModel->find($patientId);
            if ($patient && $patient['patient_type'] !== 'IPD') {
                $this->patientModel->update($patientId, [
                    'patient_type' => 'IPD',
                    'previous_patient_type' => $patient['patient_type'], // Store previous type
                    // REMOVED: 'ipd_id_code' => $patient['patient_id_code'] // This line was causing the issue
                ]);
            }
        }

        if ($success) {
            // Update bed statuses
            if ($oldBedId && $oldBedId != $bedId) {
                // Old bed is freed up
                $this->bedModel->update($oldBedId, ['status' => 'Available']);
            }
            if ($bedId) {
                // New bed is occupied
                $this->bedModel->update($bedId, ['status' => 'Occupied']);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => $message,
                'csrfHash' => csrf_hash()
            ]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to assign Ward and Bed.']);
        }
    }

    /**
     * Fetches patient admission details for editing via AJAX.
     *
     * @param int $patientId
     * @return \CodeIgniter\HTTP\Response
     */
    public function getPatientAdmissionDetails($patientId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }

        // Get the active admission for the patient
        $admission = $this->patientAdmissionModel->getActiveAdmissionForPatient((int)$patientId);

        if ($admission) {
            return $this->response->setJSON(['success' => true, 'admission' => $admission]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'No active admission found for this patient.']);
        }
    }
}
