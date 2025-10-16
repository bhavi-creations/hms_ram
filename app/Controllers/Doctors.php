<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\DoctorModel;
use App\Models\UserModel;
use App\Models\DepartmentModel;
use App\Models\SpecializationModel; // <-- 💡 NEW: Import SpecializationModel

class Doctors extends BaseController
{
    protected $doctorModel;
    protected $userModel;
    protected $departmentModel;
    protected $specializationModel; // <-- 💡 NEW: Property for SpecializationModel
    protected $db;
    public function __construct()
    {
        $this->doctorModel = new DoctorModel();
        $this->userModel = new UserModel();
        $this->departmentModel = new DepartmentModel();
        $this->specializationModel = new SpecializationModel(); // <-- 💡 NEW: Initialize SpecializationModel
        $this->db = \Config\Database::connect();
    }




    public function index()
    {
        $doctors = $this->doctorModel

            ->select('doctors.*, hospital_departments.name AS department_name, specializations.name AS specialization')
            ->join('hospital_departments', 'hospital_departments.id = doctors.department_id', 'left')
            ->join('specializations', 'specializations.id = doctors.specialization', 'left')
            ->findAll();

        $data = [
            'doctors' => $doctors,
            'title'   => 'Doctors List',
        ];
        return view('doctors/list', $data);
    }

    public function new()
    {
        $data = [
            'title'       => 'Add New Doctor',
            'departments' => $this->departmentModel->findAll(),
            'specializations' => $this->specializationModel->findAll(), // <-- 💡 NEW: Fetch all specializations
        ];
        return view('doctors/add', $data);
    }

    public function create()
    {
        return $this->save();
    }

    public function edit($id = null)
    {
        $doctor = $this->doctorModel->find($id);

        if (!$doctor) {
            return redirect()->to(base_url('doctors'))->with('error', 'Doctor not found.');
        }

        // Decode other_certificates_path from JSON string to array
        $otherCertificatesArray = [];
        if (!empty($doctor['other_certificates_path'])) {
            $decoded = json_decode($doctor['other_certificates_path'], true);
            if (is_array($decoded)) {
                $otherCertificatesArray = $decoded;
            }
        }
        $doctor['other_certificates_array'] = $otherCertificatesArray; // Pass this decoded array to the view

        $data = [
            'title'       => 'Edit Doctor',
            'doctor'      => $doctor,
            'departments' => $this->departmentModel->findAll(), // Make sure departments are passed for the dropdown
            'specializations' => $this->specializationModel->findAll(), // <-- 💡 NEW: Fetch all specializations for edit
        ];
        return view('doctors/edit', $data);
    }

    public function update($id = null)
    {
        return $this->save($id);
    }


    // C:\xampp\htdocs\hms_ram\app\Controllers\Doctors.php

