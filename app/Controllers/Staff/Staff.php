<?php
namespace App\Controllers\Staff;

use App\Controllers\BaseController;
use App\Models\UserModel; // Central model for all users/staff
use App\Models\RoleModel;

class Staff extends BaseController
{
    protected $userModel;
    protected $roleModel;
    // Staff roles (excluding Admin (1) and Doctor (2) if they are managed elsewhere, or include them if they are part of the list view)
    // We'll stick to displaying operational staff for the list view, but Admin can edit anyone.
    protected $staffRoleIds = [3, 4, 5, 6, 7, 8, 9]; 

    public function __construct()
    {
        // Initialize the central UserModel and RoleModel
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
        // Helper to enable form validation if needed outside of the model's auto-validation
        helper('form'); 
    }

    /**
     * Display a list of all staff members (excluding Admin and Doctors by default).
     */
    public function index()
    {
        // Fetch staff details along with their role name
        $data['staff'] = $this->userModel
                            ->select('users.*, roles.name AS role_name')
                            ->join('roles', 'roles.id = users.role_id')
                            ->whereIn('users.role_id', $this->staffRoleIds)
                            ->findAll();
                            
        $data['title'] = 'Staff List';
        return view('staff/list', $data);
    }

    /**
     * Show the registration form.
     */
    public function register()
    {
        // 1. Fetch only the roles that staff can be registered as
        $data['roles'] = $this->roleModel->whereIn('id', $this->staffRoleIds)->findAll();
        
        // 2. Fetch the list of users who are Department Heads for the conditional dropdown
        $data['departmentHeads'] = $this->userModel->getDepartmentHeads();
        
        $data['title'] = 'Register New Staff';
        return view('staff/register', $data);
    }

    /**
     * Handle staff registration submission.
     */
    public function save()
    {
        $postData = $this->request->getPost();

        // The UserModel validation rules and password hashing callbacks handle everything internally.
        // The manager_id will be saved here if it was present in the POST data.
        if (! $this->userModel->save($postData)) {
            // Validation failed. Send errors back to the registration form.
            return redirect()->back()->withInput()->with('errors', $this->userModel->errors());
        }

        return redirect()->to('staff')->with('success', 'Staff member **' . esc($postData['first_name']) . '** successfully registered.');
    }

    /**
     * Show the edit form for a specific staff member.
     * @param int $id The user ID of the staff member
     */
    public function edit($id = null)
    {
        $data['staff'] = $this->userModel->find($id);

        if (!$data['staff']) {
            return redirect()->to('staff')->with('error', 'Staff member not found.');
        }

        // 1. Fetch only the roles that staff can be registered as
        $data['roles'] = $this->roleModel->whereIn('id', $this->staffRoleIds)->findAll();
        
        // 2. Fetch the list of users who are Department Heads for the conditional dropdown
        $data['departmentHeads'] = $this->userModel->getDepartmentHeads();
        
        $data['title'] = 'Edit Staff Member';
        return view('staff/edit', $data);
    }

    /**
     * Handle the update submission for a specific staff member.
     * @param int $id The user ID of the staff member
     */
    public function update($id)
    {
        $postData = $this->request->getPost();
        
        // Ensure the ID is part of the data being validated and updated
        $postData['id'] = $id;

        // The hashPassword callback in UserModel handles password security and ignores empty password fields.
        if (! $this->userModel->save($postData)) {
            // Validation failed (e.g., username/email conflict, password too short)
            return redirect()->back()->withInput()->with('errors', $this->userModel->errors());
        }

        return redirect()->to('staff')->with('success', 'Staff member **' . esc($postData['first_name']) . '** successfully updated.');
    }

    /**
     * Delete a staff member.
     * @param int $id The user ID of the staff member
     */
    public function delete($id = null)
    {
        if ($this->userModel->delete($id)) { 
            return redirect()->to('staff')->with('success', 'Staff member successfully deleted.');
        } else {
            return redirect()->to('staff')->with('error', 'Could not delete staff member.');
        }
    }
}
