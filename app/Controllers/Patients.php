<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PatientModel;
use App\Models\DoctorModel;
use App\Models\ReferredPersonModel;
use App\Models\PatientIdSequenceModel;
use CodeIgniter\Files\File;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\I18n\Time;
use App\Models\AppointmentModel;
use App\Models\PatientAdmissionModel;

class Patients extends BaseController
{
    use ResponseTrait;

    protected $appointmentModel;
    protected $patientModel;
    protected $doctorModel;
    protected $referredPersonModel;
    protected $patientIdSequenceModel;
    protected $patientAdmissionModel;

    public function __construct()
    {
        // Initialize models
        $this->appointmentModel = new AppointmentModel();
        $this->patientModel = new PatientModel();
        $this->doctorModel = new DoctorModel();
        $this->referredPersonModel = new ReferredPersonModel();
        $this->patientIdSequenceModel = new PatientIdSequenceModel();
        $this->patientAdmissionModel = new PatientAdmissionModel();

        // Load helpers
        helper('form');
        helper('filesystem');
    }

    /**
     * Displays a list of all patients.
     */
    public function index()
    {
        $data['title'] = 'Patient List';
        $data['patients'] = $this->patientModel->orderBy('created_at', 'DESC')->findAll();
        return view('patients/patient_list', $data);
    }

    /**
     * Filters patients by various criteria (e.g., full name, date, etc.).
     * This method is used for AJAX filtering on the patient list page.
     */
    public function filter()
    {
        $field = $this->request->getGet('field');
        $value = $this->request->getGet('value');
        $value = trim($value);

        $model = $this->patientModel;
        $patients = [];

        if ($field && $value) {
            if ($field === 'full_name') {
                $patients = $model->groupStart()
                    ->like('first_name', $value)
                    ->orLike('last_name', $value)
                    ->groupEnd()
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
            } elseif (in_array($field, ['created_at', 'date_of_birth'])) {
                $patients = $model->like($field, $value)->orderBy('created_at', 'DESC')->findAll();
            } else {
                $patients = $model->like($field, $value)->orderBy('created_at', 'DESC')->findAll();
            }
        } else {
            $patients = $model->orderBy('created_at', 'DESC')->findAll();
        }

        return view('patients/partials/patient_table', ['patients' => $patients]);
    }

    /**
     * Displays the form to register a new patient.
     */
    public function register()
    {
        $data['title'] = 'Register New Patient';
        $data['validation'] = \Config\Services::validation();

        // Fetch doctors and referred persons for dropdowns
        $data['doctors'] = $this->doctorModel->findAllDoctors();
        $data['referred_persons'] = $this->referredPersonModel->findAll();
        $data['patient'] = []; // Empty patient array for new registration

        // Set old input values for form fields, or default values
        $data['appointment_date'] = old('appointment_date', date('Y-m-d'));
        $data['appointment_time'] = old('appointment_time', date('H:i'));
        $data['reason_for_visit'] = old('reason_for_visit');

        return view('patients/register_patient', $data);
    }

