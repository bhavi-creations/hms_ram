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


class Patients extends BaseController
{
    use ResponseTrait;

    protected $appointmentModel;
    protected $patientModel;
    protected $doctorModel;
    protected $referredPersonModel;
    protected $patientIdSequenceModel;

    public function __construct()
    {

        $this->appointmentModel = new AppointmentModel();
        $this->patientModel = new PatientModel();
        $this->doctorModel = new DoctorModel();
        $this->referredPersonModel = new ReferredPersonModel();
        $this->patientIdSequenceModel = new PatientIdSequenceModel();

        helper('form');
        helper('filesystem');
    }

    public function index()
    {
        $data['title'] = 'Patient List';
        $data['patients'] = $this->patientModel->orderBy('created_at', 'DESC')->findAll();
        return view('patients/patient_list', $data);
    }

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

    public function register()
    {
        $data['title'] = 'Register New Patient';
        $data['validation'] = \Config\Services::validation();

        $doctorModel = new \App\Models\DoctorModel();
        $data['doctors'] = $this->doctorModel->findAllDoctors();

        $data['referred_persons'] = $this->referredPersonModel->findAll();
        $data['patient'] = [];

        $data['appointment_date'] = old('appointment_date', date('Y-m-d'));
        $data['appointment_time'] = old('appointment_time', date('H:i'));

        $data['reason_for_visit'] = old('reason_for_visit');

        return view('patients/register_patient', $data);
    }

    public function save()
    {
        $session = session();
        $uploadDir = ROOTPATH . 'public/uploads/patient_reports/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileValidationRules = [
            'upload_reports.*' => [
                'rules' => 'max_size[upload_reports,5120]|ext_in[upload_reports,pdf,doc,docx,jpg,jpeg,png]',
                'errors' => [
                    'max_size' => 'Each report file must be less than 5MB.',
                    'ext_in'   => 'Only PDF, DOC, DOCX, JPG, JPEG, PNG files are allowed.',
                ],
            ],
        ];

        $appointmentValidation = [
            'appointment_date' => 'required|valid_date',
            'appointment_time' => 'required|regex_match[/^(?:[01]\d|2[0-3]):[0-5]\d$/]',
            'reason_for_visit' => 'permit_empty|string|max_length[1000]',
        ];

        $validationRules = array_merge(
            $this->patientModel->validationRules,
            $fileValidationRules,
            $appointmentValidation
        );

        // Add validation for patient_id if it's coming from the form (for update scenarios)
        // This is important because the patient_id is now dynamically selected.
        if ($this->request->getPost('patient_id')) {
            $validationRules['patient_id'] = 'required|integer';
        } else {
            // If patient_id is not present, it means a new patient is being registered,
            // or the phone number search didn't yield results and no patient was selected.
            // We should ensure that either a patient_id is selected OR the patient registration fields are valid.
            // For now, let's assume patient_id is always required for scheduling an appointment.
            // If the user is registering a new patient, they should use the separate patient registration form.
            $validationRules['patient_id'] = 'required|integer';
        }