    public function save($id = null)
    {
        // 1. Get incoming request data and resolve ID
        $postData = $this->request->getPost();
        $doctorId = $id ?? $postData['id'] ?? null;

        // 2. Load existing doctor data if it's an edit operation
        $existingDoctor = null;
        $currentUserId = null;

        if ($doctorId) {
            $existingDoctor = $this->doctorModel->find($doctorId);
            if (!$existingDoctor) {
                return redirect()->back()->with('error', 'Doctor not found for editing.')->withInput();
            }
            $currentUserId = $existingDoctor['user_id'] ?? null;
        }

        // 3. Define Validation Rules
        $usernameRule = 'required|is_unique[users.username]';
        if ($doctorId) {
            $usernameRule = 'permit_empty'; // We handle uniqueness check manually for update below
        }

        // Final username rules setup
        $usernameRules = $usernameRule . '|min_length[5]|max_length[50]';

        // Password rules only if creating or if a value is provided on update
        $passwordRules = ($doctorId ? 'permit_empty' : 'required') . '|min_length[6]|max_length[255]';

        if ($doctorId && empty($postData['password'])) {
            $passwordRules = 'permit_empty';
        }


        $rules = [
            'first_name'                 => 'required|min_length[3]|max_length[100]',
            'last_name'                  => 'required|min_length[3]|max_length[100]',
            // NOTE: The validation for doctor's email is permit_empty, which is fine for the doctors table.
            // The previous issue was the 'is_unique' validation in the UserModel.
            'email'                      => 'permit_empty|valid_email|max_length[255]',
            'specialization'             => 'required|is_natural_no_zero',
            'department_id'              => 'required|is_natural_no_zero',
            'username'                   => [
                'rules' => $usernameRules,
                'errors' => [
                    'is_unique' => 'This username is already taken. Please choose another.'
                ]
            ],
            'password'                   => $passwordRules,
            'profile_picture'            => 'if_exist|uploaded[profile_picture]|max_size[profile_picture,2048]|ext_in[profile_picture,jpg,jpeg,png]',
            'resume_file'                => 'if_exist|uploaded[resume_file]|max_size[resume_file,5120]|ext_in[resume_file,pdf,doc,docx]',
            'degree_certificate_file'    => 'if_exist|uploaded[degree_certificate_file]|max_size[degree_certificate_file,5120]|ext_in[degree_certificate_file,pdf,jpg,jpeg,png]',
            'license_certificate_file'   => 'if_exist|uploaded[license_certificate_file]|max_size[license_certificate_file,5120]|ext_in[license_certificate_file,pdf,jpg,jpeg,png]',
            'other_certificate_file.*'   => 'if_exist|uploaded[other_certificate_file]|max_size[other_certificate_file,5120]|ext_in[other_certificate_file,pdf,doc,docx,jpg,jpeg,png]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // 4. Prepare data for the doctor model 
        $doctorData = [
            'first_name'                => $postData['first_name'],
            'last_name'                 => $postData['last_name'],
            'gender'                    => $postData['gender'] ?? null,
            'date_of_birth'             => $postData['date_of_birth'] ?? null,
            'email'                     => $postData['email'] ?? null, // Doctor's table email updated here
            'phone_number'              => $postData['phone_number'] ?? null,
            'address'                   => $postData['address'] ?? null,
            'emergency_contact_name'    => $postData['emergency_contact_name'] ?? null,
            'emergency_contact_phone'   => $postData['emergency_contact_phone'] ?? null,
            'specialization'            => $postData['specialization'],
            'qualification'             => $postData['qualification'] ?? null,
            'medical_license_no'        => $postData['medical_license_no'] ?? null,
            'registration_number'       => $postData['registration_number'] ?? null,
            'medical_council'           => $postData['medical_council'] ?? null,
            'experience_years'          => $postData['experience_years'] ?? null,
            'bio'                       => $postData['bio'] ?? null,
            'department_id'             => $postData['department_id'],
            'joining_date'              => $postData['joining_date'] ?? null,
            'employment_status'         => $postData['employment_status'] ?? null,
            'contract_type'             => $postData['contract_type'] ?? null,
            'designation'               => $postData['designation'] ?? null,
            'opd_fee'                   => $postData['opd_fee'] ?? null,
            'ipd_charge_percentage'     => $postData['ipd_charge_percentage'] ?? null,
            'bank_account_number'       => $postData['bank_account_number'] ?? null,
            'bank_name'                 => $postData['bank_name'] ?? null,
            'ifsc_code'                 => $postData['ifsc_code'] ?? null,
            'pan_number'                => $postData['pan_number'] ?? null,
            'is_available'              => $postData['is_available'] ?? 1,
            'status'                    => $postData['status'] ?? 'Active',
        ];

        // 5. User Creation/Update Logic (Crucial for Login)
        $this->db->transBegin();

        try {
            $userId = $currentUserId;

            if (!$doctorId) { // This is a NEW doctor entry
                $userData = [
                    'username'      => $postData['username'],
                    'password'      => $postData['password'],
                    'role_id'       => 2,
                    'first_name'    => $postData['first_name'],
                    'last_name'     => $postData['last_name'],
                    'email'         => $postData['email'] ?? null,
                    'phone_number'  => $postData['phone_number'] ?? null,
                    'address'       => $postData['address'] ?? null,
                    'status'        => 'active',
                ];

                if (!$this->userModel->insert($userData)) {
                    $userErrors = $this->userModel->errors();
                    throw new \Exception('Failed to create user account for doctor. Details: ' . json_encode($userErrors));
                }
                $userId = $this->userModel->getInsertID();
                $doctorData['user_id'] = $userId; // Link doctor to user ID
            } else { // This is an EXISTING doctor entry (update)
                if ($userId) {
                    $updateUserData = [
                        'first_name'    => $postData['first_name'],
                        'last_name'     => $postData['last_name'],
                        // NOTE: Removed 'email', 'phone_number', and 'address' updates 
                        // from the users table to prevent uniqueness issues if the 
                        // UserModel has 'is_unique' rules on those fields. 
                        // The doctors table is already updated above.
                    ];

                    $currentUser = $this->userModel->find($userId);

                    // Check and update username if provided and different
                    if (!empty($postData['username']) && $postData['username'] !== $currentUser['username']) {
                        // Manual uniqueness check for username update
                        if ($this->userModel->where('username', $postData['username'])->where('id !=', $userId)->first()) {
                            throw new \Exception('The new username is already taken by another user.');
                        }
                        $updateUserData['username'] = $postData['username'];
                    }

                    // Check and update password if provided
                    if (!empty($postData['password'])) {
                        $updateUserData['password'] = $postData['password'];
                    }

                    // Only attempt update if there's actual data to update
                    if (!empty($updateUserData)) {
                        if (!$this->userModel->update($userId, $updateUserData)) {
                            $userErrors = $this->userModel->errors();
                            // This will show if any other field (like username) fails validation
                            throw new \Exception('Failed to update user account details. Details: ' . json_encode($userErrors));
                        }
                    }
                } else {
                    throw new \Exception('Existing doctor has no linked user account (user_id is missing). Cannot update user details.');
                }
            }

            // 6. File Upload Handling (Standard CI4 logic, unchanged)
            $uploadBaseDir = 'uploads/doctors/';
            $uploadFullPath = ROOTPATH . 'public/' . $uploadBaseDir;

            if (!is_dir($uploadFullPath)) {
                mkdir($uploadFullPath, 0777, true);
            }

            $singleFilesMapping = [
                'profile_picture'            => 'profile_picture',
                'signature_image'            => 'signature_image',
                'resume_file'                => 'resume_path',
                'degree_certificate_file'    => 'degree_certificate_path',
                'license_certificate_file'   => 'license_certificate_path',
            ];

            foreach ($singleFilesMapping as $inputName => $dbColumn) {
                $file = $this->request->getFile($inputName);

                if ($file && $file->isValid() && !$file->hasMoved()) {
                    if ($existingDoctor && !empty($existingDoctor[$dbColumn])) {
                        $oldFilePath = $uploadFullPath . $existingDoctor[$dbColumn];
                        if (file_exists($oldFilePath)) {
                            unlink($oldFilePath); // Delete old file
                        }
                    }
                    $newName = $file->getRandomName();
                    $file->move($uploadFullPath, $newName);
                    $doctorData[$dbColumn] = $newName;
                }
            }

            // Handle 'Other Certificates' multi-file upload (unchanged)
            $currentOtherCertificates = [];
            if ($doctorId && $existingDoctor && !empty($existingDoctor['other_certificates_path'])) {
                $decoded = json_decode($existingDoctor['other_certificates_path'], true);
                if (is_array($decoded)) {
                    $currentOtherCertificates = $decoded;
                }
            }
            $newlyUploadedOtherCertificates = [];
            $otherCertFiles = $this->request->getFiles('other_certificate_file');

            if ($otherCertFiles && !empty($otherCertFiles['other_certificate_file'])) {
                foreach ($otherCertFiles['other_certificate_file'] as $file) {
                    if ($file->isValid() && !$file->hasMoved()) {
                        $newName = $file->getRandomName();
                        $file->move($uploadFullPath, $newName);
                        $newlyUploadedOtherCertificates[] = $newName;
                    }
                }
            }
            $finalOtherCertificates = array_merge($currentOtherCertificates, $newlyUploadedOtherCertificates);
            $doctorData['other_certificates_path'] = json_encode(array_values($finalOtherCertificates));


            // 7. Save Doctor data to the database
            if ($doctorId) {
                // Update operation
                if (!$this->doctorModel->update($doctorId, $doctorData)) {
                    throw new \Exception('Failed to update doctor details.');
                }
            } else {
                // Insert operation
                if (!$this->doctorModel->insert($doctorData)) {
                    throw new \Exception('Failed to add new doctor.');
                }
            }

            // If all operations succeeded, commit the transaction
            $this->db->transCommit();
            return redirect()->to(base_url('doctors'))->with('success', 'Doctor ' . ($doctorId ? 'updated' : 'added') . ' successfully.');
        } catch (\Exception $e) {
            // If any operation failed, rollback the transaction
            $this->db->transRollback();

            // Return the detailed exception message
            return redirect()->back()->with('error', 'Operation failed: ' . $e->getMessage())->withInput();
        }
    }

    public function delete($id = null)
    {
        if ($this->doctorModel->delete($id)) {
            return redirect()->to(base_url('doctors'))->with('success', 'Doctor deleted successfully!');
        } else {
            return redirect()->to(base_url('doctors'))->with('error', 'Failed to delete doctor.');
        }
    }




    public function view($id = null)
    {
        // Define the columns to select. We explicitly add users.last_login AS last_login_at
        // to retrieve the last login timestamp from the linked user account.
        $selectFields = 'doctors.*, 
                             hospital_departments.name AS department_name, 
                             specializations.name AS specialization_name, 
                             users.username AS user_username, 
                             users.email AS user_email,
                             users.last_login AS last_login_at';

        // Fetch doctor details, including the department name
        $doctor = $this->doctorModel
            ->select($selectFields)
            // Join for Department Name
            ->join('hospital_departments', 'hospital_departments.id = doctors.department_id', 'left')
            // NEW Join for Specialization Name
            ->join('specializations', 'specializations.id = doctors.specialization', 'left')
            // Join for User Details
            ->join('users', 'users.id = doctors.user_id', 'left')
            ->find($id);

        if (!$doctor) {
            return redirect()->to(base_url('doctors'))->with('error', 'Doctor not found.');
        }

        $data = [
            'title'  => 'Doctor Details',
            'doctor' => $doctor,
        ];

        return view('doctors/view', $data);
    }


    public function deleteDocumentAjax()
    {
        // Ensure this is an AJAX request and method is POST
        if (!$this->request->isAJAX() || !$this->request->is('post')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid request.']);
        }

        $input = $this->request->getJSON(true); // Get JSON input as associative array
        $doctorId = $input['id'] ?? null;
        $field = $input['field'] ?? null;
        $fileName = $input['fileName'] ?? null;

        // Basic validation
        if (empty($doctorId) || empty($field) || empty($fileName)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Missing required data.']);
        }

        $doctor = $this->doctorModel->find($doctorId);

        if (!$doctor) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Doctor not found.']);
        }

        // --- IMPORTANT: This path MUST match your save() method's upload path ---
        $filePath = ROOTPATH . 'public/uploads/doctors/' . $fileName;

        // Check if file exists and delete it
        if (file_exists($filePath)) {
            if (!unlink($filePath)) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to delete file from server. Permissions issue?']);
            }
        } else {
            // File might already be gone from disk, proceed to update DB anyway
            // Or log this: log_message('warning', "File not found for deletion: " . $filePath);
        }