    /**
     * Saves a new patient record or updates an existing one.
     * Handles file uploads and appointment creation/update.
     */
    public function save()
    {
        $session = session();
        $uploadDir = ROOTPATH . 'public/uploads/patient_reports/';

        // Ensure upload directory exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Define file upload validation rules
        $fileValidationRules = [
            'upload_reports.*' => [
                'rules' => 'max_size[upload_reports,5120]|ext_in[upload_reports,pdf,doc,docx,jpg,jpeg,png]',
                'errors' => [
                    'max_size' => 'Each report file must be less than 5MB.',
                    'ext_in'   => 'Only PDF, DOC, DOCX, JPG, JPEG, PNG files are allowed.',
                ],
            ],
        ];

        // Define appointment-related validation rules
        $appointmentValidation = [
            'referred_to_doctor_id' => 'permit_empty|integer',
            'appointment_date' => 'permit_empty|valid_date',
            'appointment_time' => 'permit_empty|regex_match[/^(?:[01]\d|2[0-3]):[0-5]\d$/]',
            'reason_for_visit' => 'permit_empty|string|max_length[1000]',
        ];

        // Merge all validation rules
        $validationRules = array_merge(
            $this->patientModel->validationRules,
            $fileValidationRules,
            $appointmentValidation
        );

        // Run validation
        if (!$this->validate($validationRules)) {
            $session->setFlashdata('error', 'Please correct the errors in the form.');
            $fileErrors = $this->validator->getErrors('upload_reports.*');
            if (!empty($fileErrors)) {
                $session->setFlashdata('file_errors', $fileErrors);
            }
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $patientId = $this->request->getPost('id');
        $currentReportFilenames = [];

        // If updating an existing patient, retrieve current reports
        if (!empty($patientId)) {
            $existingPatient = $this->patientModel->find($patientId);
            if ($existingPatient && !empty($existingPatient['reports'])) {
                $currentReportFilenames = json_decode($existingPatient['reports'], true) ?? [];
            }
        }

        // Handle file uploads
        $files = $this->request->getFiles();
        if (isset($files['upload_reports'])) {
            foreach ($files['upload_reports'] as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $originalName = $file->getClientName();
                    $cleanedName = preg_replace('/[^A-Za-z0-9\.\-_ ]/', '_', $originalName);
                    $uniqueFilename = time() . '_' . rand(1000, 9999) . '_' . $cleanedName;
                    $file->move($uploadDir, $uniqueFilename);
                    $currentReportFilenames[] = $uniqueFilename;
                }
            }
        }

        // Calculate final amount based on fee and discount
        $fee = (float) $this->request->getPost('fee');
        $discountPercentage = (float) $this->request->getPost('discount_percentage');
        $finalAmount = round($fee - ($fee * ($discountPercentage / 100)), 2);

        // Prepare patient data for saving
        $data = [
            'first_name'                => $this->request->getPost('first_name'),
            'last_name'                 => $this->request->getPost('last_name'),
            'date_of_birth'             => $this->request->getPost('date_of_birth'),
            'gender'                    => $this->request->getPost('gender'),
            'patient_type'              => $this->request->getPost('patient_type'),
            'blood_group'               => $this->request->getPost('blood_group'),
            'marital_status'            => $this->request->getPost('marital_status'),
            'occupation'                => $this->request->getPost('occupation'),
            'address'                   => $this->request->getPost('address'),
            'phone_number'              => $this->request->getPost('phone_number'),
            'email'                     => $this->request->getPost('email'),
            'emergency_contact_name'    => $this->request->getPost('emergency_contact_name'),
            'emergency_contact_phone'   => $this->request->getPost('emergency_contact_phone'),
            'known_allergies'           => $this->request->getPost('known_allergies'),
            'pre_existing_conditions'   => $this->request->getPost('pre_existing_conditions'),
            'referred_to_doctor_id'     => $this->request->getPost('referred_to_doctor_id') ?: null,
            'referred_by_id'            => $this->request->getPost('referred_by_id') ?: null,
            'remarks'                   => $this->request->getPost('remarks'),
            'reports'                   => !empty($currentReportFilenames) ? json_encode($currentReportFilenames) : null,
            'fee'                       => $fee,
            'discount_percentage'       => $discountPercentage,
            'final_amount'              => $finalAmount,
        ];

        $appointmentModel = new \App\Models\AppointmentModel();

        // Handle INSERT (new patient)
        if (empty($patientId)) {
            if ($this->patientModel->save($data)) {
                $newId = $this->patientModel->getInsertID();

                // Create appointment if doctor, date, and time are provided
                $referredDoctorId = $this->request->getPost('referred_to_doctor_id');
                $appointmentDate = $this->request->getPost('appointment_date');
                $appointmentTime = $this->request->getPost('appointment_time');

                if (!empty($referredDoctorId) && !empty($appointmentDate) && !empty($appointmentTime)) {
                    $appointmentData = [
                        'patient_id'        => $newId,
                        'doctor_id'         => $referredDoctorId,
                        'appointment_date'  => $appointmentDate,
                        'appointment_time'  => $appointmentTime,
                        'reason_for_visit'  => $this->request->getPost('reason_for_visit'),
                        'status'            => 'Pending'
                    ];
                    $appointmentModel->insert($appointmentData);
                }

                // Prepare success message with generated IDs
                $newPatient = $this->patientModel->find($newId);
                $typeMsg = match ($newPatient['patient_type']) {
                    'OPD' => 'OPD ID: ' . ($newPatient['opd_id_code'] ?? 'N/A'),
                    'IPD' => 'IPD ID: ' . ($newPatient['ipd_id_code'] ?? 'N/A'),
                    'General' => 'General ID: ' . ($newPatient['gen_id_code'] ?? 'N/A'),
                    'Casualty' => 'Casualty ID: ' . ($newPatient['cus_id_code'] ?? 'N/A'),
                    default => 'Type ID: N/A'
                };

                $session->setFlashdata('success', 'Patient registered successfully! Primary ID: ' . $newPatient['patient_id_code'] . ' | ' . $typeMsg);
                return redirect()->to('/patients/register');
            } else {
                $session->setFlashdata('error', 'Failed to register patient. Please try again.');
                return redirect()->back()->withInput();
            }
        }

        // Handle UPDATE (existing patient)
        else {
            // Ensure previous_patient_type is handled correctly on update
            $existingPatient = $this->patientModel->find($patientId);
            if ($existingPatient && $data['patient_type'] === 'IPD' && $existingPatient['patient_type'] !== 'IPD') {
                $data['previous_patient_type'] = $existingPatient['patient_type'];
            } elseif ($existingPatient && $data['patient_type'] !== 'IPD' && $existingPatient['patient_type'] === 'IPD') {
                // If changing *from* IPD to another type (not discharged), clear previous_patient_type
                $data['previous_patient_type'] = null;
            }


            if ($this->patientModel->update($patientId, $data)) {
                $referredDoctorId = $this->request->getPost('referred_to_doctor_id');
                $appointmentDate = $this->request->getPost('appointment_date');
                $appointmentTime = $this->request->getPost('appointment_time');

                // Update/insert appointment if details are provided
                if (!empty($referredDoctorId) && !empty($appointmentDate) && !empty($appointmentTime)) {
                    $appointmentData = [
                        'patient_id'        => $patientId,
                        'doctor_id'         => $referredDoctorId,
                        'appointment_date'  => $appointmentDate,
                        'appointment_time'  => $appointmentTime,
                        'reason_for_visit'  => $this->request->getPost('reason_for_visit'),
                        'status'            => 'Confirmed' // Or keep existing status if editing
                    ];

                    $existingAppointment = $appointmentModel->where('patient_id', $patientId)->first();

                    if ($existingAppointment) {
                        $appointmentModel->update($existingAppointment['id'], $appointmentData);
                    } else {
                        $appointmentModel->insert($appointmentData);
                    }
                } else {
                    // If appointment details are removed from the form, and an existing appointment exists,
                    // you might want to delete it or set its status to cancelled.
                    $existingAppointment = $appointmentModel->where('patient_id', $patientId)->first();
                    if ($existingAppointment && ($existingAppointment['status'] == 'Pending' || $existingAppointment['status'] == 'Confirmed')) {
                        // Optionally set to cancelled if details are removed
                        // $appointmentModel->update($existingAppointment['id'], ['status' => 'Cancelled']);
                    }
                }

                // Prepare success message with updated IDs
                $updatedPatient = $this->patientModel->find($patientId);
                $typeMsg = match ($updatedPatient['patient_type']) {
                    'OPD' => 'OPD ID: ' . ($updatedPatient['opd_id_code'] ?? 'N/A'),
                    'IPD' => 'IPD ID: ' . ($updatedPatient['ipd_id_code'] ?? 'N/A'),
                    'General' => 'General ID: ' . ($updatedPatient['gen_id_code'] ?? 'N/A'),
                    'Casualty' => 'Casualty ID: ' . ($updatedPatient['cus_id_code'] ?? 'N/A'),
                    'Discharged' => 'Discharged (No specific ID)', // Added for discharged patients
                    default => 'Type ID: N/A'
                };

                $session->setFlashdata('success', 'Patient updated successfully! Primary ID: ' . ($updatedPatient['patient_id_code'] ?? 'N/A') . ' | ' . $typeMsg);
                return redirect()->to('/patients');
            } else {
                $session->setFlashdata('error', 'Failed to update patient. Please try again.');
                return redirect()->back()->withInput();
            }
        }
    }

