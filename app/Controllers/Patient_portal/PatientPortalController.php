<?php 

namespace App\Controllers\Patient_portal;

use App\Controllers\BaseController;
use App\Models\PatientModel;
use App\Models\AppointmentModel;
use App\Models\Diagnostics\DiagnosticsOrderModel;
use App\Models\Laboratory\LabOrderModel;
use App\Models\UserModel;
use App\Models\DoctorModel; // <-- NEW: Import DoctorModel

class PatientPortalController extends BaseController
{
    protected $patientModel;
    protected $appointmentModel;
    protected $diagnosticsOrderModel;
    protected $labOrderModel;
    protected $userModel;
    protected $doctorModel; // <-- NEW: Declare DoctorModel property

    public function __construct()
    {
        $this->patientModel = new PatientModel();
        $this->appointmentModel = new AppointmentModel();
        $this->diagnosticsOrderModel = new DiagnosticsOrderModel();
        $this->labOrderModel = new LabOrderModel();
        $this->userModel = new UserModel();
        $this->doctorModel = new DoctorModel(); // <-- NEW: Initialize DoctorModel
    }
    
    /**
     * Helper method to collect unique doctor IDs from various records.
     * Assumes records have a 'doctor_id' field.
     * @param array $records An array of patient records (appointments, orders).
     * @return array Unique doctor IDs.
     */
    private function _getUniqueDoctorIds(array $records): array
    {
        $doctorIds = [];
        foreach ($records as $record) {
            // Check if 'doctor_id' exists and is not null/empty
            if (isset($record['doctor_id']) && $record['doctor_id']) {
                $doctorIds[] = $record['doctor_id'];
            }
        }
        return array_unique($doctorIds);
    }
    
    /**
     * Helper method to fetch doctor details and map them by ID for easy lookup.
     * @param array $doctorIds The IDs of the doctors to fetch.
     * @return array Doctor data mapped by doctor_id.
     */
    private function _getDoctorMap(array $doctorIds): array
    {
        if (empty($doctorIds)) {
            return [];
        }

        // Fetch only the necessary doctors and specific fields
        $doctors = $this->doctorModel
                        ->whereIn('id', $doctorIds)
                        ->select('id, first_name, last_name, specialization, designation')
                        ->findAll();

        $doctorMap = [];
        foreach ($doctors as $doctor) {
            $doctorMap[$doctor['id']] = $doctor;
        }

        return $doctorMap;
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
            // FIX: Session recovery logic using the user_id if patient_id is missing
            $userId = $session->get('user_id');
            if ($userId) {
                // Look up patient_id using the user_id from the session
                $patientRecord = $this->patientModel->where('user_id', $userId)->first();
                if ($patientRecord) {
                    $patientId = $patientRecord['patient_id'];
                    $session->set('patient_id', $patientId);
                    log_message('info', 'PatientDashboard - Recovered missing patient_id for user_id: ' . $userId);
                } else {
                    log_message('error', 'PatientDashboard - CRITICAL ERROR: User ID ' . $userId . ' found, but no associated patient record.');
                    return redirect()->to('/patient-portal/login');
                }
            } else {
                log_message('error', 'PatientDashboard - CRITICAL ERROR: patient_id and user_id are both NULL despite successful login.');
                return redirect()->to('/patient-portal/login');
            }
        }

        $patient = $this->patientModel->find($patientId);

        if (!$patient) {
            log_message('error', 'PatientDashboard - No patient found for patient_id: ' . $patientId . '. User data integrity issue.');
            $session->setFlashdata('error', 'Your patient record could not be loaded. Please contact support.');
            return redirect()->to('/patient-portal/login');
        }

        // --- Data Fetching ---
        $appointments = $this->appointmentModel->where('patient_id', $patientId)->orderBy('appointment_date', 'DESC')->findAll();
        $diagnostics = $this->diagnosticsOrderModel->where('patient_id', $patientId)->orderBy('order_date', 'DESC')->findAll();
        $labs = $this->labOrderModel->where('patient_id', $patientId)->orderBy('order_date', 'DESC')->findAll();

        // --- Doctor Data Enrichment ---
        // Combine all records to find all relevant doctor IDs efficiently
        $allRecords = array_merge($appointments, $diagnostics, $labs);
        $uniqueDoctorIds = $this->_getUniqueDoctorIds($allRecords);
        $doctorMap = $this->_getDoctorMap($uniqueDoctorIds);
        // -----------------------------

        $data = [
            'patient' => $patient,
            'appointments' => $appointments,
            'diagnostics' => $diagnostics,
            'labs' => $labs,
            'doctorMap' => $doctorMap, // <-- NEW: Pass the doctor lookup map to the view
        ];

        return view('patient_portal/dashboard', $data);
    }

    /**
     * Fetches and displays all appointments for the logged-in patient.
     */
    public function appointments()
    {
        $session = session();
        $patientId = $session->get('patient_id');
        
        if (!$patientId) {
            return redirect()->to('/patient-portal/login')->with('error', 'Patient session expired or invalid.');
        }

        $appointments = $this->appointmentModel
                            ->where('patient_id', $patientId)
                            ->orderBy('appointment_date', 'DESC')
                            ->findAll();
        
        // Doctor Data Enrichment
        $uniqueDoctorIds = $this->_getUniqueDoctorIds($appointments);
        $doctorMap = $this->_getDoctorMap($uniqueDoctorIds);
        // ----------------------

        $data = [
            'title' => 'My Appointments',
            'appointments' => $appointments,
            'doctorMap' => $doctorMap, // <-- Pass doctor map for lookup
        ];
        
        return view('patient_portal/appointments', $data);
    }

    /**
     * Fetches and displays all lab orders for the logged-in patient.
     */
    public function labs()
    {
        $session = session();
        $patientId = $session->get('patient_id');
        
        if (!$patientId) {
            return redirect()->to('/patient-portal/login')->with('error', 'Patient session expired or invalid.');
        }

        $labs = $this->labOrderModel
                     ->where('patient_id', $patientId)
                     ->orderBy('order_date', 'DESC')
                     ->findAll();
        
        // Doctor Data Enrichment
        $uniqueDoctorIds = $this->_getUniqueDoctorIds($labs);
        $doctorMap = $this->_getDoctorMap($uniqueDoctorIds);
        // ----------------------

        $data = [
            'title' => 'My Lab Orders',
            'labs' => $labs,
            'doctorMap' => $doctorMap, // <-- Pass doctor map for lookup
        ];
        
        return view('patient_portal/labs', $data);
    }

    /**
     * Fetches and displays all diagnostic orders for the logged-in patient.
     */
    public function diagnostics()
    {
        $session = session();
        $patientId = $session->get('patient_id');
        
        if (!$patientId) {
            return redirect()->to('/patient-portal/login')->with('error', 'Patient session expired or invalid.');
        }

        $diagnostics = $this->diagnosticsOrderModel
                            ->where('patient_id', $patientId)
                            ->orderBy('order_date', 'DESC')
                            ->findAll();

        // Doctor Data Enrichment
        $uniqueDoctorIds = $this->_getUniqueDoctorIds($diagnostics);
        $doctorMap = $this->_getDoctorMap($uniqueDoctorIds);
        // ----------------------
        
        $data = [
            'title' => 'My Diagnostic Orders',
            'diagnostics' => $diagnostics,
            'doctorMap' => $doctorMap, // <-- Pass doctor map for lookup
        ];
        
        return view('patient_portal/diagnostics', $data);
    }
}
