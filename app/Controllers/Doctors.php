<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\DoctorModel;
use App\Models\UserModel;
use App\Models\DepartmentModel;

class Doctors extends BaseController
{
    protected $doctorModel;
    protected $userModel;
    protected $departmentModel;
    protected $db;
    public function __construct()
    {
        $this->doctorModel = new DoctorModel();
        $this->userModel = new UserModel();
        $this->departmentModel = new DepartmentModel();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        // --- THIS IS THE CORRECTED PART FOR DISPLAYING DEPARTMENT NAME ---
        // Changed 'departments' to 'hospital_departments'
        // Changed 'departments.department_name' to 'hospital_departments.name'
        $doctors = $this->doctorModel
            ->select('doctors.*, hospital_departments.name AS department_name') // Select department 'name' and alias it as 'department_name' for consistency
            ->join('hospital_departments', 'hospital_departments.id = doctors.department_id', 'left')
            ->findAll(); // Or ->paginate() if you are using pagination

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

        $data = [
            'title'       => 'Edit Doctor',
            'doctor'      => $doctor,
            'departments' => $this->departmentModel->findAll(),
        ];
        return view('doctors/edit', $data);
    }

    public function update($id = null)
    {
        return $this->save($id);
    }

    public function save()
    {
        // 1. Get incoming request data
        $postData = $this->request->getPost();
        $doctorId = $postData['id'] ?? null; // For edit operations

        // 2. Load existing doctor data if it's an edit operation
        $existingDoctor = null;
        if ($doctorId) {
            $existingDoctor = $this->doctorModel->find($doctorId);
            if (!$existingDoctor) {
                return redirect()->back()->with('error', 'Doctor not found for editing.')->withInput();
            }
        }

        // 3. Define Validation Rules
        // Username and Password validation for NEW entries
        // We keep is_unique here because UserModel's validation doesn't have it.
        $usernameRules = ($doctorId ? 'permit_empty' : 'required|is_unique[users.username]') . '|min_length[5]|max_length[50]';
        $passwordRules = ($doctorId ? 'permit_empty' : 'required') . '|min_length[6]|max_length[255]'; // Removed password_confirm, you can add it if needed

        $rules = [
            'first_name'             => 'required|min_length[3]|max_length[100]',
            'last_name'              => 'required|min_length[3]|max_length[100]',
            'email'                  => 'permit_empty|valid_email|max_length[255]',
            'phone_number'           => 'permit_empty|max_length[20]',
            'specialization'         => 'required|min_length[3]|max_length[255]',
            'department_id'          => 'required|is_natural_no_zero',
            'username'               => [
                'rules' => $usernameRules,
                'errors' => [
                    'is_unique' => 'This username is already taken. Please choose another.'
                ]
            ],
            'password'               => $passwordRules,
            'profile_picture'        => 'if_exist|uploaded[profile_picture]|max_size[profile_picture,2048]|ext_in[profile_picture,jpg,jpeg,png]',
            'signature_image'        => 'if_exist|uploaded[signature_image]|max_size[signature_image,1024]|ext_in[signature_image,png]',
            'resume_file'            => 'if_exist|uploaded[resume_file]|max_size[resume_file,5120]|ext_in[resume_file,pdf,doc,docx]',
            'degree_certificate_file' => 'if_exist|uploaded[degree_certificate_file]|max_size[degree_certificate_file,5120]|ext_in[degree_certificate_file,pdf,jpg,jpeg,png]',
            'license_certificate_file' => 'if_exist|uploaded[license_certificate_file]|max_size[license_certificate_file,5120]|ext_in[license_certificate_file,pdf,jpg,jpeg,png]',
            'other_certificate_file' => 'if_exist|uploaded[other_certificate_file]|max_size[other_certificate_file,5120]|ext_in[other_certificate_file,pdf,doc,docx,jpg,jpeg,png]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // 4. Prepare data for the doctor model (remains mostly the same)
        $doctorData = [
            'first_name'                => $postData['first_name'],
            'last_name'                 => $postData['last_name'],
            'gender'                    => $postData['gender'] ?? null,
            'date_of_birth'             => $postData['date_of_birth'] ?? null,
            'email'                     => $postData['email'] ?? null,
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
            // 'user_id' will be set after user creation for new doctors
        ];

        // 5. User Creation/Update Logic (Crucial for Login)
        // BEGIN TRANSACTION - Highly recommended for atomic operations
        $this->db->transBegin();

        try {
            $userId = null;
            if (!$doctorId) { // This is a NEW doctor entry
                $userData = [
                    'username'      => $postData['username'],
                    'password'      => $postData['password'], // UserModel will hash this
                    'role_id'       => 2, // Assuming role_id 2 is for 'Doctor'. Adjust as per your roles table.
                    'first_name'    => $postData['first_name'], // Duplicating in users table
                    'last_name'     => $postData['last_name'],  // Duplicating in users table
                    'email'         => $postData['email'] ?? null,
                    'phone_number'  => $postData['phone_number'] ?? null,
                    'address'       => $postData['address'] ?? null,
                    'status'        => 'active', // Default status for new users
                    // 'last_login' will be managed by login logic
                ];

                if (!$this->userModel->insert($userData)) {
                    // If user creation fails due to model validation or DB error
                    throw new \Exception('Failed to create user account for doctor.');
                }
                $userId = $this->userModel->getInsertID();
                $doctorData['user_id'] = $userId; // Link doctor to user ID
            } else { // This is an EXISTING doctor entry (update)
                // If username or password are provided during an update, update the user table
                $updateUserData = [];
                // Only update username if provided and changed AND not taken by another user
                // This requires a more complex check:
                // If $postData['username'] is not empty AND it's different from current username AND is_unique validation passed (handled by controller validation)
                if (!empty($postData['username'])) {
                    // We only update if the username is different, to avoid unnecessary updates
                    // and potential unique constraint violations if the username didn't change.
                    // $existingDoctor['user_id'] holds the ID of the user record to be updated.
                    $currentUser = $this->userModel->find($existingDoctor['user_id']);
                    if ($currentUser && $postData['username'] !== $currentUser['username']) {
                        $updateUserData['username'] = $postData['username'];
                    } else if (!$currentUser) {
                        // If for some reason the user record doesn't exist, log or handle.
                        // For now, let's just skip username update if no user is found.
                        // Or throw an error if a user account is expected to exist.
                        throw new \Exception('User account not found for existing doctor to update username.');
                    }
                }

                if (!empty($postData['password'])) { // Only update password if provided
                    $updateUserData['password'] = $postData['password']; // UserModel will hash this
                }

                if (!empty($updateUserData)) {
                    if ($existingDoctor['user_id']) {
                        if (!$this->userModel->update($existingDoctor['user_id'], $updateUserData)) {
                            throw new \Exception('Failed to update user account details.');
                        }
                    } else {
                        // This scenario means an existing doctor somehow doesn't have a user_id.
                        // Decide how to handle: create new user, or throw error.
                        // For safety, let's throw an error for now to investigate such cases.
                        throw new \Exception('Existing doctor has no linked user account. Cannot update user details.');
                    }
                }
            }

            // 6. File Upload Handling (remains the same)
            $uploadBaseDir = 'uploads/doctors/';
            $uploadFullPath = ROOTPATH . 'public/' . $uploadBaseDir; // Incorrect for your desired path

            if (!is_dir($uploadFullPath)) {
                mkdir($uploadFullPath, 0777, true);
            }

            $singleFilesMapping = [
                'profile_picture'        => 'profile_picture',
                'signature_image'        => 'signature_image',
                'resume_file'            => 'resume_path',
                'degree_certificate_file' => 'degree_certificate_path',
                'license_certificate_file' => 'license_certificate_path',
            ];

            foreach ($singleFilesMapping as $inputName => $dbColumn) {
                $file = $this->request->getFile($inputName);

                if ($file && $file->isValid() && !$file->hasMoved()) {
                    if ($existingDoctor && !empty($existingDoctor[$dbColumn])) {
                        $oldFilePath = $uploadFullPath . $existingDoctor[$dbColumn];
                        if (file_exists($oldFilePath)) {
                            unlink($oldFilePath);
                        }
                    }
                    $newName = $file->getRandomName();
                    $file->move($uploadFullPath, $newName);
                    $doctorData[$dbColumn] = $newName;
                } else if ($doctorId && $existingDoctor) {
                    if (!isset($doctorData[$dbColumn])) {
                        $doctorData[$dbColumn] = $existingDoctor[$dbColumn] ?? null;
                    }
                } else {
                    if (!isset($doctorData[$dbColumn])) {
                        $doctorData[$dbColumn] = null;
                    }
                }
            }

            $files = $this->request->getFiles();
            $currentOtherCertificates = [];

            if ($doctorId && $existingDoctor && !empty($existingDoctor['other_certificates_path'])) {
                $decoded = json_decode($existingDoctor['other_certificates_path'], true);
                if (is_array($decoded)) {
                    $currentOtherCertificates = $decoded;
                }
            }

            $filesToDelete = $this->request->getPost('delete_other_certificate');
            if (!empty($filesToDelete) && is_array($filesToDelete)) {
                foreach ($filesToDelete as $fileNameToDelete) {
                    $currentOtherCertificates = array_filter($currentOtherCertificates, function ($fileName) use ($fileNameToDelete) {
                        return $fileName !== $fileNameToDelete;
                    });
                    $filePath = $uploadFullPath . $fileNameToDelete;
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
                $currentOtherCertificates = array_values($currentOtherCertificates);
            }

            $newlyUploadedOtherCertificates = [];
            if (isset($files['other_certificate_file'])) {
                foreach ($files['other_certificate_file'] as $file) {
                    if ($file->isValid() && !$file->hasMoved()) {
                        $newName = $file->getRandomName();
                        $file->move($uploadFullPath, $newName);
                        $newlyUploadedOtherCertificates[] = $newName;
                    }
                }
            }

            $finalOtherCertificates = array_merge($currentOtherCertificates, $newlyUploadedOtherCertificates);
            $doctorData['other_certificates_path'] = json_encode($finalOtherCertificates);


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

        // Fetch doctor details, including the department name

        $doctor = $this->doctorModel

            ->select('doctors.*, hospital_departments.name AS department_name, users.username AS user_username, users.email AS user_email')

            ->join('hospital_departments', 'hospital_departments.id = doctors.department_id', 'left')

            ->join('users', 'users.id = doctors.user_id', 'left') // Join with users table to get username/email

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
}
