<?php

namespace App\Controllers\Patient_portal;

use App\Controllers\BaseController;
use App\Models\PatientModel;
use App\Models\AppointmentModel;
use App\Models\DoctorModel; // ADDED: Required to correctly load doctor details
use App\Models\Diagnostics\DiagnosticsOrderModel;
use App\Models\Laboratory\LabOrderModel;
use App\Models\UserModel; // Assuming doctors are managed via the UserModel

class PatientPortalController extends BaseController
{
    // 1. Declare protected properties for the models
    protected $patientModel;
    protected $appointmentModel;
    protected $doctorModel; // ADDED Property
    protected $diagnosticsOrderModel;
    protected $labOrderModel;
    protected $userModel;

    // 2. Initialize the models in the constructor
    public function __construct()
    {
        $this->patientModel = new PatientModel();
        $this->appointmentModel = new AppointmentModel();
        $this->doctorModel = new DoctorModel(); // ADDED Initialization
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

        // ... (Session recovery logic remains unchanged) ...

        if (!$isLoggedIn || $roleId != 10) {
            log_message('error', 'PatientDashboard - Access denied: Filter missed unauthenticated access.');
            return redirect()->to('/patient-portal/login');
        }

        if (!$patientId) {
            $userId = $session->get('user_id');
            $username = $session->get('username');
            if ($userId && $username) {
                log_message('info', 'PatientDashboard - Attempting to recover missing patient_id using patient_id_code: ' . $username);
                $patientRecord = $this->patientModel->where('patient_id_code', $username)->first();
                if ($patientRecord) {
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

        $patient = $this->patientModel->find($patientId);

        if (!$patient) {
            log_message('error', 'PatientDashboard - No patient found for patient_id: ' . $patientId . '. User data integrity issue.');
            $session->setFlashdata('error', 'Your patient record could not be loaded. Please contact support.');
            return redirect()->to('/patient-portal/login');
        }

        // --- Data Fetching (using initialized models) ---
        // Appointments are now fixed using the new JOIN method.
        $appointments = $this->appointmentModel->getPatientAppointmentsWithDoctorName($patientId) ?? [];

        // Diagnostic and Lab orders still need to be fetched separately.
        $diagnostics = $this->diagnosticsOrderModel->where('patient_id', $patientId)->orderBy('order_date', 'DESC')->findAll() ?? [];
        $labs = $this->labOrderModel->where('patient_id', $patientId)->orderBy('order_date', 'DESC')->findAll() ?? [];


        // --- Doctor Data Enrichment (Map creation) ---
        // Appointments are now pre-enriched, so we only need to map doctors for diagnostics and labs.
        $allRecords = array_merge($diagnostics, $labs); // Removed $appointments
        // The helper below MUST use the same doctor ID key as the records (e.g., 'doctor_id')
        $uniqueDoctorIds = $this->_getUniqueDoctorIds($allRecords);
        $doctorMap = $this->_getDoctorMap($uniqueDoctorIds);
        log_message('debug', 'Doctor IDs found in Map: ' . implode(', ', array_keys($doctorMap)));
        // -----------------------------

        // --- LOGIC TO ATTACH DOCTOR NAME TO EACH RECORD ---

        // ### CRITICAL STEP: Ensure 'doctor_id' matches the actual foreign key column in your tables. ###
        $doctorIdKey = 'doctor_id';

        // 1. Enrich Appointments (Loop removed, data is pre-joined)

        // 2. Enrich Diagnostics
        foreach ($diagnostics as &$diagnostic) {
            $doctorId = $diagnostic[$doctorIdKey] ?? null;

            log_message('debug', 'Diagnostic ID ' . ($diagnostic['id'] ?? 'N/A') . ' Foreign Key Value: ' . ($doctorId ?? 'NULL'));

            if ($doctorId && isset($doctorMap[$doctorId])) {
                $doctor = $doctorMap[$doctorId];
                $firstName = $doctor['first_name'] ?? '';
                $lastName = $doctor['last_name'] ?? '';
                $diagnostic['doctor_name'] = esc(trim($firstName . ' ' . $lastName));
                log_message('debug', 'Diagnostic Doctor Name set: ' . $diagnostic['doctor_name']);
            } else {
                $diagnostic['doctor_name'] = 'N/A';
                log_message('debug', 'Diagnostic Doctor lookup failed for ID: ' . ($doctorId ?? 'NULL'));
            }
        }
        unset($diagnostic);

        // 3. Enrich Labs
        foreach ($labs as &$lab) {
            $doctorId = $lab[$doctorIdKey] ?? null;

            log_message('debug', 'Lab ID ' . ($lab['id'] ?? 'N/A') . ' Foreign Key Value: ' . ($doctorId ?? 'NULL'));

            if ($doctorId && isset($doctorMap[$doctorId])) {
                $doctor = $doctorMap[$doctorId];
                $firstName = $doctor['first_name'] ?? '';
                $lastName = $doctor['last_name'] ?? '';
                $lab['doctor_name'] = esc(trim($firstName . ' ' . $lastName));
                log_message('debug', 'Lab Doctor Name set: ' . $lab['doctor_name']);
            } else {
                $lab['doctor_name'] = 'N/A';
                log_message('debug', 'Lab Doctor lookup failed for ID: ' . ($doctorId ?? 'NULL'));
            }
        }
        unset($lab);

        // --- End Name Attachment Logic ---

        $data = [
            'patient' => $patient,
            'appointments' => $appointments,
            'diagnostics' => $diagnostics,
            'labs' => $labs,
            'doctorMap' => $doctorMap,
        ];

        return view('patient_portal/dashboard', $data);
    }

    // NEW METHOD: Handles the /patient-portal/appointments URL
    public function appointments()
    {
        $session = session();
        $patientId = $session->get('patient_id');
        $isLoggedIn = $session->get('isLoggedIn');
        $roleId = $session->get('role_id');

        // --- Security and Patient ID Validation (copied from dashboard) ---
        if (!$isLoggedIn || $roleId != 10) {
            log_message('error', 'PatientAppointments - Access denied: Filter missed unauthenticated access.');
            return redirect()->to('/patient-portal/login');
        }

        if (!$patientId) {
            $userId = $session->get('user_id');
            $username = $session->get('username');
            if ($userId && $username) {
                // Attempt to recover patient_id
                $patientRecord = $this->patientModel->where('patient_id_code', $username)->first();
                if ($patientRecord) {
                    $patientId = $patientRecord['id'];
                    $session->set('patient_id', $patientId);
                } else {
                    $session->setFlashdata('error', 'Your patient record is not linked correctly. Please contact support.');
                    return redirect()->to('/patient-portal/login');
                }
            } else {
                return redirect()->to('/patient-portal/login');
            }
        }

        $patient = $this->patientModel->find($patientId);
        if (!$patient) {
            $session->setFlashdata('error', 'Your patient record could not be loaded. Please contact support.');
            return redirect()->to('/patient-portal/login');
        }
        // --- End Validation ---


        // --- Fetch Appointments using the fixed method ---
        // This method uses the JOIN and only returns Scheduled/Confirmed appointments.
        $appointments = $this->appointmentModel->getPatientAppointmentsWithDoctorName($patientId) ?? [];

        $data = [
            'patient' => $patient,
            'appointments' => $appointments,
            'page_title' => 'My Upcoming Appointments', // Title for the view
        ];

        // NOTE: You will need to create the view file at: app/Views/patient_portal/appointments_list.php
        return view('patient_portal/appointments_list', $data);
    }

    /**
     * Displays the patient's list of lab orders.
     * FIX: Removed strict ': string' return type hint to allow returning RedirectResponse.
     * FIX: Aligned authorization and patient ID validation logic with the appointments() method.
     */
    public function labs()
    {
        $session = session();
        $patientId = $session->get('patient_id');
        $isLoggedIn = $session->get('isLoggedIn');
        $roleId = $session->get('role_id');

        // --- Security and Patient ID Validation (Aligned with appointments()) ---
        if (!$isLoggedIn || $roleId != 10) {
            log_message('error', 'PatientLabs - Access denied: Filter missed unauthenticated access.');
            return redirect()->to('/patient-portal/login');
        }

        if (!$patientId) {
            $userId = $session->get('user_id');
            $username = $session->get('username');
            if ($userId && $username) {
                // Attempt to recover patient_id
                $patientRecord = $this->patientModel->where('patient_id_code', $username)->first();
                if ($patientRecord) {
                    $patientId = $patientRecord['id'];
                    $session->set('patient_id', $patientId);
                } else {
                    $session->setFlashdata('error', 'Your patient record is not linked correctly. Please contact support.');
                    return redirect()->to('/patient-portal/login');
                }
            } else {
                return redirect()->to('/patient-portal/login');
            }
        }

        $patient = $this->patientModel->find($patientId);
        if (!$patient) {
            $session->setFlashdata('error', 'Your patient record could not be loaded. Please contact support.');
            return redirect()->to('/patient-portal/login');
        }
        // --- End Validation ---


        // Use the model initialized in the constructor
        $labOrderModel = $this->labOrderModel;

        // Fetch lab orders for the patient. 
        // NOTE: You must implement the 'getLabOrdersForPatient' method in your LabOrderModel.
        // This method should also join data to show 'test_name' and 'doctor_name'.
        $labs = $labOrderModel->getLabOrdersForPatient($patientId);

        $data = [
            'page_title' => 'My Lab Orders',
            'labs'       => $labs,
        ];

        return view('patient_portal/labs', $data);
    }

    public function viewLabReport(string $filename)
    {
        $session = session();
        $patientId = $session->get('patient_id');

        // 1. Basic Security Check
        if (!$session->get('isLoggedIn') || $session->get('role_id') != 10 || !$patientId) {
            log_message('error', 'Attempted unauthorized access to lab report file: ' . $filename);
            return $this->response->setStatusCode(401)->setBody('Unauthorized Access.');
        }

        // 2. Define the file path using the confirmed robust method
        // Maps to C:\xampp\htdocs\hms_ram\public\uploads\laboratory\
        $uploadSubPath = 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'laboratory' . DIRECTORY_SEPARATOR;
        $fileDir = ROOTPATH . $uploadSubPath;
        $filePath = $fileDir . $filename;

        // 3. Authorization Check: Verify the patient is authorized to view this specific file.
        $isAuthorized = $this->labOrderModel->select('lab_orders.id')
            ->where([
                'lab_orders.patient_id' => $patientId,
            ])
            ->join('lab_order_items LOI', 'LOI.lab_order_id = lab_orders.id')
            ->join('lab_order_files LF', 'LF.lab_order_item_id = LOI.id')
            ->where('LF.file_path', $filename)
            ->countAllResults() > 0;

        if (!file_exists($filePath) || !$isAuthorized) {
            if (!file_exists($filePath)) {
                log_message('error', 'Lab Report file not found at: ' . $filePath);
                return $this->response->setStatusCode(404)->setBody('Lab Report file not found on server.');
            }
            log_message('error', 'Patient ' . $patientId . ' attempted to access unauthorized lab report: ' . $filename);
            return $this->response->setStatusCode(403)->setBody('Access Forbidden: You do not own this lab report.');
        }

        // 4. Serve the file securely
        $mimeType = mime_content_type($filePath);

        return $this->response
            ->setStatusCode(200)
            ->setContentType($mimeType)
            ->setHeader('Content-Disposition', 'inline; filename="' . basename($filename) . '"')
            ->setBody(file_get_contents($filePath));
    }
    /**
     * Helper to extract unique doctor IDs from records.
     * Ensure the key used here matches the tables' foreign key column.
     */
    protected function _getUniqueDoctorIds(array $records): array
    {
        $ids = [];
        $doctorIdKey = 'doctor_id'; // <--- MUST match the key used in dashboard()
        foreach ($records as $record) {
            if (isset($record[$doctorIdKey]) && !empty($record[$doctorIdKey])) {
                $ids[] = $record[$doctorIdKey];
            }
        }
        return array_unique($ids);
    }

    /**
     * Fetches doctor records and creates a map keyed by ID.
     * FIX: Now uses the dedicated DoctorModel.
     */
    protected function _getDoctorMap(array $ids)
    {
        $map = [];
        if (!empty($ids)) {
            // We use the dedicated DoctorModel to query the doctors table directly.
            // This is more reliable than querying the generic UserModel and applying a role filter.
            $doctors = $this->doctorModel->select('id, first_name, last_name')
                ->whereIn('id', $ids)
                ->findAll();

            foreach ($doctors as $doctor) {
                // Store the entire doctor record array.
                $map[$doctor['id']] = $doctor;
            }
        }
        return $map;
    }



    public function invoices()
    {
        $session = session();
        $patientId = $session->get('patient_id');
        $isLoggedIn = $session->get('isLoggedIn');
        $roleId = $session->get('role_id');

        // --- Security and Patient ID Validation ---
        if (!$isLoggedIn || $roleId != 10) {
            log_message('error', 'PatientInvoices - Access denied: Filter missed unauthenticated access.');
            return redirect()->to('/patient-portal/login');
        }

        // Basic check for patient data existence before loading the page
        if (!$patientId) {
            // Attempt recovery or redirect if patient ID is essential for the layout
            return redirect()->to('/patient-portal/login')->with('error', 'Patient ID missing. Please log in again.');
        }

        $data = [
            'page_title' => 'My Invoices & Billing',
            'message' => 'The Invoices and Billing section is currently under development.',
        ];

        // NOTE: The view file app/Views/patient_portal/invoices.php is required.
        return view('patient_portal/invoices', $data);
    }








    public function diagnostics()
    {
        $session = session();
        $patientId = $session->get('patient_id');
        $isLoggedIn = $session->get('isLoggedIn');
        $roleId = $session->get('role_id');

        // --- Security and Patient ID Validation ---
        if (!$isLoggedIn || $roleId != 10) {
            log_message('error', 'PatientDiagnostics - Access denied: Filter missed unauthenticated access.');
            return redirect()->to('/patient-portal/login');
        }

        if (!$patientId) {
            $userId = $session->get('user_id');
            $username = $session->get('username');
            if ($userId && $username) {
                // Attempt to recover patient_id
                $patientRecord = $this->patientModel->where('patient_id_code', $username)->first();
                if ($patientRecord) {
                    $patientId = $patientRecord['id'];
                    $session->set('patient_id', $patientId);
                } else {
                    $session->setFlashdata('error', 'Your patient record is not linked correctly. Please contact support.');
                    return redirect()->to('/patient-portal/login');
                }
            } else {
                return redirect()->to('/patient-portal/login');
            }
        }

        $patient = $this->patientModel->find($patientId);
        if (!$patient) {
            $session->setFlashdata('error', 'Your patient record could not be loaded. Please contact support.');
            return redirect()->to('/patient-portal/login');
        }
        // --- End Validation ---

        // Fetch diagnostic orders for the patient. 
        $diagnostics = $this->diagnosticsOrderModel->getDiagnosticsOrdersForPatient($patientId);

        $data = [
            'page_title' => 'My Diagnostic Orders',
            'diagnostics' => $diagnostics,
        ];

        return view('patient_portal/diagnostics', $data);
    }

    /**
     * SECURELY serves a diagnostic report file to the authenticated patient.
     * @param string $filename The unique, sanitized filename of the report.
     * @return \CodeIgniter\HTTP\Response
     */
    public function viewReport(string $filename)
    {
        $session = session();
        $patientId = $session->get('patient_id');

        // 1. Basic Security Check
        if (!$session->get('isLoggedIn') || $session->get('role_id') != 10 || !$patientId) {
            log_message('error', 'Attempted unauthorized access to report file: ' . $filename);
            return $this->response->setStatusCode(401)->setBody('Unauthorized Access.');
        }

        // 2. Define the file path using the most reliable method (ROOTPATH + public)
        // This mirrors the logic from the working admin controller and uses DIRECTORY_SEPARATOR 
        // for cross-OS compatibility.
        $uploadSubPath = 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'patient_reports' . DIRECTORY_SEPARATOR;
        $fileDir = ROOTPATH . $uploadSubPath;
        $filePath = $fileDir . $filename;

        // 3. Authorization Check: Verify the patient is authorized to view this specific file.
        $isAuthorized = $this->diagnosticsOrderModel->select('diagnostics_orders.id')
            ->where([
                'diagnostics_orders.patient_id' => $patientId,
            ])
            ->join('diagnostics_order_items OI', 'OI.diagnostics_order_id = diagnostics_orders.id')
            ->join('diagnostics_order_files R', 'R.diagnostics_order_item_id = OI.id')
            ->where('R.file_path', $filename)
            ->countAllResults() > 0;

        if (!file_exists($filePath) || !$isAuthorized) {
            if (!file_exists($filePath)) {
                log_message('error', 'Report file not found at: ' . $filePath);
                return $this->response->setStatusCode(404)->setBody('Report file not found on server.');
            }
            log_message('error', 'Patient ' . $patientId . ' attempted to access unauthorized report: ' . $filename);
            return $this->response->setStatusCode(403)->setBody('Access Forbidden: You do not own this report.');
        }

        // 4. Serve the file securely
        // Using mime_content_type and file_get_contents is standard for secure serving
        $mimeType = mime_content_type($filePath);

        return $this->response
            ->setStatusCode(200)
            ->setContentType($mimeType)
            // Use 'inline' to view in browser, or 'attachment' to force download
            ->setHeader('Content-Disposition', 'inline; filename="' . basename($filename) . '"')
            ->setBody(file_get_contents($filePath));
    }
}
