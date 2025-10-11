<?php

namespace App\Controllers\Pharmacy;

use App\Controllers\BaseController;
use App\Models\Pharmacy\PharmacySalesPersonModel;
use App\Models\Pharmacy\PharmacyBillingModel;
use App\Models\Pharmacy\PharmacySalesModel;
use App\Models\UserModel;

class SalesPersons extends BaseController
{
    protected $salesPersonModel;
    
    // Define the upload path constants, matching the Doctor controller style
    protected const UPLOAD_DIR = 'uploads/sales_persons/';
    protected const UPLOAD_PATH = ROOTPATH . 'public/' . self::UPLOAD_DIR;

    public function __construct()
    {
        $this->salesPersonModel = new PharmacySalesPersonModel();
        
        // Ensure the upload directory exists upon controller instantiation
        if (!is_dir(self::UPLOAD_PATH)) {
            mkdir(self::UPLOAD_PATH, 0777, true);
        }
    }

    public function index()
    {
        $data['sales_persons'] = $this->salesPersonModel->findAll();
        return view('pharmacy/sales_persons/index', $data);
    }

    public function create()
    {
        return view('pharmacy/sales_persons/create');
    }

    /**
     * Handles the creation of a new Salesperson (Insert operation).
     * Now includes file upload handling and database transactions.
     */
    public function store()
    {
        $validationRules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'phone' => 'required|numeric|min_length[10]|max_length[15]',
            'email' => 'required|valid_email|is_unique[pharmacy_sales_persons.email]',
            'address' => 'permit_empty',
            // Increased max_size to 2048 (2MB)
            'profile_picture' => 'if_exist|uploaded[profile_picture]|max_size[profile_picture,2048]|ext_in[profile_picture,jpg,jpeg,png]'
        ];

