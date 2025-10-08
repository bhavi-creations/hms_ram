<?php

namespace App\Controllers\Patient_portal;

use App\Controllers\BaseController;
use App\Models\PatientModel; 
use App\Models\AppointmentModel;
use App\Models\Diagnostics\DiagnosticsOrderModel; // FIX: Updated to include the Diagnostics sub-namespace
use App\Models\Laboratory\LabOrderModel;       // FIX: Updated to include the Laboratory sub-namespace
use App\Models\UserModel; // Assuming doctors are managed via the UserModel

class PatientPortalController extends BaseController
{
    // 1. Declare protected properties for the models
    protected $patientModel;
    protected $appointmentModel;
    protected $diagnosticsOrderModel;
    protected $labOrderModel;
    protected $userModel;

    // 2. Initialize the models in the constructor
    public function __construct() {
        $this->patientModel = new PatientModel();
        $this->appointmentModel = new AppointmentModel();
        $this->diagnosticsOrderModel = new DiagnosticsOrderModel();
        $this->labOrderModel = new LabOrderModel();
        $this->userModel = new UserModel(); 
    }

    public function dashboard()
    {
        $session = session();
        $patientId = $session->get('patient_id');
        $isLoggedIn = $session->get('isLoggedIn');
        $roleId = $session->get('role_id');

        // Defensive check: If the filter failed or session is corrupted
        if (!$isLoggedIn || $roleId != 10) {
            log_message('error', 'PatientDashboard - Access denied: Filter missed unauthenticated access.');
            return redirect()->to('/patient-portal/login');
        }

        if (!$patientId) {
            // FIX: Session recovery logic using the username/patient_id_code.
            $userId = $session->get('user_id');
            $username = $session->get('username'); // This holds the patient_id_code

            if ($userId && $username) {
                log_message('info', 'PatientDashboard - Attempting to recover missing patient_id using patient_id_code: ' . $username);
                
                // CRITICAL FIX: Lookup patient using the unique patient_id_code (stored as username in session)
                // This correctly uses your existing schema (patients.patient_id_code)
                $patientRecord = $this->patientModel->where('patient_id_code', $username)->first();
                
                if ($patientRecord) {
                    // The primary key for patients is 'id'
                    $patientId = $patientRecord['id']; 
                    $session->set('patient_id', $patientId);
                    log_message('info', 'PatientDashboard - Recovered missing patient_id: ' . $patientId);
                } else {
                    log_message('error', 'PatientDashboard - No associated patient record found for username/patient_id_code: ' . $username);
                    $session->setFlashdata('error', 'Your patient record is not linked correctly. Please contact support.');
                    return redirect()->to('/patient-portal/login');
                }
            } else {
                log_message('error', 'PatientDashboard - CRITICAL ERROR: patient_id and session user details are incomplete despite successful login.');
                return redirect()->to('/patient-portal/login');
            }
        }

        // --- Standard dashboard loading logic begins here ---
        
        // Use the recovered or existing patientId
        $patient = $this->patientModel->find($patientId);

        if (!$patient) {
            log_message('error', 'PatientDashboard - No patient found for patient_id: ' . $patientId . '. User data integrity issue.');
            $session->setFlashdata('error', 'Your patient record could not be loaded. Please contact support.');
            return redirect()->to('/patient-portal/login');
        }

        // --- Data Fetching (using initialized models) ---
        $appointments = $this->appointmentModel->where('patient_id', $patientId)->orderBy('appointment_date', 'DESC')->findAll() ?? [];
        $diagnostics = $this->diagnosticsOrderModel->where('patient_id', $patientId)->orderBy('order_date', 'DESC')->findAll() ?? [];
        $labs = $this->labOrderModel->where('patient_id', $patientId)->orderBy('order_date', 'DESC')->findAll() ?? [];


        // --- Doctor Data Enrichment ---
        $allRecords = array_merge($appointments, $diagnostics, $labs);
        $uniqueDoctorIds = $this->_getUniqueDoctorIds($allRecords);
        $doctorMap = $this->_getDoctorMap($uniqueDoctorIds);
        // -----------------------------

        $data = [
            'patient' => $patient,
            'appointments' => $appointments,
            'diagnostics' => $diagnostics,
            'labs' => $labs,
            'doctorMap' => $doctorMap, // <-- Pass the doctor lookup map to the view
        ];

        return view('patient_portal/dashboard', $data);
    }
    
    // Placeholder for helper methods if not present
    private function _getUniqueDoctorIds(array $records) {
        $ids = [];
        foreach ($records as $record) {
            if (isset($record['doctor_id'])) {
                $ids[] = $record['doctor_id'];
            }
        }
        return array_unique($ids);
    }
    
    private function _getDoctorMap(array $ids) {
        // Implementation using the initialized $this->userModel (assuming doctors are users)
        $map = [];
        if (!empty($ids)) {
            // Assuming Doctor IDs in the appointments/orders refer to the `users.id` field
            $doctors = $this->userModel->select('id, first_name, last_name')->whereIn('id', $ids)->findAll();
            foreach ($doctors as $doctor) {
                $map[$doctor['id']] = $doctor['first_name'] . ' ' . $doctor['last_name'];
            }
        }
        return $map;
    }
}