        if (!$this->validate($validationRules)) {
            $session->setFlashdata('error', 'Please correct the errors in the form.');
            $fileErrors = $this->validator->getErrors('upload_reports.*');
            if (!empty($fileErrors)) {
                $session->setFlashdata('file_errors', $fileErrors);
            }
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $patientId = $this->request->getPost('id'); // This 'id' is for patient update, not appointment patient_id
        $currentReportFilenames = [];

        if (!empty($patientId)) {
            $existingPatient = $this->patientModel->find($patientId);
            if ($existingPatient && !empty($existingPatient['reports'])) {
                $currentReportFilenames = json_decode($existingPatient['reports'], true) ?? [];
            }
        }

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

        $fee = (float) $this->request->getPost('fee');
        $discountPercentage = (float) $this->request->getPost('discount_percentage');
        $finalAmount = round($fee - ($fee * ($discountPercentage / 100)), 2);

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

        // INSERT
        if (empty($patientId)) {
            if ($this->patientModel->save($data)) {
                $newId = $this->patientModel->getInsertID();

                // Save appointment
                $appointmentData = [
                    'patient_id'        => $newId,
                    'doctor_id'         => $this->request->getPost('referred_to_doctor_id'),
                    'appointment_date'  => $this->request->getPost('appointment_date'),
                    'appointment_time'  => $this->request->getPost('appointment_time'),
                    'reason_for_visit'  => $this->request->getPost('reason_for_visit'),
                    'status'            => 'Pending'
                ];
                $appointmentModel->insert($appointmentData);

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

        // UPDATE
        else {
            if ($this->patientModel->update($patientId, $data)) {
                // Save or Update Appointment
                $appointmentData = [
                    'patient_id'        => $patientId,
                    'doctor_id'         => $this->request->getPost('referred_to_doctor_id'),
                    'appointment_date'  => $this->request->getPost('appointment_date'),
                    'appointment_time'  => $this->request->getPost('appointment_time'),
                    'reason_for_visit'  => $this->request->getPost('reason_for_visit'),
                    'status'            => 'Confirmed'
                ];

                $existingAppointment = $appointmentModel->where('patient_id', $patientId)->first();

                if ($existingAppointment) {
                    $appointmentModel->update($existingAppointment['id'], $appointmentData);
                } else {
                    $appointmentModel->insert($appointmentData);
                }

                $updatedPatient = $this->patientModel->find($patientId);
                $typeMsg = match ($updatedPatient['patient_type']) {
                    'OPD' => 'OPD ID: ' . ($updatedPatient['opd_id_code'] ?? 'N/A'),
                    'IPD' => 'IPD ID: ' . ($updatedPatient['ipd_id_code'] ?? 'N/A'),
                    'General' => 'General ID: ' . ($updatedPatient['gen_id_code'] ?? 'N/A'),
                    'Casualty' => 'Casualty ID: ' . ($updatedPatient['cus_id_code'] ?? 'N/A'),
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


    public function admitToIPD()
    {
        if (!$this->request->isAJAX()) {
            return $this->failForbidden('Only AJAX requests are allowed.');
        }

        $patientId = $this->request->getPost('patient_id');

        if (empty($patientId)) {
            return $this->failValidationErrors('Patient ID is required.');
        }

        $admissionData = $this->request->getPost();

        $admitted = $this->patientModel->admitPatientToIPD((int)$patientId, $admissionData);

        if ($admitted) {
            $updatedPatient = $this->patientModel->find($patientId);
            return $this->respondCreated(['success' => true, 'message' => 'Patient successfully admitted to IPD.', 'patient' => $updatedPatient]);
        } else {
            return $this->fail('Failed to admit patient to IPD.', 500);
        }
    }


    public function downloadReport($filename)
    {
        $publicFilePath = ROOTPATH . 'public/uploads/patient_reports/' . $filename;
        $legacyFilePath = WRITEPATH . 'uploads/patient_reports/' . $filename;

        $filePathToDownload = null;

        if (file_exists($publicFilePath)) {
            $filePathToDownload = $publicFilePath;
        } elseif (file_exists($legacyFilePath)) {
            $filePathToDownload = $legacyFilePath;
        }

        if (!$filePathToDownload) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('File not found: ' . $filename);
        }

        return $this->response->download($filePathToDownload, null);
    }

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

    public function deleteReportFile()
    {
        if ($this->request->isAJAX()) {
            $patientId = $this->request->getJSON()->patient_id;
            $filename = $this->request->getJSON()->filename;

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
                unlink($filePath);
            }

            $updatedReports = array_values(array_filter($reportList, fn($f) => $f !== $filename));
            $patientModel->update($patient['id'], ['reports' => json_encode($updatedReports)]);

            return $this->response->setJSON(['success' => true, 'message' => 'File deleted successfully.']);
        }

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
}