    /**
     * Handles admitting a patient to IPD from the Patients list.
     * This method will update the patient's type and potentially create an initial admission record.
     *
     * @return \CodeIgniter\HTTP\Response
     */
    public function admitToIPD()
    {
        if (!$this->request->isAJAX()) {
            return $this->failForbidden('Only AJAX requests are allowed.');
        }

        $patientId = $this->request->getPost('patient_id');

        if (empty($patientId)) {
            return $this->failValidationErrors('Patient ID is required.');
        }

        // Access the database connection from BaseController
        $db = \Config\Database::connect();
        $db->transBegin(); // Start transaction

        try {
            // Update patient type to IPD and store previous type
            $patientUpdateSuccess = $this->patientModel->admitPatientToIPD((int)$patientId, []);

            if (!$patientUpdateSuccess) {
                $db->transRollback();
                return $this->respondCreated(['success' => false, 'message' => 'Failed to update patient type to IPD.']);
            }

            // Create a new patient_admission record with 'Waiting Assignment' status
            $admissionData = [
                'patient_id' => (int)$patientId,
                'admission_date' => date('Y-m-d H:i:s'),
                'admission_status' => 'Waiting Assignment',
                'notes' => 'Admitted to IPD, awaiting ward/bed assignment.',
            ];

            $admissionId = $this->patientAdmissionModel->insert($admissionData); // Use the initialized model

            if (!$admissionId) {
                $db->transRollback();
                return $this->respondCreated(['success' => false, 'message' => 'Failed to create initial IPD admission record.']);
            }

            if ($db->transStatus() === false) {
                $db->transRollback();
                return $this->respondCreated(['success' => false, 'message' => 'Transaction failed during IPD admission.']);
            } else {
                $db->transCommit();
                return $this->respondCreated(['success' => true, 'message' => 'Patient admitted to IPD successfully. Please assign a ward and bed.']);
            }
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Exception during admitToIPD for patient ID ' . $patientId . ': ' . $e->getMessage());
            return $this->fail('An error occurred during IPD admission.', 500);
        }
    }