        // Update database record based on the field
        $updateData = [];
        if ($field === 'other_certificates_path') {
            // For multiple certificates, decode, remove, encode
            // Correctly reference the database column 'other_certificates_path'
            $currentFiles = json_decode($doctor['other_certificates_path'] ?? '[]', true);
            if (is_array($currentFiles)) {
                $updatedFiles = array_filter($currentFiles, fn($file) => $file !== $fileName);
                $updateData[$field] = json_encode(array_values($updatedFiles)); // Re-index array
            } else {
                $updateData[$field] = '[]'; // If it was malformed, reset it
            }
        } else {
            // For single file fields, set to null
            $updateData[$field] = null;
        }

        if ($this->doctorModel->update($doctorId, $updateData)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Document deleted successfully.',
                'csrf_hash' => csrf_hash() // Send updated CSRF token
            ]);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to update database record.']);
        }
    }


    public function deleteFile()
    {
        $input = json_decode($this->request->getBody(), true);
        $doctorId = $input['doctor_id'] ?? null;
        $field = $input['field'] ?? null;
        $filename = $input['filename'] ?? null;

        if (!$doctorId || !$field) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid data']);
        }

        $doctorModel = new DoctorModel();
        $doctor = $doctorModel->find($doctorId);

        if (!$doctor) {
            return $this->response->setJSON(['success' => false, 'message' => 'Doctor not found']);
        }

        // Single file field
        if (!$filename) {
            $fileToDelete = $doctor[$field] ?? null;
            if ($fileToDelete) {
                $filePath = WRITEPATH . 'uploads/doctors/' . $fileToDelete;
                if (file_exists($filePath)) unlink($filePath);
                $doctorModel->update($doctorId, [$field => null]);
                return $this->response->setJSON(['success' => true]);
            }
            return $this->response->setJSON(['success' => false, 'message' => 'File not found']);
        }

        // Multi-file field
        $files = json_decode($doctor[$field], true);
        if (in_array($filename, $files)) {
            $filePath = WRITEPATH . 'uploads/doctors/' . $filename;
            if (file_exists($filePath)) unlink($filePath);
            $newFiles = array_values(array_filter($files, fn($f) => $f !== $filename));
            $doctorModel->update($doctorId, [$field => json_encode($newFiles)]);
            return $this->response->setJSON(['success' => true]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'File not found']);
    }
}
