<?php

namespace App\Controllers\Roles;

use App\Controllers\BaseController;
use App\Models\RoleModel;

class Roles extends BaseController
{
    protected $roleModel;

    public function __construct()
    {
        $this->roleModel = new RoleModel();
    }

    /**
     * Display the list of roles, now joining to show the reports_to role name.
     * NOTE: This requires the getRolesWithReportsToName() method in RoleModel.
     */
    public function index()
    {
        // Use the dedicated method to fetch roles with the name of the role they report to
        $data['roles'] = $this->roleModel->getRolesWithReportsToName();
        $data['title'] = 'Roles List';
        return view('roles/list', $data);
    }

    /**
     * Display the form to create a new role.
     * CRITICAL UPDATE: Fetching roles with 'Department Head' level to populate the dropdown.
     */
    public function create()
    {
        // Fetch all roles that are designated as 'Department Head'
        $departmentHeadRoles = $this->roleModel
                                  ->where('management_level', 'Department Head')
                                  ->findAll();

        $data = [
            'departmentHeadRoles' => $departmentHeadRoles, // Passed to the view
            'title' => 'Create New Role',
            'validation' => service('validation') // For displaying form errors
        ];
        
        return view('roles/create', $data);
    }

    /**
     * Handles the form submission for creating or updating a role.
     * CRITICAL UPDATE: Added validation and logic for 'reports_to_role_id'.
     */
    public function save()
    {
        $id = $this->request->getPost('id'); // ID is present if editing

        // Define validation rules
        $rules = [
            'name' => 'required|min_length[3]|max_length[100]',
            'management_level' => 'required',
            'description' => 'permit_empty|max_length[255]',
            'reports_to_role_id' => 'permit_empty|integer', 
        ];

        if (!$this->validate($rules)) {
            // If validation fails, redirect back with input and errors
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }
        
        $data = $this->request->getPost();
        
        // Ensure reports_to_role_id is null if the role is not a 'Team Member'
        if ($data['management_level'] !== 'Team Member') {
            $data['reports_to_role_id'] = null;
        }

        if ($this->roleModel->save($data)) {
             $message = $id ? 'Role updated successfully!' : 'Role added successfully!';
             return redirect()->to(base_url('roles'))->with('success', $message);
        } else {
             return redirect()->back()->with('error', 'Failed to save role.');
        }
    }

    /**
     * Display the form to edit an existing role.
     * CRITICAL UPDATE: Fetching roles with 'Department Head' level for the dropdown.
     */
    public function edit($id)
    {
        $role = $this->roleModel->find($id);
        if (!$role) {
            return redirect()->to(base_url('roles'))->with('error', 'Role not found.');
        }
        
        // Fetch all roles that are designated as 'Department Head' (needed for the Reports To dropdown)
        $departmentHeadRoles = $this->roleModel
                                  ->where('management_level', 'Department Head')
                                  ->findAll();

        $data = [
            'role' => $role,
            'departmentHeadRoles' => $departmentHeadRoles, // Passed to the view
            'title' => 'Edit Role: ' . $role['name'],
            'validation' => service('validation')
        ];

        return view('roles/edit', $data);
    }

    public function delete($id)
    {
        // Simple delete logic (consider adding dependency checks here in the future)
        if ($this->roleModel->delete($id)) {
            return redirect()->to(base_url('roles'))->with('success', 'Role deleted successfully!');
        }
        return redirect()->to(base_url('roles'))->with('error', 'Failed to delete role.');
    }
}