    /**
     * Downloads or displays a patient report based on its MIME type.
     *
     * @param string $filename The name of the file to download/display.
     */
    public function downloadReport($filename)
    {
        // --- DEBUGGING START ---
        log_message('debug', 'downloadReport: Function called for filename: ' . $filename);
        // --- DEBUGGING END ---

        // Define potential file paths
        // Using FCPATH for direct access to the public folder from the root of the CodeIgniter project.
        $publicFilePath = FCPATH . 'public/uploads/patient_reports/' . $filename;

        // --- DEBUGGING START ---
        log_message('debug', 'downloadReport: Checking file path: ' . $publicFilePath);
        // --- DEBUGGING END ---

        $filePathToServe = null;

        if (file_exists($publicFilePath)) {
            $filePathToServe = $publicFilePath;
            // --- DEBUGGING START ---
            log_message('debug', 'downloadReport: File found at: ' . $publicFilePath);
            // --- DEBUGGING END ---
        } else {
            // --- DEBUGGING START ---
            log_message('error', 'downloadReport: File NOT found at expected path: ' . $publicFilePath);
            // --- DEBUGGING END ---
            throw new \CodeIgniter\Exceptions\PageNotFoundException('File not found: ' . $filename);
        }

        // Determine MIME type
        $mime = mime_content_type($filePathToServe);
        // --- DEBUGGING START ---
        log_message('debug', 'downloadReport: Determined MIME type: ' . $mime);
        // --- DEBUGGING END ---

        // List of MIME types that browsers can typically display inline
        $inlineMimeTypes = [
            'application/pdf',
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
        ];

        if (in_array($mime, $inlineMimeTypes)) {
            // Set content type and send file for inline display
            $this->response->setHeader('Content-Type', $mime);
            $this->response->setHeader('Content-Disposition', 'inline; filename="' . basename($filename) . '"');
            $this->response->setBody(file_get_contents($filePathToServe));

            // --- DEBUGGING START ---
            log_message('debug', 'downloadReport: Sending file for INLINE display. Headers: Content-Type: ' . $mime . ', Content-Disposition: inline');
            // --- DEBUGGING END ---
            return $this->response;
        } else {
            // For other file types, force download
            // --- DEBUGGING START ---
            log_message('info', 'downloadReport: Forcing DOWNLOAD for file (MIME type: ' . $mime . '). Headers: Content-Disposition: attachment');
            // --- DEBUGGING END ---
            return $this->response->download($filePathToServe, null);
        }
    }

