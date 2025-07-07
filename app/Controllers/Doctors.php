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

    public function __construct()
    {
        $this->doctorModel = new DoctorModel();
        $this->userModel = new UserModel();
        $this->departmentModel = new DepartmentModel();
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

    public function save($id = null)
    {
        $doctorId = $id;

        $rules = [
            // doctor_id_code: Removed 'is_unique' from here too, as per your request for no unique constraints.
            // The model will still GENERATE a unique one, but neither DB nor CI validation will enforce it.
            'doctor_id_code'        => 'permit_empty|alpha_numeric_punct|min_length[3]|max_length[50]',

            // first_name: Remains required
            'first_name'            => 'required|alpha_space|min_length[2]|max_length[100]',

            // All other fields: 'permit_empty' to make them completely optional
            'last_name'             => 'permit_empty',
            'email'                 => 'permit_empty',
            'phone_number'          => 'permit_empty',
            'specialization'        => 'permit_empty',
            'qualification'         => 'permit_empty',
            'medical_license_no'    => 'permit_empty',
            'experience_years'      => 'permit_empty',
            'department_id'         => 'permit_empty',
            'gender'                => 'permit_empty',
            'date_of_birth'         => 'permit_empty',
            'address'               => 'permit_empty',
            'emergency_contact_name' => 'permit_empty',
            'emergency_contact_phone' => 'permit_empty',
            'bio'                   => 'permit_empty',
            'registration_number'   => 'permit_empty',
            'medical_council'       => 'permit_empty',
            'joining_date'          => 'permit_empty',
            'employment_status'     => 'permit_empty',
            'contract_type'         => 'permit_empty',
            'designation'           => 'permit_empty',
            'opd_fee'               => 'permit_empty',
            'ipd_charge_percentage' => 'permit_empty',
            'bank_account_number'   => 'permit_empty',
            'bank_name'             => 'permit_empty',
            'ifsc_code'             => 'permit_empty',
            'pan_number'            => 'permit_empty',
            'is_available'          => 'permit_empty',
            'status'                => 'permit_empty',

            // These are related to user model creation.
            // Removed 'is_unique' for username as per your request for no unique constraints.
            'username'              => 'permit_empty|alpha_numeric_punct|min_length[3]|max_length[100]',
            'password'              => 'permit_empty|min_length[8]',

            // File upload rules are typically 'if_exist' anyway, meaning optional.
            // I've kept them as they are for proper file handling if a file is uploaded.
            'profile_picture'       => 'if_exist|uploaded[profile_picture]|max_size[profile_picture,2048]|ext_in[profile_picture,jpg,jpeg,png,gif]',
            'signature_image'       => 'if_exist|uploaded[signature_image]|max_size[signature_image,2048]|ext_in[signature_image,jpg,jpeg,png,gif]',
            'resume_file'           => 'if_exist|uploaded[resume_file]|max_size[resume_file,5120]|ext_in[resume_file,pdf,doc,docx]',
            'degree_certificate_file' => 'if_exist|uploaded[degree_certificate_file]|max_size[degree_certificate_file,5120]|ext_in[degree_certificate_file,pdf,jpg,jpeg,png]',
            'license_certificate_file' => 'if_exist|uploaded[license_certificate_file]|max_size[license_certificate_file,5120]|ext_in[license_certificate_file,pdf,jpg,jpeg,png]',
            'other_certificate_file' => 'if_exist|uploaded[other_certificate_file]|max_size[other_certificate_file,5120]|ext_in[other_certificate_file,pdf,doc,docx,jpg,jpeg,png]',
        ];


        $messages = [
            'department_id' => [
                'is_not_unique' => 'The selected department does not exist.'
            ],
            // User ID validation message might not be needed if user_id is no longer unique
            // 'user_id' => [
            //     'is_not_unique' => 'The selected user does not exist.'
            // ]
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $postData = $this->request->getPost();

        $newUserId = null;
        if (empty($doctorId)) {
            // Check if username and password are provided before trying to create a user account
            // This prevents creating a user if only doctor's first name and ID code are given
            if (!empty($postData['username']) && !empty($postData['password'])) {
                $userData = [
                    'first_name' => $postData['first_name'],
                    'last_name'  => $postData['last_name'] ?? null,
                    'username'   => $postData['username'],
                    'email'      => $postData['email'] ?? null,
                    'password'   => $postData['password'],
                    'phone_number' => $postData['phone_number'] ?? null,
                    'address'    => $postData['address'] ?? null,
                    'status'     => 'active',
                    'role_id'    => 2, // Assuming 2 is the role_id for 'Doctor'
                    'last_login' => null,
                ];

                if (!$this->userModel->insert($userData)) {
                    // This error might still occur if UserModel has its own validation/unique rules
                    return redirect()->back()->withInput()->with('error', 'Failed to create user account for the doctor. ' . implode(', ', $this->userModel->errors()));
                }
                $newUserId = $this->userModel->getInsertID();
            } else {
                // If username or password for user account is missing, do not create a user
                $newUserId = null;
            }
        }

        // doctorIDCode generation remains in DoctorModel's beforeInsert hook.
        // It's removed from here as per previous step.

        // Prepare $doctorData with null for optional fields if they are empty in $postData
        $doctorData = [
            'id'                    => $doctorId,
            'first_name'            => $postData['first_name'],
            'last_name'             => $postData['last_name'] ?? null,
            'specialization'        => $postData['specialization'] ?? null,
            'qualification'         => $postData['qualification'] ?? null,
            'medical_license_no'    => $postData['medical_license_no'] ?? null,
            'experience_years'      => $postData['experience_years'] ?? null,
            'bio'                   => $postData['bio'] ?? null,
            'department_id'         => $postData['department_id'] ?? null,
            'gender'                => $postData['gender'] ?? null,
            'date_of_birth'         => $postData['date_of_birth'] ?? null,
            'phone_number'          => $postData['phone_number'] ?? null,
            'email'                 => $postData['email'] ?? null,
            'address'               => $postData['address'] ?? null,
            'emergency_contact_name' => $postData['emergency_contact_name'] ?? null,
            'emergency_contact_phone' => $postData['emergency_contact_phone'] ?? null,
            'registration_number'   => $postData['registration_number'] ?? null,
            'medical_council'       => $postData['medical_council'] ?? null,
            'joining_date'          => $postData['joining_date'] ?? null,
            'employment_status'     => $postData['employment_status'] ?? null,
            'contract_type'         => $postData['contract_type'] ?? null,
            'designation'           => $postData['designation'] ?? null,
            'opd_fee'               => $postData['opd_fee'] ?? null,
            'ipd_charge_percentage' => $postData['ipd_charge_percentage'] ?? null,
            'bank_account_number'   => $postData['bank_account_number'] ?? null,
            'bank_name'             => $postData['bank_name'] ?? null,
            'ifsc_code'             => $postData['ifsc_code'] ?? null,
            'pan_number'            => $postData['pan_number'] ?? null,
            'is_available'          => $postData['is_available'] ?? 0,
            'status'                => $postData['status'] ?? 'Active',
        ];

        if ($newUserId) {
            $doctorData['user_id'] = $newUserId;
            // doctor_id_code is handled by the model's beforeInsert
            $doctorData['last_login_at'] = null;
        } else if ($doctorId) {
            $existingDoctor = $this->doctorModel->find($doctorId);
            // Only update user_id if we are not creating a new user and there's an existing doctor
            $doctorData['user_id'] = $existingDoctor['user_id'] ?? null;
        }

        $uploadBaseDir = 'uploads/doctors/';
        $uploadFullPath = ROOTPATH . 'public/' . $uploadBaseDir;

        if (!is_dir($uploadFullPath)) {
            mkdir($uploadFullPath, 0777, true);
        }

        $filesToProcess = [
            'profile_picture'       => 'profile_picture',
            'signature_image'       => 'signature_image',
            'resume_file'           => 'resume_path',
            'degree_certificate_file' => 'degree_certificate_path',
            'license_certificate_file' => 'license_certificate_path',
            'other_certificate_file' => 'other_certificates_path',
        ];

        $existingDoctor = null;
        if ($doctorId) {
            $existingDoctor = $this->doctorModel->find($doctorId);
        }

        foreach ($filesToProcess as $inputName => $dbColumn) {
            $file = $this->request->getFile($inputName);
            if ($file && $file->isValid() && !$file->hasMoved()) {
                $newName = $file->getRandomName();
                $file->move($uploadFullPath, $newName);
                $doctorData[$dbColumn] = $newName;
            } else if ($doctorId && $existingDoctor) {
                if (isset($existingDoctor[$dbColumn])) {
                    $doctorData[$dbColumn] = $existingDoctor[$dbColumn];
                } else {
                    $doctorData[$dbColumn] = NULL;
                }
            } else {
                $doctorData[$dbColumn] = NULL;
            }
        }

        $isUpdate = !empty($doctorId);

        if ($this->doctorModel->save($doctorData)) { // Model handles the doctor_id_code here
            $message = $isUpdate ? 'Doctor updated successfully!' : 'Doctor added successfully!';
            return redirect()->to(base_url('doctors'))->with('success', $message);
        } else {
            $message = $isUpdate ? 'Failed to update doctor.' : 'Failed to add doctor.';
            return redirect()->back()->withInput()->with('error', $message . ' Please check the form data.');
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
}