        if (!$this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // --- 1. Handle File Upload (oldFileName is null for new records) ---
        $profilePictureName = $this->handleProfilePictureUpload('profile_picture', null);
        if ($profilePictureName === false) {
             return redirect()->back()->withInput()->with('error', 'Profile picture upload failed or was invalid.')->with('errors', $this->validator->getErrors());
        }
        
        // --- 2. Prepare Data and Start Transaction ---
        $salespersonId = $this->salesPersonModel->generateSalesPersonCode();
        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $salespersonData = [
                'salesperson_id' => $salespersonId,
                'first_name' => $this->request->getPost('first_name'),
                'last_name' => $this->request->getPost('last_name'),
                'phone' => $this->request->getPost('phone'),
                'address' => $this->request->getPost('address'),
                'email' => $this->request->getPost('email'),
                'status' => 1, 
                'profile_picture' => $profilePictureName // Save the file name/path
            ];

            // Insert salesperson record
            if (!$this->salesPersonModel->insert($salespersonData)) {
                // Check for model errors on insertion failure
                $modelErrors = $this->salesPersonModel->errors();
                if (!empty($modelErrors)) {
                    $errorDetails = implode('; ', array_values($modelErrors));
                    throw new \Exception("Failed to add salesperson record. Model validation failed: {$errorDetails}");
                }
                throw new \Exception('Failed to add salesperson record (No specific model errors found).');
            }

            // Create the user login record
            $userModel = new UserModel();
            $userData = [
                'role_id' => 8, // Assuming role ID 8 is for Salesperson
                'first_name' => $salespersonData['first_name'],
                'last_name' => $salespersonData['last_name'],
                'username' => $salespersonData['salesperson_id'],
                'email' => $salespersonData['email'],
                'password' => $salespersonData['phone'], 
                'phone_number' => $salespersonData['phone'],
                'status' => 'active'
            ];

            if (!$userModel->insert($userData)) {
                $userErrors = $userModel->errors();
                if (!empty($userErrors)) {
                    $errorDetails = implode('; ', array_values($userErrors));
                    throw new \Exception("Failed to create user account. Validation/DB error: {$errorDetails}");
                }
                throw new \Exception('Failed to create user account for salesperson (No specific model errors found).');
            }
            
            // Commit transaction if both succeeded
            $db->transCommit();

            return redirect()->to('pharmacy/salespersons')->with('success', 'Salesperson added successfully. Username: ' . $userData['username'] . ', Password: ' . $userData['phone_number']);
        } catch (\Exception $e) {
            // Rollback transaction and delete uploaded file on failure
            $db->transRollback();
            if (!empty($profilePictureName) && file_exists(self::UPLOAD_PATH . $profilePictureName)) {
                @unlink(self::UPLOAD_PATH . $profilePictureName); // @ to suppress warnings if file is locked
            }
            return redirect()->back()->withInput()->with('error', 'Operation failed: ' . $e->getMessage());
        }
    }

    private function handleProfilePictureUpload(string $inputName, ?string $oldFileName)
    {
        $file = $this->request->getFile($inputName);
        
        // 1. Check if a new, valid file was uploaded
        if ($file && $file->isValid() && !$file->hasMoved()) {
            
            // 2. Delete old file if it exists (only relevant on update)
            if ($oldFileName) {
                $oldFilePath = self::UPLOAD_PATH . $oldFileName;
                if (file_exists($oldFilePath)) {
                    @unlink($oldFilePath); // Use @ to suppress errors if file is locked
                }
            }
            
            // 3. Move the new file
            $newName = $file->getRandomName();
            if ($file->move(self::UPLOAD_PATH, $newName)) {
                return $newName; // Return new file name
            } else {
                return false; // File movement failed
            }
        }
        
        // 4. No new file uploaded, return the old file name to retain the existing record
        return $oldFileName; 
    }
    
    // --- Existing methods below (edit, update, delete, toggleStatus, profile) remain unchanged ---
    
    public function edit($id = null)
    {
        $data['salesperson'] = $this->salesPersonModel->find($id);

        if (empty($data['salesperson'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Cannot find the salesperson item: ' . $id);
        }

        return view('pharmacy/sales_persons/edit', $data);
    }

    public function update($id = null)
    {
        // 1. Fetch the old record to get the existing profile picture name AND email
        $oldSalesperson = $this->salesPersonModel->find($id);
        if (!$oldSalesperson) {
             return redirect()->back()->with('error', 'Salesperson not found for update.');
        }
        
        // Validation rules are defined here. We trust these rules.
        $validationRules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'phone' => 'required|numeric|min_length[10]|max_length[15]',
            'email' => 'required|valid_email', // *** UNIQUE CHECK REMOVED from controller rules ***
            'address' => 'permit_empty',
            'status' => 'required|integer',
            // Increased max_size to 2048 (2MB)
            'profile_picture' => 'if_exist|uploaded[profile_picture]|max_size[profile_picture,2048]|ext_in[profile_picture,jpg,jpeg,png]'
        ];

        if (!$this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // --- 2. Handle File Upload (pass the existing file name for potential deletion) ---
        $profilePictureName = $this->handleProfilePictureUpload('profile_picture', $oldSalesperson['profile_picture']);
        if ($profilePictureName === false) {
             // Use the validation errors from the controller if available, otherwise generic error
             $uploadErrors = $this->validator->getErrors() ?? ['profile_picture' => 'File movement or handling failed.'];
             return redirect()->back()->withInput()->with('error', 'File upload failed or was invalid.')->with('errors', $uploadErrors);
        }
        
        // --- 3. Prepare Data for Update ---
        $data = [
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'phone' => $this->request->getPost('phone'),
            'address' => $this->request->getPost('address'),
            'email' => $this->request->getPost('email'),
            'status' => $this->request->getPost('status'),
            'profile_picture' => $profilePictureName // Will be the new name or the old name
        ];

        // *** CRITICAL FIX: Skip Model's internal validation ***
        // This prevents the model's potentially hardcoded 'is_unique' rule from running,
        // as we already checked validation in the controller above.
        $this->salesPersonModel->skipValidation(true);

        if ($this->salesPersonModel->update($id, $data)) {
            // Update associated user account if necessary (e.g., email/name change)
            $userModel = new UserModel();
            $user = $userModel->where('email', $oldSalesperson['email'])->first();
            
            if ($user) {
                // Update user account details (excluding password/username unless separate inputs are provided)
                $userUpdateData = [
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'email' => $data['email'],
                    'phone_number' => $data['phone'],
                    'status' => ($data['status'] == 1) ? 'active' : 'inactive'
                ];
                
                // --- 4. Robust User Update Error Reporting ---
                if (!$userModel->update($user['id'], $userUpdateData)) {
                    $userErrors = $userModel->errors();
                    if (!empty($userErrors)) {
                        $errorDetails = implode('; ', array_values($userErrors));
                        // Log a warning if the user update fails after the salesperson update succeeded
                        log_message('error', 'Failed to update associated user for salesperson ID ' . $id . ': ' . $errorDetails);
                        return redirect()->to('pharmacy/salespersons')->with('warning', 'Salesperson updated, but failed to update associated user login account details: ' . $errorDetails);
                    }
                }
            }
            
            return redirect()->to('pharmacy/salespersons')->with('success', 'Salesperson updated successfully!');
        } else {
            // Check for model validation errors here and display them if found.
            $modelErrors = $this->salesPersonModel->errors();
            if (!empty($modelErrors)) {
                return redirect()->back()->withInput()->with('errors', $modelErrors);
            }
            return redirect()->back()->withInput()->with('error', 'Failed to update salesperson. Check application logs for details (no specific model errors found).');
        }
    }

    public function delete($id = null)
    {
        $salesperson = $this->salesPersonModel->find($id);
        if (!$salesperson) {
            return redirect()->to('pharmacy/salespersons')->with('error', 'Salesperson not found.');
        }

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $userModel = new UserModel();
            $user = $userModel->where('email', $salesperson['email'])->first();

            // Delete the user record first
            if ($user) {
                if (!$userModel->delete($user['id'])) {
                    throw new \Exception('Failed to delete associated user account.');
                }
            }

            // Then delete the salesperson record
            if (!$this->salesPersonModel->delete($id)) {
                throw new \Exception('Failed to delete salesperson record.');
            }

            // Delete associated profile picture if it exists
            if (!empty($salesperson['profile_picture'])) {
                $filePath = self::UPLOAD_PATH . $salesperson['profile_picture'];
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }

            $db->transCommit();
            return redirect()->to('pharmacy/salespersons')->with('success', 'Salesperson and associated user record deleted successfully!');
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->to('pharmacy/salespersons')->with('error', 'Transaction failed: ' . $e->getMessage());
        }
    }

    /**
     * Toggles the status of a salesperson (active/inactive).
     */
    public function toggleStatus($id = null)
    {
        $salesperson = $this->salesPersonModel->find($id);

        if (empty($salesperson)) {
            return redirect()->to('pharmacy/salespersons')->with('error', 'Salesperson not found.');
        }

        $newStatus = ($salesperson['status'] == 1) ? 0 : 1;
        $newLoginStatus = ($newStatus == 1) ? 'active' : 'inactive';

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            // Update the user's login status
            $userModel = new UserModel();
            $user = $userModel->where('email', $salesperson['email'])->first();

            if ($user) {
                if (!$userModel->update($user['id'], ['status' => $newLoginStatus])) {
                    throw new \Exception('Failed to update user login status.');
                }
            }

            // Then, update the salesperson's record status
            $data = ['status' => $newStatus];
            if (!$this->salesPersonModel->update($id, $data)) {
                throw new \Exception('Failed to update salesperson status.');
            }

            $db->transCommit();

            $message = ($newStatus == 1) ? 'Salesperson activated successfully.' : 'Salesperson deactivated successfully.';
            return redirect()->to('pharmacy/salespersons')->with('success', $message);
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->to('pharmacy/salespersons')->with('error', 'Operation failed: ' . $e->getMessage());
        }
    }

    public function profile($userId = null)
    {
        $session = session();
        $loggedInUserId = $session->get('user_id');
        $loggedInUserRoleId = $session->get('role_id');

        // If no ID is provided, assume the logged-in user wants to see their own profile
        if ($userId === null) {
            $userId = $loggedInUserId;
        }

        // Check permissions: A Sales Person can only view their own profile
        if ($loggedInUserRoleId == 8 && $userId != $loggedInUserId) {
            return redirect()->back()->with('error', 'You are not authorized to view this profile.');
        }

        // Load the models
        $userModel = new \App\Models\UserModel();
        $salesPersonModel = new \App\Models\Pharmacy\PharmacySalesPersonModel();
        $billingModel = new PharmacyBillingModel();
        $salesModel = new PharmacySalesModel();

        // Fetch user and salesperson data
        $user = $userModel->find($userId);
        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }

        // Fetch the salesperson's profile from the pharmacy table based on user's email
        $salesPerson = $salesPersonModel->where('email', $user['email'])->first();

        if (!$salesPerson) {
            // This user is not a salesperson, so a report won't exist.
            return redirect()->back()->with('error', 'Sales person profile not found.');
        }

        // Get start and end dates from the URL query, or set a default
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-d', strtotime('-30 days'));
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-d');

        // Fetch sales data using the integer user ID, which is correctly stored in the sales tables
        $inHospitalSales = $billingModel->getInHospitalSalesBySalesPerson((int)$userId, $startDate, $endDate);
        $outsideSales = $salesModel->getOutsideSalesBySalesPerson((int)$userId, $startDate, $endDate);

        $data = [
            'title' => 'My Sales Report',
            'salesPerson' => $salesPerson,
            'user' => $user,
            'inHospitalSales' => $inHospitalSales,
            'outsideSales' => $outsideSales,
            'startDate' => $startDate,
            'endDate' => $endDate
        ];

        // Use your new view file
        return view('pharmacy/self_report/self_report', $data);
    }

    public function show($id = null)
    {
        if (is_null($id)) {
            // Handle case where ID is not provided
            return redirect()->to(site_url('pharmacy/salespersons'))->with('error', 'Salesperson ID is missing.');
        }

        // Fetch the salesperson data
        $person = $this->salesPersonModel->find($id);

        if (!$person) {
            // Handle case where salesperson is not found
            return redirect()->to(site_url('pharmacy/salespersons'))->with('error', 'Salesperson not found.');
        }

        $data = [
            'person' => $person,
            'title' => 'Salesperson Profile'
        ];

        // Load the view file we previously created
        return view('pharmacy/sales_persons/show', $data);
    }
}