    /**
     * Displays details of a specific patient.
     *
     * @param int $id The ID of the patient to view.
     */
    public function view($id = null)
    {
        $patient = $this->patientModel->find($id);

        if (!$patient) {
            return redirect()->to('/patients')->with('error', 'Patient not found.');
        }

        $referredDoctor = null;
        if (!empty($patient['referred_to_doctor_id'])) {
            $doctorModel = new \App\Models\DoctorModel();
            $referredDoctor = $doctorModel->find($patient['referred_to_doctor_id']);
        }

        $referredByPerson = null;
        if (!empty($patient['referred_by_id'])) {
            $referredPersonModel = new \App\Models\ReferredPersonModel();
            $referredByPerson = $referredPersonModel->find($patient['referred_by_id']);
        }

        return view('patients/view_patient', [
            'title' => 'Patient Details',
            'patient' => $patient,
            'referredDoctor' => $referredDoctor,
            'referredByPerson' => $referredByPerson
        ]);
    }

    /**
     * Displays the form to edit an existing patient.
     *
     * @param int $id The ID of the patient to edit.
     */
    public function edit($id = null)
    {
        $data['title'] = 'Edit Patient';
        $data['validation'] = \Config\Services::validation();

        $patient = $this->patientModel->find($id);
        if (!$patient) {
            session()->setFlashdata('error', 'Patient not found.');
            return redirect()->to('/patients');
        }

        $data['patient'] = $patient;
        $data['doctors'] = $this->doctorModel->findAllDoctors();
        $data['referred_persons'] = $this->referredPersonModel->findAll();

        $appointment = $this->appointmentModel
            ->where('patient_id', $id)
            ->orderBy('id', 'desc')
            ->first();

        $data['appointment_date'] = old('appointment_date', $appointment['appointment_date'] ?? '');
        $data['appointment_time'] = old('appointment_time', isset($appointment['appointment_time']) ? date('H:i', strtotime($appointment['appointment_time'])) : '');
        $data['reason_for_visit'] = old('reason_for_visit', $appointment['reason_for_visit'] ?? '');

        return view('patients/register_patient', $data);
    }

    /**
     * Deletes a patient record.
     *
     * @param int $id The ID of the patient to delete.
     */
    public function delete($id = null)
    {
        $session = session();
        if ($this->patientModel->delete($id)) {
            $session->setFlashdata('success', 'Patient deleted successfully.');
        } else {
            $session->setFlashdata('error', 'Failed to delete patient.');
        }
        return redirect()->to('/patients');
    }

    /**
     * Deletes a specific report file via AJAX.
     * Now correctly retrieves data using getPost() as jQuery sends form data by default.
     */
    public function deleteReportFile()
    {
        if ($this->request->isAJAX()) {
            // Changed from getJSON() to getPost() to correctly read form-urlencoded data
            $patientId = $this->request->getPost('patient_id');
            $filename = $this->request->getPost('filename');

            if (!$patientId || !$filename) {
                return $this->response->setJSON(['success' => false, 'message' => 'Invalid data provided.']);
            }

            $patientModel = new \App\Models\PatientModel();
            $patient = $patientModel->where('patient_id_code', $patientId)->first();

            if (!$patient) {
                return $this->response->setJSON(['success' => false, 'message' => 'Patient not found.']);
            }

            $reportList = json_decode($patient['reports'], true);
            if (!is_array($reportList)) $reportList = [];

            if (!in_array($filename, $reportList)) {
                return $this->response->setJSON(['success' => false, 'message' => 'File not found in record.']);
            }

            $filePath = FCPATH . 'public/uploads/patient_reports/' . $filename;
            if (file_exists($filePath)) {
                // Attempt to delete the file
                unlink($filePath);
            } else {
                // Log if file doesn't exist on disk but is in DB (for debugging)
                log_message('warning', 'Attempted to delete report file that does not exist on disk: ' . $filePath);
            }

            // Remove the filename from the patient's reports array in the database
            $updatedReports = array_values(array_filter($reportList, fn($f) => $f !== $filename));
            $patientModel->update($patient['id'], ['reports' => json_encode($updatedReports)]);

            // Return success response with updated CSRF hash
            return $this->response->setJSON(['success' => true, 'message' => 'File deleted successfully.', 'csrfHash' => csrf_hash()]);
        }

        // If not an AJAX request, return forbidden
        return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Unauthorized request.']);
    }


    /**
     * AJAX endpoint to fetch patients by phone number.
     * Returns JSON array of patient objects.
     */
    public function getPatientsByPhone()
    {
        if (!$this->request->isAJAX()) {
            return $this->failForbidden('Only AJAX requests are allowed.');
        }

        $phoneNumber = $this->request->getGet('phone');

        if (empty($phoneNumber)) {
            return $this->respond([]); // Return empty array if no phone number provided
        }

        // Fetch patients by phone number
        $patients = $this->patientModel
            ->select('id, first_name, last_name, patient_id_code')
            ->like('phone_number', $phoneNumber, 'after') // Use 'after' for "starts with" search
            ->findAll();

        return $this->respond($patients); // Return patients as JSON
    }

    /**
     * Displays a list of patients associated with the logged-in doctor.
     * This includes patients they have appointments with.
     */
    public function doctorPatientsList()
    {
        $session = session();
        $doctor_id = $session->get('doctor_id');
        $role_id = $session->get('role_id');

        if ($role_id != 2 || !$doctor_id) {
            session()->setFlashdata('error', 'Access denied. You do not have permission to view this page.');
            return redirect()->to('/doctor/dashboard');
        }

        $appointmentModel = new AppointmentModel();
        $patientIds = $appointmentModel
            ->select('patient_id')
            ->where('doctor_id', $doctor_id)
            ->distinct()
            ->findColumn('patient_id');

        $data['patients'] = [];
        if (!empty($patientIds)) {
            $data['patients'] = $this->patientModel
                ->whereIn('id', $patientIds)
                ->orderBy('created_at', 'DESC')
                ->findAll();
        }

        $data['title'] = 'My Patients';
        $data['doctor_name'] = $session->get('first_name') . ' ' . $session->get('last_name');

        return view('doctors/patients_list', $data);
    }

    /**
     * Displays a list of all discharged patients.
     */
    public function dischargedPatients()
    {
        $data = [
            'title' => 'Discharged Patients List',
            // Fetch patients where patient_type is 'Discharged'
            'patients' => $this->patientModel->where('patient_type', 'Discharged')->orderBy('created_at', 'DESC')->findAll()
        ];
        return view('patients/discharged_patients', $data);
    }
}
